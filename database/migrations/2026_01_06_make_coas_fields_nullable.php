<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->string('header_akun')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('coas', function (Blueprint $table) {
            $table->string('header_akun')->nullable(false)->change();
        });
    }
};

