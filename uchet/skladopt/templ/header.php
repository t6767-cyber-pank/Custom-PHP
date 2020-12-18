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

switch ($_SERVER['REQUEST_URI'])
{
    case "/skladopt/": $most="/excelSklad.php?str=sklad&nap=2"; break;
    case "/skladopt/index.php": $most="/excelSklad.php?str=skladopt&nap=2"; break;
    case "/skladopt/prihod.php": $most="/excelSklad.php?str=prihod&nap=2"; break;
    case "/skladopt/rashod.php": $most="/excelSklad.php?str=rashod&nap=2"; break;
}

?>
<nav class="navbar navbar-light bg-light" style="position: fixed; width: 100%;">
    <form class="form-inline">
        <a href="/skladopt/index.php"><button onclick="77" class="<?php echo check('/skladopt/index.php'); ?>" type="button">Склад</button></a>
        <a href="/skladopt/prihod.php"><button onclick="88" class="<?php echo check('/skladopt/prihod.php'); ?>" type="button">Приход</button></a>
        <a href="/skladopt/rashod.php"><button onclick="99" class="<?php echo check('/skladopt/rashod.php'); ?>" type="button">Расход</button></a>
    </form>
    <a href="<?=$most?>"><button onclick="1010" type="button">EXCEL отчет</button></a>
</nav>