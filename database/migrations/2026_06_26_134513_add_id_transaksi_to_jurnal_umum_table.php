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
        Schema::table('jurnal_umum', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_umum', 'id_transaksi')) {
                $table->string('id_transaksi')->nullable()->index()->after('ref_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_umum', 'id_transaksi')) {
                $table->dropColumn('id_transaksi');
            }
        });
    }
};
