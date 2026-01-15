<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Surat;
use App\Models\SuratDisposisi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard($user);
        }

        if ($user->hasAnyRole(['pimpinan'])) {
            return $this->pimpinanDashboard($user);
        }

        if ($user->hasRole('verifikator')) {
            return $this->verifikatorDashboard($user);
        }

        if ($user->hasRole('sekretaris_direktur')) {
            return $this->sekretarisDashboard($user);
        }

        if ($user->hasRole('pembuat_surat')) {
            return $this->pembuatSuratDashboard($user);
        }

        abort(403);
    }

    /* =========================================================
     * CORE ANALYTICS (SOURCE OF TRUTH)
     * ========================================================= */
    protected function analyticsSurat(
        callable $queryMasuk,
        callable $queryKeluar
    ) {
        $year = now()->year;

        $masuk  = array_fill(0, 12, 0);
        $keluar = array_fill(0, 12, 0);

        $suratMasuk = $queryMasuk(
            Surat::whereYear('tanggal_surat', $year)
        )->get(['tanggal_surat']);

        foreach ($suratMasuk as $s) {
            $bulan = Carbon::parse($s->tanggal_surat)->month - 1;
            $masuk[$bulan]++;
        }

        $suratKeluar = $queryKeluar(
            Surat::whereYear('tanggal_surat', $year)
        )->get(['tanggal_surat']);

        foreach ($suratKeluar as $s) {
            $bulan = Carbon::parse($s->tanggal_surat)->month - 1;
            $keluar[$bulan]++;
        }

        return [
            'chartMasuk'  => $masuk,
            'chartKeluar' => $keluar,
            'tahun'       => $year,
        ];
    }


    /* ================= ADMIN ================= */
    protected function adminDashboard(User $user)
    {
        return view('admin.dashboard', array_merge([
            'jumlahUser'        => User::count(),
            'jumlahSuratMasuk'  => Surat::whereNotNull('unit_tujuan_id')->count(),
            'jumlahSuratKeluar' => Surat::whereNotNull('pembuat_id')->count(),
        ], $this->analyticsSurat(
                fn ($q) => $q->whereNotNull('unit_tujuan_id'),
                fn ($q) => $q->whereNotNull('pembuat_id')
            )
            ));
    }

    /* ================= PIMPINAN ================= */
    protected function pimpinanDashboard(User $user)
    {
        abort_if(!$user->unit_id, 500);

        return view('pimpinan.dashboard', array_merge([
            'jumlahSuratMasuk'  => Surat::where('unit_tujuan_id', $user->unit_id)->count(),
            'jumlahSuratKeluar' => 0,
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q->whereRaw('0 = 1') // tidak ada surat keluar
        )
        ));
    }

    /* ================= VERIFIKATOR ================= */
    protected function verifikatorDashboard(User $user)
    {
        abort_if(!$user->jabatan_id, 500);

        return view('verifikator.dashboard', array_merge([
            'jumlahSuratMasuk' => Surat::whereHas('verifikasi', function ($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id);
            })->count(),
            'jumlahSuratKeluar' => 0,
        ], $this->analyticsSurat(
            fn ($q) => $q->whereHas('verifikasi', fn ($v) =>
                $v->where('jabatan_id', $user->jabatan_id)
            ),
            fn ($q) => $q->whereRaw('0 = 1')
        )
        ));
    }

    /* ================= SEKRETARIS ================= */
    protected function sekretarisDashboard(User $user)
    {
        abort_if(!$user->unit_id, 500);

        return view('sekretaris-direktur.dashboard', array_merge([
            'jumlahSuratMasuk'  => Surat::where('unit_tujuan_id', $user->unit_id)->count(),
            'jumlahSuratKeluar' => Surat::where('unit_asal_id', $user->unit_id)->count(),
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q->where('unit_asal_id', $user->unit_id)
        )
        ));
    }

    /* ================= PEMBUAT SURAT ================= */
    protected function pembuatSuratDashboard(User $user)
    {
        abort_if(!$user->jabatan_id, 500);

        $jumlahDisposisi = SuratDisposisi::where('ke_jabatan_id', $user->jabatan_id)
            ->where('status', 'pending')
            ->count();

        return view('pembuat-surat.dashboard', array_merge([
            'jumlahSuratMasuk'  => Surat::where('unit_tujuan_id', $user->unit_id)->count(),
            'jumlahSuratKeluar' => Surat::where('pembuat_id', $user->id)->count(),
            'jumlahDisposisi'   => $jumlahDisposisi,
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id)
                        ->where('pembuat_id', '!=', $user->id),
            fn ($q) => $q->where('pembuat_id', $user->id)
        )));
    }

}
