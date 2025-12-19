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
        // daftar user awal
        $users = [
            [
                'name'         => 'Admin',
                'email'        => 'admin@esurat.test',
                'password'     => 'admin123',
                'nip'          => '198000000001',
                'unit_kode'    => 'TU',                    // Unit Tata Usaha
                'jabatan_nama' => 'Staf TU',               // dari tabel jabatans
                'role'         => 'admin',
            ],
            /*[
                'name'         => 'Ketua Jurusan TI',
                'email'        => 'kajur@esurat.test',
                'password'     => 'kajur123',
                'nip'          => '198000000002',
                'unit_kode'    => 'JTI',
                'jabatan_nama' => 'Ketua Jurusan',
                'role'         => 'pimpinan_unit',
            ],*/
            [
                'name'         => 'Direktur',
                'email'        => 'direktur@esurat.test',
                'password'     => 'direktur123',
                'nip'          => '198000000003',
                'unit_kode'    => 'DIR',
                'jabatan_nama' => 'Direktur',
                'role'         => 'pimpinan',
            ],
            [
                'name'         => 'Verifikator Jurusan',
                'email'        => 'verifikator@esurat.test',
                'password'     => 'verif123',
                'nip'          => '198000000004',
                'unit_kode'    => 'JTI',
                'jabatan_nama' => 'Ketua Jurusan', // misal Ketua Jurusan merangkap verifikator
                'role'         => 'verifikator',
            ],
            [
                'name'         => 'Sekretaris Jurusan TI',
                'email'        => 'sekjur@esurat.test',
                'password'     => 'sekjur123',
                'nip'          => '198000000005',
                'unit_kode'    => 'JTI',
                'jabatan_nama' => 'Sekretaris Jurusan',
                'role'         => 'sekretaris_unit',
            ],
            [
                'name'         => 'Pembuat Surat',
                'email'        => 'pembuat@esurat.test',
                'password'     => 'pembuat123',
                'nip'          => '198000000006',
                'unit_kode'    => 'TU',
                'jabatan_nama' => 'Staf TU',
                'role'         => 'pembuat_surat',
            ],
        ];

        foreach ($users as $data) {

            // cari unit_id berdasarkan kode_unit
            $unitId = null;
            if (!empty($data['unit_kode'])) {
                $unitId = DB::table('units')
                    ->where('kode_unit', $data['unit_kode'])
                    ->value('id');
            }

            // cari jabatan_id berdasarkan nama_jabatan
            $jabatanId = null;
            if (!empty($data['jabatan_nama'])) {
                $jabatanId = DB::table('jabatans')
                    ->where('nama_jabatan', $data['jabatan_nama'])
                    ->value('id');
            }

            // buat user (tanpa phone & position)
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'       => $data['name'],
                    'password'   => Hash::make($data['password']),
                    'nip'        => $data['nip'] ?? null,
                    'unit_id'    => $unitId,
                    'jabatan_id' => $jabatanId,
                    'status'     => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // assign role
            if (!empty($data['role']) && !$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
