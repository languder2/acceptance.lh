<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
use src\report;
$report= new report();

$list= $report->mssql->table("СпециальностиПрофили")->get()->getResults();

echo "<pre>";
foreach ($list as $spec){
    $spec= $report->convert($spec);
    print_r($spec);

}