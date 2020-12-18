<?php
/**глобальные переменные**/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$PHP_SELF = $_SERVER['PHP_SELF'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);

/**Общие подключения к классу для работы с бд**/
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
/**Подключаем класс оболочку**/
$zsp=new zsp();

/**Общие подключения к компоненту листалки**/
require_once("$DOCUMENT_ROOT/comp/datetimepickerweek/using.php");

////////////////////////////////////////////////////////////////////////Пересмотреть позже
/**Переменные массивов обработки**/
$rar1 = array(); // Массив сборщик всякого общего барахла
////////////////////////////////////////////////////////////////////////Пересмотреть позже
/**Рабочие переменные**/
$crarcount=0;
////////////////////////////////////////////////////////////////////////Пересмотреть позже
/**Наполняем массив городов с базы**/
$crar = $zsp->allCityUseVk();

/**Парсинг и назначение даты на недельный интервал**/
$cran = reactionStart($_POST['dt']);
if ($cran>0) {
    $dt = date("Y-m-d",strtotime($_POST['dt']));
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dtp->set_operation('show_masters');
    $zsp->set_dt($dt);
} else
{
    $dt = date("Y-m-d");
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dt=$dtp->get_monday();
    $zsp->set_dt($dt);
}

/**Подгоняем воскресение**/
$dt_to = $dtp->get_sunday();
$dtp->set_dt_to($dt_to);
$zsp->set_dt_to($dt_to);

////////////////////////////////////////////////////////////////////////Пересмотреть позже
/**Наполним массив обработки дат**/
$drar=$dtp->arraydates();

////////////////////////////////////////////////////////////////////////Пересмотреть позже
$podpsumm = array(); // Массив суммы подписчиков
$podptek = array(); // массив текущих подписок

