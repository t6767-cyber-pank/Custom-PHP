<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.1//EN">
<html>
<head>
<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
</head>
<body>
<h2>Получить толкен доступа</h2>
https://oauth.vk.com/authorize?client_id=xxxxxxxxxxxx&display=page&redirect_uri=https://oauth.vk.com/blank.html&scope=friends,ads&response_type=token&v=5.52
<br>
<h2>Вывод списка друзей и фото</h2>
https://api.vk.com/method/friends.search?count=60&fields=photo_100&access_token=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx&v=5.52
<br>
<h2>Вывод списка расходов по дням</h2>
https://api.vk.com/method/ads.getStatistics?account_id=1900002437&ids_type=campaign&ids=1012644516,1012644308&period=day&date_from=0&date_to=0&access_token=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx&v=5.52
<br>
<h2>Вывод списка расходов по всем городам общее</h2>
https://api.vk.com/method/ads.getStatistics?account_id=1900002437&ids_type=office&ids=1900002437&period=overall&date_from=0&date_to=0&access_token=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx&v=5.52
<br>
<p><button id="load">Показать</button></p>
<ul></ul>
<script>
    loadFriends();
    $('#load').on("click", loadFriends);

function squrl(method,params)
{
    params=params || {};
    params['access_token']="c34f77ca967c2696bf4d2498392c15556bb02669ffd8bac4dd76964b9e6abe276804542e6724452b6a274";
    params['v']="5.52";
    return "https://api.vk.com/method/"+method+"?"+$.param(params);
}

function sendRequest(method,params, func)
{
    $.ajax({
        url: squrl(method,params),
        method: 'GET',
        dataType: 'JSONP',
        data:{
            'operation':'mk',
        },
        success: func
        }
    );
}

function loadFriends(){
    sendRequest("ads.getStatistics", {account_id: '1900002437', ids_type: 'campaign', ids:'1012705297, 1012705067, 1012644516,1012644308, 1012631446, 1012596378, 1012589857, 1012555356, 1012530143, 1012524055, 1012497203, 1012454829', period:'day', date_from:0, date_to:0}, function (data) {
        drawFriends(data.response);
    });
}

function drawFriends(friends) {
    //console.log(friends[0]);
    var html='';
    var mc=0;
    for (var i = 0; i<friends.length; i++)
    {
        var  f= friends[i];
        var namec="";
        switch (f.id)
        {
            case    1012705297: namec="Балашиха"; mc=54; break;
            case    1012705067: namec="Караганда татуаж"; mc=49; break;
            case    1012644516: namec="Барнаул"; mc=11; break;
            case    1012644308: namec="Томск"; mc=10; break;
            case    1012631446: namec="Волгоград"; mc=13; break;
            case    1012596378: namec="Иркутск"; mc=9; break;
            case    1012589857: namec="Красноярск"; mc=8; break;
            case    1012555356: namec="РнД"; mc=16; break;
            case    1012530143: namec="Алматы"; mc=1; break;
            case    1012524055: namec="ЕКБ"; mc=6; break;
            case    1012497203: namec="Казань"; mc=7; break;
            case    1012454829: namec="НСК"; mc=5; break;
        }
        html +='<li>'+f.id+"   <b>"+namec+'</b>';
        html +='<table>';
        for (var j=0; j<f.stats.length; j++) {
            html += '<tr>';
            html += '<td>';
            html += f.stats[j].day;
            $.ajax({
                url: "/vkposter.php",
                data: {
                    vkidcity: f.id,
                    datx: f.stats[j].day,
                    outcome: f.stats[j].spent,
                    id_mcity: mc,
                },
                success: function () {}
            });
            html += '</td>';
            html += '<td style="background: beige;">';
            html += f.stats[j].spent;
            html += '</td>';
            html += '</tr>';
        }
        html +='</table>';
        html +='</li>';
    }
    $('ul').html(html);
}

</script>
</body>
</html>
