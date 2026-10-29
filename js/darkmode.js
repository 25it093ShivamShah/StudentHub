/**
 * StudentHub – Dark Mode Manager
 * - Applies saved theme on ALL pages (persists via localStorage)
 * - Shows the toggle button ONLY on index.html (home page)
 */
(function () {
  // Apply saved theme immediately to prevent flash of unstyled content
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Re-apply after DOM ready (ensures body class is set)
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }

    // ── Only wire/inject the toggle button on the Home (index) page ──
    const isIndexPage = /\/(index\.html)?$/.test(window.location.pathname.replace(/\?.*$/, ''));

    if (!isIndexPage) return; // Other pages: just apply theme, no button

    // Wire up the existing #theamBtn on index.html
    const themeBtn = document.getElementById('theamBtn') || document.getElementById('themeBtn');
    if (themeBtn) {
      // Sync label on load
      themeBtn.textContent = document.body.classList.contains('dark-mode') ? '☀ Light Mode' : '🌙 Dark Mode';

      themeBtn.addEventListener('click', function () {
        const isDark = document.body.classList.toggle('dark-mode');
        this.textContent = isDark ? '☀ Light Mode' : '🌙 Dark Mode';
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    }
  });
})();
