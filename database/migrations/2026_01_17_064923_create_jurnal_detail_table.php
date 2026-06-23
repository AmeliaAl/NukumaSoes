<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Migration ini tidak perlu dijalankan karena sudah ada migration sebelumnya:
        // 2026_01_11_142027_create_jurnal_details_table yang sudah membuat table ini
        // dengan struktur: id_jurnal, no_akun (foreign ke akun.id), deskripsi, debit, credit
    }

    public function down(): void
    {
        // Schema::dropIfExists('jurnal_detail');
    }
};
