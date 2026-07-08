<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $journals = DB::table('jurnal_umum')
            ->where('tipe_referensi', 'penerimaan_bahan_baku')
            ->pluck('id_jurnal');

        foreach ($journals as $journalId) {
            $details = DB::table('jurnal_umum_detail')->where('id_jurnal', $journalId)->get();
            foreach ($details as $detail) {
                $account = DB::table('akun')->where('id_akun', $detail->id_akun)->first();
                if (!$account) continue;

                $balance = (float) $account->saldo;
                if ($account->saldo_normal === 'debit') {
                    $balance -= (float) $detail->debit;
                    $balance += (float) $detail->kredit;
                } else {
                    $balance -= (float) $detail->kredit;
                    $balance += (float) $detail->debit;
                }
                DB::table('akun')->where('id_akun', $account->id_akun)->update([
                    'saldo' => $balance,
                    'updated_at' => now(),
                ]);
            }

            DB::table('jurnal_umum_detail')->where('id_jurnal', $journalId)->delete();
            DB::table('jurnal_umum')->where('id_jurnal', $journalId)->delete();
        }
    }

    public function down(): void
    {
        // Jurnal pembelian/gudang harus berasal dari modul pemilik transaksi,
        // sehingga jurnal legacy produksi tidak dibuat ulang saat rollback.
    }
};
