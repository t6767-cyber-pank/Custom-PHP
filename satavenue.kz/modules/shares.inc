<?php
	$ShowShare=false;
	$BreadCrumbs[1]="<a href=\"#\">Информация</a>";
	if(preg_match("'^/([\d]{1,11})-(.*?)/$'si",$SUB_REQUEST_URI,$Id)){
		$Id=(int) $Id[1];

		if($ShareInfo=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->Shares} WHERE Id={$Id} AND `Status`='Y';",false)){
			$ShowShare=true;
			$Page->Title=$ShareInfo->Title;
			$Page->MenuTitle=$ShareInfo->Title;

		}else{
			$Kernel->e404();
		}
	}

	$ShowTitle = false;

	$DateTime = date('Y-m-d');

	if(!$ShowShare){
		unset($BreadCrumbs[2]);
		$InPage=10;$PageNum=(int) @$_GET["p"];
		$LIMIT=$InPage*$PageNum;

		$AllShares=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->Shares} WHERE `Status`='Y' AND `FinishTime`>={$DateTime} ORDER BY `id` DESC LIMIT {$LIMIT},{$InPage};");
		$Pages=$DataBase->Query("SELECT COUNT(*) AS Shares FROM {$GLOBAL->Shares} WHERE `Status`='Y' AND `FinishTime`>={$DateTime};",false,array(),"Shares");
		$Pages=ceil($Pages->Shares/$InPage);

		$Pages=$Kernel->CreatePageListener($Pages,$PageNum,"{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?p=",10);
	}

	include ".{$DIRS->modules}so/h.inc";

	if(!$ShowShare){
?>
	<section class="ShareList body">
		<div class="items">

<?php
		foreach($AllShares as $Share){
			$Kernel->PrintShareBlock($Share,"LIST");
			unset($Share);
		}
?>
		</div>
		<div class="pagesNum"><?=$Pages?></div>
	</section>
<?php
	}else{
?>
	<div class="bgReviewInfo">
		<div class="bg" style="background-image: url(<?=$DIRS->Shares.$ShareInfo->Id?>.jpg);">
			<div class="bg-cover">
				<div class="text-oncover body"><?=$ShareInfo->Title?></div>
			</div>
		</div>
<!-- 		<img src="<?=$DIRS->Shares.$ShareInfo->Id?>_sl.jpg" alt="<?=$ShareInfo->Title?>" title="<?=$ShareInfo->Title?>"/></div> -->
	<div class="bgc-share" id="bgr">
		<div class="body">
			<div class="shareInfo">
				<div class="wrapper" id="wrapakcii">
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
						<div class="author d-ib">Акция действительна до </div>
						<div class="date d-ib"><?=date("d",$ShareInfo->FinishTime)?> <?=$AllMonths[date("n",$ShareInfo->FinishTime)][1]?></div>
					</div>
				</div>
				<div class="short-description"><?=$ShareInfo->SubTitle?></div>
			</div>
		</div>
	</div>
	<section class="textPage body">
		<?=$ShareInfo->Description?>
	</section>
<?php
	}
?>
<? include ".{$DIRS->modules}so/f.inc";?>