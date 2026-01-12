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
        Schema::create('surat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')->constrained()->restrictOnDelete();
            $table->foreignId('jenis_surat_id')->constrained()->restrictOnDelete();
            $table->foreignId('alur_id')->constrained('alur_verifikasi')->restrictOnDelete();

            $table->foreignId('pembuat_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('unit_asal_id')->constrained('units')->nullOnDelete();
            $table->foreignId('unit_tujuan_id')->nullable()->constrained('units')->nullOnDelete();

            // STATUS BEBAS (NO ENUM)
            $table->string('status', 50)->default('draft');

            // STEP AKTIF
            $table->unsignedInteger('step_aktif')->default(1);

            // TTD FINAL
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('signed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('signed_token')->nullable()->unique();

            $table->json('data_json')->nullable();
            $table->date('tanggal_surat')->nullable();

            $table->timestamps();

            $table->index('status');
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
