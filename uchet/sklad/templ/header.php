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
    <style>
        button:not(:disabled) {
            width: 200px;
            height: 50px;
            font-size: 20px;
        }
     </style>
	 <title><?php echo $_SERVER['REQUEST_URI']; ?></title>
    <script>
        /*jslint browser:true*/
        /*global jQuery, document*/

        jQuery(document).ready(function () {
            'use strict';
            jQuery('#search-to-date').datetimepicker();
            jQuery.datetimepicker.setLocale('ru');
            jQuery('#search-to-date').datetimepicker({
                format:'Y-m-d',
                timepicker:false,
                lang:'ru'
            });
        });
    </script>
</head>
<body>
<?php
$AJAX_TIMEOUT = 3000;
$PHP_SELF = $_SERVER['PHP_SELF'];
$most="#";
function check($arg)
{
    if ($arg==$_SERVER['REQUEST_URI'])
    {
        return "btn btn-outline-success";
    } else
    {
        return "btn btn-sm align-middle btn-outline-secondary";
    }
}

$searname="";
if ($_POST['seller']!="")
{
    $searname="&sername=".$_POST['seller'];
}

if ($_POST['sdater']!="") {
    $d = date("Y-m-d", strtotime($_POST['sdater']));
    $dpri="&dpri=".$d;
} else {$d=""; $dpri=$d;}

switch ($_SERVER['REQUEST_URI'])
{
    case "/sklad/": $most="/excelSklad.php?str=sklad&nap=1".$searname; break;
    case "/sklad/index.php": $most="/excelSklad.php?str=sklad&nap=1".$searname; break;
    case "/sklad/prihod.php": $most="/excelSklad.php?str=prihod&nap=1".$searname.$dpri; break;
    case "/sklad/rashod.php": $most="/excelSklad.php?str=rashod&nap=1".$searname.$dpri; break;
}

$selname=" and name like '%".$_POST['seller']."%' ";
$seldate=" and data like '%".$d."%' ";
?>
<nav class="navbar navbar-light bg-light" style="position: fixed; width: 100%;">
<div style="width: 100%; " align="center">
    <form>
        <a href="/sklad/index.php"><button onclick="77" class="<?php echo check('/sklad/index.php'); ?>" type="button">Склад</button></a>
        <a href="/sklad/prihod.php"><button onclick="88" class="<?php echo check('/sklad/prihod.php'); ?>" type="button">Приход</button></a>
        <a href="/sklad/rashod.php"><button onclick="99" class="<?php echo check('/sklad/rashod.php'); ?>" type="button">Расход</button></a>
        <a href="<?=$most?>" target="_blank"><button onclick="1010" class="btn btn-outline-success" type="button">EXCEL отчет</button></a>
    </form>
</div>
<div style="width: 100%; " align="center">
    <form method="post">
    <table>
    <tr>
    <td style="width: 78px;">
        <h2>Поиск:</h2>
    </td>
    <td style="width: 375px;">
    По наименованию товара <input id="seller" name="seller" type="text" value="<?=$_POST['seller'] ?>" />
    </td>
<?php if ($_SERVER['REQUEST_URI']!="/sklad/" && $_SERVER['REQUEST_URI']!="/sklad/index.php") { ?>
    <td style="width: 240px;">
            По дате <input id="sdater" name="sdater" type="text" value="<?=$_POST['sdater'] ?>" />
    </td>
   <?php } ?>
    <td style="width: 80px;">
    <input type="submit" value="Искать" />
    </td>
    </tr>
    </table>
    </form>
</div>
</nav>