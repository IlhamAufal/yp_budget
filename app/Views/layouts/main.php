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