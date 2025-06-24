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
        Schema::table('project_tailor', function (Blueprint $table) {
            $table->date('completed_at')->nullable()->after('status');
            $table->text('completion_notes')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tailor', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'completion_notes']);
        });
    }
};
