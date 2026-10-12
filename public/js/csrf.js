/**
 * csrf.js — Menyisipkan header X-CSRF-TOKEN otomatis ke semua fetch()
 * non-GET ke origin yang sama (diperlukan oleh proteksi CSRF Laravel).
 */
(function () {
  const meta  = document.querySelector('meta[name="csrf-token"]');
  const token = meta ? meta.content : '';
  const origFetch = window.fetch.bind(window);

  window.fetch = function (input, init) {
    init = init || {};
    const method = (init.method || 'GET').toUpperCase();
    if (method !== 'GET' && method !== 'HEAD') {
      const headers = new Headers(init.headers || {});
      headers.set('X-CSRF-TOKEN', token);
      headers.set('X-Requested-With', 'XMLHttpRequest');
      headers.set('Accept', 'application/json');
      init.headers = headers;
      init.credentials = init.credentials || 'same-origin';
    }
    return origFetch(input, init);
  };
})();
