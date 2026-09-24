<?php
/**
 * @var string $title Nama Menu
 * @var string $url URL Tujuan
 * @var string $activePage Key page untuk deteksi active state
 */
?>
<li>
  <a
    href="<?= base_url($url ?? '#') ?>"
    class="menu-item group <?= (isset($currentPage) && $currentPage === $activePage) ? 'menu-item-active' : 'menu-item-inactive' ?>"
  >
    <span class="menu-item-text ml-3">
      <?= $title ?>
    </span>
  </a>
</li>