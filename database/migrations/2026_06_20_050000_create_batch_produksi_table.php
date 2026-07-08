<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_produksi', function (Blueprint $table) {
            $table->id('id_batch_produksi');
            $table->unsignedBigInteger('id_permintaan_produksi');
            $table->unsignedSmallInteger('urutan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('jumlah_target', 15, 2)->default(0);
            $table->decimal('jumlah_hasil', 15, 2)->default(0);
            $table->string('jenis_batch', 20)->default('rencana');
            $table->string('status', 20)->default('rencana');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['id_permintaan_produksi', 'urutan']);
            $table->foreign('id_permintaan_produksi')
                ->references('id_permintaan_produksi')->on('permintaan_produksi')
                ->cascadeOnDelete();
        });

        DB::table('permintaan_produksi')->orderBy('id_permintaan_produksi')->get()->each(function ($job) {
            $count = max(1, (int) ($job->jumlah_batch ?? 1));
            $total = (float) $job->jumlah_produksi;
            $base = floor(($total / $count) * 100) / 100;
            $status = match ($job->status) {
                'selesai' => 'selesai',
                'proses' => 'proses',
                default => 'rencana',
            };

            for ($urutan = 1; $urutan <= $count; $urutan++) {
                $target = $urutan === $count ? round($total - ($base * ($count - 1)), 2) : $base;
                DB::table('batch_produksi')->insert([
                    'id_permintaan_produksi' => $job->id_permintaan_produksi,
                    'urutan' => $urutan,
                    'tanggal_mulai' => $job->tanggal_mulai,
                    'tanggal_selesai' => $job->status === 'selesai' ? $job->tanggal_selesai : null,
                    'jumlah_target' => $target,
                    'jumlah_hasil' => $job->status === 'selesai' ? $target : 0,
                    'jenis_batch' => 'rencana',
                    'status' => $status,
                    'keterangan' => 'Dibentuk dari data jumlah batch lama',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_produksi');
    }
};
