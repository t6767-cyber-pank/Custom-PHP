<?
$AJAX_TIMEOUT = 3000;
$PHP_SELF = $_SERVER['PHP_SELF'];
/* $dtx=date("Y-m-d");
$rxxx = mysql_query("SELECT name, id FROM pr_city ORDER BY name ASC");
while ($axxx = mysql_fetch_array($rxxx)) {
    mysql_query("update pr_city set dt_otgruzka='$dtx' where id=".$axxx["id"]);
}*/
if (!isset($_REQUEST['razdel']))$_REQUEST['razdel']=1;
$operation = $_POST['operation'];
if ($operation == 'add_city'){
  print show_city();
  exit;
}
if ($operation == 'add_emk'){
    print show_emk();
    exit;
}
if ($operation == 'add_emk7'){
    print show_emk7();
    exit;
}
if ($operation == 'reset_cities'){
  print show_cities();
  exit;
}
if ($operation == 'save_cities'){
//print_r($_POST);
  foreach($_POST as $k=>$v){
    $m = array();
    if (preg_match('/^dostavka(.*)/',$k,$m)){
      $id = intval($m[1]);
      $id_post = $id;
      $flag = 0;
      if ($id<0){
        mysql_query("insert into pr_city (id) values(0)");
        $id = mysql_insert_id();
        $flag = 1;
      }
      if ($flag==1){
        $name = str_replace("'",'\"',$_POST['city'.$id_post]);
        mysql_query("update pr_city set name='$name' where id=$id");
        mysql_query("insert into ezh_city(name,id_shop)values('$name',1)");
      }
      if($_POST['dostavka'.$id_post]!='')$dostavka = intval($_POST['dostavka'.$id_post]);else $dostavka = 'NULL';
      $q = "update pr_city set dostavka=$dostavka where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['fact_dostavka'.$id_post]!='')$fact_dostavka = intval($_POST['fact_dostavka'.$id_post]);else $fact_dostavka = 'NULL';
      $q = "update pr_city set fact_dostavka=$fact_dostavka where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['sum_less'.$id_post]!='')$sum_less = intval($_POST['sum_less'.$id_post]);else $sum_less = 'NULL';
      $q = "update pr_city set sum_less=$sum_less where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['price_less'.$id_post]!='')$price_less = intval($_POST['price_less'.$id_post]);else $price_less = 'NULL';
      $q = "update pr_city set price_less=$price_less where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['sum_more'.$id_post]!='')$sum_more = intval($_POST['sum_more'.$id_post]);else $sum_more = 'NULL';
      $q = "update pr_city set sum_more=$sum_more where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['price_more'.$id_post]!='')$price_more = intval($_POST['price_more'.$id_post]);else $price_more = 'NULL';
      $q = "update pr_city set price_more=$price_more where id=$id";
//      print $q;
      mysql_query($q);
      if($_POST['allow_bills'.$id_post]!=0)$allow_bills = intval($_POST['allow_bills'.$id_post]);else $allow_bills = '0';
      $q = "update pr_city set allow_bills=$allow_bills where id=$id";
//      print $q;
      mysql_query($q);
    }
    if (preg_match('/del(.*)/',$k,$m)){
      $id = intval($m[1]);
      if ($id>0){
        $q = "delete from pr_city where id=$id";
        mysql_query($q);        
      }
    }
  }
  print show_cities();
  exit;
}

if ($operation == 'save_emkost'){
    if ($_POST['idem']==0 && $_POST['emk']!=""){
        $emk=$_POST['emk'];
        $q = "insert into ezh_emkost(emkost) values('$emk')";
        mysql_query($q);
    }
    if ($_POST['idem']!=0 && $_POST['emk']!=""){
        $idemkos=$_POST['idem'];
        $emk=$_POST['emk'];
        $q = "update ezh_emkost set emkost='$emk' where id=$idemkos";
        mysql_query($q);
    }
    if ($_POST['idem']!=0 && $_POST['emk']==""){
        $idemkos=$_POST['idem'];
        $q = "delete from ezh_emkost where id=$idemkos";
        mysql_query($q);
    }

    print show_cities();
    exit;
}
if ($operation == 'reset_tovar_to_archive'){
    $id = intval($_POST['id']);
    $q = "update pr_tovar set pokaz=0 where id=$id";
//      print $q;
    mysql_query($q);
//    print tovar_form($id);
//    $res = ob_get_contents();
//    ob_end_clean();
//    print json_encode($res);
//    exit;
}
if ($operation == 'set_tovar_to_archive'){
    $id = intval($_POST['id']);
    $q = "update pr_tovar set pokaz=1 where id=$id";
//      print $q;
    mysql_query($q);
//    print tovar_form($id);
//    $res = ob_get_contents();
//    ob_end_clean();
//    print json_encode($res);
    exit;
}

if ($operation == 'set_tovar_to_child'){
    $child=intval($_POST['child']);
    $qkosm = "SELECT * FROM ezh_emkost where id=$child";
    $rkosm = mysql_query($qkosm);
    $akosm = mysql_fetch_array($rkosm);
    $idemkost=$akosm['id'];
    $etalon=$akosm['emkost'];
    $id = intval($_POST['id']);
    $name=$_POST['name'];
    $seb=$_POST['seb'];
    $price=$_POST['priceemk'];

    $qkosm = "SELECT * FROM pr_tovar where id=$id";
    $rkosm = mysql_query($qkosm);
    $akosm = mysql_fetch_array($rkosm);
    $idetalonparent=$akosm['idemk'];
    $activeparent=$akosm['active'];

    $qkosm = "SELECT * FROM pr_tovar where parent=$id and idemk=$child";
    $rkosm = mysql_query($qkosm);
    $akosm = mysql_fetch_array($rkosm);
    $idemkchildsr=$akosm['idemk'];

    if($idemkost!=$idetalonparent && $idemkchildsr!=$child) {
        mysql_query("insert into pr_tovar (id, name, description, idemk, parent, price, self_price, active) values(0, '$name ($etalon)', '', $child, $id, $price, $seb, $activeparent)");
        $idx = mysql_insert_id();
        $qkosm = "SELECT * FROM pr_tovar_city where id_tovar=$id";
        $rkosm = mysql_query($qkosm);
        while ($akosm = mysql_fetch_array($rkosm)) {
            mysql_query("insert into pr_tovar_city (id_city, id_tovar, rest) values(" . $akosm['id_city'] . ", $idx, 0)");
        }
        echo "Товар с емкостью $etalon создан";
    } else { echo "Товар с емкостью $etalon уже создан"; }
    exit;
}


