<?php
			$d0='id_analizprotrombin';
			$d1='id_kart';
			$d2="otdate";
			$d3='analizprotrombin';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
от какого числа<br><input name="<?php echo $d2; ?>" type="date" ><br><br>
Запись<br><textarea rows="3" cols="45" name="<?php echo $d3; ?>"></textarea><br><br>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="addanalizprotrombin" class="button25" type="submit" value="Добавить">
</form>
</div>