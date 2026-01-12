<?php

// database/migrations/xxxx_xx_xx_create_alur_verifikasi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alur_verifikasi', function (Blueprint $table) {
            $table->id();

            $table->string('kode_alur')->unique();
            $table->string('nama_alur');
            $table->text('deskripsi')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alur_verifikasi');
    }
};
