<?php

// Path: database/migrations/2025_04_20_023415_create_projects_table.php
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
        Schema::create('penjahits', function (Blueprint $table) {
            $table->id('tailor_id');              // IdPenjahit
            $table->unsignedBigInteger('user_id'); // FK ke users.id
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->enum('status',['available','busy','inactive'])->default('available');
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailors');
    }
};
