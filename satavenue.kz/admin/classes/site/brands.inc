<?php
	if($GLOBAL->UserInfo->RightsBrands!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление брендами");
	$ShowLanguages=true;

	$Direction="ASC";
	$PageDirection="ASC";

	$SortField="Position";
	$SortMethod="Position {$Direction}";

	$AllLanguages=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} ORDER BY {$SortMethod};");

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

	$BrandList=$DataBase->Query("SELECT * FROM {$GLOBAL->Brands}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($BrandInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Brands} WHERE Id={$Id};",false)){
			$BrandExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $BrandExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		$SQL[]="`Name`='{$Name}'";

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->Brands};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$VoucherType="VoucherType_{$Language->Prefix}";
			$PhoneLabel="PhoneLabel_{$Language->Prefix}";
			$UnitTitle="UnitTitle_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
			$SQL[]="`{$VoucherType}`='".$Kernel->PostToSQL(@$_POST[$VoucherType],false,false)."'";
			$SQL[]="`{$PhoneLabel}`='".$Kernel->PostToSQL(@$_POST[$PhoneLabel],false,false)."'";
			$SQL[]="`{$UnitTitle}`='".$Kernel->PostToSQL(@$_POST[$UnitTitle],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Brands} SET {$SQL};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Brands,$LastId,"png",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $BrandExist){
		$SQL=array();

		if($GLOBAL->UserInfo->RightsAdmin=="Y") {
			$Name = $Kernel->PostToURL(@$_POST["Name"]);
			$SQL[] = "`Name`='{$Name}'";
		}

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$VoucherType="VoucherType_{$Language->Prefix}";
			$PhoneLabel="PhoneLabel_{$Language->Prefix}";
			$UnitTitle="UnitTitle_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false,false)."'";
			$SQL[]="`{$VoucherType}`='".$Kernel->PostToSQL(@$_POST[$VoucherType],false,false)."'";
			$SQL[]="`{$PhoneLabel}`='".$Kernel->PostToSQL(@$_POST[$PhoneLabel],false,false)."'";
			$SQL[]="`{$UnitTitle}`='".$Kernel->PostToSQL(@$_POST[$UnitTitle],false,false)."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Brands} SET {$SQL} WHERE Id={$BrandInfo->Id};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Brands,$BrandInfo->Id,"png",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$BrandInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($BrandList as $Brand){
			if(isset($_POST["Chk{$Brand->Id}"]) && $_POST["Chk{$Brand->Id}"]==$Brand->Id){
				$DelIds[$Brand->Id]=$Brand->Id;
				$Kernel->ClearThumbnails($DIRS->Brands,$Brand->Id,"png",$_FILES["Cover"]);
			}

			unset($Brand);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Brands} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Brands} SET `Position`={$Position} WHERE Id={$ItemId};");
		}
		exit();
	}

	$BrandCount=$DataBase->Query("SELECT COUNT(*) AS Brands FROM {$GLOBAL->Brands};",false);
	$BrandCount=(int) @$BrandCount->Brands;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
		$RowWidth=round(60/count($AllLanguages));
?>
<form action="?Delete" method="post" delquestion="выбранные бренды" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего брендов: <strong><?=$BrandCount?></strong></span>
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
					<col width="40%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
<?php
		foreach($AllLanguages as $Language){
?>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Название <span style="color:<?=$Language->Color?>;">[<?=$Language->Prefix?>]</span><?=$SortField=="Name"?$DirectionArrow:""?></a></th>
<?php
		}
?>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Имя<?=$SortField=="Name"?$DirectionArrow:""?></a></th>
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
						<th><?=$HtmlTags->InputText("Name",@$_GET["Name"],"","text","Name","text")?></th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($BrandList as $Brand){
?>
					<tr fid="<?=$Brand->Id?>" id="Pos<?=$Brand->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Brand->Id}",$Brand->Id,"",false,"","Chk{$Brand->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Brand->Id?>"><?=$Brand->Id?></a></td>
<?php
			foreach($AllLanguages as $Language){
				$Title="Title_{$Language->Prefix}";
?>
						<td><a href="?EditForm&Id=<?=$Brand->Id?>"><?=$Brand->$Title?></a></td>
<?php
			}
?>
						<td><a href="?EditForm&Id=<?=$Brand->Id?>"><?=$Brand->Name?></a></td>
					</tr>
<?php
			unset($Brand);
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
<form action="?<?=$IsEdit?"Edit&Id={$BrandInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> бренда:</h1>
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
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$BrandInfo->Name:"","","text","Name","text",0,$IsEdit?$GLOBAL->UserInfo->RightsAdmin!=="Y":false,0,"","","","","","",true)?>
					</tr>
					<tr>
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","включен",$IsEdit?$BrandInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Заголовок при пополнении счета:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$VoucherType="VoucherType_{$Language->Prefix}";
			echo $HtmlTags->InputText($VoucherType,$IsEdit?$BrandInfo->$VoucherType:"","","text",$VoucherType,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Заголовок при вводе номера:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$PhoneLabel="PhoneLabel_{$Language->Prefix}";
			echo $HtmlTags->InputText($PhoneLabel,$IsEdit?$BrandInfo->$PhoneLabel:"","","text",$PhoneLabel,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Название единиц:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$UnitTitle="UnitTitle_{$Language->Prefix}";
			echo $HtmlTags->InputText($UnitTitle,$IsEdit?$BrandInfo->$UnitTitle:"","","text",$UnitTitle,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Логотип:</th>
						<td><?=$HtmlTags->InputText("Cover","","","file","Cover","text")?></td>
					</tr>
					<tr>
						<th>Название:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";

			echo $HtmlTags->InputText($Title,$IsEdit?$BrandInfo->$Title:"","","text",$Title,"text");
		}
?>
						</td>
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