<?php

namespace App\Helpers;

use App\Models\JurnalDetail;

class AkunHelper
{
    /**
     * Hitung saldo akun murni dari jurnal (tanpa kolom saldo di tabel akun)
     * Cocok untuk akun aktiva normal (debit - kredit)
     */
    public static function getSaldo(int $akunId, ?string $sampaiTanggal = null): float
    {
        $query = JurnalDetail::where('no_akun', $akunId);

        if ($sampaiTanggal) {
            $query->whereHas('jurnal', fn($q) =>
                $q->where('tanggal', '<=', $sampaiTanggal)
            );
        }

        $debit  = (clone $query)->sum('debit');
        $kredit = (clone $query)->sum('credit');

        return $debit - $kredit;
    }
}