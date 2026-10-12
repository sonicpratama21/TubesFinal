<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>body{margin:0;font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body>
<div class="login-page">
  <div class="modal">
    <div class="login-brand">
      <img src="{{ asset('assets/Logo.png') }}" alt="Logo">
      <span>Telko<em>Medika</em></span>
    </div>

    {{-- Form Login --}}
    <div id="loginForm">
      <h2>Masuk <span>Akun</span></h2>
      <div id="loginAlert" class="alert"></div>
      <input type="text" id="loginUsername" placeholder="Username" autocomplete="username">
      <input type="password" id="loginPassword" placeholder="Password" autocomplete="current-password">
      <button type="button" class="btn-primary" id="loginBtn" onclick="doLogin()">Masuk</button>
      <button type="button" class="btn-secondary" onclick="showRegister()">Belum punya akun? Daftar</button>
    </div>

    {{-- Form Register --}}
    <div id="registerForm" class="hidden">
      <h2>Daftar <span>Akun</span></h2>
      <div id="registerAlert" class="alert"></div>
      <input type="text" id="regNama" placeholder="Nama Lengkap">
      <input type="text" id="regUsername" placeholder="Username" autocomplete="username">
      <input type="email" id="regEmail" placeholder="Email">
      <input type="password" id="regPassword" placeholder="Password (min. 6 karakter)" autocomplete="new-password">
      <input type="password" id="regKonfirm" placeholder="Konfirmasi Password" autocomplete="new-password">
      <button type="button" class="btn-primary" id="regBtn" onclick="doRegister()">Daftar Sekarang</button>
      <button type="button" class="btn-secondary" onclick="showLogin()">Sudah punya akun? Masuk</button>
    </div>
  </div>
</div>
<script>
const BASE_URL = '{{ url('/') }}/';
</script>
<script src="{{ asset('js/csrf.js') }}"></script>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
