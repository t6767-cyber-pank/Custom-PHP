<?php
// Подключаем класс для работы с excel
require_once('PHPExcel/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
// Создаем объект класса PHPExcel
$xls = new PHPExcel();

$cityid=(int)$_GET['city'];

$rmanager = mysql_query("SELECT * FROM pr_city where id=$cityid ORDER BY name ASC");
$amanager = mysql_fetch_array($rmanager);
$cityname=$amanager['name'];


$zag="Отчет".$cityname;
$chats= array();

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();

// Подписываем лист
$sheet->setTitle($zag);

$sheet->setCellValue("A1", 'Кол-во проданного товара в инстаграм');
$sheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

$sheet->setCellValue("A2", 'Наименование товара');
$sheet->getStyle('A2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A2')->getFill()->getStartColor()->setRGB('EEEEEE');

$sheet->setCellValue("B2", '01.01.2018');
$sheet->getStyle('B2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.01.2018');

$sheet->setCellValue("C2", '01.02.2018');
$sheet->getStyle('C2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('C2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.02.2018');

$sheet->setCellValue("D2", '01.03.2018');
$sheet->getStyle('D2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('D2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.03.2018');

$sheet->setCellValue("E2", '01.04.2018');
$sheet->getStyle('E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('E2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.04.2018');

$sheet->setCellValue("F2", '01.05.2018');
$sheet->getStyle('F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('F2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.05.2018');

$sheet->setCellValue("G2", '01.06.2018');
$sheet->getStyle('G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('G2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.06.2018');

$sheet->setCellValue("H2", '01.07.2018');
$sheet->getStyle('H2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('H2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.07.2018');

$sheet->setCellValue("I2", '01.08.2018');
$sheet->getStyle('I2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('I2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.08.2018');

$sheet->setCellValue("J2", '01.09.2018');
$sheet->getStyle('J2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('J2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.09.2018');

$sheet->setCellValue("K2", '01.10.2018');
$sheet->getStyle('K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('K2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.10.2018');

$sheet->setCellValue("L2", '01.11.2018');
$sheet->getStyle('L2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('L2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.11.2018');

$sheet->setCellValue("M2", '01.12.2018');
$sheet->getStyle('M2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('M2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.12.2018');

$sheet->setCellValue("N2", '01.01.2019');
$sheet->getStyle('N2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('N2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.01.2019');

$sheet->setCellValue("O2", '01.02.2019');
$sheet->getStyle('O2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('O2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.02.2019');

$sheet->setCellValue("P2", '01.03.2019');
$sheet->getStyle('P2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('P2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.03.2019');

$sheet->setCellValue("Q2", '01.04.2019');
$sheet->getStyle('Q2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Q2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.04.2019');

$sheet->setCellValue("R2", '01.05.2019');
$sheet->getStyle('R2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('R2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.05.2019');

$sheet->setCellValue("S2", '01.06.2019');
$sheet->getStyle('S2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('S2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.06.2019');

$sheet->setCellValue("T2", '01.07.2019');
$sheet->getStyle('T2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('T2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.07.2019');

$sheet->setCellValue("U2", '01.08.2019');
$sheet->getStyle('U2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('U2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.08.2019');

$sheet->setCellValue("V2", '01.09.2019');
$sheet->getStyle('V2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('V2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.09.2019');

$sheet->setCellValue("W2", '01.10.2019');
$sheet->getStyle('W2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('W2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.10.2019');

$sheet->setCellValue("X2", '01.11.2019');
$sheet->getStyle('X2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('X2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.11.2019');

$sheet->setCellValue("Y2", '01.12.2019');
$sheet->getStyle('Y2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Y2')->getFill()->getStartColor()->setRGB('EEEEEE');
array_push($chats, '01.12.2019');

// Объединяем ячейки
$sheet->mergeCells('A1:B1');

// Выравнивание текста
$sheet->getStyle('A1')->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sheet->getColumnDimension('A')->setWidth(50);
$sheet->getColumnDimension('B')->setWidth(11);
$sheet->getColumnDimension('C')->setWidth(11);
$sheet->getColumnDimension('D')->setWidth(11);
$sheet->getColumnDimension('E')->setWidth(11);
$sheet->getColumnDimension('F')->setWidth(11);
$sheet->getColumnDimension('G')->setWidth(11);
$sheet->getColumnDimension('H')->setWidth(11);
$sheet->getColumnDimension('I')->setWidth(11);
$sheet->getColumnDimension('J')->setWidth(11);
$sheet->getColumnDimension('K')->setWidth(11);
$sheet->getColumnDimension('L')->setWidth(11);
$sheet->getColumnDimension('M')->setWidth(11);
$sheet->getColumnDimension('N')->setWidth(11);
$sheet->getColumnDimension('O')->setWidth(11);
$sheet->getColumnDimension('P')->setWidth(11);
$sheet->getColumnDimension('Q')->setWidth(11);
$sheet->getColumnDimension('R')->setWidth(11);
$sheet->getColumnDimension('S')->setWidth(11);
$sheet->getColumnDimension('T')->setWidth(11);
$sheet->getColumnDimension('U')->setWidth(11);
$sheet->getColumnDimension('V')->setWidth(11);
$sheet->getColumnDimension('W')->setWidth(11);
$sheet->getColumnDimension('X')->setWidth(11);
$sheet->getColumnDimension('Y')->setWidth(11);

$r = mysql_query("SELECT * FROM `pr_tovar` t where t.pokaz>0 ORDER BY t.name ASC");

$i = 3;
$j = 3;

while ($a = mysql_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
{
    $cx=$i - 3;

    $sheet->setCellValueByColumnAndRow($cx, $j, $a['name']);
    $sheet->getStyleByColumnAndRow($cx, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-01-01';
    $dx2='2018-01-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx, $j, $x);
    $sheet->getStyleByColumnAndRow($cx, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-02-01';
    $dx2='2018-02-28';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx, $j, $x);
    $sheet->getStyleByColumnAndRow($cx, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-03-01';
    $dx2='2018-03-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-04-01';
    $dx2='2018-04-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-05-01';
    $dx2='2018-05-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-06-01';
    $dx2='2018-06-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-07-01';
    $dx2='2018-07-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-08-01';
    $dx2='2018-08-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-09-01';
    $dx2='2018-09-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-10-01';
    $dx2='2018-10-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-11-01';
    $dx2='2018-11-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2018-12-01';
    $dx2='2018-12-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-01-01';
    $dx2='2019-01-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx, $j, $x);
    $sheet->getStyleByColumnAndRow($cx, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-02-01';
    $dx2='2019-02-28';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx, $j, $x);
    $sheet->getStyleByColumnAndRow($cx, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-03-01';
    $dx2='2019-03-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-04-01';
    $dx2='2019-04-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-05-01';
    $dx2='2019-05-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-06-01';
    $dx2='2019-06-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-07-01';
    $dx2='2019-07-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-08-01';
    $dx2='2019-08-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-09-01';
    $dx2='2019-09-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-10-01';
    $dx2='2019-10-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-11-01';
    $dx2='2019-11-30';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $cx++;
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $dx1='2019-12-01';
    $dx2='2019-12-31';
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$a['id']." and o.id_city=c.id and o.dt BETWEEN '$dx1' and '$dx2' group by p.name ORDER BY ttt DESC");
    $ag = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$ag['ttt'];
    $sheet->setCellValueByColumnAndRow($cx , $j, $x);
    $sheet->getStyleByColumnAndRow($cx , $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


    $j++;
}
header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=$zag.xls" ); //".$namecity."

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>