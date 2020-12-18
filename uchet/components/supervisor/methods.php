<?php

function op_update_user_list(){
  $id = intval($_POST['id']);
  $r = mysql_query("select id from masters where id_master=$id");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $id_master = $a['id'];
  }else $id_master = -1;
  print f_show_user_lists($id_master);
  exit;
}

function op_show_masters()
{
  if ($_POST['razdel']==1 || $_POST['razdel']==5 || $_POST['razdel']==6 || $_POST['razdel']==8 || $_POST['razdel']==9){
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    print f_show_masters($dt);
  }else{
    $dt_from = $_POST['dt_from'];
    $dt_from = preg_replace('/<.*?>/','',$dt_from);
    $dt_from = str_replace('"','',$dt_from);
    $dt_from = str_replace("'",'',$dt_from);
    preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_from,$m);
    $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
    $dt_from = date('Y-m-d',strtotime(date('o-\\WW', $t)));
    $dt_to = $_POST['dt_to'];
    $dt_to = preg_replace('/<.*?>/','',$dt_to);
    $dt_to = str_replace('"','',$dt_to);
    $dt_to = str_replace("'",'',$dt_to);
    preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt_to,$m);
    $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
    $dt_to = date('Y-m-d',strtotime(date('o-\\WW', $t))+3600*24*6);
    print f_show_masters($dt_from,$dt_to);
  }
  exit;
}

function op_show_shops()
{
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $m = array();
  preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
  $dt = $m[3].'-'.$m[2].'-'.$m[1];
  print f_show_shops($dt);
  exit;
}

function op_pay_master()
{
  $id = intval($_POST['id']);
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $m = array();
  $q = "update masters m,master_week w set w.paid=1-w.paid where m.id=w.id_master and w.dt='$dt' and m.id_master=$id";
  mysql_query($q);
  print f_show_master_stat($id,$dt);
  exit;
}

function op_pay_shop()
{
  $id = intval($_POST['id']);
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $m = array();
  $q = "update ezh_city_week w,ezh_city c set w.paid=1-w.paid where w.dt='$dt' and w.id_city=c.id and c.id_shop=$id";
  mysql_query($q);
  print f_show_shop($id,$dt);
  exit;
}

function op_show_profit()
{
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $dt = str_replace(".",'-',$dt);
  $dt = strtotime($dt);
  print f_show_profit($dt);
  exit;
}

function op_sortup()
{
    $id = intval($_POST['idproc']);
    $proczn=$_POST['proczn'];
    mysql_query("update procedures set sort=$proczn where id=$id");
    exit;
}


function op_up_master()
{
  $id = intval($_POST['id']);
  $sort = mysql_result(mysql_query("select sort from masters where id=$id"),0,0);
  $r = mysql_query("select * from masters where sort<$sort order by sort desc");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $id1 = $a['id'];
    $sort1 = $a['sort'];
    mysql_query("update masters set sort=$sort1 where id=$id");
    mysql_query("update masters set sort=$sort where id=$id1");
  }
  mysql_query("set @i=0");
  mysql_query("update masters m,(SELECT @i:=@i+1 sort1,m.id FROM `masters` m order by sort)x set m.sort=x.sort1 where m.id=x.id");
  mysql_query("set @i:=null");
  print f_show_masters_settings();
  exit;
}

function op_savemastersort()
{
    $id = intval($_POST['id']);
    $s1 = intval($_POST['sor1m']);
    $s2 = intval($_POST['sor2m']);
    mysql_query("update masters set sort=$s1, sortsecond=$s2 where id=$id");
    print f_show_masters_settings();
    exit;
}

function op_down_master()
{
  $id = intval($_POST['id']);
  $sort = mysql_result(mysql_query("select sort from masters where id=$id"),0,0);
  $r = mysql_query("select * from masters where sort>$sort order by sort");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $id1 = $a['id'];
    $sort1 = $a['sort'];
    mysql_query("update masters set sort=$sort1 where id=$id");
    mysql_query("update masters set sort=$sort where id=$id1");
  }
  mysql_query("set @i=0");
  mysql_query("update masters m,(SELECT @i:=@i+1 sort1,m.id FROM `masters` m order by sort)x set m.sort=x.sort1 where m.id=x.id");
  mysql_query("set @i:=null");
  print f_show_masters_settings();
  exit;
}

