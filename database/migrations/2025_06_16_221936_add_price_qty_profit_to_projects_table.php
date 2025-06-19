<?php
// database\migrations\2025_06_16_221936_add_price_qty_profit_to_projects_table.php
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
        $table->decimal('price_per_piece', 15, 2)
              ->default(0)
              ->after('name');
        $table->decimal('profit', 15, 2)
              ->default(0)
              ->after('quantity');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropColumn(['price_per_piece', 'profit']);
    });
}
};
