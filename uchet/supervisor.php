<?php
/**Глобальные переменные**/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$PHP_SELF = $_SERVER['PHP_SELF'];
$AJAX_TIMEOUT = 20000;
/**Роутинг**/
if (!isset($_REQUEST['razdel']))$_REQUEST['razdel']=3;
if (!isset($_REQUEST['podrazdel']))$_REQUEST['podrazdel']=1;
/**Клас интерфейса роутинга**/
$InterFC=new InterFC($_REQUEST['razdel']);
$usersCRM=new usersCRM();
$supervisorFace=new supervisorFace();
$m_city=new m_city();
/**Ловля события операции**/
$operation = "";
if (isset($_POST['operation'])){ $operation = $_POST['operation']; }

// methods
require $DOCUMENT_ROOT. '/components/supervisor/methods.php';
// functions
require $DOCUMENT_ROOT. '/components/supervisor/functions.php';
require ($_SERVER['DOCUMENT_ROOT']."/timurnf/operator_functions.php");
require $DOCUMENT_ROOT. '/components/manager/weekplan2.php';
switch ($operation) {
  case 'save_user':
                    if ((int)$_POST['use_course']>0) $cur=1; else $cur=0;
                    $id=$usersCRM->save_user(intval($_POST['id']), intval($_POST['bonus']), intval($_POST['active']), intval($_POST['procent']), intval($_POST['manageroperator']), $_POST['name'], $_POST['password'], intval($_POST['type']));
                    $id_master=$usersCRM->save_Master(intval($_POST['type']), $id, $_POST['email'], $_POST['id_manager'], $_POST['id_marketolog'], $_POST['id_uchenik'], $_POST['use_course'], $_POST['vorkvk'], $cur, $_POST['by_percent'], $_POST['percent_val'], $_POST['shown'], $_POST['id_m_city']);
                    $usersCRM->addProc(intval($_POST['type']), $id, $id_master, $_POST['proc']);
                    $m_city->UpdateCity($_POST['id_m_city'], $_POST['koef']); exit; break;
  case 'update_user_list':
    op_update_user_list(); // methods
    break;
  case 'show_masters':
    op_show_masters(); // methods
    break;
  case 'show_shops':
    op_show_shops(); // methods
    break;
  case 'pay_master':
    op_pay_master(); // methods
    break;
  case 'pay_vka': $payvkVsem=new payzp($_POST['dt'],intval($_POST['razdel']), $_SERVER['PHP_SELF']);
                  echo $payvkVsem->op_pay_vka($_POST['id'], $_POST['sum'], $_POST['paymentst']); exit; break;
  case 'pay_shop':
    op_pay_shop(); // methods
    break;
  case 'show_profit':
    op_show_profit(); // methods
    break;
  case 'up_master':
    op_up_master(); // methods
    break;
    case 'savemastersort':
        op_savemastersort(); // methods
        break;
  case 'down_master':
    op_down_master(); // methods
    break;
    case 'sortup':
        op_sortup(); // methods
        break;
    case 'update_shop_list':
    op_update_shop_list(); // methods
    break;
  case 'save_shop':
    op_save_shop(); // methods
    break;
  case 'bill_button':
    op_bill_button(); // methods
    break;
  case 'close_week':
    op_close_week(); // methods
    break;
  case 'update_pr_city':
    op_update_pr_city(); // methods
    break;
  case 'save_m_city': $supervisorFace->m_city->saveCity($_POST['id'], $_POST['name'], $_POST['procent']); exit; break;
  case 'return_proc':
        op_return_proc(); // methods
        break;
  case 'get_m_city_list':
    op_get_m_city_list(); // methods
    break;
  case 'get_costs':
    op_get_costs(); // methods
    break;
  case 'save_costs':
    op_save_costs(); // methods
    break;
  case 'delete_cost':
    op_delete_cost(); // methods
    break; 
  case 'get_masters_profit':
    op_get_masters_profit(); // methods
    break;
  case 'sendfile':
    op_sendfile(); // methods
    break; 
  case 'get_clean_profit_data_by_dates':
    op_get_clean_profit_data_by_dates(); // methods
    break; 
  case 'save_currency':
    op_save_currency(); // methods
    break;
  case 'show_masters_by_city':
    op_show_masters_by_city(); // methods
    break;
  case 'save_bonuses':
    op_save_bonuses(); // methods
    break;
    case 'save_bonusesezh':
        op_save_bonusesezh(); // methods
        break;
    case 'savebaseproc': $supervisorFace->bonus->updateBonus($_POST["baseproc"], $_POST["baseball"]); exit(); break;
  case 'remove_reward':
    op_remove_reward(); // methods
    break;
  case 'remove_rewardezh':
        op_remove_rewardezh(); // methods
        break;
  
  default:
    # code...
    break;
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
      <script type="text/javascript" src="/js/Chart.min.js"></script>
      <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
      <link rel="stylesheet" type="text/css" href="/js/daterangepicker.min.css">
      <link rel="stylesheet" type="text/css" href="/style.css">
      <script src="/timurnf/scripts/pays.js"></script>
      <script src="/timurnf/scripts/supervisorface.js"></script>
      <script src="/timurnf/scripts/cities.js"></script>
      <script>var disprocflag=0;</script>
  </head>
  <body>
<?php
echo $InterFC->getTopBlockStart();
echo $InterFC->GetMenue();
if ($_REQUEST['razdel']==1 || $_REQUEST['razdel']==2 || $_REQUEST['razdel']==4 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==6 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){
?>
<script type="text/javascript">
	
function show_graph1(m_id,dt1,dt2){
  obj = $('#div_graph1'+m_id).next();
  url = '/graph1.php?id='+m_id+'&dt_from='+dt1+'&dt_to='+dt2+'&salt='+Math.random();
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
  if (obj.find('.ch_graph8').length && !obj.find('.ch_graph8').get(0).checked){
    url += '&nodata4=1';
  }
  $('#div_graph1'+m_id).find('img').get(0).src=url;
}
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
  if (obj.find('.ch_graph9').length && !obj.find('.ch_graph9').get(0).checked){
    url += '&nodata3=1';
  }
  $('#div_graph2'+m_id).find('img').get(0).src=url;
}
	var isVisibleProfit = false;
	function setProfitVisible(){
		if (!isVisibleProfit){
			$("#div_profit").show();
			$("#main_block").css("padding-top","180px");
			$("#a_profit").html("Свернуть");
		}else{
			$("#div_profit").hide();
			$("#main_block").css("padding-top","160px");
			$("#a_profit").html("Развернуть");
		}
		isVisibleProfit = !isVisibleProfit;
	}
$(function() {
<?php if($_REQUEST['razdel']==2){?>
    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');
<?php } ?>
<?php if($_REQUEST['razdel']==1 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9 ){?>
    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))-3600*24*7);?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))-3600*24);?>');
