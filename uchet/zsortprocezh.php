<?php
/**Общие подключения к базе**/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
/**------Общие подключения к базе**/

/**Настройки аякса**/
$AJAX_TIMEOUT = 20000;
$operation = $_POST['operation'];
/**-----Настройки аякса**/

/**Переменные массивов обработки**/
$rar1 = array(); // Массив сборщик всякого общего барахла
$drar = array(); // Массив дат
/**-----Переменные массивов обработки**/

/**Рабочие переменные**/
$crarcount=0;
$coursex=1;
/**-------Рабочие переменные**/

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

/**Парсинг и назначение даты на недельный интервал**/
if (isset($_POST['dt'])) {
    // выводим неделю исходя из компонента
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
} else
{
    // выводим дату при первой загрузки компонента
    $dt = date("Y-m-d");
    $operation='show_masters_first';
    $dt=get_monday($dt);
}
// Подгоняем воскресение
$dt_to = date('Y-m-d',strtotime($dt)+3600*24*6);
/**-------Парсинг и назначение даты на недельный интервал**/

/**Наполним массив обработки дат**/
$q = "select data from timer where data between '$dt' and '$dt_to' order by  data asc";
$r = mysql_query($q);
while ($a = mysql_fetch_array($r)) {
    array_push($drar, $a['data']);
}

/**-------Наполним массив обработки дат**/

$podporiginals = array(); // Массив оригинала чатов
$podpsumm = array(); // Массив суммы подписчиков
$podptek = array(); // массив текущих подписок

/** Вывод массива оригинала чатов **/
$dtm=date("Y-m-d", strtotime($dt)-3600*24); // дата воскресения прошлой недели
    $qxx = "select userg from cityimportvkezh where id_mcity=1 and data='".$dtm."' order by  data";
    $rxx1 = mysql_query($qxx);
    $ax1 = mysql_fetch_array($rxx1);
    $podporiginals[0]=(int)$ax1['userg'];
/**-------Вывод оригинала чатов**/

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

    $car[$jto]=1;
        $jto++;
//контакты
        $qxx = "select sum(contactsvk) as cvk from ezh_city_day where id_city=1 and dt='".$dar."' order by  dt";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar2[$i]=$ax1['cvk'];
            $i++;
        }
//Расходы VK
        $qxx = "select sum(outcome) as outcome from cityimportvkezh where id_mcity=1 and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar5[$ixgq]=round((float)$ax1['outcome'],2);
            $ixgq++;
        }

//Подписки VK
        $qxx = "select userg from cityimportvkezh where id_mcity=1 and data='".$dar."' order by  data";
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


    $rar3[1]=$car;
    $rar3[2]=$rar2;
    $rar3[3]=$rar4;
    $rar3[4]=$rar5;
    $rar3[6]=$rar6;
    $rar3[7]=$rar7;
    $rar1[$j]=$rar3;
    $j++;
}


