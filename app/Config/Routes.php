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
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Working Year Context Route
$routes->post('set-year', 'PeriodController::setYear', ['filter' => 'auth']);
$routes->get('api/active-years', 'PeriodController::getActiveYears');

// Login routes (public)
$routes->get('login', 'LoginController::index');
$routes->post('login/process', 'LoginController::process');
$routes->get('logout', 'LoginController::logout');
$routes->post('auth/changePassword', 'LoginController::changePassword', ['filter' => 'auth']);
$routes->get('auth/change-password-form', 'LoginController::changePasswordForm', ['filter' => 'auth']);

// Protected routes (require auth)
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// Legacy menu links — direct aliases, no redirect.
$routes->get('foh-summary', 'FohController::summary', ['filter' => 'auth']);
$routes->get('mpp-entry', 'MppController::index', ['filter' => 'auth']);
$routes->get('mpp-summary', 'MppController::summary', ['filter' => 'auth']);
$routes->get('configure-period', 'MasterController::configurePeriod', ['filter' => 'auth']);
$routes->get('master/mpp', 'MasterController::salaryMpp', ['filter' => 'auth']);
$routes->get('report-grand-opex', 'OpexReportController::grand', ['filter' => 'auth']);
$routes->match(['GET', 'POST'], 'report-grand-opex/data', 'OpexReportController::grandData', ['filter' => 'auth']);
$routes->get('report-profit-loss', 'PlController::summary', ['filter' => 'auth']);

// Legacy top-level menu links dari gw_sm__menu — alias langsung ke sys-admin.
$routes->get('menu', 'MenuController::index', ['filter' => 'auth']);
$routes->get('role', 'RoleController::index', ['filter' => 'auth']);
$routes->get('user', 'UserController::index', ['filter' => 'auth']);

// Legacy "insert" menu links — form tambah kini berupa modal di halaman index masing-masing.
$routes->get('menu/add-menu', 'MenuController::index', ['filter' => 'auth']);
$routes->get('role/add-role', 'RoleController::index', ['filter' => 'auth']);
$routes->get('user/add-user', 'UserController::index', ['filter' => 'auth']);

// Parent "2. Sales" (menu_link: sales-data)
$routes->get('sales-data', 'SalesController::index', ['filter' => 'auth']);

// Master Data (COA, Cost Center, Departemen, Periode)
$routes->group('master', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MasterController::index');
    $routes->get('coa', 'MasterController::coa');
    $routes->get('coa/export', 'MasterController::coaExport');
    $routes->get('cost-center', 'MasterController::costCenter');
    $routes->get('cost-center/export', 'MasterController::costCenterExport');
    $routes->get('department', 'MasterController::department');
    $routes->get('department/export', 'MasterController::departmentExport');
    $routes->get('product', 'MasterController::product');
    $routes->get('product/export', 'MasterController::productExport');
    $routes->get('salary-mpp', 'MasterController::salaryMpp');
    $routes->get('salary-mpp/export', 'MasterController::salaryMppExport');
    $routes->get('configure-period', 'MasterController::configurePeriod');
    $routes->get('period', 'MasterController::period');

    $routes->post('api/coa/save', 'MasterController::coaSave');
    $routes->post('api/coa/toggle', 'MasterController::coaToggle');
    $routes->post('api/coa/copy-year', 'MasterController::coaCopyYear');

    $routes->post('api/cost-center/save', 'MasterController::costCenterSave');
    $routes->post('api/cost-center/toggle', 'MasterController::costCenterToggle');

    $routes->post('api/department/save', 'MasterController::departmentSave');
    $routes->post('api/department/toggle', 'MasterController::departmentToggle');

    $routes->post('api/product/save', 'MasterController::productSave');
    $routes->post('api/product/toggle', 'MasterController::productToggle');

    $routes->post('api/salary-mpp/save', 'MasterController::salaryMppSave');
    $routes->post('api/salary-mpp/toggle', 'MasterController::salaryMppToggle');
    $routes->get('api/salary-mpp/data', 'MasterController::salaryMppData');

    $routes->post('api/period/save', 'MasterController::periodSave');
    $routes->post('api/period/set-active', 'MasterController::periodSetActive');
    $routes->post('api/period/set-locked', 'MasterController::periodSetLocked');
    $routes->post('api/period/delete', 'MasterController::periodDelete');
});

