<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shifts', function (Blueprint $table) {
             $table->id();
            $table->string('user_id', 10)->charset('latin1')->collation('latin1_swedish_ci');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->boolean('is_active')->default(true);
            $table->string('assigned_by', 10)->charset('latin1')->collation('latin1_swedish_ci');
            $table->timestamps();

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
        Schema::dropIfExists('user_shifts');
    }
}
