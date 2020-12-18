<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
/**Общие подключения к компоненту листалки**/
$dt=date("Y-m-d");
$logs=new logs(0, $dt);
$mast=$_GET['mast'];
?>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <script src="/monitoring/monitor.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container-fluid T_M_Monitor_padding" style="display: none">
    <div class="row">
        <div class='col-5'></div>
        <div class='col-3'>

        </div>
        <div class='col-4'></div>
    </div>
</div>
<?php
startMonitor($logs, $mast);

function startMonitor($logs, $mast)
{
    $usersCRM=new usersCRM();
    ?>
    <div class="container-fluid T_M_Monitor_pad">
            <?php
           foreach ($logs->showLog($mast) as $log)
            {
     
				echo "<div class='row' style='border: 1px solid; padding: 2px; margin: 5px'>";
                echo "<div class='col'>";
                echo $usersCRM->getUserbyID($log['id_user'])['name'].'<br>';
                echo "</div>";
                echo "<div class='col'>";
                echo $log['description'].'<br>';
                echo "</div>";
                echo "<div class='col'>";
                echo "дата ".$log['dt'];
                echo "   ip ".$log['ip'];
                echo "</div>";
                //echo "<div class='col-4'>";
                //echo $log['useragent'].'<br>';
                //echo $log['query'].'<br>';
                //echo "</div>";
                echo "</div>";
            }
            ?>
    </div>
    <?php
}
?>
</body>
</html>
