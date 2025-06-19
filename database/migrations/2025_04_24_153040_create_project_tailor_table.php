<?php
// Path: database/migrations/2025_04_24_153040_create_project_tailor_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tailor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('tailor_id');
            $table->integer('assigned_qty')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])
                  ->default('pending');
            $table->timestamps();

            // foreign keys
            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->onDelete('cascade');
            $table->foreign('tailor_id')
                  ->references('tailor_id')->on('penjahits')
                  ->onDelete('cascade');

            // unik: satu penjahit satu kali per proyek
            $table->unique(['project_id','tailor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tailor');
    }
};
