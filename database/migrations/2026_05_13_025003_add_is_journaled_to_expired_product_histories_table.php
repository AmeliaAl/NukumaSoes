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
            $table->boolean('is_journaled')->default(false)->after('sisa_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expired_product_histories', function (Blueprint $table) {
            $table->dropColumn('is_journaled');
        });
    }
};
