<?php
	$ShowSuccessPage=false;

	$AllBrands=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,VoucherType_{$ActiveLanguage->Prefix} AS VoucherType,PhoneLabel_{$ActiveLanguage->Prefix} AS PhoneLabel,UnitTitle_{$ActiveLanguage->Prefix} AS UnitTitle FROM {$GLOBAL->Brands} WHERE `Status`='Y' ORDER BY `Position`;");
	$AllSimPackages=$DataBase->Query("SELECT *,Period_{$ActiveLanguage->Prefix} AS Period,Message_{$ActiveLanguage->Prefix} AS Message FROM {$GLOBAL->SimPackages} WHERE `Status`='Y' ORDER BY `Position`;",true,array(),array("BrandId","Id"));

if(isset($_GET["pay"])){
	$brand=(int)$_POST["brand"];
	$package=(int)$_POST["package"];
	$to=$Kernel->PostToSQL($_POST["phone"]);
	$from=$Kernel->PostToSQL($_POST["mobile"]);
	$email=$Kernel->PostToSQL($_POST["email"]);
	$email=urlencode($email);
	$findSum=$DataBase->Query("SELECT * FROM {$GLOBAL->SimPackages} WHERE `BrandId`={$brand} AND `Id`={$package}",false);
	if(!empty($findSum)){
		if ($findSum->Id == 28 && isset($_POST['custom_price']) && $_POST['custom_price'] >= $LangPack->MinimumCustomPrice) {
			$findSum->Price = $_POST['custom_price'];
		}
		$GLOBAL->UserInfo->Id=(int)@$GLOBAL->UserInfo->Id;
		$FundsInfo=$Kernel->CreateBasket($GLOBAL->UserInfo->Id,"F");
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET `TotalToPay`={$findSum->Price},`TotalSumm`={$findSum->Price},`PackageId`={$findSum->Id},`FundsBrand`={$brand},`FundsFrom`='{$from}',`FundsTo`='{$to}',`EMail`='{$email}' WHERE `Id`={$FundsInfo->Id}");
		require_once("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/kkb.utils.php");
		$pathConfig="/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/config.txt";
		$currencyId=398;
?>
		<form id="redirect" name="redirect" action="https://epay.kkb.kz/jsp/process/logon.jsp" method="post">
			<input id="base64" name="Signed_Order_B64" value="<?=process_request($FundsInfo->Id,$currencyId,$findSum->Price,$pathConfig)?>" type="hidden">
			<input name="Language" value="rus" type="hidden">
			<input id="BackLink" name="BackLink" value="http://www.satavenue.kz/funds/?send&payOnline&id=<?=$FundsInfo->Id?>" type="hidden">
			<input id="PostLink" name="PostLink" value="http://www.satavenue.kz/payed/?funds" type="hidden">
			<input id="FailurePostLink" name="FailurePostLink" value="http://www.satavenue.kz/funds/?send&payOnline&error" type="hidden">
		</form>
		<script type="text/javascript">
			document.redirect.submit();
		</script>
<?php
		exit;
	}
}

	include ".{$DIRS->modules}so/h.inc";
?>
	<?php
	if(isset($_GET["payOnline"])){
		if(isset($_GET["id"])){
			$id=(int)$_GET["id"];
			$idMD5=md5($id);
			$OrderInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `Id`={$id} AND `UserId`={$GLOBAL->UserInfo->Id}",false);
			if(!empty($OrderInfo)){
				if($OrderInfo->Status=="P" && !empty($OrderInfo->PayDateTime) && $OrderInfo->PayMethod=="ONLINE"){
					header("Location: ?payOnline&success");
					exit;
				}else{
					header("Location: ?send&payOnline&error");
					exit;
				}
			}else{
				header("Location: ?payOnline&error");
				exit;
			}
		}
	?>
	<section class="messages body">
		<?php if(isset($_GET["success"])){ ?>
		<p><?=$LangPack->TempKKBPaySuccess?></p>
		<?php }elseif(isset($_GET["error"])){ ?>
		<p><?=$LangPack->TempKKBPayError?></p>
		<?php } ?>
	</section>
	<?php } ?>
	<section class="addFunds body">
		<div class="tabs-label">
			Выберите провайдера
		</div>
		<div class="tabs">
