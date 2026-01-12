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

            $table->string('nama_jabatan');
            // Ketua Jurusan, Direktur, Sekretaris Direktur, Staf Unit, dll

            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained('units')
                  ->nullOnDelete();

            $table->unsignedTinyInteger('level')->nullable();
            // 1 = pimpinan puncak
            // 2 = pimpinan unit
            // 3 = staf

            $table->boolean('is_pimpinan')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};
