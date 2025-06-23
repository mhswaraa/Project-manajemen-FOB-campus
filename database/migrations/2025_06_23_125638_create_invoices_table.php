<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('tailor_id')->constrained('penjahits', 'tailor_id')->onDelete('cascade');
            $table->date('issue_date'); // Tanggal invoice diterbitkan
            $table->decimal('total_amount', 15, 2); // Total nilai tagihan
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Status invoice
            
            // Kolom yang diisi oleh Admin saat pembayaran
            $table->date('payment_date')->nullable();
            $table->string('receipt_path')->nullable(); // Path bukti transfer
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
