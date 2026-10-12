<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TelkomMedika — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand-name">Telko</div>
    <div class="brand-sub">Medika</div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Utama</div>
    <button class="nav-item active" onclick="switchView('dashboard',this)">
      <span class="nav-icon"></span> Dashboard
    </button>
    <button class="nav-item" onclick="switchView('reservasi',this)">
      <span class="nav-icon"></span> Manajemen Reservasi
      <span class="nav-badge" id="nav-badge-res">0</span>
    </button>
    <button class="nav-item" onclick="switchView('pengguna',this)">
      <span class="nav-icon"></span> Kelola Pengguna
    </button>
  </nav>
  <div class="sidebar-bottom">
    <div class="sidebar-user">
      <div class="user-avatar">{{ $initial }}</div>
      <div class="user-info">
        <div class="name">{{ $nama }}</div>
        <div class="role">Super Admin</div>
      </div>
    </div>
  </div>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-title">
      <h1 id="topbar-page-title">Dashboard</h1>
      <p id="tgl"></p>
    </div>
    <div class="topbar-search">
      <span class="search-icon"></span>
      <input type="text" id="search-input" placeholder="Cari pasien, dokter..." oninput="handleSearch()">
    </div>
    <div class="topbar-actions">
      <div class="topbar-btn" id="notif-btn" onclick="switchView('reservasi', document.querySelector('.nav-item:nth-child(2)'))">
        🔔
        <span class="notif-dot" id="notif-dot" style="display:none"></span>
      </div>
      <button class="topbar-logout" onclick="window.location.href='{{ url('auth/logout') }}'">Logout</button>
    </div>
  </header>

  <!-- ═══ VIEW: DASHBOARD ═══ -->
  <div class="view active" id="view-dashboard">
    <div class="alert-banner" id="alertBanner">
      <span></span>
      <span style="font-size:13.5px;color:#2e7d32">Login admin berhasil! Selamat datang, <strong>{{ $nama }}</strong></span>
      <span class="alert-close" onclick="this.parentElement.style.display='none'">×</span>
    </div>

    <!-- Stats -->
    <div class="section-header">
      <div class="section-title">Ringkasan Hari Ini</div>
      <div class="live-pill"><span class="live-dot"></span> Live</div>
    </div>
    <div class="stats-grid">
      <div class="stat-card red">
        <div class="stat-top"><div class="stat-icon-wrap"></div></div>
        <div class="stat-value" id="stat-total">–</div>
        <div class="stat-label">Total Reservasi Hari Ini</div>
        <div class="stat-sub" id="stat-total-sub">memuat...</div>
      </div>
      <div class="stat-card teal">
        <div class="stat-top"><div class="stat-icon-wrap"></div></div>
        <div class="stat-value" id="stat-menunggu">–</div>
        <div class="stat-label">Menunggu Konfirmasi</div>
        <div class="stat-sub">perlu tindakan admin</div>
      </div>
      <div class="stat-card amber">
        <div class="stat-top"><div class="stat-icon-wrap"></div></div>
        <div class="stat-value" id="stat-konfirmasi">–</div>
        <div class="stat-label">Terkonfirmasi</div>
        <div class="stat-sub">siap dilayani</div>
      </div>
      <div class="stat-card purple">
        <div class="stat-top"><div class="stat-icon-wrap"></div></div>
        <div class="stat-value" id="stat-layanan">–</div>
        <div class="stat-label">Jenis Layanan</div>
        <div class="stat-sub">aktif hari ini</div>
      </div>
    </div>

    <!-- Middle: Chart + Donut -->
    <div class="middle-row">
      <div class="chart-card">
        <div class="chart-header">
          <div class="chart-title">Kunjungan Pasien</div>
          <div class="chart-period">
            <button class="period-btn" onclick="setPeriod(this)">Harian</button>
            <button class="period-btn active" onclick="setPeriod(this)">Mingguan</button>
            <button class="period-btn" onclick="setPeriod(this)">Bulanan</button>
          </div>
        </div>
        <div class="bar-chart-wrap">
          <svg viewBox="0 0 560 180" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="barGradR" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#e8394a"/><stop offset="100%" stop-color="#ff8a94"/></linearGradient>
              <linearGradient id="barGradT" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#00b4a0"/><stop offset="100%" stop-color="#7ee8df"/></linearGradient>
            </defs>
            <line x1="0" y1="30" x2="560" y2="30" stroke="#e2e8f3" stroke-width="1"/>
            <line x1="0" y1="70" x2="560" y2="70" stroke="#e2e8f3" stroke-width="1"/>
            <line x1="0" y1="110" x2="560" y2="110" stroke="#e2e8f3" stroke-width="1"/>
            <line x1="0" y1="150" x2="560" y2="150" stroke="#e2e8f3" stroke-width="1"/>
            <text x="0" y="33" font-size="10" fill="#8a97b0">40</text><text x="0" y="73" font-size="10" fill="#8a97b0">30</text><text x="0" y="113" font-size="10" fill="#8a97b0">20</text><text x="0" y="153" font-size="10" fill="#8a97b0">10</text>
            <rect x="34" y="60" width="28" height="90" rx="5" fill="url(#barGradR)" opacity=".9"/><rect x="66" y="110" width="28" height="40" rx="5" fill="url(#barGradT)" opacity=".9"/>
            <rect x="118" y="50" width="28" height="100" rx="5" fill="url(#barGradR)" opacity=".9"/><rect x="150" y="100" width="28" height="50" rx="5" fill="url(#barGradT)" opacity=".9"/>
            <rect x="202" y="40" width="28" height="110" rx="5" fill="url(#barGradR)" opacity=".9"/><rect x="234" y="90" width="28" height="60" rx="5" fill="url(#barGradT)" opacity=".9"/>
            <rect x="286" y="30" width="28" height="120" rx="5" fill="url(#barGradR)" opacity=".9"/><rect x="318" y="80" width="28" height="70" rx="5" fill="url(#barGradT)" opacity=".9"/>
            <rect x="370" y="55" width="28" height="95" rx="5" fill="url(#barGradR)" opacity=".9"/><rect x="402" y="105" width="28" height="45" rx="5" fill="url(#barGradT)" opacity=".9"/>
            <rect x="454" y="80" width="28" height="70" rx="5" fill="url(#barGradR)" opacity=".6"/><rect x="486" y="120" width="28" height="30" rx="5" fill="url(#barGradT)" opacity=".6"/>
            <text x="62" y="172" font-size="10.5" fill="#4a5878" text-anchor="middle">Sen</text>
            <text x="146" y="172" font-size="10.5" fill="#4a5878" text-anchor="middle">Sel</text>
            <text x="230" y="172" font-size="10.5" fill="#4a5878" text-anchor="middle">Rab</text>
            <text x="314" y="172" font-size="10.5" fill="#4a5878" text-anchor="middle">Kam</text>
            <text x="398" y="172" font-size="10.5" fill="#4a5878" text-anchor="middle">Jum</text>
            <text x="482" y="172" font-size="10.5" fill="#8a97b0" text-anchor="middle">Sab</text>
          </svg>
        </div>
        <div style="display:flex;gap:20px;margin-top:4px;">
          <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-2)"><span style="width:10px;height:10px;background:var(--accent);border-radius:3px;display:inline-block"></span> Rawat Jalan</div>
          <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-2)"><span style="width:10px;height:10px;background:var(--teal);border-radius:3px;display:inline-block"></span> Rawat Inap</div>
        </div>
      </div>

      <div class="donut-card">
        <div class="chart-title">Distribusi Layanan</div>
        <div class="donut-chart-wrap">
          <svg viewBox="0 0 130 130" xmlns="http://www.w3.org/2000/svg">
            <circle cx="65" cy="65" r="48" fill="none" stroke="#e2e8f3" stroke-width="18"/>
            <circle cx="65" cy="65" r="48" fill="none" stroke="#e8394a" stroke-width="18" stroke-dasharray="105.6 196" stroke-dashoffset="0" transform="rotate(-90 65 65)"/>
            <circle cx="65" cy="65" r="48" fill="none" stroke="#00b4a0" stroke-width="18" stroke-dasharray="75.4 226.2" stroke-dashoffset="-105.6" transform="rotate(-90 65 65)"/>
            <circle cx="65" cy="65" r="48" fill="none" stroke="#7c3aed" stroke-width="18" stroke-dasharray="60.3 241.3" stroke-dashoffset="-181" transform="rotate(-90 65 65)"/>
            <circle cx="65" cy="65" r="48" fill="none" stroke="#f59e0b" stroke-width="18" stroke-dasharray="45.2 256.4" stroke-dashoffset="-241.3" transform="rotate(-90 65 65)"/>
            <text x="65" y="62" text-anchor="middle" font-size="20" font-weight="800" fill="#0f1c35" id="donut-total">–</text>
            <text x="65" y="76" text-anchor="middle" font-size="9.5" fill="#8a97b0">Pasien</text>
          </svg>
        </div>
        <div class="donut-legend">
          <div class="legend-item"><div class="legend-dot" style="background:#e8394a"></div><div class="legend-label">Perawatan Umum</div><div class="legend-val" id="leg-umum">–</div></div>
          <div class="legend-item"><div class="legend-dot" style="background:#00b4a0"></div><div class="legend-label">Rawat Jalan</div><div class="legend-val" id="leg-jalan">–</div></div>
          <div class="legend-item"><div class="legend-dot" style="background:#7c3aed"></div><div class="legend-label">Pengobatan Gigi</div><div class="legend-val" id="leg-gigi">–</div></div>
          <div class="legend-item"><div class="legend-dot" style="background:#f59e0b"></div><div class="legend-label">Rawat Inap</div><div class="legend-val" id="leg-inap">–</div></div>
          <div class="legend-item"><div class="legend-dot" style="background:#60a5fa"></div><div class="legend-label">Lainnya</div><div class="legend-val" id="leg-lain">–</div></div>
        </div>
      </div>
    </div>

    <!-- Bottom: Live table + Actions + Dokter -->
    <div class="bottom-row">
      <div class="table-card">
        <div class="table-header">
          <div class="table-title">Reservasi Terkini <span id="res-count-badge" style="font-size:11px;background:var(--accent);color:#fff;padding:2px 8px;border-radius:20px;margin-left:6px;"></span></div>
          <div style="display:flex;align-items:center;gap:8px;">
            <span id="last-update" style="font-size:11px;color:var(--text-3)"></span>
            <span class="live-pill"><span class="live-dot"></span> Auto-refresh</span>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table>
            <thead>
              <tr>
                <th>Pasien</th><th>Dokter</th><th>Layanan</th><th>Waktu</th><th>Status</th><th>Foto</th>
              </tr>
            </thead>
            <tbody id="res-table-body">
              <tr class="empty-row"><td colspan="6">Memuat data reservasi...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="actions-card">
          <div class="section-title">Aksi Cepat</div>
          <div class="action-grid">
            <button class="action-btn" onclick="openModal('modal-tambah-reservasi')"><span class="a-icon"></span>Buat Reservasi</button>
            <button class="action-btn" onclick="switchView('reservasi', document.querySelectorAll('.nav-item')[1])"><span class="a-icon"></span>Semua Reservasi</button>
            <button class="action-btn" onclick="switchView('pengguna', document.querySelectorAll('.nav-item')[2])"><span class="a-icon"></span>Kelola Pengguna</button>
            <button class="action-btn" onclick="printLaporan()"><span class="a-icon"></span>Cetak Laporan</button>
          </div>
        </div>
        <div class="actions-card" style="flex:1">
          <div class="section-title">Dokter Bertugas</div>
          <div class="duty-list">
            <div class="duty-item">
              <div class="duty-avatar" style="background:#fff1f2"></div>
              <div class="duty-info"><div class="duty-name">dr. Rini Hartati</div><div class="duty-spec">Umum · Poli A</div></div>
              <span class="duty-status on">● Aktif</span>
            </div>
            <div class="duty-item">
              <div class="duty-avatar" style="background:#e6f9f7"></div>
              <div class="duty-info"><div class="duty-name">dr. Budi Santoso</div><div class="duty-spec">Anak · Poli B</div></div>
              <span class="duty-status on">● Aktif</span>
            </div>
            <div class="duty-item">
              <div class="duty-avatar" style="background:#f5f3ff"></div>
              <div class="duty-info"><div class="duty-name">dr. Sari Indah</div><div class="duty-spec">Gigi · Poli C</div></div>
              <span class="duty-status off">○ Istirahat</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /view-dashboard -->

  <!-- ═══ VIEW: MANAJEMEN RESERVASI ═══ -->
  <div class="view" id="view-reservasi">
    <div class="section-header" style="margin-bottom:20px;">
      <div class="section-title">Semua Reservasi</div>
      <div style="display:flex;gap:10px;align-items:center;">
        <input type="date" id="res-filter-date" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:13px;outline:none;" onchange="fetchAndRenderFull()">
        <button onclick="openModal('modal-tambah-reservasi')" style="padding:9px 20px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;">+ Buat Reservasi</button>
      </div>
    </div>
    <div class="full-table-card">
      <table>
        <thead>
          <tr><th>#</th><th>Pasien</th><th>Telepon</th><th>Dokter</th><th>Layanan</th><th>Tanggal</th><th>Jam</th><th>Jaminan</th><th>Status</th><th>Foto</th></tr>
        </thead>
        <tbody id="full-res-table">
          <tr class="empty-row"><td colspan="10">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ═══ VIEW: KELOLA PENGGUNA ═══ -->
  <div class="view" id="view-pengguna">
    <div class="section-header" style="margin-bottom:20px;">
      <div class="section-title">Daftar Pengguna</div>
    </div>
    <div class="full-table-card">
      <table>
        <thead>
          <tr><th>#</th><th>Nama Lengkap</th><th>Username</th><th>Email</th><th>Role</th><th>Bergabung</th></tr>
        </thead>
        <tbody id="users-table">
          <tr class="empty-row"><td colspan="6">Memuat data pengguna...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- ═══ MODAL: BUAT RESERVASI ═══ -->
