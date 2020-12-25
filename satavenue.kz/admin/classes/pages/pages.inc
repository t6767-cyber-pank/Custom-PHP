<?php
	if($GLOBAL->UserInfo->RightsPages!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$OwnerId=0;
	$Crumbs=array("Управление списком страниц");
	$ShowLanguages=true;
	$PageExist=false;

	$AllModelTypes=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModulesCategories} WHERE `Type`='CLIENT' ORDER BY Title;");
	$TypeIds=array_keys($AllModelTypes);
	$TypeIds=join(",",$TypeIds);
	$Modules=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModules} WHERE `Categor` IN ({$TypeIds}) ORDER BY Title;");
	foreach($Modules as $Module){
		$AllModelTypes[$Module->Categor]->Modules[$Module->Id]=$Module;
		unset($Module);
	}
	unset($Modules);

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($PageInfo=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE Id={$Id};",false)){
			$OwnerId=$PageInfo->Id;

			$PageExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $PageExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Owner=(int) @$_POST["Owner"];
		$SQL[]="`Owner`={$Owner}";

		$Type=(isset($_POST["Type"]) && $_POST["Type"]=="TEXT")?"TEXT":"MODULE";
		$SQL[]="`Type`='{$Type}'";

		$Name=$Kernel->PostToURL(@$_POST["Name"],false);
		$SQL[]="`Name`='{$Name}'";

		$Module=(int) @$_POST["Module"];
		$SQL[]="`Module`={$Module}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$MenuVisible=(isset($_POST["MenuVisible"]) && $_POST["MenuVisible"]=="Y")?"Y":"N";
		$SQL[]="`MenuVisible`='{$MenuVisible}'";

		$BottomMenuVisible=(isset($_POST["BottomMenuVisible"]) && $_POST["BottomMenuVisible"]=="Y")?"Y":"N";
		$SQL[]="`BottomMenuVisible`='{$BottomMenuVisible}'";

		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
			$CanDelete=(isset($_POST["CanDelete"]) && $_POST["CanDelete"]=="Y")?"Y":"N";
			$SQL[]="`CanDelete`='{$CanDelete}'";
		}

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->SystemPages} WHERE `Owner`={$Owner};",false);
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

		$LastId=$DataBase->Query("INSERT INTO {$GLOBAL->SystemPages} SET {$SQL};",true,array(),array(),true);

		if(!empty($_FILES["Cover"]["tmp_name"])){
			$Kernel->ClearThumbnails($DIRS->Pages,$LastId,"jpg");
			move_uploaded_file($_FILES["Cover"]["tmp_name"],"..{$DIRS->Pages}{$LastId}.jpg");
		}

		$FullInfo=$Kernel->ExtractToOwner($LastId);
		$FullIds="/".join("/",$FullInfo["FULL_IDS"])."/";
		$FullAddress="/".join("/",$FullInfo["FULL_ADD"])."/";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `FullIds`='{$FullIds}',`FullAddress`='{$FullAddress}' WHERE Id={$LastId};");

		header("Location: ?Id={$LastId}&EditForm");
		exit();
	}elseif(isset($_GET["Edit"]) && $PageExist){
		$SQL=array();

		$Owner=(int) @$_POST["Owner"];
		$SQL[]="`Owner`={$Owner}";

		$Type=(isset($_POST["Type"]) && $_POST["Type"]=="TEXT")?"TEXT":"MODULE";
		$SQL[]="`Type`='{$Type}'";

		$Name=$Kernel->PostToURL(@$_POST["Name"],false);
		$SQL[]="`Name`='{$Name}'";

		$Module=(int) @$_POST["Module"];
		$SQL[]="`Module`={$Module}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$MenuVisible=(isset($_POST["MenuVisible"]) && $_POST["MenuVisible"]=="Y")?"Y":"N";
		$SQL[]="`MenuVisible`='{$MenuVisible}'";

		$BottomMenuVisible=(isset($_POST["BottomMenuVisible"]) && $_POST["BottomMenuVisible"]=="Y")?"Y":"N";
		$SQL[]="`BottomMenuVisible`='{$BottomMenuVisible}'";

		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
			$CanDelete=(isset($_POST["CanDelete"]) && $_POST["CanDelete"]=="Y")?"Y":"N";
			$SQL[]="`CanDelete`='{$CanDelete}'";
		}

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

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET {$SQL} WHERE Id={$PageInfo->Id};");

		if(!empty($_FILES["Cover"]["tmp_name"])){
			$Kernel->ClearThumbnails($DIRS->Pages,$PageInfo->Id,"jpg");
			move_uploaded_file($_FILES["Cover"]["tmp_name"],"..{$DIRS->Pages}{$PageInfo->Id}.jpg");
		}

		$FullInfo=$Kernel->ExtractToOwner($PageInfo->Id);
		$FullIds="/".join("/",$FullInfo["FULL_IDS"])."/";
		$FullAddress="/".join("/",$FullInfo["FULL_ADD"])."/";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `FullIds`='{$FullIds}',`FullAddress`='{$FullAddress}' WHERE Id={$PageInfo->Id};");

		header("Location: ?Id={$PageInfo->Id}&EditForm");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $PageExist){
		$AllPages=$DataBase->Query("SELECT *,MenuTitle_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE `Owner`={$PageInfo->Owner};");

		foreach($AllPages as $Page){
			$Status=(isset($_POST["Status{$Page->Id}"]) && $_POST["Status{$Page->Id}"]=="Y")?"Y":"N";
			$MenuVisible=(isset($_POST["MenuVisible{$Page->Id}"]) && $_POST["MenuVisible{$Page->Id}"]=="Y")?"Y":"N";
			$BottomMenuVisible=(isset($_POST["BottomMenuVisible{$Page->Id}"]) && $_POST["BottomMenuVisible{$Page->Id}"]=="Y")?"Y":"N";
			$CanDelete="";
			if($GLOBAL->UserInfo->RightsAdmin=="Y"){
				$CanDelete=(isset($_POST["CanDelete{$Page->Id}"]) && $_POST["CanDelete{$Page->Id}"]=="Y")?"Y":"N";
				$CanDelete=",`CanDelete`='{$CanDelete}'";
			}

			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `Status`='{$Status}',`MenuVisible`='{$MenuVisible}',`BottomMenuVisible`='{$BottomMenuVisible}'{$CanDelete} WHERE Id={$Page->Id};");
		}

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$AllPages=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE `Owner`={$OwnerId};");

		$DelIds=array();
		foreach($AllPages as $Page){
			if(isset($_POST["Chk{$Page->Id}"]) && $_POST["Chk{$Page->Id}"]){
				$DelIds[$Page->Id]=$Page->Id;
				$Kernel->ClearThumbnails($DIRS->Pages,$Page->Id,"jpg");
			}

			unset($Page);
		}

		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SystemPages} WHERE Id IN ({$DelIds});");
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `Owner`=0 WHERE `Owner` IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemPages} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
		}
		exit();
	}

	if($ShowList){
		$AllPages=$DataBase->Query("SELECT *,MenuTitle_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE `Owner`={$OwnerId} ORDER BY Position;");
		$PageIds=array();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete&Id=<?=$OwnerId?>" method="post" delquestion="выбранные страницы" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
<?php
		if($PageExist){
?>
				<a href="?Id=<?=$PageInfo->Owner?>" class="back">Вернуться</a>
<?php
		}
?>
				<a href="?AddForm&Id=<?=$OwnerId?>" class="button add"><span></span> Создать страницу</a>
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
					<th rowspan="rt">Верхнее меню</th>
					<th rowspan="rt">Второе меню</th>
<?php
		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
?>
					<th rowspan="rt">Удаление</th>
<?php
		}
