<?php
// Подключаем класс для работы с excel
require_once('PHPExcel/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('PHPExcel/PHPExcel/Writer/Excel5.php');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

$PHP_SELF = $_SERVER['PHP_SELF'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
/**Общие подключения к классу для работы с бд**/
chdir(dirname(__FILE__));
include("./timurnf/class/mysqlwork.php");
$usersCRM=new usersCRM();
$users=$usersCRM->getUsersbyType(7);
$usersCRM->set_dt("2019-06-24");
$usersCRM->set_dt_to("2019-09-23");
$dates=$usersCRM->arraydatesByDW(1);
$masters=new masters();
$bonus=new bonus();
$proc=$bonus->selbonOper();
$m_city=new m_city();
$konecdoc=0;
$xls = new PHPExcel();
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();

$sheet->setCellValueByColumnAndRow(0, 1, "операторы");
$sheet->getStyleByColumnAndRow(0, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$x=2;
foreach ($users as $us) {
    $sheet->setCellValueByColumnAndRow(0, $x, $us["name"]);
    $sheet->getStyleByColumnAndRow(0, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $mas=$masters->MastersByOperator($us["id"]);
    $y=1;
    foreach ($dates as $dt) {
        $sheet->setCellValueByColumnAndRow($y, 1, date("d.m.Y", strtotime($dt)));
        $sheet->getStyleByColumnAndRow($y, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $comsisi=0;
        foreach ($mas as $m) {

            $masters->set_dt($dt);
            $mc=$masters->getMasterCom($m["id"]);
            $koef=(float)$m_city->selcoef($m["id_m_city"]);
            //echo $koef."<br>";
            if (($mc['comission']*$koef)>0) $xy=round($mc['comission']*(float)$koef/$proc['base_percent']*$proc['procopernew'],1); else $xy=0; //  /$proc['base_percent']*$proc['procopernew']
            $comsisi+=$xy;
        }
        $sheet->setCellValueByColumnAndRow($y, $x, $comsisi);
        $sheet->getStyleByColumnAndRow($y, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $y++;
        $konecdoc=$y;
    }
    $x++;
}

$sheet->setCellValueByColumnAndRow(1, 12, "=B2+B3+B4+B5+B6+B7+B8+B9+B10");
$sheet->getStyleByColumnAndRow(1, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(2, 12, "=C2+C3+C4+C5+C6+C7+C8+C9+C10");
$sheet->getStyleByColumnAndRow(2, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(3, 12, "=D2+D3+D4+D5+D6+D7+D8+D9+D10");
$sheet->getStyleByColumnAndRow(3, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(4, 12, "=E2+E3+E4+E5+E6+E7+E8+E9+E10");
$sheet->getStyleByColumnAndRow(4, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(5, 12, "=F2+F3+F4+F5+F6+F7+F8+F9+F10");
$sheet->getStyleByColumnAndRow(5, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(6, 12, "=G2+G3+G4+G5+G6+G7+G8+G9+G10");
$sheet->getStyleByColumnAndRow(6, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(7, 12, "=H2+H3+H4+H5+H6+H7+H8+H9+H10");
$sheet->getStyleByColumnAndRow(7, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(8, 12, "=I2+I3+I4+I5+I6+I7+I8+I9+I10");
$sheet->getStyleByColumnAndRow(8, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(9, 12, "=J2+J3+J4+J5+J6+J7+J8+J9+J10");
$sheet->getStyleByColumnAndRow(9, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(10, 12, "=K2+K3+K4+K5+K6+K7+K8+K9+K10");
$sheet->getStyleByColumnAndRow(10, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(11, 12, "=L2+L3+L4+L5+L6+L7+L8+L9+L10");
$sheet->getStyleByColumnAndRow(11, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(12, 12, "=M2+M3+M4+M5+M6+M7+M8+M9+M10");
$sheet->getStyleByColumnAndRow(12, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(13, 12, "=N2+N3+N4+N5+N6+N7+N8+N9+N10");
$sheet->getStyleByColumnAndRow(13, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->setCellValueByColumnAndRow(14, 12, "=O2+O3+O4+O5+O6+O7+O8+O9+O10");
$sheet->getStyleByColumnAndRow(14, $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$sheet->getColumnDimension('A')->setWidth(30);
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


header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=otchet.xls" ); //".$namecity."

// Выводим содержимое файла
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>
