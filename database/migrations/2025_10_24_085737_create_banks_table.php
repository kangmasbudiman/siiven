<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
             $table->id();
            $table->string('bank_name'); // BCA, Mandiri, BNI, dll
            $table->string('account_number')->unique(); // No rekening
            $table->string('account_name'); // Nama pemilik rekening
            $table->string('branch')->nullable(); // Cabang bank
            $table->text('notes')->nullable(); // Keterangan tambahan
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
        Schema::dropIfExists('banks');
    }
}
