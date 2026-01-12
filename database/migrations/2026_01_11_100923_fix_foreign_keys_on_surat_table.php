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
            Schema::rename('surat', 'surat_old');

            // 2. Buat ulang tabel surat dengan FK YANG BENAR
            Schema::create('surat', function (Blueprint $table) {
                $table->id();

                $table->foreignId('template_id')
                    ->constrained('template_surat');

                $table->foreignId('jenis_surat_id')
                    ->constrained('jenis_surat');

                $table->foreignId('alur_id')
                    ->constrained('alur_verifikasi');

                $table->foreignId('pembuat_id')
                    ->constrained('users');

                $table->foreignId('unit_asal_id')
                    ->constrained('units');

                $table->foreignId('unit_tujuan_id')
                    ->constrained('units');

                $table->json('data_json')->nullable();
                $table->string('status');
                $table->unsignedInteger('step_aktif');
                $table->date('tanggal_surat')->nullable();

                $table->timestamp('signed_at')->nullable();
                $table->unsignedBigInteger('signed_by_user_id')->nullable();
                $table->uuid('signed_token')->nullable();

                $table->timestamps();
            });

            // 3. Copy data dari tabel lama
            DB::statement("
                INSERT INTO surat (
                    id,
                    template_id,
                    jenis_surat_id,
                    alur_id,
                    pembuat_id,
                    unit_asal_id,
                    unit_tujuan_id,
                    data_json,
                    status,
                    step_aktif,
                    tanggal_surat,
                    signed_at,
                    signed_by_user_id,
                    signed_token,
                    created_at,
                    updated_at
                )
                SELECT
                    id,
                    template_id,
                    jenis_surat_id,
                    alur_id,
                    pembuat_id,
                    unit_asal_id,
                    unit_tujuan_id,
                    data_json,
                    status,
                    step_aktif,
                    tanggal_surat,
                    signed_at,
                    signed_by_user_id,
                    signed_token,
                    created_at,
                    updated_at
                FROM surat_old
            ");

            // 4. Drop tabel lama
            Schema::drop('surat_old');
        });
    }

    public function down(): void
    {
        // rollback tidak disediakan karena FK lama memang salah
    }
};
