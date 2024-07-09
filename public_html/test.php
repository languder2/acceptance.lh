<?php
header('Content-type: text/html; charset=utf8');
require_once "src/config.php";
require_once "src/mysql.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment};
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use src\config;
use src\mysql;
echo date("H:i:s") . "\n";

$config= new config();
$mysql= new mysql($config->mysql);
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();

$trials= [];
$res= $mysql->query("SELECT CONCAT(specName,' | ',specProfile) as spec FROM 3report WHERE trials IS NOT NULL AND CONCAT(specName,' | ',specProfile) !='' GROUP BY CONCAT(specName,' | ',specProfile)")->get()->getResults();
foreach ($res as $rec)
    $trials[$rec->spec] = [];

foreach ($trials as $spec=>$trial){
    $query= "SELECT * FROM 3report WHERE trials IS NOT NULL AND CONCAT(specName,' | ',specProfile) = '$spec' AND appPriority=1 ORDER BY surname,name,patronymic ";
    $res= $mysql->query($query)->get()->getResults();
    foreach ($res as $rec){
        $trials[$spec][$rec->uID][]= $rec;
        $query2= "SELECT * FROM 3report WHERE trials IS NOT NULL AND uID=$rec->uID AND appPriority!=1 ORDER BY appPriority";
        $res2= $mysql->query($query2)->get()->getResults();
        foreach ($res2 as $rec2)
            $trials[$spec][$rec->uID][]= $rec2;
    }
}



//$res= $mysql->table("3report")->where("trials IS NOT NULL")->order(["uID","appPriority","appID"])->get()->getResults();

$spreadsheet = new Spreadsheet();
$styles=[
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ]
];

$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle("Испытания");
$sheet1->getStyle("A:V")->applyFromArray($styles);
$sheet1->setAutoFilter('A2:V2');

$method= "web";

$file= match ($method){
    "web"=>"xls/test.xlsx",
};

if(file_exists($file))
    unset($file);

$writer = new Xlsx($spreadsheet);
$writer->save($file);
echo date("H:i:s") . "\n";
echo "success";
//echo "<a href='/download.php?xls=trials.xlsx' target='_blank'>Скачать файл испытаний.</a>";
