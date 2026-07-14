<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('coas')->where('kode_akun', '311')->exists();

        if (!$exists) {
            DB::table('coas')->insert([
                'kode_akun'  => '311',
                'nama_akun'  => 'Modal Pemilik',
                'tipe_akun'  => 'Ekuitas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('coas')->where('kode_akun', '311')->delete();
    }
};
