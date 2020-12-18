<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$AJAX_TIMEOUT = 20000;
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
$iduser=36;
if (isset($_GET["id"])){ $iduser=$_GET["id"]; if ($iduser==1) $iduser=36; }
$r1 = mysql_query("select * from users where id=$iduser");
$a1 = mysql_fetch_array($r1);
$nameUser=$a1['name'];
$timereal=new timereal();

$dt=date("d.m.Y");
if (isset($_GET["dt"])) {$dt = $_GET['dt'];}
$dt = preg_replace('/<.*?>/','',$dt);
$dt = str_replace('"','',$dt);
$dt = str_replace("'",'',$dt);
$m = array();
preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
$dt = $m[3].'-'.$m[2].'-'.$m[1];
$dt_to = date('Y-m-d',strtotime($dt)+3600*24*6);
$timereal->set_dt($dt);
$dt=$timereal->get_monday();
$dt_to=$timereal->get_sunday();
$dateNachala=$dt;
$dateKonca=$dt_to;


$itogo=0;
$r = mysql_query("SELECT p.name, p.price, p.self_price, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '".$dateNachala."' and '".$dateKonca."' and p.pokaz>0 and done=1 group by p.name ORDER BY ttt DESC");
while ($a = mysql_fetch_array($r))
{
    $itog=($a['price']-(int)$a['self_price'])*$a['ttt'];
    $itogo=$itogo+$itog;
}
$summa=$itogo;
$itogo=0;
$r = mysql_query("SELECT p.name, p.price, p.self_price, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '".$dateNachala."' and '".$dateKonca."' and p.pokaz>0 group by p.name ORDER BY ttt DESC");
while ($a = mysql_fetch_array($r))
{
    $itog=($a['price']-(int)$a['self_price'])*$a['ttt'];
    $itogo=$itogo+$itog;
}
$summaTek=$itogo; //////// Сумма с текущими
$maxprocent=0;
$s1 = array();
$s2 = array();
$r = mysql_query("SELECT * FROM bonusoperatorezh where iduser=".$iduser." ORDER BY id ASC");
$ax = mysql_fetch_array($r);
$bonus_id=$ax['idbonus'];
$r = mysql_query("SELECT * FROM bonusezh where id=".$bonus_id." ORDER BY id ASC");
$procentotsumm=0;
$ax = mysql_fetch_array($r);
$procentotsumm=$ax['base_percent'];
$r = mysql_query("SELECT * FROM bonus_rewardsezh where bonus_id=".$bonus_id." ORDER BY id ASC");
$counter=0;
$prev=0;
$prevTek=0;
$flagFont=0;
$flagFontTek=0;
$stage=0;
$stageTek=0;
$stageprev=0;
$stageprevTek=0;
$procentostatok=0;
$procentostatokTek=0;
$ravno=0;
$summaporog=0;
while ($a = mysql_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
{
    array_push($s1, $a['summ']);
    array_push($s2, $a['reward']);
    if (($summa>$prev) and ($summa<=$a['summ'])) { $flagFont=$counter; if ($summa==$a['summ']) {$ravno=1; } $stage=$a['summ']-$prev; $stageprev=$summa-$prev; }
    if (($summaTek>$prev) and ($summaTek<=$a['summ'])) { $flagFontTek=$counter; $stageTek=$a['summ']-$prevTek; $stageprevTek=$summaTek-$prevTek; }
    $prev=$a['summ'];
    $prevTek=$a['summ'];
    $counter++;
}
if ($stageprev>0 && $stage>0) $procentostatok=round($stageprev/($stage/100)); else $procentostatok=0;
if ($stageprevTek>0 && $stageTek>0) $procentostatokTek=round($stageprevTek/($stageTek/100)); else $procentostatokTek=0;
$razmer=700;
$rastoyanie=round(100/(count($s1)+1));
$blokw=round($razmer*$rastoyanie/100); //+(round($razmer*$rastoyanie/100)/100*$procentostatok)
$rastoyanieW=$blokw*$flagFont+($blokw/100*$procentostatok);
if ($summa>$prev) {$rastoyanieW=$razmer; $flagFont=$counter; }
$rastoyanieWTek=$blokw*$flagFontTek+($blokw/100*$procentostatokTek);
if ($summaTek>$prevTek) {$rastoyanieWTek=$razmer; $flagFontTek=$counter; }

$operation = $_GET['operation'];

if ($operation=='show_masters'){
    $dt = $_GET['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    $dt_to = date('Y-m-d',strtotime($dt)+3600*24*6);
    $dateNachala=$dt;
    $dateKonca=$dt_to;
    showub($nameUser, $summa, $razmer, $rastoyanie, $summaTek, $rastoyanieWTek, $s1, $flagFont, $ravno, $procentotsumm, $s2, $dateNachala, $dateKonca, $rastoyanieW);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <link rel="stylesheet" type="text/css" href="stezh/style.css">
	      <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	<link href="dist/css/datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="dist/js/datepicker.min.js"></script>

    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/moment.min.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <script type="text/javascript" src="/js/jquery.daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/js/daterangepicker.min.css">
    <link rel="stylesheet" type="text/css" href="/style.css">

	  <style>
    .root{
        width: 100%;
        background-color: #FFF;
        padding: 20px 50px;
        max-width: 1100px;
    }
    .table{
        width: 100%;
        background-color: #FFF;
        border-collapse: collapse;
    }
    .table td{
        vertical-align: top;
    }
    .progress-container{
        position: relative;
    }
    .progress-container .progress{
        position: absolute;
        left: 0;
        top: 0;
        height: 44px;
        background-color: #61d836;
        border-right: 2px solid #61d836;
    }
    .slider-td{
        padding-right: 20px;
    }
    .slider tr:nth-child(1){
        background-color: rgba(33, 41, 37, 0.11);
    }
    .slider td{
        border: none;
    }
    .slider tr:nth-child(1) td,
    .slider tr:nth-child(2) td{
        padding: 13px 10px;
    }
    .slider tr:nth-child(1) td:not(:first-child):before{
        content: '';
        border: 1px solid #FFF;
        margin: 15px 0 0 -11px;
        height: 15px;
        position: absolute;
    }
    .masters{
        margin-top: 50px;
    }
    .masters tr:nth-child(2n){
        background-color: #f2f2f2;
    }
    .masters td, 
    .bonuses td{
        padding: 10px;
    }
    .masters td{
        font-size: 13px;
    }
    .masters td:nth-child(2){
        border-right: 20px solid #FFF;
    }
    .bonuses td{    
        font-size: 12px;
        line-height: 24px;
    }
    .price-progress-container{
        position: relative;
        height: 55px;
    }
    .price-progress-container .price{
        padding: 14px 10px;
        position: absolute;
        font-weight: bold;
        margin-bottom: -5px;
        border-left: 2px solid #61d836;
        font-size: 1.7rem;
        height: 60px;
    }
    .price-progress-container .price > .left{
        position: absolute;
        transform: translateX(-100%);
        left: -10px;
    }
    .value{
        position: relative;
    }
    .bonuses td:first-child{
        text-align: right;
        font-weight: bold;
        font-size: 20px;
    }
    .table.no-width{
        width: 700px;
    }
    .masters td:nth-child(2) {
         border-right: 0px solid #FFF !important;
     }
    th{
        font-size: 13px;
        vertical-align: top;
        padding: 10px;
        font-weight: 400;
    }
</style>
  </head>
  <body style="cursor: default;">
<div align="center" style="margin-top: 5px;";>
    <a href="/program.php">Назад в главное меню</a><br><br>
</div>




<script type="text/javascript">
    $(function() {
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
                show_user_block();
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

        // листалка влево

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
            show_user_block();
            return false;
        });

        // листалка вправо

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
            show_user_block();
            return false;
        });

// ВЫделить линию на вводе даты

        $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
        $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
    });

    // Обработчик при смене даты
    function show_user_block(){
        var oper='show_masters';
        dt = $("#weekpicker").val().replace(/ .*/,'');
        $("#loader").show();
        $.ajax({
            type:'GET',
            url:'<?=$PHP_SELF?>',
            data:{
                dt:dt,
                id:"<?php echo $iduser; ?>",
                operation:'show_masters',
            },
            timeout:<?=$AJAX_TIMEOUT?>,
            success:function(html){
                $('#user_block').html(html);
            },
            error:function(html){
                alert('Ошибка соединения!');
            }
        });
    }

