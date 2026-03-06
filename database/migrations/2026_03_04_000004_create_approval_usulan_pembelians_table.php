<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalUsulanPembeliansTable extends Migration
{
    public function up()
    {
        Schema::create('approval_usulan_pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_pembelian_id')->constrained('usulan_pembelians')->cascadeOnDelete();
            $table->tinyInteger('level'); // 1=Pemeriksa, 2=Konfirmator, 3=Kabag, 4=Direktur
            $table->string('user_id', 10);
            $table->enum('status', ['approved', 'rejected']);
            $table->text('catatan')->nullable();
            $table->string('token')->unique(); // SHA256 untuk QR code
            $table->timestamp('approved_at');
            $table->timestamps();

            // Tidak FK ke user table karena charset latin1 vs utf8mb4 incompatible
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_usulan_pembelians');
    }
}
