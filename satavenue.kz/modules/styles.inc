<?php
	header('Content-Type: text/css');

	$AllSlides=$DataBase->Query("SELECT * FROM {$GLOBAL->Slider} WHERE `Status`='Y' ORDER BY `Position`;");
	foreach($AllSlides as $Slide){
?>
		section.slider div.slide.slide<?=$Slide->Id?> {
			background-image:url("<?=$DIRS->Slider.$Slide->Id?>_<?=$ActiveLanguage->Prefix?>.jpg?v2");
		}
<?php
		unset($Slide);
	}
exit;