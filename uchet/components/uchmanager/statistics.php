<?php
$uid=$id;
if ($operation=='show_master'){
    $id = intval($_POST['id']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    $masters = show_masters($id,$dt, $uid);
    $html = $masters;
    print $html;
    exit;
  }
  if ($operation=='save_master'){
      $stats=new stats($_POST);
      $stats->iduser=$uid;
      $stats->saveSelfZapVis();
      print show_master($stats->getMID(),$stats->dt, $uid);
      exit;
  }

  function show_master($master_id, $dt, $uid){
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
    $tuesday = date("Y-m-d", strtotime($dt)); // + (60*60*24)
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
    if ( (strtotime(date("Y-m-d")) >= strtotime($tuesday))){
      if ($bill_checked != 2 && ($sum_Vis > 0 || $sum_comission > 0)){    // $sum_comission
        $label = "<span style='color: #FFF; background-color: red; padding: 5px; display: inline-block; float: right; font-size:11px;'>Прошлая неделя не оплачена</span> ";
      }
      if ($bill_checked != 2 && $sum_Vis == 0  && $sum_comission==0){
            $label = "<span style='color: #FFF; background-color: #6B8E23; padding: 5px; display: inline-block; float: right; font-size:11px;'>Записей не было</span> ";
      }
      if ($bill_checked == 1){
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
                  <table style="width: 100%;">
                      <tbody>
                          <tr>
                              <td style="border-right:1px solid black; text-align: center;" width="220"> <!-- padding-bottom:30px;  -->
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
      $visitors_week_sum = 0;
      $sum_bonus_week_sum = 0;
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
  
        if ($p_count_in_scores == 1){
          $GLOBALS['stats']['cities'][$city_id]['City_Master_Records'] += $records_week;
        }else{
          $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] += $records_week;
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
        $sum_bonus = $p_bonus*intval($visitors["all"]);
  
        $visitors_week_sum += intval($visitors["all"]);
        $sum_bonus_week_sum += intval($sum_bonus);
    ?>
                          <tr data-id=<?=$p_id?>>
                              <td style="text-align: right; border-right:1px solid black;" width="250"><b><span<?php if ($p_count_in_scores==0) {echo " style='font-weight: 400; margin-right: 3px;'";} else {echo " style='margin-right: 3px;' ";}  echo ">"; if ($p_count_in_scores>0) echo "✩";  ?> <?=$p_name?></b></td>
                            <td style="text-align: center;     padding: 0px 50px; border-right:1px solid black;">
                                  <?php
                                  $cvet="";
                                  if (($csion>$prAVG) || ($csion>3000)) $cvet='style="border: 2px solid; display: inline-block; background: #ffed00; border-color: #f69240; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';
                                  if ($csion<=$prAVG && $csion<=3000) $cvet='style="border: 2px solid; display: inline-block; background: #fbf9ee; border-color: #e6e6e6; text-align: center; border-radius: 50%; font-weight: 700; padding: 0.15em 0.45em; color: #222222; float: left; box-shadow: 0px 0px 3px 0px #616161;"';

                                  ?>
                                <span <?=$cvet ?>><?php if ($csion>0 && $prb>0) echo round($csion/$prb,1); else echo 1; ?></span>
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
                                  <b  style="font-size: 12px;"><input onkeydown="kdown('p_<?=$p_id?>_6')" type="text" style="width: 30px; float: left; display: none;" id="p_<?=$p_id?>_6" value="<?=$records?>" class="p_input"/><?=$records?> шт</b>
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
                      <input onkeydown="kdown('p_<?=$p_id?>_<?=$weekday?>_zapvk')" type="text" style="margin-left: -5px; width: 29px;" id="p_<?=$p_id?>_<?=$weekday?>_zapvk" value="<?php if ($weekdaysVVV[$weekday]!=0) echo $weekdaysVVV[$weekday]; else echo '0'; ?>" class='p_input p_input<?=$m_id?>' placeholder="<?=$key?>" />
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
                      <input onkeydown="kdown('p_<?=$p_id?>_<?=$weekday?>')" type="text" style="margin-left: -5px; width: 29px;" id="px_<?=$p_id?>_<?=$weekday?>" value="<?php if ($visitors[$weekday]!=0) echo $visitors[$weekday]; else echo '0'; ?>" class='p_input p_input<?=$m_id?>' placeholder="<?=$key?>" /><br>
                  </td>
              <?php
              }
              ?>
                  <td style="padding-right:0px; width: 35px" align="center">
                      <b style="font-size: 12px;"><?=$visitors["all"]?> шт</b>
                  </td></tr></table>
                  </td>
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
  
                <div style="margin: 40px;">
                  <input style="margin-top: -30px; float:right;" type='button' value='Сохранить' onclick='save_master(<?=$id?>, <?=$uid?>)' class='orange' />
                </div>
  
                <input type='hidden' id='dt' value='<?=$dt?>' class='p_input'>
                <input type='hidden' id='id_master' value='<?=$m_id?>' class='p_input'>
              </div>
    <?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
  function show_masters($manager_id,$dt, $uid){
    ob_start();
    $cities = mysql_query("SELECT * FROM `m_city`");
    while ($city = mysql_fetch_array($cities)){
        $usvk=0;
      $city_id = $city['id'];
      ?>
      <?php 
          $masters_query = "select u.*, m.id_master from users u,masters m where u.type=0 and m.shown=1 and u.id=m.id_master and m.id_uchenik=$manager_id and id_m_city=$city_id order by m.sort";
          $masters_resource = mysql_query($masters_query);
          if (mysql_num_rows($masters_resource) > 0){
      ?>
          <section class="city" style="margin-top: 25px; border: 1px solid; padding: 3px; width: 1200px;position:relative;" data-id="<?=$city_id?>">
              <br>
              <br>
              <h3 style="margin: 0px 180px;">
                  <?=$city['name']?>
                  <?php
                  $qcitymasters=mysql_query("SELECT u.name, m.usevk FROM users u, `m_city`c, `masters` m where u.id=m.`id_master` and c.id=m.`id_m_city` and c.id=".$city['id']." and m.shown>0  ORDER BY `u`.`name` ASC");
                  ?>

                      <h5 style="margin: 0px 180px;"><?php echo "Диаграмма конверсий (";
                          $qcitymastera = mysql_fetch_array($qcitymasters);
                          echo $qcitymastera['name'];
                          if ($qcitymastera['usevk']>0) $usvk=1;
                          while ($qcitymastera = mysql_fetch_array($qcitymasters)){ echo ", ".$qcitymastera['name']; if ($qcitymastera['usevk']>0) $usvk=1;}
                          echo ")"; ?></h5>

                  <?php
                  $date1=date("Y-m-d", strtotime("-3 month"));
                  $date2=date("Y-m-d");
                  ?>
                  <p style="padding: 1px 152px; background: white; margin: 2px 0px;"><br><span style=" background: linear-gradient( #400080, transparent), linear-gradient( 200deg, #d047d1, #ff0000,    #ffff00); color: white; padding: 3px;">INSTAGRAM</span><br><img
                              src="chartkonver.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $city['name']; ?>"
                              alt="<?php echo $city['name']; ?>" class="right" id="content" /></p><br>
                  <?php if($usvk>0) { ?>
                  <p style="padding: 1px 152px; background: white; margin: 2px 0px;"><br><span style=" background: #2B587A; color: white; padding: 3px;">ВК</span><br><img
                              src="chartkonvervk.php?dateN=<?php echo $date1 ?>&dateK=<?php echo $date2 ?>&city=<?php echo $city['name']; ?>"
                              alt="<?php echo $city['name']; ?>" class="right" id="content" /></p>
                    <?php } ?>
                  <br>
                  <span style="font-size: 12px; font-weight: 300;">(В конверсии учитываются только процедуры помеченные ✩)</span>
              </h3>
              <div style="border:1px solid black; margin-bottom: 20px;">
                <div style='overflow-x:auto;'>
                      <table><tr>
                  <?php 
                      while ($masters_array = mysql_fetch_array($masters_resource)){
                        $master_id = $masters_array['id'];
                  ?>
  
                      <td style='vertical-align:top;'><div id="master<?=$master_id?>" style=" width: 1160px;float:left; padding:10px;">
                  <?php
                        print show_master($master_id,$dt, $uid);
                  ?>
                      </div></td>
                  <?php
                      }
                  ?>
                  </tr>
                </table>
                </div>
                <div style="clear:both;"></div>
               <?php
               ?>
  
              </div>
          </section>
  
    <?php }
    }
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
  
?>