<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tagihan_konsinyasi', function (Blueprint $table) {
            $table->decimal('sisa_tagihan', 15, 2)
                ->default(0)
                ->after('total_terbayar');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_konsinyasi', function (Blueprint $table) {
            $table->dropColumn('sisa_tagihan');
        });
    }
};
