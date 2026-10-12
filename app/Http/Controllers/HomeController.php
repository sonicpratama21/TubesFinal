<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // GET /  — landing page
    public function index(Request $request)
    {
        $user = $request->user();

        return view('landing', [
            'loggedIn'     => (bool) $user,
            'nama'         => $user?->nama_lengkap,
            'dashboardUrl' => url($user && $user->isAdmin() ? 'admin/dashboard' : 'user/beranda'),
        ]);
    }
}