<div class="modal-overlay" id="modal-tambah-reservasi">
  <div class="modal-box">
    <div class="modal-title">
      Buat Reservasi Baru
      <span class="modal-close" onclick="closeModal('modal-tambah-reservasi')">×</span>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Nama Pasien *</label><input type="text" id="m-nama" placeholder="Nama lengkap"></div>
      <div class="form-group"><label>No. Telepon *</label><input type="text" id="m-telp" placeholder="08xxxxxxxxxx"></div>
      <div class="form-group full"><label>Pilih Dokter *</label>
        <select id="m-dokter">
          <option value="">Pilih Dokter</option>
          <option>dr. Andi Prasetyo</option><option>dr. Siti Rahayu</option><option>dr. Budi Santoso</option>
          <option>dr. Nurul Hidayah</option><option>dr. Eko Purnomo, SpPD</option><option>dr. Lina Marlina, SpOG</option>
        </select>
      </div>
      <div class="form-group full"><label>Layanan *</label>
        <select id="m-layanan">
          <option value="">Pilih Layanan</option>
          <option>Perawatan Umum</option><option>Pemeriksaan Mata</option><option>Pengobatan Gigi</option>
          <option>Perawatan Gawat Darurat</option><option>Rawat Jalan</option><option>Rawat Inap</option><option>Apotik</option>
        </select>
      </div>
      <div class="form-group"><label>Tanggal *</label><input type="date" id="m-tanggal"></div>
      <div class="form-group"><label>Jam *</label>
        <select id="m-jam">
          <option value="">Pilih</option>
          <option>07.00</option><option>08.00</option><option>09.00</option><option>10.00</option>
          <option>11.00</option><option>13.00</option><option>14.00</option><option>15.00</option><option>16.00</option>
        </select>
      </div>
      <div class="form-group full"><label>Jaminan</label>
        <select id="m-jaminan">
          <option>Umum / Mandiri</option><option>BPJS</option><option>Asuransi Swasta</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-cancel" onclick="closeModal('modal-tambah-reservasi')">Batal</button>
      <button class="btn-modal-save" onclick="saveAdminReservasi()">Simpan Reservasi</button>
    </div>
  </div>
</div>

<!-- ═══ MODAL: FOTO PASIEN ═══ -->
<div class="modal-overlay" id="modal-foto-pasien">
  <div class="modal-box">
    <div class="modal-title">
      Foto Pasien
      <span class="modal-close" onclick="closeModal('modal-foto-pasien')">×</span>
    </div>
    <div style="display:flex;flex-direction:column;align-items:center;">
      <img id="foto-modal-preview" class="foto-preview-admin" src="" alt="" style="display:none;">
      <div id="foto-modal-empty" style="width:140px;height:140px;border-radius:var(--radius-sm);border:1.5px dashed var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--text-3);margin-bottom:12px;text-align:center;">Belum ada foto</div>
      <input type="file" id="foto-modal-input" accept="image/png,image/jpeg,image/webp" style="width:100%;">
      <div style="font-size:11px;color:var(--text-3);margin-top:6px;align-self:flex-start;">Format JPG/PNG/WEBP, maks 5MB.</div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-cancel" onclick="closeModal('modal-foto-pasien')">Batal</button>
      <button class="btn-modal-save" onclick="uploadFotoPasien()">Unggah Foto</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<script>
const BASE_URL = '{{ url('/') }}/';
</script>
<script src="{{ asset('js/csrf.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
