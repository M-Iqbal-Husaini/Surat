<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [

            // ======================
            // ADMIN
            // ======================
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@esurat.test',
                'password' => 'admin123',
                'nip' => '198000000001',
                'unit_kode' => null,
                'jabatan_nama' => 'Admin Sistem',
                'role' => 'admin',
                'status' => 'aktif',
                'ttd_path' => null, // admin tidak ttd
            ],

            /* ======================
             * PEMBUAT SURAT UNIT
             * ====================== */
            [
                'name' => 'Pembuat Surat Keuangan',
                'email' => 'pembuat.keu@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000015',
                'unit_kode' => 'KEU',
                'jabatan_nama' => 'Staf Unit Keuangan',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],
            [
                'name' => 'Pembuat Surat Akademik',
                'email' => 'pembuat.akademik@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000016',
                'unit_kode' => 'AKD',
                'jabatan_nama' => 'Staf Unit Akademik',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],
            [
                'name' => 'Pembuat Surat Perpustakaan',
                'email' => 'pembuat.perpus@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000017',
                'unit_kode' => 'PERPUS',
                'jabatan_nama' => 'Staf Unit Perpustakaan',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],

            /* ======================
             * PEMBUAT SURAT JURUSAN
             * ====================== */
            [
                'name' => 'Pembuat Surat TI',
                'email' => 'pembuat.ti@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000010',
                'unit_kode' => 'JTI',
                'jabatan_nama' => 'Staf Jurusan Teknik Informatika',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],
            [
                'name' => 'Pembuat Surat Mesin',
                'email' => 'pembuat.mesin@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000011',
                'unit_kode' => 'JTM',
                'jabatan_nama' => 'Staf Jurusan Teknik Mesin',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],
            [
                'name' => 'Pembuat Surat Elektro',
                'email' => 'pembuat.elektro@esurat.test',
                'password' => 'pembuat123',
                'nip' => '198000000012',
                'unit_kode' => 'JTE',
                'jabatan_nama' => 'Staf Jurusan Teknik Elektro',
                'role' => 'pembuat_surat',
                'status' => 'aktif',
                'ttd_path' => null,
            ],

            /* ======================
             * KETUA JURUSAN (VERIFIKATOR)
             * ====================== */
            [
                'name' => 'Ketua Jurusan TI',
                'email' => 'kajur.ti@esurat.test',
                'password' => 'kajur123',
                'nip' => '198000000004',
                'unit_kode' => 'JTI',
                'jabatan_nama' => 'Ketua Jurusan Teknik Informatika',
                'role' => 'verifikator',
                'status' => 'aktif',
                'ttd_path' => 'ttd/kajur_ti.png',
            ],
            [
                'name' => 'Ketua Jurusan Mesin',
                'email' => 'kajur.mesin@esurat.test',
                'password' => 'kajur123',
                'nip' => '198000000013',
                'unit_kode' => 'JTM',
                'jabatan_nama' => 'Ketua Jurusan Teknik Mesin',
                'role' => 'verifikator',
                'status' => 'aktif',
                'ttd_path' => 'ttd/kajur_mesin.png',
            ],
            [
                'name' => 'Ketua Jurusan Elektro',
                'email' => 'kajur.elektro@esurat.test',
                'password' => 'kajur123',
                'nip' => '198000000014',
                'unit_kode' => 'JTE',
                'jabatan_nama' => 'Ketua Jurusan Teknik Elektro',
                'role' => 'verifikator',
                'status' => 'aktif',
                'ttd_path' => 'ttd/kajur_elektro.png',
            ],

            /* ======================
             * SEKRETARIS DIREKTUR
             * ====================== */
            [
                'name' => 'Sekretaris Direktur',
                'email' => 'sekdir@esurat.test',
                'password' => 'sekdir123',
                'nip' => '198000000018',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Sekretaris Direktur',
                'role' => 'sekretaris_direktur',
                'status' => 'aktif',
                'ttd_path' => 'ttd/sekdir.png',
            ],

            /* ======================
             * PIMPINAN (TTD WAJIB)
             * ====================== */
            [
                'name' => 'Direktur Politeknik',
                'email' => 'direktur@esurat.test',
                'password' => 'direktur123',
                'nip' => '198000000006',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Direktur',
                'role' => 'pimpinan',
                'status' => 'aktif',
                'ttd_path' => 'ttd/direktur.png',
            ],
            [
                'name' => 'Wakil Direktur I',
                'email' => 'wadir1@esurat.test',
                'password' => 'wadir123',
                'nip' => '198000000007',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Wakil Direktur I',
                'role' => 'pimpinan',
                'status' => 'aktif',
                'ttd_path' => 'ttd/wadir1.png',
            ],
            [
                'name' => 'Wakil Direktur II',
                'email' => 'wadir2@esurat.test',
                'password' => 'wadir123',
                'nip' => '198000000008',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Wakil Direktur II',
                'role' => 'pimpinan',
                'status' => 'aktif',
                'ttd_path' => 'ttd/wadir2.png',
            ],
            [
                'name' => 'Wakil Direktur III',
                'email' => 'wadir3@esurat.test',
                'password' => 'wadir123',
                'nip' => '198000000009',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Wakil Direktur III',
                'role' => 'pimpinan',
                'status' => 'aktif',
                'ttd_path' => 'ttd/wadir3.png',
            ],

            /* ======================
             * CONTOH USER NONAKTIF (TEST RULE)
             * ====================== */
            [
                'name' => 'Direktur Lama',
                'email' => 'direktur.lama@esurat.test',
                'password' => 'direktur123',
                'nip' => '197900000001',
                'unit_kode' => 'DIR',
                'jabatan_nama' => 'Direktur',
                'role' => 'pimpinan',
                'status' => 'nonaktif',
                'ttd_path' => 'ttd/direktur_lama.png',
            ],
        ];

        foreach ($users as $data) {

            $unitId = $data['unit_kode']
                ? DB::table('units')->where('kode_unit', $data['unit_kode'])->value('id')
                : null;

            $jabatanId = DB::table('jabatans')
                ->where('nama_jabatan', $data['jabatan_nama'])
                ->value('id');

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password']),
                    'nip'               => $data['nip'],
                    'unit_id'           => $unitId,
                    'jabatan_id'        => $jabatanId,
                    'status'            => $data['status'],
                    'ttd_path'          => $data['ttd_path'],
                    'email_verified_at' => $now,
                ]
            );

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
