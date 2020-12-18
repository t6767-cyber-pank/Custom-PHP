<?php 
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
$date1=date("Y-m-d", strtotime("-3 month"));
$date2=date("Y-m-d");

if (isset($_GET["dateN"])){ $date1=date("Y-m-d", strtotime($_GET["dateN"])); }
if (isset($_GET["dateK"])){ $date2=date("Y-m-d", strtotime($_GET["dateK"])); }

$rashZ="";
if (isset($_GET["rash"])) $rashZ="&rash=1";

$dview1=date("d.m.Y", strtotime($date1));
$dview2=date("d.m.Y", strtotime($date2));
?>
<html>
<head>
<meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
<form action="/diagramchatsezh2.php" method="GET">
  Дата начала:
  <input name="dateN" id="disabled-days"> <!-- data-range="true" data-multiple-dates-separator=" - " class="datepicker-here" -->
  Дата конца:
  <input name="dateK" id="disabled-days2" >
<?php 
if (isset($_GET['m'])) {
?>
  <input type="hidden" id="custId" name="m" value="0">
<?php 
}
?>
    <input type="checkbox" <?php if (isset($_GET["rash"])) echo "checked"; ?> name="rash" value="1">Показать расходы
    <input type="submit" value="Показать">
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

$r = mysql_query("SELECT DISTINCT name FROM `ezh_city` ORDER BY `name` ASC"); //SELECT * FROM m_city order by name");
$counter=1;
while ($a = mysql_fetch_array($r)){
    $q = mysql_query("SELECT COUNT(contacts) as chatcount FROM `ezh_city_day` d, ezh_city m where d.id_city=m.id and m.name='".$a['name']."' and dt BETWEEN '".$date1."' AND '".$date2."' order by m.`name`, `dt`");
    $qsrav = mysql_fetch_array($q);
    if ($qsrav["chatcount"]>4) { //echo $qsrav["chatcount"];
        ?>

        <div id="content" class="content">
            <br><br>
            <?php
            $dateNachala=date("Y-m-d", strtotime($date1));
            $dateKonca=date("Y-m-d", strtotime($date2));
            $city=$a['name'];
            $dn=strtotime($dateNachala);
            $dk=strtotime($dateKonca);
            switch (date("D", strtotime($dateNachala)))
            {
                case "Mon" : $dn=$dn-0; break;
                case "Tue" : $dn=$dn-86400; break;
                case "Wed" : $dn=$dn-86400*2; break;
                case "Thu" : $dn=$dn-86400*3; break;
                case "Fri" : $dn=$dn-86400*4; break;
                case "Sat" : $dn=$dn-86400*5; break;
                case "Sun" : $dn=$dn-86400*6; break;
            }
            switch (date("D", strtotime($dateKonca)))
            {
                case "Mon" : $dk=$dk+86400*6; break;
                case "Tue" : $dk=$dk+86400*5; break;
                case "Wed" : $dk=$dk+86400*4; break;
                case "Thu" : $dk=$dk+86400*3; break;
                case "Fri" : $dk=$dk+86400*2; break;
                case "Sat" : $dk=$dk+86400; break;
                case "Sun" : $dk=$dk+0; break;
            }
            $dateNachala=date("Y-m-d",$dn);
            $dateNachalaVoskreseniya=date("Y-m-d",$dn-86400);
            $dateKonca=date("Y-m-d",$dk);
            $chatOriginal="";
            $chatPrev="";
            $dateObrabotka=strtotime($dateNachala);
            $chorigr = mysql_query("SELECT * FROM `ezh_city_day` d, ezh_city m where d.id_city=m.id and m.name='".$city."' and dt='".$dateNachalaVoskreseniya."' order by m.name, `dt`");
            while ($chorigarr = mysql_fetch_array($chorigr))  // Переборка данных с базы в данные приемлимые обработке
            {
                $chatOriginal=$chorigarr['contacts'];
                $chatPrev=$chatOriginal;
            }
            $sborvnedelyu=0;
            $sborvnedelyuRash=0;
            $summaChat=0;
            $summaRash=0;
            $dateNachalaSQLZap="";
            $dateKoncaSQLZap="";
            while ($dateObrabotka!=strtotime($dateKonca)+86400)
            {
                if (date("D",$dateObrabotka)=="Mon")
                {
                    $dateNachalaSQLZap=date("Y-m-d",$dateObrabotka);
                    $dateKoncaSQLZap=date("Y-m-d",$dateObrabotka+86400*6);
                    $qzap = mysql_query("SELECT * FROM `ezh_city_day` d, ezh_city m where d.id_city=m.id and m.name='".$city."' and dt BETWEEN '".$dateNachalaSQLZap."' AND '".$dateKoncaSQLZap."' order by `dt`");
                    while ($al = mysql_fetch_array($qzap))
                    {
                        $contactsbd=(int)$al['contacts'];
                        if ($chatOriginal=="") {$chatOriginal=$chatPrev;} else { $chatPrev=$chatOriginal; }
                        if ($chatOriginal<=0) {$chatOriginal=$chatPrev;}
                        $push=0;
                        if ($contactsbd<1) {$push=0; $chatOriginal=$chatPrev; } else { $push=(int)$al['contacts']-(int)$chatOriginal; $chatOriginal=$al['contacts']; }
                        $sborvnedelyu=$sborvnedelyu+$push;
                        $summaChat=$summaChat+$push;
                    }
                }

                $qzapRash = mysql_query("SELECT * FROM ezh_city_week ev, ezh_city m where ev.id_city=m.id and m.name='".$city."' and dt='".date("Y-m-d",$dateObrabotka)."'");
                $arash = mysql_fetch_array($qzapRash);
                $sborvnedelyuRash=$sborvnedelyuRash+(int)$arash['outcome'];

                if (date("D",$dateObrabotka)=="Sun")
                {
                    if ($sborvnedelyuRash>0) { $sborvnedelyuRashVBd=$sborvnedelyuRash/1000; } else { $sborvnedelyuRashVBd=0; }
                    $summaRash=$summaRash+$sborvnedelyuRash;
                    $sborvnedelyu=0;
                    $sborvnedelyuRash=0;
                }
                $dateObrabotka=$dateObrabotka+86400;
            }
            if ($summaRash>0 && $summaChat>0) {
                $scr = round($summaRash / $summaChat);
            } else { $scr=0; }
            ?>
            <div class="container-fluid T_M_DIAG_Prostor">
                <div class="row">
                    <div class="col-2">
                    </div>
                    <div class="col-8">

                    <div class="lableCity" style="margin-left: 30px;">
                <p><b><span style="font-size: 25px;"><?php echo $city."  "; ?></span></b> <br/>за период: <span style="color: rgb(34,113,0); font-weight: 800;">чатов <?php echo $summaChat; ?></span>, расходы: <span style="color: rgb(113,0,3); font-weight: 800;"><?php echo $summaRash; ?> тг</span>, сред. цена за лид: <span style="color: rgb(0,0,0); font-weight: 800;"><?php echo $scr; ?> тг</span></p></div>
            <div><img
                            src="chartezh2.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $city."".$rashZ; ?>"
                            alt="<?php echo $city; ?>" class="right" id="content<?php echo $counter; ?>" /></div>
                    </div>
                    <div class="col-2">
                    </div>
                </div>
            </div>
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
<br>
</body>
</html>