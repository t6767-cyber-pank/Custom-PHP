<?php
	if(!$IsAuth){
		header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}login/");
		exit;
	}

	$SubPages=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS MenuTitle FROM {$GLOBAL->SystemPages} WHERE `Owner`={$Page->FullIds[1]} AND `MenuVisible`='Y' ORDER BY `Position`;");

	$AllBaskets=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `UserId`={$GLOBAL->UserInfo->Id} AND `Status`<>'NP' ORDER BY `DateTime` DESC;",true,array(),"Id",false,false,array("Items"));
	$Ids=array_keys($AllBaskets);
	$Ids=join(",",$Ids);

	$ProductIds=array();
	$SimProductIds=array();
	$OptionIds=array();

	if(!empty($Ids)){
		$Items=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId` IN ({$Ids}) ORDER BY `DateTime` DESC;");
		foreach($Items as $Item){
			$AllBaskets[$Item->BasketId]->Items[$Item->Id]=$Item;

			if(!empty($Item->ProductId))
				$ProductIds[$Item->ProductId]=$Item->ProductId;
			if(!empty($Item->SimProductId))
				$SimProductIds[$Item->SimProductId]=$Item->SimProductId;
			if(!empty($Item->OptionId))
				$OptionIds[$Item->OptionId]=$Item->OptionId;

			unset($Item);
		}
		unset($Items);
	}

	if(isset($_GET["repeat"]) && isset($_GET["id"])){
		$Id=$_GET["id"];

		if(preg_match("'^([\d\D]{32})$'si",$Id)){
			if($Basket=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `UserId`={$GLOBAL->UserInfo->Id} AND MD5(Id)='{$Id}' AND `Status`<>'NP';",false)){
				$Basket=$AllBaskets[$Basket->Id];

				foreach($Basket->Items as $Item){
					$DataBase->InsertQuery("INSERT INTO {$GLOBAL->BasketItems} SET `BasketId`={$BasketInfo->Id},`UserId`={$GLOBAL->UserInfo->Id},`DateTime`={$DateTime},`Amount`={$Item->Amount},`ProductId`={$Item->ProductId},`SimProductId`={$Item->SimProductId},`OptionId`={$Item->OptionId},`Price`='{$Item->Price}';");

					unset($Item);
				}

				$Kernel->CalculateBasket($BasketInfo);

				header("Location: {$ActiveLanguage->LinksPrefix}/basket/");
				exit;
			}else{
				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}
		}else{
			header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
			exit;
		}
	}

	if(isset($_GET["del"]) && isset($_GET["id"])){
		$Id=$_GET["id"];

		if(preg_match("'^([\d\D]{32})$'si",$Id)){
			if($Basket=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `UserId`={$GLOBAL->UserInfo->Id} AND MD5(Id)='{$Id}' AND `Status`<>'NP';",false)){
				$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$Basket->Id} AND `UserId`={$GLOBAL->UserInfo->Id};");
				$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Baskets} WHERE `Id`={$Basket->Id} AND `UserId`={$GLOBAL->UserInfo->Id};");

				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}else{
				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}
		}else{
			header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
			exit;
		}
	}

	if(count($ProductIds)){
		$ProductIds=join(",",$ProductIds);
		$AllProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE Id IN ({$ProductIds});",true,array(),"Id",false,false,array("PhotoId"=>"int"));
		$ProductIds=array_keys($AllProducts);
		$ProductIds=join(",",$ProductIds);
		$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Owner` IN ({$ProductIds}) AND `Type`='PRODUCT' ORDER BY `Position`;");
		foreach($Photos as $Photo){
			if(empty($AllProducts[$Photo->Owner]->PhotoId))
				$AllProducts[$Photo->Owner]->PhotoId=$Photo->Id;

			unset($Photo);
		}
		unset($Photos);
	}
	if(count($SimProductIds)){
		$SimProductIds=join(",",$SimProductIds);
		$AllSimProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SimProducts} WHERE Id IN ({$SimProductIds});");
	}

	include ".{$DIRS->modules}so/h.inc";
?>
	<section class="signBlock body">
		<div class="tabs">
