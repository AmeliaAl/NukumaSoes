<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename tabel coas → akun dan sesuaikan struktur:
     * - Rename  : coas → akun
     * - Tambah  : header_akun (string, nullable) — kategori/kelompok akun
     * - Rename  : kode_akun → no_akun
     * - Pertahankan: id, nama_akun, created_at, updated_at
     * - Hapus   : tipe_akun (dipindah ke header_akun)
     *
     * Data tetap tersimpan — tidak ada DROP TABLE / TRUNCATE.
     * Foreign key di tabel lain (coa_id, coa_pembayaran_id) tetap valid
     * karena hanya nama tabel dan kolom yang berubah.
     */
    public function up(): void
    {
        // ── Step 1: Rename tabel ──────────────────────────────────────────
        Schema::rename('coas', 'akun');

        // ── Step 2: Tambah kolom baru & rename kolom ─────────────────────
        Schema::table('akun', function (Blueprint $table) {
            // Tambah header_akun (setara tipe_akun lama)
            $table->string('header_akun')->nullable()->after('id');
            // Tambah no_akun (akan diisi dari kode_akun)
            $table->string('no_akun')->nullable()->after('header_akun');
        });

        // ── Step 3: Migrasi data kode_akun → no_akun & tipe_akun → header_akun ──
        DB::statement('UPDATE akun SET no_akun = kode_akun, header_akun = tipe_akun');

        // ── Step 4: Hapus kolom lama yang sudah dipindah ─────────────────
        // (kode_akun dan tipe_akun tetap ada dulu untuk backward compat FK)
        // Kita tambahkan unique constraint pada no_akun
        Schema::table('akun', function (Blueprint $table) {
            $table->unique('no_akun', 'akun_no_akun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            $table->dropUnique('akun_no_akun_unique');
            $table->dropColumn(['header_akun', 'no_akun']);
        });

        Schema::rename('akun', 'coas');
    }
};
