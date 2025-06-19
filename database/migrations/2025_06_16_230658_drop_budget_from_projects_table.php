<?php
// database\migrations\2025_06_16_221936_add_price_qty_profit_to_projects_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'budget')) {
                $table->dropColumn('budget');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // kembalikan kalau diperlukan rollback
            $table->decimal('budget', 15, 2)
                  ->default(0)
                  ->after('name');
        });
    }
};
