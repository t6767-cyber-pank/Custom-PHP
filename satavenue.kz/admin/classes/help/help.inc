<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление помощью");
	$ShowLanguages=true;
	$HelpItemExist=false;

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
		$CategoryId=(int) @$_GET["CategoryId"];
		if(!empty($CategoryId)){
			$Filters[]="`CategoryId`={$CategoryId}";
			$FiltersURL[]="CategoryId={$CategoryId}";
		}
		$Status=@$_GET["Status"];
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllHelpItems=$DataBase->Query("SELECT * FROM {$GLOBAL->Help}{$Filters} ORDER BY `Position`;");

	$AllCategories=$DataBase->Query("SELECT * FROM {$GLOBAL->HelpCategories} ORDER BY `Title`;");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($HelpItemInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Help} WHERE Id={$Id};",false)){
			$HelpItemExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $HelpItemExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->Help};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$CategoryId=(int) @$_POST["CategoryId"];
		$SQL[]="`CategoryId`={$CategoryId}";

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

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Help} SET {$SQL};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"ADD","HELP",$LastId);

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $HelpItemExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$CategoryId=(int) @$_POST["CategoryId"];
		$SQL[]="`CategoryId`={$CategoryId}";

		$Title=$Kernel->PostToSQL(@$_POST["Title"]);
		$SQL[]="`Title`='{$Title}'";

		$Kernel->CheckAdminUpdate($Title);

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		$Name=empty($Name)?$Kernel->PostToURL($Title):$Name;
		$SQL[]="`Name`='{$Name}'";

		$Description=$Kernel->PostToSQL(@$_POST["Description"],false,false);
		$SQL[]="`Description`='{$Description}'";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Help} SET {$SQL} WHERE Id={$HelpItemInfo->Id};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"EDIT","HELP",$HelpItemInfo->Id);

		header("Location: ?EditForm&Id={$HelpItemInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $HelpItemInfo){
		$Status=(isset($_POST["Status{$HelpItemInfo->Id}"]) && $_POST["Status{$HelpItemInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Help} SET `Status`='{$Status}' WHERE Id={$HelpItemInfo->Id};");

		$Kernel->AddEvent($GLOBAL->UserInfo->Id,"EDIT","HELP",$HelpItemInfo->Id);

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllHelpItems as $HelpItem){
			if((int) $HelpItem->Id!==1 && isset($_POST["Chk{$HelpItem->Id}"]) && $_POST["Chk{$HelpItem->Id}"]==$HelpItem->Id){
				$DelIds[$HelpItem->Id]=$HelpItem->Id;

				$Kernel->AddEvent($GLOBAL->UserInfo->Id,"REMOVE","HELP",$HelpItem->Id);
			}

			unset($HelpItem);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Help} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Help} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные пункты помощи" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="60%" />
					<col width="40%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>Id</th>
						<th>Заголовок</th>
						<th>Категория</th>
						<th>Статус</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th>
							<select name="CategoryId" title="Категория помощи">
								<?=$HtmlTags->option(0,"Все")?>
<?php
		foreach($AllCategories as $Category){
			echo $HtmlTags->option($Category->Id,$Category->Title,$Category->Id==@$_GET["CategoryId"]);
			unset($Category);
		}
?>
							</select>
						</th>
						<th>
							<select name="Status" id="Status" title="Статус пункта помощи">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllHelpItems as $HelpItem){
?>
					<tr fid="<?=$HelpItem->Id?>" id="Pos<?=$HelpItem->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$HelpItem->Id}",$HelpItem->Id,"",false,"","Chk{$HelpItem->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$HelpItem->Id?>"><?=$HelpItem->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$HelpItem->Id?>"><?=$HelpItem->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$HelpItem->Id?>"><?=$AllCategories[$HelpItem->CategoryId]->Title?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$HelpItem->Id}","Y","",$HelpItem->Status=="Y","","Status{$HelpItem->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($HelpItem);
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
<form action="?<?=$IsEdit?"Edit&Id={$HelpItemInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> пункта помощи:</h1>
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
						<td><?=$HtmlTags->InputText("Title",$IsEdit?$HelpItemInfo->Title:"","","text","Title","text",0,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Категория:</th>
						<td>
							<select name="CategoryId" id="CategoryId" title="Категория помощи">
<?php
		foreach($AllCategories as $Category){
			echo $HtmlTags->option($Category->Id,$Category->Title,$IsEdit?$HelpItemInfo->CategoryId=$Category->Id:false);
			unset($Category);
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Каноническое название:</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$HelpItemInfo->Name:"","","text","Name","text")?></td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$HelpItemInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td><?=$HtmlTags->textarea("Description",$IsEdit?$HelpItemInfo->Description:"",40,10,"","","Description","text")?></td>
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