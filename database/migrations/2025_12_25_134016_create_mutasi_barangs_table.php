<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutasiBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutasi_barangs', function (Blueprint $table) {
            $table->id();
          
    $table->foreignId('barang_id')
          ->constrained('barangs');

    $table->unsignedBigInteger('ruangan_asal_id')->nullable();
    $table->unsignedBigInteger('ruangan_tujuan_id');

    $table->integer('jumlah');

    $table->unsignedBigInteger('user_id');
    $table->text('keterangan')->nullable();

    $table->timestamps();

    $table->foreign('ruangan_asal_id')
          ->references('id')->on('ruangans');

    $table->foreign('ruangan_tujuan_id')
          ->references('id')->on('ruangans');

    $table->foreign('user_id')
          ->references('idUser')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mutasi_barangs');
    }
}