<?php }
  if ($_REQUEST['razdel']==6){ ?>
    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');
<?php } ?>
<?php if($_REQUEST['razdel']==1 || $_REQUEST['razdel']==2 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==6 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){?>
    
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
			 t = startDate.getTime();
            d1 = new Date(t);
            var str1 = ('0'+d1.getDate()).slice(-2)+'.'+('0'+parseInt(d1.getMonth()+1)).slice(-2)+'.'+d1.getFullYear();
            $('#weekpicker').val($.datepicker.formatDate( dateFormat, startDate, inst.settings )+' - '+$.datepicker.formatDate( dateFormat, endDate, inst.settings ));
<?php if($_REQUEST['razdel']!=2){?>
            show_user_block();
<?php } ?>
<?php if($_REQUEST['razdel']==1){?>
            show_profit();
<?php }else if($_REQUEST['razdel']==2){?>
            show_masters_by_city(str1);
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
<?php if($_REQUEST['razdel']!=2){?>
        show_user_block();
<?php } ?>
<?php if($_REQUEST['razdel']==1){?>
        show_profit();
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
<?php if($_REQUEST['razdel']!=2){?>
            show_user_block();
<?php } ?>
<?php if($_REQUEST['razdel']==1){?>
        show_profit();
<?php }else if($_REQUEST['razdel']==2){?>
            show_masters_by_city(str1);
<?php } ?>
        return false;
    });

    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
<?php }else{ ?>  
    $('#weekpicker').dateRangePicker({
      format: 'DD.MM.YYYY',
      separator: ' - ',
      language: 'ru',
      startOfWeek: 'monday',
    }).bind('datepicker-apply',function(event,obj)
    {
      dt_from = obj.date1.getFullYear()+'-'+('0'+parseInt(obj.date1.getMonth()+1)).slice(-2)+'-'+('0'+obj.date1.getDate()).slice(-2);
      dt_to = obj.date2.getFullYear()+'-'+('0'+parseInt(obj.date2.getMonth()+1)).slice(-2)+'-'+('0'+obj.date2.getDate()).slice(-2); 
      show_user_block(dt_from,dt_to);
		
    <?php if ($_REQUEST['razdel']==4){?>
          setTimeout(function(){ getCleanProfitDataByDates(dt_from, dt_to)}, 2000);
      <?php } ?>
		
      $('#excel_export').get(0).href='export_masters.php?dt1='+dt_from+'&dt2='+dt_to;
    });
<?php } ?>
});
<?php if($_REQUEST['razdel']==1){?>
function show_profit(){
  $('body').css('cursor','wait');
  dt = $("#weekpicker").val().replace(/ .*/,'');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      dt:dt,
      operation:'show_profit'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#sum_profit').html(html); 
      if (isVisibleProfit){
        $("#div_profit").show();
      }else{
        $("#div_profit").hide();
      }
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
  }); 
}
function bill_button_click(id,dt,val){
  $.ajax({
    type:'POST',
    url:'<?=$_SERVER['PHP_SELF']?>',
    data:{
      'id':id,
      'dt':dt,
      'val':val,
      'operation':'bill_button'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      if (val=='1'){
        $('#bill_button'+id).removeClass('green');
        $('#bill_button'+id).addClass('orange');
        $('#bill_button'+id).val('Подтвердить');
        $('#bill_status'+id).val('2');
      }
      if (val=='2'){
        $('#bill_button'+id).removeClass('orange');
        $('#bill_button'+id).addClass('green');
        $('#bill_button'+id).val('Отменить');
        $('#bill_status'+id).val('1');
      }
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  });
}
function getMasterIdFromUrl(stringUrl){
  var url = new URL(stringUrl);
  return url.searchParams.get("m_id");
}
var xhr = new XMLHttpRequest();
//http://learn.javascript.ru/ajax-xmlhttprequest
xhr.timeout = 300000;
xhr.onloadstart = function() {
    document.getElementById("openfile").style.display = 'none';
    document.getElementById("abortfile").style.display = '';
}
xhr.onabort = function() {
    document.getElementById("openfile").style.display = '';
    document.getElementById("abortfile").style.display = 'none';
}
xhr.onloadend = function() {
    document.getElementById("openfile").style.display = '';
    document.getElementById("abortfile").style.display = 'none';
}
xhr.onload = function(data) {
    var id = getMasterIdFromUrl(data.target.responseURL);
    document.getElementById("picture_"+id).innerHTML = xhr.responseText;	
}
xhr.ontimeout = function() {
    alert("Загрузка занимает слишком много времени");
}
xhr.onerror = function() {
    alert("При загрузке произошла ошибка");
}

function sendfile(id_master, dt) { 
  var fileform = "fileform_"+id_master;
    var formData = new FormData(document.getElementById(fileform));
    formData.append("operation", "sendfile");
    formData.append("id_master", id_master);
    formData.append("dt", dt);
    xhr.open("POST", "<?=$_SERVER['PHP_SELF']?>?r=" + Math.random() + "&m_id="+id_master);
    xhr.send(formData);
}
<?php } ?>
<?php if($_REQUEST['razdel']==1 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){?>
function show_user_block(){
  $('body').css('cursor','wait');
  dt = $("#weekpicker").val().replace(/ .*/,'');
  if (dt!=''){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        dt:dt,
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
  }else{
    $('#user_block').html('');
  }
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
          //console.log(html);
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
<?php }else if ($_REQUEST['razdel']==6){?>
function show_user_block(){
  $('body').css('cursor','wait');
  dt = $("#weekpicker").val().replace(/ .*/,'');
  if (dt!=''){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        dt:dt,
        razdel:<?=$_REQUEST['razdel']?>,
        operation:'show_shops'
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
<?php } ?>
function pay(id,dt){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      operation:'pay_master',
      id:id,
      dt:dt
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#master'+id).html(html);
      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
  }); 
}

function pay_shop(id,dt){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      operation:'pay_shop',
      id:id,
      dt:dt
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#shop'+id).html(html);
      $('body').css('cursor','default');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
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
function show_graph(id,dt_from,dt_to){
  $('body').css('cursor','wait');
  str = '/graph1.php?id='+id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&salt='+Math.random();
  $('#div_graph1'+id).find('img').get(0).src=str;
  str = '/graph2.php?id='+id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&salt='+Math.random();
  $('#div_graph2'+id).find('img').get(0).src=str;
  $('body').css('cursor','default');
}
function close_week(dt){
  if(!confirm("Действительно закрыть?"))return false;
  flag = 0;
  $('#user_block').children().each(function(k,o){
    id_orig = o.id;
    if(id_orig.search('master')!= -1){
      id = id_orig.replace('master','');
      $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
          'operation':'close_week',
          'id':id,
          'html':o.innerHTML,
          'dt':dt
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          $('#user_block').html(html);
        },
        error:function(html){
          $('body').css('cursor','default');
          flag = 1;
        }
      });
    }
  });
  if(flag==1){
    alert('Ошибка сохранения!');
  }
}
</script>
<div style='width:80%;margin-left:auto;margin-right:auto;padding-left:20px;text-align:center;padding-bottom:15px;padding-top:15px;background-color:#f2f2f2;'>
<?php if($_REQUEST['razdel']==1 || $_REQUEST['razdel']==2 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==6 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){?>
<a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
<?php if($_REQUEST['razdel']==2 || $_REQUEST['razdel']==6){?>
Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
<?php }else{ ?>
Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24*7);?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24)?>'>
<?php } ?>
<a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
<?php
if($_REQUEST['razdel']==1){
?>
<a href='' style='text-decoration:none;border-bottom:1px dashed; margin-right: 20px;' id='a_profit'>Развернуть</a><span style='font-size: 15px;'>


<script>
  $(document).ready(function() {
    $("#a_profit").on("click", function(e){
      e.preventDefault();
      setProfitVisible();
    });
  });
</script>
<?php } ?>
<?php
if($_REQUEST['razdel']==1){
?>
<div style='margin-left:40px;' id='sum_profit'><?=f_show_profit(strtotime(date('o-\\WW'))-3600*24*7)?></div>
<?php } ?>
<?php }else{ ?>
Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW'))-4*7*3600*24);?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24)?>'>
<a href='export_masters.php?dt1=<?=date('Y-m-d',strtotime(date('o-\\WW'))-3600*24*7*4)?>&dt2=<?=date('Y-m-d',strtotime(date('o-\\WW')))?>' id='excel_export'><button class='orange'>Скачать Excel</button></a>
<?php } ?>
	<?php if($_REQUEST['razdel']==4){ ?>
 <div class="legend-controls">
    <div style='background-color:#9BBB59;' class="legend">
      <input type='checkbox' id='all_ch_graph1' checked/>
      <label for='all_ch_graph1'>
          Чистая прибыль
      </label>
    </div>
    <div style='background-color:#C0504D;' class="legend">
      <input type='checkbox' id='all_ch_graph2' checked/>
      <label for='all_ch_graph2'>
          Расходы
      </label>
    </div>
    <div style='background-color:#D99694;' class="legend">
        <input type='checkbox' id='all_ch_graph3' checked/>
        <label for='all_ch_graph3'>
            Бонусы
        </label>
    </div>
    <div style='background-color:#A6A6A6;' class="legend">
        <input type='checkbox' id='all_ch_graph4' checked/>
        <label for='all_ch_graph4'>
            Прибыль мастера
        </label>
    </div>
    <div style='background-color:#C4C4C4;' class="legend">
        <input type='checkbox' id='all_ch_graph8' checked/>
        <label for='all_ch_graph8'>
            Особые события
        </label>
    </div>
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
function chart(json){
	var dates = [];
    for (var i = 0; i < json.dt.length; i++) {
      dates.push(moment(json.dt[i]).format('DD.MM.YYYY'));
    }
    var ctx = document.getElementById("chart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Чистая прибыль',
                data: json.value,
                borderWidth: 2,
                borderColor: 'rgb(255, 99, 132)'
            }]
        },
        options: {
          legend: {
            labels: {
            // This more specific font property overrides the global property
              fontColor: 'black'
            }
          }
        }
    });
  }
  function getDates(startDate, stopDate) {
    var dateArray = new Array();
    var currentDate = startDate;
    while (currentDate.isBefore(stopDate, 'day')) {
        dateArray.push(currentDate.format("YYYY-MM-DD"));
        currentDate.add(7, 'days');
    }
    dateArray.push(stopDate.add(1, 'days').format("YYYY-MM-DD"));
    return dateArray;
  }
  function getCleanProfitDataByDates(from, to){
    var startDate = moment(from);
    var endDate = moment(to);
    var dates = getDates(startDate, endDate);
    var data = {
        'operation':'get_clean_profit_data_by_dates',
        'dates': dates
    };
    $.ajax({
      type:'POST',
	  async: false,
	  cache: false,
      url:'<?=$PHP_SELF?>',
      data:data,
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        var json = JSON.parse(html);
        chart(json);
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка отображения графика!');
      }
    });
  }
  $(document).ready(function(){
    $("input[id^='all_ch_graph']").on('click', function(){
      var idName = $(this).prop('id').substring(4, 13);
      var inputs = $("input[id^='" + idName + "']");
      inputs.click();
    });
	   
    var startDate = moment('<?=date("Y-m-d", strtotime(date('o-\\WW'))-4*7*3600*24);?>');
    var endDate = moment('<?=date("Y-m-d", strtotime(date('o-\\WW'))-3600*24);?>');
	setTimeout(function(){getCleanProfitDataByDates(startDate, endDate);}, 2000);
  })
