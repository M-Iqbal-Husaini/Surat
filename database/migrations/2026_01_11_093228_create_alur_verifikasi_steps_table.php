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
        Schema::create('alur_verifikasi_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alur_verifikasi_id')
                ->constrained('alur_verifikasi')
                ->cascadeOnDelete();

            $table->unsignedInteger('urutan');

            $table->enum('unit_scope', ['asal', 'tujuan']);
            $table->boolean('perlu_ttd')->default(false);
            $table->boolean('boleh_kembalikan')->default(false);

            $table->timestamps();

            $table->unique(['alur_verifikasi_id', 'urutan']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alur_verifikasi_steps');
    }
};
