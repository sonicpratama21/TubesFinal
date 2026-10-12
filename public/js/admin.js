/**
 * admin.js — JavaScript Dashboard Admin TelkomMedika
 */

// ── Helpers ───────────────────────────────────────────────────────────────
const $ = id => document.getElementById(id);
const avatarColors = ['#e8394a','#00b4a0','#7c3aed','#f59e0b','#60a5fa','#1a3a6b','#e8923a'];

function initials(n) {
  return n.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
}
function avatarColor(n) {
  let h = 0;
  for (let c of n) h = (h * 31 + c.charCodeAt(0)) % avatarColors.length;
  return avatarColors[h];
}
function toast(msg, dur = 3000) {
  const t = $('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), dur);
}

// ── Date ──────────────────────────────────────────────────────────────────
const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const now    = new Date();
$('tgl').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

const todayStr = now.toISOString().split('T')[0];
$('res-filter-date').value = todayStr;
$('m-tanggal').value       = todayStr;
$('m-tanggal').min         = todayStr;

// ── Navigation ────────────────────────────────────────────────────────────
const pageNames = {
  dashboard : 'Dashboard',
  reservasi : 'Manajemen Reservasi',
  pengguna  : 'Kelola Pengguna',
};

function switchView(viewName, el) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  $('view-' + viewName).classList.add('active');
  if (el) el.classList.add('active');
  $('topbar-page-title').textContent = pageNames[viewName] || viewName;

  if (viewName === 'reservasi') fetchAndRenderFull();
  if (viewName === 'pengguna')  fetchUsers();
}

