<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiUsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

// ── Auth ─────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout']);
});

// ── Halaman ──────────────────────────────────────────────────────────────
Route::get('user/beranda', [UserController::class, 'beranda']);
Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'admin']);

// ── API JSON (otorisasi dicek di controller, respons sama seperti MVC lama) ──
Route::get('apiUsers', [ApiUsersController::class, 'index']);

Route::controller(ReservasiController::class)->prefix('reservasi')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/', 'update');
    Route::patch('/', 'updateStatus');
    Route::delete('/', 'destroy');
    Route::post('foto', 'foto');
});
