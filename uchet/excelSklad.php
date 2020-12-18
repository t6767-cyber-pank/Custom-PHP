<?php
// Подключаем класс для работы с excel
require_once('PHPExcel/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

include("$DOCUMENT_ROOT/mysql_connect.php");
// Создаем объект класса PHPExcel
$xls = new PHPExcel();

$zag=$_GET["str"];
$nap=(int)$_GET["nap"];
//echo $nap."  ".$zag."<br>";
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle($zag);
$sheet->setCellValue("A1", 'Наименование');
$sheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A1')->getFill()->getStartColor()->setRGB('EEEEEE');

$sheet->setCellValue("B1", 'количество');
$sheet->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B1')->getFill()->getStartColor()->setRGB('EEEEEE');

$sheet->setCellValue("C1", 'Цена');
$sheet->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('C1')->getFill()->getStartColor()->setRGB('EEEEEE');

$sheet->setCellValue("D1", 'Сумма');
$sheet->getStyle('D1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('EEEEEE');

if ($zag!="sklad" && $zag!="skladopt") {
    $sheet->setCellValue("E1", 'Дата');
    $sheet->getStyle('E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('E1')->getFill()->getStartColor()->setRGB('EEEEEE');
}
// Объединяем ячейки
//$sheet->mergeCells('A1:B1');

// Выравнивание текста
$sheet->getStyle('A1')->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(15);

$r = "";

$sname=" and name like '%".$_GET["sername"]."%' ";
$sdate=" and s.data like '%".$_GET["dpri"]."%' ";

switch ($zag)
{
    case "sklad";  $r = mysql_query("SELECT * FROM `pr_tovar` t, skladrozn s where t.pokaz>0 and t.id=s.idtovar $sname ORDER BY t.name ASC"); break;
    case "skladopt";  $r = mysql_query("SELECT * FROM `pr_tovar` t, sklad s where t.pokaz>0 and t.id=s.idtovar $sname ORDER BY t.name ASC"); break;
    case "prihod"; $r = mysql_query("SELECT * FROM `pr_tovar` t, skladprih s where t.pokaz>0 and napr=$nap and t.id=s.idtovarprih $sname $sdate ORDER BY s.data DESC, t.name ASC"); break;
    case "rashod"; $r = mysql_query("SELECT * FROM `pr_tovar` t, skladrash s where t.pokaz>0 and napr=$nap and t.id=s.idtovarrash $sname $sdate ORDER BY s.data DESC, t.name ASC"); break;
}

//echo "SELECT * FROM `pr_tovar` t, skladprih s where t.pokaz>0 and napr=$nap and t.id=s.idtovarprih $sname $sdate ORDER BY s.data DESC, t.name ASC";
$i = 2;
$j = 2;
while ($a = mysql_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
{
    $sheet->setCellValueByColumnAndRow($i - 2, $j, $a['name']);
    $sheet->getStyleByColumnAndRow($i - 2, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $kv=0;
    switch ($zag)
    {
        case "sklad";  $sheet->setCellValueByColumnAndRow($i - 1, $j, $a['kolvorozn']); $kv=$a['kolvorozn']; break;
        case "skladopt";  $sheet->setCellValueByColumnAndRow($i - 1, $j, $a['kolvoopt']); $kv=$a['kolvoopt']; break;
        case "prihod"; $sheet->setCellValueByColumnAndRow($i - 1, $j, $a['kolvoprih']); $kv=$a['kolvoprih']; break;
        case "rashod"; $sheet->setCellValueByColumnAndRow($i - 1, $j, $a['kolvorash']); $kv=$a['kolvorash']; break;
    }


    $sheet->getStyleByColumnAndRow($i - 1, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow($i, $j, $a['price']);
    $sheet->getStyleByColumnAndRow($i, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow($i+1, $j, $kv*$a['price']);
    $sheet->getStyleByColumnAndRow($i+1, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    if ($zag!="sklad" && $zag!="skladopt") {
        $sheet->setCellValueByColumnAndRow($i + 2, $j, $a['data']);
        $sheet->getStyleByColumnAndRow($i + 2, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
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