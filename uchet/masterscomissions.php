<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body style="background-color: #f2F2F2; font-family: Arial; font-size: 16px;">
<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
include("$DOCUMENT_ROOT/timurnf/class/InterFC.php");
$masters=new masters();
$procs=new procedures();
$mast=$masters->sellAllMastersShown();
$InterFC=new InterFC(800);
echo $InterFC->GetMenue(3);
?>
<div style="text-align: -webkit-center;">
<?php
foreach ($mast as $mst)
{
    echo "<h1>".$mst["name"]."</h1>";
    $proc=$procs->getProcsMaster($mst["id"]);
    echo "<table>";
    foreach ($proc as $pr) {
        echo "<tr>";
        echo "<td width='100px'>";
        echo "Процедура";
        echo "</td>";
        echo "<td>";
        echo '<input type="text" class="p_name" style="width: 250px; margin-right: 10px;" value="'.$pr["name"].'" readonly>';
        echo "</td>";
        echo "<td width='50px'>";
        echo "Цена";
        echo "</td>";
        echo "<td>";
        echo '<input type="text" style="width:50px;  margin-right: 10px;" class="p_price" value="'.$pr["price"].'" readonly>';
        echo "</td>";
        echo "<td width='85px'>";
        echo "Комиссия";
        echo "</td>";
        echo "<td>";
        echo '<input type="text" style="width:50px;  margin-right: 10px;" class="p_price" value="'.$pr["comission"].'" readonly>';
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
</div>
</body>
</html>
