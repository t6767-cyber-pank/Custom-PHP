<style>
    .current a {
        color: #4caf50;
        font-weight: 700;
    }
    ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    li {
        float: left;
    }

    li a {
        display: block;
        text-align: center;
        margin-right: 10px !important;
    }

    li a:hover {
        color: #111111;
    }
</style>
<script>
    $(function () {
        var location = window.location.href;
        var cur_url = '/' + location.split('/').pop();
        $('.menu li').each(function () {
            var link = $(this).find('a').attr('href');
            if (link.substr(1, 13) == "diagramchats2") {
            if (cur_url.substr(1, 13) == "diagramchats2") {
                $(this).addClass('current');
            }}
            if (link.substr(1, 19) == "diagramchatskonver2") {
                if (cur_url.substr(1, 19) == "diagramchatskonver2") {
                    $(this).addClass('current');
                }}
        });
    });
</script>
<div id="menu" class="menu" style="padding-bottom:40px;padding-top:25px;display: -webkit-box;background-color:white;" align="center">
 <ul>
     <li> <a class="button7" href="index.php">Вернуться в основное меню</a></li>
     <li> <a class="button7" href="/diagramchats2.php?name=<?php echo $nameuser; ?>">Диаграммы чатов</a></li>
     <li> <a class="button7" href="/diagramchatskonver2.php?name=<?php echo $nameuser; ?>">Диаграмма конверсий</a></li>
 </ul>
 </div>