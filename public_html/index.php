<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
use src\report;
$report= new report();
session_start();
$num= $_GET['num']??0;
if(!$num){
    $report->mysql->table("3report")->truncate();
    $_SESSION['start'] = time();
}

$sql= [];
$app= $report->getApp($sql,"object",$num);
if($app){
    $report->getOrderDate($sql['orderDate'],$report->app->{"Приказ"});
    $report->getAppStatus($sql['appStatus'],$report->app->{"Статус"});
    $report->getSubmittingMethod($sql['methodSubmitting'],$report->app->{"КодСпособаПодачи"});
    /* APP END */
    $report->getUser($sql);
    $report->getFamily($sql);
    $report->getTrials($sql);
    $report->getAchivements($sql);
    $report->getSpec($sql);

    echo "<pre>";
    print_r($sql['appID']);
    $report->mysql->table("3report")->insert($sql);
    header("Refresh:0.01; ?num=".$sql['appID']);
}
?>
<hr>
start:&#9;<?php echo date("H:i:s",$_SESSION['start'])?><br>
end:&#9;<?php echo date("H:i:s")?>




<?php














/*
die();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment,Color,Font};
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$styles=[
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
        "indent"=> 1,
    ],
    "font"=> [
        "bold"=>true,
        "color"=>[
            "argb"=> "FF00FF",
        ]
    ],
];

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setCellValue('A1', 'Hello World !');
$activeWorksheet->setCellValue('B2', 'что за куйня про пустые строки');
$activeWorksheet->setCellValue('C2', 'просто тестовый текст');
$activeWorksheet->getStyle("A:C")->applyFromArray($styles);
$activeWorksheet->getColumnDimension('B')->setAutoSize(true);
$activeWorksheet->getColumnDimension('C')->setAutoSize(true);
$activeWorksheet->getColumnDimension("A")->setWidth(300, 'pt');
$activeWorksheet->getColumnDimension("A")->setWidth(100, 'pt');
$activeWorksheet->getStyle('A')->getFont()->getColor()->setARGB("33FF55");
$writer = new Xlsx($spreadsheet);
$writer->save('results/test.xlsx');

*/