<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TelkomMedika — Layanan Kesehatan Terpercaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

{{-- ═════════ NAVBAR ═════════ --}}
<nav class="navbar" id="navbar">
  <a href="{{ url('/') }}" class="nav-brand">
    <img src="{{ asset('assets/Logo.png') }}" alt="Logo TelkomMedika">
    <span class="nav-brand-text">Telko<span>Medika</span></span>
  </a>

  <ul class="nav-links">
    <li><a href="#beranda">Beranda</a></li>
    <li><a href="#layanan">Layanan</a></li>
    <li><a href="#dokter">Dokter</a></li>
    <li><a href="#reservasi">Reservasi</a></li>
  </ul>

  <div class="nav-cta">
    @if ($loggedIn)
      <a href="{{ $dashboardUrl }}"><button class="btn-reservasi">Dashboard ({{ $nama }})</button></a>
      <a href="{{ url('auth/logout') }}"><button class="btn-login">Keluar</button></a>
    @else
      <button class="btn-login" onclick="openLogin()">Masuk</button>
      <button class="btn-reservasi" onclick="openLogin('register')">Daftar</button>
    @endif
  </div>
</nav>

{{-- ═════════ HERO ═════════ --}}
<section class="hero" id="beranda">
  <div class="hero-inner">
    <div class="hero-text fade-up">
      <div class="hero-eyebrow"><span class="hero-eyebrow-dot"></span>Reservasi Online 24 Jam</div>
      <h1 class="hero-title">Kesehatan Anda,<br><em>Prioritas Kami</em></h1>
      <p class="hero-desc">
        TelkomMedika memudahkan Anda membuat jadwal kunjungan medis — pilih layanan,
        pilih dokter, tentukan waktu. Tanpa antre, tanpa ribet.
      </p>
      <div class="hero-actions">
        <button class="hero-btn-primary" onclick="goReservasi()">Buat Reservasi</button>
        <a href="#layanan"><button class="hero-btn-secondary">Lihat Layanan</button></a>
      </div>
      <div class="hero-stats">
        <div><div class="hero-stat-num">4</div><div class="hero-stat-label">Layanan Medis</div></div>
        <div class="hero-stat-divider"></div>
        <div><div class="hero-stat-num">5</div><div class="hero-stat-label">Dokter Praktik</div></div>
        <div class="hero-stat-divider"></div>
        <div><div class="hero-stat-num">3</div><div class="hero-stat-label">Langkah Reservasi</div></div>
      </div>
    </div>

    <div class="hero-visual fade-up">
      <div class="hero-img-wrap">
        <img src="{{ asset('assets/TelkoMedika.png') }}" alt="TelkomMedika">
      </div>
      <div class="float-card float-card-left">
        <div class="float-icon red">📅</div>
        <div><div class="float-label">Reservasi</div><div class="float-value">Mudah &amp; Cepat</div></div>
      </div>
      <div class="float-card float-card-right">
        <div class="float-icon green">✅</div>
        <div><div class="float-label">Status</div><div class="float-value">Dipantau Online</div></div>
      </div>
    </div>
  </div>
</section>

{{-- ═════════ LAYANAN ═════════ --}}
<section class="section" id="layanan">
  <div class="section-inner">
    <div class="section-header fade-up">
      <div class="section-eyebrow">Layanan Kami</div>
      <h2 class="section-title">Layanan Medis Lengkap</h2>
      <p class="section-sub">Pilih layanan yang Anda butuhkan, lalu reservasi jadwalnya langsung dari akun Anda.</p>
    </div>
    <div class="layanan-grid">
      <div class="layanan-card fade-up" onclick="goReservasi()">
        <div class="layanan-icon">🩺</div>
        <div class="layanan-name">Poli Umum</div>
        <div class="layanan-desc">Pemeriksaan kesehatan umum, konsultasi keluhan, dan pengobatan dasar.</div>
        <div class="layanan-tag">Reservasi →</div>
      </div>
      <div class="layanan-card fade-up" onclick="goReservasi()">
        <div class="layanan-icon">🦷</div>
        <div class="layanan-name">Poli Gigi</div>
        <div class="layanan-desc">Pemeriksaan dan perawatan kesehatan gigi serta mulut.</div>
        <div class="layanan-tag">Reservasi →</div>
      </div>
      <div class="layanan-card fade-up" onclick="goReservasi()">
        <div class="layanan-icon">👁️</div>
        <div class="layanan-name">Poli Mata</div>
        <div class="layanan-desc">Pemeriksaan ketajaman penglihatan dan kesehatan mata.</div>
        <div class="layanan-tag">Reservasi →</div>
      </div>
      <div class="layanan-card fade-up" onclick="goReservasi()">
        <div class="layanan-icon">🧪</div>
        <div class="layanan-name">Laboratorium</div>
        <div class="layanan-desc">Pemeriksaan laboratorium untuk menunjang diagnosis dokter.</div>
        <div class="layanan-tag">Reservasi →</div>
      </div>
    </div>
  </div>
</section>

