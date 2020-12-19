<?php 
include 'style.php';
function uptrans($p0, $p1, $p2){ if (isset($_POST[$p0])) {echo $p1;} else echo $p2; return 1;}
function uptrans2($p0){ if (isset($_POST[$p0])) {echo '<input type="hidden" name = "'.$p0.'" value="'.$_POST[$p0].'">';} return 1;}
$parupd="updateid";
include 'zaptren.php'; 
			$d1='FIO';
			$d2="godr";
			$d3='pol'; 
			$d4='pobochniy';
			$d5='krov';
			$d6='mestorab';
			$d7='nomersoc';
			$d8='graghd';
			$d9='kategory';
			$d10='nomerudv';
			$d11='pmg';
			$d12='vozrast';
			$d13='dolgnost';
			$d19='diagnoz';
if (isset($_POST[$parupd])) {zaptren(1, 88);}
?>
<div align="center">
<form method="POST" action="./index.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Главное меню">
</form>	
<form method="POST"  action="index.php">
<?php uptrans2($parupd); ?>
<table>
<tr>
<td align="center">
Ф.И.О. <br/><input name="FIOpac" type="text" value="<?php uptrans($parupd, $_GET[$d1], ""); ?>">
</td>
<td align="center">
Год рождения <br/><input type="date" name="godr" value="<?php uptrans($parupd, $_GET[$d2], ""); ?>">
</td>
<td align="center">
Возраст <br/><input name="vozrast" type="text" value="<?php uptrans($parupd, $_GET[$d12], ""); ?>">
</td>
</tr>
<tr>
<td align="center">
Пол <?php zaptren(4, 1); 
?>
</td>
<td align="center">
Побочные действия лекарств <?php zaptren(5, 1); ?>
</td>
<td align="center">
Группа крови <?php zaptren(6, 1); ?>
</td>
</tr>
<tr>
<td align="center">
Место работы <br/><input name="mestorab" type="text" value="<?php uptrans($parupd, $_GET[$d6], ""); ?>"> 
</td>
<td align="center">
Должность <br/><input name="dolgnost" type="text" value="<?php uptrans($parupd, $_GET[$d13], ""); ?>"> 
</td>
<td align="center">
Номер социального фонда <br/><input name="nomersoc" type="text" value="<?php uptrans($parupd, $_GET[$d7], ""); ?>"> 
</td>
</tr>
<tr>
<td align="center">
Гражданство <?php zaptren(7, 1); ?>
</td>
<td align="center">
Категория <?php zaptren(8, 1); ?>
</td>
<td align="center">
Номер удостоверения <br/><input name="nomerudv" type="text" value="<?php uptrans($parupd, $_GET[$d10], ""); ?>">
</td>
</tr>
<tr>
<td align="center">
Постоянное место жительства <br/><input name="pmg" type="text" value="<?php uptrans($parupd, $_GET[$d11], ""); ?>"> 
</td>
<td align="center">
Диагноз <br/><input name="diagnoz" type="text" value="<?php uptrans($parupd, $_GET[$d19], ""); ?>"> 
</td>
<td align="center">
<input name="<?php uptrans($parupd, "updatekart", "addnewkart"); ?>" class="button25" type="submit" value="<?php uptrans($parupd, "Изменить", "Добавить"); ?>">
</td>
</tr>
<tr>
</tr>
</table>
</form>
</div>