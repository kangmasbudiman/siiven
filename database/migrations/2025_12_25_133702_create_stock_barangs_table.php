<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_barangs', function (Blueprint $table) {
            $table->id();
            
    $table->foreignId('barang_id')
          ->constrained('barangs');

    $table->unsignedBigInteger('ruangan_id');

    $table->integer('jumlah')->default(0);

    $table->foreignId('kondisi_id')
          ->constrained('kondisis');

    $table->unsignedBigInteger('user_id'); // pencatat

    $table->timestamps();

    $table->foreign('ruangan_id')
          ->references('id')
          ->on('ruangans');

    $table->foreign('user_id')
          ->references('idUser')
          ->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_barangs');
    }
}
