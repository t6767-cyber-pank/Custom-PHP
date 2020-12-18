<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
include("$DOCUMENT_ROOT/mysql_connect.php");
/**Настройки аякса**/
$AJAX_TIMEOUT = 20000;
$operation = $_POST['operation'];
/**-----Настройки аякса**/
$rar1 = array();
$drar = array();
$crar = array();

$crarcount=0;
$coursex=1;

/**Выводит дату понедельника из выбранной даты**/
function get_monday ($dar)
{
    $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
    $adat = mysql_fetch_array($rdat);
    $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=1");
    $adat2 = mysql_fetch_array($rdat2);
    return $adat2['data'];
}
/**-------Выводит дату понедельника из выбранной даты**/

/**Выводит дату понедельника из выбранной даты**/
function get_sunday ($dar)
{
    $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
    $adat = mysql_fetch_array($rdat);
    $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=7");
    $adat2 = mysql_fetch_array($rdat2);
    return $adat2['data'];
}
/**-------Выводит дату понедельника из выбранной даты**/

$r = mysql_query("SELECT DISTINCT c.name, c.id as cidcity FROM m_city c, masters m where m.id_m_city=c.id and m.usevk>0 and m.shown>0 order by c.name asc");
while($a = mysql_fetch_array($r)) {
    $ar2 = array();
    $ar2[0]=$a['cidcity'];
    $ar2[1]=$a['name'];
    array_push($crar, $ar2);
    $crarcount++;
}


$dt1=get_sunday(date("Y-m-d")); //"+6 days"
$dt2=get_monday(date("Y-m-d", strtotime("-1 month")));
if (isset($_GET['search-to-daten'])) $dt2=get_monday($_GET['search-to-daten']);
if (isset($_GET['search-to-date'])) { $dt1 =get_sunday(date("Y-m-d", strtotime($_GET['search-to-date']))); }
$q = "select data from timer where data between '$dt2' and '$dt1' order by  data asc";
$r = mysql_query($q);
while ($a = mysql_fetch_array($r)) {
        array_push($drar, $a['data']);
    }

$podporiginals = array(); // Массив оригинала чатов
$podpsumm = array(); // Массив суммы подписчиков
$podptek = array(); // массив текущих подписок

/** Вывод массива оригинала чатов **/
$xr=0;
foreach ($crar as $ar) {
    $qxx = "select userg from cityimportvk where id_mcity=".$ar['0']." and data='".$dt1."' order by  data";
    $rxx1 = mysql_query($qxx);
    $ax1 = mysql_fetch_array($rxx1);
    $podporiginals[$xr]=(int)$ax1['userg'];
    $xr++;
}
/**-------Вывод оригинала чатов**/


/** Вывод бюджета города ВК **/
$budgetcity = array();
$xr=0;
foreach ($crar as $ar) {
    $qxxbc = "SELECT sum(budgetvk) as bvk FROM `master_week` mw, `masters` m, `m_city` c WHERE mw.id_master=m.id and m.id_m_city=c.id and m.id_m_city=".$ar['0']." and dt between '".$dt2."' and '".$dt1."'";
    $rxxbc = mysql_query($qxxbc);
    $axbc = mysql_fetch_array($rxxbc);
    $budgetcity[$xr]=(int)$axbc['bvk'];
    $xr++;
}
/**-------Вывод бюджета города ВК**/


