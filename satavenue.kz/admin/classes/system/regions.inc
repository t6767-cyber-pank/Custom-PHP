<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление городами");
	$RegionExist=false;

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

	$AllCountries=$DataBase->Query("SELECT * FROM {$GLOBAL->Countries} ORDER BY Title;");

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

	$RegionList=$DataBase->Query("SELECT * FROM {$GLOBAL->Regions}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($RegionInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Regions} WHERE Id={$Id};",false)){
			$RegionExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $RegionExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$URL=$Kernel->PostToURL(@$_POST["URL"],true,2);
		$SQL[]="`URL`='{$URL}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$Country=(int) @$_POST["Country"];
		$SQL[]="`Country`={$Country}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Regions} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $RegionExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$URL=$Kernel->PostToURL(@$_POST["URL"],true,2);
		$SQL[]="`URL`='{$URL}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$Country=(int) @$_POST["Country"];
		$SQL[]="`Country`={$Country}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Regions} SET {$SQL} WHERE Id={$RegionInfo->Id};");

		header("Location: ?EditForm&Id={$RegionInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($RegionList as $Region){
			if(isset($_POST["Chk{$Region->Id}"]) && $_POST["Chk{$Region->Id}"]==$Region->Id){
				$DelIds[$Region->Id]=$Region->Id;
			}

			unset($Region);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Regions} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$RegionCount=$DataBase->Query("SELECT COUNT(*) AS Regions FROM {$GLOBAL->Regions};",false);
	$RegionCount=(int) @$RegionCount->Regions;

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
					<span>Всего городов: <strong><?=$RegionCount?></strong></span>
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
					</tr>
				</thead>
				<tbody>
<?php
		foreach($RegionList as $Region){
?>
					<tr fid="<?=$Region->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Region->Id}",$Region->Id,"",false,"","Chk{$Region->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Region->Id?>"><?=$Region->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Region->Id?>"><?=$Region->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$Region->Id?>"><?=$Region->URL?></a></td>
						<td><a href="?EditForm&Id=<?=$Region->Id?>"><?=@$AllCountries[$Region->Country]->Title?></a></td>
					</tr>
<?php
			unset($Region);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$RegionInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
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
						<td><?=$HtmlTags->InputText("URL",$IsEdit?$RegionInfo->URL:"","","text","URL","text",0,$IsEdit?$GLOBAL->UserInfo->RightsAdmin!=="Y":false,0,"","","","","","",true)?>
					</tr>
					<tr>
						<th>Название:</th>
						<td><?=$HtmlTags->InputText("Title",$IsEdit?$RegionInfo->Title:"","","text","Title","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Страна:</th>
						<td>
							<select name="Country">
								<?=$HtmlTags->option(0,"Не установлена")?>
<?php
		foreach($AllCountries as $Country){
			echo $HtmlTags->option($Country->Id,$Country->Title,$IsEdit?$RegionInfo->Country==$Country->Id:false);
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$RegionInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
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