// System Administration — Phase 1.1 RBAC (Menu, Role, User Management)
$routes->group('sys-admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('menu', 'MenuController::index');
    $routes->post('api/menu/save', 'MenuController::save');
    $routes->post('api/menu/toggle', 'MenuController::toggle');
    $routes->post('api/menu/delete', 'MenuController::delete');

    $routes->get('role', 'RoleController::index');
    $routes->post('api/role/save', 'RoleController::save');
    $routes->post('api/role/toggle', 'RoleController::toggle');
    $routes->post('api/role/delete', 'RoleController::delete');
    $routes->post('api/role/menus', 'RoleController::menus');
    $routes->post('api/role/menus/save', 'RoleController::saveMenus');

    $routes->get('user', 'UserController::index');
    $routes->get('user/form', 'UserController::formModal');
    $routes->post('api/user/save', 'UserController::save');
    $routes->post('api/user/toggle', 'UserController::toggle');
    $routes->post('api/user/delete', 'UserController::delete');
    $routes->post('api/user/reset-password', 'UserController::resetPassword');
    $routes->post('api/user/roles', 'UserController::saveRoles');
});

// -------------------------------------------------------------------
// MODUL TRANSASI (raw files dihubungkan ke skema & route CI4)
// -------------------------------------------------------------------

// Man Power Planning (MPP) — Entry, AJAX Table, Summary, Sync OPEX
$routes->group('mpp', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MppController::index');
    $routes->get('entry', 'MppController::index');
    $routes->get('summary', 'MppController::summary');
    $routes->post('syncToOpex', 'MppController::syncToOpex');

    // AJAX Endpoints untuk Integrasi Frontend-Backend
    $routes->get('getCostCenters', 'MppController::getCostCenters');
    $routes->get('getMppMatrix', 'MppController::getMppMatrix');
    $routes->get('getMppBreakdown', 'MppController::getMppBreakdown');
    $routes->post('saveMppBreakdown', 'MppController::saveMppBreakdown');
    $routes->post('deleteMppPosition', 'MppController::deleteMppPosition');
    $routes->get('getViewData', 'MppController::getViewData');
});

// CAPEX
$routes->group('capex', ['filter' => 'auth'], function ($routes) {
    $routes->get('entry', 'CapexController::entry');
    $routes->get('summary', 'CapexController::summary');

    // AJAX Endpoints
    $routes->post('entry_budget_table', 'CapexController::entryBudgetTable');
    $routes->post('save_capex', 'CapexController::saveCapex');
    $routes->post('sync_to_opex', 'CapexController::syncToOpex');

    // Entry Form AJAX Endpoints
    $routes->get('getEntryData', 'CapexController::getEntryData');
    $routes->post('saveFormCapex', 'CapexController::saveFormCapex');

    // Summary AJAX Endpoints
    $routes->get('getSummaryViewAll', 'CapexController::getSummaryViewAll');
    $routes->get('getSummaryAcquisition', 'CapexController::getSummaryAcquisition');
    $routes->get('getSummaryDepreciation', 'CapexController::getSummaryDepreciation');
    $routes->get('getCostCenters', 'CapexController::getCostCenters');
    $routes->get('exportSummaryExcel', 'CapexController::exportSummaryExcel');
});
$routes->group('capex-summary', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'CapexController::summary');
    $routes->get('getSummaryViewAll', 'CapexController::getSummaryViewAll');
    $routes->get('getSummaryAcquisition', 'CapexController::getSummaryAcquisition');
    $routes->get('getSummaryDepreciation', 'CapexController::getSummaryDepreciation');
    $routes->get('getCostCenters', 'CapexController::getCostCenters');
    $routes->get('exportSummaryExcel', 'CapexController::exportSummaryExcel');
});

