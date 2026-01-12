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
        Schema::create('template_isi_surat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('template_surat')
                ->cascadeOnDelete();

            $table->string('field_key');   // perihal, tujuan, tanggal
            $table->string('label');

            $table->enum('type', [
                'text',
                'textarea',
                'date',
                'number',
                'file'
            ]);

            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();

            $table->unique(['template_id', 'field_key']);
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
