<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // administratif / operasional
            ['HM','Hubungan Masyarakat',true],
            ['OT','Organisasi',true],
            ['LK','Perlengkapan',true],
            ['PR','Perencanaan',true],
            ['TU','Ketatausahaan',true],
            ['RT','Kerumahtanggaan',true],
            ['TI','Informatika / SIMTIK',true],
            ['DL','Pendidikan & Pelatihan',true],
            ['AK','Akademik',true],
            ['LL','Lain-lain',true],

            // strategis / institusional
            ['KS','Kerjasama',false],
            ['KP','Kepegawaian',false],
            ['KU','Keuangan',false],
            ['HK','Hukum',false],
            ['WS','Pengawasan',false],
            ['PG','Penelitian & Pengembangan',false],
            ['PP','Pendidikan & Pengajaran',false],
            ['KM','Kemahasiswaan',false],
            ['PN','Penelitian',false],
            ['PM','Pengabdian Masyarakat',false],
            ['DT','Tata Pamong',false],
        ];

        foreach ($data as [$kode,$nama,$boleh]) {
            DB::table('jenis_surat')->updateOrInsert(
                ['kode'=>$kode],
                [
                    'nama'=>$nama,
                    'boleh_ttd_pembuat'=>$boleh,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]
            );
        }
    }
}
