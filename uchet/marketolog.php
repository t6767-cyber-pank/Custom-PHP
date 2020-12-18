<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$AJAX_TIMEOUT = 20000;

$operation = $_POST['operation'];
if (!isset($_REQUEST['razdel'])){$_REQUEST['razdel'] = 0;}
/**Клас интерфейса роутинга**/
$InterFC=new InterFC($_REQUEST['razdel']);
$usersCRM=new usersCRM();
/**Ловля события операции**/

require $DOCUMENT_ROOT. '/components/marketolog/statistics.php';

if ($operation=='show_masters'){
  if($_REQUEST['razdel']==0){
    $id = intval($_POST['id']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    print show_masters($id,$dt);
  }else{
    $id = intval($_POST['id']);
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
    print show_masters($id,$dt_from,$dt_to);
  }
  exit;
}
if (isset($operation) && $operation == "show_masters_by_city"){
  print show_masters_by_city();
}
if ($operation=='save_outcome'){
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $outcome = $_POST['outcome'];
  $outcomevk = $_POST['outcomevk'];
  foreach($outcome as $o){
    $o_id = intval($o['id']);
    $o_outcome = $o['outcome'];
    if ($o_outcome!='')$o_outcome = intval($o_outcome);else $o_outcome = 'NULL';
    $o_course = '';
    if (isset($o['course'])){
      $o_course = str_replace(',','.',$o['course']);
    }
    if ($o_course!='')$o_course = floatval($o_course);else $o_course = 'NULL';
    $r = mysql_query("select * from master_week where id_master=$o_id and dt='$dt'");
    if (mysql_num_rows($r)>0){
      mysql_query("update master_week set outcome=$o_outcome,course=$o_course where id_master=$o_id and dt='$dt'");
    }else{
      mysql_query("insert into master_week (outcome,course,id_master,dt)values($o_outcome,$o_course,$o_id,'$dt')");
    }
  }
    foreach($outcomevk as $o){
$o_id = intval($o['id']);
$o_outcome = $o['outcomevk'];
$o_outcomevork = (int)$o['vkwork'];
$o_outcomevorkbudg = (int)$o['vkworkbudg'];
if ($o_outcome!='')$o_outcome = intval($o_outcome);else $o_outcome = 'NULL';
$r = mysql_query("select * from master_week where id_master=$o_id and dt='$dt'");
if (mysql_num_rows($r)>0){
mysql_query("update master_week set outcomeworkvk=$o_outcomevork, budgetvk=$o_outcomevorkbudg where id_master=$o_id and dt='$dt'");
}else{
mysql_query("insert into master_week (outcome, id_master,dt, outcomeworkvk, budgetvk)values(0, $o_outcome,$o_id,'$dt', $o_outcomevork, $o_outcomevorkbudg)");
}
}

    $s_outcome = $_POST['s_outcome'];
    $budgetvkezh= $_POST['vkbudgezh'];
    $vkrabotaezh= $_POST['vkrabota'];
  foreach($s_outcome as $o){
    $o_id = intval($o['id']);
    $o_outcome = $o['s_outcome'];
    if ($o_outcome!='')$o_outcome = intval($o_outcome);else $o_outcome = 'NULL';
    $r = mysql_query("select * from ezh_city_week where id_city=$o_id and dt='$dt'");
    if (mysql_num_rows($r)>0){
      mysql_query("update ezh_city_week set outcome=$o_outcome where id_city=$o_id and dt='$dt'");
        if ($o_id==1)
        {
            mysql_query("update ezh_city_week set vkbudget=$budgetvkezh, vkrabota=$vkrabotaezh where id_city=$o_id and dt='$dt'");
        }
    }else{
      mysql_query("insert into ezh_city_week (outcome,id_city,dt)values($o_outcome,$o_id,'$dt')");
        if ($o_id==1)
        {
            mysql_query("update ezh_city_week set vkbudget=$budgetvkezh, vkrabota=$vkrabotaezh where id_city=$o_id and dt='$dt'");
        }
    }
  }
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
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body style="margin: 0;">
<script type="text/javascript"> 
function change_currencies(id, value){
  $("div[data-currency-id="+id+"] > input").val(value);
}

function change_vkrabota(id, value, value1){
        $("div[data-vk-id='vkvork'] > input").val(value);
        $("div[data-vk-id='vkvork88'] > input").val(value1);
}
$(function() {
    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('#weekpicker').datepicker('widget').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }

<?php if($_REQUEST['razdel']==0 || $_REQUEST['razdel']==2){ ?>
    $('#weekpicker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $('#weekpicker').val($.datepicker.formatDate( dateFormat, startDate, inst.settings )+' - '+$.datepicker.formatDate( dateFormat, endDate, inst.settings ));

          <?php if($_REQUEST['razdel']==0){?>
            show_user_block();  
          <?php }else if($_REQUEST['razdel']==2){?>
            show_masters_by_city(dateFormat);
          <?php } ?>
            
            selectCurrentWeek();
        },
        beforeShow: function() {
            selectCurrentWeek();
        },
        beforeShowDay: function(date) {
            var cssClass = '';
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
    }).datepicker('widget').addClass('ui-weekpicker');
    $( "#weekpicker" ).datepicker( $.datepicker.regional[ "ru" ] );

    $('#weekbefore').click(function(){
        s = $('#weekpicker').val().replace(/ .*/,'');
        arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
        d = new Date(arr[2]+'/'+arr[1]+'/'+arr[3]);
        t = d.getTime();
        t1 = t-7*24*3600*1000;
        d1 = new Date(t1);
        startDate = d1;
        str1 = ('0'+d1.getDate()).slice(-2)+'.'+('0'+parseInt(d1.getMonth()+1)).slice(-2)+'.'+d1.getFullYear();
        t2 = t-24*3600*1000;
        d2 = new Date(t2);
        endDate = d2;
        str2 = ('0'+d2.getDate()).slice(-2)+'.'+('0'+parseInt(d2.getMonth()+1)).slice(-2)+'.'+d2.getFullYear();
        str = str1+' - '+str2;
        $('#weekpicker').val(str);
        
        <?php if($_REQUEST['razdel']==0){?>
          show_user_block();  
        <?php }else if($_REQUEST['razdel']==2){?>
          show_masters_by_city(str1);
        <?php } ?>

        return false;
    });

    $('#weekafter').click(function(){
        s = $('#weekpicker').val().replace(/ .*/,'');
        arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
        d = new Date(arr[2]+'/'+arr[1]+'/'+arr[3]);
        t = d.getTime();
        t1 = t+7*24*3600*1000;
        d1 = new Date(t1);
        startDate = d1;
        str1 = ('0'+d1.getDate()).slice(-2)+'.'+('0'+parseInt(d1.getMonth()+1)).slice(-2)+'.'+d1.getFullYear();
        t2 = t+13*24*3600*1000;
        d2 = new Date(t2);
        endDate = d2;
        str2 = ('0'+d2.getDate()).slice(-2)+'.'+('0'+parseInt(d2.getMonth()+1)).slice(-2)+'.'+d2.getFullYear();
        str = str1+' - '+str2;
        $('#weekpicker').val(str);
        
        <?php if($_REQUEST['razdel']==0){?>
          show_user_block();  
        <?php }else if($_REQUEST['razdel']==2){?>
          show_masters_by_city(str1);
        <?php } ?>

        return false;
    });
    
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
<?}else{?>  
    $('#weekpicker').dateRangePicker({
      format: 'DD.MM.YYYY',
      separator: ' - ',
      language: 'ru',
      startOfWeek: 'monday',
    }).bind('datepicker-apply',function(event,obj)
    {
      dt_from = obj.date1.getFullYear()+'-'+('0'+parseInt(obj.date1.getMonth()+1)).slice(-2)+'-'+('0'+obj.date1.getDate()).slice(-2);
      $('#dt_from').val(dt_from);
      dt_to = obj.date2.getFullYear()+'-'+('0'+parseInt(obj.date2.getMonth()+1)).slice(-2)+'-'+('0'+obj.date2.getDate()).slice(-2);
      $('#dt_to').val(dt_to);
      show_user_block(dt_from,dt_to);
    });
<?}?>
});
<?if($_REQUEST['razdel']==0){?> 
function getDefaultCurrency()
{
  $(".currency").each(function(i, el){
    var id = parseInt($(el).prop('id').replace('currency_', ''));
    var master_currency = $("div[data-currency-id="+id+"]").first();
    if (master_currency.length > 0) {
      $(el).find('input').val(master_currency.find('input').val());
    }
  });
}
$(document).ready(function(){
  getDefaultCurrency();
});
function show_user_block(){
  dt = $("#weekpicker").val().replace(/ .*/,'');
  $("#loader").show();
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      dt:dt,
      id:<?=$id?>,
      operation:'show_masters'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $("#loader").hide();
      $('body').css('cursor','default');
      $('#user_block').html(html); 
      getDefaultCurrency();
    },
    error:function(html){
      $("#loader").hide();
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
  }); 
}
<?php }else if ($_REQUEST['razdel']==2){?>
  function show_masters_by_city(dt){
    dt = $("#weekpicker").val().replace(/ .*/,'');
    if (dt!=''){
      $("#loader").show();
     $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
          dt:dt,
          operation:'show_masters_by_city'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $("#loader").hide();
          $('body').css('cursor','default');
          $('#user_block').html(html);
        },
        error:function(html){
          $("#loader").hide();
          $('body').css('cursor','default');
          alert('Ошибка соединения!');
        }
      }); 
   }else{
    $('#user_block').html('');
   }
  }
