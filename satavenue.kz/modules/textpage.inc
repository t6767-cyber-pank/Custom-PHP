<?php
	$ShowTitle = false;
	$BreadCrumbs[1]="<a href=\"#\">сопровождение</a>";
	include ".{$DIRS->modules}so/h.inc";
?>
	<?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/i/Pages/' . $Page->Id . '.jpg')): ?>
        <div class="ims"><img src="<?=$DIRS->Pages.$Page->Id?>.jpg" alt="" class="full-screen-img"></div>
	<?php endif ?>
	<section class="textPage body">
		<?=$Page->Description?>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>	