$j=0;
foreach ($drar as $dar) {
    $rar3 = array();
    $rar2 = array();
    $rar4 = array();
    $rar5 = array();
    $rar6 = array();
    $rar7 = array();
    $car = array();
    $rar3[0][0]=$dar;
    $i=0;
    $ixgq=0;
    $ixgq6=0;
    $ito=0; //vs
    $ito1=0; //sb
    $ito2=0; //pt
    $ito3=0; //ct
    $ito4=0; //sr
    $ito5=0; //vt
    $ito6=0; //pn
    $jto=0;


    $rxx = mysql_query("SELECT DISTINCT course FROM `master_week` where course>0 and dt='".$dar."'");
    if (mysql_num_rows($rxx)>0){
        $axxx = mysql_fetch_array($rxx);
        $coursex=$axxx['course'];
    } else {
        if ($coursex==1)
        {
            $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
            $adat = mysql_fetch_array($rdat);
            $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=1");
            $adat2 = mysql_fetch_array($rdat2);
            $rxx = mysql_query("SELECT DISTINCT course FROM `master_week` where course>0 and dt='".$adat2['data']."'");
            if (mysql_num_rows($rxx)>0){
                $axxx = mysql_fetch_array($rxx);
                $coursex=$axxx['course'];
            }
        }
    }

    $rar3[5][0]=$coursex;

    foreach ($crar as $ar) {
        $car[$jto]=$ar[1];
        $jto++;
//контакты
        $qxx = "select sum(chatsvk) as cvk from m_city_day_vk where id_m_city=".$ar['0']." and dt='".$dar."' order by  dt";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar2[$i]=$ax1['cvk'];
            $i++;
        }

//Расходы VK
        $qxx = "select sum(outcome) as outcome from cityimportvk where id_mcity=".$ar['0']." and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar5[$ixgq]=round((float)$ax1['outcome'],2);
            $ixgq++;
        }

//Подписки VK
        $qxx = "select userg from cityimportvk where id_mcity=".$ar['0']." and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        $ax1 = mysql_fetch_array($rxx1);
        $rar6[$ixgq6]=(int)$ax1['userg'];
        $rar7[$ixgq6]=(int)$ax1['userg']-$podporiginals[$ixgq6];
        $podporiginals[$ixgq6]=$rar6[$ixgq6];

        if (strtotime($dar)<=strtotime(date('Y-m-d'))) {
            $podptek[$ixgq6]=$rar6[$ixgq6];
            $podpsumm[$ixgq6] += (int)$rar7[$ixgq6];
        }
        $ixgq6++;
// записи
        $qxx = "SELECT sum(zap_suvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
        $rxx1 = mysql_query($qxx);
        $ax1 = mysql_fetch_array($rxx1);
        $xd=date("N", strtotime($dar));
        if ($xd==7 && $j>=0) {
            $rar4[$ito]=$ax1['rvk'];
            $ito++;

            if (($j-1)>=0) {
                $qxx = "SELECT sum(zap_savk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=" . $ar['0'] . " and mp.dt='" . $dar . "' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j - 1][3][$ito1] = $ax1['rvk'];
                    $ito1++;
                }
            }

            if (($j-2)>=0) {
                $qxx = "SELECT sum(zap_frvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-2][3][$ito2]=$ax1['rvk'];
                    $ito2++;
                }}

            if (($j-3)>=0) {
                $qxx = "SELECT sum(zap_thvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-3][3][$ito3]=$ax1['rvk'];
                    $ito3++;
                }}

            if (($j-4)>=0) {
                $qxx = "SELECT sum(zap_wevk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-4][3][$ito4]=$ax1['rvk'];
                    $ito4++;
                }}

            if (($j-5)>=0) {
                $qxx = "SELECT sum(zap_tuvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-5][3][$ito5]=$ax1['rvk'];
                    $ito5++;
                }}

            if (($j-6)>=0) {
                $qxx = "SELECT sum(zap_monvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=" . $ar['0'] . " and mp.dt='" . $dar . "' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j - 6][3][$ito6] = $ax1['rvk'];
                    $ito6++;
                }
            }

        }

    }
    $rar3[1]=$car;
    $rar3[2]=$rar2;
    $rar3[3]=$rar4;
    $rar3[4]=$rar5;
    $rar3[6]=$rar6;
    $rar3[7]=$rar7;
    $rar1[$j]=$rar3;
    $j++;
}

