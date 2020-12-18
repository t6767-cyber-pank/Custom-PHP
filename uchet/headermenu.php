<?php
$v=0;
$d=0;
if (isset($_GET['m'])) $v=1;
if (isset($_GET['d'])) $d=1;
/*
function rec($a)
{
	$x="button7";
	if ($_SERVER['REQUEST_URI']==$a) { 
	$x='button77'; } else { $x='button7'; }
	echo $x;
}
*/
?>
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
            if (cur_url == link) {
                $(this).addClass('current');
            }
        });
    });
</script>
<div id="menu" class="menu" style="padding-bottom:40px;display: -webkit-box;padding-top:25px; background-color:white;" align="center">
    <ul>
        <?php
if ($v==0)
{
?>
<li> <a class="button7" href="index.php">Вернуться в основное меню</a></li>
<li> <a class="button7" href="/diagramchats.php?rash=1">Диаграммы чатов instagram</a></li>
<li> <a class="button7" href="/diagramchatsvk.php?rash=1">Диаграммы чатов ВК</a></li>
<li> <a class="button7" href="/diagramchatskonver.php">Диаграмма конверсий</a></li>
<li> <a class="button7" href="/diagramchatsezh2.php?&rash=1">Диаграмма чатов Еж</a></li>
<li> <a class="button7" href="/diagramProdagEzh.php">Диаграмма продаж Еж</a></li>
 <?php 
} else 
{ if ($v!=0) {
    ?>
         <li> <a class="button7" href="/program.php?razdel=1">Вернуться в основное меню</a></li>
         <li> <a class="button7" href="/diagramchatsezh2.php?m=0">Диаграмма контактов</a></li>
         <li> <a class="button7" href="/diagramProdagEzh.php?m=0">Диаграмма продаж</a></li>
    <?php
}}
 ?>
     </ul>
 </div>
 <br/>