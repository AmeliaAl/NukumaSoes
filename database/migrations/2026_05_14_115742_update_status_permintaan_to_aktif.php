<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah kolom dari ENUM ke VARCHAR agar lebih fleksibel
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE permintaan_bahan_baku MODIFY COLUMN status_permintaan VARCHAR(20) NOT NULL DEFAULT 'aktif'");
        }

        // 2. Migrate data lama: pending dan disetujui → aktif, ditolak → aktif
        DB::table('permintaan_bahan_baku')
            ->whereIn('status_permintaan', ['pending', 'disetujui', 'ditolak'])
            ->update(['status_permintaan' => 'aktif']);
    }

    public function down(): void
    {
        // Kembalikan ke ENUM dengan nilai lama
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE permintaan_bahan_baku MODIFY COLUMN status_permintaan ENUM('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending'");
        }
    }
};
