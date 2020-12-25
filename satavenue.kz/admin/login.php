<?php
	include("include/kernel.inc");
	include("../include/configs.inc");

	$Kernel=new Kernel();
	$Kernel->IsAdmin();

	$AdminTitle="Пожалуйста, авторизуйтесь";

	if(isset($_GET["LogOut"])){
		setcookie("chuid",0,time()-3600,"/");
		setcookie("chukey",0,time()-3600,"/");
		$_COOKIE['chuid']="";
		$_COOKIE['chukey']="";

		header("Location: /admin/");
		exit();
	}

	if(isset($_GET["auth"])){
		if($Kernel->IsAdmin()){
			header("Location: /admin/index.php");
			exit();
		}
	}

	include("include/header.inc");
?>
<div class="signInForm">
	<form action="?auth" method="POST">
		<img src="i/Logo.png" />
		<?=$HtmlTags->InputText("AuthUser",@$_GET["AuthUser"],"","text","AuthUser","text",0,false,0,"","","","","","Логин")?>
		<?=$HtmlTags->InputText("AuthPassword","","","password","AuthPassword","text",0,false,0,"","","","","","Введите пароль")?>
		<?=$HtmlTags->InputSubmit("Войти","","sub")?>
	</form>
</div>
<?php
	include("include/footer.inc");
?>