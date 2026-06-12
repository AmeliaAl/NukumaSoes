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
        Schema::table('mitras', function (Blueprint $table) {
            $table->decimal('soes_kemasan_pouch_trendy', 15, 2)->nullable()->after('jasa_kirim');
            $table->decimal('soes_kemasan_toples_ecofam', 15, 2)->nullable()->after('soes_kemasan_pouch_trendy');
            $table->decimal('soes_kemasan_toples_family', 15, 2)->nullable()->after('soes_kemasan_toples_ecofam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['soes_kemasan_pouch_trendy', 'soes_kemasan_toples_ecofam', 'soes_kemasan_toples_family']);
        });
    }
};
