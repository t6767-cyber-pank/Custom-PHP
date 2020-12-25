<?php
	include("mysql.inc");

	class DIRS{
		var $Imgs="/imgs/";
		var $Images="/images/";
		var $modules="/modules/";
		var $Pages="/i/Pages/";
		var $Languages="/i/Languages/";

		var $Slider="/i/Slider/";
		var $News="/i/News/";

		var $Photos="/i/Photos/";
		var $Brands="/i/Brands/";
		var $Reviews="/i/Reviews/";
		var $Shares="/i/Shares/";
	}

	class global_vars{
		var $database="";

		function global_vars($data_base){
			$this->database=$data_base;

			foreach($this as $field){
				if($field!==$data_base){
					$field=str_replace('`','',$field);
					$this->$field="$data_base.`$field`";
				}
			}

			$this->UserInfo=new stdClass();
		}

		var $SystemUsers="`SystemUsers`";
		var $SystemLanguages='`SystemLanguages`';
		var $SystemModulesCategories='`SystemModulesCategories`';
		var $SystemModules='`SystemModules`';
		var $SystemPages="`SystemPages`";

		var $Countries="`Countries`";
		var $Regions="`Regions`";
		var $Cities="`Cities`";
		var $Districts="`Districts`";

		var $LangPack="`LangPack`";
		var $TextBlocks="`TextBlocks`";

		var $Photos="`Photos`";

		var $PhoneCodes="`PhoneCodes`";
		var $EmailMessages="`EmailMessages`";

		var $Views="`Views`";

		var $Brands="`Brands`";
		var $Categories="`Categories`";
		var $Slider="`Slider`";
		var $Products="`Products`";
		var $ProductToCategory="`ProductToCategory`";
		var $Reviews="`Reviews`";
		var $Shares="`Shares`";
		var $SimTypes="`SimTypes`";
		var $SimCategories="`SimCategories`";
		var $SimProducts="`SimProducts`";
		var $ProductOptions="`ProductOptions`";
		var $ProductOffers="`ProductOffers`";
		var $ProductToOffers="`ProductToOffers`";
		var $ProductRequests="`ProductRequests`";
		var $ContactRequests="`ContactRequests`";
		var $PromoCodes="`PromoCodes`";
		var $SimPackages="`SimPackages`";
		var $Baskets="`Baskets`";
		var $BasketItems="`BasketItems`";
		var $Subscribes="`Subscribes`";
		var $Test="`TEST`";
	}

	/* class MailSMTP {
		var $Host="smtp3r.cp.idhost.kz";
		var $UserName="iecmarketing@iec-telecom.kz";
		var $Password="A1s2d345";
		var $Port="465";
		var $SMTPSecure="ssl";
	} */

	class MailSMTP
	{
		var $Host="mail.4dclick.kz";
		var $UserName="satavenue@4dclick.kz";
		var $Password="2lRSCAiD";
	}

	$sizes=array(
		"4"=>array("w"=>175,"h"=>130,"q"=>100,"PROP"=>true),
		"adminlicense"=>array("w"=>175,"h"=>130,"q"=>100,"PROP"=>false,"KEEPSIZE"=>true),
		"adminphoto"=>array("w"=>175,"h"=>130,"q"=>100,"PROP"=>false,"KEEPSIZE"=>true),
		"adminst"=>array("w"=>20,"h"=>20,"q"=>100,"PROP"=>false),
		"admphoto"=>array("w"=>200,"h"=>300,"q"=>100,"PROP"=>false),
		"admphotosmall"=>array("w"=>50,"h"=>50,"q"=>100,"PROP"=>true,"MASK"=>"/images/teammask.png"),
		"admindoctor"=>array("w"=>175,"h"=>200,"q"=>100,"PROP"=>true),
		"r"=>array("w"=>56,"h"=>56,"q"=>100,"PROP"=>true,"MASK"=>"/images/rating.png"),
		"pi"=>array("w"=>229,"h"=>190,"q"=>100,"PROP"=>true),

		"mpp"=>array("w"=>238,"h"=>150,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		"mrp"=>array("w"=>278,"h"=>175,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		//"bl"=>array("w"=>308,"h"=>120,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		"bl"=>array("w"=>300,"h"=>120,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		"rl"=>array("w"=>450,"h"=>380,"q"=>80,"PROP"=>true),
		"sl"=>array("w"=>800,"h"=>300,"q"=>80,"PROP"=>true),
		"slm"=>array("w"=>590,"h"=>400,"q"=>80,"PROP"=>true),
		"pll"=>array("w"=>285,"h"=>181,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		"psl"=>array("w"=>80,"h"=>50,"q"=>80,"PROP"=>true),
		"bp"=>array("w"=>390,"h"=>390,"q"=>80,"PROP"=>false),
		"pl"=>array("w"=>117,"h"=>90,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
		"bil"=>array("w"=>104,"h"=>66,"q"=>80,"PROP"=>false,"KEEPSIZE"=>true),
	);

	$GLOBAL=new global_vars($MySQL->DB);
	$DIRS=new DIRS();
	$MailSMTP=new MailSMTP();

	if(isset($REQUEST_URI)){
		$ThumbGenerate=false;
		foreach($DIRS as $ImageDir){
			if(substr($REQUEST_URI,0,strlen($ImageDir))==$ImageDir){
				$ThumbGenerate=true;
			}

			unset($ImageDir);
		}

		if($ThumbGenerate){
			$Kernel=new Kernel();
			$Kernel->GenerateThumbnails($REQUEST_URI);
		}
	}

	$DataBase=new DataBase($MySQL);