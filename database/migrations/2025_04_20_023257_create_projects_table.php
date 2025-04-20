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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();                    // ProyekID
            $table->string('name');          // Nama Proyek
            $table->bigInteger('budget');    // Anggaran
            $table->date('deadline');        // Deadline
            $table->string('image')->nullable(); 
            $table->enum('status',['pending','on_progress','completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
