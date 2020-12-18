<?php
// Подключаем класс для работы с excel
require_once('PHPExcel/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
// Создаем объект класса PHPExcel
$xls = new PHPExcel();

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle("Отчет");
function get_sun ($dar)
{
    $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
    $adat = mysql_fetch_array($rdat);
    $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=7");
    $adat2 = mysql_fetch_array($rdat2);
    return $adat2['data'];
}
$rar1 = array();
$drar = array();
$crar = array();
$crarcount=0;
$coursex=1;

$r = mysql_query("SELECT DISTINCT c.name, c.id as cidcity FROM m_city c, masters m where m.id_m_city=c.id and m.usevk>0 and m.shown>0 order by c.name asc");
while($a = mysql_fetch_array($r)) {
    $ar2 = array();
    $ar2[0]=$a['cidcity'];
    $ar2[1]=$a['name'];
    array_push($crar, $ar2);
    $crarcount++;
}


$d1=date("Y-m-d", strtotime("+6 days"));
$d2=date("Y-m-d", strtotime("-1 month"));

if (isset($_GET['search-to-daten'])) $d2=$_GET['search-to-daten'];
if (isset($_GET['search-to-date'])) { $d1 =date("Y-m-d", strtotime($_GET['search-to-date'])); }
$d1=get_sun ($d1);
$q = "select data from timer where data between '$d2' and '$d1' order by  data";
$r = mysql_query($q);
while ($a = mysql_fetch_array($r)) {
    array_push($drar, $a['data']);
}
/**-------Наполним массив обработки дат**/

$podporiginals = array(); // Массив оригинала чатов
$podpsumm = array(); // Массив суммы подписчиков
$podptek = array(); // массив текущих подписок

/** Вывод массива оригинала чатов **/
$xr=0;
$dtm=date("Y-m-d", strtotime($dt)-3600*24); // дата воскресения прошлой недели
foreach ($crar as $ar) {
    $qxx = "select userg from cityimportvk where id_mcity=".$ar['0']." and data='".$dtm."' order by  data";
    $rxx1 = mysql_query($qxx);
    $ax1 = mysql_fetch_array($rxx1);
    $podporiginals[$xr]=(int)$ax1['userg'];
    $xr++;
}
/**-------Вывод оригинала чатов**/

$j=0;
$coursex=1;
foreach ($drar as $dar) {
    $rar3 = array();
    $rar2 = array();
    $rar4 = array();
    $rar5 = array();
    $rar6 = array();
    $rar7 = array();
    $rar8 = array();
    $car = array();
    $rar3[0][0]=$dar;
    $i=0;
    $irar8=0;
    $ixgq=0;
    $ixgq6=0;
    $ito=0; //vs
    $ito1=0; //sb
    $ito2=0; //pt
    $ito3=0; //ct
    $ito4=0; //sr
    $ito5=0; //vt
    $ito6=0; //pn
    $jto=0;


    $rxx = mysql_query("SELECT DISTINCT course FROM `master_week` where course>0 and dt='".$dar."'");
    if (mysql_num_rows($rxx)>0){
        $axxx = mysql_fetch_array($rxx);
        $coursex=$axxx['course'];
    } else {
        if ($coursex==1)
        {
            $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
            $adat = mysql_fetch_array($rdat);
            $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=1");
            $adat2 = mysql_fetch_array($rdat2);
            $rxx = mysql_query("SELECT DISTINCT course FROM `master_week` where course>0 and dt='".$adat2['data']."'");
            if (mysql_num_rows($rxx)>0){
                $axxx = mysql_fetch_array($rxx);
                $coursex=$axxx['course'];
            }
        }
    }

    $rar3[5][0]=$coursex;
    foreach ($crar as $ar) {
        $car[$jto]=$ar[1];
        $jto++;
//контакты
        $qxx = "select sum(chatsvk) as cvk from m_city_day_vk where id_m_city=".$ar['0']." and dt='".$dar."' order by  dt";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar2[$i]=$ax1['cvk'];
            $i++;
        }

//Расходы VK
        $qxx = "select sum(outcome) as outcome from cityimportvk where id_mcity=".$ar['0']." and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar5[$ixgq]=round((float)$ax1['outcome'],2);
            $ixgq++;
        }
//контакты чаты с вк
        $qxx = "select sum(uniq) as uniqx from vk_users where id_city=".$ar['0']." and dt='".$dar."' order by  dt";
        $rxx1 = mysql_query($qxx);
        while ($ax1 = mysql_fetch_array($rxx1)) {
            $rar8[$irar8]=$ax1['uniqx'];
            $irar8++;
        }
//Подписки VK
        $qxx = "select userg from cityimportvk where id_mcity=".$ar['0']." and data='".$dar."' order by  data";
        $rxx1 = mysql_query($qxx);
        $ax1 = mysql_fetch_array($rxx1);
        $rar6[$ixgq6]=(int)$ax1['userg'];
        $rar7[$ixgq6]=(int)$ax1['userg']-$podporiginals[$ixgq6];
        $podporiginals[$ixgq6]=$rar6[$ixgq6];

        if (strtotime($dar)<=strtotime(date('Y-m-d'))) {
            $podptek[$ixgq6]=$rar6[$ixgq6];
            $podpsumm[$ixgq6] += (int)$rar7[$ixgq6];
        }
        $ixgq6++;
// записи
        $qxx = "SELECT sum(zap_suvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
        $rxx1 = mysql_query($qxx);
        $ax1 = mysql_fetch_array($rxx1);
        $xd=date("N", strtotime($dar));
        if ($xd==7 && $j>=0) {
            $rar4[$ito]=$ax1['rvk'];
            $ito++;

            if (($j-1)>=0) {
                $qxx = "SELECT sum(zap_savk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=" . $ar['0'] . " and mp.dt='" . $dar . "' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j - 1][3][$ito1] = $ax1['rvk'];
                    $ito1++;
                }
            }

            if (($j-2)>=0) {
                $qxx = "SELECT sum(zap_frvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-2][3][$ito2]=$ax1['rvk'];
                    $ito2++;
                }}

            if (($j-3)>=0) {
                $qxx = "SELECT sum(zap_thvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-3][3][$ito3]=$ax1['rvk'];
                    $ito3++;
                }}

            if (($j-4)>=0) {
                $qxx = "SELECT sum(zap_wevk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-4][3][$ito4]=$ax1['rvk'];
                    $ito4++;
                }}

            if (($j-5)>=0) {
                $qxx = "SELECT sum(zap_tuvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=".$ar['0']." and mp.dt='".$dar."' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j-5][3][$ito5]=$ax1['rvk'];
                    $ito5++;
                }}

            if (($j-6)>=0) {
                $qxx = "SELECT sum(zap_monvk) as rvk FROM master_procedure_day mp, masters m where m.id=mp.id_master and m.id_m_city=" . $ar['0'] . " and mp.dt='" . $dar . "' ORDER BY `mp`.`dt` DESC";
                $rxx1 = mysql_query($qxx);
                while ($ax1 = mysql_fetch_array($rxx1)) {
                    $rar1[$j - 6][3][$ito6] = $ax1['rvk'];
                    $ito6++;
                }
            }

        }

    }
    $rar3[1]=$car;
    $rar3[2]=$rar2;
    $rar3[3]=$rar4;
    $rar3[4]=$rar5;
    $rar3[6]=$rar6;
    $rar3[7]=$rar7;
    $rar3[8]=$rar8;
    $rar1[$j]=$rar3;
    $j++;
}
$rx=0;

