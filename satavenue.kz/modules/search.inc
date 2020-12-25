<?php
	$PageNum=(int) @$_GET["p"];
	$LIMIT=$PageNum*$InPage;
	$FilterInnerStr=array("INNER"=>array(),"LEFT"=>array(),"ON"=>array());
	$FilterSQL=array();
	$PageFilters=array();

	$ShowCategory=false;

	$AllProducts=array();

	$Messages=array(
		"ERROR"=>array(),
		"LOG"=>array()
	);

	$WrongRequest=false;

	$LikeArray=array();
	$LikeArrayStr=array();

	$SearchStr=$Kernel->PostToSQL(@$_GET["s"]);

	if(mb_strlen($SearchStr,"UTF-8")>=3){
		$SearchStrArray=preg_split("'( |\/)'si",$SearchStr);

		$SearchQueryArray=array();
		$Kernel->GetVariations($SearchStrArray,$SearchQueryArray,0);

		foreach($SearchStrArray as $Str){
			if(mb_strlen($Str,"UTF-8")>=3){
				$LikeArray["TITLE"][]=$Str;
			}

			unset($Str);
		}

//		print_r($SearchQueryArray);
//		print_r($LikeArray["TITLE"]);

		$LikeArrayStr["PRODUCT1"]=$Kernel->GetLikeArray(array("{$GLOBAL->Products}.Title_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Annotation_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.ShortDescription_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Description_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Characteristics_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Map_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Rules_{$ActiveLanguage->Prefix}"),$LikeArray["TITLE"]);
		$LikeArrayStr["PRODUCT"]=$Kernel->GetLikeArray(array("{$GLOBAL->Products}.Title_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Annotation_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.ShortDescription_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Description_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Characteristics_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Map_{$ActiveLanguage->Prefix}","{$GLOBAL->Products}.Rules_{$ActiveLanguage->Prefix}"),$SearchQueryArray);

//		print_r($LikeArrayStr["PRODUCT"]);
//		print_r($LikeArrayStr["PRODUCT1"]);

		$FilterSQL["TITLE"][]=$LikeArrayStr["PRODUCT"];

		$PageFilters[]="s={$_GET["s"]}";

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
		;",false);

		$MinMaxPrice->CurrentMinPrice=$MinMaxPrice->MinPrice;
		$MinMaxPrice->CurrentMaxPrice=$MinMaxPrice->MaxPrice;

		if(isset($_GET["from"])){
			$From=(int)$_GET["from"];
			if($From>=$MinMaxPrice->MinPrice && $From<=$MinMaxPrice->MaxPrice){
				$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`Price`>={$From}";
				$MinMaxPrice->CurrentMinPrice=$From;

				$PageFilters["from"]="from={$From}";
			}
		}
		if(isset($_GET["to"])){
			$To=(int)$_GET["to"];
			if($To<=$MinMaxPrice->MaxPrice && $To>=$MinMaxPrice->MinPrice){
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

		// Выводим только акции
		if (isset($_GET['IsSell'])) {
			$FilterSQL["MAIN"][]="{$GLOBAL->Products}.IsSell='Y'";
		}

		$FilterInnerStr["INNER"]["PTC"]=$GLOBAL->ProductToCategory;
		$FilterInnerStr["ON"]["PTC"]="{$GLOBAL->Products}.Id={$GLOBAL->ProductToCategory}.ProductId";

		$FilterSQL["MAIN"][]="{$GLOBAL->Products}.`Status`='Y'";

		foreach($FilterSQL as $Key=>$Filters){
			$Filters=join(" AND ",$Filters);

			$FilterSQL[$Key]=$Filters;
		}
		$FilterSQL="(".join(") AND (",$FilterSQL).")";

		$FilterInnerStr=$Kernel->CreateInnerSQL($FilterInnerStr);

		$AllProductsSQL="
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
		$AllProducts=$DataBase->Query($AllProductsSQL,true,array(),"Id",false,false,array("PhotoId"=>"int","Photos"));
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
		$Pages=$Kernel->CreatePageListener($Pages,$PageNum,"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}"."?p=",10,$PageFilters);
	}else
		$WrongRequest=true;

	include ".{$DIRS->modules}so/h.inc";

	if(!$WrongRequest){
?>
	<section class="categoryInfo body">
		<div class="leftMenu">
			<form action="?" method="get">
				<?=$HtmlTags->InputText("s",@$_GET["s"],"","hidden","searchString")?>
				<dl class="filter">
					<dt><?=$LangPack->Availability?></dt>
					<dd>
						<ul>
							<li><?=$HtmlTags->InputCheckbox("availability[]","in",$LangPack->AvailabilityInStock,(isset($_GET["availability"]) && in_array("in",$_GET["availability"])),"","availabilityIn","",true,"checkbox",true)?></li>
							<li><?=$HtmlTags->InputCheckbox("availability[]","order",$LangPack->AvailabilityOrder,(isset($_GET["availability"]) && in_array("order",$_GET["availability"])),"","availabilityOrder","",true,"checkbox",true)?></li>
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
							<li><?=$HtmlTags->InputCheckbox("offer[]",$Offer->Id,$Offer->Title,(isset($_GET["offer"])) && in_array($Offer->Id,$_GET["offer"]),"","offer{$Offer->Id}","",true,"checkbox",true)?></li>
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
								<div class="handler"><span><?=$MinMaxPrice->MinPrice?></span></div>
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
							<li><?=$HtmlTags->InputCheckbox("brand[]",$Brand->Id,$Brand->Title,isset($_GET["brand"]) && in_array($Brand->Id,$_GET["brand"]),"","brand{$Brand->Id}","",true,"checkbox",true)?></li>
<?php
		}
?>
						</ul>
					</dd>
				</dl>
				<div class="buttons">
					<a href="<?=$ActiveLanguage->LinksPrefix.$Page->FullAddress?>?s=<?=@$_GET["s"]?>" class="reset"><?=$LangPack->Reset?></a>
					<? /* <?=$HtmlTags->InputSubmit($LangPack->Apply,"","","",true,"submit","submit")?> */ ?>
				</div>
			</form>
		</div>
		<div class="productList">
<?php
		if(count($AllProducts)){
?>
			<div class="panel" id="listPanel">
				<form action="?" method="get">
					<label for="sort"><?=$LangPack->SortBy?></label>
					<select name="sort" id="sort">
<?php
			foreach($SortMethods as $Type=>$Title){
				echo $HtmlTags->option($Type,$Title[0],$Title[1]==$OrderBy && $Title[2]==$OrderByDirection);
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
<?php
		}else{
			echo $TextBlocks->EmptySearchResults->Description;
		}
?>
		</div>
	</section>
<?php
	}else{
?>
	<section class="emptySearch body"><?=$TextBlocks->EmptySearchResults->Description?></section>
<?php
	}
?>
<? include ".{$DIRS->modules}so/f.inc";?>