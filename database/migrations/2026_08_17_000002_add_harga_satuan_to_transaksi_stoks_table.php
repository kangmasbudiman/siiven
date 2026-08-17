<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHargaSatuanToTransaksiStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_stoks', function (Blueprint $table) {
            $table->unsignedInteger('harga_satuan')->nullable()->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi_stoks', function (Blueprint $table) {
            $table->dropColumn('harga_satuan');
        });
    }
}