// ── Period buttons ────────────────────────────────────────────────────────
function setPeriod(btn) {
  document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

// ── Modal ─────────────────────────────────────────────────────────────────
function openModal(id)  { $(id).classList.add('active'); }
function closeModal(id) { $(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

// ── REAL-TIME DATA FETCH ──────────────────────────────────────────────────
let lastResCount    = -1;
let allReservations = [];

async function fetchReservations(date) {
  try {
    const d   = date || todayStr;
    const res = await fetch(`${BASE_URL}reservasi?date=${d}&limit=100`);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return await res.json();
  } catch (e) {
    console.error('Fetch error:', e);
    return null;
  }
}

async function refreshDashboard() {
  const data = await fetchReservations(todayStr);
  if (!data || data.status !== 'success') return;

  allReservations = data.data;
  const total     = data.total;
  cacheReservasi(data.data);

  // Update stats
  $('stat-total').textContent     = total;
  $('donut-total').textContent    = total;
  $('stat-total-sub').textContent = 'reservasi hari ini';

  const menunggu = data.data.filter(r => r.status === 'menunggu').length;
  const konfirm  = data.data.filter(r => r.status === 'terkonfirmasi').length;
  $('stat-menunggu').textContent  = menunggu;
  $('stat-konfirmasi').textContent = konfirm;

  const services = [...new Set(data.data.map(r => r.layanan))];
  $('stat-layanan').textContent = services.length;

  // Legend counts
  const cnt = k => data.data.filter(r => r.layanan && r.layanan.toLowerCase().includes(k)).length;
  $('leg-umum').textContent  = cnt('umum');
  $('leg-jalan').textContent = cnt('jalan');
  $('leg-gigi').textContent  = cnt('gigi');
  $('leg-inap').textContent  = cnt('inap');
  $('leg-lain').textContent  = data.data.filter(r => !r.layanan || (
    !r.layanan.includes('Umum') && !r.layanan.includes('Jalan') &&
    !r.layanan.includes('Gigi') && !r.layanan.includes('Inap')
  )).length;

  // Nav badge & notification dot
  $('nav-badge-res').textContent  = total;
  $('notif-dot').style.display    = menunggu > 0 ? 'block' : 'none';

  // Last update time
  const t = new Date();
  $('last-update').textContent = `Update: ${String(t.getHours()).padStart(2,'0')}:${String(t.getMinutes()).padStart(2,'0')}:${String(t.getSeconds()).padStart(2,'0')}`;

  // New reservation alert
  if (lastResCount >= 0 && total > lastResCount) {
    toast(`🔔 ${total - lastResCount} reservasi baru masuk!`);
  }
  lastResCount = total;

  renderMiniTable(data.data.slice(0, 8));
  $('res-count-badge').textContent = total + ' pasien';
}

// ── Status helpers ────────────────────────────────────────────────────────
function statusClass(s) {
  if (s === 'terkonfirmasi') return 'confirmed';
  if (s === 'dibatalkan')    return 'cancelled';
  if (s === 'selesai')       return 'done';
  return 'waiting';
}
function statusLabel(s) {
  if (s === 'terkonfirmasi') return '✓ Terkonfirmasi';
  if (s === 'dibatalkan')    return '✕ Dibatalkan';
  if (s === 'selesai')       return '✔ Selesai';
  return ' Menunggu';
}

// ── Foto helper ───────────────────────────────────────────────────────────
let reservasiCacheAdmin = {};

function cacheReservasi(rows) {
  rows.forEach(r => { reservasiCacheAdmin[r.id] = r; });
}

function fotoCellAdmin(r) {
  if (r.foto) {
    const url = `${BASE_URL}uploads/reservasi/${r.foto}`;
    return `<img src="${url}" class="foto-thumb-admin" alt="foto" onclick="openFotoModal(${r.id})">`;
  }
  return `<button class="foto-add-btn" onclick="openFotoModal(${r.id})" title="Tambah foto">+</button>`;
}

// ── Mini Reservasi Table (Dashboard) ─────────────────────────────────────
function renderMiniTable(rows) {
  const tbody = $('res-table-body');
  if (!rows || rows.length === 0) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="6">Belum ada reservasi hari ini</td></tr>';
    return;
  }
  tbody.innerHTML = rows.map(r => `
    <tr>
      <td><div class="patient-cell">
        <div class="patient-avatar" style="background:${avatarColor(r.nama_pasien)}">${initials(r.nama_pasien)}</div>
        <div><div class="patient-name">${r.nama_pasien}</div><div class="patient-id">#R-${String(r.id).padStart(5,'0')}</div></div>
      </div></td>
      <td class="doctor-col">${r.dokter}</td>
      <td style="font-size:12px;color:var(--text-2)">${r.layanan}</td>
      <td class="time-col">${r.jam}</td>
      <td>
        <select class="status-select ${statusClass(r.status)}" onchange="updateStatus(${r.id},this)">
          <option value="menunggu"      ${r.status==='menunggu'?'selected':''}> Menunggu</option>
          <option value="terkonfirmasi" ${r.status==='terkonfirmasi'?'selected':''}>✓ Terkonfirmasi</option>
          <option value="selesai"       ${r.status==='selesai'?'selected':''}>✔ Selesai</option>
          <option value="dibatalkan"    ${r.status==='dibatalkan'?'selected':''}>✕ Dibatalkan</option>
        </select>
      </td>
      <td>${fotoCellAdmin(r)}</td>
    </tr>
  `).join('');
}

// ── Full Reservasi View ───────────────────────────────────────────────────
async function fetchAndRenderFull() {
  const date  = $('res-filter-date').value || todayStr;
  const data  = await fetchReservations(date);
  const tbody = $('full-res-table');
  if (!data || data.status !== 'success') {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="10">Gagal memuat data.</td></tr>'; return;
  }
  if (data.data.length === 0) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="10">Tidak ada reservasi pada tanggal ini.</td></tr>'; return;
  }
  cacheReservasi(data.data);
  tbody.innerHTML = data.data.map((r, i) => `
    <tr>
      <td style="color:var(--text-3);font-size:12px">${i + 1}</td>
      <td><div class="patient-cell">
        <div class="patient-avatar" style="background:${avatarColor(r.nama_pasien)}">${initials(r.nama_pasien)}</div>
        <div><div class="patient-name">${r.nama_pasien}</div></div>
      </div></td>
      <td style="font-size:12px;color:var(--text-2)">${r.telpon}</td>
      <td class="doctor-col">${r.dokter}</td>
      <td style="font-size:12px;color:var(--text-2)">${r.layanan}</td>
      <td style="font-size:12px;color:var(--text-2)">${r.tanggal}</td>
      <td class="time-col">${r.jam}</td>
      <td style="font-size:12px;color:var(--text-2)">${r.jaminan || '–'}</td>
      <td>
        <select class="status-select ${statusClass(r.status)}" onchange="updateStatus(${r.id},this)">
          <option value="menunggu"      ${r.status==='menunggu'?'selected':''}> Menunggu</option>
          <option value="terkonfirmasi" ${r.status==='terkonfirmasi'?'selected':''}>✓ Terkonfirmasi</option>
          <option value="selesai"       ${r.status==='selesai'?'selected':''}>✔ Selesai</option>
          <option value="dibatalkan"    ${r.status==='dibatalkan'?'selected':''}>✕ Dibatalkan</option>
        </select>
      </td>
      <td>${fotoCellAdmin(r)}</td>
    </tr>
  `).join('');
}

// ── Update Status ─────────────────────────────────────────────────────────
async function updateStatus(id, sel) {
  const status = sel.value;
  sel.className = 'status-select ' + statusClass(status);
  try {
    const res  = await fetch(`${BASE_URL}reservasi`, {
      method  : 'PATCH',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({ id, status }),
    });
    const data = await res.json();
    if (data.status === 'success') {
      toast(' Status diperbarui: ' + statusLabel(status));
      refreshDashboard();
    } else {
      toast(' Gagal memperbarui status: ' + (data.message || 'Tidak diketahui'));
      // Kembalikan ke status sebelumnya jika gagal
      const r = reservasiCacheAdmin[id];
      if (r) {
        sel.value = r.status;
        sel.className = 'status-select ' + statusClass(r.status);
      }
    }
  } catch (e) {
    toast(' Kesalahan jaringan');
  }
}

// ── Fetch Users ───────────────────────────────────────────────────────────
async function fetchUsers() {
  const tbody = $('users-table');
  tbody.innerHTML = '<tr class="empty-row"><td colspan="6"><span class="spinner"></span> Memuat data pengguna...</td></tr>';
  try {
    const res  = await fetch(`${BASE_URL}apiUsers`);
    const data = await res.json();
    if (data.status === 'success' && data.data.length > 0) {
      tbody.innerHTML = data.data.map((u, i) => `
        <tr>
          <td style="color:var(--text-3);font-size:12px">${i + 1}</td>
          <td><div class="patient-cell">
            <div class="patient-avatar" style="background:${avatarColor(u.nama_lengkap)}">${initials(u.nama_lengkap)}</div>
            <div class="patient-name">${u.nama_lengkap}</div>
          </div></td>
          <td style="font-size:13px;color:var(--text-2)">${u.username}</td>
          <td style="font-size:13px;color:var(--text-2)">${u.email}</td>
          <td><span style="background:${u.role==='admin'?'var(--accent-soft)':'var(--teal-soft)'};color:${u.role==='admin'?'var(--accent)':'var(--teal)'};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">${u.role}</span></td>
          <td style="font-size:12px;color:var(--text-3)">${u.created_at || '–'}</td>
        </tr>
      `).join('');
    } else {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="6">Tidak ada data pengguna.</td></tr>';
    }
  } catch (e) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="6">Gagal memuat data pengguna.</td></tr>';
  }
}

