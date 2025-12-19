<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->string('kode_unit')->nullable(); // contoh: JTI, DIR, TU
            $table->string('jenis_unit')->nullable(); // contoh: jurusan, prodi, direktorat, TU, dll
            $table->unsignedBigInteger('parent_id')->nullable(); // kalau mau hirarki (unit induk)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
