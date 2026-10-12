<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // GET /admin/dashboard  (middleware: auth, admin)
    public function dashboard(Request $request)
    {
        $nama = $request->user()->nama_lengkap;

        return view('admin.dashboard', [
            'nama'    => $nama,
            'initial' => strtoupper(mb_substr($nama, 0, 1)),
        ]);
    }
}
