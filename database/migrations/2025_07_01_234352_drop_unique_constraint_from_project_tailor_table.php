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
        // Baris ini aman jika foreign key-nya masih ada
        $table->dropForeign(['project_id']);
        $table->dropForeign(['tailor_id']);
    });

    Schema::table('tailor_progress', function (Blueprint $table) {
        // Karena foreign key ini sudah tidak ada, kita lewati saja proses penghapusannya.
        // $table->dropForeign('tailor_progress_assignment_id_foreign'); // <-- DIKOMENTARI
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
