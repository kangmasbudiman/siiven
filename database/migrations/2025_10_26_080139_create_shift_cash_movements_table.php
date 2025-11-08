<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftCashMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_cash_movements', function (Blueprint $table) {
                      $table->id();
            $table->unsignedBigInteger('shift_session_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->enum('direction', ['IN','OUT']);
            $table->decimal('amount', 15, 2);
            $table->string('account_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('shift_session_id')->references('id')->on('shift_sessions')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            // sesuaikan nama PK users jika tabel usersmu memakai idUser
            $table->foreign('created_by')->references('idUser')->on('user')->nullOnDelete();
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_cash_movements');
    }
}
