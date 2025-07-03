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
        Schema::table('tailor_progress', function (Blueprint $table) {
            // PERBAIKAN: Hapus foreign key dengan merujuk pada nama kolomnya.
            // Ini adalah cara yang lebih andal.
            $table->dropForeign(['assignment_id']);
        });

        // Setelah foreign key dilepas, sekarang kita aman untuk menghapus unique index.
        Schema::table('project_tailor', function (Blueprint $table) {
            $table->dropUnique('project_tailor_project_id_tailor_id_unique');
        });

        // Pasang kembali foreign key constraint untuk menjaga integritas data.
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('project_tailor')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk membatalkan, lakukan proses sebaliknya.
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
        });

        Schema::table('project_tailor', function (Blueprint $table) {
            $table->unique(['project_id', 'tailor_id']);
        });

        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('project_tailor')
                  ->onDelete('cascade');
        });
    }
};
