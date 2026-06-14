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
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->string('satuan_beli', 20)->nullable()->after('satuan');
            $table->decimal('isi_per_kemasan', 10, 2)->default(1)->after('satuan_beli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['satuan_beli', 'isi_per_kemasan']);
        });
    }
};