// ── Admin Buat Reservasi ──────────────────────────────────────────────────
async function saveAdminReservasi() {
  const nama    = $('m-nama').value.trim();
  const telp    = $('m-telp').value.trim();
  const dokter  = $('m-dokter').value;
  const layanan = $('m-layanan').value;
  const tanggal = $('m-tanggal').value;
  const jam     = $('m-jam').value;
  const jaminan = $('m-jaminan').value;
  if (!nama || !telp || !dokter || !layanan || !tanggal || !jam) {
    toast(' Lengkapi semua field wajib'); return;
  }
  try {
    const res  = await fetch(`${BASE_URL}reservasi`, {
      method  : 'POST',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({ nama, telp, dokter, layanan, tanggal, jam, jaminan, catatan: '' }),
    });
    const data = await res.json();
    if (data.status === 'success') {
      toast(' Reservasi berhasil dibuat!');
      closeModal('modal-tambah-reservasi');
      refreshDashboard();
      $('m-nama').value = ''; $('m-telp').value = ''; $('m-dokter').value = '';
      $('m-layanan').value = ''; $('m-jam').value = '';
    } else {
      toast(' ' + data.message);
    }
  } catch (e) { toast(' Kesalahan jaringan'); }
}

// ── Search ────────────────────────────────────────────────────────────────
function handleSearch() {
  const q = $('search-input').value.toLowerCase().trim();
  if (!q) { renderMiniTable(allReservations.slice(0, 8)); return; }
  const filtered = allReservations.filter(r =>
    r.nama_pasien.toLowerCase().includes(q) ||
    r.dokter.toLowerCase().includes(q)      ||
    r.layanan.toLowerCase().includes(q)
  );
  renderMiniTable(filtered.slice(0, 8));
}

// ── Print Laporan ─────────────────────────────────────────────────────────
function printLaporan() {
  window.print();
}

// ── Auto-refresh setiap 10 detik ─────────────────────────────────────────
refreshDashboard();
setInterval(refreshDashboard, 10000);

// ── FOTO PASIEN: MODAL ────────────────────────────────────────────────────
let currentFotoResId = null;

function openFotoModal(id) {
  currentFotoResId = id;
  const r       = reservasiCacheAdmin[id];
  const preview = $('foto-modal-preview');
  const empty   = $('foto-modal-empty');
  $('foto-modal-input').value = '';

  if (r && r.foto) {
    preview.src = `${BASE_URL}uploads/reservasi/${r.foto}`;
    preview.style.display = 'block';
    empty.style.display   = 'none';
  } else {
    preview.src = '';
    preview.style.display = 'none';
    empty.style.display   = 'flex';
  }

  openModal('modal-foto-pasien');
}

async function uploadFotoPasien() {
  const input = $('foto-modal-input');
  if (!currentFotoResId) return;
  if (!input.files || !input.files[0]) {
    toast(' Pilih file foto terlebih dahulu'); return;
  }

  const file = input.files[0];
  if (file.size > 5 * 1024 * 1024) {
    toast(' Ukuran file maksimal 5MB'); return;
  }

  try {
    const fd = new FormData();
    fd.append('id', currentFotoResId);
    fd.append('foto', file);

    const res  = await fetch(`${BASE_URL}reservasi/foto`, { method: 'POST', body: fd });
    const data = await res.json();

    if (data.status === 'success') {
      toast(' Foto pasien berhasil diunggah');
      closeModal('modal-foto-pasien');
      refreshDashboard();
      fetchAndRenderFull();
    } else {
      toast(' ' + data.message);
    }
  } catch (e) {
    toast(' Kesalahan jaringan saat mengunggah foto');
  }
}
