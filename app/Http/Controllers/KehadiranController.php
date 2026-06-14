<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TenagaKerja;
use App\Models\KehadiranHarian;
use App\Models\BiayaTenagaKerja;
use App\Models\PermintaanProduksi;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    /**
     * Tampilkan halaman input absensi harian
     */
    public function index(Request $request)
    {
        $dateInput = $request->input('tanggal', Carbon::now()->toDateString());
        $date = Carbon::parse($dateInput)->toDateString();

        $workers = TenagaKerja::where('status', 'aktif')->orderBy('nama_tenaga', 'asc')->get();
        
        $existingKehadiran = KehadiranHarian::whereDate('tanggal', $date)
            ->get()
            ->keyBy('id_tenaga');

        // Cari job order aktif berstatus 'proses' hari ini
        $activeJobs = PermintaanProduksi::where('status', 'proses')
            ->with('produk')
            ->get();

        $totalBatchAktif = $activeJobs->sum('jumlah_produksi');

        return view('kehadiran.index', compact('workers', 'date', 'existingKehadiran', 'activeJobs', 'totalBatchAktif'));
    }

    /**
     * Simpan absensi harian dan jalankan pooled-allocation otomatis
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status_kehadiran' => 'required|in:hadir,absen,izin,sakit',
            'attendance.*.jam_kerja' => 'required|numeric|min:0|max:24',
            'attendance.*.jam_lembur' => 'required|numeric|min:0|max:24',
            'attendance.*.keterangan' => 'nullable|string|max:255',
        ]);

        $date = $request->tanggal;
        $adminId = Auth::guard('admin')->id();
        $accountingService = app(\App\Services\AccountingService::class);

        DB::beginTransaction();
        try {
            // 1. Simpan/Update data absensi harian pekerja
            foreach ($request->attendance as $workerId => $attData) {
                KehadiranHarian::updateOrCreate(
                    [
                        'tanggal' => $date,
                        'id_tenaga' => $workerId,
                    ],
                    [
                        'status_kehadiran' => $attData['status_kehadiran'],
                        'jam_kerja' => $attData['status_kehadiran'] === 'hadir' ? floatval($attData['jam_kerja']) : 0.0,
                        'jam_lembur' => $attData['status_kehadiran'] === 'hadir' ? floatval($attData['jam_lembur']) : 0.0,
                        'keterangan' => $attData['keterangan'] ?? null,
                    ]
                );
            }

            // 2. Bersihkan alokasi biaya tenaga kerja lama untuk tanggal ini beserta JURNAL-nya
            $oldBiayaTks = BiayaTenagaKerja::whereDate('tanggal_kerja', $date)->get();
            $jobsToRecalculate = collect();

            foreach ($oldBiayaTks as $oldBiaya) {
                $jobsToRecalculate->push($oldBiaya->id_permintaan_produksi);
                
                // Cari dan reverse jurnal umum lama
                $oldJurnals = JurnalUmum::where('id_referensi', $oldBiaya->id_biaya_tk)
                                        ->where('tipe_referensi', 'biaya_tenaga_kerja')
                                        ->get();
                foreach ($oldJurnals as $jurnal) {
                    foreach ($jurnal->detail as $detail) {
                        $akun = $detail->akun;
                        if ($akun) {
                            if ($akun->saldo_normal === 'debit') {
                                $akun->saldo -= $detail->debit;
                                $akun->saldo += $detail->kredit;
                            } else {
                                $akun->saldo -= $detail->kredit;
                                $akun->saldo += $detail->debit;
                            }
                            $akun->save();
                        }
                    }
                    $jurnal->delete();
                }
                $oldBiaya->delete();
            }

            // 3. Pooled-Allocation Engine
            // Cari job order aktif berstatus 'proses' hari ini
            $activeJobs = PermintaanProduksi::where('status', 'proses')->get();
            $totalBatch = $activeJobs->sum('jumlah_produksi');

            $allocatedCount = 0;
            $totalCostAllocated = 0;

            if ($activeJobs->isNotEmpty() && $totalBatch > 0) {
                // Ambil data pekerja yang hadir hari ini
                $presentWorkers = KehadiranHarian::whereDate('tanggal', $date)
                    ->where('status_kehadiran', 'hadir')
                    ->with('tenagaKerja')
                    ->get();

                foreach ($presentWorkers as $attendance) {
                    $worker = $attendance->tenagaKerja;
                    if (!$worker) continue;

                    // Hitung upah harian pekerja
                    $upahJam = floatval($worker->upah_per_jam ?? 6000);
                    $jamKerja = floatval($attendance->jam_kerja);
                    $jamLembur = floatval($attendance->jam_lembur);

                    $upahKerja = $jamKerja * $upahJam;
                    $upahLembur = $jamLembur * $upahJam * 1.5; // lembur 1.5x upah per jam
                    $totalUpahHarian = $upahKerja + $upahLembur;

                    if ($totalUpahHarian <= 0) continue;

                    // Distribusikan upah harian secara proporsional ke semua Job Order aktif
                    foreach ($activeJobs as $job) {
                        $porsiJob = floatval($job->jumlah_produksi) / floatval($totalBatch);
                        $allocatedCost = $porsiJob * $totalUpahHarian;

                        if ($allocatedCost <= 0) continue;

                        // Simpan record biaya tenaga kerja teralokasi
                        $biaya = BiayaTenagaKerja::create([
                            'id_permintaan_produksi' => $job->id_permintaan_produksi,
                            'id_tenaga'              => $worker->id_tenaga,
                            'id_admin'               => $adminId,
                            'tanggal_kerja'          => $date,
                            'hari_kerja'             => $jamKerja / 8.0, // porsi hari standar
                            'jam_absen'              => $jamKerja,
                            'jumlah_batch'           => $job->jumlah_produksi,
                            'upah_per_minggu'        => $worker->upah_per_jam, // fallback ke upah per jam untuk kartu biaya
                            'nominal_potongan'       => 0,
                            'nominal_lembur'         => $upahLembur * $porsiJob,
                            'total_biaya'            => $allocatedCost,
                            'keterangan'             => "Alokasi Harian (" . ($worker->jenis_tenaga === 'langsung' ? 'BTKL' : 'BTKTL') . " - {$worker->nama_tenaga}, Batch: " . number_format($job->jumlah_produksi, 0) . " dari " . number_format($totalBatch, 0) . ")",
                        ]);

                        $jobsToRecalculate->push($job->id_permintaan_produksi);

                        // Buat jurnal akuntansi otomatis
                        $accountingService->recordBiayaTenagaKerja($biaya, $job);

                        $allocatedCount++;
                        $totalCostAllocated += $allocatedCost;
                    }
                }
            }

            // 4. Hitung ulang total biaya produksi untuk semua job order yang terdampak
            $uniqueJobsToRecalculate = $jobsToRecalculate->unique();
            foreach ($uniqueJobsToRecalculate as $jobId) {
                $job = PermintaanProduksi::find($jobId);
                if ($job) {
                    $job->hitungTotalBiayaProduksi();
                }
            }

            DB::commit();

            $message = 'Kehadiran harian berhasil disimpan!';
            if ($allocatedCount > 0) {
                $message .= ' Alokasi biaya otomatis ke ' . $activeJobs->count() . ' Job Order aktif berhasil dilakukan sebesar Rp ' . number_format($totalCostAllocated, 0, ',', '.') . '.';
            } else {
                $message .= ' Catatan: Tidak ada biaya yang dialokasikan ke Job Order karena tidak ada Job Order aktif berstatus \'proses\' hari ini.';
            }

            return redirect()->route('kehadiran.index', ['tanggal' => $date])
                             ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal memproses absensi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Tampilkan halaman rekap mingguan & insentif Rp 30.000
     */
    public function rekapMingguan(Request $request)
    {
        // Default minggu ini (Senin - Sabtu)
        $startOfWeek = $request->filled('tanggal_mulai') 
            ? Carbon::parse($request->tanggal_mulai)->startOfWeek(Carbon::MONDAY) 
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
            
        $endOfWeek = $startOfWeek->copy()->addDays(5); // Senin + 5 hari = Sabtu

        $startStr = $startOfWeek->toDateString();
        $endStr = $endOfWeek->toDateString();

        $workers = TenagaKerja::where('status', 'aktif')->orderBy('nama_tenaga', 'asc')->get();
        $rekapData = [];

        // Hitung kehadiran harian tiap pekerja dari Senin s.d. Sabtu
        foreach ($workers as $worker) {
            $attendances = KehadiranHarian::where('id_tenaga', $worker->id_tenaga)
                ->whereBetween('tanggal', [$startStr, $endStr])
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->tanggal)->format('l'); // 'Monday', 'Tuesday', etc.
                });

            $hariMasukCount = 0;
            $totalJamKerja = 0.0;
            $detailHari = [];

            // Nama hari Senin s.d. Sabtu dalam Bahasa Inggris untuk key Carbon
            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $daysIndo = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];

            foreach ($daysOfWeek as $day) {
                $att = $attendances->get($day);
                if ($att) {
                    $status = $att->status_kehadiran;
                    $jam = floatval($att->jam_kerja);
                    $totalJamKerja += $jam;
                    if ($status === 'hadir') {
                        $hariMasukCount++;
                    }
                    $detailHari[$daysIndo[$day]] = [
                        'status' => $status,
                        'jam' => $jam,
                    ];
                } else {
                    $detailHari[$daysIndo[$day]] = [
                        'status' => 'belum_absen',
                        'jam' => 0.0,
                    ];
                }
            }

            // Memenuhi syarat insentif jika hadir 6 hari penuh dan total jam kerja >= 48 jam
            $dapatInsentif = ($hariMasukCount === 6 && $totalJamKerja >= 48.0);

            // Cek apakah insentif untuk minggu ini sudah diklaim / dijurnal
            // Kita gunakan table JurnalUmum untuk mencari apakah ada jurnal insentif untuk id_tenaga ini pada tanggal akhir minggu tersebut
            $sudahKlaim = JurnalUmum::where('id_referensi', $worker->id_tenaga)
                ->where('tipe_referensi', 'insentif_mingguan')
                ->whereDate('tanggal', $endStr)
                ->exists();

            $rekapData[] = [
                'worker' => $worker,
                'hari_masuk' => $hariMasukCount,
                'total_jam' => $totalJamKerja,
                'dapat_insentif' => $dapatInsentif,
                'sudah_klaim' => $sudahKlaim,
                'detail' => $detailHari
            ];
        }

        return view('kehadiran.rekap-mingguan', compact('rekapData', 'startStr', 'endStr'));
    }

    /**
     * Proses posting jurnal insentif mingguan (Rp 30.000)
     */
    public function storeIncentive(Request $request)
    {
        $request->validate([
            'id_tenaga' => 'required|exists:tenaga_kerja,id_tenaga',
            'tanggal_akhir' => 'required|date',
            'nominal' => 'required|numeric|min:0',
        ]);

        $workerId = $request->id_tenaga;
        $date = $request->tanggal_akhir;
        $nominal = floatval($request->nominal);
        $adminId = Auth::guard('admin')->id();

        $worker = TenagaKerja::findOrFail($workerId);
        $accountingService = app(\App\Services\AccountingService::class);

        DB::beginTransaction();
        try {
            // Cek duplikasi klaim
            $sudahKlaim = JurnalUmum::where('id_referensi', $workerId)
                ->where('tipe_referensi', 'insentif_mingguan')
                ->whereDate('tanggal', $date)
                ->exists();

            if ($sudahKlaim) {
                return back()->with('error', 'Insentif untuk ' . $worker->nama_tenaga . ' pada minggu ini sudah diproses!');
            }

            // Post Jurnal Insentif
            $success = $accountingService->recordInsentifMingguan($date, $nominal, $adminId, $worker);

            if (!$success) {
                throw new \Exception("Gagal mencatat jurnal insentif di AccountingService.");
            }

            DB::commit();
            return back()->with('success', 'Insentif sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' untuk ' . $worker->nama_tenaga . ' berhasil dijurnal ke akun Gaji Lainnya (insentif bonus) & Hutang Lainnya.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
