<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$DOCUMENT_ROOT/mysql_connect.php");

$nodata = intval($_REQUEST['nodata']);
$nodata1 = intval($_REQUEST['nodata1']);
$nodata2 = intval($_REQUEST['nodata2']);
$nodata3 = intval($_REQUEST['nodata3']);

$id = intval($_REQUEST['id']);
$dt_from = $_REQUEST['dt_from'];
$dt_from = preg_replace('/<.*?>/','',$dt_from);
$dt_from = str_replace('"','',$dt_from);
$dt_from = str_replace("'",'',$dt_from);
$m = array();
if (preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_from)){
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_from,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
}else{
  preg_match('/(\d{2}).(\d{2}).(\d{4})/',$dt_from,$m);
  $t = mktime(0,0,0,$m[2],$m[1],$m[3]);
}
$dt_from = date('Y-m-d',strtotime(date('o-\\WW', $t)));
$t1 = strtotime(date('o-\\WW', $t))-24*3600;

$dt_to = $_REQUEST['dt_to'];
$dt_to = preg_replace('/<.*?>/','',$dt_to);
$dt_to = str_replace('"','',$dt_to);
$dt_to = str_replace("'",'',$dt_to);
$m = array();
if (preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_to)){
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_to,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
}else{
  preg_match('/(\d{2}).(\d{2}).(\d{4})/',$dt_to,$m);
  $t = mktime(0,0,0,$m[2],$m[1],$m[3]);
}
$dt_to = date('Y-m-d',strtotime(date('o-\\WW', $t)));
$t2 = strtotime(date('o-\\WW', $t))+3600*6*24;

//определим массив с данными, которые необходимо вывести в виде графика.

header ("Content-type: image/png");
$k = 0;
$start = strtotime($dt_from);
$data = array();
$data_s = array();
$data1 = array();
$data1_s = array();
$data2 = array();
$data2_s = array();
$data_dt = array();
if(date("w",strtotime($_REQUEST['dt_to']))==0)$start1 = $start;else $start1 = $start+3600*24;
while ($start1+$k*3600*24*7<=strtotime($dt_to)){
  $cur_dt = date('Y-m-d',$start+$k*3600*24*7);
  $cur_dt_prev = date('Y-m-d',$start+$k*3600*24*7-3600*24);
  $k++;

  $q = "select * from master_week where id_master=$id and dt='$cur_dt'";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $closed = 0;
    $param5 = 0;
    $param6 = 0;
    $param7 = 0;
  }else{
    $closed = $a['closed'];
    $param5 = $a['param5'];
    $param6 = $a['param6'];
    $param7 = $a['param7'];
  }

  if($closed==0){
    $r_flag = mysql_query("select chat_old from master_day where id_master=$id and dt='$cur_dt'");
    if(mysql_num_rows($r_flag)>0){
      $flag = mysql_result($r_flag,0,0);
    }else $flag = 0;
    if ($flag==1){
      $q = "select '$cur_dt'-interval 1 day dt1,sum(chats) chats from master_day where id_master=$id and dt>='$cur_dt' and dt<='$cur_dt'+interval 6 day";
      $r = mysql_query($q);
      if (mysql_num_rows($r)==0){
        $data[] = array($cur_dt_prev,0);
        $data_s[] = 0;
        $data_dt[] = $cur_dt_prev;
      }else{
        $a = mysql_fetch_array($r);
        $data[] = array($a['dt1'],intval($a['chats']));
        $data_s[] = intval($a['chats']);
        $data_dt[] = $a['dt1'];
      }
    }else{
      $q = "select '$cur_dt'-interval 1 day dt1,max(chats) chats from master_day where id_master=$id and dt>='$cur_dt' and dt<='$cur_dt'+interval 6 day";
      $r = mysql_query($q);
      if (mysql_num_rows($r)==0){
        $data[] = array($cur_dt_prev,0);
        $data_s[] = 0;
        $data_dt[] = $cur_dt_prev;
      }else{
        $a = mysql_fetch_array($r);
        $q1 = "select chats from master_day where id_master=$id and dt='$cur_dt'-interval 1 day";
        $r1 = mysql_query($q1);
        $a1 = mysql_fetch_array($r1);
        $data[] = array($a['dt1'],intval($a['chats']-$a1['chats']));
        $data_s[] = intval($a['chats']-$a1['chats']);
        $data_dt[] = $a['dt1'];
      }
    }

    $q = "select '$cur_dt'-interval 1 day dt1,sum(records) records from master_procedure_day where id_master=$id and dt>='$cur_dt' and dt<='$cur_dt'+interval 6 day";
    $r = mysql_query($q);
    if (mysql_num_rows($r)==0){
      $data1[] = array($cur_dt_prev,0);
      $data1_s[] = 0;
    }else{
      $a = mysql_fetch_array($r);
      $data1[] = array($a['dt1'],intval($a['records']));
      $data1_s[] = intval($a['records']);
    }

    $q = "select '$cur_dt'-interval 1 day dt1,sum(visitors)visitors from master_procedure_week where id_master=$id and dt='$cur_dt'";
    $r = mysql_query($q);
    if (mysql_num_rows($r)==0){
      $data2[] = array($cur_dt_prev,0);
      $data2_s[] = 0;
    }else{
      $a = mysql_fetch_array($r);
      $data2[] = array($a['dt1'],intval($a['visitors']));
      $data2_s[] = intval($a['visitors']);
    }
  }else{
    $data[] = array($cur_dt_prev,$param5);
    $data_s[] = $param5;
    $data_dt[] = $cur_dt_prev;

    $data1[] = array($cur_dt_prev,$param6);
    $data1_s[] = $param6;

    $data2[] = array($cur_dt_prev,$param7);
    $data2_s[] = $param7;
  }

}
 