// OPEX GA (sidebar pakai dash; beberapa view pakai underscore)
$routes->group('opex-ga', ['filter' => 'auth'], function ($routes) {
    // Index & Entry Budget
    $routes->get('/', 'OpexGaController::index');
    $routes->get('index', 'OpexGaController::index');
    $routes->get('entry', 'OpexGaController::entryBudget');
    $routes->get('entry-budget', 'OpexGaController::entryBudget');
    $routes->get('entry-budget-detail', 'OpexGaController::entryBudgetDetail');

    // AJAX Entry Data
    $routes->get('getEntryData', 'OpexGaController::getEntryData');
    $routes->get('getHeaderAccounts', 'OpexGaController::getHeaderAccounts');
    $routes->get('getDetailMatrix', 'OpexGaController::getDetailMatrix');
    $routes->post('saveBudget', 'OpexGaController::saveBudget');
    $routes->post('saveDetailItems', 'OpexGaController::saveDetailItems');
    $routes->post('submitBudget', 'OpexGaController::submitBudget');

    // Department Report
    $routes->get('report-department', 'OpexGaController::reportDepartment');
    $routes->match(['GET', 'POST'], 'report-data', 'OpexGaController::reportData');

    // Actual Data
    $routes->post('getActualData', 'OpexGaController::getActualData');
    $routes->post('uploadActual', 'OpexGaController::uploadActual');

    // Download / Export
    $routes->get('download-template', 'OpexGaController::downloadTemplate');
    $routes->get('exportExcel', 'OpexGaController::exportExcel');
    // View entry_budget membangun URL opex-ga/entry-budget/exportExcel (tombol Export)
    $routes->get('entry-budget/exportExcel', 'OpexGaController::exportExcel');
    $routes->get('export-template-opex-ga/(:num)', 'OpexGaController::exportTemplateOpexGa/$1');

    // Breakdown (standar 1.5)
    $routes->get('getDetailItems', 'OpexGaController::getDetailItems');
    $routes->post('saveDetail', 'OpexGaController::saveDetail');
    $routes->post('deleteDetail', 'OpexGaController::deleteDetail');
});

// FOH — Factory Overhead (Phase 2.1)
$routes->group('foh', ['filter' => 'auth'], function ($routes) {
    // Entry Budget
    $routes->get('/', 'FohController::entry');
    $routes->get('entry', 'FohController::entry');
    $routes->get('entry-budget', 'FohController::entry');
    $routes->get('entry-budget-detail', 'FohController::entryBudgetDetail');
    $routes->get('getEntryData', 'FohController::getEntryData');
    $routes->get('getConfigPeriod', 'FohController::getConfigPeriod');
    $routes->get('getHeaderAccounts', 'FohController::getHeaderAccounts');
    $routes->get('getDetailMatrix', 'FohController::getDetailMatrix');
    $routes->get('actual-foh', 'FohController::actual');
    $routes->post('saveBudget', 'FohController::saveBudget');
    $routes->post('saveDetailItems', 'FohController::saveDetailItems');
    $routes->post('submit', 'FohController::submit');
    $routes->post('approve', 'FohController::approve');
    $routes->post('reject', 'FohController::reject');
    $routes->get('getStatus', 'FohController::getStatus');

    // Actual
    $routes->get('actual', 'FohController::actual');
    $routes->get('actual-budget', 'FohController::actual');
    $routes->post('cariActualTable', 'FohController::cariActualTable');
    $routes->post('uploadActual', 'FohController::uploadActual');

    // Download / Export
    $routes->get('download-template', 'FohController::downloadTemplate');
    $routes->get('exportExcel', 'FohController::exportExcel');

    // Summary
    $routes->get('summary', 'FohController::summary');
    $routes->get('summary/export', 'FohController::summaryExport');
    $routes->post('summaryCostCenter', 'FohController::summaryCostCenter');
    $routes->post('summaryAccount', 'FohController::summaryAccount');

    // Breakdown (standar 1.5)
    $routes->get('getDetailItems', 'FohController::getDetailItems');
    $routes->post('saveDetail', 'FohController::saveDetail');
    $routes->post('deleteDetail', 'FohController::deleteDetail');
});
// Alias tanpa dash (beberapa view memakai base_url('opexga/...'))
$routes->group('opexga', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexGaController::index');
    $routes->get('exportExcel', 'OpexGaController::exportExcel');
    // upload_modal.php (index.php) submit ke opexga/processUpload
    $routes->post('processUpload', 'OpexGaController::processUpload');
});
$routes->group('opex_ga', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexGaController::index');
    $routes->get('index', 'OpexGaController::index');
    $routes->get('entry', 'OpexGaController::entryBudget');
    $routes->get('entry_budget', 'OpexGaController::entryBudget');
    $routes->get('entry_budget_detail', 'OpexGaController::entryBudgetDetail');
    $routes->get('actual', 'OpexGaController::actual');
    $routes->get('actual_budget', 'OpexGaController::actualBudget');
    $routes->post('upload_actual', 'OpexGaController::uploadActual');
    $routes->get('export_view_data', 'OpexGaController::exportExcel');
});

