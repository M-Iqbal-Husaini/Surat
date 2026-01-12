<?php

namespace App\Helpers;

use App\Models\Surat;
use Illuminate\Support\Facades\DB;

class NomorSuratHelper
{
    /**
     * Generate dan SIMPAN nomor surat
     */
    public static function generate(Surat $surat): string
    {
        $surat->refresh()->load(['jenisSurat', 'unitAsal']);

        if ($surat->nomor_surat) {
            return $surat->nomor_surat;
        }

        if (!$surat->jenisSurat || !$surat->unitAsal) {
            throw new \RuntimeException('Relasi surat belum lengkap untuk generate nomor');
        }

        $tahun = now()->year;

        $urut = Surat::whereYear('tanggal_surat', $tahun)
            ->where('unit_asal_id', $surat->unit_asal_id)
            ->where('jenis_surat_id', $surat->jenis_surat_id)
            ->whereNotNull('nomor_surat')
            ->lockForUpdate() // 🔥 penting di production
            ->count() + 1;

        $nomor = sprintf(
            '%03d/%s/%s/%d',
            $urut,
            $surat->jenisSurat->kode,
            $surat->unitAsal->kode_unit,
            $tahun
        );

        $surat->update([
            'nomor_surat' => $nomor,
        ]);

        return $nomor;
    }


    private static function bulanRomawi(int $bulan): string
    {
        return [
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ][$bulan];
    }
}

