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
            $table->integer('jumlah_pack')->nullable()->after('jumlah_masuk');
            $table->decimal('bbb', 15, 2)->nullable()->after('jumlah_pack');
            $table->decimal('btkl', 15, 2)->nullable()->after('bbb');
            $table->decimal('bop', 15, 2)->nullable()->after('btkl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pack', 'bbb', 'btkl', 'bop']);
        });
    }
};
