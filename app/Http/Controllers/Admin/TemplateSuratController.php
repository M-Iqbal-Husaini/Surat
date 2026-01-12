<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use App\Helpers\TemplateBodyGenerator;
use App\Models\JenisSurat;
use App\Models\AlurVerifikasi;
use Illuminate\Http\Request;
use App\Helpers\TemplateRenderer;
use App\Models\Jabatan;

class TemplateSuratController extends Controller
{
    /* ================= INDEX ================= */
    public function index()
    {
        $templates = TemplateSurat::with(['jenisSurat', 'alurVerifikasi'])
            ->orderBy('kode_template')
            ->get();

        return view('admin.template-surat.index', compact('templates'));
    }

    /* ================= CREATE ================= */
    public function create()
    {
        return view('admin.template-surat.create', [
            'jenisSurat'     => JenisSurat::orderBy('kode')->get(),
            'alurVerifikasi' => AlurVerifikasi::orderBy('nama_alur')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_template'  => 'required|unique:template_surat,kode_template',
            'nama_template'  => 'required|string|max:255',
            'jenis_surat_id' => 'required|exists:jenis_surat,id',
            'kode_alur'      => 'required|exists:alur_verifikasi,kode_alur',
            'penandatangan_jabatan_id' => 'nullable|exists:jabatans,id',
        ]);

        $jenis = JenisSurat::findOrFail($validated['jenis_surat_id']);

        TemplateSurat::create([
            'kode_template'  => $validated['kode_template'],
            'nama_template'  => $validated['nama_template'],
            'jenis_surat_id' => $validated['jenis_surat_id'],
            'kode_alur'      => $validated['kode_alur'],
            'penandatangan_jabatan_id' => $validated['penandatangan_jabatan_id'] ?? null,
            'body_html'      => TemplateBodyGenerator::generate($jenis->kode),
            'is_active'      => true,
        ]);

        return redirect()->route('admin.template-surat.index')
            ->with('success', 'Template surat berhasil dibuat');
    }


    /* ================= SHOW ================= */
    public function show(TemplateSurat $template)
    {
        $template->load(['jenisSurat', 'alurVerifikasi', 'fields']);

        $previewHtml = TemplateRenderer::render($template);

        return view('admin.template-surat.detail', compact('template', 'previewHtml'));
    }

    /* ================= EDIT ================= */
    public function edit(TemplateSurat $template)
    {
        return view('admin.template-surat.edit', [
            'template'       => $template,
            'jenisSurat'     => JenisSurat::orderBy('kode')->get(),
            'alurVerifikasi' => AlurVerifikasi::orderBy('nama_alur')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
        ]);
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, TemplateSurat $template)
    {
        $validated = $request->validate([
            'kode_template'  => 'required|unique:template_surat,kode_template,' . $template->id,
            'nama_template'  => 'required|string|max:255',
            'jenis_surat_id' => 'required|exists:jenis_surat,id',
            'kode_alur'      => 'required|exists:alur_verifikasi,kode_alur',
            'penandatangan_jabatan_id' => 'nullable|exists:jabatans,id',
            'is_active'      => 'required|boolean',
        ]);

        $template->update($validated);

        return redirect()
            ->route('admin.template-surat.show', $template->id)
            ->with('success', 'Template surat berhasil diperbarui');
    }

    /* ================= DELETE ================= */
    public function destroy(TemplateSurat $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.template-surat.index')
            ->with('success', 'Template surat berhasil dihapus');
    }
}
