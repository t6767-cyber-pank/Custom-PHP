<?php
	if(!$IsAuth){
		header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}login/");
		exit;
	}


	$FirstPage=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemPages} WHERE `Owner`={$Page->Id} AND `MenuVisible`='Y' ORDER BY `Position`;",false);
	header("Location: {$ActiveLanguage->LinksPrefix}{$FirstPage->FullAddress}");
	exit;