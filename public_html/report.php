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

$res= $mysql->table("3report")->order(["uID","appPriority","appID"])->get()->getResults();

$spreadsheet = new Spreadsheet();
$styles=[
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ]
];
$lettersHead=[
    "A"=>"appID",
    "B"=>"Дата ввода",
    "C"=>"Фамилия",
    "D"=>"Имя",
    "E"=>"Отчество",
    "F"=>"Дата рождения",
    "G"=>"СНИЛС",
    "H"=>"Пол",
    "I"=>"Льготы",
    "J"=>"Email",
    "K"=>"Гражданство",
    "L"=>"Вид документа",
    "M"=>"Серия документа",
    "N"=>"№ документа",
    "O"=>"Кем выдан",
    "P"=>"Дата выдачи",
    "Q"=>"Место рождения",
    "R"=>"Статус",
    "S"=>"Оригинал документов",
    "T"=>"Вид оригинала",
    "U"=>"Оператор ПК",
    "V"=>"Адрес по прописке",
    "W"=>"Адрес текущего проживания",
    "X"=>"Общежитие",
    "Y"=>"Мобильный",
    "Z"=>"Телефон",
    "AA"=>"Тип населенного пунтка",
    "AB"=>"Офиц. Название учебного заведения",
    "AC"=>"Тип учебного заведения",
    "AD"=>"Страна УЗ",
    "AE"=>"Регион УЗ",
    "AF"=>"Изучаемый язык",
    "AG"=>"Год окончания",
    "AH"=>"Вид документа",
    "AI"=>"Серия",
    "AJ"=>"Номер",
    "AK"=>"Дата выдачи",
    "AL"=>"Средний балл аттестата",
    "AM"=>"Семья",
    "AN"=>"ФИО",
    "AO"=>"Телефон",
    "AP"=>"Документ",
    "AQ"=>"Документы на льготы",
    "AR"=>"Испытания",
    "AS"=>"Дисциплина",
    "AT"=>"Балл",
    "AU"=>"Дата испытания",
    "AV"=>"Условие",
    "AW"=>"Конкурсная группа",
    "AX"=>"Достижения ",
    "AY"=>"Балл достижения",
    "AZ"=>"Приоритет",
    "BA"=>"Статус",
    "BB"=>"Уровень",
    "BC"=>"Форма обучения",
    "BD"=>"Курс",
    "BE"=>"Направление Код",
    "BF"=>"Направление",
    "BG"=>"Профиль",
    "BH"=>"Основание",
    "BI"=>"Номер Л.Д.",
    "BJ"=>"Способ подачи",
    "BK"=>"Подано согласие",
    "BL"=>"№ приказа",
    "BM"=>"Дата приказа",
];
$lettersMax=[
    "A"=>"appID",
    "B"=>"appDate",
    "C"=>"surname",
    "D"=>"name",
    "E"=>"patronymic",
    "F"=>"birthday",
    "G"=>"snils",
    "H"=>"sex",
    "I"=>"benefits",
    "J"=>"email",
    "K"=>"citizenship",
    "L"=>"docType",
    "M"=>"docSerial",
    "N"=>"docNumber",
    "O"=>"docWhoIssued",
    "P"=>"docDateIssued",
    "Q"=>"placeBirths",
    "R"=>"uStatus",
    "S"=>"docOriginal",
    "T"=>"docOriginalType",
    "U"=>"operator",
    "V"=>"address",
    "W"=>"addressActual",
    "X"=>"hostel",
    "Y"=>"mobile",
    "Z"=>"phone",
    "AA"=>"typeNP",
    "AB"=>"edName",
    "AC"=>"edType",
    "AD"=>"edCountry",
    "AE"=>"edRegion",
    "AF"=>"edLang",
    "AG"=>"edFinish",
    "AH"=>"edDocType",
    "AI"=>"edDocSerial",
    "AJ"=>"edDocNumber",
    "AK"=>"edDocDate",
    "AL"=>"edScore",
    "AQ"=>"docBenefits",
    "AV"=>"-",
    "AW"=>"-",
    "AZ"=>"appPriority",
    "BA"=>"appStatus",
    "BB"=>"specLevel",
    "BC"=>"specShape",
    "BD"=>"appCourse",
    "BE"=>"specCode",
    "BF"=>"specName",
    "BG"=>"specProfile",
    "BH"=>"appBasis",
    "BI"=>"numLD",
    "BJ"=>"methodSubmitting",
    "BK"=>"approval",
    "BL"=>"orderNum",
    "BM"=>"orderDate",
];

$baseHead= [
    "A"=>"appID",
    "B"=>"Дата ввода",
    "C"=>"Фамилия",
    "D"=>"Имя",
    "E"=>"Отчество",
    "F"=>"Уровень",
    "G"=>"Форма обучения",
    "H"=>"Приоритет",
    "I"=>"Направление Код",
    "J"=>"Направление",
    "K"=>"Профиль",
    "P"=>"Номер Л.Д.",
];
$baseData= [
    "A"=>"appID",
    "B"=>"appDate",
    "C"=>"surname",
    "D"=>"name",
    "E"=>"patronymic",
    "F"=>"specLevel",
    "G"=>"specShape",
    "H"=>"appPriority",
    "I"=>"specCode",
    "J"=>"specName",
    "K"=>"specProfile",
    "P"=>"numLD",
];
$trialsHeads= [
    "L"=>"Название испытания",
    "M"=>"Дисциплина",
    "N"=>"Балл",
    "O"=>"Дата испытания",
];
$trialsLetters= [
    "L"=>"type",
    "M"=>"discipline",
    "N"=>"score",
    "O"=>"date",
];

