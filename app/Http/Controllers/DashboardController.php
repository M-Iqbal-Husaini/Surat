<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Surat;
use App\Models\SuratDisposisi;
use App\Models\TemplateSurat;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // admin IT = super admin sistem
        if ($user->hasRole('admin')) {
            return $this->adminDashboard($user);
        }

        // pimpinan (jurusan / institusi)
        if ($user->hasAnyRole(['pimpinan'])) {
            return $this->pimpinanDashboard($user);
        }

        // verifikator
        if ($user->hasRole('verifikator')) {
            return $this->verifikatorDashboard($user);
        }

        // sekretaris direktur
        if ($user->hasRole('sekretaris_direktur')) {
            return $this->sekretarisDashboard($user);
        }

        // pembuat surat
        if ($user->hasRole('pembuat_surat')) {
            return $this->pembuatSuratDashboard($user);
        }

        // fallback kalau belum punya role / untuk testing
        abort(403, 'Anda tidak memiliki akses ke dashboard.');
    }

    protected function adminDashboard(User $user)
    {
        return view('admin.dashboard', [
            'jumlahUser'        => User::count(),
            'jumlahSuratMasuk'  => Surat::whereNotNull('unit_tujuan_id')->count(),
            'jumlahSuratKeluar' => Surat::whereNotNull('pembuat_id')->count(),
            'jumlahDisposisi'   => SuratDisposisi::count(),
            'jumlahTemplateSurat' => TemplateSurat::count(),
        ]);
    }


    protected function pimpinanDashboard(User $user)
    {
        if (!$user->unit_id) {
            abort(500, 'Pimpinan tidak memiliki unit.');
        }

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)->count();

        $jumlahDisposisi = SuratDisposisi::whereHas('surat', function ($q) use ($user) {
            $q->where('unit_tujuan_id', $user->unit_id);
        })->count();

        return view('pimpinan.dashboard', [
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => 0, // pimpinan tidak bikin surat
            'jumlahDisposisi'   => $jumlahDisposisi,
        ]);
    }


    protected function verifikatorDashboard(User $user)
    {
        if (!$user->jabatan_id) {
            abort(500, 'Verifikator tidak memiliki jabatan.');
        }

        $jumlahSuratMasuk = Surat::whereHas('verifikasi', function ($q) use ($user) {
            $q->where('jabatan_id', $user->jabatan_id)
            ->whereIn('status', ['menunggu', 'diproses']);
        })->count();

        $jumlahSuratSelesai = Surat::whereHas('verifikasi', function ($q) use ($user) {
            $q->where('jabatan_id', $user->jabatan_id)
            ->whereIn('status', ['diterima', 'ditolak', 'ditandatangani']);
        })->count();

        return view('verifikator.dashboard', [
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => 0,
            'jumlahDisposisi'   => $jumlahSuratSelesai,
        ]);
    }

    protected function sekretarisDashboard(User $user)
    {
        if (!$user->unit_id) {
            abort(500, 'Sekretaris Direktur tidak memiliki unit.');
        }

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)->count();

        $jumlahSuratKeluar = Surat::where('unit_asal_id', $user->unit_id)->count();

        $jumlahDisposisi = SuratDisposisi::whereHas('surat', function ($q) use ($user) {
            $q->where('unit_tujuan_id', $user->unit_id);
        })->count();

        return view('sekretaris-direktur.dashboard', [
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
            'jumlahDisposisi'   => $jumlahDisposisi,
        ]);
    }


    protected function pembuatSuratDashboard(User $user)
    {
        $jumlahSuratKeluar = Surat::where('pembuat_id', $user->id)->count();

        $jumlahSuratMasuk = Surat::where('unit_tujuan_id', $user->unit_id)
            ->where('pembuat_id', '!=', $user->id)
            ->count();

        $jumlahDisposisi = SuratDisposisi::whereHas('surat', function ($q) use ($user) {
            $q->where('unit_tujuan_id', $user->unit_id);
        })->count();

        return view('pembuat-surat.dashboard', [
            'jumlahSuratMasuk'  => $jumlahSuratMasuk,
            'jumlahSuratKeluar' => $jumlahSuratKeluar,
            'jumlahDisposisi'   => $jumlahDisposisi,
        ]);
    }


}
