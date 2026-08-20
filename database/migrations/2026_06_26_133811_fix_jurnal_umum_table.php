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
            if (!Schema::hasColumn('jurnal_umum', 'no_jurnal')) {
                $table->string('no_jurnal')->after('id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            if (Schema::hasColumn('jurnal_umum', 'no_jurnal')) {
                $table->dropColumn('no_jurnal');
            }
        });
    }
};
