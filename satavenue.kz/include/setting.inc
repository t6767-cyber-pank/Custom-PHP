<?php
setlocale(LC_ALL,'ru_RU.CP1251');
//	$DomainName=$_SERVER["HTTP_HOST"];
$DomainName='satavenue.kz';
$DomainName=explode("-",$DomainName);
$DomainName=join("-",$DomainName);
$ShortDomainName=str_replace("www.","",$DomainName);
$DateTime=time();
$BannerBtnId=array(1,2,3);
$BannerBtnId=$BannerBtnId[array_rand($BannerBtnId)];
if(isset($_COOKIE['BannerBtnId'])){
	$BannerBtnId=$_COOKIE['BannerBtnId'];
}
else{
	setcookie('BannerBtnId',$BannerBtnId,time()+(3600*24*7*100),'/');
}
if(isset($PageId,$Page)){
	class LangPack{
		function LangPack(){
			global $GLOBAL,$ActiveLanguage,$DataBase;
			$AllLabels=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->LangPack};");
			foreach($AllLabels as $Label){
				$name=trim($Label->Name);
				if(!empty($name))
					$this->$name=$Label->Title;
				unset($Label);
			}
			unset($AllLabels);
		}
	}
	$LangPack=new LangPack();
	class TextBlocks{
		function TextBlocks(){
			global $GLOBAL,$ActiveLanguage,$DataBase;
			$AllBlocks=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->TextBlocks};");
			$Title="Title_{$ActiveLanguage->Prefix}";
			$Description="Description_{$ActiveLanguage->Prefix}";
			foreach($AllBlocks as $TextBlock){
				$name=$TextBlock->Name;
				$TextBlock->Title=htmlspecialchars_decode($TextBlock->$Title);
				$TextBlock->Description=$TextBlock->$Description;
				$this->$name=$TextBlock;
				unset($TextBlock);
			}
			unset($AllBlocks);
		}
	}
	$TextBlocks=new TextBlocks();
	if(isset($_GET["subscribe"])){
		$EMail=@$_POST["email"];
		if(empty($EMail)||!filter_var($EMail,FILTER_VALIDATE_EMAIL)){
			echo "<div class=\"message error\">{$TextBlocks->SubscribeErrorEMail->Description}</div>";
		}
		else{
			$EMail=urlencode($EMail);
			$EMailTest=md5($EMail);
			if($Test=$DataBase->Query("SELECT * FROM {$GLOBAL->Subscribes} WHERE MD5(`EMail`)='{$EMailTest}';",false)){
				echo "<div class=\"message log\">{$TextBlocks->SubscribeSuccess->Description}</div>";
			}
			else{
				$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Subscribes} SET `DateTime`={$DateTime},`EMail`='{$EMail}';");
				echo "<div class=\"message log\">{$TextBlocks->SubscribeSuccess->Description}</div>";
			}
		}
		exit;
	}
	$IsAuth=$Kernel->IsAdminNu();
	$UserId=$IsAuth?$GLOBAL->UserInfo->Id:0;
	$BasketInfo=$Kernel->GetBasketInfo($UserId);
	$MailTo=array("iecmarketing@iec-telecom.kz","info-kz@iec-telecom.com","adelia.safina@iec-telecom.com","request@satavenue.kz", "eduardeliseev.m@gmail.com");
	$AllMonths=array(1=>array($LangPack->Month1,$LangPack->Month1In),2=>array($LangPack->Month2,$LangPack->Month2In),3=>array($LangPack->Month3,$LangPack->Month3In),4=>array($LangPack->Month4,$LangPack->Month4In),5=>array($LangPack->Month5,$LangPack->Month5In),6=>array($LangPack->Month6,$LangPack->Month6In),7=>array($LangPack->Month7,$LangPack->Month7In),8=>array($LangPack->Month8,$LangPack->Month8In),9=>array($LangPack->Month9,$LangPack->Month9In),10=>array($LangPack->Month10,$LangPack->Month10In),11=>array($LangPack->Month11,$LangPack->Month11In),12=>array($LangPack->Month12,$LangPack->Month12In));
	if(isset($_GET["callBack"])){
		$Name=$Kernel->PostToSQL(@$_POST["name"]);	
		$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
		$Messages=array("ERROR"=>array(),"LOG"=>array());
		if(empty($Name))
			$Messages["ERROR"][]=$LangPack->ErrorsName;
		if(empty($Phone))
			$Messages["ERROR"][]=$LangPack->ErrorsPhone;
		if(count($Messages["ERROR"])==0){
			$Title=str_replace(array("%NAME%","%PHONE%","%IP%","%USERAGENT%","%DATETIME%"),array($Name,$Phone,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],date("r")),$TextBlocks->CallBackRequest->Title);
			$Message=str_replace(array("%NAME%","%PHONE%","%IP%","%USERAGENT%","%DATETIME%"),array($Name,$Phone,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],date("r")),$TextBlocks->CallBackRequest->Description);
			foreach($MailTo as $Mail){
				$Kernel->SendMessage($Mail,$Title,$Message);
			}
			echo "<div class=\"message log\">{$LangPack->CallBackSended}</div>";
			exit;
		}
		if(count($Messages["ERROR"])){
			$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";
			echo "<div class=\"message error\">";
			echo $Messages["ERROR"];
			echo "</div>";
			exit;
		}
		exit;
	}
	$InPage=15;
	$InPageStep=15;
	$MinInPage=15;
	$MaxInPage=60;
	$ListView="GRID";
	$OrderBy="IF(Products.Price = 0, 1, 0), Price";
	$OrderByDirection="a";
	if(isset($_GET["products"])){
		$Products=(int)$_GET["products"];
		if(is_integer($Products/$InPageStep)&&$Products>=$MinInPage&&$Products<=$MaxInPage){
			setcookie("inp",$Products,time()+365*24*60*60,"/");
			$_COOKIE["inp"]=$Products;
		}
	}
	if(isset($_COOKIE["inp"])){
		$InPageTest=(int)$_COOKIE["inp"];
		if(is_integer($InPageTest/$InPageStep)&&$InPageTest>=$MinInPage&&$InPageTest<=$MaxInPage){
			$InPage=$InPageTest;
		}
		else{
			setcookie("inp",$InPage,time()+365*24*60*60,"/");
			$_COOKIE["inp"]=$InPage;
		}
	}
	else{
		setcookie("inp",$InPage,time()+365*24*60*60,"/");
		$_COOKIE["inp"]=$InPage;
	}
	if(isset($_GET["view"])){
		$View=$_GET["view"];
		if(in_array($View,array("grid","list"))){
			$View=strtoupper($View);
			setcookie("lv",$View,time()+365*24*60*60,"/");
			$_COOKIE["lv"]=$View;
		}
	}
	if(isset($_COOKIE["lv"])){
		$ListViewTest=$_COOKIE["lv"];
		if(in_array($ListViewTest,array("GRID","LIST"))){
			$ListView=$ListViewTest;
		}
		else{
			setcookie("lv",$ListView,time()+365*24*60*60,"/");
			$_COOKIE["lv"]=$ListView;
		}
	}
	else{
		setcookie("lv",$ListView,time()+365*24*60*60,"/");
		$_COOKIE["lv"]=$ListView;
	}
	$fromCategories=false;
	if(isset($_GET["sort"])){
		setcookie("oc",0,time()+365*24*60*60,"/");
		$SortTest=$_GET["sort"];
		if($SortTest=="pricea"){
			setcookie("ob","IF(Products.Price = 0, 1, 0), Price",time()+365*24*60*60,"/");
			$_COOKIE["ob"]="IF(Products.Price = 0, 1, 0), Price";
			setcookie("obd","a",time()+365*24*60*60,"/");
			$_COOKIE["obd"]="a";
		}
		elseif($SortTest=="priced"){
			setcookie("ob","IF(Products.Price = 0, 1, 0), Price",time()+365*24*60*60,"/");
			$_COOKIE["ob"]="IF(Products.Price = 0, 1, 0), Price";
			setcookie("obd","d",time()+365*24*60*60,"/");
			$_COOKIE["obd"]="d";
		}
		elseif($SortTest=="titlea"){
			setcookie("ob","Title",time()+365*24*60*60,"/");
			$_COOKIE["ob"]="Title";
			setcookie("obd","a",time()+365*24*60*60,"/");
			$_COOKIE["obd"]="a";
		}
		elseif($SortTest=="titled"){
			setcookie("ob","Title",time()+365*24*60*60,"/");
			$_COOKIE["ob"]="Title";
			setcookie("obd","d",time()+365*24*60*60,"/");
			$_COOKIE["obd"]="d";
		}
		elseif($SortTest=="default"){
			setcookie("ob","IF(Products.Price = 0, 1, 0), Default",time()+365*24*60*60,"/");
			setcookie("oc",1,time()+365*24*60*60,"/");
			$_COOKIE["ob"]="IF(Products.Price = 0, 1, 0), Default";
			setcookie("obd","a",time()+365*24*60*60,"/");
			$_COOKIE["obd"]="a";
		}
		var_dump($_COOKIE["oc"]);
	}
	if(isset($_COOKIE["ob"])){
		$OrderByTest=$_COOKIE["ob"];
		if(in_array($OrderByTest,array("Price","Title","Default"))){
			$OrderBy=$OrderByTest;
		}
		else{
			setcookie("ob",$OrderBy,time()+365*24*60*60,"/");
			$_COOKIE["ob"]=$OrderBy;
		}
	}
	else{
		setcookie("ob",$OrderBy,time()+365*24*60*60,"/");
		$_COOKIE["ob"]=$OrderBy;
	}
	if(isset($_COOKIE["obd"])){
		$OrderByTest=$_COOKIE["obd"];
		if(in_array($OrderByTest,array("a","d"))){
			$OrderByDirection=$OrderByTest;
		}
		else{
			setcookie("obd",$OrderByDirection,time()+365*24*60*60,"/");
			$_COOKIE["obd"]=$OrderByDirection;
		}
	}
	else{
		setcookie("obd",$OrderByDirection,time()+365*24*60*60,"/");
		$_COOKIE["obd"]=$OrderByDirection;
	}
	$OrderByDirection=$OrderByDirection=="a"?"ASC":"DESC";
	$LoadMapHeader=false;
	$AllLanguages=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} WHERE `Status`='Y' ORDER BY `Position`;");
	foreach($AllLanguages as $Language){
		$Language->LinksPrefix=$RootLanguage->Id==$Language->Id?"":"/{$Language->Prefix}";
		$AllLanguages[$Language->Id]=$Language;
		unset($Language);
	}
	unset($Languages);
	$UseMap=false;
	$IsMainPage=false;
	$ShowMainTitle=true;
	$IsMapPage=false;
	$IsSearchPage=false;
	$E404Page=false;
	$CategoryExist=false;
	$BreadCrumbs=array("<a href=\"{$ActiveLanguage->LinksPrefix}/\">{$LangPack->BreadMainPage}</a>");
	$Pages=$Kernel->ExtractPageOptions($Page->FullIds,$ActiveLanguage);
	foreach($Page->FullIds as $Id){
		if(isset($Pages[$Id])){
			$class="";
			if($Pages[$Id]->Status=="Y")
				$BreadCrumbs[]="<a href=\"{$ActiveLanguage->LinksPrefix}{$Pages[$Id]->FullAddress}\"{$class}>{$Pages[$Id]->MenuTitle}</a>";
			else
				$BreadCrumbs[]="<span{$class}>{$Pages[$Id]->MenuTitle}</span>";
		}
		unset($Id);
	}
	unset($Pages);
	$MenuItems=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE (`MenuVisible`='Y' OR `BottomMenuVisible`='Y') AND `Owner`=0 ORDER BY `Position`;");
	$Ids=array();
	foreach($MenuItems as $Item){
		$Item->Items=array();
		$Item->FullAddress=$Item->FullAddress=="/main/"?"/":$Item->FullAddress;
		$Item->FullIds=explode("/",$Item->FullIds);
		if(!$IsAuth){
			$Item->Title=$Item->Name=="sign"?$LangPack->SignInUp:$Item->Title;
		}
		if($Item->MenuVisible=="Y"){
			$MenuItems["TOP"][$Item->Id]=$Item;
			$Ids[$Item->Id]=$Item->Id;
		}
		if($Item->BottomMenuVisible=="Y")
			$MenuItems["BOTTOM"][$Item->Id]=$Item;
		unset($Item);
	}
	unset($Items);
	if(count($Ids)){
		$Ids=join(",",$Ids);
		$Items=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE (`MenuVisible`='Y' OR `BottomMenuVisible`='Y') AND `Owner` IN ({$Ids}) ORDER BY `Position`;");
		foreach($Items as $Item){
			$Item->FullIds=explode("/",$Item->FullIds);
			$MenuItems["TOP"][$Item->Owner]->Items[$Item->Id]=$Item;
			unset($Item);
		}
		unset($Items);
	}
	$SubPages=array();
	if(isset($MenuItems["TOP"][$Page->FullIds[1]])){
		foreach($MenuItems["TOP"][$Page->FullIds[1]]->Items as $SubPage){
			$SubPages[$SubPage->Id]=$SubPage;
			unset($SubPage);
		}
	}
	$AllCategories=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Status`='Y' AND `Owner`=0 ORDER BY `Position`;");
	$Ids=array();
	foreach($AllCategories as $Item){
		$Item->Items=array();
		$Item->FullIds=explode("/",$Item->FullIds);
		$AllCategories[$Item->Id]=$Item;
		$Ids[$Item->Id]=$Item->Id;
		unset($Item);
	}
	unset($Items);
	if(count($Ids)){
		$Ids=join(",",$Ids);
		$Items=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Status`='Y' AND `Owner` IN ({$Ids}) ORDER BY `Position`;");
		foreach($Items as $Item){
			$Item->FullIds=explode("/",$Item->FullIds);
			$AllCategories[$Item->Owner]->Items[$Item->Id]=$Item;
			unset($Item);
		}
		unset($Items);
	}
	$AllBrands=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Brands} WHERE `Status`='Y' ORDER BY `Position`;");
	$SocialList=array("fb"=>array(@$LangPack->SocialFBURL,@$LangPack->SocialFB),"tw"=>array(@$LangPack->SocialTWURL,@$LangPack->SocialTW),"in"=>array(@$LangPack->SocialINURL,@$LangPack->SocialIN),"yt"=>array(@$LangPack->SocialYTURL,@$LangPack->SocialYT),//"vk"=>array(@$LangPack->SocialVKURL,@$LangPack->SocialVK),
	);
	$BusinessTypes=array("PRIVATE"=>array($LangPack->BusinessTypePrivate),"BUSINESS"=>array($LangPack->BusinessTypeBusiness));
	$BusinessType=@$_COOKIE["btype"];
	if(!isset($BusinessTypes[$BusinessType])){
		$BusinessType="PRIVATE";
		$_COOKIE["btype"]=$BusinessType;
		setcookie("btype",$BusinessType,time()+365*24*60*60,"/");
	}
	if(isset($_GET["bt"])){
		$Type=$_GET["bt"];
		if(isset($BusinessTypes[$Type])){
			$BusinessType=$Type;
			$_COOKIE["btype"]=$Type;
			setcookie("btype",$Type,time()+365*24*60*60,"/");
		}
	}
	$SortMethods=array("default"=>array($LangPack->SortTitleDefault,"IF(Products.Price = 0, 1, 0), BrandId","ASC"),"pricea"=>array($LangPack->SortCostLow,"IF(Products.Price = 0, 1, 0), Price","ASC"),"priced"=>array($LangPack->SortCostHight,"IF(Products.Price = 0, 1, 0), Price","DESC"),"titlea"=>array($LangPack->SortTitleLow,"Title","ASC"),"titled"=>array($LangPack->SortTitleHight,"Title","DESC"),);
	if(isset($_GET["nomobile"])){
		setcookie("nomobile","1",null,"/");
		header("Location: {$ActiveLanguage->LinksPrefix}{$OWN_REQUEST}");
		exit;
	}
	$OgTitle=$Page->Title;
	//		$OgImage="https://www.iec-telecom.kz/images/fbogimage.jpg";
	//		$OgURL="https://www.iec-telecom.kz/";
	$OgImage="http://www.satavenue.kz/images/fbogimage.jpg";
	$OgURL="http://www.satavenue.kz/";
	$PageNum=0;
}