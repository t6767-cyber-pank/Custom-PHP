<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление категориями помощи");
	$ShowLanguages=true;
	$CategoryExist=false;

	$Direction="ASC";
	$PageDirection="ASC";

	$SortField="Position";
	$SortMethod="Position {$Direction}";

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
		$Status=@$_GET["Status"];
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllCategories=$DataBase->Query("SELECT * FROM {$GLOBAL->HelpCategories}{$Filters} ORDER BY `Position`;");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($CategoryInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->HelpCategories} WHERE Id={$Id};",false)){
			$CategoryExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $CategoryExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->HelpCategories};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$Kernel->CheckAdminUpdate($Title);

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		$Name=empty($Name)?$Kernel->PostToURL($Title):$Name;
		$SQL[]="`Name`='{$Name}'";

		$Description=$Kernel->PostToSQL(@$_POST["Description"],false,false);
		$SQL[]="`Description`='{$Description}'";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->HelpCategories} SET {$SQL};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"ADD","HELPCATEGORY",$LastId);

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $CategoryExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$Kernel->CheckAdminUpdate($Title);

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		$Name=empty($Name)?$Kernel->PostToURL($Title):$Name;
		$SQL[]="`Name`='{$Name}'";

		$Description=$Kernel->PostToSQL(@$_POST["Description"],false,false);
		$SQL[]="`Description`='{$Description}'";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->HelpCategories} SET {$SQL} WHERE Id={$CategoryInfo->Id};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"EDIT","HELPCATEGORY",$CategoryInfo->Id);

		header("Location: ?EditForm&Id={$CategoryInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $CategoryInfo){
		$Status=(isset($_POST["Status{$CategoryInfo->Id}"]) && $_POST["Status{$CategoryInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->HelpCategories} SET `Status`='{$Status}' WHERE Id={$CategoryInfo->Id};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"EDIT","HELPCATEGORY",$CategoryInfo->Id);

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllCategories as $Category){
			if((int) $Category->Id!==1 && isset($_POST["Chk{$Category->Id}"]) && $_POST["Chk{$Category->Id}"]==$Category->Id){
				$DelIds[$Category->Id]=$Category->Id;

				$Kernel->AddEvent($GLOBAL->UserInfo->Id,"REMOVE","HELPCATEGORY",$Category->Id);
			}

			unset($Category);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->HelpCategories} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->HelpCategories} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные категории" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить категорию</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="100%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>Id</th>
						<th>Заголовок</th>
						<th>Статус</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th>
							<select name="Status" id="Status" title="Статус категории">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllCategories as $Category){
?>
					<tr fid="<?=$Category->Id?>" id="Pos<?=$Category->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Category->Id}",$Category->Id,"",false,"","Chk{$Category->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Category->Id?>"><?=$Category->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Category->Id?>"><?=$Category->Title?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Category->Id}","Y","",$Category->Status=="Y","","Status{$Category->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Category);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8"></td>
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
<form action="?<?=$IsEdit?"Edit&Id={$CategoryInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> категории помощи:</h1>
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
						<th>Заголовок:</th>
						<td><?=$HtmlTags->InputText("Title",$IsEdit?$CategoryInfo->Title:"","","text","Title","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Каноническое название:</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$CategoryInfo->Name:"","","text","Name","text")?></td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$CategoryInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td><?=$HtmlTags->textarea("Description",$IsEdit?$CategoryInfo->Description:"",40,10,"","","Description","text")?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
<?$Design->LoadEditor();?>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>