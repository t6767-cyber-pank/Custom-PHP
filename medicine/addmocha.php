<?php
			$d0='id_obshiyanalizmochi';
			$d1='id_kart';
			$d2="otdate";
			$d3='cvet'; 
			$d4='prozrachnost';
			$d5="udelniy_ves";
			$d6='reakciya_mochi';
			$d7='belok';
			$d8="glukoza";
			$d9='ploskiy_epiteliy';
			$d10='leykociti';
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
Цвет<br><input name="<?php echo $d3; ?>"><br><br>
</td>
<td>
Прозрачность<br><input name="<?php echo $d4; ?>"><br><br>
</td>
</tr>
<tr>
<td>
Удельный вес<br><input name="<?php echo $d5; ?>"><br><br>
</td>
<td>
Реакция мочи<br><input name="<?php echo $d6; ?>"><br><br>
</td>
<td>
Белок<br><input name="<?php echo $d7; ?>"><br><br>
</td>
</tr>
<tr>
<td>
Глюкоза<br><input name="<?php echo $d8; ?>"><br><br>
</td>
<td>
Плоский эпителий<br><input name="<?php echo $d9; ?>"><br><br>
</td>
<td>
Лейкоциты<br><input name="<?php echo $d10; ?>"><br><br>
</td>
</tr>
<tr>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="addmocha" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
</form>
</div>