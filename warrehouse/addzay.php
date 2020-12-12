<?php
require 'conect.php';
$propusk=1;
echo "<!DOCTYPE html>";
require 'dizayn1.php';
?>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit" value="ГЛАВНОЕ МЕНЮ"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
</div>
<br>
<?php
if(isset($_POST['submitaddzay']))
{
if(isset($_POST['insertspis']))
{
mysql_query("INSERT INTO spisok(idzay, naim, edizm, kolvo, cenazaed, summa, datapost, prim, status, idzakupshik, fiozakupshik, zakbool, skladkv, ced1, ced2, ced3, prim1, prim2, prim3) values(".$_POST['idzay'].", '".$_POST['naim']."', '".$_POST['edizm']."', ".$_POST['kolvo'].", 0, 0, '".$_POST['srok']."', '".$_POST['prim']."',1, 0, '', 3, 0, 0, 0, 0, 'нет', 'нет', 'нет');");
//header("Location: index.php"); exit();
}
if(isset($_POST['insertzay']))
{
$query = mysql_query("SELECT COUNT(nomer) FROM zayavka WHERE nomer=".$_POST['nomz']);
    if(mysql_result($query, 0) > 0)
    {	
	$propusk=0;
	echo "<div align='center'>Заявка с таким номером уже есть</div>";
	}
	else
	{
		$propusk=1;
	}		
if ($propusk>0)
{
@mysql_query("INSERT INTO `zayavka`(`nomer`, `object`, `data`, `prorab`, `direktor`, `zakupshik`, `sostoyanieid`, `na_obrabotkeid`, `idprorab`, idzakup, datesleg, itogo) values(".$_POST['nomz'].", '".$_POST['object']."', '".$_POST['data']."', '".$_POST['prorab']."', '0', '0', 6, 3, ".$_POST['id_polz'].", 0, 'Заявка создана: ".$_POST['data']."<br>', 0);");
}
}
if(isset($_POST['delete']))
{
mysql_query("delete from spisok where id_spis=".$_POST['del']."");
}
if ($propusk>0)
{
$qr_result = mysql_query("select * from  zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.nomer=".$_POST['nomz']." and z.data='".$_POST['data']."' and z.object='".$_POST['object']."'");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Прораб</th>';
  echo '<th>Начальство</th>';
  echo '<th>Закупщик</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '<th>Движение документа</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $nomerzak="";
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	$nomerzak=$data['nomer'];
    echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['prorab'].'</td>';
	echo '<td>' .$data['direktor'].'</td>';
    echo '<td>' .$data['zakupshik'].'</td>';
    echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	echo '<td>' .$data['datesleg'].'</td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
}
if(isset($_POST['submitspis']))
{
if ($propusk>0)
{
$qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomz']."");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';
  echo '<th>Удалить</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
    echo '<td>' .$data['idzay'].'</td>';
	echo '<td>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['prim'].'</td>';
	
	echo '<td><form method="POST" action="addzay.php">';
	echo '<input type="hidden" name="idzay" value="'.$_POST['nomz'].'">';
	echo '<input type="hidden" name="nomz" value="'.$_POST['nomz'].'">';
	echo '<input type="hidden" name="data" value="'.$_POST['data'].'">';
	echo '<input type="hidden" name="object" value="'.$_POST['object'].'">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="submitaddzay" value="123">';
	echo '<input type="hidden" name="submitspis" value="Создать заявку">';
	echo '<input type="hidden" name="del" value="'.$data['id_spis'].'">';
	echo '<input name="delete" type="submit" value="Удалить"> <br><br>';
    echo '</form></td>';
	
    echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
}}
if ($propusk>0)
{
?>
<div align="center">
<h1>Введите метериалы</h1>
<form method="POST">
Наименование <br><input name="naim" type="text"><br><br>
Единицы измерения <br><input name="edizm" type="text"><br><br>
Количество <br><input name="kolvo" type="text"><br><br>
Срок поставки <br><input type="date" name="srok"><br><br>
Примечание <br><input name="prim" type="text"><br><br>
<input type="hidden" name="idzay" value="<?php echo $nomerzak; ?>">
<input type="hidden" name="nomz" value="<?php echo $_POST['nomz']; ?>">
<input type="hidden" name="data" value="<?php echo $_POST['data']; ?>">
<input type="hidden" name="object" value="<?php echo $_POST['object']; ?>">
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="insertspis" value="123">
<input type="hidden" name="submitaddzay" value="123">
<input name="submitspis" type="submit" value="Создать пункт">
</form>
</div>
<?php
}} 
else
{
?>
<div align="center">
<form method="POST">
<br><br>
<?php 
$query=mysql_query('SELECT MAX(nomer) as nomer FROM zayavka');
$data = @mysql_fetch_assoc($query);
if ($data['nomer']==NULL)
{$data['nomer']="1";}
else
{
 $data['nomer']++;
}
?>
<input type="hidden" name="nomz" value="<?php echo $data['nomer']; ?>">
Объект<br> <input name="object" type="text"><br><br>
Дата подачи<br> <input type="date" name="data"><br><br>
<input type="hidden" name="prorab" value="<?php echo $_POST['prorab']; ?>">
<input type="hidden" name="insertzay" value="123">
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input name="submitaddzay" type="submit" value="Создать заявку">
</form>
</div>
<?php  
}
require 'dizayn2.php';
?>