$celw=400;
$mas=$crarcount*$celw+100;
?>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link href="dist/css/main.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/datetimepicker-master/jquery.datetimepicker.css"/>
    <link rel="stylesheet" type="text/css" href="/style.css">
    <script src="/datetimepicker-master/jquery.js"></script>
    <script src="/datetimepicker-master/jquery.datetimepicker.js"></script>
    <script src="/datetimepicker-master/build/jquery.datetimepicker.full.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        button:not(:disabled) {
            width: 81px;
            height: 23px;
            font-size: 10px;
        }
        #l1 {}
        .tdxh{
            background-color: #f2f2f2; border: 1px solid;
        }

        .tdnode{
            border: 1px solid;
        }

    </style>
    <script type="text/javascript">
        var sh=0;
        function shownone() {
            if (sh==0) {document.getElementById('div_profit').style.display="none"; document.getElementById('a_profit').innerText="Развернуть"; sh=1; } else { document.getElementById('div_profit').style.display="block"; document.getElementById('a_profit').innerText="Свернуть"; sh=0; }
        }
    </script>
    <title><?php echo $_SERVER['REQUEST_URI']; ?></title>
    <script>
        jQuery(document).ready(function () {
            'use strict';
            jQuery('#search-to-date').datetimepicker();
            jQuery.datetimepicker.setLocale('ru');
            jQuery('#search-to-date').datetimepicker({
                format:'Y-m-d',
                dayOfWeekStart: 1,
                timepicker:false,
                lang:'ru'
            });

            jQuery('#search-to-daten').datetimepicker();
            jQuery.datetimepicker.setLocale('ru');
            jQuery('#search-to-daten').datetimepicker({
                format:'Y-m-d',
                timepicker:false,
                dayOfWeekStart: 1,
                lang:'ru'
            });

        });

    </script>
</head>
<body style="font-size: 12px;">
<div id="menu" class="menu" style="top:0; width: 100%; position: fixed; padding-bottom:4px;display: -webkit-box;padding-top:4px; background-color:white;" align="center">
    <ul style="list-style-type: none; margin: 0; padding: 0; ">
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" href="/zsortproc.php">Понедельная разбивка BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: black;" href="/zsortprocmes.php">Разбивка с выбором интервала BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" href="/zsortprocezh.php">Понедельная разбивкак Еж</a></li>
    </ul>
</div>
<div align="center" style="position: fixed; width: 100%; z-index: 100; top: 63px; background-color: #f2F2F2; padding: 3px;">
    <div style="text-align: center;">
    <form method="get" style="margin: 0px;">
    <label for="search-to-daten" style="font-size: 12px; margin-right: 10px;">Дата начала:</label><input type="text" name="search-to-daten" id="search-to-daten" value="<?=$dt2; ?>" style="font-size: 12px; margin-right: 10px;"/>
    <label for="search-to-date" style="font-size: 12px; margin-right: 10px;">Дата конца:</label><input type="text" name="search-to-date" id="search-to-date" value="<?=$dt1; ?>" style="font-size: 12px; margin-right: 10px;"/>
    <input type="submit" value="Выбрать" style="font-size: 12px; margin-right: 10px;" />
        <a href="javascript:void(0)" onclick="shownone();" style="text-decoration:none;border-bottom:1px dashed; margin-right: 20px; margin-right: 20px; background: none; border-left: none; border-right: none; border-top: none; font-size: 12px;" id="a_profit">Свернуть</a>
    </form>
    </div>
