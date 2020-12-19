<?php 
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
session_start();
include 'zaptren.php';
include 'style.php';



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
<div align="center">
<?php
if(isset($_POST['exit']))
{
	unset($_SESSION['doc']);
	unset($_SESSION['log']);
	unset($_SESSION['pas']);
	unset($_SESSION['exit']);
}

if (isset($_POST['addnewkart'])){zaptren(1, 3);}

if((isset($_SESSION['doc'])) || (isset($_POST['submit'])))
{
unset($_SESSION['doc']);
  include 'conect.php';
if((!isset($_SESSION['log'])) && (!isset($_SESSION['pas'])))  
{
  $_SESSION['log']=$_POST['log'];
  $_SESSION['pas']=$_POST['pas'];
}
  $query = mysql_query("SELECT COUNT(FIO), FIO FROM doctor WHERE login='".mysql_real_escape_string($_SESSION['log'])."' and pass='".mysql_real_escape_string($_SESSION['pas'])."'");
    if(mysql_result($query, 0) > 0)
    {
?>	
<table>	
<tr>
<td>
<form method="POST">
<input name="exit" type="submit" class="button25" value="Выйти">
</form>
</td>
<td>
<form method="POST" action="./insert.php">
<input name="submit" type="submit" class="button25" value="Добавить нового пациента">
</form>
</td>
<td>
<form method="POST" action="./prosmotr.xls">
<input name="submit" type="submit" class="button25" value="Сформировать отчет">
</form>			
</td>
</tr>
</table>
<br/>
<form method="POST" action="./index.php">
<table width="100%">
<tr>
<td align='center'>
Введите имя пациента <input name="namer" placeholder="Введите имя для поиска."><br/><br/>
</td>
<td align='center'>
Введите дату рождения <input name="dateer" placeholder="Введите дату для поиска."><br/><br/>
</td>
<td align='center'>
Диагноз <input name="diagnozer11" placeholder="Введите диагноз для поиска."><br/><br/>
</td>
<td><input name="submit" class="button25" type="submit" value="Поиск"></td>
</tr>
</table>
</form>				
<?php
		$query = mysql_query("SELECT login, pass, id_doctor, FIO FROM doctor WHERE  login='".mysql_real_escape_string($_SESSION['log'])."' and pass='".mysql_real_escape_string($_SESSION['pas'])."'");
		$data = @mysql_fetch_assoc($query);
		$_POST['id_doctor']=$data["id_doctor"];
		$_POST['FIO']=$data["FIO"];
		$_SESSION['doc'] = $data["id_doctor"];
		$_SESSION['login'] = $data["login"];
		$_SESSION['pass'] = $data["pass"];
		if (isset($_POST['dellid'])) {zaptren(1, 67);}
		if (isset($_POST['updatekart'])) {zaptren(1, 77);}
		zaptren(1, 1);		
	}
	else { 
	echo "Неверный логин или пароль<br/><br/>"; 
	unset($_SESSION['doc']);
	unset($_SESSION['log']);
	unset($_SESSION['pas']);
	unset($_SESSION['exit']);
	goto a; 
	}
} 
else
{
a:
?>
<form method="POST">
<table>
<tr>
<td>
Логин <input name="log" type="text"><br><br>
</td>
</tr>
<tr>
<td>
Пароль <input name="pas" type="password"><br><br>
</td>
</tr>
<tr>
<td>
<input name="submit" type="submit" class="button25" value="Войти">
</td>
</tr>
</table>
</form>
</div>
<?php
}
?>
</div>