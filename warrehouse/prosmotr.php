<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');

?>
<?php
require 'dizayn1.php';
?>
<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE); 
ini_set('display_startup_errors', TRUE); 
date_default_timezone_set('Europe/London');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Document")
							 ->setSubject("Office 2007 XLSX Document")
							 ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007")
							 ->setCategory("result file");
// Set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                          ->setSize(10);										  
								

$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.nomer=".$_POST["nomer"]." order by z.nomer asc");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '<th>Движение документа</th>';
  echo '<th>Итого</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
    $itog=$data['itogo'];
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '')
                              ->setCellValue('B1', 'Отчет по заявке '.$data['object'])
                              ->setCellValue('C1', '');
/*
$objPHPExcel->getFont()->setName(‘Arial’);
$objPHPExcel->getFont()->setBold(true);
$objPHPExcel->getFont()->setSize(12).
*/	

$arHeadStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 20,
        'name'  => 'Verdana'
    ));

$arHeadStyle1 = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 14,
        'name'  => 'Verdana'
    ));
	
$arHeadStyle2 = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 12,
        'name'  => 'Verdana'
    ));
	
	
# применение стилей к ячейкам
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($arHeadStyle);
$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($arHeadStyle1);
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($arHeadStyle2);

$style_wrap1 = array(
'borders'=>array(
//внешняя рамка
'outline' => array(
'style'=>PHPExcel_Style_Border::BORDER_THIN
),
'allborders'=>array(
'style'=>PHPExcel_Style_Border::BORDER_THIN,
'color' => array(
'rgb'=>'000000'
)
)
)
);
//применяем массив стилей к ячейкам THICK
$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($style_wrap1);

	$objPHPExcel->getActiveSheet()->setCellValue('A2', '')
                              ->setCellValue('B2', 'от '.$data['data'])
                              ->setCellValue('C2', '');

	
	echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	echo '<td>' .$data['datesleg'].'</td>';
	echo '<td>' .$data['itogo'].'</td>';
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
  
  $qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Количество на складе</th>';
  echo '<th>Цена за единицу</th>';
  echo '<th>Сумма</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';

  $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Номер п/п')
                              ->setCellValue('B3', 'Наименование')
                              ->setCellValue('C3', 'Единицы измерения')
							  ->setCellValue('D3', 'Количество')
							  ->setCellValue('E3', 'Количество на складе')
                              ->setCellValue('F3', 'Цена за единицу')
							  ->setCellValue('G3', 'Сумма')
                              ->setCellValue('H3', 'Срок поставки')
							  ->setCellValue('I3', 'Примечание');
  
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  $countx=3;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	$countx++;
	echo '<tr>';
    echo '<td>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td>' .$data['skladkv'].'</td>';
	echo '<td>' .$data['cenazaed'].'</td>';
	echo '<td>' .$data['summa'].'</td>';
	
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['prim'].'</td>';
	echo '</tr>';
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$countx, $counter)
                              ->setCellValue('B'.$countx, $data['naim'])
                              ->setCellValue('C'.$countx, $data['edizm'])
							  ->setCellValue('D'.$countx, $data['kolvo'])
							  ->setCellValue('E'.$countx, $data['skladkv'])
                              ->setCellValue('F'.$countx, $data['cenazaed'])
							  ->setCellValue('G'.$countx, $data['summa'])
                              ->setCellValue('H'.$countx, $data['datapost'])
							  ->setCellValue('I'.$countx, $data['prim']);
  }
  $countx++;

  $arHeadStyle3 = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$xcv='A4:I'.$countx;
$objPHPExcel->getActiveSheet()->getStyle($xcv)->applyFromArray($arHeadStyle3);

$style_wrap = array(
'borders'=>array(
//внешняя рамка
'outline' => array(
'style'=>PHPExcel_Style_Border::BORDER_THIN
),
'allborders'=>array(
'style'=>PHPExcel_Style_Border::BORDER_THIN,
'color' => array(
'rgb'=>'696969'
)
)
)
);
//применяем массив стилей к ячейкам THICK
$objPHPExcel->getActiveSheet()->getStyle($xcv)->applyFromArray($style_wrap);
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$countx, "Итого: ".$itog);
  $countx++;
  $countx++;
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$countx, "Составил: _________")
                              ->setCellValue('G'.$countx, "Принял____________________");
  
    $arHeadStyle4 = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$xcv='B'.$countx.':I'.$countx;
$objPHPExcel->getActiveSheet()->getStyle($xcv)->applyFromArray($arHeadStyle4);
				
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
?>

<?php 

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Datatypes');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));

?>
<br><br>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit" value="ГЛАВНОЕ МЕНЮ"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
<a href="prosmotr.xls">Документ</a>
</div>
<?php
require 'dizayn2.php';
?>