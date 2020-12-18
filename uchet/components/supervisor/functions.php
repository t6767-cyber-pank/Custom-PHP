<?php
include($_SERVER['DOCUMENT_ROOT']."/timurnf/payments.php");

function f_show_pr_cities(){
  $html = "";
  $html = "<div></div>";

  $r = mysql_query("select * from pr_city");//маркетологи
  while($a1 = mysql_fetch_array($r)){
    $c_id = $a1['id'];
    $c_name = $a1['name'];
    $c_dostavka  = $a1['dostavka'];
    $c_fact_dostavka = $a1['fact_dostavka'];
    $c_sum_less = $a1['sum_less'];
    $c_price_less = $a1['price_less'];
    $c_sum_more = $a1['sum_more'];
    $c_price_more = $a1['price_more'];
    $c_dt_otgruzka = $a1['dt_otgruzka'];
    $c_allow_bills = $a1['allow_bills'];

    $html .= "<div class='options_block' style='border:1px solid;'>";
    $html .= "  <div id='" . rand() . "' style='margin:0 0 10px 10px'>";
    $html .= "    <input type='hidden' class='c_id' value='" . $c_id . "'>";
    $html .= "    <div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Имя: ";
    $html .= "        <input type='text' class='c_name' value='" . htmlspecialchars($c_name) . "'>";
    $html .= "      </div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Доставка: ";
    $html .= "        <input type='text' class='c_dostavka' value='" . htmlspecialchars($c_dostavka) . "'>";
    $html .= "      </div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Факт доставка: ";
    $html .= "        <input type='text' class='c_fact_dostavka' value='" . htmlspecialchars($c_fact_dostavka). "'>";
    $html .= "      </div>";
    $html .= "    </div>";
    $html .= "    <div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Сумм меньше: ";
    $html .= "        <input type='text' class='c_sum_less' value='" . htmlspecialchars($c_sum_less). "'>";
    $html .= "      </div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Сумм больше: ";
    $html .= "        <input type='text' class='c_sum_more' value='" . htmlspecialchars($c_sum_more). "'>";
    $html .= "      </div>";
    $html .= "    </div>";
    $html .= "    <div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Цена меньше: ";
    $html .= "        <input type='text' class='c_price_less' value='" . htmlspecialchars($c_price_less). "'>";
    $html .= "      </div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Цена больше: ";
    $html .= "        <input type='text' class='c_sum_more' value='" . htmlspecialchars($c_sum_more). "'>";
    $html .= "      </div>";
    $html .= "    </div>";
    $html .= "    <div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        ДТ отгрузка: ";
    $html .= "        <input type='text' class='c_dt_otgruzka' value='" . htmlspecialchars($c_dt_otgruzka). "'>";
    $html .= "      </div>";
    $html .= "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>";
    $html .= "        Allow Bills: ";
    $html .= "        <input type='checkbox' class='c_allow_bills' value=1" . (($c_allow_bills==1) ? " checked" : "") . ">";
    $html .= "      </div>";
    $html .= "    </div>";
    $html .= "    <div style='display:inline-block;padding-bottom:10px;'>";
    $html .= "      <input type='button' class='orange' value='Сохранить' onclick=\"update_pr_city($(this).closest('.options_block')); return false;\">";
    $html .= "    </div>";
    $html .= "  </div>";
    $html .= "</div>";
  }
  $html .= "<div class='add_link'>";
  $html .= "<a href='' onclick='add_pr_city();return false;'>Добавить Город</a>";
  $html .= "</div>";
  
  return $html;
}

function f_show_shop($id,$dt){
    $ezh_shop=new ezh_shop();
    $s_name=$ezh_shop->Name(3);
    $payzp=new payzp($dt, "6", "");
    $sum_bonus = $payzp->paySellerEzh();
    $ezh_city_day=new ezh_city_day();
    $ezh_city_day->set_dt($dt);
    $ezh_city_day->set_dt($ezh_city_day->get_monday());
    $ezh_city_day->set_dt_to($ezh_city_day->get_sunday());
    $ezh_city=new ezh_city();
    $cityes=$ezh_city->selAllCity();
    $pr_city=new pr_city();
    $pr_order=new pr_order();
    $pr_order->set_dt($dt);
    $pr_order->set_dt($pr_order->get_monday());
    $pr_order->set_dt_to($pr_order->get_sunday());
    $cityImportVKEzh=new cityImportVKEzh();
    $cityImportVKEzh->set_dt($dt);
    $html = "";
    $templat = new templater();
    $cc = $ezh_city_day->CityesWorkWeekVK(1);
    $html .= "<div class='T_M_N_B_TABLE_width'>";
    $html .= "<div class='T_M_E_S_left'>";
    $html .= "<div class='T_M_N_B_TABLE_name_shop'><b>$s_name</b></div>";
    $html .= "</div>";
    $html .= "<div class='T_M_E_S_R'><b>Бонусов за неделю $sum_bonus</b>";
    $html .= "</div>";
    $html .="<div class='T_M_both'></div>";
    $html .= "<div  class='T_M_E_S_left'>";
    $html .= "</div>";
    $html .= "<div class='T_M_E_S_right T_M_E_S_vk  T_M_E_S_VK_Block'>";
    $html .= $templat->printTableContactsStat($cc, "ВК общий", $ezh_city_day->getMaxContactsDayVK());
    $html .= $templat->printTableContactsStat($cc, "ВК общий", $ezh_city_day->getMaxContactsDayVK());
    $html .= "    <table class='T_M_E_S_tableOutcomes'>";
    $html .= "    <tr>";
    $html .= "    <td class='header'>Сумма за неделю</td>";
    $html .= "    <td class='header'>Заказы за неделю</td>";
    $html .= "    <td class='header'>Единиц товара за неделю</td>";
    $html .= "    <td class='header'>Расходы за неделю</td>";
    $html .= "    <td class='header'>Бонусы за неделю</td>";
    $html .= "    </tr>";
    $html .= "    <tr>";
    $html .= "    <td align='center'><b>".$pr_order->getSumsPeriodVkOrdersBezDost()."</b></td>";
    $html .= "    <td align='center'><b>".$pr_order->getSumsPeriodOrdersDoneVK()."</b></td>";
    $html .= "    <td align='center'><b>".$pr_order->getSumsPeriodNumberOrdersDoneVK()."</b></td>";
    $html .= "    <td align='center'><b>".$cityImportVKEzh->CityesOutcomesWeek()."</b></td>";
    $html .= "    <td align='center'><b>" .($pr_order->getSumsPeriodNumberOrdersDoneVK()*40)."</b></td>";
    $html .= "    </tr>";
    $html .= "    </table>";
    $html .= "</div>";

    $html .= "<div class='T_M_E_S_right T_M_E_S_vk  T_M_E_S_VK_Block'>";
    $html .= $templat->printTableContactsStat2($ezh_city_day->ezhDirect(1), "INSTAGRAM DIRECT Казахстан");
    $html .= "</div>";

    $html .="<div class='T_M_both'></div>";
    foreach ($cityes as $cit) {
        $c_id = $cit['id'];
        $c_name = $cit['name'];
        $c_bonus = $cit['bonus'];
        $prc=$pr_city->getCityByName($c_name);
        if ((int)$prc['id']==0) continue;
        $pr_c_id = $prc['id'];
        $dostavka = $prc['dostavka'];
        $fact_dostavka = $prc['fact_dostavka'];
        $amount=$pr_order->getSumsPeriodNumberOrdersDone($pr_c_id);
        $outcome = $ezh_city_day->getOutcomeCity($c_id);
        $orders = $pr_order->getSumsPeriodOrdersDone($pr_c_id);
        $dost=($dostavka*$orders)-($fact_dostavka*$orders);
        $sum_fact_nodostavka=$pr_order->getSummOrderbyCity($pr_c_id)-$pr_order->skidkaPeriodcity($pr_c_id)+$dost;

        $html .= "<div  class='T_M_E_S_left'><b>$c_name</b>";
        $html .= "</div>";
        $html .="<div class='T_M_both'></div>";
        $c_id = $cit['id'];
        $cc = $ezh_city_day->CityesWorkWeek($c_id);
        $html .= "<div class='T_M_E_S_right'>";
        $html .= $templat->printTableContactsStat($cc, "WhatsApp", $ezh_city_day->getMaxContactsDay($c_id), 0);

        $html .= "    <table class='T_M_E_S_tableOutcomes'>";
        $html .= "    <tr>";
        $html .= "    <td class='header'>Сумма за неделю</td>";
        $html .= "    <td class='header'>Заказы за неделю</td>";
        $html .= "    <td class='header'>Единиц товара за неделю</td>";
        $html .= "    <td class='header'>Расходы за неделю</td>";
        $html .= "    <td class='header'>Бонусы за неделю</td>";
        $html .= "    </tr>";
        $html .= "    <tr>";
        $html .= "    <td align='center'><b>$sum_fact_nodostavka</b></td>";
        $html .= "    <td align='center'><b>$orders</b></td>";
        $html .= "    <td align='center'><b>$amount</b></td>";
        $html .= "    <td align='center'><b>$outcome</b></td>";
        $html .= "    <td align='center'><b>" . $amount*$c_bonus . "</b></td>";
        $html .= "    </tr>";
        $html .= "    </table>";

        $html .= "</div>";
        $html .="<div class='T_M_both'></div>";
    }

    $html .= "<div class='T_M_E_S_right T_M_E_S_vk  T_M_E_S_VK_Block'>";
    $html .= $templat->printTableContactsStat2($ezh_city_day->ezhDirect(0), "INSTAGRAM DIRECT Россия");
    $html .= "</div>";


    $html .= "</div>";

    $html .="<div class='T_M_both'></div>";

    ///////////////////////////////////////////////
    $cityarr = array();
    $cityidarr = array();
    $citycount = array();
    $r = mysql_query("SELECT * FROM pr_city");
    while ($a = mysql_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
    {
        $rcount = mysql_query("SELECT count(distinct cl.phone) as ttt FROM `pr_city` c, `pr_client` cl, `pr_order` ord where cl.id=ord.id_client and c.id=ord.id_city and c.id=".$a['id']);
        $acount = mysql_fetch_array($rcount);
        array_push($cityarr, $a['name']);
        array_push($cityidarr, $a['id']);
        array_push($citycount, $acount['ttt']);
    }
    $html .= "<br/><br/>";
    $html .= "<div align='center' style='border: 1px solid; margin: 10px;'><h2>Скачать контакты в формате EXCEL документа по ссылке</h2>";
    $html .= "<a href='/excelOtchet.php?idcity=".$cityidarr[1]."&city=".$cityarr[1]."' >".$cityarr[1]."</a> - кол-во ".$citycount[1]." шт.<br/>";
    $html .= "<a href='/excelOtchet.php?idcity=".$cityidarr[2]."&city=".$cityarr[2]."' >".$cityarr[2]."</a> - кол-во ".$citycount[2]." шт.<br/>";
    $html .= "<a href='/excelOtchet.php?idcity=".$cityidarr[3]."&city=".$cityarr[3]."' >".$cityarr[3]."</a> - кол-во ".$citycount[3]." шт.<br/>";
    $html .= "<a href='/excelOtchet.php?idcity=".$cityidarr[4]."&city=".$cityarr[4]."' >".$cityarr[4]."</a> - кол-во ".$citycount[4]." шт.<br/>";
    $html .= "<a href='/excelOtchet.php?idcity=".$cityidarr[0]."&city=".$cityarr[0]."' >".$cityarr[0]."</a> - кол-во ".$citycount[0]." шт.<br/><br/>";

    $rcount = mysql_query("SELECT count(distinct phone) as ttt FROM `pr_client`");
    $acount = mysql_fetch_array($rcount);
    $html .= "<a href='/excelOtchet.php?idcity=0&city=ВсеКонтакты' >Отчет все телефоны с базы данных</a> - кол-во ".$acount['ttt']." шт.</a><br/><br/></div>";
  return $html;
}

function f_show_topmanagers_settings(){
  $html = "";
  $html .= "<div class='topmanagers_inner' style='display:none;'></div>";

  $r = mysql_query("select * from users where type=4");//старшие менеджеры
  while($a = mysql_fetch_array($r)){
    $u_id = $a['id'];
    $u_name = $a['name'];
    $u_pass = $a['password'];
    $r_users = mysql_query("select * from topmanagers where id_user=$u_id");
    if (mysql_num_rows($r_users)==0){
      $id_topmanager = 0;
      $bonus1 = '';
      $bonus2 = '';
    }else{
      $a_users = mysql_fetch_array($r_users);
      $id_topmanager = $a_users['id'];
      $bonus1 = $a_users['bonus1'];
      $bonus2 = $a_users['bonus2'];
    }

  $html .= "<div id='" . rand(). "' class='topmanagers_inner options_block' style='width: 1200px;'>";
  $html .= "<input type='hidden' class='u_id' value='$u_id'>";
  $html .= "<div style='margin:0 0 20px 10px;'>";
  $html .= "<div style='display:inline-block;padding-right:10px;'>";
  $html .= "Имя: <input type='text' class='u_name' value='". htmlspecialchars($u_name) ."'>";
  $html .= "</div>";
  $html .= "<div style='display:inline-block;padding-right:10px;'>";
  $html .= "Пароль: <input type='text' class='u_pass' value='" .htmlspecialchars($u_pass). "'>";
  $html .= "</div>";
  $html .= "<div style='display:inline-block;padding-bottom:10px;'>";
  $html .= "<input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().parent().find(\".u_pass\").get(0).value,4,$(this).parent().parent().parent().get(0).id)'>";
  $html .= "</div>";
  $html .= "</div>";
  $html .= "<div style='margin:0 0 20px 10px;'>";
  $html .= "<span style='padding-right:15px;'>Размер бонуса за результативные записи</span><input type='text' style='width:50px;' class='u_bonus1' value='" . htmlspecialchars($bonus1) . "'>";
  $html .= "<span style='padding-left:30px;padding-right:15px;'>остальные записи</span><input type='text' style='width:50px;' class='u_bonus2' value='" . htmlspecialchars($bonus2). "'>";
  $html .= "</div>";
  $html .= "</div>";
    }
  $html .= "<div class='add_link'>";
  $html .= "<a href='' onclick='add_topmanager();return false;'>Добавить старшего менеджера</a>";
  $html .= "</div>";
  return $html;
}

function f_show_shops($dt){
  $html = "";
  $r = mysql_query("select * from ezh_shop");
  while ($a = mysql_fetch_array($r)){
    $s_id = $a['id'];
    $html .= "<div id='shop$s_id' style='padding-bottom:30px;'>";
    $html .= f_show_shop($s_id,$dt);
    $html .= "</div>";
  }
  return $html;
}

function f_show_shop_lists($id=-1){
  $html = "";
  $html .= "Продавец: <select class='id_seller'>";
  $html .= "<option value='-1'></option>";

  $r = mysql_query("select * from ezh_shop where id=$id");
  if (mysql_num_rows($r)==0){
    $id_seller = -1;
    $id_marketolog = -1;
  }else{
    $a = mysql_fetch_array($r);
    $id_seller = $a['id_seller'];
    $id_marketolog = $a['id_marketolog'];
  }
  $r = mysql_query("select id,name from users where type=6");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];
    $html .= "<option value='$id'" . (($id==$id_seller) ? "selected" : "") . ">$name</option>";
  }

  $html .= "</select>";
  $html .= "Маркетолог: <select class='id_marketolog'>";
  $html .= "<option value='-1'></option>";

  $r = mysql_query("select id,name from users where type=2");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];
    $html .= "<option value='$id'" . (($id==$id_marketolog) ? "selected" : "") . ">$name</option>";

  }

  $html .= "</select>";
  $html .= "<input style='position:absolute;left:730px;bottom:30px;' type='button' class='orange' value='Сохранить' onclick='save_shop($(this).parent().parent().parent().get(0).id)'>";
  return $html;
}

