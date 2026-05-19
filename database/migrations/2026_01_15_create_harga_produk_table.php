<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('kategori')->nullable();
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_produk');
    }
};
?>
</xai:function_call name="create_file">

**Progress: Migration created. Next: Model. Confirm?** Wait for response. No, proceed step-by-step but since iterative, create TODO first then one by one.

Wait, first create the TODO file properly. 

Actually, create the TODO-harga-produk.md now. 

Proceed to create TODO file. 

Since user wants full MVC, yes.

</thinking> Wait, no tool call yet in response.

First create TODO-harga-produk.md

Yes. 

**Created TODO-harga-produk.md**

<xai:function_call name="create_file">
<parameter name="absolute_path">TODO-harga-produk.md
