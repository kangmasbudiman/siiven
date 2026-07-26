<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerawatanBarangsTable extends Migration
{
    public function up()
    {
        Schema::create('perawatan_barangs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('stock_barang_id');
            $table->foreign('stock_barang_id')
                  ->references('id')
                  ->on('stock_barangs')
                  ->cascadeOnDelete();

            // Petugas teknisi yang melakukan perawatan (string karena user.idUser string & beda charset)
            $table->string('teknisi_id', 10);

            $table->date('tanggal');
            $table->time('jam');

            $table->enum('jenis_perawatan', [
                'pembersihan',
                'preventive',
                'corrective',
                'kalibrasi',
                'lainnya',
            ])->default('pembersihan');

            $table->enum('status', [
                'selesai',
                'pending',
                'bermasalah',
            ])->default('selesai');

            $table->text('keterangan')->nullable();
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_sesudah')->nullable();

            // TTD teknisi: base64 PNG dari signature pad
            $table->longText('ttd_teknisi')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['stock_barang_id', 'tanggal']);
            $table->index('teknisi_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawatan_barangs');
    }
}
