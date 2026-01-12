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
        Schema::table('alur_verifikasi_steps', function (Blueprint $table) {
            $table->foreignId('jabatan_id')
                ->nullable()               // 🔥 WAJIB untuk SQLite
                ->after('alur_verifikasi_id')
                ->constrained('jabatans')
                ->nullOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alur_verifikasi_steps', function (Blueprint $table) {
            //
        });
    }
};