function f_show_shop_info($id=-1){
  $html = "";
  $r = mysql_query("select * from ezh_city where id_shop=$id");

  $html .= "<div class='city'></div>";

  $i = 1;
  while($a = mysql_fetch_array($r)){
    $c_id = $a['id'];
    $c_name = $a['name'];
    $c_bonus = $a['bonus'];

    $html .= "<div class='city' id='" . rand(). "'>";
    $html .= "<input type='hidden' class='c_id' value='$c_id'>";
    $html .= "<div style='display:inline-block;padding:0 10px 20px;'>Город <input type='text' class='c_name' value='" . htmlspecialchars($c_name) . "'></div>";
    $html .= "<div style='display:inline-block;padding-right:10px'>Бонус <input type='text' style='width:50px;' class='c_bonus' value='" . htmlspecialchars($c_bonus). "'></div>";
    $html .= "</div>";

    $i++;
  }

  $html .= "<div class='add_link' style='padding-bottom:10px;'>";
  $html .= "<a href='' onclick='add_city($(this).parent().parent().parent().get(0).id);return false;'>Добавить город</a>";
  $html .= "</div>";
  $html .= "<div class='c_list'>" . f_show_shop_lists($id). "</div>";

  return $html;
}

function f_show_shops_settings(){
  $html = "";
  $html .= "<div class='shops_inner' style='display:none;'></div>";

  $r = mysql_query("select * from ezh_shop");//магазины
  $cnt = mysql_num_rows($r);
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];

    $html .= "<div id='". rand() . "' class='shops_inner options_block'>";
    $html .= "<input type='hidden' class='id' value='$id'>";
    $html .= "<div style='margin:0 0 20px 10px;'>";
    $html .= "Имя: <input type='text' class='name' value='". htmlspecialchars($name) . "'>";
    $html .= "</div>";
    $html .= "<div class='others' style='display:inline-block;'>". f_show_shop_info($id) . "</div>";
    $html .= "</div>";

  }

  $html .= "<div class='add_link'>";
  $html .= "<a href='' onclick='add_shop();return false;'>Добавить магазин</a>";
  $html .= "</div>";

  return $html;
}



function f_show_masters_settings($isShown = true, $includeInterestCalculation = false){ // с галочкой показывать, включить расчет по процентам
  $html = "";
  $html .= "<div class='masters_inner' style='display:none;'></div>";

  $query = "select u.* from users u left join masters m on u.id=m.id_master where u.type=0";
  if ($includeInterestCalculation){
    $query = $query . " and m.by_percent = 1";
  }else{
    $query = $query . " and m.shown = " . ($isShown ? "1" : "0");
  }
  $query = $query . " order by m.sort, m.sortsecond";
  $r = mysql_query($query);//мастера
  $cnt = mysql_num_rows($r);
  $k = 0;
  while($a = mysql_fetch_array($r)){
    $u_id = $a['id'];
    $u_name = $a['name'];
    $u_pass = $a['password'];
    $r_masters = mysql_query("select * from masters where id_master=$u_id");
    if (mysql_num_rows($r_masters)==0){
      $id_master = -1;
      $email = '';
      $course=0;
      $shown = 1;
      $by_percent = 0;
      $percent_val = '';
      $m_city = 0;
      $sortm=0;
      $sortsec=0;
    }else{
      $a_masters = mysql_fetch_array($r_masters);
      $email = $a_masters['email'];
      $id_master = $a_masters['id'];
      $use_course = $a_masters['use_course'];
        $use_vk = $a_masters['usevk'];
      $currency_id = $a_masters['currency_id'];
      $by_percent = $a_masters['by_percent'];
      $percent_val = $a_masters['percent_val'];
      $shown = $a_masters['shown'];
      $m_city = $a_masters['id_m_city'];
      $sortm=$a_masters['sort'];
      $sortsec=$a_masters['sortsecond'];
      if ($sortm<=0) $sortm=1;
    }
  $html .= "<div id='" . rand() . "' class='masters_inner options_block'>";
//  if ( $k > 0 ) {
    $html .= "<div style='display:inline-block;position:absolute;top:10px;right:10px;'><select style='background: coral;' id='sor1m$id_master'>";
  for ($i=1; $i<101; $i++)
  {
      if ($sortm==$i) {$selx="selected";} else {$selx="";}
    $html .= "<option value='$i' $selx>$i</option>";
  }
    $html .= "</select>";

      $html .= "<select id='sor2m$id_master'>";
      for ($i=1; $i<11; $i++)
      {
          if ($sortsec==$i) {$selxsec="selected";} else {$selxsec="";}
          $html .= "<option value='$i' $selxsec>$i</option>";
      }
      $html .= "</select>";

    $html .= "<button onclick='savemastersort($id_master)'>OK</button></div>";
//  }
  if( $k < $cnt-1){
    $html .= "<div style='display:inline-block;position:absolute;bottom:10px;right:10px;'></div>";
  }
  $html .= "<input type='hidden' class='u_id' value='$u_id'>";
  $html .= "<div style='margin:0 0 20px 10px;'>";
  $html .= "Имя: <input type='text' class='u_name' value='" . htmlspecialchars($u_name) ."'>";
  $html .= "Пароль: <input type='text' class='u_pass' value='" . htmlspecialchars($u_pass) ."'>";
  $html .= "Email: <input type='text' class='u_email' value='" . htmlspecialchars($email) ."'>";
  $html .= "<label for='use_course$u_id' class='u_label'>Учитывать курс</label><input type='checkbox' id='use_course$u_id' class='use_course'" . (($use_course==1) ? " checked" : "") . " value=1>";
  $html .= "<label for='shown$u_id' class='u_shown'>Показывать</label><input type='checkbox' id='shown$u_id' class='shown'" . (($shown==1) ? " checked" : "") ." value=1>";
  $html .= "<label for='vorkvk$u_id' class='u_vkvork'>Работа ВК</label><input type='checkbox' id='vorkvk$u_id' class='vorkvk'" . (($use_vk==1) ? " checked" : "") . " value=" . (($use_vk==1) ? "1" : "0") . ">";
  $html .= "</div>";
  $html .= "<div style='margin:0 0 20px 10px;'>";
  $html .= "<input type='checkbox' id='by_percent$u_id' class='by_percent'" . (($by_percent==1) ? " checked" : "") ." value=1><label for='by_percent$u_id'>Включить расчет по процентам</label>";
  $html .= "<input style='width:50px;' type='text' class='percent_val' value='" . htmlspecialchars($percent_val) . "'> %";
  $html .= "<span style='margin-left:40px;'>Город: <select class='id_m_city'>";
  $html .= "<option value='0'></option>";

  $_q = "SELECT * FROM `m_city`";
  $_r = mysql_query($_q);//города
  while ($city = mysql_fetch_array($_r)) {
      $html .= "<option value='" . $city['id'] ."' " . (($city['id']==$m_city) ? " selected" : "") .">" . $city['name'] ."</option>";
  }

  $html .= "</select></span>";
  $m_cityX=new m_city();

      $html .= "<div class='T_M_admin_City_block T_M_pad20px'>Коэффициент: <input type='text' class='koefic m_proc T_M_input_numb' value='".htmlspecialchars($m_cityX->selcoef($m_city))."'></div>";
      $html .= "</div>";
	  
  if ($use_course==1){
  $html .= "<div style='margin:0 0 20px 10px;'>";

  $html .= "<span>Валюта: <select class='currency_id'>";
  $html .= "<option value='0'></option>";

    $_q = "SELECT * FROM `currencies`";
    $_r = mysql_query($_q);//валюты
    while ($currency = mysql_fetch_array($_r)) {
        $html .= "<option value='" . $currency['id'] ."' " . (($currency['id']==$currency_id) ? " selected" : "") .">" . $currency['name'] ."</option>";
    }

    $html .= "</select></span>";
    $html .= "</div>";
  }
	  
  $html .= "<div class='u_others' style='display:inline-block;'>" . f_show_user_info($id_master) . "</div>";
  $html .= "</div>";

    $k++;
  }

  $html .= "<div class='add_link'>";
  $html .= "<a href='' onclick='add_master();return false;'>Добавить мастера</a>";
  $html .= "</div>";
  return $html;
}

