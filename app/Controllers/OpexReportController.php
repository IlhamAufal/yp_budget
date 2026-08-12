<?php

namespace App\Controllers;

use App\Models\OpexGaModel;
use App\Models\OpexSellingModel;
use App\Services\OpexReportService;
use CodeIgniter\HTTP\ResponseInterface;

/** Combined OPEX report controller for the legacy report-grand-opex URL. */
class OpexReportController extends BaseController
{
    protected OpexGaModel $gaModel;
    protected OpexSellingModel $sellingModel;
    protected OpexReportService $reportService;

    public function __construct()
    {
        $this->gaModel = new OpexGaModel();
        $this->sellingModel = new OpexSellingModel();
        $this->reportService = new OpexReportService();
    }

    public function grand(): string
    {
        $year = (string) (session()->get('year_code') ?? session()->get('working_year') ?? date('Y'));
        $report = $this->buildGrandReport($year);

        return view('reports/grand_opex', [
            'title'       => 'Grand OPEX Report',
            'workingYear' => $year,
            'report'      => $report,
            'dataUrl'     => base_url('report-grand-opex/data'),
        ]);
    }

    public function grandData(): ResponseInterface
    {
        $year = (string) ($this->request->getVar('year')
            ?? session()->get('year_code')
            ?? session()->get('working_year')
            ?? date('Y'));

        return $this->response->setJSON([
            'status' => 'success',
            'year'   => $year,
            'report' => $this->buildGrandReport($year),
        ]);
    }

    private function buildGrandReport(string $year): array
    {
        $authObj = (array) session()->get('auth_obj');
        $isAdmin = (bool) session()->get('is_admin');
        $gaScope = $isAdmin ? ['*'] : $authObj;
        $gaRows = [];

        foreach ($this->gaModel->getCostCenters($year, $gaScope) as $costCenter) {
            $internal = trim((string) ($costCenter['cost_center'] ?? ''));
            $sap = trim((string) ($costCenter['cost_center_sap'] ?? $costCenter['cc_code'] ?? ''));
            $resolved = $this->gaModel->resolveEligibleDept($internal !== '' ? $internal : $sap, $year, $gaScope);
            if ($resolved === null) {
                continue;
            }
            $entry = $this->gaModel->getEntryData($year, $resolved);
            $gaRows = array_merge($gaRows, (array) ($entry['rows'] ?? []));
        }

        $gaReport = $this->reportService->normalizeRows($gaRows);
        $sellingReport = $this->reportService->normalizeRows(
            $this->sellingModel->getReportRows($year, '0', $authObj, $isAdmin)
        );

        return $this->reportService->combine([
            'OPEX GA'      => $gaReport,
            'OPEX SELLING' => $sellingReport,
        ]);
    }
}