if ($operation == 'set_show_tovar'){
  $flag = intval($_POST['flag']);
  $id = intval($_POST['id']);
  print show_tovar($flag,$id);
  //header("Location:/program.php");
  exit;
}
if ($operation == 'show_tovar_form'){
  $id = intval($_POST['id']);
  print tovar_form($id);
  exit;
}
if ($operation == 'add_tovar_city'){
  $id = intval($_POST['id']);
  print new_tovar_city($id);
  exit;
}
if ($operation == 'reset_tovar_cities'){
  $id = intval($_POST['id']);
  print tovar_form($id);
  exit;
}
if ($operation == 'save_tovar_cities'){
  ob_start();
  $id = intval($_POST['right_tovar_id']);
  $id_post = $id;
  echo $_POST['Etalon77'];
  if($id==-1){
    mysql_query("insert into pr_tovar (id, idemk) values (0, ".$_POST['Etalon77'].$id_post.")");
    $id = mysql_insert_id();
 //   mysql_query("insert into ezh_emkost_svod(idtovar,idemkost, etalon, priceet, sebestet) values($id, ".$_POST['Etalon'].", 1, ".intval($_POST['price'.$id_post]).", ".intval($_POST['self_price'.$id_post]).")");
  }
  $name = str_replace("'",'\"',$_POST['name'.$id_post]);
  mysql_query("update pr_tovar set name='$name' where id=$id"); 
  $description = str_replace("'",'\"',$_POST['description'.$id_post]);
  mysql_query("update pr_tovar set description='$description' where id=$id"); 
  $active = intval($_POST['active'.$id_post]);
  $q = "update pr_tovar set active=$active where id=$id";
  mysql_query($q);

    $q = "update pr_tovar set active=$active where parent=$id";
    mysql_query($q);


    $by_weight = intval($_POST['by_weight'.$id_post]);
  $q = "update pr_tovar set by_weight=$by_weight where id=$id";  
//  print $q;
  mysql_query($q); 
  if($_POST['price'.$id_post]!='')$price = intval($_POST['price'.$id_post]);else $price = 'NULL';
  $q = "update pr_tovar set price=$price where id=$id";
  mysql_query($q);
    if($_POST['Etalon77'.$id_post]!='')$etl = intval($_POST['Etalon77'.$id_post]);else $etl = 'NULL';
    $q = "update pr_tovar set idemk=$etl where id=$id";
    mysql_query($q);
    if($_POST['self_price'.$id_post]!='')$self_price = intval($_POST['self_price'.$id_post]);else $price = 'NULL';
  $q = "update pr_tovar set self_price=$self_price where id=$id";
//  print $q;
  mysql_query($q); 
  $f = $_FILES['pic_file'];
  if ($f['tmp_name']!=''){
    $fname = $f['tmp_name'];
    $im = new \Imagick($fname);
    $im->scaleImage(120,120);
    $str = $im->getImageBlob();
 
//    $str = file_get_contents($fname);
    $str = mysql_real_escape_string($str);
    mysql_query("update pr_tovar set picture='$str' where id=$id"); 
  }
  foreach($_POST as $k=>$v){
    $m = array();
    if (preg_match('/^city_/',$k,$m)){
      $city_id = $v;
      mysql_query("insert into pr_tovar_city(id_city,id_tovar)values($city_id,$id)");
    }
    if ($id!=-1 && preg_match('/^del_(\d+)_(\d+)/',$k,$m)){
      $city_id = $m[2];
      mysql_query("delete from pr_tovar_city where id_city=$city_id and id_tovar=$id");
    }
  }
  print tovar_form($id);
  $res = ob_get_contents();
  ob_end_clean();
  print json_encode($res);
  exit;
}
if ($operation == 'show_zakaz'){
  $id = intval($_POST['id']);
  $unit = floatval($_POST['unit']);
  $emk = (int)$_POST['emk'];
  $emkstring = (float)$_POST['emkstring'];
  print show_zakaz($id,-1,$unit, $emk, $emkstring);
  exit;
}
if ($operation == 'change_zakaz_city'){
  $id = intval($_POST['id']);
  print dostavka($id);
  exit;
}
if ($operation == 'gen_zakaz_text'){
  print gen_zakaz_text($_POST);
  exit;
}
if ($operation == 'zakaz_client'){
  unset($_REQUEST['id']);
  print zakaz_client();
  exit;
}
if ($operation == 'save_order'){
  $phone = mysql_real_escape_string($_POST['phone']);
  $client_name = mysql_real_escape_string($_POST['client_name']);
  $address = mysql_real_escape_string($_POST['address']);
  $r = mysql_query("select * from pr_client where phone='$phone'");
  if (mysql_num_rows($r)==0){
    mysql_query("insert into pr_client(phone,name,address)values('$phone','$client_name','$address')");
    $id_client = mysql_insert_id();
  }else{
    $a = mysql_fetch_array($r);
    $id_client = $a['id'];
    mysql_query("update pr_client set address='$address' where phone=$phone");
  }

  $id_city = intval($_POST['zakaz_city']);
  $id_order = intval($_POST['id_order']);  
  if ($id_order<0){
    mysql_query("insert into pr_order(id,id_client,id_city)values(0,$id_client,$id_city)");
    $id_order = mysql_insert_id();
    $done = 0;
  }else{
    mysql_query("update pr_order set id_client=$id_client,id_city=$id_city where id=$id_order");
    $done = mysql_result(mysql_query("select done from pr_order where id=$id_order"),0,0);
  }
  $other_city_name = mysql_real_escape_string($_POST['other_city_name']);
  mysql_query("update pr_order set other_city_name='$other_city_name' where id=$id_order");
  mysql_query("update pr_order set client_name='$client_name' where id=$id_order");
  mysql_query("update pr_order set address='$address' where id=$id_order");
  $comment = mysql_real_escape_string($_POST['comment']);
  mysql_query("update pr_order set comment='$comment' where id=$id_order");
  $predoplata = intval($_POST['predoplata']);
  mysql_query("update pr_order set predoplata=$predoplata where id=$id_order");
  $vkdos = intval($_POST['VK']);
  mysql_query("update pr_order set vk_zakaz=$vkdos where id=$id_order");
  $skidka = intval($_POST['skidka']);
  mysql_query("update pr_order set skidka=$skidka where id=$id_order");
  $dt_now = intval($_POST['dt_now']);
  mysql_query("update pr_order set dt_now=$dt_now where id=$id_order");  
  if ($dt_now==1){
    if ($done==0)mysql_query("update pr_order o,pr_city c set o.dt=c.dt_otgruzka where c.id=o.id_city and o.id=$id_order");
  }else{
    $dt = mysql_real_escape_string($_POST['dt']);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    if ($done==0)mysql_query("update pr_order set dt='$dt' where id=$id_order");
  }
  $arr_order_tovar = array();
  $r = mysql_query("select * from pr_order_tovar where id_order=$id_order");
  while($a = mysql_fetch_array($r)){
    $id_tovar = $a['id_tovar'];
    $full_number = $a['number']+$a['brak'];
    $from_rest = $a['from_rest'];
    $arr_order_tovar[$id_tovar] = array('full_number'=>$full_number,'brak'=>$a['brak'],'rest'=>$a['rest'],'from_rest'=>$from_rest);
  }
  mysql_query("delete from pr_order_tovar where id_order=$id_order");
  foreach($_POST as $k=>$v){
    $m = array();
    if (preg_match('/zakaz(\d+)/',$k,$m) || preg_match('/zakaz_rest(\d+)/',$k,$m)){
      $z_id = $m[1];
      $v = floatval($v);
      if(isset($_POST['brak'.$z_id]) && $_POST['brak'.$z_id]==1){
        $full_number = floatval($arr_order_tovar[$z_id]['full_number']);
        $rest = floatval($arr_order_tovar[$z_id]['rest']);
        $number = $full_number-$rest-$v;
        $brak = $v;
//print "insert into pr_order_tovar(id_order,id_tovar,number,brak)values($id_order,$z_id,$v,$brak)";
        mysql_query("insert into pr_order_tovar(id_order,id_tovar,number,brak,rest)values($id_order,$z_id,$number,$brak,$rest)");
      }elseif(isset($_POST['ostatok'.$z_id]) && $_POST['ostatok'.$z_id]==1){
        $full_number = floatval($arr_order_tovar[$z_id]['full_number']);
        $brak = floatval($arr_order_tovar[$z_id]['brak']);
        $number = $full_number-$brak-$v;
        $rest = $v;
//print "insert into pr_order_tovar(id_order,id_tovar,number,brak)values($id_order,$z_id,$v,$brak)";
        mysql_query("insert into pr_order_tovar(id_order,id_tovar,number,brak,rest,rest_real)values($id_order,$z_id,$number,$brak,$rest,$rest)");
      }else{
        $brak = floatval($arr_order_tovar[$z_id]['brak']);
        $rest = floatval($arr_order_tovar[$z_id]['rest']);
/*
        $from_rest = floatval($arr_order_tovar[$z_id]['from_rest']);
        if(isset($_POST['from_rest'.$z_id]) && $_POST['from_rest'.$z_id]==1)$from_rest=1;
*/
        if (preg_match('/zakaz_rest(\d+)/',$k))$from_rest=1;
        if (preg_match('/zakaz(\d+)/',$k))$from_rest=0;
        $r_city_rest = mysql_query("select sum(rest_real)city_rest from pr_order_tovar ot,pr_order o where ot.id_order=o.id and o.id_city=$id_city and ot.id_tovar=$z_id");
        $a_city_rest = mysql_fetch_array($r_city_rest);
        $city_rest = $a_city_rest['city_rest'];
        if($from_rest==1 && $city_rest<$v)$v = $city_rest;
//print "insert into pr_order_tovar(id_order,id_tovar,number,brak,rest,from_rest)values($id_order,$z_id,$v,$brak,$rest,$from_rest)";
        mysql_query("insert into pr_order_tovar(id_order,id_tovar,number,brak,rest,from_rest)values($id_order,$z_id,$v,$brak,$rest,$from_rest)");
        if(isset($_POST['from_rest_first'.$z_id]) && $_POST['from_rest_first'.$z_id]==1){
          $cnt = $v;
          $r_r = mysql_query("select ot.rest_real,o.id from pr_order_tovar ot,pr_order o where ot.id_order=o.id and o.id_city=$id_city and ot.id_tovar=$z_id and ot.rest_real>0");
          while($a_r = mysql_fetch_array($r_r)){
            $rr_id = $a_r['id'];
            $rr = $a_r['rest_real'];
            if ($cnt>$rr){
              mysql_query("update pr_order_tovar o set rest_real=0 where id_tovar=$z_id and id_order=$rr_id");
              $cnt-=$rr;
            }else{
              $c = $rr-$cnt;
              mysql_query("update pr_order_tovar o set rest_real=$c where id_tovar=$z_id and id_order=$rr_id");
              break;
            }
          }
        }
      }
      $r_city_rest = mysql_query("select sum(rest_real)city_rest from pr_order_tovar ot,pr_order o where ot.id_order=o.id and o.id_city=$id_city and ot.id_tovar=$z_id");
      $a_city_rest = mysql_fetch_array($r_city_rest);
      $city_rest = $a_city_rest['city_rest'];
      mysql_query("update pr_tovar_city set rest=$city_rest where id_city=$id_city and id_tovar=$z_id");
    }
  }
  exit;
}
if ($operation == 'get_client'){
  $phone = mysql_real_escape_string($_POST['phone']);
  $r = mysql_query("select * from pr_client where phone='$phone'");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $name = $a['name'];
    $address = $a['address'];
    $sendprice = $a['sendPrice'];
    $warm = $a['warm'];
    $promokod = $a['promokod'];
    $id = $a['id'];
    $r1 = mysql_query("select count(*) cnt from pr_order where id_client=$id");
    $a1 = mysql_fetch_array($r1);
    $number = $a1['cnt'];
?>
<div id='tmp_client_div' style='display:none;'>
 <div id='tmp_id_client'><?=$id?></div>
 <div id='tmp_name'><?=$name?></div>
 <div id='tmp_address'><?=$address?></div>
 <div id='tmp_number'><?=$number?></div>
 <div id='tmp_sendprice'><?=$sendprice?></div>
 <div id='tmp_warmclient'><?=$warm?></div>
 <div id='tmp_promokod'><?=$promokod?></div>
</div>
<?
  }
  exit;
}
if ($operation == 'show_zakaz_tovar'){
  $id = intval($_POST['id']);
  print show_zakaz_tovar($id);
  exit;
}
if ($operation == 'save_client'){
  $id_client = intval($_POST['id_client']);
  if($id_client==-1){
    mysql_query("insert into pr_client (id) values (0)");
    $id_client = mysql_insert_id();
  }
  $phone = mysql_real_escape_string($_POST['phone']);
  $name = mysql_real_escape_string($_POST['client_name']);
  $address = mysql_real_escape_string($_POST['address']);
  $sp = $_POST['sendprice'];
  $warm = $_POST['warmclient'];
  $promokod = $_POST['promokod'];
  mysql_query("update pr_client set phone='$phone',name='$name',address='$address', sendPrice=$sp, warm=$warm, promokod=$promokod where id=$id_client");
  exit;
}
if ($operation == 'del_order'){
  $id = intval($_POST['id']);
  $r = mysql_query("select * from pr_order where id=$id");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $id_city = $a['id_city'];
    $r1 = mysql_query("select * from pr_order_tovar where id_order=$id and from_rest=1");
    while($a1 = mysql_fetch_array($r1)){
      $id_tovar = $a1['id_tovar'];
      $number = $a1['number'];
      mysql_query("update pr_order_tovar set rest_real=rest_real+$number where id_tovar=$id_tovar and rest_real>0 limit 1");
      $r_city_rest = mysql_query("select sum(rest_real)city_rest from pr_order_tovar ot,pr_order o where ot.id_order=o.id and o.id_city=$id_city and ot.id_tovar=$id_tovar");
      $a_city_rest = mysql_fetch_array($r_city_rest);
      $city_rest = $a_city_rest['city_rest'];
      mysql_query("update pr_tovar_city set rest=$city_rest where id_city=$id_city and id_tovar=$id_tovar");
    }
  }
  mysql_query("delete from pr_order where id=$id");
  exit;
}
if ($operation == 'set_otgruzka'){
  $id = intval($_POST['id']);
  $dt = date('Y-m-d',$_POST['dt']/1000);
  mysql_query("update pr_city set dt_otgruzka='$dt' where id=$id");
  print city_orders($id);
  exit;
}
if ($operation == 'city_orders_done'){
  $id = intval($_POST['id']);
  $id_order_arr = $_POST['id_order'];
  $r = mysql_query("select dt_otgruzka from pr_city where id=$id");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $dt = $a['dt_otgruzka'];
  }else $dt = '';
  if(is_array($id_order_arr)){
    foreach($id_order_arr as $id_order){
      $id_order = intval($id_order);
      if ($dt==''){
        mysql_query("update pr_order set done=1 where id=$id_order");
      }else{
        mysql_query("update pr_order set done=1,dt='$dt' where id=$id_order");
      }
    }
  }
  print city_orders($id);
  exit;
}
if ($operation == 'sendfile'){
  $id_order = $_POST['id_order'];
  $file = $_FILES['fileinput'.$id_order];
  $src = $file['tmp_name'];
  $size = getimagesize($src);
  if ($size!==false){
    $w = $size[0];
    $h = $size[1];
    if($w>$h){
      $w1 = 650;
      $h1 = intval($h*$w1/$w);
    }else{
      $h1 = 650;
      $w1 = intval($w*$h1/$h);
    }
   
    $isrc  = imagecreatefromstring(file_get_contents($src));
    $exif = @exif_read_data($src);
    if(!empty($exif['Orientation'])) {
      switch($exif['Orientation']) {
          case 8:
              $isrc = imagerotate($isrc,90,0);
              break;
          case 3:
              $isrc = imagerotate($isrc,180,0);
              break;
          case 6:
              $isrc = imagerotate($isrc,-90,0);
              break;
      }
    }
    $idest = imagecreatetruecolor($w1, $h1);
      
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $w1, $h1, $w, $h);

    mysql_query("insert into pr_order_bill(id_order)values($id_order)");
    $id = mysql_insert_id();
    
    imagejpeg($idest,$_SERVER['DOCUMENT_ROOT']."/bills/driver.$id_order.$id.jpg");
    print driver_bills($id_order);
  }else print driver_bills($id_order);
  exit;
}
if ($operation == 'delfile'){
  $id_order = $_POST['id_order'];
  $r = mysql_query("select * from pr_order_bill where id_order=$id_order");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    unlink($_SERVER['DOCUMENT_ROOT']."/bills/driver.$id_order.$id.jpg");
  }
  mysql_query("delete from pr_order_bill where id_order=$id_order");
  print driver_bills($id_order);
  exit;
}
if ($operation == 'order_check'){
  $id = $_POST['id'];
  $checked = $_POST['checked'];
  mysql_query("update pr_order set checked=$checked where id=$id");
  exit;
}
if ($operation=='show_link_rest'){
  $id = $_POST['id'];
  $name = $_POST['name'];
  print show_link_rest($id,$name);
  exit;
}
if ($operation=='close_week'){
//  print_r($_POST);
  $id_city = intval($_POST['id_city']);
  $limit = intval($_POST['limit']);
  if ($limit<10)$limit = 10;
  $html = mysql_real_escape_string($_POST['html']);
  $dt = mysql_real_escape_string($_POST['dt']);
  $q = "insert into pr_week (id_city,dt,closed,html) values($id_city,'$dt',1,'$html') on duplicate key update closed=1,html='$html'";
//  print $q;
  mysql_query($q);
  $allow_bills = mysql_result(mysql_query("select allow_bills from pr_city where id=$id_city"),0,0);
  print city_orders_done($id_city,$allow_bills,$limit);
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
	<title>ЕЖ принес 1</title>
</head>
<body>
<div id='menu' style='padding-top:5px; padding-left:20px; height:20px;position:relative;'>
<span>
<?
if ($_REQUEST['razdel']==1){
  if (!isset($_REQUEST['id'])){
?>
<b style='border-bottom:1px solid black;'>Создать заказ</b>
<?}else{?>
<b style='border-bottom:1px solid black;'>Редактировать заказ</b>
<?}?>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='program.php?razdel=1'>Создать заказ</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<?if ($_REQUEST['razdel']==2){?>
<b style='border-bottom:1px solid black;'>Текущие заказы</b>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='program.php?razdel=2'>Текущие заказы</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<?if ($_REQUEST['razdel']==3){?>
<b style='border-bottom:1px solid black;'>Архив заказов</b>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='program.php?razdel=3'>Архив заказов</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<?if ($_REQUEST['razdel']==4){?>
<b style='border-bottom:1px solid black;'>Настройки</b>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='program.php?razdel=4'>Настройки</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<a target='_blank' style='border-bottom:1px solid;text-decoration:none;' href='index.php?razdel=6'>Статистика</a>
</span>
<span style='margin-left:10px;'>
<a target='_blank' style='border-bottom:1px solid;text-decoration:none;' href='/diagramchatsezh2.php?m=0'>Диаграмма контактов Ёж</a>
</span>
<span style='margin-left:10px;'>
<a target='_blank' style='border-bottom:1px solid;text-decoration:none;' href='/sklad'>Склад</a>
</span>
<span style='margin-left:10px;'>
<a style='border-bottom:1px solid;text-decoration:none;' target="_blank" href='/bonusezh.php?id=<?=$_COOKIE["iduser"] ?>'>Бонусы</a>
</span>
<span style='margin-left:10px;'>
<a style='border-bottom:1px solid;text-decoration:none;' target="_blank" href='/ezh/obzvon.php'>Обзвон клиентов</a>
</span>

<span style='position:absolute;left:70%;' id='rest_top_span'>
<?
if (!isset($_GET['id'])){
  $r = mysql_query("select * from pr_city order by name limit 1");
}else{
  $order_id = intval($_GET['id']);
  $r = mysql_query("select c.* from pr_city c,pr_order o where o.id_city=c.id and o.id=$order_id");
}
$a = mysql_fetch_array($r);
$c_id = $a['id'];
$c_name = $a['name'];
?>
<?=show_link_rest($c_id,$c_name)?>
</span>

<a href='?logout=1' style='float:right;padding-right:20px;'>Выход</a>
</div>

<?
if ($_REQUEST['razdel']==1){
  if (isset($_REQUEST['id'])){
    $id_zakaz = intval($_REQUEST['id']);
  }else $id_zakaz = -1;
?>
<script>
function show_zakaz_tovar(id){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'show_zakaz_tovar',
      'id':id,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#zakaz_left').html(html);
      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function clear_order(){
    $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'zakaz_client',
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#zakaz_middle').html('');
      $('#zakaz_right').html(html);
      $('#dt').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
      });
      $( "#dt" ).datepicker( $.datepicker.regional[ "ru" ] );
      show_zakaz_tovar($('#zakaz_city').val());
      $('#old_city').val($('#zakaz_city').val());
      $('#phone').mask('+7(999)999-99-99'); 

      $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
          'operation':'show_link_rest',
          'id':$('#zakaz_city').val(),
          'name':$('#zakaz_city').get(0).options[$('#zakaz_city').get(0).selectedIndex].text
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('#rest_top_span').html(html);
        },
        error:function(html){
          alert('Ошибка подключения!');
        },
      });

      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function add_zakaz(id,unit, emk=0, emkstring=0){
  if($('#zakaz_middle #zakaz'+id).length==0){
      $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'show_zakaz',
        'id':id,
        'unit':unit,
          'emk':emk,
          'emkstring':emkstring,
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('#zakaz_middle').append(html);
        gen_zakaz_text();
        $('body').css('cursor','default');
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
  }else{
    plusone(id,unit,0);
  }
}
function normalize_input(id,zero_flag,float_flag,done){
  obj = $('#'+id);
  val = obj.val();
  if (!float_flag)val = val.replace(/[^\d]+/,'');
  else val = val.replace(/[^\d\.]+/,'');
  if (!zero_flag && val=='0')val = 1;
  if(done==1){
    if (val>$('#num_copy'+id.replace('num','')).val())val = $('#num_copy'+id.replace('num_copy','')).val();
  }
  obj.val(val);
  if(done==1){
    $('#ostatok_button'+id.replace('num','')).val("Остаток ("+val+")");
    $('#brak_button'+id.replace('num','')).val("Брак ("+val+")");
  }
}
function plusone(id,unit,done){
  obj = $('#num'+id);
  val = obj.val();
  if (unit==1){
    val = parseInt(val)+unit;
  }else{
    val = parseFloat(val)+unit;
    val = val.toFixed(1);
  }
  if(done==1){
    if (val>$('#num_copy'+id).val())val = $('#num_copy'+id).val();
  }
  obj.val(val);
  if(done==1){
    $('#ostatok_button'+id).val("Остаток ("+val+")");
    $('#brak_button'+id).val("Брак ("+val+")");
  }
  gen_zakaz_text();
}
function minusone(id,unit,done){
  obj = $('#num'+id);
  val = obj.val();
  if (val<=unit){
    if(done!=1)obj.parents('.zakaz').remove();
  }else{
    if (unit==1){
      val = parseInt(val)-unit;
    }else{
      val = parseFloat(val)-unit;
      val = val.toFixed(1);
    }
    if(done==1){
      if (val>$('#num_copy'+id).val())val = $('#num_copy'+id).val();
    }
    obj.val(val);
  }
  if(done==1){
    $('#ostatok_button'+id).val("Остаток ("+val+")");
    $('#brak_button'+id).val("Брак ("+val+")");
  }
  gen_zakaz_text();
}
function plusone_rest(id,unit,done){
  obj = $('#num_rest'+id);
  val = obj.val();
  if (unit==1){
    val = parseInt(val)+unit;
  }else{
    val = parseFloat(val)+unit;
    val = val.toFixed(1);
  }
  if(done==1){
    if (val>$('#num_rest_copy'+id).val())val = $('#num_rest_copy'+id).val();
  }
  obj.val(val);
  if(done==1){
    $('#ostatok_button'+id).val("Остаток ("+val+")");
    $('#brak_button'+id).val("Брак ("+val+")");
  }
  gen_zakaz_text();
}
function minusone_rest(id,unit,done){
  obj = $('#num_rest'+id);
  val = obj.val();
  if (val<=unit){
    if(done!=1)obj.parents('.zakaz_rest').remove();
  }else{
    if (unit==1){
      val = parseInt(val)-unit;
    }else{
      val = parseFloat(val)-unit;
      val = val.toFixed(1);
    }
    if(done==1){
      if (val>$('#num_rest_copy'+id).val())val = $('#num_rest_copy'+id).val();
    }
    obj.val(val);
  }
  if(done==1){
    $('#ostatok_button'+id).val("Остаток ("+val+")");
    $('#brak_button'+id).val("Брак ("+val+")");
  }
  gen_zakaz_text();
}
function normalize_input_rest(id,zero_flag,float_flag,done){
  obj = $('#'+id);
  val = obj.val();
  if (!float_flag)val = val.replace(/[^\d]+/,'');
  else val = val.replace(/[^\d\.]+/,'');
  if (!zero_flag && val=='0')val = 1;
  if(done==1){
    if (val>$('#num_rest_copy'+id.replace('num_rest','')).val())val = $('#num_rest_copy'+id.replace('num_rest','')).val();
  }
  obj.val(val);
  if(done==1){
    $('#ostatok_button'+id.replace('num_rest','')).val("Остаток ("+val+")");
    $('#brak_button'+id.replace('num_rest','')).val("Брак ("+val+")");
  }
}
function change_zakaz_city(){
  if($('#zakaz_middle .zakaz').length>0)flag = confirm('Текущий список товаров будет сброшен. Продолжить?');
  else flag = true;
  if (flag){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'change_zakaz_city',
        'id':$('#zakaz_city').val(),
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('#zakaz_middle').html('');
        $('#dostavka').html(html);
        show_zakaz_tovar($('#zakaz_city').val());
        gen_zakaz_text();
        if($('#zakaz_city').get(0).options[$('#zakaz_city').get(0).selectedIndex].text=="Другие города"){
          $("#other_city_div").show();
        }else{
          $("#other_city_div").hide();
        }
        $('#old_city').val($('#zakaz_city').val());
        $('body').css('cursor','default');
      },
      error:function(html){
        $('#zakaz_city').val($('#old_city').val());
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'show_link_rest',
        'id':$('#zakaz_city').val(),
        'name':$('#zakaz_city').get(0).options[$('#zakaz_city').get(0).selectedIndex].text
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('#rest_top_span').html(html);
        $('body').css('cursor','default');
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
  }
  if(!flag){
    $('#zakaz_city').val($('#old_city').val());
  }
}
function gen_zakaz_text(){
  data = {};
  data['operation'] = 'gen_zakaz_text';
  data['zakaz_city'] = $('#zakaz_city').val();
  data['other_city_name'] = $('#other_city_name').val();
  data['phone'] = $('#phone').val().replace(/[^\d]/g,'').replace(/^7/,'');
  data['client_name'] = $('#client_name').val();
  data['comment'] = $('#comment').val();
  data['address'] = $('#address').val();
  data['dt_now'] = $('#dt_now').get(0).checked?1:0;
  data['dt'] = $('#dt').val();
  data['predoplata'] = $('#predoplata').get(0).checked?1:0;
  data['skidka'] = $('#skidka').val();
  $('.zakaz').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz/,'');
    data['zakaz'+z_id] = $('#num'+z_id).val();
  });
  $('.zakaz_rest').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz_rest/,'');
    data['zakaz_rest'+z_id] = $('#num_rest'+z_id).val();
  });
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#full_description').html(html);
      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function copyToClipboard(elem) {
//https://stackoverflow.com/questions/22581345/click-button-copy-to-clipboard-using-jquery
  // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    try {
    	  succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }
    
    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}
