<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropForeign(['jenis_surat_id']);
        });

        Schema::table('surat', function (Blueprint $table) {
            $table->foreign('jenis_surat_id')
                ->references('id')
                ->on('jenis_surat');
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
