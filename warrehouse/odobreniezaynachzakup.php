<?php
require 'conect.php';
header('Content-type: text/html; charset=utf-8');
require 'dizayn1.php';
$prorabid="";
$zakupid="";
$ds="";
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.nomer=".$_POST["nomer"]." order by z.nomer asc");
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Прораб</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '<th>Итого</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  while($data = @mysql_fetch_array($qr_result)){ 
    echo '<tr>';
	$prorabid=$data['idprorab'];
	echo '<td>' .$data['nomer'].'</td>';
	echo '<td>' .$data['object'].'</td>';
    echo '<td>' .$data['data'].'</td>';
    echo '<td>' .$data['prorab'].'</td>';
	  echo '<td>' .$data['sostoyanie'].'</td>';
	echo '<td>' .$data['polz'].'</td>';
	echo '<td>' .$data['itogo'].'</td>';
	$zakupid=$data['idzakup'];
	$ds=$data['datesleg'];
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div><br><br>';
  
  $qr_result = mysql_query("select * from  spisok where idzay=".$_POST['nomer']."");
echo '<div align="center"><form method="POST" action="index.php">';
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
  echo '<th>Решение</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	echo '<tr>';
    $staz="";
	if ($data['status']==0) $staz=' bgcolor="#FF0000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>'.$data['kolvo'].'</td>';
	echo '<td>'.$data['cenazaed'].'</td>';
	echo '<td>'.$data['summa'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td><input name="prim'.$counter.'" type="text" value="'.$data['prim'].'"></td>';
	echo '<td>';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$_POST['nomer'].'">';
	echo '<input type="hidden" name="x'.$counter.'" value="' .$data['id_spis'].'">';
	echo '<input type="radio" name="'.$counter.'" value="1" checked /> Принять';
    echo '<input type="radio" name="'.$counter.'" value="0"/> Отклонить';
	echo '</td>';

	
    echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '<input type="hidden" name="counter" value="'.$counter.'">';
  echo '<input type="hidden" name="prorab" value="'.$_POST['prorab'].'">';
  echo '<input type="hidden" name="idzakup" value="'.$zakupid.'">';
  echo '<input type="hidden" name="datesleg" value="' .$ds.'">';
  echo '<br><br><input name="obrzaknachzakupki" type="submit" value="Выполнить"> <br><br>';
  echo '</form></div>';
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
<?php
require 'dizayn2.php';
?>