function f_show_profit($dt){
  $f_profit = 0;
  $f_outcome = 0;
  $f_outcomevk = 0;
  $f_outcomevkwork=0;
  $f_sum = 0;
  $f_comission = 0;
  $f_bonus = 0;
  $f_new_bonuses = 0;
  $r_master = mysql_query("select id, percent_val, by_percent from masters");
  while($a_master = mysql_fetch_array($r_master)){
    $m_id = $a_master['id']; 
    $m_percent_val = $a_master['percent_val'];
	$m_by_percent = $a_master['by_percent'];
	  
	$r2 = mysql_query("select t.* from topmanagers t,masters m where m.id_topmanager=t.id_user and m.id=$m_id");
    $a2 = mysql_fetch_array($r2);
    $tm_bonus1 = $a2['bonus1'];
    $tm_bonus2 = $a2['bonus2'];
	  
    $r = mysql_query("select outcome, outcomevk, outcomeworkvk ,paid,course,sum_no_self from master_week where id_master=$m_id and unix_timestamp(dt)='$dt'");
    $a = mysql_fetch_array($r);
    $outcome = intval($a['outcome']);
    $outcomevk = intval($a['outcomevk']);
    $outcomevkwork = (int)$a['outcomeworkvk'];
    $paid = intval($a['paid']);
    $course = $a['course']; 
    $sum_no_self = intval($a['sum_no_self']);
    
    $sum_visitors = 0;
    $sum = 0;
    $sum_comission = 0;
    $sum_bonus = 0;
    $sum_price = 0;
    $bonusesoper=0;
    $r = mysql_query("select p.name,p.price,p.bonus,p.topmanager_bonus,p.comission,p.count_in_scores,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and unix_timestamp(dt)='$dt' where p.id_master=$m_id");
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
    $outcomevk1 = $outcomevk;
      $outcomevkwork1 = $outcomevkwork;
    $sum_bonus_tg = $sum_bonus;
    
    $f_profit+=round(floatval($sum_comission_tg-$outcome1-$outcomevk1-$outcomevkwork1));
    $f_outcome += $outcome1;
    $f_outcomevk += $outcomevk1;
      $f_outcomevkwork += $outcomevkwork1;
    $f_sum += $sum1;
    $f_comission += $sum_comission_tg;
    $f_bonus += $sum_bonus1;
  }

    $f_week_outcome = 0;
    
    $c = mysql_query("SELECT summ FROM `costs`  WHERE unix_timestamp(dt)='$dt' AND isDeleted=0 AND type=1");
    while($a = mysql_fetch_array($c)){
      $f_week_outcome += intval($a['summ']);
    }

    $c = mysql_query("SELECT summ FROM `costs` WHERE (type = 2 || type = 3) AND unix_timestamp(dt)='$dt' and isDeleted=0");
    while ($a = mysql_fetch_array($c)){
      $days = cal_days_in_month(CAL_GREGORIAN, date('m', $dt), date('Y', $dt));
      $costPerWeek = ceil($a["summ"] / $days * 7);
      $f_week_outcome += $costPerWeek;
    }
    $f_profit = round($f_profit - $f_week_outcome);

    $r = mysql_query("select * from users where type in(1) order by type desc,id");
    
    while ($a = mysql_fetch_array($r)){
      $manager_id = intval($a['id']);
        $bonusHous=new bonushousemanager(date("Y-m-d", $dt), $manager_id);
        if ($dt>1545598800) {
          $f_new_bonuses += $bonusHous->getManagerBonus();
      } else {
          $f_new_bonuses += $bonusHous->getManagerBonusOld($manager_id);
    }
    }

    $ro = mysql_query("select distinct iduser from bonushouseoper");

    while ($ao = mysql_fetch_array($ro)){
        $manager_id = intval($ao['iduser']);
        $bonusesoper += getOperatorBonus($manager_id, date("Y-m-d", $dt)); // сумма новых бонусов
    }

    $dxx=strtotime("2019-07-01 ");
    if ($dt<$dxx){ $bonusesoper=0; }

    $f_profit = round($f_profit - $f_new_bonuses-$bonusesoper);

	$arr = array('outcome'=>$f_outcome,'profit'=>$f_profit,'sum_comission'=>$f_comission,'sum'=>$f_sum,'bonus'=>$f_bonus,'week_outcome' => $f_week_outcome, 'new_bonuses' => $f_new_bonuses);
	$html = "";
	$html .= "<br/>";
	$html .= "<div style='display:none; height: 40px;' id='div_profit'>";
	$html .= " <div style='width:12%;float:left'>Чистая прибыль<br/><b> ". round($arr['profit']) ."</b></div>";
	$html .= " <div style='width:10%;float:left'>Общая сумма<br/><b> " .round(($arr['sum'])) ."</b></div>";
	$html .= " <div style='width:8%;float:left'>Доход с комиссий<br/><b> " . round($arr['sum_comission']) . "</b></div>";
	$html .= " <div style='width:12%;float:left'>Расходы недели<br/><b> " . round($arr['week_outcome']) . "</b></div>";
	$html .= " <div style='width:12%;float:left'>Рекл. Instagram<br/><b> " . round($f_outcome) . "</b></div>";
    $html .= " <div style='width:8%;float:left'>Рекл. ВК<br/><b> " . round($f_outcomevk) . "</b></div>";
    $html .= " <div style='width:8%;float:left'>Работа ВК<br/><b> " . round($f_outcomevkwork) . "</b></div>";
    $html .= " <div style='width:10%;float:left'>Бонусы менеджеров <br/><b> " . round($arr['new_bonuses']) . "</b></div>";
    $html .= " <div style='width:8%;float:left'>Бонусы операторов <br/><b> " . round($bonusesoper) . "</b></div>";
    $html .= " <div style='width:10%;float:left'><h4 style='margin: 0px;'><span style='font-size: 15px;'><input type='checkbox' id='cbdolg1' onclick='toggleCheckbox(this); notchekc(document.getElementById(\"cbdolg2\"));' >показать <br/>должников</span></h4></div>";
    $html .= " <div style='width:10%;float:left'><h4 style='margin: 0px;'><span style='font-size: 15px;'><input type='checkbox' id='cbdolg2' onclick='toggleCheckbox2(this); notchekc(document.getElementById(\"cbdolg1\"));' >одобрить<br/>чеки</span></h4></div>";
    $html .= "<br><div style='display: block; background-color: #f2F2F2; text-align: center; position: fixed; left: 80%; top: 200px;'>";
?>
    <script>
        function toggleCheckbox(element)
        {
            var elem2 = document.getElementById('dolgspis2');
            elem2.style.display = 'none';
//            var elemcb = document.getElementById('cbdolg2');
//            alert(elemcb.checked);
            //            elemcb.checked = false;
            var elem = document.getElementById('dolgspis');
            if (element.checked) elem.style.display = 'block'; else elem.style.display = 'none';
           //  element.checked = !element.checked;
        }

        function toggleCheckbox2(element)
        {
            var elem2 = document.getElementById('dolgspis');
            elem2.style.display = 'none';
//            var elemcb = document.getElementById('cbdolg1');
//            alert(elemcb.checked);
            //            elemcb.checked = false;

            var elem = document.getElementById('dolgspis2');
            if (element.checked) elem.style.display = 'block'; else elem.style.display = 'none';
            //  element.checked = !element.checked;
        }

        function notchekc(element) {
            element.checked = false;
        }
    </script>
<?php
	$daterv=date("Y-m-d", $dt);

    $html .= "<ol id='dolgspis' style='list-style-type: none; display: none;'>";
    $masters=new masters();
    $masters->set_dt($daterv);
    $html .= $masters->getDolg("<li>", "</li>");

    $html .= "</ol>";

    $html .= "<ol id='dolgspis2' style='list-style-type: none; display: none;'>";
    $masters=new masters();
    $masters->set_dt($daterv);
    $html .= $masters->getDolg2("<li>", "</li>");

    $html .= "</ol>";


    $html .= "</div></div>";
	return $html;
}


function f_show_master_graph($id,$dt,$dt1){
  $q = "select m.id,u.name from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_id = $a['id'];
  $m_name = $a['name'];

  $m = array();
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1])-3600*24*7;
  $dt_from = date("d.m.Y",$t);
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt1,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
  $dt_to = date("d.m.Y",$t);
  $html ="";
$html .="<div style='padding-left:10px;'>";
$html .="  <b>$m_name</b>";
$html .="</div>";

$html .="<div style='border:1px solid;padding-bottom:20px;margin-top:15px;margin-bottom:10px;'> ";

$html .="  <div style='width:95%;margin-left:auto;margin-right:auto;margin-bottom:10px;'>";

$html .="    <div id='div_graph1$m_id' style='margin-top:10px;margin-bottom:10px;'>";
$html .="      <img style='width:100%' src='/graph1.php?id=$m_id&dt_from=" . urlencode($dt_from). "&dt_to=" . urlencode($dt_to). "&salt=" . rand(). "'>";
$html .="    </div>";

$html .="    <div style='padding-left:50px;padding-top:30px;padding-bottom:30px;'>";
$html .="   <div style='background-color:#9BBB59;' class='legend'>";
$html .="     <input type='checkbox' id='ch_graph1_$m_id' class='ch_graph1' checked onchange='show_graph1($m_id,\"". urlencode($dt_from). "\",\"" .urlencode($dt_to) . "\")'>";
$html .="     <label for='ch_graph1_$m_id'>Чистая прибыль</label>";
$html .="   </div>";

$html .="   <div style='background-color:#C0504D;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph2_$m_id' class='ch_graph2' checked onchange='show_graph1($m_id,\"" . urlencode($dt_from) . "\",\"" . urlencode($dt_to) . "\")'>";
$html .="     <label for='ch_graph2_$m_id'>Расходы</label>";
$html .="   </div>";

$html .="   <div style='background-color:#D99694;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph3_$m_id' class='ch_graph3' checked onchange='show_graph1($m_id,\"" . urlencode($dt_from). "\",\"" . urlencode($dt_to) . "\")'>";
$html .="     <label for='ch_graph3_$m_id'>Бонусы</label>";
$html .="   </div>";

$html .="   <div style='background-color:#A6A6A6;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph4_$m_id' class='ch_graph4' checked onchange='show_graph1($m_id,\"". urlencode($dt_from). "\",\"" . urlencode($dt_to). "\")'>";
$html .="         <label for='ch_graph4_$m_id'>Прибыль мастера</label>";
$html .="   </div>";

$html .="   <div style='background-color:#C4C4C4;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph8_$m_id' class='ch_graph8' checked onchange='show_graph1($m_id,\"". urlencode($dt_from) . "\",\"". urlencode($dt_to)."\")'>";
$html .="         <label for='ch_graph8_$m_id'>Особые события</label>";
$html .="   </div>";

$html .="      <div style='clear:both'></div>";

$html .="    </div>";

$html .="    <div id='div_graph2$m_id' style='margin-top:10px;margin-bottom:10px;'>";
$html .="      <img style='width:100%' src='/graph2.php?id=$m_id&dt_from=" . urlencode($dt_from). "&dt_to=" . urlencode($dt_to). "&salt=" . rand(). "'>";
$html .="    </div>";

$html .="    <div style='padding-left:50px;padding-top:30px;padding-bottom:30px;'>";

$html .="   <div style='background-color:#558ED5;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph5_$m_id' class='ch_graph1' checked onchange='show_graph2($m_id,\"" . urlencode($dt_from). "\",\"". urlencode($dt_to). "\")'>";
$html .="         <label for='ch_graph5_$m_id'>Новые контакты</label>";
$html .="   </div>";

$html .="   <div style='background-color:#95B3D7;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph6_$m_id' class='ch_graph2' checked onchange='show_graph2($m_id,\"". urlencode($dt_from). "\",\"". urlencode($dt_to). "\")'>";
$html .="         <label for='ch_graph6_$m_id'>Записи</label>";
$html .="   </div>";

$html .="   <div style='background-color:#F79646;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph7_$m_id' class='ch_graph3' checked onchange='show_graph2($m_id,\"". urlencode($dt_from). "\",\"". urlencode($dt_to). "\")'>";
$html .="         <label for='ch_graph7_$m_id'>Пришедшие</label>";
$html .="   </div>";

$html .="   <div style='background-color:#C4C4C4;' class='legend'>";
$html .="         <input type='checkbox' id='ch_graph9_$m_id' class='ch_graph9' checked onchange='show_graph2($m_id,\"". urlencode($dt_from). "\",\"" . urlencode($dt_to). "\")'>";
$html .="         <label for='ch_graph9_$m_id'>Особые события</label>";
$html .="   </div>";

$html .="      <div style='clear:both'></div>";

$html .="    </div>";
$html .="  </div>";
$html .="</div>";


  return $html;
}


function f_show_master_analitics($id,$dt){
  $q = "select m.id,u.name from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_id = $a['id'];
  $m_name = $a['name'];
  $html = "";
  $html .= "<div style='padding-left:10px;'><b>$m_name</b></div>";
  $html .= "<div style='border:1px solid;padding-top:10px;padding-bottom:20px;margin-top:15px;margin-bottom:10px;'>";
  $html .= "<div style='width:95%;margin-left:auto;margin-right:auto;margin-bottom:10px;display:flex;flex-direction:row;'>";
    $html .= "<div style='float:left;'>";
      $html .= "<table frame='none' rules='void'>";

  $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
 

  $html .= "    <tr>";
  $html .= "    <td></td>";

  for ($i = 1;$i<count($arr);$i++){

  $html .= "  <td align='center' style='width:40px;padding-bottom:15px;' class='header'>$arr[$i]</td>";

  }

  $html .= "    </tr>";
  $html .= "    <tr>";
  $html .= "    <td style='white-space:nowrap;padding-bottom:5px;'>Новые контакты</td>";

    for($i=1;$i<=7;$i++){
      $i1 = $i-1;
      $i2 = $i-2;
      $q = "select chats,chat_old from master_day where id_master=$m_id and dt='$dt'+interval $i1 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $chats = $a1['chats'];
      $q = "select chats,chat_old from master_day where id_master=$m_id and dt='$dt'+interval $i2 day";
      $r1 = mysql_query($q);
      $a2 = mysql_fetch_array($r1);
      $chats_old = $a2['chats'];
      if ($a1['chat_old']==0 && $a2['chat_old']==0){
        if ($chats==''){$chats_str = '';$chats1 = '';}
        else {$chats1 = $chats-$chats_old;$chats_str = $chats-$chats_old;}
      }
      elseif ($chats==0){$chats1 = '';$chats_str = 0;}
      else {$chats1 = $chats;$chats_str = $chats;}

      $html .= "<td align='center'>$chats_str</td>";

    }

    $html .= "  </tr>";
    $html .= "  </table>";

    $flag_r = mysql_query("select chat_old from master_day where id_master=$m_id and dt='$dt'");
    if(mysql_num_rows($flag_r)>0)$flag = 0;
    else{
      $flag_a = mysql_fetch_array($flag_r);
      $flag = $flag_a['chat_old'];
    }
    if ($flag==1){
      $q = "select contacts from master_week where id_master=$m_id and dt='$dt'";
    }else{
      $q = "select chats contacts from master_day where id_master=$m_id and dt='$dt'-interval 1 day";
    }
//print $q;
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $contacts = intval($a1['contacts']);
    if ($flag==1){
      $q = "select contacts from master_week where id_master=$m_id and dt='$dt'-interval 1 week";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $contacts_old = intval($a1['contacts']);
      $contacts_new = $contacts-$contacts_old;
    }else{
      $q = "select max(chats) contacts from master_day where id_master=$m_id and dt>='$dt' and dt<='$dt'+interval 6 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $contacts_new = $a1['contacts']-$contacts;
    }
    $q = "select sum(records) full_sum from master_procedure_day d left join procedures p on p.id=d.id_procedure where d.id_master=$m_id and d.dt>='$dt' and d.dt<='$dt'+interval 6 day and p.count_in_scores=1";
//    print $q;
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);    
    $records_full = intval($a1['full_sum']);
    if ($contacts_new>0)$percent = round($records_full/$contacts_new,2)*100;
    else $percent=0;

      $html .= "<br>";
      $html .= "<table style='margin-top:5px;margin-left:auto;margin-right:auto;'>";
      $html .= "<tr><td align='right' style='padding-bottom:20px;'>Количество контактов</td><td style='padding-left:10px;padding-bottom:20px;'>$contacts</td>";
      $html .= "<tr><td align='right' style='padding-bottom:20px;'>Новых за неделю</td><td style='padding-left:10px;padding-bottom:20px;'>$contacts_new</td>";
      $html .= "<tr><td align='right' style='padding-bottom:20px;'>Результативность</td><td style='padding-left:10px;padding-bottom:20px;'>$percent%</td>";
      $html .= "</table>";
    $html .= "</div>";
    $html .= "<div style='float:left;margin-left:20px;'>";
    $html .= "  <table frame='none' rules='void'>";

  $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс','Итого');

      $html .= "<tr>";
      $html .= "<td width=200 style='padding-bottom:15px;'></td>";

  for ($i = 1;$i<count($arr)-1;$i++){

    $html .= "  <td align='center' style='width:40px;padding-bottom:15px;' class='header'>$arr[$i]</td>";

  }

    $html .= "  <td style='width:80px;padding-bottom:15px;' align=center class='header'>Итого</td>";
    $html .= "  </tr>";

  $r = mysql_query("select id,name,bonus from procedures where id_master=$m_id");
  while ($a = mysql_fetch_array($r)){
    $p_id = $a['id'];
    $p_name = $a['name'];
    $p_bonus = $a['bonus'];


    $html .= "  <tr>";
    $html .= "  <td style='padding-bottom:30px;'>$p_name</td>";

    $records_sum = 0;
    for($i=1;$i<=7;$i++){
      $i1 = $i-1;
      $q = "select records from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt='$dt'+interval $i1 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $records = intval($a1['records']);
//      if ($records==0)$records1 = '';else $records1 = $records;
      $records_sum += $records;

      $html .= "<td  style='padding-bottom:30px;' align='center'>$records</td>";

    }

      $html .= "<td style='padding-bottom:30px;' align='center'>$records_sum шт</td>";
      $html .= "</tr>";

  }

    $html .= "      </table>";
    $html .= "    </div>";
    $html .= "    <div style='clear:both'></div>";
    $html .= "  </div>";
    $html .= "</div>";
  return $html;
}


