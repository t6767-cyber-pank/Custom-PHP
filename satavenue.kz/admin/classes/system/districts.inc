<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление районами");
	$DistrictExist=false;

	$Direction="ASC";
	$PageDirection="ASC";

	$SortField="Title";
	$SortMethod="Title {$Direction}";

	if(isset($_GET["Sort"])){
		$Direction=(isset($_GET["Direction"]) && $_GET["Direction"]=="DESC")?"DESC":"ASC";
		$PageDirection=$Direction;
		$Sort=$_GET["Sort"];

		$SortField=$Sort;

		$SortMethod="{$Sort} {$Direction}";

		$Direction=$Direction=="DESC"?"ASC":"DESC";
	}

	$AllCities=$DataBase->Query("SELECT * FROM {$GLOBAL->Cities} ORDER BY Title;");

	$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";

	$Filters=array();
	$FiltersURL=array();

	if(isset($_GET["Filter"])){
		$Id=(int) @$_GET["Id"];
		if(!empty($Id)){
			$Filters[]="`Id`={$Id}";
			$FiltersURL[]="Id={$Id}";
		}
		$Title=$Kernel->PostToSQL(@$_GET["Title"]);
		if(!empty($Title)){
			$Filters[]="`Title` LIKE '%{$Title}%'";
			$FiltersURL[]="Title={$Title}";
		}
		$City=(int) @$_GET["City"];
		if(!empty($City)){
			$Filters[]="`CityId`={$City}";
			$FiltersURL[]="City={$City}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$DistrictList=$DataBase->Query("SELECT * FROM {$GLOBAL->Districts}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($DistrictInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Districts} WHERE Id={$Id};",false)){
			$DistrictExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $DistrictExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$CityId=(int) @$_POST["CityId"];
		$SQL[]="`CityId`={$CityId}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Districts} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $DistrictExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$CityId=(int) @$_POST["CityId"];
		$SQL[]="`CityId`={$CityId}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Districts} SET {$SQL} WHERE Id={$DistrictInfo->Id};");

		header("Location: ?EditForm&Id={$DistrictInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($DistrictList as $District){
			if(isset($_POST["Chk{$District->Id}"]) && $_POST["Chk{$District->Id}"]==$District->Id){
				$DelIds[$District->Id]=$District->Id;
			}

			unset($District);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Districts} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$DistrictCount=$DataBase->Query("SELECT COUNT(*) AS Districts FROM {$GLOBAL->Districts};",false);
	$DistrictCount=(int) @$DistrictCount->Districts;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(40/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные районы" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего районов: <strong><?=$DistrictCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="70%" />
					<col width="30%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Title&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="URL"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=City&Direction=<?=$Direction?><?=$FiltersURL?>">Город<?=$SortField=="City"?$DirectionArrow:""?></a></th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th>
							<select name="City">
								<?=$HtmlTags->option(0,"Все")?>
<?php
		foreach($AllCities as $City){
			echo $HtmlTags->option($City->Id,$City->Title,$City->Id==@$_GET["City"]);
		}
?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($DistrictList as $District){
?>
					<tr fid="<?=$District->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$District->Id}",$District->Id,"",false,"","Chk{$District->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$District->Id?>"><?=$District->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$District->Id?>"><?=$District->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$District->Id?>"><?=@$AllCities[$District->CityId]->Title?></a></td>
					</tr>
<?php
			unset($District);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="<?=3+count($AllLanguages)?>"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$DistrictInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> района:</h1>
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
						<th>Название:</th>
						<td><?=$HtmlTags->InputText("Title",$IsEdit?$DistrictInfo->Title:"","","text","Title","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Город:</th>
						<td>
							<select name="CityId">
								<?=$HtmlTags->option(0,"Не установлены")?>
<?php
		foreach($AllCities as $City){
			echo $HtmlTags->option($City->Id,$City->Title,$IsEdit?$DistrictInfo->CityId==$City->Id:false);
			unset($City);
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$DistrictInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
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