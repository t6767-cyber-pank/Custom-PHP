<?php
/**глобальные переменные**/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$PHP_SELF = $_SERVER['PHP_SELF'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
/**Общие подключения к классу для работы с бд**/
chdir(dirname(__FILE__));
include("./timurnf/class/mysqlwork.php");
chdir(dirname(__FILE__));
include("./timurnf/class/diagrams.php");
/**Общие подключения к компоненту листалки**/
chdir(dirname(__FILE__));
require_once("$DOCUMENT_ROOT/comp/datetimepickerweek/using.php");
$dtp=new DateTimePickerInterval($DOCUMENT_ROOT, $dt, $PHP_SELF,1);
/**Парсинг и назначение даты на недельный интервал**/
$cran = reactionStart($_POST['dt']);
if ($cran>0) {
    $dt = date("Y-m-d",strtotime($_POST['dt']));
    $dt_to= date("Y-m-d",strtotime($_POST['dt2']));
    $dtp->set_dt($dt);
    $dtp->set_dt_to($dt_to);
    $dtp->set_operation('show_masters');
} else
{
    $dt = date("Y-m-d", strtotime('-3 months'));
    $dt_to = date("Y-m-d");
    $dtp->set_dt($dt);
    $dtp->set_dt_to($dt_to);
}


$diagrams =new diagrams($dt, $dt_to);
$diagrams->access=$dtp->access;
if ($dtp->access>0)
{
    if (isset($_POST['rash'])) {$rash=$_POST['rash']; } else { $rash=1; }
} else $rash=0;
if ($rash>0) $str='rashinst'; else $str='obshinst';

if ($_POST['operation']=='show_masters') {
    $diagrams->showAllCity($rash);
    $diagrams->showAllCitySummary($rash);
    getallDiag($diagrams, $str);
    exit;
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta Http-Equiv="Cache-Control" Content="no-cache">
    <meta Http-Equiv="Pragma" Content="no-cache">
    <meta Http-Equiv="Expires" Content="0">
    <meta Http-Equiv="Pragma-directive: no-cache">
    <meta Http-Equiv="Cache-directive: no-cache">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <?php $dtp->initalHeaders(); ?>
</head>
<body>
<?php include("$DOCUMENT_ROOT/headermenu.php"); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
        </div>
        <div class="col-8 T_M_E_S_Tcenter">
            <?php
            $dtp->initalComponent();
            ?>
        </div>
        <div class="col-2">
        </div>
    </div>
</div>
<?php $dtp->AjaxComponemtStart();
getallDiag($diagrams, $str);
$dtp->AjaxComponemtEnd();

function getallDiag($diagrams, $str)
{
    $masters=new masters();
    ?>
    <div class="container-fluid T_M_DIAG_Prostor">
        <div class="row">
            <div class="col-2">
            </div>
            <div class="col-8">
                <span class='T_M_DIAG_ZAG'>Общая диаграмма </span>
                <br><span class="T_M_diag_podzagmargin">за период: </span><span class="T_M_Diag_Charts_sum"><?php echo $diagrams->getChatsAllOfAll(); ?></span>
                <?php if ($diagrams->access>0) { ?>
                    <span>, расходы: </span><span class="T_M_Diag_outcomes_sum"><?php echo $diagrams->getallOutcomesInterval(); ?></span>
                    <span>, цена за лид: </span><span class="T_M_DIAG_LID"><?php if ($diagrams->getallOutcomesInterval()!=0 && $diagrams->getChatsAllOfAll()!=0) echo round($diagrams->getallOutcomesInterval()/$diagrams->getChatsAllOfAll()); ?></span>
                <?php } ?>
                <img src='/img/diagrams/<?=$str?>/0.png?<?=rand()?>' class="T_M_DIAG_PIC_BORDER">
            </div>
            <div class="col-2">
            </div>
        </div>
    </div>
    <div class="container-fluid T_M_DIAG_Prostor">
        <div class="row">
            <div class="col-2">
            </div>
            <div class="col-8">
                <?php
                $city=$diagrams->getAllCityes();
                foreach ($city as $cid)
                {
                    $mas=$masters->MastersByCity($cid[0]);
                    $first=true;
                    ?>
                    <div class="T_M_DIAG_Prostor">
                        <span class='T_M_DIAG_ZAG'><?=$cid[1]?> <span class="T_M_DIAG_podZAG">(<?php foreach ($mas as $m) { if ($first==true) echo $m['name']; else echo ", ".$m['name']; $first=false; } ?>)</span></span>
                        <br><span class="T_M_diag_podzagmargin">за период: </span><span class="T_M_Diag_Charts_sum"><?php echo $diagrams->getallChatsSum($cid[0]); ?></span>
                        <?php if ($diagrams->access>0) { ?>
                            <span>, расходы: </span><span class="T_M_Diag_outcomes_sum"><?php echo $diagrams->getallOutcomesSum($cid[0]); ?></span>
                            <span>, цена за лид: </span><span class="T_M_DIAG_LID"><?php if ($diagrams->getallOutcomesSum($cid[0])!=0 && $diagrams->getallChatsSum($cid[0])!=0) echo round($diagrams->getallOutcomesSum($cid[0])/$diagrams->getallChatsSum($cid[0])); ?></span>
                        <?php } ?>
                        <img src='/img/diagrams/<?=$str?>/<?=$cid[0]?>.png?<?=rand()?>' class='T_M_Diag_pic'>
                    </div>
                <?php } ?>
            </div>
            <div class="col-2">
            </div>
        </div>
    </div>
<?php } ?>
</body>
</html>
