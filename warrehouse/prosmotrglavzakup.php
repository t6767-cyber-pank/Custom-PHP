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
	
if(isset($_POST['obrzakglav']))
{
$sum=0;
for ($i=1; $i<$_POST['counter']+1; $i++)
{
	mysql_query('update spisok set prim="'.$_POST['cenazaed'.$i].'" where id_spis='.$_POST['x'.$i]);
	$sum+=$_POST[$i];
}
	mysql_query('update zayavka set sostoyanieid=9, na_obrabotkeid=12, datesleg="'.$_POST["datesleg"].'Дата одобрения гл. закуп.: '.date("d.m.y").'<br>'.'" where nomer='.$_POST["nomer"]);
}

	
$dsl="";
if(isset($_POST['obrzak']))
{
$query = mysql_query("SELECT * FROM users WHERE id_polz=".$_POST['zakupshikispolnitel']);
		$data = @mysql_fetch_assoc($query);
		$zakuporshik=$data["polz"];
for ($i=1; $i<$_POST['counter']+1; $i++)
{
	if (isset($_POST['spis'.$i]))
	{
	mysql_query('update spisok set idzakupshik='.$_POST['zakupshikispolnitel'].', fiozakupshik="'.$zakuporshik.'", zakbool=1 where id_spis='.$_POST['spis'.$i]);
	}
}
}

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
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
	
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
$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($style_wrap1);

	$objPHPExcel->getActiveSheet()->setCellValue('A2', '')
                              ->setCellValue('B2', 'от '.$data['data'])
                              ->setCellValue('C2', '');

/*	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '')
                              ->setCellValue('B1', 'Отчет по заявке '.$data['object'])
                              ->setCellValue('C1', '');

	$objPHPExcel->getActiveSheet()->setCellValue('A2', '')
                              ->setCellValue('B2', 'от '.$data['data'])
                              ->setCellValue('C2', '');
*/	
    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	$dsl=$data['datesleg'];
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
  
  $qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<form action="prosmotrglavzakup.php" method="post">'; 
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Выбор</th>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Количество на складе</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Закупщик</th>';
  echo '<th>Состояние</th>';
  $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Номер п/п')
                              ->setCellValue('B3', 'Наименование')
                              ->setCellValue('C3', 'Единицы измерения')
							  ->setCellValue('D3', 'Количество')
							  ->setCellValue('E3', 'Количество на складе')
                              ->setCellValue('F3', 'Цена за единицу')
							  ->setCellValue('G3', 'Сумма')
                              ->setCellValue('H3', 'Срок поставки');
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  $countx=3;
  $cform=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $cform++;
	$counter++;
	$countx++;
	echo '<tr>';
	echo '<td><input type="checkbox" name="'.'spis'.$counter.'" value="'.$data['id_spis'].'"></td>';
	echo '<input type="hidden" name="counter" value="'.$counter.'">';
    $staz="";
	$stazx="";
	if ($data['status']==0) $staz=' bgcolor="#FF0000"';
	if ($data['zakbool']==3) $stazx=' bgcolor="#0FF000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td>' .$data['skladkv'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['fiozakupshik'].'</td>';
	if ($data['zakbool']==0)
	{echo '<td>Без движения</td>';} else {if ($data['zakbool']==1) {echo '<td>На указании цен</td>';} else {echo '<td'.$stazx.'>На рассмотрении у кладовщика</td>';} } 
	echo '</tr>';
  $objPHPExcel->getActiveSheet()->setCellValue('A'.$countx, $counter)
                              ->setCellValue('B'.$countx, $data['naim'])
                              ->setCellValue('C'.$countx, $data['edizm'])
							  ->setCellValue('D'.$countx, $data['kolvo'])
                              ->setCellValue('E'.$countx, $data['skladkv'])
							  ->setCellValue('F'.$countx, '1)-'.$data['ced1'].' 2)-'.$data['ced2'].' 3)-'.$data['ced3'])
							  ->setCellValue('G'.$countx, $data['summa'])
                              ->setCellValue('H'.$countx, $data['datapost']);
  }
 
     $arHeadStyle3 = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$xcv='A4:H'.$countx;
$objPHPExcel->getActiveSheet()->getStyle($xcv)->applyFromArray($arHeadStyle3);

  $countx++;
  $countx++;
  $countx++;

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

  
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$countx, "Составил: _________")
                              ->setCellValue('G'.$countx, "Принял____________________");
							  
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

  
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Datatypes');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));

  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
?>

<div align="center">
<br><input type="submit" name="obrzak" value="Передать закупщику">
<p>
<select size="10" multiple name="zakupshikispolnitel">
    <option disabled>Выберите Закупщика</option>
	<?php
	$qr_result = mysql_query("select * from  users where iddolg=2");
    while($data = @mysql_fetch_array($qr_result)){ 
    echo '<option value="'.$data['id_polz'].'">'.$data['polz'].'</option>';
	}
	?>
</select>
</p> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
<input type="hidden" name="nomer" value="<?php echo $_POST['nomer']; ?>">
</form>
</div>


<br><br>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit" value="ГЛАВНОЕ МЕНЮ"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
<a href="prosmotrglavzakup.xls">Документ</a>
</div>
<br><br><br><br>
<?php
/*
<form action="index.php" method="post"> 
<input type="submit" name="obrzakglav" value="Выполнить заявку"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="nomer" value="<?php echo $_POST['nomer']; ?>">
<input type="hidden" name="datesleg" value="<?php echo $dsl; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
*/
?>


<?php
$qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<form action="index.php" method="post">'; 
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
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
  $counter++;
	echo '<tr>';
	echo '<input type="hidden" name="x'.$counter.'" value="'.$data['id_spis'].'">';
	echo '<input type="hidden" name="counter" value="'.$counter.'">';
    
	echo '<input type="hidden" name="skladkv'.$counter.'" value="'.$data['skladkv'].'">';
	echo '<input type="hidden" name="kolvo'.$counter.'" value="'.$data['kolvo'].'">';
	
	$staz="";
	$stazx="";
	if ($data['status']==0) $staz=' bgcolor="#FF0000"';
	if ($data['zakbool']==3) $stazx=' bgcolor="#0FF000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td>' .$data['skladkv'].'</td>';
	echo '<td>
	Цена за единицу: ' .$data['skladkv'].'<br/>
	<fieldset id="cenazaed'.$counter.'">
	<input name="cenazaed'.$counter.'" type="radio" checked value="'.$data['ced1'].';'.$data['prim1'].'">1) '.$data['ced1'].' | Примечание: '.$data['prim1'].'<br/>
    <input name="cenazaed'.$counter.'" type="radio" value="'.$data['ced2'].';'.$data['prim2'].'">2) '.$data['ced2'].' | Примечание: '.$data['prim2'].'<br/>
	<input name="cenazaed'.$counter.'" type="radio" value="'.$data['ced3'].';'.$data['prim3'].'">3) '.$data['ced3'].' | Примечание: '.$data['prim3'].'<br/>
	</fieldset>
	</td>';
	echo '<td>' .$data['summa'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '</tr>';
  }
	echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
?>
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
<input type="hidden" name="nomer" value="<?php echo $_POST['nomer']; ?>">
<br/><br/>
<div align="center">
<input type="submit" name="obrzakglav" value="Выполнить заявку"> 
<input type="hidden" name="datesleg" value="<?php echo $dsl; ?>">
</div>
</form>




<?php
require 'dizayn2.php';
?>