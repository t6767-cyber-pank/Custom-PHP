<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление слайдерами");
	$ShowLanguages=true;
	$SlideExist=false;

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
			$Filters[]="`Title_{$SortLanguage->Prefix}` LIKE '%{$Title}%'";
			$FiltersURL[]="Title={$Title}";
		}
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllSlides=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Slider}{$Filters} ORDER BY `Position`;");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($SlideInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Slider} WHERE Id={$Id};",false)){
			$SlideExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $SlideExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->Slider};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$TitleLabel="Title_{$Language->Prefix}";
			$ButtonLabelLabel="ButtonLabel_{$Language->Prefix}";
			$URLLabel="URL_{$Language->Prefix}";
			$Title=$Kernel->PostToSQL(@$_POST[$TitleLabel]);
			$ButtonLabel=$Kernel->PostToSQL(@$_POST[$ButtonLabelLabel]);
			$URL=$Kernel->PostToSQL(@$_POST[$URLLabel]);
			$SQL[]="`{$TitleLabel}`='{$Title}'";
			$SQL[]="`{$ButtonLabelLabel}`='{$ButtonLabel}'";
			$SQL[]="`{$URLLabel}`='{$URL}'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Slider} SET {$SQL};");

		foreach($AllLanguages as $Language){
			if(!empty($_FILES["Cover_{$Language->Prefix}"]["tmp_name"])){
				$Kernel->ClearThumbnails($DIRS->Slider,$LastId."_".$Language->Prefix,"jpg",$_FILES["Cover_{$Language->Prefix}"]);
			}
		}

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $SlideExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$TitleLabel="Title_{$Language->Prefix}";
			$ButtonLabelLabel="ButtonLabel_{$Language->Prefix}";
			$URLLabel="URL_{$Language->Prefix}";
			$Title=$Kernel->PostToSQL(@$_POST[$TitleLabel]);
			$ButtonLabel=$Kernel->PostToSQL(@$_POST[$ButtonLabelLabel]);
			$URL=$Kernel->PostToSQL(@$_POST[$URLLabel]);
			$SQL[]="`{$TitleLabel}`='{$Title}'";
			$SQL[]="`{$ButtonLabelLabel}`='{$ButtonLabel}'";
			$SQL[]="`{$URLLabel}`='{$URL}'";
			if(!empty($_FILES["Cover_{$Language->Prefix}"]["tmp_name"])){
				$Kernel->ClearThumbnails($DIRS->Slider,$SlideInfo->Id."_".$Language->Prefix,"jpg",$_FILES["Cover_{$Language->Prefix}"]);
			}
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Slider} SET {$SQL} WHERE Id={$SlideInfo->Id};");

		header("Location: ?EditForm&Id={$SlideInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $SlideInfo){
		$Status=(isset($_POST["Status{$SlideInfo->Id}"]) && $_POST["Status{$SlideInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Slider} SET `Status`='{$Status}' WHERE Id={$SlideInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllSlides as $Slide){
			if(isset($_POST["Chk{$Slide->Id}"]) && $_POST["Chk{$Slide->Id}"]==$Slide->Id){
				$DelIds[$Slide->Id]=$Slide->Id;

				$Kernel->ClearThumbnails($DIRS->Slider,$Slide->Id,"jpg");
			}

			unset($Slide);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Slider} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Slider} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные слайды" id="GroupChecked">
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
							<select name="Status" id="Status" title="Статус слайда">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllSlides as $Slide){
?>
					<tr fid="<?=$Slide->Id?>" id="Pos<?=$Slide->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Slide->Id}",$Slide->Id,"",false,"","Chk{$Slide->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Slide->Id?>"><?=$Slide->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Slide->Id?>"><?=$Slide->Title?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Slide->Id}","Y","",$Slide->Status=="Y","","Status{$Slide->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Slide);
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
<form action="?<?=$IsEdit?"Edit&Id={$SlideInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> слайда:</h1>
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
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			echo $HtmlTags->InputText($Title,$IsEdit?$SlideInfo->$Title:"","","text",$Title,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Надпись кнопки:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$ButtonLabel="ButtonLabel_{$Language->Prefix}";
			echo $HtmlTags->InputText($ButtonLabel,$IsEdit?$SlideInfo->$ButtonLabel:"","","text",$ButtonLabel,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Целевая ссылка:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$URL="URL_{$Language->Prefix}";
			echo $HtmlTags->InputText($URL,$IsEdit?$SlideInfo->$URL:"","","text",$URL,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$SlideInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Фоновое изображение:</th>
						<td>
							<?php
							foreach($AllLanguages as $Language){
								$Cover="Cover_{$Language->Prefix}";
							?>
								<?=$HtmlTags->InputText($Cover,"","","file",$Cover,"text")?>
							<?php
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