$j=0;
foreach ($drar as $dar) {
    $rar3 = array();
    $rar4 = array();
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

    foreach ($crar as $ar) {
        $car[$jto]=$ar[1];
        $jto++;
//Подписки VK
        $qxx = "select userg from cityimportvk where id_mcity=".$ar['0']." and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        $ax1 = mysql_fetch_array($rxx1);
        $rar6[$ixgq6]=(int)$ax1['userg'];
        $rar7[$ixgq6]=(int)$ax1['userg']-$zsp->getOriginalChatVKCity($ar['0'], $dar);
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
    $rar3[3]=$rar4;
    $rar3[6]=$rar6;
    $rar3[7]=$rar7;
    $rar1[$j]=$rar3;
    $j++;
}

$cran = reactionStart($_POST['operation']);
if ($cran>0 && $_POST['operation']=='show_masters') {
    getallvk($crar, $rar1, $dt, $dt_to, $podpsumm, $podptek, $zsp);
    exit;
}

$celw=400;
$mas=$crarcount*$celw+100;
?>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <?php $dtp->initalHeaders(); ?>
    <script type="text/javascript">
        var sh=0;
        function shownone() {
            if (sh==0) {
                document.getElementById('div_profit').style.display="none";
                document.getElementById('a_profit').innerText="Развернуть";

                var elements = document.querySelectorAll('#disp');
                for (var i = 0; i < elements.length; i++) {
                    elements[i].style.display="none";
                }
            sh=1;
            } else {
                document.getElementById('div_profit').style.display="block";
                var elements = document.querySelectorAll('#disp');
                for (var i = 0; i < elements.length; i++) {
                    elements[i].style.display="table-row";
                }
                document.getElementById('a_profit').innerText="Свернуть"; sh=0; }

        }

    </script>
    <title><?php echo $_SERVER['REQUEST_URI']; ?></title>
</head>
<body style="font-size: 12px;">
<div id="menu" class="menu" style="top:0; width: 100%; position: fixed; padding-bottom:4px;display: -webkit-box;padding-top:4px; background-color:white;" align="center">
    <ul style="list-style-type: none; margin: 0; padding: 0; ">
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: black;" class="scroll-to-this" href="/zsortproc.php">Понедельная разбивка BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" href="/zsortprocmes.php">Разбивка с выбором интервала BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" class="scroll-to-this" href="/zsortprocezh.php">Понедельная разбивкак Еж</a></li>

    </ul>
</div>
<div align="center" style="position: fixed; width: 100%; z-index: 100; top: 63px; background-color: #f2F2F2; padding: 3px;">
    <div style="text-align: center;">
        <?php $dtp->initalComponent(); ?>
        <button onclick="shownone();" style="text-decoration:none;border-bottom:1px dashed; margin-right: 20px; margin-right: 20px; background: none; border-left: none; border-right: none; border-top: none; font-size: 12px;" id="a_profit">Свернуть</button>
    </div>
</div>
</div>
<?php $dtp->AjaxComponemtStart(); ?>
    <div id='user_block' style="text-align: center; margin-top: 130px;">
    <?php
    getallvk($crar, $rar1, $dt, $dt_to, $podpsumm, $podptek, $zsp);
?>
</div>
<?php $dtp->AjaxComponemtEnd(); ?>
<?php

function getallvk($crar, $rar1, $dt, $dt_to, $podpsumm, $podptek, $zsp)
{
    $zsp->set_dt($dt);
    $zsp->set_dt_to($dt_to);
    $course=$zsp->getCourseWeek();
    $chatsIntervalVKALL=$zsp->chatsIntervalVKAll();
    $outcomtggall=round($zsp->OutcomeIntervalVKAll()*$course);
    $colorx=0;
    $zapx = array();
    $rx = 0;

    $zapsall = 0;
   foreach ($crar as $rc) {
        $zap = 0;
        $outcomeT = 0;
        foreach ($rar1 as $r) {
            $zap = $zap + (int)$r[3][$rx];
        }
        $zapx[$rx] = $zap;
        $zapsall = $zapsall + $zap;
        $rx++;
    }
    ?>
    <div style="position: fixed; z-index: 200; left: 67%; top: 57px;">
    <a href="/excelvk.php?search-to-daten=<?=$dt ?>&search-to-date=<?=$dt_to ?>" target="_blank">
        <button onclick="1010" class="btn btn-outline-success" type="button" style="margin-top: 10px;">EXCEL отчет</button>
    </a>
    </div>
    <?php
    echo "<div style='display: block; position: fixed; top: 86px; padding-top: 10px; width: 100%; background-color: rgb(242, 242, 242);' id='div_profit'>";
    echo "<table style='width: 100%; text-align: center;'>";
    echo "<tr>";
    echo "<td>";
    echo "<p style='font-size: 14px;'>Количество чатов: <br><b>".$chatsIntervalVKALL." шт</b></p>";
    echo "</td>";
    echo "<td>";
    echo "<p style='font-size: 14px;'>Количество записей: <br><b>$zapsall шт</b></p>";
    echo "</td>";
    echo "<td>";
    echo "<p style='font-size: 14px;'>Сумма расходов (р): <br><b>".round($zsp->OutcomeIntervalVKAll(),2)." руб</b></p>";
    echo "</td>";
    echo "<td>";
    echo "<p style='font-size: 14px;'>Сумма расходов (тг): <br><b>".$outcomtggall." тг</b></p>";
    echo "</td>";
    echo "<td>";
    $lidall = 0;
    if ($chatsIntervalVKALL > 0) $lidall = round($outcomtggall / $chatsIntervalVKALL);
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
    echo "<div style='float: left; margin-left: 50px;'><span style='color: white; background: #d9534f; padding: 2px 7px; margin-right: 10px;'>Мало чатов вчера: </span>";
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
        $chatsInterval=$zsp->chatsIntervalVK($rc[0]);
        $outcomeIntervalRub=$zsp->OutcomeIntervalVK($rc[0]);
        echo "<div style='width: 100%; overflow-x: auto; padding-left: 50px; padding-right: 50px; padding-top: 10px; padding-bottom: 50px;'>";
        echo "<div style='width: 100%; border: 1px solid; width: 100%; background-color: #f2F2F2; padding-bottom: 15px; padding-top: 15px;'>";
        $pricechats = 0;
        if ($chatsInterval > 0) {
            $pricechats = round($outcomeIntervalRub*$course / $chatsInterval);
        }
        $pricezapis = 0;
        if ($zapx[$rx] > 0) {
            $pricezapis = round($outcomeIntervalRub*$course / $zapx[$rx]);
        }
        echo "<h1 style='margin-left: 20%; width: 765px; background-color: #f2F2F2; margin-bottom: 0px; text-align: left; font-size: 30px; '><a id='l1' name='" . $rc[1] . "'>" . $rc[1] . "</a><span style='float: right; font-size: 20px;'>Недельный бюджет ВК <b>".$zsp->getBudgeVKCity($rc[0])."</b></span><br><span style='font-size: 13px; margin-top: -7px; padding-bottom: 4px; float: right;'>Средняя стоимость сообщения: <b>$pricechats</b> тг <span style='margin-left: 25px;'></span>Средняя стоимость записи: <b>$pricezapis</b> тг</span></h1>";
        echo "<table style='margin-left: 20%; width: 820px; border: 1px solid;  background: floralwhite;'>";
/**Заголовок**/
        echo "<tr class='T_M_Tr_table'>";
        echo "<td class='tdxh'>Дата</td>";
        echo "<td class='tdxh'>Чаты</td>";
        echo "<td class='tdxh'>Записи</td>";
        echo "<td class='tdxh'>Расх.(р)</td>";
        echo "<td class='tdxh'>Расх.(тг)</td>";
        echo "<td class='tdxh'>Подписчики</td>";
        echo "<td class='tdxh'>Прирост</td>";
        echo "</tr>";
/**-------Заголовок**/

/**Итого**/
        echo "<tr>";
        echo "<td class='tdxh'><b>Итого</b></td>";
        echo "<td class='tdxh'><b>".$chatsInterval."</b></td>";
        echo "<td class='tdxh'><b>" . $zapx[$rx] . "</b></td>";
        echo "<td class='tdxh'><b>".$outcomeIntervalRub."</b></td>";
        echo "<td class='tdxh'><b>".round($outcomeIntervalRub*(float)$course)."</b></td>";
        echo "<td class='tdxh'><b>".$podptek[$rx]."</b></td>";
        echo "<td class='tdxh'><b>".$podpsumm[$rx]."</b></td>";
        echo "</tr>";
/**-------Итого**/

        $rar11 = array_reverse($rar1);
        foreach ($rar11 as $r) {
            $outcomeVKX=$zsp->CityOutcomeDay($rc[0], $r[0][0]);
            $arraycolor=array("T_M_White", "T_M_Seriy");
            $dtseg = date('Y-m-d');
            if (strtotime($dtseg) < strtotime($r[0][0])) continue;
            echo "<tr id='disp' class='".$arraycolor[$colorx]."'>";
            echo "<td class='tdnode'>";
            echo date("d.m.Y", strtotime($r[0][0]));
            echo "</td>";
            echo "<td class='tdnode'>";
            echo (int)$zsp->chatsDayVK($rc[0], $r[0][0]);
            echo "</td>";
            echo "<td class='tdnode'>";
            echo (int)$r[3][$rx];
            echo "</td>";
            echo "<td class='tdnode'>";
            echo $outcomeVKX;
            echo "</td>";
            echo "<td class='tdnode'>";
            echo round($outcomeVKX * (float)$course);
            echo "</td>";
            echo "<td class='tdnode'>";
            echo $r[6][$rx];
            echo "</td>";
            echo "<td class='tdnode'>";
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
}
?>
<script src="comp/scrool/scroolUse.js"></script>
</body>
</html>
