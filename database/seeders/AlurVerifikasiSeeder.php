<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlurVerifikasiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('alur_verifikasi_steps')->truncate();
        DB::table('alur_verifikasi')->truncate();

        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 1. ALUR INTERNAL UNIT
        |--------------------------------------------------------------------------
        | Draft -> TTD Unit Asal -> Selesai
        */
        $alurInternal = DB::table('alur_verifikasi')->insertGetId([
            'kode_alur'  => 'ALUR_INTERNAL_UNIT',
            'nama_alur'  => 'Internal Unit',
            'deskripsi'  => 'Surat internal dalam satu unit',
            'is_active'  => true,
            'created_at'=> $now,
            'updated_at'=> $now,
        ]);

        DB::table('alur_verifikasi_steps')->insert([
            $this->step($alurInternal, 1, 'asal', true, false, $now),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. ALUR ANTAR UNIT
        |--------------------------------------------------------------------------
        | Unit Asal (TTD) -> Unit Tujuan (verifikasi)
        */
        $alurAntarUnit = DB::table('alur_verifikasi')->insertGetId([
            'kode_alur'  => 'ALUR_ANTAR_UNIT',
            'nama_alur'  => 'Antar Unit',
            'deskripsi'  => 'Surat dari unit asal ke unit tujuan',
            'is_active'  => true,
            'created_at'=> $now,
            'updated_at'=> $now,
        ]);

        DB::table('alur_verifikasi_steps')->insert([
            $this->step($alurAntarUnit, 1, 'asal', true, true, $now),   // TTD unit asal
            $this->step($alurAntarUnit, 2, 'tujuan', false, true, $now), // verifikasi unit tujuan
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. ALUR UNIT KE DIREKTORAT
        |--------------------------------------------------------------------------
        | Unit Asal (TTD) -> Direktorat (TTD)
        */
        $alurDirektorat = DB::table('alur_verifikasi')->insertGetId([
            'kode_alur'  => 'ALUR_UNIT_DIREKTORAT',
            'nama_alur'  => 'Unit ke Direktorat',
            'deskripsi'  => 'Surat unit ke pimpinan/direktorat',
            'is_active'  => true,
            'created_at'=> $now,
            'updated_at'=> $now,
        ]);

        DB::table('alur_verifikasi_steps')->insert([
            $this->step($alurDirektorat, 1, 'asal', true, true, $now),   // TTD unit asal
            $this->step($alurDirektorat, 2, 'tujuan', true, false, $now), // TTD pimpinan
        ]);
    }

    /* =========================
     * HELPER
     * ========================= */
    private function step(
        int $alurId,
        int $urutan,
        string $unitScope,
        bool $perluTtd,
        bool $bolehKembalikan,
        $now
    ): array {
        return [
            'alur_verifikasi_id' => $alurId,
            'urutan'             => $urutan,
            'unit_scope'         => $unitScope,
            'perlu_ttd'          => $perluTtd,
            'boleh_kembalikan'   => $bolehKembalikan,
            'created_at'         => $now,
            'updated_at'         => $now,
        ];
    }
}
