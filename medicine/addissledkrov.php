<?php
			$d0='id_biohimkrov';
			$d1='id_kart';
			$d2="otdate";
			$d3='glukoza'; 
			$d4='srb';
			$d5='seromukoid';
			$d6='sialovie_kisloti';
			$d7='mochevina';
			$d8='bilirubin';
			$d9='ast';
			$d10='alt';
			$d11='kreatinin';
			$d12='holestirin';
			$d13='lipoproteidi';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<table>
<tr>
<td>
от какого числа<br><input name="<?php echo $d2; ?>" type="date" ><br><br>
</td>
<td>
Запись<br><input name="<?php echo $d3; ?>"><br><br>
</td>
<td>
Глюкоза<br><input name="<?php echo $d3; ?>"  ><br><br>
</td>
</tr>
<tr>
<td>
СРБ<br><input name="<?php echo $d4; ?>"  ><br><br>
</td>
<td>
серомукоид<br><input name="<?php echo $d5; ?>"  ><br><br>
</td>
<td>
сиаловые кислоты<br><input name="<?php echo $d6; ?>"  ><br><br>
</td>
</tr>
<tr>
<td>
мочевина<br><input name="<?php echo $d7; ?>"  ><br><br>
</td>
<td>
билирубин<br><input name="<?php echo $d8; ?>"  ><br><br>
</td>
<td>
АСТ<br><input name="<?php echo $d9; ?>"  ><br><br>
</td>
</tr>
<tr>
<td>
АЛТ<br><input name="<?php echo $d10; ?>"  ><br><br>
</td>
<td>
Креатинин<br><input name="<?php echo $d11; ?>"  ><br><br>
</td>
<td>
Холестерин<br><input name="<?php echo $d12; ?>"  ><br><br>
</td>
</tr>
<tr>
<td>
бетта-липопротеиды<br><input name="<?php echo $d13; ?>"  ><br><br>
</td>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="addissledkrov" class="button25" type="submit" value="Добавить">
</td>
</tr>
</table>
</form>
</div>