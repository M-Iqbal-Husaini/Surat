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

        return match (true) {
            $user->hasRole('admin')               => $this->adminDashboard(),
            $user->hasRole('pimpinan')            => $this->pimpinanDashboard($user),
            $user->hasRole('verifikator')          => $this->verifikatorDashboard($user),
            $user->hasRole('sekretaris_direktur')  => $this->sekretarisDashboard($user),
            $user->hasRole('pembuat_surat')        => $this->pembuatSuratDashboard($user),
            default                                => abort(403),
        };
    }

    /* =========================================================
     * CORE ANALYTICS (BERBASIS UNIT / ROLE)
     * ========================================================= */
    protected function analyticsSurat(callable $queryMasuk, callable $queryKeluar)
    {
        $tahun  = now()->year;
        $masuk  = array_fill(0, 12, 0);
        $keluar = array_fill(0, 12, 0);

        $queryMasuk(
            Surat::whereYear('tanggal_surat', $tahun)
        )->get(['tanggal_surat'])->each(function ($s) use (&$masuk) {
            $masuk[Carbon::parse($s->tanggal_surat)->month - 1]++;
        });

        $queryKeluar(
            Surat::whereYear('tanggal_surat', $tahun)
        )->get(['tanggal_surat'])->each(function ($s) use (&$keluar) {
            $keluar[Carbon::parse($s->tanggal_surat)->month - 1]++;
        });

        return [
            'chartMasuk'  => $masuk,
            'chartKeluar' => $keluar,
            'tahun'       => $tahun,
        ];
    }

    /* =========================================================
     * ADMIN (GLOBAL)
     * ========================================================= */
    protected function adminDashboard()
    {
        return view('admin.dashboard', array_merge([
            'jumlahUser'        => User::count(),
            'jumlahSuratMasuk'  => Surat::whereNotNull('unit_tujuan_id')->count(),
            'jumlahSuratKeluar' => Surat::whereNotNull('pembuat_id')->count(),
        ], $this->analyticsSurat(
            fn ($q) => $q->whereNotNull('unit_tujuan_id'),
            fn ($q) => $q->whereNotNull('pembuat_id')
        )));
    }

    /* =========================================================
     * PIMPINAN (BERBASIS UNIT TUJUAN)
     * ========================================================= */
    protected function pimpinanDashboard(User $user)
    {
        abort_if(!$user->unit_id, 500);

        return view('pimpinan.dashboard', array_merge([
            'jumlahSuratMasuk'  => Surat::where('unit_tujuan_id', $user->unit_id)->count(),
            'jumlahSuratKeluar' => 0,
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q->whereRaw('1 = 0')
        )));
    }

    /* =========================================================
     * VERIFIKATOR (UNIT + JABATAN)
     * ========================================================= */
    protected function verifikatorDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        return view('verifikator.dashboard', array_merge([
            'jumlahSuratMasuk' => Surat::where('unit_tujuan_id', $user->unit_id)
                ->whereHas('verifikasi', fn ($q) =>
                    $q->where('jabatan_id', $user->jabatan_id)
                )
                ->count(),
            'jumlahSuratKeluar' => 0,
        ], $this->analyticsSurat(
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->whereHas('verifikasi', fn ($v) =>
                    $v->where('jabatan_id', $user->jabatan_id)
                ),
            fn ($q) => $q->whereRaw('1 = 0')
        )));
    }

    /* =========================================================
     * SEKRETARIS DIREKTUR
     * ========================================================= */
    protected function sekretarisDashboard(User $user)
    {
        abort_if(!$user->unit_id, 500);

        return view('sekretaris-direktur.dashboard', array_merge([
            'jumlahSuratMasuk'  => Surat::where('unit_tujuan_id', $user->unit_id)->count(),
            'jumlahSuratKeluar' => Surat::where('unit_asal_id', $user->unit_id)->count(),
        ], $this->analyticsSurat(
            fn ($q) => $q->where('unit_tujuan_id', $user->unit_id),
            fn ($q) => $q->where('unit_asal_id', $user->unit_id)
        )));
    }

    /* =========================================================
     * PEMBUAT SURAT (UNIT TUJUAN = SURAT MASUK)
     * ========================================================= */
    protected function pembuatSuratDashboard(User $user)
    {
        abort_if(!$user->unit_id || !$user->jabatan_id, 500);

        /* =========================================================
        * SURAT MASUK (BERDASARKAN UNIT TUJUAN)
        * ========================================================= */
        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('pembuat_id', '!=', $user->id)
            ->count();

        /* =========================================================
        * SURAT KELUAR (SURAT YANG SAYA BUAT)
        * ========================================================= */
        $jumlahSuratKeluar = Surat::where('pembuat_id', $user->id)->count();

        /* =========================================================
        * DISPOSISI AKTIF KE JABATAN SAYA
        * ========================================================= */
        $jumlahDisposisi = SuratDisposisi::where('ke_jabatan_id', $user->jabatan_id)
            ->where('status', 'pending')
            ->count();

        /* =========================================================
        * ANALYTICS (CHART)
        * ========================================================= */
        $analytics = $this->analyticsSurat(
            // Surat Masuk (unit tujuan)
            fn ($q) => $q
                ->where('unit_tujuan_id', $user->unit_id)
                ->where('pembuat_id', '!=', $user->id),

            // Surat Keluar (dibuat oleh saya)
            fn ($q) => $q->where('pembuat_id', $user->id)
        );

        return view('pembuat-surat.dashboard', array_merge([
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
            'jumlahDisposisi'   => $jumlahDisposisi,
        ], $analytics));
    }

}
