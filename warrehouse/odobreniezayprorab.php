<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');

require 'dizayn1.php';

if(isset($_POST['subupdate']))
{
	mysql_query('update spisok set naim="'.$_POST['naim'].'", edizm="'.$_POST['edizm'].'", kolvo='.$_POST['kolvo'].', datapost="'.$_POST['datapost'].'", prim="'.$_POST['prim'].'", status=1 where id_spis='.$_POST["id_spis"]);
}
if(isset($_POST['delete']))
{
mysql_query("delete from spisok where id_spis=".$_POST['del']."");
}
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.nomer=".$_POST["nomer"]." order by z.nomer asc");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
  
  $qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';
  echo '<th>Изменить</th>';
  echo '<th>Удалить</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	echo '<tr>';
    echo '<td>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['prim'].'</td>';
	
	$staz="";
	if ($data['status']==0) $staz=' bgcolor="#FF0000"';
	echo '<td'.$staz.'><form method="POST" action="odobreniezayprorabupdate.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$_POST['nomer'].'">';
	echo '<input type="hidden" name="counter" value="'.$counter.'">';
	echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
	echo '<input type="hidden" name="id_spis" value="'.$data['id_spis'].'">';
	echo '<input type="hidden" name="idzay" value="'.$data['idzay'].'">';
	echo '<input type="hidden" name="naim" value="'.$data['naim'].'">';
	echo '<input type="hidden" name="edizm" value="'.$data['edizm'].'">';
	echo '<input type="hidden" name="kolvo" value="'.$data['kolvo'].'">';
	echo '<input type="hidden" name="datapost" value="'.$data['datapost'].'">';
	echo '<input type="hidden" name="prim" value="'.$data['prim'].'">';
	echo '<input name="obr" type="submit" value="Изменить">';
	echo '</form></td>';
	
	echo '<td><form method="POST" action="odobreniezayprorab.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$_POST['nomer'].'">';
	echo '<input type="hidden" name="counter" value="'.$counter.'">';
	echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
	echo '<input type="hidden" name="del" value="'.$data['id_spis'].'">';
	echo '<input name="delete" type="submit" value="Удалить">';
	echo '</form></td>';
	
	
    echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
?>
<br><br>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit"name="updatestatuszay" value="Отправить на подпись"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="nomer" value="<?php echo $_POST['nomer']; ?>">
<input type="hidden" name="submit" value="Отправить на подпись">
</form>
</div>
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