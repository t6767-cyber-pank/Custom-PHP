<?php
	$BestSellers=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE `Status`='Y' AND `IsSell`='Y' ORDER BY `Id` DESC LIMIT 0,4;",true,array(),"Id",false,false,array("PhotoId"=>"int"));
	$MostPopular=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE `Status`='Y' AND `IsView`='Y' ORDER BY `Id` DESC LIMIT 0,4;",true,array(),"Id",false,false,array("PhotoId"=>"int"));
	$Ids=array();
	foreach($BestSellers as $Id=>$Product){
		$Ids[$Id]=$Id;
		unset($Id);
		unset($Product);
	}
	foreach($MostPopular as $Id=>$Product){
		$Ids[$Id]=$Id;
		unset($Id);
		unset($Product);
	}
	$Ids=join(",",$Ids);
	$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Type`='PRODUCT' AND `Owner` IN ({$Ids}) ORDER BY `Position`;");
	foreach($Photos as $Photo){
		if(isset($BestSellers[$Photo->Owner]) && empty($BestSellers[$Photo->Owner]->PhotoId))
			$BestSellers[$Photo->Owner]->PhotoId=$Photo->Id;
		if(isset($MostPopular[$Photo->Owner]) && empty($MostPopular[$Photo->Owner]->PhotoId))
			$MostPopular[$Photo->Owner]->PhotoId=$Photo->Id;

		unset($Photo);
	}

	$AllSlides=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,URL_{$ActiveLanguage->Prefix} AS URL,ButtonLabel_{$ActiveLanguage->Prefix} AS ButtonLabel FROM {$GLOBAL->Slider} WHERE `Status`='Y' ORDER BY `Position`;");

	$IsMainPage=true;

	include ".{$DIRS->modules}so/h.inc";
?>
	<section class="slider pro-carousel" id="slider" data-type="1" data-controls="2">
<?php
	$ThisItem=0;
	foreach($AllSlides as $Slide){
		if(is_file(".".$DIRS->Slider.$Slide->Id."_".$ActiveLanguage->Prefix.".jpg")){
		$class=$ThisItem++==0?" current":"";
?>
		<div class="slide slide<?=$Slide->Id?><?=$class?>">
<?php
		if(!empty($Slide->URL)){
?>
			<a href="<?=$Slide->URL?>" target="_blank">
<?php
		}
?>
			<span class="body">
<?php
		if(!empty($Slide->Title)){
?>
				<span class="title"><?=$Slide->Title?></span>
<?php
		}
		if(!empty($Slide->ButtonLabel)){
?>
				<span class="button"><?=$Slide->ButtonLabel?></span>
<?php
		}
?>
			</span>
<?php
		if(!empty($Slide->URL)){
?>
			</a>
<?php
		}
?>
		</div>
<?php
		}
		unset($Slide);
	}
?>
	</section>
	<section class="productList body">
		<h2 class="title"><?=$LangPack->BestSellers?></h2>
		<div class="items">
<?php
	foreach($BestSellers as $Product){
		$Kernel->PrintProductBlock($Product,"MAIN");
		unset($Product);
	}
?>
		</div>
	</section>
	<section class="productList body">
		<h2 class="title"><?=$LangPack->MostPopular?></h2>
		<div class="items">
<?php
	foreach($MostPopular as $Product){
		$Kernel->PrintProductBlock($Product,"MAIN");
		unset($Product);
	}
?>
		</div>
	</section>
	<section class="mainBanner body"><?=$TextBlocks->MainBanner->Description?></section>
	<section class="brands body">
<?php
	foreach($AllBrands as $Brand){
?>
		<a href="<?=$ActiveLanguage->LinksPrefix?>/products/?brand[]=<?=$Brand->Id?>"><img src="<?=$DIRS->Brands.$Brand->Id?>_bl.png?v1" alt="<?=$Brand->Title?>" title="<?=$Brand->Title?>" /></a>
<?php
		unset($Brand);
	}
?>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>