//параметры изображения
$width = 1200; //ширина
$height= 300; //высота
$padding = 40;//отступ от края 
$step = 5;//шаг координатной сетки
//$v_step = 20;

//определяем область отображения графика
$gwidth= $width - 2 * $padding; 
 
//вычисляем минимальное и максимальное значение
$min = 0;
$max = 300;
if (count($data_s)>0){
  $arr_min = array();
  if($nodata==0)$arr_min[] = min($data_s);
  if($nodata1==0)$arr_min[] = min($data1_s);
  if($nodata2==0)$arr_min[] = min($data2_s);
  if (count($arr_min)>0)$min = min($arr_min);
  if ($min>0)$min = 0;
  $min = floor($min/$step) * $step;
  $arr_max = array();
  if($nodata==0)$arr_max[] = max($data_s);
  if($nodata1==0)$arr_max[] = max($data1_s);
  if($nodata2==0)$arr_max[] = max($data2_s);
  if (count($arr_max)>0)$max = max($arr_max);
  if ($max<0)$max = 300;
  if ($max-$min<10){
    $step = 1;
    $v_step = 30;
  }
  $max = ceil($max/$step) * $step;
  $diff = $max-$min;
  if ($diff==0)$diff = $step*3;

//  $height = ($max-$min)*$v_step/$step;
//  if ($height==0)$height = 300;
  $gheight = $height - 2 * $padding; 
}else{
  $max=0;
  $min=0;
//  $height=300;
  $diff=1;
}
$step = round(($max-$min)/5);
if ($step<1)$step=1;
$cnt = count($data);
$h_step = $gwidth / ($cnt - 1);
if ($h_step<120){
  $h_step = 120;
  $gwidth = $h_step*($cnt-1);
  $width = $gwidth + 2 * $padding; 
}
 
//создаем изображение
$im = @ImageCreate ($width, $height) 
or die ("Cannot Initialize new GD image stream");
 
//задаем цвета, которые будут использоваться при отображении картинки
$bgcolor = ImageColor($im, array('r'=>255, 'g'=>255, 'b'=>255)); 
$color = ImageColor($im, array('b'=>175)); 
$green = ImageColor($im, array('g'=>175)); 
$gray = ImageColor($im, array('r'=>175, 'g'=>175, 'b'=>175)); 
$black = ImageColor($im, array('r'=>0, 'g'=>0, 'b'=>0)); 

$color_newchat = ImageColor($im, array('r'=>85, 'g'=>142, 'b'=>213)); 
$color_records = ImageColor($im, array('r'=>149, 'g'=>179, 'b'=>215)); 
$color_visitors = ImageColor($im, array('r'=>247, 'g'=>150, 'b'=>70)); 
$color_event = ImageColor($im, array('r'=>200, 'g'=>200, 'b'=>200)); 
 
//рисуем сетку значений
//рисуем сетку значений
$zero_flag = 0;
$zero0 = 0;
$i0 = 0;
$zero1 = 1;
$i1 = 0;
for($i = $min; $i < $max + $step; $i += $step){
  $y = $gheight - ($i - $min) * ($gheight) / $diff + $padding;
  if ($i==0){
    $zero_flag = 1;
    ImageLine($im, 0, $y, $gwidth + $padding*2, $y, $gray);
    ImageTTFText($im, 8, 0, 0, $y - 1, $black, $DOCUMENT_ROOT."/verdana.ttf", $i);
  }else{
    ImageLine($im, $padding, $y, $gwidth + $padding, $y, $gray);
    ImageTTFText($im, 8, 0, $padding/2, $y - 1, $black, $DOCUMENT_ROOT."/verdana.ttf", $i);
    if ($i==$min){
      $zero0 = $y;
      $i0 = $i;
    }
    if ($i>=$max){
      $zero1 = $y;
      $i1 = $i;
    }
  }
}
if($zero_flag==0){
  $k = ($i1-$i0)/($zero1-$zero0);
  $m = $i0-$zero0*$k;
  if ($k!=0){
    $zero_level = -$m/$k;
  }else{
    $zero_level = $gheight+$padding;
  }
  ImageLine($im, 0, $zero_level, $gwidth + $padding*2, $zero_level, $gray);
  ImageTTFText($im, 8, 0, $padding/4 + 1, $zero_level - 1, $black, $DOCUMENT_ROOT."/verdana.ttf", 0);
}
imagesetthickness($im,3);

