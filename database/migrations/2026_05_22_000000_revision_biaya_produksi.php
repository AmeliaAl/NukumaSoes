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
        $isSqlite = DB::getDriverName() === 'sqlite';

        // 1. Modify stok_produk table
        Schema::table('stok_produk', function (Blueprint $table) {
            if (!Schema::hasColumn('stok_produk', 'sisa_stok')) {
                $table->decimal('sisa_stok', 15, 2)->default(0.00)->after('jumlah');
            }
            if (!Schema::hasColumn('stok_produk', 'status')) {
                $table->string('status', 20)->default('tersedia')->after('total_nilai'); // 'tersedia', 'habis'
            }
        });

        // Initialize sisa_stok for existing stok_produk
        DB::statement("UPDATE stok_produk SET sisa_stok = jumlah WHERE status = 'tersedia' AND (sisa_stok = 0 OR sisa_stok IS NULL)");

        // 2. Modify bom_bahan table
        // Drop unique constraint first
        if (! $isSqlite) {
            Schema::table('bom_bahan', function (Blueprint $table) {
                $table->dropUnique('bom_bahan_id_produk_id_bahan_unique');
            });
        }

        // Drop foreign key, change to nullable, re-add foreign key, and add id_produk_wip
        if (! $isSqlite) {
            Schema::table('bom_bahan', function (Blueprint $table) {
                $table->dropForeign('bom_bahan_id_bahan_foreign');
            });
        }

        Schema::table('bom_bahan', function (Blueprint $table) use ($isSqlite) {
            if (! $isSqlite) {
                $table->unsignedBigInteger('id_bahan')->nullable()->change();
            }
            if (! Schema::hasColumn('bom_bahan', 'id_produk_wip')) {
                $table->unsignedBigInteger('id_produk_wip')->nullable()->after('id_bahan');
            }
            
            if (! $isSqlite) {
                $table->foreign('id_bahan')->references('id_bahan')->on('bahan_baku')->onDelete('cascade');
                $table->foreign('id_produk_wip')->references('id_produk')->on('produk')->onDelete('cascade');
            }
        });

        // 3. Modify pemakaian_bahan_baku table
        if (! $isSqlite) {
            Schema::table('pemakaian_bahan_baku', function (Blueprint $table) {
                $table->dropForeign('pemakaian_bahan_baku_id_bahan_foreign');
                $table->dropForeign('pemakaian_bahan_baku_id_stok_foreign');
            });
        }

        Schema::table('pemakaian_bahan_baku', function (Blueprint $table) use ($isSqlite) {
            if (! $isSqlite) {
                $table->unsignedBigInteger('id_bahan')->nullable()->change();
                $table->unsignedBigInteger('id_stok')->nullable()->change(); // Stok bahan baku
            }
            
            if (! Schema::hasColumn('pemakaian_bahan_baku', 'id_produk_wip')) {
                $table->unsignedBigInteger('id_produk_wip')->nullable()->after('id_bahan');
            }
            if (! Schema::hasColumn('pemakaian_bahan_baku', 'id_stok_produk')) {
                $table->unsignedBigInteger('id_stok_produk')->nullable()->after('id_stok'); // Stok WIP (FIFO)
            }

            if (! $isSqlite) {
                $table->foreign('id_bahan')->references('id_bahan')->on('bahan_baku')->onDelete('cascade');
                $table->foreign('id_stok')->references('id_stok')->on('stok_bahan_baku')->onDelete('cascade');
                $table->foreign('id_produk_wip')->references('id_produk')->on('produk')->onDelete('cascade');
                $table->foreign('id_stok_produk')->references('id_stok_produk')->on('stok_produk')->onDelete('cascade');
            }
        });

        // 4. Modify bahan_baku table (add jenis_bahan)
        Schema::table('bahan_baku', function (Blueprint $table) {
            if (!Schema::hasColumn('bahan_baku', 'jenis_bahan')) {
                $table->string('jenis_bahan', 20)->default('langsung')->after('status'); // 'langsung', 'tidak_langsung' (kemasan)
            }
        });

        // Update existing packaging items to 'tidak_langsung'
        DB::statement("UPDATE bahan_baku SET jenis_bahan = 'tidak_langsung' WHERE nama_bahan LIKE '%kemasan%' OR nama_bahan LIKE '%toples%'");

        // 5. Create kategori_bop table
        Schema::create('kategori_bop', function (Blueprint $table) {
            $table->id('id_kategori_bop');
            $table->string('nama_kategori', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Seed initial categories
        $categories = [
            ['nama_kategori' => 'Listrik', 'keterangan' => 'Biaya listrik pabrik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Air', 'keterangan' => 'Biaya air bersih produksi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Gas', 'keterangan' => 'Biaya gas elpiji/produksi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Penyusutan Mesin', 'keterangan' => 'Penyusutan mesin-mesin produksi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Penyusutan Bangunan', 'keterangan' => 'Penyusutan bangunan pabrik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Asuransi Pabrik', 'keterangan' => 'Premi asuransi aset pabrik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Bahan Penolong', 'keterangan' => 'Bahan penolong minor yang tidak dilacak di BOM', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Pemeliharaan Mesin & Alat', 'keterangan' => 'Maintenance berkala', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Toples & Kemasan (BOP)', 'keterangan' => 'Alokasi bahan tidak langsung / kemasan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'BTKTL (Tenaga Kerja Tidak Langsung)', 'keterangan' => 'Alokasi upah pekerja tidak langsung', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Lainnya', 'keterangan' => 'Overhead lain-lain', 'created_at' => now(), 'updated_at' => now()]
        ];
        DB::table('kategori_bop')->insert($categories);

        // 6. Modify biaya_overhead_pabrik table
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            if (!Schema::hasColumn('biaya_overhead_pabrik', 'id_kategori_bop')) {
                $table->unsignedBigInteger('id_kategori_bop')->nullable()->after('id_admin');
                $table->foreign('id_kategori_bop')->references('id_kategori_bop')->on('kategori_bop')->onDelete('set null');
            }
        });

        // Map existing overhead records to dynamic categories
        $oldOverheads = DB::table('biaya_overhead_pabrik')->get();
        foreach ($oldOverheads as $oh) {
            $catId = DB::table('kategori_bop')
                ->where('nama_kategori', 'like', '%' . $oh->jenis_overhead . '%')
                ->value('id_kategori_bop');
            if ($catId) {
                DB::table('biaya_overhead_pabrik')
                    ->where('id_overhead', $oh->id_overhead)
                    ->update(['id_kategori_bop' => $catId]);
            } else {
                // Default to 'Lainnya' if not found
                $lainnyaId = DB::table('kategori_bop')->where('nama_kategori', 'Lainnya')->value('id_kategori_bop');
                DB::table('biaya_overhead_pabrik')
                    ->where('id_overhead', $oh->id_overhead)
                    ->update(['id_kategori_bop' => $lainnyaId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse biaya_overhead_pabrik
        Schema::table('biaya_overhead_pabrik', function (Blueprint $table) {
            $table->dropForeign(['id_kategori_bop']);
            $table->dropColumn('id_kategori_bop');
        });

        // Drop kategori_bop
        Schema::dropIfExists('kategori_bop');

        // Reverse bahan_baku
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('jenis_bahan');
        });

        // Reverse pemakaian_bahan_baku
        Schema::table('pemakaian_bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['id_stok_produk']);
            $table->dropForeign(['id_produk_wip']);
            $table->dropColumn(['id_stok_produk', 'id_produk_wip']);
            
            $table->dropForeign(['id_bahan']);
            $table->dropForeign(['id_stok']);
        });

        Schema::table('pemakaian_bahan_baku', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bahan')->change();
            $table->unsignedBigInteger('id_stok')->change();
            
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan_baku')->onDelete('cascade');
            $table->foreign('id_stok')->references('id_stok')->on('stok_bahan_baku')->onDelete('cascade');
        });

        // Reverse bom_bahan
        Schema::table('bom_bahan', function (Blueprint $table) {
            $table->dropForeign(['id_produk_wip']);
            $table->dropColumn('id_produk_wip');
            
            $table->dropForeign(['id_bahan']);
        });

        Schema::table('bom_bahan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bahan')->change();
            $table->foreign('id_bahan')->references('id_bahan')->on('bahan_baku')->onDelete('cascade');
            
            $table->unique(['id_produk', 'id_bahan'], 'bom_bahan_id_produk_id_bahan_unique');
        });

        // Reverse stok_produk
        Schema::table('stok_produk', function (Blueprint $table) {
            $table->dropColumn(['sisa_stok', 'status']);
        });
    }
};
