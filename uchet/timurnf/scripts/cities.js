function save_m_city(id,name, procent, url){
    $('body').css('cursor','wait');
    data = {
        operation:'save_m_city',
        id:id,
        name:name,
        procent:procent
    };
    $.ajax({
        type:'POST',
        url:url,
        data:data,
        timeout:20000,
    success:function(html){
    $('body').css('cursor','default');
    alert('Изменения сохранены!');
    },
    error:function(html){
    $('body').css('cursor','default');
      alert('Ошибка сохранения!');
    }
    });
    return false;
}

function add_m_city(url){
    var str = '';
    str += "<div id='"+Math.floor(Math.random()*10000000)+"' class=''>\n";
    str += "<input type='hidden' class='m_id' value='0'>\n";
    str += "<div class='T_M_admin_City_block'>\n";
    str += "Название: <input type='text' class='m_name' value=''>\n";
    str += "</div>\n";
    str += "<div class='T_M_admin_City_block'>\n";
    str += "Коэффициент: <input type='text' class='m_proc T_M_input_numb' value='1'>\n";
    str += "</div>\n";
    str += "<div class='T_M_admin_City_save_btn'>\n";
    str += "<input type='button' class='orange' value='Сохранить' onclick='save_m_city(0,$(this).parent().parent().find(\".m_name\").get(0).value,$(this).parent().parent().find(\".m_proc\").get(0).value, \""+url+"\")'>\n";
    str += "</div>\n";
    str += "</div>\n";
    $('#m_cities div:last').after(str);
}
