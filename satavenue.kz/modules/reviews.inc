<?php
	$ShowReview=false;
	$BreadCrumbs[1]="<a href=\"#\">Информация</a>";
	if(preg_match("'^/([\d]{1,11})-(.*?)/$'si",$SUB_REQUEST_URI,$Id)){
		$Id=(int) $Id[1];

		if($ReviewInfo=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->Reviews} WHERE Id={$Id} AND `Status`='Y';",false)){
			$ShowReview=true;
			$Page->Title=$ReviewInfo->Title;
			$Page->MenuTitle=$ReviewInfo->Title;

		}else{
			$Kernel->e404();
		}
	}

	if(!$ShowReview){
		unset($BreadCrumbs[2]);
		$InPage=10;$PageNum=(int) @$_GET["p"];
		$LIMIT=$InPage*$PageNum;

		$AllReviews=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->Reviews} WHERE `Status`='Y' ORDER BY `DateTime` DESC LIMIT {$LIMIT},{$InPage};");
		$Pages=$DataBase->Query("SELECT COUNT(*) AS Reviews FROM {$GLOBAL->Reviews} WHERE `Status`='Y';",false,array(),"Reviews");
		$Pages=ceil($Pages->Reviews/$InPage);

		$Pages=$Kernel->CreatePageListener($Pages,$PageNum,"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?p=",10);
	}

	if ($ShowReview) {
		$ShowTitle = false;
	}

	include ".{$DIRS->modules}so/h.inc";

	if(!$ShowReview){
?>
	<section class="reviewList body">
		<div class="items">
<?php
		foreach($AllReviews as $Review){
			$Kernel->PrintReviewBlock($Review,"LIST");
			unset($Review);
		}
?>
		</div>
		<div class="pagesNum"><?=$Pages?></div>
	</section>
<?php
	}else{
?>
	<div class="bgReviewInfo">
		<div class="bg" style="background-image: url(<?=$DIRS->Reviews.$ReviewInfo->Id?>.jpg);">
			<div class="bg-cover">
				<div class="text-oncover body"><?=$ReviewInfo->Title?></div>
			</div>
		</div>
		<div class="bgc-share">
			<div class="body">
				<div class="shareInfo">
					<div class="wrapper">
						<div class="social-icon t-align-m d-ib">
						<?php
							foreach($SocialList as $Type=>$Social){
						?>
							<a href="<?=$Social[0]?>" target="_blank" title="<?=$Social[1]?>" class="<?=$Type?>"></a>
						<?php
								unset($Type);
								unset($Social);
							}
						?>
						</div>
						<div class="author-info t-align-m d-ib">
							<div class="date d-ib"><?=date("d.m.y",$ReviewInfo->DateTime)?></div>
						</div>
					</div>
					<div class="short-description"><?=$ReviewInfo->ShortDescription?></div>
				</div>
			</div>
		</div>
		<section class="body textPage">
			<?=$ReviewInfo->Description?>
		</section>
	</div>
<?php
	}
?>
<? include ".{$DIRS->modules}so/f.inc";?>