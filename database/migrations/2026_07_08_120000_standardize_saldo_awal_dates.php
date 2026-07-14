<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SaldoAwal;

return new class extends Migration
{
    /**
     * Standardisasi tanggal saldo awal agar semua akun menggunakan periode awal yang sama
     * 
     * Logika:
     * 1. Cari tanggal paling awal dari semua saldo awal yang ada
     * 2. Ubah semua saldo awal menjadi menggunakan tanggal tersebut
     * 3. Nominal tetap, hanya tanggal yang diseragamkan
     */
    public function up(): void
    {
        // Jika ada data saldo awal
        if (SaldoAwal::exists()) {
            // Cari tanggal paling awal
            $earliestDate = SaldoAwal::orderBy('tanggal', 'asc')
                ->first()
                ->tanggal;

            // Update semua saldo awal agar menggunakan tanggal paling awal
            \Illuminate\Support\Facades\DB::table('saldo_awals')->update([
                'tanggal' => $earliestDate,
            ]);
        }
    }

    /**
     * Rollback: kembalikan ke kondisi sebelumnya
     * (Catatan: data asli sudah tidak tersimpan, jadi rollback tidak akan me-restore data original)
     */
    public function down(): void
    {
        // Tidak bisa rollback karena data original sudah tidak tersimpan
        // Ini adalah migration one-way untuk data cleanup
    }
};
