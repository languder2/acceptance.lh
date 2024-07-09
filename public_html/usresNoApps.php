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


$users= $mysql->table("users")->where(["apps"=>0])->order(["surname","name"])->get()->getResults();




?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
<div class="container-lg">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">
                    Всего: <?=count($users)?>
                </th>
                <th scope="col">ФИО</th>
                <th scope="col">E-mail</th>
                <th scope="col">Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($users)) foreach ($users as $user):?>
                <tr>
                    <td>
                        <?=$user->uid?>
                    </td>
                    <td>
                        <?=$user->surname?>
                        <?=$user->name?>
                        <?=$user->patronymic?>
                    </td>
                    <td>
                        <?=$user->email?>
                    </td>
                    <td>
                        <?=$user->phone?>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
</body>
</html>
