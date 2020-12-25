<?php
	if($IsAuth){
		header("Location: {$ActiveLanguage->LinksPrefix}/sign/");
		exit;
	}

	$Messages=array("ERROR"=>array(),"LOG"=>array());

	if(isset($_GET["signin"])){
		$EMail=@$_POST["email"];
		$Password=@$_POST["pass"];

		if(empty($EMail) || !filter_var($EMail, FILTER_VALIDATE_EMAIL))
			$Messages["ERROR"][]=$LangPack->ErrorsEMail;
		if(empty($Password) || mb_strlen($Password,"UTF-8")<$LangPack->PasswordMinLength)
			$Messages["ERROR"][]=$LangPack->ErrorsPasswordLength;

		if(count($Messages["ERROR"])==0){
			$EMail=urlencode($EMail);
			$EMailMD5=md5($EMail);

			if($UserInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE MD5(EMail)='{$EMailMD5}';",false)){
				$Password=$Kernel->CreatePasswordCash($Password,$UserInfo->DateTime);

				if($Password==$UserInfo->Password){
					if($UserInfo->Status=="Y"){
						$Kernel->CreateUserSession($UserInfo);

						if(isset($_GET["ajax"])){
							echo "OK:{$ActiveLanguage->LinksPrefix}/sign/";
							exit;
						}else{
							header("Location: {$ActiveLanguage->LinksPrefix}/sign/");
							exit;
						}
					}else{
						$Messages["ERROR"][]=$LangPack->ErrorUserStatus;
					}
				}else{
					$Messages["ERROR"][]=$LangPack->ErrorUserPassword;
				}
			}else{
				$Messages["ERROR"][]=$LangPack->ErrorEMailExist;
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
	}elseif(isset($_GET["restore"])){
		$EMail=@$_POST["email"];

		if(empty($EMail) || !filter_var($EMail, FILTER_VALIDATE_EMAIL))
			$Messages["ERROR"][]=$LangPack->ErrorsEMail;

		if(count($Messages["ERROR"])==0){
			$EMail=urlencode($EMail);
			$EMailMD5=md5($EMail);

			if($UserInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE MD5(EMail)='{$EMailMD5}';",false)){
				if($UserInfo->Status=="Y"){
					$NewPassword=$Kernel->CreatePassword($LangPack->PasswordMinLength);

					$Title=str_replace(
						array("%EMAIL%","%PASSWORD%","%DATETIME%","%IP%","%USERAGENT%"),
						array(urldecode($UserInfo->EMail),$NewPassword,date("r"),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
						$TextBlocks->RestorePasswordMessage->Title
					);
					$Message=str_replace(
						array("%EMAIL%","%PASSWORD%","%DATETIME%","%IP%","%USERAGENT%"),
						array(urldecode($UserInfo->EMail),$NewPassword,date("r"),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
						$TextBlocks->RestorePasswordMessage->Description
					);

					$Kernel->SendMessage(urldecode($UserInfo->EMail),$Title,$Message);

					$PasswordHash=$Kernel->CreatePasswordCash($NewPassword,$UserInfo->DateTime);
					$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Password`='{$PasswordHash}' WHERE Id={$UserInfo->Id};");

					if(isset($_GET["ajax"])){
						echo "<div class=\"message log\">{$TextBlocks->PasswordResetAndSend->Description}</div>";
						exit;
					}else{
						header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
						exit;
					}
				}else{
					$Messages["ERROR"][]=$LangPack->ErrorUserStatus;
				}
			}else{
				$Messages["ERROR"][]=$LangPack->ErrorEMailExist;
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
	}elseif(isset($_GET["signup"])){
		$EMail=@$_POST["email"];
		$Name=@$_POST["name"];
		$Pass1=@$_POST["pass1"];
		$Pass2=@$_POST["pass2"];
		$Type=@$_POST["type"];
		$Type=$Type=="l"?"LEGAL":"CUSTOMER";

		if(empty($Name))
			$Messages["ERROR"][]=$LangPack->ErrorsName;
		if(empty($EMail) || !filter_var($EMail, FILTER_VALIDATE_EMAIL))
			$Messages["ERROR"][]=$LangPack->ErrorsEMail;
		if(empty($Pass1) || mb_strlen($Pass1,"UTF-8")<$LangPack->PasswordMinLength || empty($Pass2) || mb_strlen($Pass2,"UTF-8")<$LangPack->PasswordMinLength)
			$Messages["ERROR"][]=$LangPack->ErrorsPasswordLength;
		if($Pass1!==$Pass2)
			$Messages["ERROR"][]=$LangPack->ErrorsPasswordSame;

		if(count($Messages["ERROR"])==0){
			$EMail=urlencode($EMail);
			$EMailMD5=md5($EMail);

			if($UserInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE MD5(EMail)='{$EMailMD5}';",false)){
				$Messages["ERROR"][]=$LangPack->ErrorsUserAlreadyExist;
			}else{
				$SQL=array();

				$Login=$Kernel->CreatePasswordCash($EMail,$DateTime);
				$Password=$Kernel->CreatePasswordCash($Pass1,$DateTime);

				$SQL[]="`Status`='Y'";
				$SQL[]="`Type`='{$Type}'";
				$SQL[]="`Name`='{$Name}'";
				$SQL[]="`Login`='{$Login}'";
				$SQL[]="`Password`='{$Password}'";
				$SQL[]="`DateTime`={$DateTime}";
				$SQL[]="`EMail`='{$EMail}'";

				$SQL=join(",",$SQL);

				$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SystemUsers} SET {$SQL};");

				$EMail=urldecode($EMail);

				if(!empty($LastId)){
					$Title=str_replace(
						array("%NAME%","%EMAIL%","%PASSWORD%","%DATETIME%","%IP%","%USERAGENT%"),
						array($Name,urldecode($EMail),$Pass1,date("r"),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
						$TextBlocks->SignUpSuccessMessage->Title
					);
					$Message=str_replace(
						array("%NAME%","%EMAIL%","%PASSWORD%","%DATETIME%","%IP%","%USERAGENT%"),
						array($Name,urldecode($EMail),$Pass1,date("r"),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),
						$TextBlocks->SignUpSuccessMessage->Description
					);

					$Kernel->SendMessage(urldecode($EMail),$Title,$Message);

					if(isset($_GET["ajax"])){
						echo "<div class=\"message log\">{$TextBlocks->SignUpThank->Description}</div>";
						exit;
					}else{

					}
				}else{
					$Messages["ERROR"][]=$LangPack->ErrorsSignUpUnknownError;
				}
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

	include ".{$DIRS->modules}so/h.inc";
?>
	<section class="signInUp body">
		<div class="tabs">
			<a href="#signUp" class="tab<?= !isset($_GET['auth']) ? ' current' : '' ?>"><span><?=$LangPack->SignUp?></span></a>
			<a href="#signIn" class="tab<?= !isset($_GET['auth']) ? '' : ' current' ?>"><span><?=$LangPack->SignIn?></span></a>
			<a href="#restore" class="tab"><span><?=$LangPack->RestorePassword?></span></a>
		</div>
		<div class="tabItems">
			<div id="signUp" class="tab<?= !isset($_GET['auth']) ? ' current' : '' ?>">
				<form action="?signup" method="post">
					<div class="row">
						<label for="type"><?=$LangPack->TypeReq?></label>
						<?=$HtmlTags->InputRadio("type","l",$LangPack->UserTypeLegal,true,"","typeL","radio",true)?>
						<?=$HtmlTags->InputRadio("type","c",$LangPack->UserTypeCustomer,false,"","typeC","radio",true)?>
					</div>
					<div class="rows-left">
						<div class="row">
							<label for="name"><?=$LangPack->NameReq?></label>
							<?=$HtmlTags->InputText("name",@$_POST["name"],"","name","text","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="email"><?=$LangPack->EMailReq?></label>
							<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="pass1"><?=$LangPack->Password1Req?></label>
							<?=$HtmlTags->InputText("pass1","","","password","pass1","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="pass2"><?=$LangPack->Password2Req?></label>
							<?=$HtmlTags->InputText("pass2","","","password","pass2","text",0,false,0,"","","","","","",true)?>
						</div>
						<div class="cond">
							<?=$HtmlTags->InputCheckbox("cond","y",$TextBlocks->SignUpConditions->Description,false,"","cond","",true,"checkbox",true,false,true)?>
						</div>
						<div class="submit">
							<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?>
						</div>
					</div>
				</form>
			</div>
			<div id="signIn" class="tab<?= !isset($_GET['auth']) ? '' : ' current' ?>">
				<form action="?signin" method="post" >
					<div class="row">
						<label for="signEmail"><?=$LangPack->EMailReq?></label>
						<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","signEmail","text",0,false,0,"","","","","","",true)?>
					</div>
					<div class="row">
						<label for="signPass"><?=$LangPack->PasswordReq?></label>
						<?=$HtmlTags->InputText("pass","","","password","signPass","text",0,false,0,"","","","","","",true)?>
					</div>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->SignIn,"","","",true,"submit","submit")?>
					</div>
				</form>
			</div>
			<div id="restore" class="tab">
				<form action="?restore" method="post">
					<label for="restoreEMail"><?=$LangPack->EMailReq?></label>
					<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","restoreEMail","text",0,false,0,"","","","","","",true)?>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?>
					</div>
				</form>
			</div>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>