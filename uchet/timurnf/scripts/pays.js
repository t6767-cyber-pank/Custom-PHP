
function pay_vka(dt, sum, razdel, url, id, paymentst, returnblok){
    $('body').css('cursor','wait');
    $.ajax({
        type:'POST',
        url:url,
        data:{
            operation:'pay_vka',
            dt:dt,
            id:id,
            sum:sum,
            paymentst:paymentst,
            razdel: razdel
        },
        timeout:20000,
        success:function(html){
            $(returnblok).html(html);
            $('body').css('cursor','default');
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка соединения!');
        }
    });
}
