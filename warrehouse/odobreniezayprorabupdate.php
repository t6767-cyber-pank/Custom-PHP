<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');
?>
<?php
require 'dizayn1.php';
?>
<div align="center">
<h1>Введите метериалы</h1>
<form method="POST" action="odobreniezayprorab.php">
Наименование <input name="naim" type="text" value="<?php echo $_POST['naim']; ?>"><br><br>
Единицы измерения <input name="edizm" type="text" value="<?php echo $_POST['edizm']; ?>"><br><br>
Количество <input name="kolvo" type="text" value="<?php echo $_POST['kolvo']; ?>"><br><br>
Срок поставки <input name="datapost" type="text" value="<?php echo $_POST['datapost']; ?>"><br><br>
Примечание <input name="prim" type="text" value="<?php echo $_POST['prim']; ?>"><br><br>
<?php
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$_POST['nomer'].'">';
	echo '<input type="hidden" name="counter" value="'.$_POST['counter'].'">';
	echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
    echo '<input type="hidden" name="id_spis" value="'.$_POST['id_spis'].'">';
	?>
<input name="subupdate" type="submit" value="Изменить">
</form>
</div>
<?php
require 'dizayn2.php';
?>

	