function op_update_shop_list()
{
  $id = intval($_POST['id']);
  if ($id==0)$id=-1;
  print f_show_shop_lists($id);
  exit;
}

function op_save_shop()
{
  $id = intval($_POST['id']);
  $name = str_replace("'","\'",$_POST['name']);
  if ($id==0){
    if ($name!=''){
      mysql_query("insert into ezh_shop (name)values('$name')");
      $id = mysql_insert_id();
      print $id;
    }else{
      print 0;
    }
  }else{
    if ($name!=''){
      mysql_query("update ezh_shop set name='$name' where id=$id");
      print $id;
    }else{
      mysql_query("delete from ezh_shop where id=$id");
      print 0;
    }
  }
  if ($id!=0){
    $id_seller = intval($_POST['id_seller']);
    $id_marketolog = intval($_POST['id_marketolog']);
    if ($id_seller>0)mysql_query("update ezh_shop set id_seller=$id_seller where id=$id");
    if ($id_marketolog>0)mysql_query("update ezh_shop set id_marketolog=$id_marketolog where id=$id");
    if (isset($_POST['city'])){
      $city = $_POST['city'];
      print "|";
      foreach($city as $k=>$v){
        $c_id = intval($v['id']);
        $name = str_replace("'","\'",$v['name']);
        if($v['bonus']!='')$bonus = intval($v['bonus']);else $bonus = 'NULL';
        $div_id = str_replace("'","\'",$v['div_id']);
        if ($c_id>0){
          if ($name!=''){
            mysql_query("update ezh_city set name='$name',bonus=$bonus where id=$c_id");
          }else{
            mysql_query("delete from ezh_city where id=$c_id");
            print "$div_id/0|";
          }
        }else{
          if ($name!=''){
            mysql_query("insert into ezh_city (name,bonus,id_shop)values('$name',$bonus,$id)");
            $c_id = mysql_insert_id();
            print "$div_id/$c_id|";
          }else{
            print "$div_id/0|";
          }
        }
      }
    }
  }
  exit;
}

function op_bill_button()
{
  $id = intval($_POST['id']);
  $dt = str_replace("'","\'",$_POST['dt']);
  $val = intval($_POST['val']);
  print "update master_week w,masters m set w.bill_checked=$val where w.id_master=m.id and m.id_master=$id and dt='$dt'";
  mysql_query("update master_week w,masters m set w.bill_checked=$val where w.id_master=m.id and m.id_master=$id and dt='$dt'");
  exit;
}

