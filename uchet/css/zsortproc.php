<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
include("$DOCUMENT_ROOT/mysql_connect.php");
$r1 = mysql_query("select m.id as masid from masters m order by m.id ASC");
//$r1 = mysql_query("select m.id as masid, p.id as pid from masters m, procedures p where m.id=p.id_master order by m.id ASC");
while($a = mysql_fetch_array($r1)) {
$rxd = mysql_query("select p.id as pid, p.sort, p.active from masters m, procedures p where m.id=p.id_master and m.id=".$a['masid']." order by p.active DESC, p.id ASC");
$i=100;
echo "<h3>".$a['masid']."</h3>";
while($axd = mysql_fetch_array($rxd)) {
    $i--;
    echo $axd["pid"]."  ".$axd["sort"]."<br>";
    mysql_query("update procedures set sort=$i where id=".$axd["pid"]);
}
}
?>

