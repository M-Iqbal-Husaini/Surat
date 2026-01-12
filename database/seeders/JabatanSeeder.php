<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $dirId = DB::table('units')->where('kode_unit', 'DIR')->value('id');
        $jtiId = DB::table('units')->where('kode_unit', 'JTI')->value('id');
        $jtmId = DB::table('units')->where('kode_unit', 'JTM')->value('id');
        $jteId = DB::table('units')->where('kode_unit', 'JTE')->value('id');

        $keuId    = DB::table('units')->where('kode_unit', 'KEU')->value('id');
        $akdId    = DB::table('units')->where('kode_unit', 'AKD')->value('id');
        $perpusId = DB::table('units')->where('kode_unit', 'PERPUS')->value('id');


        $jabatans = [

            // DIREKTORAT
            ['Direktur', $dirId, 1, true],
            ['Wakil Direktur I', $dirId, 2, true],
            ['Wakil Direktur II', $dirId, 2, true],
            ['Wakil Direktur III', $dirId, 2, true],
            ['Sekretaris Direktur', $dirId, 3, false],

            // JURUSAN TI
            ['Ketua Jurusan Teknik Informatika', $jtiId, 2, true],
            ['Sekretaris Jurusan Teknik Informatika', $jtiId, 3, false],
            ['Staf Jurusan Teknik Informatika', $jtiId, 4, false],

            // JURUSAN MESIN
            ['Ketua Jurusan Teknik Mesin', $jtmId, 2, true],
            ['Sekretaris Jurusan Teknik Mesin', $jtmId, 3, false],
            ['Staf Jurusan Teknik Mesin', $jtmId, 4, false],

            // JURUSAN ELEKTRO
            ['Ketua Jurusan Teknik Elektro', $jteId, 2, true],
            ['Sekretaris Jurusan Teknik Elektro', $jteId, 3, false],
            ['Staf Jurusan Teknik Elektro', $jteId, 4, false],

            // UNIT KEUANGAN
            ['Staf Unit Keuangan', $keuId, 4, false],

            // UNIT AKADEMIK
            ['Staf Unit Akademik', $akdId, 4, false],

            // UNIT PERPUSTAKAAN
            ['Staf Unit Perpustakaan', $perpusId, 4, false],

            // SISTEM
            ['Admin Sistem', null, 0, false],
        ];

        foreach ($jabatans as [$nama, $unitId, $level, $isPimpinan]) {
            DB::table('jabatans')->updateOrInsert(
                ['nama_jabatan' => $nama],
                [
                    'unit_id'     => $unitId,
                    'level'       => $level,
                    'is_pimpinan' => $isPimpinan,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );
        }
    }
}
