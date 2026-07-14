<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format angka ke format Rupiah Indonesia: Rp 15.000,00
     */
    public static function rupiah($value): string
    {
        return 'Rp ' . number_format((float) $value, 2, ',', '.');
    }
}
