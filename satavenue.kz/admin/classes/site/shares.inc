<?php
	if($GLOBAL->UserInfo->RightsShares!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление акциями");
	$ShowLanguages=true;
	$ShareExist=false;

	$Direction="DESC";
	$PageDirection="DESC";

	$SortField="DateTime";
	$SortMethod="DateTime {$Direction}";

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

	$AllShares=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Shares}{$Filters} ORDER BY {$SortField} {$Direction};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($ShareInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Shares} WHERE Id={$Id};",false)){
			$ShareInfo->Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='Share' AND `Owner`={$ShareInfo->Id} ORDER BY `Position`;");

			$ShareExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $ShareExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$DefaultTitle="";
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
			$Description="Description_{$Language->Prefix}";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
			if($Language->Id==$SortLanguage->Id)
				$DefaultTitle=$Kernel->PostToSQL(@$_POST[$Title]);
		}

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		if(empty($Name))
			$Name=$Kernel->PostToURL($DefaultTitle);
		$SQL[]="`Name`='{$Name}'";

		$SubTitle=$Kernel->PostToSQL(@$_POST["SubTitle"]);
		$SQL[]="`SubTitle`='{$SubTitle}'";

		$DateTime=@$_POST["DateTime"];
		$DateTime=explode(" ",$DateTime);
		$DateTime[0]=@$DateTime[0];
		$DateTime[1]=@$DateTime[1];
		$DateTime[0]=explode("/",$DateTime[0]);
		$DateTime[1]=explode(":",$DateTime[1]);
		$DateTime=mktime((int) @$DateTime[1][0],(int) @$DateTime[1][1],0,(int) @$DateTime[0][1],(int) @$DateTime[0][0],(int) @$DateTime[0][2]);
		$SQL[]="`DateTime`={$DateTime}";

		$FinishTime=@$_POST["FinishTime"];
		$FinishTime=explode(" ",$FinishTime);
		$FinishTime[0]=@$FinishTime[0];
		$FinishTime[1]=@$FinishTime[1];
		$FinishTime[0]=explode("/",$FinishTime[0]);
		$FinishTime[1]=explode(":",$FinishTime[1]);
		$FinishTime=mktime((int) @$FinishTime[1][0],(int) @$FinishTime[1][1],0,(int) @$FinishTime[0][1],(int) @$FinishTime[0][0],(int) @$FinishTime[0][2]);
		$SQL[]="`FinishTime`={$FinishTime}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Shares} SET {$SQL};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Shares,$LastId,"jpg",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $ShareExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$DefaultTitle="";
		foreach($AllLanguages as $Language){
			$Title="Title_{$Language->Prefix}";
			$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
			$Description="Description_{$Language->Prefix}";
			$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
			if($Language->Id==$SortLanguage->Id)
				$DefaultTitle=$Kernel->PostToSQL(@$_POST[$Title]);
		}

		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		if(empty($Name))
			$Name=$Kernel->PostToURL($DefaultTitle);
		$SQL[]="`Name`='{$Name}'";

		$SubTitle=$Kernel->PostToSQL(@$_POST["SubTitle"]);
		$SQL[]="`SubTitle`='{$SubTitle}'";

		$InMain=(isset($_POST["InMain"]) && $_POST["InMain"]=="Y")?"Y":"N";
		$SQL[]="`InMain`='{$InMain}'";

		$DateTime=@$_POST["DateTime"];
		$DateTime=explode(" ",$DateTime);
		$DateTime[0]=@$DateTime[0];
		$DateTime[1]=@$DateTime[1];
		$DateTime[0]=explode("/",$DateTime[0]);
		$DateTime[1]=explode(":",$DateTime[1]);
		$DateTime=mktime((int) @$DateTime[1][0],(int) @$DateTime[1][1],0,(int) @$DateTime[0][1],(int) @$DateTime[0][0],(int) @$DateTime[0][2]);
		$SQL[]="`DateTime`={$DateTime}";

		$FinishTime=@$_POST["FinishTime"];
		$FinishTime=explode(" ",$FinishTime);
		$FinishTime[0]=@$FinishTime[0];
		$FinishTime[1]=@$FinishTime[1];
		$FinishTime[0]=explode("/",$FinishTime[0]);
		$FinishTime[1]=explode(":",$FinishTime[1]);
		$FinishTime=mktime((int) @$FinishTime[1][0],(int) @$FinishTime[1][1],0,(int) @$FinishTime[0][1],(int) @$FinishTime[0][0],(int) @$FinishTime[0][2]);
		$SQL[]="`FinishTime`={$FinishTime}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Shares} SET {$SQL} WHERE Id={$ShareInfo->Id};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Shares,$ShareInfo->Id,"jpg",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$ShareInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $ShareInfo){
		$Status=(isset($_POST["Status{$ShareInfo->Id}"]) && $_POST["Status{$ShareInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Shares} SET `Status`='{$Status}' WHERE Id={$ShareInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllShares as $Share){
			if(isset($_POST["Chk{$Share->Id}"]) && $_POST["Chk{$Share->Id}"]==$Share->Id){
				$DelIds[$Share->Id]=$Share->Id;
				$Kernel->ClearThumbnails($DIRS->Shares,$Share->Id,"jpg",$_FILES["Cover"]);
			}

			unset($Share);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Shares} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные акции" id="GroupChecked">
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
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>Id</th>
						<th>Заголовок</th>
						<th>Дата создания</th>
						<th>Дата окончания</th>
						<th>Статус</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th></th>
						<th></th>
						<th>
							<select name="Status" id="Status" title="Статус акции">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllShares as $Share){
?>
					<tr fid="<?=$Share->Id?>" id="Pos<?=$Share->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Share->Id}",$Share->Id,"",false,"","Chk{$Share->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Share->Id?>"><?=$Share->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Share->Id?>"><?=$Share->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$Share->Id?>"><?=date("d.m.y",$Share->DateTime)?></a></td>
						<td><a href="?EditForm&Id=<?=$Share->Id?>"><?=date("d.m.y",$Share->FinishTime)?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Share->Id}","Y","",$Share->Status=="Y","","Status{$Share->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Share);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$ShareInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> акции:</h1>
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
			echo $HtmlTags->InputText($Title,$IsEdit?$ShareInfo->$Title:"","","text",$Title,"text",0,false,0,"","","","","","",$Language->Prefix=="ru");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Каноническое название:</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$ShareInfo->Name:"","","text","Name","text")?></td>
					</tr>
					<tr>
						<th>Подзаголовок:</th>
						<td><?=$HtmlTags->InputText("SubTitle",$IsEdit?$ShareInfo->SubTitle:"","","text","SubTitle","text")?></td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$ShareInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Обложка:</th>
						<td><?=$HtmlTags->InputText("Cover","","","file","Cover","text")?></td>
					</tr>
					<tr>
						<th>Выводить на главной:</th>
						<td><?=$HtmlTags->InputCheckbox("InMain","Y","активен",$IsEdit?$ShareInfo->InMain=="Y":true,"","InMain","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Дата и время публикации:</th>
						<td><?=$HtmlTags->InputText("DateTime",date("d/m/Y H:i",$IsEdit?$ShareInfo->DateTime:time()),"","text","DateTime","text datetimepicker",16,false,17)?></td>
					</tr>
					<tr>
						<th>Дата и время завершения:</th>
						<td><?=$HtmlTags->InputText("FinishTime",date("d/m/Y H:i",$IsEdit?$ShareInfo->FinishTime:time()),"","text","FinishTime","text datetimepicker",16,false,17)?></td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Description="Description_{$Language->Prefix}";
			echo "<div id=\"{$Description}\">";
			echo $HtmlTags->textarea($Description,$IsEdit?$ShareInfo->$Description:"",40,20,"","","{$Description}R","text");
			echo "</div>";
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