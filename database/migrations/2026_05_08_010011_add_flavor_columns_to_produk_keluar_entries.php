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
            $table->string('rasa_pouch')->nullable()->after('jumlah_pouch');
            $table->string('rasa_ecofam')->nullable()->after('jumlah_ecofam');
            $table->string('rasa_family')->nullable()->after('jumlah_family');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_keluar_entries', function (Blueprint $table) {
            $table->dropColumn(['rasa_pouch', 'rasa_ecofam', 'rasa_family']);
        });
    }
};
