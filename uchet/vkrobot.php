<html>
<head>
    <meta charset="utf-8">
    <script type="text/javascript" src="js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
	<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
    <script type="text/javascript">

        loadFriends();
        function squrl(method,params)
        {
            params=params || {};
            params['access_token']="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
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
                for (var j=0; j<f.stats.length; j++) {
                    $.ajax({
                        url: "vkposter.php",
                        data: {
                            vkidcity: f.id,
                            datx: f.stats[j].day,
                            outcome: f.stats[j].spent,
                            id_mcity: mc,
                        },
                        success: function () {}
                    });
                }
            }
        }

    </script>
</head>
<body style="font-size: 12px;">
</body>
</html>
