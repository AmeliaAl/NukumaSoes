<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('harga_produk', function (Blueprint $table) {
            $table->string('produk_id')->nullable()->after('kode_produk');
            $table->string('jenis_mitra')->nullable()->after('produk_id');
            $table->decimal('harga', 15, 2)->nullable()->after('jenis_mitra');
        });
    }

    public function down(): void
    {
        Schema::table('harga_produk', function (Blueprint $table) {
            $table->dropColumn(['produk_id', 'jenis_mitra', 'harga']);
        });
    }
};
?>
</xai:function_call name="execute_command">
<parameter name="command">php artisan migrate
