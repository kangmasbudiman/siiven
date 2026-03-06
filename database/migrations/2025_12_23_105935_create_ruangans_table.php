<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuangansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ruangans', function (Blueprint $table) {
             $table->id();

            // Identitas Ruangan
            $table->string('kode_ruangan')->unique();
            $table->string('nama_ruangan');
            // Klasifikasi
            $table->enum('jenis_ruangan', [
                'pelayanan_medis',
                'penunjang',
                'logistik',
                'administratif'
            ]);
            // Lokasi
            $table->string('gedung')->nullable();
            $table->string('lantai')->nullable();
            // Penanggung Jawab
            $table->string('penanggung_jawab')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('kontak')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            // Status
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            // Audit
            $table->timestamps();
            $table->softDeletes();

            // Relasi ke users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ruangans');
    }
}