$ile1=0;
$ile2=1;
$ile3=2;
$ile4=3;
$ile5=4;
$ile6=5;
$ile7=6;
$ido=1;
foreach ($crar as $rc) {
    $sheet->setCellValueByColumnAndRow($ile1, $ido, $rc['1']);

    $sheet->getStyle('A'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('A'.$ido)->getFill()->getStartColor()->setRGB('5181b8');
    $sheet->getStyle('A'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('A'.$ido)->getFont()->getColor()->applyFromArray(array('rgb' => 'FFFFFF'));
    $sheet->getStyle('A'.$ido)->getFont()->setSize(16);

    $ido++;
    $sheet->setCellValueByColumnAndRow($ile1, $ido, "Число");
    $sheet->setCellValueByColumnAndRow($ile2, $ido, "Сообщения");
    $sheet->setCellValueByColumnAndRow($ile3, $ido, "Записи");
    $sheet->setCellValueByColumnAndRow($ile4, $ido, "Расх. (р)");
    $sheet->setCellValueByColumnAndRow($ile5, $ido, "Расх. (тг)");
    $sheet->setCellValueByColumnAndRow($ile6, $ido, "Подписчики");
    $sheet->setCellValueByColumnAndRow($ile7, $ido, "Прирост");
    $sheet->getStyle('A'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('A'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('B'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('B'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('C'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('C'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('D'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('D'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('E'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('E'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('F'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('F'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('G'.$ido)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('G'.$ido)->getFill()->getStartColor()->setRGB('EEEEEE');
    $sheet->getStyle('A'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('A'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('B'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('B'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('C'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('C'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('D'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('D'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('E'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('E'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('F'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('F'.$ido)->getFont()->setBold(true);
    $sheet->getStyle('G'.$ido)->getFont()->setSize(14);
    $sheet->getStyle('G'.$ido)->getFont()->setBold(true);
    $ido++;


    $rar11 = array_reverse($rar1);
    foreach ($rar11 as $r)
    {
        $dtseg=date('Y-m-d');
        if (strtotime($dtseg)<strtotime($r[0][0])) continue;
        $sheet->setCellValueByColumnAndRow($ile1, $ido, $r[0][0]);
        $sheet->setCellValueByColumnAndRow($ile2, $ido, (int)$r[2][$rx]);
        if ((int)$r[2][$rx]>0){$sheet->getStyle('B'.$ido)->getFont()->setBold(true);}
        $sheet->setCellValueByColumnAndRow($ile3, $ido, (int)$r[3][$rx]);
        if ((int)$r[3][$rx]>0){$sheet->getStyle('C'.$ido)->getFont()->setBold(true);}
        $sheet->setCellValueByColumnAndRow($ile4, $ido, (float)$r[4][$rx]);
        if ((float)$r[4][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}
        $sheet->setCellValueByColumnAndRow($ile5, $ido, round((float)$r[4][$rx]*(float)$r[5][0]));
        if ((float)$r[4][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}
        if ((float)$r[6][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}
        $sheet->setCellValueByColumnAndRow($ile6, $ido, $r[6][$rx]);
        if ((float)$r[6][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}

        if ((float)$r[7][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}
        $sheet->setCellValueByColumnAndRow($ile7, $ido, $r[7][$rx]);
        if ((float)$r[7][$rx]>0){$sheet->getStyle('D'.$ido)->getFont()->setBold(true);}

        $ido++;
    }
    $rx++;
}

$sheet->getColumnDimension('A')->setWidth(50);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(20);

header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=отчет.xls" ); //".$namecity."

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>