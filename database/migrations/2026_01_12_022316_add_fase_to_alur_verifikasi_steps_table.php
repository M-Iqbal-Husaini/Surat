<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom nullable (SQLite-safe)
        Schema::table('alur_verifikasi_steps', function (Blueprint $table) {
            $table->unsignedTinyInteger('fase')
                ->nullable()
                ->after('alur_verifikasi_id');
        });

        // 2. Mapping DATA LAMA berdasarkan urutan
        DB::statement("
            UPDATE alur_verifikasi_steps
            SET fase = CASE
                WHEN urutan <= 2 THEN 1
                ELSE 2
            END
        ");
    }

    public function down(): void
    {
        Schema::table('alur_verifikasi_steps', function (Blueprint $table) {
            $table->dropColumn('fase');
        });
    }
};
