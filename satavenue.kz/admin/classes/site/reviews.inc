<?php
	if($GLOBAL->UserInfo->RightsAdmin!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление блогом");
	$ShowLanguages=true;
	$ReviewExist=false;

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

	$AllReviews=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Reviews}{$Filters} ORDER BY {$SortField} {$Direction};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($ReviewInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Reviews} WHERE Id={$Id};",false)){
			$ReviewInfo->Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='Review' AND `Owner`={$ReviewInfo->Id} ORDER BY `Position`;");

			$ReviewExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $ReviewExist){
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

		$DateTime=@$_POST["DateTime"];
		$DateTime=explode(" ",$DateTime);
		$DateTime[0]=@$DateTime[0];
		$DateTime[1]=@$DateTime[1];
		$DateTime[0]=explode("/",$DateTime[0]);
		$DateTime[1]=explode(":",$DateTime[1]);
		$DateTime=mktime((int) @$DateTime[1][0],(int) @$DateTime[1][1],0,(int) @$DateTime[0][1],(int) @$DateTime[0][0],(int) @$DateTime[0][2]);
		$SQL[]="`DateTime`={$DateTime}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Reviews} SET {$SQL};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Reviews,$LastId,"jpg",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $ReviewExist){
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

		$SQL[]="`Author`='".$Kernel->PostToSQL(@$_POST['Author'])."'";
		$SQL[]="`ShortDescription`='".$Kernel->PostToSQL(@$_POST['ShortDescription'])."'";


		$Name=$Kernel->PostToURL(@$_POST["Name"]);
		if(empty($Name))
			$Name=$Kernel->PostToURL($DefaultTitle);
		$SQL[]="`Name`='{$Name}'";

		$DateTime=@$_POST["DateTime"];
		$DateTime=explode(" ",$DateTime);
		$DateTime[0]=@$DateTime[0];
		$DateTime[1]=@$DateTime[1];
		$DateTime[0]=explode("/",$DateTime[0]);
		$DateTime[1]=explode(":",$DateTime[1]);
		$DateTime=mktime((int) @$DateTime[1][0],(int) @$DateTime[1][1],0,(int) @$DateTime[0][1],(int) @$DateTime[0][0],(int) @$DateTime[0][2]);
		$SQL[]="`DateTime`={$DateTime}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Reviews} SET {$SQL} WHERE Id={$ReviewInfo->Id};");

		if(!empty($_FILES["Cover"]["tmp_name"]))
			$Kernel->ClearThumbnails($DIRS->Reviews,$ReviewInfo->Id,"jpg",$_FILES["Cover"]);

		header("Location: ?EditForm&Id={$ReviewInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $ReviewInfo){
		$Status=(isset($_POST["Status{$ReviewInfo->Id}"]) && $_POST["Status{$ReviewInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Reviews} SET `Status`='{$Status}' WHERE Id={$ReviewInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllReviews as $Review){
			if(isset($_POST["Chk{$Review->Id}"]) && $_POST["Chk{$Review->Id}"]==$Review->Id){
				$DelIds[$Review->Id]=$Review->Id;
				$Kernel->ClearThumbnails($DIRS->Reviews,$Review->Id,"jpg",$_FILES["Cover"]);
			}

			unset($Review);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Reviews} WHERE Id IN ({$DelIds});");
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
<form action="?Delete" method="post" delquestion="выбранные записиы" id="GroupChecked">
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
							<select name="Status" id="Status" title="Статус записи">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllReviews as $Review){
?>
					<tr fid="<?=$Review->Id?>" id="Pos<?=$Review->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Review->Id}",$Review->Id,"",false,"","Chk{$Review->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Review->Id?>"><?=$Review->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Review->Id?>"><?=$Review->Title?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$Review->Id}","Y","",$Review->Status=="Y","","Status{$Review->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($Review);
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
<form action="?<?=$IsEdit?"Edit&Id={$ReviewInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> статьи:</h1>
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
			echo $HtmlTags->InputText($Title,$IsEdit?$ReviewInfo->$Title:"","","text",$Title,"text",0,false,0,"","","","","","",$Language->Prefix=="ru");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Каноническое название:</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$ReviewInfo->Name:"","","text","Name","text")?></td>
					</tr>
					<tr>
						<th>Статусы:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$ReviewInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Автор:</th>
						<td><?=$HtmlTags->InputText("Author",$IsEdit?$ReviewInfo->Author:"","","text","Author","text")?></td>
					</tr>
					<tr>
						<th>Короткое описание:</th>
						<td><?=$HtmlTags->InputText("ShortDescription",$IsEdit?$ReviewInfo->ShortDescription:"","","text","ShortDescription","text")?></td>
					</tr>
					<tr>
						<th>Обложка:</th>
						<td><?=$HtmlTags->InputText("Cover","","","file","Cover","text")?></td>
					</tr>
					<tr>
						<th>Дата и время публикации:</th>
						<td><?=$HtmlTags->InputText("DateTime",date("d/m/Y H:i",$IsEdit?$ReviewInfo->DateTime:time()),"","text","DateTime","text datetimepicker",16,false,17)?></td>
					</tr>
					<tr>
						<th>Описание:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Description="Description_{$Language->Prefix}";
			echo "<div id=\"{$Description}\">";
			echo $HtmlTags->textarea($Description,$IsEdit?$ReviewInfo->$Description:"",40,20,"","","{$Description}R","text");
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