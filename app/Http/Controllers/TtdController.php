<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\Storage;

class TtdController extends Controller
{
    public function show(string $token)
    {
        $surat = Surat::where('qr_token', $token)->firstOrFail();

        if ($surat->status !== 'diajukan') {
            abort(403, 'Surat tidak dalam proses TTD');
        }

        return view('ttd.form', compact('surat'));
    }

    public function store(Request $request, string $token)
    {
        $surat = Surat::where('qr_token', $token)->firstOrFail();

        if ($surat->status !== 'diajukan') {
            abort(403);
        }

        $request->validate([
            'signed_name'     => 'required|string|max:100',
            'signed_jabatan'  => 'required|string|max:100',
            'signed_nip'      => 'required|string|max:50',
            'signature'       => 'required|image|max:2048',
        ]);

        // simpan file tanda tangan
        $path = $request->file('signature')
            ->store('signatures', 'public');

        $surat->update([
            'status'           => 'diajukan',
            'signed_at'        => now(),
            'signed_name'      => $request->signed_name,
            'signed_jabatan'   => $request->signed_jabatan,
            'signed_nip'       => $request->signed_nip,
            'signed_signature' => $path,
        ]);

        return view('ttd.success');     

    }
}