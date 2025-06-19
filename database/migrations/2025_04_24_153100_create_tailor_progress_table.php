<?php

// Path: database/migrations/2025_04_23_155113_create_production_progress_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tailor_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id'); // refers project_tailor.id
            $table->date('date')->default(now());
            $table->integer('quantity_done')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // foreign key to project_tailor
            $table->foreign('assignment_id')
                  ->references('id')->on('project_tailor')
                  ->onDelete('cascade');

            // unique per day per assignment
            $table->unique(['assignment_id','date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tailor_progress');
    }
};
