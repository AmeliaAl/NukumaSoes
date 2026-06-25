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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('bbb', 15, 2)->nullable()->after('harga');
            $table->decimal('btkl', 15, 2)->nullable()->after('bbb');
            $table->decimal('bop', 15, 2)->nullable()->after('btkl');
            $table->decimal('hpp', 15, 2)->nullable()->after('bop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['bbb', 'btkl', 'bop', 'hpp']);
        });
    }
};
