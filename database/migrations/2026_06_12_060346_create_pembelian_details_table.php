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
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // Migrate existing data
        $pembelians = \Illuminate\Support\Facades\DB::table('pembelians')->get();
        foreach ($pembelians as $p) {
            if (isset($p->bahan_baku_id)) {
                \Illuminate\Support\Facades\DB::table('pembelian_details')->insert([
                    'pembelian_id' => $p->id,
                    'bahan_baku_id' => $p->bahan_baku_id,
                    'qty' => $p->qty ?? 1,
                    'harga' => $p->harga ?? 0,
                    'subtotal' => ($p->qty ?? 1) * ($p->harga ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
    }
};
