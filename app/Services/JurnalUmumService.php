<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\JurnalUmum;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JurnalUmumService
{
    /**
     * Buat jurnal entry yang menulis ke BOTH:
     * 1. Filament structure (jurnal + jurnal_detail)
     * 2. Laravel structure (jurnal_umum)
     * 
     * @param array $data - Contains:
     *   - tanggal: date
     *   - entries: array of [
     *       'keterangan' => string (nama akun or description),
     *       'akun_id' => int (id dari tabel akun),
     *       'ref' => string (optional reference),
     *       'debit' => numeric,
     *       'kredit' => numeric
     *     ]
     */
    public static function buatJurnalDual(array $data): void
    {
        DB::transaction(function () use ($data) {
            $tanggal = $data['tanggal'];
            $entries = $data['entries'];
            $deskripsi = $data['deskripsi'] ?? 'Jurnal Umum Manual';

            // Generate unique reference number
            $date = Carbon::parse($tanggal)->format('Ym');
            $last = Jurnal::where('no_referensi', 'like', "JU-{$date}-%")
                ->orderByDesc('id')
                ->value('no_referensi');
            
            $urut = $last ? ((int) substr($last, -3)) + 1 : 1;
            $noReferensi = "JU-{$date}-" . str_pad($urut, 3, '0', STR_PAD_LEFT);

            // =========================
            // 1️⃣ CREATE FILAMENT JURNAL (jurnal + jurnal_detail)
            // =========================
            $jurnal = Jurnal::create([
                'tanggal'      => $tanggal,
                'no_referensi' => $noReferensi,
                'deskripsi'    => $deskripsi,
            ]);

            // Create id_transaksi for grouping in jurnal_umum
            $idTransaksi = now()->format('YmdHis') . uniqid();

            foreach ($entries as $entry) {
                // Skip if both debit and kredit are 0
                if (($entry['debit'] ?? 0) == 0 && ($entry['kredit'] ?? 0) == 0) {
                    continue;
                }

                $akunId = $entry['akun_id'];
                $debit = $entry['debit'] ?? 0;
                $kredit = $entry['kredit'] ?? 0;
                $keterangan = $entry['keterangan'] ?? '';
                $ref = $entry['ref'] ?? null;

                // 1a. Create jurnal_detail entry (Filament structure)
                $jurnal->details()->create([
                    'no_akun'   => $akunId, // This is the foreign key to akun.id
                    'debit'     => $debit,
                    'credit'    => $kredit,
                    'deskripsi' => $keterangan,
                ]);

                // =========================
                // 2️⃣ CREATE JURNAL_UMUM ENTRY (Laravel structure)
                // =========================
                JurnalUmum::create([
                    'tanggal'      => $tanggal,
                    'keterangan'   => $keterangan,
                    'ref'          => $ref,
                    'debit'        => $debit,
                    'kredit'       => $kredit,
                    'id_transaksi' => $idTransaksi, // Group entries together
                ]);
            }
        });
    }

    /**
     * Update storeJurnalUmum to use dual-write pattern
     * Converts request data to format expected by buatJurnalDual
     */
    public static function storeFromRequest($request): void
    {
        $entries = [];
        
        foreach ($request->keterangan as $index => $ket) {
            $debit = $request->debit[$index] ?? 0;
            $kredit = $request->kredit[$index] ?? 0;

            // Only process if either debit or kredit is > 0
            if ($debit > 0 || $kredit > 0) {
                // Find akun by nama_akun (keterangan)
                $akun = Akun::where('nama_akun', $ket)->first();
                
                if (!$akun) {
                    throw new \Exception("Akun dengan nama '{$ket}' tidak ditemukan");
                }

                $entries[] = [
                    'keterangan' => $ket,
                    'akun_id'    => $akun->id,
                    'ref'        => $request->ref[$index] ?? null,
                    'debit'      => $debit,
                    'kredit'     => $kredit,
                ];
            }
        }

        self::buatJurnalDual([
            'tanggal'   => $request->tanggal,
            'deskripsi' => 'Jurnal Umum Manual',
            'entries'   => $entries,
        ]);
    }
}
