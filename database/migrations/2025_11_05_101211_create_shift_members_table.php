<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_members', function (Blueprint $table) {
            $table->id();
        $table->foreignId('shift_session_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
        $table->timestamp('joined_at')->nullable();
        $table->timestamp('left_at')->nullable();
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
        Schema::dropIfExists('shift_members');
    }
}