function f_show_master_stat($id,$dt){
  $q = "select m.id,u.name,m.by_percent,m.percent_val from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_id = $a['id'];
  $m_name = $a['name'];
  $m_by_percent = $a['by_percent'];
  $m_percent_val = $a['percent_val'];

  $r = mysql_query("select t.* from topmanagers t,masters m where m.id_topmanager=t.id_user and m.id=$m_id");
  $a = mysql_fetch_array($r);
  $r = mysql_query("select outcome,paid,course,bill_checked,closed,html,sum_no_self,files from master_week where id_master=$m_id and dt='$dt'");
  $a = mysql_fetch_array($r);

  if ($a['closed']==1){
    echo $a['html'];

  }else{ 
    $files = unserialize($a['files']);
	  
    if ($files == null){
      $files = [ 0 => "/bills/".$dt.$id."_1.jpg" ];
    }
    $html = "";
    $html .= "<div style='padding-left:10px;'><b>$m_name</b></div>";
    $html .= "<div style='border:1px solid;padding-top:10px;padding-bottom:20px;margin-top:15px;margin-bottom:10px;'>";
    $html .= "  <div style='float:left;width:300px;'>";
    $html .= "    <table rules='none' frame='void' style='width:100%;'>";
    $html .= "    <tr>";
    $html .= "      <td width='80%' align='right' style='padding-bottom:30px;' class='header'>Процедуры за неделю</td>";
    $html .= "      <td width='20%' align='center' style='padding-bottom:30px;' class='header'>шт</td>";
    $html .= "    </tr>";

    $outcome = intval($a['outcome']);
    $paid = intval($a['paid']);
    $course = $a['course'];
    $bill_checked = $a['bill_checked'];
    $sum_no_self = intval($a['sum_no_self']);

    $arr_text = array();
    $sum_visitors = 0;
    $sum = 0;
    $sum_comission = 0;
    $arr_comission = array();
    $arr_comission1 = array();
    $sum_price = 0;
    $r = mysql_query("select p.name,p.price,p.bonus,p.topmanager_bonus,p.comission,p.count_in_scores,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$dt' where p.id_master=$m_id order by p.sort desc");
    while($a = mysql_fetch_array($r)){
      $price = intval($a['price']);
      $name = $a['name'].' ('.$price.')';
      $comission = intval($a['comission']);
      $visitors = intval($a['visitors']);
      $bonus = intval($a['bonus']);
      $topmanager_bonus = intval($a['topmanager_bonus']);
 
      if ($visitors>0){
        $str = ' - '.$name.' x '.$visitors.' шт = '.$visitors*$price;
        $arr_text[] = $str;
        $sum_visitors += $visitors;
        $sum += $visitors*$price;
        $sum_comission += $visitors*$comission;
        if ($course>0){
          $arr_comission[] = $visitors*$comission;
          $arr_comission1[] = $visitors*$comission*$course;
        }else{
          $arr_comission[] = $visitors*$comission;
          $arr_comission1[] = $visitors*$comission;
        }
        $sum_price += $visitors*$price;

    $html .= "<tr>";
    $html .= "  <td align='right' style='padding-bottom:30px;'><b>$name</b></td>";
    $html .= "  <td align='center' style='padding-bottom:30px;'>$visitors</td>";
    $html .= "</tr>";

      }
    }
    if($m_by_percent==1){
      $sum_comission1 = $sum_no_self*$m_percent_val/100;
    }else{
      $sum_comission1 = $sum_comission;
    }
	$sum_comission_tg = $sum_comission1;
    if ($course>0)$sum_comission_tg *= $course;
    $sum1 = $sum;
    $sum_tg = $sum1;
    if ($course>0) {
      $sum1 *= $course;
      $sum_tg *= $course;
    }

    $outcome1 = $outcome;
    $sum_no_self_tg = $sum_no_self;
    if ($course>0)$sum_no_self_tg *= $course;

$html .= "    </table>";
$html .= "  </div>";

$html .= "  <div style='float:left;width:350px;margin-left:10px;margin-top:5px;'>";
$html .= "<div class='header' style='padding-bottom:20px;'>Расшифровка:</div>";
$html .= "    <div class='text_to_copy'>";
$html .= "Всего процедур: $sum_visitors<br>";
$html .= implode("<br>\n",$arr_text)."<br>";

if ($m_by_percent==1) {
    $html .= "Сумма с процедур $sum<br>";
}
else
{
    $html .= "Сумма $sum<br>";
}
$valyuta='тг';

$dtrazn=date_diff(new DateTime(), new DateTime($dt))->days;
$proc=0;
$dnstr="дня";
if ($dtrazn>8) { $proc=2; }
if ($dtrazn>9) { $proc=3; }
if ($dtrazn>10) { $proc=4; }
if ($dtrazn>11) { $proc=5; $dnstr="дней"; }
if ($dtrazn>12) { $proc=6; $dnstr="дней"; }
$dtrazn=$dtrazn-7;
if ($m_by_percent==1){
    $html .= "Сумма минус себестоимость $sum_no_self_tg<br>\n";
    $html .= "Комиссия $sum_comission_tg<br>\n";
    if (($bill_checked==0) && ($sum_comission_tg>0) && ($proc>0)) {
        $xsum_com = $sum_comission_tg / 100 * $proc;
        $html .= "<b>Пеня: " . $xsum_com . " ($proc% от комиссии за $dtrazn $dnstr просрочки)</b><br>\n";
        $xitogtg=$sum_comission_tg+$xsum_com;
        $html .= "<b>Итого: " .$xitogtg. "</b><br>\n";
    }
}else{
$html .= "Комиссия $sum_comission (". implode(" + ",$arr_comission). ")<br>\n";
    if (($bill_checked==0) && ($sum_comission>0) && ($proc>0)) {
        $xsumc = $sum_comission / 100 * $proc;
        $html .= "<b>Пеня: " . $xsumc." ($proc% от комиссии за $dtrazn $dnstr просрочки)</b><br>\n"; //$bill_checked
        $xitogtg=$sum_comission+$xsumc;
        $html .= "<b>Итого: " .$xitogtg. "</b><br>\n";
    }
}

$html .= "    </div>";
      if ($m_by_percent==1){
          if ($course>0)$valyuta='руб';
          $html .= "<br ><div style='width: 100%;'><span style='float: right'>Комиссия: <b>$sum_comission_tg $valyuta</b></span></div>";
      }else{
          if ($course>0)$valyuta='руб';
          $html .= "<br ><div style='width: 100%; '><span style='float: right'>Комиссия: <b>$sum_comission $valyuta</b></span></div>";
      }
$html .= "  </div>";
$html .= "  <div style='float:left;margin-left:10px;margin-top:5px;padding-right:10px;text-align:center;border-left:1px solid;border-right:1px solid;padding-left:10px;'>";
$html .= "  </div>";

$html .= "  <div style='float:left;margin-left:10px;margin-top:5px;text-align:center;padding-right:10px;width:200px;'>";

 if($bill_checked>0){
$html .= "<div style='margin-top:10px;'>";
	foreach ($files as $key => $filename) {	
	  $html .= "<a href='$filename' target='_blank' style='margin-bottom:10px;'><img src='$filename' style='width:90%;height:50%;'></a> <br/>";
	}
$html .= "</div>";
 } 
 if($bill_checked!=2){
$html .= "<div style='margin-top:10px;'>";
$html .= "<input id='bill_button$id' type='button' class='orange' value='Подтвердить' onclick='bill_button_click(\"$id\",\"$dt\",$(\"#bill_status$id\").val())'>";
$html .= "<input type='hidden' id='bill_status$id' value=2>";
$html .= "</div>";
 } 
 if($bill_checked==2){
$html .= "<div style='margin-top:10px;'>";
$html .= "<input id='bill_button$id' type='button' class='green' value='Отменить' onclick='bill_button_click(\"$id\",\"$dt\",$(\"#bill_status$id\").val())'>";
$html .= "<input type='hidden' id='bill_status$id' value=1>";
$html .= "</div>";
 } 
 
 if ($bill_checked == 0){ 
  $html .="<div id='picture_".$m_id."' class='form-group'  style='margin-top:10px;'></div>";
  $html .="<form id='fileform_".$m_id."'>";
  $html .="<input class='orange' id='abortfile' type='button' value='Отмена' onclick='xhr.abort();' style='display:none;'>";
  $html .="<input class='orange' id='openfile' type='button' value='Загрузить чек' onclick='document.getElementById(\"fileinput_".$m_id."\").click();'>";
  $html .="<input id='fileinput_".$m_id."' name='fileinput_".$m_id."' style='display:none;' type='file' onchange='sendfile(".$m_id.", \"".$dt."\");'>";
  $html .="</form>";
 }
	  
$html .= "  </div>";
$html .= "  <div style='clear:both'></div>";
$html .= "</div>";
$html .= "<div style='clear:both'></div>";

  }
  return $html;
}



function f_show_masters_by_city($dt,$dt1=0){

  $html = "";
$cities = mysql_query("SELECT * FROM `m_city`");
  while ($city = mysql_fetch_array($cities)){ 
    $city_id = $city['id'];

        $masters_query = "select u.* from users u,masters m where u.type=0 and u.id=m.id_master and m.shown=1 and m.id_m_city=$city_id";
        $masters_query .= " order by m.sort";
        $masters_resource = mysql_query($masters_query);
        if (mysql_num_rows($masters_resource) > 0){

$html .="        <section class='city' style='width: 1200px;position:relative;' data-id='$city_id'>";
$html .="            <h3>". $city['name']. "</h3>";
$html .="            <div style='border:1px solid black; padding: 106px 10px 10px 10px; margin-bottom: 20px;'>";
$html .="             <div style='overflow-x:auto;'>";               
$html .="             <table><tr>";               
                    while ($masters_array = mysql_fetch_array($masters_resource)){
                      $master_id = $masters_array['id'];
                

$html .="                    <td style='vertical-align:top;'><div id='master$master_id' style=' width: 500px;float:left; padding:10px;'>";
                
$html .=                    f_analytics_show_master($master_id,$dt);
                
$html .="                    </div></td>";
                
                    }
$html .=                "</tr></table> ";
$html .=                "</div>";
$html .="              <div style='clear:both;'></div>";
$html .=                     f_analytics_show_contacts($city_id,$dt);

 $html .=                    f_analytics_show_city_statistics($city_id,$dt);
$html .="            </div>";
$html .="        </section>";

   }
  }
  return $html;
}

