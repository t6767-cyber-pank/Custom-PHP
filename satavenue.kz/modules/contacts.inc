<?php
	$Messages=array(
		"ERROR"=>array(),
		"LOG"=>array()
	);

	if(isset($_GET["send"])){
		$Name=$Kernel->PostToSQL(@$_POST["name"]);
		$SurName=$Kernel->PostToSQL(@$_POST["surname"]);
		$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
		$EMail=@$_POST["email"];
		$Message=$Kernel->PostToSQL(@$_POST["message"],true,true,2048);

		if(empty($Name) || empty($SurName))
			$Messages["ERROR"][]=$LangPack->ErrorsName;
		if(empty($Phone))
			$Messages["ERROR"][]=$LangPack->ErrorsPhone;
		if(empty($EMail) || !filter_var($EMail, FILTER_VALIDATE_EMAIL))
			$Messages["ERROR"][]=$LangPack->ErrorsEMail;
		if(empty($Message))
			$Messages["ERROR"][]=$LangPack->ErrorsMessage;

		if(count($Messages["ERROR"])){
			$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";

			if(isset($_GET["ajax"])){
				echo $Messages["ERROR"];
				exit;
			}
		}else{
			$UserId=$IsAuth?$GLOBAL->UserInfo->Id:0;
			$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ContactRequests} SET `UserId`={$UserId},`DateTime`={$DateTime},`PageTitle`='{$Page->Title}',`PageURL`='{$OWN_REQUEST}',`Name`='{$Name}',`SurName`='{$SurName}',`EMail`='{$EMail}',`Phone`='{$Phone}',`Message`='{$Message}';");
			$Title=str_replace(
				array("%NAME%","%SURNAME%","%PAGETITLE%","%PAGEURL%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),
				array($Name,$SurName,$Page->Title,$OWN_REQUEST,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
				$TextBlocks->ContactRequestMessage->Title
			);
			$Message=str_replace(
				array("%NAME%","%SURNAME%","%PAGETITLE%","%PAGEURL%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),
				array($Name,$SurName,$Page->Title,$OWN_REQUEST,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
				$TextBlocks->ContactRequestMessage->Description
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

	if(isset($_GET["sended"])){
		$Messages["LOG"][]=$TextBlocks->ContactRequestSended->Description;

		$Messages["LOG"]="<p>".join("</p><p>",$Messages["LOG"])."</p>";
	}

	$EnLang = (trim($_SERVER['REQUEST_URI'], '/') == 'contacts_en');
	$UseMap=true;
	include ".{$DIRS->modules}so/h.inc";
?>
	<?php if ($EnLang): ?>
		<img src="/imgs/contact_banner.png" alt="" class="full-screen-img">
	<?php endif ?>
	<section class="contactPage body">
		<div class="line">
			<div class="contactsMap">
				<div class="description"><?=$Page->Description?></div>
				<?php if ($EnLang): ?>
					<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A631173d46313fe1fa826f8e32bf132811198b7889730a10a546728e0620e363d&source=constructorLink&amp;lang=en_US&amp;scroll=true" width="100%" height="300" frameborder="0"></iframe>
				<?php else: ?>
					<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A631173d46313fe1fa826f8e32bf132811198b7889730a10a546728e0620e363d&amp;source=constructorLink&amp;lang=ru_RU&amp;scroll=true" width="100%" height="300" frameborder="0"></iframe>
				<?php endif ?>
			</div>
			<div class="form">
				<h2 class="title"><?=!$EnLang?$TextBlocks->FeedBack->Title:'Send Message'?></h2>
				<div class="description"><?=!$EnLang?$TextBlocks->FeedBack->Description:''?></div>
				<form action="?send" method="post" id="frecaptcha2">
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
					<div class="rows">
						<div class="row">
							<label for="name"><?=!$EnLang?$LangPack->NameReq:'Name'?></label>
							<?=$HtmlTags->InputText("name",@$_POST["name"],"","text","name","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="name"><?=!$EnLang?$LangPack->SurNameReq:'Family Name'?></label>
							<?=$HtmlTags->InputText("surname",@$_POST["surname"],"","text","surname","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="email"><?=!$EnLang?$LangPack->EMailReq:'Email'?></label>
							<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="phone"><?=!$EnLang?$LangPack->PhoneReq:'Telephone'?></label>
							<?=$HtmlTags->InputText("phone",@$_POST["phone"],"","text","phone","text",0,false,0,"","","","","","",true)?>
						</div>
					</div>
					<label for="message"><?=!$EnLang?$LangPack->MessageReq:'Message'?></label>
					<?=$HtmlTags->textarea("message",@$_POST["message"],30,7,"","","message","text","","",true)?>
						<div id="recaptcha2"></div>
					<p id="recaptchaError2"></p>
					<div class="submit"><?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?></div>
				</form>
			</div>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>