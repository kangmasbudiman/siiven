<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_sessions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('shift_id'); // relasi ke shift (Pagi, Siang, Malam)
        $table->string('session_code')->unique();
        $table->timestamp('start_time')->nullable();
        $table->timestamp('end_time')->nullable();
        $table->unsignedBigInteger('opened_by');
        $table->unsignedBigInteger('closed_by')->nullable();
        $table->enum('status', ['ACTIVE', 'CLOSED'])->default('ACTIVE');
        $table->timestamps();

        $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
        $table->foreign('opened_by')->references('idUser')->on('user');
        $table->foreign('closed_by')->references('idUser')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_sessions');
    }
}
