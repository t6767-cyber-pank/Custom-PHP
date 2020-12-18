<?
$id = intval($_GET['id']);
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$DOCUMENT_ROOT/mysql_connect.php");
$r = mysql_query("select * from pr_order_bill where id_order=$id");
while($a = mysql_fetch_array($r)){
  $id_pic = $a['id'];
?>
<img src='/bills/driver.<?=$id?>.<?=$id_pic?>.jpg'>
<? 
}
?>