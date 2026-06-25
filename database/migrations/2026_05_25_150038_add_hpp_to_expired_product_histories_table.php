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
        Schema::table('expired_product_histories', function (Blueprint $table) {
            $table->decimal('hpp', 15, 2)->nullable()->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expired_product_histories', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });
    }
};
