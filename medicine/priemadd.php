<?php 
			include 'zaptren.php';
			include 'style.php';
			$d0='id_priem';
			$d1='date_vrem_osmotr';
			$d2="ghalobi";
			$d3='istoriyazabol'; 
			$d4='socbitusloviya';
			$d5='estliprofvrednosti';
			$d6='napensii';
			$d7='invalidnost';
			$d8='perenesennie_bolezni';
			$d9='alerganamnez';
			$d10='faktoririska';
			$d11='mesyachnie';
			$d12='akusherskiyanamnez';
			$d13='techenienastberemennosti';
			$d14='priemlekprepvovremyaberemennosti';
			$d15="molochnieghelezi";
			$d16="obsheesost";
			$d17="temperatura";
			$d18="ves";
			$d19="rost";
			$d20="telosloghenie";
			$d21="pologheniebolnogo";
			$d22="koghaniepokrovi";
			$d23="nogti";
			$d24="mishci";
			$d25="kosti";
			$d26="sustavi";
			$d27="limfauzli";
			$d28="organichuvstv";
			$d29="nosovoedihanie";
			$d30="forma_grud_kletki";
			$d31="uchastie_grudkletkivaktedihaniya";
			$d32="uchastie_v_akte_dihaniya_mishc";
			$d33="golosovoe_droghanie";
			$d34="perkussiyalegkih";  
			$d35="opushenie_nighnih_granic";
			$d36="podvighnost_nighnego_kraya_legkih";
			$d37="Auskultativno_v_legkih_dihanie";
			$d38="hripi";
			$d39="chislodihaniyvminutu";
			$d40="ritm_tipdihaniya";
			$d41="osmotr_oblastiserdca";
			$d42="epigastralnaya_pulsaciya";
			$d43="vidimieserdechniepuls";
			$d44="cianoz";
			$d45="verhush_tolchok";
			$d46="granici_serdca";
			$d47="sosudistiypuchok";
			$d48="auskultachiya_serca_na_verhushke";
			$d49="perviy_ton";
			$d50="vtoroy_ton";
			$d51="sistolicheskiy_shum";
			$d52="diastolicheskiyshum";
			$d53="na_aorte";
			$d54="na_legochnoy_arterii"; 
			$d55="stol_distol_shum_naaorte";
			$d56="stol_distol_shum_nalegarterii";
			$d57="natrehstvorklapane";
			$d58="na_levom_krayu_grudini";
			$d59="arterii";
			$d60="veni";
			$d61="puls";
			$d62="chastota";
			$d63="deficitpulsa";
			$d64="arterialnoedavlenie";
			$d65="slizistaya_polosti_rta";
			$d66="zubi";
			$d67="yazik";
			$d68="zev";
			$d69="mindalini";
			$d70="ghivot";
			$d71="simptomi_razdragheniya_brushini";
			$d72="pechen";
			$d73="pri_palpacii_kraya";
			$d74="ghelchniy_puzir";	
			$d75="podghelud_gheleza";
			$d76="selezenka";
			$d77="stul_so_slov";
			$d78="mochepuskanie";
			$d79="simptomi_pokolach_po_12_rebru";
			$d80="palpaciya_pochek";
			$d81="mochevoy_puzir";
			$d82="polovie_organi";
			$d83="shitovidnaya_gheleza";
			$d84="glaznie_simptomi";	
			$d85="soznanie";
			$d86="nevrolog_status";
			$d87="glaznie_sheli";
			$d88="zrachki";
			$d89="dvighenie_glaz_jablok";
			$d90="lico";
			$d91="golovakrughenie";
			$d92="glotanie";
			$d93="jazik_pri_vsasivanii";
			$d94="poverh_i_glubok_reflex";	
			$d95="mish_tonus";
			$d96="chuvstvitelnost";
			$d97="sindrom_parkinsonizma";
			$d98="funk_taz_org";
			$d99="meningealnie_simptomi";
			$d100="simptom_kerninga";
			$d101="pamyat";
			$d102="intelekt";
			$d103="mnitelnost";
			$d104="vnushaemost";	
			$d105="rech";
			$d106="pohodka";
			$d107="patolog_reflex";
			$d108="dermografizm";
			$d109="v_poze_romberga";
			$d110="status_localis";
			$d111="dannie_ambul_obsled";
			$d112="date_end";
			$d113="time_end";
			$d114="id_doctor";	
			$d115="plan_obsled";
			$d116="id_predv_klin_diag";
			$d117="osnov_zabol";
			$d118="oslogh_osn_zabol";
			$d119="soput_zabol";			
			$d120="id_karta";
