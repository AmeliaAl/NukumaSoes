<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->decimal('total_hpp', 15, 2)
                ->default(0)
                ->after('total')
                ->comment('Total HPP = SUM(subtotal_hpp) dari detail penjualan');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->dropColumn('total_hpp');
        });
    }
};
