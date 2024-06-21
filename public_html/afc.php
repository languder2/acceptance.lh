<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
use src\report;
$report= new report();

$methodSubmitting['title']=[
    "ЕПГУ"=>"Гос услуги",
    "Лично"=>"Лично",
    "Личный кабинет"=>"Веб",
];
$res= $report->mysql->table("3report")->select("methodSubmitting,COUNT(id) as cnt")->where("methodSubmitting IS NOT NULL")->group("methodSubmitting")->get();
$methodSubmitting['results']= $res->getResults();

echo "<pre>";
print_r($methodSubmitting);