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
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
  <style>
    [x-cloak] { display: none !important; }

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
<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900 text-white': darkMode === true}"
  >

  <!-- ============================================================ -->
  <!-- GENERAL COMPONENTS — harus selalu di luar wrapper layout     -->
  <!-- agar fixed positioning & z-index tidak terpengaruh overflow  -->
  <!-- ============================================================ -->
  <?= $this->include('partials/preloader') ?>
  <?= $this->include('partials/toast') ?>
  <?= $this->include('partials/modal_select_year') ?>
  <?= $this->include('partials/global_modal') ?>
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

  <?= $this->include('partials/master_scripts') ?>
  <?= $this->include('partials/footer_scripts') ?>
</body>
</html>