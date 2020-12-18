<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include($_SERVER['DOCUMENT_ROOT']."/timurnf/operator_functions.php");
include("$DOCUMENT_ROOT/timurnf/class/operatorFace.php");
$operatorFace=new operatorFace();
$operatorFace->operation=$_POST['operation'];
$operation = $_POST['operation'];
$GLOBALS['stats'] = [
  'global' => [],
  'cities' => []
];
if ($operatorFace->operation=='show_week_plan'){
    $id = intval($_POST['id']);
    $operatorFace->set_dt(date("Y-m-d", strtotime($_POST['dt'])));
    $html=$operatorFace->show_weekplan($id);
    print $html;
    exit;
}
  include("$DOCUMENT_ROOT/components/uchmanager/statistics.php");
  $dsayat1=date("d.m.Y", strtotime(date('o-\\WW')));
  $dsayat2=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6);
?>
<html>
<head>
    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <script>
        var tikusid=<?=$id ?>;
        var dts=<?=date("m/d/Y", strtotime(date('o-\\WW')));?>;
        var dtk=<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>;
    </script>
    <script src="/timurnf/scripts/operator.js"></script>
</head>
<body class="T_M_mar0">
<div class='user_block T_M_OPBLOCKX'>
    <div>
        <a href='' id='weekbefore' class='T_M_Link'>&larr;</a>
        Дата: <input type="text" id="weekpicker" class='T_M_INstyl' value='<?=$dsayat1 ?> - <?=$dsayat2 ?>'>
        <a href='' id='weekafter' class='T_M_Link'>&rarr;</a>
    </div>
    <div class='T_M_operexit'><a href='?logout=1' class='T_M_pad200px'>Выход</a></div>
</div>
<div id='user_block' class='user_block T_M_mar49'> <script> show_week_plan(<?=$id?>); </script> </div>
<br>
<div id='user_block1' class='user_block'> <script> show_user_block(<?=$id?>); </script> </div>
</body>
</html>