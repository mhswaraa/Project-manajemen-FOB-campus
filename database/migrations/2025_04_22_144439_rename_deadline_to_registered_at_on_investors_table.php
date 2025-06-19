<?php

// Path: database/migrations/2025_04_22_144439_rename_deadline_to_registered_at_on_investors_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->renameColumn('deadline', 'registered_at');
        });
    }
    
    public function down()
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->renameColumn('registered_at', 'deadline');
        });
    }
};
