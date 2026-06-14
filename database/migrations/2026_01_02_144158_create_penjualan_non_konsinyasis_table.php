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
        Schema::create('penjualan_non_konsinyasi', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal');

            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('no_invoice')->unique();

            $table->decimal('total', 15, 2);

            $table->enum('jenis_pembayaran', ['tunai', 'kredit']);

            $table->date('jatuh_tempo')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_non_konsinyasi');
    }
};