<?php }else{ ?>
function show_user_block(dt_from,dt_to){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      id:<?=$id?>,
      dt_from:dt_from,
      dt_to:dt_to,
      razdel:<?=$_REQUEST['razdel']?>,
      operation:'show_masters'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#user_block').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
  }); 
}
<?}?>
function save_outcome(){
    big_arr = [];
    $('.outcome').each(function(k,o){
        arr = {};
        arr['id'] = o.id.replace('outcome','');
        arr['outcome'] = o.value;
        o1 = $(o).parent().parent().find('.course').get(0);
        if (o1!=null){
            arr['course'] = o1.value;
        }
        big_arr[k] = arr;
    });
    big_arr1 = [];
    $('.s_outcome').each(function(k,o){
        arr = {};
        arr['id'] = o.id.replace('s_outcome','');
        arr['s_outcome'] = o.value;
        big_arr1[k] = arr;
    });
    big_arrvk = [];
    $('.outcomevk').each(function(k,o){
        arr2 = {};
        arr2['id'] = o.id.replace('outcomevk','');
        arr2['outcomevk'] = o.value;
        o1 = $(o).parent().parent().find('.vkwork').get(0);
        if (o1!=null){
            arr2['vkwork'] = o1.value;
        }

        o2 = $(o).parent().parent().find('.vkworkbudg').get(0);
        if (o2!=null){
            arr2['vkworkbudg'] = o2.value;
        }


        big_arrvk[k] = arr2;
    });
    var vkbudgezh=document.getElementById('budgetvk').value;
    var vkrabota=document.getElementById('rabotavk').value;
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
            dt:$("#dt").val(),
            outcome: big_arr,
            s_outcome: big_arr1,
            outcomevk : big_arrvk,
            vkbudgezh:vkbudgezh,
            vkrabota:vkrabota,
            operation:'save_outcome',
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            alert("Изменения сохранены!");
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка сохранения!');
        }
    });
}
</script>
<?php
echo $InterFC->getTopBlockStart("T_M_Top_block_marketol");
echo $InterFC->GetMenue(2); ?>
<div class='user_block' style='position:relative;padding: 10px;background-color: #f2f2f2;width: 100%;'>
  <div style="text-align: center;">
