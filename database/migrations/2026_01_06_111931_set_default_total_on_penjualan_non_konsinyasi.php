<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->decimal('total', 15, 2)
                ->default(0)
                ->change();

            $table->decimal('total_terbayar', 15, 2)
                ->default(0)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->decimal('total', 15, 2)
                ->change();

            $table->decimal('total_terbayar', 15, 2)
                ->change();
        });
    }
};
