<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('hpp', 15, 2)->nullable()->after('harga');
            $table->decimal('margin', 15, 2)->nullable()->after('hpp');
            $table->decimal('harga_dasar_jual', 15, 2)->nullable()->after('margin');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['hpp', 'margin', 'harga_dasar_jual']);
        });
    }
};
