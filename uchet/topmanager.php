<?
$AJAX_TIMEOUT = 20000;
$operation = $_POST['operation'];$GLOBALS['stats'] = [
  'global' => [],
  'cities' => []
];
if ($operation=='show_master'){
  if($_REQUEST['razdel']==0){
    $id = intval($_POST['id']);
    $manager = intval($_POST['manager']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    $masters = show_masters_by_city($id,$manager,$dt);
    $stats = show_all_statistics();
    $html = $stats . $masters;
    //var_dump($html);die();
    print $html;
  }else{
    $id = intval($_POST['id']);
    $manager = intval($_POST['manager']);
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
    print show_masters($id,$manager,$dt_from,$dt_to);
  }
  exit;
}
if ($operation=='show_common'){
  $id = intval($_POST['id']);
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt);
  $dt = str_replace('"','',$dt);
  $dt = str_replace("'",'',$dt);
  $m = array();
  preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
  $dt = $m[3].'-'.$m[2].'-'.$m[1];
  print show_common($id,$dt);
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
<style>
.p_input{
  width:50px;
}
</style>
<script type="text/javascript">
$(function() {

    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('#weekpicker').datepicker('widget').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }
    
<?if($_REQUEST['razdel']==0){?>
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('#weekpicker').datepicker('widget').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }
    
    $('#weekpicker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $('#weekpicker').val($.datepicker.formatDate( dateFormat, startDate, inst.settings )+' - '+$.datepicker.formatDate( dateFormat, endDate, inst.settings ));
            show_user_block();
            show_common_block();
           
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
        show_user_block();
        show_common_block();

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
        show_user_block();
        show_common_block();

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
<?if($_REQUEST['razdel']==0){?>
function show_user_block(){
  dt = $("#weekpicker").val().replace(/ .*/,'');
  if (dt!=''){
    $("#loader").show();
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        dt:dt,
        id:<?=$id?>,
        manager:$('#sel_manager').val(),
        operation:'show_master'
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
<?}else{?>
function show_user_block(dt_from,dt_to){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      id:<?=$id?>,
      dt_from:dt_from,
      dt_to:dt_to,
      manager:$('#sel_manager').val(),
      razdel:<?=$_REQUEST['razdel']?>,
      operation:'show_master'
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
function show_common_block(){
  dt = $("#weekpicker").val().replace(/ .*/,'');
  if (dt!=''){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        id:<?=$id?>,
        dt:dt,
        operation:'show_common'
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('body').css('cursor','default');
        $('#common_block').html(html);
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка соединения!');
      }
    }); 
  }else{
    $('#common_block').html('');
  }
}
function save_master(id){
  data = {};
  $('#master'+id+' .p_input').each(function(k,o){
    o_id = o.id;
    val = o.value;
    data[o_id] = val;
  });
  data['operation'] = 'save_master';
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#master'+id).html(html);
      show_common_block();
      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  }); 
}
function chose_manager(){
<?if($_REQUEST['razdel']==0){?>
  show_user_block();
<?}else{?>
  show_user_block($('#dt_from').val(),$('#dt_to').val());
<?}?>
}
</script>
<div style="position: fixed;width: 100%;z-index: 999;">
<div id='menu' style='padding:10px;background-color:white;'>
<span>
<?if ($_REQUEST['razdel']==0){?>
<b style='border-bottom:1px solid;'>Аналитика</b>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='index.php'>Аналитика</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<?if ($_REQUEST['razdel']==1){?>
<b style='border-bottom:1px solid;'>Диаграммы</b>
<input type='hidden' id='dt_from' value='<?=date("Y-m-d", strtotime(date('o-\\WW'))-3*7*3600*24);?>'>
<input type='hidden' id='dt_to' value='<?=date("Y-m-d", strtotime(date('o-\\WW'))+3600*24*6)?>'>
<?}else{?>
<a style='border-bottom:1px solid;text-decoration:none;' href='index.php?razdel=1'>Диаграммы</a>
<?}?>
</span>
<span style='margin-left:10px;'>
<?php if ($_REQUEST['razdel']==77){?>
    <b style='border-bottom:1px solid;'>Диаграмма чатов</b>
<?php }else{ ?>
    <a style='...' href='/diagramchats.php'>Диаграмма чатов</a>
<?php } ?>
 </span>
<div style='position:absolute;top:10px;right:20px;'><a href='?logout=1' style='padding-left:200px;'>Выход</a></div>
</div>
<div class='user_block' style='position:relative;padding: 10px; background-color: #f2f2f2;'>
	 <div>
        <span style='padding-right:20px;'>
        <select id='sel_manager' onchange='chose_manager(this.id)'>
        <option value='0'>Показать всех</option>
        <?php
        $r = mysql_query("select * from users where type=1");
        while($a = mysql_fetch_array($r)){
          $man_id = $a['id'];
          $man_name = $a['name'];
        ?>
        <option value='<?=$man_id?>'><?=$man_name?></option>
        <?php
        }
        ?>
        </select>
        </span>
        <?php if(intval($_REQUEST['razdel'])==0){?>
        <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
        Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
        <a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
        <div style='display:inline-block' id='common_block'><?=show_common($id,date("Y-m-d", strtotime(date('o-\\WW'))))?></div>
        <?php }else{ ?>
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
		 <?php } ?>
  </div>
  <div style='clear:both;'></div>
</div> 
</div>
<?if(intval($_REQUEST['razdel'])==0){?>
<div id='oper_status' style='color:green;text-align:center;height:20px;'></div>
  <div id='user_block' class='user_block' style="margin-top: 100px;">
  <script>
    $(document).ready(function(){
      show_user_block();
    });
  </script>
</div>
<?}else{?>
<div id='user_block' class='user_block' style="padding-top: 100px;"><?=show_masters($id,0,date("Y-m-d", strtotime(date('o-\\WW'))-4*7*3600*24),date("Y-m-d", strtotime(date('o-\\WW'))-3600*24))?></div>
<?}?>
<div id="loader">
  <img src="img/loader.gif" alt="" />
</div>
</body>
</html>
<?
function show_common($id,$dt){
  ob_start();

  $r_tm = mysql_query("select * from topmanagers where id_user=$id");
  if (mysql_num_rows($r_tm)>0){
    $a_tm = mysql_fetch_array($r_tm);
    $tm_bonus1 = $a_tm['bonus1'];
    $tm_bonus2 = $a_tm['bonus2'];
  }else{
    $tm_bonus1 = 0;
    $tm_bonus2 = 0;
  }
 
  $q = "SELECT sum(w.visitors*if(p.count_in_scores=1,$tm_bonus1,$tm_bonus2)) sum_bonus FROM procedures p left join master_procedure_week w on p.id=w.id_procedure and w.id_master=p.id_master left join masters m on p.id_master=m.id where m.id_topmanager=$id and w.dt='$dt'";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $sum_bonus = intval($a['sum_bonus']);
?>
Всего бонусов за неделю: <b><?=$sum_bonus?></b>
<?

  $q = "SELECT count(*) cnt FROM masters m left join master_week w on m.id=w.id_master and w.dt='$dt' where m.id_topmanager=$id and (paid=0 or paid is null)";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $cnt_paid = $a['cnt'];
  if($cnt_paid==0){?>
<div style='background-color:green;color:white;display:inline-block;padding:5px;margin-left:5px;'>Оплачено</div>
<?
  }
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}

function show_master_graph($id,$dt,$dt1){
  $q = "select m.id,u.name from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_id = $a['id'];
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
<?php
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
function show_all_statistics(){
   ob_start(); 
   $GLOBALS['stats']['global']['procent']['City_Efficiency'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Contacts'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Master_Records'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Extras_Records'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Visitors'] = 0;
   $GLOBALS['stats']['global']['procent']['City_Bonuses'] = 0;

   $GLOBALS['stats']['global']['noprocent']['City_Efficiency'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Contacts'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Master_Records'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Extras_Records'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Visitors'] = 0;
   $GLOBALS['stats']['global']['noprocent']['City_Bonuses'] = 0;
   $i = 0;
    foreach ($GLOBALS['stats']['cities'] as $key => $city) {
      $i++;
      $procent = (isset($city['OnProcent']) && $city['OnProcent']) ? 'procent' : 'noprocent';

     $GLOBALS['stats']['global'][$procent]['City_Contacts'] += $city['City_Contacts'];
     $GLOBALS['stats']['global'][$procent]['City_Master_Records'] += $city['City_Master_Records'];
     $GLOBALS['stats']['global'][$procent]['City_Extras_Records'] += $city['City_Extras_Records'];
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
   }
   foreach ($arr as $p) {
   ?>

  <div id="statistics" style="width: 1200px; margin: 10px auto; padding: 10px; background-color: #eedbb4;">
    <table style="width: 100%;">
        <tr>
            <td style="width: 35%;font-size:20px;">
              <?=($p == 'procent') ? "%" : "";?>
                Результативность
                <strong id="City_Efficiency"><?=$GLOBALS['stats']['global'][$p]['City_Efficiency']?>%</strong> 
            </td>
            <td style="text-align: center;">
                Контакты
                <br/>
                <strong id="City_Contacts"><?=$GLOBALS['stats']['global'][$p]['City_Contacts']?></strong>
            </td>
            <td style="text-align: center;"> 
                Основные записи: 
                <br/>
                <strong id="City_Master_Records"><?=$GLOBALS['stats']['global'][$p]['City_Master_Records']?></strong>
            </td>
            <td style="text-align: center;">
                Доп. записи
                <br/>
                <strong id="City_Extras_Records"><?=$GLOBALS['stats']['global'][$p]['City_Extras_Records']?></strong>
            </td>
            <td style="text-align: center;">
                Пришедшие
                <br/>
                <strong id="City_Visitors"><?=$GLOBALS['stats']['global'][$p]['City_Visitors']?></strong>
            </td>
            <td style="text-align: center;">
                Бонусы
                <br/>
                <strong id="City_Bonuses"><?=$GLOBALS['stats']['global'][$p]['City_Bonuses']?></strong>
            </td>
        </tr>
    </table>  
  </div>
   <?php
   }
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
function show_city_statistics($city_id,$dt){
   ob_start();
   if ($GLOBALS['stats']['cities'][$city_id]['City_Contacts'] == 0){
      $GLOBALS['stats']['cities'][$city_id]['City_Efficiency'] = 0;
   }else{
      $GLOBALS['stats']['cities'][$city_id]['City_Efficiency'] = round($GLOBALS['stats']['cities'][$city_id]['City_Master_Records']*100/$GLOBALS['stats']['cities'][$city_id]['City_Contacts']);
   }
   ?>       
            <div style="position:absolute;top:60px;left:0;width:1178px;padding:10px;border:1px solid black;border-top:0;background-color:white;">
              <table style='width: 100%;'>
                    <tr>
                        <td style="width: 35%;font-size:20px;">
                            Результативность
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
                                <?=(($GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'] == null) ? 0 : $GLOBALS['stats']['cities'][$city_id]['City_Extras_Records'])?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Пришедшие
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_Visitors']?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            Бонусы
                            <br/>
                            <strong>
                                <?=$GLOBALS['stats']['cities'][$city_id]['City_Bonuses']?>
                            </strong>
                        </td>
                    </tr>
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
  $sum_no_self = $master_week_array['sum_no_self'];


  /*$extra_procedure_summ = '';
  $eps = mysql_query("SELECT summ FROM master_extra_procedure where master_id = $m_id and dt = '$dt' LIMIT 1");
  if (mysql_num_rows($eps) > 0){
    $extra_procedure_summ = mysql_fetch_array($eps)['summ'];
  }*/
  ?>

            <h3>
                <?= $master_data['name']?>
  <?php 
  $current_dt = $dt;
  $week_ago_dt = date("Y-m-d", strtotime($dt) - (60*60*24*7));
  $tuesday = date("Y-m-d", strtotime($dt) + (60*60*24));
  $_q = "select bill_checked from master_week where id_master=$m_id and dt='$week_ago_dt'";
  $_r = mysql_query($_q);
  $_a = mysql_fetch_array($_r);
  $bill_checked = $_a['bill_checked'];
 
  /** Проверка комиссии за предыдущую неделю **/
  $procedures_query = "SELECT sum(p.comission*w.visitors) sum_comission FROM procedures p left join master_procedure_week w on p.id=w.id_procedure and w.id_master=p.id_master where p.id_master=$m_id and w.dt='$week_ago_dt'";
  $procedures_resources = mysql_query($procedures_query);
  $procedures_array = mysql_fetch_array($procedures_resources);
  $sum_comission = intval($procedures_array['sum_comission']);
  $label = "";
  if ( (strtotime(date("Y-m-d")) >= strtotime($tuesday))){
    if ($bill_checked != 2 && $sum_comission > 0){
      $label = "<span style='color: #FFF; background-color: red; padding: 5px; display: inline-block; float: right; font-size:11px;'>Прошлая неделя не оплачена</span>";
    }
    if ($bill_checked == 1){
      $label = "<span style='color: #FFF; background-color: orange; padding: 5px; display: inline-block; float: right; font-size:11px;'>Чек загружен</span>";
    }
    if ($bill_checked == 2){
      $label = "";
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
                            <td style="padding-bottom:30px; border-right:1px solid black;" width="200">
                                Процедуры
                            </td>
                            <td style="padding-bottom:30px; border-right:1px solid black;" class="header" align="center">
                                Запись
                            </td>
                            <td style="padding-bottom:30px;" class="header" align="center">
                                Пришедшие
                            </td>
                            <td style="padding-bottom:30px;" class="header" align="center">
                                Бонусы
                            </td>
                        </tr>
  <?php
    $records_week_sum = 0;
    $visitors_week_sum = 0;
    $sum_bonus_week_sum = 0;
    $send_mail_flag = 1;
    $r = mysql_query("select * from procedures where id_master=$m_id");
    while ($a = mysql_fetch_array($r)){
      $p_id = $a['id'];
      $p_name = $a['name'];
      $p_bonus = $a['bonus'];
      $p_count_in_scores = intval($a['count_in_scores']);


      $q1 = "select records from master_procedure_day where id_master=$m_id and id_procedure=$p_id and dt='$dt'+interval 6 day";
      $r1 = mysql_query($q1);
      $a1 = mysql_fetch_array($r1);
      $records = $a1['records'];

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
  ?>
                        <tr data-id=<?=$p_id?>>
                            <td style="padding-bottom:30px; border-right:1px solid black;" width="150">
                                <b>
                                    <?=$p_name?>
                                </b>
                            </td>
                            <td style="padding-bottom:30px;padding-right:10px;padding-left:10px; border-right:1px solid black;">
                                <input type="text" style="width: 30px;" value="<?=$records?>" disabled/>
                                <b>
                                    <?=$records_week?><b> шт</b>
                                </b>
                            </td>
                            <td style="padding-bottom:30px;padding-right:10px;padding-left:10px;">
                                <input type="text" style="width: 30px; " value="<?=$visitors?>" class='p_input p_input<?=$m_id?>'  disabled/>
                                шт
                            </td>
                            <td style="padding-bottom:30px;padding-right:10px;" align="center">
                                <b>
                                    <?=$sum_bonus?>
                                </b>
                            </td>
                        </tr>
  <?php 
    }
    $GLOBALS['stats']['cities'][$city_id]['City_Visitors'] += $visitors_week_sum;
  ?>
                        <tr>
                            <td style="border-top: 1px solid black; text-align: center;">
                              <b>Итого:</b>
                            </td>
                            <td style="border-top: 1px solid black; text-align: center;">
                              <?=$records_week_sum?>
                            </td>
                            <td style="border-top: 1px solid black; text-align: center;">
                              <?=$visitors_week_sum?>
                            </td>
                            <td style="border-top: 1px solid black; text-align: center;">
                              <?=$sum_bonus_week_sum?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p>
                  <span style="margin-right: 20px;">Сумма доп. процедур</span>
                  <span><input type="text" name="extra_procedure_summ" value="<?= $extra_procedure_summ ?>" disabled/></span>
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
                  <b>Сумма за неделю (без себестоимости)</b>
                  <input type='text' style='width:100px;' class='p_input' value='<?=$sum_no_self?>'  disabled/>
                </div>
              </div>
  <?php
  }
  ?>

              <div>
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
                    <input type='hidden' value='<?=$chats_old?>' disabled>
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
                    <td style="text-align: center;"><input type="text" style="width: 50px;" value='<?=$chats?>' class='p_input' disabled></td>
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
                      Погрешность <input type="text"  value='<?=$other_contacts?>' disabled>
                    </td>
                    <td> 
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align: right;">Прирост</td>
  <?php
  for($i=1;$i<=7;$i++){
  ?>
                    <td style="text-align: center;" id='contacts<?=$city_id?>_<?=$i?>' class='p_input'><?=$new_chats[$i]?></td>
  <?php
  }
  ?>
                  </tr>
                </table>
              </div>
  <?php
    $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
function show_masters_by_city($topmanager_id,$manager_id,$dt){
  ob_start();
  $cities = mysql_query("SELECT * FROM `m_city`");
  while ($city = mysql_fetch_array($cities)){ 
    $city_id = $city['id'];

    ?>
    <?php 
        $masters_query = "select u.*, m.id_master from users u,masters m where u.type=0 and m.shown=1 and u.id=m.id_master and m.id_topmanager=$topmanager_id and id_m_city=$city_id";
        if ($manager_id>0) $masters_query .= " and m.id_manager=$manager_id ";
        $masters_query .= " order by m.sort";
        //var_dump($masters_query);die();
        $masters_resource = mysql_query($masters_query);
        if (mysql_num_rows($masters_resource) > 0){
    ?>
        <section class="city" style="width: 1200px;position:relative;" data-id="<?=$city_id?>">
            <h3>
                <?=$city['name']?>
            </h3>
            <div style="border:1px solid black; padding: 60px 10px 10px 10px; margin-bottom: 20px;">
              <div style='overflow-x:auto;'>
                <table><tr>
                <?php 
                    while ($masters_array = mysql_fetch_array($masters_resource)){
                      $master_id = $masters_array['id'];
                ?>

                    <td style='vertical-align:top;'><div id="master<?=$master_id?>" style=" width: 500px;float:left; padding:10px;">
                <?php
                      print show_master($master_id,$dt);
                ?>
                    </div></td>
                <?php
                    }
                ?>
                </tr>
              </table>
              </div>
              <div style="clear:both;"></div>
              <?= show_contacts($city_id,$dt) ?>
              <?= show_city_statistics($city_id,$dt) ?>
            </div>
        </section>

  <?php }
  }
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
function show_masters($id,$manager,$dt,$dt_to=''){
  ob_start();
  $q = "select u.* from users u,masters m where u.type=0 and u.id=m.id_master and m.shown=1 and m.id_topmanager=$id ";
  if ($manager>0) $q .= " and m.id_manager=$manager ";
  $q .= " order by m.sort";
  $r = mysql_query($q);
  while ($a = mysql_fetch_array($r)){
    $u_id = $a['id'];
?>
<div id='master<?=$u_id?>' style='padding-bottom:20px;'><?=show_master_graph($u_id,$dt,$dt_to,$manager)?></div>
<?php
  }
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
?>