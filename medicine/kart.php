<?php 
include 'style.php';
?>
<style>
.korpus > div, .korpus > input { display: none; }

.korpus label { padding: 5px; border: 1px solid #aaa; line-height: 28px; cursor: pointer; position: relative; bottom: 1px; background: #fff; }
.korpus input[type="radio"]:checked + label { border-bottom: 2px solid #fff; }

.korpus > input:nth-of-type(1):checked ~ div:nth-of-type(1),
.korpus > input:nth-of-type(2):checked ~ div:nth-of-type(2),
.korpus > input:nth-of-type(3):checked ~ div:nth-of-type(3), 
.korpus > input:nth-of-type(4):checked ~ div:nth-of-type(4),
.korpus > input:nth-of-type(5):checked ~ div:nth-of-type(5) { display: block; padding: 5px; border: 1px solid #aaa; }
</style>
<form method="POST" action="./index.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Главное меню">
</form>	
<div class="korpus">
  <input type="radio" name="odin" checked="checked" id="vkl1"/><label for="vkl1">Данные пациента</label><input type="radio" name="odin" id="vkl2"/><label for="vkl2">Добавить записи</label><input type="radio" name="odin" id="vkl3"/><label for="vkl3">Медицинские записи</label><input type="radio" name="odin" id="vkl4"/><label for="vkl4">Приемное отделение</label><input type="radio" name="odin" id="vkl5"/><label for="vkl5">Результаты дополнительных методов исследования больного</label>
  <div align="center">
	<?php
	include 'zaptren.php';
	
	if (isset($_POST['addnewlist'])){zaptren(2, 3);}
	if (isset($_POST['addnewpriem'])){zaptren(3, 3);}
	if (isset($_POST['addnewkrow'])){zaptren(13, 3);}
	if (isset($_POST['addkrowtime'])){zaptren(14, 3);}
	if (isset($_POST['addkrowvass'])){zaptren(15, 3);}
	if (isset($_POST['addmarkgep'])){zaptren(16, 3);}
	if (isset($_POST['addspid'])){	zaptren(17, 3);}
	if (isset($_POST['addmocha'])){zaptren(18, 3);}
	if (isset($_POST['poch_testi'])){zaptren(19, 3);}
	if (isset($_POST['addissledkrov'])){zaptren(20, 3);}
	if (isset($_POST['addanalizprotrombin'])){zaptren(21, 3);}
	
if (isset($_POST['protrombindex111'])){zaptren(22, 3);}
if (isset($_POST['rengorgg'])){zaptren(23, 3);}
if (isset($_POST['ekg1'])){zaptren(24, 3);}
if (isset($_POST['eho1'])){zaptren(25, 3);}
if (isset($_POST['uzi1'])){zaptren(26, 3);}
if (isset($_POST['okul1'])){zaptren(27, 3);}
if (isset($_POST['stam1'])){zaptren(28, 3);}
if (isset($_POST['newr1'])){zaptren(29, 3);}
if (isset($_POST['lor1'])){zaptren(30, 3);}
if (isset($_POST['medlech1'])){zaptren(31, 3);}
	
	if (isset($_POST['dellid'])) {zaptren(2, 67);}
	if (isset($_POST['dellpriemotdel'])) {zaptren(3, 67);}
	if (isset($_POST['dellobankrov'])) {zaptren(13, 67);}
	if (isset($_POST['dellkrowtime'])) {zaptren(14, 67);}
	if (isset($_POST['dellwas'])) {zaptren(15, 67);}
	if (isset($_POST['dellmark'])) {zaptren(16, 67);}
	if (isset($_POST['dellspid'])) {zaptren(17, 67);}
	if (isset($_POST['dellmocha'])){zaptren(18, 67);}
	if (isset($_POST['dellpoch_testi'])){zaptren(19, 67);}
	if (isset($_POST['dellissledkrov'])){zaptren(20, 67);}
	if (isset($_POST['dellanalizprotrombin'])){zaptren(21, 67);}
	
if (isset($_POST['dellprotrombindex111'])){zaptren(22, 67);}
if (isset($_POST['dellrengorgg'])){zaptren(23, 67);}
if (isset($_POST['dellekg1'])){zaptren(24, 67);}
if (isset($_POST['delleho1'])){zaptren(25, 67);}
if (isset($_POST['delluzi1'])){zaptren(26, 67);}
if (isset($_POST['dellokul1'])){zaptren(27, 67);}
if (isset($_POST['dellstam1'])){zaptren(28, 67);}
if (isset($_POST['dellnewr1'])){zaptren(29, 67);}
if (isset($_POST['delllor1'])){zaptren(30, 67);}
if (isset($_POST['dellmedlech1'])){zaptren(31, 67);}
	
	$parametr=$_GET['kart'];
	zaptren(1, 2);
	?>
	<h1><?php echo $_GET['FIO']; ?></h1>
	<dl class="holiday">
	<dt>Дата рождения</dt> <dd><?php echo $_GET['godr']; ?></dd>
	<dt>Диагноз</dt> <dd> <?php echo $_GET['diagnoz']; ?></dd>
	<dt>возраст</dt> <dd> <?php echo $_GET['vozrast']; ?></dd>
	<dt>Пол</dt> <dd> <?php echo $_GET['pol']; ?></dd>
	<dt>Побочные действия лекарств</dt> <dd> <?php echo $_GET['pobochniy']; ?></dd>
	<dt>Группа крови</dt> <dd> <?php echo $_GET['krov']; ?></dd>
	<dt>Место работы</dt> <dd> <?php echo $_GET['mestorab']; ?></dd>
	<dt>Должность</dt> <dd> <?php echo $_GET['dolgnost']; ?></dd>
	<dt>Номер удостоверения социальной защиты</dt> <dd> <?php echo $_GET['nomersoc']; ?></dd>
	<dt>Гражданство</dt> <dd> <?php echo $_GET['graghd']; ?></dd>
	<dt>Категория</dt> <dd> <?php echo $_GET['kategory']; ?></dd>
	<dt>Номер удостоверения</dt> <dd> <?php echo $_GET['nomerudv']; ?></dd>
	<dt>Постоянное место жительства</dt> <dd> <?php echo $_GET['pmg']; ?></dd>
	</dl>
  </div>
  <div align="center">
  <table>
  <tr>
  <td>
	<form method="POST" action="./listadd.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Добавить медицинскую запись">
	</form>	
	<form method="POST" action="./priemadd.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Добавить запись приемного отделения">
	</form>	
	<form method="POST" action="./krovanaladd.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Добавить Общий анализ крови">
	</form>	
	<form method="POST" action="./addkrowtime.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Добавить Время свертывания крови">
	</form>	
	<form method="POST" action="./addkrowvass.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Добавить Реакция Вассермана ">
	</form>
	<form method="POST" action="./addmarkgep.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Маркеры гепатитов А,В, С, Д">
	</form>	
	<form method="POST" action="./addspid.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="СПИД (ВИЧ)">
	</form>
	</td>
	<td>
	<form method="POST" action="./addmocha.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Общий анализ мочи">
	</form>
	<form method="POST" action="./poch_testi.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Почечные тесты">
	</form>
	</form>
	<form method="POST" action="./addissledkrov.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Биохимическое исследование крови">
	</form>
	<form method="POST" action="./addanalizprotrombin.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Анализ крови на протромбин ">
	</form>
	<form method="POST" action="./protrombindex.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Протромбиновый индекс">
	</form>
	<form method="POST" action="./addrengorggrudklet.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Рентгенография органов грудной клетки">
	</form>
	<form method="POST" action="./addekg.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="ЭКГ">
	</form>
	</td>
	<td>
	<form method="POST" action="./addeho.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Эхокардиография">
	</form>
	<form method="POST" action="./adduzi12.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="УЗИ внутренних органов">
	</form>
	<form method="POST" action="./addokul.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Консультация окулиста">
	</form>
	<form method="POST" action="./addstam.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Консультация стоматолога">
	</form>
	<form method="POST" action="./addnewr.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Консультация невропатолога">
	</form>
	<form method="POST" action="./addlor.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Консультация ЛОР-врача">
	</form>
	<form method="POST" action="./addmedlech.php">
	<input type="hidden" name = "id_kart" value="<?php echo $_GET["kart"]; ?>">
	<input name="submit" class="button25" type="submit" value="Медикаментозное лечение">
	</form>
	</td>
	</tr>
	</table>
  </div>
  <div align="center"><?php zaptren(2, 1); ?></div>
  <div align="center"><?php zaptren(3, 1); ?></div>
  <div align="center">
  <p>
	<hr/>
	<h1>Общий анализ крови</h1>
	<hr/>
	<?php zaptren(13, 1); ?>
	<hr/>
	<h1>Время свертывания крови</h1>
	<hr/>
	<?php zaptren(14, 1); ?>
	<hr/>
	<h1>Реакция Вассермана </h1>
	<hr/>
	<?php zaptren(15, 1); ?>
	<hr/>
	<h1>Маркеры гепатитов А,В, С, Д</h1>
	<hr/>
	<?php zaptren(16, 1); ?>
	<hr/>
	<h1>СПИД (ВИЧ)</h1>
	<hr/>
	<?php zaptren(17, 1); ?>
	<hr/>
	<h1>Общий анализ мочи</h1>
	<hr/>
	<?php zaptren(18, 1); ?>
	<hr/>
	<h1>Почечные тесты</h1>
	<hr/>
	<?php zaptren(19, 1); ?>
	<hr/>
	<h1>Биохимическое исследование крови</h1>
	<hr/>
	<?php zaptren(20, 1); ?>
	<hr/>
	<h1>Анализ крови на протромбин</h1>
	<hr/>
	<?php zaptren(21, 1); ?>
	<hr/>
	<h1>Протромбиновый индекс</h1>
	<hr/>
	<?php zaptren(22, 1); ?>
	<hr/>
	<h1>Рентгенография органов грудной клетки</h1>
	<hr/>
	<?php zaptren(23, 1); ?>
	<hr/>
	<h1>ЭКГ</h1>
	<hr/>
	<?php zaptren(24, 1); ?>
	<hr/>
	<h1>Эхокардиография</h1>
	<hr/>
	<?php zaptren(25, 1); ?>
	<hr/>
	<h1>УЗИ внутренних органов</h1>
	<hr/>
	<?php zaptren(26, 1); ?>
	<hr/>
	<h1>Консультация окулиста</h1>
	<hr/>
	<?php zaptren(27, 1); ?>
	<hr/>
	<h1>Консультация стоматолога</h1>
	<hr/>
	<?php zaptren(28, 1); ?>
	<hr/>
	<h1>Консультация невропатолога</h1>
	<hr/>
	<?php zaptren(29, 1); ?>
	<hr/>
	<h1>Консультация ЛОР-врача</h1>
	<hr/>
	<?php zaptren(30, 1); ?>
	<hr/>
	<h1>Медикаментозное лечение</h1>
	<hr/>
	<?php zaptren(31,1); ?>
	<hr/>
	</p>
  </div>
</div>
