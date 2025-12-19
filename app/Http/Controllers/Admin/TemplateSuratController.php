<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TemplateSuratController extends Controller
{
    public function index()
    {
        return view('admin.template-surat.index');
    }
}
