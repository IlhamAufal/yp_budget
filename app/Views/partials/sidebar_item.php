<?php
/**
 * @var string $title Nama Menu
 * @var string $icon Class Font Awesome (misal: 'fa-solid fa-wallet')
 * @var string $url URL Tujuan
 * @var string $activePage Key page untuk deteksi active state
 */
?>
<li>
  <a
    href="<?= base_url($url ?? '#') ?>"
    class="menu-item group <?= (isset($currentPage) && $currentPage === $activePage) ? 'menu-item-active' : 'menu-item-inactive' ?>"
  >
    <i class="<?= $icon ?> w-6 text-center text-lg"></i>

    <span class="menu-item-text ml-3" :class="sidebarToggle ? 'lg:hidden' : ''">
      <?= $title ?>
    </span>
  </a>
</li>