<?php
	foreach($SubPages as $SubPage){
		$class=$SubPage->Id==$Page->Id?" current":"";
?>
			<a href="<?=$ActiveLanguage->LinksPrefix.$SubPage->FullAddress?>" class="tab <?=$class?>"><span><?=$SubPage->MenuTitle?></span></a>
<?php
		unset($SubPage);
	}
?>
		</div>
		<div class="description"><?=$Page->Description?></div>
		<div class="basketHistory">
			<div class="table">
				<div class="row header">
					<div class="cell more"></div>
					<div class="cell id"><?=$LangPack->OrderNumber?></div>
					<div class="cell date"><?=$LangPack->OrderDate?></div>
					<div class="cell price"><?=$LangPack->OrderTotalSumm?></div>
					<div class="cell status"><?=$LangPack->OrderStatus?></div>
					<div class="cell delete"></div>
					<div class="cell repeat"></div>
				</div>
			</div>
<?php
	foreach($AllBaskets as $Basket){
		switch($Basket->Status){
			case "P":$Basket->StatusStr=$LangPack->OrderProccessing;break;
			case "NP":$Basket->StatusStr=$LangPack->OrderWaitForPayment;break;
			case "PV":$Basket->StatusStr=$LangPack->OrderWaitForPayment;break;
			case "D":$Basket->StatusStr=$LangPack->OrderDelivered;break;
			default:$Basket->StatusStr=$Basket->Status;break;
		}
		$Basket->IdStr=md5($Basket->Id);
?>
			<div class="table" data-id="<?=$Basket->Id?>">
				<div class="row">
					<div class="cell more"><a href="#" class="more"><?=$LangPack->OrderDetails?></a></div>
					<div class="cell id"><?=$Basket->Id?></div>
					<div class="cell date"><?=date("d.m.y",$Basket->DateTime)?></div>
					<div class="cell price"><?=str_replace("%PRICE%",$Kernel->DigitalToPrice($Basket->TotalSumm),$LangPack->PriceTg)?></div>
					<div class="cell status"><?=$Basket->StatusStr?></div>
					<div class="cell delete"><a href="?del&id=<?=$Basket->IdStr?>" class="delete"><?=$LangPack->Delete?></a></div>
					<div class="cell repeat"><a href="?repeat&id=<?=$Basket->IdStr?>" class="repeat"><?=$LangPack->OrderRepeat?></a></div>
				</div>
			</div>
			<div class="basket" data-basket="<?=$Basket->Id?>">
				<div class="table">
<?php
		foreach($Basket->Items as $Item){
?>
					<div class="row">
						<div class="cell title">
<?php
			if(!empty($Item->ProductId) && !empty($Item->SimProductId)){
?>
							<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank" class="info">
								<?=$AllProducts[$Item->ProductId]->Title?> + <?=$AllSimProducts[$Item->SimProductId]->Title?>
							</a>
<?php
			}elseif(!empty($Item->ProductId)){
?>
							<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank" class="info">
								<?=$AllProducts[$Item->ProductId]->Title?>
							</a>
<?php
			}elseif(!empty($Item->SimProductId)){
?>
							<a href="<?=$ActiveLanguage->LinksPrefix?>/products/sim/" target="_blank" class="info"><?=$AllSimProducts[$Item->SimProductId]->Title?></a>
<?php
			}
?>
						</div>
						<div class="cell"></div>
						<div class="cell price"><?=str_replace("%PRICE%",$Kernel->DigitalToPrice($Item->Price),$LangPack->PriceTg)?></div>
						<div class="cell amount">
							<span class="amount"><?=$LangPack->Amount?></span>
							<?=$Kernel->DigitalToPrice($Item->Amount)?>
						</div>
						<div class="cell">
							<span class="amount"><?=$LangPack->Summ?></span>
							<?=str_replace("%PRICE%",$Kernel->DigitalToPrice($Item->Price*$Item->Amount),$LangPack->PriceTg)?>
						</div>
						<div class="cell repeat"></div>
						<div class="cell delete"></div>
					</div>
<?php
			unset($Item);
		}
?>
				</div>
			</div>
<?php
		unset($Basket);
	}
?>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>