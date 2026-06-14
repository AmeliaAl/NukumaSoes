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
        Schema::table('tagihan_konsinyasi', function (Blueprint $table) {
            $table->date('jatuh_tempo')->after('tanggal_tagihan');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_konsinyasi', function (Blueprint $table) {
            //
        });
    }
};
