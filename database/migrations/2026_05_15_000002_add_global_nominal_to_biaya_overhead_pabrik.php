<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            // Total tagihan penuh sebelum dibagi ke batch (untuk referensi/audit)
            $table->decimal('total_nominal_global', 15, 2)
                  ->nullable()
                  ->after('nominal')
                  ->comment('Total tagihan BOP sebelum dibagi. Null = BOP langsung (nominal sudah final)');

            // Total batch dari semua job yang berbagi BOP ini
            $table->unsignedSmallInteger('jumlah_batch_terlibat')
                  ->nullable()
                  ->after('total_nominal_global')
                  ->comment('Total jumlah batch dari semua job order yang ikut berbagi BOP ini');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->dropColumn(['total_nominal_global', 'jumlah_batch_terlibat']);
        });
    }
};