</script>
<?php } ?>
</div>
<?php echo $InterFC->getTopBlockEnd(); ?>
<div style='padding-top:160px;' id='main_block'>
<?php if($_REQUEST['razdel']==1 || $_REQUEST['razdel']==5 || $_REQUEST['razdel']==8 || $_REQUEST['razdel']==9){?>
<div id='user_block' class='user_block'>
    <?=f_show_masters(date("Y-m-d", strtotime(date('o-\\WW'))-7*3600*24))?></div>
<?php }elseif($_REQUEST['razdel']==2){?>
<div id='user_block' class='user_block'></div>
<script>
  $(document).ready(function(){
    show_masters_by_city();
  });
</script>
<?php }elseif($_REQUEST['razdel']==6){?>
<div id='user_block' class='user_block'><?=f_show_shops(date("Y-m-d", strtotime(date('o-\\WW'))))?></div>
<?php }else{ ?>
<div id='user_block' class='user_block'><?=f_show_masters(date("Y-m-d", strtotime(date('o-\\WW'))-4*7*3600*24),date("Y-m-d", strtotime(date('o-\\WW'))-3600*24))?></div>
<?php } ?>
<?php
}elseif ($_REQUEST['razdel']==3){
?>
<script>
function add_manager(){
  var str = '';
  str += "<div id='"+Math.floor(Math.random()*10000000)+"'>\n";
  str += "<input type='hidden' class='u_id' value='0'>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Имя: <input type='text' class='u_name' value=''>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Пароль: <input type='text' class='u_pass' value=''>\n";
  str += "</div>\n";
  str +="<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Процент от бонуса: <input style=\"width: 35px; text-align: center;\" type='text' class='u_proc' value='0'></div>\n";
  str += "<div style='display:inline-block;padding-bottom:10px;'>\n";
  str += "<input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().find(\".u_pass\").get(0).value,1,$(this).parent().parent().get(0).id,0,0,0,$(this).parent().parent().find(\".u_proc\").get(0).value)'>\n";
  str += "</div>\n";
  str += "</div>\n";
  $('#managers div:last').after(str);
}

function add_uchenik(){
    var str = '';
    str += "<div id='"+Math.floor(Math.random()*10000000)+"'>\n";
    str += "<input type='hidden' class='u_id' value='0'>\n";
    str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
    str += "Имя: <input type='text' class='u_name' value=''>\n";
    str += "</div>\n";
    str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
    str += "Пароль: <input type='text' class='u_pass' value=''>\n";
    str += "</div>\n";
    str += "<div style='display:inline-block;padding-bottom:10px;'>\n";
    str += "менеджер: <select class='u_manager'>\n";
    str += "<option value='0' selected >Не назначен</option>\n";

<?php
    $rbon = mysql_query("SELECT * FROM users where type=1");
    while($abon = mysql_fetch_array($rbon)){
        $idbon = $abon['id'];
        $namebon = $abon['name'];
        ?>
    str += "<option value='<?=$idbon ?>'><?=$namebon ?></option>\n";
    <?php } ?>

   str += "</select>\n";
    str += "<input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().find(\".u_pass\").get(0).value,7,$(this).parent().parent().get(0).id, 0, $(this).parent().parent().find(\".u_manager\").get(0).value)'>\n";
    str += "</div>\n";
    str += "</div>\n";
    $('#uchenik div:last').after(str);
}

function add_topmanager(){
  var str = '';
  str += "<div id="+Math.floor(Math.random()*10000000)+" class='topmanagers_inner options_block'>\n";
  str += "<input class='u_id' value='0' type='hidden'>\n";
  str += "<div style='margin:0 0 20px 10px;'>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>\n";
  str += "Имя: <input class='u_name' type='text'>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>\n";
  str += "Пароль: <input class='u_pass' type='text'>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-bottom:10px;'>\n";
  str += "<input class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().parent().find(\".u_pass\").get(0).value,4,$(this).parent().parent().parent().get(0).id)' type='button'>\n";
  str += "</div>\n";
  str += "</div>\n";
  str += "<div style='margin:0 0 20px 10px;'>\n";
  str += "<span style='padding-right:15px;'>Размер бонуса за результативные записи</span><input style='width:50px;' class='u_bonus1' type='text'>\n";
  str += "<span style='padding-left:30px;padding-right:15px;'>остальные записи</span><input style='width:50px;' class='u_bonus2' type='text'>\n";
  str += "</div>\n";
  str += "</div>\n";
  $('#topmanagers div.topmanagers_inner:last').after(str);
}
function add_marketolog(){
  var str = '';
  str += "<div id='"+Math.floor(Math.random()*10000000)+"'>\n";
  str += "<input type='hidden' class='u_id' value='0'>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Имя: <input type='text' class='u_name' value=''>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Пароль: <input type='text' class='u_pass' value=''>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>\n";
  str += "<input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().find(\".u_pass\").get(0).value,2,$(this).parent().parent().get(0).id)'>\n";
  str += "</div>\n";
  str += "</div>\n";
  $('#marketologs div:last').after(str);
}
function add_master(){
  var str = '';
  str += "<div id='"+Math.floor(Math.random()*10000000)+"' class='masters_inner options_block'>\n";
  str += "<input type='hidden' class='u_id' value='0'>\n";
  str += "<div style='margin:0 0 20px 10px;'>\n";
  str += "Имя: <input type='text' class='u_name' value=''>\n";
  str += "Пароль: <input type='text' class='u_pass' value=''>\n";
  str += "Email: <input type='text' class='u_email' value=''>\n";
  s = Math.floor(Math.random()*10000000);
  str += "<label for='use_course"+s+"' class='u_label'>Учитывать курс</label><input type='checkbox' id='use_course"+s+"' class='use_course' value=1>\n";
  str += "&nbsp;\n";
  str += "<label for='shown"+s+"' class='u_shown'>Показывать</label><input type='checkbox' id='shown"+s+"' class='shown' checked value=1>\n";

  str += "<label for='vorkvk' class='u_vkvork'>Работа ВК</label><input type='checkbox' id='vorkvk0' class='vorkvk0' value='0'>\n";


    str += "</div>\n";
  str += "<div style='margin:0 0 20px 10px;'>\n";
  str += "<input type='checkbox' id='by_percent"+s+"' class='by_percent' value=1><label for='by_percent"+s+"'>Включить расчет по процентам</label>\n";
  str += "<input style='width:50px;' type='text' class='percent_val' value=''> %\n";
  str += "<span style='margin-left:40px;'>Город: <select class='id_m_city'>";
  str += "<option value='0'></option>";
  str += get_m_city_list();
  str += "</select></span>";
  str += "<div class='T_M_admin_City_block T_M_pad20px'>Коэффициент: <input type='text' class='koefic m_proc T_M_input_numb' value='0'></div>";

    str += "</div>\n";
  str += "<div class='u_others' style='display:inline-block;'>\n";
  str += "<div class='proc'></div>";
  str += "<div class='add_link' style='padding-bottom:10px;'><a href='' onclick='add_proc($(this).parent().parent().parent().get(0).id);return false;'>Добавить процедуру</a></div>";
  str += "<div class='u_list'></div>";
  str += "</div>\n";
  str += "</div>\n";
  $('#masters div.masters_inner:last').after(str);
  update_user_list();
}
function get_m_city_list(){
  var options = "";
  var data = {
      'operation':'get_m_city_list'
  };
  $.ajax({
    async: false,
    cache: false,
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      options = html;
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  });
  return options;
}
function add_seller(){
  var str = '';
  str += "<div id='"+Math.floor(Math.random()*10000000)+"'>\n";
  str += "<input type='hidden' class='u_id' value='0'>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Имя: <input type='text' class='u_name' value=''>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "Пароль: <input type='text' class='u_pass' value=''>\n";
  str += "</div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>\n";
/*    <?php
    $rbon1 = mysql_query("SELECT * FROM bonusoperatorezh where iduser=".$u_id);
    $abon1 = mysql_fetch_array($rbon1);
    $id_bonusskal=(int)$abon1['idbonus'];
    ?> */
  str += "шкала бонусов: <select class='u_bonus'>\n";
  str += "<option value='0' selected >Не назначен</option>\n";
/*    <?php
    $rbon = mysql_query("SELECT * FROM bonusezh");
    while($abon = mysql_fetch_array($rbon)){
        $idbon = $abon['id'];
        $namebon = $abon['namebonus'];
        ?> */
  str += "<option value='<?=$idbon ?>'><?=$namebon ?></option>\n";
/*   <?php
    }
    ?> */
  str += "</select>\n";
  str += "<input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(\".u_id\").get(0).value,$(this).parent().parent().find(\".u_name\").get(0).value,$(this).parent().parent().find(\".u_pass\").get(0).value,6,$(this).parent().parent().get(0).id, $(this).parent().parent().find(\".u_bonus\").get(0).value)'>\n";
  str += "</div>\n";
  str += "</div>\n";
  $('#sellers div:last').after(str);
}

function return_proc(id){
var name=document.getElementById('namep'+id).value;
    var price=document.getElementById('pricep'+id).value;
    var comission=document.getElementById('comissionp'+id).value;
    var ball=document.getElementById('ballp'+id).value;
    var scores=document.getElementById('scoresp'+id).value;
    data = {
        'id':id,
        'price':price,
        'comission':comission,
        'ball':ball,
        'scores':scores,
        'name':name,
        'operation':'return_proc'
    };
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:data,
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            alert('Изменения сохранены!');
            location.reload();
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка сохранения!');
        }
    });
    return false;
}

