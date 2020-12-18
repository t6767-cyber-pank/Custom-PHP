/**Стрелочки**/
function lf(napr) {
    if(napr==0) { d.setDate(d.getDate() -1); }
    else
    { d.setDate(d.getDate()+ 1); }
    $.ajax({
        type:'POST',
        url:urli,
        data:{
            'dt':parserDate(getMonday(d)),
            'operation':operation
        },
        success:function(html){
            document.getElementById('search-to-daten').value=parserDate(d);
            $('#ajax').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}

/**Настройки пикера**/
jQuery(document).ready(function () {
    'use strict';
    jQuery('#search-to-daten').datetimepicker();
    /**Локация**/
    jQuery.datetimepicker.setLocale('ru');
    /**Формат**/
    jQuery('#search-to-daten').datetimepicker({
        format:'Y-m-d',
        timepicker:false,
        dayOfWeekStart: 1,
        closeOnDateSelect: true,
        lang:'ru'
    });

    jQuery('#search-to-daten').datetimepicker({
        /**При выборе даты отправляем аякс запрос**/
        onSelectDate: function (ct,$i) {
            d=ct;
            $.ajax({
                type:'POST',
                url:urli,
                data:{
                    'dt':parserDate(getMonday(ct)),
                    'operation':operation
                },
                success:function(html){
                    $(idkomponent).html(html);
                },
                error:function(html){
                    $('body').css('cursor','default');
                    alert('Ошибка подключения!');
                },
            });
        },

        /**При выборе смене даты меняем дату на расширенную**/
        onChangeDateTime: function () {document.getElementById('search-to-daten').value=parserDate(d);},

        /**До того как показать день**/
        beforeShowDay: function(date) {

            if (date.getMonth() == getMonday(d).getMonth() && date.getDate() == getMonday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getThusday(d).getMonth() && date.getDate() == getThusday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getWensday(d).getMonth() && date.getDate() == getWensday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getThersday(d).getMonth() && date.getDate() == getThersday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getFriday(d).getMonth() && date.getDate() == getFriday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getSaturday(d).getMonth() && date.getDate() == getSaturday(d).getDate()) {return [true, ""]}
            if (date.getMonth() == getSunday(d).getMonth() && date.getDate() == getSunday(d).getDate()) {return [true, ""]}


            if (date.getMonth() == getMonday(date).getMonth() && date.getDate() == getMonday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getThusday(date).getMonth() && date.getDate() == getThusday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getWensday(date).getMonth() && date.getDate() == getWensday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getThersday(date).getMonth() && date.getDate() == getThersday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getFriday(date).getMonth() && date.getDate() == getFriday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getSaturday(date).getMonth() && date.getDate() == getSaturday(date).getDate()) {return [true, "trx"]}
            if (date.getMonth() == getSundayLastWeek(date).getMonth() && date.getDate() == getSundayLastWeek(date).getDate()) {return [true, "trx"]}


            return [true, ""];}
    });

});