<?php if(intval($_REQUEST['razdel'])==0 || intval($_REQUEST['razdel'])==2){ ?>
<a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
<a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
<?}else{?>
Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW'))-4*7*3600*24);?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24)?>'>
<div class="legend-controls">
    <div style='background-color:#558ED5;' class="legend">
        <input type='checkbox' id='all_ch_graph5' checked/>
        <label for='all_ch_graph5'>
            Новые контакты
        </label>
    </div>
    <div style='background-color:#95B3D7;' class="legend">
        <input type='checkbox' id='all_ch_graph6' checked/>
        <label for='all_ch_graph6'>
            Записи
        </label>
    </div>
    <div style='background-color:#F79646;' class="legend">
        <input type='checkbox' id='all_ch_graph7' checked/>
        <label for='all_ch_graph7'>
            Пришедшие
        </label>
    </div>
    <div style='background-color:#C4C4C4;' class="legend">
        <input type='checkbox' id='all_ch_graph9' checked/>
        <label for='all_ch_graph9'>
            Особые события
        </label>
    </div>
    <div style='clear:both'>
    </div>
</div>

<script>
  $(document).ready(function(){
    $("input[id^='all_ch_graph']").on('click', function(){
      var idName = $(this).prop('id').substring(4, 13);
      var inputs = $("input[id^='" + idName + "']");
      inputs.click();
    });
  })
</script>
<?}?>
  </div>
  <div style='clear:both;'></div>
