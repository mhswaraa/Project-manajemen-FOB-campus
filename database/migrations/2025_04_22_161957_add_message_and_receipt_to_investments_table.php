<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            // Tambah kolom message (pesan) dan receipt (path file)
            $table->text('message')
                  ->nullable()
                  ->after('deadline');
            $table->string('receipt')
                  ->nullable()
                  ->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['message', 'receipt']);
        });
    }
};
