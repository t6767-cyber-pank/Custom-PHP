<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
$id_city = intval($_REQUEST['city']); 
$inc_dt = "";
if (!empty($_REQUEST['dt'])){
  $inc_dt = $_REQUEST['dt'];
}
$r = mysql_query("select *,unix_timestamp(dt_otgruzka)dt from pr_city where id=$id_city");
if (mysql_num_rows($r)==0){
  $city = '';
  $dt = '';
  $dt0 = '';
}else{
  $a = mysql_fetch_array($r);
  $city = $a['name']; 
  $dt = empty($inc_dt) ? date('d.m.Y',$a['dt']) : date('d.m.Y',strtotime($inc_dt));	
  $dt0 = date('Y-m-d',$a['dt']);
}
if ($_REQUEST['pdf']==1){ 
  $html = container($id_city,$city,$dt,$dt0,$inc_dt);

  include("$DOCUMENT_ROOT/mpdf/mpdf.php");
//  $mpdf=new mPDF('utf-8', 'A4', '22', '', 10, 10, 7, 7, 10, 10);
//  $mpdf = new mPDF();
  $mpdf=new mPDF('utf-8', 'A4', '12');
  //$mpdf->charset_in = 'cp1251';
  //$mpdf->charset_in = 'utf-8';

  $mpdf->WriteHTML($html);
  $mpdf->Output(Utf8ToWin("$city, заказ для склада от $dt.pdf"),"D");
  exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/moment.min.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <script type="text/javascript" src="/js/jquery.daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/js/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="/style_program.css">
<style>
@media print {
  body > * {display:none;}
  #container {display:block;}
}
</style>
</head>
<body style='position:relative;'>
<div id='buttons' style='position:fixed;top:0;left:0;height:30px;padding:10px;border-bottom:1px solid;z-index:100000;width:100%;background-color:#DDDDDD;'>
<input type='button' value='Печать' onclick='window.print();'>
<form method='GET' action='<?=$_SERVER['PHP_SELF']?>' style='display:inline;'>
<input type='submit' value='Сохранить в pdf'>
<input type='hidden' name='city' value='<?=$id_city?>'>
<input type='hidden' name='pdf' value='1'>
</form>
</div>
<div id='container' style='background-color:white;position:absolute;top:42px;left:0;border:1px solid;padding:10px;'> 
<?=container($id_city,$city,$dt,$dt0,$inc_dt)?>
</div>
</body>
</html>
<? 
function container($id_city,$city,$dt,$dt0,$inc_dt){
  ob_start();
?>
 <span style='margin-left:50px;border-bottom:1px solid;display:inline-block; font-weight:bold;'><?=$city?>, заказ для склада от <?=$dt?></span><br/>
 <div id='main' style='padding:10px;font-size:18px;'>
-----------------------<br/>
<?
  $fsum = 0;
  $fsum_d = 0;
  $cnt = 0;
  $done = 0;
  $query = "select t.name,sum(ot.number)cnt from pr_order o, pr_order_tovar ot,pr_tovar t where ot.id_tovar=t.id and ot.id_order=o.id and ot.from_rest=0 and o.dt<='$dt0' and o.id_city=$id_city and o.done='$done' group by t.id order by t.name";
  if (!empty($inc_dt)){
    $dt0 = $inc_dt;
    $done = 1;
    $query = "select t.name,sum(ot.number)cnt from pr_order o, pr_order_tovar ot,pr_tovar t where ot.id_tovar=t.id and ot.id_order=o.id and ot.from_rest=0 and o.dt='$dt0' and o.id_city=$id_city and o.done=$done group by t.id order by t.name";
  }
  $r = mysql_query($query);
  while($a = mysql_fetch_array($r)){
    $name = $a['name'];
    $cnt = $a['cnt'];
?>
<?=$cnt?> <?=$name?><br>
<?
  }
?>
-----------------------
 </div>
 <div style='padding-left:10px;font-size:18px;'> 
Сформировано: <?=date('d.m.Y')?> в <?=date('H:m')?>
 </div>
<?
  $s = ob_get_contents();
  ob_end_clean();
  return $s;
}
function Utf8ToWin($fcontents) {
    $out = $c1 = '';
    $byte2 = false;
    for ($c = 0;$c < strlen($fcontents);$c++) {
        $i = ord($fcontents[$c]);
        if ($i <= 127) {
            $out .= $fcontents[$c];
        }
        if ($byte2) {
            $new_c2 = ($c1 & 3) * 64 + ($i & 63);
            $new_c1 = ($c1 >> 2) & 5;
            $new_i = $new_c1 * 256 + $new_c2;
            if ($new_i == 1025) {
                $out_i = 168;
            } else {
                if ($new_i == 1105) {
                    $out_i = 184;
                } else {
                    $out_i = $new_i - 848;
                }
            }
            // UKRAINIAN fix
            switch ($out_i){
                case 262: $out_i=179;break;// і
                case 182: $out_i=178;break;// І 
                case 260: $out_i=186;break;// є
                case 180: $out_i=170;break;// Є
                case 263: $out_i=191;break;// ї
                case 183: $out_i=175;break;// Ї
                case 321: $out_i=180;break;// ґ
                case 320: $out_i=165;break;// Ґ
            }
            $out .= chr($out_i);
            
            $byte2 = false;
        }
        if ( ( $i >> 5) == 6) {
            $c1 = $i;
            $byte2 = true;
        }
    }
    return $out;
}
mysql_close($conn);
?>