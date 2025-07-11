<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('profit_amount', 15, 2);
            $table->date('payment_date');
            $table->string('receipt_path')->nullable(); // Untuk menyimpan bukti transfer
            $table->text('notes')->nullable();
            $table->foreignId('processed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