</div>
    <div id='user_block' style="text-align: center; margin-top: 150px;">
        <?php
            getallvk($crar, $rar1, $dt2, $dt1, $podporiginals, $podpsumm, $podptek, $budgetcity);
        ?>
    </div>
    <?php

    function getallvk($crar, $rar1, $dt2, $dt1, $podporiginals, $podpsumm, $podptek, $budgetcity)
    {
        $colorx=0;
        $chatsx = array();
        $zapx = array();
        $outcomerub = array();
        $outcometg = array();
        $rx = 0;

        $chatsall = 0;
        $zapsall = 0;
        $outcomrrubsall = 0;
        $outcomtggall = 0;
        foreach ($crar as $rc) {
            $chats = 0;
            $zap = 0;
            $outcomeR = 0;
            $outcomeT = 0;
            foreach ($rar1 as $r) {
                $chats = $chats + (int)$r[2][$rx];
                $zap = $zap + (int)$r[3][$rx];
                $outcomeR = $outcomeR + (float)$r[4][$rx];
                $outcomeT = $outcomeT + ((float)$r[4][$rx] * (float)$r[5][0]);
            }
            $chatsx[$rx] = $chats;
            $zapx[$rx] = $zap;
            $outcomerub[$rx] = $outcomeR;
            $outcometg[$rx] = $outcomeT;

            $chatsall = $chatsall + $chats;
            $zapsall = $zapsall + $zap;
            $outcomrrubsall = $outcomrrubsall + $outcomeR;
            $outcomtggall = round($outcomtggall + $outcomeT);

            $rx++;
        }
        ?>
        <div style="position: fixed; z-index: 200; left: 81%; top: 57px;">
            <a href="/excelvk.php?search-to-daten=<?=$dt2 ?>&search-to-date=<?=$dt1 ?>" target="_blank">
                <button onclick="1010" class="btn btn-outline-success" type="button" style="margin-top: 10px;">EXCEL отчет</button>
            </a>
        </div>
        <?php
        echo "<div style='display: block; position: fixed; top: 90px; padding-top: 10px; width: 100%; background-color: rgb(242, 242, 242);' id='div_profit'>";
        echo "<table style='width: 100%; text-align: center;'>";
        echo "<tr>";
        echo "<td>";
        echo "<p style='font-size: 14px;'>Количество чатов: <br><b>$chatsall шт</b></p>";
        echo "</td>";
        echo "<td>";
        echo "<p style='font-size: 14px;'>Количество записей: <br><b>$zapsall шт</b></p>";
        echo "</td>";
        echo "<td>";
        echo "<p style='font-size: 14px;'>Сумма расходов (р): <br><b>$outcomrrubsall руб</b></p>";
        echo "</td>";
        echo "<td>";
        echo "<p style='font-size: 14px;'>Сумма расходов (тг): <br><b>$outcomtggall тг</b></p>";
        echo "</td>";
        echo "<td>";
        $lidall = 0;
        if ($chatsall > 0) $lidall = round($outcomtggall / $chatsall);
        echo "<p style='font-size: 14px;'>Средняя цена сообщения: <br><b>$lidall тг</b></p>";
        echo "</td>";
        echo "<td>";
        $zapppall = 0;
        if ($zapsall > 0) $zapppall = round($outcomtggall / $zapsall);
        echo "<p style='font-size: 14px;'>Средняя цена записи: <br><b>$zapppall тг</b></p>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        $dtvcherav = date('d.m.Y', strtotime("- 1 days"));
        $dtvchera = date('Y-m-d', strtotime("- 1 days"));
        echo "<div style='float: left; margin-left: 50px;'><span style='color: white; background: #d9534f; padding: 2px 7px; margin-right: 10px;'>Мало чатов  вчера: </span>";
        $znal="";
        foreach ($crar as $rc) {
            $rdat = mysql_query("SELECT * FROM `m_city_day_vk` WHERE `id_m_city`=".$rc[0]." and `dt`='$dtvchera'");
            $adat = mysql_fetch_array($rdat);
            if ((int)$adat['chatsvk']<2) {
                echo $znal.$rc[1];
                $znal=", ";
            }
        }
        echo "</div>";
        echo "</div>";
        echo "<div  class='container' style='background-color: white; width: 100%; position: fixed; top: 25px; margin: 0px; clear: both; padding: 10px; max-width: 100%;'>";
        foreach ($crar as $rc) {
            echo "<a href='#".$rc[1]."' class='transition' style='margin-right: 10px; font-size: 13px; text-decoration: none; border-bottom: 1px dashed;'>".$rc[1]."</a>";
        }
        echo "</div >";
        ?>

        <?php

        $rx = 0;
        foreach ($crar as $rc) {
            echo "<div style='width: 100%; overflow-x: auto; padding-left: 50px; padding-right: 50px; padding-top: 10px; padding-bottom: 50px;'>";
            echo "<div style='width: 100%; border: 1px solid; width: 100%; background-color: #f2F2F2; padding-bottom: 15px; padding-top: 15px;'>";
            $pricechats = 0;
            if ($chatsx[$rx] > 0) {
                $pricechats = round($outcometg[$rx] / $chatsx[$rx]);
            }
            $pricezapis = 0;
            if ($zapx[$rx] > 0) {
                $pricezapis = round($outcometg[$rx] / $zapx[$rx]);
            }
            echo "<h1 style='margin-left: 20%; width: 765px; background-color: #f2F2F2; margin-bottom: 0px; text-align: left; font-size: 30px; '><a id='l1' name='" . $rc[1] . "'>" . $rc[1] . "</a><span style='float: right; font-size: 20px;'>Сумма недельных бюджетов ВК <b>".$budgetcity[$rx]."</b></span><br><span style='font-size: 13px; margin-top: -7px; padding-bottom: 4px; float: right;'>Средняя стоимость сообщения: <b>$pricechats</b> тг <span style='margin-left: 25px;'></span>Средняя стоимость записи: <b>$pricezapis</b> тг</span></h1>";
            echo "<table style='margin-left: 20%; width: 820px; border: 1px solid;  background: floralwhite;'>";
            /**Заголовок**/
            echo "<tr style='background-color: aliceblue; font-size: 19px;'>";
            echo "<td align='center' class='tdxh'>Дата</td>";
            echo "<td align='center' class='tdxh'>Чаты</td>";
            echo "<td align='center' class='tdxh'>Записи</td>";
            echo "<td align='center' class='tdxh'>Расх.(р)</td>";
            echo "<td align='center' class='tdxh'>Расх.(тг)</td>";
            echo "<td align='center' class='tdxh'>Подписчики</td>";
            echo "<td align='center' class='tdxh'>Прирост</td>";
            echo "</tr>";
            /**-------Заголовок**/

            /**Итого**/
            echo "<tr>";
            echo "<td align='center' class='tdxh'><b>Итого</b></td>";
            echo "<td align='center' class='tdxh'><b>" . $chatsx[$rx] . "</b></td>";
            echo "<td align='center' class='tdxh'><b>" . $zapx[$rx] . "</b></td>";
            echo "<td align='center' class='tdxh'><b>" . $outcomerub[$rx] . "</b></td>";
            echo "<td align='center' class='tdxh'><b>" . round($outcometg[$rx]) . "</b></td>";
            echo "<td align='center' class='tdxh'><b>".$podptek[$rx]."</b></td>";
            echo "<td align='center' class='tdxh'><b>".$podpsumm[$rx]."</b></td>";
            echo "</tr>";
            /**-------Итого**/

            $rar11 = array_reverse($rar1);
            foreach ($rar11 as $r) {
                $arraycolor=array("background-color: white;", "background-color: #f2f2f2;");
                $dtseg = date('Y-m-d');
                if (strtotime($dtseg) < strtotime($r[0][0])) continue;
                echo "<tr style='".$arraycolor[$colorx]."'>";
                echo "<td class='tdnode' align='center'>";
                echo date("d.m.Y", strtotime($r[0][0]));
                echo "</td>";

                echo "<td class='tdnode' align='center'>";
                echo (int)$r[2][$rx];
                echo "</td>";
                echo "<td class='tdnode' align='center'>";
                echo (int)$r[3][$rx];
                echo "</td>";
                echo "<td class='tdnode' align='center'>";
                echo (float)$r[4][$rx];
                echo "</td>";
                echo "<td class='tdnode' align='center'>";
                echo round($r[4][$rx] * (float)$r[5][0]);
                echo "</td>";
                echo "<td class='tdnode' align='center'>";
                echo $r[6][$rx];
                echo "</td>";
                echo "<td class='tdnode' align='center'>";
                echo $r[7][$rx];
                echo "</td>";
                echo "</tr>";
                $colorx++;
                if ($colorx>1) $colorx=0;
            }
            echo "</table></div>";
            echo "</div>";
            $rx++;
        }
        /*
            echo "<pre>";
        var_dump($podptek);
            echo "</pre>";
        */
    }
    ?>

<script>
    (function($, window) {
        var adjustAnchor = function() {

            var $anchor = $(':target');
            fixedElementHeight = 180;

            if ($anchor.length > 0) {
                $( "html, body" ).stop().animate({
                    scrollTop: $anchor.offset().top - fixedElementHeight,
                }, 100, function() {
                    // Animation complete.
                });


            }
        };

        $(window).on('hashchange load', function() {
            adjustAnchor();
        });

    })(jQuery, window);
</script>
</body>
</html>
