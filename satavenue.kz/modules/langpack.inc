<?php
	$TopMenuSubItems=array();
	if(isset($SubMenuNeeded))
		return;
	header("Content-type: application/x-javascript");
	$LangPackID=array();
	foreach($LangPack as $Key=>$Value){
		$LangPackID[]="'{$Key}':'".addslashes($Value)."'";
/*?>langpack.<?=$Key?>="<?=addslashes($Value)?>";<?php*/
	}
	$LangPackID=join(",",$LangPackID);
	echo "var langpack={{$LangPackID}};";

	exit();
?>