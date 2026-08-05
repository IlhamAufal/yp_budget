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

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
