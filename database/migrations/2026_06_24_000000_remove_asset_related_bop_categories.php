<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ASSET_RELATED_PATTERNS = [
        '%mesin%',
        '%penyusutan%',
        '%aset%',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('kategori_bop')) {
            return;
        }

        DB::table('kategori_bop')
            ->where(function ($query) {
                foreach (self::ASSET_RELATED_PATTERNS as $pattern) {
                    $query->orWhere('nama_kategori', 'like', $pattern);
                }
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('biaya_overhead_pabrik')
                    ->whereColumn('biaya_overhead_pabrik.id_kategori_bop', 'kategori_bop.id_kategori_bop');
            })
            ->delete();

        DB::table('kategori_bop')->where('nama_kategori', 'Gas')->update([
            'keterangan' => 'Biaya gas untuk operasional mesin/oven produksi. Nama mesin ditulis pada keterangan transaksi.',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Tidak mengembalikan kategori mesin/aset agar batas lingkup produksi tetap terjaga.
    }
};
