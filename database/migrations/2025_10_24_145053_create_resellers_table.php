<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->string('namereseller'); // Nama reseller
            $table->string('code')->unique(); // Kode unik reseller
            $table->string('phone')->nullable(); // No telepon
            $table->string('email')->nullable(); // Email
            $table->text('address')->nullable(); // Alamat
            $table->decimal('initial_balance', 15, 2)->default(0); // Saldo awal/saldo hutang
            $table->text('notes')->nullable(); // Keterangan
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });

        // Tabel pivot untuk harga khusus reseller per aplikasi
        Schema::create('application_reseller', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained('aplikasis')->onDelete('cascade');
            $table->decimal('special_price', 10, 2); // Harga khusus untuk reseller
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['reseller_id', 'application_id']); // Pastikan kombinasi unik
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('application_reseller');
        Schema::dropIfExists('resellers');
    }
}