?>
				</thead>
				<tbody>
<?php
		foreach($AllPages as $Page){
?>
					<tr fid="<?=$Page->Id?>" id="Pos<?=$Page->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Page->Id}",$Page->Id,"",false,"","Chk{$Page->Id}","",true,"checkbox",true)?></td>
						<td><?=$Page->Id?></td>
						<td>
							<a href="?Id=<?=$Page->Id?>" class="plus"><span></span></a>
							<a href="?EditForm&Id=<?=$Page->Id?>"><?=$Page->Title?></a>
						</td>
						<td><?=urldecode($Page->FullAddress)?></td>
						<td><a href="<?=$Page->FullAddress?>" target="_blank">Открыть</a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Page->Id}","Y","",$Page->Status=="Y","","Status{$Page->Id}","",true,"checkbox-slider",true)?></td>
						<td><?=$HtmlTags->InputCheckbox("MenuVisible{$Page->Id}","Y","",$Page->MenuVisible=="Y","","MenuVisible{$Page->Id}","",true,"checkbox-slider",true)?></td>
						<td><?=$HtmlTags->InputCheckbox("BottomMenuVisible{$Page->Id}","Y","",$Page->BottomMenuVisible=="Y","","BottomMenuVisible{$Page->Id}","",true,"checkbox-slider",true)?></td>
<?php
			if($GLOBAL->UserInfo->RightsAdmin=="Y"){
?>
						<td><?=$HtmlTags->InputCheckbox("CanDelete{$Page->Id}","Y","",$Page->CanDelete=="Y","","CanDelete{$Page->Id}","",true,"checkbox-slider",true)?></td>
<?php
			}
?>
					</tr>
