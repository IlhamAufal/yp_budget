<?php
// Load explicitly because this partial can be rendered outside the normal helper bootstrap.
helper('menu_helper');

// Navigation is rendered from the same effective authorization context as RoleFilter.
$currentPath = trim((string) uri_string(), '/');
?>
<aside
  x-data="{ selected: '' }"
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
  <div class="sidebar-header flex h-20 items-center justify-center px-4 pt-4 pb-2 transition-all duration-300">
    <a href="<?= base_url('/') ?>" class="flex items-center justify-center w-full">
      <span class="logo flex items-center justify-center" x-show="!sidebarToggle" x-transition.opacity>
        <img class="dark:hidden max-h-9 w-auto object-contain" src="<?= base_url('assets/images/logo/logo-sidebar.svg') ?>" alt="Logo" />
        <img class="hidden dark:block max-h-9 w-auto object-contain" src="<?= base_url('assets/images/logo/logo-sidebar.svg') ?>" alt="Logo" />
      </span>
      <span class="logo-icon flex items-center justify-center" x-show="sidebarToggle" x-transition.opacity>
        <img class="max-h-9 w-auto object-contain" src="<?= base_url('assets/images/logo/logo-icon.svg') ?>" alt="Logo" />
      </span>
    </a>
  </div>

  <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
    <?= render_menu_sidebar() ?>
  </div>
</aside>
