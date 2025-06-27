<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            // FIX: Menghapus ->after('status') karena kolom status tidak ada.
            // Kolom baru akan ditambahkan di akhir tabel secara otomatis.
            $table->string('gdrive_link')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn('gdrive_link');
        });
    }
};