<?php
			unset($Page);
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
<form action="?<?=$IsEdit?"Edit&Id={$PageInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> страницы:</h1>
			<div class="buttons">
				<a href="?Id=<?=$IsEdit?$PageInfo->Owner:$OwnerId?>" class="back">Вернуться</a>
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
						<th>Заголовок страницы:</th>
						<td>
<?php
		$ThisItem=0;
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			echo $HtmlTags->InputText($Title,$IsEdit?$PageInfo->$Title:"","","text",$Title,"text",254,false,0,"","","","","","",$ThisItem++==0);
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
			echo $HtmlTags->InputText($MenuTitle,$IsEdit?$PageInfo->$MenuTitle:"","","text",$MenuTitle,"text",254,false,0,"","","","","","",$ThisItem++==0);
		}
?>
						</td>
					</tr>
					<tr>
						<th>
							Каноническое название:
							<span class="hint">Латинские символы без спецсимволов</span>
						</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?urldecode($PageInfo->Name):"","","text","Name","text",254,false,0,"","","","","","",true)?></td>
					</tr>
					<tr>
						<th>Родитель:</th>
						<td>
							<select name="Owner" id="Owner" title="Родитель"><?$Kernel->PrintPagesList(0,$IsEdit?$PageInfo->Id:0,$IsEdit?$PageInfo->Owner:$OwnerId)?></select>
						</td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td>
							<?=$HtmlTags->InputCheckbox("Status","Y","активна",$IsEdit?$PageInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?>
							<?=$HtmlTags->InputCheckbox("MenuVisible","Y","верхнее меню",$IsEdit?$PageInfo->MenuVisible=="Y":true,"","MenuVisible","",true,"checkbox-slider",true)?>
							<?=$HtmlTags->InputCheckbox("BottomMenuVisible","Y","нижнее меню",$IsEdit?$PageInfo->BottomMenuVisible=="Y":true,"","BottomMenuVisible","",true,"checkbox-slider",true)?>
<?php
		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
			echo $HtmlTags->InputCheckbox("CanDelete","Y","можно удалять",$IsEdit?$PageInfo->CanDelete=="Y":true,"","CanDelete","",true,"checkbox-slider",true);
		}
?>
						</td>
					</tr>
					<tr>
						<th>Тип страницы:</th>
						<td><?=$HtmlTags->InputCheckbox("Type","TEXT","текстовая",$IsEdit?$PageInfo->Type=="TEXT":true,"","Type","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Description="Description_{$Language->Prefix}";
			echo "<div id=\"{$Description}\">";
			echo $HtmlTags->textarea($Description,$IsEdit?$PageInfo->$Description:"",60,20,"",$Language->Prefix,"{$Description}R","text");
			echo "</div>";
		}
?>
						</td>
					</tr>
					<tr>
						<th>Используемый модуль:</th>
						<td>
							<select name="Module" id="Module" title="Используемый модуль">
<?php
		foreach($AllModelTypes as $Type){
			echo "<optgroup label=\"{$Type->Title}\">";
			foreach($Type->Modules as $Module){
				echo $HtmlTags->option($Module->Id,$Module->Title,$IsEdit?$PageInfo->Module==$Module->Id:false);
			}
			echo "</optgroup>";
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Изображение к странице:</th>
						<td><?=$HtmlTags->InputText("Cover","","","file","Cover","text")?></td>
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
			echo $HtmlTags->InputText($MoreDescription,$IsEdit?$PageInfo->$MoreDescription:"","","text",$MoreDescription,"text",254);
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
			echo $HtmlTags->InputText($MetaDescription,$IsEdit?$PageInfo->$MetaDescription:"","","text",$MetaDescription,"text",254);
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
			echo $HtmlTags->InputText($MetaKeywords,$IsEdit?$PageInfo->$MetaKeywords:"","","text",$MetaKeywords,"text",254);
		}
?>
						</td>
					</tr>
				</tbody>
			</table>
<?php
		/*
?>
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" class="tl tr">Фотографии</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>
							Добавить фотографии:
							<span class="hint">Можно выбрать сразу несколько фотографий</span>
						</th>
						<td><?=$HtmlTags->InputText("Photos[]","","","file","Photos","text",0,false,0,"","","","","","",false,true)?></td>
					</tr>
<?php
		if($IsEdit && count($PageInfo->Photos)){
?>
					<tr>
						<td colspan="2">
							<ul class="PhotoList" id="Photos">
<?php
			foreach($PageInfo->Photos as $Photo){
?>
								<li photoid="<?=$Photo->Id?>">
									<?=$HtmlTags->InputText("PhotoPos{$Photo->Id}",$Photo->Position,"","hidden")?>
									<?=$HtmlTags->InputText("PhotoStatus{$Photo->Id}","Y","","hidden")?>
									<img src="<?=$DIRS->Photos.$Photo->Id?>_4.jpg" alt="" title="" />
									<a href="#" class="delete" title="удалить фотографию"></a>
								</li>
<?php
			}
?>
							</ul>
						</td>
					</tr>
<?php
		}
?>
				</tbody>
			</table>
<?php
		*/
?>
		</div>
	</section>
	<?$Design->LoadEditor();?>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>