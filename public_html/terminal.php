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
    $output = shell_exec('php terminal.php arg1 arg2');
    echo $output;
}
