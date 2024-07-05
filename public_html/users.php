<?php
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});
use src\report;
$report= new report();
$report->mysql->table("users")->truncate();
$users= $report->mssql->table("Все_Абитуриенты")->select(["ID","Мобильный","E_Mail","Фамилия","Имя","Отчество"])->get()->getResults();
echo "<pre>";
foreach ($users as $user) {
    $user= (array)$report->convert($user);
    $sql= [
        "uid"=>$user["ID"],
        "surname"=>$user["Фамилия"],
        "name"=>$user["Имя"],
        "patronymic"=>$user["Отчество"],
        "email"=>$user["E_Mail"],
        "phone"=>$user["Мобильный"],
    ];
    $report->mysql->table("users")->insert($sql);
    echo $user["ID"];
    echo "<hr>";
}
