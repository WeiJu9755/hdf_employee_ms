<?php
// ----------------------------------------------------
// 【環境設定】PHP 7.4+ 相容
// ----------------------------------------------------
ini_set('display_errors', 0);
ini_set('memory_limit', '512M');          // 提高記憶體限制
ini_set('max_execution_time', 300);       // 最長執行 5 分鐘
date_default_timezone_set("Asia/Taipei");

$site_db = "eshop";
$web_id  = "sales.eshop";

// ----------------------------------------------------
// 載入共用函數與資料庫
// ----------------------------------------------------
@include_once '/website/include/pub_function.php';
@include_once("/website/class/".$site_db."_info_class.php");

// ----------------------------------------------------
// 載入 PHPExcel
// ----------------------------------------------------
require_once '/website/os/PHPExcel-1.8.1/Classes/PHPExcel.php';

// ----------------------------------------------------
// 建立 Excel 物件
// ----------------------------------------------------
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// 文件屬性
$objPHPExcel->getProperties()
    ->setCreator("PowerSales")
    ->setLastModifiedBy("PowerSales")
    ->setTitle("員工資料表")
    ->setSubject("員工資料匯出")
    ->setDescription("自動匯出員工資料 Excel 檔案")
    ->setCategory("報表");

// ----------------------------------------------------
// 標題列
// ----------------------------------------------------
$headers = [
    '員工代號','姓名','身分證字號','性別','血型','出生日期','連絡電話','舊工號',
    '緊急連絡人','緊急連絡人電話','入職日期','離職日期','縣市','區里鄉鎮',
    '郵遞區號','地址','公司代號','職務','團隊代號','主要工地','會員帳號',
    '實際投保公司代號','勞健保','勞退','備註'
];
$sheet->fromArray($headers, NULL, 'A1');

// ----------------------------------------------------
// 取資料 (PHP 7.4 安全寫法)
// ----------------------------------------------------
$mDB = new MywebDB();

$sql = "SELECT * FROM employee ORDER BY employee_id ";
$mDB->query($sql);

$rows = [];
while ($row = $mDB->fetchRow(2)) {
    $rows[] = [
        $row['employee_id'],
        htmlspecialchars_decode($row['employee_name']),
        $row['id_number'],
        $row['gender'],
        $row['blood_type'],
        $row['birthday'],
        $row['mobile_no'],
        $row['old_employee_id'],
        $row['emergency_contact'],
        $row['emergency_mobile_no'],
        $row['start_date'],
        $row['seniority'],
        $row['county'],
        $row['town'],
        $row['zipcode'],
        $row['address'],
        $row['company_id'],
        $row['employee_type'],
        $row['team_id'],
        $row['construction_id'],
        $row['member_no'],
        $row['actual_insurance'],
        $row['labor_health_insurance'],
        $row['labor_Pension'],
        $row['employee_content']
    ];
}
$mDB->remove();

// ----------------------------------------------------
// 一次寫入 Excel（比逐格快 10 倍）
// ----------------------------------------------------
if (!empty($rows)) {
    $sheet->fromArray($rows, NULL, 'A2');
}

// ----------------------------------------------------
// 基本樣式設定
// ----------------------------------------------------
$sheet->getStyle('A1:Y1')->getFont()->setBold(true);
$sheet->getStyle('A1:Y1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->freezePane('A2'); // 鎖定標題列

// 欄寬
$widths = [18,15,15,7,7,15,20,20,20,20,15,15,12,12,10,40,15,15,15,15,15,20,12,12,50];
foreach (range('A','Y') as $index => $col) {
    $sheet->getColumnDimension($col)->setWidth($widths[$index]);
}

// 對齊設定
$alignCols = ['A','B','C','D','E','F','K','L','M','N','O','Q','S','T','U','V','W','X','Y'];
foreach ($alignCols as $col) {
    $sheet->getStyle($col)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}

// ----------------------------------------------------
// 匯出
// ----------------------------------------------------
$sheet->setTitle("員工資料表");
$filename = "員工資料表_" . date("Ymd_His") . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
