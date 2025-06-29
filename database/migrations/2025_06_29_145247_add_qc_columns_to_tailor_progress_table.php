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
            // Status untuk setiap laporan: 'pending_qc', 'approved', 'rejected'
            $table->string('status')->default('pending_qc')->after('notes');
            
            // Jumlah yang diterima oleh tim QC.
            $table->integer('accepted_qty')->unsigned()->nullable()->after('status');
            
            // Jumlah yang ditolak/reject oleh tim QC.
            $table->integer('rejected_qty')->unsigned()->nullable()->after('accepted_qty');
            
            // Catatan dari tim QC mengenai hasil pemeriksaan.
            $table->text('qc_notes')->nullable()->after('rejected_qty');
            
            // Waktu saat pemeriksaan QC dilakukan.
            $table->timestamp('qc_checked_at')->nullable()->after('qc_notes');
            
            // Foreign key untuk mencatat siapa admin yang melakukan QC.
            $table->foreignId('qc_admin_id')->nullable()->constrained('users')->onDelete('set null')->after('qc_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tailor_progress', function (Blueprint $table) {
            // Urutan drop harus dibalik dari pembuatan
            $table->dropForeign(['qc_admin_id']);
            $table->dropColumn([
                'status', 
                'accepted_qty', 
                'rejected_qty', 
                'qc_notes', 
                'qc_checked_at', 
                'qc_admin_id'
            ]);
        });
    }
};
