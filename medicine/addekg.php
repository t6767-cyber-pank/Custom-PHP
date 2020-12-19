<?php
			$d0='id_EKG';
			$d1='id_kart';
			$d2="otdate";
			$d3='ritm'; 
			$d4='chss';
			$d5='electricosserdca';
			$d6='priznakigipertrofii';
			$d7='repolyarizaciya';
			$d8='zakluchenie';
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
ритм<br><input name="<?php echo $d3; ?>">
</td>
<td>
ЧСС<br><input name="<?php echo $d4; ?>">
</td>
</tr>
<tr>
<td>
Электрическая ось сердца<br><input name="<?php echo $d5; ?>">
</td>
<td>
Признаки гипертрофии<br><input name="<?php echo $d6; ?>">
</td>
<td>
Процессы реполяризации<br><input name="<?php echo $d7; ?>">
</td>
</tr>
<tr>
<td>
Заключение<br><input name="<?php echo $d8; ?>">
</td>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="ekg1" type="submit" class="button25" value="Добавить">
</td>
</tr>
</form>
</div>