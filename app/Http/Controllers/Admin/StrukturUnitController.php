<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StrukturUnitController extends Controller
{
    public function index()
    {
        return view('admin.struktur-unit.index');
    }
}
