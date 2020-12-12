<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');
require 'dizayn1.php';

if(isset($_POST['updates']))
{
for ($i=1; $i<$_POST['counter']+1; $i++)
{
	$ssum=$_POST['cenazaed'.$i]*$_POST['kolvo'.$i];
	mysql_query('update spisok set cenazaed='.$_POST['cenazaed'.$i].', summa="'.$ssum.'", status=1 where id_spis='.$_POST['id_spis'.$i]);
}
/*
$data['cenazaed'].'"></td>';
	echo '<td><input name="id_spis'.$counter.'" type="hidden" value="'.$data['id_spis']
*/	
}
if(isset($_POST['subupdate']))
{
	mysql_query('update spisok set naim="'.$_POST['naim'].'", edizm="'.$_POST['edizm'].'", kolvo='.$_POST['kolvo'].', datapost="'.$_POST['datapost'].'", prim="'.$_POST['prim'].'" where id_spis='.$_POST["id_spis"]);
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
echo '<form action="odobreniezayzakup.php" method="post">'; 
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Цена за единицу</th>';
  echo '<th>Сумма</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	echo '<tr>';
	$staz="";
	if ($data['status']==0) $staz=' bgcolor="#FFF000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<input name="kolvo'.$counter.'" type="hidden" value="'.$data['kolvo'].'">';
	echo '<td><input name="cenazaed'.$counter.'" type="text" value="'.$data['cenazaed'].'"></td>';
	echo '<input name="id_spis'.$counter.'" type="hidden" value="'.$data['id_spis'].'">';
	echo '<input name="counter" type="hidden" value="'.$counter.'">';
	echo '<td>' .$data['summa'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['prim'].'</td>';	
    echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
?>
<br>
<input type="submit"name="updates" value="Изменить"> 
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input type="hidden" name="nomer" value="<?php echo $_POST['nomer']; ?>">
<input type="hidden" name="submit" value="Отправить на подпись">
</form>
</div>
<br><br>
<div align="center">
<form action="index.php" method="post"> 
<input type="submit"name="updatezakupx" value="Отправить главному закупщику"> 
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