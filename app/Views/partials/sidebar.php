<?php
  /**
   * Sidebar dinamis (PRD Phase 1.2).
   * Struktur menu dirender dari database gw_sm__menu via MenuBuilder
   * (app/Libraries/MenuBuilder.php → helper render_menu_sidebar()).
   *
   * Hirarki, ikon, grouping & status aktif diambil dari DB, difilter
   * berdasarkan role user (gw_sm__rolemenu).
   */
?>
<aside
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
>
  <!-- SIDEBAR HEADER -->
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 sidebar-header pb-7"
  >
    <a href="<?= base_url('/') ?>">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden" src="<?= base_url('assets/images/logo/logo-sidebar.svg') ?>" alt="Logo" />
        <img
          class="hidden dark:block"
          src="<?= base_url('assets/images/logo/logo-sidebar-dark.svg') ?>"
          alt="Logo"
        />
      </span>

      <img
        class="logo-icon"
        :class="sidebarToggle ? 'lg:block' : 'hidden'"
        src="<?= base_url('assets/images/logo/logo-icon.svg') ?>"
        alt="Logo"
      />
    </a>
  </div>
  <!-- SIDEBAR HEADER -->

  <div
    class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
  >
    <!-- Sidebar Menu (dinamis dari DB) -->
    <?= render_menu_sidebar() ?>
    <!-- Sidebar Menu -->
  </div>
</aside>
