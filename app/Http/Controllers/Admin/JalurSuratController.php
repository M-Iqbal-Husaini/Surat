<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class JalurSuratController extends Controller
{
    public function index()
    {
        return view('admin.jalur-surat.index');
    }
}
