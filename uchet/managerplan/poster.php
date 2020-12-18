<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
$managerMark=new managerMark();
$managerMark->set_dt($_POST['dt']);
echo $_POST['idcity']." ".$_POST['color']."  ".$managerMark->selectMark($_POST['idcity']);
$managerMark->saveMark($_POST['idcity'], $_POST['color']);
?>