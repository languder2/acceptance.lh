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
$sheet1->getStyle("A:Q")->applyFromArray($styles);
$sheet1->setAutoFilter('A2:Q2');

$heads= [
    "A"=>"ЛД",
    "B"=>"ФИО",
    "C"=>"Дата",
    "D"=>"Метод",
    "E"=>"E-mail",
    "F"=>"Телефон",
    "G"=>"Уровень",
    "H"=>"Форма",
    "I"=>"Приоритет",
    "J"=>"Тип",
    "K"=>"Дисциплина",
    "L"=>"Балл",
    "M"=>"Направление",
    "Q"=>"Номер заявки",
    "R"=>"Номер абит.",
    "S"=>"Фамилия",
    "T"=>"Имя",
    "U"=>"Отчество",
    "V"=>"ОКСО",
];

foreach ($heads as $letter=>$field) {
    $sheet1->setCellValueExplicit($letter . "1", $field, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet1->getColumnDimension($letter)->setAutoSize(true);
}
$sheet1->getColumnDimension("I")->setAutoSize(false);
$sheet1->getColumnDimension("I")->setWidth(70);

$fieldsApp= [
    "A"=>"numLD",
    "B"=>[
        "separator"=>" ",
        "fields"=>["surname","name","patronymic"]
    ],
    "C"=>"appDate",
    "D"=>"methodSubmitting",
    "E"=>"email",
    "F"=>"phone",
    "H"=>"specShape",
    "I"=>"appPriority",
    "M"=>[
        "separator"=>" | ",
        "fields"=>["specName","specProfile"]
    ],
    "Q"=>"appID",
    "R"=>"uID",
    "S"=>"surname",
    "T"=>"name",
    "U"=>"patronymic",
    "V"=>"specCode",
];
$fieldsTrial= [
    "J"=>"type",
    "K"=>"discipline",
    "L"=>"score",
];

$groupLetter= "N";

$replaces= (object)[
    "type"=>[
        "Результаты вступительного испытания"=>"Исп",
        "Результаты ЕГЭ"=>"ЕГЭ",
    ],
    "specShape"=>[
        "Заочная форма"=>"Заоч",
        "Очная форма"=>"Очн",
        "Очно-заочная форма"=>"Заоч"
    ],
    "specLevel"=>[
        "ВПО-Специалисты"=>"Спец",
        "ВПО-Бакалавры"=>"Бак",
        "ВПО-Магистры"=>"Маг",
        "Аспирантура"=>"Асп",
        "Докторантура"=>"Док",
        "НПО (лицей, гимназия)"=>"НПО",
        "СПО-базовый уровень"=>"СПО-Б",
        "СПО-повышенный уровень"=>"СПО-П",
    ]
];

$rcs= 3;
$sections= 0;
$check=[];
foreach ($trials as $spec){
    $sections++;
    foreach ($spec as $uid)
        foreach ($uid as $app):
            if(!isset($check[$app->uID]))
                $check[$app->uID]= ["name"=>"$app->surname $app->name $app->patronymic"];
            $app->trials= json_decode($app->trials);
            foreach ($app->trials as $trial):
                if(empty($trial->type)) continue;
                if(in_array($trial->discipline,$check[$app->uID])) continue;
                $check[$app->uID][]= $trial->discipline;
                foreach ($fieldsTrial as $letter=>$field)
                    $sheet1->setCellValueExplicit(
                        $letter.$rcs,
                        empty($replaces->{$field}[$trial->{$field}])?$trial->{$field}??"":$replaces->{$field}[$trial->{$field}],
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );

                $sheet1->setCellValueExplicit(
                    $groupLetter.$rcs,
                    $sections,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );

                foreach ($fieldsApp as $letter=>$field):
                    $value= "";
                    $tmp= [];
                    if(is_array($field)) {
                        foreach ($field['fields'] as $fl)
                            $tmp[]= $app->{$fl}??"";
                        $value= implode($field['separator'], $tmp);
                    }
                    else
                        $value= empty($replaces->{$field}[$app->{$field}])?$app->{$field}??"":$replaces->{$field}[$app->{$field}];
                    $sheet1->setCellValueExplicit($letter.$rcs,$value,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                endforeach;
                $rcs++;
            endforeach;
        endforeach;
}
$writer = new Xlsx($spreadsheet);
$writer->save("xls/trials.xlsx");
echo "success";
//echo "<a href='/download.php?xls=trials.xlsx' target='_blank'>Скачать файл испытаний.</a>";
