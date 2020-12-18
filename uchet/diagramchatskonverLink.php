<?php 
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
$date1=date("Y-m-d", strtotime("-3 month"));
$date2=date("Y-m-d");
$usermanager=0;
$usermanagertext="";

if (isset($_GET["dateN"])){ $date1=date("Y-m-d", strtotime($_GET["dateN"])); }
if (isset($_GET["dateK"])){ $date2=date("Y-m-d", strtotime($_GET["dateK"])); }
if (isset($_GET["manager"])){ $usermanager=$_GET["manager"]; }

$dview1=date("d.m.Y", strtotime($date1));
$dview2=date("d.m.Y", strtotime($date2));
?>

<html>
<head>
<meta charset="utf-8">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <meta http-equiv="cache-control" content="no-cache, no store"/>
    <meta http-equiv="Expires" Content="Mon, 25 May 2009 19:07:03 GMT">
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
<br><br>
<div align="center" style="margin-top: 5px;";>
<form action="/diagramchatskonverLink.php" method="GET">
  Дата начала:
  <input name="dateN" id="disabled-days"> <!-- data-range="true" data-multiple-dates-separator=" - " class="datepicker-here" -->
  Дата конца:
  <input name="dateK" id="disabled-days2" >
    <select size="1" name="manager" style="display: none">
        <option disabled>Выберите менеджера</option>
        <option <?php if ($usermanager==0) echo 'selected'; ?> value="0">Все менеджеры</option>
        <?php
        $rmanager = mysql_query("SELECT * FROM users WHERE type=1 and active=1 ORDER BY name ASC");
        while ($amanager = mysql_fetch_array($rmanager)){
            echo  "<option ";
            if ($usermanager==$amanager['id']) echo 'selected ';
            echo "value='".$amanager['id']."' >".$amanager['name']."</option>";
        }
        ?>
    </select>
    <input type="submit">
</form>
</div>
<script>
var d =new Date("<?php echo $date1; ?>");
var d2=new Date("<?php echo $date2; ?>");
document.getElementById('datePicker').valueAsDate = d;
document.getElementById('datePicker1').valueAsDate = d2;
</script
<br>
<?php
if ($usermanager>0) {$usermanagertext="and `id_manager`=".$usermanager." ";}
$r = mysql_query("SELECT DISTINCT c.name, c.id as cityid FROM users u, `m_city` c, `masters` m WHERE u.id=m.id_master and m.usevk>0 and m.shown>0 and m.`id_m_city`=c.id ".$usermanagertext."ORDER BY m.`sort` ASC"); //SELECT * FROM m_city order by name");
$counter=1;
while ($a = mysql_fetch_array($r)){
    $q = mysql_query("SELECT COUNT(chats) as chatcount FROM `m_city_day` d, m_city m where d.`id_m_city`=m.id and m.name='".$a['name']."' and dt BETWEEN '".$date1."' AND '".$date2."' order by `id_m_city`, `dt`");
    $qsrav = mysql_fetch_array($q);
    if ($qsrav["chatcount"]>0) { //echo $qsrav["chatcount"];
        $qcitymasters=mysql_query("SELECT u.name FROM users u, `m_city`c, `masters` m where u.id=m.`id_master` and c.id=m.`id_m_city` and c.id=".$a['cityid']." and m.shown>0 and m.usevk>0  ORDER BY `u`.`name` ASC");
        ?>

        <div id="content" class="content" align="center">
            <br/><h5><?php echo $a['name']." (";
                $qcitymastera = mysql_fetch_array($qcitymasters);
                echo $qcitymastera['name'];
                while ($qcitymastera = mysql_fetch_array($qcitymasters)){ echo ", ".$qcitymastera['name']; }
                echo ")"; ?></h5>
            <p><img
                        src="chartkonvervk.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $a['name']; ?>"
                        alt="<?php echo $a['name']; ?>" class="right" id="content<?php echo $counter; ?>" /></p>
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