<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->decimal('margin', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('persediaan_entries', function (Blueprint $table) {
            $table->decimal('margin', 5, 2)->nullable()->change();
        });
    }
};
