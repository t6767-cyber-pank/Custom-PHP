<?php
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
  $date1=date("Y-m-d", strtotime("-7 day"));  //-7 day
  $dayweek=date("D", strtotime($date1));
if (isset($_GET["dateN"])){ $date1=date("Y-m-d", strtotime($_GET["dateN"])); }
  switch ($dayweek)
  {
              case "Mon" : $date1=$date1; break;
              case "Tue" : $date1=date("Y-m-d", strtotime($date1)-86400); break;
              case "Wed" : $date1=date("Y-m-d", strtotime($date1)-86400*2); break;
              case "Thu" : $date1=date("Y-m-d", strtotime($date1)-86400*3); break;
              case "Fri" : $date1=date("Y-m-d", strtotime($date1)-86400*4); break;
              case "Sat" : $date1=date("Y-m-d", strtotime($date1)-86400*5); break;
              case "Sun" : $date1=date("Y-m-d", strtotime($date1)-86400*6); break;
  }

$dview1=date("d.m.Y", strtotime($date1));
?>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="dist/css/datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="dist/js/datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        body {
            background-color: #f2F2F2;
            font-family: Arial;
            font-size: 16px;
        }
        ol {
            list-style-type: none; /* Заглавные буквы */
        }
        .pills {
            margin-left: 0;
            list-style: none;
            counter-reset: li;
            font-family: calibri;
        }
        .pills li {
            padding: 10px 0;
            position: relative;
            left: 1.5em;
            margin-bottom: 0.75em;
            padding-left: 1em;
            background: #E3DEDC;
        }
        .pills li:before {
            padding: 10px 0;
            position: absolute;
            top: 0;
            bottom: 0;
            left: -1.5em;
            width: 1.875em;
            text-align: center;
            color: white;
            font-weight: bold;
            background: #D66786;
            border-bottom-left-radius: 70em;
            border-top-left-radius: 70em;
            counter-increment: li;
            content: counter(li);
        }
    </style>
</head>
<body>
<?php include("$DOCUMENT_ROOT/headermenu.php"); ?>
<div align='center'><h1>Список должников:</h1><br/></div>
<div align="center" style="margin-top: 5px;";>
    <form action="/dolg.php" method="GET">
        Дата:
        <input name="dateN" id="disabled-days"> <!-- data-range="true" data-multiple-dates-separator=" - " class="datepicker-here" -->
        <input name="m" type="hidden" value="1">
        <input name="d" type="hidden" value="1">
        <input type="submit" value="Показать">
    </form>
</div>
<script>
    var d =new Date("<?php echo $date1; ?>");
    document.getElementById('disabled-days').valueAsDate = d;
</script>
<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div align="center">
    <ol>
<?php
    $rqw = mysql_query("SELECT u.name, m.id FROM `users` u, `masters` m where m.id_master=u.id ORDER BY `m`.`sort` ASC"); //SELECT * FROM m_city order by name");
    while ($arrrqw = mysql_fetch_array($rqw)){
        $rqw2 = mysql_query("select count(w.visitors) as vis from procedures p, master_procedure_week w, `master_week` mw where p.id=w.id_procedure and w.dt='".$date1."' and w.dt=mw.dt and mw.id_master=p.`id_master` and p.`id_master`=".$arrrqw['id']."  and `mw`.`bill_checked`=0 and w.visitors>0");
        $qsravn = mysql_fetch_array($rqw2);
        if ($qsravn["vis"]>0) {
        echo "<li>".$arrrqw['name']."</li>"; }
    }
?>
    </ol>
</div></div></div></div>
<script>
    var disabledDays = [0, 2, 3, 4, 5, 6];

    $('#disabled-days').datepicker({
        onRenderCell: function (date, cellType) {
            if (cellType == 'day') {
                var day = date.getDay(),
                    isDisabled = disabledDays.indexOf(day) != -1;

                return {
                    disabled: isDisabled
                }
            }
        }
    });
    $('#disabled-days').datepicker({
        todayButton: new Date()
    });

    document.getElementById("disabled-days").value = "<?php echo $dview1; ?>";
</script>
</body>
</html>
