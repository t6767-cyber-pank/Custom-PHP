<?php
			$d1='id_kart';
			$d2="otdate";
			$d3='eritrociti'; 
			$d4='gemoglobin';
			$d5='cp';
			$d6='leykociti';
			$d7='eozinofili';
			$d8='palochkoyadernie';
			$d9='segmentoyadernie';
			$d10='limfociti';
			$d11='monociti';
			$d12='soe';
			include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<table>
<tr>
<td align="center">
от какого числа<br/><input name="<?php echo $d2; ?>" type="date" >
</td>
<td align="center">
Эритроциты<br/><input name="<?php echo $d3; ?>">
</td>
<td align="center">
Гемоглобин<br/><input name="<?php echo $d4; ?>">
</td>
</tr>
<tr>
<td align="center">
ЦП<br/><input name="<?php echo $d5; ?>">
</td>
<td align="center">
Лейкоциты<br/><input name="<?php echo $d6; ?>">
</td>
<td align="center">
эозинофилы<br/><input name="<?php echo $d7; ?>">
</td>
</tr>
<tr>
<td align="center">
палочкоядерные<br/><input name="<?php echo $d8; ?>">
</td>
<td align="center">
сегментоядерные<br/><input name="<?php echo $d9; ?>">
</td>
<td align="center">
лимфоциты<br/><input name="<?php echo $d10; ?>">
</td>
</tr>
<tr>
<td align="center">
моноциты <br/><input name="<?php echo $d11; ?>">
</td>
<td align="center">
СОЭ<br/><input name="<?php echo $d12; ?>">
</td>
<td align="center">
<input name="addnewkrow" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
</form>
</div>