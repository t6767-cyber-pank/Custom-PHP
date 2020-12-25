<?php
	if($GLOBAL->UserInfo->RightsCategories!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$OwnerId=0;
	$Crumbs=array("Управление списком категорий");
	$ShowLanguages=true;
	$CategoryExist=false;

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($CategoryInfo=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE Id={$Id};",false)){
			$OwnerId=$CategoryInfo->Id;

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

		$Owner=(int) @$_POST["Owner"];
		$SQL[]="`Owner`={$Owner}";

		$Name=$Kernel->PostToURL(@$_POST["Name"],false);
		$SQL[]="`Name`='{$Name}'";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->Categories} WHERE `Owner`={$Owner};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$MenuTitle="MenuTitle_{$Language->Prefix}";
			$Description="Description_{$Language->Prefix}";
			$MoreDescription="MoreDescription_{$Language->Prefix}";
			$MetaDescription="MetaDescription_{$Language->Prefix}";
			$MetaKeywords="MetaKeywords_{$Language->Prefix}";

			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false)."'";
			$SQL[]="`{$MenuTitle}`='".$Kernel->PostToSQL(@$_POST[$MenuTitle])."'";
			$SQL[]="`{$MoreDescription}`='".$Kernel->PostToSQL(@$_POST[$MoreDescription])."'";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
			$SQL[]="`{$MetaDescription}`='".$Kernel->PostToSQL(@$_POST[$MetaDescription])."'";
			$SQL[]="`{$MetaKeywords}`='".$Kernel->PostToSQL(@$_POST[$MetaKeywords])."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->Query("INSERT INTO {$GLOBAL->Categories} SET {$SQL};",true,array(),array(),true);

		$FullInfo=$Kernel->ExtractToOwner($LastId,$GLOBAL->Categories);
		$FullIds="/".join("/",$FullInfo["FULL_IDS"])."/";
		$FullAddress="/".join("/",$FullInfo["FULL_ADD"])."/";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET `FullIds`='{$FullIds}',`FullAddress`='{$FullAddress}' WHERE Id={$LastId};");

		header("Location: ?Id={$LastId}&EditForm");
		exit();
	}elseif(isset($_GET["Edit"]) && $CategoryExist){
		$SQL=array();

		$Owner=(int) @$_POST["Owner"];
		$SQL[]="`Owner`={$Owner}";

		$Name=$Kernel->PostToURL(@$_POST["Name"],false);
		$SQL[]="`Name`='{$Name}'";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$MenuTitle="MenuTitle_{$Language->Prefix}";
			$Description="Description_{$Language->Prefix}";
			$MoreDescription="MoreDescription_{$Language->Prefix}";
			$MetaDescription="MetaDescription_{$Language->Prefix}";
			$MetaKeywords="MetaKeywords_{$Language->Prefix}";

			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title],false)."'";
			$SQL[]="`{$MenuTitle}`='".$Kernel->PostToSQL(@$_POST[$MenuTitle])."'";
			$SQL[]="`{$MoreDescription}`='".$Kernel->PostToSQL(@$_POST[$MoreDescription])."'";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
			$SQL[]="`{$MetaDescription}`='".$Kernel->PostToSQL(@$_POST[$MetaDescription])."'";
			$SQL[]="`{$MetaKeywords}`='".$Kernel->PostToSQL(@$_POST[$MetaKeywords])."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET {$SQL} WHERE Id={$CategoryInfo->Id};");

		$FullInfo=$Kernel->ExtractToOwner($CategoryInfo->Id,$GLOBAL->Categories);
		$FullIds="/".join("/",$FullInfo["FULL_IDS"])."/";
		$FullAddress="/".join("/",$FullInfo["FULL_ADD"])."/";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET `FullIds`='{$FullIds}',`FullAddress`='{$FullAddress}' WHERE Id={$CategoryInfo->Id};");

		header("Location: ?Id={$CategoryInfo->Id}&EditForm");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $CategoryExist){
		$AllCategories=$DataBase->Query("SELECT *,MenuTitle_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Owner`={$CategoryInfo->Owner};");

		foreach($AllCategories as $Category){
			$Status=(isset($_POST["Status{$Category->Id}"]) && $_POST["Status{$Category->Id}"]=="Y")?"Y":"N";

			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET `Status`='{$Status}' WHERE Id={$Category->Id};");
		}

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$AllCategories=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Owner`={$OwnerId};");

		$DelIds=array();
		foreach($AllCategories as $Category){
			if(isset($_POST["Chk{$Category->Id}"]) && $_POST["Chk{$Category->Id}"]){
				$DelIds[$Category->Id]=$Category->Id;
			}

			unset($Category);
		}

		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Categories} WHERE Id IN ({$DelIds});");
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET `Owner`=0 WHERE `Owner` IN ({$DelIds});");
		}

		header("Location: ?Id={$OwnerId}");
		exit();
	}elseif(isset($_GET["ChangePosition"])){
		$Positions=explode(",",$Positions);
		foreach($Positions as $Position){
			$Position=explode("-",$Position);
			$ItemId=$Position[0];
			$Position=$Position[1];
			$ItemId=substr($ItemId,3);
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Categories} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	if($ShowList){
		$AllCategories=$DataBase->Query("SELECT *,MenuTitle_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Owner`={$OwnerId} ORDER BY Position;");
		$CategoryIds=array();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete&Id=<?=$OwnerId?>" method="post" delquestion="выбранные категории" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
<?php
		if($CategoryExist){
?>
				<a href="?Id=<?=$CategoryInfo->Owner?>" class="back">Вернуться</a>
<?php
		}
?>
				<a href="?AddForm&Id=<?=$OwnerId?>" class="button add"><span></span> Создать категорию</a>
				<button class="button delete"><span></span> Удалить</button>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="60%" />
					<col width="30%" />
					<col width="10%" />
				</colgroup>
				<thead>
					<th class="checkbox lt"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
					<th>Id</th>
					<th>Имя</th>
					<th>Полный адрес</th>
					<th>Открыть</th>
					<th rowspan="rt">Статус</th>
				</thead>
				<tbody>
<?php
		foreach($AllCategories as $Category){
?>
					<tr fid="<?=$Category->Id?>" id="Pos<?=$Category->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Category->Id}",$Category->Id,"",false,"","Chk{$Category->Id}","",true,"checkbox",true)?></td>
						<td><?=$Category->Id?></td>
						<td>
							<a href="?Id=<?=$Category->Id?>" class="plus"><span></span></a>
							<a href="?EditForm&Id=<?=$Category->Id?>"><?=$Category->Title?></a>
						</td>
						<td><?=urldecode($Category->FullAddress)?></td>
						<td><a href="<?=$Category->FullAddress?>" target="_blank">Открыть</a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Category->Id}","Y","",$Category->Status=="Y","","Status{$Category->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Category);
		}
