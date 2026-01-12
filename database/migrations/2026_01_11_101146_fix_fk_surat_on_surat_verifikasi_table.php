<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // 1. Rename tabel lama
            Schema::rename('surat_verifikasi', 'surat_verifikasi_old');

            // 2. Create ulang tabel dengan FK BENAR
            Schema::create('surat_verifikasi', function (Blueprint $table) {
                $table->id();

                $table->foreignId('surat_id')
                    ->constrained('surat')
                    ->cascadeOnDelete();

                $table->foreignId('unit_id')
                    ->constrained('units');

                $table->unsignedInteger('urutan');
                $table->string('status');
                $table->boolean('perlu_ttd')->default(false);

                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('acted_at')->nullable();

                $table->timestamps();

                // ⬇️ PAKAI NAMA INDEX CUSTOM (WAJIB)
                $table->unique(
                    ['surat_id', 'urutan'],
                    'sv_surat_urutan_unique'
                );
            });
            // 3. Copy data lama
            DB::statement("
                INSERT INTO surat_verifikasi (
                    id,
                    surat_id,
                    unit_id,
                    urutan,
                    status,
                    perlu_ttd,
                    user_id,
                    acted_at,
                    created_at,
                    updated_at
                )
                SELECT
                    id,
                    surat_id,
                    unit_id,
                    urutan,
                    status,
                    0 as perlu_ttd,
                    user_id,
                    acted_at,
                    created_at,
                    updated_at
                FROM surat_verifikasi_old
            ");


            // 4. Drop tabel lama
            Schema::drop('surat_verifikasi_old');
        });
    }

    public function down(): void
    {
        // intentionally left blank
    }
};
