/**
 * login.js — JavaScript Halaman Login/Register TelkomMedika
 * (pengganti landing page; halaman ini satu-satunya pintu masuk aplikasi)
 */

function showRegister() {
  document.getElementById('loginForm').classList.add('hidden');
  document.getElementById('registerForm').classList.remove('hidden');
  clearAlert('loginAlert');
}
function showLogin() {
  document.getElementById('registerForm').classList.add('hidden');
  document.getElementById('loginForm').classList.remove('hidden');
  clearAlert('registerAlert');
}

function showAlert(id, msg, type) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.className   = 'alert ' + type;
  el.style.display = 'block';
}
function clearAlert(id) {
  const el = document.getElementById(id);
  el.style.display = 'none';
  el.textContent   = '';
}

function setLoading(btnId, loading) {
  const btn = document.getElementById(btnId);
  if (!btn) return;
  if (loading) {
    btn.disabled   = true;
    btn.innerHTML  = '<span class="spinner"></span>Memproses...';
  } else {
    btn.disabled   = false;
    btn.textContent = btnId === 'loginBtn' ? 'Masuk' : 'Daftar Sekarang';
  }
}

function doLogin() {
  const username = document.getElementById('loginUsername').value.trim();
  const password = document.getElementById('loginPassword').value;
  clearAlert('loginAlert');
  if (!username || !password) {
    showAlert('loginAlert', 'Username dan password wajib diisi.', 'error');
    return;
  }
  setLoading('loginBtn', true);
  const fd = new FormData();
  fd.append('username', username);
  fd.append('password', password);
  fetch(BASE_URL + 'auth/login', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      setLoading('loginBtn', false);
      if (data.status === 'success') {
        showAlert('loginAlert', data.message, 'success');
        setTimeout(() => { window.location.href = data.redirect; }, 800);
      } else {
        showAlert('loginAlert', data.message, 'error');
      }
    })
    .catch(() => {
      setLoading('loginBtn', false);
      showAlert('loginAlert', 'Terjadi kesalahan. Coba lagi.', 'error');
    });
}

function doRegister() {
  const nama     = document.getElementById('regNama').value.trim();
  const username = document.getElementById('regUsername').value.trim();
  const email    = document.getElementById('regEmail').value.trim();
  const password = document.getElementById('regPassword').value;
  const konfirm  = document.getElementById('regKonfirm').value;
  clearAlert('registerAlert');
  if (!nama || !username || !email || !password || !konfirm) {
    showAlert('registerAlert', 'Semua field wajib diisi.', 'error'); return;
  }
  if (password !== konfirm) {
    showAlert('registerAlert', 'Password dan konfirmasi tidak cocok.', 'error'); return;
  }
  if (password.length < 6) {
    showAlert('registerAlert', 'Password minimal 6 karakter.', 'error'); return;
  }
  setLoading('regBtn', true);
  const fd = new FormData();
  fd.append('nama', nama); fd.append('username', username); fd.append('email', email);
  fd.append('password', password); fd.append('konfirmasi', konfirm);
  fetch(BASE_URL + 'auth/register', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      setLoading('regBtn', false);
      if (data.status === 'success') {
        showAlert('registerAlert', data.message, 'success');
        setTimeout(showLogin, 1200);
      } else {
        showAlert('registerAlert', data.message, 'error');
      }
    })
    .catch(() => {
      setLoading('regBtn', false);
      showAlert('registerAlert', 'Terjadi kesalahan. Coba lagi.', 'error');
    });
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    const regVisible = !document.getElementById('registerForm').classList.contains('hidden');
    if (regVisible) { doRegister(); } else { doLogin(); }
  }
});
