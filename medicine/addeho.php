<?php
			$table="ehokardiya";
			$d0='id_ehokardiya';
			$d1='id_kart';
			$d2="otdate";
			$d3='aorta'; 
			$d4='aortalklapan';
			$d5='diametrdugi';
			$d6='fk1';
			$d7='raskritie1';
			$d8='stepenregurgitacii1';
			$d9='graddavlensist';
			$d10='mitralniyklapan';
			$d11="fk2";
			$d12='raskritie2'; 
			$d13='ploshadotverstiya2';
			$d14='stepenregurgitacii2';
			$d15='graddavlendist2';
			$d16='trikuspidalniyklapan';
			$d17='fk3';
			$d18='raskritie3';
			$d19='ploshadotverstiya3';
			$d20="stepenregurgitacii3";
			$d21='graddavlendist3'; 
			$d22='legochnayaarteriya';
			$d23='diametrstvola';
			$d24='pravlegarter';
			$d25='levlegarter';
			$d26='klapanlegart';
			$d27='sredneelad';
			$d28='stolicheskoelad';
			$d29="stepenregurgitacii4";
			$d30='graddavlen4'; 
			$d31='levoepredserdie';
			$d32='razmeri';
			$d33='leviygheludochik';
			$d34='kdr';
			$d35='ksr';
			$d36='kdo';
			$d37='kso';
			$d38='uo';
			$d39='zslg';
			$d40='imm';
			$d41='pravoepredserdie';
			$d42='praviygheludochek';
			$d43='meghserdechnayaperegorodka';
			$d44='meggheludochkovayaperegorodka';
			$d45='perikard';
			$d46='diastolicheskayafunklevgheludka';
			$d47='zonigipoiakinezi';
			$d48='doposobennosti';
			$d49='zakluchenie';
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
Аорта<br><input name="<?php echo $d3; ?>">
</td>
<td>
Аортальный клапан<br><input name="<?php echo $d4; ?>">
</td>
<td>
Диаметр дуги<br><input name="<?php echo $d5; ?>">
</td>
</tr>
<tr>
<td>
ФК<br><input name="<?php echo $d6; ?>">
</td>
<td>
Раскрытие<br><input name="<?php echo $d7; ?>">
</td>
<td>
Степень регургитации<br><input name="<?php echo $d8; ?>">
</td>
<td>
Градиент давления систолический<br><input name="<?php echo $d9; ?>">
</td>
</tr>
<tr>
<td>
Митральныйклапан<br><input name="<?php echo $d10; ?>">
</td>
<td>
ФК<br><input name="<?php echo $d11; ?>">
</td>
<td>
Раскрытие<br><input name="<?php echo $d12; ?>">
</td>
<td>
Площадь отверстия<br><input name="<?php echo $d13; ?>">
</td>
</tr>
<tr>
<td>
Степень регургитации<br><input name="<?php echo $d14; ?>">
</td>
<td>
Градиент давления диастолический<br><input name="<?php echo $d15; ?>">
</td>
<td>
Трикуспидальныйклапан<br><input name="<?php echo $d16; ?>">
</td>
<td>
ФК<br><input name="<?php echo $d17; ?>">
</td>
</tr>
<tr>
<td>
Раскрытие<br><input name="<?php echo $d18; ?>">
</td>
<td>
Площадь отверстия<br><input name="<?php echo $d19; ?>">
</td>
<td>
Степень регургитации<br><input name="<?php echo $d20; ?>">
</td>
<td>
Градиент давления диастолический<br><input name="<?php echo $d21; ?>">
</td>
</tr>
<tr>
<td>
Легочная артерия<br><input name="<?php echo $d22; ?>">
</td>
<td>
Диаметр ствола<br><input name="<?php echo $d23; ?>">
</td>
<td>
Правая легочная артерия<br><input name="<?php echo $d24; ?>">
</td>
<td>
Левая легочная артерия<br><input name="<?php echo $d25; ?>">
</td>
</tr>
<tr>
<td>
Клапан легочной артерии<br><input name="<?php echo $d26; ?>">
</td>
<td>
Среднее ЛАД<br><input name="<?php echo $d27; ?>">
</td>
<td>
Систолическое ЛАД<br><input name="<?php echo $d28; ?>">
</td>
<td>
Степень регургитации<br><input name="<?php echo $d29; ?>">
</td>
</tr>
<tr>
<td>
Градиент давления<br><input name="<?php echo $d30; ?>">
</td>
<td>
Левое предсердие<br><input name="<?php echo $d31; ?>">
</td>
<td>
Размеры<br><input name="<?php echo $d32; ?>">
</td>
<td>
Левый желудочек<br><input name="<?php echo $d33; ?>">
</td>
</tr>
<tr>
<td>
КДР<br><input name="<?php echo $d34; ?>">
</td>
<td>
КСР<br><input name="<?php echo $d35; ?>">
</td>
<td>
КДО<br><input name="<?php echo $d36; ?>">
</td>
<td>
КСО<br><input name="<?php echo $d37; ?>">
</td>
</tr>
<tr>
<td>
УО<br><input name="<?php echo $d38; ?>">
</td>
<td>
ЗСЛЖ<br><input name="<?php echo $d39; ?>">
</td>
<td>
ИММ<br><input name="<?php echo $d40; ?>">
</td>
<td>
Правое предсердие<br><input name="<?php echo $d41; ?>">
</td>
</tr>
<tr>
<td>
Правый желудочек<br><input name="<?php echo $d42; ?>">
</td>
<td>
Межпредсердная перегородка<br><input name="<?php echo $d43; ?>">
</td>
<td>
Межжелудочковая перегородка<br><input name="<?php echo $d44; ?>">
</td>
<td>
Перикард<br><input name="<?php echo $d45; ?>">
</td>
</tr>
<tr>
<td>
Диастолическая функция левого желудочка<br><input name="<?php echo $d46; ?>">
</td>
<td>
Зоны гипо- и акинезии<br><input name="<?php echo $d47; ?>">
</td>
<td>
Дополнительные особенности<br><input name="<?php echo $d48; ?>">
</td>
<td>
Заключение<br><input name="<?php echo $d49; ?>">
</td>
</tr>
<tr>
<td>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
<input name="eho1" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
</form>
</div>