<?php

// Path: database/migrations/2025_04_30_001256_add_quantity_to_projects_table.php
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
    Schema::table('projects', function (Blueprint $table) {
        $table->integer('quantity')->default(0)->after('budget');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropColumn('quantity');
    });
}
};
