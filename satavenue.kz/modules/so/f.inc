<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
	<section class="advantages body">
		<h2 class="title"><?=$LangPack->OurAdvantages?></h2>
		<div class="list">
		<?php
			foreach($TextBlocks as $TextBlock){
				if(substr($TextBlock->Name,0,13)=="OurAdvantages"){
		?>
					<div class="item"><?=$TextBlock->Description?></div>
		<?php
				}
			}
		?>
		</div>
	</section>
<?php else: ?>
	<section class="advantages body">
		<h2 class="title">Our advantages</h2>
		<div class="list">
		<?php
			foreach($TextBlocks as $TextBlock){
				if(substr($TextBlock->Name,0,13)=="OurAdvantages"){
		?>
					<div class="item"><?=$TextBlock->Description_en?></div>
		<?php
				}
			}
		?>
		</div>
	</section>
<?php endif ?>
	<footer class="footer">
		<div class="items body">
			<?php if (trim($_SERVER['REQUEST_URI'], '/') != 'contacts_en'): ?>
				<dl class="item categories">
					<dt><?=$LangPack->ProductCategories?></dt>
					<dd>
						<ul>
							<?php
								foreach($AllCategories as $Category){
							?>
													<li><a href="<?=$ActiveLanguage->LinksPrefix?>/products<?=$Category->FullAddress?>"><?=$Category->Title?></a></li>
							<?php
									unset($Category);
								}
							?>
						</ul>
					</dd>
				</dl>
				<dl class="item customers">
					<dt><?=$LangPack->ForCustomers?></dt>
					<dd>
						<ul>
							<?php
								foreach($MenuItems["BOTTOM"] as $Item){
							?>
								<li><a href="<?=$ActiveLanguage->LinksPrefix.$Item->FullAddress?>"><?=$Item->Title?></a></li>
							<?php
									unset($Item);
								}
							?>
						</ul>
					</dd>
				</dl>
				<dl class="item contacts">
					<dt><?=$TextBlocks->ContactUs->Title?></dt>
					<dd><?=$TextBlocks->ContactUs->Description?></dd>
					<dt class="social"><?=$LangPack->WeInSocial?></dt>
					<dd class="social">
						<?php
							foreach($SocialList as $Type=>$Social){
						?>
							<a href="<?=$Social[0]?>" target="_blank" title="<?=$Social[1]?>" class="<?=$Type?>"></a>
						<?php
								unset($Type);
								unset($Social);
							}
						?>
					</dd>
				</dl>
				<dl class="item subscribe" id="subscribe">
					<dt><?=$LangPack->Subscribe?></dt>
					<dd>
						<p><?=$LangPack->SubscribeAndShares?></p>
						<form action="?subscribe" method="post">
							<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text",0,false,0,"","","","","",$LangPack->YourEmailPlaceholder,true)?>
							<?=$HtmlTags->InputSubmit($LangPack->SubscribeButton,"","","",true,"submit","submit")?>
						</form>
					</dd>
					<dd class="copyrights">
						<?=str_replace("%YEAR%",date("Y"),$TextBlocks->FooterCopyrights->Description)?>
					</dd>
				</dl>
			<?php else: ?>
				<dl class="item contacts contacts-en">
					<dt class="social">FOLLOW US ON SOCIAL MEDIA</dt>
					<dd class="social">
						<?php
							foreach($SocialList as $Type=>$Social){
						?>
							<a href="<?=$Social[0]?>" target="_blank" title="<?=$Social[1]?>" class="<?=$Type?>"></a>
						<?php
								unset($Type);
								unset($Social);
							}
						?>
					</dd>
					<dt class="social">VISIT OUR CORPORATE WEBSITE</dt>
					<dd><a href="https://iec-telecom.kz" target="_blank">iec-telecom.kz</a></dd>
				</dl>
				<dl class="item subscribe subscribe-en">
					<dd>
						© ТОО «DARIN TEL»
						All right reserved. Prices, terms and
						conditions are valid for the current date.
					</dd>
				</dl>
			<?php endif ?>
		</div>
	</footer>
