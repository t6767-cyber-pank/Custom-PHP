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
$usersCRM=new usersCRM();
$uid=$_COOKIE["iduser"];
$res=$usersCRM->getUserbyID($uid);
$access=0;
switch ($res["type"])
{
    case 3: $access=1; break;
    case 1: $access=2; break;
    case 2: $access=3; break;
}
$access=4;
$cran = reactionStart($_POST['dt']);
if ($cran>0) {
    $dt = date("Y-m-d",strtotime($_POST['dt']));
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dtp->set_operation('show_masters');
    $monitor=new monitor($dt, $access);
    $monitor->set_dt($monitor->get_sundayPars($dt));
} else
{
    $dt = date("Y-m-d");
    /**Создадим объект листалки**/
    $dtp=new DateTimePickerWeeks($DOCUMENT_ROOT, $dt, $PHP_SELF);
    $dt=$dtp->get_monday();
    $monitor=new monitor($dt, $access);
    $monitor->set_dt($monitor->get_sundayPars($dt));
}

if (isset($_POST['status'])){
    $comp=$_POST['idcomp'];
    $status=$_POST['status'];
    $monitor->setZadachaStatus($comp, $status);
}

$cran = reactionStart($_POST['operation']);
if ($cran>0 && $_POST['operation']=='updatePos') {
    $monitor->setPosit($_POST['idcomp'], $_POST['strstyle']);
    startMonitor($monitor);
    exit;
}
if ($cran>0 && $_POST['operation']=='show_masters') {
    startMonitor($monitor);
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
switch ($monitor->access){
    case 1: echo $InterFC->getTopBlockStart(); echo $InterFC->GetMenue(); break;
    case 2: echo $InterFC->getTopBlockStart("T_M_Top_block_manager"); echo $InterFC->GetMenue(3); break;
    case 3: echo $InterFC->getTopBlockStart("T_M_Top_block_marketol"); echo $InterFC->GetMenue(2); break;
    case 4: echo $InterFC->getTopBlockStart(); echo $InterFC->GetMenue(); break;
    default:
        echo $InterFC->getTopBlockStart(); break;
}
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
startMonitor($monitor);
$dtp->AjaxComponemtEnd();
function startMonitor($monitor)
{
    ?>
    <div class="container-fluid T_M_Monitor_pad">
    <div class="row" id="conteinerxxx">
    <?php
    echo $monitor->initalize();

    ?>
    </div>
    </div>
    <?php
}
?>
</body>
</html>
