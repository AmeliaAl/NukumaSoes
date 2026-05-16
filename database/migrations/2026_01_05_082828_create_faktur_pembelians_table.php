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
        Schema::create('faktur_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_vendor')->constrained('vendor')->onDelete('restrict');
            $table->string('no_faktur', 50)->unique();
            $table->date('tanggal_faktur');
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->enum('status', ['belum_dibayar', 'belum_lunas', 'lunas'])->default('belum_dibayar');
            $table->decimal('biaya_lain', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur_pembelian');
    }
};
