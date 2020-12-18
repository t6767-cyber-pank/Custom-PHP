<?php
$AJAX_TIMEOUT = 3000;
$operation = $_POST['operation'];
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

// functions
require $DOCUMENT_ROOT. '/components/master/functions.php';

if ($operation=='sendfile'){
  $dt = $_POST['dt'];
  $id_master = $_POST['id_master'];
  $file = $_FILES['fileinput'];
  $src = $file['tmp_name'];
  $size = getimagesize($src);
  if ($size!==false){
	  $q = "select m.id,u.name,m.by_percent,m.percent_val from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id_master";
	  $r = mysql_query($q);
	  $a = mysql_fetch_array($r);
	  $m_id = $a['id'];

      $q = "SELECT * FROM `master_week`  WHERE id_master=$m_id AND dt='$dt' LIMIT 1";
      $r = mysql_query($q);
      $a = mysql_fetch_array($r);
      $files = unserialize($a['files']);
      $fileindex = 0;
      if ($a['files'] == null){
        $files = [];
      }
      $fileindex = count($files);

    $width = 650;
    $height = 650;
    list($width_orig, $height_orig) = $size; // 1000, 700

    $isrc  = imagecreatefromstring(file_get_contents($src));
    $exif = @exif_read_data($src);
    if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
            case 8:
                $isrc = imagerotate($isrc,90,0);
                $temp = $width_orig; 
                $width_orig = $height_orig; 
                $height_orig = $temp; 
                break;
            case 3:
                $isrc = imagerotate($isrc,180,0);
                break;
            case 6:
                $isrc = imagerotate($isrc,-90,0);
                $temp = $width_orig;
                $width_orig = $height_orig;
                $height_orig = $temp;
                break;
        }
    }
    
    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
       $width = $height*$ratio_orig;
    } else {
       $height = $width/$ratio_orig;
    }

    // ресэмплирование
    //$isrc  = imagecreatefromstring(file_get_contents($src));
    $idest = imagecreatetruecolor($width, $height);
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    $filename = '/bills/' . $dt . $m_id ."_". ($fileindex+1) .".jpg";
    imagejpeg($idest,$_SERVER['DOCUMENT_ROOT'].$filename);
    array_push($files, $filename);
    $toPrint = "";
    foreach ($files as $key => $filename) {
        $toPrint .= "<img style='margin-bottom:20px;' src='$filename?r=".rand()."'><br/>";
    }
    print $toPrint;
    $files = serialize($files);
    mysql_query("update master_week w,masters m set w.bill_checked=1, w.files='$files' where w.id_master=m.id and m.id_master=$id_master and dt='$dt'");
  }else print "Неизвестный формат файла";
  exit;
}
if ($operation=='del_file'){
  $dt = $_POST['dt'];
  $id_master = $_POST['id_master'];
	
	$q = "select m.id,u.name,m.by_percent,m.percent_val from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id_master";
	$r = mysql_query($q);
	$a = mysql_fetch_array($r);
	$m_id = $a['id'];

  $q = "SELECT * FROM `master_week` WHERE id_master=$m_id AND dt='$dt' LIMIT 1";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $files = unserialize($a['files']);
  if ($files == null){
      $files = [ 0 => "/bills/$dt.$m_id.jpg" ];
  }
  foreach ($files as $key => $filename) {
    unlink($_SERVER['DOCUMENT_ROOT'].$filename);
  }
  mysql_query("update master_week w,masters m set w.bill_checked=0,w.files=null where w.id_master=m.id and m.id_master=$id_master and dt='$dt'");
  exit;
}
if ($operation=='show_masters'){
  $id = intval($_POST['id']);
  $dt = $_POST['dt'];
  $dt = preg_replace('/<.*?>/','',$dt); $dt = str_replace('"','',$dt); $dt = str_replace("'",'',$dt); $m = array(); preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m); $dt = $m[3].'-'.$m[2].'-'.$m[1]; print show_masters($id,$dt); exit; } ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head> 
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/js/jquery-ui.js"></script>
        <script type="text/javascript" src="/js/datepicker-ru.js"></script>
        <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="/style.css">
    </head>

    <body>
        <script type="text/javascript">
            $(function() {
                var startDate = new Date('<?=date("m/d/Y", strtotime(date('
                    o - \\WW '))-3600*24*6);?>');
                var endDate = new Date('<?=date("m/d/Y", strtotime(date('
                    o - \\WW '))-3600*24);?>');

                var selectCurrentWeek = function() {
                    window.setTimeout(function() {
                        $('#weekpicker').datepicker('widget').find('.ui-datepicker-current-day a').addClass('ui-state-active')
                    }, 1);
                }

                $('#weekpicker').datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    onSelect: function(dateText, inst) {
                        var date = $(this).datepicker('getDate');
                        startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1);
                        endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
                        var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                        $('#weekpicker').val($.datepicker.formatDate(dateFormat, startDate, inst.settings) + ' - ' + $.datepicker.formatDate(dateFormat, endDate, inst.settings));
                        show_user_block();

                        selectCurrentWeek();
                    },
                    beforeShow: function() {
                        selectCurrentWeek();
                    },
                    beforeShowDay: function(date) {
                        var cssClass = '';
                        if (date >= startDate && date <= endDate)
                            cssClass = 'ui-datepicker-current-day';
                        return [true, cssClass];
                    },
                    onChangeMonthYear: function(year, month, inst) {
                        selectCurrentWeek();
                    }
                }).datepicker('widget').addClass('ui-weekpicker');
                $("#weekpicker").datepicker($.datepicker.regional["ru"]);

                $('#weekbefore').click(function() {
                    s = $('#weekpicker').val().replace(/ .*/, '');
                    arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                    d = new Date(arr[2] + '/' + arr[1] + '/' + arr[3]);
                    t = d.getTime();
                    t1 = t - 7 * 24 * 3600 * 1000;
                    d1 = new Date(t1);
                    startDate = d1;
                    str1 = ('0' + d1.getDate()).slice(-2) + '.' + ('0' + parseInt(d1.getMonth() + 1)).slice(-2) + '.' + d1.getFullYear();
                    t2 = t - 24 * 3600 * 1000;
                    d2 = new Date(t2);
                    endDate = d2;
                    str2 = ('0' + d2.getDate()).slice(-2) + '.' + ('0' + parseInt(d2.getMonth() + 1)).slice(-2) + '.' + d2.getFullYear();
                    str = str1 + ' - ' + str2;
                    $('#weekpicker').val(str);
                    show_user_block();
                    return false;
                });

                $('#weekafter').click(function() {
                    s = $('#weekpicker').val().replace(/ .*/, '');
                    arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                    d = new Date(arr[2] + '/' + arr[1] + '/' + arr[3]);
                    t = d.getTime();
                    t1 = t + 7 * 24 * 3600 * 1000;
                    d1 = new Date(t1);
                    startDate = d1;
                    str1 = ('0' + d1.getDate()).slice(-2) + '.' + ('0' + parseInt(d1.getMonth() + 1)).slice(-2) + '.' + d1.getFullYear();
                    t2 = t + 13 * 24 * 3600 * 1000;
                    d2 = new Date(t2);
                    endDate = d2;
                    str2 = ('0' + d2.getDate()).slice(-2) + '.' + ('0' + parseInt(d2.getMonth() + 1)).slice(-2) + '.' + d2.getFullYear();
                    str = str1 + ' - ' + str2;
                    $('#weekpicker').val(str);
                    show_user_block();
                    return false;
                });

                $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mousemove', function() {
                    $(this).find('td a').addClass('ui-state-hover');
                });
                $('#ui-datepicker-div .ui-datepicker-calendar tr').live('mouseleave', function() {
                    $(this).find('td a').removeClass('ui-state-hover');
                });
            });

            function show_user_block() {
                dt = $("#weekpicker").val().replace(/ .*/, '');
                $.ajax({
                    type: 'POST',
                    url: '<?= $_SERVER['PHP_SELF'] ?>',
                    data: {
                        dt: dt,
                        id: <?=$id?>,
                        operation: 'show_masters'
                    },
                    timeout: <?=$AJAX_TIMEOUT?>,
                    success: function(html) {
                        $('body').css('cursor', 'default');
                        $('#user_block').html(html);
                    },
                    error: function(html) {
                        $('body').css('cursor', 'default');
                        alert('Ошибка соединения!');
                    }
                });
            }
        </script>
        <div class="container-fluid">
          <table class="table">
            <tr>
              <td class="text-right" style="vertical-align: middle;">
                <a href='' class="btn btn-sm btn-primary" id='weekbefore' style='text-decoration:none;'>
                  <i class="glyphicon glyphicon-arrow-left"></i>
                </a>
              </td>
              <td class="text-center">
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                  </div>
                  <input type="text" id="weekpicker" class="form-control" value='<?=date("d.m.Y", strtotime(date(' o-\\WW '))-3600*24*7);?> - <?=date("d.m.Y", strtotime(date('o-\\WW '))-3600*24)?>'>
                </div>
              </td>
              <td  style="vertical-align: middle;">
                <a href='' id='weekafter' class="btn btn-sm btn-primary"  style='text-decoration:none;'>
                  <i class="glyphicon glyphicon-arrow-right"></i>
                </a>
              </td>
            </tr>
          </table>
          <div class="form-group text-center">
            <a href='?logout=1' class="btn btn-danger">
              Выход
              <i class="glyphicon glyphicon-log-out"></i>
            </a>
          </div>

          <div id='user_block'>
              <?=show_masters($id,date("Y-m-d", strtotime(date('o-\\WW'))-3600*24*7))?>
          </div>
        </div>
    </body>

    </html>
    <?php
