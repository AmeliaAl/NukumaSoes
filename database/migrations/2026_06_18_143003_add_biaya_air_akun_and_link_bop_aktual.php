<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename akun 712 dari "Biaya Listrik, air" → "Biaya Listrik"
        DB::table('akun')->where('kode_akun', '712')->update([
            'nama_akun' => 'Biaya Listrik',
        ]);

        // 2. Tambah akun 713 "Biaya Air" jika belum ada
        if (DB::table('akun')->where('kode_akun', '713')->doesntExist()) {
            DB::table('akun')->insert([
                'kode_akun'    => '713',
                'nama_akun'    => 'Biaya Air',
                'tipe_akun'    => 'beban',
                'saldo_normal' => 'debit',
                'saldo'        => 0,
                'status'       => 'aktif',
                'keterangan'   => 'Biaya tagihan air pabrik',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // 3. Tambah kolom id_jurnal_aktual ke biaya_overhead_pabrik
        //    untuk menautkan rencana BOP ke realisasi aktual
        if (!Schema::hasColumn('biaya_overhead_pabrik', 'id_jurnal_aktual')) {
            Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
                $table->unsignedBigInteger('id_jurnal_aktual')->nullable()->after('keterangan')
                      ->comment('FK ke jurnal_umums: null = belum diaktualkan');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan nama akun 712
        DB::table('akun')->where('kode_akun', '712')->update([
            'nama_akun' => 'Biaya Listrik, air',
        ]);

        // Hapus akun 713
        DB::table('akun')->where('kode_akun', '713')->delete();

        // Hapus kolom id_jurnal_aktual
        if (Schema::hasColumn('biaya_overhead_pabrik', 'id_jurnal_aktual')) {
            Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
                $table->dropColumn('id_jurnal_aktual');
            });
        }
    }
};
