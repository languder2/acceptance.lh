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

$results= [];
$res= $mysql->query("SELECT CONCAT(surname,' ',name,' ',patronymic) as fio, MIN(uID) as min, MAX(uID) as max FROM 3report GROUP BY CONCAT(surname,' ',name,' ',patronymic)")->get()->getResults();
foreach ($res as $rec)
    if($rec->min !== $rec->max)
        $results[$rec->fio]=[];
?>
<div class="d-grid">
    <div class="title">
        ФИО
    </div>
    <div class="title">
        Код абитуриента
    </div>
    <div class="title">
        Кол-во заявок
    </div>
    <?php
    foreach ($results as $fio=>$arr):
        $res= $mysql->query("SELECT uID,COUNT(appID) AS cnt FROM 3report WHERE CONCAT(surname,' ',name,' ',patronymic)='$fio' GROUP BY uID")->get()->getResults();
        foreach ($res as $rec):
            ?>
            <div>
                <?=$fio?>
            </div>
            <div>
                <?=$rec->uID?>
            </div>
            <div>
                <?=$rec->cnt?>
            </div>
        <?php endforeach;?>
    <?php endforeach;?>
</div>




<style>
    .d-grid{
        display: grid;
        width: 600px;
        grid-template-columns: 1fr 150px 120px;
        gap: 5px;
        text-align: center;
    }
    .d-grid>div:nth-child(3n+1){
        text-align: left;
    }
    .d-grid .title{
        font-weight: bold;
    }
    .d-grid>div:not(.title):nth-child(6n+1),
    .d-grid>div:not(.title):nth-child(6n+2),
    .d-grid>div:not(.title):nth-child(6n+3){
        background: #f5f5f5;
    }


</style>
