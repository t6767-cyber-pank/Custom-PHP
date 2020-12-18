<?
$AJAX_TIMEOUT = 3000;
$PHP_SELF = $_SERVER['PHP_SELF'];
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
$id = intval($_REQUEST['id']);
$r = mysql_query("select name from pr_city where id=$id");
if(mysql_num_rows($r)==0){
  $city = '';
}else{
  $a = mysql_fetch_array($r);
  $city = $a['name'];
}

$operation = $_POST['operation'];
if ($operation == 'del_rest'){
  foreach ($_POST as $k=>$v){
    if(strpos($k,"zakaz")===0){
      $id_zakaz = str_replace("zakaz","",$k);
//      if(!isset($_POST['ch'.$id_zakaz]) || $_POST['ch'.$id_zakaz]!=1)continue;
      $r_old = mysql_query("select rest from pr_tovar_city where id_city=$id and id_tovar=$id_zakaz");
      if (mysql_num_rows($r_old)>0){
        $a_old = mysql_fetch_array($r_old);
        $rest_old = $a_old['rest'];
        $v = $rest_old-$v;
        mysql_query("update pr_tovar_city set rest=$v where id_city=$id and id_tovar=$id_zakaz");
        $i = 0;
        $r = mysql_query("select ot.rest_real,o.id from pr_order_tovar ot,pr_order o where ot.id_order=o.id and o.id_city=$id and ot.id_tovar=$id_zakaz and ot.rest>0");
        while($a = mysql_fetch_array($r)){
          $id_order = $a['id'];
          if($i>0){
            $q = "update pr_order_tovar o set rest_real=0 where id_tovar=$id_zakaz and id_order=$id_order";
          }else{
            $q = "update pr_order_tovar o set rest_real=$v where id_tovar=$id_zakaz and id_order=$id_order";
          }
          mysql_query($q);
          $i++;
        }
      }
    }
  }
  exit;
}
if ($operation == 'to_order'){
  $id_city = intval($_POST['id_city']);
  $arr = array();
  foreach ($_POST as $k=>$v){
    if(strpos($k,"zakaz")===0){
      $id_zakaz = str_replace("zakaz","",$k);
      if($v==0)continue;
//      if(!isset($_POST['ch'.$id_zakaz]) || $_POST['ch'.$id_zakaz]!=1)continue;
      if($_POST['replace'.$id_zakaz]==0){
        print show_zakaz($id_zakaz,$v);
      }else{
        $r = mysql_query("select * from pr_tovar_city where id_tovar=$id_zakaz and id_city=$id");
        if (mysql_num_rows($r)>0){
          $a = mysql_fetch_array($r);
          $rest = $a['rest'];
          if ($v>$rest)$v = $rest;
          $arr[] = "$id_zakaz=$v";
        }
      }
    }
  }
  print '|'.implode('|',$arr);
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
    <script type="text/javascript" src="/js/jquery.maskedinput-1.3.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/js/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="/style_program.css">
</head>
<body>
<script>
function normalize_input(id,zero_flag,float_flag,rest){
  obj = $('#'+id);
  val = obj.val();
  if (!float_flag)val = val.replace(/[^\d]+/,'');
  else val = val.replace(/[^\d\.]+/,'');
  if (val=='')val = 0;
  if (val=='.')val = 0;
  if(val>rest)val=rest; 
  obj.val(val);
}
function plusone(id,unit,rest){
  obj = $('#num'+id);
  val = obj.val();
  if (unit==1){
    val = parseInt(val)+unit;
  }else{
    val = parseFloat(val)+unit;
    val = val.toFixed(1);
  }
  if(val>rest)val=rest;
  obj.val(val);
}
function minusone(id,unit,rest){
  obj = $('#num'+id);
  val = obj.val();
  if (unit==1){
    val = parseInt(val);
  }else{
    val = parseFloat(val);
  }
  if (val<unit){
    return false;
  }else{
    if (unit==1){
      val = val-unit;
    }else{
      val = val-unit;
      val = val.toFixed(1);
    }
    if(val>rest)val=rest;
    obj.val(val);
  }
}
function del_rest(){
  if(!confirm("Действительно удалить?"))return false;
  data = {};
  $('.zakaz').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz/,'');
    data['zakaz'+z_id] = $('#num'+z_id).val();
//    data['ch'+z_id] = $('#ch'+z_id).get(0).checked?1:0;
  });
  data['operation'] = 'del_rest';
  data['id'] = <?=$id?>;
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
//      console.log(html);
      window.close();
    },
    error:function(html){
      alert('Ошибка подключения!');
    },
  });
}
function to_order(id){
  if(window.opener==null)return false;
  d = window.opener.document;
  obj = d.getElementById('zakaz_middle');
  data = {};
  $('.zakaz').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz/,'');
    if($('#num'+z_id).val()>0){
      if(d.getElementById('zakaz_rest'+z_id)===null){
        data['zakaz'+z_id] = $('#num'+z_id).val();
        data['replace'+z_id] = 0;
      }else{
        if($('#unit'+z_id).val()==0){
          data['zakaz'+z_id] = parseInt($('#num'+z_id).val())+parseInt(d.getElementById('num_rest'+z_id).value);
        }else{
          data['zakaz'+z_id] = parseFloat($('#num'+z_id).val())+parseFloat(d.getElementById('num_rest'+z_id).value);
        }
        data['replace'+z_id] = 1;
      }
    }
  });
  data['operation'] = 'to_order';
  data['id'] = <?=$id?>;
  data['id_city'] = d.getElementById('zakaz_city').options[d.getElementById('zakaz_city').selectedIndex].value;
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      arr = html.split('|');
//console.log(html);
      arr.forEach(function(item, i, arr) {
        if (item!=''){
//          console.log( i + ": " + item + " (массив:" + arr + ")" );
          if (i==0){
            obj.innerHTML+= item;
          }else{
            arr1 = item.split('=');
            z_id = arr1[0];
            val = arr1[1];
            d.getElementById('num_rest'+z_id).value = val;
          }
        }
      });
 
