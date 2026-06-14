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
        Schema::table('overheads', function (Blueprint $table) {
            $table->foreignId('coa_pembayaran_id')
                ->nullable()
                ->constrained('coas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overheads', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['coa_pembayaran_id']);
            $table->dropColumn('coa_pembayaran_id');
        });
    }
};
