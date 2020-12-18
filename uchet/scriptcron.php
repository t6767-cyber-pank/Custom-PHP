<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL);
include("mysql_connect.php");
$dt=date("r");
mysql_query("insert into cron_test(date) values('$dt')");
/*
$q=mysql_query("select * from cron_test");
while ($xfact= mysql_fetch_array($q))
{
	echo $xfact['date']."<br>";
}
*/
?>