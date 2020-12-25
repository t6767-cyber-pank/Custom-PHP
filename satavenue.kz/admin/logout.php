<?php
	setcookie("chuid","",time()-3600,"/");
	setcookie("chukey","",time()-3600,"/");
	unset($_SERVER['PHP_AUTH_USER']);
	unset($_SERVER['PHP_AUTH_PW']);

	header("Location: /admin/?LogOut");
	return;