function f_analytics_show_all_statistics(){ 
   $GLOBALS['stats']['global']['procent']['City_Efficiency'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Contacts'] = 0;
    $GLOBALS['stats']['global']['procent']['City_ContactsVK'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Master_Records'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Extras_Records'] = 0;
    $GLOBALS['stats']['global']['procent']['City_Master_RecordsVK'] = 0;
    $GLOBALS['stats']['global']['procent']['City_Extras_RecordsVK'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Visitors'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Bonuses'] = 0;

   $GLOBALS['stats']['global']['noprocent']['City_Efficiency'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Contacts'] = 0;
    $GLOBALS['stats']['global']['noprocent']['City_ContactsVK'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Master_Records'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Extras_Records'] = 0;
    $GLOBALS['stats']['global']['noprocent']['City_Master_RecordsVK'] = 0;
    $GLOBALS['stats']['global']['noprocent']['City_Extras_RecordsVK'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Visitors'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Bonuses'] = 0;
   $i = 0;
    foreach ($GLOBALS['stats']['cities'] as $key => $city) {
      $i++;
      $procent = (isset($city['OnProcent']) && $city['OnProcent']) ? 'procent' : 'noprocent';

     $GLOBALS['stats']['global'][$procent]['City_Contacts'] += $city['City_Contacts'];
        $GLOBALS['stats']['global'][$procent]['City_ContactsVK'] += $city['City_ContactsVK'];
     $GLOBALS['stats']['global'][$procent]['City_Master_Records'] += $city['City_Master_Records'];
     $GLOBALS['stats']['global'][$procent]['City_Extras_Records'] += $city['City_Extras_Records'];
        $GLOBALS['stats']['global'][$procent]['City_Master_RecordsVK'] += $city['City_Master_RecordsVK'];
        $GLOBALS['stats']['global'][$procent]['City_Extras_RecordsVK'] += $city['City_Extras_RecordsVK'];
     $GLOBALS['stats']['global'][$procent]['City_Visitors'] += $city['City_Visitors'];
     $GLOBALS['stats']['global'][$procent]['City_Bonuses'] += $city['City_Bonuses'];
   }
   $arr = ['noprocent', 'procent'];

   foreach ($arr as $p) {
    if ($GLOBALS['stats']['global'][$p]['City_Contacts'] != 0){
      $GLOBALS['stats']['global'][$p]['City_Efficiency'] = round($GLOBALS['stats']['global'][$p]['City_Master_Records']*100/$GLOBALS['stats']['global'][$p]['City_Contacts']);
    }else{
      $GLOBALS['stats']['global'][$p]['City_Efficiency'] = 0;
    }

       if ($GLOBALS['stats']['global'][$p]['City_ContactsVK'] != 0){
           $GLOBALS['stats']['global'][$p]['City_EfficiencyVK'] = round($GLOBALS['stats']['global'][$p]['City_Master_RecordsVK']*100/$GLOBALS['stats']['global'][$p]['City_ContactsVK']);
       }else{
           $GLOBALS['stats']['global'][$p]['City_EfficiencyVK'] = 0;
       }
   }
$html = "";

foreach ($arr as $p) {
$html .="  <div id='statistics' style='width: 1200px; margin: 10px auto; padding: 10px; background-color: #eedbb4;'>";
$html .="    <table style='width: 100%;'>";
$html .="        <tr>";
$html .="            <td style='width: 35%;font-size:20px;'>";
$html .= $p == 'procent' ? "%" : "";
$html .="                Конверсия Instagram";
$html .="                <strong id='City_Efficiency'>".$GLOBALS['stats']['global'][$p]['City_Efficiency']."%</strong>"; 
$html .="            </td>";
$html .="            <td style='text-align: center;'>";
$html .="                Контакты";
$html .="                <br/>";
$xkb=$GLOBALS['stats']['global'][$p]['City_Contacts'];
$html .="                <strong id='City_Contacts'>".$xkb."</strong>";
$html .="            </td>";
$html .="            <td style='text-align: center;'>"; 
$html .="                Основные записи: ";
$html .="                <br/>";
$html .="                <strong id='City_Master_Records'>".$GLOBALS['stats']['global'][$p]['City_Master_Records']."</strong>";
$html .="            </td>";
$html .="            <td style='text-align: center;'>";
$html .="                Доп. записи";
$html .="                <br/>";
$html .="                <strong id='City_Extras_Records'>".$GLOBALS['stats']['global'][$p]['City_Extras_Records']."</strong>";
$html .="            </td>";
$html .="            <td style='text-align: center;'>";
$html .="                Пришедшие";
$html .="                <br/>";
$html .="                <strong id='City_Visitors'>".$GLOBALS['stats']['global'][$p]['City_Visitors']."</strong>";
$html .="            </td>";
$html .="            <td style='text-align: center;'>";
$html .="                Бонусы";
$html .="                <br/>";
$html .="                <strong id='City_Bonuses'>".$GLOBALS['stats']['global'][$p]['City_Bonuses']."</strong>";
$html .="            </td>";
$html .="        </tr>";
$html .="    </table>  ";
    $html .="    <table style='width: 100%;'>";
    $html .="        <tr>";
    $html .="            <td style='width: 35%;font-size:20px;'>";
    $html .= $p == 'procent' ? "%" : "";
    $html .="                Конверсия ВК";
    $html .="                <strong id='City_Efficiency'>".$GLOBALS['stats']['global'][$p]['City_EfficiencyVK']."%</strong>";
    $html .="            </td>";
    $html .="            <td style='text-align: center;'>";
    $html .="                Контакты";
    $html .="                <br/>";
    $xkb=$GLOBALS['stats']['global'][$p]['City_ContactsVK'];
    $html .="                <strong id='City_Contacts'>".$xkb."</strong>";
    $html .="            </td>";
    $html .="            <td style='text-align: center;'>";
    $html .="                Основные записи: ";
    $html .="                <br/>";
    $html .="                <strong id='City_Master_Records'>".$GLOBALS['stats']['global'][$p]['City_Master_RecordsVK']."</strong>";
    $html .="            </td>";
    $html .="            <td style='text-align: center;'>";
    $html .="                Доп. записи";
    $html .="                <br/>";
    $html .="                <strong id='City_Extras_Records'>".$GLOBALS['stats']['global'][$p]['City_Extras_RecordsVK']."</strong>";
    $html .="            </td>";
    $html .="            <td style='text-align: center;'>";
    $html .="                Пришедшие";
    $html .="                <br/>";
    $html .="                <strong id='City_Visitors'>".$GLOBALS['stats']['global'][$p]['City_Visitors']."</strong>";
    $html .="            </td>";
    $html .="            <td style='text-align: center;'>";
    $html .="                Бонусы";
    $html .="                <br/>";
    $html .="                <strong id='City_Bonuses'>".$GLOBALS['stats']['global'][$p]['City_Bonuses']."</strong>";
    $html .="            </td>";
    $html .="        </tr>";
    $html .="    </table>  ";
$html .="  </div>";
	}
return $html;
}

function f_analytics_show_city_statistics($city_id,$dt){
  $html = "";
  if ($GLOBALS['stats']['cities'][$city_id]['City_Contacts'] == 0){
      $GLOBALS['stats']['cities'][$city_id]['City_Efficiency'] = 0;
   }else{
      $GLOBALS['stats']['cities'][$city_id]['City_Efficiency'] = round($GLOBALS['stats']['cities'][$city_id]['City_Master_Records']*100/$GLOBALS['stats']['cities'][$city_id]['City_Contacts']);
   }
    if ($GLOBALS['stats']['cities'][$city_id]['City_ContactsVK'] == 0){
        $GLOBALS['stats']['cities'][$city_id]['City_EfficiencyVK'] = 0;
    }else{
        $GLOBALS['stats']['cities'][$city_id]['City_EfficiencyVK'] = round($GLOBALS['stats']['cities'][$city_id]['City_Master_RecordsVK']*100/$GLOBALS['stats']['cities'][$city_id]['City_ContactsVK']);
    }

$html .="             <div style='position:absolute;top:60px;left:0;width:1178px;padding:10px;border:1px solid black;border-top:0;background-color:white;'>";
$html .="              <table style='width: 100%;'>";
$html .="                    <tr>";
$html .="                        <td style='width: 35%;font-size:20px;'>";
$html .="                            Конверсия Instagram";
$html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Efficiency'] . "%";

$html .="                            </strong>";
$html .="                        </td>";
$html .="                        <td style='text-align: center;'>";
$html .="                            Контакты";
$html .="                            <br/>";
$xvb=$GLOBALS['stats']['cities'][$city_id]['City_Contacts'];
$html .="                            <strong>". $xvb;

$html .="                            </strong>";
$html .="                        </td>";
$html .="                        <td style='text-align: center;'>";
$html .="                            Основные записи";
$html .="                            <br/>";
$html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Master_Records'];

$html .="                            </strong>";
$html .="                        </td>";
$html .="                        <td style='text-align: center;'>";
$html .="                            Доп. записи";
$html .="                            <br/>";
$html .="                            <strong>". (($GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] == null) ? 0 : $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records']);

$html .="                            </strong>";
$html .="                        </td>";
$html .="                        <td style='text-align: center;'>";
$html .="                            Пришедшие";
$html .="                            <br/>";
$html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Visitors'];

$html .="                            </strong>";
$html .="                        </td>";
$html .="                        <td style='text-align: center;'>";
$html .="                            Бонусы";
$html .="                            <br/>";
$html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Bonuses'];

$html .="                            </strong>";
$html .="                        </td>";
$html .="                    </tr>";
$html .="                </table>";
    $html .="              <table style='width: 100%;'>";
    $html .="                    <tr>";
    $html .="                        <td style='width: 35%;font-size:20px;'>";
    $html .="                            Конверсия ВК";
    $html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_EfficiencyVK'] . "%";

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                        <td style='text-align: center;'>";
    $html .="                            Контакты";
    $html .="                            <br/>";
    $xvb=$GLOBALS['stats']['cities'][$city_id]['City_ContactsVK'];
    $html .="                            <strong>". $xvb;

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                        <td style='text-align: center;'>";
    $html .="                            Основные записи";
    $html .="                            <br/>";
    $html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Master_RecordsVK'];

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                        <td style='text-align: center;'>";
    $html .="                            Доп. записи";
    $html .="                            <br/>";
    $html .="                            <strong>". (($GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK'] == null) ? 0 : $GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK']);

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                        <td style='text-align: center;'>";
    $html .="                            Пришедшие";
    $html .="                            <br/>";
    $html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Visitors'];

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                        <td style='text-align: center;'>";
    $html .="                            Бонусы";
    $html .="                            <br/>";
    $html .="                            <strong>". $GLOBALS['stats']['cities'][$city_id]['City_Bonuses'];

    $html .="                            </strong>";
    $html .="                        </td>";
    $html .="                    </tr>";
    $html .="                </table>";
$html .="             </div>";
return $html;
}

function f_analytics_show_contacts($city_id,$dt){
$html = "";
$html .="            <div style='margin-top: 20px;'>";
$html .="                <table style='width: 100%;' class='city_table' data-id='$city_id'>";
$html .="                  <tr>";
$html .="                    <td></td>";
$html .="                    <td style='text-align: center;'>Пн</td>";
$html .="                    <td style='text-align: center;'>Вт</td>";
$html .="                    <td style='text-align: center;'>Ср</td>";
$html .="                    <td style='text-align: center;'>Чт</td>";
$html .="                    <td style='text-align: center;'>Пт</td>";
$html .="                    <td style='text-align: center;'>Сб</td> ";
$html .="                    <td style='text-align: center;'>Вс</td>";
$html .="                    <td></td>";
$html .="                    <td></td>";
$html .="                  </tr>";
$html .="                  <tr>";
$html .="                    <td style='text-align: right;''>Контакты в тел.</td>";
   
    $q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $isOnPercent = intval($a1['count']) > 0;
	
    $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'-interval 1 day";
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $chats_old = $a1['chats'];
  
$html .="                    <input type='hidden' value='$chats_old' disabled>";
  
  $new_chats = [];
  for($i=1;$i<=7;$i++){
      $i1 = $i-1;
      $i2 = $i-2;
      $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $chats = $a1['chats'];

      $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i2 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $chats_old = $a1['chats'];
      $diff = $chats - $chats_old;
      $diff = ($diff < 0) ? 0 : $diff;
      $new_chats[$i] = $diff;
      $GLOBALS['stats']['cities'][$city_id]['City_Contacts'] += intval($new_chats[$i]);
      $current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
      if ($chats>$chats_max)$chats_max = $chats;
  
$html .="                    <td style='text-align: center;'><input type='text' style='width: 50px;' value='$chats' class='p_input' disabled></td>";
  
  }

  $other_contacts = 0;

  $q = "select * from m_city_week where m_city_id=$city_id and dt='$dt' LIMIT 1";

  $r1 = mysql_query($q);
  $a1 = mysql_fetch_array($r1);
  $other_contacts = intval($a1['other_contacts']);
  $master_on_procent_procedures_count = intval($a1['master_on_procent_procedures_count']);

  
$html .="                    <td style='text-align: center;'>";
$html .="                      Погрешность <input type='text'  value='$other_contacts' disabled>";
$html .="                    </td>";
$html .="                    <td>";
$html .="                    </td>";
$html .="                  </tr>";
$html .="                  <tr>";
$html .="                    <td style='text-align: right;'>Прирост</td>";
    $new_chatsLF = [];
    for($i=1;$i<=7;$i++) {
        $i1 = $i - 1;
        $r1 = mysql_query("select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day");
        $a1 = mysql_fetch_array($r1);
        $lfchats = $a1['lidfit'];
        $new_chatsLF[$i] = $lfchats;
    }


  for($i=1;$i<=7;$i++){
  $xxres=$new_chats[$i]+$new_chatsLF[$i];
$html .="                    <td style='text-align: center;' id='contacts$city_id_$i' class='p_input'>$xxres</td>";
  
  }
  
$html .="                  </tr>";


    $html .="<tr>";
    $html .='<td style="text-align: right;">Контакты Direct</td>';
    $new_chatsLF = [];
    for($i=1;$i<=7;$i++) {
    $i1 = $i - 1;
    $r1 = mysql_query("select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day");
    $a1 = mysql_fetch_array($r1);
    $lfchats = $a1['lidfit'];
    $new_chatsLF[$i] = $lfchats;
    $current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
    $html .='<td style="text-align: center;"><input type="text" style="width: 50px;" id="chlf_'.$city_id.'_'.$i1.'" value="'.$lfchats.'" disabled></td>';
                          }
    $html .="</tr>";
    /*    ************************************************     */
    $html .="                  <tr>";
    $html .="                    <td style='text-align: right;''>Контакты в ВК.</td>";

    $q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $isOnPercent = intval($a1['count']) > 0;

    $new_chats = [];
    for($i=1;$i<=7;$i++){
        $i1 = $i-1;
        $i2 = $i-2;
        $q = "select chatsvk from m_city_day_vk where id_m_city=$city_id and dt='$dt'+interval $i1 day";
        $r1 = mysql_query($q);
        $a1 = mysql_fetch_array($r1);
        $chats = $a1['chatsvk'];

        $diff = $chats;
        $diff = ($diff < 0) ? 0 : $diff;
        $new_chats[$i] = $diff;
        $GLOBALS['stats']['cities'][$city_id]['City_ContactsVK'] += intval($new_chats[$i]);
        $current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
        if ($chats>$chats_max)$chats_max = $chats;

        $html .="                    <td style='text-align: center;'><input type='text' style='width: 50px;' value='$chats' class='p_input' disabled></td>";

    }

    $html .="                    <td>";
    $html .="                    </td>";
    $html .="                  </tr>";
    /* * ******************************************************/
$html .="                </table>";
$html .="              </div>";
return $html;
}

function f_analytics_show_master($master_id,$dt){
  $html = "";
  $id = $master_id;
  $master_query = "select m.id,u.name,m.by_percent,m.id_m_city from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $master_resource = mysql_query($master_query);
  $master_data = mysql_fetch_array($master_resource);

  $m_id = $master_data['id'];
  $city_id = $master_data['id_m_city'];
  $m_by_percent = intval($master_data['by_percent']); 
  if ($m_by_percent == 1){
    $GLOBALS['stats']['cities'][$city_id]['OnProcent'] = true;
  }

  $procedures_query = "SELECT sum(p.bonus*w.visitors) sum_bonus FROM procedures p left join master_procedure_week w on p.id=w.id_procedure and w.id_master=p.id_master where p.id_master=$m_id and w.dt='$dt'";
  $procedures_resources = mysql_query($procedures_query);
  $procedures_array = mysql_fetch_array($procedures_resources);

  $sum_bonus = intval($procedures_array['sum_bonus']);



  $master_week_query = "select * from master_week where id_master=$m_id and dt='$dt'";
  $master_week_resource = mysql_query($master_week_query);
  $master_week_array = mysql_fetch_array($master_week_resource);

  $paid = $master_week_array['paid'];
  $sent = $master_week_array['sent'];
  $sum_no_self = $master_week_array['sum_no_self'];


$html .="            <h3>". $master_data['name'];
                 
   
  $current_dt = $dt;
  $week_ago_dt = date("Y-m-d", strtotime($dt) - (60*60*24*7));
  $tuesday = date("Y-m-d", strtotime($dt) + (60*60*24));
  $_q = "select bill_checked from master_week where id_master=$m_id and dt='$week_ago_dt'";
  $_r = mysql_query($_q);
  $_a = mysql_fetch_array($_r);
  $bill_checked = $_a['bill_checked'];
  
$html .="            </h3>";
$html .="             <div style='border: 1px solid black; padding: 10px;'>";
$GLOBALS['stats']['cities'][$city_id]['City_Bonuses'] += $sum_bonus;

$html .="                 <table style='width: 100%;'>";
$html .="                     <tbody>";
$html .="                         <tr>";
$html .="                             <td style='padding-bottom:30px; border-right:1px solid black;' width='200'>";
$html .="                                 Процедуры";
$html .="                             </td>";
$html .="                             <td style='padding-bottom:30px; border-right:1px solid black;' class='header' align='center'>";
$html .="                                 Запись Instagram";
$html .="                             </td>";
    $html .="                             <td style='padding-bottom:30px; border-right:1px solid black;' class='header' align='center'>";
    $html .="                                 Запись ВК";
    $html .="                             </td>";
$html .="                             <td style='padding-bottom:30px;' class='header' align='center'>";
$html .="                                 Пришедшие";
$html .="                             </td>";
$html .="                             <td style='padding-bottom:30px;' class='header' align='center'>";
$html .="                                 Бонусы";
$html .="                             </td>";
$html .="                         </tr>";
  
    $records_week_sum = 0;
    $records_week_sumVK = 0;
    $visitors_week_sum = 0;
    $sum_bonus_week_sum = 0;
    $send_mail_flag = 1;
    $r = mysql_query("select * from procedures where id_master=$m_id order by sort desc");
    while ($a = mysql_fetch_array($r)){
      $p_id = $a['id'];
      $p_name = $a['name'];
      $p_bonus = $a['bonus'];
      $p_count_in_scores = intval($a['count_in_scores']);


      $q1 = "select records, recordsvk from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt='$dt'+interval 6 day";
      $r1 = mysql_query($q1);
      $a1 = mysql_fetch_array($r1);
      $records = $a1['records'];
        $recordsVK = $a1['recordsvk'];

      $q1 = "select sum(records) records_week from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt>='$dt' and dt<='$dt'+interval 6 day";
      $r1 = mysql_query($q1);
      $a1 = mysql_fetch_array($r1);
      $records_week = intval($a1['records_week']);

      $records_week_sum +=  $records_week;

        $q1 = "select sum(recordsvk) records_weekvk from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt>='$dt' and dt<='$dt'+interval 6 day";
        $r1 = mysql_query($q1);
        $a1 = mysql_fetch_array($r1);
        $records_weekVK = intval($a1['records_weekvk']);

        $records_week_sumVK +=  $records_weekVK;


        if ($p_count_in_scores == 1){
        $GLOBALS['stats']['cities'][$city_id]['City_Master_Records'] += $records_week;
      }else{
        $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] += $records_week;
      }

        if ($p_count_in_scores == 1){
            $GLOBALS['stats']['cities'][$city_id]['City_Master_RecordsVK'] += $records_weekVK;
        }else{
            $GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK'] += $records_weekVK;
        }


      $q1 = "select visitors from master_procedure_week where id_master=$m_id and id_procedure=$p_id and dt='$dt'";
      $r1 = mysql_query($q1);
      if (mysql_num_rows($r1)>0){
      $a1 = mysql_fetch_array($r1);
        $visitors = $a1['visitors'];
      }else{
        $visitors = '';
      }
      if($visitors=='') $send_mail_flag = 0;
      $sum_bonus = $p_bonus*intval($visitors);

      $visitors_week_sum += intval($visitors);
      $sum_bonus_week_sum += intval($sum_bonus);
  
$html .="                        <tr data-id=$p_id>";
$html .="                            <td style='padding-bottom:30px; border-right:1px solid black;' width='150'>";
$html .="                                <b>$p_name";
                                    
$html .="                                </b>";
$html .="                            </td>";
$html .="                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px; border-right:1px solid black;'>";
$html .="                                <b>$records_week<b> шт</b></b>";
$html .="                            </td>";
        $html .="                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px; border-right:1px solid black;'>";
        $html .="                                <b>$records_weekVK<b> шт</b></b>";
        $html .="                            </td>";
$html .="                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px;'>";
$html .="                                <input type='text' style='width: 30px; ' value='$visitors' class='p_input p_input$m_id'  disabled/>";
$html .="                                шт";
$html .="                            </td>";
$html .="                            <td style='padding-bottom:30px;padding-right:10px;' align='center'>";
$html .="                                <b>$sum_bonus";
                                      
$html .="                                </b>";
$html .="                            </td>";
$html .="                        </tr>";
   
    }
    $GLOBALS['stats']['cities'][$city_id]['City_Visitors'] += $visitors_week_sum;
  
$html .="                        <tr>";
$html .="                            <td style='border-top: 1px solid black; text-align: center;'>";
$html .="                              <b>Итого:</b>";
$html .="                            </td>";
$html .="                            <td style='border-top: 1px solid black; text-align: center;'>$records_week_sum";
                              
$html .="                            </td>";
    $html .="                            <td style='border-top: 1px solid black; text-align: center;'>$records_week_sumVK";

    $html .="                            </td>";
$html .="                            <td style='border-top: 1px solid black; text-align: center;'>$visitors_week_sum";
                              
$html .="                            </td>";
$html .="                            <td style='border-top: 1px solid black; text-align: center;'>$sum_bonus_week_sum";
                              
$html .="                            </td>";
$html .="                        </tr>";
$html .="                    </tbody>";
$html .="                </table>";

$html .="                <p>";
$html .="                  <span style='margin-right: 20px;'>Сумма доп. процедур</span>";
$html .="                  <span><input type='text' name='extra_procedure_summ' value='$extra_procedure_summ' disabled/></span>";
$html .="                </p>";
   
  if($m_by_percent==1){
  	$q = "select * from m_city_week where m_city_id=$city_id and dt='$dt' LIMIT 1";
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $master_on_procent_procedures_count = intval($a1['master_on_procent_procedures_count']);

    //$GLOBALS['stats']['cities'][$city_id]['City_Master_Records'] = $master_on_procent_procedures_count; 
	  
$html .="              <div>";
$html .="                <div style='padding-bottom:20px;'>";
$html .="                  <b>Сумма за неделю (без себестоимости)</b>";
$html .="                  <input type='text' style='width:100px;' class='p_input' value='$sum_no_self'  disabled/>";
$html .="                </div>";
$html .="              </div>";
  
  }
  

$html .="              <div>";
$html .="              </div>";
$html .="";
$html .="              <input type='hidden' id='dt' value='$dt' class='p_input'>";
$html .="              <input type='hidden' id='id_master' value='$m_id' class='p_input'>";
$html .="            </div>";
return $html;
}
function f_show_city_masters($city_id, $dt){
  $html = "";
  $html .= "<div style='float:left;margin-left:20px;'>";
  $query = "select m.id,m.id_master, u.name from masters m, users u  where m.id_m_city = $city_id and m.id_master = u.id and m.shown=1";
  $_r = mysql_query($query);
  while ($master = mysql_fetch_array($_r)){
    $m_id = $master['id'];
    $master_name = $master['name'];
          $html .= "<div id='master$m_id'><strong>$master_name</strong></div>";
       $html .= "    <table frame='none' rules='void' data-master-id='$m_id' style='margin-bottom:30px;'>";

        $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс','Итого');

        $html .= "    <tr>";
        $html .= "    <td style='padding-bottom:15px;'>Явка</td>";
        $html .= "    <td width=200 style='padding-bottom:15px;'></td>";

        for ($i = 1;$i<count($arr)-1;$i++){

        $html .= "    <td align='center' style='width:40px;padding-bottom:15px;' class='header'>$arr[$i]</td>";

        }

       $html .= "     <td style='width:80px;padding-bottom:15px;' align=center class='header'>Итого</td>";
       $html .= "     </tr>";

        $r = mysql_query("select id,name,bonus from procedures where id_master=$m_id");
        while ($a = mysql_fetch_array($r)){
          $p_id = $a['id'];
          $p_name = $a['name'];
          $p_bonus = $a['bonus'];

          $r1 = mysql_query("select visitors from master_procedure_week where id_master=$m_id and id_procedure=$p_id and dt='$dt'");
          if (mysql_num_rows($r1)>0){
            $a1 = mysql_fetch_array($r1);
            $p_visitors = $a1['visitors'];
          }else $p_visitors = '';

        $html .= "    <tr>";
        $html .= "    <td align=center style='padding-bottom:30px;background-color:#B3B8C6;'>$p_visitors</td>";
        $html .= "    <td style='padding-bottom:30px;'>$p_name</td>";

          $records_sum = 0;
          for($i=1;$i<=7;$i++){
            $i1 = $i-1;
            $q = "select records from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt='$dt'+interval $i1 day";
            $r1 = mysql_query($q);
            $a1 = mysql_fetch_array($r1);
            $records = intval($a1['records']);
      //      if ($records==0)$records1 = '';else $records1 = $records;
            $records_sum += $records;
        
            $html .= "     <td  style='padding-bottom:30px;' align='center'>$records</td>";

          }

        $html .= "     <td style='padding-bottom:30px;' align='center'>$records_sum шт</td>";
        $html .= "    </tr>";

        }

        $html .= "    </table>";

  }
  $html .= "  </div>";
  return $html;
}


function f_show_masters($dt,$dt1=0){
  $payzp=new payzp($dt, $_REQUEST['razdel'], $_SERVER['PHP_SELF']);
  $usersCRM=new usersCRM();
  $sellers=$usersCRM->getUsersbyType(6);
  $operators=$usersCRM->getUsersbyType(7);
  $html = "";
  if($_REQUEST['razdel']==8) {

      $html .= "<div id='master119' class='T_M_N_B_border' >";
      // Показывает бонусы отдела продаж
      $html .=$payzp->showManagers();
      $html .= "</div>";

      $html .="<div id='vkrab' class='T_M_N_B_border'>";
      // Выводит работу ВК
      $html .=$payzp->showVKRabota();
      $html .= "</div>";

      $html .="<div class='T_M_N_B_border'>";
      foreach ($sellers as $sel)
      {
          $html .= "<div id='prodavec" . $sel['id'] . "'>";
          // Выводит продовцов Ежа
          $html.=$payzp->showEzhSeller($sel['id'], $sel['name']);
          $html .= "</div>";
      }
      $html .="</div>";

      $html .="<div class='T_M_N_B_border'>";
      foreach ($operators as $oper) {
              $html .= "<div id='operator" . $oper['id'] . "'>";
              $html .= $payzp->showOperator($oper['id']);
              $html .= "</div>";
      }
      $monitor=new monitor($dt, 1);
      $monitor->set_dt($monitor->get_sundayPars($dt));
      $html .= "<div class='T_M_N_B_MON'>".$monitor->showBonusesOper()."</div>";
      $html .="</div>";
  }

  if ($_REQUEST['razdel']==3){
    $r = mysql_query("select u.* from users u,masters m where u.id=m.id_master and u.type=0 and u.active=1 order by m.sort, m.sortsecond");
  }elseif ($_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){
    $r = mysql_query("select * from users where active=1 and type in(1) order by type desc,id");
  }elseif ($_REQUEST['razdel']!=5){
    $r = mysql_query("select u.* from users u,masters m where u.active=1 and u.id=m.id_master and u.type=0 and m.shown=1 order by m.sort, m.sortsecond");
  }else{
    $r = mysql_query("select * from users where active=1 and type in(1,6) order by type desc,id");
  }

  if ($_REQUEST['razdel']==4){
    $html .="<div style='margin: 50px 0;'><canvas id='chart' height='400' width='1000' ></canvas></div>";
  }elseif($_REQUEST['razdel']==9){
  }
  while ($a = mysql_fetch_array($r)){
    $u_id = $a['id'];
    if ($_REQUEST['razdel']==1){

$html .= "<div id='master$u_id' style='padding-bottom:30px;'>";
$html .= f_show_master_stat($u_id,$dt);
$html .= "</div>";

    }elseif($_REQUEST['razdel']==2){

$html .= "<div id='master$u_id' style='padding-bottom:30px;'>";
$html .= f_show_master_analitics($u_id,$dt);
$html .= "</div>";

    }elseif($_REQUEST['razdel']==4){
$html .= "<div id='master$u_id' style='padding-bottom:30px;'>";
$html .= f_show_master_graph($u_id,$dt,$dt1);
$html .= "</div>";

    }elseif($_REQUEST['razdel']==9){
      $html .= "<div id='manager$u_id' style='padding-bottom:30px;width:1100px;'>";
      $html .= "<div style='margin-bottom:10px;'><strong>".$a['name']."</strong></div>";
      $html .= show_weekplan($dt, $u_id);
      $html .= "</div>";

      }
  }

    if ($_REQUEST['razdel'] == 9) {
        $users=$usersCRM->getUsersbyType(7);
        $compRebuild=new CompRebuild(2);
        $html .=$compRebuild->initStyles();
        foreach($users as $az) {
            $html .= "<div id='operator".$az['id']."' style='padding-bottom:30px;width:1100px;'>";
            $html .= "<div style='margin-bottom:10px;'><strong>" . $az['name'] . " (оператор)</strong></div>";
            $manager_id = $az['id'];
            $bonushouseoper=new bonushouseoper($dt, $manager_id);
            $bho=$bonushouseoper->getOperatorBonustoExcel();
            if($bho['summa']>0 && $bho['basproc']>0) {$sumvb=round($bho['summa']/$bho['basproc']);} else {$sumvb=0;}
            $perMasterTmpl = "";
            $bonushouseoper->set_dt(date("Y-m-d", strtotime($dt."-1 week")));
            $bho2=$bonushouseoper->getOperatorBonustoExcel();
            if($bho2['summa']>0 && $bho2['basproc']>0) {$sumvb2=round($bho2['summa']/$bho2['basproc']);} else {$sumvb2=0;}
            if ($sumvb<750) $currentPosition = $sumvb."px"; else $currentPosition = "750px";
            if ($sumvb2<750) {$currentPosition2 = $sumvb2-45; } else {$currentPosition2 = "701";}
            /**Шаблон слайдера**/
            $sliderTmpl = "<table class='table slidex no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: $currentPosition;'></div></td>";
            $sliderTmpl .= " <td width='".$sumvb2."px' height='19px'><span class='value'></span></td><td width='".(800-$sumvb2)."px' height='19px'></td>";
            $sliderTmpl .= "</tr></table>";
            /**Вывод данных в шаблон**/
            $totalBonusTmpl=$bho['znachbezporoga']+$bho['porog'];
            $currentPosition3=$currentPosition2+42;
            $tmpl=$compRebuild->DrawSliderOperator($currentPosition, $sumvb, $sliderTmpl, $totalBonusTmpl, $currentPosition2, $sumvb2, $currentPosition3, $perMasterTmpl);
            $html .= $tmpl;

            $html .= "</div>";
        }

        $rm = mysql_query("select * from users where type=6 order by type desc,id");
        while ($az = mysql_fetch_array($rm)) {
            $html .= "<div id='seller".$az['id']."' style='padding-bottom:30px; width:1100px;'>";
            $html .= "<div style='margin-bottom:10px;'><strong>" . $az['name'] . " (продавец Ежа)</strong></div>";
            $manager_id = $az['id'];
            $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
            $tmpl_urlx = "$DOCUMENT_ROOT/components/manager/weekplan.html";  //  components/manager/weekplan.html   //
            $tmplx = file_get_contents($tmpl_urlx);
            $total_comission = 0;
            $percent_comission = 0;
            $base_percent = 0;
            $week_bonus = 0;
            $total = 0;

            $qqw = "select br.base_percent from bonusezh br, bonusoperatorezh bo where bo.idbonus = br.id and bo.iduser=".$manager_id;
            $rqw = mysql_query($qqw);
            $aqw = mysql_fetch_array($rqw);

            $base_percent = $aqw['base_percent'];

            $dn=strtotime($dt);
            $dk=strtotime($dt);
            $s2 = array();
            /* Определяем понедельник путем вычисления */
            switch (date("D", strtotime($dt)))
            {
                case "Mon" : $dn=$dn-0; break;
                case "Tue" : $dn=$dn-86400; break;
                case "Wed" : $dn=$dn-86400*2; break;
                case "Thu" : $dn=$dn-86400*3; break;
                case "Fri" : $dn=$dn-86400*4; break;
                case "Sat" : $dn=$dn-86400*5; break;
                case "Sun" : $dn=$dn-86400*6; break;
            }
            /* Определяем воскресение путем вычисления */
            switch (date("D", strtotime($dt)))
            {
                case "Mon" : $dk=$dk+86400*6; break;
                case "Tue" : $dk=$dk+86400*5; break;
                case "Wed" : $dk=$dk+86400*4; break;
                case "Thu" : $dk=$dk+86400*3; break;
                case "Fri" : $dk=$dk+86400*2; break;
                case "Sat" : $dk=$dk+86400; break;
                case "Sun" : $dk=$dk+0; break;
            }

            $dateNachala=date("Y-m-d",$dn);
            $dateKonca=date("Y-m-d",$dk);

            $itogo=0;
            $rtr = mysql_query("SELECT p.name, p.price, p.self_price, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '".$dateNachala."' and '".$dateKonca."' and p.pokaz>0 and done=1 group by p.name ORDER BY ttt DESC");
            while ($atr = mysql_fetch_array($rtr))
            {
                $pric=(int)$atr['price'];
                $sprod=(float)$atr['self_price'];
                $ttar=(float)$atr['ttt'];
                $itog=($pric-$sprod)*$ttar;
                $itogo=$itogo+$itog;
            //    echo $pric."    ".$sprod."   ".$ttar."  ".$itogo."<br>";
                $itog=0;
            }
            $total_comission=$itogo;


            $sliderTmpl = "<table class='table slider no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: currentPosition;'></div></td>";
            $q = "select * from bonus_rewardsezh br, bonusoperatorezh bo where br.bonus_id = bo.idbonus and bo.iduser=$manager_id";
            $r = mysql_query($q);
            $rewards = [];
            while ($a = mysql_fetch_array($r)) {
                $rewards[] = [
                    "summ" => intval($a["summ"]),
                    "reward" => intval($a["reward"])
                ];
            }

            $week_bonus = getWeekBonus($total_comission, $rewards);
            if ($total_comission>0 )$percent_comission=$total_comission*$base_percent/100;
            //$currentPosition = getCurrentPosition($total_comission, $rewards);

            $numberOfSegments = count($rewards)+1;
            $maxSumm = $rewards[count($rewards)-1]["summ"];
            $currentPosition = 0;
            $lastReward = 0;
            $fff=count($rewards);
            if ($fff<=0) { $fff=1; }
            $pixelsInSegment =  ceil(620 / $fff);
            for ($i=0; $i < count($rewards); $i++) {
                $pixels = ($i == 0) ? 120 : $pixelsInSegment;

                if ($total_comission >= $rewards[$i]["summ"]){
                    $currentPosition += $pixels;
                    if ($i == (count($rewards)-1)) $currentPosition += ceil($pixelsInSegment/2);
                }else{
                    $currentPosition += ceil( ($pixels / ($rewards[$i]["summ"] -  $lastReward)) * ($total_comission - $lastReward));
                    break;
                }
                $lastReward = $rewards[$i]["summ"];
            }
            $currentPosition = $currentPosition . "px";

            $total = $week_bonus + $percent_comission;
            $fff=count($rewards);
            if ($fff<=0) $fff=1;
            $pixelPerSegment = ceil(600 / $fff) - 17;
            $prev = 0;
            foreach ($rewards as $reward) {
                $summ = $reward["summ"];
                $weight = ($total_comission > $prev && $total_comission < $summ) ? "bold" : "normal";
                $sliderTmpl .= " <td width='$pixelPerSegment'><span class='value' style='font-weight:$weight;'>$summ</span></td>";
                $prev = $summ;
            }
            $sliderTmpl .= "</tr><tr><td></td>";
            $prev = 0;
            foreach ($rewards as $reward) {
                $reward_summ = $reward["reward"];
                $summ = $reward["summ"];
                $weight = ($total_comission > $prev && $total_comission < $summ) ? "bold" : "normal";
                $sliderTmpl .= "<td style='font-weight:$weight;'>$reward_summ</td>";
                $prev = $summ;
            }

            $sliderTmpl .= "</tr></table>";
            $perMasterTmpl='';
            $tmplx = str_replace("5", $base_percent, $tmplx);
            $tmplx = str_replace("<button ", "<button style='display:none;' ", $tmplx);
            $tmplx = str_replace("height: 22px;", "height: 50px;", $tmplx);
            $tmplx = str_replace("sliderTmpl", $sliderTmpl, $tmplx);
            $tmplx = str_replace("perMasterTmpl", $perMasterTmpl, $tmplx);
            $tmplx = str_replace("percentComissionTmpl", $percent_comission, $tmplx);
            $tmplx = str_replace("weekBonusTmpl", $week_bonus, $tmplx);
            $tmplx = str_replace("currentPosition", $currentPosition, $tmplx);
            $tmplx = str_replace("currentPosition", $currentPosition, $tmplx);
            $tmplx = str_replace("totalBonusTmpl", $total, $tmplx);
            $tmplx = str_replace("totalComissionTmpl", $total_comission, $tmplx);


            $html .= $tmplx;

            $html .= "</div>";
        }
    }
  return $html;
}


function f_show_user_info($id_master=-1){
  $r = mysql_query("select * from procedures where id_master=$id_master and active>0 order by sort desc");
  $html = "";
$html .= "<div class='proc'></div>";

  $i = 1;
  while($a = mysql_fetch_array($r)){
    $p_id = $a['id'];
    $p_name = $a['name'];
    $p_price = $a['price'];
    $p_comission = $a['comission'];
    $p_bonus = $a['bonus'];
    $p_topmanager_bonus = $a['topmanager_bonus'];
    $p_scores = $a['count_in_scores'];
    $p_archiv = $a['active'];
    $p_balls = $a['bals'];
    $sorter=$a['sort'];

$html .= "<div class='proc' id='". rand(). "'>";
$html .= "<input type='hidden' class='p_id' value='$p_id'>";
$html .= "<div style='display:inline-block;padding:0 10px 20px;'>Процедура <input type='text' class='p_name' style='width: 250px;' value='" . htmlspecialchars($p_name). "'></div>";
$html .= "<div style='display:inline-block;padding-right:10px'>Цена <input type='text' style='width:50px;' class='p_price' value='" . htmlspecialchars($p_price). "'></div>";
$html .= "<div style='display:inline-block;padding-right:10px'>Комиссия <input type='text' style='width:50px;' class='p_comission' value='" . htmlspecialchars($p_comission). "'></div>";
$html .= "<div style='display:none;padding-right:10px'>Бонусы <input type='text' style='width:50px;' class='p_bonus' value='0'></div>";
$html .= "<div style='display:none;padding-right:10px'>Баллы <select style='width: 50px;' class='p_balls' name='balls'><option disabled>Назначение балов</option><option ".(($p_balls==0) ? " selected " : "")." value='0' >0</option><option ".(($p_balls==1) ? " selected " : "")." value='1' >1</option><option ".(($p_balls==2) ? " selected " : "")." value='2' >2</option><option ".(($p_balls==3) ? " selected " : "")." value='3' >3</option><option ".(($p_balls==4) ? " selected " : "")." value='4' >4</option><option ".(($p_balls==5) ? " selected " : "")." value='5' >5</option></select></div>";
$html .= "<div style='display:none;padding-right:10px'>Ст.мен.<input type='text' style='width:50px;' class='p_topmanager_bonus' value='" . htmlspecialchars($p_topmanager_bonus). "'></div>";
$html .= "<div style='display:inline-block;'><label class='p_label' for='scores$p_id'>Считать в конверсии</label><input id='scores$p_id' type='checkbox' class='p_scores' value=1 " . (($p_scores==1) ? " checked" : "") . "></div>";
$html .= "<div style='display:inline-block;'><label class='p_label1'  ".(($p_archiv==0) ? " style='box-shadow: 1px 1px 7px 1px red; padding: 3px; border-radius: 10px; margin-left: 14px;'" : " style='margin-left: 14px;'")."   for='archiv$p_id'>   В архиве</label><input id='archiv$p_id' type='checkbox' class='p_archiv' value=1 " . (($p_archiv==0) ? " checked " : "") . "></div>";
$html .= "<div style='display:inline-block;padding-right:10px'>Порядок <input type='text' style='width:40px; text-align: center;' class='p_sortproc' value='" .$sorter. "'></div>";
$html .= "</div>";
    $i++;
  }

$html .= "<div class='add_link' style='padding-bottom:10px;'>";
$html .= "<a href='' onclick='add_proc($(this).parent().parent().parent().get(0).id);return false;'>Добавить процедуру</a>";
$html .= "</div>";

    $html .= "<div onclick='disproc(disprocflag, $id_master);' style='cursor: default; '><h5 style='margin: 0px'>Архив  <span id='sp$id_master'>▼</span></h5>";

    $html .= "</div>";
    $html .= "<div id='archivproc$id_master' style='display: none; padding: 2px; '>";
    $r = mysql_query("select * from procedures where id_master=$id_master and active=0");
    $i = 1;
    while($a = mysql_fetch_array($r)){
        $p_id = $a['id'];
        $p_name = $a['name'];
        $p_price = $a['price'];
        $p_comission = $a['comission'];
        $p_bonus = $a['bonus'];
        $p_topmanager_bonus = $a['topmanager_bonus'];
        $p_scores = $a['count_in_scores'];
        $p_archiv = $a['active'];
        $p_balls = $a['bals'];
        $html .= "<div class='procbu' id='". rand(). "'>";
        $html .= "<input type='hidden' class='p_idbu' value='$p_id'>";
        $html .= "<div style='display:inline-block;padding:0 10px 20px;'>Процедура <input type='text' id='namep$p_id' value='" . htmlspecialchars($p_name). "'></div>";
        $html .= "<div style='display:inline-block;padding-right:10px'>Цена <input type='text' style='width:50px;' id='pricep$p_id' value='" . htmlspecialchars($p_price). "'></div>";
        $html .= "<div style='display:inline-block;padding-right:10px'>Комиссия <input type='text' style='width:50px;' id='comissionp$p_id' value='" . htmlspecialchars($p_comission). "'></div>";
        $html .= "<div style='display:none;padding-right:10px'>Баллы <select style='width: 50px;' class='p_ballsp' id='ballp$p_id'><option disabled>Назначение балов</option><option ".(($p_balls==0) ? " selected " : "")." value='0' >0</option><option ".(($p_balls==1) ? " selected " : "")." value='1' >1</option><option ".(($p_balls==2) ? " selected " : "")." value='2' >2</option><option ".(($p_balls==3) ? " selected " : "")." value='3' >3</option><option ".(($p_balls==4) ? " selected " : "")." value='4' >4</option><option ".(($p_balls==5) ? " selected " : "")." value='5' >5</option></select></div>";
        $html .= "<div style='display:inline-block;'><label class='p_label' for='scores$p_id'>Считать в конверсии</label><input id='scoresp$p_id' type='checkbox' class='p_scoresbu' value=$p_scores " . (($p_scores==1) ? " checked" : "") . "></div>";
        $html .= "<div style='display:inline-block;padding-right:10px'><input type='button' style='width:150px;' onclick='return_proc($p_id);' class='p_pricebu' value='Вернуть с архива'></div>";
        $html .= "</div>";
    }
$html .= "</div>";
$html .= "<br><div class='u_list'>" . f_show_user_lists($id_master) . "</div>";

  return $html;
}

function f_show_user_lists($id_master=-1){
  $html = "";
$html .= "Менеджер: <select class='id_manager'>";
$html .= "<option value='-1'></option>";

  $r = mysql_query("select * from masters where id=$id_master");
  if (mysql_num_rows($r)==0){
    $id_manager = -1;
    $id_marketolog = -1;
    $id_topmanager = -1;
    $id_uchenik= -1;
  }else{
    $a = mysql_fetch_array($r);
    $id_manager = $a['id_manager'];
    $id_marketolog = $a['id_marketolog'];
    $id_topmanager = $a['id_topmanager'];
    $id_uchenik = $a['id_uchenik'];
  }
  $r = mysql_query("select id,name from users where type=1 and active=1");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];

$html .= "<option value='$id'" . (($id==$id_manager) ? " selected" : ""). ">$name</option>";

  }

