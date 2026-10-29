/**
 * StudentHub – Dark Mode Manager
 * Theme is applied INLINE on every page immediately after <body> opens.
 * This script only wires the toggle button which exists on index.html only.
 */
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    // Sync button label with current theme
    var btn = document.getElementById('theamBtn') || document.getElementById('themeBtn');
    if (!btn) return; // No button on this page – nothing to do

    btn.textContent = document.body.classList.contains('dark-mode') ? '☀ Light Mode' : '🌙 Dark Mode';

    btn.addEventListener('click', function () {
      var isDark = document.body.classList.toggle('dark-mode');
      this.textContent = isDark ? '☀ Light Mode' : '🌙 Dark Mode';
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
  });
})();
