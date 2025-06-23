<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tailor_progress', function (Blueprint $table) {
            // Kolom ini akan menghubungkan setiap pekerjaan ke invoice spesifiknya
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
};
