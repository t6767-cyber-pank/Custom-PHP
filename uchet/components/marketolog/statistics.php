<?php

function show_masters_by_city(){
    $dt = $_POST['dt'];
    $dt = date('Y-m-d', strtotime($dt));
    $masters =  f_show_masters_by_city($dt);
    $html = $masters;
    print $html;
    exit();
  }

  function f_show_masters_by_city($dt,$dt1=0){

    $html = "";
  $cities = mysql_query("SELECT * FROM `m_city`");
    while ($city = mysql_fetch_array($cities)){ 
      $city_id = $city['id'];
  
          $masters_query = "select u.* from users u,masters m where u.type=0 and u.id=m.id_master and m.shown=1 and id_marketolog=3 and m.id_m_city=$city_id";
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
  $sunK=$GLOBALS['stats']['cities'][$city_id]['City_Contacts'];
  $html .="                            <strong>". $sunK;
  
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
      $sunK=$GLOBALS['stats']['cities'][$city_id]['City_ContactsVK'];
      $html .="                            <strong>". $sunK;

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
          $xxccx=$new_chats[$i]+$new_chatsLF[$i];
    $html .="                    <td style='text-align: center;' id='contacts$city_id_$i' class='p_input'>$xxccx</td>";
      }
    $html .="                  </tr>";

    $html .='<tr>';
    $html .='<td style="text-align: right;">Контакты Direct</td>';
    for($i=1;$i<=7;$i++) {
    $i1 = $i - 1;
    $r1 = mysql_query("select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day");
    $a1 = mysql_fetch_array($r1);
    $lfchats = $a1['lidfit'];
    $html .='<td style="text-align: center;"><input type="text" style="width: 50px;" value="'.$lfchats.'" disabled></td>';
                          }
    $html .='</tr>';

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
  
  
    $extra_procedure_summ = '';
    // $eps = mysql_query("SELECT summ FROM master_extra_procedure where master_id = $m_id and dt = '$dt' LIMIT 1");
    // if (mysql_num_rows($eps) > 0){
    //   $extra_procedure_summ = mysql_fetch_array($eps)['summ'];
    // }
    
  
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
  // $html .="                 <p>";
                     
  // $html .="                   бонусов за неделю: <b>$sum_bonus</b>";
  //$html .="                 </p>";
  
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
        //    if ($visitors==0)$visitors1 = '';else $visitors1 = $visitors;
        $sum_bonus = $p_bonus*intval($visitors);
  
        $visitors_week_sum += intval($visitors);
        $sum_bonus_week_sum += intval($sum_bonus);
    
  $html .="                        <tr data-id=$p_id>";
  $html .="                            <td style='padding-bottom:30px; border-right:1px solid black;' width='150'>";
  $html .="                                <b>$p_name";
                                      
  $html .="                                </b>";
  $html .="                            </td>";
  $html .="                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px; border-right:1px solid black;'>";
//  $html .="                                <input type='text' style='width: 30px;' value='$records' disabled/>";
  $html .="                                <b>$records_week<b> шт</b></b>";
  $html .="                            </td>";
          $html .="                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px; border-right:1px solid black;'>";
//          $html .="                                <input type='text' style='width: 30px;' value='$recordsVK' disabled/>";
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
  