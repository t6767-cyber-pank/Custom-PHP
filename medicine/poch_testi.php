<?php
			$table="poch_testi";
			$d0='id_poch_testi';
			$d1='id_kart';
			$d2="otdate";
			$d3='poch_testi';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
от какого числа<br><input name="<?php echo $d2; ?>" type="date" ><br><br>
Запись<br><textarea rows="3" cols="45" name="<?php echo $d3; ?>"></textarea><br><br>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="addmocha" type="submit" class="button25" value="Добавить">
</form>
</div>