</div>
<?php echo $InterFC->getTopBlockEnd(); ?>
<br><br><br><br><br>
<div id='user_block' class='flex_left' style="margin: 0px auto auto auto;width:1200px;">
<?if(intval($_REQUEST['razdel'])==0){?>
  <?=show_masters($id,date("Y-m-d", strtotime(date('o-\\WW'))))?>
<?php }elseif($_REQUEST['razdel']==2){?>
  <script>
  $(document).ready(function(){
    show_masters_by_city();
  });
  </script>
<?php }else{ ?>
  <?=show_masters($id,date("Y-m-d", strtotime(date('o-\\WW'))-4*7*3600*24),date("Y-m-d", strtotime(date('o-\\WW'))-3600*24))?>
<?php }?>
</div>

<div id="loader">
  <img src="img/loader.gif" alt="" />
</div>
</body>
</html>
<?php
function show_masters($id,$dt,$dt_to=''){
    $marketolog=new marketolog($dt);
    if (intval($_REQUEST['razdel'])==0){ echo $marketolog->showTopBlock($id);
        $slider=new CompRebuild(1);
        $slider->set_dt($slider->get_mondayPar($dt));
        echo "<br>";
        echo $slider->initStyles();
        echo $slider->DrawSlider();
  $qc = "select * from m_city";
  $rc = mysql_query($qc);
  while ($ac = mysql_fetch_array($rc)){
    $city_id = intval($ac['id']);
    $city_name = $ac['name'];
    $q = "select m.id,m.use_course,u.name,w.outcome,w.course,w.paid from users u join masters m on u.id=m.id_master and u.type=0 and m.shown=1 and m.id_marketolog=$id and m.id_m_city=$city_id left join master_week w on m.id=w.id_master and w.dt='$dt' order by m.sort";
    $r = mysql_query($q);
    if (mysql_num_rows($r)>0){
      ?>
      <h5><?=$city_name?></h5>
      <div style="border:1px solid;padding:30px 100px 30px 40px;margin-bottom:20px;">
        <div style="width:500px;float:left;">
          <?php
            while ($a = mysql_fetch_array($r)){
              $m_id = $a['id'];
              print show_master_analitics($m_id,$dt);   
            }
          ?>
        </div>
        <div style="float:right;width:500px;">
          <div style='float:left;margin-left:40px;'>
                    <table frame='none' rules='void'>
              <?php
                $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
              
              ?>
                    <tr>
                    <td style='white-space:nowrap;padding-bottom:15px; width: 240px;'>Новые контакты</td>
              <?php
                for ($i = 1;$i<count($arr);$i++){
              ?>
                  <td align='center' style='width:40px;padding-bottom:15px;' class='header'><?=$arr[$i]?></td>
              <?php
                }
              ?>
					<td align='center' style='width:40px;padding-bottom:15px;font-weight:bold;' class='header'>Общ</td>
                    </tr>
                    <tr>
                    <td></td>
              <?php
                $q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
                $r1 = mysql_query($q);
                $a1 = mysql_fetch_array($r1);
                $isOnPercent = intval($a1['count']) > 0;
            
                $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'-interval 1 day";
                $r1 = mysql_query($q);
                $a1 = mysql_fetch_array($r1);
                $chats_old = $a1['chats'];
              
              $new_chats = [];
              $total_week_new_charts = 0;
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
                  $total_week_new_charts += $new_chats[$i]; ?>
              
                  <td style='text-align: center;'><?=$new_chats[$i]?></td>
                  <?php } ?>
                    <td style='text-align: center;font-weight:bold;'><?=$total_week_new_charts?></td>
                    </tr>
                    </table>
              <br><br>
              <table frame='none' rules='void'>
                  <?php
                  $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс');

                  ?>
                  <tr>
                      <td style='white-space:nowrap;padding-bottom:15px; width: 240px;'>Новые контакты Direct</td>
                      <?php
                      for ($i = 1;$i<count($arr);$i++){
                          ?>
                          <td align='center' style='width:40px;padding-bottom:15px;' class='header'><?=$arr[$i]?></td>
                          <?php
                      }
                      ?>
                      <td align='center' style='width:40px;padding-bottom:15px;font-weight:bold;' class='header'>Общ</td>
                  </tr>
                  <tr>
                      <td></td>
                      <?php
                      $q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
                      $r1 = mysql_query($q);
                      $a1 = mysql_fetch_array($r1);
                      $isOnPercent = intval($a1['count']) > 0;

                      $q = "select chats from m_city_day where id_m_city=$city_id and dt='$dt'-interval 1 day";
                      $r1 = mysql_query($q);
                      $a1 = mysql_fetch_array($r1);
                      $chats_old = $a1['chats'];

                      $new_chats = [];
                      $total_week_new_charts = 0;
                      for($i=1;$i<=7;$i++){
                          $i1 = $i-1;
                          $q = "select lidfit from m_city_day where id_m_city=$city_id and dt='$dt'+interval $i1 day";
                          $r1 = mysql_query($q);
                          $a1 = mysql_fetch_array($r1);
                          $chatslf = $a1['lidfit'];
                          $total_week_new_charts+=(int)$chatslf;
                          ?>

                          <td style='text-align: center;'><?=$chatslf?></td>
                      <?php } ?>
                      <td style='text-align: center;font-weight:bold;'><?=$total_week_new_charts?></td>
                  </tr>
              </table>
              <br><br>
              <table frame='none' rules='void'>
                  <?php
$arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
?>
                  <tr>
                      <td style='white-space:nowrap;padding-bottom:15px; width: 200px;'>Новые контакты из ВК</td>
                      <?php
for ($i = 1;$i<count($arr);$i++){
    ?>
    <td align='center' style='width:40px;padding-bottom:15px;' class='header'><?=$arr[$i]?></td>
    <?php
}
?>
                      <td align='center' style='width:40px;padding-bottom:15px;font-weight:bold;' class='header'>Общ</td>
                  </tr>
                  <tr>
                      <td></td>
                      <?php
$q = "select count(*) count from masters where id_m_city=$city_id and by_percent = 1";
$r1 = mysql_query($q);
$a1 = mysql_fetch_array($r1);
$isOnPercent = intval($a1['count']) > 0;
$q = "select chatsvk from m_city_day_vk where id_m_city=$city_id and dt='$dt'-interval 1 day";
$r1 = mysql_query($q);
$a1 = mysql_fetch_array($r1);
$chats_old = $a1['chatsvk'];
$new_chats = [];
$total_week_new_charts = 0;
for($i=1;$i<=7;$i++){
$i1 = $i-1;
$i2 = $i-2;
$q = "select chatsvk from m_city_day_vk where id_m_city=$city_id and dt='$dt'+interval $i1 day";
$r1 = mysql_query($q);
$a1 = mysql_fetch_array($r1);
$chats = $a1['chatsvk'];
$q = "select chatsvk from m_city_day_vk where id_m_city=$city_id and dt='$dt'+interval $i2 day";
$r1 = mysql_query($q);
$a1 = mysql_fetch_array($r1);
$chats_old = $a1['chatsvk'];
$diff = $chats;
$diff = ($diff < 0) ? 0 : $diff;
$new_chats[$i] = $diff;
$GLOBALS['stats']['cities'][$city_id]['City_Contacts'] += intval($new_chats[$i]);
$current_dt = date('Y-m-d', strtotime($dt) + 60*60*24*($i-1));
if ($chats>$chats_max)$chats_max = $chats;
$total_week_new_charts += $new_chats[$i]; ?>

    <td style='text-align: center;'><?=(int)$new_chats[$i]?></td>
<?php } ?>
                      <td style='text-align: center;font-weight:bold;'><?=$total_week_new_charts?></td>
                  </tr>
              </table>
                  </div>
        </div> 
      </div>
  <?php
    }
  }
}else{
  $q = "select m.id,m.use_course,u.name,w.outcome,w.course,w.paid from users u join masters m on u.id=m.id_master and u.type=0 and m.shown=1 and m.id_marketolog=$id left join master_week w on m.id=w.id_master and w.dt='$dt' order by m.sort";
  $r = mysql_query($q);
  while ($a = mysql_fetch_array($r)){
    $m_id = $a['id'];
    print show_master_graph($m_id,$dt,$dt_to);
  }
}

