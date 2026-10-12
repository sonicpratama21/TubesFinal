<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Reservasi — TelkomMedika</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-name">Telko<span>Medika</span></div>
  </div>
  <div class="sidebar-section">
    <div class="sidebar-section-label">Reservasi</div>
    <button class="nav-item active" onclick="switchView('reservasi', this)">
      <span class="nav-icon"></span>Daftar Reservasi
    </button>
    <button class="nav-item" onclick="switchView('booking', this)">
      <span class="nav-icon"></span>Buat Reservasi
    </button>
  </div>
  <div class="sidebar-bottom">
    <a href="{{ url('auth/logout') }}">
      <button class="logout-btn">
        <span></span> Keluar
      </button>
    </a>
  </div>
</aside>

<main class="main">
  <header class="topbar">
    <div class="topbar-user">
      <div class="user-avatar">{{ $initial }}</div>
      <span class="user-name">{{ $nama }}</span>
      <span class="user-chevron"></span>
    </div>
  </header>

  <div class="content">

    <div class="view active" id="view-reservasi">
      <div class="greeting">
        <h1>Reservasi Saya</h1>
        <p>Kelola semua reservasi kunjungan medis Anda di TelkomMedika</p>
      </div>
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <div class="card-title">Daftar Reservasi</div>
          <a class="card-link" onclick="loadReservasi()">Muat ulang</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Jenis Layanan</th>
              <th>Dokter</th>
              <th>Jam</th>
              <th>Keluhan</th>
              <th>Status</th>
              <th>Foto</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="reservasi-table">
            <tr>
              <td colspan="8" style="text-align:center; color:var(--text3); padding:20px;">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="view" id="view-booking">
      <div class="greeting">
        <h1>Buat Reservasi Baru</h1>
        <p>Isi formulir berikut untuk membuat jadwal kunjungan medis</p>
      </div>
      <div id="booking-content">
        <div class="card" id="booking-form-card">
          <div style="font-size:15px; font-weight:600; margin-bottom:20px;">
            Form Reservasi &nbsp;<span style="background:#ffebee; color:var(--red); font-size:12px; font-weight:500; padding:3px 12px; border-radius:20px;" id="form-layanan-tag">Pilih Layanan</span>
          </div>
          <div class="form-section">
            <div class="form-section-title">Pilih Layanan</div>
            <div style="display:grid; grid-template-columns: repeat(4,1fr); gap:10px; margin-bottom:8px;">
              <div class="pill" onclick="selectLayanan(this,'Laboratorium')">Laboratorium</div>
              <div class="pill" onclick="selectLayanan(this,'Pemeriksaan Umum')">Poli Umum</div>
              <div class="pill" onclick="selectLayanan(this,'Pemeriksaan Gigi')">Poli Gigi</div>
              <div class="pill" onclick="selectLayanan(this,'Pemeriksaan Mata')">Poli Mata</div>
            </div>
          </div>
          <div class="form-section">
            <div class="form-section-title">Data Pasien</div>
            <div class="form-grid">
              <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" id="f-nama" placeholder="Masukkan nama lengkap">
              </div>
              <div class="form-group">
                <label>No. Telepon / WhatsApp *</label>
                <input type="text" id="f-telp" placeholder="08xxxxxxxxxx">
              </div>
              <div class="form-group full">
                <label>Pilih Dokter *</label>
                <select id="f-dokter">
                  <option value="">Pilih Dokter</option>
                  <option>dr. Agus Subianto</option>
                  <option>dr. Aryasatya Ramadhan</option>
                  <option>dr. Ratna Sumiatni</option>
                  <option>dr. Andi Prasetyo</option>
                  <option>dr. Siti Rahayu</option>
                </select>
              </div>
              <div class="form-group">
                <label>Tanggal Kunjungan *</label>
                <input type="date" id="f-tanggal" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
              </div>
              <div class="form-group">
                <label>Jaminan Kesehatan</label>
                <div class="pill-group" id="jaminan-group">
                  <div class="pill" onclick="selectPill(this,'jaminan')">Umum / Mandiri</div>
                  <div class="pill" onclick="selectPill(this,'jaminan')">Mahasiswa Telyu</div>
                </div>
              </div>
              <div class="form-group full">
                <label>Pilih Jam Kunjungan *</label>
                <div class="time-grid" id="time-grid">
                  @php $disabled = ['09.00','11.00']; @endphp
                  @foreach (['07.00','08.00','09.00','10.00','11.00','12.00','13.00','14.00','15.00'] as $t)
                    <div class="time-pill{{ in_array($t, $disabled) ? ' disabled' : '' }}" onclick="selectTime(this,'{{ $t }}')">{{ $t }}</div>
                  @endforeach
                </div>
                <div style="font-size:11px; color:var(--text3); margin-top:6px;">Slot yang tidak aktif sudah penuh</div>
              </div>
              <div class="form-group full">
                <label>Keluhan / Catatan</label>
                <textarea id="f-catatan" placeholder="Deskripsikan keluhan atau kebutuhan Anda..."></textarea>
              </div>
              <div class="form-group full">
                <label>Upload Foto Pendukung (opsional)</label>
                <div class="foto-edit-wrap">
                  <img id="f-foto-preview" class="foto-preview" src="" alt="" style="display:none;">
                  <input type="file" id="f-foto" accept="image/png,image/jpeg,image/webp" onchange="previewBookingFoto(this)">
                  <div style="font-size:11px; color:var(--text3); margin-top:6px;">Format JPG/PNG/WEBP, maks 5MB. Contoh: hasil tes, surat rujukan, foto keluhan, dll.</div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-footer">
            <button class="btn-outline" onclick="resetBookingForm()">Batal</button>
            <button class="btn-submit" onclick="submitBooking()">Konfirmasi Reservasi →</button>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /content -->
