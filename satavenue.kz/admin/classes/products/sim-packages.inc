<?php
	if($GLOBAL->UserInfo->RightsProducts!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление пакетами для пополнения баланса");
	$ShowLanguages=true;
	$SimPackageExist=false;

	$Direction="DESC";
	$PageDirection="DESC";

	$SortField="`Position`";
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
		$Units=(int)@$_GET["Units"];
		if(!empty($Units)){
			$Filters[]="Units={$Units}";
			$FiltersURL[]="Units={$Units}";
		}
		$BrandId=(int)@$_GET["BrandId"];
		if(!empty($BrandId)){
			$Filters[]="BrandId={$BrandId}";
			$FiltersURL[]="BrandId={$BrandId}";
		}
		$Price=(int)@$_GET["Price"];
		if(!empty($Price)){
			$Filters[]="Price={$Price}";
			$FiltersURL[]="Price={$Price}";
		}
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllBrands=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Brands} ORDER BY Title;");
	$AllSimPackages=$DataBase->Query("SELECT *,Period_{$SortLanguage->Prefix} AS Period FROM {$GLOBAL->SimPackages}{$Filters} ORDER BY {$SortField} {$Direction};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];

		if($SimPackageInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SimPackages} WHERE Id={$Id};",false)){
			$SimPackageExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $SimPackageExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->SimPackages};",false);
		$Position=((int) @$Position->Position)+1;
		$SQL[]="`Position`={$Position}";

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$BrandId=(int) @$_POST["BrandId"];
		$SQL[]="`BrandId`={$BrandId}";

		$Units=(int) @$_POST["Units"];
		$SQL[]="`Units`={$Units}";

		$Price=(int) @$_POST["Price"];
		$SQL[]="`Price`={$Price}";

		foreach($AllLanguages as $Language){
			$Period="Period_{$Language->Prefix}";
			$Message="Message_{$Language->Prefix}";
			$SQL[]="`{$Period}`='".$Kernel->PostToSQL(@$_POST[$Period])."'";
			$SQL[]="`{$Message}`='".$Kernel->PostToSQL(@$_POST[$Message])."'";
		}

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SimPackages} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $SimPackageExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$BrandId=(int) @$_POST["BrandId"];
		$SQL[]="`BrandId`={$BrandId}";

		$Units=(int) @$_POST["Units"];
		$SQL[]="`Units`={$Units}";

		$Price=(int) @$_POST["Price"];
		$SQL[]="`Price`={$Price}";

		foreach($AllLanguages as $Language){
			$Period="Period_{$Language->Prefix}";
			$Message="Message_{$Language->Prefix}";
			$SQL[]="`{$Period}`='".$Kernel->PostToSQL(@$_POST[$Period])."'";
			$SQL[]="`{$Message}`='".$Kernel->PostToSQL(@$_POST[$Message])."'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimPackages} SET {$SQL} WHERE Id={$SimPackageInfo->Id};");

		header("Location: ?EditForm&Id={$SimPackageInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $SimPackageInfo){
		$Status=(isset($_POST["Status{$SimPackageInfo->Id}"]) && $_POST["Status{$SimPackageInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimPackages} SET `Status`='{$Status}' WHERE Id={$SimPackageInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllSimPackages as $SimPackage){
			if(isset($_POST["Chk{$SimPackage->Id}"]) && $_POST["Chk{$SimPackage->Id}"]==$SimPackage->Id){
				$DelIds[$SimPackage->Id]=$SimPackage->Id;
			}

			unset($SimPackage);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SimPackages} WHERE Id IN ({$DelIds});");
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
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimPackages} SET `Position`={$Position} WHERE Id={$ItemId};");
		}
		exit();
	}

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные пакеты" id="GroupChecked">
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
					<col width="40%" />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>Id</th>
						<th>Бренд</th>
						<th>Период</th>
						<th>Юнитов</th>
						<th>Стоимость</th>
						<th>Статус</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th>
							<select name="BrandId">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
<?php
		foreach($AllBrands as $Brand){
			echo $HtmlTags->option($Brand->Id,$Brand->Title,$Brand->Id==@$_GET["BrandId"]);
		}
?>
							</select>
						</th>
						<th><?=$HtmlTags->InputText("Period",@$_GET["Period"],"","text","Period","text")?></th>
						<th><?=$HtmlTags->InputText("Units",@$_GET["Units"],"","text","Units","text")?></th>
						<th><?=$HtmlTags->InputText("Price",@$_GET["Price"],"","text","Price","text")?></th>
						<th>
							<select name="Status" id="Status" title="Статус пакета">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllSimPackages as $SimPackage){
?>
					<tr fid="<?=$SimPackage->Id?>" id="Pos<?=$SimPackage->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$SimPackage->Id}",$SimPackage->Id,"",false,"","Chk{$SimPackage->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$SimPackage->Id?>"><?=$SimPackage->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$SimPackage->Id?>"><?=$AllBrands[$SimPackage->BrandId]->Title?></a></td>
						<td><a href="?EditForm&Id=<?=$SimPackage->Id?>"><?=$SimPackage->Period?></a></td>
						<td><a href="?EditForm&Id=<?=$SimPackage->Id?>"><?=$SimPackage->Units?></a></td>
						<td><a href="?EditForm&Id=<?=$SimPackage->Id?>"><?=$SimPackage->Price?></a></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$SimPackage->Id}","Y","",$SimPackage->Status=="Y","","Status{$SimPackage->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($SimPackage);
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
<form action="?<?=$IsEdit?"Edit&Id={$SimPackageInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> пакета:</h1>
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
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","включен",$IsEdit?$SimPackageInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Бренд:</th>
						<td>
							<select name="BrandId" id="BrandId">
<?php
		foreach($AllBrands as $Brand){
			echo $HtmlTags->option($Brand->Id,$Brand->Title,$IsEdit?$SimPackageInfo->BrandId==$Brand->Id:false);
		}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Число юнитов:</th>
						<td><?=$HtmlTags->InputText("Units",$IsEdit?$SimPackageInfo->Units:"","","number","Units","text",11,false,0,"","","","min=\"0\"","","")?></td>
					</tr>
					<tr>
						<th>Стоимость:</th>
						<td><?=$HtmlTags->InputText("Price",$IsEdit?$SimPackageInfo->Price:0,"","number","Price","text",11,false,0,"","","","min=\"0\"","","",true)?> тг.</td>
					</tr>
					<tr>
						<th>Период:</th>
						<td>
<?php
		foreach($AllLanguages as $Language){
			$Period="Period_{$Language->Prefix}";
			echo $HtmlTags->InputText($Period,$IsEdit?$SimPackageInfo->$Period:"","","text",$Period,"text");
		}
?>
						</td>
					</tr>
					<tr>
						<th>Примечание:</th>
						<td>
							<?php
							foreach($AllLanguages as $Language){
								$Message="Message_{$Language->Prefix}";
								echo $HtmlTags->InputText($Message,$IsEdit?$SimPackageInfo->$Message:"","","text",$Message,"text");
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