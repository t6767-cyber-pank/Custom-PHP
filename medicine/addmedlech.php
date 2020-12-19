<?php
			$d0='id_medikamentlech';
			$d1='id_kart';
			$d2="naimprep";
			$d3='kolvodneyvair'; 
			$d4='vesbolnogo';
			$d5='extrubnasutki';
			$d6='anesteziologkarta';
			$d7='ik';
			$d8='gemotransfuzia';
			$d9='krovdooper';
			$d10='krovposleoper'; 
			$d11='anesteziologposobie';
			$d12='hirurglech';
			$d13='operacia';
			$d14='timenachala';
			$d15='datenachala';
			$d16='dispansernoenabl';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<table>
<tr>
<td>
Наименования препаратов<br><input name="<?php echo $d2; ?>">
</td>
<td>
Количество дней в АиР<br><input name="<?php echo $d3; ?>">
</td>
<td>
Вес больного<br><input name="<?php echo $d4; ?>">
</td>
</tr>
<tr>
<td>
Экстубация на какие сутки<br><input name="<?php echo $d5; ?>">
</td>
<td>
Анестезиологическая карта<br><input name="<?php echo $d6; ?>">
</td>
<td>
ИК<br><input name="<?php echo $d7; ?>">
</td>
</tr>
<tr>
<td>
Гемотрансфузия<br><input name="<?php echo $d8; ?>">
</td>
<td>
Кровь до операции<br><input name="<?php echo $d9; ?>">
</td>
<td>
Кровь после операции<br><input name="<?php echo $d10; ?>">
</td>
</tr>
<tr>
<td>
Анестезиологическое пособие<br><input name="<?php echo $d11; ?>">
</td>
<td>
Хирургическое лечение<br><input name="<?php echo $d12; ?>">
</td>
<td>
Операция<br><input name="<?php echo $d13; ?>">
</td>
</tr>
<tr>
<td>
Время начала<br><input name="<?php echo $d14; ?>" type="time">
</td>
<td>
Время окончания<br><input name="<?php echo $d15; ?>" type="date">
</td>
<td>
Диспансерное наблюдение<br><input name="<?php echo $d16; ?>">
</td>
</tr>
<tr>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="medlech1" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
</form>
</div>