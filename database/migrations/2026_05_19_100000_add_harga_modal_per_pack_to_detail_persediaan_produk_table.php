<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_persediaan_produk', function (Blueprint $table) {
            $table->decimal('harga_modal_per_pack', 15, 2)
                ->default(0)
                ->after('harga')
                ->comment('HPP / harga modal per pack produk jadi');
        });
    }

    public function down(): void
    {
        Schema::table('detail_persediaan_produk', function (Blueprint $table) {
            $table->dropColumn('harga_modal_per_pack');
        });
    }
};
