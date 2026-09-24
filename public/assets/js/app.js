/* =====================================================================
 * app.js — Core layout behaviors (vanilla JS, tanpa Alpine.js)
 *
 * Menangani: dark mode, sidebar toggle/collapse, mobile menu,
 * user dropdown, submenu accordion, preloader, dan aksi generik
 * via atribut data-action.
 * ===================================================================== */
(function () {
  'use strict';

  var body = document.body;
  var DARK_KEY = 'darkMode';
  var LG = window.matchMedia('(min-width: 1024px)');

  /* ---------------- Dark mode ---------------- */

  function readDark() {
    try {
      return JSON.parse(localStorage.getItem(DARK_KEY)) === true;
    } catch (e) {
      return false;
    }
  }

  function applyDark(on) {
    body.classList.toggle('dark', on);
    body.classList.toggle('bg-gray-900', on);
    body.classList.toggle('text-white', on);
  }

  var dark = readDark();
  applyDark(dark);

  function toggleDark() {
    dark = !dark;
    localStorage.setItem(DARK_KEY, JSON.stringify(dark));
    applyDark(dark);
  }

  /* ---------------- Sidebar ---------------- */

  function sidebarOpen() {
    return body.classList.contains('sidebar-open');
  }

  function setSidebar(open) {
    if (LG.matches) {
      body.classList.toggle('sidebar-closed', !open);
    } else {
      body.classList.toggle('sidebar-open', open);
    }
  }

  function toggleSidebar() {
    if (LG.matches) {
      body.classList.toggle('sidebar-closed');
    } else {
      body.classList.toggle('sidebar-open');
    }
  }

  /* ---------------- User dropdown & click-outside ---------------- */

  function closeUserMenu() {
    var menu = document.getElementById('user-menu');
    if (menu) menu.classList.remove('open');
  }

  document.addEventListener('click', function (e) {
    var menu = document.getElementById('user-menu');
    if (menu && menu.classList.contains('open') && !menu.contains(e.target)) {
      menu.classList.remove('open');
    }
  });

  /* ---------------- Delegated actions ---------------- */

  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-action]');
    if (!el) return;

    switch (el.getAttribute('data-action')) {
      case 'toggle-dark':
        e.preventDefault();
        toggleDark();
        break;

      case 'toggle-sidebar':
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
        break;

      case 'close-sidebar':
        setSidebar(false);
        break;

      case 'toggle-menu':
        e.preventDefault();
        e.stopPropagation();
        body.classList.toggle('menu-open');
        break;

      case 'toggle-user-menu':
        e.preventDefault();
        e.stopPropagation();
        if (el.id !== 'user-menu') {
          var host = el.closest('#user-menu') || document.getElementById('user-menu');
          if (host) host.classList.toggle('open');
        }
        break;

      case 'open-year-modal':
        window.dispatchEvent(new CustomEvent('open-year-modal'));
        break;

      case 'open-modal':
        if (typeof window.Modal === 'undefined') {
          console.warn('[app.js] Modal belum tersedia.');
          break;
        }
        window.Modal.show({
          url: el.getAttribute('data-modal-url'),
          title: el.getAttribute('data-modal-title') || 'Detail',
          size: el.getAttribute('data-modal-size') || 'md'
        });
        closeUserMenu();
        break;

      case 'dismiss':
        var targetSel = el.getAttribute('data-dismiss-target');
        var target = targetSel ? document.querySelector(targetSel) : el.closest('[data-dismissible]');
        if (target && target.parentNode) target.parentNode.removeChild(target);
        break;
    }
  });

  /* ---------------- Submenu accordion (sidebar) ---------------- */

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-submenu-target]');
    if (!trigger) return;
    e.preventDefault();

    var li = trigger.closest('li');
    if (!li) return;

    li.classList.toggle('submenu-open');
    trigger.classList.toggle('menu-item-active');
    trigger.classList.toggle('menu-item-inactive');
  });

  /* ---------------- Preloader ---------------- */

  function hidePreloader() {
    var pre = document.getElementById('preloader');
    if (!pre) return;
    setTimeout(function () {
      pre.classList.add('opacity-0');
      setTimeout(function () {
        if (pre.parentNode) pre.parentNode.removeChild(pre);
      }, 300);
    }, 500);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hidePreloader);
  } else {
    hidePreloader();
  }

  /* Escape menutup sidebar mobile */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebarOpen() && !LG.matches) {
      setSidebar(false);
    }
  });
})();