function save_order(flag,razdel){
  if ($('#phone').val().replace(/[^\d]/g,'').replace(/^7/,'')==''){
    alert('Телефон не может быть пустым');
    return false;
  }
  if ($('#client_name').val()==''){
    alert('Имя покупателя не может быть пустым');
    return false;
  }
  if ($('#address').val()==''){
    alert('Адрес покупателя не может быть пустым');
    return false;
  }
  if ($('.zakaz').length==0 && $('.zakaz_rest').length==0){
    alert('В заказ не добавлен ни один товар');
    return false;
  }
  if (razdel==3){
    flag_arhiv = 1;
    $('.zakaz').each(function(k,v){
      z_id = v.id;
      z_id = z_id.replace(/zakaz/,'');
      if($('#brak'+z_id).val()==0 && $('#ostatok'+z_id).val()==0 && $('#num'+z_id).val()!=$('#num_copy'+z_id).val()){
        flag_arhiv=0;
      }
    });
    $('.zakaz_rest').each(function(k,v){
      z_id = v.id;
      z_id = z_id.replace(/zakaz_rest/,'');
      if($('#brak'+z_id).val()==0 && $('#ostatok'+z_id).val()==0 && $('#num_rest'+z_id).val()!=$('#num_rest_copy'+z_id).val()){
        flag_arhiv=0;
      }
    });
    if(flag_arhiv==0){
      alert('Нельзя редактировать архивные заказы');
      return false;
    }
  }
  data = {};
  data['operation'] = 'save_order';
  data['id_order'] = $('#id_order').val();
  data['zakaz_city'] = $('#zakaz_city').val();
  data['phone'] = $('#phone').val().replace(/[^\d]/g,'').replace(/^7/,'');
  data['client_name'] = $('#client_name').val();
  data['other_city_name'] = $('#other_city_name').val();
  data['comment'] = $('#comment').val();
  data['address'] = $('#address').val();
  data['dt_now'] = $('#dt_now').get(0).checked?1:0;
  data['dt'] = $('#dt').val();
  data['predoplata'] = $('#predoplata').get(0).checked?1:0;
  data['VK'] = $('#VK').get(0).checked?1:0;
  data['skidka'] = $('#skidka').val();
  $('.zakaz').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz/,'');
    data['zakaz'+z_id] = $('#num'+z_id).val();
  });
  $('.zakaz_rest').each(function(k,v){
    z_id = v.id;
    z_id = z_id.replace(/zakaz_rest/,'');
    data['zakaz_rest'+z_id] = $('#num_rest'+z_id).val();
  });
  $('.brak').each(function(k,v){
    brak_id = v.id;
    brak_id = brak_id.replace(/brak/,'');
    data['brak'+brak_id] = $('#brak'+brak_id).val();
  });
  $('.ostatok').each(function(k,v){
    ostatok_id = v.id;
    ostatok_id = ostatok_id.replace(/ostatok/,'');
    data['ostatok'+ostatok_id] = $('#ostatok'+ostatok_id).val();;
  });
  $('.from_rest').each(function(k,v){
    from_rest_id = v.id;
    from_rest_id = from_rest_id.replace(/from_rest/,'');
    data['from_rest'+from_rest_id] = $('#from_rest'+from_rest_id).val();;
  });
  $('.from_rest_first').each(function(k,v){
    from_rest_first_id = v.id;
    from_rest_first_id = from_rest_id.replace(/from_rest_first/,'');
    data['from_rest_first'+from_rest_first_id] = $('#from_rest_first'+from_rest_first_id).val();
  });
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      if(flag)clear_order();
      $('body').css('cursor','default');
      if(!flag)location.href = '<?=$_SERVER['PHP_SELF']?>?razdel='+razdel;
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function cancel_order(razdel){
  location.href = '<?=$_SERVER['PHP_SELF']?>?razdel='+razdel;
}
function get_client(){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'get_client',
      'phone':$('#phone').val().replace(/[^\d]/g,'').replace(/^7/,''),
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      if (html!=''){
        $('body').append(html);
        $('#id_client').val($('#tmp_id_client').html());
        $('#client_name').val($('#tmp_name').html());
        $('#address').val($('#tmp_address').html().replace(/&nbsp;/g,' '));
        $('#num_order').html('Заказов: '+$('#tmp_number').html());
        //$('#sendprice').html($('#tmp_sendprice').html());

        if ($('#tmp_sendprice').html()>0)
          document.getElementById('sendprice').checked=true;
        else
            document.getElementById('sendprice').checked=false;
          if ($('#tmp_warmclient').html()>0)
              document.getElementById('warmclient').checked=true;
          else
              document.getElementById('warmclient').checked=false;
          if ($('#tmp_promokod').html()>0)
              document.getElementById('promokod').checked=true;
          else
              document.getElementById('promokod').checked=false;
        $('#tmp_client_div').remove();
      }else{
        $('#id_client').val('');
        $('#client_name').val('');
        $('#address').val('');
        $('#num_order').val('Заказов: 0');
      }
      gen_zakaz_text();
    },
  });
}
function save_client(){
    var ch=document.getElementById('sendprice').checked;
    var warm=document.getElementById('warmclient').checked;
    var promo=document.getElementById('promokod').checked;
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'save_client',
      'id_client':$('#id_client').val(),
      'phone':$('#phone').val().replace(/[^\d]/g,'').replace(/^7/,''),
      'client_name':$('#client_name').val(),
      'address':$('#address').val(),
      'sendprice':ch,
      'warmclient':warm,
      'promokod':promo,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function del_order(){
  id = parseInt($('#id_order').val());
  if (id>0){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'del_order',
        'id':id,
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        location.href = '<?=$_SERVER['PHP_SELF']?>?razdel=1';
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
  }
}
function show_rest(id){
  win = window.open("/rest.php?id="+id,"rest_win","height=600,width=640,menubar=no,toolbar=no,location=no,scrollbars=yes");
  win.onunload = function(){
    gen_zakaz_text();
  }
}
$(function() {
  $('#dt').datepicker( {
    showOtherMonths: true,
    selectOtherMonths: true,
  });
  $( "#dt" ).datepicker( $.datepicker.regional[ "ru" ] );
  $('#old_city').val($('#zakaz_city').val());
  $('#phone').mask('+7(999)999-99-99'); 
});
</script>
<div style='display:flex;'>
<?
  if ($id_zakaz>0){
    $r0 = mysql_query("select * from pr_order where id=$id_zakaz");
    if (mysql_num_rows($r0)>0){
      $a0 = mysql_fetch_array($r0);
      $id_city0 = $a0['id_city'];
    }else $id_city0=0;
  }else{
    $r0 = mysql_query("select * from pr_city order by name limit 1");
    if (mysql_num_rows($r0)>0){
      $a0 = mysql_fetch_array($r0);
      $id_city0 = $a0['id'];
    }else $id_city0=0;
  }
?>
    <div class='zakaz_left' id='zakaz_left' style="overflow: auto; height: 110vh;"><?=show_zakaz_tovar($id_city0)?></div>
    <div class='zakaz_middle' id='zakaz_middle' style="overflow: auto; height: 110vh;"><?=show_zakaz_middle($id_zakaz)?></div>
    <div class='zakaz_right' id='zakaz_right'><?=zakaz_client($id_zakaz)?></div>
</div>
<?
}
if ($_REQUEST['razdel']==2){
?>
<script>
function set_otgruzka(id,dt){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'set_otgruzka',
      'id':id,
      'dt':dt,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#city'+id).html(html);
      $( "#dt_otgruzka"+id ).datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
          var date = $(this).datepicker('getDate');
          date = Date.parse(date);
          set_otgruzka(this.id.replace('dt_otgruzka',''),date);
        }
      });
      $( "#dt_otgruzka"+id ).datepicker( $.datepicker.regional[ "ru" ] );
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function city_orders_done(id){
  str = '';
  $('.order_selected').each(function(k,o){
    if (o.checked){
      str += 'id_order[]='+o.id.replace('sel','')+'&';
    }
  });
  str += 'operation=city_orders_done&id='+id;
    dt="!___!";
    cou=0;
    dtpiker=document.getElementById("dt_otgruzka"+id).value;
      <?php $rdate = mysql_query("SELECT dt_otgruzka, id FROM pr_city WHERE id=1"); $adate=mysql_fetch_array($rdate); $idc=1; ?>
      <?php $rdate2 = mysql_query("SELECT count(dt) as ttt FROM pr_order WHERE id_city=".$idc." and dt='".$adate["dt_otgruzka"]."' and done=1"); $adate2=mysql_fetch_array($rdate2); ?>
      dt1="<?=date("d.m.Y", strtotime($adate["dt_otgruzka"]))?>";
      id1="<?php echo $idc; ?>";
      cou1="<?=$adate2["ttt"]?>";
      <?php $rdate = mysql_query("SELECT dt_otgruzka, id FROM pr_city WHERE id=2"); $adate=mysql_fetch_array($rdate); $idc=2; ?>
    <?php $rdate2 = mysql_query("SELECT count(dt) as ttt FROM pr_order WHERE id_city=".$idc." and dt='".$adate["dt_otgruzka"]."' and done=1"); $adate2=mysql_fetch_array($rdate2); ?>
        dt2="<?=date("d.m.Y", strtotime($adate["dt_otgruzka"]))?>";
        id2="<?php echo $idc; ?>";
        cou2="<?=$adate2["ttt"]?>";
        <?php $rdate = mysql_query("SELECT dt_otgruzka, id FROM pr_city WHERE id=9"); $adate=mysql_fetch_array($rdate); $idc=9; ?>
    <?php $rdate2 = mysql_query("SELECT count(dt) as ttt FROM pr_order WHERE id_city=".$idc." and dt='".$adate["dt_otgruzka"]."' and done=1"); $adate2=mysql_fetch_array($rdate2); ?>
        dt9="<?=date("d.m.Y", strtotime($adate["dt_otgruzka"]))?>";
    id9="<?php echo $idc; ?>";
    cou9="<?=$adate2["ttt"]?>";
    <?php $rdate = mysql_query("SELECT dt_otgruzka, id FROM pr_city WHERE id=11"); $adate=mysql_fetch_array($rdate); $idc=11; ?>
    <?php $rdate2 = mysql_query("SELECT count(dt) as ttt FROM pr_order WHERE id_city=".$idc." and dt='".$adate["dt_otgruzka"]."' and done=1"); $adate2=mysql_fetch_array($rdate2); ?>
        dt11="<?=date("d.m.Y", strtotime($adate["dt_otgruzka"]))?>";
    id11="<?php echo $idc; ?>";
    cou11="<?=$adate2["ttt"]?>";
    <?php $rdate = mysql_query("SELECT dt_otgruzka, id FROM pr_city WHERE id=12"); $adate=mysql_fetch_array($rdate); $idc=12; ?>
    <?php $rdate2 = mysql_query("SELECT count(dt) as ttt FROM pr_order WHERE id_city=".$idc." and dt='".$adate["dt_otgruzka"]."' and done=1"); $adate2=mysql_fetch_array($rdate2); ?>
    id12="<?php echo $idc; ?>";
    dt12="<?=date("d.m.Y", strtotime($adate["dt_otgruzka"]))?>";
    cou12="<?=$adate2["ttt"]?>";
    if (id==id1){ dt=dt1; cou=cou1; }
    if (id==id2){ dt=dt2; cou=cou2; }
    if (id==id9){ dt=dt9; cou=cou9; }
    if (id==id11){ dt=dt11; cou=cou11; }
    if (id==id12){ dt=dt12; cou=cou12; }

    var now = new Date();
    var parts =dtpiker.split('.');
    var srav= new Date(parts[2], parts[1]-1, parts[0]);

    if (srav<=now) { showDialog(); }
    else {
        {
            $.ajax({
                type:'POST',
                url:'<?=$PHP_SELF?>',
                data:str,
                timeout:<?=$AJAX_TIMEOUT?>,
                error:function(html){
                    $('body').css('cursor','default');
                    alert('Ошибка подключения!');
                },
                success:function(html){
                    $('body').css('cursor','default');
                    $('#city'+id).replaceWith(html);
                    $( "#dt_otgruzka"+id ).datepicker( {
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        onSelect: function(dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            date = Date.parse(date);
                            set_otgruzka(this.id.replace('dt_otgruzka',''),date);
                        }
                    });
                    $( "#dt_otgruzka"+id ).datepicker( $.datepicker.regional[ "ru" ] );
                    var height = window.innerHeight - 240;
                    $('.unscroll').css('height',height+'px');
                },
            }); }
    }

    function showDialog() {
        var isReady = confirm("У вас стоит неверная дата. Вы ушли в прошлое. Желаете продолжить?");
        if (isReady==true){
    $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:str,
    timeout:<?=$AJAX_TIMEOUT?>,
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
    success:function(html){
      $('body').css('cursor','default');
      $('#city'+id).replaceWith(html);
      $( "#dt_otgruzka"+id ).datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
          var date = $(this).datepicker('getDate');
          date = Date.parse(date);
          set_otgruzka(this.id.replace('dt_otgruzka',''),date);
        }
      });
      $( "#dt_otgruzka"+id ).datepicker( $.datepicker.regional[ "ru" ] );
      var height = window.innerHeight - 240;
      $('.unscroll').css('height',height+'px');
    },
  }); }
    }
}
$(function() {
  $('.dt_otgruzka').datepicker( {
    showOtherMonths: true,
    selectOtherMonths: true,
    onSelect: function(dateText, inst) { 
      var date = $(this).datepicker('getDate');
      date = Date.parse(date);
      set_otgruzka(this.id.replace('dt_otgruzka',''),date);
    }
  });
  $( ".dt_otgruzka" ).datepicker( $.datepicker.regional[ "ru" ] );
});
</script>
<div class='city_orders_container' style="height:94vh;">
<?
  $r = mysql_query("select * from pr_city order by name");
  while ($a = mysql_fetch_array($r)){
    print city_orders($a['id']);
  }
