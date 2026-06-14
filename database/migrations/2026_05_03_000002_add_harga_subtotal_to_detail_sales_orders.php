<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_sales_orders', function (Blueprint $table) {
            $table->integer('harga')->default(0)->after('qty');
            $table->integer('subtotal')->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('detail_sales_orders', function (Blueprint $table) {
            $table->dropColumn(['harga', 'subtotal']);
        });
    }
};
