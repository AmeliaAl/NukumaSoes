<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->date('periode_mulai')->nullable()->after('tanggal');
            $table->date('periode_selesai')->nullable()->after('periode_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropColumn(['periode_mulai', 'periode_selesai']);
        });
    }
};
