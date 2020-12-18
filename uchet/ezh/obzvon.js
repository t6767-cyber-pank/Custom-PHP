function Call(id, tema, dt, operation, urli) {
    let status=document.getElementById('sel'+id).value;
    $.ajax({
        type:'POST',
        url:urli,
        data:{
            'dt':dt,
            'idclient':id,
            'status': status,
            'tema': tema,
			'operation':operation,
			'call': 1
        },
        success:function(html){
            $('#ajax').html(html);
            alert("Сохранено");
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}

function menu(operation, urli) {
    $.ajax({
        type:'POST',
        url:urli,
        data:{
            'operation':operation
        },
        success:function(html){
            $('#ajax').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}