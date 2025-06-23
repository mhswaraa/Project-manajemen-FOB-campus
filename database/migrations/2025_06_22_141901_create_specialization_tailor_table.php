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
    Schema::create('specialization_tailor', function (Blueprint $table) {
        $table->foreignId('specialization_id')->constrained()->onDelete('cascade');
        $table->foreignId('tailor_id')->constrained('penjahits', 'tailor_id')->onDelete('cascade');
        $table->primary(['specialization_id', 'tailor_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialization_tailor');
    }
};
