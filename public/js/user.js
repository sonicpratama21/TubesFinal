/**
 * user.js — JavaScript Dashboard Reservasi TelkomMedika
 */

// ── NAV ───────────────────────────────────────────────────────────────────
let currentView = 'reservasi';

function switchView(key, el) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.getElementById('view-' + key).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  currentView = key;
  if (key === 'reservasi') loadReservasi();
}

// ── CACHE: simpan data terakhir untuk dipakai modal edit ──────────────────
let reservasiCache = {};

function cacheRows(rows) {
  rows.forEach(r => { reservasiCache[r.id] = r; });
}

// ── LOAD SEMUA RESERVASI ──────────────────────────────────────────────────
async function loadReservasi() {
  const tbody = document.getElementById('reservasi-table');
  try {
    const res  = await fetch(`${BASE_URL}reservasi?limit=100`);
    const data = await res.json();
    if (data.status === 'success' && data.data.length > 0) {
      cacheRows(data.data);
      tbody.innerHTML = data.data.map(r => `
        <tr>
          <td>${formatTanggal(r.tanggal)}</td>
          <td>${r.layanan}</td>
          <td>${r.dokter}</td>
          <td>${r.jam}</td>
          <td><span class="badge badge-orange">${r.catatan || '-'}</span></td>
          <td><span class="badge badge-${statusColor(r.status)}">${r.status}</span></td>
          <td>${fotoCell(r)}</td>
          <td>${actionCell(r)}</td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:var(--text3); padding:20px;">Belum ada reservasi</td></tr>';
    }
  } catch (e) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:var(--text3); padding:20px;">Tidak dapat memuat data</td></tr>';
  }
}

// ── HELPERS: FOTO & ACTION CELL ───────────────────────────────────────────
function fotoCell(r) {
  if (r.foto) {
    const url = `${BASE_URL}uploads/reservasi/${r.foto}`;
    return `<img src="${url}" class="foto-thumb" alt="foto" onclick="openEditReservasi(${r.id})">`;
  }
  return `<div class="foto-placeholder" onclick="openEditReservasi(${r.id})" style="cursor:pointer;">Tambah foto</div>`;
}

function actionCell(r) {
  return `
    <div class="row-actions">
      <button class="btn-icon btn-edit" onclick="openEditReservasi(${r.id})">Edit</button>
      <button class="btn-icon btn-delete" onclick="deleteReservasi(${r.id})">Hapus</button>
    </div>
  `;
}

function statusColor(s) {
  return { menunggu: 'orange', terkonfirmasi: 'green', dibatalkan: 'red', selesai: 'blue' }[s] || 'gray';
}
function formatTanggal(d) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Load reservasi saat halaman pertama kali dibuka
loadReservasi();

// ── BOOKING ───────────────────────────────────────────────────────────────
let selectedTime = '', selectedLayanan = '', selectedJaminan = '';

function selectLayanan(el, val) {
  selectedLayanan = val;
  document.querySelectorAll('.form-section .pill').forEach(p => {
    if (['Laboratorium','Pemeriksaan Umum','Pemeriksaan Gigi','Pemeriksaan Mata']
        .some(l => p.textContent.includes(l.split(' ').pop()))) {
      p.classList.remove('selected');
    }
  });
  el.classList.add('selected');
  document.getElementById('form-layanan-tag').textContent = val;
}

function selectPill(el, group) {
  const parent = document.getElementById('jaminan-group');
  parent.querySelectorAll('.pill').forEach(p => p.classList.remove('selected'));
  el.classList.add('selected');
  selectedJaminan = el.textContent.trim();
}

function selectTime(el, val) {
  if (el.classList.contains('disabled')) return;
  selectedTime = val;
  document.querySelectorAll('#time-grid .time-pill').forEach(t => t.classList.remove('selected'));
  el.classList.add('selected');
}

function resetBookingForm() {
  document.getElementById('f-nama').value    = '';
  document.getElementById('f-telp').value    = '';
  document.getElementById('f-dokter').value  = '';
  document.getElementById('f-catatan').value = '';
  selectedTime = ''; selectedLayanan = ''; selectedJaminan = '';
  document.querySelectorAll('.time-pill').forEach(t => t.classList.remove('selected'));
  document.querySelectorAll('.pill').forEach(p => p.classList.remove('selected'));
  document.getElementById('form-layanan-tag').textContent = 'Pilih Layanan';

  const fotoInput   = document.getElementById('f-foto');
  const fotoPreview = document.getElementById('f-foto-preview');
  if (fotoInput) fotoInput.value = '';
  if (fotoPreview) { fotoPreview.src = ''; fotoPreview.style.display = 'none'; }
}

// ── PREVIEW FOTO BOOKING ───────────────────────────────────────────────────
function previewBookingFoto(input) {
  const preview = document.getElementById('f-foto-preview');
  const file = input.files && input.files[0];
  if (!file) { preview.src = ''; preview.style.display = 'none'; return; }

  if (file.size > 5 * 1024 * 1024) {
    alert('Ukuran file maksimal 5MB.');
    input.value = '';
    preview.src = ''; preview.style.display = 'none';
    return;
  }

  const reader = new FileReader();
  reader.onload = e => {
    preview.src = e.target.result;
    preview.style.display = 'block';
  };
  reader.readAsDataURL(file);
}

async function submitBooking() {
  const nama    = document.getElementById('f-nama').value.trim();
  const telp    = document.getElementById('f-telp').value.trim();
  const dokter  = document.getElementById('f-dokter').value;
  const tanggal = document.getElementById('f-tanggal').value;
  const catatan = document.getElementById('f-catatan').value.trim();

  if (!nama || !telp || !dokter || !tanggal || !selectedTime) {
    alert('Mohon lengkapi semua field yang wajib diisi (*)'); return;
  }
  if (!selectedLayanan) {
    alert('Pilih jenis layanan terlebih dahulu'); return;
  }

  try {
    const res = await fetch(`${BASE_URL}reservasi`, {
      method  : 'POST',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({
        nama, telp, dokter,
        layanan : selectedLayanan,
        tanggal,
        jam     : selectedTime,
        jaminan : selectedJaminan || 'Umum / Mandiri',
        catatan,
      }),
    });
    const result = await res.json();

    if (result.status !== 'success') {
      alert('Gagal menyimpan: ' + result.message); return;
    }

    // Upload foto pendukung jika user memilih file
    const fotoInput = document.getElementById('f-foto');
    if (fotoInput && fotoInput.files && fotoInput.files[0]) {
      try {
        const fd = new FormData();
        fd.append('id', result.id);
        fd.append('foto', fotoInput.files[0]);

        const fotoRes    = await fetch(`${BASE_URL}reservasi/foto`, { method: 'POST', body: fd });
        const fotoResult = await fotoRes.json();
        if (fotoResult.status !== 'success') {
          alert('Reservasi tersimpan, tetapi gagal mengunggah foto: ' + fotoResult.message);
        }
      } catch (e) {
        alert('Reservasi tersimpan, tetapi terjadi kesalahan saat mengunggah foto.');
      }
    }

    const tglFmt = new Date(tanggal).toLocaleDateString('id-ID', {
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    });
    document.getElementById('booking-content').innerHTML = `
      <div class="card">
        <div class="success-wrap">
          <div class="success-emoji">✅</div>
          <div class="success-title">Reservasi Berhasil Disimpan!</div>
          <div class="success-sub">Data reservasi Anda telah tersimpan. Admin akan segera mengkonfirmasi.</div>
          <div class="summary-box">
            <div><strong>Layanan:</strong> ${selectedLayanan}</div>
            <div><strong>Pasien:</strong> ${nama}</div>
            <div><strong>WhatsApp:</strong> ${telp}</div>
            <div><strong>Dokter:</strong> ${dokter}</div>
            <div><strong>Tanggal:</strong> ${tglFmt}</div>
            <div><strong>Jam:</strong> ${selectedTime} WIB</div>
            ${selectedJaminan ? `<div><strong>Jaminan:</strong> ${selectedJaminan}</div>` : ''}
          </div>
          <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
            <button class="btn-red" style="width:auto; padding:10px 28px;" onclick="showBookingForm()">← Buat Reservasi Lain</button>
            <button class="btn-outline" onclick="switchView('reservasi', document.querySelector('.nav-item'))">Lihat Daftar Reservasi</button>
          </div>
        </div>
      </div>
    `;
  } catch (err) {
    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
  }
}

function showBookingForm() {
  location.reload();
}

// ── EDIT RESERVASI (MODAL) ─────────────────────────────────────────────────
function openEditReservasi(id) {
  const r = reservasiCache[id];
  if (!r) { alert('Data tidak ditemukan, silakan muat ulang.'); return; }

  document.getElementById('e-id').value      = r.id;
  document.getElementById('e-nama').value    = r.nama_pasien;
  document.getElementById('e-telp').value    = r.telpon;
  document.getElementById('e-dokter').value  = r.dokter;
  document.getElementById('e-layanan').value = r.layanan;
  document.getElementById('e-jaminan').value = r.jaminan || 'Umum / Mandiri';
  document.getElementById('e-tanggal').value = r.tanggal;
  document.getElementById('e-jam').value     = r.jam;
  document.getElementById('e-catatan').value = r.catatan || '';
  document.getElementById('e-foto').value    = '';

  const preview = document.getElementById('e-foto-preview');
  if (r.foto) {
    preview.src = `${BASE_URL}uploads/reservasi/${r.foto}`;
    preview.style.display = 'block';
  } else {
    preview.src = '';
    preview.style.display = 'none';
  }

  document.getElementById('edit-modal').classList.add('active');
}

function closeEditModal() {
  document.getElementById('edit-modal').classList.remove('active');
}

async function saveEditReservasi() {
  const id      = document.getElementById('e-id').value;
  const nama    = document.getElementById('e-nama').value.trim();
  const telp    = document.getElementById('e-telp').value.trim();
  const dokter  = document.getElementById('e-dokter').value;
  const layanan = document.getElementById('e-layanan').value;
  const jaminan = document.getElementById('e-jaminan').value;
  const tanggal = document.getElementById('e-tanggal').value;
  const jam     = document.getElementById('e-jam').value;
  const catatan = document.getElementById('e-catatan').value.trim();
  const fotoInp = document.getElementById('e-foto');

  if (!nama || !telp || !dokter || !layanan || !tanggal || !jam) {
    alert('Mohon lengkapi semua field yang wajib diisi (*)'); return;
  }

  try {
    const res = await fetch(`${BASE_URL}reservasi`, {
      method  : 'PUT',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({ id, nama, telp, dokter, layanan, jaminan, tanggal, jam, catatan }),
    });
    const result = await res.json();
    if (result.status !== 'success') {
      alert('Gagal menyimpan: ' + result.message); return;
    }

    if (fotoInp.files && fotoInp.files[0]) {
      const fd = new FormData();
      fd.append('id', id);
      fd.append('foto', fotoInp.files[0]);

      const fotoRes = await fetch(`${BASE_URL}reservasi/foto`, {
        method: 'POST',
        body  : fd,
      });
      const fotoResult = await fotoRes.json();
      if (fotoResult.status !== 'success') {
        alert('Data tersimpan, tetapi gagal mengunggah foto: ' + fotoResult.message);
      }
    }

    closeEditModal();
    loadReservasi();
    alert('Reservasi berhasil diperbarui.');
  } catch (err) {
    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
  }
}

// ── HAPUS RESERVASI ──────────────────────────────────────────────────────
async function deleteReservasi(id) {
  if (!confirm('Hapus reservasi ini? Tindakan ini tidak dapat dibatalkan.')) return;

  try {
    const res = await fetch(`${BASE_URL}reservasi`, {
      method  : 'DELETE',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({ id }),
    });
    const result = await res.json();
    if (result.status !== 'success') {
      alert('Gagal menghapus: ' + result.message); return;
    }
    loadReservasi();
  } catch (err) {
    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
  }
}
