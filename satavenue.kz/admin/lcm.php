<?php
	$Query=@$_GET["query"];

	$Query=trim($Query,"/");
	$Query=explode("/",$Query);

	if(count($Query)==1){
		$Query[count($Query)]=$Query[0];
	}

	$Query[count($Query)-1]=$Query[count($Query)-1].".inc";
	$Query=join("/",$Query);

	$FileName="./classes/{$Query}";

	class Design{
		function LoadHeader($AdminTitle){
			global $Kernel,$GLOBAL,$ShowLanguages,$AllLanguages,$DataBase,$IsCallCenter;

			include("./include/header.inc");
		}
		function LoadFooter(){
			global $Kernel,$GLOBAL,$ShowLanguages,$AllLanguages,$SortLanguage;

			include("./include/footer.inc");
		}
		function LoadEditor(){
			include("./editor/java.inc.php");
		}
	}

	if(is_file($FileName)){
		include("include/kernel.inc");
		include("../include/configs.inc");

		$Kernel=new Kernel();
		if(!$Kernel->IsAdmin()){
			header("Location: /admin/login.php");
			exit;
		}

		include("../include/setting.inc");

		$CanDelete=false;

		if(isset($_GET["DelAccess"])){
			$CanDelete=true;
		}

		$AllLanguages=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemLanguages} ORDER BY `Position`;");
		$SortLanguage=current($AllLanguages);

		$ShowList=true;
		$ShowForm=false;
		$IsEdit=false;

		$Design=new Design();
		$HtmlTags=new HtmlTags();

		if(isset($_GET["Positions"]) || isset($_POST["Positions"]))
			$Positions=isset($_GET["Positions"])?$_GET["Positions"]:$_POST["Positions"];

		$IsCallCenter=false;

		include($FileName);
	}