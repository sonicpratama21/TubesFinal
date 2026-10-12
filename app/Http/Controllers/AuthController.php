<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /auth/login
    public function login(Request $request): JsonResponse
    {
        $username = trim((string) $request->input('username', ''));
        $password = (string) $request->input('password', '');

        if ($username === '' || $password === '') {
            return $this->error('Username dan password wajib diisi.');
        }

        $user = User::where('username', $username)->first();

        if (! $user) {
            return $this->error('Username tidak ditemukan atau role tidak sesuai.');
        }

        if (! Hash::check($password, $user->password)) {
            return $this->error('Password salah. Silakan coba lagi.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Login berhasil! Selamat datang, ' . $user->nama_lengkap . '.',
            'nama'     => $user->nama_lengkap,
            'role'     => $user->role,
            'redirect' => url($user->isAdmin() ? 'admin/dashboard' : 'user/beranda'),
        ]);
    }

    // POST /auth/register
    public function register(Request $request): JsonResponse
    {
        $nama     = trim((string) $request->input('nama', ''));
        $username = trim((string) $request->input('username', ''));
        $email    = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $konfirm  = (string) $request->input('konfirmasi', '');

        if ($nama === '' || $username === '' || $email === '' || $password === '') {
            return $this->error('Semua field wajib diisi.');
        }
        if ($password !== $konfirm) {
            return $this->error('Password dan konfirmasi tidak cocok.');
        }
        if (strlen($password) < 6) {
            return $this->error('Password minimal 6 karakter.');
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Format email tidak valid.');
        }
        if (User::where('username', $username)->exists()) {
            return $this->error('Username sudah digunakan. Pilih username lain.');
        }
        if (User::where('email', $email)->exists()) {
            return $this->error('Email sudah terdaftar.');
        }

        $user = new User([
            'nama_lengkap' => $nama,
            'username'     => $username,
            'email'        => $email,
            'password'     => $password, // di-hash otomatis oleh cast 'hashed'
        ]);
        $user->role = 'pengguna';

        if ($user->save()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil! Silakan login dengan akun Anda.',
            ]);
        }

        return $this->error('Gagal mendaftar. Silakan coba lagi.');
    }

    // GET /auth/logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function error(string $message): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message]);
    }
}
