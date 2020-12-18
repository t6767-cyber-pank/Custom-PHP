<?php 
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
$date1=date("Y-m-d", strtotime("-3 month"));
$date2=date("Y-m-d");

if (isset($_GET["dateN"])){ $date1=date("Y-m-d", strtotime($_GET["dateN"])); }
if (isset($_GET["dateK"])){ $date2=date("Y-m-d", strtotime($_GET["dateK"])); }

$dview1=date("d.m.Y", strtotime($date1));
$dview2=date("d.m.Y", strtotime($date2));
$iduser=0;
$nameuser="";
if (isset($_GET["name"])) {
$nameuser=$_GET["name"];
$quser = mysql_query("SELECT * FROM users where name='".$_GET["name"]."'");
$qsravUser = mysql_fetch_array($quser);
$iduser=$qsravUser["id"];
}
?>
<html>
<head>
<meta charset="utf-8">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	<link href="dist/css/datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="dist/js/datepicker.min.js"></script>
	<style>
body {
    background-color: #f2F2F2;
    font-family: Arial;
    font-size: 16px;
}

</style>
<script type="text/javascript">

   function changeFunc() {
	//var dtp = document.getElementById("datePicker");
    //var selectedValueDateN = dtp.value;
	
	//var dtp2 = document.getElementById("datePicker1");
    //var selectedValueDateK = dtp2.value;
	
	//var img = document.getElementById("content1");
	//img.src = "City.jpg";

	//img.src = "http://localhost/chart.php?dateN="+selectedValueDateN+"&dateK="+selectedValueDateK+"&city="+img.alt;
	
	//var link = document.getElementById("forImage");
	//link.href=img.src;
   }

  </script> 
  
</head>
<body>
<?php include("$DOCUMENT_ROOT/headermenu2.php"); ?>
<div align="center" style="margin-top: 5px;";>
<form action="/diagramchatskonver2.php" method="GET">
  Дата начала:
  <input name="dateN" id="disabled-days"> <!-- data-range="true" data-multiple-dates-separator=" - " class="datepicker-here" -->
  Дата конца:
  <input name="dateK" id="disabled-days2" >
    <input type="hidden" id="custId" name="name" value="<?php echo $nameuser; ?>">
  <input type="submit">
</form>
</div>
<script>
var d =new Date("<?php echo $date1; ?>");
var d2=new Date("<?php echo $date2; ?>");
document.getElementById('disabled-days').valueAsDate = d;
document.getElementById('disabled-days2').valueAsDate = d2;
</script
<br>
<?php

$r = mysql_query("SELECT DISTINCT c.name, c.id as cityid FROM users u, `m_city` c, `masters` m WHERE u.id=m.id_master and m.`id_m_city`=c.id and `id_manager`=".$iduser." ORDER BY m.`sort` ASC");
$counter=1;
while ($a = mysql_fetch_array($r)){
    $usvk=0;
    $q = mysql_query("SELECT COUNT(chats) as chatcount FROM `m_city_day` d, m_city m where d.`id_m_city`=m.id and m.name='".$a['name']."' and dt BETWEEN '".$date1."' AND '".$date2."' order by `id_m_city`, `dt`");
    $qsrav = mysql_fetch_array($q);
    if ($qsrav["chatcount"]>0) { //echo $qsrav["chatcount"];
        $qcitymasters=mysql_query("SELECT u.name, m.usevk FROM users u, `m_city`c, `masters` m where u.id=m.`id_master` and c.id=m.`id_m_city` and c.id=".$a['cityid']." and m.shown>0  ORDER BY `u`.`name` ASC");
        ?>

        <div id="content" class="content" align="center">
            <br/><h5><?php echo $a['name']." (";
                $qcitymastera = mysql_fetch_array($qcitymasters);
                if ($qcitymastera['usevk']>0) $usvk=1;
                echo $qcitymastera['name'];
                while ($qcitymastera = mysql_fetch_array($qcitymasters)){ echo ", ".$qcitymastera['name'];  if ($qcitymastera['usevk']>0) $usvk=1;}
                echo ")"; ?></h5>
            <p><span style=" background: linear-gradient( #400080, transparent), linear-gradient( 200deg, #d047d1, #ff0000,    #ffff00); color: white; padding: 3px;">INSTAGRAM</span><br><img
                            src="chartkonver.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $a['name']; ?>"
                            alt="<?php echo $a['name']; ?>" class="right" id="content<?php echo $counter; ?>" /></p>
        <?php if($usvk>0) { ?>
            <p><span style=" background: #2B587A; color: white; padding: 3px;">ВК</span><br><img
                        src="chartkonvervk.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $a['name']; ?>"
                        alt="<?php echo $a['name']; ?>" class="right" id="content<?php echo $counter; ?>" /></p>
        <?php } ?>
        </div>
        <?php
        $counter++;
    }}
?>
<script>

var disabledDays = [1, 2, 3, 4, 5, 6];

$('#disabled-days').datepicker({
    onRenderCell: function (date, cellType) {
        if (cellType == 'day') {
            var day = date.getDay(),
                isDisabled = disabledDays.indexOf(day) != -1;

            return {
                disabled: isDisabled
            }
        }
    }
});
$('#disabled-days').datepicker({
	todayButton: new Date()
});

$('#disabled-days2').datepicker({
    onRenderCell: function (date, cellType) {
        if (cellType == 'day') {
            var day = date.getDay(),
                isDisabled = disabledDays.indexOf(day) != -1;

            return {
                disabled: isDisabled
            }
        }
    }
});
$('#disabled-days2').datepicker({
	todayButton: new Date()
});

document.getElementById("disabled-days").value = "<?php echo $dview1; ?>";
document.getElementById("disabled-days2").value = "<?php echo $dview2; ?>";
</script>
</body>
</html>