//      console.log(obj.innerHTML);
      window.close();
    },
    error:function(html){
      alert('Ошибка подключения!');
    },
  });
}
</script>
<div style='text-align:center;position:fixed;left:0;top:0;right:0;height:20px;z-index:10;background-color:#DDDDDD;'>
Остатки <?=$city?>
</div>
<form id='mainform'>
 <div style='margin-top:20px;'>
  <div style='display:flex;flex-wrap:wrap;width:610px;background-color:white;'>
<?=show_rest($id)?>
  </div>
 </div>
 <div>
<?
$username = $_COOKIE['name'];
//print "select type from users where name='$username'";
$usertype = mysql_result(mysql_query("select type from users where name='$username'"),0,0);
if ($usertype==3){
?>
  <input type='button' value='Удалить' onclick='del_rest();'>
<?}?>
  <input type='button' value='В заказ' onclick='to_order();'>
  <input type='button' value='Отменить' onclick='$("#mainform").get(0).reset();'>
 </div>
</form>
</body>
</html>
<?

function show_rest($id){
  $r = mysql_query("select * from pr_tovar_city where rest>0 and id_city=$id");
  while($a = mysql_fetch_array($r)){
    $id = $a['id_tovar'];
    $rest = $a['rest'];
    show_tovar($id,$rest);
  }
}
function show_tovar($id,$rest){
  $r = mysql_query("select * from pr_tovar where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $name = $a['name'];
  $by_weight = $a['by_weight'];
?>
<div id='zakaz<?=$id?>' class='zakaz' style='width:200px;'>
 <div>
<?
  if($by_weight==0){
?>
<input type='hidden' id='unit<?=$id?>' value=0>
<?=$rest?> шт.
<?
  }else{
?>
<input type='hidden' id='unit<?=$id?>' value=1>
<?=$rest?> гр.
<?
  }
?>
 </div>
 <div style='position:relative;height:120px;'>
  <div class='zakaz_pic_container' style='width:100px;height:100px;margin-top:5px;overflow:hidden;'><img style='width:100%;' class='zakaz_pic' src='/pic<?=$id?>.jpg'></div>
  <div style='position:absolute;left:110px;top:0;right:3px;bottom:0;'>
   <div style='height:33%;width:100%;' class='zakaz_f1'><input type='button' value='+' style='width:100%;' onclick='<?if($by_weight==1){?>plusone(<?=$id?>,0.1,<?=$rest?>)<?}else{?>plusone(<?=$id?>,1,<?=$rest?>)<?}?>'></div>
   <div style='height:33%;width:100%;' class='zakaz_f2'><div style='padding:0 4px 0 1px;'><input type='text' id='num<?=$id?>' style='width:100%;padding:0;text-align:center;' <?if($by_weight==1){?>oninput='normalize_input(this.id,false,true,<?=$rest?>);'<?}else{?>oninput='normalize_input(this.id,false,false,<?=$rest?>);'<?}?> value='0'></div></div>
   <div style='height:33%;width:100%;' class='zakaz_f3'><input type='button' value='-' style='width:100%;' onclick='<?if($by_weight==1){?>minusone(<?=$id?>,0.1,<?=$rest?>)<?}else{?>minusone(<?=$id?>,1,<?=$rest?>)<?}?>'></div>
  </div>
  <div style='clear:both;'></div>
 </div>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'><?=$name?></div>
</div>
<?
}
function show_zakaz($id,$num){
  $r = mysql_query("select * from pr_tovar where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $name = $a['name'];
  $by_weight = $a['by_weight'];
  if ($by_weight==0)$unit = 1;else $unit = 0.1;
  $id_zakaz = -1;
?>
<div id='zakaz_rest<?=$id?>' class='zakaz_rest' style='background-color:#FFBEA6;'>
 <div style='position:relative;height:120px;'>
  <div class='zakaz_pic_container' style='width:100px;height:100px;margin-top:5px;overflow:hidden;'><img style='width:100%;' class='zakaz_pic' src='/pic<?=$id?>.jpg'></div>
  <div style='position:absolute;left:110px;top:0;right:3px;bottom:0;'>
   <div style='height:33%;width:100%;' class='zakaz_f1'><input type='button' value='+' style='width:100%;' onclick='<?if($by_weight==1){?>plusone_rest(<?=$id?>,0.1)<?}else{?>plusone_rest(<?=$id?>,1)<?}?>'></div>
   <div style='height:33%;width:100%;' class='zakaz_f2'><div style='padding:0 4px 0 1px;'><input type='text' id='num_rest<?=$id?>' style='width:100%;padding:0;text-align:center;' <?if($by_weight==1){?>oninput='normalize_input(this.id,false,true);gen_zakaz_text();'<?}else{?>oninput='normalize_input(this.id,false,false);gen_zakaz_text();'<?}?> value='<?=$num?>'></div></div>
   <div style='height:33%;width:100%;' class='zakaz_f3'><input type='button' value='-' style='width:100%;' onclick='<?if($by_weight==1){?>minusone_rest(<?=$id?>,0.1)<?}else{?>minusone_rest(<?=$id?>,1)<?}?>'></div>
  </div>
  <div style='clear:both;'></div>
 </div>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'><?=$name?></div>
</div>
<input type='hidden' id='from_rest<?=$id?>' value=1 class='from_rest'>
<input type='hidden' id='from_rest_first<?=$id?>' value=1 class='from_rest_first'>
<?
}
mysql_close($conn);
?>