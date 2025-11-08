<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAplikasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aplikasis', function (Blueprint $table) {
 $table->id();
            $table->string('nameaplication'); // Yoho, Hiva, dll
            $table->string('code')->unique(); // Kode unik: YOHO, HIVA
            $table->decimal('normal_price', 10, 2); // Harga normal
            $table->text('description')->nullable(); // Deskripsi aplikasi
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aplikasis');
    }
}
