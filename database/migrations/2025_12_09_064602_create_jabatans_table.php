<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan'); // contoh: Ketua Jurusan, Direktur, Sekretaris, Staf TU
            $table->unsignedBigInteger('unit_id')->nullable(); // relasi logis ke tabel units
            $table->unsignedTinyInteger('level')->nullable(); // contoh: 1 = pimpinan puncak, 2 = pimpinan unit, dst
            $table->boolean('is_pimpinan')->default(false); // true jika jabatan pimpinan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};
