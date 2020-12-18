<?php 
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
$date1=date("Y-m-d", strtotime("-1 month"));
$date2=date("Y-m-d");
$usermanager=0;
$usermanagertext="";
$rashZ="";
if (isset($_GET["dateN"])){ $date1=date("Y-m-d", strtotime($_GET["dateN"])); }
if (isset($_GET["dateK"])){ $date2=date("Y-m-d", strtotime($_GET["dateK"])); }


$dview1=date("d.m.Y", strtotime($date1));
$dview2=date("d.m.Y", strtotime($date2));
$usercity=0;
if (isset($_GET["city"])){ $usercity=$_GET["city"]; }
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
</head>
<body>
<?php include("$DOCUMENT_ROOT/headermenu.php"); ?>
<div align="center" style="margin-top: 5px;";>
<form action="/diagramProdagEzh.php" method="GET">
  Дата начала:
  <input name="dateN" id="disabled-days">
  Дата конца:
  <input name="dateK" id="disabled-days2" >
    <?php
    if (isset($_GET['m'])) {
        ?>
        <input type="hidden" id="custId" name="m" value="0">
        <?php
    }
    ?>
    <select size="1" name="city">
        <option disabled>Выберите город</option>
        <option <?php if ($usercity==0) echo 'selected'; ?> value="0">Все города</option>
        <?php
        $rmanager = mysql_query("SELECT * FROM pr_city ORDER BY name ASC");
        while ($amanager = mysql_fetch_array($rmanager)){
            echo  "<option ";
            if ($usercity==$amanager['id']) echo 'selected ';
            echo "value='".$amanager['id']."' >".$amanager['name']."</option>";
        }
        ?>
    </select>
    <input type="submit" value="Показать">
</form>
    <form action="/excelTovar.php" target="_blank" method="GET">
    <button>Вывести в EXCEL продажи по городам</button>
    <select size="1" name="city">
        <option disabled>Выберите город</option>
        <option <?php if ($usercity==0) echo 'selected'; ?> value="0">Все города</option>
        <?php
        $rmanager = mysql_query("SELECT * FROM pr_city ORDER BY name ASC");
        while ($amanager = mysql_fetch_array($rmanager)){
            echo  "<option ";
            if ($usercity==$amanager['id']) echo 'selected ';
            echo "value='".$amanager['id']."' >".$amanager['name']."</option>";
        }
        ?>
    </select>
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
    $q = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '$date1' and '$date2' group by p.name ORDER BY ttt DESC");
    $qsrav = mysql_fetch_array($q);
//    if ($qsrav["chatcount"]>2) {
        ?>
        <div id="content" class="content" align="center">
            <br/>
            <p><img
                        src="i.php?dateN=<?php echo $date1; ?>&dateK=<?php echo $date2; ?>&city=<?php echo $usercity; ?>"
                        alt="Продукция" class="right" id="content" /></p>
        </div>
        <?php
 //   }
?>
<script>

var disabledDays = [1, 2, 3, 4, 5, 6];
$('#disabled-days').datepicker({
	todayButton: new Date()
});
$('#disabled-days2').datepicker({
	todayButton: new Date()
});

document.getElementById("disabled-days").value = "<?php echo $dview1; ?>";
document.getElementById("disabled-days2").value = "<?php echo $dview2; ?>";
</script>
</body>
</html>