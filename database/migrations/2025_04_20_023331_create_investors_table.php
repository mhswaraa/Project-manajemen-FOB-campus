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
        Schema::create('investors', function (Blueprint $table) {
            $table->id('investor_id');
            $table->foreignId('user_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            
            // PERBAIKAN: Hapus ->after('phone') dari baris ini
            $table->decimal('amount', 15, 2)->default(0); 

            $table->date('deadline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
