<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
use src\report;
$report= new report();

echo "<pre>";
$table=$_GET['t']??false;
if(!$table) die("not table");
$res= $report->mssql->table($table)->get();
if($res->numRows()):
    $list= $res->getResults();

    foreach ($list as $spec){
        $spec= $report->convert($spec);
        print_r($spec);
    }

endif;