$html .= "</select>";
$html .= " Маркетолог: <select class='id_marketolog'>";
$html .= "<option value='-1'></option>";

  $r = mysql_query("select id,name from users where type=2");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];

$html .= "<option value='$id'" . (($id==$id_marketolog) ? " selected" : "") . ">$name</option>";

  }

$html .= "</select>";
$html .= "<span style='display: none;'>Старший менеджер: <select class='id_topmanager'>";
$html .= "<option value='0'" . (($id_topmanager==0) ? " selected" : ""). ">Не назначен</option>";

  $r = mysql_query("select id,name from users where type=4");
  while($a = mysql_fetch_array($r)){
    $id = $a['id'];
    $name = $a['name'];

$html .= "<option value='$id'" . (($id==$id_topmanager) ? " selected" : "") .">$name</option>";

  }

$html .= "</select></span>";
    $html .= " Оператор: <select class='id_uchenik'>";
    $html .= "<option value='0'" . (($id_uchenik==0) ? " selected" : ""). ">Не назначен</option>";

    $r = mysql_query("select id,name from users where type=7");
    while($a = mysql_fetch_array($r)){
        $id = $a['id'];
        $name = $a['name'];

        $html .= "<option value='$id'" . (($id==$id_uchenik) ? " selected" : "") .">$name</option>";

    }

    $html .= "</select>";