if ($operation=='show_masters'){
    getallvk($crar, $rar1, $dt, $dt_to, $podporiginals, $podpsumm, $podptek, $budgetcity);
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
    <link href="dist/css/main.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/datetimepicker-master/jquery.datetimepicker.css"/>
    <script src="/datetimepicker-master/jquery.js"></script>
    <script src="/datetimepicker-master/jquery.datetimepicker.js"></script>
    <script src="/datetimepicker-master/build/jquery.datetimepicker.full.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/moment.min.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <script type="text/javascript" src="/js/jquery.daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/js/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="/style.css">
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

        $(function() {
            var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
            var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');

            var selectCurrentWeek = function() {
                window.setTimeout(function () {
                    $('#weekpicker').datepicker('widget').find('.ui-datepicker-current-day a').addClass('ui-state-active')
                }, 1);
            }

            $('#weekpicker').datepicker( {
                showOtherMonths: true,
                selectOtherMonths: true,
                onSelect: function(dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
                    var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                    $('#weekpicker').val($.datepicker.formatDate( dateFormat, startDate, inst.settings )+' - '+$.datepicker.formatDate( dateFormat, endDate, inst.settings ));
                    show_user_block();
                    selectCurrentWeek();
                },
                beforeShow: function() {
                    selectCurrentWeek();
                },
                beforeShowDay: function(date) {
                    var cssClass = '';
                    if(date >= startDate && date <= endDate)
                        cssClass = 'ui-datepicker-current-day';
                    return [true, cssClass];
                },
                onChangeMonthYear: function(year, month, inst) {
                    selectCurrentWeek();
                }
            }).datepicker('widget').addClass('ui-weekpicker');
            $( "#weekpicker" ).datepicker( $.datepicker.regional[ "ru" ] );

            // листалка влево

            $('#weekbefore').click(function(){
                s = $('#weekpicker').val().replace(/ .*/,'');
                arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                d = new Date(arr[2]+'/'+arr[1]+'/'+arr[3]);
                t = d.getTime();
                t1 = t-7*24*3600*1000;
                d1 = new Date(t1);
                startDate = d1;
                str1 = ('0'+d1.getDate()).slice(-2)+'.'+('0'+parseInt(d1.getMonth()+1)).slice(-2)+'.'+d1.getFullYear();
                t2 = t-24*3600*1000;
                d2 = new Date(t2);
                endDate = d2;
                str2 = ('0'+d2.getDate()).slice(-2)+'.'+('0'+parseInt(d2.getMonth()+1)).slice(-2)+'.'+d2.getFullYear();
                str = str1+' - '+str2;
                $('#weekpicker').val(str);
                show_user_block();
                return false;
            });

            // листалка вправо

            $('#weekafter').click(function(){
                s = $('#weekpicker').val().replace(/ .*/,'');
                arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                d = new Date(arr[2]+'/'+arr[1]+'/'+arr[3]);
                t = d.getTime();
                t1 = t+7*24*3600*1000;
                d1 = new Date(t1);
                startDate = d1;
                str1 = ('0'+d1.getDate()).slice(-2)+'.'+('0'+parseInt(d1.getMonth()+1)).slice(-2)+'.'+d1.getFullYear();
                t2 = t+13*24*3600*1000;
                d2 = new Date(t2);
                endDate = d2;
                str2 = ('0'+d2.getDate()).slice(-2)+'.'+('0'+parseInt(d2.getMonth()+1)).slice(-2)+'.'+d2.getFullYear();
                str = str1+' - '+str2;
                $('#weekpicker').val(str);
                show_user_block();
                return false;
            });

// ВЫделить линию на вводе даты

            $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
            $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
        });

        // Обработчик при смене даты
        function show_user_block(){
            var oper='show_masters';
            dt = $("#weekpicker").val().replace(/ .*/,'');
            $("#loader").show();
            $.ajax({
                type:'POST',
                url:'<?=$PHP_SELF?>',
                data:{
                    dt:dt,
                    operation:'show_masters',
                },
                timeout:<?=$AJAX_TIMEOUT?>,
                success:function(html){
                    $('#user_block').html(html);
                },
                error:function(html){
                    alert('Ошибка соединения!');
                }
            });
        }

    </script>
    <title><?php echo $_SERVER['REQUEST_URI']; ?></title>
</head>
<body style="font-size: 12px;">
<div id="menu" class="menu" style="top:0; width: 100%; position: fixed; padding-bottom:4px;display: -webkit-box;padding-top:4px; background-color:white;" align="center">
    <ul style="list-style-type: none; margin: 0; padding: 0; ">
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" href="/zsortproc.php">Понедельная разбивка BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: blue;" href="/zsortprocmes.php">Разбивка с выбором интервала BEAUTY</a></li>
        <li style="float: left;"> <a class="button7" style="display: block; text-align: center; margin-right: 10px !important; font-family: Arial; font-size: 16px; color: black;" class="scroll-to-this" href="/zsortprocezh.php">Понедельная разбивкак Еж</a></li>
    </ul>
</div>
<div align="center" style="position: fixed; width: 100%; z-index: 100; top: 35px; background-color: #f2F2F2; padding: 3px;">
    <div style="text-align: center;">
        <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
        Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
        <a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
        <button onclick="shownone();" style="text-decoration:none;border-bottom:1px dashed; margin-right: 20px; margin-right: 20px; background: none; border-left: none; border-right: none; border-top: none; font-size: 12px;" id="a_profit">Свернуть</button>
    </div>
</div>
</div>
<div id='user_block' style="text-align: center; margin-top: 130px;">
    <?php
    if ($operation=='show_masters_first'){
        getallvk($crar, $rar1, $dt, $dt_to, $podporiginals, $podpsumm, $podptek, $budgetcity);
    }
    ?>
</div>
<?php

function getallvk($crar, $rar1, $dt, $dt_to, $podporiginals, $podpsumm, $podptek, $budgetcity)
{
    $pr_order=new pr_order();
    $pr_order->set_dt($dt);
    $ecw=new ezh_city_week();
    $ecw->set_dt($dt);
    $budgetVk=$ecw->CityesBudgetWeek();
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
    ?>
    <div style="position: fixed; z-index: 200; left: 67%; top: 28px;">
        <a href="/excelvk.php?search-to-daten=<?=$dt ?>&search-to-date=<?=$dt_to ?>" target="_blank">
            <button onclick="1010" class="btn btn-outline-success" type="button" style="margin-top: 10px;">EXCEL отчет</button>
        </a>
    </div>
    <?php
    echo "<div style='display: block; position: fixed; top: 60px; padding-top: 10px; width: 100%; background-color: rgb(242, 242, 242);' id='div_profit'>";
    echo "<table style='width: 100%; text-align: center;'>";
    echo "<tr>";
    echo "<td>";
    echo "<p style='font-size: 14px;'>Количество чатов: <br><b>$chatsall шт</b></p>";
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
    echo "</tr>";
    echo "</table>";
    echo "</div>";
    ?>

    <?php

    $rx = 0;
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
        echo "<h1 style='margin-left: 20%; width: 765px; background-color: #f2F2F2; margin-bottom: 0px; text-align: left; font-size: 30px; '><a id='l1' >Ёж принес</a><span style='float: right; font-size: 20px;'>Недельный бюджет ВК <b>$budgetVk</b></span></h1>";
        echo "<table style='margin-left: 20%; width: 820px; border: 1px solid;  background: floralwhite;'>";
        /**Заголовок**/
        echo "<tr style='background-color: aliceblue; font-size: 19px;'>";
        echo "<td align='center' class='tdxh'>Дата</td>";
        echo "<td align='center' class='tdxh'>Чаты</td>";
        echo "<td align='center' class='tdxh'>Расх.(р)</td>";
        echo "<td align='center' class='tdxh'>Расх.(тг)</td>";
        echo "<td align='center' class='tdxh'>Подписчики</td>";
        echo "<td align='center' class='tdxh'>Прирост</td>";
        echo "<td align='center' class='tdxh'>Сумма заказа</td>";
        echo "</tr>";
        /**-------Заголовок**/

        /**Итого**/
        echo "<tr>";
        echo "<td align='center' class='tdxh'><b>Итого</b></td>";
        echo "<td align='center' class='tdxh'><b>" . $chatsx[$rx] . "</b></td>";
        echo "<td align='center' class='tdxh'><b>" . $outcomerub[$rx] . "</b></td>";
        echo "<td align='center' class='tdxh'><b>" . round($outcometg[$rx]) . "</b></td>";
        echo "<td align='center' class='tdxh'><b>".$podptek[$rx]."</b></td>";
        echo "<td align='center' class='tdxh'><b>".$podpsumm[$rx]."</b></td>";
        $pr_order->set_dt($pr_order->get_monday());
        $pr_order->set_dt_to($pr_order->get_sunday());
        echo "<td align='center' class='tdxh'><b>".$pr_order->getSumsPeriodVkOrdersBezDost()."</b></td>";
        echo "</tr>";
        /**-------Итого**/

        $rar11 = array_reverse($rar1);
        foreach ($rar11 as $r) {
            $arraycolor=array("background-color: white;", "background-color: #f2f2f2;");
            $dtseg = date('Y-m-d');
            if (strtotime($dtseg) < strtotime($r[0][0])) continue;
            echo "<tr id='disp' style='".$arraycolor[$colorx]."'>";
            echo "<td class='tdnode' align='center'>";
            echo date("d.m.Y", strtotime($r[0][0]));
            echo "</td>";

            echo "<td class='tdnode' align='center'>";
            echo (int)$r[2][$rx];
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
            echo "<td class='tdnode' align='center'>";
            $pr_order->set_dt($r[0][0]);
            echo $pr_order->getSumsVkOrdersBezDost();
            echo "</td>";
            echo "</tr>";
            $colorx++;
            if ($colorx>1) $colorx=0;
        }
        echo "</table></div>";
        echo "</div>";
        $rx++;
}
?>

<script>
    (function($, window) {
        var adjustAnchor = function() {

            var $anchor = $(':target'),
                fixedElementHeight = 180;

            if ($anchor.length > 0) {

                $('html, body')
                    .stop()
                    .animate({
                        scrollTop: $anchor.offset().top - fixedElementHeight
                    }, 200);

            }

        };

        $(window).on('hashchange load', function() {
            adjustAnchor();
        });

    })(jQuery, window);
</script>
<style>
    .container.fadeout {
        opacity: 0;
    }
</style>
</body>
</html>