{{-- ═════════ DOKTER ═════════ --}}
<section class="section" id="dokter">
  <div class="section-inner">
    <div class="section-header fade-up">
      <div class="section-eyebrow">Tim Medis</div>
      <h2 class="section-title">Dokter Kami</h2>
      <p class="section-sub">Tenaga medis berpengalaman yang siap melayani Anda.</p>
    </div>
    <div class="dokter-grid">
      <div class="dokter-card fade-up">
        <div class="dokter-img">
          <img class="foto" src="{{ asset('assets/dr_eko.png') }}" alt="dr. Agus Subianto">
          <span class="dokter-badge">Tersedia</span>
        </div>
        <div class="dokter-info">
          <div class="dokter-spec">Dokter</div>
          <div class="dokter-name">dr. Agus Subianto</div>
          <div class="dokter-jadwal">Reservasi tersedia online</div>
        </div>
      </div>
      <div class="dokter-card fade-up">
        <div class="dokter-img">
          <img class="foto" src="{{ asset('assets/dr_ratna.png') }}" alt="dr. Ratna Sumiatni">
          <span class="dokter-badge">Tersedia</span>
        </div>
        <div class="dokter-info">
          <div class="dokter-spec">Dokter</div>
          <div class="dokter-name">dr. Ratna Sumiatni</div>
          <div class="dokter-jadwal">Reservasi tersedia online</div>
        </div>
      </div>
      <div class="dokter-card fade-up">
        <div class="dokter-img">
          <img class="foto" src="{{ asset('assets/dr_yani.png') }}" alt="dr. Siti Rahayu">
          <span class="dokter-badge">Tersedia</span>
        </div>
        <div class="dokter-info">
          <div class="dokter-spec">Dokter</div>
          <div class="dokter-name">dr. Siti Rahayu</div>
          <div class="dokter-jadwal">Reservasi tersedia online</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═════════ STATS ═════════ --}}
<section class="stats-band">
  <div class="stats-inner">
    <div class="fade-up"><div class="stat-num">4</div><div class="stat-label">Layanan Medis</div></div>
    <div class="fade-up"><div class="stat-num">5</div><div class="stat-label">Dokter Praktik</div></div>
    <div class="fade-up"><div class="stat-num">3</div><div class="stat-label">Langkah Reservasi</div></div>
    <div class="fade-up"><div class="stat-num">24/7</div><div class="stat-label">Reservasi Online</div></div>
  </div>
</section>

{{-- ═════════ RESERVASI ═════════ --}}
<section class="section" id="reservasi">
  <div class="reservasi-inner">
    <div class="reservasi-left fade-up">
      <div class="section-eyebrow">Reservasi</div>
      <h2 class="section-title">Buat Janji dalam Hitungan Menit</h2>
      <p class="section-sub">Daftar atau masuk ke akun Anda, isi formulir, dan jadwal kunjungan langsung tercatat.</p>
      <div class="reservasi-features">
        <div class="res-feature"><span class="res-feature-dot"></span>Pilih layanan dan dokter sesuai kebutuhan</div>
        <div class="res-feature"><span class="res-feature-dot"></span>Tentukan tanggal dan jam kunjungan</div>
        <div class="res-feature"><span class="res-feature-dot"></span>Pantau status reservasi dari dashboard</div>
      </div>
      <button class="hero-btn-primary" onclick="goReservasi()">Mulai Reservasi</button>
    </div>

    <div class="reservasi-form-card fade-up">
      <div class="form-title">Reservasi Cepat</div>
      <div class="form-group">
        <label>Jenis Layanan</label>
        <select id="q-layanan">
          <option>Pemeriksaan Umum</option>
          <option>Pemeriksaan Gigi</option>
          <option>Pemeriksaan Mata</option>
          <option>Laboratorium</option>
        </select>
      </div>
      <div class="form-group">
        <label>Dokter</label>
        <select id="q-dokter">
          <option>dr. Agus Subianto</option>
          <option>dr. Aryasatya Ramadhan</option>
          <option>dr. Ratna Sumiatni</option>
          <option>dr. Andi Prasetyo</option>
          <option>dr. Siti Rahayu</option>
        </select>
      </div>
      <div class="form-group">
        <label>Tanggal Kunjungan</label>
        <input type="date" id="q-tanggal" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
      </div>
      <button class="form-submit" onclick="goReservasi()">Lanjutkan Reservasi</button>
    </div>
  </div>
</section>

{{-- ═════════ FOOTER ═════════ --}}
<footer>
  <div class="footer-inner">
    <div>
      <div class="footer-brand">Telko<span>Medika</span></div>
      <div class="footer-tagline">Layanan kesehatan yang mudah dijangkau.</div>
    </div>
    <div class="footer-links">
      <a href="#layanan">Layanan</a>
      <a href="#dokter">Dokter</a>
      <a href="#reservasi">Reservasi</a>
    </div>
    <div class="footer-copy">&copy; {{ date('Y') }} TelkomMedika</div>
  </div>
</footer>

{{-- ═════════ MODAL LOGIN / REGISTER ═════════ --}}
@unless ($loggedIn)
<div class="login-wrapper" id="loginWrapper">
  <div class="overlay" onclick="closeLogin()"></div>
  <div class="modal">
    <div id="loginForm">
      <h2>Masuk <span>Akun</span></h2>
      <div id="loginAlert" class="alert"></div>
      <input type="text" id="loginUsername" placeholder="Username" autocomplete="username">
      <input type="password" id="loginPassword" placeholder="Password" autocomplete="current-password">
      <button type="button" class="btn-primary" id="loginBtn" onclick="doLogin()">Masuk</button>
      <button type="button" class="btn-secondary" onclick="showRegister()">Belum punya akun? Daftar</button>
    </div>

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
@endunless

<script>
const BASE_URL   = '{{ url('/') }}/';
const IS_LOGGED  = @json($loggedIn);
const DASHBOARD  = @json($dashboardUrl);
</script>
<script src="{{ asset('js/csrf.js') }}"></script>
<script src="{{ asset('js/login.js') }}"></script>
<script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