function op_close_week()
{
  $id = intval($_POST['id']);
  $html = mysql_real_escape_string($_POST['html']);
  $dt = mysql_real_escape_string($_POST['dt']);

  $q = "select outcome,course,m.id from master_week w,masters m where w.id_master=m.id and m.id_master=$id and w.dt='$dt'";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $param1 = 0;
    $course = 1;
    $m_id = 0;
  }else{
    $a = mysql_fetch_array($r);
    $course = floatval($a['course']);
    if ($course==0)$course = 1;
    $outcome = intval($a['outcome']);
    $param1 = $outcome;
    $m_id = $a['id'];
  }

  $q = "select sum(w.visitors*p.comission) income,sum(w.visitors*p.price) price from master_procedure_week w join procedures p where p.id=w.id_procedure and p.id_master=$m_id and dt='$dt'";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $income = 0;
    $param2 = 0;
  }else{
    $a = mysql_fetch_array($r);
    $income = intval($a['income'])*$course;
    $param2 = $a['price']*$course-$income;
  }

  $q = "select sum(w.visitors*p.bonus) bonus from master_procedure_week w join procedures p where p.id=w.id_procedure and p.id_master=$m_id and dt='$dt'";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $bonus = 0;
    $param3 = 0;
  }else{
    $a = mysql_fetch_array($r);
    $bonus = intval($a['bonus']);
    $param3 = $bonus;
  }
  $param4 = $income-$outcome-$bonus;

  $r_flag = mysql_query("select chat_old from master_day where id_master=$m_id and dt='$dt'");
  if(mysql_num_rows($r_flag)>0){
    $flag = mysql_result($r_flag,0,0);
  }else $flag = 0;
  if ($flag==1){
    $q = "select sum(chats) chats from master_day where id_master=$m_id and dt>='$dt' and dt<='$dt'+interval 6 day";
    $r = mysql_query($q);
    if (mysql_num_rows($r)==0){
      $param5 = 0;
    }else{
      $a = mysql_fetch_array($r);
      $param5 = intval($a['chats']);
    }
  }else{
    $q = "select max(chats) chats from master_day where id_master=$m_id and dt>='$dt' and dt<='$dt'+interval 6 day";
    $r = mysql_query($q);
    if (mysql_num_rows($r)==0){
      $param5 = 0;
    }else{
      $a = mysql_fetch_array($r);
      $q1 = "select chats from master_day where id_master=$m_id and dt='$dt'-interval 1 day";
      $r1 = mysql_query($q1);
      $a1 = mysql_fetch_array($r1);
      $param5 = intval($a['chats']-$a1['chats']);
    }
  }

  $q = "select sum(records) records from master_procedure_day where id_master=$m_id and dt>='$dt' and dt<='$dt'+interval 6 day";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $param6 = 0;
  }else{
    $a = mysql_fetch_array($r);
    $param6 = intval($a['records']);
  }

  $q = "select sum(visitors)visitors from master_procedure_week where id_master=$m_id and dt='$dt'";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
    $param7 = 0;
  }else{
    $a = mysql_fetch_array($r);
    $param7 = intval($a['visitors']);
  }

  $q = "insert into master_week (id_master,dt,closed,html,param1,param2,param3,param4,param5,param6,param7) values($m_id,'$dt',1,'$html',$param1,$param2,$param3,$param4,$param5,$param6,$param7) on duplicate key update closed=1,html='$html',param1=$param1,param2=$param2,param3=$param3,param4=$param4,param5=$param5,param6=$param6,param7=$param7";

  print f_show_masters($dt);
  mysql_query($q);
  exit;
}

function op_update_pr_city()
{
  $c_id = intval($_POST['c_id']);
  $c_name = $_POST['c_name'];
  $c_dostavka = intval($_POST['c_dostavka']);
  $c_sum_less = intval($_POST['c_sum_less']);
  $c_price_less = intval($_POST['c_price_less']);
  $c_sum_more = intval($_POST['c_sum_more']);
  $c_price_more = intval($_POST['c_price_more']);
  $c_dt_otgruzka = $_POST['c_dt_otgruzka'];
  $c_fact_dostavka = intval($_POST['c_fact_dostavka']);
  $c_allow_bills = ($_POST['c_allow_bills'] == "true" ) ? 1 : 0;
  $q = "select * from pr_city where id=$c_id";
  $r = mysql_query($q);
  if (mysql_num_rows($r)==0){
      // add
      //$q = "INSERT INTO `pr_city`(name, dostavka, sum_less, price_less, sum_more, price_more, dt_otgruzka, fact_dostavka, allow_bills) VALUES ('$c_name',$c_dostavka,$c_sum_less,$c_price_less,$c_sum_more,$c_price_more,'$c_dt_otgruzka',$c_fact_dostavka,$c_allow_bills)";
      $query = sprintf("INSERT INTO `pr_city`(`name`, `dostavka`, `sum_less`, `price_less`, `sum_more`, `price_more`, `dt_otgruzka`, `fact_dostavka`, `allow_bills`) VALUES ('%s',%s,%s,%s,%s,%s,'%s',%s,%s)",
      mysql_real_escape_string($c_name),
      mysql_real_escape_string($c_dostavka),
      mysql_real_escape_string($c_sum_less),
      mysql_real_escape_string($c_price_less),
      mysql_real_escape_string($c_sum_more),
      mysql_real_escape_string($c_price_more),
      mysql_real_escape_string($c_dt_otgruzka),
      mysql_real_escape_string($c_fact_dostavka),
      mysql_real_escape_string($c_allow_bills));
      $r = mysql_query($query);
      $c_id = mysql_insert_id();

  }else{
      // update
    $q = "UPDATE pr_city SET name='$c_name',dostavka=$c_dostavka,sum_less=$c_sum_less,price_less=$c_price_less,sum_more=$c_sum_more,price_more=$c_price_more,`dt_otgruzka`='$c_dt_otgruzka',`fact_dostavka`=$c_fact_dostavka,`allow_bills`=$c_allow_bills WHERE id = $c_id";
    $r = mysql_query($q);
  }
      print $c_id;
}

