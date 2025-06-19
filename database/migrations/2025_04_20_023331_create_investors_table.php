<?php
// Path: database/migrations/2025_04_20_023331_create_investors_table.php

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
            $table->id('investor_id');            // IdInvestor
            $table->unsignedBigInteger('user_id'); // FK ke users.id (jika perlu)
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->decimal('amount', 15, 2)->default(0)->after('phone');
            $table->date('deadline');
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
        Schema::dropIfExists('investors');
    }
};
