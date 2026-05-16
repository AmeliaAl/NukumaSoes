<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;
use App\Models\Penyusutan;
use Carbon\Carbon;

class PemeliharaanService
{
    public static function buatJurnal($pemeliharaan): void
    {
        DB::transaction(function () use ($pemeliharaan) {

            // =========================
            // 1️⃣ AMBIL AKUN
            // =========================
            if ($pemeliharaan->metode_pembayaran === 'cash') {
                    $akunKas = Akun::where('no_akun', 111)->firstOrFail(); // kas default
                } else {
                    $akunKas = Akun::findOrFail($pemeliharaan->id_akun); // bank yang dipilih
                }
            $akunBeban = Akun::where('no_akun', 616)->firstOrFail(); // Beban Pemeliharaan
            $akunAset  = Akun::where('no_akun', 171)->firstOrFail(); // Peralatan

            // =========================
            // 2️⃣ HEADER JURNAL
            // =========================
            $jurnal = Jurnal::create([
                'tanggal'      => $pemeliharaan->tanggal,
                'no_referensi' => null,
                'deskripsi'    => 'Pemeliharaan aset - ' . $pemeliharaan->aset->nama_aset,
            ]);

            // =========================
            // 3️⃣ JURNAL BERDASARKAN JENIS
            // =========================
            if ($pemeliharaan->jenis_perbaikan === 'maintenance') {

                // 🔹 BEBAN PEMELIHARAAN
                // Dr Beban
                //     Cr Kas
                $jurnal->details()->create([
                    'no_akun'   => $akunBeban->id,
                    'debit'     => $pemeliharaan->biaya,
                    'credit'    => 0,
                    'deskripsi' => 'Beban pemeliharaan aset',
                ]);

                $jurnal->details()->create([
                    'no_akun'   => $akunKas->id,
                    'debit'     => 0,
                    'credit'    => $pemeliharaan->biaya,
                    'deskripsi' => 'Pembayaran pemeliharaan aset',
                ]);

            } elseif ($pemeliharaan->jenis_perbaikan === 'peningkatan') {

                // 🔹 KAPITALISASI ASET
                // Dr Aset
                //     Cr Kas
                $jurnal->details()->create([
                    'no_akun'   => $akunAset->id,
                    'debit'     => $pemeliharaan->biaya,
                    'credit'    => 0,
                    'deskripsi' => 'Kapitalisasi biaya pemeliharaan',
                ]);

                $jurnal->details()->create([
                    'no_akun'   => $akunKas->id,
                    'debit'     => 0,
                    'credit'    => $pemeliharaan->biaya,
                    'deskripsi' => 'Pembayaran peningkatan aset',
                ]);

            } else {
                throw new \Exception('Jenis perbaikan tidak valid');
            }

        if ($pemeliharaan->jenis_perbaikan === 'peningkatan') {

            $aset = $pemeliharaan->aset;
            $periodePemeliharaan = Carbon::parse($pemeliharaan->tanggal)->format('Y-m');

            $last = Penyusutan::where('aset_id', $aset->id)
                ->where('periode', '<', $periodePemeliharaan)
                ->orderBy('periode', 'desc')
                ->first();

            $nilaiBukuAwal = $last?->nilai_buku ?? $aset->nilai_perolehan;
            $sisaUmur = $last?->sisa_umur ?? ($aset->masa_manfaat * 12);

            $nilaiBukuAwal += $pemeliharaan->biaya;
            $sisaUmur += ($pemeliharaan->tambah_umur ?? 0);

            if ($sisaUmur <= 0) {
                return;
            }
        }
        });
    }
}
