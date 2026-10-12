<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /user/beranda
    public function beranda(Request $request)
    {
        $user = $request->user();

        // Belum login -> tampilkan halaman login
        if (! $user) {
            return view('user.login');
        }

        // Admin tidak boleh mengakses halaman pasien
        if ($user->isAdmin()) {
            return redirect('admin/dashboard');
        }

        return view('user.beranda', [
            'nama'    => $user->nama_lengkap,
            'initial' => strtoupper(mb_substr($user->nama_lengkap, 0, 1)),
            'role'    => $user->role,
        ]);
    }
}
