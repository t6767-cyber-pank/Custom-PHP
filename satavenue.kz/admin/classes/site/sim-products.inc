<?php
if($GLOBAL->UserInfo->RightsSimTypes!=="Y"){
	header("Location: /admin/");
	exit;
}
$ParentId=0;
$Crumbs=array("Управление sim услугами");
$LanguageExist=false;
$ShowLanguages=true;
$Direction="ASC";
$PageDirection="ASC";
$SortField="Position";
$SortMethod="Position {$Direction}";
if(isset($_GET["Sort"])){
	$Direction=(isset($_GET["Direction"])&&$_GET["Direction"]=="DESC")?"DESC":"ASC";
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
	$Id=(int)@$_GET["Id"];
	if(!empty($Id)){
		$Filters[]="`Id`={$Id}";
		$FiltersURL[]="Id={$Id}";
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
$SimProductList=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title,PriceValue_{$SortLanguage->Prefix} AS PriceValue FROM {$GLOBAL->SimProducts}{$Filters} ORDER BY {$SortMethod};");
$SimTypes=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimTypes} ORDER BY Title;");
$SimCategories=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimCategories} ORDER BY Title;");
$SimPages=$DataBase->Query("SELECT *,MenuTitle_{$SortLanguage->Prefix} AS MenuTitle FROM {$GLOBAL->SystemPages} WHERE `Module`=463 ORDER BY MenuTitle;");
if(isset($_GET["Id"])){
	$Id=(int)$_GET["Id"];
	if($SimProductInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SimProducts} WHERE Id={$Id};",false)){
		$SimProductExist=true;
	}
}
if(isset($_GET["AddForm"])){
	$ShowForm=true;
	$ShowList=false;
}
elseif(isset($_GET["EditForm"])&&$SimProductExist){
	$ShowForm=true;
	$ShowList=false;
	$IsEdit=true;
}
elseif(isset($_GET["Add"])){
	$SQL=array();
	$Position=$DataBase->Query("SELECT MAX(Position) AS Position FROM {$GLOBAL->SimProducts};",false);
	$Position=((int)@$Position->Position)+1;
	$SQL[]="`Position`={$Position}";
	$Status=(isset($_POST["Status"])&&$_POST["Status"]=="Y")?"Y":"N";
	$SQL[]="`Status`='{$Status}'";
	$TypeId=(int)@$_POST["TypeId"];
	$SQL[]="`TypeId`={$TypeId}";
	$CategoryId=(int)@$_POST["CategoryId"];
	$SQL[]="`CategoryId`={$CategoryId}";
	$PageId=(int)@$_POST["PageId"];
	$SQL[]="`PageId`={$PageId}";
	$Price=(int)@$_POST["Price"];
	$SQL[]="`Price`={$Price}";
	$VaucherTime=(int)@$_POST["VaucherTime"];
	$SQL[]="`VaucherTime`={$VaucherTime}";
	$PriceMinute=(int)@$_POST["PriceMinute"];
	$SQL[]="`PriceMinute`={$PriceMinute}";
	$PriceSeconds=(int)@$_POST["PriceSeconds"];
	$SQL[]="`PriceSeconds`={$PriceSeconds}";
	foreach($AllLanguages as $Language){
		$Title="Title_{$Language->Prefix}";
		$PriceValue="PriceValue_{$Language->Prefix}";
		$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
		$SQL[]="`{$PriceValue}`='".$Kernel->PostToSQL(@$_POST[$PriceValue])."'";
	}
	$SQL=join(",",$SQL);
	$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SimProducts} SET {$SQL};");
	header("Location: ?EditForm&Id={$LastId}&inserted");
	exit();
}
elseif(isset($_GET["Edit"])&&$SimProductExist){
	$SQL=array();
	$Status=(isset($_POST["Status"])&&$_POST["Status"]=="Y")?"Y":"N";
	$SQL[]="`Status`='{$Status}'";
	$TypeId=(int)@$_POST["TypeId"];
	$SQL[]="`TypeId`={$TypeId}";

	$CategoryId=(int)@$_POST["CategoryId"];
	$SQL[]="`CategoryId`={$CategoryId}";
	$PageId=(int)@$_POST["PageId"];
	$SQL[]="`PageId`={$PageId}";
	$Price=(int)@$_POST["Price"];
	$SQL[]="`Price`={$Price}";
	$VaucherTime=(int)@$_POST["VaucherTime"];
	$SQL[]="`VaucherTime`={$VaucherTime}";
	$PriceMinute=(int)@$_POST["PriceMinute"];
	$SQL[]="`PriceMinute`={$PriceMinute}";
	$PriceSeconds=(int)@$_POST["PriceSeconds"];
	$SQL[]="`PriceSeconds`={$PriceSeconds}";
	foreach($AllLanguages as $Language){
		$Title="Title_{$Language->Prefix}";
		$PriceValue="PriceValue_{$Language->Prefix}";
		$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
		$SQL[]="`{$PriceValue}`='".$Kernel->PostToSQL(@$_POST[$PriceValue])."'";
	}
	$SQL=join(",",$SQL);
	$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimProducts} SET {$SQL} WHERE Id={$SimProductInfo->Id};");
	header("Location: ?EditForm&Id={$SimProductInfo->Id}&updated");
	exit();
}
elseif(isset($_GET["Delete"])&&$CanDelete){
	$DelIds=array();
	foreach($SimProductList as $SimProduct){
		if(isset($_POST["Chk{$SimProduct->Id}"])&&$_POST["Chk{$SimProduct->Id}"]==$SimProduct->Id){
			$DelIds[$SimProduct->Id]=$SimProduct->Id;
		}
		unset($SimProduct);
	}
	if(count($DelIds)){
		$DelIds=join(",",$DelIds);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SimProducts} WHERE Id IN ({$DelIds});");
	}
	header("Location: ?deleted");
	exit();
}
elseif(isset($_GET["ChangePosition"])){
	$Positions=explode(",",$Positions);
	foreach($Positions as $Position){
		$Position=explode("-",$Position);
		$ItemId=$Position[0];
		$Position=$Position[1];
		$ItemId=substr($ItemId,3);
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimProducts} SET `Position`={$Position} WHERE Id={$ItemId};");
	}
	exit();
}
elseif(isset($_GET["ChangeStatus"])&&$SimProductExist){
	$Status=(isset($_POST["Status{$SimProductInfo->Id}"])&&$_POST["Status{$SimProductInfo->Id}"]=="Y")?"Y":"N";
	$DataBase->UpdateQuery("UPDATE {$GLOBAL->SimProducts} SET `Status`='{$Status}' WHERE Id={$SimProductInfo->Id};");
	exit();
}
$SimProductCount=$DataBase->Query("SELECT COUNT(*) AS SimProducts FROM {$GLOBAL->SimProducts};",false);
$SimProductCount=(int)@$SimProductCount->SimProducts;
$Crumbs=join(" / ",$Crumbs);
?>
<? $Design->LoadHeader($Crumbs); ?>
<?php
if($ShowList){
	$RowWidth=round(100/count($AllLanguages));
	?>
	<form action="?Delete" method="post" delquestion="выбранные услуги" id="GroupChecked">
		<section id="listBlock">
			<div class="title" id="formTitle">
				<h1><?=$Crumbs?></h1>
				<div class="buttons">
					<a href="?AddForm" class="button">Добавить</a>
					<button class="button filter" id="filter">Применить фильтр</button>
					<button class="button delete">Удалить</button>
					<div class="pages">
						<span>Всего услуг:
							<strong><?=$SimProductCount?></strong>
						</span>
					</div>
				</div>
			</div>
			<div class="container">
				<table class="table" id="Positions">
					<colgroup>
						<col/>
						<col/>
						<col width="60%"/>
						<col width="20%"/>
						<col width="20%"/>
					</colgroup>
					<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>
							<a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a>
						</th>
						<th>
							<a href="?Sort=Title&Direction=<?=$Direction?><?=$FiltersURL?>">Название<?=$SortField=="Title"?$DirectionArrow:""?></a>
						</th>
						<th>
							<a href="?Sort=PriceValue&Direction=<?=$Direction?><?=$FiltersURL?>">Стоимость<?=$SortField=="PriceValue"?$DirectionArrow:""?></a>
						</th>
						<th>
							<a href="?Sort=Price&Direction=<?=$Direction?><?=$FiltersURL?>">Стоимость<?=$SortField=="Price"?$DirectionArrow:""?></a>
						</th>
						<th>Статус</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th><?=$HtmlTags->InputText("PriceValue",@$_GET["PriceValue"],"","text","PriceValue","text")?></th>
						<th><?=$HtmlTags->InputText("Price",@$_GET["Price"],"","text","Price","text")?></th>
						<th>
							<select name="Status" id="Status" title="Статус">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($SimProductList as $SimProduct){
						?>
						<tr fid="<?=$SimProduct->Id?>" id="Pos<?=$SimProduct->Id?>">
							<td><?=$HtmlTags->InputCheckbox("Chk{$SimProduct->Id}",$SimProduct->Id,"",false,"","Chk{$SimProduct->Id}","",true,"checkbox",true)?></td>
							<td>
								<a href="?EditForm&Id=<?=$SimProduct->Id?>"><?=$SimProduct->Id?></a>
							</td>
							<td>
								<a href="?EditForm&Id=<?=$SimProduct->Id?>"><?=$SimProduct->Title?></a>
							</td>
							<td>
								<a href="?EditForm&Id=<?=$SimProduct->Id?>"><?=$SimProduct->PriceValue?></a>
							</td>
							<td>
								<a href="?EditForm&Id=<?=$SimProduct->Id?>"><?=$SimProduct->Price?></a>
							</td>
							<td><?=$HtmlTags->InputCheckbox("Status{$SimProduct->Id}","Y","",$SimProduct->Status=="Y","","Status{$SimProduct->Id}","",true,"checkbox-slider",true)?></td>
						</tr>
						<?php
						unset($SimProduct);
					}
					?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
					</tfoot>
				</table>
				<script type="text/javascript">
					$(document).ready(function(){
						function SavePositions(InputPositions){
							$.ajax({
								type:"POST",
								cache:false,
								data:'Positions='+InputPositions,
								url:'?ChangePosition',
								success:function(data){
									$("#Positions tbody tr").removeClass('line');
									$("#Positions tbody tr:even").addClass('line');
								}
							});
						}

						$("#Positions").tableDnD({
							onDragClass:"current",
							onDrop:function(table,row){
								for(var j=0; j<table.tBodies.length; j++){
									var rows=table.tBodies[j].rows;
									var ItemsPositions=Array();
									for(var i=0; i<rows.length; i++){
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
}
elseif($ShowForm){
	?>
	<form action="?<?=$IsEdit?"Edit&Id={$SimProductInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
		<section>
			<div class="title" id="formTitle">
				<h1><?=$IsEdit?"Редактирование существующей":"Добавление новой"?> услуги:</h1>
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
						<th>Статус:</th>
						<td><?=$HtmlTags->InputCheckbox("Status","Y","включен",$IsEdit?$SimProductInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?></td>
					</tr>
					<tr>
						<th>Тип:</th>
						<td>
							<select name="TypeId" id="TypeId">
								<?php
								foreach($SimTypes as $Type){
									echo $HtmlTags->option($Type->Id,$Type->Title,$IsEdit?$SimProductInfo->TypeId==$Type->Id:false);
								}
								?>
							</select>
						</td>
					</tr>

					<tr>
						<th>Категория:</th>
						<td>
							<select name="CategoryId" id="CategoryId">
								<?php
								foreach($SimCategories as $Category){
									echo $HtmlTags->option($Category->Id,$Category->Title,$IsEdit?$SimProductInfo->CategoryId==$Category->Id:false);
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Целевая страница:</th>
						<td>
							<select name="PageId" id="PageId">
								<?php
								foreach($SimPages as $Page){
									echo $HtmlTags->option($Page->Id,$Page->MenuTitle,$IsEdit?$SimProductInfo->PageId==$Page->Id:false);
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Название:</th>
						<td>
							<?php
							foreach($AllLanguages as $Language){
								$Title="Title_{$Language->Prefix}";
								echo $HtmlTags->InputText($Title,$IsEdit?$SimProductInfo->$Title:"","","text",$Title,"text",0,false,0,"","","","","","",$Language->Root=="Y");
							}
							?>
						</td>
					</tr>
					<tr>
						<th>Стоимость:</th>
						<td><?=$HtmlTags->InputText("Price",$IsEdit?$SimProductInfo->Price:0,"","number","Price","text",11,false,0,"","","","","","Например: 1000")?> тг.</td>
					</tr>
					<tr>
						<th>Написание стоимости:</th>
						<td>
							<?php
							foreach($AllLanguages as $Language){
								$PriceValue="PriceValue_{$Language->Prefix}";
								echo $HtmlTags->InputText($PriceValue,$IsEdit?$SimProductInfo->$PriceValue:"","","text",$PriceValue,"text");
							}
							?>
						</td>
					</tr>
					<tr>
						<th>Время действия :</th>
						<td><?=$HtmlTags->InputText("VaucherTime",$IsEdit?$SimProductInfo->VaucherTime:0,"","number","VaucherTime","text",11,false,0,"","","","","","Например: 1")?> мес.</td>
					</tr>
					<tr>
						<th>Стоимость зв минуту:</th>
						<td><?=$HtmlTags->InputText("PriceMinute",$IsEdit?$SimProductInfo->PriceMinute:0,"","number","PriceMinute","text",11,false,0,"","","","","","Например: 5")?> ед.</td>
					</tr>
					<tr>
						<th>Стоимость за 20 секунд:</th>
						<td><?=$HtmlTags->InputText("PriceSeconds",$IsEdit?$SimProductInfo->PriceSeconds:0,"","number","PriceSeconds","text",11,false,0,"","","","","","Например: 5")?> ед.</td>
					</tr>
					</tbody>
				</table>
			</div>
		</section>
	</form>
<?php
}
?>
<? $Design->LoadFooter(); ?>