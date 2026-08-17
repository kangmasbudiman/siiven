<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_stoks', function (Blueprint $table) {
            $table->id();

            $table->enum('tipe', ['masuk', 'keluar']);

            $table->foreignId('barang_id')
                  ->constrained('barangs');

            $table->unsignedBigInteger('ruangan_asal_id')->nullable();
            $table->unsignedBigInteger('ruangan_tujuan_id')->nullable();

            $table->unsignedInteger('jumlah');

            // Tanpa FK: tabel user pakai charset latin1 & idUser varchar tanpa index
            $table->unsignedBigInteger('user_id');

            $table->date('tanggal');
            $table->string('keterangan', 255)->nullable();

            $table->timestamps();

            $table->foreign('ruangan_asal_id')
                  ->references('id')->on('ruangans');

            $table->foreign('ruangan_tujuan_id')
                  ->references('id')->on('ruangans');

            $table->index(['barang_id', 'tanggal']);
            $table->index('tipe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_stoks');
    }
}
