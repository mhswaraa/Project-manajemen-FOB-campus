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
        // Langkah 1: Hapus foreign key constraint dari tabel 'tailor_progress' terlebih dahulu.
        Schema::table('tailor_progress', function (Blueprint $table) {
            // Nama default foreign key di Laravel adalah 'nama_tabel_nama_kolom_foreign'
            $table->dropForeign('tailor_progress_assignment_id_foreign');
        });

        // Langkah 2: Setelah foreign key dilepas, sekarang kita aman untuk menghapus unique index.
        Schema::table('project_tailor', function (Blueprint $table) {
            $table->dropUnique('project_tailor_project_id_tailor_id_unique');
        });

        // Langkah 3: Pasang kembali foreign key constraint untuk menjaga integritas data.
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('project_tailor')
                  ->onDelete('cascade'); // Gunakan onDelete('cascade') atau sesuai aturan awal Anda.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk membatalkan, lakukan proses sebaliknya.
        // 1. Hapus foreign key.
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
        });

        // 2. Tambahkan kembali unique index.
        Schema::table('project_tailor', function (Blueprint $table) {
            $table->unique(['project_id', 'tailor_id']);
        });

        // 3. Pasang kembali foreign key.
        Schema::table('tailor_progress', function (Blueprint $table) {
            $table->foreign('assignment_id')
                  ->references('id')
                  ->on('project_tailor')
                  ->onDelete('cascade');
        });
    }
};