if (intval($_REQUEST['razdel'])==0){
    echo $marketolog->showEzhBlock($id);
    echo $marketolog->showButtonsave();
    }
}
function show_master_analitics($m_id,$dt){
  $q = "select m.use_course,m.currency_id,u.name,w.outcome, w.outcomevk, w.outcomeworkvk, m.usevk,w.course,w.paid, w.budgetvk from users u join masters m on u.id=m.id_master and u.type=0 and m.shown=1 and m.id=$m_id left join master_week w on m.id=w.id_master and w.dt='$dt'";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);

  $m_name = $a['name'];
    $outcomeworkvk=$a['outcomeworkvk'];
    $budgetvkk=$a['budgetvk'];
  $m_outcome = $a['outcome'];
  $m_outcomevk = $a['outcomevk'];
//  if ($m_outcome==0)$m_outcome = '';
  $m_course = $a['course'];
  $use_course = $a['use_course'];
    $use_vk = $a['usevk'];
    $currency_id = $a['currency_id'];
  $paid = $a['paid'];
?>
<div style="margin:10px 0;  border: 1px solid; padding: 10px; border-color: burlywood;">
  <div style='float:left;width:200px;'><b><?=$m_name?></b></div>
  <div style='float:left;'>
Расходы <input type='text' style="width: 40px; margin-right: 7px;" class='outcome' id='outcome<?=$m_id?>' value='<?=htmlspecialchars($m_outcome)?>'<?if($paid==1){?> disabled<?}?>>
  </div>
    <div style='float:left;'>
          Расходы ВК <input type='text' style="width: 40px;" class='outcomevk' id='outcomevk<?=$m_id?>' value='<?=htmlspecialchars($m_outcomevk)?>' disabled>
    </div>
