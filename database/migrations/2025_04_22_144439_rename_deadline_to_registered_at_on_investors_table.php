<?php

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