function add_pr_city(){
  var str = '';
  str += "<div class='options_block' style='border:1px solid;'>\n";
  str += "  <div id='"+Math.floor(Math.random()*10000000)+"' style='margin:0 0 10px 10px'>\n";
  str += "    <input type='hidden' class='c_id' value=''>\n";
  str += "    <div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Имя: \n";
  str += "        <input type='text' class='c_name' value=''>\n";
  str += "      </div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Доставка: \n";
  str += "        <input type='text' class='c_dostavka' value=''>\n";
  str += "      </div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Факт доставка: \n";
  str += "        <input type='text' class='c_fact_dostavka' value=''>\n";
  str += "      </div>\n";
  str += "    </div>\n";
  str += "    <div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Сумм меньше: \n";
  str += "        <input type='text' class='c_sum_less' value=''>\n";
  str += "      </div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Сумм больше: \n";
  str += "        <input type='text' class='c_sum_more' value=''>\n";
  str += "      </div>\n";
  str += "    </div>\n";
  str += "    <div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Цена меньше: \n";
  str += "        <input type='text' class='c_price_less' value=''>\n";
  str += "      </div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Цена больше: \n";
  str += "        <input type='text' class='c_price_more' value=''>\n";
  str += "      </div>\n";
  str += "    </div>\n";
  str += "    <div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        ДТ отгрузка: \n";
  str += "        <input type='text' class='c_dt_otgruzka' value=''>\n";
  str += "      </div>\n";
  str += "      <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
  str += "        Allow Bills: \n";
  str += "        <input type='checkbox' class='c_allow_bills' value=0>\n";
  str += "      </div>\n";
  str += "    </div>\n";
  str += "    <div style='display:inline-block;padding-bottom:10px;'>\n";
  str += "      <input type='button' class='orange' value='Сохранить' onclick='update_pr_city($(this).closest(\".options_block\"));return false;'>\n";
  str += "    </div>\n";
  str += "  </div>\n";
  str += "</div>\n";
  $('#cities > div:last').after(str);
}
function update_pr_city(block){
  $('body').css('cursor','wait');
  $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'update_pr_city',
        'c_id' : block.find('.c_id').val(),
        'c_name' : block.find('.c_name').val(),
        'c_dostavka' : block.find('.c_dostavka').val(),
        'c_fact_dostavka' : block.find('.c_fact_dostavka').val(),
        'c_sum_less' : block.find('.c_sum_less').val(),
        'c_price_less' : block.find('.c_price_less').val(),
        'c_sum_more' : block.find('.c_sum_more').val(),
        'c_price_more' : block.find('.c_price_more').val(),
        'c_dt_otgruzka' : block.find('.c_dt_otgruzka').val(),
        'c_allow_bills' : block.find('.c_allow_bills').is(':checked'),
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('body').css('cursor','default');
        console.log(html);
	    alert('Изменения сохранены!');
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
}
function update_user_list(){
  $('body').css('cursor','wait');
  $('#masters .u_id').each(function(i,o){
    $.ajax({
      type:'POST',
      url:'<?=$PHP_SELF?>',
      data:{
        'operation':'update_user_list',
        'id':o.value
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $('body').css('cursor','default');
        $(o).parent().find('.u_list').html(html);
      },
      error:function(html){
        $('body').css('cursor','default');
        alert('Ошибка подключения!');
      },
    });
  });
}
function add_proc(id){
  var str = '';
  str += "<div class='proc' id='"+Math.floor(Math.random()*10000000)+"'>\n";
  str += "<input type='hidden' class='p_id' value='0'>\n";
  str += "<div style='display:inline-block;padding:0 10px 20px;'>Процедура <input type='text' class='p_name' value=''></div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>Цена <input type='text' style='width:50px;' class='p_price' value=''></div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>Комиссия <input type='text' style='width:50px;' class='p_comission' value=''></div>\n";
  str += "<div style='display:none;padding-right:10px;'>Бонус <input type='text' style='width:50px;' class='p_bonus' value=''></div>\n";
  str += "<div style='display:none;padding-right:10px'>Баллы <select style='width: 50px;' class='p_balls' name='balls'><option disabled>Назначение балов</option><option value='0' >0</option><option value='1' >1</option><option value='2' >2</option><option value='3' >3</option><option value='4' >4</option><option value='5' >5</option></select></div>\n";
    str += "<div style='display:none;padding-right:10px'>Ст.мен.<input type='text' style='width:50px;' class='p_topmanager_bonus' value='0'></div>\n";
  s = Math.floor(Math.random()*10000000);
  str += "<div style='display:inline-block;'><label class='p_label' for='scores_tmp"+s+"'>Считать в конверсии</label><input type='checkbox' id='scores_tmp"+s+"' class='p_scores' value='1' checked></div>";
  str += "<div style='display:inline-block;'><label class='p_label1' for='archiv$p_id' style='margin-left: 14px;'>В архиве</label><input id='archiv$p_id' type='checkbox' class='p_archiv' value=1 disabled></div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>Порядок <input type='text' style='width:50px; text-align: center;' class='p_sortproc' value='1'></div>\n";
  str += "</div>\n";
  $('#'+id+' div.proc:last').after(str);
}

function disproc(flag, id){
    if (flag>0) {
        document.getElementById("archivproc" + id).style.display = "none";
        document.getElementById("sp" + id).innerHTML = "▼";
        disprocflag=0;
    } else
    {
        document.getElementById("archivproc" + id).style.display = "block";
        document.getElementById("sp" + id).innerHTML = "▲";
        disprocflag=1;
    }
}