$html .= "<input style='position:absolute;left:730px;bottom:0px;' type='button' class='orange' value='Сохранить' onclick='save_user($(this).closest(\".masters_inner\").find(\".u_id\").get(0).value,$(this).closest(\".masters_inner\").find(\".u_name\").get(0).value,$(this).closest(\".masters_inner\").find(\".u_pass\").get(0).value,0,$(this).closest(\".masters_inner\").get(0).id);'>";

  return $html;
}

function f_show_costs($dt_start, $dt_end){

  $dt = date('Y-m-d', strtotime($dt_start));
  $prev_week = date('Y-m-d', strtotime($dt) - (60*60*24*7));
  $is_current_week = false;
  $next_week_start = date("Y-m-d", strtotime("+1 week"));
  if ( ((time() > strtotime($dt_start))) && (time() < strtotime($next_week_start))){
    $is_current_week = true;
  }
  $html = "";
  $html .= "<input type='hidden' id='current_dt' value='$dt' />";
  $html .= "<h5>Расходы недели</h5>";
  $html .= "<div id='week_costs' class='options_block' style='border:1px solid;'>";
  $html .= "  <div></div>";
  $html .= "    <div class='cost-list'>";

          $query = "select * from costs where type = 1 and dt = '$dt' and isDeleted = 0";
          $r = mysql_query($query);
          while($a = mysql_fetch_array($r)){
  $html .= "      <div class='cost' data-id='" . $a['id']. "' data-type='". $a['type']. "' data-dt='". $a['dt']. "'>";
  $html .= "        <input class='cost-name' input style='margin: 10px;' type='text' value='". $a['name']. "' placeholder='Название' />";
  $html .= "        <input class='cost-summ' <input style='margin: 10px;' type='text' value='". $a['summ']. "' placeholder='Сумма' />";
  $html .= "        <a href='#' onclick='delete_cost(" . $a['id'] . ")'>Удалить</a>";
  $html .= "      </div>";

          }
   $html .= "    </div>";
   $html .= "    <div class='new-cost'></div>";
   $html .= "  <div>";
   $html .= "    <div class='add_link' style='display: inline-block;'>";
   $html .= "    <a href='' onclick='add_cost($(this));return false;'>Добавить</a>";
   $html .= "    </div>";
   $html .= "    <div style='display:inline-block;padding-bottom:10px; float:right'><input type='button' class='orange' value='Сохранить' onclick='save_costs($(this), 1);'></div>";
   $html .= "   </div>";
   $html .= " </div>";
  
  $html .= " <h5>Зарплаты в месяц</h5>";
  $html .= " <div id='salary_costs' class='options_block' style='border:1px solid;'>";
  $html .= "   <div></div>";
  $html .= "     <div class='cost-list'>";
          
          $query = "select * from costs where type = 2 and dt ='$dt' and isDeleted = 0";
          $r = mysql_query($query);
          while($a = mysql_fetch_array($r)){

  $html .= "      <div  class='cost' data-id='" . $a['id']. "' data-type='" . $a['type']. "' data-dt='" . $a['dt']. "'>";
  $html .= "        <input class='cost-name' style='margin: 10px;' type='text' value='" . $a['name']. "' placeholder='Название' />";
  $html .= "        <input class='cost-summ' style='margin: 10px;' type='text' value='" . $a['summ']. "' placeholder='Сумма' />";
  $html .= "        <a href='#' onclick='delete_cost(" . $a['id'] . ")'>Удалить</a>";
  $html .= "      </div>";
          }
  $html .= "    </div>";
  $html .= "    <div class='new-cost'></div>";
  $html .= "  <div>";
  $html .= "    <div class='add_link' style='display: inline-block;'>";
  $html .= "    <a href='' onclick='add_cost($(this), 2);return false;'>Добавить</a>";
  $html .= "    </div>";
  $html .= "    <div style='display:inline-block;padding-bottom:10px; float:right'><input type='button' class='orange' value='Сохранить' onclick='save_costs($(this));'></div>";
  $html .= "    </div>";
  $html .= "  </div>";
  
  $html .= "<h5>Регулярные расходы в месяц</h5>";
  $html .= "<div id='regular_costs' class='options_block' style='border:1px solid;'>";
  $html .= "  <div></div>";
  $html .= "    <div class='cost-list'>";
        
          
          $query = "select * from costs where type = 3 and dt ='$dt' and isDeleted = 0";
          $r = mysql_query($query);
          while($a = mysql_fetch_array($r)){

  $html .= "      <div class='cost' data-id='" . $a['id']. "' data-type='" . $a['type']. "' data-dt='" . $a['dt']. "'>";
  $html .= "        <input class='cost-name' style='margin: 10px;' type='text' value='" . $a['name']. "' placeholder='Название' />";
          
  $html .= "        <input class='cost-summ' style='margin: 10px;' type='text' value='" . $a['summ']. "' placeholder='Сумма' />";
  $html .= "        <a href='#' onclick='delete_cost(" . $a['id'] . ")'>Удалить</a>";
  $html .= "      </div>";
          }
   $html .= "    </div>";
   $html .= "    <div class='new-cost'></div>";
   $html .= "  <div>";
   $html .= "    <div class='add_link' style='display: inline-block;'>";
   $html .= "    <a href='' onclick='add_cost($(this), 3);return false;'>Добавить</a>";
   $html .= "    </div>";
   $html .= "  <div style='display:inline-block;padding-bottom:10px; float:right'><input type='button' class='orange' value='Сохранить' onclick='save_costs($(this));'></div>";
  $html .= "    </div>";
  $html .= "  </div>";
  $html .= "</div>";
  return $html;
}

