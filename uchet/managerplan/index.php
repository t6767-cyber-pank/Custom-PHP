<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
include("$DOCUMENT_ROOT/timurnf/class/monitoring.php");
include("$DOCUMENT_ROOT/timurnf/class/InterFC.php");
include("$DOCUMENT_ROOT/timurnf/class/CompRebuild.php");
/**Общие подключения к компоненту листалки**/
require_once("$DOCUMENT_ROOT/comp/datetimepickerweek/using.php");
/**Парсинг и назначение даты на недельный интервал**/
$cran = reactionStart($_POST['dt']);
if ($cran>0) {
    $dt = date("Y-m-d",strtotime($_POST['dt']));
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dtp->set_operation('show_masters');
} else
{
    $dt = date("Y-m-d");
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dt=$dtp->get_monday();
}
if ($cran>0 && $_POST['operation']=='show_masters') {
    startMonitor($dt);
    exit;
}

?>
<html>
<head>
        <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <script src="/monitoring/monitor.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $dtp->initalHeaders(); ?>
    <style>
        tr:nth-child(2n) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<?php
$InterFC=new InterFC($_REQUEST['razdel']);
echo $InterFC->getTopBlockStart();
echo $InterFC->GetMenue(3);
?>
<div class="container-fluid T_M_Monitor_padding">
    <div class="row" style="text-align: center">
<?php
$dtp->initalComponent();
?>
    </div>
</div>
<?php
echo $InterFC->getTopBlockEnd();
$dtp->AjaxComponemtStartNONE();
startMonitor($dt);
$dtp->AjaxComponemtEnd();
function startMonitor($dt)
{
    $dt2=date("Y-m-d", strtotime($dt." -7 days"));
    $m_city=new m_city();
    $cityes=$m_city->allCitiesMasters();

    $m_city_day= new m_city_day();
    $m_city_day->set_dt($dt);
    $m_city_day->set_dt_to($m_city_day->get_sunday());

    $m_city_day2= new m_city_day();
    $m_city_day2->set_dt($dt2);
    $m_city_day2->set_dt_to($m_city_day2->get_sunday());
    ?>
    <div class="container-fluid T_M_Monitor_pad">
    <div class="row" id="conteinerxxx">
    <?php
    echo "<div class='container' style='margin-top: 20px; margin-bottom: 20px;'>";
    echo "<div class='row'>";
    echo "<div class='col-2'></div>";
    echo "<div class='col-8'>";
    echo "<table style='width: 100%'><tr>";
    echo "<td style='width: 25%; text-align: left'>Позапрошлая неделя</td>";
    echo "<td style='width: 25%; text-align: left'>Прошлая неделя</td>";
    echo "<td style='width: 25%; text-align: center'>Город</td>";
    echo "<td style='width: 25%; text-align: left'>Новая неделя</td>";
    echo "</tr></table>";
    echo "</div>";
    echo "<div class='col-2'></div>";
    echo "</div>";
    echo "</div>";


    foreach ($cityes as $city) {
        echo "<div class='container' style='margin-top: 20px; margin-bottom: 20px;'>";
        echo "<div class='row'>";
        echo "<div class='col-2'></div>";


        echo "<div class='col-8'>";
        $charts=$m_city_day->chats($city['id']);
        $original=(int)$m_city_day->chatsoriginal($city['id']);
        $sumchats=0;
        foreach ($charts as $chart)
        {
            $sumchats=$sumchats+$chart['lidfit']+((int)$chart['chats']-$original);
            $original=$chart['chats'];
        }

        $masters=new masters();
        $thismasters=$masters->MastersByCity($city['id']);
        $procedures =new procedures();
        $master_procedure_day=new master_procedure_day();
        $master_procedure_day->set_dt($dt);
        $procedurescount=0;

        $master_week=new master_week();
        $master_week->set_dt($dt);


        foreach ($thismasters as $mast)
        {
            $procs=$procedures->getProcsMasterInScores($mast['id']);
            foreach ($procs as $pr) {
                $pcount=$master_procedure_day->recs($mast['id'], $pr["id"]);
                foreach ($pcount as $pc)
                {
                    $procedurescount+=(int)$pc["records"];
                }
            }
        }
        $managerMark=new managerMark();
        $managerMark->set_dt($dt);

        $dt2x=date("y-m-d", strtotime("$dt - 7 days"));
        $managerMark2=new managerMark();
        $managerMark2->set_dt($dt2x);
        $colorX2=$managerMark2->getMark($city['id']);

        $dt3x=date("y-m-d", strtotime("$dt - 14 days"));
        $managerMark3=new managerMark();
        $managerMark3->set_dt($dt3x);
        $colorX3=$managerMark3->getMark($city['id']);

        $colorX=$managerMark->getMark($city['id']);
        echo "<table style='width: 100%'><tr>";
        echo "<td style='width: 25%; text-align: center'><div style='width: 150px; height: 20px; background: $colorX3; margin: 5px;'></td>";
        echo "<td style='width: 25%; text-align: center'><div style='width: 150px; height: 20px; background: $colorX2; margin: 5px;'></td>";
        echo "<td style='width: 25%; text-align: center'><h6 style='font-weight: 700'>".$city['name']."</h6></td>";
        echo "<td style='width: 25%; text-align: center'>";
        echo "<select style=\"background: $colorX; color: $colorX; width:200px\" id=\"sel".$city['id']."\" onchange=\"ttt('sel".$city['id']."', ".$city['id']." , '$dt')\">
            <option style=\"background: #ffffff; color: #ffffff\" value=\"#ffffff\"></option>
            <option style=\"background: #7bf518; color: #7bf518\" value=\"#7bf518\"></option>
            <option style=\"background: #ff4800; color: #ff4800\" value=\"#ff4800\"></option>
            <option style=\"background: #fcff00; color: #fcff00\" value=\"#fcff00\"></option>
            </select>";
        echo "</td>";
        echo "</tr></table>";
        echo "</div>";

        echo "</div>";
        echo "<div class='col-2'></div>";
        echo "</div>";
    }
    ?>
    </div>
    </div>
    <?php
}
?>
<script>
    function sendColor(idcity, color, dt) {
        $.ajax({
            type:'POST',
            url:'/managerplan/poster.php',
            data:{
                idcity:idcity,
                color: color,
                dt: dt
            },
            timeout:20000,
            success:function(html){
            }
        });
    }

    function ttt(id, cid, dt)
    {
        var color=document.getElementById(id).value;
        document.getElementById(id).style.background=document.getElementById(id).value;
        document.getElementById(id).style.color=document.getElementById(id).value;
        sendColor(cid, color, dt);
    }
</script>
</body>
</html>