function op_return_proc(){
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = intval($_POST['price']);
    $comission = intval($_POST['comission']);
    $ball = intval($_POST['ball']);
    $scores = intval($_POST['scores']);
    $query = "UPDATE `procedures` SET `name`='$name', price=$price, comission=$comission, bals=$ball, count_in_scores=$scores, active=1 WHERE `id`=$id";
    $r = mysql_query($query);
}

function op_get_m_city_list(){
  $html = "";
  $_q = "SELECT * FROM `m_city`";
  $_r = mysql_query($_q);//города
  while ($city = mysql_fetch_array($_r)) {
      $html .= "<option value='" . $city['id'] ."'>" . $city['name'] ."</option>";
  }
  print $html;
  exit;
}

function op_get_costs()
{
  $dt_start = $_POST['dt_start'];
  $dt_end = $_POST['dt_end'];
  print f_show_costs($dt_start, $dt_end);
  exit();
}

function op_save_costs()
{
  $costs = $_POST['costs'];
  foreach ($costs as $cost) {
    $id = intval($cost['id']);
    if ($id != 0){
      $q = "select * from costs where id=$id LIMIT 1";
      $r = mysql_query($q);
      if (mysql_num_rows($r)!=0){
        $row = mysql_fetch_array($r);
        if (intval($cost['type']) == 1){
          $query = "UPDATE `costs` SET `name`='". $cost['name']. "',`summ`=".intval($cost['summ'])." WHERE id = $id";
          $r = mysql_query($query);
        }else{
          $week_start = date("Y-m-d",strtotime('monday this week'));
          $next_week_start = date("Y-m-d",strtotime('monday this week') + (60*60*24)*7);
          if (strtotime($next_week_start) >= strtotime($cost['dt'])){
            
            // get all week starts from dt to week_start
            $mondays_count = ceil((time()-strtotime($cost['dt'])) / (7*60*60*24));
            $parentId = 0;
            for ($i=0; $i < $mondays_count; $i++) {
              $week = date('Y-m-d',strtotime($cost['dt']) + (60*60*24*7*$i));
              $query = "UPDATE `costs` SET `name`='". $cost['name']. "',`summ`=".intval($cost['summ'])." WHERE parentId = ".$row['parentId']." AND `dt` = '$week'";
              $r = mysql_query($query);
            }
          }
        }
      }
    }else{
      if (intval($cost['type']) == 1){
        $query = "INSERT INTO `costs`(`name`, `summ`, `type`, `dt`) VALUES ('". $cost['name']. "',".intval($cost['summ']).",".intval($cost['type']).",'". $cost['dt']. "')";
            $r = mysql_query($query);
            $new_id = mysql_insert_id();
            $query = "UPDATE `costs` SET `parentId`=$new_id WHERE id=$new_id";
            $_u = mysql_query($query);
      }else{
        $week_start = date("Y-m-d",strtotime('monday this week'));
        $next_week_start = date("Y-m-d",strtotime('monday this week') + (60*60*24)*7);
        if (strtotime($next_week_start) >= strtotime($cost['dt'])){
          
          // get all week starts from dt to week_start
          $mondays_count = ceil((time()-strtotime($cost['dt'])) / (7*60*60*24));
          $parentId = 0;
          for ($i=0; $i < $mondays_count; $i++) {
            $week = date('Y-m-d',strtotime($cost['dt']) + (60*60*24*7*$i));

            if ($i == 0){
              $query = "INSERT INTO `costs`(`name`, `summ`, `type`, `dt`) VALUES ('". $cost['name']. "',".intval($cost['summ']).",".intval($cost['type']).",'". $week. "')";
              $r = mysql_query($query);
              $new_id = mysql_insert_id();
              $query = "UPDATE `costs` SET `parentId`=$new_id WHERE id=$new_id";
              $_u = mysql_query($query);
              $parentId = $new_id;
            }else{
              $query = "INSERT INTO `costs`(`name`, `summ`, `type`, `dt`,`parentId`) VALUES ('". $cost['name']. "',".intval($cost['summ']).",".intval($cost['type']).",'". $week. "', $parentId)";
              $r = mysql_query($query);
              $new_id = mysql_insert_id();
            }
          }
        }
      }
    }
  }
  $dt_end = date('Y-m-d', strtotime('sunday this week'));
  print f_show_costs($_POST['dt'], $dt_end);
  exit();
}

