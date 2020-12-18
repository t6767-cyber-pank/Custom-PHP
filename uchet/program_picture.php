<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$DOCUMENT_ROOT/mysql_connect.php");
$id = $_REQUEST['id'];
header("Content-type: image/png");
$r = mysql_query("select * from pr_tovar where id=$id");
while($a = mysql_fetch_array($r)){
  $pic = $a['picture'];
  print $pic;
}
mysql_close($conn);
?>