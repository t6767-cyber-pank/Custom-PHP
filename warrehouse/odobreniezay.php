<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');
require 'dizayn1.php';
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
			

$prorabid="";
$ds="";
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.nomer=".$_POST["nomer"]." order by z.nomer asc");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Прораб</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	$prorabid=$data['idprorab'];
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '')
                              ->setCellValue('B1', 'Отчет по заявке '.$data['object'])
                              ->setCellValue('C1', '');

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

							  
	$objPHPExcel->getActiveSheet()->setCellValue('A2', '')
                              ->setCellValue('B2', 'от '.$data['data'])
                              ->setCellValue('C2', '');

# применение стилей к ячейкам
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($arHeadStyle);
$objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($arHeadStyle1);
$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($arHeadStyle2);
	
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
$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($style_wrap1);

	$objPHPExcel->getActiveSheet()->setCellValue('A2', '')
                              ->setCellValue('B2', 'от '.$data['data'])
                              ->setCellValue('C2', '');

    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['prorab'].'</td>';
	  echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	$ds=$data['datesleg'];
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
  
  $qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<div align="center"><form method="POST" action="odobrenie.php">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';
  echo '<th>Решение</th>';
  $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Номер п/п')
                              ->setCellValue('B3', 'Наименование')
                              ->setCellValue('C3', 'Единицы измерения')
							  ->setCellValue('D3', 'Количество')
                              ->setCellValue('E3', 'Цена за единицу')
							  ->setCellValue('F3', 'Сумма')
                              ->setCellValue('G3', 'Срок поставки')
							  ->setCellValue('H3', 'Примечание');
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
    echo '<td><input name="kolvo'.$counter.'" type="text" value="'.$data['kolvo'].'"></td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td><input name="prim'.$counter.'" type="text" value="'.$data['prim'].'"></td>';
	echo '<td>';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$_POST['nomer'].'">';
	echo '<input type="hidden" name="x'.$counter.'" value="' .$data['id_spis'].'">';
	echo '<input type="radio" name="'.$counter.'" value="1" checked /> Принять';
    echo '<input type="radio" name="'.$counter.'" value="0"/> Отклонить';
	echo '</td>';

	
    echo '</tr>';
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$countx, $counter)
                              ->setCellValue('B'.$countx, $data['naim'])
                              ->setCellValue('C'.$countx, $data['edizm'])
							  ->setCellValue('D'.$countx, $data['kolvo'])
                              ->setCellValue('E'.$countx, $data['cenazaed'])
							  ->setCellValue('F'.$countx, $data['summa'])
                              ->setCellValue('G'.$countx, $data['datapost'])
							  ->setCellValue('H'.$countx, $data['prim']);
  
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
 $countx++;
 
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$countx, "Составил: _________")
                              ->setCellValue('G'.$countx, "Принял____________________");
							  
    $arHeadStyle4 = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$xcv='B'.$countx.':H'.$countx;
$objPHPExcel->getActiveSheet()->getStyle($xcv)->applyFromArray($arHeadStyle4);

  echo '</tbody>'; 
  echo '</table>';
  echo '<input type="hidden" name="counter" value="'.$counter.'">';
  echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
  echo '<input type="hidden" name="prorabid" value="'.$prorabid.'">';
  echo '<input type="hidden" name="datesleg" value="' .$ds.'">';
  echo '<br><br><input name="obr" type="submit" value="Выполнить"> <br><br>';
  echo '</form></div>';
  
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
<div align="center">
<form action="index.php" method="post"> 
<input type="submit" value="ГЛАВНОЕ МЕНЮ"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
<a href="odobreniezay.xls">Документ</a>
</div>
<?php
require 'dizayn2.php';
?>
