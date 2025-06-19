<?php
// Path: database/migrations/2025_06_16_230658_add_qty_drop_deadline_from_investments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            // 1) Tambah qty
            $table->integer('qty')->default(1)->after('project_id');
            // 2) Hapus kolom deadline
            $table->dropColumn('deadline');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            // 1) Tambah kembali deadline
            $table->date('deadline')->after('qty');
            // 2) Hapus kolom qty
            $table->dropColumn('qty');
        });
    }
};
