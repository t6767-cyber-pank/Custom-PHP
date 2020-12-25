<?php
	if($GLOBAL->UserInfo->RightsSimTypes!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление типами sim услуг");
	$LanguageExist=false;

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

	$SimTypeList=$DataBase->Query("SELECT * FROM {$GLOBAL->SimTypes}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($SimTypeInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SimTypes} WHERE Id={$Id};",false)){
			$SimTypeExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $SimTypeExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->SimTypes};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$Unit="Unit_{$Language->Prefix}";
			$Description="Description_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
			$SQL[]="`{$Unit}`='".$Kernel->PostToSQL(@$_POST[$Unit])."'";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SimTypes} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $SimTypeExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$Unit="Unit_{$Language->Prefix}";
			$Description="Description_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
			$SQL[]="`{$Unit}`='".$Kernel->PostToSQL(@$_POST[$Unit])."'";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimTypes} SET {$SQL} WHERE Id={$SimTypeInfo->Id};");

		header("Location: ?EditForm&Id={$SimTypeInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($SimTypeList as $SimType){
			if(isset($_POST["Chk{$SimType->Id}"]) && $_POST["Chk{$SimType->Id}"]==$SimType->Id){
				$DelIds[$SimType->Id]=$SimType->Id;
			}

			unset($SimType);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SimTypes} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimTypes} SET `Position`={$Position} WHERE Id={$ItemId};");
		}
		exit();
	}

	$SimTypeCount=$DataBase->Query("SELECT COUNT(*) AS SimTypes FROM {$GLOBAL->SimTypes};",false);
	$SimTypeCount=(int) @$SimTypeCount->SimTypes;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(100/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные услуги" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего услуг: <strong><?=$SimTypeCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
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
<?php
		foreach($AllLanguages as $Language){
?>
						<th><a href="?Sort=Title&Direction=<?=$Direction?><?=$FiltersURL?>">Название <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span><?=$SortField=="Title"?$DirectionArrow:""?></a></th>
<?php
		}
?>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
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
		foreach($SimTypeList as $SimType){
?>
					<tr fid="<?=$SimType->Id?>" id="Pos<?=$SimType->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$SimType->Id}",$SimType->Id,"",false,"","Chk{$SimType->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$SimType->Id?>"><?=$SimType->Id?></a></td>
<?php
			foreach($AllLanguages as $Language){
				$Title="Title_{$Language->Prefix}";
?>
						<td><a href="?EditForm&Id=<?=$SimType->Id?>"><?=$SimType->$Title?></a></td>
<?php
			}
?>
					</tr>
<?php
			unset($SimType);
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
				success: function(data){
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

					var ItemsPositions=Array();
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
<form action="?<?=$IsEdit?"Edit&Id={$SimTypeInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> услуги:</h1>
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
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","включен",$IsEdit?$SimTypeInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$Unit="Unit_{$Language->Prefix}";
			$Description="Description_{$Language->Prefix}"
?>
					<tr>
						<th>Название <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span>:</th>
						<td><?=$HtmlTags->InputText($Title,$IsEdit?$SimTypeInfo->$Title:"","","text",$Title,"text")?></td>
					</tr>
					<tr>
						<th>Единицы измерения <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span>:</th>
						<td><?=$HtmlTags->InputText($Unit,$IsEdit?$SimTypeInfo->$Unit:"","","text",$Unit,"text")?></td>
					</tr>
					<tr>
						<th>Описание <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span>:</th>
						<td><?=$HtmlTags->textarea($Description,$IsEdit?$SimTypeInfo->$Description:"",30,5,"",$Language->Prefix,$Description,"text")?></td>
					</tr>
<?php
		}
?>
				</tbody>
			</table>
		</div>
	</section>
<?$Design->LoadEditor()?>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>