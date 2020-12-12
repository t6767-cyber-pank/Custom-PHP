<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');

require 'dizayn1.php';
if(isset($_POST['obr']))
{
$sum=0;
for ($i=1; $i<$_POST['counter']+1; $i++)
{
	mysql_query('update spisok set kolvo='.$_POST['kolvo'.$i].', prim="'.$_POST['prim'.$i].'", status='.$_POST[$i].' where id_spis='.$_POST['x'.$i]);
	$sum+=$_POST[$i];
}
if ($_POST['counter']==$sum)
{   
	mysql_query('update zayavka set sostoyanieid=1, na_obrabotkeid=5, datesleg="'.$_POST["datesleg"].'Дата одобрения тех. дир.: '.date("d.m.y").'<br>'.'" where nomer='.$_POST["nomer"]);
}
else
{
	mysql_query('update zayavka set sostoyanieid=3, na_obrabotkeid='.$_POST["prorabid"].' where nomer='.$_POST["nomer"]);
}
}
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and sostoyanieid=6 order by z.nomer asc");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Выполнить</th>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Прораб</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	echo '<td><form method="POST" action="odobreniezay.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$data['nomer'].'">';
	echo '<input name="obr" type="submit" value="Открыть"> <br><br>';
    echo '</form></td>';
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['prorab'].'</td>';
	echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
?>
<br>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit" value="ГЛАВНОЕ МЕНЮ"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="submit" value="Перенапровление">
</form>
</div>
<?php
require 'dizayn2.php';
?>