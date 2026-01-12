<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use App\Models\TemplateIsiSurat;
use Illuminate\Http\Request;

class TemplateFieldController extends Controller
{
    /* ======================================================
     | INDEX
     | Menampilkan daftar field milik satu template surat
     ====================================================== */
    public function index(TemplateSurat $template)
    {
        return view('admin.template-surat.template-fields.index', [
            'template' => $template,
            'fields'   => $template->fields()
                ->orderBy('order')
                ->get(),
        ]);
    }

    /* ======================================================
     | STORE
     | Menambahkan field baru ke template
     ====================================================== */
    public function store(Request $request, TemplateSurat $template)
    {
        $validated = $request->validate([
            'field_key' => 'required|string|max:100',
            'label'     => 'required|string|max:255',
            'type'      => 'required|in:text,textarea,date,number',
            'required'  => 'nullable|boolean',
        ]);

        // Tentukan urutan terakhir
        $lastOrder = $template->fields()->max('order') ?? 0;

        $template->fields()->create([
            'field_key' => $validated['field_key'],
            'label'     => $validated['label'],
            'type'      => $validated['type'],
            'required'  => $request->boolean('required'),
            'order'     => $lastOrder + 1,
        ]);

        return redirect()
            ->route('admin.template-surat.template-fields.index', $template->id)
            ->with('success', 'Field template berhasil ditambahkan');
    }

    /* ======================================================
     | DESTROY
     | Menghapus field dari template (AMAN)
     ====================================================== */
    public function destroy(TemplateSurat $template, TemplateIsiSurat $field)
    {
        // 🔒 Pastikan field benar-benar milik template ini
        if ($field->template_id !== $template->id) {
            abort(404);
        }

        $field->delete();

        return redirect()
            ->route('admin.template-surat.template-fields.index', $template->id)
            ->with('success', 'Field template berhasil dihapus');
    }
}
