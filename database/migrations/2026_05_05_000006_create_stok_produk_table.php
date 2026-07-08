<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_produk', function (Blueprint $table) {
            $table->id('id_stok_produk');

            // FK ke job order yang menghasilkan stok ini
            $table->foreignId('id_permintaan_produksi')
                  ->constrained('permintaan_produksi', 'id_permintaan_produksi')
                  ->onDelete('cascade');

            // FK ke produk
            $table->unsignedBigInteger('id_produk');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');

            // Tipe stok: WIP kulit atau barang jadi
            $table->enum('tipe_stok', ['wip_kulit', 'barang_jadi'])
                  ->comment('wip_kulit=barang setengah jadi (per gram), barang_jadi=produk akhir');

            // Jumlah & satuan
            $table->decimal('jumlah', 12, 2)->comment('Gram untuk kulit, pcs/box untuk jadi');
            $table->string('satuan', 20)->comment('gram, pcs, box, dll');

            // Harga pokok dari job order
            $table->decimal('harga_pokok_per_unit', 15, 2)->default(0);
            $table->decimal('total_nilai', 15, 2)->default(0)
                  ->comment('jumlah × harga_pokok_per_unit');

            // Status stok
            $table->enum('status', ['tersedia', 'terpakai', 'habis'])->default('tersedia');

            $table->date('tanggal_masuk');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('id_produk');
            $table->index('tipe_stok');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_produk');
    }
};
