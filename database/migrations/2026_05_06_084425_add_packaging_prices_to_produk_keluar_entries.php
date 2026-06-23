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
            $table->decimal('harga_pouch', 15, 2)->nullable()->default(0);
            $table->decimal('harga_ecofam', 15, 2)->nullable()->default(0);
            $table->decimal('harga_family', 15, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->dropColumn(['harga_pouch', 'harga_ecofam', 'harga_family']);
        });
    }
};
