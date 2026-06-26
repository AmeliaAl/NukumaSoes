<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $water = DB::table('akun')->where('kode_akun', '713')->first();
        if (!$water) {
            DB::table('akun')->insert([
                'kode_akun' => '713',
                'nama_akun' => 'Biaya Air',
                'tipe_akun' => 'beban',
                'saldo_normal' => 'debit',
                'saldo' => 0,
                'status' => 'aktif',
                'keterangan' => 'Akun BOP air produksi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('akun')->where('kode_akun', '712')->update([
            'nama_akun' => 'Biaya Listrik',
            'updated_at' => now(),
        ]);

        if (!Schema::hasColumn('kategori_bop', 'id_akun')) {
            Schema::table('kategori_bop', function (Blueprint $table) {
                $table->unsignedBigInteger('id_akun')->nullable()->after('nama_kategori');
            });
        }

        $mapping = [
            'Listrik' => '712',
            'Air' => '713',
            'Gas' => '612',
            'Penyusutan Mesin' => '616',
            'Penyusutan Bangunan' => '535',
            'Asuransi Pabrik' => '535',
            'Bahan Penolong' => '620',
            'Pemeliharaan Mesin & Alat' => '616',
            'Toples & Kemasan (BOP)' => '620',
            'BTKTL (Tenaga Kerja Tidak Langsung)' => '601',
            'Lainnya' => '535',
        ];

        foreach ($mapping as $category => $accountCode) {
            $accountId = DB::table('akun')->where('kode_akun', $accountCode)->value('id_akun');
            DB::table('kategori_bop')->where('nama_kategori', $category)->update([
                'id_akun' => $accountId,
                'updated_at' => now(),
            ]);
        }

        // Pisahkan kembali transaksi aktual air yang sebelumnya sempat digabung ke 712.
        $electricityId = DB::table('akun')->where('kode_akun', '712')->value('id_akun');
        $waterId = DB::table('akun')->where('kode_akun', '713')->value('id_akun');
        $airCategoryId = DB::table('kategori_bop')->where('nama_kategori', 'Air')->value('id_kategori_bop');
        $airJurnalIds = DB::table('biaya_overhead_pabrik')
            ->where('id_kategori_bop', $airCategoryId)
            ->whereNotNull('id_jurnal_aktual')
            ->pluck('id_jurnal_aktual')
            ->merge(DB::table('jurnal_umum')
                ->where('tipe_referensi', 'pengeluaran_bop_aktual')
                ->whereRaw("LOWER(keterangan) REGEXP '(^|[^a-z])air([^a-z]|$)'")
                ->pluck('id_jurnal'))
            ->unique();

        if ($electricityId && $waterId && $airJurnalIds->isNotEmpty()) {
            DB::table('jurnal_umum_detail')
                ->where('id_akun', $electricityId)
                ->whereIn('id_jurnal', $airJurnalIds)
                ->update(['id_akun' => $waterId]);
        }

        foreach ([$electricityId, $waterId] as $accountId) {
            if (!$accountId) continue;
            $balance = DB::table('jurnal_umum_detail')->where('id_akun', $accountId)
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(kredit), 0) AS saldo')
                ->value('saldo');
            DB::table('akun')->where('id_akun', $accountId)->update(['saldo' => $balance]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kategori_bop', 'id_akun')) {
            Schema::table('kategori_bop', function (Blueprint $table) {
                $table->dropColumn('id_akun');
            });
        }
    }
};
