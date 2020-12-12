<?php
header('Content-type: text/html; charset=utf-8');
require 'conect.php';
require 'defines.php';
echo "<a href='index.php'><img src='integra.png'> </img></a>";
$prorab="";
$iddolg="";
$id_polz="";
require 'dizayn1.php';
echo "<div align='center'>";
if(isset($_POST['obrzaknachzakupki']))
{
$sum=0;
for ($i=1; $i<$_POST['counter']+1; $i++)
{
	mysql_query('update spisok set prim="'.$_POST['prim'.$i].'", status='.$_POST[$i].' where id_spis='.$_POST['x'.$i]);
	$sum+=$_POST[$i];
}
if ($_POST['counter']==$sum)
{
	mysql_query('update zayavka set sostoyanieid=10, na_obrabotkeid=3, datesleg="'.$_POST["datesleg"].'Дата одобрения нач. закуп.: '.date("d.m.y").'<br>'.'" where nomer='.$_POST["nomer"]);
}
else
{
	mysql_query('update zayavka set sostoyanieid=1, na_obrabotkeid=5 where nomer='.$_POST["nomer"]);
}
}
if(isset($_POST['obrzakglav']))
{
$itogo=0;
$sum=0;
for ($i=1; $i<$_POST['counter']+1; $i++)
{
$pieces = explode(";", $_POST['cenazaed'.$i]);
  $sum=($_POST['kolvo'.$i]-$_POST['skladkv'.$i])*$pieces[0];
$itogo+=$sum;
  mysql_query('update spisok set cenazaed='.$pieces[0].', prim="'.$pieces[1].'", summa='.$sum.' where id_spis='.$_POST['x'.$i]);
}
	mysql_query('update zayavka set sostoyanieid=9, itogo='.$itogo.', na_obrabotkeid=12, datesleg="'.$_POST["datesleg"].'Дата одобрения гл. закуп.: '.date("d.m.y").'<br>'.'" where nomer='.$_POST["nomer"]);
}

if(isset($_POST['updatezakupx']))
{
		mysql_query('update zayavka set sostoyanieid=8, na_obrabotkeid=5 where nomer='.$_POST["nomer"]);
}