function up_master(id){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'up_master',
      'id':id
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#masters').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}

function savemastersort(id){
    $('body').css('cursor','wait');
    var s1=document.getElementById('sor1m'+id).value;
    var s2=document.getElementById('sor2m'+id).value;
    $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
            'operation':'savemastersort',
            'sor1m':s1,
            'sor2m':s2,
            'id':id
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
            $('body').css('cursor','default');
            $('#masters').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}


function down_master(id){
  $('body').css('cursor','wait');
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'down_master',
      'id':id
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#masters').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
}
function add_shop(){
  var str = '';
  s = Math.floor(Math.random()*10000000);
  str += "<div id='"+s+"' class='shops_inner options_block'>\n";
  str += "<input type='hidden' class='id' value='0'>\n";
  str += "<div style='margin:0 0 20px 10px;'>\n";
  str += "Имя: <input type='text' class='name' value=''>\n";
  str += "</div>\n";
  str += "<div class='others' style='display:inline-block;'>\n";
  str += "<div class='city'></div>";
  str += "<div class='add_link' style='padding-bottom:10px;'><a href='' onclick='add_city($(this).parent().parent().parent().get(0).id);return false;'>Добавить город</a></div>";
  str += "<div class='c_list'></div>";
  str += "</div>\n";
  str += "</div>\n";
  $('#shops div.shops_inner:last').after(str);
  update_shop_list(s);
}
function add_city(id){
  var str = '';
  str += "<div class='city' id='"+Math.floor(Math.random()*10000000)+"'>\n";
  str += "<input type='hidden' class='c_id' value='0'>\n";
  str += "<div style='display:inline-block;padding:0 10px 20px;'>Город <input type='text' class='c_name' value=''></div>\n";
  str += "<div style='display:inline-block;padding-right:10px;'>Бонус <input type='text' style='width:50px;' class='c_bonus' value=''></div>\n";
  str += "</div>\n";
  $('#'+id+' div.city:last').after(str);
}
function update_shop_list(id){
  $('body').css('cursor','wait');
  if (id!=''){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      'operation':'update_shop_list'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#'+id).find('.c_list').html(html);
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка подключения!');
    },
  });
  }else{
    $('#shops .id').each(function(i,o){
      $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
          'operation':'update_shop_list',
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          $(o).parent().find('.c_list').html(html);
        },
        error:function(html){
          $('body').css('cursor','default');
          alert('Ошибка подключения!');
        },
      });
    });
  }
}
function save_shop(id){
  $('body').css('cursor','wait');
  obj = $('#'+id);
  data = {
      'id':obj.find('.id').val(),
      'name':obj.find('.name').val(),
      'operation':'save_shop'
  };
  obj = $('#'+id).find('.id_seller').get(0);
  data['id_seller'] = obj.options[obj.selectedIndex].value;
  obj = $('#'+id).find('.id_marketolog').get(0);
  data['id_marketolog'] = obj.options[obj.selectedIndex].value;
  arr_big = [];
  k1 = 0;
  $('#'+id).find('.city').each(function(k,o){
    if ($(o).find('.c_name').length>0){
      arr = {};
      arr['id'] = $(o).find('.c_id').get(0).value;
      arr['name'] = $(o).find('.c_name').get(0).value;
      arr['bonus'] = $(o).find('.c_bonus').get(0).value;
      arr['div_id'] = o.id;
      arr_big[k1] = arr;
      k1++;
    }
  });
  data['city'] = arr_big;
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      re = /\|/;
      if (!re.test(html)){
        if (html!='0'){
          $('#'+id).find('.id').get(0).value = html;
        }else{
          $('#'+id).remove();
        }
      }else{
        arr = html.split('|');
        html = arr[0];
        if (html!='0'){
          $('#'+id).find('.id').get(0).value = html;
          for(i=0;i<arr.length;i++){
            if (i==0)continue;
            if (arr[i]=='')continue;
            t = arr[i];
            arr1 = t.split('/');
            c_div_id = arr1[0];
            c_id = arr1[1];
            $('#'+c_div_id).find('.c_id').get(0).value=c_id;
            if (c_id==0)$('#'+c_div_id).remove();
          }
        }else{
          $('#'+id).remove();
        }
      }
	  alert('Изменения сохранены!');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  });
  return false;
}
$(document).ready(function(){
  $(document).on("click", "input[id*='by_percent']", function(){
    var checkBox = $(this).closest("div").find("input[id*='by_extra_procedure_prcnt_']");
    if ($(this).is(':checked')){
      checkBox.prop("checked", false);
    }else{
      checkBox.prop("checked", true);
    }
  });

  $(document).on("click", "input[id*='by_extra_procedure_prcnt_']", function(){
    var checkBox = $(this).closest("div").find("input[id*='by_percent']");
    if ($(this).is(':checked')){
      checkBox.prop("checked", false);
    }else{
      checkBox.prop("checked", true);
    }
  });
});
</script>
  <ul class="list-inline" style="background-color: #f2f2f2;padding:10px; margin: 0;">
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 1) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=1">Основной</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 2) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=2">На процентах</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 3) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=3">Учетные записи</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 4) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=4">Архив</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 7) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=7">Расходы</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 5) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=5">Еж Принес</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 6) ? 'selected' : '' ?>"> 
      <a href="/index.php?razdel=3&podrazdel=6">Города и валюты</a>
    </li>
    <li class="<?= ($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 8) ? 'selected' : '' ?>">
      <a href="/index.php?razdel=3&podrazdel=8">Размеры бонусов</a>
    </li>
  </ul> 
