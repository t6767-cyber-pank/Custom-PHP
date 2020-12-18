<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
$id_city = intval($_REQUEST['city']);
$dt = $_REQUEST['dt'];
$m = array();
if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/',$dt,$m)){
  $dt0 = $dt;
  $dt = $m[3].'.'.$m[2].'.'.$m[1];
}
$done = intval($_REQUEST['done']);
$r = mysql_query("select *,unix_timestamp(dt_otgruzka)dt from pr_city where id=$id_city");
if (mysql_num_rows($r)==0){
  $city = '';
  $dostavka = 0;
  $fact_dostavka = 0;
  $sum_less = 0;
  $price_less = 0;
  $sum_more = 0;
  $price_more = 0; 
}else{
  $a = mysql_fetch_array($r);
  $city = $a['name'];
  $dostavka = $a['dostavka'];
  $fact_dostavka = $a['fact_dostavka'];
  $sum_less = $a['sum_less'];
  $price_less = $a['price_less'];
  $sum_more = $a['sum_more'];
  $price_more = $a['price_more'];
  if($dt==''){
    $dt = date('d.m.Y',$a['dt']);
    $dt0 = date('Y-m-d',$a['dt']);
  }
}
if ($_REQUEST['pdf']==1){
  $html = container($id_city,$city,$dt,$dostavka,$fact_dostavka,$sum_less,$price_less,$sum_more,$price_more,$done,$dt0);
 
  include("$DOCUMENT_ROOT/mpdf/mpdf.php");
//  $mpdf=new mPDF('utf-8', 'A4', '12', '', 0, 0, 0, 0, 0, 0);
  $mpdf=new mPDF('utf-8', 'A4', '12');
//  $mpdf = new mPDF();
  //$mpdf->charset_in = 'cp1251';
  $mpdf->charset_in = 'utf-8';

  $mpdf->WriteHTML($html);
  $mpdf->Output(Utf8ToWin("$city $dt.pdf"),"D");
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
  tr, td {
    page-break-inside: avoid;
  }
  .pbb {
    page-break-after: always;
  }
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
<input type='hidden' name='done' value='<?=$done?>'>
<input type='hidden' name='dt' value='<?=$dt0?>'>
</form>
</div>
<div id='container' style='background-color:white;position:absolute;top:42px;left:0;border:1px solid;padding:15mm;'>
<?=container($id_city,$city,$dt,$dostavka,$fact_dostavka,$sum_less,$price_less,$sum_more,$price_more,$done,$dt0)?>
</div>
</body>
</html>
<?
function container_height_local($id_order){
  $r1 = mysql_query("select * from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.number>0 and ot.id_order=$id_order");
  $cnt_rows = mysql_num_rows($r1);
  if ($cnt_rows<8)$h = 69;
  else $h = 5*$cnt_rows+50;

  return $h;
}
function container_height($id_order,$num,$q){
  if ($num%3==0){
    $h = container_height_local($id_order);
    $q1 = str_replace(" order"," and id>$id_order order",$q);
    $q1 = str_replace(" desc","",$q1);
    $q1 .= ' limit 2';
    $r1 = mysql_query($q1);
    $a1 = mysql_fetch_array($r1);
    $id_order1 = $a1['id'];
    if ($id_order1!='')$h1 = container_height_local($id_order1);
    else $h1 = 0;
    $a2 = mysql_fetch_array($r1);
    $id_order2 = $a2['id'];
    if ($id_order2!='')$h2 = container_height_local($id_order2);
    else $h2 = 0;
//print $num."a$id_order $id_order1 $id_order2";
    $h = max($h,$h1,$h2);
  }
  if ($num%3==1){
    $h = container_height_local($id_order);
    $q1 = str_replace(" order"," and id<$id_order order",$q);
    $q1 .= ' limit 2';
    $r1 = mysql_query($q1);
    $a1 = mysql_fetch_array($r1);
    $id_order1 = $a1['id'];
    if ($id_order1!='')$h1 = container_height_local($id_order1);
    else $h1 = 0;
    $a2 = mysql_fetch_array($r1);
    $id_order2 = $a2['id'];
    if ($id_order2!='')$h2 = container_height_local($id_order2);
    else $h2 = 0;
//print $num."b$id_order $id_order1 $id_order2";
    $h = max($h,$h1,$h2);
  }
  if ($num%3==2){
    $h = container_height_local($id_order);
    $q1 = str_replace(" order"," and id>$id_order order",$q);
    $q1 = str_replace(" desc","",$q1);
    $q1 .= ' limit 1';
    $r1 = mysql_query($q1);
    $a1 = mysql_fetch_array($r1);
    $id_order1 = $a1['id'];
    if ($id_order1!='')$h1 = container_height_local($id_order1);
    else $h1 = 0;
    $q1 = str_replace(" order"," and id<$id_order order",$q);
    $q1 .= ' limit 1';
    $r1 = mysql_query($q1);
    $a1 = mysql_fetch_array($r1);
    $id_order2 = $a1['id'];
    if ($id_order2!='')$h2 = container_height_local($id_order2);
    else $h2 = 0;
//print $num."c$id_order $id_order1 $id_order2";
    $h = max($h,$h1,$h2);
  }
  return $h;
}
function container($id_city,$city,$dt,$dostavka,$fact_dostavka,$sum_less,$price_less,$sum_more,$price_more,$done,$dt0){
  ob_start();
?>
 <span style='margin-left:50px;border-bottom:1px solid;display:inline-block;font-weight:bold;font-size:4.2mm;'><?=$city?> <?=$dt?></span>
 <table id='main' style='padding:2mm;width:100%;'>
<?
  $fsum = 0;
  $xsumc=0;
  $fsum_d = 0;
  $tovar_fact_dostavka = 0;
  $cnt = 0;
  $q = '';
  $arr_rest = array();
  if($done==0){
    $q = "select * from pr_order where dt<='$dt0' and id_city=$id_city and done=0 order by id desc";
    $r = mysql_query($q);
  }else{
    $q = "select * from pr_order where dt='$dt0' and id_city=$id_city and done=1 order by id desc";
    $r = mysql_query($q);
  }
  $b_height = 21.2;
  $b_height_old = 0;
  while($a = mysql_fetch_array($r)){
    $cnt1 = $cnt+1;
    $id_order = $a['id'];
      $id_client = $a['id_client'];
    $client_name = $a['client_name'];
    $address = $a['address'];
    $comment = $a['comment'];
    $dt_now = $a['dt_now'];
    $dt = $a['dt'];
    if($city=='Другие города')$city1 = $a['other_city_name'];else $city1=$city;
    $predoplata = $a['predoplata'];
    $skidka = $a['skidka'];
    $r1 = mysql_query("select * from pr_client where id=$id_client");
    if (mysql_num_rows($r1)>0){
      $a1 = mysql_fetch_array($r1);
      $phone = $a1['phone'];
    }else $phone = '';
    $h = container_height($id_order,$cnt1,$q);
    if ($cnt1%3==1){
      $b_height+=$h;
      if (intval($b_height/297)!=intval($b_height_old/297) && $b_height_old!=0){
        $pb_flag = 1;
      }else{
        $pb_flag = 0;
      }
      $b_height_old = $b_height;
?>
  <tr<?if($pb_flag==1){?> class='tbb'<?}?>>
<?
    }else{
      $pb_flag = 0;
    }
?>
  <td style='border:1px solid;padding:5px;width:30%;margin-bottom:10px;font-size:4.2mm;' valign=top>
<p>
<span style='font-weight:bold;'>
<?=$cnt1?>)
<?
    if ($client_name!=''){
?>
<?=$client_name?>
<?
    }
?>
</span>
</p>
<?
    if ($phone!=''){
?>
<p><span style='font-weight:bold;'>+7<?=$phone?></span></p>
<?
    }
?>
   <div class='order_tovar_list' style='font-size:3.175mm !important;'>
<p><?=$city1?></p>
<?
    if ($address!=''){
?>
<p><?=$address?></p>
<?
    }
?>
<p>-----------------</p>
<span style="font-size: 10px!important;">
<?
    $r1 = mysql_query("select ot.number,ot.brak,ot.from_rest,t.price,t.name from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and (ot.number>0 or ot.brak>0) and ot.id_order=$id_order");
    $sum_order = 0;
    $full_price = 0;
    $full_price_brak = 0;
    while($a1 = mysql_fetch_array($r1)){
      $z_name = $a1['name'];
      $z_price = $a1['price'];
      $z_number = $a1['number'];
      $z_brak = $a1['brak'];
      $cur_price = $z_price*$z_number;
      $brak_price = $z_price*$z_brak;
      $fsum_d += $cur_price;
        if ($predoplata==1){ $fsum+= 0;} else { $fsum += $cur_price; }
      $full_price += $cur_price;
      $full_price_brak += $cur_price+$brak_price;
      if($a1['from_rest']==1)$arr_rest[] = "<div style='font-size:4.4mm;'>$client_name $cnt1</div>+$z_number $z_name<br><br>";
      if($z_number>0){
?>
<p><?if($a1['from_rest']==1){?><b>Остаток</b> <?}?><?=$z_number?> <?=$z_name?> - <?=$z_price?>x<?=$z_number?>=<?=$cur_price?></p>
<?
      }
      if($z_brak>0){
?>
<p><s><?=$z_brak?> <?=$z_name?> - <?=$z_price?>x<?=$z_brak?>=<?=$brak_price?></s> Брак</p>
<?
      }
    }
$sumx=$full_price - $skidka;
if ($predoplata==1) {} else {
    $fsum -= $skidka;
    $fsum_d -= $skidka;
    $sum = $full_price - $skidka;
    $sum_brak = $full_price_brak - $skidka;
}
    $dostavka1 = $dostavka;
    if($sum_less!='' && $sumx<=$sum_less)$dostavka1 = $price_less;
    if($sum_more!='' && $sumx>=$sum_more)$dostavka1 = $price_more;

//if ($predoplata!=1) $tovar_fact_dostavka += $fact_dostavka;
$tovar_fact_dostavka += $fact_dostavka;
if ($predoplata!=1) $fsum += $dostavka1;
if ($predoplata!=1) $sum += $dostavka1;
$sumx += $dostavka1;
//    if ($dostavka1>0){
?>
<p>Доставка: <?=$dostavka1?></p>
<?
//    }
    if ($skidka>0){
?>
<p>Скидка: <?=$skidka?></p>
<?
    }
 //   if ($sum>0){
      if ($predoplata==1){ //$sum=0;
          $xsumc+=$sumx;
?>
<p><b>ИТОГО: <?=$sumx?> (уже оплачено)</b></p>
<?
      }else{
          $xsumc+=$sum;
?>
<p><b>ИТОГО: <?=$sum?></b></p>
<?
      }
//    }
?>
</span>
<p>-----------------</p>
<p><?=$comment?></p> 
   </div>
  </td>
<?
    if ($cnt1%3==0){
?>
  </tr>
<?
    }
    $cnt++;
  }
  if ($cnt1%3==1){
?>
  <td></td>
  <td></td>
  </tr>
<?
  }
  if ($cnt1%3==2){
?>
  <td></td>
  </tr>
<?
  }
  $sum_fact_nodostavka = $fsum - $tovar_fact_dostavka;
?>
 </table>
    <br>
    <div style="border: 1px solid; height: 87px;">
 <div id='summary' style='font-size: 20px;'>
     <div style="float: left; padding: 10px;">
         Собрать сумму с клиентов = <?=$fsum?><br>
         Оплата за доставку = <?=$tovar_fact_dostavka?><br>
         Количество заказов = <?=$cnt?>
      </div>
     <div style="float: right; padding: 10px;">
         Общая сумма = <?=$xsumc?><br>
         Общая сумма без доставок = <?=$xsumc-$tovar_fact_dostavka?><br>
     </div>
  </div>
    </div>
<?
  if (count($arr_rest)>0){
?>
<div style='font-size:3.175mm;border:1px solid;'>
 <div style='font-size:4.4mm'>Добавить остатки к заказам:</div>
<?=implode("",$arr_rest);?>
</div>
<?
  }
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
