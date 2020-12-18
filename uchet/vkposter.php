<?php 
error_reporting(E_ALL);
include("mysql_connect.php");
if (isset($_GET['vkidcity'])) {
$id_mcity=$_GET['id_mcity'];
$data=$_GET['datx'];
$outcome=$_GET['outcome'];
$vkidcity=$_GET['vkidcity'];
$q=mysql_query("select * from cityimportvk where vkidcity=$vkidcity and data='$data'");
$xfact= mysql_fetch_array($q);
if ((int)$xfact['id_mcity']>0) {

    if ($xfact['outcome']!=$outcome) {
        mysql_query("update cityimportvk set outcome=$outcome where vkidcity=$vkidcity and data='$data'");
    }
}
else
{ mysql_query("insert into cityimportvk(id_mcity, data, outcome, vkidcity) values($id_mcity, '$data', $outcome, $vkidcity)"); }
    echo "$id_mcity $data $outcome ttt<br>";
exit;
}
?>