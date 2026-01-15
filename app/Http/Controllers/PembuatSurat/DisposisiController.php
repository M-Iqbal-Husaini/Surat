<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratDisposisi;
use Illuminate\Support\Facades\Auth;
use App\Services\SuratWorkflowService;

class DisposisiController extends Controller
{
    /**
     * ==================================================
     * INDEX – DAFTAR DISPOSISI AKTIF SAYA
     * ==================================================
     */
    public function index()
    {
        $jabatanId = Auth::user()->jabatan_id;

        $disposisi = SuratDisposisi::with([
                'surat.template',
                'surat.unitAsal',
            ])
            ->where('ke_jabatan_id', $jabatanId)
            ->where(function ($q) {
                $q->where('status', 'pending')
                ->orWhere(function ($x) {
                    $x->where('status', 'selesai')
                        ->whereDate('updated_at', now()->toDateString());
                });
            })
            ->orderByRaw("
                CASE 
                    WHEN status = 'pending' THEN 1
                    ELSE 2
                END
            ")
            ->orderByDesc('updated_at')
            ->get();

        return view(
            'pembuat-surat.disposisi.index',
            compact('disposisi')
        );
    }


    /**
     * ==================================================
     * SHOW DISPOSISI
     * ==================================================
     */
    public function show(SuratDisposisi $disposisi)
    {
        abort_if(
            $disposisi->ke_jabatan_id !== Auth::user()->jabatan_id,
            403
        );

        $disposisi->load([
            'surat.template.jenisSurat',
            'surat.unitAsal',
        ]);

        $html = $disposisi->surat->template->body_html;

        foreach ($disposisi->surat->data_json ?? [] as $key => $value) {
            $html = str_replace(
                ['{{ '.$key.' }}', '{{'.$key.'}}'],
                e($value),
                $html
            );
        }

        return view('pembuat-surat.disposisi.show', [
            'disposisi'   => $disposisi,
            'surat'       => $disposisi->surat,
            'previewHtml' => $html,
        ]);
    }

    /**
     * ==================================================
     * TINDAK LANJUT DISPOSISI
     * ==================================================
     */
    public function tindaklanjuti(Surat $surat)
    {
        SuratWorkflowService::tindaklanjutiDisposisi(
            $surat,
            Auth::user()
        );

        return redirect()
            ->route('pembuat-surat.disposisi.index')
            ->with('success', 'Disposisi berhasil ditindaklanjuti.');
    }
}