if(isset($_POST['zakupshikispolnitel']))
{
	$query=mysql_query('select * from users where id_polz='.$_POST['zakupshikispolnitel'].'');
	$data = @mysql_fetch_assoc($query);
	mysql_query('update zayavka set idzakup='.$_POST['zakupshikispolnitel'].', zakupshik="'.$data["polz"].'", sostoyanieid=7, na_obrabotkeid='.$_POST['zakupshikispolnitel'].' where nomer='.$_POST["nomer"]);
}
if(isset($_POST['updatestatuszay']))
{
	mysql_query('update zayavka set sostoyanieid=6, na_obrabotkeid=3 where nomer='.$_POST["nomer"]);
}
if(isset($_POST['submit']))
{
$query = mysql_query("SELECT COUNT(login), polz FROM users WHERE login='".mysql_real_escape_string($_POST['log'])."' and pass='".mysql_real_escape_string($_POST['pas'])."'");
    if(mysql_result($query, 0) > 0)
    {
		$query = mysql_query("SELECT login, polz, pass, iddolg, id_polz FROM users WHERE  login='".mysql_real_escape_string($_POST['log'])."' and pass='".mysql_real_escape_string($_POST['pas'])."'");
		$data = @mysql_fetch_assoc($query);
		$prorab=$data["polz"];
        $iddolg=$data["iddolg"];
		$id_polz=$data["id_polz"];
		$_POST['iddolg']=$iddolg;
		$_POST['iddolg1']=$iddolg;
		$_POST['id_polz']=$id_polz;
		echo "<h1 text='white'>Добро пожаловать ".$data["polz"]."</h1><br><br>";
		$login=7;
	}
if(isset($_POST['delete']))
{
mysql_query('delete from zayavka where nomer='.$_POST["del"]);
mysql_query('delete from spisok where idzay='.$_POST["del"]);
}
}
?>
</div>
<?php
if ($login<1)
{
?>
<div align="center">
<form method="POST">
Логин <input name="log" type="text"><br><br>
Пароль <input name="pas" type="password"><br><br>
<input name="submit" type="submit" value="Войти">
</form>
</div>
<?php
} else 
{
if ($iddolg==4)
{
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.sostoyanieid=5 order by z.nomer asc");	
}
if ($iddolg==6)
{
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.sostoyanieid=5 order by z.nomer asc");	
}
if ($iddolg==8)
{
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.sostoyanieid=9 order by z.nomer asc");	
	echo "<div align='center'><h1>Одобрение цен</h1></div>";
}
if ($iddolg==5)
{
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.sostoyanieid=1 order by z.nomer asc");	
echo "<div align='center'><h1>Передача закупщикам</h1></div>";
}
if ($iddolg==3)
{
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz order by z.nomer asc");	
}
if ($iddolg==1)
{
	$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz and z.idprorab=".$id_polz." order by z.nomer asc");
}
echo '<div align="center">';
if(isset($_POST['iddolg']) && $_POST['iddolg']=="1")
{
?>
<form method="POST" action="addzay.php">
<input type="hidden" name="prorab" value="<?php echo $prorab; ?>">
<input type="hidden" name="iddolg" value="<?php echo $iddolg; ?>">
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input name="sub" type="submit" value="Добавить заявку">
</form>
<?php } ?>
<?php
if(isset($_POST['iddolg1']) && $_POST['iddolg']=="3")
{
?>
<form method="POST" action="odobrenie.php">
<input type="hidden" name="$iddolg" value="<?php echo $iddolg; ?>">
<input type="hidden" name="prorab" value="<?php echo $prorab; ?>">
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input name="sub" type="submit" value="Заявки на одобрение от прорабов">
</form>
<br>
<form method="POST" action="odobreniezakup.php">
<input type="hidden" name="$iddolg" value="<?php echo $iddolg; ?>">
<input type="hidden" name="prorab" value="<?php echo $prorab; ?>">
<input type="hidden" name="log" value="<?php echo $_POST['log']; ?>">
<input type="hidden" name="pas" value="<?php echo $_POST['pas']; ?>">
<input type="hidden" name="id_polz" value="<?php echo $_POST['id_polz']; ?>">
<input name="sub" type="submit" value="Заявки на одобрение от закупщиков">
</form>
<?php } ?>

<?php
if ($iddolg!=2 && $iddolg!=9)
{
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '<th>Движение документа</th>';
  echo '<th>Итого</th>';
  if(isset($_POST['iddolg1']) && $_POST['iddolg']=="3" || $_POST['iddolg']=="5")
{
  echo '<th>Прораб</th>';
  echo '<th>Закупщик</th>';
}
  echo '<th>Просмотр</th>';
if ($iddolg==1)
{
  echo '<th>Удалить</th>';
  echo '<th>Изменить</th>';
}
if ($iddolg==6 || $iddolg==3)
{
	echo '<th>Удалить</th>';
}
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
	echo '<td>' .$data['datesleg'].'</td>';
	echo '<td>' .$data['itogo'].'</td>';
	  if(isset($_POST['iddolg1']) && $_POST['iddolg']=="3" || $_POST['iddolg']=="5")
{
	echo '<td>' .$data['prorab'].'</td>';
	echo '<td>' .$data['zakupshik'].'</td>';
}
if ($iddolg==5)
{	
	echo '<td><form method="POST" action="prosmotrglavzakup.php">';
}
else 
{   
	if ($iddolg==2)
	{
	echo '<td><form method="POST" action="odobreniezayzakup.php">';
	}
	else
	{
		if ($iddolg==8)
		{
		echo '<td><form method="POST" action="odobreniezaynachzakup.php">';
		}
		else
		{
		echo '<td><form method="POST" action="prosmotr.php">';	
		}
	}
}
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$data['nomer'].'">';
	if ($iddolg==2)
	{
	echo '<input name="prosmotr" type="submit" value="Задать цены"> <br><br>';
    }
	else
	{
	echo '<input name="prosmotr" type="submit" value="Открыть"> <br><br>';
    }
	echo '</form></td>';
if ($iddolg==6 || $iddolg==3)
	{
	echo '<td><form method="POST" action="index.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="del" value="' .$data['nomer'].'">';
	echo '<input name="delete" type="submit" value="Удалить"> <br><br>';
    echo '</form></td>';
	}
if ($iddolg==1)
{
	if ($data['sostoyanieid']==6 || $data['sostoyanieid']==3)
	{
	echo '<td><form method="POST" action="index.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="del" value="' .$data['nomer'].'">';
	echo '<input name="delete" type="submit" value="Удалить"> <br><br>';
    echo '</form></td>';
	} else echo "<td>нет доступа</td>";
	if ($data['sostoyanieid']==6 || $data['sostoyanieid']==3)
	{
	echo '<td><form method="POST" action="odobreniezayprorab.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
echo '<input type="hidden" name="prorab" value="'.$data['prorab'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$data['nomer'].'">';
	echo '<input name="updatezay" type="submit" value="Изменить"> <br><br>';
    echo '</form></td>';
	} else echo "<td>нет доступа</td>";
}
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}}
?>
<?php
if ($iddolg==5)
{  
  echo "<div align='center'><h1>Все заявки</h1></div>";
$qr_result = mysql_query("select * from zayavka z, sostoyanie s, users u where z.sostoyanieid=s.id_sost and z.na_obrabotkeid=u.id_polz order by z.nomer asc");	
  echo '<br><br><div align="center">';
  echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Объект</th>';
  echo '<th>Дата создания</th>';
  echo '<th>Состояние</th>';
  echo '<th>На обработке</th>';
  echo '<th>Движение документа</th>';
  echo '<th>Прораб</th>';
  echo '<th>Закупщик</th>';
  echo '<th>Просмотр</th>';
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
	echo '<td>' .$data['datesleg'].'</td>';
	echo '<td>' .$data['prorab'].'</td>';
	echo '<td>' .$data['zakupshik'].'</td>';
	echo '<td><form method="POST" action="prosmotr.php">';
	echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
	echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
	echo '<input type="hidden" name="submit" value="submit">';
	echo '<input type="hidden" name="nomer" value="' .$data['nomer'].'">';
	echo '<input name="prosmotr" type="submit" value="Открыть"> <br><br>';
	echo '</form></td>';
	echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';

}

if(isset($_POST['izmenitzayavku']))
{
	$suxummm=$_POST['cenazaed']*$_POST['kolvo'];
	mysql_query('update spisok set ced1='.$_POST['ced1'].', ced2='.$_POST['ced2'].', ced3='.$_POST['ced3'].', prim1="'.$_POST['prim1'].'", prim2="'.$_POST['prim2'].'", prim3="'.$_POST['prim3'].'", zakbool=0, status=1 where id_spis='.$_POST['id_spis']);
}

if(isset($_POST['izmenitkvsklad']))
{
	mysql_query('update spisok set skladkv='.$_POST['skladkv'].', zakbool=0 where id_spis='.$_POST['id_spis']);
}

if ($iddolg==2)
{
$qr_result = mysql_query("select * from  spisok where idzakupshik=".$id_polz." and zakbool=1");
echo '<div align="center">';
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер<br>заявки</th>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы<br>измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Цена <br>за<br>единицу 1</th>';
  echo '<th>Цена <br>за<br>единицу 2</th>';
  echo '<th>Цена <br>за<br>единицу 3</th>';
  echo '<th>Сумма</th>';
  echo '<th>Срок<br>поставки</th>';
  echo '<th>Примечание 1</th>';
  echo '<th>Примечание 2</th>';
  echo '<th>Примечание 3</th>';
  echo '<th>Действие</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	echo '<tr>';
	echo '<form action="index.php" method="post">'; 
    echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
    echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
    echo '<input type="hidden" name="submit" value="Перенапровление">';
	echo '<td>' .$data['idzay'].'</td>';
	$staz="";
	if ($data['status']==0) $staz=' bgcolor="#FFF000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
//	echo '<td><input name="cenazaed" type="text" value="'.$data['cenazaed'].'"></td>';
	echo '<td><input name="ced1" type="text" value="'.$data['ced1'].'"></td>';
	echo '<td><input name="ced2" type="text" value="'.$data['ced2'].'"></td>';
	echo '<td><input name="ced3" type="text" value="'.$data['ced3'].'"></td>';
	echo '<td>' .$data['summa'].'</td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td><input name="prim1" type="text" value="'.$data['prim1'].'"></td>';
	echo '<td><input name="prim2" type="text" value="'.$data['prim2'].'"></td>';
	echo '<td><input name="prim3" type="text" value="'.$data['prim3'].'"></td>';
    echo '<td><input type="submit" name="izmenitzayavku" value="Отправить"> </td>';
	echo '<input type="hidden" name="kolvo" value="'.$data['kolvo'].'">';
	echo '<input type="hidden" name="id_spis" value="'.$data['id_spis'].'">';
	echo '</form>';
	echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
}

if ($iddolg==9)
{
$qr_result = mysql_query("select * from  spisok where zakbool=3");
echo '<div align="center">';
echo '<div align="center">';
echo '<table border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th>Номер заявки</th>';
  echo '<th>Наименование</th>';
  echo '<th>Единицы измерения</th>';
  echo '<th>Количество</th>';
  echo '<th>Количество на складе</th>';
  echo '<th>Срок поставки</th>';
  echo '<th>Примечание</th>';
  echo '<th>Действие</th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';
  $counter=0;
  while($data = @mysql_fetch_array($qr_result)){ 
    $counter++;
	echo '<tr>';
	echo '<form action="index.php" method="post">'; 
    echo '<input type="hidden" name="log" value="'.$_POST['log'].'">';
	echo '<input type="hidden" name="pas" value="'.$_POST['pas'].'">';
    echo '<input type="hidden" name="id_polz" value="'.$_POST['id_polz'].'">';
    echo '<input type="hidden" name="submit" value="Перенапровление">';
	echo '<td>' .$data['idzay'].'</td>';
	$staz="";
	if ($data['status']==0) $staz=' bgcolor="#FFF000"';
    echo '<td'.$staz.'>' .$data['naim'].'</td>';
    echo '<td>' .$data['edizm'].'</td>';
    echo '<td>' .$data['kolvo'].'</td>';
	echo '<td><input name="skladkv" type="text" value="'.$data['skladkv'].'"></td>';
	echo '<td>' .$data['datapost'].'</td>';
    echo '<td>' .$data['prim'].'</td>';	
    echo '<td><input type="submit" name="izmenitkvsklad" value="Изменить"> </td>';
	echo '<input type="hidden" name="kolvo" value="'.$data['kolvo'].'">';
	echo '<input type="hidden" name="id_spis" value="'.$data['id_spis'].'">';
	echo '</form>';
	echo '</tr>';
  }
  echo '</tbody>'; 
  echo '</table>';
  echo '</div>';
}


require 'dizayn2.php';
?>