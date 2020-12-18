<?php
if ($operation=='show_master'){
    $id = intval($_POST['id']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    $masters = show_masters($id,$dt);
    $html = $masters;
    print $html;
    exit;
  }
  if ($operation=='save_master'){
      $stats=new stats($_POST);
      $stats->iduser=$id;
      $stats->saveSelfZapVis();
      print show_master($stats->getMID(),$stats->dt);
      exit;
  }
  if ($operation=='save_contacts'){
    $city_id = $_POST['city_id'];
    $dt = $_POST['dt'];
    $other_contacts = intval($_POST['other_contacts']);
    $master_on_procent_procedures_count = null;
    if (isset($_POST['master_on_procent_procedures_count'])){
      $master_on_procent_procedures_count = intval($_POST['master_on_procent_procedures_count']);
    }
    
    $contacts = $_POST['contacts'];
   // $contactsvk = $_POST['contactsvk'];
    $select_query = "SELECT * FROM `m_city_week` WHERE m_city_id = $city_id AND dt='$dt'";
    $select_r = mysql_query($select_query);
    if (mysql_num_rows($select_r)==0){
      $insert_query = "INSERT INTO `m_city_week` (`m_city_id`, `dt`, `other_contacts`, `master_on_procent_procedures_count`) VALUES ($city_id,'$dt',$other_contacts,$master_on_procent_procedures_count);";
      mysql_query($insert_query);
    }else{
      $update_query = "UPDATE `m_city_week` SET `other_contacts` = $other_contacts, `master_on_procent_procedures_count` = $master_on_procent_procedures_count WHERE m_city_id = $city_id AND dt='$dt'; ";
      mysql_query($update_query);
    }
  
    foreach ($contacts as $date => $value) {
      $select_query = "SELECT * FROM `m_city_day` WHERE id_m_city = $city_id AND dt='$date'";
      $select_r = mysql_query($select_query);
      $value = $value;
  
        if ($value >= 0){
          if (mysql_num_rows($select_r)==0){
              $insert_query = "INSERT INTO `m_city_day` (`id_m_city`, `dt`, `chats`, `chat_old`, `lidfit`) VALUES ($city_id,'$date',$value, 0, 0);";
              mysql_query($insert_query);
          }else{
              $a = mysql_fetch_array($select_r);
              $id = $a['id'];
              $update_query = "UPDATE `m_city_day` SET `chats` = $value WHERE id = $id;";
              mysql_query($update_query);
          }
        } else{
            $a = mysql_fetch_array($select_r);
            $id = $a['id'];
            $update_query = "UPDATE `m_city_day` SET `chats` = $value WHERE id = $id;";
            mysql_query($update_query);
        }
    }
      $contacts = $_POST['contactsLEDFIT'];
      foreach ($contacts as $date => $value) {
          $select_query = "SELECT * FROM `m_city_day` WHERE id_m_city = $city_id AND dt='$date'";
          $select_r = mysql_query($select_query);
          $value = $value;

          if ($value >= 0){
              if (mysql_num_rows($select_r)==0){
                  $insert_query = "INSERT INTO `m_city_day` (`id_m_city`, `dt`, `chats`, `chat_old`, `lidfit`) VALUES ($city_id,'$date',0, 0, $value);";
                  mysql_query($insert_query);
              }else{
                  $a = mysql_fetch_array($select_r);
                  $id = $a['id'];
                  $update_query = "UPDATE `m_city_day` SET `lidfit` = $value WHERE id = $id;";
                  mysql_query($update_query);
              }
          } else{
              $a = mysql_fetch_array($select_r);
              $id = $a['id'];
              $update_query = "UPDATE `m_city_day` SET `lidfit` = $value WHERE id = $id;";
              mysql_query($update_query);
          }
      }
      exit;
  }
if ($operation=='save_contactsLF'){
    $city_id = $_POST['city_id'];
    $dt = $_POST['dt'];
    $contacts = $_POST['contactsLEDFIT'];
    foreach ($contacts as $date => $value) {
        $select_query = "SELECT * FROM `m_city_day` WHERE id_m_city = $city_id AND dt='$date'";
        $select_r = mysql_query($select_query);
        $value = $value;

        if ($value >= 0){
            if (mysql_num_rows($select_r)==0){
                $insert_query = "INSERT INTO `m_city_day` (`id_m_city`, `dt`, `chats`, `chat_old`, `lidfit`) VALUES ($city_id,'$date',0, 0, $value);";
                mysql_query($insert_query);
            }else{
                $a = mysql_fetch_array($select_r);
                $id = $a['id'];
                $update_query = "UPDATE `m_city_day` SET `lidfit` = $value WHERE id = $id;";
                mysql_query($update_query);
            }
        } else{
            $a = mysql_fetch_array($select_r);
            $id = $a['id'];
            $update_query = "UPDATE `m_city_day` SET `lidfit` = $value WHERE id = $id;";
            mysql_query($update_query);
        }
    }
    exit;
}
  if ($operation=='send_mail'){
    $id = intval($_POST['id']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $r = mysql_query("select * from master_week where id_master=$id and dt='$dt'");
    if (mysql_num_rows($r)==0){
  //print "insert into master_week(id_master,dt,sent)values($id,'$dt',1)";
      mysql_query("insert into master_week(id_master,dt,sent)values($id,'$dt',1)");
    }else{
      mysql_query("update master_week set sent=1 where id_master=$id and dt='$dt'");
    }
    $r = mysql_query("select email from masters where id=$id");
    if(mysql_num_rows($r)>0){
      $a = mysql_fetch_array($r);
      $email = $a['email'];
      if ($email!=''){
        $m = array();
        preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt,$m);
        $t1 = mktime(0,0,0,$m[2],$m[3],$m[1])+3600*24*6;
        $dt1 = date("d.m.Y",$t1);
        $t2 = $t1-3600*24*6;
        $dt2 = date("d.m.Y",$t2);
        $subject = "Отчет $dt2-$dt1";
        $subject = "=?utf-8?B?".base64_encode($subject)."?=";
        $header="From: info@ezhprines.kz\nReply-To: info@ezhprines.kz\nContent-Type: text/html; charset=utf-8\nContent-Transfer-Encoding: 8bit";
  
        $arr_text = array();
        $sum_visitors = 0;
        $sum = 0;
        $sum_comission = 0;
        $arr_comission = array();
        $arr_comission1 = array();
        $sum_bonus = 0;
        $r = mysql_query("select p.name,p.price,p.bonus,p.comission,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$dt' where p.id_master=$id");
        while($a = mysql_fetch_array($r)){
          $price = intval($a['price']);
          $name = $a['name'].' ('.$price.')';
          $comission = intval($a['comission']);
          $visitors = intval($a['visitors']);
          $bonus = intval($a['bonus']);
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
            $sum_bonus += $visitors*$bonus;
            $sum_price += $visitors*$price;
          }
        }
        $sum_comission1 = $sum_comission;
        if ($course>0)$sum_comission1 *= $course;
        $sum1 = $sum;
        if ($course>0)$sum1 *= $course;
        $sum_bonus1 = $sum_bonus;
        $outcome1 = $outcome;
  
        ob_start();
  ?>
  Здравствуйте.<br/>
  Отчет по процедурам <?=$dt2?>-<?=$dt1?><br/>
  Всего процедур: <?=$sum_visitors?><br/>
  <?=implode("<br>\n",$arr_text)?><br/>
  Сумма <?=$sum?><br/>
  Комиссия <?=$sum_comission?> (<?=implode(" + ",$arr_comission)?>)<br/>
  Чек оплаты можно <a href='http://<?=$_SERVER['SERVER_NAME']?>/'>загрузить здесь</a>.<br/>
  Не забудьте ввести свои логин и пароль.<br/>
  <?php
        $body = ob_get_contents();
        ob_end_clean();
        mail($email,$subject,$body,$header);
      }
    }
    exit;
  }

  function show_city_statistics($city_id,$dt){
     ob_start();
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
     ?>
            <div style='position:absolute;top:60px;left:0;width:1178px;padding:10px;border:1px solid black;border-top:0;background-color:white;'>
                <table style="width: 100%;">
                      <tr>
                          <td style="width: 35%;font-size:20px;">
                              Конверсия Instagram
                              <strong>
                                  <?=$GLOBALS['stats']['cities'][$city_id]['City_Efficiency']?>%
                              </strong>
                          </td>
                          <td style="text-align: center;">
                              Контакты
                              <br/>
                              <strong>
                                  <?=$GLOBALS['stats']['cities'][$city_id]['City_Contacts']?>
                              </strong>
                          </td>
                          <td style="text-align: center;">
                              Основные записи
                              <br/>
                              <strong>
                                  <?=$GLOBALS['stats']['cities'][$city_id]['City_Master_Records']?>
                              </strong>
                          </td>
                          <td style="text-align: center;">
                              Доп. записи
                              <br/>
                              <strong>
                                  <?= (($GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] == null) ? 0 : $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'])?>
                              </strong>
                          </td>
                          <td style="text-align: center;">
                              Пришедшие
                              <br/>
                              <strong>
                                  <?=$GLOBALS['stats']['cities'][$city_id]['City_Visitors']?>
                              </strong>
                          </td>
                          <td style="text-align: center;display:none;">
                              Бонусы
                              <br/>
                              <strong>
                                  <?=$GLOBALS['stats']['cities'][$city_id]['City_Bonuses']?>
                              </strong>
                          </td>
                      </tr>
                  </table>
                <table style="width: 100%;">
      <?php
      $qxck = mysql_query("SELECT sum(usevk) as ttr FROM masters where id_m_city=$city_id");
      $axck = mysql_fetch_array($qxck);
      if ((int)$axck['ttr']>0)
      {
          ?>
                    <tr>
                        <td style="width: 35%;font-size:20px;">
                            Конверсия ВК
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_EfficiencyVK']?>%
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Контакты
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_ContactsVK']?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Основные записи
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_Master_RecordsVK']?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Доп. записи
                            <br/>
                            <strong>
                                <?= (($GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK'] == null) ? 0 : $GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK'])?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Пришедшие
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_Visitors']?>
                            </strong>
                        </td>
                        <td style="text-align: center;display:none;">
                            Бонусы
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_Bonuses']?>
                            </strong>
                        </td>
                    </tr>
          <?php } ?>
                </table>
                </div>
     <?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }

  function show_master($master_id, $dt){
    ob_start();
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
    $sum_no_self["all"] = $master_week_array['sum_no_self'];

    $sum_no_self["mo"] = $master_week_array['sum_no_self_mo'];
    $sum_no_self["tu"] = $master_week_array['sum_no_self_tu'];
    $sum_no_self["we"] = $master_week_array['sum_no_self_we'];
    $sum_no_self["th"] = $master_week_array['sum_no_self_th'];
    $sum_no_self["fr"] = $master_week_array['sum_no_self_fr'];
    $sum_no_self["sa"] = $master_week_array['sum_no_self_sa'];
    $sum_no_self["su"] = $master_week_array['sum_no_self_su'];
    ?>
  
              <h3>
                  <?= $master_data['name']?>
    <?php 
    $current_dt = $dt;
    $week_ago_dt = date("Y-m-d", strtotime($dt) - (60*60*24*7));
    $monday = date("Y-m-d", strtotime($dt));
    $_q = "select bill_checked, course from master_week where id_master=$m_id and dt='$week_ago_dt'";
    $_r = mysql_query($_q);
    $_a = mysql_fetch_array($_r);
    $bill_checked = $_a['bill_checked'];
    $mcourse=$_a['course'];
  
    /** Проверка комиссии за предыдущую неделю **/
    $procedures_query = "SELECT sum(p.comission*w.visitors) sum_comission FROM procedures p left join master_procedure_week w on p.id=w.id_procedure and w.id_master=p.id_master where p.id_master=$m_id and w.dt='$week_ago_dt'";
    $procedures_resources = mysql_query($procedures_query);
    $procedures_array = mysql_fetch_array($procedures_resources);
    $sum_comission = intval($procedures_array['sum_comission']);
  
      /** Проверка посетителей за предыдущую неделю **/
    $procedures_queryVis = "SELECT sum(w.visitors) sum_vis FROM procedures p left join master_procedure_week w on p.id=w.id_procedure and w.id_master=p.id_master where p.id_master=$m_id and w.dt='$week_ago_dt'";
    $procedures_resourcesVis = mysql_query($procedures_queryVis);
    $procedures_arrayVis = mysql_fetch_array($procedures_resourcesVis);
    $sum_Vis = intval($procedures_arrayVis['sum_vis']);
    
    $label = "";
    if ( (strtotime(date("Y-m-d")) >= strtotime($monday))){
      if ($bill_checked != 2 && ($sum_Vis > 0 || $sum_comission > 0)){    // $sum_comission
        $label = "<span style='color: #FFF; background-color: red; padding: 5px; display: inline-block; float: right; font-size:11px;'>Прошлая неделя не оплачена</span> ";
      }
      if ($bill_checked != 2 && $sum_Vis == 0  && $sum_comission==0){
            $label = "<span style='color: #FFF; background-color: #6B8E23; padding: 5px; display: inline-block; float: right; font-size:11px;'>Записей не было</span> ";
      }
/*      if ($bill_checked != 2 && $sum_bonus > 0){
            $label = "<span style='color: #FFF; background-color: #ffff00; padding: 5px; display: inline-block; float: right; font-size:11px;'>Записей не было</span> ";
        }
*/      if ($bill_checked == 1){
        $label = "<span style='color: #FFF; background-color: orange; padding: 5px; display: inline-block; float: right; font-size:11px;'>Чек загружен</span>";
      }
      if ($bill_checked == 2){
          $label = "<span style='color: #FFF; background-color: green; padding: 5px; display: inline-block; float: right; font-size:11px;'>Неделя оплачена</span>";
      }
    }
    echo $label;
    ?>
              </h3>
              <div style="border: 1px solid black; padding: 10px;">
                    <?php $GLOBALS['stats']['cities'][$city_id]['City_Bonuses'] += $sum_bonus;?>
                  <!-- <p>
                    бонусов за неделю: <b>$sum_bonus</b>
                  </p> -->
  
                  <table style="width: 100%;">
                      <tbody>
                          <tr>
                              <td style="border-right:1px solid black; text-align: center;" width="220;"> <!-- padding-bottom:30px;  -->
                                  Процедуры
                              </td>
                            <td style="border-right:1px solid black;" align="center"><?php if($m_by_percent==1){echo "Примерный ";} ?>Балл</td>
                              <td style="border-right:1px solid black;" class="header" align="center"><!-- padding-bottom:30px;  -->
                                  <div align="center"><span style="background: linear-gradient( #400080, transparent), linear-gradient( 200deg, #d047d1, #ff0000, #ffff00); color: white; padding: 3px;">Запись INSTAGRAM</span></div>
                                  <table style="float: left;">
                                      <tr>
                                          <td style="width:35px;text-align:center;">Пн</td>
                                          <td style="width:35px;text-align:center;">Вт</td>
                                          <td style="width:35px;text-align:center;">Ср</td>
                                          <td style="width:35px;text-align:center;">Чт</td>
                                          <td style="width:35px;text-align:center;">Пт</td>
                                          <td style="width:35px;text-align:center;">Сб</td>
                                          <td style="width:35px;text-align:center;">Вс</td>
                                          <td style="width:35px;text-align:center;">Общ</td>
                                      </tr>
                                  </table>
                              </td>
                              <td style="border-right:1px solid black;" class="header" align="center"><!-- padding-bottom:30px;  -->
                                  <div align="center"><span style="background: #2B587A; color: white; padding: 3px;">Запись ВК</span></div>
                                  <table style="float: left;">
                                      <tr>
                                          <td style="width:35px;text-align:center;">Пн</td>
                                          <td style="width:35px;text-align:center;">Вт</td>
                                          <td style="width:35px;text-align:center;">Ср</td>
                                          <td style="width:35px;text-align:center;">Чт</td>
                                          <td style="width:35px;text-align:center;">Пт</td>
                                          <td style="width:35px;text-align:center;">Сб</td>
                                          <td style="width:35px;text-align:center;">Вс</td>
                                          <td style="width:35px;text-align:center;">Общ</td>
                                      </tr>
                                  </table>
                              </td>
                              <td style="" class="header"><!-- padding-bottom:30px;  -->
                                  <div align="center">Пришедшие</div>
                                  <table>
                                      <tr>
                                          <td style="width:35px;text-align:center;">Пн</td>
                                          <td style="width:35px;text-align:center;">Вт</td>
                                          <td style="width:35px;text-align:center;">Ср</td>
                                          <td style="width:35px;text-align:center;">Чт</td>
                                          <td style="width:35px;text-align:center;">Пт</td>
                                          <td style="width:35px;text-align:center;">Сб</td>
                                          <td style="width:35px;text-align:center;">Вс</td>
                                          <td style="width:35px;text-align:center;">Общ</td>
                                      </tr>
                                  </table>
                              </td>
                              <td style="display:none;" class="header" align="center"><!-- padding-bottom:30px;  -->
                                  Бонусы
                              </td>
                          </tr>
    <?php
      $records_week_sum = 0;
      $records_week_sumvk = 0;
      $visitors_week_sum = 0;
      $sum_bonus_week_sum = 0;
      $send_mail_flag = 1;
      $prAVG=0;
      $prb=1;

    $rb = mysql_query("select AVG(comission) as avg from procedures where id_master=$m_id and active>0 order by sort desc");
    $ab = mysql_fetch_array($rb);
    if ($mcourse>0){$prAVG=$ab['avg']*$mcourse;} else {$prAVG=$ab['avg'];}

    $rb = mysql_query("select distinct base_percent from bonus where id<>1");
    $ab = mysql_fetch_array($rb);
    $prb=$ab['base_percent'];

      $r = mysql_query("select * from procedures where id_master=$m_id and active>0 order by sort desc");
      while ($a = mysql_fetch_array($r)){
        $p_id = $a['id'];
        $p_name = $a['name'];
        $p_bonus = $a['bonus'];
        $p_count_in_scores = intval($a['count_in_scores']);
        $balx=intval($a['bals']);
    if ($mcourse>0) { $csion=$a['comission']*$mcourse; } else {$csion=$a['comission'];}
  
  
        $q1 = "select * from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt='$dt'+interval 6 day";
        $r1 = mysql_query($q1);
        $a1 = mysql_fetch_array($r1);
        $records = $a1['records'];
        $recordsvk = $a1['recordsvk'];
        $weekdaysXXX = ["mon"=>$a1['zap_mon'],"tu"=>$a1['zap_tu'],"we"=>$a1['zap_we'],"th"=>$a1['zap_th'],"fr"=>$a1['zap_fr'],"sa"=>$a1['zap_sa'],"su"=>$a1['zap_su']];

    $weekdaysVVV = ["mon"=>$a1['zap_monvk'],"tu"=>$a1['zap_tuvk'],"we"=>$a1['zap_wevk'],"th"=>$a1['zap_thvk'],"fr"=>$a1['zap_frvk'],"sa"=>$a1['zap_savk'],"su"=>$a1['zap_suvk']];
        $q1 = "select sum(records) records_week from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt>='$dt' and dt<='$dt'+interval 6 day";
        $r1 = mysql_query($q1);
        $a1 = mysql_fetch_array($r1);
        $records_week = intval($a1['records_week']);
  
        $records_week_sum +=  $records_week;

    $q1 = "select sum(recordsvk) recordsvk from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt>='$dt' and dt<='$dt'+interval 6 day";
    $r1 = mysql_query($q1);
    $a1 = mysql_fetch_array($r1);
    $records_weekvk = intval($a1['recordsvk']);
    $records_week_sumvk +=  $records_weekvk;

        if ($p_count_in_scores == 1){
          $GLOBALS['stats']['cities'][$city_id]['City_Master_Records'] += $records_week;
          $GLOBALS['stats']['cities'][$city_id]['City_Master_RecordsVK'] += $records_weekvk;
        }else{
          $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] += $records_week;
            $GLOBALS['stats']['cities'][$city_id]['City_Extras_RecordsVK'] += $records_weekvk;
        }
  
        $visitors = [];
        $q1 = "select * from master_procedure_week where id_master=$m_id and id_procedure=$p_id and dt='$dt'";
        $r1 = mysql_query($q1);
        if (mysql_num_rows($r1)>0){
        $a1 = mysql_fetch_array($r1);
          $visitors["all"] = intval($a1['visitors']);

          $visitors["mo"] = (is_null($a1['visitors_mo']))?'':intval($a1['visitors_mo']);
          $visitors["tu"] = (is_null($a1['visitors_tu']))?'':intval($a1['visitors_tu']);
          $visitors["we"] = (is_null($a1['visitors_we']))?'':intval($a1['visitors_we']);
          $visitors["th"] = (is_null($a1['visitors_th']))?'':intval($a1['visitors_th']);
          $visitors["fr"] = (is_null($a1['visitors_fr']))?'':intval($a1['visitors_fr']);
          $visitors["sa"] = (is_null($a1['visitors_sa']))?'':intval($a1['visitors_sa']);
          $visitors["su"] = (is_null($a1['visitors_su']))?'':intval($a1['visitors_su']);
        }else{
          $visitors["all"] = '';
        }
        if($visitors["all"]=='') $send_mail_flag = 0;
        //    if ($visitors==0)$visitors1 = '';else $visitors1 = $visitors;
        $sum_bonus = $p_bonus*intval($visitors["all"]);
  
        $visitors_week_sum += intval($visitors["all"]);
        $sum_bonus_week_sum += intval($sum_bonus);
    ?>
                          <tr data-id=<?=$p_id?>>
                              <td style="text-align: right; border-right:1px solid black;" width="150">
                                  <b>
                                      <span<?php if ($p_count_in_scores==0) {echo " style='font-weight: 400; margin-right: 3px;'";} else {echo " style='margin-right: 3px;' ";}  echo ">"; if ($p_count_in_scores>0) echo "✩";  ?> <?=$p_name?>
                                  </b>
                              </td>
                                  <td style="text-align: center;     padding: 0px 50px; border-right:1px solid black;">
                                  <?php
                                  $cvet="";
                                  if (($csion>$prAVG) || ($csion>3000)) $cvet='style="border: 2px solid; display: inline-block; background: #ffed00; border-color: #f69240; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                //  if ($balx==4) $cvet='style="border: 2px solid; display: inline-block; background: #d3d3d3; border-color: #aaaaaa; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                //  if ($balx==3) $cvet='style="border: 2px solid; display: inline-block; background: #f69240; border-color: #f5b696; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                //  if ($balx==2) $cvet='style="border: 2px solid; display: inline-block; background: #fbf9ee; border-color: #e6e6e6; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                //  if ($balx==1) $cvet='style="border: 2px solid; display: inline-block; background: #fbf9ee; border-color: #e6e6e6; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                  if ($csion<=$prAVG && $csion<=3000) $cvet='style="border: 2px solid; display: inline-block; background: #fbf9ee; border-color: #e6e6e6; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';

                                  ?>
                                   <span <?=$cvet ?>><?php if ($csion>0 && $prb>0) echo round($csion/$prb, 1); else echo 1; ?></span>
                                 </td>
                              <script>
                                  function clickb(elem, param) {
                                      var x=document.getElementById(elem).value;
                                      if (param=="v") x++;
                                      if (param=="n") x--;
                                  document.getElementById(elem).style.backgroundColor='rgb(196, 214, 156)';
                                  document.getElementById(elem).value = x;
                                  if (x<0) {alert("Число уходит в минус проверьте данные."); document.getElementById(elem).value=""; document.getElementById(elem).style.backgroundColor='rgb(256, 256, 256)'; }
                                  }

                                  function kdown(elem) {
                                      var x=document.getElementById(elem).value;
                                      if (event.keyCode==38) x++;
                                      if (event.keyCode==40) x--;
                                      document.getElementById(elem).value = x;
                                      document.getElementById(elem).style.backgroundColor='rgb(196, 214, 156)';
                                      if (x<0) {alert("Меньше нуля"); document.getElementById(elem).value=""; document.getElementById(elem).style.backgroundColor='rgb(256, 256, 256)'; }
                                  }
                              </script>
                              <td style="padding-right:0px;padding-left:5px; border-right:1px solid black;">
                                  <?php
                                  $weekdays = ["Пн"=>"mon","Вт"=>"tu","Ср"=>"we","Чт"=>"th","Пт"=>"fr","Сб"=>"sa","Вс"=>"su"];
                                  echo "<table ><tr>";
                                  foreach($weekdays as $key => $weekday){
                                  ?>
                              <td style="width: 35px;">
                                  <input onkeydown="kdown('p_<?=$p_id?>_<?=$weekday?>_zap')" type="text" style="margin-left: -5px; width: 29px;" id="p_<?=$p_id?>_<?=$weekday?>_zap" value="<?php if ($weekdaysXXX[$weekday]!=0) echo $weekdaysXXX[$weekday]; else echo '0'; ?>" class='p_input p_input<?=$m_id?>' placeholder="<?=$key?>" />
                              </td>
                              <?php
                              }
                              ?>
                              <td style="padding-right:0px; width: 35px;" align="left">
                                  <b style="font-size: 12px;"><input onkeydown="kdown('p_<?=$p_id?>_6')" type="text" style="width: 29px; float: left; display: none; " id="p_<?=$p_id?>_6" value="<?=$records?>" class="p_input"/><?=$records?> шт</b>
                              </td></tr></table>
                              </td>

              <!--   /* вк*****************************************/  -->
              <td style="padding-right:0px;padding-left:5px; border-right:1px solid black;">
              <?php
              $weekdays = ["Пн"=>"mon","Вт"=>"tu","Ср"=>"we","Чт"=>"th","Пт"=>"fr","Сб"=>"sa","Вс"=>"su"];
              echo "<table ><tr>";
              foreach($weekdays as $key => $weekday){
              ?>
                  <td style="width: 35px;">
                      <input onkeydown="kdown('p_<?=$p_id?>_<?=$weekday?>_zapvk')" type="text" style="margin-left: -5px; width: 29px; " id="p_<?=$p_id?>_<?=$weekday?>_zapvk" value="<?php if ($weekdaysVVV[$weekday]!=0) echo $weekdaysVVV[$weekday]; else echo '0'; ?>" class='p_input p_input<?=$m_id?>' placeholder="<?=$key?>" />
                  </td>
              <?php
              }
              ?>
                  <td style="padding-right:0px; width: 35px;" align="left">
                      <b style="font-size: 12px;"><?=$recordsvk?> шт</b>
                  </td></tr></table>
                  </td>
              <!--   /* вк*****************************************/  -->



              <td style="padding-right:0px;padding-left:5px;">
                              <?php
                                $weekdays = ["Пн"=>"mo","Вт"=>"tu","Ср"=>"we","Чт"=>"th","Пт"=>"fr","Сб"=>"sa","Вс"=>"su"];
                                echo "<table><tr>";
                                foreach($weekdays as $key => $weekday){
                                  ?>
                              <td style="width: 35px;">
                                  <input onkeydown="kdown('p_<?=$p_id?>_<?=$weekday?>')" type="text" style="margin-left: -5px; width: 29px; " id="px_<?=$p_id?>_<?=$weekday?>" value="<?php if ($visitors[$weekday]!=0) echo $visitors[$weekday]; else echo '0'; ?>" class='p_input p_input<?=$m_id?>' placeholder="<?=$key?>" /><br>
                              </td>
                               <?php
                                }
                              ?>
                              <td style="padding-right:0px; width: 35px;" align="center">
                                  <b style="font-size: 12px;"><?=$visitors["all"]?> шт</b>
                              </td></tr></table>
                              </td>
              <!--                <td style="padding-right:10px;" align="center">
                                  <b>
                                      <?=$sum_bonus?>
                                  </b>
                              </td>  -->
                          </tr>
    <?php 
      }
      $GLOBALS['stats']['cities'][$city_id]['City_Visitors'] += $visitors_week_sum;
    ?>
                      </tbody>
                  </table>
  
                  <p>
                    <span></span>
                  </p>
    <?php
    if($m_by_percent==1){
      $q = "select * from m_city_week where m_city_id=$city_id and dt='$dt' LIMIT 1";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $master_on_procent_procedures_count = intval($a1['master_on_procent_procedures_count']);
  
      //$GLOBALS['stats']['cities'][$city_id]['City_Master_Records'] = $master_on_procent_procedures_count;
  
    ?>
                <div>
                  <div style='padding-bottom:20px;'>
                    <b>Сумма за неделю (без себестоимости):</b> <br/>
                    <table>
                    <tr>
                    <?php
                      $weekdays = ["Пн"=>"mo","Вт"=>"tu","Ср"=>"we","Чт"=>"th","Пт"=>"fr","Сб"=>"sa","Вс"=>"su"];
                      foreach($weekdays as $key => $weekday){ 
                    ?>
                    <td align="center"><?=$key?></td>
                    <?php }?>
                    <td>Общ</td>
                    </tr>
                    <tr>
                        <?php
                        foreach($weekdays as $key => $weekday){ 
                        ?>
                        <td>
                        <input onkeydown="kdown('sum_no_self<?=$m_id?>_<?=$weekday?>')" type='text' id='sum_no_self<?=$m_id?>_<?=$weekday?>' name='sum_no_self<?=$m_id?>' class='p_input' value='<?=$sum_no_self[$weekday]?>' placeholder="<?=$key?>"/>
                        </td>
                        <?php }?>
                        <td>
                        <input type='text' id='sum_no_self<?=$m_id?>' name='sum_no_self<?=$m_id?>' class='p_input' value='<?=$sum_no_self["all"]?>' disabled/>
                        </td>
                    </tr>
                    </table>
                    
                  </div>
                </div>
    <?php
    }
    ?>
  
                <div style="margin: 10px;">
    <?php
    if($sent==0){
    ?>
      <input type='button' id='send_mail<?=$m_id?>'<?php if($send_mail_flag==1){?> class='orange'<?php } ?> onclick='send_mail(<?=$m_id?>,"<?=$dt?>")'<?php if($send_mail_flag==0){?> disabled<?php } ?> value='Отправить мастеру'>
    <?php
    }else{
    ?>
      <input type='button' id='send_mail<?=$m_id?>' class='green' onclick='send_mail(<?=$m_id?>,"<?=$dt?>")' value='Отправить еще раз'>
    <?php
    }
    ?>
                  <input style="float:right;" type='button' value='Сохранить' onclick='save_master(<?=$id?>,<?=$m_id?>)' class='orange' />
                </div>
  
                <input type='hidden' id='dt' value='<?=$dt?>' class='p_input'>
                <input type='hidden' id='id_master' value='<?=$m_id?>' class='p_input'>
              </div>
    <?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
  function show_contacts($city_id,$dt){  ob_start();
    ?>
                <div style="margin-top: 20px;">
                  <table style="width: 100%;" class='city_table' data-id='<?=$city_id?>'>
                    <tr>
                      <td></td>
                      <td style="text-align: center;">Пн</td>
                      <td style="text-align: center;">Вт</td>
                      <td style="text-align: center;">Ср</td>
                      <td style="text-align: center;">Чт</td>
                      <td style="text-align: center;">Пт</td>
                      <td style="text-align: center;">Сб</td> 
                      <td style="text-align: center;">Вс</td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td style="text-align: right;">Контакты в тел.</td>
    <?php
  
      $q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $isOnPercent = intval($a1['count']) > 0;
  
  
      $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'-interval 1 day";
      $r1 = mysql_query($q);
      $a1 = mysql_fetch_array($r1);
      $chats_old = $a1['chats'];
    ?>
                      <input type='hidden' name='ch_<?=$city_id?>_old' id='ch_<?=$city_id?>_old' value='<?=$chats_old?>'>
    <?php
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
    ?>
        <script>
        function kdownK(elem, cid, li) {
        var x=document.getElementById(elem).value;
        if (event.keyCode==38) x++;
        if (event.keyCode==40) x--;
        document.getElementById(elem).value = x;
            document.getElementById(elem).style.backgroundColor='rgb(196, 214, 156)';
//      background-color: rgb(196, 214, 156);  this.set_contacts(cid, li);
        if (x<0) {alert("Меньше нуля"); document.getElementById(elem).value=""; document.getElementById(elem).style.backgroundColor='rgb(256, 256, 256)'; }
        }
        </script>
        <td style="text-align: center;"><input onkeydown="kdownK('ch_<?=$city_id?>_<?=$i1?>', '<?=$city_id?>', '<?=$i1?>')" type="text" style="width: 50px;" id='ch_<?=$city_id?>_<?=$i1?>' value='<?=$chats?>' oninput='set_contacts(<?=$city_id?>, <?=$i1?>);' class='p_input' data-dt='<?=$current_dt?>' data-type='contacts'></td>
    <?php
    }
  
    $other_contacts = 0;
  
    $q = "select * from m_city_week where m_city_id=$city_id and dt='$dt' LIMIT 1";
    //var_dump($dt);die();
    $r1 = mysql_query($q);
    $a1 = mysql_fetch_array($r1);
    $other_contacts = intval($a1['other_contacts']);
    $master_on_procent_procedures_count = intval($a1['master_on_procent_procedures_count']);
  
    ?>
                    <td style="text-align: center;">
                        <!--              Погрешность <input type="text" data-type='other_contacts' value='<?=$other_contacts?>'>-->
                      </td>
                      <td>
                        <input style='vertical-align: middle;' type='button' value='Сохранить' onclick='save_contacts($(this), <?=$city_id?>, "<?=$dt?>")' class='orange'></td>
                      </td>
                    </tr>
                    <tr>
                      <td style="text-align: right;">Прирост</td>
    <?php
    $new_chatsLF = [];
    for($i=1;$i<=7;$i++) {
        $i1 = $i - 1;
        $r1 = mysql_query("select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day");
        $a1 = mysql_fetch_array($r1);
        $lfchats = $a1['lidfit'];
        $new_chatsLF[$i] = $lfchats;
    }


    for($i=1;$i<=7;$i++){
    ?>
                      <td style="text-align: center;" id='contacts<?=$city_id?>_<?=$i?>' class='p_input'><?=$new_chats[$i]+$new_chatsLF[$i]?></td>
    <?php
    }
    ?>
                    </tr>
                      <tr>
                          <td style="text-align: right;">Контакты Direct</td>
                          <?php
                          $new_chatsLF = [];
                          for($i=1;$i<=7;$i++) {
                              $i1 = $i - 1;
                              $r1 = mysql_query("select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day");
                              $a1 = mysql_fetch_array($r1);
                              $lfchats = $a1['lidfit'];
                              $new_chatsLF[$i] = $lfchats;
                              $current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
                              ?>
                              <td style="text-align: center;"><input type="text" style="width: 50px;" id='chlf_<?=$city_id?>_<?=$i1?>' value='<?=$lfchats?>' oninput='set_contacts(<?=$city_id?>, <?=$i1?>);' class='p_input' data-dt='<?=$current_dt?>' data-type='contactsLEDFIT'></td>
                              <?php
                          }
                          ?>
                      </tr>
                      <?php
                      $qxck = mysql_query("SELECT sum(usevk) as ttr FROM masters where id_m_city=$city_id");
                      $axck = mysql_fetch_array($qxck);
                      if ((int)$axck['ttr']>0)
                      {
                      ?>
                    <tr>
                        <td style="text-align: right;"><br>Контакты с ВК </td>
                        <?php
                        $new_chatsvk = [];
                        for($i=1;$i<=7;$i++){
                            $current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
                            $qxx = "select chatsvk from m_city_day_vk where id_m_city=$city_id and dt='$current_dt'";
                            $rxx1 = mysql_query($qxx);
                            $ax1 = mysql_fetch_array($rxx1);
                            $new_chatsvk[$i] = $ax1['chatsvk'];
                            $GLOBALS['stats']['cities'][$city_id]['City_ContactsVK'] += intval($new_chatsvk[$i]);
                            ?>
                            <td style="text-align: center;"><br><input type="text" style="width: 50px;" id='chvk_<?=$city_id?>_<?=$i?>' value='<?=$ax1['chatsvk']; ?>' class='p_input' data-dt='<?=$current_dt?>' data-type='contactsvk' disabled></td>

                            <?php
                        }
                        ?>
                    </tr>
                          <?php } ?>
                  </table>
                </div>
    <?php
      $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
  function show_masters($manager_id,$dt){
    ob_start();
    $html="";
    $m_city=new m_city();
    $cities = $m_city->allCities();
    $masters=new masters();
    foreach ($cities as $city){
      $city_id = $city['id'];
          $mast=$masters->selectAllMastersByManagerAndCity($manager_id, $city_id);
          if (count($mast) > 0){
              $html.= "<section class='city T_M_manager_st_block' data-id='$city_id'>";
              $html.= "<h3>";
              $html.= $city['name'].".<br><span class='T_M_manager_st_city'>(В конверсии учитываются только процедуры помеченные ✩)</span>";
              $html.= "</h3>";
              $html.= "<div class='T_M_manager_st_block_inside'>";
              $html.= "<div class='T_M_manager_overflo'>";
              $html.= "<table><tr>";
              foreach ($mast as $mmaa) {
                  $master_id = $mmaa['id'];
                  $html.= "<td class='T_M_manager_valign'><div id='master$master_id' class='T_M_manager_block_inser'>";
                  $html.= show_master($master_id,$dt); ///правки
                  $html.= "</div></td>";
                  }
              $html.= "</tr>";
              $html.= "</table>";
              $html.= "</div>";
              $html.= "<div class='T_M_both;'></div>";
              $html.= show_contacts($city_id,$dt); // правки
              $html.= show_city_statistics($city_id,$dt); // правки
              $html.= "</div>";
              $html.= "</section>";
          }
    }
    $res = ob_get_contents();
    ob_end_clean();
    return $html;
  }
  
?>