<?php
	if($IsMainPage){
?>
		<section class="mainPageInfo"><div class="body"><?=$TextBlocks->MainPageInfo->Description?></div></section>
<?php
	}
?>
	<div class="modal fade" id="after-buy-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<a class="btn-buy" href="/basket"><?= $LangPack->GoToCart ?></a>
					<a href="#" data-dismiss="modal" class="btn-continue"><?= $LangPack->ContinueShopping ?></a>
				</div>
			</div>
		</div>
	</div>
	<script src="/js/jquery.js?v1.26"></script>
	<script src="/jscripts/jquery-ui.js"></script>
	<script src="/jscripts/jquery.ui.touch-punch.min.js"></script>
	<script src="/js/core.js?v31"></script>
	<script src="/js/inputmask.js?v23"></script>
	<script src="/js/bs-modal.js?v23"></script>
	<script type="text/javascript" src="/jscripts/pro-carousel-2.4.js?v0.10a"></script>
	<script src="<?=$ActiveLanguage->LinksPrefix?>/langpack/?v4.40"></script>
	<script type="text/javascript" src="/jscripts/slick/slick.min.js">
		
	</script>
<?php
	if($UseMap){
?>
		<script src="https://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript" defer></script>
<?php
	}
?>
	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 855941404;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
	</script>
	<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
	</script>
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/855941404/?guid=ON&amp;script=0"/>
		</div>
	</noscript>
	<?php if($Page->Id==1){?>
		<!-- <script>
			yaCounter44499730.reachGoal('remarketing');
		</script> -->
	<?php }?>
	<script type="text/javascript">
	$(document).ready(function(){
	  $('#product-slider').slick({
			dots: true,
			slidesToShow: 4,
			slidesToScroll:1,
			arrows: false,
			variableWidth: false,
			adaptiveHeight: true,
			autoplaySpeed: 2000,
			responsive: [
		    {
		      breakpoint: 1205,
		      settings: {
		        slidesToShow: 3,
		        variableWidth: false,
		      }
		    },
		    {
		    	breakpoint: 935,
		    	settings:{
		    		variableWidth: false,
		    		slidesToShow:2,
		    	}
		    },{
		    	breakpoint: 615,
		    	settings:{
		    		variableWidth: false,
		    		slidesToShow:1,
		    	}
		    },{
		    	breakpoint: 321,
		    	settings:{
		    		variableWidth: false,
		    		slidesToShow:1,
		    	}
		    }]
		});
	  $('#sales-slider').slick({
			dots: true,
			slidesToShow: 1,
			slidesToScroll:1,
			arrows: false,
			// variableWidth: true,
			// swipe:false,
			// autoplay: true,
			// centerMode: true,
			autoplaySpeed: 2000,
	  });
	  $('.reviewList').find('.main-items').slick({
			dots: true,
			slidesToShow: 3,
			slidesToScroll:1,
			arrows: false,
			// variableWidth: true,
			// swipe:false,
			// autoplay: true,
			// centerMode: true,
			autoplaySpeed: 2000,
			responsive: [
		    {
		      breakpoint: 1124,
		      settings: {
		        slidesToShow: 2,
		      }
		    },
		    {
		    	breakpoint: 780,
		    	settings:{
		    		slidesToShow:1,
		    	}
		    }]
	  });
	  var width_window = $('.body').width() + 'px';
	  console.log(width_window);
	  $('.slick-dots').css({width:width_window,});
	  $('#product-slider').css({width:width_window,});
	  // $('.share').css({width:width_window,}).parent('#sales-slider');
	});
	</script>
	<div class="banner-gift-close">
		<a class="close-layer" href="#">&nbsp;</a>
		<a class="close-it" href="&nbsp;"></a>
		<a href="https://www.facebook.com/IECTelecom/" class="img-banner" target="_blank"  id="showed-banner">
			<img class="banner" src="/images/banner-gift.jpg?v1" alt="" title=""/>
		</a>
	</div>
	</body>
</html>