function f_show_masters_profit($dt_start, $dt_end)
{
  $html = "";
  $dt = $dt_start;
  $q = "select m.id,u.name,m.by_percent,m.percent_val from users u join masters m on u.id=m.id_master and u.type=0 AND m.shown=1";
  $r = mysql_query($q);
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  require $DOCUMENT_ROOT. '/components/master/functions.php';
  while($a = mysql_fetch_array($r)){
    $m_id = $a['id'];
    $name = $a['name'];
    $m_by_percent = $a['by_percent'];
    $m_percent_val = $a['percent_val'];

    $html .= "<div style='padding-left:10px;'><b>$name</b></div>";
     $html .= "<div class='options_block' style='width:900px; margin-bottom:10px;'>"; 
     $html .= "<div id='finance_block'>
                  Чистый доход (уже с вычетом комиссий): <br />
                  <strong>Текущий месяц: " . GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val, "current_month"). "</strong> <br />
                  За последние 12 недель: ".GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val,"last_12_weeks")." <br />
                  За все время: ".GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val,"all")." <br />
              </div>";
      $html .= "</div>";
     $html .= "</div>";
  }
  return $html;
}

function getOperatorBonus($manager_id, $dt){
    /**Запрос данных с таблицы**/
    $rvb = mysql_query("select * from bonushouseoper where iduser=$manager_id and daten='$dt'");
    $avb = mysql_fetch_array($rvb);
    $sumvb=$avb['znachbezporoga'];
    $porog = $avb['porog'];
    return $sumvb + $porog;
}
