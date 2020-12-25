<?php
	$LastReviews=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Reviews} WHERE `Status`='Y' ORDER BY `DateTime` DESC LIMIT 0,12;");
	foreach($BreadCrumbs as $Position=>$Crumb){
		if($Position>0){
			preg_match("'<a href=\"(.*?)\">(.*?)</a>'si",$Crumb,$CI);
			$Crumb=<<<CRUMB
				<span itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">
					<a itemprop="item" title="{$CI[2]}" href="{$CI[1]}">
						<span itemprop="name">{$CI[2]}</span>
						<meta itemprop="position" content="{$Position}">
					</a>
				</span>
CRUMB;

			$BreadCrumbs[$Position]=$Crumb;
		}

		unset($Crumb);
	}

	$BusinessTypesGet=$_GET;
	unset($BusinessTypesGet["query"]);
?>
<!DOCTYPE html>
<html lang="<?=$ActiveLanguage->Prefix?>">
<head>
	<meta charset="UTF-8"/>

	<meta name="description" content="<?=$Page->MetaDescription?>" />
	<meta name="keywords" content="<?=$Page->MetaKeywords?>" />


	<meta name="og:title" content="<?=$OgTitle?>" />
	<meta name="og:description" content="<?=$Page->MetaDescription?>" />
	<meta name="og:image" content="<?=$OgImage?>" />
	<meta name="og:type" content="image/jpeg" />
	<meta property="og:site_name" content='satavenue.kz'>
	<meta property="og:url" content="<?=$OgURL?>" >

	<link rel="shortcut icon" href="/images/favicon.png"/>	
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="/css/main.css?v2.80g" rel="stylesheet"/>
	<link href="<?=$ActiveLanguage->LinksPrefix?>/styles.css?v2.12b" rel="stylesheet"/>
	<link href="/css/responsive.css?v3.40e" rel="stylesheet"/>
	<link rel="stylesheet" href="/jscripts/slick/slick.css">
	<link rel="stylesheet" href="/jscripts/slick/slick-theme.css">

	<link rel="canonical" href="http://<?=$DomainName.$OWN_REQUEST?>" />

	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="theme-color" content="#000000"/>
	<title><?=$Page->Title?></title>

	<link rel="search" type="application/opensearchdescription+xml" title="SatAvenue" href="/opensearch.xml?v2.1b">
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NV3SJGZ');</script>
<!-- End Google Tag Manager -->
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NV3SJGZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<!-- Yandex.Metrika counter --> 
	<!--<script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter44499730 = new Ya.Metrika({ id:44499730, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = false; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/44499730" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-98411612-1', 'auto');
		ga('send', 'pageview');
	</script>-->
	<header class="page-header">
		<div class="body">
			<!-- новый хэдэр для sm-->
			<div class="page-header__row enable-md">
				<div class="page-header__col page-header__col--1">
					<div class="page-header__burger burger js-burger">
						<div class="burger__icon">
						</div>
					</div>
				</div>
				<div class="page-header__col page-header__col--1">
					<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
						<div class="basket basketOpenWrapper" id="basketInHeader">
							<a href="<?=$ActiveLanguage->LinksPrefix?>/basket/" class="basket-open-link">
								<span class="amount"><?=$BasketInfo->TotalItems?></span>
								<span class="total"><?=str_replace("%PRICE%",$Kernel->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg)?></span>
							</a>
							<div class="basket-popover">Корзина пуста</div>
						</div>					
					<?php endif ?>
				</div>
				<div class="page-header__col page-header__col-- float-right vertical-middle">
					<div class="page-header__langs langs">
						<button class="langs-drop-btn" id="lanbutras">
							<?php
                            if ($_SERVER['REQUEST_URI']=="/") {echo "RU";} else {echo "EN";};
                            ?>
						</button>
						<div class="drop-list">
							<a href="/" class="langs__item langs__item--active">
								RU
							</a>
							<a href="/contacts_en/" class="langs__item">
								EN
							</a>
						</div>
					</div>
				</div>
				<div class="page-header__col page-header__col-- float-right special-margin vertical-middle">
					<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
						<a href="/funds/" class="page-header__up-balance-wrap">
							<div  class="page-header__up-balance">
							</div>
						</a>
					<?php endif ?>
				</div>			
				<div class="page-header__col page-header__col-- float-right vertical-middle">
					<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
						<?php if ($IsAuth): ?>
							<div class="login">
								<button class="login-drop-btn">
									<span class="user-circle"></span>
									<span>Личный кабинет</span>
								</button>
								<div class="drop-list">
									<a href="/sign/" class="page-header__link">
										Личный кабинет
									</a>
									<a href="/exit/" class="page-header__link">
										Выход
									</a>
								</div>
							</div>
						<?php else: ?>
							<div class="login">
								<button class="login-drop-btn">
									<span class="user-circle"></span>
									<span>Личный кабинет</span>
								</button>
								<div class="drop-list">
									<a href="/sign/" class="page-header__link">
										Войти
									</a>
									<a href="/exit/" class="page-header__link">
										Зарегистрироваться
									</a>
								</div>
							</div>
						<?php endif ?>
<!-- 						<a href="/sign/login/?auth" class="page-header__link">
							Войти
						</a>
						<a href="/sign/" class="page-header__link">
							Зарегистрироваться
						</a> -->
					<?php endif ?>
				</div>		
			</div>
			<div class="page-header__row enable-md ">
				<div class="page-header__col page-header__col--">
					<div class="page-header__feedback feedback">
						<a href="tel:<?= $TextBlocks->HeaderPhone->Description ?>" class="feedback__icon">
						</a>
						<a href="tel:<?= $TextBlocks->HeaderPhone->Description ?>" class="feedback__phone">
							<?= $TextBlocks->HeaderPhone->Description ?>
						</a>
					</div>
				</div>
				<div class="page-header__col page-header__col-- float-right">
					<form action="<?=$ActiveLanguage->LinksPrefix?>/search/" method="get" class="js-search page-header__search search">
						<input type="text" class="search__input" name="s" value="<?= isset($_GET['s']) ? $_GET['s'] : '' ?>">
						<button type="submit" class="search__button"></button>
						<div class="search__icon js-search__icon"></div>
					</form>
<!-- 					<a href="/funds/" class="page-header__up-balance-wrap">
						<div  class="page-header__up-balance">
						</div>
					</a> -->
				</div>
			</div>
			<div class="page-header__row disable-md">
				<div class="page-header__col page-header__col--4">
					<div class="page-header__feedback feedback">
						<a href="tel:<?= $TextBlocks->HeaderPhone->Description ?>" class="feedback__icon">
						</a>
						<a href="tel:<?= $TextBlocks->HeaderPhone->Description ?>" class="feedback__phone">
							<?= $TextBlocks->HeaderPhone->Description ?>
						</a>
						<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
							<a href="#" class="feedback__link callback">
								Заказать обратный звонок
							</a>
						<?php endif ?>
					</div>
				</div>
				<div class="page-header__col page-header__col--2">
					<div class="page-header__langs langs">
						<a href="/" class="langs__item langs__item--active">
							На русском
						</a>
						<a href="/contacts_en/" class="langs__item">
							In English
						</a>
					</div>
				</div>
				<div class="page-header__col page-header__col--4">
					<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
						<?php if ($IsAuth): ?>
							<a href="/sign/" class="page-header__link">
								Личный кабинет
							</a>
							<a href="/exit/" class="page-header__link">
								Выход
							</a>
						<?php else: ?>
							<a href="/sign/login/?auth" class="page-header__link">
								Войти
							</a>
							<a href="/sign/" class="page-header__link">
								Зарегистрироваться
							</a>
						<?php endif ?>
						<a href="/funds/" class="page-header__up-balance-wrap">
							<div  class="page-header__up-balance">
								Пополнить баланс
							</div>
						</a>
					<?php endif ?>
					<form action="<?=$ActiveLanguage->LinksPrefix?>/search/" method="get" class="js-search page-header__search search">
						<input type="text" class="search__input" name="s" value="<?= isset($_GET['s']) ? $_GET['s'] : '' ?>">
						<button type="submit" class="search__button"></button>
						<div class="search__icon js-search__icon"></div>
					</form>
				</div>
			</div>
		</div>
		<div class="callback">
			<div id="callBack">
				<div class="title"><span><?=$LangPack->CallBack?></span></div>
				<div class="form">
					<form action="?callBack" method="post" id="frecaptcha1">
						<label for="callName"><?=$LangPack->NameReq?></label>
						<?=$HtmlTags->InputText("name","","","text","callName","text",0,false,0,"","","","","","",true)?>
						<label for="callPhone"><?=$LangPack->PhoneReq?></label>
						<?=$HtmlTags->InputText("phone","","","tel","callPhone","text",0,false,0,"","","",'',"","",true)?>
						<div class="border-capcha">
							<div id="recaptcha1"></div>
						</div>

						<p id="recaptchaError"></p>
						<div class="submit">
							<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?>
						</div>
					</form>
				</div>
			</div>
		</div>
	</header>
	<header class="page-header page-header--white">
		<div class="body">
			<div class="page-header__row">
				<div class="page-header__col page-header__col--3 disable-md">
					<div class="page-header__burger burger js-burger">
						<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
							<div class="burger__icon">
							</div>
							<div class="burger__text">
								Меню
							</div>
						<?php endif ?>
					</div>
				</div>
				<div class="page-header__col page-header__col--12 enable-md logo-center">
					<div class="page-header__logo-wrap">
						<a href="/" class="page-header__logo">
							<img src="../images/header-logo.png" alt="Satavenue">
						</a>
						<div class="page-header__text">
							от
						</div>
						<a href="/" class="page-header__logo">
							<img src="../images/header-logo-2.png" alt="IEC telecom">
						</a>
					</div>
				</div>
				<div class="page-header__col page-header__col--6 disable-md">
					<div class="page-header__logo-wrap">
						<a href="/" class="page-header__logo">
							<img src="../images/header-logo.png" alt="Satavenue">
						</a>
						<div class="page-header__text">
							от
						</div>
						<a href="/" class="page-header__logo">
							<img src="../images/header-logo-2.png" alt="IEC telecom">
						</a>
					</div>
				</div>
				<div class="page-header__col page-header__col--3 disable-md">
					<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
						<div class="basket basketOpenWrapper" id="basketInHeader">
							<span class="YourBasket">Ваша корзина:</span>
							<a href="<?=$ActiveLanguage->LinksPrefix?>/basket/" class="basket-open-link">
								<span class="amount"><?=$BasketInfo->TotalItems?></span>
								<span class="total"><?=str_replace("%PRICE%",$Kernel->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg)?></span>
							</a>
							<div class="basket-popover">Корзина пуста</div>
						</div>					
					<?php endif ?>
				</div>
			</div>
		</div>
	</header>
	<nav class="page-nav js-page-nav">
		<div class="body">
			<div class="page-nav__row">
				<div class="page-nav__col page-nav__col--">
					<div class="page-nav__item">
						<a href="/" class="page-nav__link">
							Главная
						</a>
					</div>
					<?php /*<div class="page-nav__item">
						<a href="#" class="page-nav__link">
							Каталог брендов
						</a>
						<div class="page-nav__item page-nav__item--lvl-2">
							<?php foreach ($AllBrands as $Brand): ?>
								<a href="<?=$ActiveLanguage->LinksPrefix?>/products/?brand[]=<?=$Brand->Id?>" class="page-nav__link page-nav__link--lvl-2">
									<?= $Brand->Title ?>
								</a>
							<?php endforeach ?>
						</div>
					</div>*/ ?>
					<div class="page-nav__item">
						<a href="#" class="page-nav__link">
							Информация
						</a>
						<div class="page-nav__item page-nav__item--lvl-2">
							<?php $category = $AllCategories[14] ?>
							<a href="/products<?= $category->FullAddress ?>" class="page-nav__link page-nav__link--lvl-2">
								<?= $category->Title ?>
							</a>
<!-- 							<a href="/products/?offer%5B%5D=5&from=0&to=460000" class="page-nav__link page-nav__link--lvl-2">
								Распродажа
							</a> -->
							<?php $category = $AllCategories[15] ?>
							<a href="/products<?= $category->FullAddress ?>" class="page-nav__link page-nav__link--lvl-2">
								<?= $category->Title ?>
							</a>
							<?php $category = $AllCategories[16] ?>
							<a href="/products<?= $category->FullAddress ?>" class="page-nav__link page-nav__link--lvl-2">
								<?= $category->Title ?>
							</a>
						</div>
					</div>
				</div>
				<div class="page-nav__col page-nav__col--">
					<? $category = $AllCategories[11] ?>
					<div class="page-nav__item">
						<a href="/products<?= $category->FullAddress ?>" class="page-nav__link">
							<?= $category->Title ?>
						</a>
						<?php if (! empty($category->Items)): ?>						
							<div class="page-nav__item page-nav__item--lvl-2">
								<?php foreach ($category->Items as $Item): ?>
									<a href="/products<?= $Item->FullAddress  ?>" class="page-nav__link page-nav__link--lvl-2">
										<?= $Item->Title ?>
									</a>
								<?php endforeach ?>
							</div>
						<?php endif ?>
					</div>
					<? $category = $AllCategories[12] ?>
					<div class="page-nav__item">
						<a href="/products<?= $category->FullAddress ?>" class="page-nav__link">
							<?= $category->Title ?>
						</a>
						<?php if (! empty($category->Items)): ?>						
							<div class="page-nav__item page-nav__item--lvl-2">
								<?php foreach ($category->Items as $Item): ?>
									<a href="/products<?= $Item->FullAddress  ?>" class="page-nav__link page-nav__link--lvl-2">
										<?= $Item->Title ?>
									</a>
								<?php endforeach ?>
							</div>
						<?php endif ?>
					</div>
				</div>
				<div class="page-nav__col page-nav__col--">
					<? $category = $AllCategories[13] ?>
					<div class="page-nav__item">
						<a href="/products<?= $category->FullAddress ?>" class="page-nav__link">
							<?= $category->Title ?>
						</a>
						<?php if (! empty($category->Items)): ?>						
							<div class="page-nav__item page-nav__item--lvl-2">
								<?php foreach ($category->Items as $Item): ?>
									<a href="/products<?= $Item->FullAddress  ?>" class="page-nav__link page-nav__link--lvl-2">
										<?= $Item->Title ?>
									</a>
								<?php endforeach ?>
							</div>
						<?php endif ?>
					</div>
				</div>
				<div class="page-nav__col page-nav__col--">
					<div class="page-nav__item">
						<a href="#" class="page-nav__link">
							Сопровождение
						</a>
						<div class="page-nav__item page-nav__item--lvl-2">
							<a href="/dostavka/" class="page-nav__link page-nav__link--lvl-2">
								Доставка
							</a>
							<a href="/payment/" class="page-nav__link page-nav__link--lvl-2">
								Оплата
							</a>
							<a href="/rent/" class="page-nav__link page-nav__link--lvl-2">
								Аренда
							</a>
							<a href="/garanty/" style="width: 236px;" class="page-nav__link page-nav__link--lvl-2">
								Гарантийное обслуживание
							</a>
							<a href="/korporativnye-preimuschestva/" class="page-nav__link page-nav__link--lvl-2">
                                Корпоративные преимущества
                            </a>
						</div>
					</div>
					<div class="page-nav__item">	
						<a href="/contacts/" class="page-nav__link">
							Контакты
						</a>
					</div>
				</div>
			</div>
		</div>
	</nav>
<?php
if(!$IsMainPage && $ShowMainTitle){
	?>
	<nav class="breadcrumbs body"><?php if ($_SERVER['REQUEST_URI']=="/contacts_en/") {echo "";} else { ?> <?=join(" - ",$BreadCrumbs)?> <?php } ?></nav>
	<?php if(!@$ShowProduct && (!isset($ShowTitle) || $ShowTitle)){ ?>
		<h1 class="title"><?=$Page->Title?></h1>
	<?php } ?>
<?php
}
?>