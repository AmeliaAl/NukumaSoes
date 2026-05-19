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
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->string('no_batch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->dropColumn(['harga', 'total_harga', 'no_batch']);
        });
    }
};
