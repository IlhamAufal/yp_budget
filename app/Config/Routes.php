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

$routes->group('mpp', ['filter' => ['auth', 'context']], function($routes) {
    // 7.1 Entry MPP
    $routes->get('entry', 'NewHeadcountController::entry');
    $routes->post('save-entry', 'NewHeadcountController::saveEntry');
    
    // Summary Headcount
    $routes->get('summary', 'NewHeadcountController::summary');
    $routes->post('process-opex', 'NewHeadcountController::processToOpex');
});

$routes->group('capex', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('entry', 'Capex::entry');
    $routes->get('report', 'Capex::report');
    $routes->get('summary', 'Capex::summary');
    $routes->get('pdf_reader', 'Capex::pdfReader');
    
    // AJAX Endpoints
    $routes->post('entry_budget_table', 'Capex::entryBudgetTable');
    $routes->post('save_capex', 'Capex::saveCapex');
});

$routes->group('mpp', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Mpp::index');
    $routes->get('getEntryTable', 'Mpp::getEntryTable');
    $routes->post('saveBudget', 'Mpp::saveBudget');
    $routes->get('summary', 'Mpp::summary');
    $routes->post('syncToOpex', 'Mpp::syncToOpex');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