?>
<div align="center">
<form method="POST" action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<h1>Запись врача приемного отделения</h1>
<table>
<tr>
<td align="center">
Дата и время осмотра <br/><input name="<?php echo $d1; ?>" type="datetime-local">
</td>
</tr>
<tr>
<td align="center">
Жалобы при поступлении <br/>
<script type="text/javascript">

   function changeFuncghaloba() {
    var selectBox = document.getElementById("selectBoxghaloba");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("ghaloba2").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="35" name="<?php echo $d2; ?>" id="ghaloba2"></textarea>
<?php zaptren(34,2); ?>
</td>
</tr>
</table>
<h1>История настоящего заболевания <h1/>
<script type="text/javascript">

   function changeFuncistzab() {
    var selectBox = document.getElementById("selectBoxistzab");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("istzabnast").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="20" cols="100" name="<?php echo $d3; ?>" id="istzabnast"></textarea>
<?php zaptren(35,2); ?>
<h1>История жизни больного </h1>
<table>
<tr>
<td align="center">
Социально бытовые условия 
<?php zaptren(36,1); ?>
</td>
<td align="center">
Профвредности
<p><select size="3" name="<?php echo $d5; ?>">
<option value="есть">есть</option>
<option value="нет">нет</option>
</select></p>
</td>
<td align="center">
На пенсии <br/><input name="<?php echo $d6; ?>">
</td>
<td align="center">
Инвалидность <?php zaptren(37,1); ?>
</td>
</tr>
<tr>
<td align="center">
Перенесенные болезни <br/>
<script type="text/javascript">

   function changeFuncperenesennie_bolezni() {
    var selectBox = document.getElementById("selectBoxperenesennie_bolezni");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("perenesennie_bolezni").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="35" name="<?php echo $d8; ?>" id="perenesennie_bolezni"></textarea>
<?php zaptren(38,2); ?>
</td>
<td align="center">
Аллергоанамнез <br/>
<script type="text/javascript">

   function changeFuncalerganamnez() {
    var selectBox = document.getElementById("selectBoxalerganamnez");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("alerganamnez").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="35" name="<?php echo $d9; ?>" id="alerganamnez"></textarea>
<?php zaptren(39,2); ?>
</td>
<td align="center">
Фактор риска <br/>
<script type="text/javascript">

   function changeFuncfaktoririska() {
    var selectBox = document.getElementById("selectBoxfaktoririska");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("faktoririska2").value += selectedValue+"<br>";
   }
  </script>
<textarea rows="3" cols="35" name="<?php echo $d10; ?>" id="faktoririska2"></textarea>
<?php zaptren(40,2); ?>
</td>
<td align="center">
Месячные <br/><input name="<?php echo $d11; ?>">
</td>
</tr>
<tr>
<td align="center">
Акушерский анамнез <br/>
<textarea rows="3" cols="35" name="<?php echo $d12; ?>"></textarea>
</td>
<td align="center">
Течение настоящей беременности <br/>
<textarea rows="3" cols="35" name="<?php echo $d13; ?>"></textarea>
</td>
<td align="center">
Прием лекарственных средств во время беременности <br/>
<script type="text/javascript">

   function changeFuncpriemlekprepvovremyaberemennosti() {
    var selectBox = document.getElementById("selectBoxpriemlekprepvovremyaberemennosti");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("priemlekprepvovremyaberemennosti2").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="35" name="<?php echo $d14; ?>" id="priemlekprepvovremyaberemennosti2"></textarea>
<?php zaptren(41,2); ?>
</td>
<td align="center">
Молочные железы <br/><input name="<?php echo $d15; ?>">
</td>
</tr>
</table>
<h1>Настоящее состояние больного<h1/>
<table>
<tr>
<td align="center">
Общее состояние 
<p><select size="3" name="<?php echo $d16; ?>">
<option value="Удовлетворительное">Удовлетворительное</option>
<option value="Средней тяжести">Средней тяжести</option>
<option value="Тяжелое">Тяжелое</option>
</select></p>
</td>
<td align="center">
Температура <br/><input name="<?php echo $d17; ?>">
</td>
<td align="center">
Вес <br/><input name="<?php echo $d18; ?>">
</td>
</tr>
<tr>
<td align="center">
Рост <br/><input name="<?php echo $d19; ?>">
</td>
<td align="center">
Телосложение <br/>
<p><select size="3" name="<?php echo $d20; ?>">
<option value="Астеническое">Астеническое</option>
<option value="Нормостеническое">Нормостеническое</option>
<option value="Гиперстеническое">Гиперстеническое</option>
</select></p>
</td>
<td align="center">
Положение больного
<p><select size="3" name="<?php echo $d21; ?>">
<option value="Активное">Активное</option>
<option value="Вынужденное">Вынужденное</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Кожание покровы
<?php zaptren(42,1); ?>
</td>
<td align="center">
Ногти 
<p><select size="3" name="<?php echo $d23; ?>">
<option value="Здоровые">Здоровые</option>
<option value="Сухие">Сухие</option>
<option value="Тусклые">Тусклые</option>
<option value="Ломкие">Ломкие</option>
<option value="Деформированные">Деформированные</option>
<option value="В виде часовых стекол">В виде часовых стекол</option>
<option value="Другие">Другие</option>
</select></p>
</td>
<td align="center">
Мышцы
<p><select size="3" name="<?php echo $d24; ?>">
<option value="Степень развития">Степень развития</option>
<option value="Мышечный тонус">Мышечный тонус</option>
<option value="Атрофия">Атрофия</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Кости
<p><select size="3" name="<?php echo $d25; ?>">
<option value="Деформация">Деформация</option>
<option value="Периоститы">Периоститы</option>
<option value="Уменьшение эпифизов">Уменьшение эпифизов</option>
<option value="Искривление позвоночника">Искривление позвоночника</option>
<option value="Другие">Другие</option>
</select></p>
</td>
<td align="center">
Суставы 
<p><select size="3" name="<?php echo $d26; ?>">
<option value="Внешне не изменены">Внешне не изменены</option>
<option value="Движение в полном объеме">Движение в полном объеме</option>
<option value="Подвижность">Подвижность</option>
<option value="Болезненность">Болезненность</option>
<option value="Периартикулярные инфильтрации">Периартикулярные инфильтрации</option>
<option value="Контрактуры">Контрактуры</option>
<option value="Другие">Другие</option>
</select></p>
</td>
<td align="center">
Лимфаузлы 
<p><select size="3" name="<?php echo $d27; ?>">
<option value="Не пальпируются">Не пальпируются</option>
<option value="Пальпируются">Пальпируются</option>
<option value="Спаяны">Спаяны</option>
<option value="Не спаяны с окружающей тканью">Не спаяны с окружающей тканью</option>
<option value="Болезненные">Болезненные</option>
<option value="Безболезненные">Безболезненные</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Органы чувств 
<p><select size="3" name="<?php echo $d28; ?>">
<option value="Глаза">Глаза</option>
<option value="Слух">Слух</option>
<option value="Чувствительность кожных покровов">Чувствительность кожных покровов</option>
<option value="Обоняние">Обоняние</option>
</select></p>
</td>
<td align="center">
Носовое дыхание 
<p><select size="3" name="<?php echo $d29; ?>">
<option value="Свободное">Свободное</option>
<option value="Затрудненное справа">Затрудненное справа</option>
<option value="Затрудненное слева">Затрудненное слева</option>
<option value="Выделения">Выделения</option>
</select></p>
</td>
<td align="center">
Форма грудной клетки 
<p><select size="3" name="<?php echo $d30; ?>">
<option value="Нормостеническая">Нормостеническая</option>
<option value="Цилиндрическая">Цилиндрическая</option>
<option value="Эмфизематозная">Эмфизематозная</option>
<option value="Впавшая">Впавшая</option>
<option value="Деформирована">Деформирована</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Участие грудной клетки в процессе дыхания 
<p><select size="3" name="<?php echo $d31; ?>">
<option value="Симметрично">Симметрично</option>
<option value="Отстает справа">Отстает справа</option>
<option value="Отстает слева">Отстает слева</option>
</select></p>
</td>
<td align="center">
Участие мышц в акте дыхания  
<p><select size="3" name="<?php echo $d32; ?>">
<option value="Шеи">Шеи</option>
<option value="Межреберий">Межреберий</option>
<option value="Плечевого пояса">Плечевого пояса</option>
<option value="Втяжение межреберий">Втяжение межреберий</option>
</select></p>
</td>
<td align="center">
Голосовое дрожание 
<p><select size="3" name="<?php echo $d33; ?>">
<option value="Проводится одинаково с обеих сторон">Проводится одинаково с обеих сторон</option>
<option value="Усилено">Усилено</option>
<option value="Ослаблено">Ослаблено</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Перкуссия легких 
<p><select size="3" name="<?php echo $d34; ?>">
<option value="Легочной звук">Легочной звук</option>
<option value="Притупление слева">Притупление слева</option>
<option value="Притупление справа">Притупление справа</option>
<option value="Коробочный">Коробочный</option>
<option value="Другие">Другие</option>
</select></p>
</td>
<td align="center">
Опущение нижних границ 
<p><select size="3" name="<?php echo $d35; ?>">
<option value="На 1 ребро">На 1 ребро</option>
<option value="На 2 ребра">На 2 ребра</option>
<option value="Границы в норме">Границы в норме</option>
</select></p>
</td>
<td align="center">
Подвижность нижнего края легких 
<p><select size="3" name="<?php echo $d36; ?>">
<option value="В норме">В норме</option>
<option value="Снижена до">Снижена до</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Аускультативно в легких дыхание 
<p><select size="3" name="<?php echo $d37; ?>">
<option value="Везикулярное">Везикулярное</option>
<option value="Жесткое">Жесткое</option>
<option value="С удлиненным выдохом">С удлиненным выдохом</option>
<option value="Ослабленное">Ослабленное</option>
<option value="Пуэрильное">Пуэрильное</option>
<option value="Другое">Другое</option>
</select></p>
</td>
<td align="center">
Хрипы 
<p><select size="3" name="<?php echo $d38; ?>">
<option value="Нет">Нет</option>
<option value="Сухие при форсированном выдохе">Сухие при форсированном выдохе</option>
<option value="Рассеянные сухие в умеренном количеств">Рассеянные сухие в умеренном количеств</option>
<option value="Масса сухих хрипов">Масса сухих хрипов</option>
<option value="Влажные мелкопузырчатые">Влажные мелкопузырчатые</option>
<option value="Крепитация">Крепитация</option>
<option value="Другое">Другое</option>
</select></p>
</td>
<td align="center">
Число дыханий в минуту <br/><input name="<?php echo $d39; ?>">
</td>
</tr>
<tr>
<td align="center">
Ритм (Тип дыхания)
<p><select size="3" name="<?php echo $d40; ?>">
<option value="смешанный">смешанный</option>
<option value="Обе половины грудной клетки одинаково участвуют в акте дыхания">Обе половины грудной клетки одинаково участвуют в акте дыхания</option>
<option value="Дыхание обычной глубины">Дыхание обычной глубины</option>
<option value="Ритм правильный">Ритм правильный</option>
<option value="С частотой">С частотой</option>
</select></p>
</td>
<td align="center">
Осмотр области сердца 
<p><select size="3" name="<?php echo $d41; ?>">
<option value="Область сердца не изменена">Область сердца не изменена</option>
<option value="Имеется сердечный горб">Имеется сердечный горб</option>
</select></p>
</td>
<td align="center">
Эпигастральная пульсация 
<p><select size="3" name="<?php echo $d42; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Выраженная">Выраженная</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Видимые сердечные пульсации <br/><input name="<?php echo $d43; ?>">
</td>
<td align="center">
Цианоз 
<p><select size="3" name="<?php echo $d44; ?>">
<option value="Нет">Нет</option>
<option value="Акроцианоз">Акроцианоз</option>
<option value="Диффузный">Диффузный</option>
<option value="Легкий">Легкий</option>
<option value="Выраженный">Выраженный</option>
<option value="Умеренный">Умеренный</option>
</select></p>
</td>
<td align="center">
Верхушечный толчек 
<p><select size="3" name="<?php echo $d45; ?>">
<option value="Не пальпируется">Не пальпируется</option>
<option value="Пальпируется в">Пальпируется в</option>
<option value="Не усилен">Не усилен</option>
<option value="Усилен">Усилен</option>
<option value="Разлитой">Разлитой</option>
<option value="Ослаблен">Ослаблен</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Границы сердца 
<p><select size="3" name="<?php echo $d46; ?>">
<option value="Правая">Правая</option>
<option value="Левая">Левая</option>
<option value="Верхняя">Верхняя</option>
</select></p>
</td>
<td align="center">
Сосудистый пучек <br/><input name="<?php echo $d47; ?>">
</td>
<td align="center">
Аускультация сердца на верхушке
<p><select size="3" name="<?php echo $d48; ?>">
<option value="Тоны сохранены">Тоны сохранены</option>
<option value="Ритмичны">Ритмичны</option>
<option value="Шумов нет">Шумов нет</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Первый тон 
<p><select size="3" name="<?php echo $d49; ?>">
<option value="Сохранен">Сохранен</option>
<option value="Усилен">Усилен</option>
<option value="Ослаблен">Ослаблен</option>
</select></p>
</td>
<td align="center">
Второй тон 
<p><select size="3" name="<?php echo $d50; ?>">
<option value="Сохранен">Сохранен</option>
<option value="Усилен">Усилен</option>
<option value="Ослаблен">Ослаблен</option>
</select></p>
</td>
<td align="center">
Систолический шум 
<p><select size="3" name="<?php echo $d51; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Характеристика">Характеристика</option>
</select></p> 
</td>
</tr>
<tr>
<td align="center">
Диастолический шум  
<p><select size="3" name="<?php echo $d52; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Характеристика">Характеристика</option>
</select></p> 
</td>
<td align="center">
На аорте 
<p><select size="3" name="<?php echo $d53; ?>">
<option value="II тон сохранен">II тон сохранен</option>
<option value="Акцент">Акцент</option>
<option value="Ослаблен">Ослаблен</option>
</select></p> 
</td>
<td align="center">
На легочной артерии 
<p><select size="3" name="<?php echo $d54; ?>">
<option value="II тон сохранен">II тон сохранен</option>
<option value="Акцент">Акцент</option>
<option value="Ослаблен">Ослаблен</option>
</select></p> 
</td>
</tr>
<tr>
<td align="center">
Систолический, диастолический шумы на аорте
<p><select size="3" name="<?php echo $d55; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Характеристика">Характеристика</option>
</select></p> 
</td>
<td align="center">
Систолический, диастолический шумы на легочной артерии 
<p><select size="3" name="<?php echo $d56; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Характеристика">Характеристика</option>
</select></p> 
</td>
<td align="center">
На трехстворчатом клапане <br/><input name="<?php echo $d57; ?>">
</td>
</tr>
<tr>
<td align="center">
На левом краю грудины <br/><input name="<?php echo $d58; ?>">
</td>
<td align="center">
Артерии 
<p><select size="3" name="<?php echo $d59; ?>">
<option value="Пульсация сохранена">Пульсация сохранена</option>
<option value="Симметрична">Симметрична</option>
<option value="Ослаблена">Ослаблена</option>
</select></p> 
</td>
<td align="center">
Вены <br/><input name="<?php echo $d60; ?>">
</td>
</tr>
<tr>
<td align="center">
Пульс <br/><input name="<?php echo $d61; ?>">
</td>
<td align="center">
Частота <br/><input name="<?php echo $d62; ?>">
</td>
<td align="center">
Дефицит пульса <br/><input name="<?php echo $d63; ?>">
</td>
</tr>
<tr>
<td align="center">
Артериальное давление 
<p><select size="3" name="<?php echo $d64; ?>">
<option value="Шум около пупочной области">Шум около пупочной области</option>
<option value="Есть">Есть</option>
<option value="Нет">Нет</option>
</select></p> 
</td>
<td align="center">
Слизистая полость рта
<p><select size="3" name="<?php echo $d65; ?>">
<option value="Обычной окраски">Обычной окраски</option>
<option value="Бледная">Бледная</option>
</select></p> 
</td>
<td align="center">
Зубы <br/><input name="<?php echo $d66; ?>">
</td>
</tr>
<tr>
<td align="center">
Язык <br/>
<p><select size="3" name="<?php echo $d67; ?>">
<option value="Сухой">Сухой</option>
<option value="Влажный">Влажный</option>
<option value="Чистый">Чистый</option>
<option value="Обложен">Обложен</option>
</select></p> 
</td>
<td align="center">
Зев <br/><input name="<?php echo $d68; ?>">
</td>
<td align="center">
Миндалины <br/><input name="<?php echo $d69; ?>">
</td>
</tr>
<tr>
<td align="center">
Живот
<p><select size="3" name="<?php echo $d70; ?>">
<option value="Обычных размеров">Обычных размеров</option>
<option value="Увеличен">Увеличен</option>
<option value="Не увеличен">Не увеличен</option>
<option value="Мягкий">Мягкий</option>
<option value="Болезнен">Болезнен</option>
<option value="Безболезнен">Безболезнен</option>
</select></p> 
</td>
<td align="center">
Симптомы раздражения брюшины 
<p><select size="3" name="<?php echo $d71; ?>">
<option value="Есть">Есть</option>
<option value="Нет">Нет</option>
</select></p> 
</td>
<td align="center">
Печень  
<p><select size="3" name="<?php echo $d72; ?>">
<option value="Размеры печени по Курлову в норме">Размеры печени по Курлову в норме</option>
<option value="Увеличены">Увеличены</option>
<option value="Верхняя по среднеключичной линии">Верхняя по среднеключичной линии</option>
<option value="Нижняя по среднеключичной линии">Нижняя по среднеключичной линии</option>
<option value="Левой реберной дуге">Левой реберной дуге</option>
</select></p> 
</td>
</tr>
<tr>
<td align="center">
При пальпации края 
<p><select size="3" name="<?php echo $d73; ?>">
<option value="Болезненность">Болезненность</option>
<option value="Поверхность">Поверхность</option>
<option value="Консистенция">Консистенция</option>
</select></p>
</td>
<td align="center">
Желчный пузырь 
<p><select size="3" name="<?php echo $d74; ?>">
<option value="не пальпируется">не пальпируется</option>
<option value="пальпируется">пальпируется</option>
<option value="симптомы желчного пузыря положительные">симптомы желчного пузыря положительные</option>
</select></p>
</td>
<td align="center">
Поджелудочная железа <br/><input name="<?php echo $d75; ?>">
</td>
</tr>
<tr>
<td align="center">
Селезенка 
<p><select size="3" name="<?php echo $d76; ?>">
<option value="Верхняя граница по средней мышечной линии">Верхняя граница по средней мышечной линии</option>
<option value="Нижняя граница по средней мышечной линии">Нижняя граница по средней мышечной линии</option>
<option value="При пальпации края">При пальпации края</option>
<option value="Консистенция">Консистенция</option>
<option value="Болезненность">Болезненность</option>
</select></p>
</td>
<td align="center">
Стул со слов 
<p><select size="3" name="<?php echo $d77; ?>">
<option value="Регулярный">Регулярный</option>
<option value="Жидкий">Жидкий</option>
<option value="Запоры">Запоры</option>
</select></p>
</td>
<td align="center">
Мочепускание 
<p><select size="3" name="<?php echo $d78; ?>">
<option value="Свободное">Свободное</option>
<option value="Затрудненное">Затрудненное</option>
<option value="Частое">Частое</option>
<option value="Редкое">Редкое</option>
<option value="болезненное">болезненное</option>
<option value="безболезненное">безболезненное</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Симптомы покалачивания по 12 ребру 
<p><select size="3" name="<?php echo $d79; ?>">
<option value="Отрицательный">Отрицательный</option>
<option value="Положительный">Положительный</option>
<option value="Справа">Справа</option>
<option value="Слева">Слева</option>
</select></p>
</td>
<td align="center">
Полпация почек 
<p><select size="3" name="<?php echo $d80; ?>">
<option value="Не пальпируется">Не пальпируется</option>
<option value="Пальпируется">Пальпируется</option>
<option value="Болезненная">Болезненная</option>
</select></p>
</td>
<td align="center">
Мочевой пузырь <br/><input name="<?php echo $d81; ?>"> 
</td>
</tr>
<tr>
<td align="center">
Половые органы <br/><input name="<?php echo $d82; ?>"> 
</td>
<td align="center">
Щитовидная железа
<p><select size="3" name="<?php echo $d83; ?>">
<option value="Пальпируется">Пальпируется</option>
<option value="Увеличена">Увеличена</option>
<option value="Мягкая">Мягкая</option>
<option value="Плотная">Плотная</option>
<option value="Узлы">Узлы</option>
</select></p>
</td>
<td align="center">
Глазные симптомы 
<p><select size="3" name="<?php echo $d84; ?>">
<option value="Экзофтальм">Экзофтальм</option>
<option value="Нистагм">Нистагм</option>
<option value="Зрачковые рефлексы">Зрачковые рефлексы</option>
<option value="Симптомы Мебиуса, Грефе">Симптомы Мебиуса, Грефе</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Сознание
<p><select size="3" name="<?php echo $d85; ?>">
<option value="Ясное">Ясное</option>
<option value="Заторможенное">Заторможенное</option>
<option value="Ориентирован в месте, во времени">Ориентирован в месте, во времени</option>
<option value="Не ориентирован">Не ориентирован</option>
</select></p>
</td>
<td align="center">
Неврологический статус 
<p><select size="3" name="<?php echo $d86; ?>">
<option value="Воспринимает хорошо">Воспринимает хорошо</option>
<option value="Снижено">Снижено</option>
<option value="Утрачено">Утрачено</option>
</select></p>
</td>
<td align="center">
Глазные щели 
<p><select size="3" name="<?php echo $d87; ?>">
<option value="Одинаковые">Одинаковые</option>
<option value="Полуптоз">Полуптоз</option>
<option value="Птоз слева">Птоз слева</option>
<option value="Птоз справа">Птоз справа</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Зрачки 
<p><select size="3" name="<?php echo $d88; ?>">
<option value="Округлой формы">Округлой формы</option>
<option value="Деформированы">Деформированы</option>
<option value="Размеры">Размеры</option>
</select></p>
</td>
<td align="center">
Давление глазных яблок 
<p><select size="3" name="<?php echo $d89; ?>">
<option value="В полном объеме">В полном объеме</option>
<option value="Ограничены">Ограничены</option>
</select></p>
</td>
<td align="center">
Лицо 
<p><select size="3" name="<?php echo $d90; ?>">
<option value="Симметричное">Симметричное</option>
<option value="Сглаженность носогубной складки">Сглаженность носогубной складки</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Головокружение 
<p><select size="3" name="<?php echo $d91; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Несистемное">Несистемное</option>
<option value="Системное">Системное</option>
</select></p>
</td>
<td align="center">
Глотание  
<p><select size="3" name="<?php echo $d92; ?>">
<option value="Сохранено">Сохранено</option>
<option value="Дисфагия">Дисфагия</option>
<option value="Небный и глоточный рефлексы">Небный и глоточный рефлексы</option>
</select></p>
</td>
<td align="center">
Язык при всасывании 
<p><select size="3" name="<?php echo $d93; ?>">
<option value="По средней линии">По средней линии</option>
<option value="Отклонен вправо">Отклонен вправо</option>
<option value="Отклонен влево">Отклонен влево</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Поверхностный и глубокий рефлекс <br/><input name="<?php echo $d94; ?>">						
</td>
<td align="center">
Мышечный тонус <br/><input name="<?php echo $d95; ?>">
</td>
<td align="center">
Чувствительность 
<p><select size="3" name="<?php echo $d96; ?>">
<option value="Сохранена">Сохранена</option>
<option value="Нарушена по периферическому типу">Нарушена по периферическому типу</option>
<option value="Нарушена по сегментарному типу">Нарушена по сегментарному типу</option>
<option value="Нарушена по проводниковому типу">Нарушена по проводниковому типу</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
Синдром паркенсионизма 
<p><select size="3" name="<?php echo $d97; ?>">
<option value="Нет">Нет</option>
<option value="Есть">Есть</option>
<option value="Форма">Форма</option>
</select></p>
</td>
<td align="center">
Функции тазовых органов <br/><input name="<?php echo $d98; ?>">
</td>
<td align="center">
Менингеальные симптомы <br/><input name="<?php echo $d99; ?>">
</td>
</tr>
<tr>
<td align="center">
Симптом Кернинга <br/><input name="<?php echo $d100; ?>">
</td>
<td align="center">
Память <br/><input name="<?php echo $d101; ?>"> 
</td>
<td align="center">
Интелект <br/><input name="<?php echo $d102; ?>"> 
</td>
</tr>
<tr>
<td align="center">
Мнительность <br/><input name="<?php echo $d103; ?>">
</td>
<td align="center">
Внушаемость <br/><input name="<?php echo $d104; ?>">
</td>
<td align="center">
Речь <br/><input name="<?php echo $d105; ?>">
</td>
</tr>
<tr>
<td align="center">
Походка <br/><input name="<?php echo $d106; ?>">
</td>
<td align="center">
Патологические рефлексы <br/><input name="<?php echo $d107; ?>">
</td>
<td align="center">
Дермографизм 
<p><select size="3" name="<?php echo $d108; ?>">
<option value="Красный">Красный</option>
<option value="Стойкий">Стойкий</option>
<option value="Белый">Белый</option>
<option value="Нестойкий">Нестойкий</option>
</select></p>
</td>
</tr>
<tr>
<td align="center">
В позе Ромберга 
<p><select size="3" name="<?php echo $d109; ?>">
<option value="Устойчив">Устойчив</option>
<option value="Не устойчив">Не устойчив</option>
<option value="Тремор рук">Тремор рук</option>
<option value="Тремор туловищ">Тремор туловищ</option>
</select></p>
</td>
<td align="center">
Статус локалис <br/><input name="<?php echo $d110; ?>">
</td>
<td align="center">
Данные амбулаторного исследования <br/><input name="<?php echo $d111; ?>"> 
</td>
</tr>
<tr>
<td align="center">
Дата <br/><input name="<?php echo $d112; ?>" type="date" > 
</td>
<td align="center">
Время <br/><input name="<?php echo $d113; ?>" type="time" >
</td>
<td align="center">
Ф.И.О. врача <br/><?php zaptren(11, 1); ?>
</td>
</tr>
<tr>
<td align="center">
План обследования <br/>
<script type="text/javascript">

   function changeFuncplan() {
    var selectBox = document.getElementById("selectBoxplan");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("plan2").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="45" name="<?php echo $d115; ?>" id="plan2"></textarea>
<?php zaptren(43,2); ?>
</td>
<td align="center">
Предворительный клинический диагноз <?php zaptren(44,2); ?>
</td>
<td align="center">
Основное заболевание <input name="<?php echo $d117; ?>"> 
</td>
</tr>
<tr>
<td align="center">
Осложнение основного заболевания <input name="<?php echo $d118; ?>">
</td>
<td align="center">
Сопутствующие заболевания <input name="<?php echo $d119; ?>">
</td>
<td align="center">
<input name="addnewpriem" type="submit" class="button25" value="Добавить">
</td>
</tr>
</table>
<input type="hidden" name = "id_karta" value="<?php echo $_POST["id_kart"]; ?>">
</form>
</div>