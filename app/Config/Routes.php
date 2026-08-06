<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Working Year Context Route
$routes->post('set-year', 'PeriodController::setYear');
$routes->get('api/active-years', 'PeriodController::getActiveYears');

// Login routes (public)
$routes->get('login', 'LoginController::index');
$routes->post('login/process', 'LoginController::process');
$routes->get('logout', 'LoginController::logout');

// Protected routes (require auth)
$routes->get('/', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Master Data (COA, Cost Center, Departemen, Periode)
$routes->group('master', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Master::index');
    $routes->get('coa', 'Master::coa');
    $routes->get('coa/export', 'Master::coaExport');
    $routes->get('cost-center', 'Master::costCenter');
    $routes->get('cost-center/export', 'Master::costCenterExport');
    $routes->get('department', 'Master::department');
    $routes->get('department/export', 'Master::departmentExport');
    $routes->get('product', 'Master::product');
    $routes->get('product/export', 'Master::productExport');
    $routes->get('salary-mpp', 'Master::salaryMpp');
    $routes->get('salary-mpp/export', 'Master::salaryMppExport');
    $routes->get('configure-period', 'Master::configurePeriod');
    $routes->get('period', 'Master::period');

    $routes->post('api/coa/save', 'Master::coaSave');
    $routes->post('api/coa/toggle', 'Master::coaToggle');
    $routes->post('api/coa/copy-year', 'Master::coaCopyYear');

    $routes->post('api/cost-center/save', 'Master::costCenterSave');
    $routes->post('api/cost-center/toggle', 'Master::costCenterToggle');

    $routes->post('api/department/save', 'Master::departmentSave');
    $routes->post('api/department/toggle', 'Master::departmentToggle');

    $routes->post('api/product/save', 'Master::productSave');
    $routes->post('api/product/toggle', 'Master::productToggle');

    $routes->post('api/salary-mpp/save', 'Master::salaryMppSave');
    $routes->post('api/salary-mpp/toggle', 'Master::salaryMppToggle');

    $routes->post('api/period/save', 'Master::periodSave');
    $routes->post('api/period/set-active', 'Master::periodSetActive');
    $routes->post('api/period/set-locked', 'Master::periodSetLocked');
    $routes->post('api/period/delete', 'Master::periodDelete');
});

// System Administration — Phase 1.1 RBAC (Menu, Role, User Management)
$routes->group('sys-admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('menu', 'Menu::index');
    $routes->post('api/menu/save', 'Menu::save');
    $routes->post('api/menu/toggle', 'Menu::toggle');
    $routes->post('api/menu/delete', 'Menu::delete');

    $routes->get('role', 'Role::index');
    $routes->post('api/role/save', 'Role::save');
    $routes->post('api/role/toggle', 'Role::toggle');
    $routes->post('api/role/delete', 'Role::delete');
    $routes->post('api/role/menus', 'Role::menus');
    $routes->post('api/role/menus/save', 'Role::saveMenus');

    $routes->get('user', 'User::index');
    $routes->post('api/user/save', 'User::save');
    $routes->post('api/user/toggle', 'User::toggle');
    $routes->post('api/user/delete', 'User::delete');
    $routes->post('api/user/reset-password', 'User::resetPassword');
});

// -------------------------------------------------------------------
// MODUL TRANSASI (raw files dihubungkan ke skema & route CI4)
// -------------------------------------------------------------------

// Man Power Planning (MPP) — Entry, AJAX Table, Summary, Sync OPEX
$routes->group('mpp', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MppController::index');
    $routes->get('entry', 'MppController::index');
    $routes->get('getEntryTable', 'MppController::getEntryTable');
    $routes->post('saveBudget', 'MppController::saveBudget');
    $routes->get('summary', 'MppController::summary');
    $routes->post('syncToOpex', 'MppController::syncToOpex');
});

