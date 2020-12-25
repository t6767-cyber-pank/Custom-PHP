<?php
	if(!$IsAuth){
		header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}login/");
		exit;
	}

if(isset($_GET["signout"])){
	$Kernel->DeleteUserSession($GLOBAL->UserInfo);
	header("Location: {$ActiveLanguage->LinksPrefix}{$Kernel->ExtractPageOptions(19)->FullAddress}");
}

	$Messages=array(
		"ERROR"=>array(),
		"LOG"=>array()
	);

	$ShowEdit=false;
	$ShowPassword=false;

	if(isset($_GET["send"])){
		$Message=$Kernel->PostToSQL(@$_POST["review"],true,true,2048);

		if(empty($Message))
			$Messages["ERROR"][]=$LangPack->ErrorsMessage;

		if(count($Messages["ERROR"])){
			$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";

			if(isset($_GET["ajax"])){
				echo $Messages["ERROR"];
				exit;
			}
		}else{
			$Title=str_replace(
				array("%NAME%","%SURNAME%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),
				array($GLOBAL->UserInfo->Name,$GLOBAL->UserInfo->SurName,urldecode($GLOBAL->UserInfo->EMail),$GLOBAL->UserInfo->Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
				$TextBlocks->AuthContactRequestMessage->Title
			);
			$Message=str_replace(
				array("%NAME%","%SURNAME%","%PAGETITLE%","%PAGEURL%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),
				array($GLOBAL->UserInfo->Name,$GLOBAL->UserInfo->SurName,$Page->Title,$OWN_REQUEST,urldecode($GLOBAL->UserInfo->EMail),$GLOBAL->UserInfo->Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
				$TextBlocks->AuthContactRequestMessage->Description
			);

			foreach($MailTo as $Mail){
				$Kernel->SendMessage($Mail,$Title,$Message);
			}

			if(isset($_GET["ajax"])){
				echo "<div class=\"message log\">{$TextBlocks->ContactRequestSended->Description}</div>";
				exit;
			}else{
				header("Location: ?sended");
				exit;
			}
		}
	}
	if(isset($_GET["edit"])){
		if(isset($_GET["submit"])){
			$SQL=array();
			
			$Name=$Kernel->PostToSQL(@$_POST["name"]);
			$SurName=$Kernel->PostToSQL(@$_POST["surname"]);
			$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
			
			if(!empty($Name))
				$SQL[]="`Name`='{$Name}'";
			if(!empty($SurName))
				$SQL[]="`SurName`='{$SurName}'";
			if(!empty($Phone))
				$SQL[]="`Phone`='{$Phone}'";


			if($GLOBAL->UserInfo->Type=="CUSTOMER"){
				$City=$Kernel->PostToSQL(@$_POST["city"]);
				$SQL[]="`City`='{$City}'";

				$Street=$Kernel->PostToSQL(@$_POST["street"]);
				$SQL[]="`Street`='{$Street}'";

				$House=$Kernel->PostToSQL(@$_POST["house"]);
				$SQL[]="`House`='{$House}'";

				$Apartment=$Kernel->PostToSQL(@$_POST["apartment"]);
				$SQL[]="`Apartment`='{$Apartment}'";

				$INN=$Kernel->PostToSQL(@$_POST["inn"]);
				$SQL[]="`INN`='{$INN}'";

				$BIN=$Kernel->PostToSQL(@$_POST["bin"]);
				$SQL[]="`BIN`='{$BIN}'";

				$Address=$Kernel->PostToSQL(@$_POST["address"]);
				$SQL[]="`Address`='{$Address}'";
			}elseif($GLOBAL->UserInfo->Type=="LEGAL"){
				$ShortOrganization=$Kernel->PostToSQL(@$_POST["shortOrganization"]);
				$SQL[]="`ShortOrganization`='{$ShortOrganization}'";

				$Organization=$Kernel->PostToSQL(@$_POST["organization"]);
				$SQL[]="`Organization`='{$Organization}'";

				$CompanyIIN=$Kernel->PostToSQL(@$_POST["companyIin"]);
				$SQL[]="`CompanyIIN`='{$CompanyIIN}'";

				$KPP=$Kernel->PostToSQL(@$_POST["kpp"]);
				$SQL[]="`KPP`='{$KPP}'";

				$OKPO=$Kernel->PostToSQL(@$_POST["okpo"]);
				$SQL[]="`OKPO`='{$OKPO}'";

				$BIK=$Kernel->PostToSQL(@$_POST["bik"]);
				$SQL[]="`BIK`='{$BIK}'";

				$Bank=$Kernel->PostToSQL(@$_POST["bank"]);
				$SQL[]="`PaymentBank`='{$Bank}'";

				$CorrespondentAccount=$Kernel->PostToSQL(@$_POST["correspondentAccount"]);
				$SQL[]="`CorrespondentAccount`='{$CorrespondentAccount}'";

				$PaymentAccount=$Kernel->PostToSQL(@$_POST["paymentAccount"]);
				$SQL[]="`PaymentAccount`='{$PaymentAccount}'";

				$SecondName=$Kernel->PostToSQL(@$_POST["secondname"]);
				$SQL[]="`SecondName`='{$SecondName}'";

				$IIN=$Kernel->PostToSQL(@$_POST["iin"]);
				$SQL[]="`IIN`='{$IIN}'";

				$BIN=$Kernel->PostToSQL(@$_POST["bin"]);
				$SQL[]="`BIN`='{$BIN}'";

				$Address=$Kernel->PostToSQL(@$_POST["address"]);
				$SQL[]="`Address`='{$Address}'";

				$Index=$Kernel->PostToSQL(@$_POST["index"]);
				$SQL[]="`Index`='{$Index}'";

				$Region=$Kernel->PostToSQL(@$_POST["region"]);
				$SQL[]="`Region`='{$Region}'";

				$District=$Kernel->PostToSQL(@$_POST["district"]);
				$SQL[]="`District`='{$District}'";

				$City=$Kernel->PostToSQL(@$_POST["city"]);
				$SQL[]="`City`='{$City}'";

				$Street=$Kernel->PostToSQL(@$_POST["street"]);
				$SQL[]="`Street`='{$Street}'";

				$House=$Kernel->PostToSQL(@$_POST["house"]);
				$SQL[]="`House`='{$House}'";

				$Hull=$Kernel->PostToSQL(@$_POST["hull"]);
				$SQL[]="`Hull`='{$Hull}'";

				$Office=$Kernel->PostToSQL(@$_POST["office"]);
				$SQL[]="`Office`='{$Office}'";

				$Building=$Kernel->PostToSQL(@$_POST["building"]);
				$SQL[]="`Building`='{$Building}'";

				$LegalIndex=$Kernel->PostToSQL(@$_POST["legalIndex"]);
				$SQL[]="`LegalIndex`='{$LegalIndex}'";

				$LegalRegion=$Kernel->PostToSQL(@$_POST["legalRegion"]);
				$SQL[]="`LegalRegion`='{$LegalRegion}'";

				$LegalCity=$Kernel->PostToSQL(@$_POST["legalCity"]);
				$SQL[]="`LegalCity`='{$LegalCity}'";

				$LegalDistrict=$Kernel->PostToSQL(@$_POST["legalDistrict"]);
				$SQL[]="`LegalDistrict`='{$LegalDistrict}'";

				$LegalStreet=$Kernel->PostToSQL(@$_POST["legalStreet"]);
				$SQL[]="`LegalStreet`='{$LegalStreet}'";

				$LegalHouse=$Kernel->PostToSQL(@$_POST["legalHouse"]);
				$SQL[]="`LegalHouse`='{$LegalHouse}'";

				$LegalHull=$Kernel->PostToSQL(@$_POST["legalHull"]);
				$SQL[]="`LegalHull`='{$LegalHull}'";

				$LegalOffice=$Kernel->PostToSQL(@$_POST["legalOffice"]);
				$SQL[]="`LegalOffice`='{$LegalOffice}'";
			}

			/* TODO
			 * 		1. Админка для корзин
			 * 		2. Админка для пользователей
			 */

			if(count($SQL)){
				$SQL=join(",",$SQL);
				$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET {$SQL} WHERE Id={$GLOBAL->UserInfo->Id};");
			}

			$Kernel->GetResponse("OK:{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");

			header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
			exit;
		}

		$ShowEdit=true;
	}
	if(isset($_GET["pass"])){
		if(isset($_GET["submit"])){
			$Password=@$_POST["oldpass"];
			$Pass1=@$_POST["pass1"];
			$Pass2=@$_POST["pass2"];

			$Password=$Kernel->CreatePasswordCash($Password,$GLOBAL->UserInfo->DateTime);

			if(empty($Pass1) || mb_strlen($Pass1,"UTF-8")<$LangPack->PasswordMinLength)
				$Messages["ERROR"][]=$LangPack->ErrorsPasswordLength;
			if(empty($Pass2) || mb_strlen($Pass2,"UTF-8")<$LangPack->PasswordMinLength)
				$Messages["ERROR"][]=$LangPack->ErrorsPasswordLength;
			if($Pass1!==$Pass2)
				$Messages["ERROR"][]=$LangPack->ErrorsPasswordSame;
			if($Password!==$GLOBAL->UserInfo->Password)
				$Messages["ERROR"][]=$LangPack->OldPasswordNoMatch;

			if(count($Messages["ERROR"])==0){
				$Password=$Kernel->CreatePasswordCash($Pass1,$GLOBAL->UserInfo->DateTime);

				$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Password`='{$Password}' WHERE Id={$GLOBAL->UserInfo->Id};");

				if(isset($_GET["ajax"])){
					echo "<div class=\"message log\">{$TextBlocks->PasswordChanged->Description}</div>";
					exit;
				}
			}

			if(count($Messages["ERROR"])){
				$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";

				if(isset($_GET["ajax"])){
					echo "<div class=\"message error\">";
					echo $Messages["ERROR"];
					echo "</div>";
					exit;
				}
			}
		}

		$ShowPassword=true;
	}

	if(isset($_GET["sended"])){
		$Messages["LOG"][]=$TextBlocks->ContactRequestSended->Description;

		$Messages["LOG"]="<p>".join("</p><p>",$Messages["LOG"])."</p>";
	}

	$SubPages=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS MenuTitle FROM {$GLOBAL->SystemPages} WHERE `Owner`={$Page->FullIds[1]} AND `MenuVisible`='Y' ORDER BY `Position`;");

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
		<div class="authInfo">
			<div class="infoBlock">
<?php
	if(!$ShowEdit && !$ShowPassword){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthName?></span>
					<span class="value"><?=$GLOBAL->UserInfo->Name?></span>
				</div>
				<div class="line">
					<span class="title"><?=$LangPack->AuthSurName?></span>
					<span class="value"><?=$GLOBAL->UserInfo->SurName?></span>
				</div>
				<div class="line">
					<span class="title"><?=$LangPack->AuthEMail?></span>
					<span class="value"><?=urldecode($GLOBAL->UserInfo->EMail)?></span>
				</div>
				<div class="line">
					<span class="title"><?=$LangPack->AuthPhone?></span>
					<span class="value"><?=$GLOBAL->UserInfo->Phone?></span>
				</div>
<?php
		if($GLOBAL->UserInfo->Type=="CUSTOMER"){
			if(!empty($GLOBAL->UserInfo->City)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthCity?></span>
					<span class="value"><?=$GLOBAL->UserInfo->City?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->Street)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthStreet?></span>
					<span class="value"><?=$GLOBAL->UserInfo->Street?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->House)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthHouse?></span>
					<span class="value"><?=$GLOBAL->UserInfo->House?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->Apartment)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthApartment?></span>
					<span class="value"><?=$GLOBAL->UserInfo->Apartment?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->INN)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthINN?></span>
					<span class="value"><?=$GLOBAL->UserInfo->INN?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->BIN)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthBIN?></span>
					<span class="value"><?=$GLOBAL->UserInfo->BIN?></span>
				</div>
<?php
			}
			if(!empty($GLOBAL->UserInfo->Address)){
?>
				<div class="line">
					<span class="title"><?=$LangPack->AuthAddress?></span>
					<span class="value"><?=$GLOBAL->UserInfo->Address?></span>
				</div>
<?php
			}
		}
?>
				<div class="submit">
					<a href="?edit" class="edit"><?=$LangPack->EditMyInfo?></a>
					<a href="?pass" class="submit"><?=$LangPack->ChangePassword?></a>
				</div>
<?php
	}elseif($ShowEdit){
?>
				<form action="?edit&submit" method="post">
					<label for="name"><?=$LangPack->NameReq?></label>
					<?=$HtmlTags->InputText("name",$GLOBAL->UserInfo->Name,"","text","name","text",0,false,0,"","","","","","",true)?>
					<label for="surname"><?=$LangPack->SurNameReq?></label>
					<?=$HtmlTags->InputText("surname",$GLOBAL->UserInfo->SurName,"","text","surname","text",0,false,0,"","","","","","",true)?>
<?php
		if($GLOBAL->UserInfo->Type=="LEGAL"){
?>
					<label for="secondname"><?=$LangPack->SecondName?></label>
					<?=$HtmlTags->InputText("secondname",$GLOBAL->UserInfo->SecondName,"","text","secondname","text")?>
<?php
		}
?>
					<label for="phone"><?=$LangPack->PhoneReq?></label>
					<?=$HtmlTags->InputText("phone",$GLOBAL->UserInfo->Phone,"","text","phone","text",0,false,0,"","","","","","",true)?>
<?php
		if($GLOBAL->UserInfo->Type=="CUSTOMER"){
?>
					<label for="city"><?=$LangPack->CityReq?></label>
					<?=$HtmlTags->InputText("city",$GLOBAL->UserInfo->City,"","text","city","text",0,false,0,"","","","","","",true)?>
					<label for="street"><?=$LangPack->StreetReq?></label>
					<?=$HtmlTags->InputText("street",$GLOBAL->UserInfo->Street,"","text","street","text",0,false,0,"","","","","","",true)?>
					<label for="house"><?=$LangPack->HouseReq?></label>
					<?=$HtmlTags->InputText("house",$GLOBAL->UserInfo->House,"","text","house","text",0,false,0,"","","","","","",true)?>
					<label for="apartment"><?=$LangPack->ApartmentReq?></label>
					<?=$HtmlTags->InputText("apartment",$GLOBAL->UserInfo->Apartment,"","text","apartment","text",0,false,0,"","","","","","",true)?>
					<label for="inn"><?=$LangPack->INNReq?></label>
					<?=$HtmlTags->InputText("inn",$GLOBAL->UserInfo->INN,"","text","inn","text",0,false,0,"","","","","","",true)?>
					<label for="bin"><?=$LangPack->BINReq?></label>
					<?=$HtmlTags->InputText("bin",$GLOBAL->UserInfo->BIN,"","text","bin","text",0,false,0,"","","","","","",true)?>
					<label for="address"><?=$LangPack->AddressReq?></label>
					<?=$HtmlTags->InputText("address",$GLOBAL->UserInfo->Address,"","text","address","text",0,false,0,"","","","","","",true)?>
<?php
		}elseif($GLOBAL->UserInfo->Type=="LEGAL"){
?>
					<label for="shortOrganization"><?=$LangPack->ShorOrganization?></label>
					<?=$HtmlTags->InputText("shortOrganization",$GLOBAL->UserInfo->ShortOrganization,"","text","shortOrganization","text",0,false,0,"","","","","","",true)?>
					<label for="organization"><?=$LangPack->Organization?></label>
					<?=$HtmlTags->InputText("organization",$GLOBAL->UserInfo->Organization,"","text","organization","text",0,false,0,"","","","","","",true)?>
					<label for="companyIin"><?=$LangPack->IIN?></label>
					<?=$HtmlTags->InputText("companyIin",$GLOBAL->UserInfo->CompanyIIN,"","text","companyIin","text",0,false,0,"","","","","","",true)?>
					<label for="kpp"><?=$LangPack->KPP?></label>
					<?=$HtmlTags->InputText("kpp",$GLOBAL->UserInfo->KPP,"","text","kpp","text",0,false,0,"","","","","","",true)?>
					<label for="okpo"><?=$LangPack->OKPO?></label>
					<?=$HtmlTags->InputText("okpo",$GLOBAL->UserInfo->OKPO,"","text","okpo","text",0,false,0,"","","","","","",true)?>
					<label for="bik"><?=$LangPack->BIK?></label>
					<?=$HtmlTags->InputText("bik",$GLOBAL->UserInfo->BIK,"","text","bik","text",0,false,0,"","","","","","",true)?>
					<label for="bank"><?=$LangPack->PaymentBank?></label>
					<?=$HtmlTags->InputText("bank",$GLOBAL->UserInfo->PaymentBank,"","text","bank","text",0,false,0,"","","","","","",true)?>
					<label for="correspondentAccount"><?=$LangPack->CorrespondentAccount?></label>
					<?=$HtmlTags->InputText("correspondentAccount",$GLOBAL->UserInfo->CorrespondentAccount,"","text","correspondentAccount","text",0,false,0,"","","","","","",true)?>
					<label for="paymentAccount"><?=$LangPack->PaymentAccount?></label>
					<?=$HtmlTags->InputText("paymentAccount",$GLOBAL->UserInfo->PaymentAccount,"","text","paymentAccount","text",0,false,0,"","","","","","",true)?>
					<label for="legalIin"><?=$LangPack->IINReq?></label>
					<?=$HtmlTags->InputText("iin",$GLOBAL->UserInfo->IIN,"","text","legalIin","text",0,false,0,"","","","","","",true)?>
					<label for="legalBin"><?=$LangPack->BINReq?></label>
					<?=$HtmlTags->InputText("bin",$GLOBAL->UserInfo->BIN,"","text","legalBin","text",0,false,0,"","","","","","",true)?>
					<label for="legalAddress"><?=$LangPack->AddressReq?></label>
					<?=$HtmlTags->InputText("address",$GLOBAL->UserInfo->Address,"","text","legalAddress","text",0,false,0,"","","","","","",true)?>
					<div class="title"><?=$LangPack->LegalAddress?></div>
					<label for="index"><?=$LangPack->Index?></label>
					<?=$HtmlTags->InputText("index",$GLOBAL->UserInfo->Index,"","text","index","text",0,false,0,"","","","","","",true)?>
					<label for="region"><?=$LangPack->Region?></label>
					<?=$HtmlTags->InputText("region",$GLOBAL->UserInfo->Region,"","text","region","text",0,false,0,"","","","","","",true)?>
					<label for="district"><?=$LangPack->District?></label>
					<?=$HtmlTags->InputText("district",$GLOBAL->UserInfo->District,"","text","district","text",0,false,0,"","","","","","",true)?>
					<label for="city"><?=$LangPack->City?></label>
					<?=$HtmlTags->InputText("city",$GLOBAL->UserInfo->City,"","text","city","text",0,false,0,"","","","","","",true)?>
					<label for="street"><?=$LangPack->Street?></label>
					<?=$HtmlTags->InputText("street",$GLOBAL->UserInfo->Street,"","text","street","text",0,false,0,"","","","","","",true)?>
					<label for="house"><?=$LangPack->House?></label>
					<?=$HtmlTags->InputText("house",$GLOBAL->UserInfo->House,"","text","house","text",0,false,0,"","","","","","",true)?>
					<label for="hull"><?=$LangPack->Hull?></label>
					<?=$HtmlTags->InputText("hull",$GLOBAL->UserInfo->Hull,"","text","hull","text")?>
					<label for="office"><?=$LangPack->Office?></label>
					<?=$HtmlTags->InputText("office",$GLOBAL->UserInfo->Office,"","text","office","text")?>
					<label for="building"><?=$LangPack->Building?></label>
					<?=$HtmlTags->InputText("building",$GLOBAL->UserInfo->Building,"","text","building","text")?>
					<div class="title"><?=$LangPack->DeliveryLegalFormTitle?></div>
					<label for="courierLegalIndex"><?=$LangPack->Index?></label>
					<?=$HtmlTags->InputText("legalIndex",$GLOBAL->UserInfo->Index,"","text","courierLegalIndex","text",0,false,0,"","","","","","",true)?>
					<label for="courierLegalRegion"><?=$LangPack->Region?></label>
					<?=$HtmlTags->InputText("legalRegion",$GLOBAL->UserInfo->Region,"","text","courierLegalRegion","text",0,false,0,"","","","","","",true)?>
					<label for="courierLegalCity"><?=$LangPack->City?></label>
					<?=$HtmlTags->InputText("legalCity",$GLOBAL->UserInfo->City,"","text","courierLegalCity","text",0,false,0,"","","","","","",true)?>
					<label for="courierLegalDistrict"><?=$LangPack->District?></label>
					<?=$HtmlTags->InputText("legalDistrict",$GLOBAL->UserInfo->District,"","text","courierLegalDistrict","text")?>
					<label for="courierLegalStreet"><?=$LangPack->Street?></label>
					<?=$HtmlTags->InputText("legalStreet",$GLOBAL->UserInfo->Street,"","text","courierLegalStreet","text",0,false,0,"","","","","","",true)?>
					<label for="courierLegalHouse"><?=$LangPack->House?></label>
					<?=$HtmlTags->InputText("legalHouse",$GLOBAL->UserInfo->House,"","text","courierLegalHouse","text",0,false,0,"","","","","","",true)?>
					<label for="courierLegalHull"><?=$LangPack->Hull?></label>
					<?=$HtmlTags->InputText("legalHull",$GLOBAL->UserInfo->Hull,"","text","courierLegalHull","text")?>
					<label for="courierLegalOffice"><?=$LangPack->Office?></label>
					<?=$HtmlTags->InputText("legalOffice",$GLOBAL->UserInfo->Office,"","text","courierLegalOffice","text")?>
<?php
		}
?>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","edit")?>
						<a href="<?=$ActiveLanguage->LinksPrefix.$Page->FullAddress?>" class="submit"><?=$LangPack->back?></a>
					</div>
				</form>
<?php
	}elseif($ShowPassword){
?>
				<form action="?pass&submit" method="post">
					<label for="oldpass"><?=$LangPack->OldPasswordReq?></label>
					<?=$HtmlTags->InputText("oldpass","","","password","oldpass","text",0,false,0,"","","","","","",true)?>
					<label for="pass1"><?=$LangPack->NewPassword1Req?></label>
					<?=$HtmlTags->InputText("pass1","","","password","pass1","text",0,false,0,"","","","","","",true)?>
					<label for="pass2"><?=$LangPack->NewPassword2Req?></label>
					<?=$HtmlTags->InputText("pass2","","","password","pass2","text",0,false,0,"","","","","","",true)?>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","edit")?>
						<a href="<?=$ActiveLanguage->LinksPrefix.$Page->FullAddress?>" class="submit"><?=$LangPack->back?></a>
					</div>
				</form>
<?php
	}
?>
			</div>
			<div class="feedbackBlock">
				<form action="?send" method="post">
<?php
			if(isset($Messages["ERROR"]) && !empty($Messages["ERROR"])){
?>
					<div class="message error"><?=$Messages["ERROR"]?></div>
<?php
			}
			if(isset($Messages["LOG"]) && !empty($Messages["LOG"])){
?>
					<div class="message log"><?=$Messages["LOG"]?></div>
<?php
			}
?>
					<h3 class="title"><?=$LangPack->PleaseSendReview?></h3>
					<?=$HtmlTags->textarea("review","",40,7,"","","review","text","","",true)?>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?>
					</div>
				</form>
			</div>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>