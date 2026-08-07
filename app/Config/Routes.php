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
    $routes->get('user/form', 'User::formModal');
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
    $routes->get('summary', 'CapexController::summary');

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
    $routes->get('export_template_opex_ga/(:num)', 'OpexGaController::exportTemplateOpexGa/$1');
    $routes->get('getEntryData', 'OpexGaController::getEntryData');
    $routes->post('save_budget', 'OpexGaController::saveBudget');
    $routes->post('submit_budget', 'OpexGaController::submitBudget');
    $routes->post('getActualData', 'OpexGaController::getActualData');
    $routes->get('getDetailItems', 'OpexGaController::getDetailItems');
    $routes->post('saveDetail', 'OpexGaController::saveDetail');
    $routes->post('deleteDetail', 'OpexGaController::deleteDetail');
    $routes->post('processUpload', 'OpexGaController::processUpload');
    $routes->get('exportExcel', 'OpexGaController::exportExcel');
});

// FOH — Factory Overhead (Phase 2.1)
$routes->group('foh', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Foh::entry');
    $routes->get('entry', 'Foh::entry');
    $routes->get('getEntryData', 'Foh::getEntryData');
    $routes->post('saveBudget', 'Foh::saveBudget');
    $routes->post('submit', 'Foh::submit');
    $routes->get('actual', 'Foh::actual');
    $routes->post('cariActualTable', 'Foh::cariActualTable');
    $routes->get('summary', 'Foh::summary');
    $routes->get('getDetailItems', 'Foh::getDetailItems');
    $routes->post('saveDetail', 'Foh::saveDetail');
    $routes->post('deleteDetail', 'Foh::deleteDetail');
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
});

// OPEX Selling
$routes->group('opex-selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->get('entry-budget', 'OpexSellingController::index');
    $routes->get('actual', 'OpexSellingController::actual');
    $routes->post('entryBudgetDetail', 'OpexSellingController::entryBudgetDetail');
    $routes->post('saveBudget', 'OpexSellingController::saveBudget');
    $routes->post('uploadActual', 'OpexSellingController::uploadActual');
    $routes->get('exportExcel', 'OpexSellingController::exportExcel');
    $routes->get('getDetailItems', 'OpexSellingController::getDetailItems');
    $routes->post('saveDetail', 'OpexSellingController::saveDetail');
    $routes->post('deleteDetail', 'OpexSellingController::deleteDetail');
});
$routes->group('opex_selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->post('entryBudgetDetail', 'OpexSellingController::entryBudgetDetail');
    $routes->post('saveBudget', 'OpexSellingController::saveBudget');
    $routes->post('uploadActual', 'OpexSellingController::uploadActual');
    $routes->get('exportExcel', 'OpexSellingController::exportExcel');
    $routes->get('getDetailItems', 'OpexSellingController::getDetailItems');
    $routes->post('saveDetail', 'OpexSellingController::saveDetail');
    $routes->post('deleteDetail', 'OpexSellingController::deleteDetail');
});

// Sales
$routes->group('sales', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SalesController::index');
    $routes->get('domestic/entry', 'SalesController::entryDomestic');
    $routes->get('export/entry', 'SalesController::entryExport');
    $routes->get('simulation', 'SalesController::simulation');
    $routes->get('setup-target', 'SalesController::setupTarget');
    $routes->post('saveDiscountReclass', 'SalesController::saveDiscountReclass');
    $routes->get('getDiscountReclass', 'SalesController::getDiscountReclassAjax');
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
    $routes->get('get_section_detail', 'PlController::getSectionDetail');
    $routes->post('save_notes', 'PlController::saveNotes');
    $routes->post('save_adjs', 'PlController::saveAdjs');
    $routes->get('export_excel', 'PlController::exportExcel');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
