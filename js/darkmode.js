/**
 * StudentHub – Global Dark Mode Manager
 * Persists theme in localStorage across ALL pages.
 */
(function () {
  // Apply theme immediately to prevent flash
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Ensure body class is correct after DOM ready
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }

    // Inject theme toggle button into nav if not already present
    const nav = document.querySelector('nav');
    const navLinks = document.getElementById('navLinks');
    const existingBtn = document.getElementById('themeBtn') || document.getElementById('theamBtn');

    if (nav && !existingBtn) {
      const btn = document.createElement('button');
      btn.id = 'themeBtn';
      btn.type = 'button';
      btn.textContent = document.body.classList.contains('dark-mode') ? '☀ Light Mode' : '🌙 Dark Mode';
      btn.style.cssText = [
        'background:#1a365d', 'color:#fff', 'border:none',
        'border-radius:6px', 'padding:7px 14px', 'cursor:pointer',
        'font-size:13px', 'font-weight:600', 'white-space:nowrap',
        'transition:background-color 0.2s ease', 'margin-left:4px'
      ].join(';');

      btn.addEventListener('mouseenter', function () {
        this.style.backgroundColor = '#2b6cb0';
      });
      btn.addEventListener('mouseleave', function () {
        this.style.backgroundColor = document.body.classList.contains('dark-mode') ? '#3182ce' : '#1a365d';
      });

      // Insert at end of navLinks div, or directly into nav
      if (navLinks) {
        navLinks.appendChild(btn);
      } else {
        nav.appendChild(btn);
      }
    }

    // Wire up click for injected OR pre-existing button
    const themeBtn = document.getElementById('themeBtn') || document.getElementById('theamBtn');
    if (themeBtn) {
      // Sync label on load
      themeBtn.textContent = document.body.classList.contains('dark-mode') ? '☀ Light Mode' : '🌙 Dark Mode';

      themeBtn.addEventListener('click', function () {
        const isDark = document.body.classList.toggle('dark-mode');
        this.textContent = isDark ? '☀ Light Mode' : '🌙 Dark Mode';
        this.style.backgroundColor = isDark ? '#3182ce' : '#1a365d';
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    }
  });
})();
