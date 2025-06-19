<?php
// Path: database/migrations/2025_04_21_140710_create_investments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestmentsTable extends Migration
{
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_id');
            $table->unsignedBigInteger('project_id');
            $table->decimal('amount', 15, 2);
            $table->date('deadline');
            $table->timestamps();

            // foreign keys
            $table->foreign('investor_id')
                  ->references('investor_id')->on('investors')
                  ->onDelete('cascade');

            $table->foreign('project_id')
                  ->references('id')->on('projects')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('investments');
    }
}