</script>
<!--Компонент -->
<div style="text-align: center;">
    <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
    Дата: <input type="text" id="weekpicker" style='width:200px;' value='<?=date("d.m.Y", strtotime(date('o-\\WW')));?> - <?=date("d.m.Y", strtotime(date('o-\\WW'))+3600*24*6)?>'>
    <a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
</div>



  <div id="user_block" class="user_block">
<?php
showub($nameUser, $summa, $razmer, $rastoyanie, $summaTek, $rastoyanieWTek, $s1, $flagFont, $ravno, $procentotsumm, $s2, $dateNachala, $dateKonca, $rastoyanieW);
/*Пользовательский блок*/
function showub($nameUser, $summa, $razmer, $rastoyanie, $summaTek, $rastoyanieWTek, $s1, $flagFont, $ravno, $procentotsumm, $s2, $dateNachala, $dateKonca, $rastoyanieW)
{
    $payzp = new payzp($dateNachala, "", "");
    $pr_order=new pr_order();
    $pr_order->set_dt($dateNachala);
    $pr_order->set_dt_to($dateKonca);
    ?>
    <div id="manager2" style="padding-bottom:30px;width:1000px;">
        <div style="margin-bottom:10px;"><strong><?= $nameUser ?></strong></div>
        <div class="root">
            <div class="price-progress-container">
                <span class="price" style="left: <?= $rastoyanieW ?>px;"><span class="left"><?= $pr_order->getSummMargaperiod() ?></span></span>
                <!-- <span style="float: right; font-size: 1.0rem; font-weight: bold; background: #f8ba00; padding: 10px;">с текущими заказами: <?= $summaTek ?></span> -->
            </div>
            <table class="table main">
                <tbody>
                <tr>
                    <td style="width: <?= $razmer ?>px;" class="slider-td">
                        <table class="table slider no-width">
                            <tbody>
                            <tr>
                                <td width="<?= $rastoyanie; ?>%" class="progress-container">
                                    <div class="progress"
                                         style="width: <?= $rastoyanieWTek ?>px; background: #f8ba00; border-right: 2px solid #f8ba00;"></div>
                                    <div class="progress" style="width: <?= $rastoyanieW ?>px;"></div>
                                </td>
                                <?php
                                $c = 0;
                                foreach ($s1 as $v) {
                                    $c++;
                                    if (($flagFont == $c) && ($ravno == 0)) {
                                        $style = "700";
                                    } else {
                                        if ((($flagFont + 1) == $c) && ($ravno > 0)) $style = "700"; else $style = "normal";
                                    }
                                    ?>
                                    <td width="<?= $rastoyanie ?>%"><span class="value"
                                                                          style="font-weight:<?= $style ?>;"><?= $v ?></span>
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td></td>
                                <?php
                                $c = 0;
                                foreach ($s2 as $m) {
                                    $c++;
                                    if (($flagFont == $c) && ($ravno == 0)) {
                                        $style = "700";
                                        $summaporog = $m;
                                    } else {
                                        if ((($flagFont + 1) == $c) && ($ravno > 0)) {
                                            $style = "700";
                                            $summaporog = $m;
                                        } else $style = "normal";
                                    }
                                    ?>
                                    <td style="font-weight:<?= $style ?>;"><?= $m ?></td>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table class="table bonuses">
                            <tbody>
                            <tr style="background-color: #61d836;">
                                <td><?php $it = $summa * $procentotsumm / 100;
                                    echo $payzp->paySellerN2bezProc(36); ?></td>
                                <td><?= $procentotsumm; ?>% от суммы</td>
                            </tr>
                            <tr>
                                <td><?=$pr_order->skidkaPeriod()/100*$procentotsumm; ?></td>
                                <td>- скидки</td>
                            </tr>
                            <tr style="background-color: #e2e2e2;">
                                <td><?php echo $payzp->paySellerN(36); ?></td>
                                <td><strong style="font-size: 18px;">ИТОГО</strong><br>
                                    <script>
                                        var bflag = 1;
                                    </script>
                                    <button onclick="if (bflag==0) { document.getElementById('masters').style.display='table'; bflag=1; } else { document.getElementById('masters').style.display='none'; bflag=0; }  ">
                                        показать детали
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

            <table id="masters" class="table masters" style="display: table;">
                <th>Наименование товара</th>
                <th>Цена за единицу</th>
                <th>Себестоимость</th>
                <th>Количество</th>
                <th>Итого (маржа)</th>
                <?php
                $r = mysql_query("SELECT p.name, p.price, p.self_price, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '$dateNachala' and '$dateKonca' and p.pokaz>0 and done=1 group by p.name ORDER BY ttt DESC");
                while ($a = mysql_fetch_array($r)) {
                    $itog = ($a['price'] - (int)$a['self_price']) * $a['ttt'];
                    echo "<tr>";
                    echo "<td>";
                    echo $a['name'];
                    echo "</td>";
                    echo "<td style='width: 15%; text-align: center;'>";
                    echo $a['price'];
                    echo "</td>";
                    echo "<td style='width: 15%; text-align: center;'>";
                    echo (int)$a['self_price'];
                    echo "</td>";
                    echo "<td style='width: 15%; text-align: center;'>";
                    echo $a['ttt'];
                    echo "</td>";
                    echo "<td style='width: 15%; text-align: center;'>";
                    echo $itog;
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <?php
}
/*Конец пользовательского блока*/
?>
  </div>

</body></html>