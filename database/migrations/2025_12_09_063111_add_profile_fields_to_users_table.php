<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->unsignedBigInteger('unit_id')->nullable()->after('nip');
            $table->unsignedBigInteger('jabatan_id')->nullable()->after('unit_id');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('jabatan_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'unit_id', 'jabatan_id', 'status']);
        });
    }
};
