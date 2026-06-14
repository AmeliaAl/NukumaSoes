<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_konsinyasi', function (Blueprint $table) {
            $table->decimal('harga_modal_per_pack', 15, 2)
                ->default(0)
                ->after('harga_konsinyasi')
                ->comment('HPP rata-rata tertimbang dari batch FEFO yang keluar');

            $table->decimal('subtotal_hpp', 15, 2)
                ->default(0)
                ->after('harga_modal_per_pack')
                ->comment('Total HPP = SUM(qty_keluar × harga_modal_per_pack) per batch');
        });
    }

    public function down(): void
    {
        Schema::table('detail_konsinyasi', function (Blueprint $table) {
            $table->dropColumn(['harga_modal_per_pack', 'subtotal_hpp']);
        });
    }
};
