<?php
die();
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$DOCUMENT_ROOT/mysql_connect.php");

$start_date = "03.07.2017";
$end_date = "26.03.2018";

$start_date = strtotime($start_date);
$end_date = strtotime($end_date);

while ($start_date <= $end_date) {
	$dt = date('Y-m-d',$start_date);
	$start_date = $start_date + 60*60*24*7;
	$insert_query = "INSERT INTO `salonkrasoty`.`costs`(`name`,`summ`,`type`,`dt`,`parentId`,`isDeleted`)VALUES('Общий расход',330000,1,'$dt',0,0);";
	mysql_query($insert_query);
	$id_cost = mysql_insert_id();

	$update_query = "UPDATE `salonkrasoty`.`costs` SET `parentId` = $id_cost WHERE `id` = $id_cost;";
	mysql_query($update_query);	
}
?>