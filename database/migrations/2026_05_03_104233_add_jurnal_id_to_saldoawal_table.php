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
        Schema::table('saldoawal', function (Blueprint $table) {
            $table->foreignId('jurnal_id')->nullable()->after('nominal')->constrained('jurnal')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saldoawal', function (Blueprint $table) {
            $table->dropForeign(['jurnal_id']);
            $table->dropColumn('jurnal_id');
        });
    }
};
