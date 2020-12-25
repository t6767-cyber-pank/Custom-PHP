<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление языками");
	$ShowLanguages=true;
	$LanguageExist=false;

	$Direction="DESC";
	$PageDirection="DESC";

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

	$FieldsToAdd=array(
		array($GLOBAL->SystemPages,"Title_","","varchar(256)"),
		array($GLOBAL->SystemPages,"MenuTitle_","","varchar(256)"),
		array($GLOBAL->SystemPages,"Description_","","text"),
		array($GLOBAL->SystemPages,"MoreDescription_","","text"),
		array($GLOBAL->SystemPages,"MetaDescription_","","varchar(256)"),
		array($GLOBAL->SystemPages,"MetaKeywords_","","varchar(256)"),

		array($GLOBAL->LangPack,"Title_","","text"),

		array($GLOBAL->TextBlocks,"Title_","","varchar(256)"),
		array($GLOBAL->TextBlocks,"Description_","","text"),

		array($GLOBAL->Regions,"Title_","","varchar(256)"),
		array($GLOBAL->Regions,"MenuTitle_","","varchar(256)"),

		array($GLOBAL->Brands,"Title_","","varchar(256)"),
		array($GLOBAL->Brands,"VoucherType_","","varchar(256)"),
		array($GLOBAL->Brands,"PhoneLabel_","","varchar(256)"),
		array($GLOBAL->Brands,"UnitTitle_","","varchar(256)"),

		array($GLOBAL->Categories,"Title_","","varchar(256)"),
		array($GLOBAL->Categories,"MenuTitle_","","varchar(256)"),
		array($GLOBAL->Categories,"Description_","","text"),
		array($GLOBAL->Categories,"MetaDescription_","","varchar(256)"),
		array($GLOBAL->Categories,"MetaKeywords_","","varchar(256)"),
		array($GLOBAL->Categories,"MoreDescription_","","varchar(256)"),

		array($GLOBAL->ProductOffers,"Title_","","varchar(256)"),

		array($GLOBAL->Products,"Title_","","varchar(256)"),
		array($GLOBAL->Products,"Annotation_","","varchar(512)"),
		array($GLOBAL->Products,"ShortDescription_","","text"),
		array($GLOBAL->Products,"Description_","","text"),
		array($GLOBAL->Products,"Characteristics_","","text"),
		array($GLOBAL->Products,"Map_","","text"),
		array($GLOBAL->Products,"Rules_","","text"),

		array($GLOBAL->Reviews,"Title_","","varchar(256)"),
		array($GLOBAL->Reviews,"Description_","","text"),

		array($GLOBAL->Shares,"Title_","","varchar(256)"),
		array($GLOBAL->Shares,"Description_","","text"),

		array($GLOBAL->SimCategories,"Title_","","varchar(256)"),

		array($GLOBAL->SimProducts,"Title_","","varchar(256)"),
		array($GLOBAL->SimProducts,"PriceValue_","","varchar(256)"),

		array($GLOBAL->SimTypes,"Title_","","varchar(256)"),
		array($GLOBAL->SimTypes,"Unit_","","varchar(256)"),

		array($GLOBAL->Slider,"Title_","","varchar(256)"),
		array($GLOBAL->Slider,"URL_","","varchar(256)"),
		array($GLOBAL->Slider,"ButtonLabel_","","varchar(256)"),

		array($GLOBAL->PromoCodes,"Title_","","varchar(256)"),

		array($GLOBAL->SimPackages,"Period_","","varchar(256)"),
	);

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
		$Prefix=addslashes(stripslashes(strip_tags(@$_GET["Prefix"])));
		if(!empty($Prefix)){
			$Filters[]="`Prefix` LIKE '%{$Prefix}%'";
			$FiltersURL[]="Prefix={$Prefix}";
		}
		$Color=addslashes(stripslashes(strip_tags(@$_GET["Color"])));
		if(!empty($Color)){
			$Filters[]="`Color` LIKE '%{$Color}%'";
			$FiltersURL[]="Color={$Color}";
		}
		$Status=@$_GET["Status"];
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllLanguages=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($Id==1 && (int) $GLOBAL->UserInfo->Id!==1){
			$Id=-1;
		}
		if($LanguageInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} WHERE Id={$Id};",false)){
			$LanguageExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $LanguageExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Name=$Kernel->PostToSQL(@$_POST["Name"]);
		$SQL[]="`Name`='{$Name}'";

		$Prefix=$Kernel->PostToSQL(@$_POST["Prefix"]);
		$SQL[]="`Prefix`='{$Prefix}'";

		$Color=$Kernel->PostToSQL(@$_POST["Color"]);
		$SQL[]="`Color`='{$Color}'";

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->SystemLanguages};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SystemLanguages} SET {$SQL};");

		foreach($FieldsToAdd as $Field){
			$SQL="ALTER TABLE {$Field[0]} ADD COLUMN `{$Field[1]}{$Prefix}{$Field[2]}` {$Field[3]} NULL;";
			$DataBase->UpdateQuery($SQL);
		}

		header("Location: ?EditForm&Id={$LastId}&inserted");
		exit();
	}elseif(isset($_GET["Edit"]) && $LanguageExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Name=$Kernel->PostToSQL(@$_POST["Name"]);
		$SQL[]="`Name`='{$Name}'";

		$Prefix=$Kernel->PostToSQL(@$_POST["Prefix"]);
		$SQL[]="`Prefix`='{$Prefix}'";

		$Color=$Kernel->PostToSQL(@$_POST["Color"]);
		$SQL[]="`Color`='{$Color}'";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemLanguages} SET {$SQL} WHERE Id={$LanguageInfo->Id};");

		header("Location: ?EditForm&Id={$LanguageInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $LanguageInfo){
		$Status=(isset($_POST["Status{$LanguageInfo->Id}"]) && $_POST["Status{$LanguageInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemLanguages} SET `Status`='{$Status}' WHERE Id={$LanguageInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllLanguages as $Language){
			if(isset($_POST["Chk{$Language->Id}"]) && $_POST["Chk{$Language->Id}"]==$Language->Id){
				$DelIds[$Language->Id]=$Language->Id;

				foreach($FieldsToAdd as $Field){
					$SQL="ALTER TABLE {$Field[0]} DROP COLUMN `{$Field[1]}{$Language->Prefix}{$Field[2]}`;";
					$DataBase->UpdateQuery($SQL);
				}
			}

			unset($Language);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SystemLanguages} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}elseif(isset($_GET["hash"])){
		foreach($AllLanguages as $Language){
			foreach($FieldsToAdd as $md){
				$SQL="ALTER TABLE {$md[0]} ADD COLUMN `{$md[1]}{$Language->Prefix}{$md[2]}` {$md[3]} NULL;";
				$DataBase->UpdateQuery($SQL);
			}
		}

		header("Location: ?hashed");
		exit;
	}elseif(isset($_GET["ChangePosition"])){
		$Positions=explode(",",$Positions);
		foreach($Positions as $Position){
			$Position=explode("-",$Position);
			$ItemId=$Position[0];
			$Position=$Position[1];
			$ItemId=substr($ItemId,3);
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemLanguages} SET `Position`={$Position} WHERE Id={$ItemId};");
		}
		exit();
	}

	$LanguageCount=$DataBase->Query("SELECT COUNT(*) AS Users FROM {$GLOBAL->SystemLanguages};",false);
	$LanguageCount=(int) @$LanguageCount->Users;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные языки" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить язык</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего языков: <strong><?=$LanguageCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="60%" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="Name"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Prefix&Direction=<?=$Direction?><?=$FiltersURL?>">Префик<?=$SortField=="Prefix"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Color&Direction=<?=$Direction?><?=$FiltersURL?>">Цвет<?=$SortField=="Color"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Status&Direction=<?=$Direction?><?=$FiltersURL?>">Статус<?=$SortField=="Status"?$DirectionArrow:""?></a></th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Name",@$_GET["Name"],"","text","Name","text")?></th>
						<th><?=$HtmlTags->InputText("Prefix",@$_GET["Prefix"],"","text","Prefix","text")?></th>
						<th></th>
						<th>
							<select name="Status" id="Status" title="Статус">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Отключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllLanguages as $Language){
?>
					<tr fid="<?=$Language->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Language->Id}",$Language->Id,"",false,"","Chk{$Language->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Language->Id?>"><?=$Language->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Language->Id?>"><?=$Language->Name?></a></td>
						<td><a href="?EditForm&Id=<?=$Language->Id?>"><?=$Language->Prefix?></a></td>
						<td><a href="?EditForm&Id=<?=$Language->Id?>"><?=$Language->Color?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Language->Id}","Y","",$Language->Status=="Y","","Status{$Language->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Language);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6"></td>
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
<form action="?<?=$IsEdit?"Edit&Id={$LanguageInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> языка:</h1>
			<div class="buttons">
				<a href="?Id=<?=$ParentId?>" class="back">Вернуться</a>
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
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$LanguageInfo->Name:"","","text","Name","text")?>
					</tr>
					<tr>
						<th>Префикс:</th>
						<td><?=$HtmlTags->InputText("Prefix",$IsEdit?$LanguageInfo->Prefix:"","","text","Prefix","text",10,false,10)?>
					</tr>
					<tr>
						<th>Цвет:</th>
						<td><?=$HtmlTags->InputText("Color",$IsEdit?$LanguageInfo->Color:"","","color","Color","text",10,false,10)?>
					</tr>
					<tr>
						<th>Статус:</th>
						<td>
							<?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$LanguageInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?>
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