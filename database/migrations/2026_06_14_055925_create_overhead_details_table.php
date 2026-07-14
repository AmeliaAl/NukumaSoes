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
        Schema::create('overhead_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overhead_id')->constrained('overheads')->onDelete('cascade');
            $table->foreignId('coa_id')->constrained('coas')->onDelete('cascade');
            $table->foreignId('coa_pembayaran_id')->nullable()->constrained('coas')->onDelete('set null');
            $table->text('keterangan');
            $table->integer('nominal');
            $table->timestamps();
        });

        // Migrate existing data
        $overheads = \Illuminate\Support\Facades\DB::table('overheads')->get();
        foreach ($overheads as $o) {
            if (isset($o->coa_id)) {
                \Illuminate\Support\Facades\DB::table('overhead_details')->insert([
                    'overhead_id' => $o->id,
                    'coa_id' => $o->coa_id,
                    'coa_pembayaran_id' => $o->coa_pembayaran_id ?? null,
                    'keterangan' => $o->keterangan ?? '',
                    'nominal' => $o->nominal ?? 0,
                    'created_at' => $o->created_at ?? now(),
                    'updated_at' => $o->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overhead_details');
    }
};
