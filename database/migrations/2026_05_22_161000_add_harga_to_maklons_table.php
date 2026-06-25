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
        Schema::table('maklons', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->nullable()->after('masa_simpan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maklons', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};
