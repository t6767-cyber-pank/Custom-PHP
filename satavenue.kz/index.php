<?php
	if (strstr($_SERVER['REQUEST_URI'], '/en/')) header('Location: '.str_replace('/en/', '/', $_SERVER['REQUEST_URI']));

	$mtime=microtime();
	$mtime=explode(" ",$mtime);
	$mtime=$mtime[1]+$mtime[0];
	$tstart=$mtime;

	foreach($_GET as $Key=>$Value){
		$Key=str_replace("_",".",$Key);
		$_GET[$Key]=$Value;
	}

	if(isset($_GET["chpukcomp"])){
		$File=$_GET["chpukcomp"];
		$File=explode(".",$File);
		$Extension=$File[count($File)-1];
		unset($File[count($File)-1]);
		$File=join(".",$File);
		$File=array($File,$Extension);

		if($File[1]=="css"){
			header('Content-Type: text/css');
			header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T',time()+365*24*60*60));

			$FileName="./style/{$File[0]}.{$File[1]}";
		}elseif($File[1]=="js"){
			header('Content-Type: application/javascript');
			header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T',time()+365*24*60*60));

			$FileName="./jscripts/{$File[0]}.{$File[1]}";
		}

		$FileContent=file_get_contents($FileName);
		if($File[1]=="css" || ($File[1]=="js" && $File[0]=="core")){
			$FileContent=str_replace(array("	","\r\n","\n\r","\r","\n","  "),"",$FileContent);
			$FileContent=str_replace(array("{ "," {"," }","} ",": "," :"),array("{","{","}","}",":",":"),$FileContent);
		}

		echo $FileContent;
		exit;
	}

	$REQUEST_URI=(string) @$_GET['query'];
	if(substr($REQUEST_URI,0,1)!=="/")
		$REQUEST_URI="/{$REQUEST_URI}";
	$OWN_REQUEST=$REQUEST_URI;

	include("admin/include/kernel.inc");
	include("include/configs.inc");

	$REQUEST_URI=$OWN_REQUEST;

	$Kernel=new Kernel();

	class Language{
		var $Id=0;
		var $Name="Russia";
		var $Prefix="ru";
		var $Color="Green";
		var $Status="Y";
		var $Position=0;
		var $LinksPrefix="/";
		var $RegionLinksPrefix="/";
	}

	$ActiveLanguage=new Language();

	if(strlen($REQUEST_URI)>1 and $REQUEST_URI[1]=="?"){
		$REQUEST_URI="/";
	}

	$REQUEST_URI=explode("?",$REQUEST_URI);
	$REQUEST_URI=$REQUEST_URI[0];

	if($REQUEST_URI!=="/"){
		$ActiveLanguage=explode("/",$REQUEST_URI);
		unset($ActiveLanguage[0]);
		unset($ActiveLanguage[count($ActiveLanguage)]);
		$ActiveLanguage=array_values($ActiveLanguage);

		if($ActiveLanguage=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} WHERE `Prefix`=:prefix ORDER BY `Position` LIMIT 0,1;",false,array(":prefix"=>@$ActiveLanguage[0]))){
			$REQUEST_URI=substr($REQUEST_URI,strlen($ActiveLanguage->Prefix)+1,strlen($REQUEST_URI));
			$OWN_REQUEST=substr($OWN_REQUEST,strlen($ActiveLanguage->Prefix)+1,strlen($OWN_REQUEST));

			if($ActiveLanguage->Root=="N")
				$RootLanguage=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} ORDER BY Position ASC LIMIT 0,1;",false);
			else
				$RootLanguage=$ActiveLanguage;
		}else{
			$ActiveLanguage=$RootLanguage=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} ORDER BY Position LIMIT 0,1;",false);
		}
	}else{
		$ActiveLanguage=$RootLanguage=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} ORDER BY Position LIMIT 0,1;",false);
	}

	$ActiveLanguage->UrlPrefix=$RootLanguage->Id==$ActiveLanguage->Id?"":"{$ActiveLanguage->Prefix}/";
	$ActiveLanguage->LinksPrefix=$RootLanguage->Id==$ActiveLanguage->Id?"":"/{$ActiveLanguage->Prefix}";

	$Title="Title_{$ActiveLanguage->Prefix}";
	$MenuTitle="MenuTitle_{$ActiveLanguage->Prefix}";

	$ActiveLanguage->RegionLinksPrefix=$ActiveLanguage->LinksPrefix;
	$ActiveLanguage->LinksPrefix=$ActiveLanguage->LinksPrefix;

	$OWN_REQUEST=str_replace($ActiveLanguage->LinksPrefix,"",$OWN_REQUEST);

	$Title="Title_{$ActiveLanguage->Prefix}";
	$MenuTitle="MenuTitle_{$ActiveLanguage->Prefix}";

	if(!isset($REQUEST_URI) || empty($REQUEST_URI) || $REQUEST_URI=='/'){
		$PageId=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemPages} WHERE `Owner`=0 ORDER BY `Position` LIMIT 0,1;",false);
		$REQUEST_URI=$PageId->FullAddress;
		$PageId=@$PageId->Id;
	}else{
		$Position=strrpos($REQUEST_URI,"?");
		if((int) $Position==0){
			if(substr($REQUEST_URI,strlen($REQUEST_URI)-1,1)!=="/"){
				$REQUEST_URI=$REQUEST_URI."/";
			}
		}
		$SubStr=substr($REQUEST_URI,$Position-1,1);
		if($SubStr!=="/"){
			$REQUEST_URI=substr($REQUEST_URI,0,$Position)."/".substr($REQUEST_URI,$Position,strlen($REQUEST_URI)-$Position);
		}
		$Pages=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemPages} ORDER BY LENGTH(FullAddress) DESC;");
		$IsPage=false;
		foreach($Pages as $Page){
			if(substr($REQUEST_URI,0,strlen($Page->FullAddress))==$Page->FullAddress){
				$PageLength=strlen($Page->FullAddress);
				$PageId=$Page->Id;
				$SUB_REQUEST_URI=substr($REQUEST_URI,$PageLength-1,strlen($REQUEST_URI)-$PageLength+1);
				$REQUEST_URI=substr($REQUEST_URI,0,$PageLength);
				if(substr($SUB_REQUEST_URI,0,7)=='/http:/' || substr($SUB_REQUEST_URI,0,8)=='/https:/'){
					header("Location: ".$REQUEST_URI);
					return;
				}
				$IsPage=true;
			}
			if($IsPage)
				break;

			unset($Page);
		}
		unset($Pages);

		if(!$IsPage){
			$Kernel->e404();
		}
	}

	if(empty($PageId)){
		$Kernel->e404();
		return;
	}

	$PageOptions=$Page=$Kernel->ExtractPageOptions($PageId,$ActiveLanguage);

	if($Page->Status=='N'){
		$Kernel->e404();
		return;
	}

	$PageModule=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModules} WHERE Id=:id;",false,array(":id"=>$Page->Module));
	$PageModule=$PageModule->File;
	$FileInclude=".".$DIRS->modules.$PageModule;
	if(!is_file($FileInclude)){
		$Kernel->e404();
		return;
	}

	$REQUEST_URI_ARRAY=explode("/",$REQUEST_URI);
	unset($REQUEST_URI_ARRAY[0]);
	unset($REQUEST_URI_ARRAY[count($REQUEST_URI_ARRAY)]);
	sort($REQUEST_URI_ARRAY);
	$SUB_REQUEST_URI=(!isset($SUB_REQUEST_URI) || $SUB_REQUEST_URI=="/")?"":$SUB_REQUEST_URI;
	$OWN_REQUEST=explode("?",$OWN_REQUEST);
	$OWN_REQUEST=$OWN_REQUEST[0];

	$Page->FullIds=explode("/",$Page->FullIds);

	$HtmlTags=new HtmlTags();
	include("include/setting.inc");

	ob_start();

	include($FileInclude);
	$Page_=ob_get_contents();
	ob_clean();
	$Page_=str_replace("﻿","",$Page_);
	$Page_=str_replace("../","/",$Page_);

	$Page_=stripslashes($Page_);
	$Page_=str_replace("	","",$Page_);

	echo $Page_;

	$mtime=microtime();
	$mtime=explode(" ",$mtime);
	$mtime=$mtime[1]+$mtime[0];
	$tend=$mtime;
	$totaltime=($tend-$tstart);
	echo "<!--{$totaltime} $PageId-->";