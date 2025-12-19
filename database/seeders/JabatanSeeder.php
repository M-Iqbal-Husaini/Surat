<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // ambil id unit dari tabel units
        $dirId = DB::table('units')->where('kode_unit', 'DIR')->value('id');
        $jtiId = DB::table('units')->where('kode_unit', 'JTI')->value('id');
        $tuId  = DB::table('units')->where('kode_unit', 'TU')->value('id');

        DB::table('jabatans')->insert([
            [
                'nama_jabatan' => 'Direktur',
                'unit_id'      => $dirId,
                'level'        => 1,
                'is_pimpinan'  => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_jabatan' => 'Wakil Direktur',
                'unit_id'      => $dirId,
                'level'        => 2,
                'is_pimpinan'  => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_jabatan' => 'Ketua Jurusan',
                'unit_id'      => $jtiId,
                'level'        => 2,
                'is_pimpinan'  => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_jabatan' => 'Sekretaris Jurusan',
                'unit_id'      => $jtiId,
                'level'        => 3,
                'is_pimpinan'  => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'nama_jabatan' => 'Staf TU',
                'unit_id'      => $tuId,
                'level'        => 3,
                'is_pimpinan'  => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
