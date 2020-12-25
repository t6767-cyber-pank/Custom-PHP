<?php
	include "config.inc";
	include("../class.phpmailer.php");

	class LangPack{
		function LangPack(){
			global $GLOBAL,$DataBase;
			$AllLabels=$DataBase->Query("SELECT *,Title_ru AS Title FROM {$GLOBAL->LangPack};");
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
	$AllMessages=$DataBase->Query("SELECT * FROM {$GLOBAL->EmailMessages} WHERE `Status`='N' ORDER BY `DateTime`;");
	foreach($AllMessages as $Message){
		if(!empty($Message->EMail))
			$Kernel->SendEMailMessage($Message->EMail,$Message->Title,$Message->Message);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->EmailMessages} SET `Status`='Y' WHERE Id={$Message->Id};");

		unset($Message);
	}