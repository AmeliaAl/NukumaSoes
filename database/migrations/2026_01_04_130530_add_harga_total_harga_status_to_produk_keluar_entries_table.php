<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->after('jumlah_keluar');
            $table->decimal('total_harga', 15, 2)->after('harga');
            $table->string('status')->after('total_harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->dropColumn(['harga', 'total_harga', 'status']);
        });
    }
};
