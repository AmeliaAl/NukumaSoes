<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('upah_per_jam', 'upah_per_minggu');
            $table->string('bagian', 50)->nullable()->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('tenaga_kerja', function (Blueprint $table) {
            $table->renameColumn('upah_per_minggu', 'upah_per_jam');
            $table->dropColumn('bagian');
        });
    }
};
