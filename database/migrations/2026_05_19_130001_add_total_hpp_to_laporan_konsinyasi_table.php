<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_konsinyasi', function (Blueprint $table) {
            $table->decimal('total_hpp', 15, 2)
                ->default(0)
                ->after('total_laporan')
                ->comment('Total HPP = SUM(subtotal_hpp) dari detail laporan konsinyasi');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_konsinyasi', function (Blueprint $table) {
            $table->dropColumn('total_hpp');
        });
    }
};