// OPEX Selling
$routes->group('opex-selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->get('entry-budget', 'OpexSellingController::index');
    $routes->get('entry-budget-detail', 'OpexSellingController::entryBudgetDetail');
    $routes->get('actual', 'OpexSellingController::actual');
    $routes->get('actual-budget', 'OpexSellingController::actualBudget');
    $routes->get('report-department', 'OpexSellingController::reportDepartment');
    $routes->match(['GET', 'POST'], 'report-data', 'OpexSellingController::reportData');

    // AJAX Actual Data
    $routes->post('getActualData', 'OpexSellingController::getActualData');

    // AJAX Entry Data
    $routes->get('getHeaderAccounts', 'OpexSellingController::getHeaderAccounts');
    $routes->get('getEntryDataGrouped', 'OpexSellingController::getEntryDataGrouped');
    $routes->post('cariActualTable', 'OpexSellingController::cariActualTable');
    $routes->get('getViewData', 'OpexSellingController::getViewData');
    $routes->get('getDetailMatrix', 'OpexSellingController::getDetailMatrix');
    $routes->post('saveBudget', 'OpexSellingController::saveBudget');
    $routes->post('saveEntryDetail', 'OpexSellingController::saveEntryDetail');
    $routes->post('saveDetailItems', 'OpexSellingController::saveDetailItems');

    // Actual & Export
    $routes->post('uploadActual', 'OpexSellingController::uploadActual');
    $routes->get('download-template', 'OpexSellingController::downloadTemplate');
    $routes->get('exportActual', 'OpexSellingController::exportActual');
    $routes->get('exportExcel', 'OpexSellingController::exportExcel');

    // Breakdown (standar 1.5)
    $routes->get('getDetailItems', 'OpexSellingController::getDetailItems');
    $routes->post('saveDetail', 'OpexSellingController::saveDetail');
    $routes->post('deleteDetail', 'OpexSellingController::deleteDetail');
});
$routes->group('opex_selling', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'OpexSellingController::index');
    $routes->get('index', 'OpexSellingController::index');
    $routes->get('entry', 'OpexSellingController::index');
    $routes->get('entry_budget', 'OpexSellingController::index');
    $routes->get('entry_budget_detail', 'OpexSellingController::entryBudgetDetail');
    $routes->get('actual_budget', 'OpexSellingController::actual');
    $routes->post('get_actual_data', 'OpexSellingController::getActualData');
    $routes->post('save_entry_detail', 'OpexSellingController::saveEntryDetail');
    $routes->post('upload_actual', 'OpexSellingController::uploadActual');
    $routes->post('upload_actual_process', 'OpexSellingController::uploadActual');
    $routes->get('export_excel', 'OpexSellingController::exportExcel');
    $routes->get('export_actual', 'OpexSellingController::exportActual');
    $routes->get('download_template', 'OpexSellingController::downloadTemplate');
    $routes->post('cari_actual_table', 'OpexSellingController::cariActualTable');
});

// Sales legacy aliases — direct handlers, no redirect.
$routes->get('sales-domestic/entry', 'SalesController::entryDomestic', ['filter' => 'auth']);
$routes->get('sales-export/entry', 'SalesController::entryExport', ['filter' => 'auth']);

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
    $routes->get('export_template_sales/(:segment)', 'SalesController::exportTemplateSales/$1');
    $routes->post('proses_summary_domestic', 'SalesController::prosesSummaryDomestic');

    // --- FE-BE Sync: AJAX Data Fetch ---
    $routes->get('getDomesticEntryData', 'SalesController::getDomesticEntryData');
    $routes->post('cari_domestic_sales', 'SalesController::cariDomesticSales');
    $routes->get('getExportEntryData', 'SalesController::getExportEntryData');
    $routes->get('getRegionalData', 'SalesController::getRegionalData');

    // --- FE-BE Sync: Batch Save ---
    $routes->post('saveDomesticEntry', 'SalesController::saveDomesticEntry');
    $routes->post('saveExportEntry', 'SalesController::saveExportEntry');

    // --- FE-BE Sync: Adjustment Simulation ---
    $routes->post('processAdjustment', 'SalesController::processAdjustment');

    // --- FE-BE Sync: Upload Domestic & Export (structured) ---
    $routes->post('uploadDomestic', 'SalesController::uploadDomestic');
    $routes->post('uploadExport', 'SalesController::uploadExport');

    // --- FE-BE Sync: Export & Summary ---
    $routes->post('proses_summary_export', 'SalesController::prosesSummaryExport');
    $routes->get('exportRegionalExcel', 'SalesController::exportRegionalExcel');
    $routes->get('exportCountryExcel', 'SalesController::exportCountryExcel');
    $routes->get('export_template_export/(:segment)', 'SalesController::exportTemplateExport/$1');
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

