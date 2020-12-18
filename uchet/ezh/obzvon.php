<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$PHP_SELF = $_SERVER['PHP_SELF'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
include("$DOCUMENT_ROOT/timurnf/class/obzvon.php");
$obzvon=new obzvon();
$obzvon->set_dt(date("Y-m-d"));
if (isset($_POST["operation"]))
{
    $obzvon->operation=$_POST["operation"];
    if(isset($_POST["call"]))
    {
        $pr_zvon = new pr_zvon($_POST["idclient"]);
        $pr_zvon->set_dt($_POST["dt"]);
        $pr_zvon->setZvon($_POST["status"], $_POST["tema"]);
    }
    echo $obzvon->ajaxStart();
    exit;
}
else
{
    $obzvon->operation="1rnedavno";
}
?>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
    <script src="/ezh/obzvon.js"></script>
</head>
<body>
<div id="ajax">
<?php
echo $obzvon->ajaxStart();
?>
</div>
</body>
</html>