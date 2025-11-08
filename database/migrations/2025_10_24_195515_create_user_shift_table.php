<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shift', function (Blueprint $table) {
           $table->id();
            $table->string('user_id', 10); // Sesuai dengan idUser di tabel user
            $table->foreignId('shift_id')->constrained('shifts');
            $table->boolean('is_active')->default(true);
            $table->string('assigned_by', 10); // User yang assign shift
            $table->timestamps();

            // Set engine dan collation to match the `user` table
            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            // Foreign key constraints
            $table->foreign('user_id')->references('idUser')->on('user');
            $table->foreign('assigned_by')->references('idUser')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_shift');
    }
}
