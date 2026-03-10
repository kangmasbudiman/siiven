<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampiranUsulanPembeliansTable extends Migration
{
    public function up()
    {
        Schema::create('lampiran_usulan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usulan_pembelian_id');
            $table->string('nama_file');       // original filename
            $table->string('path');            // storage path
            $table->string('mime_type', 50)->nullable();
            $table->unsignedBigInteger('ukuran')->nullable(); // bytes
            $table->timestamps();

            $table->foreign('usulan_pembelian_id')
                  ->references('id')
                  ->on('usulan_pembelians')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lampiran_usulan_pembelians');
    }
}
