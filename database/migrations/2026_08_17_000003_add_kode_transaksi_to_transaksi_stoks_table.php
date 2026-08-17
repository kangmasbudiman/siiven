<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeTransaksiToTransaksiStoksTable extends Migration
{
    public function up()
    {
        Schema::table('transaksi_stoks', function (Blueprint $table) {
            $table->string('kode_transaksi', 30)->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('transaksi_stoks', function (Blueprint $table) {
            $table->dropIndex(['kode_transaksi']);
            $table->dropColumn('kode_transaksi');
        });
    }
}
