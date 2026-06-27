<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_umum', 'ref')) {
                $table->string('ref')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('jurnal_umum', 'debit')) {
                $table->decimal('debit', 15, 2)->default(0)->after('ref');
            }
            if (!Schema::hasColumn('jurnal_umum', 'kredit')) {
                $table->decimal('kredit', 15, 2)->default(0)->after('debit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            foreach (['ref', 'debit', 'kredit'] as $col) {
                if (Schema::hasColumn('jurnal_umum', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