<?php
  if($use_course==1){
?>
  <div style='clear:both;'></div>
  <div style='margin-top:10px; float: left;' data-currency-id="<?=$currency_id?>">
Курс <input type='text' class='course' style="width: 40px; margin-right: 10px;" id='course<?=$m_id?>' value='<?=htmlspecialchars($m_course)?>'>
  </div>

<?
  }
?>
    <?php
    if($use_vk==1){
        ?>
        <div style=''></div>
        <div style='margin-top:10px; margin-left: 189px; float: left; clear: both;' id="vkr<?=$m_id?>"; data-vk-id="vkvork<?php if ((int)$outcomeworkvk>0) echo 'xxx'; ?>">
            ВК работа <input type='text' onblur="" class='vkwork' style="width: 40px;" id='wkwork<?=$m_id?>' value='<?=$outcomeworkvk?>'>
        </div>
        <div style='margin-top:10px; margin-left: 11px; float: left;' id="vkr<?=$m_id?>"; data-vk-id="vkvork88<?php if ((int)$outcomeworkvk>0) echo 'xxx'; ?>">
            <span>Бюджет ВК </span><input type='text' onblur="" class='vkworkbudg' style="width: 40px;" id='wkworkbudg<?=$m_id?>' value='<?=$budgetvkk?>'>
        </div>
        <?
    }
    ?>
  <div style='clear:both;'></div>
