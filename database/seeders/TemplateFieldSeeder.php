<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateFieldSeeder extends Seeder
{
    public function run(): void
    {
        $templates = DB::table('template_surat')
            ->join('jenis_surat', 'jenis_surat.id', '=', 'template_surat.jenis_surat_id')
            ->select(
                'template_surat.id as template_id',
                'jenis_surat.kode'
            )
            ->get();

        foreach ($templates as $tpl) {

            $fields = match ($tpl->kode) {

                // 1. Himbauan
                'HM' => [
                    ['kegiatan','Nama Kegiatan','text'],
                    ['tempat','Tempat','text'],
                    ['tanggal','Tanggal','date'],
                    ['sasaran','Sasaran','text'],
                    ['uraian','Uraian','textarea'],
                ],

                // 2. Kerja Sama
                'KS' => [
                    ['mitra','Mitra Kerjasama','text'],
                    ['bidang','Bidang Kerjasama','text'],
                    ['periode','Periode','text'],
                    ['uraian','Ruang Lingkup','textarea'],
                ],

                // 3. Organisasi / Tugas
                'OT' => [
                    ['organisasi','Nama Organisasi','text'],
                    ['kegiatan','Kegiatan','text'],
                    ['waktu','Waktu','text'],
                    ['uraian','Uraian','textarea'],
                ],

                // 4. Laporan Kehilangan
                'LK' => [
                    ['nama_barang','Nama Barang','text'],
                    ['jumlah','Jumlah','number'],
                    ['lokasi','Lokasi','text'],
                    ['spesifikasi','Spesifikasi','textarea'],
                ],

                // 5. Permohonan
                'PR' => [
                    ['kegiatan','Nama Kegiatan','text'],
                    ['tujuan','Tujuan','text'],
                    ['waktu','Waktu','text'],
                    ['uraian','Uraian','textarea'],
                ],

                // 6. Surat Tugas
                'KP' => [
                    ['nama_pegawai','Nama Pegawai','text'],
                    ['nip','NIP','text'],
                    ['jabatan','Jabatan','text'],
                    ['unit_kerja','Unit Kerja','text'],
                    ['penugasan','Jenis Penugasan','text'],
                ],

                // 7. Keuangan
                'KU' => [
                    ['kegiatan','Kegiatan','text'],
                    ['nilai_anggaran','Nilai Anggaran','number'],
                    ['sumber_dana','Sumber Dana','text'],
                    ['uraian','Uraian','textarea'],
                ],

                // 8. Penelitian
                'PG' => [
                    ['judul','Judul Penelitian','text'],
                    ['ketua','Ketua Peneliti','text'],
                    ['anggota','Anggota','textarea'],
                    ['uraian','Uraian','textarea'],
                ],

                // 9. Perkuliahan
                'PP' => [
                    ['mata_kuliah','Mata Kuliah','text'],
                    ['prodi','Program Studi','text'],
                    ['semester','Semester','number'],
                    ['uraian','Uraian','textarea'],
                ],

                // 10. Akademik Mahasiswa
                'AK' => [
                    ['nama_mahasiswa','Nama Mahasiswa','text'],
                    ['nim','NIM','text'],
                    ['prodi','Program Studi','text'],
                    ['semester','Semester','number'],
                    ['keperluan','Keperluan','textarea'],
                ],

                // 11–12. Surat Umum
                'TU','RT','TI','DL','KM','PN','PM','HK','WS','DT' => [
                    ['uraian','Uraian','textarea'],
                ],

                // Default
                default => [
                    ['judul','Judul','text'],
                    ['uraian','Uraian','textarea'],
                ],
            };

            $order = 1;

            foreach ($fields as [$key, $label, $type]) {
                DB::table('template_isi_surat')->updateOrInsert(
                    [
                        'template_id' => $tpl->template_id,
                        'field_key'   => $key,
                    ],
                    [
                        'label'      => $label,
                        'type'       => $type,
                        'required'   => 1,          // tinyint(1)
                        'order'      => $order++,   // INTEGER NOT NULL
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
