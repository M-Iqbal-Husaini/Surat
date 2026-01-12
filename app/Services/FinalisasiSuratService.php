<?php

namespace App\Services;

use App\Models\Surat;
use App\Helpers\NomorSuratHelper;
use App\Helpers\TtdHelper;
use App\Helpers\QrHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FinalisasiSuratService
{
    public static function finalisasi(Surat $surat, $user): void
    {
        DB::transaction(function () use ($surat, $user) {

            // 1️⃣ Nomor surat (sekali)
            if (!$surat->nomor_surat) {
                NomorSuratHelper::generate($surat);
            }

            // 2️⃣ Token (sekali)
            $token = $surat->signed_token ?? Str::uuid()->toString();

            // 3️⃣ QR (PASTI ADA)
            if (!$surat->qr_svg) {
                $qrUrl = TtdHelper::url($token);
                $qrSvg = QrHelper::make($qrUrl, 200);
            } else {
                $qrSvg = $surat->qr_svg;
            }

            // 4️⃣ Update legal artefact (idempotent)
            $surat->update([
                'signed_by_user_id' => $surat->signed_by_user_id ?? $user->id,
                'signed_token'      => $token,
                'signed_at'         => $surat->signed_at ?? now(),
                'qr_svg'            => $qrSvg,
                'status'            => 'final',
                'is_completed'      => true,
            ]);
        });
    }

    public static function ttdPembuat(Surat $surat, $user): void
    {
        if ($surat->signed_by_user_id) {
            return;
        }

        if (!$surat->nomor_surat) {
            NomorSuratHelper::generate($surat);
        }

        $token = $surat->signed_token ?? Str::uuid()->toString();

        $qrUrl = route('surat.verify', $token);
        $qrSvg = QrHelper::make($qrUrl, 120);

        $surat->update([
            'signed_by_user_id' => $user->id,
            'signed_at'         => now(),
            'signed_token'      => $token,
            'qr_svg'            => $qrSvg, 
        ]);
    }
}