<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsulanPembeliansTable extends Migration
{
    public function up()
    {
        Schema::create('usulan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_usulan')->unique(); // UPB-2026-001
            $table->date('tanggal_pengajuan');
            $table->unsignedBigInteger('ruangan_id');
            $table->string('nama_penanggung_jawab');
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'draft',
                'diajukan',
                'diperiksa',
                'dikonfirmasi',
                'diketahui',
                'disetujui',
                'ditolak'
            ])->default('draft');
            $table->string('dibuat_oleh', 10);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ruangan_id')->references('id')->on('ruangans');
            // Tidak FK ke user table karena charset latin1 vs utf8mb4 incompatible
        });
    }

    public function down()
    {
        Schema::dropIfExists('usulan_pembelians');
    }
}