function op_delete_cost(){
  $id = intval($_POST['id']);
  $q = "UPDATE `costs` SET `isDeleted`=1 where id=$id";
  $r = mysql_query($q);
  exit();
} 

function op_get_masters_profit(){
  $dt_start = $_POST['dt_start'];
  $dt_end = $_POST['dt_end'];
  print f_show_masters_profit($dt_start, $dt_end);
  exit();
}
function op_sendfile(){
   $dt = $_POST['dt'];
  $m_id = $_POST['id_master'];
  $file = $_FILES['fileinput_'.$m_id];
  $src = $file['tmp_name'];
  $size = getimagesize($src);

  if ($size!==false){
      $q = "SELECT * FROM `master_week`  WHERE id_master=$m_id AND dt='$dt' LIMIT 1";
      $r = mysql_query($q);
      $a = mysql_fetch_array($r);
      $files = unserialize($a['files']);
      $fileindex = 0;
      if ($a['files'] == null){
        $files = [];
      }
      $fileindex = count($files);


    $width = 650;
    $height = 650;
    list($width_orig, $height_orig) = $size; // 1000, 700

    $isrc  = imagecreatefromstring(file_get_contents($src));
    $exif = @exif_read_data($src);
    if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
            case 8:
                $isrc = imagerotate($isrc,90,0);
                $temp = $width_orig; 
                $width_orig = $height_orig; 
                $height_orig = $temp; 
                break;
            case 3:
                $isrc = imagerotate($isrc,180,0);
                break;
            case 6:
                $isrc = imagerotate($isrc,-90,0);
                $temp = $width_orig;
                $width_orig = $height_orig;
                $height_orig = $temp;
                break;
        }
    }
    
    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
       $width = $height*$ratio_orig;
    } else {
       $height = $width/$ratio_orig;
    }

    // ресэмплирование
    //$isrc  = imagecreatefromstring(file_get_contents($src));
    $idest = imagecreatetruecolor($width, $height);
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    $filename = '/bills/' . $dt . $m_id ."_". ($fileindex+1) .".jpg";
    imagejpeg($idest,$_SERVER['DOCUMENT_ROOT'].$filename);
    array_push($files, $filename);
    $toPrint = "";
    foreach ($files as $key => $filename) {
        $toPrint .= "<img style='margin-bottom:20px;width:90%;height:50%;' src='$filename?r=".rand()."'><br/>";
    }
    print $toPrint;
    $files = serialize($files);
    mysql_query("update master_week w set w.bill_checked=1, w.files='$files' where w.id_master=$m_id and w.dt='$dt'");

  }else print "Неизвестный формат файла";
  exit;
}

