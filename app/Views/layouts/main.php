<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title><?= $title ?? 'Budget Planning & Monitoring System - CI4' ?></title>
  <link rel="icon" href="<?= base_url('favicon.ico') ?>">
  <meta name="csrf-token-name" content="<?= csrf_token() ?>" />
  <meta name="csrf-hash" content="<?= csrf_hash() ?>" />
  <?= csrf_meta() ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300..900;1,300..900&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
  <style>
    /* ============================================================ */
    /* GLOBAL FONT: POPPINS / MONTSERRAT                            */
    /* ============================================================ */
    body, button, input, select, textarea, table, th, td, h1, h2, h3, h4, h5, h6, p, span, a, div, li, nav {
      font-family: 'Poppins', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }

    /* ============================================================ */
    /* GLOBAL THEAD PRIMARY (BRAND NAVY #2F3185) & TEXT-WHITE       */
    /* ============================================================ */
    thead, table thead, thead tr {
      background-color: #2F3185 !important;
      color: #ffffff !important;
    }
    thead th, table thead th, thead tr th {
      color: #ffffff !important;
      font-weight: 600 !important;
      font-size: 0.75rem !important; /* 12px text-xs */
      text-transform: none !important;
      border-color: rgba(255, 255, 255, 0.18) !important;
    }
    .dark thead, .dark table thead, .dark thead tr {
      background-color: #25276d !important;
      color: #ffffff !important;
    }
    .dark thead th, .dark table thead th, .dark thead tr th {
      color: #ffffff !important;
      border-color: rgba(255, 255, 255, 0.18) !important;
    }

    /* ============================================================ */
    /* REMOVE UPPERCASE STYLING                                      */
    /* ============================================================ */
    .uppercase {
      text-transform: none !important;
    }

    /* ============================================================ */
    /* ACTIVE PAGINATION STYLING (BRAND PRIMARY NAVY #2F3185)       */
    /* ============================================================ */
    .pagination .active,
    .pagination .page-item.active .page-link,
    .pagination li.active a,
    [aria-current="page"] {
      background-color: #2F3185 !important;
      color: #ffffff !important;
      border-color: #2F3185 !important;
      font-weight: 700 !important;
    }

    /* ============================================================ */
    /* BRAND NAVY #2F3185 UTILITIES & TAB-VIEW STYLING              */
    /* ============================================================ */
    .bg-\[\#2F3185\], .bg-brand-navy { background-color: #2F3185 !important; }
    .bg-\[\#25276d\], .bg-brand-navy-dark { background-color: #25276d !important; }
    .bg-\[\#1e2056\] { background-color: #1e2056 !important; }
    .text-\[\#2F3185\], .text-brand-navy { color: #2F3185 !important; }
    .border-\[\#2F3185\], .border-brand-navy { border-color: #2F3185 !important; }
    .border-\[\#25276d\] { border-color: #25276d !important; }
    .hover\:bg-\[\#25276d\]:hover { background-color: #25276d !important; }
    .bg-\[\#2F3185\]\/10 { background-color: rgba(47, 49, 133, 0.1) !important; }
    .bg-\[\#2F3185\]\/15 { background-color: rgba(47, 49, 133, 0.15) !important; }
    .bg-\[\#2F3185\]\/20 { background-color: rgba(47, 49, 133, 0.2) !important; }
    .bg-\[\#2F3185\]\/5  { background-color: rgba(47, 49, 133, 0.05) !important; }
    .border-\[\#2F3185\]\/30 { border-color: rgba(47, 49, 133, 0.3) !important; }
    .border-\[\#2F3185\]\/20 { border-color: rgba(47, 49, 133, 0.2) !important; }

    /* Tab navigation utilities: Inactive #2F3185 with white text, Active White with #2F3185 text */
    .nav-tab-container {
      display: inline-flex;
      max-width: 100%;
      background-color: #2F3185 !important;
      padding: 0.375rem;
      border-radius: 1rem;
    }
    .tab-btn {
      background-color: #2F3185 !important;
      color: #ffffff !important;
      font-weight: 600 !important;
      transition: all 0.2s ease-in-out;
    }
    .tab-btn:hover {
      background-color: #25276d !important;
      color: #ffffff !important;
    }
    .tab-btn.active,
    .tab-btn[data-active="true"] {
      background-color: #ffffff !important;
      color: #2F3185 !important;
      font-weight: 700 !important;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .tab-btn.active span,
    .tab-btn[data-active="true"] span {
      color: #2F3185 !important;
    }
    .tab-btn:not(.active):not([data-active="true"]) span {
      color: #ffffff !important;
    }
    .tab-btn.active .tab-badge,
    .tab-btn[data-active="true"] .tab-badge {
      background-color: rgba(47, 49, 133, 0.12) !important;
      color: #2F3185 !important;
    }
    .tab-btn:not(.active):not([data-active="true"]) .tab-badge {
      background-color: rgba(255, 255, 255, 0.25) !important;
      color: #ffffff !important;
    }

    [x-cloak] { display: none !important; }

    /* ============================================================ */
    /* Z-INDEX UTILITIES (arbitrary values)                          */
    /* Class z-[...] TIDAK ter-generate di style.css hasil compile,  */
    /* sehingga modal/toast ber-z-index auto dan tertutup navbar     */
    /* (z-99999) & sidebar (z-9999). Didefinisikan manual di sini.   */
    /* ============================================================ */
    .z-\[1\]        { z-index: 1; }
    .z-\[999999\]   { z-index: 999999; }
    .z-\[9999999\]  { z-index: 9999999; }
    .z-\[99999999\] { z-index: 99999999; }

    /* ============================================================ */
    /* MISSING TAILWIND UTILITIES                                    */
    /* Utilitas standar yang tidak ter-generate di style.css namun   */
    /* dipakai luas di views (toast, modal, transisi, flex).         */
    /* ============================================================ */
    .pointer-events-auto { pointer-events: auto; }
    .flex-col-reverse { flex-direction: column-reverse; }
    .translate-y-2 { transform: translateY(0.5rem); }
    .shrink-0 { flex-shrink: 0; }
    .transition-all {
      transition-property: all;
      transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
      transition-duration: 150ms;
    }
    .duration-200 { transition-duration: 200ms; }
    .shadow-xl {
      box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }
    .border-emerald-500\/30 { border-color: rgb(16 185 129 / 0.3); }
    .border-red-500\/30 { border-color: rgb(239 68 68 / 0.3); }
    .border-brand-500\/30 { border-color: rgb(70 95 255 / 0.3); }

    /* ============================================================ */
    /* LAYOUT STATE — vanilla replacement of Alpine.js bindings      */
    /* State dikendalikan class di <body>:                           */
    /*   body.sidebar-open   — sidebar mobile terbuka                */
    /*   body.sidebar-closed — sidebar desktop disembunyikan         */
    /*   body.menu-open      — panel menu navbar terbuka di mobile   */
    /* ============================================================ */
    #preloader { transition: opacity .3s ease-in; }

    #app-sidebar { transition: transform .3s ease; }
    #sidebar-overlay { display: none; }
    #navbar-menu { display: none; }
    #hamburger-close { display: none; }

    #user-menu-panel { display: none; }
    #user-menu.open #user-menu-panel { display: flex; }
    #user-menu-chevron { transition: transform .2s ease; }
    #user-menu.open #user-menu-chevron { transform: rotate(180deg); }

    /* Sidebar submenu accordion — panah indikator murni CSS */
    .submenu-container { display: none; }
    li.submenu-open > .submenu-container { display: block; }
    .menu-item-arrow {
      display: block;
      width: 7px;
      height: 7px;
      margin-left: auto;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      rotate: 45deg;
      transition: rotate .2s ease;
    }
    li.submenu-open > a .menu-item-arrow { rotate: 225deg; }

    @media (max-width: 1023.98px) {
      #app-sidebar { transform: translateX(-100%); }
      body.sidebar-open #app-sidebar { transform: translateX(0); }
      body.sidebar-open #sidebar-overlay { display: block; }
      body.menu-open #navbar-menu { display: flex; }

      body.sidebar-open #sidebar-toggle-btn { background-color: #f3f4f6; }
      .dark body.sidebar-open #sidebar-toggle-btn { background-color: #1f2937; }
      body.menu-open #menu-toggle-btn { background-color: #f3f4f6; }
      .dark body.menu-open #menu-toggle-btn { background-color: #1f2937; }

      body.sidebar-open #hamburger-close { display: block; }
      body.sidebar-open #hamburger-bars-mobile { display: none; }
    }

    @media (min-width: 1024px) {
      #navbar-menu { display: flex; }
      body.sidebar-closed #app-sidebar { display: none; }
    }


    /* Global Darkmode Text Enforcement: Make all non-accent text pure white */
    .dark body,
    .dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6,
    .dark p, .dark label, .dark td, .dark th, .dark caption,
    .dark .text-gray-400, .dark .text-gray-500, .dark .text-gray-600, .dark .text-gray-700, .dark .text-gray-800, .dark .text-gray-900,
    .dark .dark\:text-gray-200, .dark .dark\:text-gray-300, .dark .dark\:text-gray-400, .dark .dark\:text-gray-500 {
      color: #ffffff !important;
    }

    /* ============================================================ */
    /* COLOR UTILITY SHIMS FOR BUTTONS & ACCENTS                    */
    /* ============================================================ */
    /* Brand Palette */
    .bg-brand-500 { background-color: #465fff !important; }
    .bg-brand-600 { background-color: #3641f5 !important; }
    .hover\:bg-brand-600:hover { background-color: #3641f5 !important; }
    .hover\:bg-brand-700:hover { background-color: #252dae !important; }
    .text-brand-500 { color: #465fff !important; }
    .text-brand-600 { color: #3641f5 !important; }
    .border-brand-500 { border-color: #465fff !important; }
    .border-brand-600 { border-color: #3641f5 !important; }
    .dark .dark\:text-brand-400 { color: #7592ff !important; }
    .dark .dark\:border-brand-400 { border-color: #7592ff !important; }
    .dark .dark\:bg-brand-500 { background-color: #465fff !important; }
    .dark .dark\:hover\:bg-brand-600:hover { background-color: #3641f5 !important; }

    /* Primary Palette & Alpha Variants (TailAdmin compatibility) */
    .bg-primary { background-color: #465fff !important; }
    .hover\:bg-primary:hover { background-color: #3641f5 !important; }
    .hover\:bg-primary\/90:hover { background-color: rgba(70, 95, 255, 0.9) !important; }
    .hover\:bg-opacity-90:hover { opacity: 0.9 !important; }
    .text-primary { color: #465fff !important; }
    .border-primary { border-color: #465fff !important; }
    .bg-primary\/5 { background-color: rgba(70, 95, 255, 0.05) !important; }
    .bg-primary\/10 { background-color: rgba(70, 95, 255, 0.1) !important; }
    .bg-primary\/15 { background-color: rgba(70, 95, 255, 0.15) !important; }
    .bg-primary\/20 { background-color: rgba(70, 95, 255, 0.2) !important; }
    .bg-primary\/25 { background-color: rgba(70, 95, 255, 0.25) !important; }
    .bg-primary\/30 { background-color: rgba(70, 95, 255, 0.3) !important; }
    .bg-primary\/40 { background-color: rgba(70, 95, 255, 0.4) !important; }
    .border-primary\/20 { border-color: rgba(70, 95, 255, 0.2) !important; }
    .border-primary\/30 { border-color: rgba(70, 95, 255, 0.3) !important; }
    .border-primary\/40 { border-color: rgba(70, 95, 255, 0.4) !important; }
    .text-danger { color: #f04438 !important; }
    .bg-danger { background-color: #f04438 !important; }
    .text-warning { color: #f79009 !important; }
    .bg-warning { background-color: #fdb022 !important; }

    /* Standard Blue & Sky Palette */
    .bg-blue-500 { background-color: #3b82f6 !important; }
    .bg-blue-600 { background-color: #2563eb !important; }
    .bg-blue-700 { background-color: #1d4ed8 !important; }
    .hover\:bg-blue-600:hover { background-color: #2563eb !important; }
    .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
    .hover\:bg-blue-800:hover { background-color: #1e40af !important; }
    .text-blue-500 { color: #3b82f6 !important; }
    .text-blue-600 { color: #2563eb !important; }
    .text-blue-700 { color: #1d4ed8 !important; }
    .bg-sky-500 { background-color: #0ea5e9 !important; }
    .bg-sky-600 { background-color: #0284c7 !important; }
    .hover\:bg-sky-600:hover { background-color: #0284c7 !important; }
    .hover\:bg-sky-700:hover { background-color: #0369a1 !important; }
    .text-sky-500 { color: #0ea5e9 !important; }
    .text-sky-600 { color: #0284c7 !important; }

    /* Emerald & Green Palette (Excel / Success) */
    .bg-emerald-500 { background-color: #10b981 !important; }
    .bg-emerald-600 { background-color: #059669 !important; }
    .bg-emerald-700 { background-color: #047857 !important; }
    .hover\:bg-emerald-600:hover { background-color: #059669 !important; }
    .hover\:bg-emerald-700:hover { background-color: #047857 !important; }
    .hover\:bg-emerald-800:hover { background-color: #065f46 !important; }
    .text-emerald-500 { color: #10b981 !important; }
    .text-emerald-600 { color: #059669 !important; }
    .text-emerald-700 { color: #047857 !important; }
    .bg-emerald-50 { background-color: #ecfdf5 !important; }
    .hover\:bg-emerald-100:hover { background-color: #d1fae5 !important; }
    .border-emerald-200 { border-color: #a7f3d0 !important; }
    .border-emerald-300 { border-color: #6ee7b7 !important; }
    .bg-green-500 { background-color: #22c55e !important; }
    .bg-green-600 { background-color: #16a34a !important; }
    .bg-green-700 { background-color: #15803d !important; }
    .hover\:bg-green-600:hover { background-color: #16a34a !important; }
    .hover\:bg-green-700:hover { background-color: #15803d !important; }
    .text-green-600 { color: #16a34a !important; }
    .text-green-700 { color: #15803d !important; }
    .bg-green-50 { background-color: #f0fdf4 !important; }
    .hover\:bg-green-100:hover { background-color: #dcfce7 !important; }
    .dark .dark\:text-emerald-400 { color: #34d399 !important; }
    .dark .dark\:text-emerald-300 { color: #6ee7b7 !important; }
    .dark .dark\:bg-emerald-950\/30 { background-color: rgba(6, 78, 59, 0.3) !important; }
    .dark .dark\:bg-emerald-950\/40 { background-color: rgba(6, 78, 59, 0.4) !important; }
    .dark .dark\:hover\:bg-emerald-900\/40:hover { background-color: rgba(6, 78, 59, 0.4) !important; }
    .dark .dark\:hover\:bg-emerald-900\/50:hover { background-color: rgba(6, 78, 59, 0.5) !important; }

    /* Amber / Warning Palette */
    .bg-amber-500 { background-color: #f59e0b !important; }
    .bg-amber-600 { background-color: #d97706 !important; }
    .hover\:bg-amber-600:hover { background-color: #d97706 !important; }
    .hover\:bg-amber-700:hover { background-color: #b45309 !important; }
    .text-amber-500 { color: #f59e0b !important; }
    .text-amber-600 { color: #d97706 !important; }

    /* Red / Danger Palette */
    .bg-red-500 { background-color: #ef4444 !important; }
    .bg-red-600 { background-color: #dc2626 !important; }
    .bg-red-700 { background-color: #b91c1c !important; }
    .hover\:bg-red-600:hover { background-color: #dc2626 !important; }
    .hover\:bg-red-700:hover { background-color: #b91c1c !important; }
    .text-red-500 { color: #ef4444 !important; }
    .text-red-600 { color: #dc2626 !important; }
  </style>
</head>
<body id="app-body">

  <!-- ============================================================ -->
  <!-- GENERAL COMPONENTS (pre-layout)                              -->
  <!-- ============================================================ -->
  <?= $this->include('partials/preloader') ?>
  <?= $this->include('partials/toast') ?>
  <?= $this->renderSection('modals') ?>
  <!-- ============================================================ -->

  <div class="flex h-screen overflow-hidden">
    <?= $this->include('partials/sidebar') ?>

    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      <?= $this->include('partials/overlay') ?>
      <?= $this->include('partials/navbar') ?>

      <main>
        <?= $this->renderSection('content') ?>
      </main>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- MODALS — rendered AFTER layout so they sit on top of all     -->
  <!-- ============================================================ -->
  <?= $this->include('partials/modal_select_year') ?>
  <?= $this->include('partials/global_modal') ?>
  <?= $this->include('partials/confirm_modal') ?>

  <?= $this->include('partials/master_scripts') ?>
  <?= $this->include('partials/footer_scripts') ?>
</body>
</html>