<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailUsulanPembeliansTable extends Migration
{
    public function up()
    {
        Schema::create('detail_usulan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_pembelian_id')->constrained('usulan_pembelians')->cascadeOnDelete();
            $table->tinyInteger('no_urut');
            $table->text('keterangan'); // deskripsi barang yang diminta
            $table->integer('jumlah');
            $table->bigInteger('harga_satuan')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_usulan_pembelians');
    }
}
