<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {

            $table->string('nomor_surat', 100)
                ->nullable()
                ->after('tanggal_surat');

            // OPTIONAL (lihat catatan di bawah)
            // $table->unique('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {

            // kalau kamu aktifkan unique, drop dulu index-nya
            // $table->dropUnique(['nomor_surat']);

            $table->dropColumn('nomor_surat');
        });
    }
};
