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
        Schema::table('payouts', function (Blueprint $table) {
            // 1. Ganti nama kolom profit agar konsisten
            $table->renameColumn('profit_amount', 'amount');

            // 2. Ubah kolom tanggal agar bisa menyimpan waktu dan ganti nama
            $table->dateTime('paid_at')->nullable()->after('profit_amount');
            
            // 3. Hapus kolom lama yang tidak fleksibel
            $table->dropColumn('payment_date');

            // 4. Buat kolom foreign key untuk user tidak wajib (opsional)
            $table->dropForeign(['processed_by_user_id']); // Hapus constraint dulu
            $table->foreignId('processed_by_user_id')->nullable()->change(); // Ubah jadi nullable
            $table->foreign('processed_by_user_id')->references('id')->on('users')->onDelete('set null'); // Buat ulang constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            // Logika untuk mengembalikan perubahan jika diperlukan (opsional)
            $table->renameColumn('amount', 'profit_amount');
            $table->date('payment_date')->nullable();
            $table->dropColumn('paid_at');
            $table->dropForeign(['processed_by_user_id']);
            $table->foreignId('processed_by_user_id')->nullable(false)->change();
            $table->foreign('processed_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};