function show_masters($id,$dt){
  $q = "select m.id,u.name,m.by_percent,m.percent_val from users u join masters m on u.id=m.id_master and u.type=0 and u.id=$id";
  $r = mysql_query($q);
  $a = mysql_fetch_array($r);
  $m_id = $a['id'];
  $m_name = $a['name'];
  $m_by_percent = $a['by_percent'];
  $m_percent_val = $a['percent_val'];

  $r = mysql_query("select outcome,paid,course,bill_checked,sum_no_self,files from master_week where id_master=$m_id and dt='$dt'");
  if (mysql_num_rows($r)>0){
    $a = mysql_fetch_array($r);
    $outcome = intval($a['outcome']);
    $paid = intval($a['paid']);
    $course = $a['course'];
    $files = unserialize($a['files']);
    if ($files == null){
        $files = [ 0 => "/bills/$dt.$id.jpg" ];
    }
    $bill_checked = $a['bill_checked'];
    $sum_no_self = intval($a['sum_no_self']);
    if ($bill_checked>0){
        $bill_pic = "";
        foreach ($files as $key => $filename) {
            $bill_pic .= "<img style='margin-bottom:20px;max-width:400px;width:100%;' src='$filename?r=".rand()."'><br/>";
        }
      if($bill_checked==1)$bill_status = '<span style="color:#FE9301">На рассмотрении</span>';
      if($bill_checked==2)$bill_status = '<span style="color:#4EB001">Подтверждено</span>';
    }
    $bill_flag = 1;
  }else{
    $outcome = 0;
    $paid=0;
    $course=0;
    $bill_flag=0;
    $sum_no_self=0;
  }

  ob_start();
?>
        <div class="form-group"><b ><?=$m_name?></b></div>
        <div class="form-group">
            <div id="finance_block" class="well" style="background-color: #FFF;">
                Чистый доход (уже с вычетом комиссий): <br />

                <strong>Текущий месяц: <?= GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val, "current_month"); ?></strong> <br />
                За последние 12 недель: <?= GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val,"last_12_weeks"); ?> <br />
                За все время: <?= GetMasterProfit($m_id, $dt, $m_by_percent, $m_percent_val,"all"); ?>  <br />
            </div> 
            <div id='picture_status'  class="form-group">
                <?=$bill_status?>
            </div>
            <?php
  $arr_text = array();
  $sum_visitors = 0;
  $sum = 0;
  $sum_comission = 0;
  $arr_comission = array();
  $arr_comission1 = array();
  $sum_bonus = 0;
  $r = mysql_query("select p.name,p.price,p.bonus,p.comission,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$dt' where p.id_master=$m_id");
  while($a = mysql_fetch_array($r)){
    $price = intval($a['price']);
    $name = $a['name'].' ('.$price.')';
    $comission = intval($a['comission']);
    $visitors = intval($a['visitors']);
    if ($visitors>0){
      $str = ' - '.$name.' x '.$visitors.' шт = '.$visitors*$price;
      $arr_text[] = $str;
      $sum_visitors += $visitors;
      $sum += $visitors*$price;
      $sum_comission += $visitors*$comission;
      if ($course>0){
        $arr_comission[] = $visitors*$comission;
        $arr_comission1[] = $visitors*$comission*$course;
      }else{
        $arr_comission[] = $visitors*$comission;
        $arr_comission1[] = $visitors*$comission;
      }
    }
  }
  if($m_by_percent==1){
    $sum_comission1 = $sum_no_self*$m_percent_val/100;
  }else{
    $sum_comission1 = $sum_comission;
  }
  //if ($course>0)$sum_comission1 *= $course;
  //if ($course>0)$sum_no_self *= $course;