?>
</div>
<?  
}

if ($_REQUEST['razdel']==3){
?>
<script>
function sendfile(id){
  var xhr = new XMLHttpRequest();
  xhr.timeout = 300000;
  xhr.onload = function(){
    document.getElementById("driver_bills"+id).innerHTML=xhr.responseText;
  }
  var formData = new FormData(document.getElementById("fileform"+id));
  formData.append("operation","sendfile");
  formData.append("id_order",id);
  xhr.open("POST", "<?=$_SERVER['PHP_SELF']?>");
  xhr.send(formData);
}
function driver_bill_change(id,value){
  if (value==1){
    window.open("/driver_bills.php?id="+id,"bills_win","height=1200,width=1020,menubar=no,toolbar=no,location=no,scrollbars=yes");
  }
  if (value==2){
    document.getElementById("fileinput"+id).click();
  }
  if (value==3){
    if (confirm("Действительно удалить?")){
      var xhr = new XMLHttpRequest();
      xhr.timeout = 10000;
      xhr.onload = function(){
        document.getElementById("driver_bills"+id).innerHTML=xhr.responseText;
      }
      var formData = new FormData(document.getElementById("fileform"+id));
      formData.append("operation","delfile");
      formData.append("id_order",id);
      xhr.open("POST", "<?=$_SERVER['PHP_SELF']?>");
      xhr.send(formData);
    }
  }
}
function order_check(id,val){
  var xhr = new XMLHttpRequest();
  xhr.timeout = 10000;
  var formData = new FormData();
  formData.append("operation","order_check");
  formData.append("id",id);
  formData.append("checked",val?1:0);
  xhr.open("POST", "<?=$_SERVER['PHP_SELF']?>");
  xhr.send(formData);
}
function close_week(id_city,dt){
  if(!confirm("Действительно закрыть?"))return false;
  o = $('#city'+id_city+"-"+dt);
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'close_week',
      'id_city':id_city,
      'html':o.html(),
      'dt':dt,
      'limit':<?=intval($_GET['limit'])?>
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#city'+id_city).html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  });
}
</script>
<div class='city_orders_container'>
<?
  $limit = intval($_REQUEST['limit']);
  $LIMIT = 10;
  if ($limit==0)$limit = $LIMIT;
  $r = mysql_query("select * from pr_city order by name");
  $max = 0;
  while ($a = mysql_fetch_array($r)){
    $max_r = mysql_query("select count(distinct dt) cnt from pr_order where done=1 and id_city=".$a['id']);
    if (mysql_num_rows($max_r)>0){
      $max_a = mysql_fetch_array($max_r);
      if($max<$max_a['cnt'])$max = $max_a['cnt'];
    }
    print city_orders_done($a['id'],$a['allow_bills'],$limit);
  }
  $limit1 = $limit+$LIMIT;
?>
</div>
<?
  if($limit<$max){
?>
<div style="width:100%;text-align:center;padding: 20px 0;">
<a href='?razdel=3&limit=<?=$limit1?>'>Показать еще <?=$LIMIT?></a>
</div>
<?
  }
?>
<?  
}

