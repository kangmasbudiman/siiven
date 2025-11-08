<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
           $table->id();

    // System tracking
    $table->timestamp('transaction_datetime')->useCurrent(); // otomatis dari server
    $table->unsignedBigInteger('shift_id');
    $table->unsignedBigInteger('admin_id'); // Role A

    // Aplikasi & rate snapshot
    $table->unsignedBigInteger('app_id');
    $table->string('coin_type', 50)->nullable();
    $table->decimal('rate', 12, 2)->nullable();

    // Transaksi utama
    $table->decimal('amount_due', 15, 2); // harga total
    $table->decimal('coin_qty', 15, 2)->nullable(); // jumlah koin dihitung otomatis
    $table->enum('payment_type', ['Lunas', 'Kasbon']);
    $table->decimal('amount_paid', 15, 2)->default(0);
    $table->decimal('outstanding_amount', 15, 2)->default(0);

    // Metode & akun pembayaran
    $table->string('payment_method', 50)->nullable(); // Cash / Transfer / QRIS
    $table->unsignedBigInteger('payment_account_id')->nullable();

    // Customer info
    $table->enum('customer_type', ['Reseller', 'Non-Reseller']);
    $table->unsignedBigInteger('reseller_id')->nullable();
    $table->string('customer_phone', 30)->nullable();
    $table->text('notes')->nullable();

    // Status workflow
    $table->enum('status', ['PENDING', 'DONE', 'FAILED', 'CANCELLED'])->default('PENDING');
    $table->unsignedBigInteger('processed_by')->nullable(); // Role B
    $table->timestamp('processed_datetime')->nullable();
    $table->text('process_notes')->nullable();

    $table->timestamps();

    // Relasi
    $table->foreign('shift_id')->references('id')->on('shift_sessions');
    $table->foreign('admin_id')->references('id')->on('user');
    $table->foreign('app_id')->references('id')->on('aplikasi');
    $table->foreign('payment_account_id')->references('id')->on('banks');
    $table->foreign('reseller_id')->references('id')->on('resellers')->nullOnDelete();
    $table->foreign('processed_by')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};