?> 
                <div  class="form-group">
                    <div class='well' style="background-color: #FFF;">
                        Всего процедур:
                        <?=$sum_visitors?><br>
                            <?=implode("<br>\n",$arr_text)?><br>
                        <?php if ($m_by_percent==1){  echo "Сумма с процедур";  } else { echo "Сумма"; } ?>
<?=$sum ?><br>
<?php
$dtrazn=date_diff(new DateTime(), new DateTime($dt))->days;
$proc=0;
$dnstr="дня";
if ($dtrazn>8) { $proc=2; }
if ($dtrazn>9) { $proc=3; }
if ($dtrazn>10) { $proc=4; }
if ($dtrazn>11) { $proc=5; $dnstr="дней"; }
if ($dtrazn>12) { $proc=6; $dnstr="дней"; }
$dtrazn=$dtrazn-7;

if ($m_by_percent==1)
{
echo "Сумма минус себестоимость".$sum_no_self."<br>";
echo "Комиссия ".$sum_comission1."<br>";
    if (($bill_checked==0) && ($sum_comission1>0) && ($proc>0)) {
        $xsum_com = $sum_comission1 / 100 * $proc;
        echo "<b>Пеня: " . $xsum_com . " ($proc% от комиссии за $dtrazn $dnstr просрочки)</b><br>";
        $xitogtg=$sum_comission1+$xsum_com;
        echo "<b>Итого: " .$xitogtg. "</b><br>";
    }
}else{
echo "Комиссия ".$sum_comission."(".implode(" + ",$arr_comission).")<br>";
    if (($bill_checked==0) && ($sum_comission>0) && ($proc>0)) {
        $xsumc = $sum_comission / 100 * $proc;
        echo "<b>Пеня: " . $xsumc." ($proc% от комиссии за $dtrazn $dnstr просрочки)</b><br>\n"; //$bill_checked
        $xitogtg=$sum_comission+$xsumc;
        echo "<b>Итого: " .$xitogtg. "</b><br>\n";
    }
}
?>
                    </div>
                </div>
                <div style="clear:both"></div>
        </div>
        <script>
            var xhr = new XMLHttpRequest();
            //http://learn.javascript.ru/ajax-xmlhttprequest
            xhr.timeout = 300000;
            xhr.onloadstart = function() {
                document.getElementById("openfile").style.display = 'none';
                document.getElementById("abortfile").style.display = '';
            }
            xhr.onabort = function() {
                document.getElementById("openfile").style.display = '';
                document.getElementById("abortfile").style.display = 'none';
            }
            xhr.onloadend = function() {
                document.getElementById("openfile").style.display = '';
                document.getElementById("abortfile").style.display = 'none';
            }
            xhr.onload = function() {
                document.getElementById("picture").innerHTML = xhr.responseText;
                document.getElementById("picture_status").innerHTML = "<span style='color:#FE9301'>На рассмотрении</span>";
                //document.getElementById("have_bill_div").style.display = 'none';
                document.getElementById("no_bill_div").style.display = '';
            }
            xhr.ontimeout = function() {
                alert("Загрузка занимает слишком много времени");
            }
            xhr.onerror = function() {
                alert("При загрузке произошла ошибка");
            }

            function sendfile() {
                var formData = new FormData(document.getElementById("fileform"));
                formData.append("operation", "sendfile");
                s = $('#weekpicker').val().replace(/ .*/, '');
                arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                d = arr[3] + '-' + arr[2] + '-' + arr[1];
                formData.append("dt", d);
                formData.append("id_master", <?=$id?>);
                xhr.open("POST", "<?=$_SERVER['PHP_SELF']?>?r=" + Math.random());
                xhr.send(formData);
            }

            function del_file() {
                if (!confirm("Действительно удалить?")) return false;
                var xhr1 = new XMLHttpRequest();
                xhr1.timeout = 10000;
                var formData = new FormData();
                s = $('#weekpicker').val().replace(/ .*/, '');
                arr = s.match(/(\d{2})\.(\d{2})\.(\d{4})/);
                d = arr[3] + '-' + arr[2] + '-' + arr[1];
                formData.append("dt", d);
                formData.append("id_master", <?=$id?>);
                formData.append("operation", "del_file");
                xhr1.onload = function() {
                    document.getElementById("picture").innerHTML = xhr1.responseText;
                    document.getElementById("picture_status").innerHTML = '';
                    document.getElementById("have_bill_div").style.display = '';
                    document.getElementById("no_bill_div").style.display = 'none';
                }
                xhr1.open("POST", "<?=$_SERVER['PHP_SELF']?>?r=" + Math.random());
                xhr1.send(formData);
            }
        </script> 
        <div id='picture' class="form-group">
            <?=$bill_pic?>
        </div> 
        <div class="form-group">
            <form id='fileform'> 
                <div id='have_bill_div'  class="form-group">
                        <input class='orange' id='abortfile' type='button' value='Отмена' onclick='xhr.abort();' style='display:none;'>
                        <input class='orange' id='openfile' type='button' value='Загрузить чек' onclick='document.getElementById("fileinput").click();'>
                        <input id="fileinput" name="fileinput" style="display:none;" type="file" onchange='sendfile();'>
                </div>
                <div id='no_bill_div' class="form-group" <?php if ($bill_checked==0 || $bill_flag!=1){?> style='display:none'
                    <?php } ?>>
                    <input class='orange' id='delfile' type='button' value='Удалить все' onclick='del_file();'>
                </div>
            </form>
        </div>
        <?php
  $res = ob_get_contents();
  ob_end_clean();
  return $res;
}
?>
