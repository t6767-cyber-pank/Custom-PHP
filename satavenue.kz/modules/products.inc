<?php
/*ini_set("display_errors",1);
error_reporting(E_ALL);*/
$PageNum=(int)@$_GET["p"];
$LIMIT=$PageNum*$InPage;
$FilterInnerStr=array("INNER"=>array(),"LEFT"=>array(),"ON"=>array());
$FilterSQL=array();
$PageFilters=array();
$ShowCategory=false;
$ShowProduct=false;
$ProductToCategoryFilter="";
$CategoryInfoURL="";
$Messages=array("ERROR"=>array(),"LOG"=>array());
if(preg_match("'^/([\d]{1,11})-(.*?)/$'si",$SUB_REQUEST_URI,$Id)){
	$Id=(int)$Id[1];
	if($ProductInfo=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Annotation_{$ActiveLanguage->Prefix} AS Annotation,ShortDescription_{$ActiveLanguage->Prefix} AS ShortDescription,Description_{$ActiveLanguage->Prefix} AS Description,Characteristics_{$ActiveLanguage->Prefix} AS Characteristics,Map_{$ActiveLanguage->Prefix} AS Map,Rules_{$ActiveLanguage->Prefix} AS Rules FROM {$GLOBAL->Products} WHERE Id={$Id} AND `Status`='Y';",false)){
			if(isset($_GET["order"])){
			$Name=$Kernel->PostToSQL(@$_POST["name"]);
			$EMail=@$_POST["email"];
			$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
			$Message=$Kernel->PostToSQL(@$_POST["message"]);
			if(empty($Name))
				$Messages["ERROR"][]=$LangPack->ErrorsName;
			if(empty($EMail)||!filter_var($EMail,FILTER_VALIDATE_EMAIL))
				$Messages["ERROR"][]=$LangPack->ErrorsEMail;
			if(empty($Phone))
				$Messages["ERROR"][]=$LangPack->ErrorsPhone;
			if(empty($Message))
				$Messages["ERROR"][]=$LangPack->ErrorsMessage;
			if(count($Messages["ERROR"])){
				$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";
				if(isset($_GET["ajax"])){
					echo $Messages["ERROR"];
					exit;
				}
			}
			else{
				$UserId=$IsAuth?$GLOBAL->UserInfo->Id:0;
				$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ProductRequests} SET `ProductId`={$ProductInfo->Id},`UserId`={$UserId},`DateTime`={$DateTime},`Name`='{$Name}',`EMail`='{$EMail}',`Phone`='{$Phone}',`Message`='{$Message}';");
				$Title=str_replace(array("%NAME%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%","%TITLE%"),array($Name,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"],$ProductInfo->Title),$TextBlocks->ProductRequestMessage->Title);
				$Message=str_replace(array("%NAME%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%","%TITLE%"),array($Name,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"],$ProductInfo->Title),$TextBlocks->ProductRequestMessage->Description);
				foreach($MailTo as $Mail){
					$Kernel->SendMessage($Mail,$Title,$Message);
				}
				if(isset($_GET["ajax"])){
					echo "<div class=\"message log\">{$TextBlocks->ProductRequestSended->Description}</div>";
					exit;
				}
				else{
					header("Location: ?sended");
					exit;
				}
			}
		}
		if(isset($_GET["sended"])){
			$Messages["LOG"][]=$TextBlocks->ProductRequestSended->Description;
			$Messages["LOG"]="<p>".join("</p><p>",$Messages["LOG"])."</p>";
		}
		if($IsAuth)
			$Kernel->AddViewEvent("PRODUCT",$ProductInfo->Id,$GLOBAL->UserInfo->Id);
		//$BreadCrumbs[]="<a href=\"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}".ltrim($CategoryInfo->FullAddress,"/")."\">{$CategoryInfo->Title}</a>";
		$BreadCrumbs[]="<a href=\"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}{$ProductInfo->Id}-{$ProductInfo->Name}/\">{$ProductInfo->Title}</a>";
		$ShowProduct=true;
	}
	else{
		$Kernel->e404();
	}
}
elseif(preg_match("'^/(.*?)/$'si",$SUB_REQUEST_URI,$Name)){
	$Name=$Name[0];
	$NameMD5=md5($Name);
	if($CategoryInfo=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,MenuTitle_{$ActiveLanguage->Prefix} AS MenuTitle FROM {$GLOBAL->Categories} WHERE MD5(FullAddress)='{$NameMD5}' AND `Status`='Y';",false)){
		$Page->Title=$CategoryInfo->Title;
		$Page->MenuTitle=$CategoryInfo->MenuTitle;
		$ProductToCategoryFilter=" AND {$GLOBAL->ProductToCategory}.CategoryId={$CategoryInfo->Id}";
		$CategoryInfoURL=ltrim($CategoryInfo->FullAddress,"/");
		if(!empty($CategoryInfo->Owner)){
			$SubCategoryInfo=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,MenuTitle_{$ActiveLanguage->Prefix} AS MenuTitle FROM {$GLOBAL->Categories} WHERE `Id`={$CategoryInfo->Owner} AND `Status`='Y';",false);
			$BreadCrumbs[]="<a href=\"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}".ltrim($SubCategoryInfo->FullAddress,"/")."\">{$SubCategoryInfo->Title}</a>";
		}
		$BreadCrumbs[]="<a href=\"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}".ltrim($CategoryInfo->FullAddress,"/")."\">{$CategoryInfo->Title}</a>";
		$ShowCategory=true;
	}
	else{
		$Kernel->e404();
	}
}
/*if(!$ShowCategory && !$ShowProduct){
	$FirstCategory=$DataBase->Query("SELECT * FROM {$GLOBAL->Categories} WHERE `Status`='Y' AND `Owner`=0 ORDER BY `Position` LIMIT 0,1;",false);

	header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}".ltrim($FirstCategory->FullAddress,"/"));
	exit;
}*/
if(!$ShowProduct){
	$AllOffers=$DataBase->Query("
			SELECT
				{$GLOBAL->ProductOffers}.*,
				{$GLOBAL->ProductOffers}.Title_{$ActiveLanguage->Prefix} AS Title
			FROM
				{$GLOBAL->ProductOffers}
			INNER JOIN
				{$GLOBAL->ProductToOffers}
			ON 
				{$GLOBAL->ProductOffers}.Id={$GLOBAL->ProductToOffers}.OfferId
			INNER JOIN
				{$GLOBAL->ProductToCategory}
			ON 
				{$GLOBAL->ProductToOffers}.ProductId={$GLOBAL->ProductToCategory}.ProductId
			WHERE
				{$GLOBAL->ProductOffers}.`Status`='Y'
			{$ProductToCategoryFilter}
			GROUP BY
				{$GLOBAL->ProductOffers}.Id
			ORDER BY
				{$GLOBAL->ProductOffers}.Position
		;");
	$AllBrands=$DataBase->Query("
			SELECT
				{$GLOBAL->Brands}.*,
				{$GLOBAL->Brands}.Title_{$ActiveLanguage->Prefix} AS Title
			FROM
				{$GLOBAL->Brands}
			INNER JOIN
				{$GLOBAL->Products}
			ON 
				{$GLOBAL->Products}.BrandId={$GLOBAL->Brands}.Id
			INNER JOIN
				{$GLOBAL->ProductToCategory}
			ON 
				{$GLOBAL->Products}.Id={$GLOBAL->ProductToCategory}.ProductId
			WHERE
				{$GLOBAL->Brands}.`Status`='Y'
			{$ProductToCategoryFilter}
			GROUP BY
				{$GLOBAL->Brands}.Id
			ORDER BY
				{$GLOBAL->Brands}.`Position`
		;");
	$MinMaxPrice=$DataBase->Query("
			SELECT
				MIN({$GLOBAL->Products}.`Price`) AS MinPrice,
				MAX({$GLOBAL->Products}.`Price`) AS MaxPrice
			FROM
				{$GLOBAL->Products}
			INNER JOIN
				{$GLOBAL->ProductToCategory}
			ON 
				{$GLOBAL->Products}.Id={$GLOBAL->ProductToCategory}.ProductId
			WHERE
				{$GLOBAL->Products}.`Status`='Y'
			{$ProductToCategoryFilter}
		;",false);
	$MinMaxPrice->CurrentMinPrice=$MinMaxPrice->MinPrice;
	$MinMaxPrice->CurrentMaxPrice=$MinMaxPrice->MaxPrice;
	if(isset($_GET["from"])){
		$From=(int)$_GET["from"];
		if($From>=$MinMaxPrice->MinPrice&&$From<=$MinMaxPrice->MaxPrice){
			$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`Price`>={$From}";
			$MinMaxPrice->CurrentMinPrice=$From;
			$PageFilters["from"]="from={$From}";
		}
	}
	if(isset($_GET["to"])){
		$To=(int)$_GET["to"];
		if($To<=$MinMaxPrice->MaxPrice&&$To>=$MinMaxPrice->MinPrice){
			$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`Price`<={$To}";
			$MinMaxPrice->CurrentMaxPrice=$To;
			$PageFilters["to"]="to={$To}";
		}
	}
	if(isset($_GET["availability"])){
		$Availabilities=array();
		foreach($_GET["availability"] as $Availability){
			if(in_array($Availability,array("in","order"))){
				$PageFilters["availability{$Availability}"]="availability[]={$Availability}";
				$Availability=strtoupper($Availability);
				$Availabilities[$Availability]=$Availability;
			}
		}
		if(count($Availabilities)){
			$Availabilities="'".join("','",$Availabilities)."'";
			$FilterSQL["MAIN"][]="`StockStatus` IN ({$Availabilities})";
		}
	}
	if(isset($_GET["offer"])){
		$Offers=array();
		foreach($_GET["offer"] as $Offer){
			$Offer=(int)$Offer;
			if(isset($AllOffers[$Offer])){
				$Offers[$Offer]=$Offer;
				$PageFilters["offer{$Offer}"]="offer[]={$Offer}";	
			}
			unset($Offer);
		}
		if(count($Offers)){
			$Offers=join(",",$Offers);
			$FilterSQL["MAIN"][]="{$GLOBAL->ProductToOffers}.OfferId IN ({$Offers})";
			$FilterInnerStr["INNER"]["PTO"]=$GLOBAL->ProductToOffers;
			$FilterInnerStr["ON"]["PTO"]="{$GLOBAL->Products}.Id={$GLOBAL->ProductToOffers}.ProductId";
		}
	}
	// Выводим только акции
	if (isset($_GET['IsSell'])) {
		$FilterSQL["MAIN"][]="{$GLOBAL->Products}.IsSell='Y'";
	}
	if(isset($_GET["brand"])){
		$Brands=array();
		foreach($_GET["brand"] as $Brand){
			$Brand=(int)$Brand;
			if(isset($AllBrands[$Brand])){
				$Brands[$Brand]=$Brand;
				$PageFilters["brand{$Brand}"]="brand[]={$Brand}";
			}
			unset($Brand);
		}
		if(count($Brands)){
			$Brands=join(",",$Brands);
			$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`BrandId` IN ({$Brands})";
		}
	}
	$FilterInnerStr["INNER"]["PTC"]=$GLOBAL->ProductToCategory;
	$FilterInnerStr["ON"]["PTC"]="{$GLOBAL->Products}.Id={$GLOBAL->ProductToCategory}.ProductId";
	$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`Status`='Y'";
	if(!empty($ProductToCategoryFilter))
		$FilterSQL["MAIN"][]="{$GLOBAL->ProductToCategory}.`CategoryId`={$CategoryInfo->Id}";
	foreach($FilterSQL as $Key=>$Filters){
		$Filters=join(" AND ",$Filters);
		$FilterSQL[$Key]=$Filters;
	}
	$FilterSQL=join(" AND ",$FilterSQL);
	$FilterInnerStr=$Kernel->CreateInnerSQL($FilterInnerStr);
	if(isset($_COOKIE["oc"])&&(int)$_COOKIE["oc"]==1){
		//не срабатывает во время аякс перегрузки, хотя следующее эхо работает
		$OrderBy="IF(Products.Price = 0, 1, 0), {$GLOBAL->Categories}.`Position`";
		$FilterInnerStr="INNER JOIN {$GLOBAL->ProductToCategory} ON {$GLOBAL->Products}.Id={$GLOBAL->ProductToCategory}.ProductId\nINNER JOIN {$GLOBAL->Categories} ON {$GLOBAL->Categories}.Id={$GLOBAL->ProductToCategory}.CategoryId";
	}
	/*if(isset($_GET["brand"]) && !$ShowCategory){
		$addBy=" INNER JOIN {$GLOBAL->Categories} ON {$GLOBAL->Categories}.Id={$GLOBAL->ProductToCategory}.CategoryId";
		$OrderBy="{$GLOBAL->Categories}.`Position`";
	}else{
		$addBy="";
		$OrderBy="{$GLOBAL->Products}.`BrandId`";
	}*/
	$ProductSQL="
			SELECT
				{$GLOBAL->Products}.*,
				{$GLOBAL->Products}.Title_{$ActiveLanguage->Prefix} AS Title,
				{$GLOBAL->Products}.Annotation_{$ActiveLanguage->Prefix} AS Annotation
			FROM
				{$GLOBAL->Products}
			{$FilterInnerStr}
			WHERE
				{$FilterSQL}
			GROUP BY
				{$GLOBAL->Products}.Id
			ORDER BY
				{$OrderBy} {$OrderByDirection}
			LIMIT
				{$LIMIT},{$InPage}
		;";

	$AllProducts=$DataBase->Query($ProductSQL,true,array(),"Id",false,false,array("PhotoId"=>"int","Photos"));
	$Ids=array_keys($AllProducts);
	$Ids=join(",",$Ids);
	if(!empty($Ids)){
		$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner` IN ({$Ids}) ORDER BY `Position`;");
		foreach($Photos as $Photo){
			if(empty($AllProducts[$Photo->Owner]->PhotoId))
				$AllProducts[$Photo->Owner]->PhotoId=$Photo->Id;
			if(count($AllProducts[$Photo->Owner]->Photos)<3)
				$AllProducts[$Photo->Owner]->Photos[$Photo->Id]=$Photo;
			unset($Photo);
		}
		unset($Photos);
	}
	$Pages=$DataBase->Query("
			SELECT
				{$GLOBAL->Products}.*
			FROM
				{$GLOBAL->Products}
			{$FilterInnerStr}
			WHERE
				{$FilterSQL}
			GROUP BY
				{$GLOBAL->Products}.Id
		;");
	$Pages=count($Pages);
	$Pages=ceil($Pages/$InPage);
	$Pages=$Kernel->CreatePageListener($Pages,$PageNum,"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}".$CategoryInfoURL."?p=",10,$PageFilters);
}
if($ShowProduct){
	$ProductInfo->Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner`={$ProductInfo->Id} ORDER BY `Position`;");
	if(count($ProductInfo->Photos)){
		$ProductInfo->FirstPhoto=current($ProductInfo->Photos);
	}
	$ProductInfo->Options=$DataBase->Query("
			SELECT
				{$GLOBAL->SimProducts}.Id,
				{$GLOBAL->SimProducts}.Title_{$ActiveLanguage->Prefix} AS Title,
				{$GLOBAL->ProductOptions}.Price
			FROM
				{$GLOBAL->SimProducts}
			INNER JOIN
				{$GLOBAL->ProductOptions}
			ON 
				{$GLOBAL->SimProducts}.Id={$GLOBAL->ProductOptions}.SimProductId
			WHERE
				{$GLOBAL->ProductOptions}.ProductId={$ProductInfo->Id};
			AND 
				{$GLOBAL->SimProducts}.`Status`='Y'
			GROUP BY
				{$GLOBAL->ProductOptions}.ProductId
			ORDER BY
				{$GLOBAL->ProductOffers}.`Position`
		;");
	$Page->Title=$ProductInfo->Title;
	//$ShowMainTitle=false;
}
include ".{$DIRS->modules}so/h.inc";
if(!$ShowProduct){
	?>
	<section class="categoryInfo body">
		<div class="leftMenu">
			<form action="?" method="get">
				<dl class="filter">
					<dt><?=$LangPack->Availability?></dt>
					<dd>
						<ul>
							<li><?=$HtmlTags->InputCheckbox("availability[]","in",$LangPack->AvailabilityInStock,(isset($_GET["availability"])&&in_array("in",$_GET["availability"])),"","availabilityIn","",true,"checkbox",true)?></li>
							<li><?=$HtmlTags->InputCheckbox("availability[]","order",$LangPack->AvailabilityOrder,(isset($_GET["availability"])&&in_array("order",$_GET["availability"])),"","availabilityOrder","",true,"checkbox",true)?></li>
						</ul>
					</dd>
				</dl>
				<dl class="filter">
					<dt><?=$LangPack->OurOffers?></dt>
					<dd>
						<ul>
							<?php
							foreach($AllOffers as $Offer){
								?>
								<li><?=$HtmlTags->InputCheckbox("offer[]",$Offer->Id,$Offer->Title,(isset($_GET["offer"]))&&in_array($Offer->Id,$_GET["offer"]),"","offer{$Offer->Id}","",true,"checkbox",true)?></li>
								<?php
								unset($Offer);
							}
							?>
							<li><?=$HtmlTags->InputCheckbox("IsSell",'true','Акция',(isset($_GET["IsSell"])&&$_GET["IsSell"]),"","IsSell","",true,"checkbox",true)?></li>
						</ul>
					</dd>
				</dl>
				<dl class="filter">
					<dt><?=$LangPack->PriceTenge?></dt>
					<dd>
						<div class="slider price">
							<div class="input">
								<label for="from"><?=$LangPack->from?></label>
								<?=$HtmlTags->InputText("from",$MinMaxPrice->CurrentMinPrice,"","text","from","text")?>
								<label for="to"><?=$LangPack->to?></label>
								<?=$HtmlTags->InputText("to",$MinMaxPrice->CurrentMaxPrice,"","text","to","text")?>
							</div>

							<div class="controls" data-min="<?=$MinMaxPrice->MinPrice?>" data-max="<?=$MinMaxPrice->MaxPrice?>" data-current-min="<?=$MinMaxPrice->CurrentMinPrice?>" data-current-max="<?=$MinMaxPrice->CurrentMaxPrice?>">
								<div class="progress"></div>
								<div class="handler">
									<span><?=$MinMaxPrice->MinPrice?></span>
								</div>
							</div>
						</div>
					</dd>
				</dl>
				<dl class="filter">
					<dt><?=$LangPack->Manufacturer?></dt>
					<dd>
						<ul>
							<?php
							foreach($AllBrands as $Brand){
								?>
								<li><?=$HtmlTags->InputCheckbox("brand[]",$Brand->Id,$Brand->Title,isset($_GET["brand"])&&in_array($Brand->Id,$_GET["brand"]),"","brand{$Brand->Id}","",true,"checkbox",true)?></li>
							<?php
							}
							?>
						</ul>
					</dd>
				</dl>
				<div class="buttons">
					<a href="<?=$ActiveLanguage->LinksPrefix.$Page->FullAddress.$CategoryInfoURL?>" class="reset"><?=$LangPack->Reset?></a>
					<? /* <?=$HtmlTags->InputSubmit($LangPack->Apply,"","","",true,"submit","submit")?> */ ?>
				</div>
			</form>
		</div>
		<div class="productList">
			<div class="panel" id="listPanel">
				<form action="?" method="get">
					<label for="sort"><?=$LangPack->SortBy?></label>
					<select name="sort" id="sort">
						<?php
						foreach($SortMethods as $Type=>$Title){
							echo $HtmlTags->option($Type,$Title[0],$Title[1]==$OrderBy&&$Title[2]==$OrderByDirection);
						}
						?>
					</select>
					<label for="view"><?=$LangPack->View?></label>
					<div class="items">
						<?=$HtmlTags->InputRadio("view","grid","",$ListView=="GRID","","viewG","radio",true,$LangPack->ViewWithGrid)?>
						<?=$HtmlTags->InputRadio("view","list","",$ListView=="LIST","","viewL","radio",true,$LangPack->ViewWithList)?>
					</div>
					<label for="products"><?=$LangPack->ProductsPerPage?></label>
					<select name="products" id="products">
						<?php
						for($i=$MinInPage;$i<=$MaxInPage;$i+=$InPageStep){
							echo $HtmlTags->option($i,$i,$i==$InPage);
						}
						?>
					</select>
				</form>
			</div>
			<div class="products">
				<?php
				foreach($AllProducts as $Product){
					$Kernel->PrintProductBlock($Product,$ListView);
				}
				?>
				<div class="pagesNum"><?=$Pages?></div>
			</div>
		</div>
	</section>
<?php
}
elseif($ShowProduct){
	?>
	<section class="productInfo body">
		<div class="mainInfo">
			<?php
			if(count($ProductInfo->Photos)){
				?>
				<div class="photos">
					<div class="cover  ico-<?=strtolower($ProductInfo->Type)?>  <?=(!empty($ProductInfo->BestCost)&&($ProductInfo->BestCost=='Y'))?"bestcost":""?>">
						<?=(!empty($ProductInfo->HasGift)&&($ProductInfo->HasGift=='Y'))?"<span class=\"hasgift\"></span>":""?>

						<img src="<?=$DIRS->Photos.$ProductInfo->FirstPhoto->Id?>_bp.jpg" alt="<?=$ProductInfo->Title?>" title="<?=$ProductInfo->Title?>"/>
					</div>
					<div class="list">
						<?php
						foreach($ProductInfo->Photos as $Photo){
							$class=$Photo->Id==$ProductInfo->FirstPhoto->Id?" class=\"current\"":"";
							?>
							<a href="<?=$DIRS->Photos.$Photo->Id?>_bp.jpg" target="_blank"<?=$class?>>
								<img src="<?=$DIRS->Photos.$Photo->Id?>_pl.jpg" alt="<?=$ProductInfo->Title?>" title="<?=$ProductInfo->Title?>"/>
							</a>
							<?php
							unset($Photo);
						}
						?>
					</div>
				</div>
			<?php
			}
			?>
			<div class="info">
				<h1 class="title"><?=$ProductInfo->Title?></h1>
				<?php
				if($ProductInfo->StockStatus=="ORDER"){
					?>
					<div class="orderInfo"><?=$TextBlocks->ProductForOrder->Description?></div>
				<?php
				}
				?>
				<div class="annotation"><?=$ProductInfo->ShortDescription?></div>
			</div>
			<div class="order">
				<?php
				if($ProductInfo->StockStatus=="ORDER"){
					?>
					<div id="getPrice">Запросить цену</div>
					<form class="order request recaptchaForm" id="getPriceForm" action="?order" method="post" style="display: none;">
						<h2 class="title"><?=$LangPack->ProductForOrder?></h2>
						<div class="form">
							<?php
							if(isset($Messages["ERROR"])&&!empty($Messages["ERROR"])){
								?>
								<div class="message error"><?=$Messages["ERROR"]?></div>
							<?php
							}
							if(isset($Messages["LOG"])&&!empty($Messages["LOG"])){
								?>
								<div class="message log"><?=$Messages["LOG"]?></div>
							<?php
							}
							?>
							<label for="name"><?=$LangPack->FullNameReq?></label>
							<?=$HtmlTags->InputText("name",@$_POST["name"],"","text","name","text",0,false,0,"","","","","","",true)?>
							<label for="email"><?=$LangPack->EMailReq?></label>
							<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text",0,false,0,"","","","","","",true)?>
							<label for="phone"><?=$LangPack->PhoneReq?></label>
							<?=$HtmlTags->InputText("phone",@$_POST["phone"],"","text","phone","text",0,false,0,"","","","","","",true)?>
							<label for="message"><?=$LangPack->MessageReq?></label>
							<?=$HtmlTags->textarea("message",@$_POST["message"],20,5,"","","message","text","","",true)?>
							<div class="border-capcha">
								<div id="recaptchaForm"></div>
							</div>
							<div id="recaptchaErrorForm"></div>
							<?=$HtmlTags->InputSubmit($LangPack->SendRequest,"","","",true,"submit","submit")?>
						</div>
					</form>
				<?php
				}
				else{
					?>
					<form class="order" action="<?=$ActiveLanguage->LinksPrefix?>/basket/?add" method="post">
						<?=$HtmlTags->InputText("product",$ProductInfo->Id,"","hidden","product")?>
						<div class="form">
							<h2 class="title"><?=$LangPack->OptionsChoose?></h2>
							<ul class="list">
								<li><?=$HtmlTags->InputRadio("optionId","p".$ProductInfo->Id,str_replace("%TITLE%",$ProductInfo->Title,$LangPack->JustProduct),true,"","productId","radio",true,"",array("price"=>$ProductInfo->Price))?></li>
								<?php
								foreach($ProductInfo->Options as $Option){
									?>
									<li><?=$HtmlTags->InputRadio("optionId",$Option->Id,$ProductInfo->Title." + ".$Option->Title,false,"","optionId{$Option->Id}","radio",true,"",array("price"=>$Option->Price))?></li>
									<?php
									unset($Option);
								}
								?>
							</ul>
							<div class="total">
								<span class="title">Цена:</span>
								<span class="price" id="originalPrice">
									<span class="value" data-price="<?= $ProductInfo->Price ?>"><?=$Kernel->DigitalToPrice($ProductInfo->Price)?></span>
									<span class="currency"><?=$LangPack->tg?></span>
								</span>
							</div>
							<div class="amount">
								<label for="amount"><?=$LangPack->Amount?></label>
								<?=$HtmlTags->InputText("amount",1,"","number","amount","text",0,false,0,"","","","min=\"1\" max=\"{$ProductInfo->MaxAmount}\"","","",true)?>
							</div>
							<?php if(!empty($ProductInfo->OldPrice)){?>
								<div class="old-price"><?=$LangPack->OldPrice?>: <span><?=$Kernel->DigitalToPrice($ProductInfo->OldPrice).$LangPack->tg?></span></div>
							<?php }?>
							<div class="total absolutlyTotal">
								<span class="title"><?=$LangPack->Total?></span>
								<span class="price">
									<span class="value"><?=$Kernel->DigitalToPrice($ProductInfo->Price)?></span>
									<span class="currency"><?=$LangPack->tg?></span>
								</span>
							</div>
							<?=$HtmlTags->InputSubmit($LangPack->buy,"","","",true,"submit","submit")?>
						</div>
					</form>
				<?php
				}
				?>
			</div>
		</div>
		<div class="tabInfo">
			<div class="tabs">
				<a href="#" class="tab current">
					<span><?=$LangPack->Description?></span>
				</a>
				<?php
				if(!empty($ProductInfo->Characteristics)){
					?>
					<a href="#" class="tab">
						<span><?=$LangPack->Characteristics?></span>
					</a>
				<?php
				}
				if(!empty($ProductInfo->Map)){
					?>
					<a href="#" class="tab">
						<span><?=$LangPack->Map?></span>
					</a>
				<?php
				}
				if(!empty($ProductInfo->Rules)){
					?>
					<a href="#" class="tab">
						<span><?=$LangPack->Rules?></span>
					</a>
				<?php
				}
				?>
			</div>
			<div class="tabBlocks">
				<div class="tab description current"><?=$ProductInfo->Description?></div>
				<?php
				if(!empty($ProductInfo->Characteristics)){
					?>
					<div class="tab characteristics"><?=$ProductInfo->Characteristics?></div>
				<?php
				}
				if(!empty($ProductInfo->Map)){
					?>
					<div class="tab map"><?=$ProductInfo->Map?></div>
				<?php
				}
				if(!empty($ProductInfo->Rules)){
					?>
					<div class="tab rules"><?=$ProductInfo->Rules?></div>
				<?php
				}
				?>
			</div>
		</div>
	</section>
	<section class="productList body">
		<h2 class="title"><?= $LangPack->RecommendedProducts ?></h2>
		<?
			$RecommendedProductsIds = json_decode($ProductInfo->RecommendedProducts, true);
			$RecommendedProducts = array();
			if ($ProductInfo->RecommendedProducts) {
				$in = '"' . implode('", "', $RecommendedProductsIds) . '"';
				$RecommendedProducts = $DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Annotation_{$ActiveLanguage->Prefix} AS Annotation,ShortDescription_{$ActiveLanguage->Prefix} AS ShortDescription,Description_{$ActiveLanguage->Prefix} AS Description,Characteristics_{$ActiveLanguage->Prefix} AS Characteristics,Map_{$ActiveLanguage->Prefix} AS Map,Rules_{$ActiveLanguage->Prefix} AS Rules FROM {$GLOBAL->Products} WHERE Id in ({$in}) AND `Status`='Y';");

			}
		?>
		<?php if ($RecommendedProducts): ?>
			<div class="items">
				<?php foreach ($RecommendedProducts as $RecommendedProduct): ?>
					<?php 
						$RecommendedProduct->Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner`={$RecommendedProduct->Id} ORDER BY `Position`;");

						if(count($RecommendedProduct->Photos)){
							$RecommendedProduct->FirstPhoto=current($RecommendedProduct->Photos);
							$RecommendedProduct->PhotoId=$RecommendedProduct->FirstPhoto->Id;
						}
						
						$RecommendedProduct->Options=$DataBase->Query("
								SELECT
									{$GLOBAL->SimProducts}.Id,
									{$GLOBAL->SimProducts}.Title_{$ActiveLanguage->Prefix} AS Title,
									{$GLOBAL->ProductOptions}.Price
								FROM
									{$GLOBAL->SimProducts}
								INNER JOIN
									{$GLOBAL->ProductOptions}
								ON 
									{$GLOBAL->SimProducts}.Id={$GLOBAL->ProductOptions}.SimProductId
								WHERE
									{$GLOBAL->ProductOptions}.ProductId={$RecommendedProduct->Id};
								AND 
									{$GLOBAL->SimProducts}.`Status`='Y'
								GROUP BY
									{$GLOBAL->ProductOptions}.ProductId
								ORDER BY
									{$GLOBAL->ProductOffers}.`Position`
							;");
					 ?>

					<?= $Kernel->PrintProductBlock($RecommendedProduct,$ListView); ?>
				<?php endforeach ?>
			</div>
		<?php endif ?>
	</section>
<?php
}
?>
<? include ".{$DIRS->modules}so/f.inc"; ?>