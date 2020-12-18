// Сука Саят нахрена такой гигантский компонент написал еще и кинул его в документ. Столько времени на эту хуету пришлось убить чтоб так структурировать. Сволочь гнойная.
$(function() {
    set_input_colors();
    var startDate = new Date(dts);
    var endDate = new Date(dtk);
    var id=tikusid;
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
            show_week_plan(id);
            show_user_block(id);
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
        show_week_plan(id);
        show_user_block(id);
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
        show_week_plan(id);
        show_user_block(id);
        return false;
    });

    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });

    $('.e_date').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
    });

});

// Сохраняет мастера. Писал Саят походу пришлось прибирать за этим говнюком
function save_master(id, idr){
    data = {};
    var id_master;
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
    data['idr'] = '119';
    new_event_flag = 0;
    if ($('#new_event'+id_master).val()==1)new_event_flag = 1;
    if ($("#events_block"+id_master).is(":visible"))new_event_flag = 1;
    $.ajax({
        type:'POST',
        url:'index.php',
        data:data,
        timeout:20000,
success:function(html){
$('#master'+id).html(html);
      if (new_event_flag==1)$('#events_block'+id_master).show();
      set_input_colors();
      show_week_plan(idr);
      $('.e_date').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
      });
      $('body').css('cursor','default');
    alert('Изменения сохранены!');
},
error:function(html){
$('body').css('cursor','default');
      alert('Ошибка сохранения!');
}
});
}
// Задает стили.Писал Саят. Пойдет
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
// Показ аякс блока Писал Саят. Код инвалидноватый но более менее с ним работать можно.
function show_user_block(id){
    dt = $("#weekpicker").val().replace(/ .*/,'');
    if (dt!=''){
        $.ajax({
            type:'POST',
            url:'index.php',
            data:{
                dt:dt,
                id:id,
    operation:'show_master'
    },
    timeout:20000,
        success:function(html){
            $('body').css('cursor','default');
            $('#user_block1').html(html);
            set_input_colors();
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка соединения!');
        }
    });
    }else{
        $('#user_block1').html('');
    }
}
// Показать блок веб плана
function show_week_plan(id){
    dt = $("#weekpicker").val().replace(/ .*/,'');
    if (dt!=''){
        $.ajax({
            type:'POST',
            url:'index.php',
            data:{
                dt:dt,
                id:id,
    operation:'show_week_plan'
    },
    timeout:20000,
        success:function(html){
            $('body').css('cursor','default');
            $('#user_block').html(html);
            set_input_colors();
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