<?php
	include("include/kernel.inc");
	include("../include/configs.inc");

	$Kernel=new Kernel();
	$Kernel->IsAdmin();

	if($GLOBAL->UserInfo->RightsAdmin=="Y")
		header("Location: /admin/products/");
	elseif($GLOBAL->UserInfo->RightsPages=="Y")
		header("Location: /admin/pages/");
	elseif($GLOBAL->UserInfo->RightsModules=="Y")
		header("Location: /admin/modules.php");
	elseif($GLOBAL->UserInfo->RightsUsers=="Y")
		header("Location: /admin/users/");
	elseif($GLOBAL->UserInfo->RightsCategories=="Y")
		header("Location: /admin/site/categories/");
	elseif($GLOBAL->UserInfo->RightsBrands=="Y")
		header("Location: /admin/site/brands/");
	elseif($GLOBAL->UserInfo->RightsProducts=="Y")
		header("Location: /admin/products/");
	elseif($GLOBAL->UserInfo->RightsShares=="Y")
		header("Location: /admin/site/shares/");
	elseif($GLOBAL->UserInfo->RightsSimTypes=="Y")
		header("Location: /admin/site/sim-types/");
	elseif($GLOBAL->UserInfo->RightsBaskets=="Y")
		header("Location: /admin/baskets/");

	exit;