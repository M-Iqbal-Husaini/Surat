<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /* =============================
         * ROOT UNIT
         * ============================= */
        DB::table('units')->insert([
            'id'         => 1,
            'nama_unit'  => 'Direktorat',
            'kode_unit'  => 'DIR',
            'jenis_unit' => 'direktorat',
            'parent_id'  => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /* =============================
         * JURUSAN
         * ============================= */
        DB::table('units')->insert([
            [
                'id'         => 2,
                'nama_unit'  => 'Jurusan Teknik Informatika',
                'kode_unit'  => 'JTI',
                'jenis_unit' => 'jurusan',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 3,
                'nama_unit'  => 'Jurusan Teknik Mesin',
                'kode_unit'  => 'JTM',
                'jenis_unit' => 'jurusan',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 4,
                'nama_unit'  => 'Jurusan Teknik Elektro',
                'kode_unit'  => 'JTE',
                'jenis_unit' => 'jurusan',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        /* =============================
         * UNIT PENDUKUNG
         * ============================= */
        DB::table('units')->insert([
            [
                'id'         => 6,
                'nama_unit'  => 'Unit Keuangan',
                'kode_unit'  => 'KEU',
                'jenis_unit' => 'unit',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 8,
                'nama_unit'  => 'Unit Akademik',
                'kode_unit'  => 'AKD',
                'jenis_unit' => 'unit',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 10,
                'nama_unit'  => 'Unit Perpustakaan',
                'kode_unit'  => 'PERPUS',
                'jenis_unit' => 'unit',
                'parent_id'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