</div> 
<div style='padding-top:107px;'>
<?php if($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 1) { ?>

  <div id='masters'><?=f_show_masters_settings()?></div>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 2) { ?>

  <div id='masters'><?=f_show_masters_settings(true, true)?></div>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 3) { ?>
  
  <!-- show managers -->
  <div id='managers' class='options_block' style='border:1px solid; width: 1200px;'>
  <div></div>
  <?php
    $r = mysql_query("select * from users where type=1");//менеджеры
    while($a = mysql_fetch_array($r)){
      $u_id = $a['id'];
      $u_name = $a['name'];
      $u_pass = $a['password'];
      $u_proc = $a['inprocent'];
      $checked= $a['active'];
  ?>
  <div id='<?=rand()?>' style='margin:0 0 10px 10px'>
  <input type='hidden' class='u_id' value='<?=$u_id?>'>
  <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Имя: <input type='text' class='u_name' value='<?=htmlspecialchars($u_name)?>'></div>
  <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Пароль: <input type='text' class='u_pass' value='<?=htmlspecialchars($u_pass)?>'></div>
      <?php
      $sty="";
      if ($checked==0) $sty="style='background: lightcoral;'";
      echo " <select class='u_active' $sty >";
      echo  "<option value='1'" . (($checked==1) ? " selected " : ""). ">Работает</option>";
      echo  "<option value='0'" . (($checked==0) ? " selected" : "") .">В архиве</option>";
      echo  "</select>";
      ?>
  <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Процент от бонуса: <input style="width: 35px; text-align: center;" type='text' class='u_proc' value='<?=htmlspecialchars($u_proc)?>'></div>
  <div style='display:inline-block;padding-bottom:10px;'><input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(".u_id").get(0).value,$(this).parent().parent().find(".u_name").get(0).value,$(this).parent().parent().find(".u_pass").get(0).value,1,$(this).parent().parent().get(0).id,0,0,$(this).parent().parent().find(".u_active").get(0).value, $(this).parent().parent().find(".u_proc").get(0).value)'></div>
  </div>
  <?php
    }
  ?>
  </div>
  <div class='add_link'>
  <a href='' onclick='add_manager();return false;'>Добавить менеджера</a>
  </div>

    <!-- show ученики -->
    <div id='uchenik' class='options_block' style='border:1px solid; width: 1200px;'>
        <div></div>
        <?php
        $r = mysql_query("select * from users where type=7");//ученики
        while($a = mysql_fetch_array($r)){
            $u_id = $a['id'];
            $u_name = $a['name'];
            $u_pass = $a['password'];
            $checked= $a['active'];
            ?>
            <div id='<?=rand()?>' style='margin:0 0 10px 10px'>
                <input type='hidden' class='u_id' value='<?=$u_id?>'>
                <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Имя: <input type='text' class='u_name' value='<?=htmlspecialchars($u_name)?>'></div>
                <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Пароль: <input type='text' class='u_pass' value='<?=htmlspecialchars($u_pass)?>'></div>
                <?php
                $sty="";
                if ($checked==0) $sty="style='background: lightcoral;'";
                echo " <select class='u_active' $sty >";
                echo  "<option value='1'" . (($checked==1) ? " selected " : ""). ">Работает</option>";
                echo  "<option value='0'" . (($checked==0) ? " selected" : "") .">В архиве</option>";
                echo  "</select>";

                    $rbon1 = mysql_query("SELECT * FROM bonusoperator where iduser=".$u_id);
                    $abon1 = mysql_fetch_array($rbon1);
                    $id_bonusskal=(int)$abon1['idbonus'];
                    //echo "шкала бонусов: <select class='u_bonus'>";
                    //echo  "<option value='0'" . (($id_bonusskal==0) ? " selected " : ""). ">Не назначен</option>";
                    //$rbon = mysql_query("SELECT * FROM bonus where id!=1");
                    //while($abon = mysql_fetch_array($rbon)){
                    //$idbon = $abon['id'];
                    //$namebon = $abon['namebonus'];
                    // echo  "<option value='$idbon'" . (((int)$idbon==(int)$id_bonusskal) ? " selected" : "") .">$namebon</option>";
                    //}
                    // echo  "</select>";
                    ?>
                <?php
                $rbon1 = mysql_query("SELECT * FROM users u, operatortomanager o where type=1 and o.idoperator=".$u_id);
                $abon1 = mysql_fetch_array($rbon1);
                $id_managerx=(int)$abon1['idmanager'];
                echo "менеджер: <select class='u_manager'>";
                echo  "<option value='0'" . (($id_managerx==0) ? " selected " : ""). ">Не назначен</option>";
                $rbon = mysql_query("SELECT * FROM users where type=1");
                while($abon = mysql_fetch_array($rbon)){
                    $idbon = $abon['id'];
                    $namebon = $abon['name'];
                    echo  "<option value='$idbon'" . (((int)$idbon==(int)$id_managerx) ? " selected" : "") .">$namebon</option>";
                }
                echo  "</select>";
                ?>

                <div style='display:inline-block;padding-bottom:10px;'><input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(".u_id").get(0).value,$(this).parent().parent().find(".u_name").get(0).value,$(this).parent().parent().find(".u_pass").get(0).value,1,$(this).parent().parent().get(0).id, 0, $(this).parent().parent().find(".u_manager").get(0).value, $(this).parent().parent().find(".u_active").get(0).value)'></div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class='add_link'>
        <a href='' onclick='add_uchenik();return false;'>Добавить оператора</a>
    </div>


  <div id='marketologs' class='options_block' style='border:1px solid; width: 1200px;'>
  <div></div>
  <?php
    $r = mysql_query("select * from users where type=2");//маркетологи
    while($a = mysql_fetch_array($r)){
      $u_id = $a['id'];
      $u_name = $a['name'];
      $u_pass = $a['password'];
  ?>
  <div id='<?=rand()?>' style='margin:0 0 10px 10px'>
  <input type='hidden' class='u_id' value='<?=$u_id?>'>
  <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Имя: <input type='text' class='u_name' value='<?=htmlspecialchars($u_name)?>'></div>
  <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Пароль: <input type='text' class='u_pass' value='<?=htmlspecialchars($u_pass)?>'></div>
  <div style='display:inline-block;padding-bottom:10px;'><input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(".u_id").get(0).value,$(this).parent().parent().find(".u_name").get(0).value,$(this).parent().parent().find(".u_pass").get(0).value,1,$(this).parent().parent().get(0).id)'></div>
  </div>
  <?php
    }
  ?>
  </div>
  <div class='add_link'>
  <a href='' onclick='add_marketolog();return false;'>Добавить маркетолога</a>
  </div>

  <div id='topmanagers'><?=f_show_topmanagers_settings();?></div>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 4) { ?>
  
  <div id='masters'><?=f_show_masters_settings(false)?></div>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 5) { ?>
  <div id='sellers' class='options_block' style='border:1px solid;'>
    <div></div>
    <?php
      $r = mysql_query("select * from users where type=6");//продавцы
      while($a = mysql_fetch_array($r)){
        $u_id = $a['id'];
        $u_name = $a['name'];
        $u_pass = $a['password'];
    ?>
    <div id='<?=rand()?>' style='margin:0 0 10px 10px'>
    <input type='hidden' class='u_id' value='<?=$u_id?>'>
    <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Имя: <input type='text' class='u_name' value='<?=htmlspecialchars($u_name)?>'></div>
    <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Пароль: <input type='text' class='u_pass' value='<?=htmlspecialchars($u_pass)?>'></div>
        <?php
        $rbon1 = mysql_query("SELECT * FROM bonusoperatorezh where iduser=".$u_id);
        $abon1 = mysql_fetch_array($rbon1);
        $id_bonusskal=(int)$abon1['idbonus'];
        echo "шкала бонусов: <select class='u_bonus'>";
        echo  "<option value='0'" . (($id_bonusskal==0) ? " selected " : ""). ">Не назначен</option>";
        $rbon = mysql_query("SELECT * FROM bonusezh");
        while($abon = mysql_fetch_array($rbon)){
            $idbon = $abon['id'];
            $namebon = $abon['namebonus'];
            echo  "<option value='$idbon'" . (((int)$idbon==(int)$id_bonusskal) ? " selected" : "") .">$namebon</option>";
        }
        echo  "</select>";
        ?>
        <div style='display:inline-block;padding-bottom:10px;'><input type='button' class='orange' value='Сохранить' onclick='save_user($(this).parent().parent().find(".u_id").get(0).value,$(this).parent().parent().find(".u_name").get(0).value,$(this).parent().parent().find(".u_pass").get(0).value,6,$(this).parent().parent().get(0).id, $(this).parent().parent().find(".u_bonus").get(0).value)'></div>
    </div>
          <?php
      }
    ?>
    </div>
    <div class='add_link'>
    <a href='' onclick='add_seller();return false;'>Добавить продавца</a>
    </div>

<?php
$queryOper = "SELECT * FROM bonusezh";
$roper = mysql_query($queryOper);
while ($aoper = mysql_fetch_array($roper)){
$idop = $aoper['id'];
$base_percentop = $aoper['base_percent'];
?>
    <h5 name="bonuses_formezh<?= $aoper['id']; ?>">Размеры бонусов (<?=$aoper['namebonus']?>)</h5>
    <div class="options_block" style="background: beige;">
        <form id="bonuses_formezh<?= $aoper['id']; ?>" method="post"  data-id="<?= $aoper['id']; ?>">
            <table style="width: 100%;text-align:left;" class="bonuses">
                <tr>
                    <th style="width: 30%;">
                        Процент от суммы
                    </th>
                    <th>
                        Пороговые суммы
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="base_percent" id="base_percent" value="<?=$aoper['base_percent']?>" required/><br><br>
                        <h3>Наименование шкалы</h3>
                        <input type="text" name="nameskal" id="nameskal" value="<?=$aoper['namebonus']?>"/>
                    </td>
                    <td>
                        <table style="width: 100%;text-align:left;">
                            <tr>
                                <td>
                                    Сумма
                                </td>
                                <td>
                                    Вознаграждение
                                </td>
                                <td></td>
                            </tr>
                            <?php
                            $tmpl = "";
                            $query = "SELECT * FROM `bonus_rewardsezh` WHERE `bonus_id`=".$aoper['id'];
                            $r = mysql_query($query);
                            while($a = mysql_fetch_array($r)){
                                $reward_id = $a['id'];
                                $summ = $a['summ'];
                                $reward = $a['reward'];
                                $tmpl .= "<tr class='bonus_reward' data-id='$reward_id'><td><input type='text' data-type='summ' value='$summ' required/></td><td><input type='text' data-type='reward' value='$reward' required/></td><td><a href='#' class='remove-reward' onclick='removeRewardezh(this);'>Удалить</a></td></tr>";
                            }
                            echo $tmpl;
                            ?>
                            <tr class="addRewardBtnCntr">
                                <td colspan="3">
                                    <button onclick="addRewardezh(this);">Добавить</button>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;">
                        <input type='submit' class='orange' value='Сохранить'/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script>

        $(document).ready(function(){
            $('form#bonuses_formezh<?= $aoper['id']; ?>').on("submit", function(e){
                e.preventDefault();
                var ns = $(this).find("#nameskal").val();
                var text="Вы хотите сохранить данные?";
                if (ns=="") text="Вы хотите удалить шкалу?";
                if (confirm(text)){
                    var data = {
                        id: $(this).attr('data-id')
                    };
                    var base_percent = $(this).find("#base_percent").val();
                    var nameskal = $(this).find("#nameskal").val();
                    var rewards = [];
                    $.each($("tr.bonus_reward"), function(i, el){
                        var reward = {
                            summ: $(el).find("input[data-type='summ']").val(),
                            reward: $(el).find("input[data-type='reward']").val(),
                            id: $(el).attr('data-id')
                        }
                        rewards.push(reward);
                    });
                    data.base_percent = base_percent;
                    data.rewards = rewards;
                    data.nameskal= nameskal;
                    data.operation = 'save_bonusesezh';

                    $.ajax({
                        type:'POST',
                        url:'<?=$_SERVER['PHP_SELF']?>',
                        data: data,
                        timeout:<?=$AJAX_TIMEOUT?>,
                        success:function(html){
                            $('body').css('cursor','default');
                            $('#bonuses_formezh<?= $aoper['id']; ?> tr.bonus_reward').remove();
                            $('#bonuses_formezh<?= $aoper['id']; ?> .addRewardBtnCntr').before(html);
                            location.reload();
                        },
                        error:function(html){
                            $('body').css('cursor','default');
                            alert('Ошибка отображения! Перезагрузите страницу');
                        }
                    });
                }
            })
        });
    </script>

<?php  } ?>

    <div id="skalaezh" ></div> <br>
    <div class='add_link'>
        <button onclick='add_shkalaezh();return false;'>Добавить бонусную шкалу продовца</button>
        <br>
        <br>
    </div>
<script>
    function add_shkalaezh(){
        var str = '';
        str += "<div class=\"options_block\" style=\"background: beige;\">\n";
        str += "<form id=\"bonuses_formEzh0\" method=\"post\" data-id=\"1\" onsubmit='alert(\"Бонусная шкала создана\");'>\n";
        str += "<table style=\"width: 100%;text-align:left;\" class=\"bonuses\">\n";
        str += "<tr> <th style=\"width: 30%;\">Процент от суммы </th> <th>Пороговые суммы </th> </tr>\n";

        str += "<tr> <td> <input type=\"text\" name=\"base_percent\" id=\"base_percent\" value=\"0.01\" required/><br><br> <h3>Наименование шкалы</h3>\n";
        str += "<input type=\"text\" name=\"nameskal\" id=\"nameskal\" value=\"\" required/> </td> <td>\n";
        str += "<table style=\"width: 100%;text-align:left;\"> <tr> <td>Сумма </td> <td>Вознаграждение </td> <td></td> </tr>\n";
        str += "<tr class=\"addRewardBtnCntr\"> <td colspan=\"3\"> <button onclick=\"addRewardezh(this);\">Добавить</button> </td> </tr> </table> </td> </tr>\n";
        str += "<tr> <td colspan=\"2\" style=\"text-align:right;\"> <input type='submit' onclick=\"addSkEzh(this);\" class='orange' value='Сохранить'/> </td> </tr>\n";
        str += "</table>\n";
        str += "</form>\n";
        str += "</div>\n"; //zav div
        $('#skalaezh').html(str);
    }

    function removeRewardezh(el){
        if (confirm("Вы хотите удалить запись?")){
            var reward = $(el).closest("tr");
            var id = reward.attr('data-id');
            $.ajax({
                type:'POST',
                url:'<?=$_SERVER['PHP_SELF']?>',
                data: {
                    id: id,
                    operation: 'remove_rewardezh'
                },
                timeout:<?=$AJAX_TIMEOUT?>,
                success:function(html){
                    $('body').css('cursor','default');
                    if (html == "OK"){
                        reward.remove();
                    }
                },
                error:function(html){
                    $('body').css('cursor','default');
                    alert('Ошибка удаления! Перезагрузите страницу');
                }
            });
        }
    }

    function addSkEzh(el){
        var data = {
            id: 0
        };
        var base_percent = $('form#bonuses_formEzh0').find("#base_percent").val();
        var nameskal = $('form#bonuses_formEzh0').find("#nameskal").val();
        var rewards = [];
        $.each($("tr.bonus_reward"), function(i, el){
            var reward = {
                summ: $(el).find("input[data-type='summ']").val(),
                reward: $(el).find("input[data-type='reward']").val(),
                id: $(el).attr('data-id')
            }
            rewards.push(reward);
        });
        data.base_percent = base_percent;
        data.rewards = rewards;
        data.nameskal= nameskal;
        data.operation = 'save_bonusesezh';
        $.ajax({
            type:'POST',
            url:'<?=$_SERVER['PHP_SELF']?>',
            data: data,
            timeout:<?=$AJAX_TIMEOUT?>,
            success:function(html){
                $('body').css('cursor','default');
                $('#bonuses_formEzh0 tr.bonus_reward').remove();
                $('#bonuses_formEzh0 .addRewardBtnCntr').before(html);
            },
            error:function(html){
                $('body').css('cursor','default');
                alert('Ошибка отображения! Перезагрузите страницу');
            }
        });
    }

    function addRewardezh(el){
        var base = $(el).closest("table");
        var addBtn = base.find("tr.addRewardBtnCntr");
        var rewardTmpl = `
            <tr class="bonus_reward" data-id="0">
              <td>
                <input type="text" data-type="summ" required=""/>
              </td>
              <td>
                <input type="text" data-type="reward" required=""/>
              </td>
              <td>
                <a href="#" class="remove-reward" onclick="removeRewardezh(this);">Удалить</a>
              </td>
            </tr>`;
        addBtn.before(rewardTmpl);
    }

</script>

    <div id='shops'><?=f_show_shops_settings()?></div>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 6) {
    echo $supervisorFace->showCityes(); // вывод городов
    ?>
    <div class="right" style="width: 46%;border: 1px solid black;padding: 10px;float: right;">
      <h4>Валюты</h4>
      <div class="currencies">
        <?php
          $r =  mysql_query("select * from currencies");//валюты
          while($a = mysql_fetch_array($r)){
            $c_id = $a['id'];
            $c_name = $a['name'];
            ?>
              <div id='<?=rand()?>' style='margin:0 0 10px 10px' class="currency-row">
                <input type='hidden' class='m_id' value='<?=$c_id?>'>
                <div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>Валюта:<input type='text' class='m_name' value='<?=htmlspecialchars($c_name)?>'></div>
                <div style='display:inline-block;padding-bottom:10px;'><input type='button' class='orange' value='Сохранить' onclick='save_currency($(this).closest(".currency-row"))'></div>
              </div>
          <?php } ?>
      </div>
        <div class='add_link'>
          <a href='' onclick='add_currency();return false;'>Добавить валюту</a>
        </div>
    </div>
    <script>
      function add_currency(){
        var str = '';
        str += "<div id='"+Math.floor(Math.random()*10000000)+"' style='margin:0 0 10px 10px' class='currency-row'>\n";
        str += "<input type='hidden' class='m_id' value='0'>\n";
        str += "<div style='display:inline-block;padding-right:10px;padding-bottom:10px;'>\n";
        str += "Валюта:<input type='text' class='m_name' value=''>\n";
        str += "</div>\n";
        str += "<div style='display:inline-block;padding-right:10px;'>\n";
        str += "<input type='button' class='orange' value='Сохранить' onclick='save_currency($(this).closest(\".currency-row\"))'>\n";
        str += "</div>\n";
        str += "</div>\n";
        $('.currencies').append(str);
      }
      function save_currency(parent){
        var id = parent.find(".m_id").val();
        var name = parent.find(".m_name").val();

        $.ajax({
          type:'POST',
          url:'<?=$_SERVER['PHP_SELF']?>',
          data:{
            'id':id,
            'name': name,
            'operation':'save_currency'
          },
          timeout:<?=$AJAX_TIMEOUT?>,
          success:function(html){
            $('body').css('cursor','default');
            alert('Изменения сохранены!');
          },
          error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка сохранения!');
          }
        });
      }
    </script>

