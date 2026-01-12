<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_verifikasi', function (Blueprint $table) {
            $table->foreignId('jabatan_id')
                ->nullable()
                ->after('surat_id')
                ->constrained('jabatans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat_verifikasi', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn('jabatan_id');
        });
    }
};