</div>
<?
}
function show_master_graph($m_id,$dt,$dt1){
  $q = "select u.name from users u join masters m on u.id=m.id_master and u.type=0 and m.id=$m_id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_name = $a['name'];

  ob_start();

  $m = array();
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1])-3600*24*7;
  $dt_from = date("d.m.Y",$t);
  preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt1,$m);
  $t = mktime(0,0,0,$m[2],$m[3],$m[1]);
  $dt_to = date("d.m.Y",$t);
?>
<div style="padding-left:10px;">
  <b><?=$m_name?></b>
</div>

<div style="border:1px solid;padding-bottom:20px;margin-top:15px;margin-bottom:10px;"> 

  <div style="width:95%;margin-left:auto;margin-right:auto;margin-bottom:10px;">

    <div id="div_graph2<?=$m_id?>" style="margin-top:10px;margin-bottom:10px;">
      <img style="width:100%" src="/graph2.php?id=<?=$m_id?>&dt_from='<?=urlencode($dt_from)?>'&dt_to='<?=urlencode($dt_to)?>'&salt='<?=rand()?>'">
    </div>

    <div style="padding-left:50px;padding-top:30px;padding-bottom:30px;">

   <div style="background-color:#558ED5;" class="legend">
         <input type="checkbox" id="ch_graph5_<?=$m_id?>" class="ch_graph1" checked onchange="show_graph2(<?=$m_id?>,'<?=urlencode($dt_from)?>','<?=urlencode($dt_to)?>')">
         <label for="ch_graph5_<?=$m_id?>">Новые контакты</label>
   </div>

   <div style="background-color:#95B3D7;" class="legend">
         <input type="checkbox" id="ch_graph6_<?=$m_id?>" class="ch_graph2" checked onchange="show_graph2(<?=$m_id?>,'<?=urlencode($dt_from)?>','<?=urlencode($dt_to)?>')">
         <label for="ch_graph6_<?=$m_id?>">Записи</label>
   </div>

   <div style="background-color:#F79646;" class="legend">
         <input type="checkbox" id="ch_graph7_<?=$m_id?>" class="ch_graph3" checked onchange="show_graph2(<?=$m_id?>,'<?=urlencode($dt_from)?>','<?=urlencode($dt_to)?>')">
         <label for="ch_graph7_<?=$m_id?>">Пришедшие</label>
   </div>

   <div style="background-color:#C4C4C4;" class="legend">
         <input type="checkbox" id="ch_graph9_<?=$m_id?>" class="ch_graph9" checked onchange="show_graph2(<?=$m_id?>,'<?=urlencode($dt_from)?>','<?=urlencode($dt_to)?>')">
         <label for="ch_graph9_<?=$m_id?>">Особые события</label>
   </div>

      <div style="clear:both"></div>

    </div>
  </div>
</div>
<script type="text/javascript">
  function show_graph2(m_id,dt1,dt2){
    obj = $('#div_graph2'+m_id).next();
    url = '/graph2.php?id='+m_id+'&dt_from='+dt1+'&dt_to='+dt2+'&salt='+Math.random();
    if (obj.find('.ch_graph1').length && !obj.find('.ch_graph1').get(0).checked){
      url += '&nodata=1';
    }
    if (obj.find('.ch_graph2').length && !obj.find('.ch_graph2').get(0).checked){
      url += '&nodata1=1';
    }
    if (obj.find('.ch_graph3').length && !obj.find('.ch_graph3').get(0).checked){
      url += '&nodata2=1';
    }
    if (obj.find('.ch_graph4').length && !obj.find('.ch_graph4').get(0).checked){
      url += '&nodata3=1';
    }
    $('#div_graph2'+m_id).find('img').get(0).src=url;
  }
</script>
<?
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
?>