function op_get_clean_profit_data_by_dates(){
  $dates = $_POST['dates'];
  $data = [];
foreach ($dates as $dt) {
   $f_profit = 0;
  $f_outcome = 0;
  $f_sum = 0;
  $f_comission = 0;
  $f_bonus = 0;
  $r_master = mysql_query("select id, percent_val, by_percent from masters");
  while($a_master = mysql_fetch_array($r_master)){
    $m_id = $a_master['id']; 
    $m_percent_val = $a_master['percent_val'];
	$m_by_percent = $a_master['by_percent'];
	  
	$r2 = mysql_query("select t.* from topmanagers t,masters m where m.id_topmanager=t.id_user and m.id=$m_id");
    $a2 = mysql_fetch_array($r2);
    $tm_bonus1 = $a2['bonus1'];
    $tm_bonus2 = $a2['bonus2'];
	  
    $r = mysql_query("select outcome,paid,course,sum_no_self from master_week where id_master=$m_id and dt='$dt'");
    $a = mysql_fetch_array($r);
    $outcome = intval($a['outcome']);
    $paid = intval($a['paid']);
    $course = $a['course']; 
    $sum_no_self = intval($a['sum_no_self']);
    
    $sum_visitors = 0;
    $sum = 0;
    $sum_comission = 0;
    $sum_bonus = 0;
    $r = mysql_query("select p.name,p.price,p.bonus,p.topmanager_bonus,p.comission,p.count_in_scores,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$dt' where p.id_master=$m_id");
    while($a = mysql_fetch_array($r)){
      $price = intval($a['price']);
      $comission = intval($a['comission']);
      $visitors = intval($a['visitors']);
      $bonus = intval($a['bonus']);
	  $topmanager_bonus = intval($a['topmanager_bonus']);
      if($a['count_in_scores']==1){
        if ($topmanager_bonus == 0){
          $bonus+=$tm_bonus1;
        }else{
          $bonus+=$topmanager_bonus;
        }
      }else {
        if ($topmanager_bonus == 0){
          $bonus+=$tm_bonus2;
        }else{
          $bonus+=$topmanager_bonus;
        }
      }
      if ($visitors>0){
        $sum_visitors += $visitors;
        $sum += $visitors*$price;
        $sum_comission += $visitors*$comission;
        $sum_bonus += $visitors*$bonus;
        $sum_price += $visitors*$price;
      }
    } 
    if($m_by_percent==1){
      $sum_comission1 = $sum_no_self*$m_percent_val/100;
    }else{
      $sum_comission1 = $sum_comission;
    } 
    $sum_comission_tg = $sum_comission1;
    if ($course>0)$sum_comission_tg *= $course;
    if ($course>0)$sum_comission1 *= $course;
    $sum1 = $sum;
    if ($course>0)$sum1 *= $course;
    $sum_bonus1 = $sum_bonus;
    $outcome1 = $outcome; 
    $sum_bonus_tg = $sum_bonus;
    
    $f_profit+=round(floatval($sum_comission_tg-$outcome1-$sum_bonus_tg));
    $f_outcome += $outcome1;
    $f_sum += $sum1;
    $f_comission += $sum_comission_tg;
    $f_bonus += $sum_bonus1;
  }
	$f_week_outcome = 0;

	$c = mysql_query("SELECT summ FROM `costs`  WHERE dt='$dt' AND isDeleted=0 AND type=1");
	while($a = mysql_fetch_array($c)){
		$f_week_outcome += intval($a['summ']);
	}

	$c = mysql_query("SELECT summ FROM `costs` WHERE (type = 2 || type = 3) AND dt='$dt' and isDeleted=0");
	while ($a = mysql_fetch_array($c)){
		$days = cal_days_in_month(CAL_GREGORIAN, date('m', $dt), date('Y', $dt));
		$costPerWeek = ceil($a["summ"] / $days * 7);
		$f_week_outcome += $costPerWeek;
	}
	$f_profit = round($f_profit - $f_week_outcome, -3);

        $data['dt'][] = $dt;
        $data['value'][] = $f_profit;
    }
  print_r(json_encode($data));
  exit();
} 