?>
				</tbody>
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
<script>
	CategoryId=<?=$OwnerId?>;
</script>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$CategoryInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> категории:</h1>
			<div class="buttons">
				<a href="?Id=<?=$IsEdit?$CategoryInfo->Owner:$OwnerId?>" class="back">Вернуться</a>
				<button class="button add"><span></span> <?=$IsEdit?"Применить":"Добавить"?></button>
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
						<th>Заголовок категории:</th>
						<td>
<?php
		$ThisItem=0;
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			echo $HtmlTags->InputText($Title,$IsEdit?$CategoryInfo->$Title:"","","text",$Title,"text",254,false,0,"","","","","","",$ThisItem++==0);
		}
?>
						</td>
					</tr>
					<tr>
						<th>Заголовок меню:</th>
						<td>
<?php
		$ThisItem=0;
		foreach($AllLanguages as $Language){
			$MenuTitle="MenuTitle_{$Language->Prefix}";
			echo $HtmlTags->InputText($MenuTitle,$IsEdit?$CategoryInfo->$MenuTitle:"","","text",$MenuTitle,"text",254,false,0,"","","","","","",$ThisItem++==0);
		}
?>
						</td>
					</tr>
					<tr>
						<th>
							Каноническое название:
							<span class="hint">Латинские символы без спецсимволов</span>
						</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?urldecode($CategoryInfo->Name):"","","text","Name","text",254,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Родитель:</th>
						<td>
							<select name="Owner" id="Owner" title="Родитель"><?$Kernel->PrintPagesList(0,$IsEdit?$CategoryInfo->Id:0,$IsEdit?$CategoryInfo->Owner:$OwnerId,0,true,$GLOBAL->Categories)?></select>
						</td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td>
							<?=$HtmlTags->InputCheckbox("Status","Y","активна",$IsEdit?$CategoryInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?>
						</td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Description="Description_{$Language->Prefix}";
			echo "<div id=\"{$Description}\">";
			echo $HtmlTags->textarea($Description,$IsEdit?$CategoryInfo->$Description:"",60,20,"",$Language->Prefix,"{$Description}R","text");
			echo "</div>";
		}
?>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" class="tl tr">Ключевые слова</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Дополнительная строка текста:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$MoreDescription="MoreDescription_{$Language->Prefix}";
			echo $HtmlTags->InputText($MoreDescription,$IsEdit?$CategoryInfo->$MoreDescription:"","","text",$MoreDescription,"text",254);
		}
?>
						</td>
					</tr>
					<tr>
						<th>Meta description:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$MetaDescription="MetaDescription_{$Language->Prefix}";
			echo $HtmlTags->InputText($MetaDescription,$IsEdit?$CategoryInfo->$MetaDescription:"","","text",$MetaDescription,"text",254);
		}
?>
						</td>
					</tr>
					<tr>
						<th>Meta keywords:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$MetaKeywords="MetaKeywords_{$Language->Prefix}";
			echo $HtmlTags->InputText($MetaKeywords,$IsEdit?$CategoryInfo->$MetaKeywords:"","","text",$MetaKeywords,"text",254);
		}
?>
						</td>
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