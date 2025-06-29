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
        Schema::table('investors', function (Blueprint $table) {
            // Menambahkan kolom NIK setelah kolom user_id
            $table->string('nik', 16)->after('user_id')->nullable()->unique();
            // Menambahkan kolom alamat setelah kolom NIK
            $table->text('alamat')->after('nik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn(['nik', 'alamat']);
        });
    }
};