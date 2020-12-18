<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$AJAX_TIMEOUT = 20000;

include($_SERVER['DOCUMENT_ROOT']."/timurnf/operator_functions.php");

$operation = $_POST['operation'];
$GLOBALS['stats'] = [
  'global' => [],
  'cities' => []
];
if (!isset($_REQUEST['razdel'])){$_REQUEST['razdel'] = 1;}
/**Клас интерфейса роутинга**/
$InterFC=new InterFC($_REQUEST['razdel']);
$usersCRM=new usersCRM();
/**Ловля события операции**/

if($_REQUEST['razdel'] == 1) {
  include("$DOCUMENT_ROOT/components/manager/weekplan.php");
}

if($_REQUEST['razdel'] == 2) {
  include("$DOCUMENT_ROOT/components/manager/statistics.php");
}


require $DOCUMENT_ROOT. '/components/supervisor/methods.php';
require $DOCUMENT_ROOT. '/components/supervisor/functions.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body style="margin: 0;">
<style>
.p_input{
  width:50px;
}
</style>
<script type="text/javascript">

function set_input_colors(){
  var color = '#C4D69C';
  $('input[type=text]').on('input',function(e){
    if($(this).hasClass('p_input')){
      this.value = this.value.replace(/[^0-9]/g,'');
    }
    if(this.id=='weekpicker')return;
    if(this.disabled)return;
    if(this.value==''){
      $(this).css('background-color','#FFFFFF');
    }else{
      $(this).css('background-color',color);
    }
    $('#oper_status').html('');
  });
  $('input[type=text]').each(function(e){
    if(this.id=='weekpicker')return;
    if(this.disabled)return;
    if(this.value==''){
      $(this).css('background-color','#FFFFFF');
    }else{
      $(this).css('background-color',color);
    }
  });
}
$(function() {
    set_input_colors();


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
            <?php if($_REQUEST['razdel'] != 2) { ?>
              show_week_plan();
            <?php }else{ ?>  
              show_user_block();
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
        <?php if($_REQUEST['razdel'] != 2) { ?>
              show_week_plan();
        <?php }else{ ?>  
          show_user_block();
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
        <?php if($_REQUEST['razdel'] != 2) { ?>
              show_week_plan();
        <?php }else{ ?>  
          show_user_block();
        <?php } ?>
        return false;
    });
    
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });

    $('.e_date').datepicker( {
      showOtherMonths: true,
      selectOtherMonths: true,
    });

});
<?php if($_REQUEST['razdel'] != 2) { ?>
  
  function decorate(){
      setTimeout(function(){
          // bold slider 
          $(".table.slider.no-width tr").each(function(i, tr){
              $(tr).find("td").each(function(k, td){
                  var tdPosition = $(td).offset().left;
                  var progress = $(this).closest(".table.slider");
                  var sliderPosition = progress.width() + progress.offset().left; 
                  if (tdPosition >= sliderPosition){
                      $(td).attr('style', 'font-weight:bold;');
                      return false;
                  } 
              });
          });
      }, 500);
  }

  function show_week_plan(){
    dt = $("#weekpicker").val().replace(/ .*/,'');
    if (dt!=''){
      $("#loader").show();
      $.ajax({
        type:'POST',
        url:'<?=$PHP_SELF?>',
        data:{
          dt:dt,
          id:<?=$id?>,
          operation:'show_week_plan'
        },
        timeout:<?=$AJAX_TIMEOUT?>,
        success:function(html){
          $("#loader").hide();
          $('body').css('cursor','default');
          $('#user_block').html(html);
          set_input_colors();
          //show_all_statistics();
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
function send_mail_button(p_input_class){
  p_button_id = p_input_class.replace('p_input','send_mail');
//  if($('#'+p_button_id).val()=='Отправить еще раз')return;
  var flag = 1;
  $('input[type=text].'+p_input_class).each(function(e){
    if(this.value=='')flag = 0;
  }); 
  if(flag==1){
    $('#'+p_button_id).get(0).disabled = false;
    $('#'+p_button_id).addClass('orange');
  }else{
    $('#'+p_button_id).get(0).disabled = true;
    $('#'+p_button_id).removeClass('orange');
  }
}
function send_mail(id,dt){
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:{
      dt:dt,
      id:id,
      operation:'send_mail'
    },
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('body').css('cursor','default');
      $('#send_mail'+id).removeClass('orange');
      $('#send_mail'+id).addClass('green');
      $('#send_mail'+id).val('Отправить еще раз'); 
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка соединения!');
    }
  });
}
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
        operation:'show_master'
      },
      timeout:<?=$AJAX_TIMEOUT?>,
      success:function(html){
        $("#loader").hide();
        $('body').css('cursor','default');
        $('#user_block').html(html);
        set_input_colors();
        //show_all_statistics();
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
function save_master(id){
  data = {};
  var id_master;
  var idr='<?=$id?>';
  var city_table = $('#master'+id).closest('.city');
  var city_id = city_table.attr('data-id');
  data['city_id'] = city_id;

  data['extra_procedure_summ'] = $('#master'+id).find("input[name='extra_procedure_summ']").val();
  $('#master'+id+' .p_input').each(function(k,o){
    o_id = o.id;
    val = o.value;
    if (o_id=='id_master')id_master = val;
    data[o_id] = val;
  });
  $('#master'+id+' .event').each(function(k,o){
    if ($(o).find('.e_name').length==0)return;
    data['event_name_'+k] = $(o).find('.e_name').get(0).value;
    data['event_date_'+k] = $(o).find('.e_date').get(0).value;    
  });
  data['operation'] = 'save_master';
  data['idr'] = idr;
  new_event_flag = 0;
  if ($('#new_event'+id_master).val()==1)new_event_flag = 1;
  if ($("#events_block"+id_master).is(":visible"))new_event_flag = 1;
  $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
      $('#master'+id).html(html);
      if (new_event_flag==1)$('#events_block'+id_master).show();
      set_input_colors();
      $('.e_date').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
      });
      send_mail_button("p_input"+id_master);
      $('body').css('cursor','default');
      $('#oper_status').html('Сохранено!');
      alert('Изменения сохранены!');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  }); 
}
function myParseInt(s){
  s1 = parseInt(s);
  if (isNaN(s1))return 0;
  return s1;
}
function set_contacts(id, index){
  if (index === 0){
    val0 = myParseInt($('#ch_'+id+'_old').val());
  }else{
    val0 = myParseInt($('#ch_'+id+'_'+(index-1)).val());
  }
    valx = myParseInt($('#chlf_'+id+'_'+(index)).val());
  res = 0;
  val = parseInt($('#ch_'+id+'_'+index).val());
  if (!isNaN(val))res = val-val0+valx;
  $('#contacts'+id+'_'+(index+1)).html(res);
}