$x2 = $padding;
for($i = 1; $i < $cnt; $i++){
  $x1 = $x2;
  $x2 = $x1 + $h_step;
  ImageTTFText($im, 8, 0, $x1, $height-20, $black, $DOCUMENT_ROOT."/verdana.ttf", $data_dt[$i]);
}
ImageTTFText($im, 8, 0, $x2-50, $height-20, $black, $DOCUMENT_ROOT."/verdana.ttf", date('Y-m-d',strtotime($data_dt[$cnt-1])+3600*24*7));

$arr_points = array(); 
if ($nodata==0){
//отображение графика
  $x2 = $padding;
  $i= 0;
//стоит отметить, что начало координат для картинки находится 
//в левом верхнем углу, что определяет формулу вычисления координаты y
  $y2 = $gheight - ($data[$i][1] - $min) * ($gheight) / $diff + $padding;
  $arr_points[$x2][] = array($y2,$data[0][1]);
  for($i = 1; $i < $cnt; $i++){
    $x1 = $x2;
    $x2 = $x1 + $h_step;
    $y1 = $y2;
    $y2 = $gheight - ($data[$i][1] - $min) * ($gheight) / $diff + $padding;
 
    ImageLine($im, $x1, $y1, $x2, $y2, $color_newchat);
    $arr_points[$x2][] = array($y2,$data[$i][1]);
  }
}

if ($nodata1==0){
  $x2 = $padding;
  $i= 0;
  $y2 = $gheight - ($data1[$i][1] - $min) * ($gheight) / $diff + $padding;
  $arr_points[$x2][] = array($y2,$data1[0][1]);
  for($i = 1; $i < $cnt; $i++){
    $x1 = $x2;
    $x2 = $x1 + $h_step;
    $y1 = $y2;
    $y2 = $gheight - ($data1[$i][1] - $min) * ($gheight) / $diff + $padding;
 
    ImageLine($im, $x1, $y1, $x2, $y2, $color_records);
    $arr_points[$x2][] = array($y2,$data1[$i][1]);
  }
}

if ($nodata2==0){
  $x2 = $padding;
  $i= 0;
  $y2 = $gheight - ($data2[$i][1] - $min) * ($gheight) / $diff + $padding;
  $arr_points[$x2][] = array($y2,$data2[0][1]);
  for($i = 1; $i < $cnt; $i++){
    $x1 = $x2;
    $x2 = $x1 + $h_step;
    $y1 = $y2;
    $y2 = $gheight - ($data2[$i][1] - $min) * ($gheight) / $diff + $padding;
 
    ImageLine($im, $x1, $y1, $x2, $y2, $color_visitors);
    $arr_points[$x2][] = array($y2,$data2[$i][1]);
  }
}

foreach ($arr_points as $x=>$a){
  usort($a,"cmp");
  $thr = 11;
//  ImageTTFText($im, 8, 0, $x, 0, $black, $DOCUMENT_ROOT."/verdana.ttf", print_r($a,1));
  for ($i=0;$i<count($a)-1;$i++){
    $i1 = $i+1;
    $a11 = $a[$i];
    $a12 = $a[$i1];
    if ($a11[0]+$thr>$a12[0])$a[$i1][0]=$a11[0]+$thr;
  }
  for ($i=0;$i<count($a);$i++){
    $a1 = $a[$i];
    if ($i==0)$last = $a1[1];
    elseif ($a1[1]==$last)continue;
    else $last = $a1[1];
    $y = $a1[0];
    $val = $a1[1];
    ImageTTFText($im, 7, 0, $x, $y, $black, $DOCUMENT_ROOT."/verdana.ttf", $val);
  }
}

if ($nodata3==0){
  $c = $gwidth/($t2-$t1);
  $m = $padding-$c*$t1;
  $r = mysql_query("select name,unix_timestamp(dt)dt from master_events where id_master=$id");
  while ($a = mysql_fetch_array($r)){
    $t = $a['dt'];
    $dt = date('Y-m-d',$t);
    $name = $a['name'];
    $x = $m+$t*$c;
    ImageLine($im, $x, 0, $x, $height, $color_event);
    ImageTTFText($im, 8, 0, $x-40, $height-5, $black, $DOCUMENT_ROOT."/verdana.ttf", $dt);
    $arr = imagettfbbox(8,0,$DOCUMENT_ROOT."/verdana.ttf", $name);
    $s = intval(($arr[2]-$arr[0])/2);
    ImageTTFText($im, 8, 0, $x-$s, 20, $black, $DOCUMENT_ROOT."/verdana.ttf", $name);
  }
}

//Отдаем полученный график браузеру, меняя заголовок файла
ImagePng ($im);

function ImageColor($im, $color_array){
  return ImageColorAllocate(
    $im,
    isset($color_array['r']) ? $color_array['r'] : 0, 
    isset($color_array['g']) ? $color_array['g'] : 0, 
    isset($color_array['b']) ? $color_array['b'] : 0 
  );
}
function cmp($a,$b){
  if ($a[0]==$b[0])return 0;
  return ($a[0]<$b[0])?-1:1;
}
?>