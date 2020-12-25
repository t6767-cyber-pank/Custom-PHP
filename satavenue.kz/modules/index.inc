<?php
	$BestSellers=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE `Status`='Y' AND `IsSell`='Y' ORDER BY `Id` DESC LIMIT 0,8;",true,array(),"Id",false,false,array("PhotoId"=>"int"));
	$MostPopular=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE `Status`='Y' AND `IsView`='Y' ORDER BY `Id` DESC LIMIT 0,4;",true,array(),"Id",false,false,array("PhotoId"=>"int"));
	$Shares=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Shares} WHERE `Status`='Y' ORDER BY `Id` DESC ",true,array(),"Id",false,false,array("PhotoId"=>"int"));
	$AllReviews=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->Reviews} WHERE `Status`='Y' ORDER BY `DateTime` DESC LIMIT 10;");
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
		if(isset($BestSellers[$Photo->Owner])&&empty($BestSellers[$Photo->Owner]->PhotoId))
			$BestSellers[$Photo->Owner]->PhotoId=$Photo->Id;
		if(isset($MostPopular[$Photo->Owner])&&empty($MostPopular[$Photo->Owner]->PhotoId))
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
				if (!empty($Slide->URL)){
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
					if (!empty($Slide->URL)){
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
<section class="productList body shareList">
	<div class="title"><a href="http://satavenue-kz/products/">хиты продаж</a></div>
	<div class="items index-class">
		<div id="product-slider">
		<?php $chunks = array_chunk($BestSellers, 1) ?>
		<?php foreach ($chunks as $chunk): ?>
				<?php foreach ($chunk as $Product): ?>
					<div class="cover">
						<?= $Kernel->PrintProductBlock($Product,"MAIN");?>
					</div>
				<?php endforeach ?>
		<?php endforeach ?>
		</div>
	</div>
</section>
<section class="sharesList body">
	<div class="title"><a href="#">последние акции</a></div>
	<div class="items index-class">
		<div id="sales-slider">
		<?php foreach ($Shares as $Share): ?>
			<div class="slide-index">
				<?= $Kernel->PrintShareBlock($Share,"LIST");?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
</section>

<section class="warning-message body">
	<div class="title"><a href="#">оповещение</a></div>
	<a href="http://www.satavenue.kz/products/sim/voice/" class="wm_body">
		<div class="wm_content">
			<div class="wm_title">Уважаемый Абонент, c 1 апреля 2019 года спутниковый оператор Thuraya (ОАЭ) вводит новые условия для пользователей (держателей) SIM-карт Thuraya Prepaid</div>
			<ol class="wm_list">
				<li><span>Срок действия SIM карты — 12 месяцев, плата за продления срока действия — 39 юнит.</span></li>
				<li><span>Срок действия пополненного баланса (unit) будет ограничен: все номиналы (20, 39, 50, 80 и 160 unit) будут сроком на 12 месяцев, а номинал в 10 unit — на 6 месяцев.</span></li>
			</ol>
		</div>
	</a>
</section>
	<section class="reviewList body main-page">
		<div class="title"><a href="#">свежие новости и обзоры в нашем блоге</a></div>
		<div class="items main-items">
<?php
		foreach($AllReviews as $Review){
			$Kernel->PrintReviewBlock($Review,"LIST");
			unset($Review);
		}
?>
		</div>
		<div class="pagesNum"><?=$Pages?></div>
	</section>	
<? include ".{$DIRS->modules}so/f.inc"; ?>