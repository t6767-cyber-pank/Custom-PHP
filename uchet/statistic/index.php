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
echo $InterFC->getTopBlockStart("T_M_Top_block_marketol");
echo $InterFC->GetMenue(2);
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

    $managerMark=new managerMark();
    $managerMark->set_dt($dt);

    $managerMark3=new managerMark();
    $managerMark3->set_dt($dt2);
    ?>
    <div class="container-fluid T_M_Monitor_pad">
    <div class="row" id="conteinerxxx">
    <?php
    echo "<div class='container-fluid' style='position: fixed; left: 20px; z-index: 1000; background: #f2F2F2; top: 105px;'>";
    echo "<div class='row'>";
    echo "<div class='col-1' style='text-align: center; border: 1px solid'>Прощлая</div>";
    echo "<div class='col-1'>Чаты</div><div class='col-1'>Записи</div><div class='col-1'>Затраты</div><div class='col-1'>График</div>";
    echo "<div class='col-1'></div>";
    echo "<div class='col-1' style='text-align: center; border: 1px solid'>Текущая</div>";
    echo "<div class='col-1'>Чаты</div><div class='col-1'>Записи</div><div class='col-1'>Затраты</div><div class='col-1'>График</div>";
    echo "<div class='col-1'></div>";
    echo "</div>";
    echo "</div>";

    foreach ($cityes as $city) {
        echo "<div class='container-fluid' style='margin-top: 20px; margin-bottom: 20px;'>";
        echo "<div class='row'>";
        echo "<div class='col-1'></div>";
        echo "<div class='col-4'>";
        echo "<h6 style='font-weight: 700'>".$city['name']."</h6>";
        $charts=$m_city_day2->chats($city['id']);
        $original=(int)$m_city_day2->chatsoriginal($city['id']);
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
        $master_procedure_day->set_dt($dt2);
        $procedurescount=0;

        $master_week=new master_week();
        $master_week->set_dt($dt2);


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
        echo "<table style='width: 100%; border: 1px solid;'><tr>";
        echo "<td style='width: 25%; text-align: center'>$sumchats</td>";
        echo "<td style='width: 25%; text-align: center'>$procedurescount</td>";
        echo "<td style='width: 25%; text-align: center'>".(int)$master_week->getOutcomesInsta($city['id'], $dt2)."</td>";
        $colorX2=$managerMark3->getMark($city['id']);
        echo "<td style='width: 25%; text-align: center'><div style='width: 60px; height: 20px; background: $colorX2; margin: 5px;'></div></td>";
        echo "</tr></table>";
        echo "</div>";

        echo "<div class='col-2'></div>";


        echo "<div class='col-4'>";
        echo "<h6 style='font-weight: 700'>".$city['name']."</h6>";
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
        echo "<table style='width: 100%; border: 1px solid;'><tr>";
        echo "<td style='width: 25%; text-align: center'>$sumchats</td>";
        echo "<td style='width: 25%; text-align: center'>$procedurescount</td>";
        echo "<td style='width: 25%; text-align: center'>".(int)$master_week->getOutcomesInsta($city['id'], $dt)."</td>";
        $colorX=$managerMark->getMark($city['id']);
        echo "<td style='width: 25%; text-align: center'><div style='width: 60px; height: 20px; background: $colorX; margin: 5px;'></div></td>";
        echo "</tr></table>";
        echo "</div>";

        echo "</div>";
        echo "<div class='col-1'></div>";
        echo "</div>";
    }
    ?>
    </div>
    </div>
    <?php
}
?>
</body>
</html>
