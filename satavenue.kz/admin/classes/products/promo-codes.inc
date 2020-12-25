<?php
	if($GLOBAL->UserInfo->RightsProducts!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление промо кодами");
	$LanguageExist=false;

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

	$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";

	$Filters=array();
	$FiltersURL=array();

	if(isset($_GET["Filter"])){
		$Id=(int) @$_GET["Id"];
		if(!empty($Id)){
			$Filters[]="`Id`={$Id}";
			$FiltersURL[]="Id={$Id}";
		}
		foreach($AllLanguages as $Language){
			$Title=$Kernel->PostToSQL(@$_GET["Title_{$Language->Prefix}"]);
			if(!empty($Title)){
				$Filters[]="`Title_{$Language->Prefix}` LIKE '%{$Title}%'";
				$FiltersURL[]="Title_{$Language->Prefix}={$Title}";
			}
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllPromoCodes=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->PromoCodes}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($CodeInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->PromoCodes} WHERE Id={$Id};",false)){
			$CodeExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $CodeExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Code=$Kernel->PostToSQL(@$_POST["Code"],true,true,50);
		$SQL[]="`Code`='{$Code}'";

		$Discount=(int) @$_POST["Discount"];
		$Discount=$Discount<0?0:$Discount;
		$Discount=$Discount>100?100:$Discount;
		$SQL[]="`Discount`={$Discount}";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->PromoCodes} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $CodeExist){
		$SQL=array();

		$Code=$Kernel->PostToSQL(@$_POST["Code"],true,true,50);
		$SQL[]="`Code`='{$Code}'";

		$Discount=(int) @$_POST["Discount"];
		$Discount=$Discount<0?0:$Discount;
		$Discount=$Discount>100?100:$Discount;
		$SQL[]="`Discount`={$Discount}";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->PromoCodes} SET {$SQL} WHERE Id={$CodeInfo->Id};");

		header("Location: ?EditForm&Id={$CodeInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllPromoCodes as $Code){
			if(isset($_POST["Chk{$Code->Id}"]) && $_POST["Chk{$Code->Id}"]==$Code->Id){
				$DelIds[$Code->Id]=$Code->Id;
			}

			unset($Code);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->PromoCodes} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$CodeCount=$DataBase->Query("SELECT COUNT(*) AS Codes FROM {$GLOBAL->PromoCodes};",false);
	$CodeCount=(int) @$CodeCount->Codes;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(100/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные промо коды" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего промо кодов: <strong><?=$CodeCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="20%" />
					<col width="20%" />
					<col width="60%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Discount&Direction=<?=$Direction?><?=$FiltersURL?>">Скидка<?=$SortField=="Discount"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Code&Direction=<?=$Direction?><?=$FiltersURL?>">Код<?=$SortField=="Code"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Title&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="Title"?$DirectionArrow:""?></a></th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Discount",@$_GET["Discount"],"","text","Discount","text")?></th>
						<th><?=$HtmlTags->InputText("Code",@$_GET["Code"],"","text","Code","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllPromoCodes as $Code){
?>
					<tr fid="<?=$Code->Id?>" id="Pos<?=$Code->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Code->Id}",$Code->Id,"",false,"","Chk{$Code->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Code->Id?>"><?=$Code->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Code->Id?>"><?=$Code->Discount?></a></td>
						<td><a href="?EditForm&Id=<?=$Code->Id?>"><?=$Code->Code?></a></td>
						<td><a href="?EditForm&Id=<?=$Code->Id?>"><?=$Code->Title?></a></td>
					</tr>
<?php
			unset($Code);
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
<form action="?<?=$IsEdit?"Edit&Id={$CodeInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> промо кода:</h1>
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
						<th>Код:</th>
						<td><?=$HtmlTags->InputText("Code",$IsEdit?$CodeInfo->Code:"","","text","Code","text",50)?></td>
					</tr>
					<tr>
						<th>Скидка:</th>
						<td><?=$HtmlTags->InputText("Discount",$IsEdit?$CodeInfo->Discount:0,"","number","Discount","text",3,false,0,"","","","min=\"0\" max=\"100\"")?>%</td>
					</tr>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
?>
					<tr>
						<th>Название <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span>:</th>
						<td><?=$HtmlTags->InputText($Title,$IsEdit?$CodeInfo->$Title:"","","text",$Title,"text")?></td>
					</tr>
<?php
		}
?>
				</tbody>
			</table>
		</div>
	</section>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>