</main>

<div class="modal-overlay" id="edit-modal">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Edit Reservasi</div>
      <button class="modal-close" onclick="closeEditModal()">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="e-id">
      <div class="form-grid">
        <div class="form-group">
          <label>Nama Lengkap *</label>
          <input type="text" id="e-nama">
        </div>
        <div class="form-group">
          <label>No. Telepon / WhatsApp *</label>
          <input type="text" id="e-telp">
        </div>
        <div class="form-group full">
          <label>Pilih Dokter *</label>
          <select id="e-dokter">
            <option value="">Pilih Dokter</option>
            <option>dr. Agus Subianto</option>
            <option>dr. Aryasatya Ramadhan</option>
            <option>dr. Ratna Sumiatni</option>
            <option>dr. Andi Prasetyo</option>
            <option>dr. Siti Rahayu</option>
          </select>
        </div>
        <div class="form-group">
          <label>Jenis Layanan *</label>
          <select id="e-layanan">
            <option>Laboratorium</option>
            <option>Pemeriksaan Umum</option>
            <option>Pemeriksaan Gigi</option>
            <option>Pemeriksaan Mata</option>
          </select>
        </div>
        <div class="form-group">
          <label>Jaminan Kesehatan</label>
          <select id="e-jaminan">
            <option>Umum / Mandiri</option>
            <option>Mahasiswa Telyu</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tanggal Kunjungan *</label>
          <input type="date" id="e-tanggal">
        </div>
        <div class="form-group">
          <label>Jam Kunjungan *</label>
          <select id="e-jam">
            @foreach(['07.00','08.00','09.00','10.00','11.00','12.00','13.00','14.00','15.00'] as $t)
              <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group full">
          <label>Keluhan / Catatan</label>
          <textarea id="e-catatan"></textarea>
        </div>
        <div class="form-group full">
          <label>Foto Pendukung (opsional)</label>
          <div class="foto-edit-wrap">
            <img id="e-foto-preview" class="foto-preview" src="" alt="" style="display:none;">
            <input type="file" id="e-foto" accept="image/png,image/jpeg,image/webp">
            <div style="font-size:11px; color:var(--text3); margin-top:6px;">Format JPG/PNG/WEBP, maks 5MB. Unggah untuk mengganti foto.</div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" onclick="closeEditModal()">Batal</button>
      <button class="btn-submit" onclick="saveEditReservasi()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<script>
const BASE_URL = '{{ url('/') }}/';
</script>
<script src="{{ asset('js/csrf.js') }}"></script>
<script src="{{ asset('js/user.js') }}"></script>
</body>
</html>