function add_event(id){
  str = '';
  str += "<div class='event'>\n";
  str += "<span>Имя: <input type='text' class='e_name'></span>\n";
  str += "<span style='padding-left:10px;'>Дата: <input type='text' class='e_date' value='<?=date('d.m.Y')?>' style='width:80px;'></span>\n";
  str += "</div>\n";
  $('#new_event'+id).val(1);
  $('#events'+id+' div.event:last').after(str);
  $('.e_date').datepicker( {
    showOtherMonths: true,
    selectOtherMonths: true,
  });
}

function save_contacts(btn, city_id, dt)
{
  var error = false;
  var parent = $(btn).closest("table");
  var data = {};
  data['operation'] = 'save_contacts';
  data['city_id'] = city_id;
  data['dt'] = dt;
  data['other_contacts'] = parent.find('input[data-type="other_contacts"]').val();
  if (parent.find('input[data-type="master_on_procent_procedures_count"]').length > 0){
    data['master_on_procent_procedures_count'] = parent.find('input[data-type="master_on_procent_procedures_count"]').val();
  }
  data['contacts'] = {};
  //data['contactsvk'] = {};
  var last_contact_val = parseInt($("#ch_"+city_id+"_old").val());
  $.each(parent.find('input[data-type="contacts"]'), function(i, c){

    var contact = $(c);
    data['contacts'][contact.attr('data-dt')] = contact.val();
    var diff = parseInt(contact.val()) - last_contact_val;
    error = error || diff > 350 || diff < 0;
    last_contact_val = parseInt(contact.val());
  });
    data['contactsLEDFIT'] = {};
    $.each(parent.find('input[data-type="contactsLEDFIT"]'), function(i, c){

        var contact = $(c);
        data['contactsLEDFIT'][contact.attr('data-dt')] = contact.val();
        var diff = parseInt(contact.val());
        error = error || diff > 350 || diff < 0;
    });
 /**   $.each(parent.find('input[data-type="contactsvk"]'), function(i, c){

        var contact = $(c);
        data['contactsvk'][contact.attr('data-dt')] = contact.val();
    }); **/
/*
if (error){
  alert("Проверьте количество контактов. Возможно, прирост больше 350, либо меньше 0.");
}else{*/
 $.ajax({
    type:'POST',
    url:'<?=$PHP_SELF?>',
    data:data,
    timeout:<?=$AJAX_TIMEOUT?>,
    success:function(html){
        console.log(data);
      alert('Изменения сохранены!');
    },
    error:function(html){
      $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
  });

}
<?php } ?>

</script>
<div class='user_block' style='font-size:20px;width: 100%;position: fixed;background-color: #f2f2f2;padding: 10px 0;margin-top: -49px;z-index: 999;'>
    <?php echo $InterFC->GetMenue(3); ?>
  <div>
<a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
Дата: <input type="text" id="weekpicker" style='width:200px;font-size: 18px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
<a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
  </div>
  <div style='clear:both;'></div>

</div>
<div id='oper_status' style='color:green;text-align:center;height:50px;margin-top: 49px;'></div>

<div id='user_block' class='user_block'>
  <script>
    $(document).ready(function(){
      <?php if($_REQUEST['razdel'] != 2) { ?>
        show_week_plan();
      <?php }else{ ?>
        show_user_block();
      <?php } ?>
    });
  </script>
</div>
<br>
<br>
<div id="loader">
  <img src="img/loader.gif" alt="" />
</div>
</body>
</html>