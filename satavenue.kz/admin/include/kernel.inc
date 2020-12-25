<?php
class DataBase{
	var $Connection;
	function DataBase($MySQL){
		$dsn="mysql:host={$MySQL->HOST};dbname={$MySQL->DB};charset=UTF8";
		$opt=array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ);
		$this->Connection=new PDO($dsn,$MySQL->USERNAME,$MySQL->PASSWORD,$opt);
		$this->Connection->exec("set names utf8");
	}
	function FillArray(&$Array,$Value,$Keys,$JustKey=true,$Level=1){
		$Key=$Keys[$Level-1];
		if(!isset($Array[$Value->$Key]))
			$Array[$Value->$Key]=array();
		if(!$JustKey)
			$Array[$Value->$Key]=$Value;
		else
			$this->FillArray($Array[$Value->$Key],$Value,$Keys,$Level++==count($Keys),$Level);
	}
	function Query($Query,$ReturnInArray=true,$Values=array(),$ArrayKey="Id",$ReturnLastId=false,$IsUpdate=false,$ArrayValues=array()){
		$QueryResults=$this->Connection->prepare($Query,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
		$QueryResults->execute($Values);
		if($IsUpdate)
			return true;
		if($ReturnLastId)
			return $this->Connection->lastInsertId();
		if(!$ReturnInArray){
			$QueryResults=$QueryResults->fetch();
			foreach($ArrayValues as $Key=>$Value){
				if(!is_numeric($Key)){
					if($Value=="int")
						$QueryResults->$Key=0;
					if($Value=="boolean")	
						$QueryResults->$Key=true;
					if($Value=="stdClass")
						$QueryResults->$Key=new stdClass();
				}
				else
					$QueryResults->$Value=array();
				unset($Value);
			}
			unset($ArrayValues);
			return $QueryResults;
		}
		else{
			$Results=array();
			while($Row=$QueryResults->fetch()){
				if(is_null($ArrayKey)){
					$Results[]=$Row;
				}
				elseif(is_array($ArrayKey)){
					$this->FillArray($Results,$Row,$ArrayKey,true);
				}
				elseif(!empty($ArrayKey))
					$Results[$Row->$ArrayKey]=$Row;
				else
					$Results[]=$Row;
				unset($Row);
			}
			unset($QueryResults);
			if(count($ArrayValues)){
				foreach($Results as $Key=>$Result){
					foreach($ArrayValues as $ValKey=>$Value){
						if(!is_numeric($ValKey)){
							if($Value=="int")
								$Result->$ValKey=0;
							if($Value=="boolean")
								$Result->$ValKey=true;
							if($Value=="stdClass")
								$Result->$ValKey=new stdClass();
						}
						else{
							$Result->$Value=array();
						}
						$Results[$Key]=$Result;
						unset($Value);
					}
					unset($Result);
				}
				unset($ArrayValues);
			}
			reset($Results);
			return $Results;
		}
	}
	function UpdateQuery($Query,$Values=array()){
		$this->Query($Query,true,$Values,"Id",false,true);
	}
	function InsertQuery($Query,$Values=array(),$ReturnLastId=true){
		return (int)$this->Query($Query,true,$Values,"Id",$ReturnLastId);
	}
}
class Kernel{
	function Kernel(){
	}
	function FillUserRights(&$UserInfo){
		$UserInfo->RightsPages=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsPages;
		$UserInfo->RightsModules=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsModules;
		$UserInfo->RightsUsers=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsUsers;
		$UserInfo->RightsCategories=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsCategories;
		$UserInfo->RightsBrands=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsBrands;
		$UserInfo->RightsProducts=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsProducts;
		$UserInfo->RightsShares=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsShares;
		$UserInfo->RightsSimTypes=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsSimTypes;
		$UserInfo->RightsBaskets=$UserInfo->RightsAdmin=="Y"?"Y":$UserInfo->RightsBaskets;
	}
	function IsAdmin(){
		global $GLOBAL,$PHP_AUTH_USER,$PHP_AUTH_PW,$_COOKIE,$_GET,$_SERVER,$_POST,$DataBase;
		if(isset($_GET["logout"])){
			setcookie("chuid",rand(0,time()),time(),"/");
			setcookie("chukey",rand(0,time()),time(),"/");
			header("Location: /admin/");
			exit;
		}
		$RootURL=$_SERVER['REQUEST_URI'];
		$RootURL=explode("/",$RootURL);
		$RootURL=$RootURL[count($RootURL)-1];
		if(isset($_POST["AuthUser"],$_POST["AuthPassword"])){
			$PHP_AUTH_USER=addslashes(stripslashes($_POST["AuthUser"]));
			$PHP_AUTH_PW=addslashes(stripslashes($_POST["AuthPassword"]));
			if($UserTest=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE `Login`='{$PHP_AUTH_USER}';",false)){
				$Password=$this->CreatePasswordCash($PHP_AUTH_PW,$UserTest->DateTime);
				if($Password==$UserTest->Password&&$UserTest->Status=="Y"){
					$Key=md5($UserTest->Login."ChPuK[45]".time());
					$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Key`='{$Key}' WHERE Id={$UserTest->Id};");
					setcookie("chuid",$UserTest->Id,time()+30*24*60*60,"/");
					setcookie("chukey",$Key,time()+30*24*60*60,"/");
					$this->FillUserRights($UserTest);
					$GLOBAL->UserInfo=$UserTest;
					$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET LastLogin=UNIX_TIMESTAMP(),LastLoginFrom='{$_SERVER["REMOTE_ADDR"]}' WHERE Id={$UserTest->Id};");
					header("Location: /admin/pages/");
					exit();
				}
				else{
					header("Location: /admin/login.php");
					exit();
				}
			}
		}
		else{
			if(empty($_COOKIE["chuid"])&&empty($_COOKIE["chukey"])){
				if($RootURL!=="login.php"){
					header("Location: /admin/login.php");
					exit();
				}
			}
		}
		if(isset($_COOKIE['chuid'],$_COOKIE['chukey'])){
			$_COOKIE['chuid']=(int)$_COOKIE['chuid'];
			if($UserTest=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE Id={$_COOKIE['chuid']} AND `Key`='{$_COOKIE['chukey']}';",false)){
				if($UserTest->Status=="Y"){
					$this->FillUserRights($UserTest);
					$GLOBAL->UserInfo=$UserTest;
					return true;
				}
				else
					return false;
			}
			else{
				$_COOKIE['chuid']="";
				$_COOKIE['chukey']="";
			}
		}
		if(!isset($_COOKIE['chuid'],$_COOKIE['chukey'])||empty($_COOKIE['chuid'])||empty($_COOKIE['chukey'])){
			extract($_SERVER);
			if(!isset($PHP_AUTH_USER)){
				if($RootURL!=="login.php"){
					header("Location: /admin/login.php");
					exit();
				}
			}
			else{
				if($UserTest=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE `Login`='{$PHP_AUTH_USER}';",false)){
					$Password=$this->CreatePasswordCash($PHP_AUTH_PW,$UserTest->DateTime);
					if($Password==$UserTest->Password&&$UserTest->Status=="Y"){
						$Key=md5($UserTest->Login."ChPuK[45]".time());
						$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Key`='$Key' WHERE Id={$UserTest->Id};");
						setcookie("chuid",$UserTest->Id,0,"/");
						setcookie("chukey",$Key,0,"/");
						$this->FillUserRights($UserTest);
						$GLOBAL->UserInfo=$UserTest;
						$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET LastLogin=UNIX_TIMESTAMP(),LastLoginFrom='{$_SERVER["REMOTE_ADDR"]}' WHERE Id={$UserTest->Id};");
					}
				}
				else{
					if($RootURL!=="login.php"){
						header("Location: /admin/login.php");
						exit();
					}
				}
			}
		}
	}
	function IsAdminNu(){
		global $GLOBAL,$_COOKIE,$DataBase;
		if(isset($_COOKIE['chuid'],$_COOKIE['chukey'])){
			$Id=(int)$_COOKIE['chuid'];
			$Key=addslashes(stripslashes($_COOKIE['chukey']));
			if($UserTest=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE Id='{$Id}' AND `Key`='{$Key}';",false)){
				if($UserTest->Status=="Y"){
					$this->FillUserRights($UserTest);
					$GLOBAL->UserInfo=$UserTest;
					return true;
				}
				else
					return $UserTest;
			}
		}
		return false;
	}
	function ExtractToOwner($PageID,$TableName=""){
		global $GLOBAL,$DataBase;
		$TableName=empty($TableName)?$GLOBAL->SystemPages:$TableName;
		$ThisDirs=array("FULL_ADD"=>array(),"FULL_IDS"=>array());
		while($PageID!=="0"){
			if($PageID=$DataBase->Query("SELECT * FROM {$TableName} WHERE Id={$PageID};",false)){
				$ThisDirs["FULL_ADD"][]=$PageID->Name;
				$ThisDirs["FULL_IDS"][]=$PageID->Id;
				$PageID=$PageID->Owner;
			}
			else{
				$PageID="0";
			}
		}
		$ThisDirs["FULL_ADD"]=array_reverse($ThisDirs["FULL_ADD"]);
		$ThisDirs["FULL_IDS"]=array_reverse($ThisDirs["FULL_IDS"]);
		return $ThisDirs;
	}
	function ExtractPageOptions($Id,$ActiveLanguage=""){
		global $GLOBAL,$DataBase;
		$IsArray=is_array($Id);
		if(!is_array($Id)){
			$Id=array($Id);
		}
		$Pages=array();
		$SQL=array();
		$SQL[]="SELECT *";
		if(!empty($ActiveLanguage)){
			$SQL[]="Title_{$ActiveLanguage->Prefix} AS Title";
			$SQL[]="MenuTitle_{$ActiveLanguage->Prefix} AS MenuTitle";
			$SQL[]="Description_{$ActiveLanguage->Prefix} AS Description";
			$SQL[]="MetaDescription_{$ActiveLanguage->Prefix} AS MetaDescription";
			$SQL[]="MetaKeywords_{$ActiveLanguage->Prefix} AS MetaKeywords";
		}
		$SQL=join(",",$SQL);
		foreach($Id as $PageID){
			$PageID=(int)$PageID;
			$PageSQL=$SQL." FROM {$GLOBAL->SystemPages} WHERE Id={$PageID} ORDER BY Position;";
			if($Page=$DataBase->Query($PageSQL,false)){
				$Pages[$PageID]=$Page;
			}
			else{
			}
		}
		if(count($Pages)==1&&!$IsArray){
			$Pages=@$Pages[$PageID];
		}
		return $Pages;
	}
	function GetAnnotation($Input){
		if(preg_match("'<p.*?>(.*?)</p>'si",$Input,$Output)){
			$Output=$Output[0];
			return $Output;
		}
		return $Input;
	}
	function CrCaption($InputStr,$Length,$Splitter=" "){
		$InputStr=strip_tags(htmlspecialchars_decode($InputStr));
		$InputStr=str_replace(array("&nbsp;")," ",$InputStr);
		$InputStr=explode(" ",$InputStr);
		for($i=0;$i<count($InputStr);$i++){
			$InputStr[$i]=trim($InputStr[$i]);
		}
		for($i=0;$i<count($InputStr);$i++){
			if(empty($InputStr[$i]))
				unset($InputStr[$i]);
		}
		$InputStr=join(" ",$InputStr);
		$Output=array();
		if(mb_strlen($InputStr,"UTF-8")>$Length){
			$ii=0;
			$strs=explode($Splitter,$InputStr);
			unset($strs[count($strs)]);
			$strs=array_values($strs);
			while(mb_strlen(join($Splitter,$Output),"UTF-8")<$Length){
				$Output[]=$strs[$ii++];
			}
			$Output[]="&hellip;";
			$Output=join($Splitter,$Output);
		}
		else{
			$Output=$InputStr;
		}
		return $Output;
	}
	function PostToURL($Input,$DropDot=true,$Mode=1){
		$Input=trim($Input);
		$Search=array(" ","\\","/","!","@","#","$","%","^","&","*","(",")","=","+","'",'"',":",";","<",">",",","?","№","«","»");
		$Replace=array("-","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
		if($Mode==2){
			$Replace[0]="_";
			$Search[]="-";
			$Replace[]="_";
		}
		if($DropDot){
			$Search[]=".";
			$Replace[]="";
		}
		$Input=str_replace(array("	","   ","  ")," ",$Input);
		$Input=str_replace($Search,$Replace,$Input);
		$Input=$this->translitIt($Input);
		$Input=trim($Input);
		if($Mode==1)
			$Input=mb_strtolower($Input,"UTF-8");
		return $Input;
	}
	function PostToSQL($Input,$StripTags=true,$SpecialChar=true,$MaxLength=256){
		$Input=htmlspecialchars_decode($Input);
		$Input=trim($Input);
		if($StripTags)
			$Input=strip_tags($Input);
		$Input=trim($Input);
		if($SpecialChar)
			$Input=htmlspecialchars($Input);
		if($StripTags&&$SpecialChar&&$MaxLength>0)
			$Input=mb_substr($Input,0,$MaxLength,"UTF-8");
		$Input=addslashes(stripslashes($Input));
		return $Input;
	}
	function DigitalToPrice($InputPrice,$FirstChar="",$Digitals=3,$Delim=" "){
		$InputPrice=(int)$InputPrice;
		if((int)$InputPrice>100){
			$ListingPrice=array();
			$Parts=floor(strlen($InputPrice)/$Digitals);
			$FirstLength=strlen($InputPrice)-$Parts*$Digitals;
			for($i=$Parts-1;$i>=0;$i--){
				$StartPosition=$FirstLength+$i*$Digitals;
				$ListingPrice[]=substr($InputPrice,$StartPosition,$Digitals);
			}
			$FirstValue=substr($InputPrice,0,$FirstLength);
			if(!empty($FirstValue))
				$ListingPrice[]=substr($InputPrice,0,$FirstLength);
			$ListingPrice=array_reverse($ListingPrice);
			$result=join($Delim,$ListingPrice);
			$result=$FirstChar.$result;
			return $result;
		}
		else{
			return $InputPrice;
		}
	}
	function e404(){
		global $ActiveLanguage;
		//header("Location: {$ActiveLanguage->LinksPrefix}/e404/?page={$_SERVER['REQUEST_URI']}");
		//exit;
	}
	function AddZero($InputNumber,$Length=2){
		$InputNumber=(int)$InputNumber;
		if(strlen($InputNumber)<$Length)
			$InputNumber=str_repeat("0",$Length-strlen($InputNumber)).$InputNumber;
		return $InputNumber;
	}
	function translitIt($str){
		$tr=array("А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"Ts","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sch","Ъ"=>"","Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"Ya","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j","з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y","ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",);
		return strtr($str,$tr);
	}
	function SendEMailMessage($To,$Title,$Message){
		global $MailSMTP;
		$To=urldecode($To);
		$mail=new PHPMailer(true);
		$mail->isHTML(true);
		$mail->isSMTP();
		$mail->SMTPAuth=true;
		//$mail->SMTPSecure = 'ssl';
		$mail->Host=$MailSMTP->Host;
		//$mail->Port = $MailSMTP->Port;
		$mail->Username=$MailSMTP->UserName;
		$mail->Password=$MailSMTP->Password;
		$mail->setFrom("robot@satavenue.kz","iec-telecom.kz");
		$mail->CharSet="UTF-8";
		$mail->addReplyTo($MailSMTP->UserName);
		$mail->addAddress($To);
		$Message=$this->makeLetterMessage($Message);
		preg_match_all("'<img.*?src=\"(.*?)\".*?>'si",$Message,$Imgs);
		$ThisSid=1;
		foreach($Imgs[1] as $Img){
			if(is_file($_SERVER['DOCUMENT_ROOT']."".$Img)){
				$mail->addEmbeddedImage($_SERVER['DOCUMENT_ROOT']."".$Img,"cid{$ThisSid}");
				$Message=str_replace($Img,"cid:cid{$ThisSid}",$Message);
				$ThisSid++;
			}
		}
		$mail->Subject=$this->makeLetterTitle($Title);
		$mail->AltBody='To view the message, please use an HTML compatible email viewer!';
		$mail->msgHTML($Message);
		$mail->send();
		$mail->smtpClose();
	}
	function makeLetterTitle($InputTitle){
		return "iec-telecom.kz :: {$InputTitle}";
	}
	function makeLetterMessage($InputHTML){
		global $LangPack,$TextBlocks;
		$InputHTML=str_replace("<td>","<td style=\"padding:5px 7px;min-width:20%;\">",$InputHTML);
		$Message="<body style='padding:0;margin:0;font-family:Arial,serif;'>";
		$Message.=<<<END
		
			<div style="border-bottom:1px solid #37b9b7;padding:20px 0;margin:0 0 20px;">
				<div style="width:640px;height:91px;margin:0 auto;font-size:0;">
					<div style="display:inline-block;width:202px;vertical-align:top;">
						<a href="https://www.iec-telecom.kz/" style="padding:24px 0;text-decoration: none;display: block;width: 202px;height: 43px;box-sizing: content-box;"><img src="/images/logo.png" alt="iec-telecom.kz" title="iec-telecom.kz" style="width:155px;height:98px;border:0;" /></a>
					</div>
				</div>
			</div>
			<div style="width:640px;margin:0 auto;font-size:14px;color:#666;padding:0 0 30px;">
				{$InputHTML}
			</div>
END;
		$Message.="</body>";
		return $Message;
	}
	function SendMessage($To,$Title,$Message,$Replacements=array()){
		global $DataBase,$GLOBAL;
		$DateTime=time();
		$To=urldecode($To);
		$To=urlencode($To);
		if(!isset($Replacements["%EMAIL%"]))
			$Replacements["%EMAIL%"]=$To;
		foreach($Replacements as $Key=>$Value){
			$Title=str_replace($Key,$Value,$Title);
			$Message=str_replace($Key,$Value,$Message);
		}
		$Title=$this->PostToSQL($Title);
		$Message=$this->PostToSQL($Message,false,false);
		$DataBase->InsertQuery("INSERT INTO {$GLOBAL->EmailMessages} SET `DateTime`={$DateTime},`Status`='N',`LastTry`=0,`EMail`='{$To}',`Title`='{$Title}',`Message`='{$Message}';");
	}
	function SendPhoneMessage($To,$Message,$Replacements=array(),$AppointmentId=0,$Type=""){
		global $DataBase,$GLOBAL;
		$Code=$this->CreatePassword(5,true,true,false);
		$DateTime=time();
		$To=$this->MakePhoneLink($To,false);
		if(!isset($Replacements["%PHONE%"]))
			$Replacements["%PHONE%"]=$To;
		$Replacements["%CODE%"]=$Code;
		foreach($Replacements as $Key=>$Value){
			$Message=str_replace($Key,$Value,$Message);
		}
		$Message=$this->PostToSQL($this->translitIt($Message));
		//$Message=$this->PostToSQL($Message);
		$DataBase->InsertQuery("INSERT INTO {$GLOBAL->PhoneCodes} SET `DateTime`={$DateTime},`Status`='N',`Phone`='{$To}',`Message`='{$Message}',`Code`='{$Code}',`AppointmentId`={$AppointmentId},`Type`='{$Type}';");
	}
	function printTrClass($ThisItem=0,$EditingId=0,$ActualId=0,$ExtraClass=""){
		$class=array();
		if(is_integer($ThisItem/2))
			$class[]="line";
		if($EditingId==$ActualId)
			$class[]="current";
		if(!empty($ExtraClass))
			$class[]=$ExtraClass;
		$class=count($class)?(" class=\"".join(" ",$class)."\""):"";
		return $class;
	}
	function CreatePasswordCash($Password,$DateTime){
		$return=md5($Password."ChPuK[30]".$DateTime);
		return $return;
	}
	function CreateUserSession($SessionInfo){
		global $GLOBAL,$_COOKIE,$DataBase;
		if(is_integer($SessionInfo)){
			$SessionInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE Id={$SessionInfo};",false);
		}
		$Key=md5($SessionInfo->Login."ChPuK[45]".time());
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Key`='{$Key}',LastLogin=UNIX_TIMESTAMP(),LastLoginFrom='{$_SERVER["REMOTE_ADDR"]}' WHERE Id={$SessionInfo->Id};");
		setcookie("chuid",$SessionInfo->Id,time()+30*24*60*60,"/");
		setcookie("chukey",$Key,time()+30*24*60*60,"/");
		$_COOKIE["chuid"]=$SessionInfo->Id;
		$_COOKIE["chukey"]=$Key;
		$this->FillUserRights($SessionInfo);
		$GLOBAL->UserInfo=$SessionInfo;
	}
	function DeleteUserSession($SessionInfo){
		global $GLOBAL,$DataBase,$_COOKIE,$ShortDomainName;
		$AuthDateTime=time();
		$SessionKey=md5($SessionInfo->Id."ChPuK[45]".$AuthDateTime);
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Key`='{$SessionKey}' WHERE `Id`={$SessionInfo->Id}");
		$UId=md5("ChPuK[28]".$SessionInfo->Id);
		setcookie("uid",$UId,time()+30*24*60*60,"/");
		setcookie("skey","",time()+30*24*60*60,"/");
		$_COOKIE["uid"]=$UId;
		$_COOKIE["skey"]=$SessionKey;
	}
	function CheckCoverDirectory($NewPath){
		$NewPath=explode("/",$NewPath);
		$PathArr=array();
		foreach($NewPath as $Path){
			$PathArr[]=$Path;
			if($Dir=@opendir(join("/",$PathArr))){
				//
			}
			else{
				@mkdir(join("/",$PathArr),0755);
				@chmod(join("/",$PathArr),0755);
			}
		}
	}
	function AddViewEvent($Type,$Owner,$UserId=0){
		global $GLOBAL,$DataBase;
		$DateTime=time();
		$DateStr=date("d/m/Y",$DateTime);
		$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Views} SET `Type`='{$Type}',`Owner`={$Owner},`DateTime`={$DateTime},`DateStr`='{$DateStr}',`IP`='{$_SERVER['REMOTE_ADDR']}',`UserId`={$UserId};");
	}
	/**
	 * Добавляет событие манипуляции с данными
	 * @param int $UserId <p>Идентификатор пользователя, совершающего действе</p>
	 * @param string $Type <p>Тип действия. ADD,EDIT,REMOVE</p>
	 * @param string $TargetType <p>Тип объекта. COMPANY,CATEGORY</p>
	 * @param int $TargetId <p>Идентификатор объекта, над которым совершается действие</p>
	 */
	function AddEvent($UserId,$Type,$TargetType,$TargetId){
		global $GLOBAL,$DataBase;
		$DateTime=time();
		$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Events} SET `DateTime`={$DateTime},`UserId`={$UserId},`Type`='{$Type}',`TargetType`='{$TargetType}',`TargetId`={$TargetId}");
	}
	function ImageAlphaMask(&$picture,$mask){
		$xSize=imagesx($picture);
		$ySize=imagesy($picture);
		$newPicture=imagecreatetruecolor($xSize,$ySize);
		imagesavealpha($newPicture,true);
		imagefill($newPicture,0,0,imagecolorallocatealpha($newPicture,0,0,0,127));
		if($xSize!=imagesx($mask)||$ySize!=imagesy($mask)){
			$tempPic=imagecreatetruecolor($xSize,$ySize);
			imagecopyresampled($tempPic,$mask,0,0,0,0,$xSize,$ySize,imagesx($mask),imagesy($mask));
			imagedestroy($mask);
			$mask=$tempPic;
		}
		for($x=0;$x<$xSize;$x++){
			for($y=0;$y<$ySize;$y++){
				$alpha=imagecolorsforindex($mask,imagecolorat($mask,$x,$y));
				$alpha=127-floor($alpha['red']/2);
				$color=imagecolorsforindex($picture,imagecolorat($picture,$x,$y));
				imagesetpixel($newPicture,$x,$y,imagecolorallocatealpha($newPicture,$color['red'],$color['green'],$color['blue'],$alpha));
			}
		}
		imagedestroy($picture);
		$picture=$newPicture;
	}
	function CreatePassword($pw_length=8,$use_caps=true,$use_numeric=true,$use_specials=false){
		$caps=array();
		$numbers=array();
		$reg_length=$pw_length;
		$pws=array();
		for($ch=97;$ch<=122;$ch++)
			$chars[]=$ch;
		if($use_caps)
			for($ca=65;$ca<=90;$ca++)
				$caps[]=$ca;
		if($use_numeric)
			for($nu=48;$nu<=57;$nu++)
				$numbers[]=$nu;
		$all=array_merge($chars,$caps,$numbers);
		if($use_specials){
			$reg_length=ceil($pw_length*0.75);
			$num_specials=$pw_length-$reg_length;
			if($num_specials>5)
				$num_specials=5;
			for($si=33;$si<=47;$si++)
				$signs[]=$si;
			$rs_keys=array_rand($signs,$num_specials);
			foreach($rs_keys as $rs){
				$pws[]=chr($signs[$rs]);
			}
		}
		$rand_keys=array_rand($all,$reg_length);
		foreach($rand_keys as $rand){
			$pw[]=chr($all[$rand]);
		}
		$compl=array_merge($pw,$pws);
		shuffle($compl);
		return implode('',$compl);
	}
	function GenerateThumbnails($URL){
		global $sizes;
		$ORIGINAL_NAME=$URL;
		$DIRNAME=dirname($URL)."/";
		$URL=basename($URL);
		$URL=explode(".",$URL);
		$extension=$URL[count($URL)-1];
		unset($URL[count($URL)-1]);
		$URL=join(".",$URL);
		$URL=explode("_",$URL);
		$mid=array();
		for($i=0;$i<count($URL)-1;$i++)
			$mid[]=$URL[$i];
		$mid=join("_",$mid);
		$ImagePostfix=$URL[count($URL)-1];
		$Width=@$sizes[$ImagePostfix]["w"];
		$Height=@$sizes[$ImagePostfix]["h"];
		unset($URL[count($URL)-1]);
		if(count($URL)<1){
			$URL[0]=@$URL[0];
			$URL[0]=empty($URL[0])?"0":$URL[0];
			$FileName="{$DIRNAME}{$URL[0]}.{$extension}";
			if(!is_file(".{$FileName}")&&$URL[0]=="0")
				$this->e404();
			else
				$FileName=str_replace($URL[0],"0",$FileName);
			header("Location: {$FileName}");
			exit();
		}
		else{
			$FileName=".{$DIRNAME}{$mid}.{$extension}";
			if(!is_file($FileName))
				$FileName=str_replace($mid,"0",$FileName);
		}
		$ImageSize=getimagesize($FileName);
		if($Width==0||$Height==0){
			$Width=$ImageSize[0];
			$Height=$ImageSize[1];
		}
		if($ImageSize["mime"]=="image/png")
			$im=imagecreatefrompng($FileName);
		elseif($ImageSize["mime"]=="image/gif")
			$im=imagecreatefromgif($FileName);
		else
			$im=imagecreatefromjpeg($FileName);
		$SaveSize=false;
		if(isset($sizes[$ImagePostfix]["PROP"])&&$sizes[$ImagePostfix]["PROP"]==true){
			$SaveSize=true;
		}
		$WidthCoef=$ImageSize[0]/$Width;
		$HeightCoef=$ImageSize[1]/$Height;
		if(!$SaveSize){
			if($WidthCoef>$HeightCoef){
				$Width=round($ImageSize[0]/$WidthCoef);
				$Height=round($ImageSize[1]/$WidthCoef);
			}
			elseif($WidthCoef<$HeightCoef){
				$Width=round($ImageSize[0]/$HeightCoef);
				$Height=round($ImageSize[1]/$HeightCoef);
			}
			else{
				$Width=round($ImageSize[0]/$WidthCoef);
				$Height=round($ImageSize[1]/$HeightCoef);
			}
		}
		else{
			if($WidthCoef<$HeightCoef){
				$Width=round($ImageSize[0]/$WidthCoef);
				$Height=round($ImageSize[1]/$WidthCoef);
			}
			elseif($WidthCoef>$HeightCoef){
				$Width=round($ImageSize[0]/$HeightCoef);
				$Height=round($ImageSize[1]/$HeightCoef);
			}
			else{
				$Width=round($ImageSize[0]/$WidthCoef);
				$Height=round($ImageSize[1]/$HeightCoef);
			}
		}
		if($SaveSize){
			$nim=imagecreatetruecolor($sizes[$ImagePostfix]["w"],$sizes[$ImagePostfix]["h"]);
			if(($ImageSize[2]==1) OR ($ImageSize[2]==3)){
				imagealphablending($nim,false);
				imagesavealpha($nim,true);
				$transparent=imagecolorallocatealpha($nim,255,255,255,127);
				imagefilledrectangle($nim,0,0,$sizes[$ImagePostfix]["w"],$sizes[$ImagePostfix]["h"],$transparent);
			}
			if($WidthCoef<$HeightCoef){
				$Width1=round($ImageSize[0]/$WidthCoef);
				$Height1=round($ImageSize[1]/$WidthCoef);
			}
			elseif($WidthCoef>$HeightCoef){
				$Width1=round($ImageSize[0]/$HeightCoef);
				$Height1=round($ImageSize[1]/$HeightCoef);
			}
			else{
				$Width1=round($ImageSize[0]/$WidthCoef);
				$Height1=round($ImageSize[1]/$HeightCoef);
			}
			$nim1=imagecreatetruecolor($Width1,$Height1);
			imagealphablending($nim1,false);
			imagesavealpha($nim1,true);
			$transparent=imagecolorallocatealpha($nim1,255,255,255,127);
			imagefilledrectangle($nim1,0,0,$sizes[$ImagePostfix]["w"],$sizes[$ImagePostfix]["h"],$transparent);
			imagecopyresampled($nim1,$im,0,0,0,0,$Width1,$Height1,$ImageSize[0],$ImageSize[1]);
			imagecopyresampled($nim,$nim1,0,0,$Width/2-$sizes[$ImagePostfix]["w"]/2,$Height/2-$sizes[$ImagePostfix]["h"]/2,$Width,$Height,$Width1,$Height1);
		}
		else{
			$nim=imagecreatetruecolor($Width,$Height);
			if(($ImageSize[2]==1)||($ImageSize[2]==3)){
				imagealphablending($nim,false);
				imagesavealpha($nim,true);
				$transparent=imagecolorallocatealpha($nim,255,255,255,127);
				imagefilledrectangle($nim,0,0,$Width,$Height,$transparent);
			}
			imagecopyresampled($nim,$im,0,0,0,0,$Width,$Height,$ImageSize[0],$ImageSize[1]);
		}
		if(isset($sizes[$ImagePostfix]["KEEPSIZE"])&&$sizes[$ImagePostfix]["KEEPSIZE"]){
			$FullSizeImage=imagecreatetruecolor($sizes[$ImagePostfix]["w"],$sizes[$ImagePostfix]["h"]);
			imagealphablending($FullSizeImage,false);
			imagesavealpha($FullSizeImage,true);
			$transparent=imagecolorallocatealpha($FullSizeImage,255,255,255,127);
			imagefilledrectangle($FullSizeImage,0,0,$sizes[$ImagePostfix]["w"],$sizes[$ImagePostfix]["h"],$transparent);
			$x=$sizes[$ImagePostfix]["w"]-$Width;
			$y=$sizes[$ImagePostfix]["h"]-$Height;
			$x=round($x/2);
			$y=round($y/2);
			imagecopy($FullSizeImage,$nim,$x,$y,0,0,$Width,$Height);
			$nim=$FullSizeImage;
			$ImageSize["mime"]="image/png";
			unset($FullSizeImage);
		}
		$FileInfo=stat($FileName);
		$Time=($FileInfo['mtime']<$FileInfo['ctime'])?$FileInfo['ctime']:$FileInfo['mtime'];
		header("Last-Modified: ".gmdate('D, d M Y H:i:s',$Time)." GMT");
		header("Content-Type: {$ImageSize['mime']}");
		if(isset($sizes[$ImagePostfix]["MASK"])){
			$mask=imagecreatefrompng(".{$sizes[$ImagePostfix]["MASK"]}");
			$this->ImageAlphaMask($nim,$mask);
			if(isset($sizes[$ImagePostfix]["COVER"])){
				$CoverImg=imagecreatefrompng(".{$sizes[$ImagePostfix]["COVER"]}");
				$CoverSize=getimagesize(".{$sizes[$ImagePostfix]["COVER"]}");
				imagecopy($nim,$CoverImg,0,0,0,0,$CoverSize[0],$CoverSize[1]);
			}
			$ImageSize["mime"]="image/png";
		}
		if(isset($sizes[$ImagePostfix]["WHITE"])&&$sizes[$ImagePostfix]["WHITE"]){
			imagefilter($nim,IMG_FILTER_GRAYSCALE);
		}
		if(isset($nim)){
			$SaveName=".{$ORIGINAL_NAME}";
			//$SaveName=null;
			if($ImageSize["mime"]=="image/png")
				imagepng($nim,$SaveName);
			elseif($ImageSize["mime"]=="image/gif")
				imagepng($nim,$SaveName);
			else
				imagejpeg($nim,$SaveName,$sizes[$ImagePostfix]["q"]);
			imagedestroy($nim);
			imagedestroy($im);
			if(!is_null($SaveName))
				header("Location: {$ORIGINAL_NAME}");
		}
		exit();
	}
	function ClearThumbnails($Directory,$Name,$Extension,$FileArray=array(),$DelOriginal=true,$FromAdmin=true){
		global $sizes;
		$Dots=$FromAdmin?"..":".";
		if($DelOriginal){
			$FullName="{$Dots}{$Directory}{$Name}.{$Extension}";
			@unlink($FullName);
		}
		foreach($sizes as $Key=>$Size){
			$FullName="{$Dots}{$Directory}{$Name}_{$Key}.{$Extension}";
			@unlink($FullName);
		}
		if((is_array($FileArray)&&isset($FileArray["tmp_name"]))||(is_string($FileArray)&&!empty($FileArray))){
			$FileArray=is_array($FileArray)?$FileArray["tmp_name"]:$FileArray;
			move_uploaded_file($FileArray,"{$Dots}{$Directory}{$Name}.{$Extension}");
		}
	}
	function PrintPagesList($Owner=0,$IgnoreId=0,$SelectedId=0,$Level=0,$ShowNull=true,$Table=""){
		global $GLOBAL,$SortLanguage,$DataBase,$HtmlTags;
		$TitlePrefix=$Level==0?"":(str_repeat("-",$Level)." ");
		$SelectedId=is_array($SelectedId)?$SelectedId:array($SelectedId);
		$Table=empty($Table)?$GLOBAL->SystemPages:$Table;
		if($ShowNull&&empty($ParentId))
			echo $HtmlTags->option(0,"Нет");
		$Pages=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$Table} WHERE `Owner`={$Owner} AND `Id`<>{$IgnoreId} ORDER BY Title_{$SortLanguage->Prefix};");
		foreach($Pages as $Page){
			echo $HtmlTags->option($Page->Id,$TitlePrefix.$Page->Title,in_array($Page->Id,$SelectedId));
			$this->PrintPagesList($Page->Id,$IgnoreId,$SelectedId,$Level+1,false,$Table);
			unset($Page);
		}
		unset($Pages);
	}
	function CreatePageListener($PagesCount,$ActivePage,$ListPrefix,$MaxCount,$PagesParameters=array()){
		$Pages=array();
		$PageInOut=5;
		if(isset($_GET["Print"]))
			$PagesParameters[]="Print";
		$PagesParameters=count($PagesParameters)?("&".join("&",$PagesParameters)):"";
		if($ActivePage==0)
			$Pages[]="<span class=\"first\"></span>";
		else
			$Pages[]="<a class=\"first\" href=\"{$ListPrefix}".($ActivePage-1)."{$PagesParameters}\"></a>";
		for($i=$ActivePage-$PageInOut;$i<=$ActivePage+$PageInOut;$i++){
			if($i>-1&&$i<$PagesCount){
				$class=$i==$ActivePage?" class=\"current\"":"";
				if($i==0){
					$ListPrefixStr=str_replace("?p=","",$ListPrefix);
					$PagesParametersStr="?".ltrim($PagesParameters,"&");
				}
				else{
					$ListPrefixStr=$ListPrefix.$i;
					$PagesParametersStr=$PagesParameters;
				}
				//$ListPrefixStr=$i==0?str_replace("?p=","",$ListPrefix):($ListPrefix.$i);
				$Pages[]="<a href=\"{$ListPrefixStr}{$PagesParametersStr}\"{$class}>".($i+1)."</a>";
			}
		}
		if($i<$PagesCount){
			$To=$MaxCount-1;
			$Pages[]="...";
			$Pages[]="<a href=\"{$ListPrefix}".($PagesCount-1)."{$PagesParameters}\">{$To}</a>";
		}
		if($ActivePage>$PageInOut){
			array_unshift($Pages,"<a href=\"{$ListPrefix}0{$PagesParameters}\">1</a>","...");
		}
		if($ActivePage==$PagesCount-1)
			$Pages[]="<span class=\"last\"></span>";
		else
			$Pages[]="<a class=\"last\" href=\"{$ListPrefix}".($ActivePage+1)."{$PagesParameters}\"></a>";
		$Pages=join(" ",$Pages);
		return $Pages;
	}
	function getWindDirection($wind){
		$wind=(string)$wind;
		$wind_direction=array('s'=>'↑ ю','n'=>'↓ с','w'=>'→ з','e'=>'← в','sw'=>'↗ юз','se'=>'↖ юв','nw'=>'↘ сз','ne'=>'↙ св');
		return $wind_direction[$wind];
	}
	function GetVariations($Array,&$SearchQueryArray,$Level,$TmpArray=array()){
		if($Level<count($Array)){
			foreach($Array as $Value){
				$TmpArray[$Level]=$Value;
				$this->GetVariations($Array,$SearchQueryArray,$Level+1,$TmpArray);
				unset($Value);
			}
		}
		else{
			$TmpArray=array_unique($TmpArray);
			if(count($Array)==count($TmpArray)){
				$SearchQueryArray[join(" ",$TmpArray)]=$TmpArray;
			}
		}
	}
	public function ShortName($FullName){
		$FullName=explode(" ",$FullName);
		if(count($FullName)>1){
			$FullName[0]=mb_substr($FullName[0],0,1,"UTF-8");
			$FullName=array($FullName[0],$FullName[1]);
			return join(".",$FullName);
		}
		else
			return join(" ",$FullName);
	}
	/**
	 * @param $Type    array('COMPANY','DOCTOR')
	 * @param $Info
	 */
	function GenerateScheduleInfo($Type,$Info){
		global $GLOBAL,$DataBase;
		$DaysWithTime=array();
		$DaysWithTimeSchedules=array();
		$DaysWithTimePrices=array();
		$DaysWithTimeCompany=array();
		if(!is_object($Info)){
			$Info=(int)$Info;
			if($Type=="COMPANY")
				$Info=$DataBase->Query("SELECT * FROM {$GLOBAL->Companies} WHERE Id={$Info};",false);
			elseif($Type=="DOCTOR")
				$Info=$DataBase->Query("SELECT * FROM {$GLOBAL->Doctors} WHERE Id={$Info};",false);
		}
		if(is_object($Info)){
			$StartTime=mktime(0,0,0,date("m"),date("d"),date("Y"));
			if($Type=="COMPANY"){
				$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Schedules} WHERE `Type`='COMPANY' AND `CompanyId`={$Info->Id}/* AND `DateTime`>={$StartTime}*/;");
				$ScheduleInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->CompanySchedule} WHERE CompanyId={$Info->Id};");
				foreach($ScheduleInfo as $Schedule){
					$Schedule->Days=explode(",",$Schedule->Days);
					if($Schedule->Type=="SCHEDULE"){
						if($Schedule->RoundTheClock=="Y"){
							$StartTime=mktime(0,0,0,date("m"),date("d"),date("Y"));
							$FinishTime=$StartTime+$Info->ScheduleDays*24*60*60;
							for($i=$StartTime;$i<$FinishTime;$i=$i+$Info->PatientTakingTile*60){
								if(in_array(date("N",$i),$Schedule->Days)){
									$DaysWithTime[date("d.m.Y",$i)][date("H:i",$i)]=$i;
									$DaysWithTimeSchedules[date("d.m.Y",$i)][date("H:i",$i)]=$Schedule->Id;
								}
							}
						}
						else{
							for($i=0;$i<$Info->ScheduleDays;$i++){
								$StartTime=mktime($Schedule->StartHour,$Schedule->StartMinute,0,date("m"),date("d")+$i,date("Y"));
								$FinishTime=mktime($Schedule->FinishHour,$Schedule->FinishMinute,59,date("m"),date("d")+$i,date("Y"))-$Info->PatientTakingTile*60;
								for($j=$StartTime;$j<$FinishTime;$j=$j+$Info->PatientTakingTile*60){
									if(in_array(date("N",$j),$Schedule->Days)){
										$DaysWithTime[date("d.m.Y",$j)][date("H:i",$j)]=$j;
										$DaysWithTimeSchedules[date("d.m.Y",$j)][date("H:i",$j)]=$Schedule->Id;
									}
								}
							}
						}
					}
					unset($Schedule);
				}
				unset($ScheduleInfo);
			}
			elseif($Type=="DOCTOR"){
				$FinishTime=time();
				$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Schedules} WHERE `Type`='DOCTOR' AND `DoctorId`={$Info->Id}/* AND `DateTime`>={$StartTime}*/;");
				$ScheduleInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->DoctorSchedule} WHERE DoctorId={$Info->Id} AND `FinishTime`>={$FinishTime};");
				foreach($ScheduleInfo as $Schedule){
					$Schedule->Days=explode(",",$Schedule->Days);
					if($Schedule->Type=="SCHEDULE"){
						if($Schedule->RoundTheClock=="Y"){
							$StartTime=mktime(0,0,0,date("m"),date("d"),date("Y"));
							$FinishTime=$StartTime+$Info->ScheduleDays*24*60*60;
							$FinishTime=$Schedule->FinishTime;
							for($i=$StartTime;$i<$FinishTime;$i=$i+$Info->PatientTakingTile*60){
								if(in_array(date("N",$i),$Schedule->Days)&&$i>=$Schedule->StartTime&&$i<=$Schedule->FinishTime){
									$DaysWithTime[date("d.m.Y",$i)][date("H:i",$i)]=$i;
									$DaysWithTimePrices[date("d.m.Y",$i)][date("H:i",$i)]=$Schedule->Price;
									$DaysWithTimeCompany[date("d.m.Y",$i)][date("H:i",$i)]=$Schedule->CompanyId;
									$DaysWithTimeSchedules[date("d.m.Y",$i)][date("H:i",$i)]=$Schedule->Id;
								}
							}
						}
						else{
							for($i=0;$i<$Info->ScheduleDays;$i++){
								$StartTime=mktime($Schedule->StartHour,$Schedule->StartMinute,0,date("m"),date("d")+$i,date("Y"));
								$FinishTime=mktime($Schedule->FinishHour,$Schedule->FinishMinute,59,date("m"),date("d")+$i,date("Y"))-$Info->PatientTakingTile*60;
								for($j=$StartTime;$j<$FinishTime;$j=$j+$Info->PatientTakingTile*60){
									if(in_array(date("N",$j),$Schedule->Days)&&$j>=$Schedule->StartTime&&$j<=$Schedule->FinishTime){
										$DaysWithTime[date("d.m.Y",$j)][date("H:i",$j)]=$j;
										$DaysWithTimePrices[date("d.m.Y",$j)][date("H:i",$j)]=$Schedule->Price;
										$DaysWithTimeCompany[date("d.m.Y",$j)][date("H:i",$j)]=$Schedule->CompanyId;
										$DaysWithTimeSchedules[date("d.m.Y",$j)][date("H:i",$j)]=$Schedule->Id;
									}
								}
							}
						}
					}
					unset($Schedule);
				}
				unset($ScheduleInfo);
			}
			if($Info->HolidayJob=="Y"){
				$StartTime=mktime(0,0,0,date("m"),date("d"),date("Y"));
				$FinishTime=mktime(23,59,59,date("m"),date("d")+$Info->ScheduleDays,date("Y"));
				$Holidays=$DataBase->Query("SELECT * FROM {$GLOBAL->PublicHolidays} WHERE `Status`='Y' AND `StartTime`>={$StartTime} AND `StartTime`<={$FinishTime};");
				foreach($Holidays as $Holiday){
					for($i=$Holiday->StartTime;$i<=$Holiday->FinishTime;$i+=24*60*60)
						unset($DaysWithTime[date("d.m.Y",$i)]);
					unset($Holiday);
				}
				unset($Holidays);
			}
		}
		if($Type=="COMPANY"){
			foreach($DaysWithTime as $Day=>$Times){
				foreach($Times as $Key=>$Time){
					$WeekDay=date("N",$Time);
					$ScheduleId=(int)@$DaysWithTimeSchedules[$Day][$Key];
					$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Schedules} SET `Type`='COMPANY',`CompanyId`={$Info->Id},`DateTime`={$Time},`WeekDay`={$WeekDay},`ScheduleId`={$ScheduleId};");
					unset($WeekDay);
				}
				unset($Day);
				unset($Times);
			}
		}
		elseif($Type=="DOCTOR"){
			foreach($DaysWithTime as $Day=>$Times){
				foreach($Times as $Key=>$Time){
					$WeekDay=date("N",$Time);
					$Price=(int)@$DaysWithTimePrices[$Day][$Key];
					$CompanyId=(int)@$DaysWithTimeCompany[$Day][$Key];
					$ScheduleId=(int)@$DaysWithTimeSchedules[$Day][$Key];
					$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Schedules} SET `Type`='DOCTOR',`DoctorId`={$Info->Id},`CompanyId`={$CompanyId},`DateTime`={$Time},`WeekDay`={$WeekDay},`Price`={$Price},`ScheduleId`={$ScheduleId};");
				}
				unset($Day);
				unset($Times);
			}
		}
		unset($Info);
	}
	public function PrintContactBlock($Contact=null,$Type="PHONE"){
		global $ContactTypes;
		if(is_null($Contact)){
			$Contact=new stdClass();
			$Contact->Id="[]";
			$Contact->CompanyId=0;
			$Contact->Position=0;
			$Contact->Type=$Type;
			$Contact->Public="N";
			$Contact->Value="";
			$Contact->Comment="";
			$Contact->Notifications="N";
		}
		$Contact->Type=strtolower($Contact->Type);

		?>
		<div class="contactBlock">
			<?=HtmlTags::InputText($Contact->Type.$Contact->Id,$Contact->Value,"","text",$Contact->Type.$Contact->Id,"text",256,false,25,"","","","","",$ContactTypes[$Contact->Type])?>
			<a href="#" class="button add"></a>
			<a href="#" class="button remove"></a>
			<?=HtmlTags::InputText($Contact->Type."Comment".$Contact->Id,$Contact->Comment,"","text",$Contact->Type."Comment".$Contact->Id,"text",256,false,25,"","","","","","Дополнительная информация")?>
			<?php
			if($Contact->Type=="phone"||$Contact->Type=="mobile"||$Contact->Type=="email"){
				echo "<div>";
				echo HtmlTags::InputCheckbox($Contact->Type."Notifications".$Contact->Id,"y","Для уведомлений",$Contact->Notifications=="Y","","","",true,"checkbox-slider",true);
				echo "</div>";
			}
			?>
		</div>
	<?php
	}
	function GetCity($IP){
		$info=geoip_record_by_name($IP);
		$City=@$info["city"];
		$City=strtolower($City);
		return $City;
	}
	function SaveRegionCookie($RegionId){
		global $_COOKIE;
		setcookie("mregion",$RegionId,time()+365*24*60*60,"/");
		$_COOKIE["mregion"]=$RegionId;
	}
	function CheckCityCookie(){
		global $_COOKIE,$GLOBAL,$DataBase,$ActiveLanguage;
		$Id=(int)@$_COOKIE["mregion"];
		if($CityInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Cities} WHERE `Id`={$Id};",false)){
			$this->SaveRegionCookie($CityInfo->Id);
			return $CityInfo;
		}
		else{
			$RegionInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Cities} WHERE `URL`='almaty';",false);
			$this->SaveRegionCookie($RegionInfo->Id);
			return $RegionInfo;
		}
	}
	function MakePhoneLink($MainContactPhone,$AddTelPrefix=true){
		preg_match_all("'([\d]{1,})'si",$MainContactPhone,$Numbers);
		$Numbers=join("",$Numbers[1]);
		if(substr($Numbers,0,1)=="8")
			$Numbers="+7".substr($Numbers,1);
		else
			$Numbers="+".$Numbers;
		if($AddTelPrefix)
			$Numbers="tel:".$Numbers;
		return $Numbers;
	}
	function CheckPhone($Phone){
		return strlen($Phone)==12;
	}
	function CountPlus($Count){
		$Count=$Count/10;
		$Count=floor($Count)*10;
		$Count=$this->DigitalToPrice($Count);
		$Count.="+";
		return $Count;
	}
	function PrintCalendar($Month,$Year,$BlockDays=array(),$CurrentDate=0){
		$CurrentDate=empty($CurrentDate)?time():$CurrentDate;
		$StartDate=mktime(0,0,1,$Month,1,$Year);
		$FinishDate=mktime(23,59,59,date("m",$StartDate),date("t",$StartDate),date("Y",$StartDate));
		$ThisDay=date("N",$StartDate);
		for($i=($ThisDay-1);$i>0;$i--){
			$DayTime=$StartDate-$i*24*60*60;
			?>
			<div class="day other"><?=date("j",$DayTime)?></div>
		<?php
		}
		for($i=$StartDate;$i<=$FinishDate;$i+=24*60*60){
			?>
			<div class="day">
				<?php
				if((date("dmy",$CurrentDate)==date("dmy",$i)||$i>=time())&&!in_array(date("dmy",$i),$BlockDays)){
					echo HtmlTags::InputRadio("day",$i,date("j",$i),date("dmy",$CurrentDate)==date("dmy",$i),"","day".date("dmy",$i),"day",true);
				}
				else{
					echo "<span class=\"day\">".date("j",$i)."</span>";
				}
				?>
			</div>
		<?php
		}
	}
	function PrintTimesList($StartTime,$StartMinute,$FinishTime,$FinishMinute,$TimeStep,$Date=0,$IgnoreTime=array()){
		$Date=empty($Date)?time():$Date;
		$StartTime=mktime($StartTime,$StartMinute,0,date("m",$Date),date("d",$Date),date("Y",$Date));
		$FinishTime=mktime($FinishTime,$FinishMinute,0,date("m",$Date),date("d",$Date),date("Y",$Date));
		for($Time=$StartTime;$Time<=$FinishTime;$Time+=$TimeStep*60){
			?>
			<div class="time">
				<?php
				if($Time>time()&&!in_array(date("Hi",$Time),$IgnoreTime)){
					echo HtmlTags::InputCheckbox("time".date("Hi",$Time),date("H:i",$Time),date("H:i",$Time),false,"","time".date("Hi",$Time),"",true,"time",true);
				}
				else{
					echo HtmlTags::InputCheckbox("time".date("Hi",$Time),date("H:i",$Time),date("H:i",$Time),false,"","time".date("Hi",$Time),"",true,"time",true,true);
				}
				?>
			</div>
		<?php
		}
	}
	function PrintTimeListFromArray($TimeArray,$IgnoreTime=array()){
		global $TextBlocks;
		if(count($TimeArray)==0){
			echo "<div class=\"message error\">{$TextBlocks->DoctorOutOfSchedule->Description}</div>";
		}
		foreach($TimeArray as $Time){
			$Time=$Time->DateTime;
			if(!in_array(date("Hi",$Time),$IgnoreTime)){
				echo "<div class=\"time\">";
				echo HtmlTags::InputCheckbox("time".date("Hi",$Time),date("H:i",$Time),date("H:i",$Time),false,"","time".date("Hi",$Time),"",true,"time",true,!($Time>time()&&!in_array(date("Hi",$Time),$IgnoreTime)));
				echo "</div>";
			}
			unset($Time);
		}
	}
	function PrintRatingBar($Value,$MaxValue=5,$Title=""){
		global $LangPack;
		$Title=empty($Title)?$LangPack->Rating:$Title;
		$RatingValue=$this->RatingValue($Value);
		$RatingWidth=round($Value/$MaxValue*100,0);
		?>
		<div class="ratingBar" title="<?=$Title?>">
			<span class="value"><?=$RatingValue?></span>
			<div class="bar">
				<div class="value" style="width:<?=$RatingWidth?>%;"></div>
			</div>
		</div>
	<?php
	}
	function PrintCompanyRatingBar($Company){
		global $LangPack;
		if($Company->Type=="LABORATORY"||!empty($Company->Rating)||(empty($Company->Rating)&&empty($Company->DoctorRating)))
			$this->PrintRatingBar($Company->Rating);
		else{
			$this->PrintRatingBar($Company->DoctorRating,5,$LangPack->CompanyDoctorRating);
		}
	}
	function PrintCommentBar($Value,$URL="",$NewTab=false){
		?>
		<div class="commentBar">
			<?php
			if (!empty($URL)){
			?>
			<a href="<?=$URL?>"<?=$NewTab?" target=\"blank\"":""?>>
				<?php
				}
				?>
				<span class="value"><?=$this->DigitalToPrice($Value)?></span>
				<span class="label"><?=$this->ReviewCounter($Value)?></span>
				<?php
				if (!empty($URL)){
				?>
			</a>
		<?php
		}
		?>
		</div>
	<?php
	}
	function ReviewCounter($ReviewCount){
		global $LangPack;
		$x=$ReviewCount%100;
		$y=($x%10)-1;
		$Reviews=($x/10)>>0==1?$LangPack->CounterReviews:($y&12?$LangPack->CounterReviews:($y&3?$LangPack->CounterReview:$LangPack->CounterReview1));
		return $Reviews;
	}
	function DoctorCounter($DoctorCount){
		global $LangPack;
		$x=$DoctorCount%100;
		$y=($x%10)-1;
		$Doctors=($x/10)>>0==1?$LangPack->CounterDoctors:($y&12?$LangPack->CounterDoctors:($y&3?$LangPack->CounterDoctor:$LangPack->CounterDoctor1));
		return $Doctors;
	}
	function SharesCounter($ShareCount){
		global $LangPack;
		$x=$ShareCount%100;
		$y=($x%10)-1;
		$Shares=($x/10)>>0==1?$LangPack->CounterShares:($y&12?$LangPack->CounterShares:($y&3?$LangPack->CounterShare:$LangPack->CounterShare1));
		return $Shares;
	}
	function CompanyCounter($CompanyCount){
		global $LangPack;
		$x=$CompanyCount%100;
		$y=($x%10)-1;
		$Companies=($x/10)>>0==1?$LangPack->CounterCompanies:($y&12?$LangPack->CounterCompanies:($y&3?$LangPack->CounterCompany:$LangPack->CounterCompany1));
		return $Companies;
	}
	function ExperienceCounter($Experience){
		global $LangPack;
		$x=$Experience%100;
		$y=($x%10)-1;
		$Experience=($x/10)>>0==1?$LangPack->CounterExperience1:($y&12?$LangPack->CounterExperience1:($y&3?$LangPack->CounterExperience2:$LangPack->CounterExperience3));
		return $Experience;
	}
	/*function GetCompanyPrice($CompanySchedule,$OldPrice=false){
		global $LangPack;

		$Price=array();
		$Field=$OldPrice?"OldPrice":"Price";
		foreach($CompanySchedule as $Schedule){
			if(isset($Schedule->$Field))
				$Price[$Schedule->$Field]=$Schedule->$Field;

			unset($Schedule);
		}

		if(count($Price)==0 || empty(current($Price))){
			return "";
		}if(count($Price)>1){
			$Price=min($Price);

			return $LangPack->from." ".$this->DigitalToPrice($Price)." ".$LangPack->tg;
		}else
			return $this->DigitalToPrice(current($Price))." ".$LangPack->tg;
	}*/
	function PrintDaysArray($Days){
		if(count($Days)<=2)
			$Days=$this->GetDaysArray($Days);
		else{
			$Keys=array_keys($Days);
			$First=$Days[0];
			$Last=$Keys[count($Keys)-1];
			$Last=$Days[$Last];
			$Days=$First."-".$Last;
		}
		return $Days;
	}
	function GetDaysArray($Days){
		$Days=is_array($Days)?$Days:array($Days);
		if(count($Days)<=2)
			$Days=join(",",$Days);
		else{
			$ThisNum=0;
			$LastDay=array_keys($Days);
			$LastDay=current($LastDay);
			$DaysArray=array();
			foreach($Days as $Num=>$Day){
				if($Num!==$LastDay&&($Num-1)!==$LastDay)
					$ThisNum++;
				$LastDay=$Num;
				$DaysArray[$ThisNum][]=$Day;
				unset($Num);
				unset($Day);
			}
			foreach($DaysArray as $Key=>$Days){
				$DaysArray[$Key]=$this->PrintDaysArray($Days);
				unset($Days);
			}
			$Days=join(", ",$DaysArray);
		}
		return $Days;
	}
	function GetCompanyShortSchedule($ScheduleList){
		global $LangPack;
		foreach($ScheduleList as $Key=>$Schedule){
			$Schedule->DaysArray=explode(",",$Schedule->Days);
			$DaySchedule=array();
			foreach($Schedule->DaysArray as $DayKey=>$Day){
				$DayLabel="ShortDay{$Day}";
				$DaySchedule[$Day]=$LangPack->$DayLabel;
				unset($DayLabel);
				unset($Day);
				unset($DayKey);
			}
			$Schedule->DaysArray=$DaySchedule;
			$Schedule->DaysArray=$this->GetDaysArray($Schedule->DaysArray);
			$ScheduleList[$Key]=$Schedule->DaysArray." ".$this->AddZero($Schedule->StartHour).":".$this->AddZero($Schedule->StartMinute)."-".$this->AddZero($Schedule->FinishHour).":".$this->AddZero($Schedule->FinishMinute);
			unset($DaySchedule);
			unset($Key);
			unset($Schedule);
		}
		$ScheduleList="<span class=\"schedule\">".join("</span><span class=\"schedule\">",$ScheduleList)."</span>";
		return $ScheduleList;
	}
	function GetCompanyScopeSchedule($ScheduleList){
		$Days=array("Mo","Tu","We","Th","Fr","Sa","Su");
		foreach($ScheduleList as $Key=>$Schedule){
			$Schedule->DaysArray=explode(",",$Schedule->Days);
			$DaySchedule=array();
			foreach($Schedule->DaysArray as $DayKey=>$Day){
				$DaySchedule[$Day]=$Days[$Day-1];
				unset($Day);
				unset($DayKey);
			}
			$Schedule->DaysArray=$DaySchedule;
			$Schedule->DaysArray=$this->GetDaysArray($Schedule->DaysArray);
			$Schedule->DaysArray=str_replace(" ","",$Schedule->DaysArray);
			$ScheduleList[$Key]=$Schedule->DaysArray." ".$this->AddZero($Schedule->StartHour).":".$this->AddZero($Schedule->StartMinute)."-".$this->AddZero($Schedule->FinishHour).":".$this->AddZero($Schedule->FinishMinute);
			unset($DaySchedule);
			unset($Key);
			unset($Schedule);
		}
		$ScheduleList=join(", ",$ScheduleList);
		return $ScheduleList;
	}
	function GetAddressBlock($Address,$CityId,$MetroIds=array()){
		global $AllCities;
		$Result=array();
		if(isset($AllCities[$CityId])&&!empty($Address)){
			$Address=str_replace("{$AllCities[$CityId]->Title},","",$Address);
			$Address=trim($Address);
			$Result[]=$AllCities[$CityId]->Title;
		}
		if(!empty($Address))
			$Result[]="<span itemprop=\"address\">".$Address."</span>";
		$Result=join(", ",$Result);
		return $Result;
	}
	function PrintArticleBlock($Article,$ViewType="GLOBAL"){
		global $ActiveLanguage,$DIRS;
		$Article->Directories=join("",$Article->Directories);
		?>
		<div class="article">
			<?php
			if($ViewType=="GLOBAL"){
				?>
				<div class="cover">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/blog/<?=$Article->Id?>-<?=$Article->Name?>/">
						<img src="<?=$DIRS->Blogs.$Article->Id?>_bil.jpg" alt="<?=$Article->Title?>" title="<?=$Article->Title?>"/>
					</a>
				</div>
			<?php
			}
			?>
			<div class="info">
				<div class="date"><?=date("d.m.y",$Article->DateTime)?></div>
				<div class="title">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/blog/<?=$Article->Id?>-<?=$Article->Name?>/"><?=$Article->Title?></a>
				</div>
				<div class="annotation"><?=$this->CrCaption($Article->Annotation,96)?></div>
			</div>
			<?php
			if(count($Article->Directories)){
				?>
				<div class="directories"><?=$Article->Directories?></div>
			<?php
			}
			?>
		</div>
	<?php
	}
	function ListToRows($Items,$RowCount=2,$ReturnWithRows=false){
		$ThisItem=0;
		$NewItems=array();
		foreach($Items as $Key=>$Item){
			$NewItems[$ThisItem][$Key]=$Item;
			$ThisItem++;
			$ThisItem=$ThisItem>=$RowCount?0:$ThisItem;
			unset($Item);
			unset($Key);
		}
		if($ReturnWithRows)
			return $NewItems;
		$NewArray=array();
		foreach($NewItems as $Items){
			$NewArray=array_merge($NewArray,$Items);
			unset($Items);
		}
		unset($Items);
		unset($NewItems);
		return $NewArray;
	}
	function PrintBlogBlock($Blog,$NewTab=false,$ViewType="GLOBAL"){
		global $DIRS,$ActiveLanguage,$AllMonths;
		$NewTab=$NewTab?" target=\"_blank\"":"";
		?>
		<div class="blogBlock" data-id="<?=$Blog->Id?>" data-date="<?=date("d.m.Y",$Blog->DateTime)?>">
			<a href="<?=$ActiveLanguage->LinksPrefix?>/blog/<?=$Blog->Id?>-<?=$Blog->Name?>/" class="title"<?=$NewTab?>><?=$Blog->Title?></a>
			<?php
			if($ViewType=="GLOBAL"){
				?>
				<div class="cover">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/blog/<?=$Blog->Id?>-<?=$Blog->Name?>/" class="cover"<?=$NewTab?>>
						<img src="<?=$DIRS->Blogs.$Blog->Id?>_al.jpg" alt="<?=$Blog->Title?>" title="<?=$Blog->Title?>"/>
					</a>
					<div class="date">
						<?php
						/*
			?>
								<span class="day"><?=date("d",$Blog->DateTime)?></span>
								<span class="month"><?=$AllMonths[date("n",$Blog->DateTime)][1]?></span>
								<span class="year"><?=date("Y",$Blog->DateTime)?></span>
						*/
						?>
						<span class="date"><?=date("d",$Blog->DateTime)?> <?=$AllMonths[date("n",$Blog->DateTime)][1]?> <?=date("Y",$Blog->DateTime)?></span>
					</div>
					<div class="info">
						<span class="views"><?=$this->DigitalToPrice($Blog->Views)?></span>
					</div>
				</div>
			<?php
			}
			?>
			<div class="annotation"><?=$Blog->Annotation?></div>
			<div class="specialties">
				<?php
				if($ViewType=="RIGHT"){
					?>
					<span class="category"><?=date("d.m.y",$Blog->DateTime)?></span>
				<?php
				}
				if(isset($Blog->CategoryInfo)&&isset($Blog->CategoryInfo->Id)){
					?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/blog/<?=$Blog->CategoryInfo->Name?>/" class="category" target="_blank"><?=$Blog->CategoryInfo->Title?></a>
				<?php
				}
				?>
				<?=join("",$Blog->Specialties)?>
			</div>
		</div>
	<?php
	}
	function PrintCategoriesCheckbox($ParentId=0,$IgnoreId=0,$SelectedId=0,$Level=0,$ShowNull=true,$ShowDisabled=true){
		global $GLOBAL,$SortLanguage,$DataBase;
		$TitlePrefix=$Level==0?"":(str_repeat("-",$Level)." ");
		$SelectedId=is_array($SelectedId)?$SelectedId:array($SelectedId);
		$ShowDisabled=$ShowDisabled?"":" AND `Status`='Y' ";
		$Categories=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Categories} WHERE `Owner`={$ParentId} {$ShowDisabled} AND `Id`<>{$IgnoreId} ORDER BY Title;");
		if(count($Categories)){
			echo "<ul>";
			foreach($Categories as $Category){
				echo "<li>";
				echo HtmlTags::InputCheckbox("Category{$Category->Id}",$Category->Id,$Category->Title,in_array($Category->Id,$SelectedId),"","Category{$Category->Id}","",true,"checkbox",true);
				$this->PrintCategoriesCheckbox($Category->Id,$IgnoreId,$SelectedId,$Level+1);
				echo "</li>";
				unset($Category);
			}
			echo "</ul>";
			unset($Categories);
		}
	}
	function RatingValue($Rating){
		$Rating=str_replace(",",".",$Rating);
		$Rating=explode(".",$Rating);
		if($Rating[0]==0)
			return $Rating[0];
		$Rating[1]=isset($Rating[1])?$Rating[1]:0;
		$Rating=join(".",$Rating);
		return $Rating;
	}
	public function SendSMS($Phone,$Message){
		return send_sms($Phone,$Message);
	}
	function CreateInnerSQL($FilterInnerStr){
		if(count($FilterInnerStr["ON"])){
			$FilterInnerStr["READY"]=array();
			foreach($FilterInnerStr["LEFT"] as $Type=>$Inner){
				$FilterInnerStr["READY"][$Type]="LEFT JOIN\n {$Inner}\n ON\n {$FilterInnerStr["ON"][$Type]}";
				unset($Type);
				unset($Inner);
			}
			foreach($FilterInnerStr["INNER"] as $Type=>$Inner){
				$FilterInnerStr["READY"][$Type]="INNER JOIN\n {$Inner}\n ON\n {$FilterInnerStr["ON"][$Type]}";
				unset($Type);
				unset($Inner);
			}
			$FilterInnerStr=join("\n",$FilterInnerStr["READY"]);
		}
		else
			$FilterInnerStr="";
		return $FilterInnerStr;
	}
	function PrintAdminAppointmentTableRows($Appointment){
		global $AllCompanies,$AllDoctors,$AllDistricts,$AllShares;
		if($Appointment->Status=="WAIT"){
			if(!empty($Appointment->TimeStr)){
				$Time=explode(",",$Appointment->TimeStr);
				$Time=$Time[0];
				$Time=explode(":",$Time);
				$Appointment->AppointmentDateTime=mktime($Time[0],$Time[1],0,date("m",$Appointment->AppointmentDateTime),date("d",$Appointment->AppointmentDateTime),date("Y",$Appointment->AppointmentDateTime));
			}
		}
		?>
		<tbody data-id="<?=$Appointment->Id?>" data-type="<?=$Appointment->Type?>"<?=$Appointment->Type=="DOCTOR"?" data-doctor-id=\"{$Appointment->TargetId}\" data-company-id=\"{$Appointment->CompanyId}\"":""?><?=$Appointment->Type=="COMPANY"?" data-company-id=\"{$Appointment->TargetId}\"":""?><?=$Appointment->Type=="SHARE"?" data-company-id=\"{$Appointment->CompanyId}\"":""?> data-status="<?=$Appointment->Status?>"<?=($Appointment->Status=="CONFIRM"&&$Appointment->VisitDateTime<time())?" data-visited=\"y\"":""?>>
		<tr>
			<th>Запись:</th>
			<td>
				<strong>#<?=$Appointment->Id?></strong>
				<?php
				if($Appointment->Type=="GLOBAL")
					echo "Общая";
				elseif($Appointment->Type=="DOCTOR")
					echo "К врачу";
				elseif($Appointment->Type=="COMPANY")
					echo "В компанию";
				elseif($Appointment->Type=="SHARE")
					echo "На акцию";
				?>
			</td>
		</tr>
		<tr>
			<th>Дата и время заявки:</th>
			<td><?=date("d.m.y H:i",$Appointment->DateTime)?></td>
		</tr>
		<?php
		if($Appointment->Type=="GLOBAL"&&!empty($Appointment->SpecialtyId)){
			?>
			<tr>
				<th>Указанная специальность:</th>
				<td><?=$Appointment->Specialty->Title?></td>
			</tr>
		<?php
		}
		elseif($Appointment->Type=="DOCTOR"){
			if(!empty($Appointment->TargetId)){
				?>
				<tr>
					<th>Врач:</th>
					<td><?=$AllDoctors[$Appointment->TargetId]->FullName?></td>
				</tr>
				<?php
				if(isset($AllDoctors[$Appointment->TargetId]->Specialties)&&count($AllDoctors[$Appointment->TargetId]->Specialties)){
					?>
					<tr>
						<th>Специализация:</th>
						<td><?=join(", ",$AllDoctors[$Appointment->TargetId]->Specialties)?></td>
					</tr>
				<?php
				}
				if(isset($AllDoctors[$Appointment->TargetId]->Skills)&&count($AllDoctors[$Appointment->TargetId]->Skills)){
					?>
					<tr>
						<th>Квалификация:</th>
						<td><?=join(", ",$AllDoctors[$Appointment->TargetId]->Skills)?></td>
					</tr>
				<?php
				}
			}
		}
		elseif($Appointment->Type=="SHARE"){
			?>
			<tr>
				<th>Акция:</th>
				<td><?=$AllShares[$Appointment->TargetId]->Title?></td>
			</tr>
		<?php
		}
		if(!empty($Appointment->CompanyId)){
			?>
			<tr>
				<th>В компанию:</th>
				<td><?=$AllCompanies[$Appointment->CompanyId]->Title?></td>
			</tr>
		<?php
		}
		if($Appointment->Type=="COMPANY"&&!empty($Appointment->ServiceId)){
			?>
			<tr>
				<th>Услуга:</th>
				<td><?=$Appointment->Service->Title?></td>
			</tr>
		<?php
		}
		if(!empty($Appointment->Districts)){
			$Districts=explode(",",$Appointment->Districts);
			foreach($Districts as $Key=>$District){
				if(isset($AllDistricts[$District]))
					$Districts[$Key]=$AllDistricts[$District]->Title;
				else
					unset($Districts[$Key]);
				unset($Key);
				unset($District);
			}
			if(count($Districts)){
				$Districts=join(", ",$Districts);
				?>
				<tr>
					<th>Районы:</th>
					<td><?=$Districts?></td>
				</tr>
			<?php
			}
		}
		if($Appointment->ForChild=="Y"||$Appointment->HouseCall=="Y"){
			$Wishes=array();
			if($Appointment->ForChild=="Y")
				$Wishes[]="Для ребенка";
			if($Appointment->HouseCall=="Y")
				$Wishes[]="Вызов на дом";
			$Wishes=join(", ",$Wishes);
			?>
			<tr>
				<th>Пожелания:</th>
				<td><?=$Wishes?></td>
			</tr>
		<?php
		}
		?>
		<tr>
			<th>Имя:</th>
			<td><?=$Appointment->Name?></td>
		</tr>
		<tr>
			<th>Телефон:</th>
			<td><?=$Appointment->Phone?></td>
		</tr>
		<?php
		if(!empty($Appointment->EMail)){
			?>
			<tr>
				<th>EMail:</th>
				<td><?=urldecode($Appointment->EMail)?></td>
			</tr>
		<?php
		}
		if(!empty($Appointment->AppointmentDateTime)){
			?>
			<tr>
				<th>Желаемое время визита:</th>
				<td>
					<?=date("d.m.y",$Appointment->AppointmentDateTime)?>
					<?php
					if(!empty($Appointment->TimeStr)){
						?>
						<?=$Appointment->TimeStr?>
					<?php
					}
					?>
				</td>
			</tr>
		<?php
		}
		if(!empty($Appointment->VisitDateTime)){
			?>
			<tr>
				<th>Фактическая дата записи:</th>
				<td><?=date("d.m.y H:i",$Appointment->VisitDateTime)?></td>
			</tr>
		<?php
		}
		if(!empty($Appointment->Price)){
			?>
			<tr>
				<th>Стоимость:</th>
				<td><?=$this->DigitalToPrice($Appointment->Price)?> тг.</td>
			</tr>
		<?php
		}
		if(($Appointment->Status=="CONFIRM"/* && $Appointment->VisitDateTime>time()*/)||$Appointment->Status=="WAIT"){
			?>
			<tr>
				<td colspan="2" class="cancel">
					<form action="?cancel&id=<?=$Appointment->Id?>" method="post">
						<?=HtmlTags::InputText("reason","","","text","reason{$Appointment->Id}","text",0,false,0,"","","","","","Введите причину отмены заявки",true)?>
						<?=HtmlTags::InputSubmit("Отменить","","","",true,"submit","button delete")?>
					</form>
				</td>
			</tr>
			<?php
			if(($Appointment->Status=="WAIT"/* && $Appointment->AppointmentDateTime>time()*/)||($Appointment->Status=="WAIT"&&$Appointment->Type=="GLOBAL")){
				?>
				<tr>
					<td colspan="2" class="confirm">
						<form action="?confirm&id=<?=$Appointment->Id?>" method="post">
							<?=HtmlTags::InputText("date",date("d/m/Y",$Appointment->AppointmentDateTime),"","text","date{$Appointment->Id}","text calendar",10,false,12)?>
							<select name="time" title="Время визита">
								<?php
								$CurrentTime=explode(",",$Appointment->TimeStr);
								$CurrentTime=@$CurrentTime[0];
								foreach($Appointment->ScheduleTime as $Time){
									$Value=date("H:i",$Time->DateTime);
									echo HtmlTags::option($Value,$Value,$Value==$CurrentTime);
									unset($Time);
								}
								?>
							</select>
							<?=HtmlTags::InputSubmit("Подтвердить","","","",true,"submit","button add")?>
						</form>
					</td>
				</tr>
			<?php
			}
		}
		if(in_array($Appointment->Status,array("CONFIRM"))&&$Appointment->VisitDateTime<time()){
			?>
			<tr>
				<td colspan="2">
					<form action="?visited&id=<?=$Appointment->Id?>" method="post">
						<table class="table">
							<colgroup>
								<col width="30%"/>
								<col width="70%"/>
							</colgroup>
							<thead>
							<tr>
								<th colspan="2">Отзыв после визита</th>
							</tr>
							</thead>
							<tbody>
							<?php
							if($Appointment->Type=="DOCTOR"){
								?>
								<tr>
									<th>Внимательность:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("attentive",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->Care:5),"","attentive{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
								<tr>
									<th>Цена/Качество:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("price",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->Price:5),"","price{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
							<?php
							}
							elseif($Appointment->Type=="COMPANY"){
								?>
								<tr>
									<th>Время ожидания:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("waittime",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->WaitTime:5),"","waittime{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
								<tr>
									<th>Работа регестратуры:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("reception",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->Reception:5),"","reception{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
								<tr>
									<th>Комфорт пребывания:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("staycomfort",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->StayComfort:5),"","staycomfort{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
							<?php
							}
							elseif($Appointment->Type=="SHARE"){
								?>
								<tr>
									<th>Общая оценка:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("rating",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->Rating:5),"","rating{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
								<tr>
									<th>Цена/Качество:</th>
									<td>
										<?php
										for($i=0;$i<=5;$i++){
											echo HtmlTags::InputRadio("price",$i,empty($i)?"-":"{$i}",$i==(isset($Appointment->Review)?$Appointment->Review->Price:5),"","price{$i}{$Appointment->Id}","radio",true);
										}
										?>
									</td>
								</tr>
							<?php
							}
							?>
							<tr>
								<th>Рекомендуете другим пациентам?</th>
								<td>
									<?=HtmlTags::InputRadio("recommend","y","да",isset($Appointment->Review)?$Appointment->Review->IsRecommended=="Y":true,"","recommendY{$Appointment->Id}","radio",true)?>
									<?=HtmlTags::InputRadio("recommend","n","нет",isset($Appointment->Review)?$Appointment->Review->IsRecommended=="N":false,"","recommendN{$Appointment->Id}","radio",true)?>
									<?=HtmlTags::InputRadio("recommend","yn","нейтрально",isset($Appointment->Review)?$Appointment->Review->IsRecommended=="YN":false,"","recommendYN{$Appointment->Id}","radio",true)?>
								</td>
							</tr>
							<tr>
								<th>Отзыв:</th>
								<td><?=HtmlTags::textarea("review",isset($Appointment->Review)?$Appointment->Review->Review:"",40,5,"","","review{$Appointment->Id}","text")?></td>
							</tr>
							<tr>
								<th colspan="2"><?=HtmlTags::InputSubmit("Отправить","","","",true,"submit","button add")?></th>
							</tr>
							</tbody>
						</table>
					</form>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	<?php
	}
	function PrintCallBackRequestBlock($Request){
		?>
		<tbody>
		<tr>
			<th>Имя:</th>
			<td><?=$Request->Name?></td>
		</tr>
		<tr>
			<th>Телефон:</th>
			<td><?=$Request->Phone?></td>
		</tr>
		<tr>
			<th>Дата и время:</th>
			<td><?=date("d.m.y H:i",$Request->DateTime)?></td>
		</tr>
		<tr>
			<th>Примечание:</th>
			<td class="comment"><?=$Request->Comment?></td>
		</tr>
		<tr>
			<th>Заявка со страницы:</th>
			<td>
				<a href="<?=$Request->PageURL?>" target="_blank"><?=$Request->PageURL?></a>
			</td>
		</tr>
		<?php
		if($Request->Status=="N"){
			?>
			<tr>
				<td colspan="2">
					<a href="?confirmCall&id=<?=$Request->Id?>" class="button callBackBtn">Уже перезвонили</a>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
	<?php
	}
	function FormatDescription($Description){
		preg_match_all("'<a.*?>.*?</a>'si",$Description,$Links);
		foreach($Links[0] as $Link){
			if(!preg_match("'target='si",$Link)){
				$NewLink=preg_replace("'(href=\".*?\")'si","$1 target=\"blank\"",$Link);
				$Description=str_replace($Link,$NewLink,$Description);
			}
			unset($Link);
		}
		unset($Links);
		return $Description;
	}
	function GetMatchesKeyArray($SearchArray=array(),$ValuesArray=array(),&$FillingArray){
		foreach($SearchArray as $Search){
			if(is_object($Search)&&isset($Search->Title))
				$SearchStr=$Search->Title;
			elseif(is_object($Search)&&isset($Search->Value))
				$SearchStr=$Search->Value;
			else
				$SearchStr=$Search;
			foreach($ValuesArray as $Key=>$Str){
				if(mb_stripos($SearchStr,$Str,null,"UTF-8")!==false)
					$FillingArray[$Key]=$Key;
				unset($Key);
				unset($Str);
			}
			unset($SearchStr);
			unset($Search);
		}
	}
	function GetResponse($Answer){
		if(isset($_GET["ajax"])){
			echo $Answer;
			exit;
		}
	}
	function GetLikeArray($FieldName,$Values,$ReturnString=true){
		$Results=array();
		$FieldName=is_array($FieldName)?$FieldName:array($FieldName);
		foreach($FieldName as $Field){
			foreach($Values as $Value){
				$Value=is_array($Value)?(join("% %",$Value)):$Value;
				$Results[$Field][$Value]="{$Field} LIKE '%{$Value}%'";
				//$Results[$Field][$Value]="%{$Value}%";
				unset($Value);
			}
			//$Results[$Field]="{$Field} LIKE '".join(" ",$Results[$Field])."'";
			unset($Field);
		}
		//print_r($Results);
		if($ReturnString){
			foreach($Results as $Field=>$Values){
				$Values=join(" OR ",$Values);
				$Results[$Field]=$Values;
				unset($Field);
				unset($Values);
			}
			$Results=join(" OR ",$Results);
		}
		return $Results;
	}
	function GetSearchBarHtml($SearchBarArray,$SearchBarMaxCount=15,$Final=false){
		global $ActiveLanguage,$DIRS,$LangPack;
		if(count($SearchBarArray)>=$SearchBarMaxCount||($Final&&count($SearchBarArray))){
			if(!isset($_GET["json"])){
				?>
				<ul>
			<?php
			}
			foreach($SearchBarArray as $Key=>$Item){
				if($Item->Type=="DOCTOR"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/doctors/{$Item->Id}-{$Item->Name}/";
					$Item->Cover="{$DIRS->Photos}{$Item->PhotoId}_sbi.jpg";
					$Item->Title=$Item->FullName;
					$Item->TypeTitle=$LangPack->ItemTypeDoctor;
				}
				elseif($Item->Type=="CLINIC"||$Item->Type=="BOTH"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/clinics/{$Item->Id}-{$Item->Name}/";
					$Item->Cover="{$DIRS->Companies}{$Item->Id}_sbi.png";
					$Item->TypeTitle=$LangPack->ItemTypeClinic;
				}
				elseif($Item->Type=="LABORATORY"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/laboratories/{$Item->Id}-{$Item->Name}/";
					$Item->Cover="{$DIRS->Companies}{$Item->Id}_sbi.png";
					$Item->TypeTitle=$LangPack->ItemTypeLaboratory;
				}
				elseif($Item->Type=="SHARE"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/shares/{$Item->Id}-{$Item->Name}/";
					$Item->Cover="{$DIRS->Shares}{$Item->Id}_sbi.jpg";
					$Item->TypeTitle=$LangPack->ItemTypeLaboratory;
				}
				elseif($Item->Type=="BLOG"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/blog/{$Item->Id}-{$Item->Name}/";
					$Item->Cover="{$DIRS->Blogs}{$Item->Id}_sbi.jpg";
					$Item->TypeTitle=$LangPack->ItemTypeBlog;
				}
				elseif($Item->Type=="DIRECTORY"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/directory/{$Item->Name}/";
					$Item->Cover="{$DIRS->Images}directory.svg";
					$Item->TypeTitle=$LangPack->ItemTypeDirectory;
				}
				elseif($Item->Type=="ARTICLE"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/directory/{$Item->DirectoryName}/{$Item->Name}/";
					$Item->Cover="{$DIRS->Images}article.svg";
					$Item->TypeTitle=$LangPack->ItemTypeDirectoryArticle;
				}
				elseif($Item->Type=="DISEASE"){
					$Item->URL="{$ActiveLanguage->LinksPrefix}/diseases/{$Item->Name}/";
					$Item->Cover="{$DIRS->Images}disease.svg";
					$Item->TypeTitle=$LangPack->ItemTypeDisease;
				}
				if(!isset($_GET["json"])){
					?>
					<li>
						<a href="<?=$Item->URL?>">
							<img src="<?=$Item->Cover?>" alt="<?=$Item->Title?>" title="<?=$Item->Title?>"/>
							<span class="title"><?=$Item->Title?></span>
							<span class="type"><?=$Item->TypeTitle?></span>
						</a>
					</li>
				<?php
				}
				else{
					$SearchBarArray[$Key]=$Item->Title;
				}
				unset($Item);
			}
			if(!isset($_GET["json"])){
				?>
				<li class="total">
					<label for="searchBtn"><?=$LangPack->ShowAllResults?></label>
				</li>
				</ul>
			<?php
			}
			else{
				header('Content-Type: application/json');
				$SearchBarArray="\"".join("\",\"",$SearchBarArray)."\"";
				$JSON="[\"{$_GET["s"]}\",[{$SearchBarArray}],[],[],{\"google:suggesttype\":[\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\",\"QUERY\"]}]";
				echo $JSON;
			}
			exit;
		}
		elseif(count($SearchBarArray)==0){
			?>
			<div class="noresults"><?=$LangPack->NoResults?></div>
			<?php
			exit;
		}
	}
	function GetMetaFromDescription($Input){
		preg_match("'<p.*?>(.*?)</p>'si",$Input,$Meta);
		if(isset($Meta[1])){
			$Meta=$Meta[1];
			$Meta=strip_tags($Meta);
			$Meta=html_entity_decode($Meta);
			$Meta=$this->PostToSQL($Meta);
			if(mb_strlen($Meta,"UTF-8")>50){
				return $Meta;
			}
		}
		return false;
	}
	function CheckAdminUpdate($Title){
		if(empty($Title)){
			header("Location: {$_SERVER["HTTP_REFERER"]}");
			exit;
		}
	}
	function PrintProductBlock($Product,$ViewType){
		global $ActiveLanguage,$DIRS,$LangPack;
		$BasketURL=$Product->StockStatus=="ORDER"?"{$ActiveLanguage->LinksPrefix}/products/{$Product->Id}-{$Product->Name}/":"#";
		?>
		<div class="product <?=strtolower($ViewType)?> ico-<?=$Product->IsSell == 'Y'  ?'sell':''?> ico-<?=strtolower($Product->Type)?> <?=(!empty($Product->BestCost)&&($Product->BestCost=='Y'))?"bestcost":""?>" data-id="<?=$Product->Id?>" data-price="<?=$Product->Price?>">
			<?=(!empty($Product->HasGift)&&($Product->HasGift=='Y'))?"<span class=\"hasgift\"></span>":""?>
			<?php
			if($ViewType=="MAIN"||$ViewType=="GRID"){
				?>
				<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$Product->Id?>-<?=$Product->Name?>/" class="product">
					<img src="<?=$DIRS->Photos.$Product->PhotoId?>_mpp.jpg" alt="<?=$Product->Title?>" title="<?=$Product->Title?>"/>
					<span class="title"><?=$Product->Title?></span>
					<div class="center">
						<?php if(!empty($Product->OldPrice)): ?>
							<span class="old-price"><?=str_replace("%PRICE%",$this->DigitalToPrice($Product->OldPrice),$LangPack->PriceTg)?></span>
						<?php endif; ?>
						<?php if ($Product->Price): ?>
							<span class="price"><?=str_replace("%PRICE%",$this->DigitalToPrice($Product->Price),$LangPack->PriceTg)?></span>
						<?php else: ?>
							<span style="font-size: 15px;"><?= $LangPack->ZeroPrice ?></span>
						<?php endif; ?>
					</div>
				</a>

				<div class="buttons">
					<?php if ($Product->Price): ?>
						<a href="<?=$BasketURL?>" class="basket"><?=$LangPack->buy?></a>
					<?php endif; ?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$Product->Id?>-<?=$Product->Name?>/" class="more"><?=$LangPack->more?></a>
				</div>
			<?php
			}
			elseif($ViewType=="LIST"){
				?>
				<div class="photos">
					<div class="cover">
						<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$Product->Id?>-<?=$Product->Name?>/" class="product">
							<img src="<?=$DIRS->Photos.$Product->PhotoId?>_pll.jpg" alt="<?=$Product->Title?>" title="<?=$Product->Title?>"/>
						</a>
					</div>
					<div class="list">
						<?php
						foreach($Product->Photos as $Photo){
							?>
							<a href="<?=$DIRS->Photos.$Photo->Id?>_pll.jpg" target="_blank">
								<img src="<?=$DIRS->Photos.$Photo->Id?>_psl.jpg" alt="<?=$Product->Title?>" title="<?=$Product->Title?>"/>
							</a>
							<?php
							unset($Photo);
						}
						?>
					</div>
				</div>
				<div class="info">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$Product->Id?>-<?=$Product->Name?>/" class="title"><?=$Product->Title?></a>
					<div class="price">
						<?php if ($Product->Price): ?>
							<span class="price"><?=str_replace("%PRICE%",$this->DigitalToPrice($Product->Price),$LangPack->PriceTg)?></span>
							<a href="<?=$BasketURL?>" class="buy basket"><?=$LangPack->buy?></a>
						<?php else: ?>
							<div style="font-size: 15px;"><?= $LangPack->ZeroPrice ?></div>
						<?php endif ?>
					</div>
					<div class="annotation"><?=$Product->Annotation?></div>
					<div class="more">
						<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$Product->Id?>-<?=$Product->Name?>/"><?=$LangPack->more?></a>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	<?php
	}
	public function PrintReviewBlock($Review,$ViewType){
		global $ActiveLanguage,$DIRS,$LangPack;
		?>
		<div class="review" data-id="<?=$Review->Id?>">
			<?php
			if($ViewType=="MAIN"){
				?>
				<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/" class="review">
					<img src="<?=$DIRS->Reviews.$Review->Id?>_rl.jpg" alt="<?=$Review->Title?>" title="<?=$Review->Title?>"/>
					<span class="title"><?=$Review->Title?></span>
				</a>
				<? /*<div class="buttons">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/" class="more"><?=$LangPack->more?></a>
				</div> */ ?>
			<?php
			}
			elseif($ViewType=="LIST"){
				?>
				<div class="cover">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/">
						<img src="<?=$DIRS->Reviews.$Review->Id?>_rl.jpg" alt="<?=$Review->Title?>" title="<?=$Review->Title?>"/>
					</a>
				</div>
				<div class="info">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/" class="title"><?=$Review->Title?></a>
					<div class="description"><span>
					<?=$Review->ShortDescription?></span><span>Подробнее...</span></div>
						
					<div class="more">
						<div class="date"><?=date("d.m.y",$Review->DateTime)?></div>
						<? /*<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/" class="more"><?=$LangPack->more?></a> */?>
					</div>
				</div>
				<a href="<?=$ActiveLanguage->LinksPrefix?>/products/reviews/<?=$Review->Id?>-<?=$Review->Name?>/" class="mega-cover"><!-- <?=$Review->Title?> --></a>
			<?php
			}
			?>
		</div>
	<?php
	}
	function PrintShareBlock($Share,$ViewType){
		global $ActiveLanguage,$DIRS,$LangPack,$AllMonths;
		?>
		<div class="share" data-id="<?=$Share->Id?>" data-time="<?=$Share->FinishTime?>">
			<?php
			if($ViewType=="LIST"){
				?>
				<div class="cover">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/">
						<img src="<?=$DIRS->Shares.$Share->Id?>_sl.jpg" alt="<?=$Share->Title?>" title="<?=$Share->Title?>"/>
					</a>
				</div>
				<div class="info">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/" class="title"><?=$Share->Title?></a>
					<div class="finish">
						<span class="sticker">Акция</span>
						<span><?=date("d",$Share->FinishTime)?> <?=$AllMonths[date("n",$Share->FinishTime)][1]?></span>
					</div>
					<div class="description"><?=$this->GetMetaFromDescription($Share->Description_ru)?></div>
					<div class="more">
<!-- 						<div class="date"><?=date("d.m.y",$Share->DateTime)?></div> -->
<!-- 						<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/" class="more"><?=$LangPack->more?></a> -->
<!-- здесь была кнопка и дата -->
					</div>
				</div>
			<?php
			} elseif ($ViewType=="MAIN") { ?>
				<div class="cover">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/">
						<img src="<?=$DIRS->Shares.$Share->Id?>_slm.jpg" alt="<?=$Share->Title?>" title="<?=$Share->Title?>"/>
					</a>
				</div>
				<div class="info">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/" class="title"><?=$Share->Title?></a>
					<div class="finish">
						<span class="sticker">Акция</span>
						<span><?=date("d",$Share->FinishTime)?> <?=$AllMonths[date("n",$Share->FinishTime)][1]?></span>
					</div>
					<div class="description"><?=$this->GetMetaFromDescription($Share->Description_ru)?></div>
					<div class="more">
<!-- 						<div class="date"><?=date("d.m.y",$Share->DateTime)?></div> -->
<!-- 						<a href="<?=$ActiveLanguage->LinksPrefix?>/products/shares/<?=$Share->Id?>-<?=$Share->Name?>/" class="more"><?=$LangPack->more?></a> -->
<!-- здесь была кнопка и дата -->
					</div>
				</div><?php
			}
			?>
		</div>
	<?php
	}
	function CreateBasket($UserId,$type="B"){
		global $GLOBAL,$DataBase;
		$DateTime=time();

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Baskets} SET `UserId`={$UserId},`DateTime`={$DateTime},`Status`='NP',`TotalItems`=0,`TotalToPay`=0,`TypeBasket`='{$type}';");
		$BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE Id={$LastId};",false,array(),"Id",false,false,array("Items"));
		if(empty($UserId)){
			setcookie("basketid",md5($LastId),time()+365*24*60*60,"/");
			$_COOKIE["basketid"]=md5($LastId);
		}
		return $BasketInfo;
	}
	function GetBasketItems(&$BasketInfo){
		global $GLOBAL,$DataBase;
		$BasketInfo->Items=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id} ORDER BY `DateTime`;");
	}
	function GetBasketInfo($UserId=0,$type="B"){
		global $GLOBAL,$DataBase;
		if(!empty($UserId)){
			if($BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `UserId`={$UserId} AND `TypeBasket`='{$type}' AND `Status`='NP';",false)){
				$this->GetBasketItems($BasketInfo);
				return $BasketInfo;
			}
			else{
				return $this->CreateBasket($UserId);
			}
		}
		else{
			if(isset($_COOKIE["basketid"])){
				$BasketId=$_COOKIE["basketid"];
				if(preg_match("'^([\d\D]{32})$'si",$BasketId)){
					if($BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `UserId`={$UserId} AND MD5(`Id`)='{$BasketId}' AND `Status`='NP';",false)){
						$this->GetBasketItems($BasketInfo);
						return $BasketInfo;
					}
					else{
						return $this->CreateBasket($UserId);
					}
				}
				else{
					return $this->CreateBasket($UserId);
				}
			}
			else{
				return $this->CreateBasket($UserId);
			}
		}
	}
	function CalculateBasket(&$BasketInfo){
		global $GLOBAL,$DataBase;
		$BasketItems=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id};");
		$ProductIds=array();
		$SimProductIds=array();
		$OptionIds=array();
		foreach($BasketItems as $Item){
			if(!empty($Item->ProductId))
				$ProductIds[$Item->ProductId]=$Item->ProductId;
			if(!empty($Item->SimProductId))
				$SimProductIds[$Item->SimProductId]=$Item->SimProductId;
			if(!empty($Item->OptionId))
				$OptionIds[$Item->OptionId]=$Item->OptionId;
			unset($Item);
		}
		if(count($ProductIds)){
			$ProductIds=join(",",$ProductIds);
			$AllProducts=$DataBase->Query("SELECT * FROM {$GLOBAL->Products} WHERE Id IN ({$ProductIds});",true,array(),"Id",false,false,array("PhotoId"=>"int"));
			$ProductIds=array_keys($AllProducts);
			$ProductIds=join(",",$ProductIds);
			$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Owner` IN ({$ProductIds}) AND `Type`='PRODUCT' ORDER BY `Position`;");
			foreach($Photos as $Photo){
				if(empty($AllProducts[$Photo->Owner]->PhotoId))
					$AllProducts[$Photo->Owner]->PhotoId=$Photo->Id;
				unset($Photo);
			}
			unset($Photos);
		}
		if(count($SimProductIds)){
			$SimProductIds=join(",",$SimProductIds);
			$AllSimProducts=$DataBase->Query("SELECT * FROM {$GLOBAL->SimProducts} WHERE Id IN ({$SimProductIds});");
		}
		if(count($OptionIds)){
			$OptionIds=join(",",$OptionIds);
			$AllOptions=$DataBase->Query("SELECT * FROM {$GLOBAL->ProductOptions} WHERE Id IN ({$OptionIds});");
		}
		$Amount=0;
		$TotalToPay=0;
		foreach($BasketItems as $Item){
			$OldPrice=$Item->Price;
			if(!empty($Item->ProductId)&&empty($Item->SimProductId)&&empty($Item->OptionId))
				$Item->Price=$AllProducts[$Item->ProductId]->Price;
			elseif(!empty($Item->SimProductId)&&empty($Item->ProductId)&&empty($Item->OptionId)) {
				// И здесь тогда тоже нужен костыль 
				if ($Item->SimProductId != 56) {
					$Item->Price=$AllSimProducts[$Item->SimProductId]->Price;
				};
			} elseif(!empty($Item->OptionId))
				$Item->Price=$AllOptions[$Item->OptionId]->Price;
			$Amount+=$Item->Amount;
			$TotalToPay+=$Item->Price*$Item->Amount;
			$BasketItems[$Item->Id]->Price=$Item->Price;
			if($OldPrice!==$Item->Price)
				$DataBase->UpdateQuery("UPDATE {$GLOBAL->BasketItems} SET `Price`={$Item->Price} WHERE Id={$Item->Id};");
			unset($Item);
		}
		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET `TotalItems`={$Amount},`TotalToPay`={$TotalToPay} WHERE Id={$BasketInfo->Id};");
		$BasketInfo->TotalItems=$Amount;
		$BasketInfo->TotalToPay=$TotalToPay;
		$BasketInfo->Items=$BasketItems;
	}
	function PrintBasketItem($Item,$FullItem=true){
		global $ActiveLanguage,$DIRS,$AllProducts,$AllSimProducts,$LangPack;
		?>
		<div class="item" data-id="<?=$Item->Id?>">
			<div class="cover">
				<?php
				if(!empty($Item->ProductId)){
					?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<img src="<?=$DIRS->Photos.$AllProducts[$Item->ProductId]->PhotoId?>_bil.jpg" alt="<?=$AllProducts[$Item->ProductId]->Title?>" title="<?=$AllProducts[$Item->ProductId]->Title?>"/>
					</a>
				<?php
				}
				?>
			</div>
			<div class="title">
				<?php
				if(!empty($Item->ProductId)&&!empty($Item->SimProductId)){
					?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<?=$AllProducts[$Item->ProductId]->Title?> + <?=$AllSimProducts[$Item->SimProductId]->Title?>
					</a>
				<?php
				}
				elseif(!empty($Item->ProductId)){
					?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<?=$AllProducts[$Item->ProductId]->Title?>
					</a>
				<?php
				}
				elseif(!empty($Item->SimProductId)){
					?>
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/sim/" target="_blank"><?=$AllSimProducts[$Item->SimProductId]->Title?></a>
				<?php
				}
				?>
			</div>
			<div class="price"><?=str_replace("%PRICE%",$this->DigitalToPrice($Item->Price),$LangPack->PriceTg)?></div>
			<div class="amount">
				<label for="amount<?=$Item->Id?>"><?=$LangPack->Amount?></label>
				<?php
				if($FullItem){
					?>
					<?=HtmlTags::InputText("amount{$Item->Id}",$Item->Amount,"","number","amount{$Item->Id}","text",0,false,0,"","","","min=\"1\"")?>
				<?php
				}
				else{
					echo "<span>".$this->DigitalToPrice($Item->Amount)."</span>";
				}
				?>
			</div>
			<div class="total"><?=$LangPack->Summ?>
				<span><?=str_replace("%PRICE%",$this->DigitalToPrice($Item->Amount*$Item->Price),$LangPack->PriceTg)?></span>
			</div>
			<?php
			if($FullItem){
				?>
				<div class="delete">
					<a href="#"><?=$LangPack->Delete?></a>
				</div>
			<?php
			}
			?>
		</div>
	<?php
	}
	function PrintBasketAdminItem($Item,$FullItem=true){
		global $DIRS,$AllProducts,$AllSimProducts,$LangPack;
		?>
		<tr>
			<td class="cover">
				<?php
				if(!empty($Item->ProductId)){
					?>
					<a href="/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<img src="<?=$DIRS->Photos.$AllProducts[$Item->ProductId]->PhotoId?>_bil.jpg" alt="<?=$AllProducts[$Item->ProductId]->Title?>" title="<?=$AllProducts[$Item->ProductId]->Title?>"/>
					</a>
				<?php
				}
				?>
			</td>
			<td class="title">
				<?php
				if(!empty($Item->ProductId)&&!empty($Item->SimProductId)){
					?>
					<a href="/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<?=$AllProducts[$Item->ProductId]->Title?> + <?=$AllSimProducts[$Item->SimProductId]->Title?>
					</a>
				<?php
				}
				elseif(!empty($Item->ProductId)){
					?>
					<a href="/products/<?=$AllProducts[$Item->ProductId]->Id?>-<?=$AllProducts[$Item->ProductId]->Name?>/" target="_blank">
						<?=$AllProducts[$Item->ProductId]->Title?>
					</a>
				<?php
				}
				elseif(!empty($Item->SimProductId)){
					?>
					<a href="/products/sim/" target="_blank"><?=$AllSimProducts[$Item->SimProductId]->Title?></a>
				<?php
				}
				?>
			</td>
			<td class="price"><?=str_replace("%PRICE%",$this->DigitalToPrice($Item->Price),$LangPack->PriceTg)?></td>
			<td class="amount">
				<label for="amount<?=$Item->Id?>"><?=$LangPack->Amount?></label>
				<?php
				echo "<span>".$this->DigitalToPrice($Item->Amount)."</span>";
				?>
			</td>
			<td class="total"><?=$LangPack->Summ?>
				<span><?=str_replace("%PRICE%",$this->DigitalToPrice($Item->Amount*$Item->Price),$LangPack->PriceTg)?></span>
			</td>
		</tr>
	<?php
	}
	function PrintBasketStatusBar($BasketInfo,$ViewType="GLOBAL",$FullItem=true){
		global $LangPack,$ActiveLanguage;
		?>
		<div class="actions">
			<div class="price<?=isset($BasketInfo->DiscountPrice)?" discount":""?>">
				<span class="label"><?=$LangPack->Total?></span>
				<span class="value"><?=str_replace("%PRICE%",$this->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg)?></span>
			</div>
			<?php
			if(isset($BasketInfo->DiscountPrice)){
				?>
				<div class="price">
					<span class="label"><?=$LangPack->PriceWithDiscount?></span>
					<span class="value"><?=str_replace("%PRICE%",$this->DigitalToPrice($BasketInfo->DiscountPrice),$LangPack->PriceTg)?></span>
				</div>
			<?php
			}
			?>
			<div class="products">
				<span class="label"><?=$LangPack->TotalProducts?></span>
				<span class="value"><?=$this->DigitalToPrice($BasketInfo->TotalItems)?></span>
			</div>
			<?php
			if($ViewType=="GLOBAL"&&$FullItem){
				?>
				<div class="promo">
					<span class="label"><?=$LangPack->PromoCode?></span>
					<div class="value">
						<?=HtmlTags::InputText("promo","","","text","promo","text")?>
						<?=HtmlTags::InputSubmit($LangPack->Activate,"","","",true,"submit","submit")?>
					</div>
				</div>
			<?php
			}
			?>
		</div>
		<?php
		if(!$FullItem){
			$PayMethod="";
			switch($BasketInfo->PayMethod){
				case "CASH":
					$PayMethod=$LangPack->PayMethodCash;
					break;
				case "BANK":
					$PayMethod=$LangPack->PayMethodBank;
					break;
				case "ONLINE":
					$PayMethod=$LangPack->PayMethodOnline;
					break;
			}
			$DeliveryTime="";
			switch($BasketInfo->DeliveryTime){
				case "ANY":
					$DeliveryTime=$LangPack->DeliveryTimeAny;
					break;
				case "MORNING":
					$DeliveryTime=$LangPack->DeliveryTimeMorning;
					break;
				case "DAY":
					$DeliveryTime=$LangPack->DeliveryTimeDay;
					break;
				case "EVENING":
					$DeliveryTime=$LangPack->DeliveryTimeEvening;
					break;
			}
			$DeliveryType="";
			switch($BasketInfo->DeliveryType){
				case "PICKUP":
					$DeliveryType=$LangPack->DeliveryPickup;
					break;
				case "COURIER":
					$DeliveryType=$LangPack->DeliveryCourier;
					break;
				case "EXPRESS":
					$DeliveryType=$LangPack->DeliveryExpress;
					break;
			}
			?>
			<div class="actions info">
				<div class="item">
					<span class="title"><?=$LangPack->PayMethodTotal?></span>
					<span class="value"><?=$PayMethod?></span>
				</div>
				<div class="item">
					<span class="title"><?=$LangPack->DeliveryTimeChoose?></span>
					<span class="value"><?=$DeliveryTime?></span>
				</div>
				<div class="item">
					<span class="title"><?=$LangPack->DeliveryTypeChoose?></span>
					<span class="value"><?=$DeliveryType?></span>
				</div>
				<div class="item back">
					<a href="<?=$ActiveLanguage->LinksPrefix?>/products/" class="back"><?=$LangPack->BackToMarket?></a>
				</div>
			</div>
		<?php
		}
	}
	function mb_ucfirst($str,$enc='utf-8'){
		return mb_strtoupper(mb_substr($str,0,1,$enc),$enc).mb_substr($str,1,mb_strlen($str,$enc),$enc);
	}
	function num2str($num){
		$nul='ноль';
		$ten=array(array('','один','два','три','четыре','пять','шесть','семь','восемь','девять'),array('','одна','две','три','четыре','пять','шесть','семь','восемь','девять'),);
		$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
		$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто');
		$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот');
		$unit=array(array('тиын','тиын','тиын',1),array('тенге','тенге','тенге',0),array('тысяча','тысячи','тысяч',1),array('миллион','миллиона','миллионов',0),array('миллиард','милиарда','миллиардов',0),);
		list($rub,$kop)=explode('.',sprintf("%015.2f",floatval($num)));
		$out=array();
		if(intval($rub)>0){
			foreach(str_split($rub,3) as $uk=>$v){
				if(!intval($v))
					continue;
				$uk=sizeof($unit)-$uk-1;
				$gender=$unit[$uk][3];
				list($i1,$i2,$i3)=array_map('intval',str_split($v,1));
				$out[]=$hundred[$i1];
				if($i2>1)
					$out[]=$tens[$i2].' '.$ten[$gender][$i3];
				else $out[]=$i2>0?$a20[$i3]:$ten[$gender][$i3];
				if($uk>1)
					$out[]=$this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
			}
		}
		else $out[]=$nul;
		$out[]=$this->morph(intval($rub),$unit[1][0],$unit[1][1],$unit[1][2]);
		$out[]=$kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]);
		return trim(preg_replace('/ {2,}/',' ',join(' ',$out)));
	}
	function morph($n,$f1,$f2,$f5){
		$n=abs(intval($n))%100;
		if($n>10&&$n<20)
			return $f5;
		$n=$n%10;
		if($n>1&&$n<5)
			return $f2;
		if($n==1)
			return $f1;
		return $f5;
	}
}
class HtmlTags{
	static function InputText($name,$value,$style="",$type="text",$id="",$class="",$maxlength=0,$readonly=false,$size=0,$title="",$Language="",$onClick="",$extra="",$tabindex="",$placeholder="",$required=false,$multiple=false){
		$name=(stripslashes($name));
		$value=(stripslashes($value));
		$value=htmlspecialchars_decode($value);
		$value=htmlspecialchars($value);
		$Language=(stripslashes($Language));
		$style=(empty($style))?"":" style=\"{$style}\"";
		$id=empty($id)?$name:$id;
		$label="";
		$class=(empty($class))?"":" class=\"{$class}\"";
		$id=(empty($id))?"":" id=\"{$id}\"";
		$size=(empty($size))?"":" size=\"{$size}\"";
		if(!empty($maxlength)&&empty($size)){
			if($maxlength<24)
				$size=" size=\"".($maxlength+2)."\"";
		}
		$maxlength=(empty($maxlength))?"":" maxlength=\"{$maxlength}\"";
		$readonly=($readonly)?" readonly":"";
		$title=(empty($title))?"":" title=\"{$title}\"";
		$Language=(empty($Language))?"":" lang=\"{$Language}\"";
		$tabindex=(empty($tabindex))?"":" tabindex=\"{$tabindex}\"";
		$onClick=empty($onClick)?"":" onclick=\"{$onClick}\"";
		$placeholder=empty($placeholder)?"":" placeholder=\"{$placeholder}\"";
		$multiple=($multiple)?" multiple=\"multiple\"":"";
		$required=$required?" required":"";
		return "<input type=\"{$type}\" name=\"{$name}\" value=\"{$value}\"{$style}{$id}{$class}{$maxlength}{$readonly}{$size}{$title}{$Language}{$onClick}{$extra}{$tabindex}{$placeholder}{$required}{$multiple} />{$label}";
	}
	static function InputCheckbox($name,$value,$title,$status=false,$label_style="",$id="",$onclick="",$ShowLabel=true,$class="",$LabelAfter=false,$Disabled=false,$required=false){
		$name=addslashes(stripslashes($name));
		$value=addslashes(stripslashes($value));
		$id=empty($id)?$name:$id;
		$status=($status)?" checked":"";
		$label_style=(empty($label_style))?"":" style=\"{$label_style}\"";
		$onclick=(empty($onclick))?"":" onclick=\"{$onclick}\"";
		if($class=="checkbox-slider"&&!empty($title)){
			$title="<span>{$title}</span>";
		}
		$class=!empty($class)?" class=\"{$class}\"":"";
		$Disabled=$Disabled?" disabled":"";
		$required=$required?" required":"";
		$title=empty($title)?"":" {$title}";
		if($LabelAfter)
			return "<input type=\"checkbox\" name=\"{$name}\" value=\"{$value}\" id=\"{$id}\"{$status}{$class}{$onclick}{$Disabled}{$required}>".($ShowLabel?"<label for=\"{$id}\"{$label_style}>{$title}":"").($ShowLabel?"</label>":"");
		else
			return ($ShowLabel?"<label for=\"{$id}\"{$label_style}>":"")."<input type=\"checkbox\" name=\"{$name}\" value=\"{$value}\" id=\"{$id}\"{$status}{$class}{$onclick}{$Disabled}{$required}>{$title}".($ShowLabel?"</label>":"");
	}
	static function InputRadio($name,$value,$title,$status=false,$on_change="",$id="",$class="",$LabelAfter=false,$LabelTitle="",$data=array()){
		$name=addslashes(stripslashes($name));
		$value=addslashes(stripslashes($value));
		$id=empty($id)?$name:$id;
		$status=($status)?" checked":"";
		$on_change=empty($on_change)?"":" onChange='{$on_change}'";
		if(strpos($class,"checkbox-slider")>-1&&!empty($title)){
			$title="<span>{$title}</span>";
		}
		$class=!empty($class)?" class=\"{$class}\"":"";
		$LabelTitle=!empty($LabelTitle)?" title=\"{$LabelTitle}\"":"";
		foreach($data as $k=>$v){
			$data[$k]="data-{$k}=\"{$v}\"";
		}
		$data=join(" ",$data);
		$data=empty($data)?"":" {$data}";
		if($LabelAfter)
			return "<input type=\"radio\" name=\"{$name}\" value=\"{$value}\" id=\"{$id}\"{$status}{$on_change}{$class}{$data} /> <label for=\"{$id}\"{$LabelTitle}>{$title}</label>";
		else
			return "<label for=\"{$id}\"{$LabelTitle}><input type=\"radio\" name=\"{$name}\" value=\"{$value}\" id=\"{$id}\"{$status}{$on_change}{$class}{$data} /> {$title}</label>";
	}
	static function InputSubmit($value,$style="",$id="",$onclick="",$status=true,$type="submit",$class="",$tabindex=""){
		$value=addslashes(stripslashes($value));
		$style=(empty($style))?"":" style=\"{$style}\"";
		$class=(empty($class))?"":" class=\"{$class}\"";
		$id=(empty($id))?"":" id=\"{$id}\"";
		$status=($status)?"":" disabled";
		$onclick=(empty($onclick))?"":" onClick=\"{$onclick}\"";
		$tabindex=(empty($tabindex))?"":" tabindex=\"{$tabindex}\"";
		return "<input type=\"{$type}\" value=\"{$value}\"{$style}{$id}{$status}{$onclick}{$class}{$tabindex} />";
	}
	function InputReset($value){
		return $this->InputSubmit($value,"","","",true,"reset");
	}
	static function option($value,$title,$selected=false,$class="",$data=array()){
		$value=htmlspecialchars(addslashes(stripslashes($value)));
		$title=addslashes(stripslashes($title));
		$selected=($selected)?" selected":"";
		$class=(empty($class))?"":" class=\"{$class}\"";
		foreach($data as $k=>$v){
			$data[$k]="data-{$k}=\"{$v}\"";
		}
		$data=join(" ",$data);
		$data=empty($data)?"":" {$data}";
		return "<option value=\"{$value}\"{$class}{$selected}{$data}>{$title}</option>";
	}
	static function textarea($name,$value,$cols,$rows,$style="",$Language="",$id="",$class="",$onkeyup="",$placeholder="",$required=false,$readonly=false){
		$name=addslashes(stripslashes($name));
		$value=stripslashes($value);
		$Language=stripslashes($Language);
		$cols=(int)$cols;
		$rows=(int)$rows;
		$style=(empty($style))?"":" style=\"{$style}\"";
		$Language=(empty($Language))?"":" lang=\"{$Language}\"";
		$class=(empty($class))?"":" class=\"{$class}\"";
		$placeholder=empty($placeholder)?"":" placeholder=\"{$placeholder}\"";
		$id=(empty($id))?"":" id=\"{$id}\"";
		$onkeyup=(empty($id))?"":" onkeyup=\"{$onkeyup}\"";
		$required=$required?" required":"";
		$readonly=($readonly)?" readonly":"";
		return "<textarea cols=\"{$cols}\" rows=\"{$rows}\" name=\"{$name}\"{$style}{$Language}{$id}{$onkeyup}{$class}{$placeholder}{$required}{$readonly}>{$value}</textarea>";
	}
}