<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            // ubah kolom amount jadi nullable dengan default 0
            $table->decimal('amount', 15, 2)
                  ->default(0)
                  ->nullable()
                  ->change();

            // ubah kolom deadline jadi nullable
            $table->date('deadline')
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('investors', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)
                  ->default(0)
                  ->nullable(false)
                  ->change();
            $table->date('deadline')
                  ->nullable(false)
                  ->change();
        });
    }
};
