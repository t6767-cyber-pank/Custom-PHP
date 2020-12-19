<?php
//header('Content-type: text/html; charset=utf-8');
include 'conect.php';
$table=""; // Имя таблицы
function zaptren($x, $y)
{
 switch($x)
 {
	case 1: 
			$table="karta";
			$d0='id_kart';
			$d1='FIO';
			$d2="godr";
			$d3='pol'; 
			$d4='pobochniy';
			$d5='krov';
			$d6='mestorab';
			$d7='nomersoc';
			$d8='graghd';
			$d9='kategory';
			$d10='nomerudv';
			$d11='pmg';
			$d12='vozrast';
			$d13='dolgnost';
			$d19='diagnoz';
			
			
			$d14='id_pol';
			$d15='id_pod_dey';
			$d16='id_grkr';
			$d17='id_grag';
			$d18='id_kateg';
			$par1="";
			$par2="";
			$par3="";
			$par4="";
			if (isset($_GET["kart"])) {  $par1=" and t.id_kart=".$_GET['kart']." ";  }
			if (isset($_POST["namer"])) {  $par2=" and ".$d1." like '%".$_POST["namer"]."%'";  }
			if (isset($_POST["dateer"])) {  $par3=" and ".$d2." like '%".$_POST["dateer"]."%'";  }
			if (isset($_POST["diagnozer11"])) {  $par4=" and ".$d19." like '%".$_POST["diagnozer11"]."%'";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." t, graghdanstvo g, kategory k, krov b, pobochka pob, pol pol where t.id_pol=pol.id_pol and  t.id_pod_dey=pob.id_pob and t.id_grkr=b.id_gkr and t.id_grag=g.id_gr and t.id_kateg=k.id_kat ".$par2." ".$par3." ".$par4." order by ".$d1." asc");				
					$indesg=0;
						echo "<table border='1' width='100%'>";
						echo "<tr>";
						echo "<th>№ п/п</th>";
						echo "<th>Ф.И.О.</th>";
						echo "<th>Дата рождения</th>";
						echo "<th>Диагноз</th>";
						echo "<th>Категория</th>";
						echo "<th>Изменить запись</th>";
						echo "<th>Удалить запись</th>";
						echo "</tr>";
						while($data = @mysql_fetch_array($qr_result)){ 
						echo "<tr>";
						$indesg++;
						echo "<td align='center'>";
						echo $indesg;
						echo "</td>";
						echo "<td align='center'>";
						echo '<a href="kart.php?kart='.$data[$d0].'" class="sdvig">'.$data[$d1];
						echo '</a>';
						echo "</td>";
						echo "<td align='center'>";
						echo $data[$d2];
						echo "</td>";
						echo "<td align='center'>";
						echo $data[$d19];
						echo "</td>";
						echo "<td align='center'>";
						echo $data[$d9];
						echo "</td>";
						echo "<td align='center'>";
						echo '<form method="POST"  action="insert.php">
						<input type="hidden" name = "updateid" value="'.$data[$d0].'">
						<input name="submit" class="button8" type="submit" value="Изменить">
						</form>';
						echo "</td>";
						echo "<td align='center'>";
						echo '<form method="POST"  action="index.php">
						<input type="hidden" name = "dellid" value="'.$data[$d0].'">
						<input name="submit" class="button8" type="submit" value="Удалиь">
						</form>';
						echo "</td>";
						echo "</tr>";
						}
						echo "</table>";
				break;
				case 2:
					$qr_result = mysql_query("select * from karta t, graghdanstvo g, kategory k, krov b, pobochka pob, pol pol where t.id_grag=g.id_gr ".$par1." and t.id_kateg=k.id_kat and t.id_grkr=b.id_gkr and t.id_pod_dey=pob.id_pob and t.id_pol=pol.id_pol");				
					while($data = @mysql_fetch_array($qr_result)){    
							$_GET[$d0]=$data[$d0];
							$_GET["FIOpac"]=$data[$d1];
							$_GET[$d2]=$data[$d2];
							$_GET[$d3]=$data[$d3];
							$_GET[$d4]=$data[$d4];
							$_GET[$d5]=$data[$d5];
							$_GET[$d6]=$data[$d6];
							$_GET[$d7]=$data[$d7];
							$_GET[$d8]=$data[$d8];
							$_GET[$d9]=$data[$d9];
							$_GET[$d10]=$data[$d10];
							$_GET[$d11]=$data[$d11];
							$_GET[$d12]=$data[$d12];
							$_GET[$d13]=$data[$d13];
							$_GET[$d14]=$data[$d14];
							$_GET[$d15]=$data[$d15];
							$_GET[$d16]=$data[$d16];
							$_GET[$d17]=$data[$d17];
							$_GET[$d18]=$data[$d18];
							$_GET[$d19]=$data[$d19];
						}
				break;
					case 3:
							if (!isset($_POST["FIOpac"]) || $_POST["FIOpac"]=="") {$_POST[$d1]="Нет данных";}
							if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]="Нет данных";}
							if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
							if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
							if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
							if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
							if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
							if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
							if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
							if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
							if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
							if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]=0;}
							if (!isset($_POST[$d13]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
							if (!isset($_POST[$d14]) || $_POST[$d14]=="") {$_POST[$d14]=3;}
							if (!isset($_POST[$d15]) || $_POST[$d15]=="") {$_POST[$d15]=4;}					
							if (!isset($_POST[$d16]) || $_POST[$d16]=="") {$_POST[$d16]=10;}
							if (!isset($_POST[$d17]) || $_POST[$d17]=="") {$_POST[$d17]=7;}
							if (!isset($_POST[$d18]) || $_POST[$d18]=="") {$_POST[$d18]=5;}
							if (!isset($_POST[$d19]) || $_POST[$d19]=="") {$_POST[$d19]="Нет данных";}
							
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d14.", ".$d15.", ".$d16.", ".$d6.", ".$d7.", ".$d17.", ".$d18.", ".$d10.", ".$d11.", ".$d12.", ".$d13.", ".$d19.") values('".$_POST["FIOpac"]."', '".$_POST[$d2]."', ".$_POST[$d14].", ".$_POST[$d15].", ".$_POST[$d16].", '".$_POST[$d6]."', '".$_POST[$d7]."', ".$_POST[$d17].", ".$_POST[$d18].", '".$_POST[$d10]."', '".$_POST[$d11]."', ".$_POST[$d12].", '".$_POST[$d13]."', '".$_POST[$d19]."')");				
					break;
				case 77:
					$qr_result = mysql_query("update ".$table." set ".$d1."='".$_POST["FIOpac"]."', ".$d2."='".$_POST[$d2]."', ".$d12."=".$_POST[$d12].", ".$d14."=".$_POST[$d14].", ".$d15."=".$_POST[$d15].", ".$d16."=".$_POST[$d16].", ".$d6."='".$_POST[$d6]."', ".$d13."='".$_POST[$d13]."', ".$d7."='".$_POST[$d7]."', ".$d17."=".$_POST[$d17].", ".$d18."=".$_POST[$d18].", ".$d10."='".$_POST[$d10]."', ".$d11."='".$_POST[$d11]."', ".$d19."='".$_POST[$d19]."' where ".$d0."=".$_POST["updateid"]);
					break;
				case 88:
					$qr_result = mysql_query("select * from karta t where t.id_kart=".$_POST["updateid"]);				
					while($data = @mysql_fetch_array($qr_result)){    
							$_GET[$d1]=$data[$d0];
							$_GET[$d1]=$data[$d1];
							$_GET[$d2]=$data[$d2];
							$_GET[$d3]=$data[$d3];
							$_GET[$d4]=$data[$d4];
							$_GET[$d5]=$data[$d5];
							$_GET[$d6]=$data[$d6];
							$_GET[$d7]=$data[$d7];
							$_GET[$d8]=$data[$d8];
							$_GET[$d9]=$data[$d9];
							$_GET[$d10]=$data[$d10];
							$_GET[$d11]=$data[$d11];
							$_GET[$d12]=$data[$d12];
							$_GET[$d13]=$data[$d13];
							$_GET[$d14]=$data[$d14];
							$_GET[$d15]=$data[$d15];
							$_GET[$d16]=$data[$d16];
							$_GET[$d17]=$data[$d17];
							$_GET[$d18]=$data[$d18];
							$_GET[$d19]=$data[$d19];
						}
					break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellid"]);	
					$qr_result = mysql_query("delete from list where ".$d0."=".$_POST["dellid"]);
					$qr_result = mysql_query("delete from priemnoeotdelenie where ".$d0."=".$_POST["dellid"]);	
					$qr_result = mysql_query("delete from obsiyanalizkrovi where ".$d0."=".$_POST["dellid"]);
					$qr_result = mysql_query("delete from timesvert where ".$d0."=".$_POST["dellid"]);	
					$qr_result = mysql_query("delete from reakciyavasermana where ".$d0."=".$_POST["dellid"]);	
					$qr_result = mysql_query("delete from markgepat where ".$d0."=".$_POST["dellid"]);	
					$qr_result = mysql_query("delete from spid where ".$d0."=".$_POST["dellid"]);
				break;
			}
			break;
	case 2:
			$table="list";
			$d0='id_list';
			$d1='date_post';
			$d2="vrem_post";
			$d3='otdel'; 
			$d4='otdelperev';
			$d5='nompalat';
			$d6='kolvodney';
			$d7='kemnapravlen';
			$d8='diagnoznaprucher';
			$d9='terrstrah';
			$d10='diagnozpripost';
			$d11='FIO';
			$d12='klindiag';
			$d13='naznachlechenie';
			$d14='date_vipis';
			$d15="vrem_vipis";
			$d16="date_ustan";
			$d17="fiovrachaust";
			$d18="diagnozzakklin";
			$d19="osnbolezn";
			$d20="osloghnenieosnzabolev";
			$d21="soputbolezn";
			$d22="dateustsopbol";
			$d23="fiovrachasoput";
			$d24="nazvoper";
			$d25="datetimeoper";
			$d26="metod_obezbal";
			$d27="oslogneniya";
			$d28="fio_operir_doc";
			$d29="drugvidlech";
			$d30="vidlisttrud";
			$d31="ishodlechvstoc";
			$d32="provereno";
			$d33="fiodiagnozdoc";
			$d34="fiozavotdel";
			
			$d35="id_kart";
			$d36="id_otdel";
			$d37="id_perevod";
			$d38="id_doctor";
			$par1="";
			if (isset($_GET["kart"])) {$par1=" and t.id_kart=".$_GET['kart']." ";} else { $par1=""; }
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table." t, otdel o, perevodotdel p, doctor d where d.id_doctor=t.id_doctor and t.id_otdel=o.id_otdel and p.id_otdelperev=t.id_perevod ".$par1." order by t.".$d1." desc");		
						$cou=0;
						while($data = @mysql_fetch_array($qr_result))
						{
							$cou++;
							echo '<h2>Запись № '.$cou.'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d35].'">
							<input type="hidden" name = "dellid" value="'.$data[$d0].'">
							<input name="submit" class="button8" type="submit" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Дата поступления</dt> <dd>'.$data[$d1].'</dd>';
							echo '<dt>Время поступления</dt> <dd>'.$data[$d2].'</dd>';
							echo '<dt>Отдел</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Предидущий отдел</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Номер палаты</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Дата выписки</dt> <dd>'.$data[$d14].'</dd>';
							echo '<dt>Время выписки</dt> <dd>'.$data[$d15].'</dd>';
							echo '<dt>Количество дней</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Кем направлен</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Диагноз направившего учреждения</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Территория страхования</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>Диагноз при поступлении</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>Ф.И.О. врача поставившего диагноз</dt> <dd>'.$data[$d33].'</dd>';
							echo '<dt>Клинический диагноз</dt> <dd>'.$data[$d12].'</dd>';
							echo '<dt>Дата установления</dt> <dd>'.$data[$d16].'</dd>';
							echo '<dt>Ф.И.О врача установившего</dt> <dd>'.$data[$d17].'</dd>';
							echo '<dt>Диагноз заключительный клинический</dt> <dd>'.$data[$d18].'</dd>';
							echo '<dt>Назначенное лечение</dt> <dd>'.$data[$d13].'</dd>';
							echo '<dt>Основная болезнь</dt> <dd>'.$data[$d19].'</dd>';
							echo '<dt>Осложнение основного заболевания</dt> <dd>'.$data[$d20].'</dd>';
							echo '<dt>Сопутствующая болезнь</dt> <dd>'.$data[$d21].'</dd>';
							echo '<dt>Дата установления сопутствующей болезни</dt> <dd>'.$data[$d22].'</dd>';
							echo '<dt>Ф.И.О врача сопутствующей болезни</dt> <dd>'.$data[$d23].'</dd>';
							echo '<dt>Название операции</dt> <dd>'.$data[$d24].'</dd>';
							echo '<dt>Дата и время опрерации</dt> <dd>'.$data[$d25].'</dd>';
							echo '<dt>Метод обезбаливания</dt> <dd>'.$data[$d26].'</dd>';
							echo '<dt>Осложнения</dt> <dd>'.$data[$d27].'</dd>';
							echo '<dt>Ф.И.О. оперировавшего врача</dt> <dd>'.$data[$d28].'</dd>';
							echo '<dt>Другие виды лечения</dt> <dd>'.$data[$d29].'</dd>';
							echo '<dt>Отметка о выдаче листка трудоспособности</dt> <dd>'.$data[$d30].'</dd>';
							echo '<dt>Исход лечения в стационаре</dt> <dd>'.$data[$d31].'</dd>'; // таблицы
							echo '<dt>Проверено</dt> <dd>'.$data[$d32].'</dd>'; // таблицы
							echo '<dt>Ф.И.О лечащего врача</dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>Ф.И.О заведующего отделением</dt> <dd>'.$data[$d34].'</dd>';
							echo '</dl>';
						} 
						break;
						case 3:
							if (!isset($_POST[$d1]) || $_POST[$d1]=="") {$_POST[$d1]=date("Y-m-d");}
							if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("H:i");}
							if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
							if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
							if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
							if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]=0;}
							if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
							if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
							if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
							if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
							if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
							if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
							if (!isset($_POST[$d13]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
							if (!isset($_POST[$d14]) || $_POST[$d14]=="") {$_POST[$d14]=date("Y-m-d");}
							if (!isset($_POST[$d15]) || $_POST[$d15]=="") {$_POST[$d15]=date("H:i");}					
							if (!isset($_POST[$d16]) || $_POST[$d16]=="") {$_POST[$d16]=date("Y-m-d");}
							if (!isset($_POST[$d17]) || $_POST[$d17]=="") {$_POST[$d17]="Нет данных";}
							if (!isset($_POST[$d18]) || $_POST[$d18]=="") {$_POST[$d18]="Нет данных";}
							if (!isset($_POST[$d19]) || $_POST[$d19]=="") {$_POST[$d19]="Нет данных";}
							if (!isset($_POST[$d20]) || $_POST[$d20]=="") {$_POST[$d20]="Нет данных";}
							if (!isset($_POST[$d22]) || $_POST[$d22]=="") {$_POST[$d22]=date("Y-m-d");}
							if (!isset($_POST[$d23]) || $_POST[$d23]=="") {$_POST[$d23]="Нет данных";}
							if (!isset($_POST[$d24]) || $_POST[$d24]=="") {$_POST[$d24]="Нет данных";}
							if (!isset($_POST[$d25]) || $_POST[$d25]=="") {$_POST[$d25]=date("Y-m-d H:i");}
							if (!isset($_POST[$d26]) || $_POST[$d26]=="") {$_POST[$d26]="Нет данных";}
							if (!isset($_POST[$d27]) || $_POST[$d27]=="") {$_POST[$d27]="Нет данных";}
							if (!isset($_POST[$d28]) || $_POST[$d28]=="") {$_POST[$d28]="Нет данных";}
							if (!isset($_POST[$d29]) || $_POST[$d29]=="") {$_POST[$d29]="Нет данных";}
							if (!isset($_POST[$d30]) || $_POST[$d30]=="") {$_POST[$d30]="Нет данных";}
							if (!isset($_POST[$d31]) || $_POST[$d31]=="") {$_POST[$d31]="Нет данных";}
							if (!isset($_POST[$d32]) || $_POST[$d32]=="") {$_POST[$d32]="Нет данных";}
							if (!isset($_POST[$d33]) || $_POST[$d33]=="") {$_POST[$d33]="Нет данных";}
							if (!isset($_POST[$d34]) || $_POST[$d34]=="") {$_POST[$d34]="Нет данных";}
							if (!isset($_POST[$d36]) || $_POST[$d36]=="") {$_POST[$d36]=4;}
							if (!isset($_POST[$d37]) || $_POST[$d37]=="") {$_POST[$d37]=4;}
							if (!isset($_POST[$d38]) || $_POST[$d38]=="") {$_POST[$d38]=4;} 
							$qr_result = mysql_query("insert into ".$table."(".$d35.", ".$d1.", ".$d2.", ".$d36.", ".$d37.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d33.", ".$d38.", ".$d12.", ".$d13.", ".$d14.", ".$d15.", ".$d16.", ".$d17.", ".$d18.", ".$d19.", ".$d20.", ".$d21.", ".$d22.", ".$d23.", ".$d24.", ".$d25.", ".$d26.", ".$d27.", ".$d28.", ".$d29.", ".$d30.", ".$d31.", ".$d32.", ".$d34.") values(".$_POST[$d35].", '".$_POST[$d1]."', '".$_POST[$d2]."', ".$_POST[$d36].", ".$_POST[$d37].", '".$_POST[$d5]."', ".$_POST[$d6].", '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d33]."', ".$_POST[$d38].", '".$_POST[$d12]."', '".$_POST[$d13]."', '".$_POST[$d14]."', '".$_POST[$d15]."', '".$_POST[$d16]."', '".$_POST[$d17]."', '".$_POST[$d18]."', '".$_POST[$d19]."', '".$_POST[$d20]."', '".$_POST[$d21]."', '".$_POST[$d22]."', '".$_POST[$d23]."', '".$_POST[$d24]."', '".$_POST[$d25]."', '".$_POST[$d26]."', '".$_POST[$d27]."', '".$_POST[$d28]."', '".$_POST[$d29]."', '".$_POST[$d30]."', '".$_POST[$d31]."', '".$_POST[$d32]."', '".$_POST[$d34]."')");				
						break;
						case 67:
							$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellid"]);				
						break;
			}
			break;
	case 3:
			$table="priemnoeotdelenie";
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
			$par1="";
			if (isset($_GET["kart"])) {$par1=$_GET['kart'];} else { $par1=""; }
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select *, d.FIO as drFIO from ".$table." t, karta k, doctor d, klinichdiag kd where kd.id_klindiag=t.id_predv_klin_diag and d.id_doctor=t.id_doctor and t.id_karta=k.id_kart and t.id_karta=".$par1." order by ".$d0." desc");	
						while($data = @mysql_fetch_array($qr_result))
						{
							
							echo '<h1><i>Дата и время осмотра</i></h1><h1>'.$data[$d1].'</h1>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d120].'">
							<input type="hidden" name = "dellpriemotdel" value="'.$data[$d0].'">
							<input name="submit" class="button8" type="submit" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Жалобы при поступлении</dt> <dd>'.$data[$d2].'</dd>';
							echo '<dt>История настоящего заболевания</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Социально бытовые условия</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Профвредности</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>На пенсии</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Инвалидность</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Перенесенные болезни</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Аллергоанамнез</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>Фактор риска</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>Месячные</dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>Акушерский анализ</dt> <dd>'.$data[$d12].'</dd>';
							echo '<dt>Течение настоящей беременности</dt> <dd>'.$data[$d13].'</dd>';
							echo '<dt>Прием лекарственных средств во время беременности</dt> <dd>'.$data[$d14].'</dd>';
							echo '<dt>Молочные железы</dt> <dd>'.$data[$d15].'</dd>';
							echo '<dt>Общее состояние</dt> <dd>'.$data[$d16].'</dd>';
							echo '<dt>Температура</dt> <dd>'.$data[$d17].'</dd>';
							echo '<dt>Вес</dt> <dd>'.$data[$d18].'</dd>';
							echo '<dt>Рост</dt> <dd>'.$data[$d19].'</dd>';
							echo '<dt>Телосложение</dt> <dd>'.$data[$d20].'</dd>';
							echo '<dt>Положение больного</dt> <dd>'.$data[$d21].'</dd>';
							echo '<dt>Кожание покровы</dt> <dd>'.$data[$d22].'</dd>';
							echo '<dt>Ногти</dt> <dd>'.$data[$d23].'</dd>';
							echo '<dt>Мышцы</dt> <dd>'.$data[$d24].'</dd>';
							echo '<dt>Кости</dt> <dd>'.$data[$d25].'</dd>';
							echo '<dt>Суставы</dt> <dd>'.$data[$d26].'</dd>';
							echo '<dt>Лимфаузлы</dt> <dd>'.$data[$d27].'</dd>';
							echo '<dt>Органы чувств</dt> <dd>'.$data[$d28].'</dd>';
							echo '<dt>Носовое дыхание</dt> <dd>'.$data[$d29].'</dd>';
							echo '<dt>Форма грудной клетки</dt> <dd>'.$data[$d30].'</dd>';
							echo '<dt>Участие грудной клетки в процессе дыхания</dt> <dd>'.$data[$d31].'</dd>'; // таблицы
							echo '<dt>Участие мышц в акте дыхания</dt> <dd>'.$data[$d32].'</dd>'; // таблицы
							echo '<dt>Голосовое дрожание</dt> <dd>'.$data[$d33].'</dd>';
							echo '<dt>Перкурсия легких</dt> <dd>'.$data[$d34].'</dd>';
							echo '<dt>Опущение нижних границ</dt> <dd>'.$data[$d35].'</dd>';
							echo '<dt>Подвижность нижнего края легких</dt> <dd>'.$data[$d36].'</dd>';
							echo '<dt>Аускультативно в легких дыхание</dt> <dd>'.$data[$d37].'</dd>';
							echo '<dt>Хрипы</dt> <dd>'.$data[$d38].'</dd>';
							echo '<dt>Число дыханий в минуту</dt> <dd>'.$data[$d39].'</dd>';
							echo '<dt>Ритм (Тип дыхания)</dt> <dd>'.$data[$d40].'</dd>';
							echo '<dt>Осмотр области сердца</dt> <dd>'.$data[$d41].'</dd>';
							echo '<dt>Эпигастральная пульсация</dt> <dd>'.$data[$d42].'</dd>';
							echo '<dt>Видимые сердечные пульсации</dt> <dd>'.$data[$d43].'</dd>';
							echo '<dt>Цианоз</dt> <dd>'.$data[$d44].'</dd>';
							echo '<dt>Верхушечный толчек</dt> <dd>'.$data[$d45].'</dd>';
							echo '<dt>Границы сердца</dt> <dd>'.$data[$d46].'</dd>';
							echo '<dt>Сосудистый пучек</dt> <dd>'.$data[$d47].'</dd>';
							echo '<dt>Аускультация сердца на верхушке</dt> <dd>'.$data[$d48].'</dd>';
							echo '<dt>Первый тон</dt> <dd>'.$data[$d49].'</dd>';
							echo '<dt>Второй тон</dt> <dd>'.$data[$d50].'</dd>';
							echo '<dt>Систолический шум</dt> <dd>'.$data[$d51].'</dd>'; // таблицы
							echo '<dt>Диастолический шум</dt> <dd>'.$data[$d52].'</dd>'; // таблицы
							echo '<dt>На аорте</dt> <dd>'.$data[$d53].'</dd>';
							echo '<dt>На легочной артерии</dt> <dd>'.$data[$d54].'</dd>';
							echo '<dt>Систолический, диастолический шумы на аорте</dt> <dd>'.$data[$d55].'</dd>';
							echo '<dt>Систолический, диастолический шумы на легочной артерии</dt> <dd>'.$data[$d56].'</dd>';
							echo '<dt>На трехстворчатом клапане</dt> <dd>'.$data[$d57].'</dd>';
							echo '<dt>На левом краю грудины</dt> <dd>'.$data[$d58].'</dd>';
							echo '<dt>Артерии</dt> <dd>'.$data[$d59].'</dd>';
							echo '<dt>Вены</dt> <dd>'.$data[$d60].'</dd>';
							echo '<dt>Пульс</dt> <dd>'.$data[$d61].'</dd>';
							echo '<dt>Частота</dt> <dd>'.$data[$d62].'</dd>';
							echo '<dt>Дефицит пульса</dt> <dd>'.$data[$d63].'</dd>';
							echo '<dt>Артериальное давление</dt> <dd>'.$data[$d64].'</dd>';
							echo '<dt>Слизистая полость рта</dt> <dd>'.$data[$d65].'</dd>';
							echo '<dt>Зубы</dt> <dd>'.$data[$d66].'</dd>';
							echo '<dt>Язык</dt> <dd>'.$data[$d67].'</dd>';
							echo '<dt>Зев</dt> <dd>'.$data[$d68].'</dd>';
							echo '<dt>Миндалины</dt> <dd>'.$data[$d69].'</dd>';
							echo '<dt>Живот</dt> <dd>'.$data[$d70].'</dd>';
							echo '<dt>Симптомы раздражения брюшины</dt> <dd>'.$data[$d71].'</dd>'; // таблицы
							echo '<dt>Печень</dt> <dd>'.$data[$d72].'</dd>'; // таблицы
							echo '<dt>При пальпации края</dt> <dd>'.$data[$d73].'</dd>';
							echo '<dt>Желчный пузырь</dt> <dd>'.$data[$d74].'</dd>';
							echo '<dt>Поджелудочная железа</dt> <dd>'.$data[$d75].'</dd>';
							echo '<dt>Селезенка</dt> <dd>'.$data[$d76].'</dd>';
							echo '<dt>Стул со слов</dt> <dd>'.$data[$d77].'</dd>';
							echo '<dt>Мочепускание</dt> <dd>'.$data[$d78].'</dd>';
							echo '<dt>Симптомы покалачивания по 12 ребру</dt> <dd>'.$data[$d79].'</dd>';
							echo '<dt>Полпация почек</dt> <dd>'.$data[$d80].'</dd>';
							echo '<dt>Мочевой пузырь</dt> <dd>'.$data[$d81].'</dd>'; // таблицы
							echo '<dt>Половые органы</dt> <dd>'.$data[$d82].'</dd>'; // таблицы
							echo '<dt>Щитовидная железа</dt> <dd>'.$data[$d83].'</dd>';
							echo '<dt>Глазные симптомы</dt> <dd>'.$data[$d84].'</dd>';
							echo '<dt>Сознание</dt> <dd>'.$data[$d85].'</dd>';
							echo '<dt>Неврологический статус</dt> <dd>'.$data[$d86].'</dd>';
							echo '<dt>Глазные щели</dt> <dd>'.$data[$d87].'</dd>';
							echo '<dt>Зрачки</dt> <dd>'.$data[$d88].'</dd>';
							echo '<dt>Давление глазных яблок</dt> <dd>'.$data[$d89].'</dd>';
							echo '<dt>Лицо</dt> <dd>'.$data[$d90].'</dd>';
							echo '<dt>Головокружение</dt> <dd>'.$data[$d91].'</dd>'; // таблицы
							echo '<dt>Глотание</dt> <dd>'.$data[$d92].'</dd>'; // таблицы
							echo '<dt>Язык при всасывании</dt> <dd>'.$data[$d93].'</dd>';
							echo '<dt>Поверхностный и глубокий рефлекс</dt> <dd>'.$data[$d94].'</dd>';						
							echo '<dt>Мышечный тонус</dt> <dd>'.$data[$d95].'</dd>';
							echo '<dt>Чувствительность</dt> <dd>'.$data[$d96].'</dd>';
							echo '<dt>Синдром паркенсионизма</dt> <dd>'.$data[$d97].'</dd>';
							echo '<dt>Функции тазовых органов</dt> <dd>'.$data[$d98].'</dd>';
							echo '<dt>Менингеальные симптомы</dt> <dd>'.$data[$d99].'</dd>';
							echo '<dt>Симптом Кернинга</dt> <dd>'.$data[$d100].'</dd>';
							echo '<dt>Память</dt> <dd>'.$data[$d101].'</dd>'; // таблицы
							echo '<dt>Интелект</dt> <dd>'.$data[$d102].'</dd>'; // таблицы
							echo '<dt>Мнительность</dt> <dd>'.$data[$d103].'</dd>';
							echo '<dt>Внушаемость</dt> <dd>'.$data[$d104].'</dd>';
							echo '<dt>Речь</dt> <dd>'.$data[$d105].'</dd>';
							echo '<dt>Походка</dt> <dd>'.$data[$d106].'</dd>';
							echo '<dt>Патологические рефлексы</dt> <dd>'.$data[$d107].'</dd>';
							echo '<dt>Дермографизм</dt> <dd>'.$data[$d108].'</dd>';
							echo '<dt>В позе Ромберга</dt> <dd>'.$data[$d109].'</dd>';
							echo '<dt>Статус локалис</dt> <dd>'.$data[$d110].'</dd>';
							echo '<dt>Данные амбулаторного исследования</dt> <dd>'.$data[$d111].'</dd>'; // таблицы
							echo '<dt>Дата</dt> <dd>'.$data[$d112].'</dd>'; // таблицы
							echo '<dt>Время</dt> <dd>'.$data[$d113].'</dd>';
							echo '<dt>Ф.И.О. врача</dt> <dd>'.$data["drFIO"].'</dd>';
							echo '<dt>План обследования</dt> <dd>'.$data[$d115].'</dd>';
							echo '<dt>Предворительный клинический диагноз</dt> <dd>'.$data["klinicheskdiag"].'</dd>'; // таблицы
							echo '<dt>Основное заболевание</dt> <dd>'.$data[$d117].'</dd>'; // таблицы
							echo '<dt>Осложнение основного заболевания</dt> <dd>'.$data[$d118].'</dd>';
							echo '<dt>Сопутствующие заболевания</dt> <dd>'.$data[$d119].'</dd>';
							echo '</dl>';
						}
						break;
				case 3:
						if (!isset($_POST[$d1]) || $_POST[$d1]=="") {$_POST[$d1]=date("Y-m-d H:i");}
						if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]="Нет данных";}
						if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
						if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
						if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
						if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
						if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
						if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
						if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
						if (!isset($_POST[$d10]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
						if (!isset($_POST[$d11]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
						if (!isset($_POST[$d12]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
						if (!isset($_POST[$d13]) || $_POST[$d14]=="") {$_POST[$d14]="Нет данных";}
						if (!isset($_POST[$d14]) || $_POST[$d15]=="") {$_POST[$d15]="Нет данных";}
						if (!isset($_POST[$d15]) || $_POST[$d16]=="") {$_POST[$d16]="Нет данных";}
						if (!isset($_POST[$d16]) || $_POST[$d17]=="") {$_POST[$d17]="Нет данных";}
						if (!isset($_POST[$d17]) || $_POST[$d18]=="") {$_POST[$d18]="Нет данных";}					
						if (!isset($_POST[$d18]) || $_POST[$d18]=="") {$_POST[$d18]="Нет данных";}
						if (!isset($_POST[$d19]) || $_POST[$d19]=="") {$_POST[$d19]="Нет данных";}
						if (!isset($_POST[$d20]) || $_POST[$d20]=="") {$_POST[$d20]="Нет данных";}
						if (!isset($_POST[$d21]) || $_POST[$d21]=="") {$_POST[$d21]="Нет данных";}
						if (!isset($_POST[$d22]) || $_POST[$d22]=="") {$_POST[$d22]="Нет данных";}
						if (!isset($_POST[$d23]) || $_POST[$d23]=="") {$_POST[$d23]="Нет данных";}
						if (!isset($_POST[$d24]) || $_POST[$d24]=="") {$_POST[$d24]="Нет данных";}
						if (!isset($_POST[$d25]) || $_POST[$d25]=="") {$_POST[$d25]="Нет данных";}
						if (!isset($_POST[$d26]) || $_POST[$d26]=="") {$_POST[$d26]="Нет данных";}
						if (!isset($_POST[$d27]) || $_POST[$d27]=="") {$_POST[$d27]="Нет данных";}
						if (!isset($_POST[$d28]) || $_POST[$d28]=="") {$_POST[$d28]="Нет данных";}
						if (!isset($_POST[$d29]) || $_POST[$d29]=="") {$_POST[$d29]="Нет данных";}
						if (!isset($_POST[$d30]) || $_POST[$d30]=="") {$_POST[$d30]="Нет данных";}
						if (!isset($_POST[$d31]) || $_POST[$d31]=="") {$_POST[$d31]="Нет данных";}
						if (!isset($_POST[$d32]) || $_POST[$d32]=="") {$_POST[$d32]="Нет данных";}
						if (!isset($_POST[$d33]) || $_POST[$d33]=="") {$_POST[$d33]="Нет данных";}						
						if (!isset($_POST[$d34]) || $_POST[$d34]=="") {$_POST[$d34]="Нет данных";}
						if (!isset($_POST[$d35]) || $_POST[$d35]=="") {$_POST[$d35]="Нет данных";}
						if (!isset($_POST[$d36]) || $_POST[$d36]=="") {$_POST[$d36]="Нет данных";}
						if (!isset($_POST[$d37]) || $_POST[$d37]=="") {$_POST[$d37]="Нет данных";}
						if (!isset($_POST[$d38]) || $_POST[$d38]=="") {$_POST[$d38]="Нет данных";}
						if (!isset($_POST[$d39]) || $_POST[$d39]=="") {$_POST[$d39]=0;}
						if (!isset($_POST[$d40]) || $_POST[$d40]=="") {$_POST[$d40]="Нет данных";}
						if (!isset($_POST[$d41]) || $_POST[$d41]=="") {$_POST[$d41]="Нет данных";}
						if (!isset($_POST[$d42]) || $_POST[$d42]=="") {$_POST[$d42]="Нет данных";}
						if (!isset($_POST[$d43]) || $_POST[$d43]=="") {$_POST[$d43]="Нет данных";}
						if (!isset($_POST[$d44]) || $_POST[$d44]=="") {$_POST[$d44]="Нет данных";}
						if (!isset($_POST[$d45]) || $_POST[$d45]=="") {$_POST[$d45]="Нет данных";}
						if (!isset($_POST[$d46]) || $_POST[$d46]=="") {$_POST[$d46]="Нет данных";}
						if (!isset($_POST[$d47]) || $_POST[$d47]=="") {$_POST[$d47]="Нет данных";}
						if (!isset($_POST[$d48]) || $_POST[$d48]=="") {$_POST[$d48]="Нет данных";}
						if (!isset($_POST[$d49]) || $_POST[$d49]=="") {$_POST[$d49]="Нет данных";}					
						if (!isset($_POST[$d50]) || $_POST[$d50]=="") {$_POST[$d50]="Нет данных";}
						if (!isset($_POST[$d51]) || $_POST[$d51]=="") {$_POST[$d41]="Нет данных";}
						if (!isset($_POST[$d52]) || $_POST[$d52]=="") {$_POST[$d52]="Нет данных";}
						if (!isset($_POST[$d53]) || $_POST[$d53]=="") {$_POST[$d53]="Нет данных";}
						if (!isset($_POST[$d54]) || $_POST[$d54]=="") {$_POST[$d54]="Нет данных";}
						if (!isset($_POST[$d55]) || $_POST[$d55]=="") {$_POST[$d55]="Нет данных";}
						if (!isset($_POST[$d56]) || $_POST[$d56]=="") {$_POST[$d56]="Нет данных";}
						if (!isset($_POST[$d57]) || $_POST[$d57]=="") {$_POST[$d57]="Нет данных";}
						if (!isset($_POST[$d58]) || $_POST[$d58]=="") {$_POST[$d58]="Нет данных";}
						if (!isset($_POST[$d59]) || $_POST[$d59]=="") {$_POST[$d59]="Нет данных";}
						if (!isset($_POST[$d60]) || $_POST[$d60]=="") {$_POST[$d60]="Нет данных";}
						if (!isset($_POST[$d61]) || $_POST[$d61]=="") {$_POST[$d61]="Нет данных";}
						if (!isset($_POST[$d62]) || $_POST[$d62]=="") {$_POST[$d62]="Нет данных";}
						if (!isset($_POST[$d63]) || $_POST[$d63]=="") {$_POST[$d63]="Нет данных";}
						if (!isset($_POST[$d64]) || $_POST[$d64]=="") {$_POST[$d64]="Нет данных";}
						if (!isset($_POST[$d65]) || $_POST[$d65]=="") {$_POST[$d65]="Нет данных";}	
						if (!isset($_POST[$d66]) || $_POST[$d66]=="") {$_POST[$d66]="Нет данных";}
						if (!isset($_POST[$d67]) || $_POST[$d67]=="") {$_POST[$d67]="Нет данных";}
						if (!isset($_POST[$d68]) || $_POST[$d68]=="") {$_POST[$d68]="Нет данных";}
						if (!isset($_POST[$d69]) || $_POST[$d69]=="") {$_POST[$d69]="Нет данных";}					
						if (!isset($_POST[$d70]) || $_POST[$d70]=="") {$_POST[$d70]="Нет данных";}
						if (!isset($_POST[$d71]) || $_POST[$d71]=="") {$_POST[$d71]="Нет данных";}
						if (!isset($_POST[$d72]) || $_POST[$d72]=="") {$_POST[$d72]="Нет данных";}
						if (!isset($_POST[$d73]) || $_POST[$d73]=="") {$_POST[$d73]="Нет данных";}
						if (!isset($_POST[$d74]) || $_POST[$d74]=="") {$_POST[$d74]="Нет данных";}
						if (!isset($_POST[$d75]) || $_POST[$d75]=="") {$_POST[$d75]="Нет данных";}
						if (!isset($_POST[$d76]) || $_POST[$d76]=="") {$_POST[$d76]="Нет данных";}
						if (!isset($_POST[$d77]) || $_POST[$d77]=="") {$_POST[$d77]="Нет данных";}
						if (!isset($_POST[$d78]) || $_POST[$d78]=="") {$_POST[$d78]="Нет данных";}
						if (!isset($_POST[$d79]) || $_POST[$d79]=="") {$_POST[$d79]="Нет данных";}
						if (!isset($_POST[$d80]) || $_POST[$d80]=="") {$_POST[$d80]="Нет данных";}
						if (!isset($_POST[$d81]) || $_POST[$d81]=="") {$_POST[$d81]="Нет данных";}
						if (!isset($_POST[$d82]) || $_POST[$d82]=="") {$_POST[$d82]="Нет данных";}
						if (!isset($_POST[$d83]) || $_POST[$d83]=="") {$_POST[$d83]="Нет данных";}
						if (!isset($_POST[$d84]) || $_POST[$d84]=="") {$_POST[$d84]="Нет данных";}
						if (!isset($_POST[$d85]) || $_POST[$d85]=="") {$_POST[$d85]="Нет данных";}						
						if (!isset($_POST[$d86]) || $_POST[$d86]=="") {$_POST[$d86]="Нет данных";}
						if (!isset($_POST[$d87]) || $_POST[$d87]=="") {$_POST[$d87]="Нет данных";}
						if (!isset($_POST[$d88]) || $_POST[$d88]=="") {$_POST[$d88]="Нет данных";}
						if (!isset($_POST[$d89]) || $_POST[$d89]=="") {$_POST[$d89]="Нет данных";}
						if (!isset($_POST[$d90]) || $_POST[$d90]=="") {$_POST[$d90]="Нет данных";}
						if (!isset($_POST[$d91]) || $_POST[$d91]=="") {$_POST[$d91]="Нет данных";}
						if (!isset($_POST[$d92]) || $_POST[$d92]=="") {$_POST[$d92]="Нет данных";}
						if (!isset($_POST[$d93]) || $_POST[$d93]=="") {$_POST[$d93]="Нет данных";}
						if (!isset($_POST[$d94]) || $_POST[$d94]=="") {$_POST[$d94]="Нет данных";}
						if (!isset($_POST[$d95]) || $_POST[$d95]=="") {$_POST[$d95]="Нет данных";}	
						if (!isset($_POST[$d96]) || $_POST[$d96]=="") {$_POST[$d96]="Нет данных";}
						if (!isset($_POST[$d97]) || $_POST[$d97]=="") {$_POST[$d97]="Нет данных";}
						if (!isset($_POST[$d98]) || $_POST[$d98]=="") {$_POST[$d98]="Нет данных";}
						if (!isset($_POST[$d99]) || $_POST[$d99]=="") {$_POST[$d99]="Нет данных";}					
						if (!isset($_POST[$d100]) || $_POST[$d100]=="") {$_POST[$d100]="Нет данных";}
						if (!isset($_POST[$d101]) || $_POST[$d101]=="") {$_POST[$d101]="Нет данных";}
						if (!isset($_POST[$d102]) || $_POST[$d102]=="") {$_POST[$d102]="Нет данных";}
						if (!isset($_POST[$d103]) || $_POST[$d103]=="") {$_POST[$d103]="Нет данных";}
						if (!isset($_POST[$d104]) || $_POST[$d104]=="") {$_POST[$d104]="Нет данных";}
						if (!isset($_POST[$d105]) || $_POST[$d105]=="") {$_POST[$d105]="Нет данных";}
						if (!isset($_POST[$d106]) || $_POST[$d106]=="") {$_POST[$d106]="Нет данных";}
						if (!isset($_POST[$d107]) || $_POST[$d107]=="") {$_POST[$d107]="Нет данных";}
						if (!isset($_POST[$d108]) || $_POST[$d108]=="") {$_POST[$d108]="Нет данных";}
						if (!isset($_POST[$d109]) || $_POST[$d109]=="") {$_POST[$d109]="Нет данных";}
						if (!isset($_POST[$d110]) || $_POST[$d110]=="") {$_POST[$d110]="Нет данных";}
						if (!isset($_POST[$d111]) || $_POST[$d111]=="") {$_POST[$d111]="Нет данных";}
						if (!isset($_POST[$d112]) || $_POST[$d112]=="") {$_POST[$d112]=date("Y-m-d");}
						if (!isset($_POST[$d113]) || $_POST[$d113]=="") {$_POST[$d113]=date("H:i");}
						if (!isset($_POST[$d114]) || $_POST[$d114]=="") {$_POST[$d114]=4;}
						if (!isset($_POST[$d115]) || $_POST[$d115]=="") {$_POST[$d115]="Нет данных";}
						if (!isset($_POST[$d116]) || $_POST[$d116]=="") {$_POST[$d116]=23;}
						if (!isset($_POST[$d117]) || $_POST[$d117]=="") {$_POST[$d117]="Нет данных";}
						if (!isset($_POST[$d118]) || $_POST[$d118]=="") {$_POST[$d118]="Нет данных";}
						if (!isset($_POST[$d119]) || $_POST[$d119]=="") {$_POST[$d119]="Нет данных";}
						/*
							if (!isset($_POST[$d25]) || $_POST[$d25]=="") {$_POST[$d25]=date("Y-m-d H:i");}
						*/
						$qr_result = mysql_query("insert into ".$table."(".$d120.", ".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d11.", ".$d12.", ".$d13.", ".$d14.", ".$d15.", ".$d16.", ".$d17.", ".$d18.", ".$d19.", ".$d20.", ".$d21.", ".$d22.", ".$d23.", ".$d24.", ".$d25.", ".$d26.", ".$d27.", ".$d28.", ".$d29.", ".$d30.", ".$d31.", ".$d32.", ".$d33.", ".$d34.", ".$d35.", ".$d36.", ".$d37.", ".$d38.", ".$d39.", ".$d40.", ".$d41.", ".$d42.", ".$d43.", ".$d44.", ".$d45.", ".$d46.", ".$d47.", ".$d48.", ".$d49.", ".$d50.", ".$d51.", ".$d52.", ".$d53.", ".$d54.", ".$d55.", ".$d56.", ".$d57.", ".$d58.", ".$d59.", ".$d60.", ".$d61.", ".$d62.", ".$d63.", ".$d64.", ".$d65.", ".$d66.", ".$d67.", ".$d68.", ".$d69.", ".$d70.", ".$d71.", ".$d72.", ".$d73.", ".$d74.", ".$d75.", ".$d76.", ".$d77.", ".$d78.", ".$d79.", ".$d80.", ".$d81.", ".$d82.", ".$d83.", ".$d84.", ".$d85.", ".$d86.", ".$d87.", ".$d88.", ".$d89.", ".$d90.", ".$d91.", ".$d92.", ".$d93.", ".$d94.", ".$d95.", ".$d96.", ".$d97.", ".$d98.", ".$d99.", ".$d100.", ".$d101.", ".$d102.", ".$d103.", ".$d104.", ".$d105.", ".$d106.", ".$d107.", ".$d108.", ".$d109.", ".$d110.", ".$d111.", ".$d112.", ".$d113.", ".$d114.", ".$d115.", ".$d116.", ".$d117.", ".$d118.", ".$d119.") values(".$_POST[$d120].", '".$_POST[$d1]."', '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d11]."', '".$_POST[$d12]."', '".$_POST[$d13]."', '".$_POST[$d14]."', '".$_POST[$d15]."', '".$_POST[$d16]."', '".$_POST[$d17]."', '".$_POST[$d18]."', '".$_POST[$d19]."', '".$_POST[$d20]."', '".$_POST[$d21]."', '".$_POST[$d22]."', '".$_POST[$d23]."', '".$_POST[$d24]."', '".$_POST[$d25]."', '".$_POST[$d26]."', '".$_POST[$d27]."', '".$_POST[$d28]."', '".$_POST[$d29]."', '".$_POST[$d30]."', '".$_POST[$d31]."', '".$_POST[$d32]."', '".$_POST[$d33]."', '".$_POST[$d34]."', '".$_POST[$d35]."', '".$_POST[$d36]."', '".$_POST[$d37]."', '".$_POST[$d38]."', ".$_POST[$d39].", '".$_POST[$d40]."', '".$_POST[$d41]."', '".$_POST[$d42]."', '".$_POST[$d43]."', '".$_POST[$d44]."', '".$_POST[$d45]."', '".$_POST[$d46]."', '".$_POST[$d47]."', '".$_POST[$d48]."', '".$_POST[$d49]."', '".$_POST[$d50]."', '".$_POST[$d51]."', '".$_POST[$d52]."', '".$_POST[$d53]."', '".$_POST[$d54]."', '".$_POST[$d55]."', '".$_POST[$d56]."', '".$_POST[$d57]."', '".$_POST[$d58]."', '".$_POST[$d59]."', '".$_POST[$d60]."', '".$_POST[$d61]."', '".$_POST[$d62]."', '".$_POST[$d63]."', '".$_POST[$d64]."', '".$_POST[$d65]."', '".$_POST[$d66]."', '".$_POST[$d67]."', '".$_POST[$d68]."', '".$_POST[$d69]."', '".$_POST[$d70]."', '".$_POST[$d71]."', '".$_POST[$d72]."', '".$_POST[$d73]."', '".$_POST[$d74]."', '".$_POST[$d75]."', '".$_POST[$d76]."', '".$_POST[$d77]."', '".$_POST[$d78]."', '".$_POST[$d79]."', '".$_POST[$d80]."', '".$_POST[$d81]."', '".$_POST[$d82]."', '".$_POST[$d83]."', '".$_POST[$d84]."', '".$_POST[$d85]."', '".$_POST[$d86]."', '".$_POST[$d87]."', '".$_POST[$d88]."', '".$_POST[$d89]."', '".$_POST[$d90]."', '".$_POST[$d91]."', '".$_POST[$d92]."', '".$_POST[$d93]."', '".$_POST[$d94]."', '".$_POST[$d95]."', '".$_POST[$d96]."', '".$_POST[$d97]."', '".$_POST[$d98]."', '".$_POST[$d99]."', '".$_POST[$d100]."', '".$_POST[$d101]."', '".$_POST[$d102]."', '".$_POST[$d103]."', '".$_POST[$d104]."', '".$_POST[$d105]."', '".$_POST[$d106]."', '".$_POST[$d107]."', '".$_POST[$d108]."', '".$_POST[$d109]."', '".$_POST[$d110]."', '".$_POST[$d111]."', '".$_POST[$d112]."', '".$_POST[$d113]."', ".$_POST[$d114].", '".$_POST[$d115]."', ".$_POST[$d116].", '".$_POST[$d117]."', '".$_POST[$d118]."', '".$_POST[$d119]."')");				
						break;
				case 67:
							$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellpriemotdel"]);				
						break;
			}
			break;
	case 4:
			$table="pol";
			$d0='id_pol';
			$d1='pol';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_pol">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'"'; 
						    if ($_GET["id_pol"]==$data[$d0]) echo 'selected';
							echo '>'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 5:
			$table="pobochka";
			$d0='id_pob';
			$d1='pobochniy';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_pod_dey">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'"';
							if ($_GET["id_pod_dey"]==$data[$d0]) echo 'selected';
							echo '>'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 6:
			$table="krov";
			$d0='id_gkr';
			$d1='krov';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_grkr">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'"';
							if ($_GET["id_grkr"]==$data[$d0]) echo 'selected';
							echo '>'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 7:
			$table="graghdanstvo";
			$d0='id_gr';
			$d1='graghd';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_grag">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'"';
							if ($_GET["id_grag"]==$data[$d0]) echo 'selected';
							echo '>'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 8:
			$table="kategory";
			$d0='id_kat';
			$d1='kategory';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_kateg">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'"';
							if ($_GET["id_kateg"]==$data[$d0]) echo 'selected';
							echo '>'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 9:
			$table="otdel";
			$d0='id_otdel';
			$d1='otdel';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_otdel">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 10:
			$table="perevodotdel";
			$d0='id_otdelperev';
			$d1='otdelperev';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_perevod">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 11:
			$table="doctor";
			$d0='id_doctor';
			$d1='FIO';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_doctor">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 12:
			$table="klinichdiag";
			$d0='id_klindiag';
			$d1='klinicheskdiag';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_predv_klin_diag">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBox" name="klindiag111" onchange="changeFunc();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 13: 
			$table="obsiyanalizkrovi";
			$d0='id_obsiyanalizkrovi';
			$d1='id_kart';
			$d2="otdate";
			$d3='eritrociti'; 
			$d4='gemoglobin';
			$d5='cp';
			$d6='leykociti';
			$d7='eozinofili';
			$d8='palochkoyadernie';
			$d9='segmentoyadernie';
			$d10='limfociti';
			$d11='monociti';
			$d12='soe';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from obsiyanalizkrovi o where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h3><i>от какого числа</i></h3><h3>'.$data[$d2].'</h3>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellobankrov" value="'.$data[$d0].'">
							<input name="submit" class="button8" type="submit" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Эритроциты</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Гемоглобин</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>ЦП</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Лейкоциты</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>эозинофилы</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>палочкоядерные</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>сегментоядерные</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>лимфоциты</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>моноциты </dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>СОЭ</dt> <dd>'.$data[$d12].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
						if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
						if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
						if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
						if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
						if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
						if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
						if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
						if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
						if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
						if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
						if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d11.", ".$d12.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d11]."', '".$_POST[$d12]."')");				
				break;
				case 67:
							$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellobankrov"]);				
						break;
			}
			break;
	case 14: 
			$table="timesvert";
			$d0='id_timesvert';
			$d1='id_kart';
			$d2="otdate";
			$d3='timesvert'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellkrowtime" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
							$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellkrowtime"]);				
						break;
			}
			break;
	case 15: 
			$table="reakciyavasermana";
			$d0='id_reakciyavasermana';
			$d1='id_kart';
			$d2="otdate";
			$d3='reakciyavasermana'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellwas" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
							$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellwas"]);				
						break;
			}
			break;
	case 16: 
			$table="markgepat";
			$d0='id_markgepat';
			$d1='id_kart';
			$d2="otdate";
			$d3='markgepat'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellmark" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellmark"]);				
				break;
			}
			break;
	case 17: 
			$table="spid";
			$d0='id_spid';
			$d1='id_kart';
			$d2="otdate";
			$d3='spid'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellspid" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellspid"]);				
				break;
			}
			break;
	case 18: 
			$table="obshiyanalizmochi";
			$d0='id_obshiyanalizmochi';
			$d1='id_kart';
			$d2="otdate";
			$d3='cvet'; 
			$d4='prozrachnost';
			$d5="udelniy_ves";
			$d6='reakciya_mochi';
			$d7='belok';
			$d8="glukoza";
			$d9='ploskiy_epiteliy';
			$d10='leykociti';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellmocha" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Цвет</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Прозрачность</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Удельный вес</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Реакция мочи</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Белок</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Глюкоза</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Плоский эпителий</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>Лейкоциты</dt> <dd>'.$data[$d10].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
					if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellmocha"]);				
				break;
			}
			break;
	case 19: 
			$table="poch_testi";
			$d0='id_poch_testi';
			$d1='id_kart';
			$d2="otdate";
			$d3='poch_testi'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellpoch_testi" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellpoch_testi"]);				
				break;
			}
			break;
	case 20: 
			$table="biohimkrov";
			$d0='id_biohimkrov';
			$d1='id_kart';
			$d2="otdate";
			$d3='glukoza'; 
			$d4='srb';
			$d5='seromukoid';
			$d6='sialovie_kisloti';
			$d7='mochevina';
			$d8='bilirubin';
			$d9='ast';
			$d10='alt';
			$d11='kreatinin';
			$d12='holestirin';
			$d13='lipoproteidi';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellissledkrov" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Глюкоза</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>СРБ</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>серомукоид</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>сиаловые кислоты</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>мочевина</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>билирубин</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>АСТ</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>АЛТ</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>Креатинин</dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>Холестерин</dt> <dd>'.$data[$d12].'</dd>';
							echo '<dt>бетта-липопротеиды</dt> <dd>'.$data[$d13].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
					if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
					if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
					if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
					if (!isset($_POST[$d13]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d11.", ".$d12.", ".$d13.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d11]."', '".$_POST[$d12]."', '".$_POST[$d13]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellissledkrov"]);				
				break;
			}
			break;
	case 21: 
			$table="analizprotrombin";
			$d0='id_analizprotrombin';
			$d1='id_kart';
			$d2="otdate";
			$d3='analizprotrombin'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellanalizprotrombin" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellanalizprotrombin"]);				
				break;
			}
			break;
	case 22: 
			$table="protrombindex";
			$d0='id_protrombindex';
			$d1='id_kart';
			$d2="otdate";
			$d3='protrombindex'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellprotrombindex111" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellprotrombindex111"]);				
				break;
			}
			break;		
	case 23: 
			$table="rengorggrudklet";
			$d0='id_rengorggrudklet';
			$d1='id_kart';
			$d2="otdate";
			$d3='v_legkih'; 
			$d4='korni';
			$d5='diafragma';
			$d6='serdce';
			$d7='index_mura';
			$d8='kard_index';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellrengorgg" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>В легких</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Корни</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Диафрагма и синусы</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Сердце</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Индекс Мура</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Кардиоторакальный индекс</dt> <dd>'.$data[$d8].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellrengorgg"]);				
				break;
			}
			break;
	case 24: 
			$table="ekg";
			$d0='id_EKG';
			$d1='id_kart';
			$d2="otdate";
			$d3='ritm'; 
			$d4='chss';
			$d5='electricosserdca';
			$d6='priznakigipertrofii';
			$d7='repolyarizaciya';
			$d8='zakluchenie';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellekg1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>от какого числа</dt> <dd>'.$data[$d2].'</dd>';
							echo '<dt>ритм</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>ЧСС</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Электрическая ось сердца</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Признаки гипертрофии</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Процессы реполяризации</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Заключение</dt> <dd>'.$data[$d8].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellekg1"]);				
				break;
			}
			break;
	case 25: 
			$table="ehokardiya";
			$d0='id_ehokardiya';
			$d1='id_kart';
			$d2="otdate";
			$d3='aorta'; 
			$d4='aortalklapan';
			$d5='diametrdugi';
			$d6='fk1';
			$d7='raskritie1';
			$d8='stepenregurgitacii1';
			
			$d9='graddavlensist';
			$d10='mitralniyklapan';
			$d11="fk2";
			$d12='raskritie2'; 
			$d13='ploshadotverstiya2';
			$d14='stepenregurgitacii2';
			$d15='graddavlendist2';
			$d16='trikuspidalniyklapan';
			$d17='fk3';
			$d18='raskritie3';
			$d19='ploshadotverstiya3';
			$d20="stepenregurgitacii3";
			$d21='graddavlendist3'; 
			$d22='legochnayaarteriya';
			$d23='diametrstvola';
			$d24='pravlegarter';
			$d25='levlegarter';
			$d26='klapanlegart';
			$d27='sredneelad';
			$d28='stolicheskoelad';
			$d29="stepenregurgitacii4";
			$d30='graddavlen4'; 
			$d31='levoepredserdie';
			$d32='razmeri';
			$d33='leviygheludochik';
			$d34='kdr';
			$d35='ksr';
			$d36='kdo';
			$d37='kso';
			$d38='uo';
			$d39='zslg';
			$d40='imm';
			$d41='pravoepredserdie';
			$d42='praviygheludochek';
			$d43='meghserdechnayaperegorodka';
			$d44='meggheludochkovayaperegorodka';
			$d45='perikard';
			$d46='diastolicheskayafunklevgheludka';
			$d47='zonigipoiakinezi';
			$d48='doposobennosti';
			$d49='zakluchenie';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "delleho1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Аорта</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Аортальный клапан</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Диаметр дуги</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>ФК</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Раскрытие</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Степень регургитации</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Градиент давления систолический</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>Митральныйклапан</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>ФК</dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>Раскрытие</dt> <dd>'.$data[$d12].'</dd>';
							echo '<dt>Площадь отверстия</dt> <dd>'.$data[$d13].'</dd>';
							echo '<dt>Степень регургитации</dt> <dd>'.$data[$d14].'</dd>';
							echo '<dt>Градиент давления диастолический</dt> <dd>'.$data[$d15].'</dd>';
							echo '<dt>Трикуспидальныйклапан</dt> <dd>'.$data[$d16].'</dd>';
							echo '<dt>ФК</dt> <dd>'.$data[$d17].'</dd>';
							echo '<dt>Раскрытие</dt> <dd>'.$data[$d18].'</dd>';
							echo '<dt>Площадь отверстия</dt> <dd>'.$data[$d19].'</dd>';
							echo '<dt>Степень регургитации</dt> <dd>'.$data[$d20].'</dd>';
							echo '<dt>Градиент давления диастолический</dt> <dd>'.$data[$d21].'</dd>';
							echo '<dt>Легочная артерия</dt> <dd>'.$data[$d22].'</dd>';
							echo '<dt>Диаметр ствола</dt> <dd>'.$data[$d23].'</dd>';
							echo '<dt>Правая легочная артерия</dt> <dd>'.$data[$d24].'</dd>';
							echo '<dt>Левая легочная артерия</dt> <dd>'.$data[$d25].'</dd>';
							echo '<dt>Клапан легочной артерии</dt> <dd>'.$data[$d26].'</dd>';
							echo '<dt>Среднее ЛАД</dt> <dd>'.$data[$d27].'</dd>';
							echo '<dt>Систолическое ЛАД</dt> <dd>'.$data[$d28].'</dd>';
							echo '<dt>Степень регургитации</dt> <dd>'.$data[$d29].'</dd>';
							echo '<dt>Градиент давления</dt> <dd>'.$data[$d30].'</dd>';
							echo '<dt>Левое предсердие</dt> <dd>'.$data[$d31].'</dd>';
							echo '<dt>Размеры</dt> <dd>'.$data[$d32].'</dd>';
							echo '<dt>Левый желудочек</dt> <dd>'.$data[$d33].'</dd>';
							echo '<dt>КДР</dt> <dd>'.$data[$d34].'</dd>';
							echo '<dt>КСР</dt> <dd>'.$data[$d35].'</dd>';
							echo '<dt>КДО</dt> <dd>'.$data[$d36].'</dd>';
							echo '<dt>КСО</dt> <dd>'.$data[$d37].'</dd>';
							echo '<dt>УО</dt> <dd>'.$data[$d38].'</dd>';
							echo '<dt>ЗСЛЖ</dt> <dd>'.$data[$d39].'</dd>';
							echo '<dt>ИММ</dt> <dd>'.$data[$d40].'</dd>';
							echo '<dt>Правое предсердие</dt> <dd>'.$data[$d41].'</dd>';
							echo '<dt>Правый желудочек</dt> <dd>'.$data[$d42].'</dd>';
							echo '<dt>Межпредсердная перегородка</dt> <dd>'.$data[$d43].'</dd>';
							echo '<dt>Межжелудочковая перегородка</dt> <dd>'.$data[$d44].'</dd>';
							echo '<dt>Перикард</dt> <dd>'.$data[$d45].'</dd>';
							echo '<dt>Диастолическая функция левого желудочка</dt> <dd>'.$data[$d46].'</dd>';
							echo '<dt>Зоны гипо- и акинезии</dt> <dd>'.$data[$d47].'</dd>';
							echo '<dt>Дополнительные особенности</dt> <dd>'.$data[$d48].'</dd>';
							echo '<dt>Заключение</dt> <dd>'.$data[$d49].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
					if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
					if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
					if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
					if (!isset($_POST[$d13]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
					if (!isset($_POST[$d14]) || $_POST[$d14]=="") {$_POST[$d14]="Нет данных";}
					if (!isset($_POST[$d15]) || $_POST[$d15]=="") {$_POST[$d15]="Нет данных";}
					if (!isset($_POST[$d16]) || $_POST[$d16]=="") {$_POST[$d16]="Нет данных";}
					if (!isset($_POST[$d17]) || $_POST[$d17]=="") {$_POST[$d17]="Нет данных";}
					if (!isset($_POST[$d18]) || $_POST[$d18]=="") {$_POST[$d18]="Нет данных";}
					if (!isset($_POST[$d19]) || $_POST[$d19]=="") {$_POST[$d19]="Нет данных";}
					if (!isset($_POST[$d20]) || $_POST[$d20]=="") {$_POST[$d20]="Нет данных";}
					if (!isset($_POST[$d21]) || $_POST[$d21]=="") {$_POST[$d21]="Нет данных";}
					if (!isset($_POST[$d22]) || $_POST[$d22]=="") {$_POST[$d22]="Нет данных";}
					if (!isset($_POST[$d23]) || $_POST[$d23]=="") {$_POST[$d23]="Нет данных";}
					if (!isset($_POST[$d24]) || $_POST[$d24]=="") {$_POST[$d24]="Нет данных";}
					if (!isset($_POST[$d25]) || $_POST[$d25]=="") {$_POST[$d25]="Нет данных";}
					if (!isset($_POST[$d26]) || $_POST[$d26]=="") {$_POST[$d26]="Нет данных";}
					if (!isset($_POST[$d27]) || $_POST[$d27]=="") {$_POST[$d27]="Нет данных";}
					if (!isset($_POST[$d28]) || $_POST[$d28]=="") {$_POST[$d28]="Нет данных";}
					if (!isset($_POST[$d29]) || $_POST[$d29]=="") {$_POST[$d29]="Нет данных";}
					if (!isset($_POST[$d30]) || $_POST[$d30]=="") {$_POST[$d30]="Нет данных";}
					if (!isset($_POST[$d31]) || $_POST[$d31]=="") {$_POST[$d31]="Нет данных";}
					if (!isset($_POST[$d32]) || $_POST[$d32]=="") {$_POST[$d32]="Нет данных";}
					if (!isset($_POST[$d33]) || $_POST[$d33]=="") {$_POST[$d33]="Нет данных";}
					if (!isset($_POST[$d34]) || $_POST[$d34]=="") {$_POST[$d34]="Нет данных";}
					if (!isset($_POST[$d35]) || $_POST[$d35]=="") {$_POST[$d35]="Нет данных";}
					if (!isset($_POST[$d36]) || $_POST[$d36]=="") {$_POST[$d36]="Нет данных";}
					if (!isset($_POST[$d37]) || $_POST[$d37]=="") {$_POST[$d37]="Нет данных";}
					if (!isset($_POST[$d38]) || $_POST[$d38]=="") {$_POST[$d38]="Нет данных";}
					if (!isset($_POST[$d39]) || $_POST[$d39]=="") {$_POST[$d39]="Нет данных";}
					if (!isset($_POST[$d40]) || $_POST[$d40]=="") {$_POST[$d40]="Нет данных";}
					if (!isset($_POST[$d41]) || $_POST[$d41]=="") {$_POST[$d41]="Нет данных";}
					if (!isset($_POST[$d42]) || $_POST[$d42]=="") {$_POST[$d42]="Нет данных";}
					if (!isset($_POST[$d43]) || $_POST[$d43]=="") {$_POST[$d43]="Нет данных";}
					if (!isset($_POST[$d44]) || $_POST[$d44]=="") {$_POST[$d44]="Нет данных";}
					if (!isset($_POST[$d45]) || $_POST[$d45]=="") {$_POST[$d45]="Нет данных";}
					if (!isset($_POST[$d46]) || $_POST[$d46]=="") {$_POST[$d46]="Нет данных";}
					if (!isset($_POST[$d47]) || $_POST[$d47]=="") {$_POST[$d47]="Нет данных";}
					if (!isset($_POST[$d48]) || $_POST[$d48]=="") {$_POST[$d48]="Нет данных";}
					if (!isset($_POST[$d49]) || $_POST[$d49]=="") {$_POST[$d49]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d11.", ".$d12.", ".$d13.", ".$d14.", ".$d15.", ".$d16.", ".$d17.", ".$d18.", ".$d19.", ".$d20.", ".$d21.", ".$d22.", ".$d23.", ".$d24.", ".$d25.", ".$d26.", ".$d27.", ".$d28.", ".$d29.", ".$d30.", ".$d31.", ".$d32.", ".$d33.", ".$d34.", ".$d35.", ".$d36.", ".$d37.", ".$d38.", ".$d39.", ".$d40.", ".$d41.", ".$d42.", ".$d43.", ".$d44.", ".$d45.", ".$d46.", ".$d47.", ".$d48.", ".$d49.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d11]."', '".$_POST[$d12]."', '".$_POST[$d13]."', '".$_POST[$d14]."', '".$_POST[$d15]."', '".$_POST[$d16]."', '".$_POST[$d17]."', '".$_POST[$d18]."', '".$_POST[$d19]."', '".$_POST[$d20]."', '".$_POST[$d21]."', '".$_POST[$d22]."', '".$_POST[$d23]."', '".$_POST[$d24]."', '".$_POST[$d25]."', '".$_POST[$d26]."', '".$_POST[$d27]."', '".$_POST[$d28]."', '".$_POST[$d29]."', '".$_POST[$d30]."', '".$_POST[$d31]."', '".$_POST[$d32]."', '".$_POST[$d33]."', '".$_POST[$d34]."', '".$_POST[$d35]."', '".$_POST[$d36]."', '".$_POST[$d37]."', '".$_POST[$d38]."', '".$_POST[$d39]."', '".$_POST[$d40]."', '".$_POST[$d41]."', '".$_POST[$d42]."', '".$_POST[$d43]."', '".$_POST[$d44]."', '".$_POST[$d45]."', '".$_POST[$d46]."', '".$_POST[$d47]."', '".$_POST[$d48]."', '".$_POST[$d49]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["delleho1"]);				
				break;
			}
			break;
	case 26: 
			$table="uzivnutrorg";
			$d0='id_uzivnutrorg';
			$d1='id_kart';
			$d2="otdate";
			$d3='pechen'; 
			$d4='ghelchniypuzir';
			$d5='poghelgheleza';
			$d6='selezenka';
			$d7='levayapochka';
			$d8='pravayapochka';
			$d9='zakluchenie';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "delluzi1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Печень</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Желчный пузырь</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Поджелудочная железа</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Селезенка</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>Левая почка</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Правая почка</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Заключение</dt> <dd>'.$data[$d9].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["delluzi1"]);				
				break;
			}
			break;
	case 27: 
			$table="okulist";
			$d0='id_okulist';
			$d1='id_kart';
			$d2="otdate";
			$d3='okulist'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellokul1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellokul1"]);				
				break;
			}
			break;
	case 28: 
			$table="stamotolog";
			$d0='id_stamotolog';
			$d1='id_kart';
			$d2="otdate";
			$d3='stamotolog'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellstam1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellstam1"]);				
				break;
			}
			break;			
	case 29: 
			$table="nevropat";
			$d0='id_nevropat';
			$d1='id_kart';
			$d2="otdate";
			$d3='nevropat'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellnewr1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellnewr1"]);				
				break;
			}
				break;
	case 30: 
			$table="lor";
			$d0='id_lor';
			$d1='id_kart';
			$d2="otdate";
			$d3='lor'; 
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<h2><i>от какого числа</i></h2><h2>'.$data[$d2].'</h2>';
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "delllor1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Запись</dt> <dd>'.$data[$d3].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]=date("Y-m-d");}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["delllor1"]);				
				break;
			}
			break;
			
	case 31: 
			$table="medikamentlech";
			$d0='id_medikamentlech';
			$d1='id_kart';
			$d2="naimprep";
			$d3='kolvodneyvair'; 
			$d4='vesbolnogo';
			$d5='extrubnasutki';
			$d6='anesteziologkarta';
			$d7='ik';
			$d8='gemotransfuzia';
			$d9='krovdooper';
			$d10='krovposleoper'; 
			$d11='anesteziologposobie';
			$d12='hirurglech';
			$d13='operacia';
			$d14='timenachala';
			$d15='datenachala';
			$d16='dispansernoenabl';
			$par1="";
			if (isset($_GET["kart"])) {  $par1=" id_kart=".$_GET['kart']." ";  }
			switch($y)
		    {	
				case 1:
					$qr_result = mysql_query("select * from ".$table." where  ".$par1." order by ".$d0." desc");				
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<form method="POST"  action="kart.php?kart='.$data[$d1].'">
							<input type="hidden" name = "dellmedlech1" value="'.$data[$d0].'">
							<input name="submit" type="submit" class="button8" value="Удалиь">
							</form>';
							echo '<dl class="holiday">';
							echo '<dt>Наименования препаратов</dt> <dd>'.$data[$d2].'</dd>';
							echo '<dt>Количество дней в АиР</dt> <dd>'.$data[$d3].'</dd>';
							echo '<dt>Вес больного</dt> <dd>'.$data[$d4].'</dd>';
							echo '<dt>Экстубация на какие сутки</dt> <dd>'.$data[$d5].'</dd>';
							echo '<dt>Анестезиологическая карта</dt> <dd>'.$data[$d6].'</dd>';
							echo '<dt>ИК</dt> <dd>'.$data[$d7].'</dd>';
							echo '<dt>Гемотрансфузия</dt> <dd>'.$data[$d8].'</dd>';
							echo '<dt>Кровь до операции</dt> <dd>'.$data[$d9].'</dd>';
							echo '<dt>Кровь после операции</dt> <dd>'.$data[$d10].'</dd>';
							echo '<dt>Анестезиологическое пособие</dt> <dd>'.$data[$d11].'</dd>';
							echo '<dt>Хирургическое лечение</dt> <dd>'.$data[$d12].'</dd>';
							echo '<dt>Операция</dt> <dd>'.$data[$d13].'</dd>';
							echo '<dt>Время начала</dt> <dd>'.$data[$d14].'</dd>';
							echo '<dt>Время окончания</dt> <dd>'.$data[$d15].'</dd>';
							echo '<dt>Диспансерное наблюдение</dt> <dd>'.$data[$d16].'</dd>';
							echo '</dl>';
						} 
				break;
				case 3:
					if (!isset($_POST[$d2]) || $_POST[$d2]=="") {$_POST[$d2]="Нет данных";}
					if (!isset($_POST[$d3]) || $_POST[$d3]=="") {$_POST[$d3]="Нет данных";}
					if (!isset($_POST[$d4]) || $_POST[$d4]=="") {$_POST[$d4]="Нет данных";}
					if (!isset($_POST[$d5]) || $_POST[$d5]=="") {$_POST[$d5]="Нет данных";}
					if (!isset($_POST[$d6]) || $_POST[$d6]=="") {$_POST[$d6]="Нет данных";}
					if (!isset($_POST[$d7]) || $_POST[$d7]=="") {$_POST[$d7]="Нет данных";}
					if (!isset($_POST[$d8]) || $_POST[$d8]=="") {$_POST[$d8]="Нет данных";}
					if (!isset($_POST[$d9]) || $_POST[$d9]=="") {$_POST[$d9]="Нет данных";}
					if (!isset($_POST[$d10]) || $_POST[$d10]=="") {$_POST[$d10]="Нет данных";}
					if (!isset($_POST[$d11]) || $_POST[$d11]=="") {$_POST[$d11]="Нет данных";}
					if (!isset($_POST[$d12]) || $_POST[$d12]=="") {$_POST[$d12]="Нет данных";}
					if (!isset($_POST[$d13]) || $_POST[$d13]=="") {$_POST[$d13]="Нет данных";}
					if (!isset($_POST[$d14]) || $_POST[$d14]=="") {$_POST[$d14]=date("H:i");}
					if (!isset($_POST[$d15]) || $_POST[$d15]=="") {$_POST[$d15]=date("Y-m-d");}
					if (!isset($_POST[$d16]) || $_POST[$d16]=="") {$_POST[$d16]="Нет данных";}
					$qr_result = mysql_query("insert into ".$table."(".$d1.", ".$d2.", ".$d3.", ".$d4.", ".$d5.", ".$d6.", ".$d7.", ".$d8.", ".$d9.", ".$d10.", ".$d11.", ".$d12.", ".$d13.", ".$d14.", ".$d15.", ".$d16.") values(".$_POST[$d1].", '".$_POST[$d2]."', '".$_POST[$d3]."', '".$_POST[$d4]."', '".$_POST[$d5]."', '".$_POST[$d6]."', '".$_POST[$d7]."', '".$_POST[$d8]."', '".$_POST[$d9]."', '".$_POST[$d10]."', '".$_POST[$d11]."', '".$_POST[$d12]."', '".$_POST[$d13]."', '".$_POST[$d14]."', '".$_POST[$d15]."', '".$_POST[$d16]."')");				
				break;
				case 67:
					$qr_result = mysql_query("delete from ".$table." where ".$d0."=".$_POST["dellmedlech1"]);				
				break;
			}
			break;
	case 32:
			$table="stacionar";
			$d0='id_stac';
			$d1='stacionar';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="ishodlechvstoc">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 33:
			$table="provereno";
			$d0='id_prov';
			$d1='prover';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBox222" name="prover1" onchange="changeFuncprover();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 34:
			$table="ghalobipripostuplenii";
			$d0='id_ghalob';
			$d1='ghaloba';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxghaloba" name="prover1" onchange="changeFuncghaloba();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 35:
			$table="istoriyazabol";
			$d0='id_ist';
			$d1='istzab';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxistzab" name="istzab1" onchange="changeFuncistzab();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 36:
			$table="socbitusloviya";
			$d0='id_socbitusloviya';
			$d1='socbitusloviya';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="socbitusloviya">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 37:
			$table="invalidnost";
			$d0='id_invalidnost';
			$d1='invalidnost';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="invalidnost">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 38:
			$table="perenesennie_bolezni";
			$d0='id_perenesennie_bolezni';
			$d1='perenesennie_bolezni';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxperenesennie_bolezni" name="perenesennie_bolezni11" onchange="changeFuncperenesennie_bolezni();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 39:
			$table="alerganamnez";
			$d0='id_alerganamnez';
			$d1='alerganamnez';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxalerganamnez" name="alerganamnez1" onchange="changeFuncalerganamnez();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 40:
			$table="faktoririska";
			$d0='id_faktoririska';
			$d1='faktoririska';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxfaktoririska" name="faktoririskaz1" onchange="changeFuncfaktoririska();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 41:
			$table="priemlekprepvovremyaberemennosti";
			$d0='id_priemlekprepvovremyaberemennosti';
			$d1='priemlekprepvovremyaberemennosti';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxpriemlekprepvovremyaberemennosti" name="priemlekprepvovremyaberemennostiz1" onchange="changeFuncpriemlekprepvovremyaberemennosti();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	case 42:
			$table="koghaniepokrovi";
			$d0='id_koghaniepokrovi';
			$d1='koghaniepokrovi';
			switch($y)
		    {	
				case 1:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="koghaniepokrovi">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
			}
			break;
	case 43:
			$table="planobs";
			$d0='id_planobs';
			$d1='planobs';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" id="selectBoxplan" name="prover1" onchange="changeFuncplan();">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d1].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
		case 44:
			$table="klinichdiag";
			$d0='id_klindiag';
			$d1='klinicheskdiag';
			switch($y)
		    {	
				case 2:
						$qr_result = mysql_query("select * from ".$table);	
						echo '<p><select size="3" name="id_predv_klin_diag">';
						while($data = @mysql_fetch_array($qr_result))
						{
							echo '<option value="'.$data[$d0].'">'.$data[$d1].'</option>';
						}
						echo '</select></p>';
					break;
			}
			break;
	}
}


?>