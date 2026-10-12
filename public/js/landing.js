/**
 * landing.js — Interaksi landing page TelkomMedika
 * (navbar, animasi scroll, modal login/register, tombol reservasi)
 */
function openLogin(mode) {
  const w = document.getElementById('loginWrapper');
  if (!w) return;
  w.classList.add('active');
  document.body.style.overflow = 'hidden';
  if (mode === 'register') { showRegister(); } else { showLogin(); }
}

function closeLogin() {
  const w = document.getElementById('loginWrapper');
  if (!w) return;
  w.classList.remove('active');
  document.body.style.overflow = '';
}

// Tombol "Reservasi": sudah login -> dashboard, belum -> modal login
function goReservasi() {
  if (IS_LOGGED) { window.location.href = DASHBOARD; }
  else { openLogin(); }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLogin(); });

// Navbar bayangan saat scroll
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 10);
});

// Animasi fade-up saat elemen terlihat
const io = new IntersectionObserver(entries => {
  entries.forEach(en => {
    if (en.isIntersecting) { en.target.classList.add('visible'); io.unobserve(en.target); }
  });
}, { threshold: 0.12 });
document.querySelectorAll('.fade-up').forEach(el => io.observe(el));

// /?login=1 (mis. redirect dari halaman terproteksi) langsung buka modal
if (new URLSearchParams(location.search).get('login') === '1') openLogin();
