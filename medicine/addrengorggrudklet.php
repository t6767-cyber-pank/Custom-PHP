<?php
			$d0='id_rengorggrudklet';
			$d1='id_kart';
			$d2="otdate";
			$d3='v_legkih'; 
			$d4='korni';
			$d5='diafragma';
			$d6='serdce';
			$d7='index_mura';
			$d8='kard_index';
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
В легких<br><input name="<?php echo $d3; ?>"><br><br>
</td>
</tr>
<tr>
<td>
Корни<br><input name="<?php echo $d4; ?>"><br><br>
</td>
<td>
Диафрагма и синусы<br><input name="<?php echo $d5; ?>"><br><br>
</td>
<td>
Сердце<br><input name="<?php echo $d6; ?>"><br><br>
</td>
</tr>
<tr>
<td>
Индекс Мура<br><input name="<?php echo $d7; ?>"><br><br>
</td>
<td>
Кардиоторакальный индекс<br><input name="<?php echo $d8; ?>"><br><br>
</td>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="rengorgg" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
</form>
</div>