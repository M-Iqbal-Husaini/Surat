<?php

// database/migrations/xxxx_xx_xx_create_surat_disposisi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_disposisi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_id')
                ->constrained('surat')
                ->cascadeOnDelete();

            $table->foreignId('dari_jabatan_id')
                ->constrained('jabatans');

            $table->foreignId('ke_jabatan_id')
                ->constrained('jabatans');

            $table->text('instruksi');
            $table->string('status');

            $table->timestamp('ditindaklanjuti_pada')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('surat_disposisi');
    }
};
