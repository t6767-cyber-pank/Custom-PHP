<?php
	$ShowLanguages=isset($ShowLanguages)?$ShowLanguages:false;

	if(!isset($HtmlTags)){
		$HtmlTags=new HtmlTags();
	}

	$MenuItems=array(
		array(array("modules.php","mod.php"),"Настройки")
	);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="icon" type="image/png" href="/admin/i/favicon.png">
<title><?=strip_tags($AdminTitle)?></title>
<link href="/admin/main.css?v2.23" rel="stylesheet" type="text/css" />
<link href="/admin/jquery/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="/admin/jquery/fancybox.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="/admin/jquery/jquery.js"></script>
<script type="text/javascript" src="/admin/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="/admin/jquery/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="/admin/jquery/jquery.tablednd.js"></script>
<script type="text/javascript" src="/admin/jquery/fancybox.js"></script>
<script type="text/javascript" src="/admin/core.js?v2.15"></script>
<script type="text/javascript" src="/jscripts/core.js?v2.3"></script>
<script type="text/javascript" src="/langpack/"></script>
</head>
<body<?=@$IsCallCenter?" class=\"callCenter\"":""?>>
<?php
	if($Kernel->IsAdminNu()){
?>
<div class="headerBar">
	<div class="logo">
		<a href="/"><img src="/admin/i/Logo.png" alt="ChPuK CMS" title="ChPuK CMS" /></a>
	</div>
	<div class="menu">
		<a href="#" class="leftArrow"></a>
		<div>
			<ul>
<?php
		if($GLOBAL->UserInfo->RightsAdmin=="Y" || $GLOBAL->UserInfo->RightsUsers=="Y"){
?>
				<li class="users">
					<div>
						<div class="row">
							<ul>
								<li><span>Пользователи</span></li>
								<li><a href="/admin/users/">Список пользователей</a></li>
								<li><a href="/admin/users/?AddForm">Добавить пользователя</a></li>
								<li><a href="/admin/users/subscribers/">Скачать список подписчиков</a></li>
							</ul>
						</div>
					</div>
					<a href="#">Пользователи</a>
				</li>
<?php
		}
		if($GLOBAL->UserInfo->RightsPages=="Y" || $GLOBAL->UserInfo->RightsBrands=="" || $GLOBAL->UserInfo->RightsProducts=="Y" || $GLOBAL->UserInfo->RightsShares=="Y"){
?>
				<li class="site">
					<div>
						<div class="row">
<?php
			if($GLOBAL->UserInfo->RightsPages=="Y"){
?>
							<ul>
<?php
				if($GLOBAL->UserInfo->RightsPages=="Y"){
?>
								<li><span>Страницы</span></li>
								<li><a href="/admin/pages/">Список страниц</a></li>
								<li><a href="/admin/pages/?AddForm">Добавить страницу</a></li>
<?php
				}
?>
							</ul>
<?php
			}
			if($GLOBAL->UserInfo->RightsBrands=="Y" || $GLOBAL->UserInfo->RightsCategories=="Y" || $GLOBAL->UserInfo->RightsProducts=="Y" || $GLOBAL->UserInfo->RightsShares=="Y" || $GLOBAL->UserInfo->RightsSimTypes=="Y"){
?>
							<ul>
								<li><span>Бренды/Категории/Слайдер</span></li>
<?php
				if($GLOBAL->UserInfo->RightsBrands=="Y"){
?>
								<li><a href="/admin/site/brands/">Бренды</a></li>
<?php
				}
				if($GLOBAL->UserInfo->RightsCategories=="Y"){
?>
								<li><a href="/admin/site/categories/">Категории</a></li>
<?php
				}
				if($GLOBAL->UserInfo->RightsProducts=="Y"){
?>
								<li><a href="/admin/products/">Продукция</a></li>
								<li><a href="/admin/products/offers/">Предложения</a></li>
								<li><a href="/admin/products/promo-codes/">Промо коды</a></li>
								<li><a href="/admin/products/sim-packages/">Пакеты для пополнения</a></li>
<?php
				}
				if($GLOBAL->UserInfo->RightsShares=="Y"){
?>
								<li><a href="/admin/site/shares/">Акции</a></li>
<?php
				}
?>
								<li><a href="/admin/site/slider/">Слайдер</a></li>
								<li><a href="/admin/site/reviews/">Блог</a></li>
<?php
				if($GLOBAL->UserInfo->RightsSimTypes=="Y"){
?>
								<li><span>SIM</span></li>
								<li><a href="/admin/site/sim-types/">Типы sim услуг</a></li>
								<li><a href="/admin/site/sim-categories/">Категории sim услуг</a></li>
								<li><a href="/admin/site/sim-products/">Продукция</a></li>
<?php
				}
?>
							</ul>
<?php
			}
?>
						</div>
					</div>
					<a href="#">Сайт</a>
				</li>
<?php
		}
		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
?>
				<li class="modules">
					<div>
						<div class="row">
							<ul>
								<li><span>Языковые настройки</span></li>
								<li><a href="/admin/system/languages/">Список языков</a></li>
								<li><a href="/admin/system/langpack/">Языковые настройки</a></li>
								<li><a href="/admin/system/textblocks/">Текстовые блоки</a></li>
								<li><span>Региональные настройки</span></li>
								<li><a href="/admin/system/countries/">Страны</a></li>
								<li><a href="/admin/system/regions/">Регионы</a></li>
								<li><a href="/admin/system/cities/">Города</a></li>
								<li><a href="/admin/system/districts/">Районы</a></li>
							</ul>
						</div>
					</div>
					<a href="#">Система</a>
				</li>
<?php
		}
		if($GLOBAL->UserInfo->RightsBaskets=="Y"){
?>
				<li class="orders">
					<div>
						<div class="row">
							<ul>
								<li><span>Заказы</span></li>
								<li><a href="/admin/baskets/">Список заказов</a></li>
							</ul>
						</div>
					</div>
					<a href="/admin/baskets/">Заказы</a>
				</li>
<?php
		}
?>
			</ul>
		</div>
		<a href="#" class="rightArrow"></a>
	</div>
	<div class="auth">
		<a href="/admin/users/?EditForm&Id=<?=$GLOBAL->UserInfo->Id?>" class="profile">Мой профиль</a>
		<a href="?logout" class="logout">Выход</a>
	</div>
</div>
<?php
	}
	if($ShowLanguages){
?>
	<div id="LanguagesSlider">
		<ul>
<?php
		foreach($AllLanguages as $Language){
?>
			<li id="<?=$Language->Prefix?>" title="<?=$Language->Name?>"><?=$Language->Name?></li>
<?php
		}
?>
		</ul>
	</div>
<?php
	}
	if($Kernel->IsAdminNu()){
?>
<table class="mainTable">
	<tr>
		<td>
<?php
	}