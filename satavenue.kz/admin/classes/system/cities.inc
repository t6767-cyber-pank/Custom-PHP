<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление городами");
	$CityExist=false;

	$Direction="ASC";
	$PageDirection="ASC";

	$SortField="Position";
	$SortMethod="Position {$Direction}";

	if(isset($_GET["Sort"])){
		$Direction=(isset($_GET["Direction"]) && $_GET["Direction"]=="DESC")?"DESC":"ASC";
		$PageDirection=$Direction;
		$Sort=$_GET["Sort"];

		$SortField=$Sort;

		$SortMethod="{$Sort} {$Direction}";

		$Direction=$Direction=="DESC"?"ASC":"DESC";
	}

	$AllCountries=$DataBase->Query("SELECT * FROM {$GLOBAL->Countries} ORDER BY Title;");
	$AllRegions=$DataBase->Query("SELECT * FROM {$GLOBAL->Regions} ORDER BY Title;");
	foreach($AllRegions as $Region){
		$AllCountries[$Region->Country]->Regions[$Region->Id]=$Region;
	}

	$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";

	$Filters=array();
	$FiltersURL=array();

	if(isset($_GET["Filter"])){
		$Id=(int) @$_GET["Id"];
		if(!empty($Id)){
			$Filters[]="`Id`={$Id}";
			$FiltersURL[]="Id={$Id}";
		}
		$URL=$Kernel->PostToSQL(@$_GET["URL"]);
		if(!empty($URL)){
			$Filters[]="`URL` LIKE '%{$URL}%'";
			$FiltersURL[]="URL={$URL}";
		}
		$Title=$Kernel->PostToSQL(@$_GET["Title"]);
		if(!empty($Title)){
			$Filters[]="`Title` LIKE '%{$Title}%'";
			$FiltersURL[]="Title={$Title}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$CityList=$DataBase->Query("SELECT * FROM {$GLOBAL->Cities}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($CityInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Cities} WHERE Id={$Id};",false)){
			$CityExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $CityExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->Cities};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$URL=$Kernel->PostToURL(@$_POST["URL"],true,2);
		$SQL[]="`URL`='{$URL}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$TitleIn=$Kernel->PostToSQL(@$_POST["TitleIn"]);
		$SQL[]="`TitleIn`='{$TitleIn}'";

		$Region=(int) @$_POST["Region"];
		$SQL[]="`Region`={$Region}";

		$Country=(int) @$AllRegions[$Region]->Country;
		$SQL[]="`Country`={$Country}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Cities} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $CityExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$URL=$Kernel->PostToURL(@$_POST["URL"],true,2);
		$SQL[]="`URL`='{$URL}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$TitleIn=$Kernel->PostToSQL(@$_POST["TitleIn"]);
		$SQL[]="`TitleIn`='{$TitleIn}'";

		$Region=(int) @$_POST["Region"];
		$SQL[]="`Region`={$Region}";

		$Country=(int) @$AllRegions[$Region]->Country;
		$SQL[]="`Country`={$Country}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Cities} SET {$SQL} WHERE Id={$CityInfo->Id};");

		header("Location: ?EditForm&Id={$CityInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($CityList as $City){
			if(isset($_POST["Chk{$City->Id}"]) && $_POST["Chk{$City->Id}"]==$City->Id){
				$DelIds[$City->Id]=$City->Id;
			}

			unset($City);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Cities} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}elseif(isset($_GET["ChangePosition"])){
		$Positions=explode(",",$Positions);
		foreach($Positions as $Position){
			$Position=explode("-",$Position);
			$ItemId=$Position[0];
			$Position=$Position[1];
			$ItemId=substr($ItemId,3);
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	$CityCount=$DataBase->Query("SELECT COUNT(*) AS Cities FROM {$GLOBAL->Cities};",false);
	$CityCount=(int) @$CityCount->Cities;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(40/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные города" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего городов: <strong><?=$CityCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="70%" />
					<col width="10%" />
					<col width="10%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Title&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="URL"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=URL&Direction=<?=$Direction?><?=$FiltersURL?>">URL<?=$SortField=="URL"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Country&Direction=<?=$Direction?><?=$FiltersURL?>">Страна<?=$SortField=="Country"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Region&Direction=<?=$Direction?><?=$FiltersURL?>">Регион<?=$SortField=="Region"?$DirectionArrow:""?></a></th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th><?=$HtmlTags->InputText("URL",@$_GET["URL"],"","text","URL","text")?></th>
						<th>
							<select name="Country">
								<?=$HtmlTags->option(0,"Все")?>
<?php
		foreach($AllCountries as $Country){
			echo $HtmlTags->option($Country->Id,$Country->Title,$Country->Id==@$_GET["Country"]);
		}
?>
							</select>
						</th>
						<th>
							<select name="Region">
								<?=$HtmlTags->option(0,"Все")?>
<?php
		foreach($AllRegions as $Region){
			echo $HtmlTags->option($Region->Id,$Region->Title,$Region->Id==@$_GET["Region"]);
		}
?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($CityList as $City){
?>
					<tr fid="<?=$City->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$City->Id}",$City->Id,"",false,"","Chk{$City->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$City->Id?>"><?=$City->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$City->Id?>"><?=$City->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$City->Id?>"><?=$City->URL?></a></td>
						<td><a href="?EditForm&Id=<?=$City->Id?>"><?=@$AllCountries[$City->Country]->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$City->Id?>"><?=@$AllRegions[$City->Region]->Title?></a></td>
					</tr>
<?php
			unset($City);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="<?=3+count($AllLanguages)?>"></td>
					</tr>
				</tfoot>
			</table>
<script type="text/javascript">
	$(document).ready(function() {
		function SavePositions(InputPositions){
			$.ajax({
				type: "POST",
				cache: false,
				data: 'Positions='+InputPositions,
				url: '?ChangePosition',
				success:function(){
					$("#Positions tbody tr").removeClass('line');
					$("#Positions tbody tr:even").addClass('line');
				}
			});
		}

		$("#Positions").tableDnD({
			onDragClass: "current",
			onDrop: function(table, row){
				for(var j=0;j<table.tBodies.length;j++){
					var rows = table.tBodies[j].rows;

					var ItemsPositions=[];
					for(var i=0;i<rows.length;i++) {
						ItemsPositions[ItemsPositions.length]=rows[i].id+"-"+i;
					}
					SavePositions(ItemsPositions);
				}
			}
		});
	});
</script>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$CityInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> города:</h1>
			<div class="buttons">
				<a href="?Id=<?=$ParentId?>" class="back">Вернуться</a>
<?php
		if($IsEdit){
?>
				<a href="?AddForm" class="button add">Добавить новый</a>
<?php
		}
?>
				<button class="button add"><?=$IsEdit?"Применить":"Добавить"?></button>
			</div>
		</div>
		<div class="container">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" class="tl tr">Информация</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Каноническое название:</th>
						<td><?=$HtmlTags->InputText("URL",$IsEdit?$CityInfo->URL:"","","text","URL","text",0,$IsEdit?$GLOBAL->UserInfo->RightsAdmin!=="Y":false,0,"","","","","","",true)?>
					</tr>
					<tr>
						<th>Название:</th>
						<td><?=$HtmlTags->InputText("Title",$IsEdit?$CityInfo->Title:"","","text","Title","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Находится в:</th>
						<td><?=$HtmlTags->InputText("TitleIn",$IsEdit?$CityInfo->TitleIn:"","","text","TitleIn","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Страна и регион:</th>
						<td>
							<select name="Region">
								<?=$HtmlTags->option(0,"Не установлены")?>
<?php
		foreach($AllCountries as $Country){
			echo "<optgroup label=\"{$Country->Title}\">";
			foreach($Country->Regions as $Region){
				echo $HtmlTags->option($Region->Id,$Region->Title,$IsEdit?$CityInfo->Region==$Region->Id:false);
			}
			echo "</optgroup>";
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$CityInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>