if ($_REQUEST['razdel']==4){
?>
<script>
function add_city(){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'add_city',
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#cities div.city:last').after(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}

function add_emk(){
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
            'operation':'add_emk',
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            $('#xem').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}

function add_emk7(){
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
            'operation':'add_emk7',
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            $('#xem7').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}

function remove_city(id){
  obj = $('#dostavka'+id);
  obj.parents('.city').remove();
  $('#cities').after("<input type='hidden' class='c_input' id='del"+id+"' value='1'>");
}
function save_cities(){
  flag1 = true;
  flag2 = true;
  flag3 = true;
  $('.c_input').each(function(i,o){
    if(o.id.search(/^dostavka/)!=-1 && o.value==''){
      flag1 = false;
    }
    if(o.id.search(/^city/)!=-1 && o.value==''){
      flag2 = false;
    }
    if(o.id.search(/^sum_less/)!=-1 && o.value!=''){
      sum_less = o.value;
      id_more = o.id.replace('sum_less','sum_more');
      sum_more = $('#'+id_more).val();
      if (sum_more!='' && parseInt(sum_less)>=parseInt(sum_more)){
        flag3 = false;
      }
    }
  });
  if (!flag1){
    alert('Стоимость доставки не может быть пустой');
    return false;
  }
  if (!flag2){
    alert('Название города не может быть пустым');
    return false;
  }
  if (!flag3){
    alert('Условие акции "сумма больше" должно быть больше условия "сумма меньше"');
    return false;
  }
  data = {};
  $('.c_input').each(function(i,o){
    if (o.id.search('allow_bills')==-1){
      data[o.id] = o.value;
    }else{
      data[o.id] = o.checked?1:0;
    }
  });
  data['operation'] = 'save_cities';
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#product_left').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}

function save_emkost(id, emk){
    data = {
        'operation':'save_emkost',
        'idem':id,
        'emk':emk
    };
    //data['operation'] = 'save_emkost';
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:data,
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            $('#product_left').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}

function reset_cities(){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'reset_cities',
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#product_left').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function set_show_tovar(flag,id){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'set_show_tovar',
      'flag':flag,
      'id':id,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#product_middle').html(html);
      },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function show_tovar_form(id){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'show_tovar_form',
      'id':id,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#product_right').html(html);
      rightform_startup();
      $('body').css('cursor','default');
//        document.getElementById("archivall").style.display = "block";
//        document.getElementById("archiv").style.display = "block";
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function remove_tovar_city(obj,tovar_id,id){
  obj.parents('.tovar_city').remove();
  $('#tovar_cities').after("<input type='hidden' class='t_c_input' name='del_"+tovar_id+'_'+id+"' id='del_"+tovar_id+'_'+id+"' value='1'>");
}
function add_tovar_city(id){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'add_tovar_city',
      'id':id,
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#tovar_cities div.tovar_city:last').after(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function reset_tovar_cities(id){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'reset_tovar_cities',
      'id':id
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#product_right').html(html);
      rightform_startup();
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}

function reset_tovar_to_archive(id) {
    isReady = false;
    var isReady = confirm("Вы желаете убрать товар в архив? После этого он не будет доступен");
    if (isReady == true){
        $.ajax({
            type: 'POST',
            url: '<?=$PHP_SELF?>',
            data: {
                'operation': 'reset_tovar_to_archive',
                'id': id
            },
            timeout:<?=$AJAX_TIMEOUT?>,
            success: function (html) {
            //    $('body').css('cursor', 'default');
            //    $('#product_right').html(html);
            //    rightform_startup();
                alert("Товар отправился в архив");
            },
            error: function (html) {
                $('body').css('cursor', 'default');
                alert('Ошибка подключения!');
            },
        });
    }
}


function set_tovar_to_archive(id) {
        $.ajax({
            type: 'POST',
            url: '<?=$PHP_SELF?>',
            data: {
                'operation': 'set_tovar_to_archive',
                'id': id
            },
            timeout:<?=$AJAX_TIMEOUT?>,
            success: function (html) {
          //      $('body').css('cursor', 'default');
          //      $('#product_right').html(html);
          //      rightform_startup();
                alert("Товар востановлен из архива.");
            },
            error: function (html) {
                $('body').css('cursor', 'default');
                alert('Ошибка подключения!');
            },
        });
}

function set_tovar_to_child(id) {
    var child=document.getElementById('emchild').value;
    var name=document.getElementById('name'+id).value;
    var seb=document.getElementById('sebemk').value;
    var price=document.getElementById('priceemk').value;
    $.ajax({
        type: 'POST',
        url: '<?=$PHP_SELF?>',
        data: {
            'operation': 'set_tovar_to_child',
            'id': id,
            'child':child,
            'name': name,
            'seb': seb,
            'priceemk': price
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success: function (html) {
            alert(html);
            set_show_tovar(0);
        },
        error: function (html) {
            $('body').css('cursor', 'default');
            alert('Ошибка подключения!');
        },
    });
}


function readUrl(input){
  if (input.files && input.files[0]){
    var reader = new FileReader();

    reader.onload = function(e){
      $('#img_file').attr('src',e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}
function rightform_startup(){
  $('#rightform').on('submit', function(e){
    right_tovar_id = $('#right_tovar_id').val();
    if ($('#price'+right_tovar_id).val()==''){
      alert('Цена товара не может быть пустой');
      return false;
    }
    if ($('#name'+right_tovar_id).val()==''){
      alert('Наименование товара не может быть пустым');
      return false;
    }
    e.preventDefault();
    var $that = $(this),
    formData = new FormData($that.get(0)); // создаем новый экземпляр объекта и передаем ему нашу форму (*)
    $.ajax({
      url: $that.attr('action'),
      type: $that.attr('method'),
      contentType: false, // важно - убираем форматирование данных по умолчанию
      processData: false, // важно - убираем преобразование строк по умолчанию
      data: formData,
      dataType: 'json',
      success: function(json){
        if(json){
          set_show_tovar(0);
          $that.replaceWith(json);
          rightform_startup();
        }
      }
    });
  });
  $('#pic_file').change(function(){
    readUrl(this);
  })
}
$(function(){
  rightform_startup();
});
</script>
<div style='display:flex;'>
  <div class='product_left' id='product_left' style='width:380px;'><?=show_cities()?></div>
  <div class='product_middle' id='product_middle' style='width:57%;'><?=show_tovar()?></div>
  <div class='product_right' id='product_right' style='width:370px;'><?=tovar_form()?></div>
</div>
<?
}
?>
</body>
</html>
<?
function show_cities(){
?>
    <div style='text-align:center; margin-top: -35px;'>
        <h4 style="font-size: 15px;">Показать продукцию</h4>
        <input type='button' name='tovar_radio' id='tovar_radio0' value="В продаже" style="width:80%; height: 30px; margin-bottom: 5px;" onclick='set_show_tovar(0)'>
        <br/>
        <input type='button' name='tovar_radio' id='tovar_radio2' value="Не в продаже" style="width:80%; height: 30px; margin-bottom: 5px;" onclick='set_show_tovar(2)'>
        <br/>
        <input type='button' name='tovar_radio' id='tovar_radio3' value="Архив" style="width:80%; height: 30px; margin-bottom: 5px;" onclick='set_show_tovar(3)'>
        <br/>
        <br/>
    </div>
<script>
    disemkenterflag=0;
    function disemkenter(flag) {
        var st='';
        if (flag>0) {
            st='table';
            disemkenterflag=1;
            document.getElementById('ekost1status').innerHTML='▲ свернуть';
        } else {
            st='none';
            disemkenterflag=0;
            document.getElementById('ekost1status').innerHTML='▼ развернуть';
        }
        document.getElementById('ekost1').style.display=st;
    }

</script>
    <div style='width:100%;text-align:center;float:left; border: 1px solid; background: fixed; padding: 3px; border-color: darkgrey; cursor: pointer; margin: 1px;' onclick="disemkenter(!disemkenterflag);">Емкости <div id="ekost1status" style="float: right">▼ развернуть</div></div><br>
    <div id="ekost1" style='border:none; background:none; display: none; text-align: center; width: 100%; border: 2px solid; border-color: ghostwhite;'>
        <div>
            <?php
            $qemk = "SELECT * FROM ezh_emkost order by emkost asc";
            $remk = mysql_query($qemk);
            while($aemk = mysql_fetch_array($remk))
            {
            ?>
            <div align="center" style="border: 1px solid; margin: 4px 0px;">
                <table>
                    <tr>
                        <td>
                            <input type='text' id='emk<?=$aemk['id'] ?>' value='<?=$aemk['emkost'] ?>' placeholder='введите емкость' style='width:90%; margin:0 2px;'>
                        </td>
                        <td>
                            <button style="float: right; padding: 4px;" onclick='save_emkost(<?=$aemk['id'] ?>, document.getElementById("emk<?=$aemk['id'] ?>").value)'>Сохранить</button>
                        </td>
                    </tr>
                </table>
            </div>
                <?php
            }
            ?>
            <div class="xem" id="xem"></div>
            <br>
            <button onclick='add_emk();'>Добавить емкость</button>
        </div>
        <br><br>
    </div>


    <script>
        discityenterflag=0;
        function discityenter(flag) {
            var st='';
            if (flag>0) {
                st='table';
                discityenterflag=1;
                document.getElementById('city1status').innerHTML='▲ свернуть';
            } else {
                st='none';
                discityenterflag=0;
                document.getElementById('city1status').innerHTML='▼ развернуть';
            }
            document.getElementById('cityshow').style.display=st;
        }

    </script>
    <div style='width:100%;text-align:center;float:left; border: 1px solid; background: fixed; padding: 3px; border-color: darkgrey; cursor: pointer; margin: 1px;' onclick="discityenter(!discityenterflag);">Города <div id="city1status" style="float: right">▼ развернуть</div></div><br>
    <div id="cityshow" style=" display: none;  border: 2px solid; border-color: ghostwhite; width: 100%;">
<div class='cities_white' style='border:none; background:none;'>
 <div id='cities'>
  <div style='background-color:#DDDDDD;'>
   <div style='width:40%;text-align:center;float:left;'>Город</div>
   <div style='width:20%;text-align:center;float:left;'>&nbsp;</div>
   <div style='width:30%;text-align:center;float:left;'>Цена доставки</div>
   <div style='clear:both;'></div>
  </div>
  <div class='cities_wrapper' id='cities_wrapper'>
   <div class='city'></div>
<?
  $r = mysql_query("select id from pr_city order by name");
  while($a = mysql_fetch_array($r)){
    $id_city = $a['id'];
?>
<?=show_city($id_city)?>
<?
  }
?>
  </div>
  <button onclick='add_city();'>Добавить<br/> город</button>
 </div>
</div>
<div style='text-align:right;'>
<input style='padding:10px;' type='button' value='Сохранить' onclick='save_cities();'>&nbsp;<input style='padding:10px;' type='button' value='Отменить' onclick='reset_cities();'>
</div>
</div>
<?
}
function show_city($id=-1){
  $r = mysql_query("select * from pr_city where id=$id");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $flag = 1;
    $name = $a['name'];
    $dostavka = $a['dostavka'];
    $fact_dostavka = $a['fact_dostavka'];
    $sum_less = $a['sum_less'];
    $sum_more = $a['sum_more'];
    $price_less = $a['price_less'];
    $price_more = $a['price_more']; 
    $allow_bills = $a['allow_bills'];
  }else{
    $id = -rand();
    $flag = 0;
    $name = '';
    $dostavka = '';
    $fact_dostavka = '';
    $sum_less = '';
    $sum_more = '';
    $price_less = '';
    $price_more = ''; 
    $allow_bills = 0;
  }
?>
<div style='border:1px solid;margin:5px 1px 0 1px;;padding:5px 10px 5px 10px;' class='city'>
 <div style='padding:2px 0;'>
  <div style='width:30%;text-align:center;float:left; font-size:13px; font-weight:bold;'><?if($flag==1){?><?=$name?><?}else{?><input type='text' id='city<?=$id?>' class='c_input' value='' placeholder='город' style='width:150px;'><?}?></div>
  <div style='width:35%;text-align:center;float:left;'>&nbsp;</div>
  <div style='width:25%;text-align:center;float:left;'><input type='text' id='dostavka<?=$id?>' class='c_input' value='<?=$dostavka?>' placeholder='цена' style='width:90%; margin:0 2px;'></div>
  <div style='clear:both;'></div>
 </div>
 <div style='padding:2px 0;'>
  <div style='width:30%;text-align:center;float:left; font-size:12px;'>Фактическая доставка</div>
  <div style='width:35%;text-align:center;float:left;'>&nbsp;</div>
  <div style='width:25%;text-align:center;float:left;'><input type='text' id='fact_dostavka<?=$id?>' class='c_input' value='<?=$fact_dostavka?>' placeholder='цена' style='width:90%; margin:0 2px;'></div>
  <div style='clear:both;'></div>
 </div>
 <div style='padding:2px 0;'>
  <div style='width:40%;text-align:center;float:left; font-size:12px;'>При сумме менее</div>
  <div style='width:25%;text-align:center;float:left;'><input type='text' id='sum_less<?=$id?>' class='c_input' value='<?=$sum_less?>' placeholder='сумма' style='width:90%; margin: 0 2px;'></div>
  <div style='width:25%;text-align:center;float:left;'>
   <input type='text' id='price_less<?=$id?>' class='c_input' value='<?=$price_less?>' placeholder='цена' style='width:90%; margin:0 2px;'>
  </div>
  <div style='width:7%;text-align:center;float:left; padding-left:3%'>
   <input type='button' value='X' onclick='remove_city(<?=$id?>);'>
  </div>
  <div style='clear:both;'></div>
 </div>
 <div style='padding:2px 0;'>
  <div style='width:40%;text-align:center;float:left; font-size:12px;'>При сумме более</div>
  <div style='width:25%;text-align:center;float:left;'><input type='text' id='sum_more<?=$id?>' class='c_input' value='<?=$sum_more?>' placeholder='сумма' style='width:90%; margin:0 2px;'></div>
  <div style='width:25%;text-align:center;float:left;'><input type='text' id='price_more<?=$id?>' class='c_input' value='<?=$price_more?>' placeholder='цена' style='width:90%; margin:0 2px;'></div>
  <div style='clear:both;'></div>
 </div>
 <div style='padding:2px 0;'>
  <div style='width:30%;text-align:center;float:left; font-size:12px;'><label for='allow_bills<?=$id?>'>Загружать чеки</label></div>
  <div style='width:25%;text-align:center;float:left;'><input type='checkbox' id='allow_bills<?=$id?>' class='c_input' value=1<?if($allow_bills==1){?> checked<?}?>></div>
  <div style='width:35%;text-align:center;float:left;'>&nbsp;</div>
  <div style='clear:both;'></div>
 </div>
</div>
<?
}

function show_emk($id=0){
    ?>
    <div align="center" style="border: 1px solid; margin: 4px 0px;">
        <table>
            <tr>
                <td>
                    <input type='text' id='emk0' value='' placeholder='введите емкость' style='width:90%; margin:0 2px;'>
                </td>
                <td>
                    <button style="float: right; padding: 4px;" onclick='save_emkost(0, document.getElementById("emk0").value)'>Сохранить</button>
                </td>
            </tr>
        </table>
    </div>
    <?
}

function show_emk7($id=0){
    ?>
    <div align="center" style="border: 1px solid; margin: 4px 0px;">
        <table>
            <tr>
                <td>
                    <select size="1" name="Etalon" style="margin-bottom: 6px;" >
                        <option value="0" disabled>Выберите емкость</option>
                        <?php
                        $remk = mysql_query("SELECT * FROM ezh_emkost order by emkost");
                        while ($aemk = mysql_fetch_array($remk)){
                            echo  "<option ";
                            //  if ($usercity==$amanager['id']) echo 'selected ';
                            echo "value='".$aemk['id']."' >".$aemk['emkost']."</option>";
                        }
                        ?>
                        </select>
                </td>
            </tr>
            <tr>
                <td>
                    <button style="float: right; padding: 4px;" onclick='save_emkost(0, document.getElementById("emk0").value)'>Сохранить</button>
                </td>
            </tr>
        </table>
    </div>
    <?
}

function show_tovar($flag=0,$id=-1){
  if ($flag==1)$q = "select t.*,c.*,sum(o.number)cnt from pr_tovar t left join pr_tovar_city c on t.id=c.id_tovar left join pr_order_tovar o on o.id_tovar=t.id where t.pokaz>0 and t.parent=0 group by t.id order by cnt desc";
  else $q = "select t.*,c.*,sum(o.number)cnt from pr_tovar t left join pr_tovar_city c on t.id=c.id_tovar left join pr_order_tovar o on o.id_tovar=t.id where t.active=1 and t.pokaz>0 and t.parent=0 group by t.id order by cnt desc";
  if ($flag==2) $q = "select t.*,c.*,sum(o.number)cnt from pr_tovar t left join pr_tovar_city c on t.id=c.id_tovar left join pr_order_tovar o on o.id_tovar=t.id where t.active=0 and t.pokaz>0 and t.parent=0 group by t.id order by cnt desc";
  if ($flag==3) $q = "select t.*,c.*,sum(o.number)cnt from pr_tovar t left join pr_tovar_city c on t.id=c.id_tovar left join pr_order_tovar o on o.id_tovar=t.id where t.pokaz=0 and t.parent=0 group by t.id order by cnt desc";

    if ($flag==1 || $flag==2 || $flag==0) { ?>
        <style> .archiv1{ display: none; } </style>
    <?php    }

if ($flag==3) { ?>
<style> .archiv{ display: none; } .archivall{ display: none; } </style>
<?php    }


  $r = mysql_query($q);
?>
    <div class='product_middle_main'>
<?
  while($a = mysql_fetch_array($r)){
    $id_tovar = $a['id'];
    $active = $a['active'];
?>
     <div class='pic_container' style="width: 180px; height: 145px;" >
      <img onclick='show_tovar_form(<?=$id_tovar?>);' class='pic'<?if($active==0){?> style='opacity:0.5; transform: translate(0, 0) !important; top: 0 !important; left: 0 !important; float: left !important;'<?}?><?if($id==$id_tovar){ ?> src='/program_picture.php?id=<?=$id_tovar?>&salt=<?=rand()?>'<? } else{?> src='/pic<?=$id_tovar?>.jpg'<?}?>  style="transform: translate(0, 0) !important; top: 0 !important; left: 0 !important; float: left !important;">
         <?php $rmod = mysql_query("SELECT *, t.id as tid FROM `pr_tovar` t, ezh_emkost e WHERE t.`idemk`=e.id and t.parent=$id_tovar  order by e.emkost");
      while($amod = mysql_fetch_array($rmod)){
         ?>
         <button class="prodchild" style=""  onclick='show_tovar_form(<?=$amod['tid']; ?>);' ><?=$amod['emkost']; ?></button>
          <?php } ?>
     <div style="margin-top: 125px; font-size: 11px;" onclick='show_tovar_form(<?=$id_tovar?>);'>
     <span style="margin-left: 10px"><?=$a['name']; ?></span>
     </div>
     </div>
<?
  }
?>
    </div>
    <div style='text-align:center;'>
 <!--     <input type='radio' name='tovar_radio' id='tovar_radio0'<?if($flag==0){?> checked<?}?> value=0 onchange='set_show_tovar(0)'><label for='tovar_radio0'>В продаже</label> -->
      &nbsp;
 <!--    <input type='radio' name='tovar_radio' id='tovar_radio1'<?if($flag==1){?> checked<?}?> value=1 onchange='set_show_tovar(1)'><label for='tovar_radio1'>Весь каталог</label> -->

 <!--     <input type='radio' name='tovar_radio' id='tovar_radio2'<?if($flag==2){?> checked<?}?> value=2 onchange='set_show_tovar(2)'><label for='tovar_radio2'>Не в продаже</label> -->

 <!--     <input type='radio' name='tovar_radio' id='tovar_radio3'<?if($flag==3){?> checked<?}?> value=3 onchange='set_show_tovar(3)'><label for='tovar_radio3'>Архив</label> -->
    </div>
<?
}
function tovar_form($id=-1){
    $pic='';
    if ($id==-1){
    $img = "/noimage.png"; 
    $active_checked = " checked";
    $by_weight_checked = '';
    $price = '';
    $self_price = '';
    $name = '';
    $description = '';
  }else{
    $img = "/program_picture.php?id=$id&salt=".rand();
    $r = mysql_query("select * from pr_tovar where id=$id");
    if (mysql_num_rows($r)>0){
      $a = mysql_fetch_array($r);
      $active_checked = ($a['active']==1)?' checked':'';
      $by_weight_checked = ($a['by_weight']==1)?' checked':'';
      $price = ($a['price']!='')?intval($a['price']):'';
      $self_price = ($a['self_price']!='')?intval($a['self_price']):'';
      $name = $a['name'];
      $parentx = $a['parent'];
      $description = $a['description'];
      $pic=$a['picture'];
    }else{
      $active_checked = '';
      $by_weight_checked = '';
      $price = '';
      $self_price = '';
      $name = '';
      $description = '';
    }
  }

  if ($pic=="") $img = "/noimage.png";
    $qkosm = "SELECT * FROM pr_tovar t, ezh_emkost e where t.id=$id and e.id=t.idemk";
    $rkosm = mysql_query($qkosm);
    $akosm = mysql_fetch_array($rkosm);
    $etalon=$akosm['idemk'];
    $etalonname=$akosm['emkost'];
?>
<form method="POST" id='rightform' action='<?=$_SERVER['PHP_SELF']?>' enctype='multipart/form-data'>
<div style='text-align:center;padding:10px;'>
<input type='button' value='Добавить новый' style='width:100%; padding: 4px 0px;' onclick='show_tovar_form(-1);'>
</div>
<div style='padding:0 10px 0 10px;'>
 <div style='float:left; padding-right:5px;'><label for='pic_file'><img id='img_file' src='<?=$img?>' style='cursor:pointer; width: 145px;'></label><input type='file' id='pic_file' name='pic_file' style='display:none;'></div>
 <div style='float:left; width:100px;'>
  <div><input style='transform: scale(1.5);' class='t_c_input' type='checkbox' id='active<?=$id?>' name='active<?=$id?>' value=1<?=$active_checked?>><label for='active<?=$id?>'>В продаже</label></div>
    <?php if ($parentx==0) { ?>
  <div style='padding-bottom:10px;'><input style='transform: scale(1.5);' class='t_c_input' type='checkbox' id='by_weight<?=$id?>' name='by_weight<?=$id?>' value=1<?=$by_weight_checked?>><label for='by_weight<?=$id?>'>На вес</label></div>
<?php } ?>
    <?php if (($parentx==0) && ($by_weight_checked!=" checked")) { ?>
     Выберите емкость  <select size="1" name="Etalon77<?=$id?>" style="margin-bottom: 6px;" >
         <option value="0" disabled>Выберите емкость</option>
         <?php
         $remk = mysql_query("SELECT * FROM ezh_emkost order by emkost");
         while ($aemk = mysql_fetch_array($remk)){
             echo  "<option ";
             if ($etalon==$aemk['id']) echo 'selected ';
             echo "value='".$aemk['id']."' >".$aemk['emkost']."</option>";
         }
         ?>
     </select><br>
    <?php } else { echo "<input type='hidden' name='Etalon77".$id."' value='".$etalon."'>"; } ?>
  <div style='padding-bottom:10px;'><input style='width:100px;' class='t_c_input' type='text' id='price<?=$id?>' name='price<?=$id?>' value='<?=$price?>' placeholder='Цена эталона' oninput='this.value=this.value.replace(/[^\d]/g,"");'></div>
  <div><input style='width:100px;' <?php if (($_COOKIE['name']!="Admin") && ($_COOKIE['name']!="admin")) echo "readonly"; ?> class='t_c_input' type='text' id='self_price<?=$id?>' name='self_price<?=$id?>' value='<?=$self_price?>' placeholder='Себестоимость'  oninput='this.value=this.value.replace(/[^\d]/g,"");'></div>
 </div>
 <div style='clear:both;'></div>
</div>
<div style='text-align:center;padding:10px;'>
 <input class='t_c_input' type='text' id='name<?=$id?>' name='name<?=$id?>' value='<?=htmlspecialchars($name)?>' style='width:100%;' placeholder='Наименование'>
</div>
<a style='padding-left:0;;text-decoration:none;dashed;' href='' onclick='$("#description_div<?=$id?>").toggle();return false;'><button style='width:95%; margin:0 9px;'>Полное описание</button></a>
    <div id='description_div<?=$id?>' style='text-align:center;padding:10px;display:none;'>
 <textarea class='t_c_input' id='description<?=$id?>' name='description<?=$id?>' style='width:100%;height:50px;resize:vertical;'><?=$description?></textarea>
</div>
<!-- <div style='padding:10px 0 0 10px;'>Наличие в городах</div> -->
    <script>
        discitynallflag=0;
        function discitynall(flag) {
            var st='';
            if (flag>0) {
                st='block';
                discitynallflag=1;
                document.getElementById('city2status').innerHTML='▲ свернуть';
            } else {
                st='none';
                discitynallflag=0;
                document.getElementById('city2status').innerHTML='▼ развернуть';
            }
            document.getElementById('tovar_cities').style.display=st;
        }

    </script>
<div style='width:98%; font-size: 14px; text-align:center;float:left; border: 1px solid; background: fixed; padding: 3px; border-color: darkgrey; cursor: pointer; margin: 1px;  margin-top: 10px; margin-bottom: 10px;' onclick="discitynall(!discitynallflag);">Наличие в городах <div id="city2status" style="float: right">▼ развернуть</div></div><br>
    <div style='border:1px solid;margin:10px;background-color:white; display: none; ' id='tovar_cities'>
<div class='tovar_city'></div>
<?
  if ($id!=-1){
//print "select c.* from pr_tovar_city tc,pr_city c where tc.id_city=c.id and tc.id_tovar=$id";
    $r_city = mysql_query("select c.* from pr_tovar_city tc,pr_city c where tc.id_city=c.id and tc.id_tovar=$id");
    while($a_city = mysql_fetch_array($r_city)){
      $c_name = $a_city['name'];
      $c_id = $a_city['id'];
?>
<div class='tovar_city' style='background-color:#DDDDDD;padding:0 0 5px 5px;margin-bottom:5px;border-top:1px solid;border-bottom:1px solid;'>
 <div style='float:left;'><?=$c_name?></div>
 <div style='float:right;margin-right:10px;'><input type='button' value='X' onclick='remove_tovar_city($(this),<?=$id?>,<?=$c_id?>);'></div>
 <div style='clear:both;'></div>
</div>
<?
    }
  }
?>
<div style='background-color:#DDDDDD;padding:0 0 5px 5px;margin-bottom:5px;'>
 <input type='button' onclick='add_tovar_city(<?=$id?>);' value='Добавить город'>
<!--  <button onclick='add_tovar_city(<?=$id?>);'>Добавить<br/>город</button>-->
</div>
</div>
<div style='padding-left:10px;'>
<input type='submit' value='<?if($id!=-1){?>Сохранить<?}else{?>Добавить<?}?>'>
&nbsp;
<input type='button' value='Отмена' onclick='reset_tovar_cities(<?=$id?>);'>
</div>
<input type='hidden' name='operation' value='save_tovar_cities'>
<input type='hidden' name='right_tovar_id' id='right_tovar_id' value='<?=$id?>'>
    <!-- action='/?razdel=1' -->
    <?php if ($name!="" && $parentx==0) { ?>
    <div class="archivall" id="archivall" align="center"><input type='button' class="archiv" id="archiv" value='Убрать товар в архив' onclick='reset_tovar_to_archive(<?=$id?>); window.location.reload(); '>
        <img src="/8835401_orig1.png" style="width: 10px; height: 10px; " onmouseover="mouseto(event)" onmouseout="mouseout(event)" />

        <p class="uved" style="display: none;" id="uved">После удаления товара в архив вы перестанете видеть данный товар в во всех вкладках за исключением архива. Так же данный товар не будет показываться в диаграмме продаж.</p>
    </div>
    <script>
        function mouseto(event) {
            var elem = document.getElementById('uved');
            elem.style.display = 'block';
            //document.body.style.display = "none";
        }
        function mouseout(event) {
            var elem = document.getElementById('uved');
            elem.style.display = 'none';
        }
    </script>
        <br/>
    <div align="center"><input type='submit' class="archiv1" value='Востановить из архива' onclick='set_tovar_to_archive(<?=$id?>); window.location.reload(); '></div>
<?php } ?>

    <?php if ($by_weight_checked=="" && $name!="" && $parentx==0) { ?>
       <div align="center"> <div onclick="document.getElementById('xem7').style.display='block';" class="archiv" style="">Добавить емкость для товара</div></div>
    <?php } ?>
    <br>
    <div class="xem7" id="xem7" style="display: none; float: left; text-align: center; width: 100%;">
        <div align="center" style=" margin: 4px 0px;">
            <table>
                <tr style="<?php if (($_COOKIE['name']!="Admin") && ($_COOKIE['name']!="admin")) echo "display: none"; ?> ">
                    <td>
                        Себестоимость
                    </td><td>
                        <input type="text" id="sebemk" value="0" style="width: 75px">
                    </td>
                </tr> <tr>
                    <td>
                        Цена
                    </td><td>
                        <input type="text" id="priceemk" value="0" style="width: 75px">
                    </td>
                </tr>
                <tr>
                    <td>
                        Емкость
                    </td><td>
                        <select id="emchild" size="1" name="Etalon" style="width: 79px;" >
                            <option value="0" disabled>Выберите емкость</option>
                            <?php
                            $remk = mysql_query("SELECT * FROM ezh_emkost order by emkost");
                            while ($aemk = mysql_fetch_array($remk)){
                                echo  "<option ";
                           //       if ($usercity==$amanager['id']) echo 'selected ';
                                echo "value='".$aemk['id']."' >".$aemk['emkost']."</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr> <tr>
                    <td>
                        <div onclick="set_tovar_to_child(<?=$id?>);" class="archiv" style="width: 85px;">Сохранить</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</form>
    <?
}
function new_tovar_city($tovar_id){
  $c_id = rand();
?>
<div class='tovar_city' style='background-color:#DDDDDD;padding:0 0 5px 5px;margin-bottom:5px;border-top:1px solid;border-bottom:1px solid;'>
 <div style='float:left;'>
  <select class='tovar_city' id='city_<?=$tovar_id?>_<?=$c_id?>' name='city_<?=$tovar_id?>_<?=$c_id?>'>
<?
  $r = mysql_query("select * from pr_city order by name");
  while($a = mysql_fetch_array($r)){
?>
  <option value='<?=$a['id']?>'><?=$a['name']?></option>
<?
  }
?>
  </select>
 </div>
 <div style='float:right;margin-right:10px;'><input type='button' value='X' onclick='remove_tovar_city($(this),<?=$tovar_id?>,<?=$c_id?>);'></div>
 <div style='clear:both;'></div>
</div>
<?
}
function show_zakaz_tovar($id_city){
  $q = "select t.*,c.*,sum(o.number)cnt from pr_tovar t left join pr_tovar_city c on t.id=c.id_tovar left join pr_order_tovar o on o.id_tovar=t.id where t.active=1 and t.pokaz>0 and c.id_city=$id_city and parent=0 group by t.id order by cnt desc";
  $r = mysql_query($q);
?>
    <div class='zakaz_left_main'>
<?
  while($a = mysql_fetch_array($r)){
    $id_tovar = $a['id'];
    $by_weight = $a['by_weight'];
    if($by_weight==1)$unit = 0.1;else $unit = 1;
?>
      <style>
          .but{
              margin: 1px 0px;
              width: 35px;
              padding: 0px;
              font-size: 11px;
          }
      </style>
        <div style=" align="center">
                  <div class='pic_container' style='width: 180px; height: 145px;' >
                      <img class='pic' src='/pic<?=$id_tovar?>.jpg' style="transform: translate(0, 0) !important; top: 0 !important; left: 0 !important; float: left !important;" onclick='add_zakaz(<?=$id_tovar?>,<?=$unit?>, 0,0);'>
          <?php $rmod = mysql_query("SELECT *, t.id as tid FROM `pr_tovar` t, ezh_emkost e, pr_tovar_city c WHERE t.`idemk`=e.id and t.id=c.id_tovar and c.id_city=$id_city and t.parent=$id_tovar order by e.emkost");
          while($amod = mysql_fetch_array($rmod)){
              ?>
              <button class="prodchild" style="float: right"  onclick="add_zakaz(<?=$amod['tid']; ?>,1, 0,0);" ><?=$amod['emkost']; ?></button>
          <?php } ?>
          <div style="padding-top: 125px; font-size: 11px;" onclick='add_zakaz(<?=$id_tovar?>,<?=$unit?>, 0,0);'>
              <span style="margin-left: 10px"><?=$a['name']; ?></span>
          </div>
                  </div>
          </div>
<?
  }
?>

    </div>
<?
}
function show_zakaz($id,$id_zakaz=-1,$unit=1,$from_rest=0, $emk=0, $emkstring=''){
  $r = mysql_query("select * from pr_tovar where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $name = $a['name'];
  $by_weight = $a['by_weight'];

  $r1 = mysql_query("select * from pr_order_tovar where id_tovar=$id and id_order=$id_zakaz and from_rest=$from_rest");
  if (mysql_num_rows($r1)==0){
    $num = $unit; 
  }else {
    $a1 = mysql_fetch_array($r1);
    $num = $a1['number'];
  }
  $r1 = mysql_query("select * from pr_order o,pr_city c where o.id_city=c.id and o.id=$id_zakaz");
  if(mysql_num_rows($r1)>0){
    $a1 = mysql_fetch_array($r1);
    $done = $a1['done'];
    $allow_bills = $a1['allow_bills'];
  }else{
    $done = 0;
    $allow_bills = 0;
  }
  if($from_rest==0){
?>
<div id='zakaz<?=$id?>' class='zakaz'>
 <div style='position:relative;height:120px;'>
  <div class='zakaz_pic_container' style='width:100px;height:100px;margin-top:5px;overflow:hidden;'><img style='width:100%;' class='zakaz_pic' src='/pic<?=$id?>.jpg'></div>
  <div style='position:absolute;left:110px;top:0;right:3px;bottom:0;'>
   <div style='height:33%;width:100%;' class='zakaz_f1'><input type='button' value='+' style='width:100%;' onclick='<?if($by_weight==1){?>plusone(<?=$id?>,0.1,<?=$done?>)<?}else{?>plusone(<?=$id?>,1,<?=$done?>)<?}?>'></div>
   <div style='height:33%;width:100%;' class='zakaz_f2'><div style='padding:0 4px 0 1px;'><input type='text' id='num<?=$id?>' style='width:100%;padding:0;text-align:center;' <?if($by_weight==1){?>oninput='normalize_input(this.id,false,true,<?=$done?>);gen_zakaz_text();'<?}else{?>oninput='normalize_input(this.id,false,false,<?=$done?>);gen_zakaz_text();'<?}?> value='<?=$num?>'></div></div>
   <div style='height:33%;width:100%;' class='zakaz_f3'><input type='button' value='-' style='width:100%;' onclick='<?if($by_weight==1){?>minusone(<?=$id?>,0.1,<?=$done?>)<?}else{?>minusone(<?=$id?>,1,<?=$done?>)<?}?>'></div>
   <input type='hidden' id='num_copy<?=$id?>' value='<?=$num?>'>
  </div>
  <div style='clear:both;'></div>
 </div>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'><?=$name?></div>
<?
    if($done==1){
?>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'>
<?
      if($allow_bills==1){
?>
  <input type='button' id='ostatok_button<?=$id?>' value='Остаток (<?=$num?>)' onclick='if($("#ostatok<?=$id?>").val()==0){$(this).addClass("yellow");$("#ostatok<?=$id?>").val(1);$("#brak_button<?=$id?>").removeClass("red");$("#brak<?=$id?>").val(0);}else{$(this).removeClass("yellow");$("#ostatok<?=$id?>").val(0);}'>
  <input type='hidden' id='ostatok<?=$id?>' class='ostatok' value=0>
<?
      }
?>
  <input type='button' id='brak_button<?=$id?>' value='Брак (<?=$num?>)' onclick='if($("#brak<?=$id?>").val()==0){$(this).addClass("red");$("#brak<?=$id?>").val(1);$("#ostatok_button<?=$id?>").removeClass("yellow");$("#ostatok<?=$id?>").val(0);}else{$(this).removeClass("red");$("#brak<?=$id?>").val(0);}'>
  <input type='hidden' id='brak<?=$id?>' class='brak' value=0>
 </div>
<?
    }
?>
</div>
<?
  }else{
?>
<div id='zakaz_rest<?=$id?>' class='zakaz_rest' style='background-color:#FFBEA6;'>
 <div style='position:relative;height:120px;'>
  <div class='zakaz_pic_container' style='width:100px;height:100px;margin-top:5px;overflow:hidden;'><img style='width:100%;' class='zakaz_pic' src='/pic<?=$id?>.jpg'></div>
  <div style='position:absolute;left:110px;top:0;right:3px;bottom:0;'>
   <div style='height:33%;width:100%;' class='zakaz_f1'><input type='button' value='+' style='width:100%;' onclick='<?if($by_weight==1){?>plusone_rest(<?=$id?>,0.1,<?=$done?>)<?}else{?>plusone_rest(<?=$id?>,1,<?=$done?>)<?}?>'></div>
   <div style='height:33%;width:100%;' class='zakaz_f2'><div style='padding:0 4px 0 1px;'><input type='text' id='num_rest<?=$id?>' style='width:100%;padding:0;text-align:center;' <?if($by_weight==1){?>oninput='normalize_input_rest(this.id,false,true,<?=$done?>);gen_zakaz_text();'<?}else{?>oninput='normalize_input(this.id,false,false,<?=$done?>);gen_zakaz_text();'<?}?> value='<?=$num?>'></div></div>
   <div style='height:33%;width:100%;' class='zakaz_f3'><input type='button' value='-' style='width:100%;' onclick='<?if($by_weight==1){?>minusone_rest(<?=$id?>,0.1,<?=$done?>)<?}else{?>minusone_rest(<?=$id?>,1,<?=$done?>)<?}?>'></div>
   <input type='hidden' id='num_rest_copy<?=$id?>' value='<?=$num?>'>
  </div>
  <div style='clear:both;'></div>
 </div>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'><?=$name?></div>
<?
    if($done==1){
?>
 <div style='text-align:left; padding-left:5px;padding-bottom:5px;font-size:12px;'>
<?
      if($allow_bills==1){
?>
  <input type='button' id='ostatok_button<?=$id?>' value='Остаток (<?=$num?>)' onclick='if($("#ostatok<?=$id?>").val()==0){$(this).addClass("yellow");$("#ostatok<?=$id?>").val(1);$("#brak_button<?=$id?>").removeClass("red");$("#brak<?=$id?>").val(0);}else{$(this).removeClass("yellow");$("#ostatok<?=$id?>").val(0);}'>
  <input type='hidden' id='ostatok<?=$id?>' class='ostatok' value=0>
<?
      }
?>
  <input type='button' id='brak_button<?=$id?>' value='Брак (<?=$num?>)' onclick='if($("#brak<?=$id?>").val()==0){$(this).addClass("red");$("#brak<?=$id?>").val(1);$("#ostatok_button<?=$id?>").removeClass("yellow");$("#ostatok<?=$id?>").val(0);}else{$(this).removeClass("red");$("#brak<?=$id?>").val(0);}'>
  <input type='hidden' id='brak<?=$id?>' class='brak' value=0>
 </div>
<?
    }
?>
</div>
<?
  }
}
function zakaz_client($id_zakaz=-1){
  $r = mysql_query("select * from pr_order where id=$id_zakaz");
  if (mysql_num_rows($r)==0){
    $cur_id_client = -1;
    $r0 = mysql_query("select * from pr_city order by name limit 1");
    if (mysql_num_rows($r0)>0){
      $a0 = mysql_fetch_array($r0);
      $cur_city = $a0['id'];
    }else $cur_city = -1;
    $cur_phone = '';
    $cur_client_name = '';
    $cur_address = '';
    $cur_comment = '';
    $cur_dt_now = 1;
    $cur_dt = '';
    $cur_predoplata = 0;
    $cur_skidka = 0;
    $cur_razdel = 2;
  }else{
    $a = mysql_fetch_array($r);
    $cur_id_client = $a['id_client'];
    $r_ph = mysql_query("select phone from pr_client where id=$cur_id_client");
    if(mysql_num_rows($r_ph)==0)$cur_phone = '';
    else{
      $a_ph = mysql_fetch_array($r_ph);
      $cur_phone = $a_ph['phone'];
    }
    $cur_city = $a['id_city'];
    $r_d = mysql_query("select name,dostavka,sum_less,price_less,sum_more,price_more from pr_city where id=$cur_city");
    if(mysql_num_rows($r_d)==0){
      $cur_dostavka = '';
      $cur_city_name = '';
    }else{
      $a_d = mysql_fetch_array($r_d);
      $dostavka = $a_d['dostavka'];
      $sum_less = $a_d['sum_less'];
      $price_less = $a_d['price_less'];
      $sum_more = $a_d['sum_more'];
      $price_more = $a_d['price_more'];
      $cur_dostavka = "Доставка: $dostavka тг";
      $cur_city_name = $a_d['name'];
    }
    $cur_client_name = htmlspecialchars($a['client_name']);
    $cur_other_city_name = $a['other_city_name'];
    $cur_address = $a['address'];
    $cur_comment = $a['comment'];
    $cur_dt_now = intval($a['dt_now']);
    $cur_dt = $a['dt'];
    $m = array();
    preg_match('/(\d{4})-(\d{2})-(\d{2})/',$cur_dt,$m);
    $cur_dt = $m[3].'.'.$m[2].'.'.$m[1];
    if ($cur_dt=='00.00.0000')$cur_dt = '';
    $cur_predoplata = intval($a['predoplata']);
    $cur_skidka = intval($a['skidka']);
    if($a['done']==0)$cur_razdel=2;else $cur_razdel=3;
  }
  $POST = array();
  $POST['zakaz_city'] = $cur_city;
  $POST['phone'] = $cur_phone;
  $POST['client_name'] = $cur_client_name;
  $POST['address'] = $cur_address;
  $POST['comment'] = $cur_comment;
  $POST['dt_now'] = $cur_dt_now;
  $POST['dt'] = $cur_dt;
  $POST['dt_predoplata'] = $cur_predoplata;
  $POST['dt_skidka'] = $cur_skidka;
  $r1 = mysql_query("select * from pr_order_tovar where id_order=$id_zakaz");
  while($a1 = mysql_fetch_array($r1)){
    if($a1['from_rest']==0)$POST['zakaz'.$a1['id_tovar']] = $a1['number'];
    else $POST['zakaz_rest'.$a1['id_tovar']] = $a1['number'];
  }
  $m = array();
  preg_match('/(\d{3})(\d{3})(\d{2})(\d{2})/',$cur_phone,$m);
  $cur_phone1 = "(".$m[1].")".$m[2]."-".$m[3]."-".$m[4];
?>
<div style='width:100%;'><input type='button' value='Создать новый заказ' onclick='clear_order();' style='padding:15px;width:100%;'></div>
<div style='width:100%;padding-top:10px;'>
 <div style='width:50%;padding:0 10px 0 0;float:left;'>
  <select id='zakaz_city' style='width:100%;' onchange='return change_zakaz_city();'>
<?
  $r = mysql_query("select * from pr_city order by name");
  while($a = mysql_fetch_array($r)){
    $c_id = $a['id'];
    $c_name = $a['name'];
?>
   <option value='<?=$c_id?>'<?if($cur_city==$c_id){?> selected<?}?>><?=$c_name?></option>
<?
  }
?>
  </select>
  <input type='hidden' id='old_city' value=''>
 </div>
 <div style='width:40%;float:left; font-size:12px;' id='dostavka'><?=dostavka($cur_city)?></div>
 <div style='clear:both;'></div>
</div>
<div id='other_city_div' style='width:100%;padding-top:10px;<?if($cur_city_name!="Другие города"){?>display:none;<?}?>'>
 <div style='padding:0 10px 0 0;'>
  <input type='text' id='other_city_name' placeholder='Другой город' style='width:100%;' value='<?=htmlspecialchars($cur_other_city_name)?>' oninput='gen_zakaz_text();'>
 </div>
</div>
<div style='width:100%;padding-top:0;'>
<!--<span style='color:#717171;'>+7&nbsp;</span>-->
<input type='text' id='phone' placeholder='Телефон' style='width:65%;' oninput='gen_zakaz_text();' onblur='get_client();' value='<?=$cur_phone1?>'><input style='margin-left:3%;transform: translate(0,5px);' type='image' src='/diskette.png' onclick='save_client();return false;' style='transform:translate(0,5px);'>
</div>
<div style='width:100%;padding-top:10px;'>
 <div style='width:50%;padding:0 10px 0 0;float:left;'>
  <input type='text' id='client_name' placeholder='Покупатель' style='width:100%;' oninput='gen_zakaz_text();' value='<?=$cur_client_name?>'>
 </div>
    <br>
    <br>
 <div style='width:50%;float:left; font-size:12px;' id='num_order'>Заказов: 0</div>
 <div style='width:50%;float:left; font-size:12px;'><input id='sendprice' type="checkbox">есть прайс</div>
    <div style='width:50%;float:left; font-size:12px;'><input id='warmclient' type="checkbox"><label for="warmclient">Теплый клиент</label></div>
    <div style='width:50%;float:left; font-size:12px;'><input id='promokod' type="checkbox"><label for="promokod">Восп. промокодом</label></div>
    <div style='clear:both;'></div>
</div>
<div style='width:100%;padding-top:10px;'>
 <textarea id='address' style='width:100%;height:40px;resize:vertical;' placeholder='Адрес' oninput='gen_zakaz_text();'><?=$cur_address?></textarea>
</div>
<div style='width:100%;padding-top:10px;'>
 <textarea id='comment' style='width:100%;height:40px;resize:vertical;' placeholder='Примечание к заказу' oninput='gen_zakaz_text();'><?=$cur_comment?></textarea>
</div>
<div style='width:100%;padding-top:10px;'>
 <div style='width:100%;padding:0 10px 0 0;float:left;'>
  <input style='float:left;' type='checkbox' id='dt_now' value=1<?if($cur_dt_now==1){?> checked<?}?> onchange='if(this.checked){$("#dt_div").hide();}else{$("#dt_div").show();}'><label for='dt_now' style='font-size:12px;'>Ближайшая доставка</label>
 </div>
    <div style='width:40%;padding:0 10px;float:left;<?if($cur_dt_now==1){?>display:none;<?}?>' id='dt_div'><input type='text' id='dt' value='<?=$cur_dt?>'></div>
 <div style='clear:both;'></div>
</div>
<div style='width:100%;padding-top:10px;'>
 <div id='full_description' style='height:300px;background-color:white;cursor:text;border:1px solid;padding:5px;overflow-y:auto; font-size:12px;resize:vertical;'><?=gen_zakaz_text($POST)?></div>
</div>
<div style='width:100%;padding-top:10px;'>
 <div style='width:50%;float:left; margin-top:4px;'>
  <input style='float:left; margin:0;' type='checkbox' id='predoplata' value=1<?if($cur_predoplata==1){?> checked<?}?> onchange='gen_zakaz_text();'><label style='font-size:12px;' for='predoplata'>Предоплата</label>
 </div>

    <div style='width: 75px;float:right; margin-top:4px;'>
        <input style='float:left; margin:0;' type='checkbox' id='VK' value=0'><label style='font-size:12px;' for='VK'>Заказ с ВК</label>
    </div>


    <div style='width:50%;float:right;text-align:right; font-size:12px; margin-top: 10px;'>Скидка: <input type='text' id='skidka' value='<?=$cur_skidka?>' style='width:40%;' oninput='normalize_input(this.id,true,false,0);gen_zakaz_text();'></div>
 <div style='clear:both;'></div>
</div>

<?
  if (!isset($_REQUEST['id'])){
?>
<div style='width:100%;padding-top:10px;' id='buttons'>
 <button style='padding:4px; float:left;'  onclick='copyToClipboard($("#full_description").get(0))'>Скопировать<br/> текст</button>
 &nbsp;
 <button style='padding:4px; float:right;' onclick='save_order(true,<?=$cur_razdel?>);'>Отправить<br/> в заказы<br/></button>
</div>
<?
  }else{
?>
<div style='width:100%;padding-top:10px;' id='buttons'>
 <input type='button' value='Скопировать текст' style='padding:2.5%;' onclick='copyToClipboard($("#full_description").get(0))'>
 &nbsp;
 <input type='button' value='Сохранить заказ' style='padding:2.5%;' onclick='save_order(false,<?=$cur_razdel?>);'>
 &nbsp;
 <input type='button' value='Отменить редактирование' style='padding:2.5%;' onclick='cancel_order(<?=$cur_razdel?>);'>
 &nbsp;
 <input type='button' value='Удалить заказ' style='padding:2.5%;' onclick='if(confirm("Действительно удалить?"))del_order();'>
</div>
<?
  }
?>


<input type='hidden' id='id_client' value='<?=$cur_id_client?>'>
<input type='hidden' id='id_order' value='<?=$id_zakaz?>'>
<?
}
function dostavka($id){
  $r = mysql_query("select * from pr_city where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $dostavka = $a['dostavka'];
  $sum_less = $a['sum_less'];
  $price_less = $a['price_less'];
  $sum_more = $a['sum_more'];
  $price_more = $a['price_more'];
?>
Доставка: <?=$dostavka?> тг.
<?
}
function gen_zakaz_text($POST){
  if (isset($POST['client_name']) && $POST['client_name']!='')print $POST['client_name']."<br/>\n";
  if (isset($POST['phone']) && $POST['phone']!='')print '+7'.$POST['phone']."<br/>\n";
  if (isset($POST['zakaz_city']) && $POST['zakaz_city']!=-1){
    $zakaz_city = $POST['zakaz_city'];
    $r = mysql_query("select name,dostavka,sum_less,price_less,sum_more,price_more from pr_city where id=$zakaz_city");
    if (mysql_num_rows($r)>0){
      $a = mysql_fetch_array($r);
      $zakaz_city_name = $a['name'];
      $dostavka = $a['dostavka'];
      $sum_less = $a['sum_less'];
      $price_less = $a['price_less'];
      $sum_more = $a['sum_more'];
      $price_more = $a['price_more'];
      if ($zakaz_city_name!='Другие города')print $zakaz_city_name."<br/>\n";
      else print $zakaz_city_name.": ".$POST['other_city_name']."<br/>\n";
    }
  }
  $skidka = intval($POST['skidka']);
  print $POST['address']."<br/>\n";
  print "-----------------<br/>\n";
  $full_price = 0;
//print_r($POST);
  foreach($POST as $k=>$v){
    $m = array();
    if (preg_match('/zakaz(\d+)/',$k,$m)){
      $z_id = $m[1];
      if(isset($POST["zakaz_rest$z_id"])){
        $v += $POST["zakaz_rest$z_id"];
      }
      $r = mysql_query("select name,price from pr_tovar where id=$z_id");
      if (mysql_num_rows($r)>0){
        $a = mysql_fetch_array($r);
        $z_name = $a['name'];
        $z_price = $a['price'];
        $cur_price = $z_price*$v;
        $full_price += $cur_price;
        $pieces = explode("(", $z_name);
        if($pieces[1]!="") $pieces[1]="(".$pieces[1];
  $s="$v ".$pieces[0]." <b>".$pieces[1]."</b> - ${z_price}x$v=$cur_price<br/>\n";
        print $s;
      }
    }
    if (preg_match('/zakaz_rest(\d+)/',$k,$m)){
      $z_id = $m[1];
      if (isset($POST["zakaz$z_id"]))continue;
      $r = mysql_query("select name,price from pr_tovar where id=$z_id");
      if (mysql_num_rows($r)>0){
        $a = mysql_fetch_array($r);
        $z_name = $a['name'];
        $z_price = $a['price'];
        $cur_price = $z_price*$v;
        $full_price += $cur_price;
        print "$v $z_name - ${z_price}x$v=$cur_price<br/>\n";
      }
    }
  }
  $sum1 = $full_price-$skidka;
  if($sum_less!='' && $sum1<=$sum_less)$dostavka = $price_less;
  if($sum_more!='' && $sum1>=$sum_more)$dostavka = $price_more;
  if ($dostavka>0)print "Доставка: $dostavka<br/>\n";
  if ($skidka>0)print "Скидка: $skidka<br/>\n";
  $sum = $full_price+$dostavka-$skidka;
  if ($sum>0)print "ИТОГО: $sum<br/>\n";
  if ($POST['predoplata']==1){
    print "<b>Оплачено 100%</b><br/>\n";
  }
  print "-----------------<br/>\n";
  print $POST['comment']."<br/>\n"; 
  print "ПРОВЕРЬТЕ АДРЕС и ТЕЛЕФОН <br>Все ОК, подтверждаете заказ?";
}
function show_zakaz_middle($id_zakaz=-1){
  $r = mysql_query("select * from pr_order_tovar where id_order=$id_zakaz");
  while($a = mysql_fetch_array($r)){
    $id = $a['id_tovar'];
    show_zakaz($id,$id_zakaz,1,$a['from_rest']);
  }
}
function city_orders($id){
  $r = mysql_query("select * from pr_city where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $city = $a['name'];
  $dt_otgruzka = $a['dt_otgruzka'];
  $dostavka = $a['dostavka'];
  $fact_dostavka = $a['fact_dostavka'];
  $sum_less = $a['sum_less'];
  $price_less = $a['price_less'];
  $sum_more = $a['sum_more'];
  $price_more = $a['price_more'];
  $m = array();
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_otgruzka,$m);
  $dt = $m[3].'.'.$m[2].'.'.$m[1];
  if ($dt == '00.00.0000')$dt = '';
//  $r = mysql_query("select *,dt>curdate() late_flag from pr_order where done=0 and id_city=$id");
  $r = mysql_query("select *,dt>'$dt_otgruzka' late_flag,unix_timestamp(dt)dtu from pr_order where done=0 and id_city=$id order by id desc");
  $r_top = mysql_query("select * from pr_order where done=0 and id_city=$id and dt<='$dt_otgruzka' order by id desc");
  $sum = 0;
  $sum_nodostavka = 0;
  $tovar_fact_dostavka = 0;
  $number = 0;
  $tovar = 0;
  if (mysql_num_rows($r)==0)return '';
  while($a = mysql_fetch_array($r_top)){
    $number++;
    $id_order = $a['id'];
    $skidka = $a['skidka'];
    $r1 = mysql_query("select ot.number,t.price from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order");
    $sum_order = 0;
    while($a1 = mysql_fetch_array($r1)){
      $tovar+=$a1['number'];
      $sum_nodostavka+=$a1['number']*$a1['price'];
      $sum_order+=$a1['number']*$a1['price'];
    }
    $sum_nodostavka -= $skidka;
    $dostavka1 = $dostavka;
    if($sum_less!='' && $sum_order-$skidka<=$sum_less)$dostavka1 = $price_less;
    if($sum_more!='' && $sum_order-$skidka>=$sum_more)$dostavka1 = $price_more;
    $sum += $sum_order-$skidka+$dostavka1; 
    $tovar_fact_dostavka += $fact_dostavka;
  }
  $sum_fact_nodostavka = $sum - $tovar_fact_dostavka;
?>
<div class='city_orders' id='city<?=$id?>' style="height:100%;">
 <div style="height:155px;">
 <div>
  <div style='margin-left:10px;margin-top:10px;float:left;'><b><?=$city?></b></div>
  <div style='margin-right:10px;margin-top:10px;float:right;'><input type='text' id='dt_otgruzka<?=$id?>' class='dt_otgruzka' style='width:140px;' value='<?=$dt?>'></div> <!-- <?=$dt?> -->
  <div style='clear:both;'></div>
 </div>
 <div>
  <div style='margin-left:10px;margin-top:10px;float:left;'><input style='padding:5px 15px;' type='button' value='Отгрузка' onclick='window.open("/otgruzka.php?city=<?=$id?>","otgruzka_win","height=1200,width=1020,menubar=no,toolbar=no,location=no,scrollbars=yes")'></div>
  <div style='margin-right:10px;margin-top:10px;float:right;'><input style='padding:5px 15px;' type='button' value='Склад' onclick='window.open("/sklad.php?city=<?=$id?>","sklad_win","height=1200,width=500,menubar=no,toolbar=no,location=no,scrollbars=yes")'></div>
  <div style='clear:both;'></div>
 </div>
 <div style='margin-bottom:10px;'>
  <div style='display:flex;'>
   <div style='margin-left:10px;margin-top:10px;width:25%; font-size:12px;'>Общая сумма</div>
   <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$sum?></b></div>
   <div style='margin-left:10px;margin-top:10px;width:25%; font-size:12px;'>Кол-во заказов</div>
   <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$number?></b></div>
  </div>
  <div style='display:flex;'>
   <div style='margin-left:10px;margin-top:10px;width:25%; font-size:12px;'>Сумма без доставки</div>
   <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$sum_fact_nodostavka?></b></div>
   <div style='margin-left:10px;margin-top:10px;width:25%; font-size:12px;'>Единиц товара</div>
   <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$tovar?></b></div>
  </div>
 </div>
</div>
<script>
window.onload = window.onresize = function () {
    var height = window.innerHeight - 240;
    $('.unscroll').css('height',height+'px');
}
</script>
 <div class="unscroll"  style='overflow-y:scroll;'>
<?
  if (mysql_num_rows($r)>0){
    mysql_data_seek($r,0);
    $city0 = $city;
    while($a = mysql_fetch_array($r)){
//print "<!--".print_r($a,1)."-->";
      $id_order = $a['id'];
      $id_client = $a['id_client'];
      $client_name = $a['client_name'];
      $statusX=$a['status'];
      $other_city_name = $a['other_city_name'];
      if ($city0=='Другие города')$city = "Другие города: $other_city_name";
      $address = $a['address'];
      $comment = $a['comment'];
      $dt_now = $a['dt_now'];
      $dt = $a['dt'];
      $dtu = date('d.m.Y',$a['dtu']);
      $predoplata = $a['predoplata'];
      $skidka = $a['skidka'];
      $late_flag = $a['late_flag'];
      $r1 = mysql_query("select * from pr_client where id=$id_client");
      if (mysql_num_rows($r1)>0){
        $a1 = mysql_fetch_array($r1);
        $phone = $a1['phone'];
      }else $phone = '';
?>
  <div class='city_order_description'>
   <div>
    <div>
     <input style='transform: scale(1.5);' type='checkbox' class='order_selected' id='sel<?=$id_order?>'>
    </div>
    <div style='position:relative;height:100%;'>
     <div class='zakaz_pic'>
      <a href='/program.php?razdel=1&id=<?=$id_order?>'><img src='/edit.png'></a>
     </div>
    </div>
   </div>
      <?php
      $sty="";
      $dtcity = strtotime($dt);
      $dateobr=strtotime(Date("d.m.Y"))-86400*7;
      if ($city0=="Другие города" && $dateobr>$dtcity) { $sty="background-color: rgb(252,249,135);"; }
      ?>
   <div style='font-size:12px;margin-left:10px;padding:5px;<?=$sty ?><?if($late_flag==1){?>background-color:#FFBEA6;<?}?>;width:100%;'>
<?
  if ($client_name!='')print $client_name."<br/>\n";
  if ($phone!='')print '+7'.$phone."<br/>\n";
  print $city."<br/>\n";
  if ($address!='')print $address."<br/>\n";
  print "-----------------<br/>\n";
  
  $full_price = 0;
  $r1 = mysql_query("select sum(ot.number)number,t.price,t.name from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order group by ot.id_tovar");
  $sum_order = 0;
  while($a1 = mysql_fetch_array($r1)){
    $z_name = $a1['name'];
    $z_price = $a1['price'];
    $z_number = $a1['number'];
    $cur_price = $z_price*$z_number;
    $full_price += $cur_price;
     $pieces = explode("(", $z_name);
      if($pieces[1]!="") $pieces[1]="(".$pieces[1];
      $s="$z_number ".$pieces[0]." <b>".$pieces[1]."</b> - ${z_price}x$z_number=$cur_price<br/>\n";
      print $s;
  }
  $sum_order = $full_price-$skidka;
  $dostavka1 = $dostavka;
  if($sum_less!='' && $sum_order-$skidka<=$sum_less)$dostavka1 = $price_less;
  if($sum_more!='' && $sum_order-$skidka>=$sum_more)$dostavka1 = $price_more;
  $sum = $full_price+$dostavka1-$skidka;
  if ($dostavka1>0)print "Доставка: $dostavka1<br/>\n";
  if ($skidka>0)print "Скидка: $skidka<br/>\n";
  if ($sum>0)print "ИТОГО: $sum<br/>\n";
  if ($predoplata==1){
    print "Оплачено 100%<br/>\n";
  }
  print "-----------------<br/>\n";
  $styleRB1="";
  $styleRB2="";
  $styleRB3="";
  $chek1="";
  $chek2="";
  $chek3="";
  switch ($statusX)
  {
      case 1: $styleRB1="box-shadow: 0px 0px 5px 1px green; color: green;"; $chek1="checked"; break;
      case 2: $styleRB2="box-shadow: 0px 0px 5px 1px orange; color: orange;"; $chek2="checked"; break;
      case 3: $styleRB3="box-shadow: 0px 0px 5px 1px orangered; color: orangered;"; $chek3="checked"; break;
  }
  echo "<div id='lob$id_order'><form>";
  echo "<label style='$styleRB1 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px; margin-right: 7px;'><input type=\"radio\" id=\"contactChoice1\" name=\"contact\" value=\"email\" onclick='upstatus($id_order, 1, \"lob$id_order\", 1)' $chek1>Дозаказ</label>";
  echo "<label style='$styleRB2 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px; margin-right: 7px;'><input type=\"radio\" id=\"contactChoice2\" name=\"contact\" value=\"email\" onclick='upstatus($id_order, 2, \"lob$id_order\", 2)' $chek2>Недозвон</label>";
  echo "<label style='$styleRB3 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px;'><input type=\"radio\" id=\"contactChoice3\" name=\"contact\" value=\"email\" onclick='upstatus($id_order, 3, \"lob$id_order\", 3)' $chek3>Отказ</label>";
  echo "</form></div>";
  print $comment."<br/>\n"; 
  if ($late_flag==1)print "Доставка с $dtu<br/>";
?>
   </div>
  </div>
<?
    }
  }
?>
 </div>
    <script>
        function upstatus(id, stat, retx, style) {
            $.ajax({
                type: 'POST',
                url: 'programmainajax.php',
                data: {
                    'operation': "upstatus",
                    'idproc': id,
                    'stat': stat,
                    'style': style,
                },
                timeout: 20000,
                success: function (html) {
                    document.getElementById(retx).innerHTML=html;
                },
                error: function (html) {
                    alert('Ошибка подключения!');
                },
            });
        }
    </script>
 <div style='padding:5px; height:20px;'>
  <div style='float:left;'>
   <input type='button' value='+' onclick='$("#city"+<?=$id?>).find(".order_selected").attr("checked",true);'>
  </div>
  <div style='float:left;'>
   <input type='button' value='-' onclick='$("#city"+<?=$id?>).find(".order_selected").attr("checked",false);'>
  </div>
  <div style='float:right;'>
   <input type='button' value='Выполнено' onclick='city_orders_done(<?=$id?>)'>
  </div>
  <div style='clear:both;'></div>
 </div>
</div>
<?
}
function city_orders_done($id,$allow_bills,$limit=10){
  $r = mysql_query("select * from pr_city where id=$id");
  if (mysql_num_rows($r)==0)return;
  $a = mysql_fetch_array($r);
  $city = $a['name'];
  $dostavka = $a['dostavka'];
  $fact_dostavka = $a['fact_dostavka'];
  $sum_less = $a['sum_less'];
  $price_less = $a['price_less'];
  $sum_more = $a['sum_more'];
  $price_more = $a['price_more'];
  $r0 = mysql_query("select distinct dt from pr_order where done=1 and id_city=$id order by dt desc limit $limit");
  if(mysql_num_rows($r0)==0)return '';
?>
<div class='city_orders' id='city<?=$id?>'>
 <div>
  <div style='margin-left:10px;margin-top:10px;margin-bottom:10px;float:left;'><b><?=$city?></b></div>
  <div style='clear:both;'></div>
 </div>
<?
  while($a0 = mysql_fetch_array($r0)){
    $dt = $a0['dt'];
    $m = array();
    preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt,$m);
    $dt1 = $m[3].".".$m[2].".".$m[1];
    $r = mysql_query("select * from pr_order where done=1 and id_city=$id and dt='$dt'");
    $sum = 0;
    $sum_nodostavka = 0;
    $tovar_fact_dostavka = 0;
    $number = 0;
    $tovar = 0;
    while($a = mysql_fetch_array($r)){
      $number++;
      $id_order = $a['id'];
      $skidka = $a['skidka'];
      $order_checked = $a['checked'];
      $r1 = mysql_query("select ot.number,t.price from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order order by t.id desc");
      $sum_order = 0;
      while($a1 = mysql_fetch_array($r1)){
        $tovar+=$a1['number'];
        $sum_nodostavka+=$a1['number']*$a1['price'];
        $sum_order+=$a1['number']*$a1['price'];
      }
      $sum_nodostavka -= $skidka;
      $dostavka1 = $dostavka;
      if($sum_less!='' && $sum_order-$skidka<=$sum_less)$dostavka1 = $price_less;
      if($sum_more!='' && $sum_order-$skidka>=$sum_more)$dostavka1 = $price_more;
      //if ($a['predoplata']==1) {$sum=0; } else { $sum += $sum_order+$dostavka1-$skidka; }
      $sum += $sum_order+$dostavka1-$skidka;
      $tovar_fact_dostavka += $fact_dostavka;
    }
    $sum_fact_nodostavka = $sum - $tovar_fact_dostavka;
?>
  <div style='background-color:white;' id='city<?=$id?>-<?=$dt?>'>
<?
    $r_w = mysql_query("select * from pr_week where id_city=$id and dt='$dt'");
    $flag_w = 0;
    $html = '';
    $closed = 0;
    if (mysql_num_rows($r_w)>0){
      $a_w = mysql_fetch_array($r_w);
      $html = $a_w['html'];
      $closed = $a_w['closed'];
      if($closed=1)$flag_w = 1;
    }
    if ($flag_w==1){
      $html = preg_replace('/<a.*?\/a>/',"",$html);
      $html = preg_replace('/<div style="(.*?)" id="legend(.*?)">/',"<div style=\"display:none;$1\" id=\"legend$2\">",$html);
      $html = preg_replace('/<div style="(.*?) display: block;" id="legend(.*?)">/',"<div style=\"$1\" id=\"legend$2\">",$html);
      $html = preg_replace('/<button.*?>Загрузить чек<\/button>/u','',$html);
      $html = preg_replace('/<option value=.*?>Загрузить еще<\/option>/u','',$html);
      $html = preg_replace('/<option value=.*?>Удалить<\/option>/u','',$html);
      $html = preg_replace('/<input id="order_checked.*?" name="order_checked" checked.*?>/',"<input type='checkbox' id='order_checked' name='order_checked' checked disabled>",$html);
      $html = preg_replace('/<input id="order_checked.*?" name="order_checked" onchange.*?>/',"<input type='checkbox' id='order_checked' name='order_checked' disabled>",$html);
?>
<?=$html?>
<?
    }else{
      if($allow_bills==1){
?> 
  <div style='margin-left:10px;padding-top:10px;float:left;'><?=$dt1?></div>
  <div style='margin-right:40px;margin-top:10px;float:right;'>
   <input type="checkbox" id="order_checked<?=$id_order?>" name="order_checked"<?if($order_checked==1){?> checked<?}?> onchange="order_check(<?=$id_order?>,this.checked);">
   <label for='order_checked<?=$id_order?>'>Проверено</label>
  </div>
  <div style='clear:both;'></div>
<?
      }else{
?>
  <div style='margin-left:10px;padding-top:10px;'><?=$dt1?></div>
<?
      }
?>
  <div>
   <div style='margin-left:10px;margin-top:10px;float:left;'><input style="padding:5px;" value="Развернуть" onclick='if($("#legend<?=$id?>_<?=$dt?>").css("display")=="none"){$("#legend<?=$id?>_<?=$dt?>").show();$(this).val("Свернуть");}else{$("#legend<?=$id?>_<?=$dt?>").hide();$(this).val("Развернуть");};' type="button"></div>
<?
      if($allow_bills==1){
?> 
   <div style='margin-left:10px;margin-top:10px;float:left;' id='driver_bills<?=$id_order?>'><?=driver_bills($id_order)?></div>
<?
      }
      if($allow_bills==1){
?>
   <div style='margin-left:5px;margin-top:10px;margin-right:5px;float:right;'><input style="padding:5px;" value="Распечатать" onclick='window.open("/otgruzka.php?city=<?=$id?>&done=1&dt=<?=urlencode($dt)?>","otgruzka_win","height=1200,width=1020,menubar=no,toolbar=no,location=no,scrollbars=yes");' type="button"></div>
<?
      }else{
?>
   <div style='margin-left:15px;margin-top:10px;margin-right:40px;float:right;'><input style="padding:5px;" value="Распечатать" onclick='window.open("/otgruzka.php?city=<?=$id?>&done=1&dt=<?=urlencode($dt)?>","otgruzka_win","height=1200,width=1020,menubar=no,toolbar=no,location=no,scrollbars=yes");' type="button"></div>
<?
      }
?>
   <div style='clear:both;'></div>
  </div>
  <div style='margin-bottom:10px;padding-bottom:10px;'>
   <div style='display:flex;'>
    <div style='margin-left:10px;margin-top:10px;width:25%;font-size:12px;'>Общая сумма</div>
    <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$sum?></b></div>
    <div style='margin-left:10px;margin-top:10px;width:25%;font-size:12px;'>Кол-во заказов</div>
    <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$number?></b></div>
   </div>
   <div style='display:flex;'>
    <div style='margin-left:10px;margin-top:10px;width:25%;font-size:12px;'>Сумма без доставки</div>
    <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$sum_fact_nodostavka?></b></div>
    <div style='margin-left:10px;margin-top:10px;width:25%;font-size:12px;'>Единиц товара</div>
    <div style='margin-left:10px;margin-top:15px;width:25%;'><b><?=$tovar?></b></div>
   </div>
  </div>
  <div style='display:none;height:75vh;overflow-y:scroll;background-color:#DDDDDD;padding-top:20px;' id='legend<?=$id?>_<?=$dt?>'>
<?
      if (mysql_num_rows($r)>0){
        mysql_data_seek($r,0);
        while($a = mysql_fetch_array($r)){
//print_r($a);
          $id_order = $a['id'];
          $id_client = $a['id_client'];
          $other_city_name = $a['other_city_name'];
          if ($city=='Другие города')$city0 = "Другие города: $other_city_name";
          $client_name = $a['client_name'];
          $address = $a['address'];
          $comment = $a['comment'];
          $dt_now = $a['dt_now'];
          $dt = $a['dt'];
          $predoplata = $a['predoplata'];
          $skidka = $a['skidka'];
          $r1 = mysql_query("select * from pr_client where id=$id_client");
          if (mysql_num_rows($r1)>0){
            $a1 = mysql_fetch_array($r1);
            $phone = $a1['phone'];
          }else $phone = '';

          $rest_flag = 0;
          $r1 = mysql_query("select ot.rest from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order");
          while($a1 = mysql_fetch_array($r1)){
            $z_rest = $a1['rest'];
            if($z_rest>0){
              $rest_flag = 1;
              break;
            }
          }
?>
   <div class='city_order_description' style='background-color:white;'>
    <div>
     <div style='position:relative;height:100%;width:20px;'>
      <div class='zakaz_pic'>
<?if($rest_flag==0){?>
       <a href='/program.php?razdel=1&id=<?=$id_order?>'><img src='/edit.png'></a>
<?}?>
      </div>
     </div>
    </div>
    <div style='font-size:12px;margin-left:10px;padding:5px;'>
<?
          if ($client_name!='')print $client_name."<br/>\n";
          if ($phone!='')print '+7'.$phone."<br/>\n";
          print $city0."<br/>\n";
          if ($address!='')print $address."<br/>\n";
          print "-----------------<br/>\n";
          $full_price = 0;
          $full_price_brak = 0;
          $r1 = mysql_query("select sum(ot.number)number,ot.brak,ot.rest,t.price,t.name from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order group by ot.id_tovar");
          $sum_order = 0;
          while($a1 = mysql_fetch_array($r1)){
            $z_name = $a1['name'];
            $z_price = $a1['price'];
            $z_number = $a1['number'];
            $z_brak = $a1['brak'];
            $z_rest = $a1['rest'];
            $cur_price = $z_price*$z_number;
            $brak_price = $z_price*$z_brak;
            $rest_price = $z_price*$z_rest;
            $full_price += $cur_price;
            $full_price_brak += $cur_price+$brak_price;
            if($z_number>0)print "$z_number $z_name - ${z_price}x$z_number=$cur_price<br/>\n";
            if($z_brak>0)print "<nobr><s>$z_brak $z_name - ${z_price}x$z_brak=$brak_price</s> Брак</nobr><br/>\n";
            if($z_rest>0)print "<nobr><s>$z_rest $z_name - ${z_price}x$z_rest=$rest_price</s> Остаток</nobr><br/>\n";
          }
          $sum_order = $full_price-$skidka;
          $sum_order_brak = $full_price_brak-$skidka;
          $dostavka1 = $dostavka;
          if($sum_less!='' && $sum_order_brak-$skidka<=$sum_less)$dostavka1 = $price_less;
          if($sum_more!='' && $sum_order_brak-$skidka>=$sum_more)$dostavka1 = $price_more;
          $sum = $full_price+$dostavka1-$skidka;
          if ($dostavka>0)print "Доставка: $dostavka1<br/>\n";
          if ($skidka>0)print "Скидка: $skidka<br/>\n";
          if ($sum>0)print "ИТОГО: $sum<br/>\n";
          if ($predoplata==1){
            print "Оплачено 100%<br/>\n";
          }
          print "-----------------<br/>\n";
          print $comment."<br/>\n"; 
?>
    </div>
   </div>
<?
        }
      }
?>
  </div>
<?
    }
?>
 </div> 
 <div style="float: left;display: inline-block;">
   <input style='padding:5px 15px;' type='button' value='Склад' onclick='window.open("/sklad.php?city=<?=$id?>&dt=<?=$dt?>","sklad_win","height=1200,width=500,menubar=no,toolbar=no,location=no,scrollbars=yes")'>
 </div>
 <div style='margin-bottom:50px;text-align:center;'>
<?if($closed==0){?>
 <!-- <input type='button' value='Закрыть дату' class='orange' onclick='close_week(<?=$id?>,"<?=$dt?>")'> -->
<?}else{?>
  <input type='button' value='Дата закрыта' class='green'>
<?}?>
 </div>
<?
  }
?>
</div>
<?
}
function driver_bills($id_order){
  $r = mysql_query("select * from pr_order_bill where id_order=$id_order");
  if (mysql_num_rows($r)==0){
?>
<form id='fileform<?=$id_order?>'>
<button style='padding:5px;' onclick='document.getElementById("fileinput<?=$id_order?>").click();return false;'>Загрузить чек</button>
<input id="fileinput<?=$id_order?>" name="fileinput<?=$id_order?>" style="display:none;" type="file" onchange='sendfile(<?=$id_order?>);'>
</form>
<?
  }else{
?>
<form id='fileform<?=$id_order?>'>
<select onchange='driver_bill_change(<?=$id_order?>,this.value);'>
<option></option>
<option value='1'>Открыть чек</option>
<option value='2'>Загрузить еще</option>
<option value='3'>Удалить</option>
</select>
<input id="fileinput<?=$id_order?>" name="fileinput<?=$id_order?>" style="display:none;" type="file" onchange='sendfile(<?=$id_order?>);'>
</form>
<?
  }
}
function show_link_rest($id,$name){
  if($_REQUEST['razdel']!=1)return;
  $flag = false;
  $r = mysql_query("select sum(rest)rest from pr_tovar_city where id_city=$id");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    if ($a['rest']>0)$flag = true;
  }
  if ($flag){
?>
<a href='' style='color:#B51600' onclick='show_rest(<?=$id?>);return false;'>Остатки <?=$name?></a>
<?
  }else{
?>
<a href='' onclick='return false;'>Остатки <?=$name?></a>
<?
  }
}
?>
