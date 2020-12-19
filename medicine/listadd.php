<?php
include 'style.php';
?>
<div align="center">
<form method="POST"  action="kart.php?kart=<?php echo $_POST["id_kart"]; ?>">
<table>
<tr>
<td align="center">
Дата поступления <br/><input type="date" name="date_post">
</td>
<td align="center">
Время поступления <br/><input type="time" name="vrem_post">
</td>
<td align="center">
Отдел<?php include 'zaptren.php'; zaptren(9, 1); ?>
</td>
</tr>
<tr>
<td align="center">
Предидущий отдел<?php zaptren(10, 1); ?>
</td>
<td align="center">
Номер палаты <br/><input name="nompalat" type="text"> 
</td>
<td align="center">
Количество дней <br/><input name="kolvodney" type="text"> 
</td>
</tr>
<tr>
<td align="center">
Дата выписки <br/><input type="date" name="date_vipis">
</td>
<td align="center">
Время выписки <br/><input type="time" name="vrem_vipis">
</td>
<td align="center">
Кем направлен <br/><input name="kemnapravlen" type="text">
</td>
</tr>
<tr>
<td align="center">
Диагноз направившего учреждения <br/><input name="diagnoznaprucher" type="text"> 
</td>
<td align="center">
Территория страхования <br/><input name="terrstrah" type="text">
</td>
<td align="center">
Диагноз при поступлении <br/><input name="diagnozpripost" type="text">
</td>
</tr>
<tr>
<td align="center">
Ф.И.О. врача поставившего диагноз <br/><input name="fiodiagnozdoc" type="text">
</td>
<td align="center">
Клинический диагноз<br/>
<script type="text/javascript">

   function changeFunc() {
    var selectBox = document.getElementById("selectBox");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("alltext").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="45" name="klindiag" id="alltext"></textarea>
<?php zaptren(12,2); ?>
</td>
<td align="center">
Назначенное лечение <br/><input name="naznachlechenie" type="text">
</td>
</tr>
<tr>
<td align="center">
Дата установления <br/><input type="date" name="date_ustan">
</td>
<td align="center">
Ф.И.О врача установившего <br/><input name="fiovrachaust" type="text">
</td>
<td align="center">
Диагноз заключительный клинический <br/><input name="diagnozzakklin" type="text">
</td>
</tr>
<tr>
<td align="center">
Основная болезнь <br/><input name="osnbolezn" type="text">
</td>
<td align="center">
Осложнение основного заболевания <br/><input name="osloghnenieosnzabolev" type="text">
</td>
<td align="center">
Сопутствующая болезнь <br/><input name="soputbolezn" type="text">
</td>
</tr>
<tr>
<td align="center">
Дата установления сопутствующей болезни <br/><input type="date" name="dateustsopbol">
</td>
<td align="center">
Ф.И.О врача сопутствующей болезни <br/><input name="fiovrachasoput" type="text">
</td>
<td align="center">
Название операции <br/><input name="nazvoper" type="text">
</td>
</tr>
<tr>
<td align="center">
Дата и время опрерации <br/><input type="datetime-local" name="datetimeoper">
</td>
<td align="center">
Метод обезбаливания <br/><input name="metod_obezbal" type="text">
</td>
<td align="center">
Осложнения <br/><input name="oslogneniya" type="text">
</td>
</tr>
<tr>
<td align="center">
Ф.И.О. оперировавшего врача <br/><input name="fio_operir_doc" type="text">
</td>
<td align="center">
Другие виды лечения <br/><input name="drugvidlech" type="text">
</td>
<td align="center">
Отметка о выдаче листка трудоспособности <br/><input name="vidlisttrud" type="text">
</td>
</tr>
<tr>
<td align="center">
Исход лечения в стационаре <?php zaptren(32,1); ?>
</td>
<td align="center">
Проверено <br/>
<script type="text/javascript">

   function changeFuncprover() {
    var selectBox = document.getElementById("selectBox222");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    document.getElementById("alltext222").value += selectedValue+"<br>\n";
   }
  </script>
<textarea rows="3" cols="45" name="provereno" id="alltext222"></textarea>
<?php zaptren(33,2); ?>
</td>
<td align="center">
Ф.И.О лечащего врача <?php zaptren(11, 1); ?>
</td>
</tr>
<tr>
<td align="center">
Ф.И.О заведующего отделением <br/><input name="fiozavotdel" type="text">
</td>
<td align="center">
<input name="addnewlist" class="button25" type="submit" value="Добавить">
</td>
</tr>
</table>
<input type="hidden" name = "id_kart" value="<?php echo $_POST["id_kart"]; ?>">
</form>
</div>