<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 7) { ?>
  <div style='text-align:center;background-color:#f2f2f2;position: fixed;width: 100%;padding: 5px 0;'>
    <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
    Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
    <a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
  </div>

  <div id="costs" style="margin-top: 33px;"><?=f_show_costs(date("Y-m-d", strtotime(date('o-\\WW'))), date("Y-m-d", strtotime(date('o-\\WW'))+3600*24*6))?></div>

  <script type="text/javascript">
    function add_cost(link, type = 1){
      var block = link.closest('.options_block');
      var newCost = '';
      var current_dt = $("#current_dt").val();
      newCost += "<div class='cost'  data-id='0' data-type='" + type + "' data-dt='" + current_dt + "'>";
      newCost += "<input class='cost-name' style='margin: 10px;' type='text' value='' placeholder='Название' />";
      newCost += "<input class='cost-summ' style='margin: 10px;' type='text' value='' placeholder='Сумма' />";
      newCost += "</div>";
      block.find('.new-cost').append(newCost);
    };
    function delete_cost(id) { 
      var confirmed = confirm("Вы хотите удалить данный расход?");
      if (!confirmed) return;
      $.ajax({
        type:'POST',
        url:'<?=$_SERVER['PHP_SELF']?>',
        data:{
          'id':id,
          'operation':'delete_cost'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          $('#costs').find('.cost[data-id="' + id + '"]').remove();
        },
        error:function(html){
          $('body').css('cursor','default');
          alert('Ошибка отображения!');
        }
      });
    }
    function get_costs(dt_start, dt_end) {
      $.ajax({
        type:'POST',
        url:'<?=$_SERVER['PHP_SELF']?>',
        data:{
          'dt_start':dt_start,
          'dt_end':dt_end,
          'operation':'get_costs'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          $('#costs').html(html);
        },
        error:function(html){
          $('body').css('cursor','default');
          alert('Ошибка отображения!');
        }
      });
    }
    function save_costs(btn){
      var confirmed = confirm("Вы хотите сохранить расходы?");
      if (!confirmed) return;
      var block = btn.closest('.options_block');
      var costs = block.find(".cost");
      var current_dt = $("#current_dt").val();
      var data = [];
      $.each(costs, function(index, cost) {
         var item = {
          id: $(cost).attr('data-id'),
          type: $(cost).attr('data-type'),
          dt: $(cost).attr('data-dt'),
          name: $(cost).find('.cost-name').val(),
          summ: $(cost).find('.cost-summ').val(),
         };
         data.push(item);
      });
      $.ajax({
        type:'POST',
        url:'<?=$_SERVER['PHP_SELF']?>',
        data:{
          'costs':data,
          'dt': current_dt,
          'operation':'save_costs'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          //$('#costs').html(html);
		  alert('Изменения сохранены!');
        },
        error:function(html){
          $('body').css('cursor','default');
          alert('Ошибка отображения!');
        }
      });
    }
  $(function() {
    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW')));?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))+3600*24*6);?>');
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
              
              selectCurrentWeek();

              var dt_start = ('0'+startDate.getDate()).slice(-2)+'.'+('0'+parseInt(startDate.getMonth()+1)).slice(-2)+'.'+startDate.getFullYear();
              var dt_end = ('0'+endDate.getDate()).slice(-2)+'.'+('0'+parseInt(endDate.getMonth()+1)).slice(-2)+'.'+endDate.getFullYear();
              
              get_costs(dt_start, dt_end);
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
          get_costs(str1, str2);
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
          get_costs(str1, str2);
          return false;
      });

      $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
      $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
  
    });
  </script>
	
