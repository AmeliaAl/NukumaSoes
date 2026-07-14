<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Finalisasi struktur tabel akun agar sesuai database utama tim:
     *   id, header_akun, no_akun, nama_akun, created_at, updated_at
     *
     * - Pastikan no_akun & header_akun sudah terisi dari kolom lama
     * - Hapus kolom kode_akun (sudah digantikan no_akun)
     * - Hapus kolom tipe_akun (sudah digantikan header_akun)
     *
     * Tambahkan FK constraint pada jurnal_detail.no_akun → akun.no_akun
     */
    public function up(): void
    {
        // ── Pastikan no_akun dan header_akun terisi sebelum drop ──────────
        DB::statement('UPDATE akun SET no_akun = kode_akun WHERE no_akun IS NULL OR no_akun = ""');
        DB::statement('UPDATE akun SET header_akun = tipe_akun WHERE header_akun IS NULL OR header_akun = ""');

        // ── Drop kolom legacy ─────────────────────────────────────────────
        Schema::table('akun', function (Blueprint $table) {
            // Drop FK references dulu jika ada (di tabel lain yang reference kode_akun)
            // kode_akun dan tipe_akun tidak punya FK dari tabel lain
            $table->dropColumn(['kode_akun', 'tipe_akun']);
        });

        // ── Tambah FK jurnal_detail.no_akun → akun.no_akun ───────────────
        // (sebelumnya hanya index, sekarang jadikan FK yang proper)
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->foreign('no_akun', 'fk_jurnal_detail_no_akun')
                  ->references('no_akun')
                  ->on('akun')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->dropForeign('fk_jurnal_detail_no_akun');
        });

        Schema::table('akun', function (Blueprint $table) {
            $table->string('kode_akun')->nullable()->after('no_akun');
            $table->string('tipe_akun')->nullable()->after('nama_akun');
        });

        DB::statement('UPDATE akun SET kode_akun = no_akun, tipe_akun = header_akun');
    }
};