$familyHeads= [
    "L"=>"Родство",
    "M"=>"ФИО",
    "N"=>"Телефон",
    "O"=>"Документ",
];
$familyLetters= [
    "L"=>"fType",
    "M"=>"fFIO",
    "N"=>"fPhone",
    "O"=>"fDoc",
];

$achsHeads= [
    "L"=>"Балл достижения",
    "M"=>"Название достижения ",
];
$achsLetters= [
    "L"=>"achName",
    "M"=>"achScore",
];
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle("Заявки");
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle("Испытания");
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle("Семья");
$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle("Достижения");
$rs=3;

$sheet1->getStyle("A:BL")->applyFromArray($styles);
$sheet1->setAutoFilter('A2:BL2');
$sheet2->getStyle("A:BL")->applyFromArray($styles);
$sheet2->setAutoFilter('A2:P2');
$sheet3->getStyle("A:BL")->applyFromArray($styles);
$sheet3->setAutoFilter('A2:O2');
$sheet4->getStyle("A:BL")->applyFromArray($styles);
$sheet4->setAutoFilter('A2:O2');


$hide=[
    "AN",
    "AO",
    "AP",
    "AS",
    "AT",
    "AU",
    "AY",
];

foreach ($hide as $letter){
    $sheet1->getColumnDimension($letter)->setCollapsed(true);
    $sheet1->getColumnDimension($letter)->setVisible(false);
}

foreach ($lettersHead as $letter=>$field) {
    $sheet1->setCellValueExplicit($letter . "1", $field, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet1->getColumnDimension($letter)->setAutoSize(true);
}

foreach (["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P"] as $letter){
    $sheet2->getColumnDimension($letter)->setAutoSize(true);
    $sheet3->getColumnDimension($letter)->setAutoSize(true);
    $sheet4->getColumnDimension($letter)->setAutoSize(true);
}

foreach ($baseHead as $letter=>$field)
    $sheet2->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
foreach ($trialsHeads as $letter=>$field)
    $sheet2->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

foreach ($baseHead as $letter=>$field)
    $sheet3->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
foreach ($familyHeads as $letter=>$field)
    $sheet3->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

foreach ($baseHead as $letter=>$field)
    $sheet4->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
foreach ($achsHeads as $letter=>$field)
    $sheet4->setCellValueExplicit($letter."1",$field,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);


$rcs= (object)[  // row counters
    "app"=>$rs,
    "family"=>$rs,
    "trial"=>$rs,
    "ach"=>$rs,
];
foreach ($res as $app){
    foreach ($lettersMax as $letter=>$field)
        $sheet1->setCellValueExplicit($letter.$rcs->app,$app->{$field}??"",\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

    $rcs->app++;
    if(!empty($app->trials)){
        $app->trials= json_decode($app->trials);
        if(count($app->trials)) {
            $sheet1->setCellValue("AR$rcs->app","=HYPERLINK(\"#Испытания!A$rcs->trial\",\"link\")");

            foreach ($app->trials as $key=>$trial){
                if(empty($trial->type)) continue;
                foreach ($baseData as $letter=>$field){
                    $sheet2->setCellValueExplicit($letter.$rcs->trial,$app->{$field},\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
                foreach ($trialsLetters as $letter=>$field){
                    $sheet2->setCellValueExplicit($letter.$rcs->trial,$trial->{$field},\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
                $rcs->trial++;
            }
        }
    }
    if(!empty($app->family)) {
        $app->family = json_decode($app->family);
        if (count($app->family)) {
            $sheet1->setCellValue("AM$rcs->app","=HYPERLINK(\"#Семья!A$rcs->family\",\"link\")");
            foreach ($app->family as $key => $family) {
                foreach ($baseData as $letter => $field)
                    $sheet3->setCellValueExplicit($letter . $rcs->family, $app->{$field}, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                foreach ($familyLetters as $letter => $field)
                    $sheet3->setCellValueExplicit($letter . $rcs->family, $family->{$field}, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $rcs->family++;
            }
        }
    }

    if(!empty($app->achs)) {
        $app->achs = json_decode($app->achs);
        if (count($app->achs)) {
            $sheet1->setCellValue("AX$rcs->app","=HYPERLINK(\"#Достижения!A$rcs->ach\",\"link\")");
            foreach ($app->achs as $key => $achs) {
                foreach ($baseData as $letter => $field)
                    $sheet4->setCellValueExplicit($letter . $rcs->ach, $app->{$field}, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                foreach ($achsLetters as $letter => $field)
                    $sheet4->setCellValueExplicit($letter . $rcs->ach, $achs->{$field}, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $rcs->ach++;
            }
        }
    }

}
/** SAVE */


$method= "local";

$file= match ($method){
    "web"=>"xls/report.xlsx",
    "local"=>"D:\OSPanel\home\afc.report\public\xls/report.xlsx",
    "host"=>"stats.mgu-mlt.ru/public_html/xls/report.xlsx"
};

if(file_exists($file))
    unlink($file);


$spreadsheet->setActiveSheetIndex(0);
$writer = new Xlsx($spreadsheet);
$writer->save($file);

echo "success";