<?php
	$ThisItem=0;
	foreach($AllBrands as $Brand){
		if(isset($AllSimPackages[$Brand->Id])){
			$class=$ThisItem++==0?" ":"";
?>
			<a href="#<?=$Brand->Name?>" data-brand="<?=$Brand->Id?>" class="tab<?=$class?>"><span><?=$Brand->Title?></span></a>
<?php
		}
	}
?>
		</div>
		<div class="formBlock">
			<div class="leftBlock">
				<form action="?pay" method="post">
					<div class="brandTabs">
<?php
	$ThisItem=0;$PackageItem=0;
	foreach($AllBrands as $Brand){
		if(isset($AllSimPackages[$Brand->Id])){
			$class=$ThisItem++==0?" ":"";
?>
						<div class="brand <?=$Brand->Name?><?=$class?>">
							<h3 class="title"><?=$Brand->VoucherType?></h3>
							<ul>
<?php
			foreach($AllSimPackages[$Brand->Id] as $Package){
				$Title=array();
				$Title[]=$Kernel->DigitalToPrice($Package->Units)." ".$Brand->UnitTitle;
				if(!empty($Package->Period))
					$Title[]=$Package->Period;
				$Title[]=str_replace("%PRICE%",$Kernel->DigitalToPrice($Package->Price),$LangPack->PriceTg);
				$Title=join(" - ",$Title);
?>	
								<?php if ($Package->Id == 28): ?>
									<li><?=$HtmlTags->InputRadio("package",$Package->Id,$LangPack->SimPackage28Title,false,"","package{$Package->Id}","radio",true)?><?=""?>
									</li>
									<input type="number" name="custom_price" value="" min="<?=$LangPack->SimPackage28Minimum?>" id="simproduct-custom-input" style="display: none; font-size: 14px; height: auto !important;">
								<?php else: ?>
									<li><?=$HtmlTags->InputRadio("package",$Package->Id,$Title,false,"","package{$Package->Id}","radio",true)?><?=!empty($Package->Message)?"<span class=\"hint\">".$Package->Message."</span>":""?></li>
								<?php endif ?>
<?php
				unset($Package);
			}
?>
							</ul>
							<div><?=$Package?></div>
						</div>
<?php
		}
		unset($Brand);
	}
?>
					</div>
					<div class="form" id="fundsFullForm" style="display: none;">
						<div class="title"><?=$LangPack->FillAllDataForCustomer?></div>
<?php
	$ThisItem=0;
	foreach($AllBrands as $Brand){
		$class=$ThisItem++==0?" current":"";
?>
						<label for="phone" class="brand <?=$Brand->Name?><?=$class?>"><?=$Brand->PhoneLabel?></label>
<?php
		unset($Brand);
	}
reset($AllBrands);
?>
						<?=$HtmlTags->InputText("phone",@$_POST["phone"],"","text","phone","text phone-pattern",0,false,0,"","","","","","",true)?>
						<label for="mobile"><?=$LangPack->CustomerMobile?></label>
						<?=$HtmlTags->InputText("mobile",@$_POST["mobile"],"","text","mobile","text ",0,false,0,"","","","","","",true)?>
						<label for="email"><?=$LangPack->CustomerEMail?></label>
						<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text email-pattern",0,false,0,"","","","","","",true)?>
					</div>
					<div class="rightBlock" style="display: none;"><?=$Page->Description?></div>
					<div class="submit" style="display: none;">
						<input type="hidden" id="brand" name="brand" value="<?=current($AllBrands)->Id?>" />
						<?=$HtmlTags->InputCheckbox("conditions","y",$TextBlocks->AddFundConditions->Description,false,"","conditions","",true,"checkbox",true,false,true)?>
						<?=$HtmlTags->InputSubmit($LangPack->pay,"","","",true,"submit","submit")?>
					</div>
				</form>
			</div>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>