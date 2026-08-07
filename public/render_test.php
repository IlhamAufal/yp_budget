<?php
define('FCPATH', __DIR__ . '/');
define('APPPATH', 'C:/xampp/htdocs/yp_budget/app/');
define('SYSTEMPATH', 'C:/xampp/htdocs/yp_budget/vendor/codeigniter4/framework/system/');
define('WRITEPATH', 'C:/xampp/htdocs/yp_budget/writable/');
define('CI_VERSION', '4.0.0');
require SYSTEMPATH . 'Common.php';
require APPPATH . 'Config/Routes.php';
CodeIgniter\Config\Env::preload();
/*************************************************
 * Now simulate data and render the view
 *************************************************/
$workingYear = '2026';
$data = [
  'workingYear' => '2026',
  'domesticProducts' => [],
  'exportProducts' => [],
  'kurs' => ['usd'=>15800, 'baht'=>430, 'ringgit'=>3400],
  'summary' => [],
];
$renderer = \CodeIgniter\Config\Services::renderer(APPPATH . 'Views');
$html = $renderer->setData($data)->render('sales/index', $data, ... );
echo $html;
