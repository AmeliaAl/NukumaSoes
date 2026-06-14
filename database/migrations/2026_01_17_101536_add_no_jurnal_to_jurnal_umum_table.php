<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->string('no_jurnal')->after('id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('jurnal_umum', function (Blueprint $table) {
            $table->dropColumn('no_jurnal');
        });
    }

};
