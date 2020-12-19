<?php
			$d0='id_uzivnutrorg';
			$d1='id_kart';
			$d2="otdate";
			$d3='pechen'; 
			$d4='ghelchniypuzir';
			$d5='poghelgheleza';
			$d6='selezenka';
			$d7='levayapochka';
			$d8='pravayapochka';
			$d9='zakluchenie';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<table>
<tr>
<td>
от какого числа<br><input name="<?php echo $d2; ?>" type="date" >
</td>
<td>
Печень<br><input name="<?php echo $d3; ?>">
</td>
<td>
Желчный пузырь<br><input name="<?php echo $d4; ?>">
</td>
</tr>
<tr>
<td>
Поджелудочная железа<br><input name="<?php echo $d5; ?>">
</td>
<td>
Селезенка<br><input name="<?php echo $d6; ?>">
</td>
<td>
Левая почка<br><input name="<?php echo $d7; ?>">
</td>
</tr>
<tr>
<td>
Правая почка<br><input name="<?php echo $d8; ?>">
</td>
<td>
Заключение<br><input name="<?php echo $d9; ?>">
</td>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="uzi1" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
</form>
</div>