<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrasi data lama Permintaan
        $permintaans = DB::table('permintaan_bahan_baku')->get();
        foreach ($permintaans as $p) {
            DB::table('permintaan_bahan_baku_detail')->insert([
                'id_permintaan_bahan' => $p->id_permintaan_bahan,
                'id_bahan' => $p->id_bahan,
                'jumlah_permintaan' => $p->jumlah_permintaan,
                'jumlah_diterima' => $p->jumlah_diterima,
                'status_penerimaan' => $p->status_penerimaan,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ]);
        }

        // 2. Drop kolom dari Permintaan
        Schema::table('permintaan_bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['id_bahan']);
            $table->dropColumn(['id_bahan', 'jumlah_permintaan', 'jumlah_diterima', 'harga_per_satuan', 'status_penerimaan']);
        });

        // 3. Migrasi data lama Penerimaan
        $penerimaans = DB::table('penerimaan_bahan_baku')->get();
        foreach ($penerimaans as $p) {
            DB::table('penerimaan_bahan_baku_detail')->insert([
                'id_penerimaan' => $p->id_penerimaan,
                'id_bahan' => $p->id_bahan,
                'jumlah_diterima' => $p->jumlah_diterima,
                'harga_per_satuan' => $p->harga_per_satuan,
                'total_biaya' => $p->total_biaya,
                'created_at' => $p->created_at,
                'updated_at' => $p->updated_at,
            ]);
        }

        // 4. Drop kolom dari Penerimaan
        Schema::table('penerimaan_bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['id_bahan']);
            $table->dropColumn(['id_bahan', 'jumlah_diterima', 'harga_per_satuan', 'total_biaya']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add columns back to Permintaan
        Schema::table('permintaan_bahan_baku', function (Blueprint $table) {
            $table->foreignId('id_bahan')->nullable()->constrained('bahan_baku', 'id_bahan');
            $table->decimal('jumlah_permintaan', 15, 2)->default(0);
            $table->decimal('jumlah_diterima', 15, 2)->default(0);
            $table->decimal('harga_per_satuan', 15, 2)->default(0);
            $table->enum('status_penerimaan', ['belum', 'partial', 'completed'])->default('belum');
        });

        // Restore Data Permintaan
        $details = DB::table('permintaan_bahan_baku_detail')->get();
        foreach ($details as $d) {
            DB::table('permintaan_bahan_baku')
              ->where('id_permintaan_bahan', $d->id_permintaan_bahan)
              ->update([
                  'id_bahan' => $d->id_bahan,
                  'jumlah_permintaan' => $d->jumlah_permintaan,
                  'jumlah_diterima' => $d->jumlah_diterima,
                  'status_penerimaan' => $d->status_penerimaan
              ]);
        }

        // Add columns back to Penerimaan
        Schema::table('penerimaan_bahan_baku', function (Blueprint $table) {
            $table->foreignId('id_bahan')->nullable()->constrained('bahan_baku', 'id_bahan');
            $table->decimal('jumlah_diterima', 15, 2)->default(0);
            $table->decimal('harga_per_satuan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
        });

        // Restore Data Penerimaan
        $pDetails = DB::table('penerimaan_bahan_baku_detail')->get();
        foreach ($pDetails as $d) {
            DB::table('penerimaan_bahan_baku')
              ->where('id_penerimaan', $d->id_penerimaan)
              ->update([
                  'id_bahan' => $d->id_bahan,
                  'jumlah_diterima' => $d->jumlah_diterima,
                  'harga_per_satuan' => $d->harga_per_satuan,
                  'total_biaya' => $d->total_biaya
              ]);
        }
    }
};
