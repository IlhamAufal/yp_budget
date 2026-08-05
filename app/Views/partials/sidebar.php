<?php
  $currentGroup = 'Dashboard';
  if (url_is('foh*')) {
      $currentGroup = 'FOH';
  } elseif (url_is('opex-ga*')) {
      $currentGroup = 'OPEX_GA';
  } elseif (url_is('opex-selling*')) {
      $currentGroup = 'OPEX_Selling';
  } elseif (url_is('capex*')) {
      $currentGroup = 'CAPEX';
  } elseif (url_is('sales*')) {
      $currentGroup = 'Sales';
  } elseif (url_is('new-head-account*')) {
      $currentGroup = 'NewHeadAccount';
  } elseif (url_is('master*')) {
      $currentGroup = 'MasterData';
  }
?>
<aside
  x-data="{ selected: '<?= $currentGroup ?>' }"
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
  <!-- SIDEBAR HEADER -->
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 sidebar-header pb-7"
  >
    <a href="<?= base_url('/') ?>">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden" src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="Logo" />
        <img
          class="hidden dark:block"
          src="<?= base_url('assets/images/logo/logo-dark.svg') ?>"
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
    <!-- Sidebar Menu -->
    <nav x-data="{ selected: '<?= $currentGroup ?>' }">
      <!-- Menu Group: MENU UTAMA -->
      <div class="mb-6">
        <h3 class="mb-4 text-xs font-semibold leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            MENU UTAMA
          </span>
        </h3>

        <ul class="flex flex-col gap-1.5">
          <!-- Menu Item Dashboard -->
          <li>
            <a
              href="<?= base_url('/') ?>"
              class="menu-item group <?= (url_is('/') || url_is('dashboard*')) ? 'menu-item-active' : 'menu-item-inactive' ?>"
            >
              <i class="fa-solid fa-gauge-high text-lg min-w-[24px] text-center <?= (url_is('/') || url_is('dashboard*')) ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Dashboard
              </span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Menu Group: BUDGET & PLANNING -->
      <div class="mb-6">
        <h3 class="mb-4 text-xs font-semibold leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            BUDGET & PLANNING
          </span>
        </h3>

        <ul class="flex flex-col gap-1.5">
          <!-- Menu Item: FOH -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'FOH' ? '' : 'FOH')"
              class="menu-item group <?= url_is('foh*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'FOH' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-industry text-lg min-w-[24px] text-center <?= url_is('foh*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'FOH' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                FOH
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'FOH') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'FOH') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('foh/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('foh/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry Budget
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('foh/actual') ?>"
                    class="menu-dropdown-item group <?= url_is('foh/actual*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Realisasi (Actual)
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('foh/summary') ?>"
                    class="menu-dropdown-item group <?= url_is('foh/summary*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('foh/report/department') ?>"
                    class="menu-dropdown-item group <?= url_is('foh/report*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Report Departemen
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item: OPEX GA -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'OPEX_GA' ? '' : 'OPEX_GA')"
              class="menu-item group <?= url_is('opex-ga*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'OPEX_GA' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-building text-lg min-w-[24px] text-center <?= url_is('opex-ga*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'OPEX_GA' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Opex GA
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'OPEX_GA') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'OPEX_GA') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('opex-ga/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-ga/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry Budget
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('opex-ga/actual') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-ga/actual*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Realisasi (Actual)
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('opex-ga/report/department') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-ga/report/department*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Report Departemen
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('opex-ga/report/combine') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-ga/report/combine*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Report Combined
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item: OPEX Selling -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'OPEX_Selling' ? '' : 'OPEX_Selling')"
              class="menu-item group <?= url_is('opex-selling*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'OPEX_Selling' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-store text-lg min-w-[24px] text-center <?= url_is('opex-selling*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'OPEX_Selling' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Opex Selling
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'OPEX_Selling') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'OPEX_Selling') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('opex-selling/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-selling/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry Budget
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('opex-selling/actual') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-selling/actual*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Realisasi (Actual)
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('opex-selling/report/department') ?>"
                    class="menu-dropdown-item group <?= url_is('opex-selling/report*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Report Departemen
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item: CAPEX -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'CAPEX' ? '' : 'CAPEX')"
              class="menu-item group <?= url_is('capex*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'CAPEX' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-coins text-lg min-w-[24px] text-center <?= url_is('capex*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'CAPEX' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Capex
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'CAPEX') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'CAPEX') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('capex/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('capex/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry CAPEX
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('capex/summary') ?>"
                    class="menu-dropdown-item group <?= url_is('capex/summary*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('capex/report') ?>"
                    class="menu-dropdown-item group <?= url_is('capex/report*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Report
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item: Sales% -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'Sales' ? '' : 'Sales')"
              class="menu-item group <?= url_is('sales*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'Sales' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-chart-line text-lg min-w-[24px] text-center <?= url_is('sales*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'Sales' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Sales
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'Sales') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'Sales') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('sales/domestic/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/domestic/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry Domestic
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/export/entry') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/export/entry*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Entry Export
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/summary') ?>"
                    class="menu-dropdown-item group <?= (url_is('sales/summary') && !url_is('sales/summary/*')) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary Keseluruhan
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/summary/domestic') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/summary/domestic*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary Domestic
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/summary/export') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/summary/export*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary Export
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/summary/country') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/summary/country*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary per Negara
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('sales/summary/region') ?>"
                    class="menu-dropdown-item group <?= url_is('sales/summary/region*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Summary per Region
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>

      <!-- Menu Group: MASTER & PENGATURAN -->
      <div class="mb-6">
        <h3 class="mb-4 text-xs font-semibold leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            MASTER & PENGATURAN
          </span>
          <!-- <i
            :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
            class="fa-solid fa-ellipsis text-center text-gray-400 block py-1"
          ></i> -->
        </h3>

        <ul class="flex flex-col gap-1.5">
          <!-- Menu Item: New Head Account -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'NewHeadAccount' ? '' : 'NewHeadAccount')"
              class="menu-item group <?= url_is('new-head-account*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'NewHeadAccount' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-folder-plus text-lg min-w-[24px] text-center <?= url_is('new-head-account*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'NewHeadAccount' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                New Head Account
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'NewHeadAccount') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'NewHeadAccount') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('new-head-account') ?>"
                    class="menu-dropdown-item group <?= (url_is('new-head-account') || url_is('new-head-account/index')) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Daftar Pengajuan
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('new-head-account/create') ?>"
                    class="menu-dropdown-item group <?= url_is('new-head-account/create*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Pengajuan Baru
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <!-- Menu Item: Master Data -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'MasterData' ? '' : 'MasterData')"
              class="menu-item group <?= url_is('master*') ? 'menu-item-active' : 'menu-item-inactive' ?>"
              :class="selected === 'MasterData' ? 'menu-item-active' : ''"
            >
              <i class="fa-solid fa-database text-lg min-w-[24px] text-center <?= url_is('master*') ? 'text-brand-500 dark:text-brand-400' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' ?>" :class="selected === 'MasterData' ? 'text-brand-500 dark:text-brand-400' : ''"></i>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Master Data
              </span>
              <i
                class="fa-solid fa-chevron-down menu-item-arrow text-xs transition-transform duration-200"
                :class="[(selected === 'MasterData') ? 'menu-item-arrow-active rotate-180' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
              ></i>
            </a>

            <!-- Dropdown Menu -->
            <div
              class="overflow-hidden transition-all duration-300"
              :class="(selected === 'MasterData') ? 'block' : 'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="<?= base_url('master/coa') ?>"
                    class="menu-dropdown-item group <?= url_is('master/coa*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Master COA
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('master/cost-center') ?>"
                    class="menu-dropdown-item group <?= url_is('master/cost-center*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Master Cost Center
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('master/department') ?>"
                    class="menu-dropdown-item group <?= url_is('master/department*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Master Departemen
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('master/product') ?>"
                    class="menu-dropdown-item group <?= url_is('master/product*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Master Product
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('master/salary-mpp') ?>"
                    class="menu-dropdown-item group <?= url_is('master/salary-mpp*') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Master Salary MPP
                  </a>
                </li>
                <li>
                  <a
                    href="<?= base_url('master/configure-period') ?>"
                    class="menu-dropdown-item group <?= (url_is('master/configure-period*') || url_is('master/period*')) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive' ?>"
                  >
                    Configure Period
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </nav>
    <!-- Sidebar Menu -->
  </div>
</aside>
