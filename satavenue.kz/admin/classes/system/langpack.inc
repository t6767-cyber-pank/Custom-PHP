<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление заголовками и надписями");
	$LanguageExist=false;

	$Direction="ASC";
	$PageDirection="ASC";

	$SortField="Name";
	$SortMethod="Name {$Direction}";

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
		$Name=$Kernel->PostToSQL(@$_GET["Name"]);
		if(!empty($Name)){
			$Filters[]="`Name` LIKE '%{$Name}%'";
			$FiltersURL[]="Name={$Name}";
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

	$LangPackList=$DataBase->Query("SELECT * FROM {$GLOBAL->LangPack}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($LangPackInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->LangPack} WHERE Id={$Id};",false)){
			$LangPackExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $LangPackExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Name=$Kernel->PostToURL(@$_POST["Name"],true,2);
		$SQL[]="`Name`='{$Name}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->LangPack} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $LangPackExist){
		$SQL=array();

		if($GLOBAL->UserInfo->RightsAdmin=="Y") {
			$Name = $Kernel->PostToURL(@$_POST["Name"],true,2);
			$SQL[] = "`Name`='{$Name}'";
		}

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->LangPack} SET {$SQL} WHERE Id={$LangPackInfo->Id};");

		header("Location: ?EditForm&Id={$LangPackInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($LangPackList as $LangPack){
			if(isset($_POST["Chk{$LangPack->Id}"]) && $_POST["Chk{$LangPack->Id}"]==$LangPack->Id){
				$DelIds[$LangPack->Id]=$LangPack->Id;
			}

			unset($LangPack);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->LangPack} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$LangPackCount=$DataBase->Query("SELECT COUNT(*) AS LangPacks FROM {$GLOBAL->LangPack};",false);
	$LangPackCount=(int) @$LangPackCount->LangPacks;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(40/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные настройки" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего настроек: <strong><?=$LangPackCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="60%" />
<?php
		foreach($AllLanguages as $Language){
?>
					<col width="<?=$RowWidth?>%" />
<?php
		}
?>
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="Name"?$DirectionArrow:""?></a></th>
<?php
		foreach($AllLanguages as $Language){
?>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Значение <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span><?=$SortField=="Name"?$DirectionArrow:""?></a></th>
<?php
		}
?>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Name",@$_GET["Name"],"","text","Name","text")?></th>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
?>
						<th><?=$HtmlTags->InputText($Title,@$_GET[$Title],"","text",$Title,"text")?></th>
<?php
		}
?>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($LangPackList as $LangPack){
?>
					<tr fid="<?=$LangPack->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$LangPack->Id}",$LangPack->Id,"",false,"","Chk{$LangPack->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$LangPack->Id?>"><?=$LangPack->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$LangPack->Id?>"><?=$LangPack->Name?></a></td>
<?php
			foreach($AllLanguages as $Language){
				$Title="Title_{$Language->Prefix}";
?>
						<td><a href="?EditForm&Id=<?=$LangPack->Id?>"><?=$LangPack->$Title?></a></td>
<?php
			}
?>
					</tr>
<?php
			unset($LangPack);
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
<form action="?<?=$IsEdit?"Edit&Id={$LangPackInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> настройки:</h1>
			<div class="buttons">
				<a href="?Id=<?=$ParentId?>" class="back">Вернуться</a>
<?php
		if($IsEdit){
?>
				<a href="?AddForm" class="button add">Добавить новую</a>
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
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$LangPackInfo->Name:"","","text","Name","text",0,$IsEdit?$GLOBAL->UserInfo->RightsAdmin!=="Y":false,0,"","","","","","",true)?>
					</tr>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
?>
					<tr>
						<th>Значение <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span>:</th>
						<td><?=$HtmlTags->InputText($Title,$IsEdit?$LangPackInfo->$Title:"","","text",$Title,"text")?></td>
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