function op_save_currency(){
    $id = $_POST['id'];
    $name = $_POST['name'];


    $q = "select * from currencies where id=$id";
    $r = mysql_query($q);

    if (mysql_num_rows($r)==0){
      $query = "INSERT INTO `currencies`( `name`) VALUES ('$name')";
      $r = mysql_query($query);
      $id = mysql_insert_id();
    }else{
      if ($name == ''){
        $query = "DELETE FROM `currencies` WHERE `id`=$id";
      }else{
        $query = "UPDATE `currencies` SET `name`='$name' WHERE `id`=$id";
      }
      $r = mysql_query($query);
    }
    print $id;
    exit();
}
function op_show_masters_by_city(){
  $dt = $_POST['dt'];
  $dt = date('Y-m-d', strtotime($dt));
  $masters =  f_show_masters_by_city($dt);
  $analytics = f_analytics_show_all_statistics();
  $html = $analytics . $masters;
  print $html;
  exit();
}
function op_save_bonuses(){
  $id = intval($_POST["id"]);
  $base_percent = $_POST["base_percent"];
  $rewards = $_POST["rewards"];
  $nameskal = $_POST["nameskal"];
  $procentoperator = $_POST["procentoperator"];

  $query = "UPDATE `bonus` SET `base_percent`='$base_percent', namebonus='$nameskal', procentoperator='$procentoperator' WHERE `id`=$id";
  $r = mysql_query($query);
  if ($id==0){
      $query = "insert into `bonus`(dt_to, dt_from, base_percent, namebonus, procentoperator) values('2025-12-31', '2018-08-27', '$base_percent', '$nameskal', '$procentoperator')";
      $r = mysql_query($query);
      $id=mysql_insert_id();
  }



    foreach($rewards as $row){
    $reward_id = intval($row["id"]);
    $summ = intval($row["summ"]);
    $reward = intval($row["reward"]);
    if ($reward_id == 0){
      $query = "INSERT INTO `bonus_rewards`(`bonus_id`,`summ`,`reward`) VALUES ($id, $summ, $reward)";
    }else{
      $query = "UPDATE `bonus_rewards` SET `summ`='$summ', `reward`='$reward' WHERE `id`=$reward_id";
    }
    $r = mysql_query($query);
  }

  $tmpl = "";
  $query = "SELECT * FROM `bonus_rewards` WHERE `bonus_id`=$id";
  $r = mysql_query($query);
  while($a = mysql_fetch_array($r)){
    $reward_id = $a['id'];
    $summ = $a['summ'];
    $reward = $a['reward'];
    $tmpl .= "<tr class='bonus_reward' data-id='$reward_id'><td><input type='text' data-type='summ' value='$summ' required/></td><td><input type='text' data-type='reward' value='$reward' required/></td><td><a href='#' class='remove-reward' onclick='removeReward(this);'>Удалить</a></td></tr>";
  }

  if ($nameskal=="" && $id>1)
  {
      $query = "delete from `bonus` WHERE `id`=$id";
      $r = mysql_query($query);
      $query = "delete from `bonus_rewards` WHERE `bonus_id`=$id";
      $r = mysql_query($query);
  }

  print $tmpl;
  exit();
}

function op_save_bonusesezh(){
    $id = intval($_POST["id"]);
    $base_percent = $_POST["base_percent"];
    $rewards = $_POST["rewards"];
    $nameskal = $_POST["nameskal"];

    $query = "UPDATE `bonusezh` SET `base_percent`='$base_percent', namebonus='$nameskal' WHERE `id`=$id";
    $r = mysql_query($query);
    if ($id==0){
        $query = "insert into `bonusezh`(base_percent, namebonus) values($base_percent, '$nameskal')";
        $r = mysql_query($query);
        $id=mysql_insert_id();
    }



    foreach($rewards as $row){
        $reward_id = intval($row["id"]);
        $summ = intval($row["summ"]);
        $reward = intval($row["reward"]);
        if ($reward_id == 0){
            $query = "INSERT INTO `bonus_rewardsezh`(`bonus_id`,`summ`,`reward`) VALUES ($id, $summ, $reward)";
        }else{
            $query = "UPDATE `bonus_rewardsezh` SET `summ`='$summ', `reward`='$reward' WHERE `id`=$reward_id";
        }
        $r = mysql_query($query);
    }

    $tmpl = "";
    $query = "SELECT * FROM `bonus_rewardsezh` WHERE `bonus_id`=$id";
    $r = mysql_query($query);
    while($a = mysql_fetch_array($r)){
        $reward_id = $a['id'];
        $summ = $a['summ'];
        $reward = $a['reward'];
        $tmpl .= "<tr class='bonus_reward' data-id='$reward_id'><td><input type='text' data-type='summ' value='$summ' required/></td><td><input type='text' data-type='reward' value='$reward' required/></td><td><a href='#' class='remove-reward' onclick='removeReward(this);'>Удалить</a></td></tr>";
    }

    if ($nameskal=="")
    {
        $query = "delete from `bonusezh` WHERE `id`=$id";
        $r = mysql_query($query);
        $query = "delete from `bonus_rewardsezh` WHERE `bonus_id`=$id";
        $r = mysql_query($query);
    }

    print $tmpl;
    exit();
}


function op_remove_reward(){
  $id = intval($_POST["id"]);
  $query = "DELETE FROM `bonus_rewards` WHERE `id`=$id";
  $r = mysql_query($query);
  print "OK";
  exit();
}

function op_remove_rewardezh(){
    $id = intval($_POST["id"]);
    $query = "DELETE FROM `bonus_rewardsezh` WHERE `id`=$id";
    $r = mysql_query($query);
    print "OK";
    exit();
}