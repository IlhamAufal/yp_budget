<?php

namespace Config;

/**
 * Compatibility metadata for legacy aliases and utility routes.
 *
 * Runtime permission decisions are made by AuthorizationService against
 * gw_sm__menu.menu_link. This class is intentionally not an access source of
 * truth; canonicalFor remains available only to older callers and tests.
 */
class Authorization
{
    /**
     * @return array<string, string|null>
     */
    public static function policies(): array
    {
        $policies = [];

        $add = static function (array $methods, string $canonical, array $paths) use (&$policies): void {
            foreach ($paths as $path) {
                foreach ($methods as $method) {
                    $policies[strtoupper($method) . ' ' . trim($path, '/')] = $canonical;
                }
            }
        };
        $utility = static function (array $methods, array $paths) use (&$policies): void {
            foreach ($paths as $path) {
                foreach ($methods as $method) {
                    $policies[strtoupper($method) . ' ' . trim($path, '/')] = null;
                }
            }
        };

        $add(['GET'], 'dashboard', ['', 'dashboard']);
        $add(['GET'], 'foh/summary', ['foh-summary']);
        $add(['GET'], 'mpp/entry', ['mpp-entry']);
        $add(['GET'], 'mpp/summary', ['mpp-summary']);
        $add(['GET'], 'master/configure-period', ['configure-period']);
        $add(['GET'], 'master/salary-mpp', ['master/mpp']);
        $add(['GET'], 'opex-ga/report-department', ['opex-ga/report-department']);
        $add(['GET', 'POST'], 'opex-ga/report-department', ['opex-ga/report-data']);
        $add(['GET'], 'opex-selling/report-department', ['opex-selling/report-department']);
        $add(['GET', 'POST'], 'opex-selling/report-department', ['opex-selling/report-data']);
        $add(['GET'], 'report-grand-opex', ['report-grand-opex']);
        $add(['GET'], 'pl', ['report-profit-loss']);
        $add(['GET', 'POST'], 'report-grand-opex', ['report-grand-opex/data']);
        $utility(['GET'], ['auth/change-password-form']);
        $utility(['POST'], ['auth/changePassword']);

        $add(['GET'], 'master/coa', ['master', 'master/', 'master/coa', 'master/coa/export']);
        $add(['GET'], 'master/cost-center', ['master/cost-center', 'master/cost-center/export']);
        $add(['GET'], 'master/department', ['master/department', 'master/department/export']);
        $add(['GET'], 'master/product', ['master/product', 'master/product/export']);
        $add(['GET'], 'master/salary-mpp', ['master/salary-mpp', 'master/salary-mpp/export', 'master/api/salary-mpp/data']);
        $add(['GET'], 'master/configure-period', ['master/configure-period', 'master/period']);
        $add(['POST'], 'master/coa', ['master/api/coa/save', 'master/api/coa/toggle', 'master/api/coa/copy-year']);
        $add(['POST'], 'master/cost-center', ['master/api/cost-center/save', 'master/api/cost-center/toggle']);
        $add(['POST'], 'master/department', ['master/api/department/save', 'master/api/department/toggle']);
        $add(['POST'], 'master/product', ['master/api/product/save', 'master/api/product/toggle']);
        $add(['POST'], 'master/salary-mpp', ['master/api/salary-mpp/save', 'master/api/salary-mpp/toggle']);
        $add(['POST'], 'master/configure-period', ['master/api/period/save', 'master/api/period/set-active', 'master/api/period/set-locked', 'master/api/period/delete']);

        $add(['GET'], 'sys-admin/menu', ['sys-admin/menu']);
        $add(['POST'], 'sys-admin/menu', ['sys-admin/api/menu/save', 'sys-admin/api/menu/toggle', 'sys-admin/api/menu/delete']);
        $add(['GET'], 'sys-admin/role', ['sys-admin/role']);
        $add(['POST'], 'sys-admin/role', ['sys-admin/api/role/save', 'sys-admin/api/role/toggle', 'sys-admin/api/role/delete', 'sys-admin/api/role/menus', 'sys-admin/api/role/menus/save']);
        $add(['GET'], 'sys-admin/user', ['sys-admin/user', 'sys-admin/user/form']);
        $add(['POST'], 'sys-admin/user', ['sys-admin/api/user/save', 'sys-admin/api/user/toggle', 'sys-admin/api/user/delete', 'sys-admin/api/user/reset-password', 'sys-admin/api/user/roles']);

        $add(['GET'], 'mpp/entry', ['mpp', 'mpp/', 'mpp/entry', 'mpp/getCostCenters', 'mpp/getMppMatrix', 'mpp/getMppBreakdown', 'mpp/getViewData']);
        $add(['GET'], 'mpp/summary', ['mpp/summary']);
        $add(['POST'], 'mpp/entry', ['mpp/syncToOpex', 'mpp/saveMppBreakdown', 'mpp/deleteMppPosition']);

        $add(['GET'], 'capex/entry', ['capex/entry', 'capex/getEntryData']);
        $add(['POST'], 'capex/entry', ['capex/entry_budget_table', 'capex/save_capex', 'capex/sync_to_opex', 'capex/saveFormCapex']);
        $add(['GET'], 'capex/summary', ['capex/summary', 'capex/getSummaryViewAll', 'capex/getSummaryAcquisition', 'capex/getSummaryDepreciation', 'capex/getCostCenters', 'capex/exportSummaryExcel']);
        $add(['GET'], 'capex/summary', ['capex-summary', 'capex-summary/', 'capex-summary/getSummaryViewAll', 'capex-summary/getSummaryAcquisition', 'capex-summary/getSummaryDepreciation', 'capex-summary/getCostCenters', 'capex-summary/exportSummaryExcel']);

        $add(['GET'], 'opex-ga/entry', ['opex-ga', 'opex-ga/', 'opex-ga/index', 'opex-ga/entry', 'opex-ga/entry-budget', 'opex-ga/entry-budget-detail', 'opex-ga/getEntryData', 'opex-ga/getHeaderAccounts', 'opex-ga/getDetailMatrix', 'opex-ga/download-template', 'opex-ga/entry-budget/exportExcel', 'opex-ga/getDetailItems']);
        $add(['POST'], 'opex-ga/entry', ['opex-ga/saveBudget', 'opex-ga/saveDetailItems', 'opex-ga/submitBudget', 'opex-ga/saveDetail', 'opex-ga/deleteDetail']);
        $add(['GET'], 'opex-ga/actual', ['opex-ga/actual', 'opex-ga/actual-budget', 'opex-ga/exportExcel', 'opex-ga/export-template-opex-ga/*']);
        $add(['POST'], 'opex-ga/actual', ['opex-ga/getActualData', 'opex-ga/uploadActual']);
        $add(['GET'], 'opex-ga/entry', ['opexga', 'opexga/', 'opexga/exportExcel']);
        $add(['POST'], 'opex-ga/entry', ['opexga/processUpload']);
        $add(['GET'], 'opex-ga/entry', ['opex_ga', 'opex_ga/', 'opex_ga/index', 'opex_ga/entry', 'opex_ga/entry_budget', 'opex_ga/entry_budget_detail', 'opex_ga/export_view_data']);
        $add(['GET'], 'opex-ga/actual', ['opex_ga/actual', 'opex_ga/actual_budget']);
        $add(['POST'], 'opex-ga/actual', ['opex_ga/upload_actual']);

        $add(['GET'], 'foh/entry', ['foh', 'foh/', 'foh/entry', 'foh/entry-budget', 'foh/entry-budget-detail', 'foh/getEntryData', 'foh/getConfigPeriod', 'foh/getHeaderAccounts', 'foh/getDetailMatrix', 'foh/getStatus', 'foh/getDetailItems']);
        $add(['POST'], 'foh/entry', ['foh/saveBudget', 'foh/saveDetailItems', 'foh/submit', 'foh/approve', 'foh/reject', 'foh/saveDetail', 'foh/deleteDetail']);
        $add(['GET'], 'foh/actual', ['foh/actual', 'foh/actual-budget', 'foh/download-template', 'foh/exportExcel']);
        $add(['POST'], 'foh/actual', ['foh/cariActualTable', 'foh/uploadActual']);
        $add(['GET'], 'foh/summary', ['foh/summary', 'foh/summary/export']);
        $add(['POST'], 'foh/summary', ['foh/summaryCostCenter', 'foh/summaryAccount']);

        $add(['GET'], 'opex-selling/entry', ['opex-selling', 'opex-selling/', 'opex-selling/entry', 'opex-selling/entry-budget', 'opex-selling/entry-budget-detail', 'opex-selling/getHeaderAccounts', 'opex-selling/getEntryDataGrouped', 'opex-selling/getViewData', 'opex-selling/getDetailMatrix', 'opex-selling/getDetailItems']);
        $add(['POST'], 'opex-selling/entry', ['opex-selling/saveBudget', 'opex-selling/saveEntryDetail', 'opex-selling/saveDetailItems', 'opex-selling/saveDetail', 'opex-selling/deleteDetail']);
        $add(['GET'], 'opex-selling/actual', ['opex-selling/actual', 'opex-selling/actual-budget', 'opex-selling/download-template', 'opex-selling/exportActual', 'opex-selling/exportExcel']);
        $add(['POST'], 'opex-selling/actual', ['opex-selling/getActualData', 'opex-selling/cariActualTable', 'opex-selling/uploadActual']);
        $add(['GET'], 'opex-selling/entry', ['opex_selling', 'opex_selling/', 'opex_selling/index', 'opex_selling/entry', 'opex_selling/entry_budget', 'opex_selling/entry_budget_detail', 'opex_selling/export_excel', 'opex_selling/export_actual', 'opex_selling/download_template']);
        $add(['GET'], 'opex-selling/actual', ['opex_selling/actual_budget']);
        $add(['POST'], 'opex-selling/entry', ['opex_selling/get_actual_data', 'opex_selling/save_entry_detail', 'opex_selling/cari_actual_table']);
        $add(['POST'], 'opex-selling/actual', ['opex_selling/upload_actual', 'opex_selling/upload_actual_process']);

        $add(['GET'], 'sales/domestic/entry', ['sales', 'sales/', 'sales/domestic/entry', 'sales-domestic/entry', 'sales/simulation', 'sales/setup-target', 'sales/getDomesticEntryData', 'sales/getDiscountReclass', 'sales/getRegionalData', 'sales/exportRegionalExcel', 'sales/exportCountryExcel', 'sales/export_template_sales/*', 'sales/export_template_export/*']);
        $add(['GET'], 'sales/export/entry', ['sales/export/entry', 'sales-export/entry', 'sales/getExportEntryData']);
        $add(['POST'], 'sales/domestic/entry', ['sales/saveDiscountReclass', 'sales/processUpload', 'sales/proses_summary_domestic', 'sales/cari_domestic_sales', 'sales/saveDomesticEntry', 'sales/processAdjustment', 'sales/uploadDomestic']);
        $add(['POST'], 'sales/export/entry', ['sales/saveExportEntry', 'sales/uploadExport', 'sales/proses_summary_export']);

        $add(['GET'], 'monitoring', ['monitoring', 'monitoring/', 'monitoring/cari_view_data']);
        $add(['GET'], 'pl', ['pl', 'pl/', 'pl/get_detail_account', 'pl/get_section_detail', 'pl/export_excel']);
        $add(['POST'], 'pl', ['pl/save_notes', 'pl/save_adjs']);

        return $policies;
    }

