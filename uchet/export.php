<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$DOCUMENT_ROOT/mysql_connect.php");
require_once ("$DOCUMENT_ROOT/PHPExcel/PHPExcel/IOFactory.php");
require_once("$DOCUMENT_ROOT/PHPExcel/PHPExcel/Writer/Excel5.php");

$xls = new PHPExcel();
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();

$sheet->setCellValueByColumnAndRow(0,1,"Клиент");
$sheet->getStyleByColumnAndRow(0,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyleByColumnAndRow(0,1)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow(1,1,"Телефон");
$sheet->getStyleByColumnAndRow(1,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyleByColumnAndRow(1,1)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow(2,1,"Заказов после первого");
$sheet->getStyleByColumnAndRow(2,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyleByColumnAndRow(2,1)->getAlignment()->setWrapText(true);
$sheet->getStyleByColumnAndRow(2,1)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow(3,1,"Единиц после первого заказа");
$sheet->getStyleByColumnAndRow(3,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyleByColumnAndRow(3,1)->getAlignment()->setWrapText(true);
$sheet->getStyleByColumnAndRow(3,1)->getFont()->setBold(true);
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);

$r = mysql_query("select * from pr_client");
$i = 2;
while ($a = mysql_fetch_array($r)){
  $id_client = $a['id'];
  $client = $a['name'];
  $phone = $a['phone'];
  $r1 = mysql_query("select * from pr_order where id_client=$id_client order by dt limit 1");
  if (mysql_num_rows($r1)==0)continue;
  $a1 = mysql_fetch_array($r1);
  $id = $a1['id'];
  $r_cnt = mysql_query("select * from pr_order_tovar where id_order=$id");
  if (mysql_num_rows($r_cnt)!=1)continue;
  $a_cnt = mysql_fetch_array($r_cnt);
  if($a_cnt['number']!=1)continue;
  $r1 = mysql_query("select * from pr_order where id_client=$id_client and id>$id order by dt");
  $num1 = mysql_num_rows($r1);
  $num2 = 0;
  while ($a1 = mysql_fetch_array($r1)){
    $id1 = $a1['id'];
    $r_cnt = mysql_query("select number from pr_order_tovar where id_order=$id1");
    $a_cnt = mysql_fetch_array($r_cnt);
    $num2 += $a_cnt['number'];
  }
//  print "<pre>$client $phone $num1 $num2</pre>";
  $sheet->setCellValueByColumnAndRow(0,$i,$client);
  $sheet->getStyleByColumnAndRow(0,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $sheet->setCellValueByColumnAndRow(1,$i,$phone);
  $sheet->getStyleByColumnAndRow(1,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $sheet->setCellValueByColumnAndRow(2,$i,$num1);
  $sheet->getStyleByColumnAndRow(2,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  $sheet->setCellValueByColumnAndRow(3,$i,$num2);
  $sheet->getStyleByColumnAndRow(3,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  $i++;
}
mysql_close($conn);
header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=export.xls" );
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
?>