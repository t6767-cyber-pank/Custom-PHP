<?php
// Подключаем класс для работы с excel
require_once('PHPExcel/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/PHPExcel/Writer/Excel5.php');

// Создаем объект класса PHPExcel
$xls = new PHPExcel();

$namecity="net";
$idcity=0;

$idcity=$_GET["idcity"];
$namecity=$_GET["city"];

// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
// Подписываем лист
$sheet->setTitle($namecity);
$sheet->setCellValue("A1", 'Телефоны');
$sheet->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A1')->getFill()->getStartColor()->setRGB('EEEEEE');

$sheet->setCellValue("B1", 'Имена');
$sheet->getStyle('B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B1')->getFill()->getStartColor()->setRGB('EEEEEE');

//$sheet->setCellValue("C1", 'Адресса');
//$sheet->getStyle('C1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//$sheet->getStyle('C1')->getFill()->getStartColor()->setRGB('EEEEEE');

// Объединяем ячейки
//$sheet->mergeCells('A1:B1');

// Выравнивание текста
$sheet->getStyle('A1')->getAlignment()->setHorizontal(
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sheet->getColumnDimension('A')->setWidth(90);
$sheet->getColumnDimension('B')->setWidth(90);
//$sheet->getColumnDimension('C')->setWidth(90);

$conn=mysqli_connect('localhost','dbuser','0dU6&kv8');
mysqli_select_db($conn,'salonkrasoty');

if ($idcity==0){ $r = mysqli_query($conn,"SELECT phone, name as clientttt, address as adr FROM `pr_client` ORDER BY `pr_client`.`phone` ASC");} else
{ $r = mysqli_query($conn,"SELECT distinct cl.phone, cl.name as clientttt, cl.address as adr FROM `pr_city` c, `pr_client` cl, `pr_order` ord where cl.id=ord.id_client and c.id=ord.id_city and c.id=$idcity GROUP by cl.phone ORDER BY `cl`.`phone` ASC"); } //  and c.id=$idcity

$i = 2;
$j = 2;
while ($a = mysqli_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
{
    $sheet->setCellValueByColumnAndRow($i - 2, $j, $a['phone']);
    $sheet->getStyleByColumnAndRow($i - 2, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow($i - 1, $j, $a['clientttt']);
    $sheet->getStyleByColumnAndRow($i - 1, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//    $sheet->setCellValueByColumnAndRow($i, $j, $a['adr']);
//    $sheet->getStyleByColumnAndRow($i, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $j++;
}

// Выводим HTTP-заголовки
header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=".$namecity.".xls" ); //".$namecity."

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>