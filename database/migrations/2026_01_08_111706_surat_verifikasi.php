<?php

// database/migrations/xxxx_xx_xx_create_surat_verifikasi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_verifikasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('urutan');

            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('jabatan_id')
                ->nullable()
                ->after('surat_id')
                ->constrained('jabatans') // 🔥 HARUS jabatans
                ->nullOnDelete();

            // STATUS STEP
            $table->string('status', 30)->default('pending');

            $table->timestamp('acted_at')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['surat_id', 'urutan']);
            $table->index('status');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('surat_verifikasi');
    }
};