// CAPEX
$routes->group('capex', ['filter' => 'auth'], function ($routes) {
    $routes->get('entry', 'CapexController::entry');
    $routes->get('report', 'CapexController::report');
    $routes->get('summary', 'CapexController::summary');
    $routes->get('manual_book', 'CapexController::manual_book');

    // AJAX Endpoints
    $routes->post('entry_budget_table', 'CapexController::entryBudgetTable');
    $routes->post('save_capex', 'CapexController::saveCapex');
    $routes->post('sync_to_opex', 'CapexController::syncToOpex');
});

// OPEX GA (sidebar pakai dash; beberapa view pakai underscore)
$routes->group('opex-ga', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexGaController::index');
    $routes->get('index', 'OpexGaController::index');
    $routes->get('entry', 'OpexGaController::entryBudget');
    $routes->get('entry-budget', 'OpexGaController::entryBudget');
    $routes->get('actual', 'OpexGaController::actual');
    $routes->get('report/department', 'OpexGaController::reportDepartment');
    $routes->get('report/combine', 'OpexGaController::index');
    $routes->post('cari_actual_table', 'OpexGaController::cariActualTable');
    $routes->get('export_template_opex_ga/(:num)', 'OpexGaController::exportTemplateOpexGa/$1');
    $routes->post('saveBudgetDetail', 'OpexGaController::saveBudgetDetail');
    $routes->post('processUpload', 'OpexGaController::processUpload');
    $routes->get('exportExcel', 'OpexGaController::exportExcel');
});
// Alias tanpa dash (beberapa view memakai base_url('opexga/...'))
$routes->group('opexga', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexGaController::index');
    $routes->get('exportExcel', 'OpexGaController::exportExcel');
});
$routes->group('opex_ga', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexGaController::index');
    $routes->get('index', 'OpexGaController::index');
    $routes->get('entry', 'OpexGaController::entryBudget');
    $routes->get('actual', 'OpexGaController::actual');
    $routes->get('report/department', 'OpexGaController::reportDepartment');
    $routes->post('cari_actual_table', 'OpexGaController::cariActualTable');
});

// OPEX Selling
$routes->group('opex-selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->get('entry-budget', 'OpexSellingController::index');
    $routes->get('actual', 'OpexSellingController::actual');
    $routes->get('report/department', 'OpexSellingController::reportDepartment');
    $routes->post('entryBudgetDetail', 'OpexSellingController::entryBudgetDetail');
    $routes->post('cari_actual_table', 'OpexSellingController::cariActualTable');
    $routes->post('saveBudget', 'OpexSellingController::saveBudget');
    $routes->post('uploadActual', 'OpexSellingController::uploadActual');
});
$routes->group('opex_selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->get('entryBudgetDetail', 'OpexSellingController::entryBudgetDetail');
    $routes->post('cari_actual_table', 'OpexSellingController::cariActualTable');
    $routes->post('saveBudget', 'OpexSellingController::saveBudget');
    $routes->get('report/department', 'OpexSellingController::reportDepartment');
    $routes->post('uploadActual', 'OpexSellingController::uploadActual');
});

// Sales
$routes->group('sales', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SalesController::index');
    $routes->get('summary', 'SalesController::index');
    $routes->get('summary/domestic', 'SalesController::index');
    $routes->get('summary/export', 'SalesController::index');
    $routes->get('summary/country', 'SalesController::index');
    $routes->get('summary/region', 'SalesController::index');
    $routes->get('domestic/entry', 'SalesController::entryDomestic');
    $routes->get('export/entry', 'SalesController::entryExport');
    $routes->get('simulation', 'SalesController::simulation');
    $routes->get('setup-target', 'SalesController::setupTarget');
    $routes->post('saveDiscountReclass', 'SalesController::saveDiscountReclass');
    $routes->post('processUpload', 'SalesController::processUpload');
    $routes->get('exportExcel', 'SalesController::exportExcel');
});

// Monitoring Progress Entry (Phase 3.1) & P/L Report (Phase 3.2)
$routes->group('monitoring', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PlController::index');
    $routes->get('cari_view_data', 'PlController::cariViewData');
});
$routes->group('pl', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PlController::summary');
    $routes->get('get_detail_account', 'PlController::getDetailAccount');
    $routes->get('export_excel', 'PlController::exportExcel');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
