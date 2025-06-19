<?php

// Path: database/migrations/2025_04_23_155113_create_production_progress_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->integer('completed_units')->default(0);
            $table->integer('total_units')->default(0);
            $table->text('note')->nullable();     // catatan progress
            $table->timestamps();

            // FK ke projects.id
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_progresses');
    }
};
