<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
/**Общие подключения к классу для работы с бд**/
chdir(dirname(__FILE__));
include("../timurnf/class/mysqlwork.php");
/*Общие подключения к классу для работы с бд*/

/**Создаем клас работы с cityImportVK**/
$mweek=new master_week();
$mweek->updateOutcomeVk();
?>