    /**
     * @return string|null|false null=authenticated utility, false=unknown
     */
    public static function canonicalFor(string $method, string $uri)
    {
        $method = strtoupper($method);
        $uri = trim($uri, '/');
        $policies = self::policies();
        $key = $method . ' ' . $uri;
        if (array_key_exists($key, $policies)) {
            return self::normalizeLink($policies[$key]);
        }

        foreach ($policies as $pattern => $canonical) {
            [$policyMethod, $policyPath] = array_pad(explode(' ', $pattern, 2), 2, '');
            if ($policyMethod !== $method || ! str_ends_with($policyPath, '/*')) {
                continue;
            }
            $prefix = rtrim(substr($policyPath, 0, -1), '/');
            if (str_starts_with($uri, $prefix)) {
                return self::normalizeLink($canonical);
            }
        }

        return false;
    }

    public static function normalizeLink(?string $link): ?string
    {
        if ($link === null) {
            return null;
        }
        $link = trim($link, '/');
        if ($link === '') {
            return 'dashboard';
        }
        if ($link === '#') {
            return '#';
        }

        $aliases = [
            'opex-ga' => 'opex-ga/entry',
            'opex-ga/index' => 'opex-ga/entry',
            'opex-ga/entry-budget' => 'opex-ga/entry',
            'opex-ga/entry-budget-detail' => 'opex-ga/entry',
            'opex-ga/actual-budget' => 'opex-ga/actual',
            'opexga' => 'opex-ga/entry',
            'opex_ga' => 'opex-ga/entry',
            'opex_ga/index' => 'opex-ga/entry',
            'opex_ga/entry' => 'opex-ga/entry',
            'opex_ga/entry_budget' => 'opex-ga/entry',
            'opex_ga/entry_budget_detail' => 'opex-ga/entry',
            'opex_ga/actual' => 'opex-ga/actual',
            'opex_ga/actual_budget' => 'opex-ga/actual',
            'opex-selling' => 'opex-selling/entry',
            'opex-selling/entry-budget' => 'opex-selling/entry',
            'opex-selling/entry-budget-detail' => 'opex-selling/entry',
            'opex-selling/actual' => 'opex-selling/actual',
            'opex_selling' => 'opex-selling/entry',
            'opex_selling/index' => 'opex-selling/entry',
            'opex_selling/entry' => 'opex-selling/entry',
            'opex_selling/entry_budget' => 'opex-selling/entry',
            'opex_selling/entry_budget_detail' => 'opex-selling/entry',
            'opex_selling/actual_budget' => 'opex-selling/actual',
            'foh' => 'foh/entry',
            'foh/entry-budget' => 'foh/entry',
            'foh/entry-budget-detail' => 'foh/entry',
            'capex-summary' => 'capex/summary',
            'report-profit-loss' => 'pl',
            'sales-domestic/entry' => 'sales/domestic/entry',
            'sales-export/entry' => 'sales/export/entry',
        ];
        if (isset($aliases[$link])) {
            return $aliases[$link];
        }
        foreach (['opexga/' => 'opex-ga/', 'opex_ga/' => 'opex-ga/', 'opex_selling/' => 'opex-selling/', 'capex-summary/' => 'capex/summary/'] as $alias => $canonical) {
            if (str_starts_with($link, $alias)) {
                return $canonical . substr($link, strlen($alias));
            }
        }
        if ($link === 'opexga' || $link === 'opex_ga') {
            return 'opex-ga/entry';
        }
        if ($link === 'opex_selling') {
            return 'opex-selling/entry';
        }
        if ($link === 'capex-summary') {
            return 'capex/summary';
        }

        return $link;
    }
}
