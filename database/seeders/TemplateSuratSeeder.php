<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSuratSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = DB::table('jenis_surat')->pluck('id', 'kode');
        $alur  = DB::table('alur_verifikasi')->pluck('id', 'kode_alur');

        $direkturId = DB::table('jabatans')
            ->where('nama_jabatan', 'Direktur')
            ->value('id');

        /* ===================== BODY HTML MAP ===================== */
        $bodyHtmlMap = [

            'HM' => '
<p>Sehubungan dengan akan dilaksanakannya kegiatan berikut:</p>
<table>
    <tr><td class="label">Nama Kegiatan</td><td class="separator">:</td><td class="value">{{kegiatan}}</td></tr>
    <tr><td class="label">Tempat</td><td class="separator">:</td><td class="value">{{tempat}}</td></tr>
    <tr><td class="label">Tanggal</td><td class="separator">:</td><td class="value">{{tanggal}}</td></tr>
    <tr><td class="label">Sasaran</td><td class="separator">:</td><td class="value">{{sasaran}}</td></tr>
</table>
<p>{{uraian}}</p>
',

            'KS' => '
<p>Dalam rangka menjalin kerja sama, bersama ini kami sampaikan:</p>
<table>
    <tr><td class="label">Mitra Kerjasama</td><td class="separator">:</td><td class="value">{{mitra}}</td></tr>
    <tr><td class="label">Bidang Kerjasama</td><td class="separator">:</td><td class="value">{{bidang}}</td></tr>
    <tr><td class="label">Periode</td><td class="separator">:</td><td class="value">{{periode}}</td></tr>
</table>
<p>{{uraian}}</p>
',

            'OT' => '
<p>Dengan ini kami sampaikan informasi organisasi sebagai berikut:</p>
<table>
    <tr><td class="label">Nama Organisasi</td><td class="separator">:</td><td class="value">{{organisasi}}</td></tr>
    <tr><td class="label">Kegiatan</td><td class="separator">:</td><td class="value">{{kegiatan}}</td></tr>
    <tr><td class="label">Waktu</td><td class="separator">:</td><td class="value">{{waktu}}</td></tr>
</table>
<p>{{uraian}}</p>
',

            'LK' => '
<p>Bersama ini kami laporkan kehilangan barang dengan rincian berikut:</p>
<table>
    <tr><td class="label">Nama Barang</td><td class="separator">:</td><td class="value">{{nama_barang}}</td></tr>
    <tr><td class="label">Jumlah</td><td class="separator">:</td><td class="value">{{jumlah}}</td></tr>
    <tr><td class="label">Lokasi</td><td class="separator">:</td><td class="value">{{lokasi}}</td></tr>
</table>
<p>{{spesifikasi}}</p>
',

            'KP' => '
<p>Dengan ini ditugaskan kepada:</p>
<table>
    <tr><td class="label">Nama Pegawai</td><td class="separator">:</td><td class="value">{{nama_pegawai}}</td></tr>
    <tr><td class="label">NIP</td><td class="separator">:</td><td class="value">{{nip}}</td></tr>
    <tr><td class="label">Jabatan</td><td class="separator">:</td><td class="value">{{jabatan}}</td></tr>
    <tr><td class="label">Unit Kerja</td><td class="separator">:</td><td class="value">{{unit_kerja}}</td></tr>
</table>
<p>Untuk melaksanakan penugasan berupa {{penugasan}}.</p>
',
        ];

        /* ===================== TEMPLATE MASTER ===================== */
        $templates = [

            ['HM','HM-KEGIATAN','Surat Hubungan Masyarakat','ALUR_UNIT'],
            ['OT','OT-ORGANISASI','Surat Organisasi','ALUR_UNIT'],
            ['LK','LK-PERLENGKAPAN','Permohonan Perlengkapan','ALUR_UNIT'],
            ['PR','PR-PERENCANAAN','Surat Perencanaan','ALUR_UNIT'],
            ['TU','TU-KETATAUSAHAAN','Surat Ketatausahaan','ALUR_UNIT'],
            ['RT','RT-KERUMAHTANGGAAN','Surat Kerumahtanggaan','ALUR_UNIT'],
            ['TI','TI-INFORMATIKA','Surat Informatika / SIMTIK','ALUR_UNIT'],
            ['DL','DL-DIKLAT','Surat Pendidikan dan Pelatihan','ALUR_UNIT'],
            ['AK','AK-AKADEMIK','Surat Akademik','ALUR_UNIT'],
            ['LL','LL-UMUM','Surat Lain-lain','ALUR_UNIT'],

            ['KS','KS-KERJASAMA','Surat Kerjasama','ALUR_UNIT_DIREKTORAT'],
            ['KP','KP-KEPEGAWAIAN','Surat Kepegawaian','ALUR_UNIT_DIREKTORAT'],
            ['KU','KU-KEUANGAN','Surat Keuangan','ALUR_UNIT_DIREKTORAT'],
            ['HK','HK-HUKUM','Surat Hukum','ALUR_UNIT_DIREKTORAT'],
            ['WS','WS-PENGAWASAN','Surat Pengawasan','ALUR_UNIT_DIREKTORAT'],
            ['PG','PG-PENGEMBANGAN','Surat Penelitian & Pengembangan','ALUR_UNIT_DIREKTORAT'],
            ['PP','PP-PENGAJARAN','Surat Pendidikan & Pengajaran','ALUR_UNIT_DIREKTORAT'],
            ['KM','KM-KEMAHASISWAAN','Surat Kemahasiswaan','ALUR_UNIT_DIREKTORAT'],
            ['PN','PN-PENELITIAN','Surat Penelitian','ALUR_UNIT_DIREKTORAT'],
            ['PM','PM-PENGABDIAN','Surat Pengabdian Masyarakat','ALUR_UNIT_DIREKTORAT'],
            ['DT','DT-TATAPAMONG','Surat Tata Pamong','ALUR_UNIT_DIREKTORAT'],
        ];

        foreach ($templates as [$kode, $kodeTpl, $nama, $alurKode]) {

            $bodyHtml = $bodyHtmlMap[$kode] ?? '
<p>Dengan hormat,</p>
<p>{{uraian}}</p>
';

            DB::table('template_surat')->updateOrInsert(
                ['kode_template' => $kodeTpl],
                [
                    'jenis_surat_id' => $jenis[$kode],
                    'nama_template'  => $nama,
                    'kode_alur'      => $alurKode,
                    'penandatangan_jabatan_id' => $direkturId,
                    'body_html'      => trim($bodyHtml),
                    'is_active'      => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            );
        }
    }
}
