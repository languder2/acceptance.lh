<?php
if(empty($_GET['xls'])) die();
$xls= $_GET['xls'];
header("Content-disposition: attachment;filename=$xls");
readfile("xls/$xls");