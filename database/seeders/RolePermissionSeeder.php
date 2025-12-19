<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSION LIST
        |--------------------------------------------------------------------------
        | Catatan:
        | - Tambahan permission "view surat masuk/keluar/disposisi"
        |   agar sidebar bisa tampil berbeda per role (pakai can()).
        */
        $permissions = [
            // admin
            'manage users',
            'manage roles',
            'manage templates',
            'manage struktur unit',
            'manage jalur surat',

            // menu / akses modul
            'view surat masuk',
            'view surat keluar',
            'view disposisi',

            // surat (umum)
            'create surat',
            'edit own surat',
            'view own surat',
            'view all surat',

            // alur surat
            'verify surat',
            'approve surat',
            'disposition surat',
            'archive surat',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE LIST (TIDAK ADA SUPER ADMIN)
        |--------------------------------------------------------------------------
        */
        $roles = [
            'admin',            
            'pembuat_surat',
            'sekretaris_unit',
            'verifikator',
            'pimpinan',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE → PERMISSION MAPPING
        |--------------------------------------------------------------------------
        */

        Role::findByName('admin')->syncPermissions(Permission::all());

        Role::findByName('pembuat_surat')->syncPermissions([
            'create surat',
            'edit own surat',
            'view own surat',
            'view surat masuk',
            'view surat keluar',
        ]);

        Role::findByName('sekretaris_unit')->syncPermissions([
            'create surat',
            'edit own surat',
            'view own surat',
            'view surat masuk',
            'view surat keluar',
            'view disposisi',
        ]);

        Role::findByName('verifikator')->syncPermissions([
            'verify surat',
            'view all surat',
            'view surat masuk',
            'view surat keluar',
        ]);

        Role::findByName('pimpinan')->syncPermissions([
            'approve surat',
            'disposition surat',
            'view all surat',
            'view surat masuk',
            'view surat keluar',
            'view disposisi',
        ]);

    }
}
