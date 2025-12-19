<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->insert([
            [
                'nama_unit'  => 'Direktorat',
                'kode_unit'  => 'DIR',
                'jenis_unit' => 'direktorat',
                'parent_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_unit'  => 'Jurusan Teknik Informatika',
                'kode_unit'  => 'JTI',
                'jenis_unit' => 'jurusan',
                'parent_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_unit'  => 'Unit Tata Usaha',
                'kode_unit'  => 'TU',
                'jenis_unit' => 'unit',
                'parent_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
