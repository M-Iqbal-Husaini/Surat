<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // foreign key ke units (unit_id boleh null, kalau unit dihapus -> set null)
            $table->foreign('unit_id')
                  ->references('id')->on('units')
                  ->nullOnDelete();

            // foreign key ke jabatans
            $table->foreign('jabatan_id')
                  ->references('id')->on('jabatans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // hapus foreign key jika migration di-rollback
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['jabatan_id']);
        });
    }
};
