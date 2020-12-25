<?php
if($GLOBAL->UserInfo->RightsProducts!=="Y"){
	header("Location: /admin/");
	exit;
}
$ParentId=0;
$Crumbs=array("Управление продукцией");
$ShowLanguages=true;
$ProductExist=false;
$Direction="ASC";
$PageDirection="ASC";
$SortField="Title";
$SortMethod="Title_ru {$Direction}";
$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";
$Filters=array();
$FiltersURL=array();
if(isset($_GET["Filter"])){
	$Id=(int)@$_GET["Id"];
	if(!empty($Id)){
		$Filters[]="`Id`={$Id}";
		$FiltersURL[]="Id={$Id}";
	}
	$Title=$Kernel->PostToSQL(@$_GET["Title"]);
	if(!empty($Title)){
		$Filters[]="`Title_{$SortLanguage->Prefix}` LIKE '%{$Title}%'";
		$FiltersURL[]="Title={$Title}";
	}
	$Status=$Kernel->PostToSQL(@$_GET["Status"]);
	if(!empty($Status)){
		$Filters[]="`Status`='{$Status}'";
		$FiltersURL[]="Status={$Status}";
	}
	$StockStatus=$Kernel->PostToSQL(@$_GET["StockStatus"]);
	if(!empty($StockStatus)){
		$Filters[]="`StockStatus`='{$StockStatus}'";
		$FiltersURL[]="StockStatus={$StockStatus}";
	}
}
$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";
$AllProducts=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Products}{$Filters} ORDER BY {$SortField} {$Direction};");
$AllCategories=$DataBase->Query("SELECT * FROM {$GLOBAL->Categories};");
$AllBrands=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Brands} ORDER BY Title;");
$AllSimProducts=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimProducts} ORDER BY Title;");
$AllSimCategories=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimCategories} ORDER BY Title;");
$AllSimTypes=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimTypes} ORDER BY Title;");
$AllProductOffers=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->ProductOffers} ORDER BY Title;");
if(isset($_GET["Id"])){
	$Id=(int)$_GET["Id"];
	if($ProductInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Products} WHERE Id={$Id};",false)){
		$ProductInfo->Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner`={$ProductInfo->Id} ORDER BY `Position`;");
		$ProductInfo->Categories=$DataBase->Query("SELECT * FROM {$GLOBAL->ProductToCategory} WHERE `ProductId`={$ProductInfo->Id};",true,array(),"CategoryId");
		foreach($ProductInfo->Categories as $Category){
			$ProductInfo->Categories[$Category->CategoryId]=$Category->CategoryId;
		}
		$ProductInfo->Options=$DataBase->Query("SELECT * FROM {$GLOBAL->ProductOptions} WHERE `ProductId`={$ProductInfo->Id};",true,array(),"SimProductId");
		$ProductInfo->Offers=$DataBase->Query("SELECT * FROM {$GLOBAL->ProductToOffers} WHERE `ProductId`={$ProductInfo->Id};",true,array(),"OfferId");
		$ProductExist=true;
	}
}
if(isset($_GET["AddForm"])){
	$ShowForm=true;
	$ShowList=false;
}
elseif(isset($_GET["EditForm"])&&$ProductExist){
	$ShowForm=true;
	$ShowList=false;
	$IsEdit=true;
}
elseif(isset($_GET["Add"])){
	$SQL=array();
	$Status=(isset($_POST["Status"])&&$_POST["Status"]=="Y")?"Y":"N";
	$SQL[]="`Status`='{$Status}'";
$Type=$Kernel->PostToSQL(@$_POST["Type"]);
$SQL[]="`Type`='{$Type}'";
	$BestCost=(isset($_POST["BestCost"])&&$_POST["BestCost"]=="Y")?"Y":"N";
	$SQL[]="`BestCost`='{$BestCost}'";
	$HasGift=(isset($_POST["HasGift"])&&$_POST["HasGift"]=="Y")?"Y":"N";
	$SQL[]="`HasGift`='{$HasGift}'";
	$StockStatus=(isset($_POST["StockStatus"])&&$_POST["StockStatus"]=="IN")?"IN":"ORDER";
	$SQL[]="`StockStatus`='{$StockStatus}'";
	$IsSell=(isset($_POST["IsSell"])&&$_POST["IsSell"]=="Y")?"Y":"N";
	$SQL[]="`IsSell`='{$IsSell}'";
	$IsView=(isset($_POST["IsView"])&&$_POST["IsView"]=="Y")?"Y":"N";
	$SQL[]="`IsView`='{$IsView}'";
	$Price=(int)@$_POST["Price"];
	$SQL[]="`Price`={$Price}";
	$OldPrice=(int)@$_POST["OldPrice"];
	$SQL[]="`OldPrice`={$OldPrice}";
	$MaxAmount=(int)@$_POST["MaxAmount"];
	$SQL[]="`MaxAmount`={$MaxAmount}";
	$BrandId=(int)@$_POST["BrandId"];
	$SQL[]="`BrandId`={$BrandId}";
	$RecommendedProducts = json_encode(@$_POST["RecommendedProducts"]);
	$SQL[]="`RecommendedProducts`={$RecommendedProducts}";
	$DefaultTitle="";
	foreach($AllLanguages as $Language){
		$Title="Title_{$Language->Prefix}";
		$Annotation="Annotation_{$Language->Prefix}";
		$ShortDescription="ShortDescription_{$Language->Prefix}";
		$Description="Description_{$Language->Prefix}";
		$Characteristics="Characteristics_{$Language->Prefix}";
		$Map="Map_{$Language->Prefix}";
		$Rules="Rules_{$Language->Prefix}";
		$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
		$SQL[]="`{$Annotation}`='".$Kernel->PostToSQL(@$_POST[$Annotation],true,true,512)."'";
		$SQL[]="`{$ShortDescription}`='".$Kernel->PostToSQL(@$_POST[$ShortDescription],false,false)."'";
		$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
		$SQL[]="`{$Characteristics}`='".$Kernel->PostToSQL(@$_POST[$Characteristics],false,false)."'";
		$SQL[]="`{$Map}`='".$Kernel->PostToSQL(@$_POST[$Map],false,false)."'";
		$SQL[]="`{$Rules}`='".$Kernel->PostToSQL(@$_POST[$Rules],false,false)."'";
		if($Language->Id==$SortLanguage->Id)
			$DefaultTitle=$Kernel->PostToSQL(@$_POST[$Title]);
	}
	$Name=$Kernel->PostToURL(@$_POST["Name"]);
	if(empty($Name))
		$Name=$Kernel->PostToURL($DefaultTitle);
	$SQL[]="`Name`='{$Name}'";
	$SQL=join(",",$SQL);
	$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Products} SET {$SQL};");
	foreach($AllCategories as $Category){
		if(isset($_POST["Category{$Category->Id}"])&&$_POST["Category{$Category->Id}"]==$Category->Id)
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductToCategory} SET `ProductId`={$LastId},`CategoryId`={$Category->Id};");
		unset($Category);
	}
	$Position=0;
	foreach($_FILES["Photos"]["tmp_name"] as $Photo){
		if(!empty($Photo)){
			$PhotoId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Photos} SET `Type`='PRODUCT',`Owner`={$LastId},`Position`={$Position};");
			$Kernel->ClearThumbnails($DIRS->Photos,$PhotoId,"jpg",$Photo);
			$Position++;
		}
	}
	foreach($AllSimProducts as $Product){
		$Price=(int)@$_POST["Product{$Product->Id}"];
		if(!empty($Price))
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductOptions} SET `ProductId`={$LastId},`SimProductId`={$Product->Id},`Price`={$Price};");
		unset($Product);
	}
	foreach($AllProductOffers as $Offer){
		if(isset($_POST["Offer{$Offer->Id}"])&&$_POST["Offer{$Offer->Id}"]==$Offer->Id)
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductToOffers} SET `ProductId`={$LastId},`OfferId`={$Offer->Id};");
	}
	header("Location: ?EditForm&Id={$LastId}&updated");
	exit();
}
elseif(isset($_GET["Edit"])&&$ProductExist){
	$SQL=array();
	$Status=(isset($_POST["Status"])&&$_POST["Status"]=="Y")?"Y":"N";
	$SQL[]="`Status`='{$Status}'";
	$Type=$Kernel->PostToSQL(@$_POST["Type"]);
	$SQL[]="`Type`='{$Type}'";
	$HasGift=(isset($_POST["HasGift"])&&$_POST["HasGift"]=="Y")?"Y":"N";
	$SQL[]="`HasGift`='{$HasGift}'";
	$BestCost=(isset($_POST["BestCost"])&&$_POST["BestCost"]=="Y")?"Y":"N";
	$SQL[]="`BestCost`='{$BestCost}'";
	$StockStatus=(isset($_POST["StockStatus"])&&$_POST["StockStatus"]=="IN")?"IN":"ORDER";
	$SQL[]="`StockStatus`='{$StockStatus}'";
	$IsSell=(isset($_POST["IsSell"])&&$_POST["IsSell"]=="Y")?"Y":"N";
	$SQL[]="`IsSell`='{$IsSell}'";
	$IsView=(isset($_POST["IsView"])&&$_POST["IsView"]=="Y")?"Y":"N";
	$SQL[]="`IsView`='{$IsView}'";
	$Price=(int)@$_POST["Price"];
	$SQL[]="`Price`={$Price}";
	$OldPrice=(int)@$_POST["OldPrice"];
	$SQL[]="`OldPrice`={$OldPrice}";
	$MaxAmount=(int)@$_POST["MaxAmount"];
	$SQL[]="`MaxAmount`={$MaxAmount}";
	$BrandId=(int)@$_POST["BrandId"];
	$SQL[]="`BrandId`={$BrandId}";
	$RecommendedProducts = json_encode(@$_POST["RecommendedProducts"]);
	$SQL[]="`RecommendedProducts`='{$RecommendedProducts}'";
	$DefaultTitle="";
	foreach($AllLanguages as $Language){
		$Title="Title_{$Language->Prefix}";
		$Annotation="Annotation_{$Language->Prefix}";
		$ShortDescription="ShortDescription_{$Language->Prefix}";
		$Description="Description_{$Language->Prefix}";
		$Characteristics="Characteristics_{$Language->Prefix}";
		$Map="Map_{$Language->Prefix}";
		$Rules="Rules_{$Language->Prefix}";
		$SQL[]="`{$Title}`='".$Kernel->PostToSQL(@$_POST[$Title])."'";
		$SQL[]="`{$Annotation}`='".$Kernel->PostToSQL(@$_POST[$Annotation],true,true,512)."'";
		$SQL[]="`{$ShortDescription}`='".$Kernel->PostToSQL(@$_POST[$ShortDescription],false,false)."'";
		$SQL[]="`{$Description}`='".$Kernel->PostToSQL(@$_POST[$Description],false,false)."'";
		$SQL[]="`{$Characteristics}`='".$Kernel->PostToSQL(@$_POST[$Characteristics],false,false)."'";
		$SQL[]="`{$Map}`='".$Kernel->PostToSQL(@$_POST[$Map],false,false)."'";
		$SQL[]="`{$Rules}`='".$Kernel->PostToSQL(@$_POST[$Rules],false,false)."'";
		if($Language->Id==$SortLanguage->Id)
			$DefaultTitle=$Kernel->PostToSQL(@$_POST[$Title]);
	}
	$Name=$Kernel->PostToURL(@$_POST["Name"]);
	if(empty($Name))
		$Name=$Kernel->PostToURL($DefaultTitle);
	$SQL[]="`Name`='{$Name}'";
	$SQL=join(",",$SQL);
	$DataBase->UpdateQuery("UPDATE {$GLOBAL->Products} SET {$SQL} WHERE Id={$ProductInfo->Id};");
	foreach($ProductInfo->Categories as $Category){
		if(isset($_POST["Category{$Category}"])&&$_POST["Category{$Category}"]==$Category){
			unset($ProductInfo->Categories[$Category]);
			unset($AllCategories[$Category]);
		}
	}
	$ProductInfo->Categories=join(",",$ProductInfo->Categories);
	if(!empty($ProductInfo->Categories))
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductToCategory} WHERE `ProductId`={$ProductInfo->Id} AND `CategoryId` IN ({$ProductInfo->Categories});");
	foreach($AllCategories as $Category){
		if(isset($_POST["Category{$Category->Id}"])&&$_POST["Category{$Category->Id}"]=$Category->Id){
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductToCategory} SET `ProductId`={$ProductInfo->Id},`CategoryId`={$Category->Id};");
		}
	}
	$LastPosition=0;
	foreach($ProductInfo->Photos as $Photo){
		if(!isset($_POST["PhotoStatus{$Photo->Id}"])||@$_POST["PhotoStatus{$Photo->Id}"]=="N"){
			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Photos} WHERE Id={$Photo->Id};");
			$Kernel->ClearThumbnails($DIRS->Photos,$Photo->Id,"jpg");
		}
		else{
			$Position=(int)@$_POST["PhotoPos{$Photo->Id}"];
			if($Position!==$Photo->Position)
				$DataBase->UpdateQuery("UPDATE {$GLOBAL->Photos} SET `Position`={$Position} WHERE Id={$Photo->Id};");
			$LastPosition=$LastPosition<$Position?$Position:$LastPosition;
		}
	}
	$Position=$LastPosition;
	foreach($_FILES["Photos"]["tmp_name"] as $Photo){
		if(!empty($Photo)){
			$PhotoId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Photos} SET `Type`='PRODUCT',`Owner`={$ProductInfo->Id},`Position`={$Position};");
			$Kernel->ClearThumbnails($DIRS->Photos,$PhotoId,"jpg",$Photo);
			$Position++;
		}
	}
	foreach($ProductInfo->Options as $Option){
		$Price=(int)@$_POST["Product{$Option->SimProductId}"];
		if(!empty($Price)){
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->ProductOptions} SET `Price`={$Price} WHERE Id={$Option->Id};");
			unset($ProductInfo->Options[$Option->SimProductId]);
		}
		unset($Option);
	}
	foreach($AllSimProducts as $Product){
		$Price=(int)@$_POST["Product{$Product->Id}"];
		if(!empty($Price))
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductOptions} SET `ProductId`={$ProductInfo->Id},`SimProductId`={$Product->Id},`Price`={$Price};");
		unset($Product);
	}
	if(count($ProductInfo->Options)){
		$ProductInfo->Options=array_keys($ProductInfo->Options);
		$ProductInfo->Options=join(",",$ProductInfo->Options);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductOptions} WHERE `ProductId`={$ProductInfo->Id} AND `SimProductId` IN ({$ProductInfo->Options});");
	}
	foreach($ProductInfo->Offers as $Offer){
		if(isset($_POST["Offer{$Offer->OfferId}"])&&$_POST["Offer{$Offer->OfferId}"]==$Offer->OfferId){
			unset($ProductInfo->Offers[$Offer->OfferId]);
			unset($AllProductOffers[$Offer->OfferId]);
		}
		unset($Offer);
	}
	foreach($AllProductOffers as $Offer){
		if(isset($_POST["Offer{$Offer->Id}"])&&$_POST["Offer{$Offer->Id}"]==$Offer->Id)
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductToOffers} SET `ProductId`={$ProductInfo->Id},`OfferId`={$Offer->Id};");
		unset($Offer);
	}
	if(count($ProductInfo->Offers)){
		$ProductInfo->Offers=array_keys($ProductInfo->Offers);
		$ProductInfo->Offers=join(",",$ProductInfo->Offers);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductToOffers} WHERE `ProductId`={$ProductInfo->Id} AND `OfferId` IN ({$ProductInfo->Offers});");
	}
	header("Location: ?EditForm&Id={$ProductInfo->Id}&updated");
	exit();
}
elseif(isset($_GET["ChangeStatus"])&&$ProductInfo){
	$Status=(isset($_POST["Status{$ProductInfo->Id}"])&&$_POST["Status{$ProductInfo->Id}"]=="Y")?"Y":"N";
	$StockStatus=(isset($_POST["StockStatus{$ProductInfo->Id}"])&&$_POST["StockStatus{$ProductInfo->Id}"]=="IN")?"IN":"ORDER";
	$DataBase->UpdateQuery("UPDATE {$GLOBAL->Products} SET `Status`='{$Status}',`StockStatus`='{$StockStatus}' WHERE Id={$ProductInfo->Id};");
	exit();
}
elseif(isset($_GET["Delete"])&&$CanDelete){
	$DelIds=array();
	foreach($AllProducts as $Product){
		if(isset($_POST["Chk{$Product->Id}"])&&$_POST["Chk{$Product->Id}"]==$Product->Id){
			$DelIds[$Product->Id]=$Product->Id;
		}
		unset($Product);
	}
	if(count($DelIds)){
		$DelIds=join(",",$DelIds);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Products} WHERE Id IN ({$DelIds});");
		$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner` IN ({$DelIds});");
		foreach($Photos as $Photo){
			$Kernel->ClearThumbnails($DIRS->Photos,$Photo->Id,"jpg");
		}
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner` IN ({$DelIds});",$MainConnection);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductToCategory} WHERE `ProductId` IN ({$DelIds});",$MainConnection);
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductOptions} WHERE `ProductId` IN ({$DelIds});");
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->ProductToOffers} WHERE `ProductId` IN ({$DelIds});");
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
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Products} SET `Position`=:position WHERE Id=:id;",array(":position"=>$Position,":id"=>$ItemId));
	}
	exit();
}
$Crumbs=join(" / ",$Crumbs);
?>
<? $Design->LoadHeader($Crumbs); ?>
<?php
if($ShowList){
	?>
	<form action="?Delete" method="post" delquestion="выбранные продукты" id="GroupChecked">
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
						<col/>
						<col/>
						<col width="100%"/>
					</colgroup>
					<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th>Id</th>
						<th>Заголовок</th>
						<th>Статус</th>
						<th>Доступность</th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Title",@$_GET["Title"],"","text","Title","text")?></th>
						<th>
							<select name="Status" id="Status" title="Статус продукта">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="Y")?>
								<?=$HtmlTags->option("N","Выключенные",@$_GET["Status"]=="N")?>
							</select>
						</th>
						<th>
							<select name="StockStatus" id="StockStatus" title="Статус доступности">
								<?=$HtmlTags->option(0,"Все",@$_GET["StockStatus"]==0)?>
								<?=$HtmlTags->option("IN","В наличии",@$_GET["StockStatus"]=="IN")?>
								<?=$HtmlTags->option("ORDER","На заказ",@$_GET["StockStatus"]=="ORDER")?>
							</select>
						</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($AllProducts as $Product){
						?>
						<tr fid="<?=$Product->Id?>" id="Pos<?=$Product->Id?>">
							<td><?=$HtmlTags->InputCheckbox("Chk{$Product->Id}",$Product->Id,"",false,"","Chk{$Product->Id}","",true,"checkbox",true)?></td>
							<td>
								<a href="?EditForm&Id=<?=$Product->Id?>"><?=$Product->Id?></a>
							</td>
							<td>
								<a href="?EditForm&Id=<?=$Product->Id?>"><?=$Product->Title?></a>
							</td>
							<td><?=$HtmlTags->InputCheckbox("Status{$Product->Id}","Y","",$Product->Status=="Y","","Status{$Product->Id}","",true,"checkbox-slider",true)?></td>
							<td><?=$HtmlTags->InputCheckbox("StockStatus{$Product->Id}","IN","в наличии",$Product->StockStatus=="IN","","StockStatus{$Product->Id}","",true,"checkbox-slider",true)?></td>
						</tr>
						<?php
						unset($Product);
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
					$(document).ready(function(){
						function SavePositions(InputPositions){
							$.ajax({
								type:"POST",
								cache:false,
								data:'Positions='+InputPositions,
								url:'?ChangePosition',
								success:function(){
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
									var ItemsPositions=[];
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
	<form action="?<?=$IsEdit?"Edit&Id={$ProductInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
	<div class="title" id="formTitle">
		<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> продукта:</h1>
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
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Title="Title_{$Language->Prefix}";
					echo $HtmlTags->InputText($Title,$IsEdit?$ProductInfo->$Title:"","","text",$Title,"text",0,false,0,"","","","","","",$Language->Prefix=="ru");
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Иконка:</th>
			<td>
				<select name="Type" id="Type">
					<?php
					echo $HtmlTags->option('NORMAL','Нет',$IsEdit?$ProductInfo->Type=='NORMAL':false);
					echo $HtmlTags->option('ACTION','Акция',$IsEdit?$ProductInfo->Type=='ACTION':false);
					echo $HtmlTags->option('DISCOUNT','Скидка',$IsEdit?$ProductInfo->Type=='DISCOUNT':false);
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Лучшая цена:</th>
			<td><?=$HtmlTags->InputCheckbox("BestCost","Y","включен",$IsEdit?$ProductInfo->BestCost=='Y':false,"","BestCost","",true,"checkbox-slider",true)?></td>
		</tr>
		<tr>
			<th>Наличие подарока:</th>
			<td><?=$HtmlTags->InputCheckbox("HasGift","Y","включен",$IsEdit?$ProductInfo->HasGift=='Y':false,"","HasGift","",true,"checkbox-slider",true)?></td>
		</tr>
		<tr>
			<th>Каноническое название:</th>
			<td><?=$HtmlTags->InputText("Name",$IsEdit?$ProductInfo->Name:"","","text","Name","text")?></td>
		</tr>
		<tr>
			<th>Старая цена:</th>
			<td><?=$HtmlTags->InputText("OldPrice",$IsEdit?$ProductInfo->OldPrice:0,"","number","OldPrice","text",11)?></td>
		</tr>
		<tr>
			<th>Стоимость:</th>
			<td><?=$HtmlTags->InputText("Price",$IsEdit?$ProductInfo->Price:0,"","number","Price","text",11)?></td>
		</tr>
		<tr>
			<th>Бренд:</th>
			<td>
				<select name="BrandId" id="BrandId">
					<?php
					foreach($AllBrands as $Brand){
						echo $HtmlTags->option($Brand->Id,$Brand->Title,$IsEdit?$ProductInfo->BrandId==$Brand->Id:false);
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Максимум к заказу:</th>
			<td><?=$HtmlTags->InputText("MaxAmount",$IsEdit?$ProductInfo->MaxAmount:1,"","number","MaxAmount","text",11)?></td>
		</tr>
		<tr>
			<th>Статусы:</th>
			<td>
				<?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$ProductInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?>
				<?=$HtmlTags->InputCheckbox("StockStatus","IN","в наличии",$IsEdit?$ProductInfo->StockStatus=="IN":true,"","StockStatus","",true,"checkbox-slider",true)?>
				<?=$HtmlTags->InputCheckbox("IsSell","Y","хит продаж?",$IsEdit?$ProductInfo->IsSell=="Y":true,"","IsSell","",true,"checkbox-slider",true)?>
				<?=$HtmlTags->InputCheckbox("IsView","Y","популярное?",$IsEdit?$ProductInfo->IsView=="Y":true,"","IsView","",true,"checkbox-slider",true)?>
			</td>
		</tr>
		<tr>
			<th>Предложения:</th>
			<td>
				<?php
				foreach($AllProductOffers as $Offer){
					echo $HtmlTags->InputCheckbox("Offer{$Offer->Id}",$Offer->Id,$Offer->Title,$IsEdit?isset($ProductInfo->Offers[$Offer->Id]):false,"","Offer{$Offer->Id}","",true,"checkbox",true);
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Категории:</th>
			<td><?=$Kernel->PrintCategoriesCheckbox(0,0,$IsEdit?$ProductInfo->Categories:array())?></td>
		</tr>
		<tr>
			<th>Аннотация:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Annotation="Annotation_{$Language->Prefix}";
					echo $HtmlTags->textarea($Annotation,$IsEdit?$ProductInfo->$Annotation:"",40,3,"",$Language->Prefix,$Annotation,"text mceNoEditor");
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Краткое описание:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$ShortDescription="ShortDescription_{$Language->Prefix}";
					echo "<div id=\"{$ShortDescription}\">";
					echo $HtmlTags->textarea($ShortDescription,$IsEdit?$ProductInfo->$ShortDescription:"",40,9,"",$Language->Prefix,"{$ShortDescription}R","text");
					echo "</div>";
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Описание:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Description="Description_{$Language->Prefix}";
					echo "<div id=\"{$Description}\">";
					echo $HtmlTags->textarea($Description,$IsEdit?$ProductInfo->$Description:"",40,9,"",$Language->Prefix,"{$Description}R","text");
					echo "</div>";
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Характеристики:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Characteristics="Characteristics_{$Language->Prefix}";
					echo "<div id=\"{$Characteristics}\">";
					echo $HtmlTags->textarea($Characteristics,$IsEdit?$ProductInfo->$Characteristics:"",40,9,"",$Language->Prefix,"{$Characteristics}R","text");
					echo "</div>";
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Карта покрытия:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Map="Map_{$Language->Prefix}";
					echo "<div id=\"{$Map}\">";
					echo $HtmlTags->textarea($Map,$IsEdit?$ProductInfo->$Map:"",40,9,"",$Language->Prefix,"{$Map}R","text");
					echo "</div>";
				}
				?>
			</td>
		</tr>
		<tr>
			<th>Правила безопасности:</th>
			<td>
				<?php
				foreach($AllLanguages as $Language){
					$Rules="Rules_{$Language->Prefix}";
					echo "<div id=\"{$Rules}\">";
					echo $HtmlTags->textarea($Rules,$IsEdit?$ProductInfo->$Rules:"",40,9,"",$Language->Prefix,"{$Rules}R","text");
					echo "</div>";
				}
				?>
			</td>
		</tr>
		</tbody>
	</table>
	<table class="table">
		<colgroup>
			<col width="30%"/>
			<col/>
			<col width="20%"/>
			<col width="50%"/>
		</colgroup>
		<thead>
		<tr>
			<th colspan="4" class="tl tr">Опции</th>
		</tr>
		<tr>
			<th>Название продукта</th>
			<th>Стоимость в комплекте</th>
			<th>Категория</th>
			<th>Тип</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($AllSimProducts as $Product){
			?>
			<tr>
				<th><?=$Product->Title?></th>
				<td><?=$HtmlTags->InputText("Product{$Product->Id}",$IsEdit?(isset($ProductInfo->Options[$Product->Id])?$ProductInfo->Options[$Product->Id]->Price:0):0,"","number","Product{$Product->Id}","text",11)?></td>
				<td><?=$AllSimCategories[$Product->CategoryId]->Title?></td>
				<td><?=$AllSimTypes[$Product->TypeId]->Title?></td>
			</tr>
			<?php
			unset($Product);
		}
		?>
		</tbody>
	</table>
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
		if($IsEdit&&count($ProductInfo->Photos)){
			?>
			<tr>
				<td colspan="2">
					<ul class="PhotoList" id="Photos">
						<?php
						foreach($ProductInfo->Photos as $Photo){
							?>
							<li photoid="<?=$Photo->Id?>">
								<?=$HtmlTags->InputText("PhotoPos{$Photo->Id}",$Photo->Position,"","hidden")?>
								<?=$HtmlTags->InputText("PhotoStatus{$Photo->Id}","Y","","hidden")?>
								<img src="<?=$DIRS->Photos.$Photo->Id?>_4.jpg" alt="" title=""/>
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
	<table class="table">
		<thead>
			<tr>
				<th colspan="2" class="tl tr">Рекомендованные товары</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					<select name="RecommendedProducts[]" id="RecommendedProducts" multiple="multiple">
						<? $RecommendedProducts = json_decode($ProductInfo->RecommendedProducts, true); ?>
						<?php foreach ($AllProducts as $Product): ?>
							<?php if ($Product->Id == $ProductInfo->Id) continue; ?>
							<option value="<?= $Product->Id ?>" <? if (is_array($RecommendedProducts) && in_array($Product->Id, $RecommendedProducts)) echo 'selected="selected"'; ?>>
								<?= $Product->Title ?>
							</option>
						<?php endforeach ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	</section>
	<? $Design->LoadEditor() ?>
	</form>
<?php
}
?>
<? $Design->LoadFooter(); ?>