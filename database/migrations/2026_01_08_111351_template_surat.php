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
        Schema::create('template_surat', function (Blueprint $table) {
            $table->id();

            $table->string('kode_template')->unique();   // TMP_PERMOHONAN_UNIT
            $table->string('nama_template');

            $table->foreignId('jenis_surat_id')
                ->constrained('jenis_surat')
                ->restrictOnDelete();

            // 🔑 penentu alur
            $table->string('kode_alur'); // FK logis ke alur_verifikasi.kode_alur

            // penandatangan akhir (optional, override alur)
            $table->foreignId('penandatangan_jabatan_id')
                ->nullable()
                ->constrained('jabatans')
                ->nullOnDelete();

            // HTML surat final
            $table->longText('body_html')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