<?php } elseif($_REQUEST['razdel'] == 3 && $_REQUEST['podrazdel'] == 8) { ?>
  <script>
    function addReward(el){
      var base = $(el).closest("table");
      var addBtn = base.find("tr.addRewardBtnCntr");
      var rewardTmpl = `
            <tr class="bonus_reward" data-id="0">
              <td>
                <input type="text" data-type="summ" required=""/>
              </td>
              <td>
                <input type="text" data-type="reward" required=""/>
              </td>
              <td>
                <a href="#" class="remove-reward" onclick="removeReward(this);">Удалить</a>
              </td>
            </tr>`;
      addBtn.before(rewardTmpl);
    }
    function removeReward(el){
      if (confirm("Вы хотите удалить запись?")){
        var reward = $(el).closest("tr");
        var id = reward.attr('data-id');
        $.ajax({
          type:'POST',
          url:'<?=$_SERVER['PHP_SELF']?>',
          data: {
            id: id,
            operation: 'remove_reward'
          },
          timeout:<?=$AJAX_TIMEOUT?>,
          success:function(html){
            $('body').css('cursor','default');
            if (html == "OK"){
              reward.remove();
            }
          },
          error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка удаления! Перезагрузите страницу');
          }
        });
      }
    }

    $(document).ready(function(){
        $('form#bonuses_form').on("submit", function(e){
          e.preventDefault();
          if (confirm("Вы хотите сохранить данные?")){
            var data = {
              id: $(this).attr('data-id')
            };
            var base_percent = $(this).find("#base_percent").val();
            var rewards = [];
            $.each($("tr.bonus_reward"), function(i, el){
              var reward = {
                summ: $(el).find("input[data-type='summ']").val(),
                reward: $(el).find("input[data-type='reward']").val(),
                id: $(el).attr('data-id')
              }
              rewards.push(reward);
            });
            data.base_percent = base_percent;
            data.rewards = rewards;
            data.operation = 'save_bonuses';

            $.ajax({
              type:'POST',
              url:'<?=$_SERVER['PHP_SELF']?>',
              data: data,
              timeout:<?=$AJAX_TIMEOUT?>,
              success:function(html){
                $('body').css('cursor','default');
                $('#bonuses_form tr.bonus_reward').remove();
                $('#bonuses_form .addRewardBtnCntr').before(html);
              },
              error:function(html){
                $('body').css('cursor','default');
                alert('Ошибка отображения! Перезагрузите страницу');
              }
            });
          }
        })
    });
  </script>
  <?php
    $query = "SELECT * FROM `bonus` WHERE `id`=1";
    $r = mysql_query($query);
    $a = mysql_fetch_array($r);
    $id = $a['id'];
    $base_percent = $a['base_percent'];
  ?>


  <h5>Размер бонусов менеджера</h5>
  <div class="options_block">
    <form id="bonuses_form" method="post" data-id="<?= $id; ?>">
      <table style="width: 100%;text-align:left;" class="bonuses">
        <tr>
          <th style="width: 30%;">
            Процент от суммы
          </th>
          <th>
            Пороговые суммы
          </th>
        </tr>
        <tr>
          <td>
            <input type="text" name="base_percent" id="base_percent" value="<?= $base_percent; ?>" required/>
          </td>
          <td>
            <table style="width: 100%;text-align:left;">
              <tr>
                <td>
                  Сумма
                </td>
                <td>
                  Вознаграждение
                </td>
                <td></td>
              </tr>
              <?php
                $tmpl = "";
                $query = "SELECT * FROM `bonus_rewards` WHERE `bonus_id`=1";
                $r = mysql_query($query);
                while($a = mysql_fetch_array($r)){
                  $reward_id = $a['id'];
                  $summ = $a['summ'];
                  $reward = $a['reward'];
                  $tmpl .= "<tr class='bonus_reward' data-id='$reward_id'><td><input type='text' data-type='summ' value='$summ' required/></td><td><input type='text' data-type='reward' value='$reward' required/></td><td><a href='#' class='remove-reward' onclick='removeReward(this);'>Удалить</a></td></tr>";
                }
                echo $tmpl;
              ?>
              <tr class="addRewardBtnCntr">
                <td colspan="3">
                  <a href="#" onclick="addReward(this);">Добавить</a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align:right;">
            <input type='submit' class='orange' value='Сохранить'/>
          </td>
        </tr>
      </table>
    </form>
  </div>
    <br>
<br>
<?php
    echo $supervisorFace->showSaverBonus();
    echo $supervisorFace->showOperators();
} ?>
	
<?php }elseif ($_REQUEST['razdel'] == 7) { ?>
  <div style='width:80%;margin-left:auto;margin-right:auto;padding-left:20px;text-align:center;padding-bottom:35px;padding-top:15px;background-color:#f2f2f2;'>
    <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
    Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24*7);?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))-3600*24)?>'>
	<a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
  </div> 
</div>

  <div id="masters_profit" style="margin: 138px auto; width: 1000px;"></div>

  <script type="text/javascript">
    function get_masters_profit(dt_start, dt_end) {
      $("#masters_profit").html("<div style='text-align:center;'><img src='/img/loader.gif'/></div>");
      $.ajax({
        type:'POST',
        url:'<?=$_SERVER['PHP_SELF']?>',
        data:{
          'dt_start':dt_start,
          'dt_end':dt_end,
          'operation':'get_masters_profit'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $('body').css('cursor','default');
          $('#masters_profit').html(html);
        },
        error:function(html){
          $('body').css('cursor','default');
          alert('Ошибка отображения! Перезагрузите страницу');
        }
      });
    }
  $(function() {
    get_masters_profit(
      '<?=date("Y-m-d", strtotime(date('o-\\WW'))-3600*24*7)?>', 
      '<?=date("Y-m-d", strtotime(date('o-\\WW'))+3600*24)?>'
    );

    var startDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))-3600*24*7);?>');
    var endDate = new Date('<?=date("m/d/Y", strtotime(date('o-\\WW'))-3600*24);?>');
    
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
              
              selectCurrentWeek();

              var dt_start = ('0'+startDate.getDate()).slice(-2)+'.'+('0'+parseInt(startDate.getMonth()+1)).slice(-2)+'.'+startDate.getFullYear();
              var dt_end = ('0'+endDate.getDate()).slice(-2)+'.'+('0'+parseInt(endDate.getMonth()+1)).slice(-2)+'.'+endDate.getFullYear();
              get_masters_profit(dt_start, dt_end);
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
          get_masters_profit(str1, str2);
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
          get_masters_profit(str1, str2);
          return false;
      });

      $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
      $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
  
    });
  </script>
<?php }
//if ($_REQUEST['razdel']==111){ require $DOCUMENT_ROOT. '/monitoring/monitor.php'; }
?>
</div>
<div id="loader">
  <img src="img/loader.gif" alt="" />
</div>
  <br>
<br>
</body>
</html>