<?php
$Messages=array("ERROR"=>array(),"LOG"=>array());
if(isset($_GET["request"])){
	$Name=$Kernel->PostToSQL(@$_POST["name"]);
	$SurName=$Kernel->PostToSQL(@$_POST["surname"]);
	$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
	$EMail=@$_POST["email"];
	$Message=$Kernel->PostToSQL(@$_POST["message"],true,true,2048);
	if(empty($Name)||empty($SurName))
		$Messages["ERROR"][]=$LangPack->ErrorsName;
	if(empty($Phone))
		$Messages["ERROR"][]=$LangPack->ErrorsPhone;
	if(empty($EMail)||!filter_var($EMail,FILTER_VALIDATE_EMAIL))
		$Messages["ERROR"][]=$LangPack->ErrorsEMail;
	if(empty($Message))
		$Messages["ERROR"][]=$LangPack->ErrorsMessage;
	if(count($Messages["ERROR"])){
		$Messages["ERROR"]="<p>".join("</p><p>",$Messages["ERROR"])."</p>";
		if(isset($_GET["ajax"])){
			echo $Messages["ERROR"];
			exit;
		}
	}
	else{
		$UserId=$IsAuth?$GLOBAL->UserInfo->Id:0;
		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->ContactRequests} SET `UserId`={$UserId},`DateTime`={$DateTime},`PageTitle`='{$Page->Title}',`PageURL`='{$OWN_REQUEST}',`Name`='{$Name}',`SurName`='{$SurName}',`EMail`='{$EMail}',`Phone`='{$Phone}',`Message`='{$Message}';");
		$Title=str_replace(array("%NAME%","%SURNAME%","%PAGETITLE%","%PAGEURL%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),array($Name,$SurName,$Page->Title,$OWN_REQUEST,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),$TextBlocks->ContactRequestMessage->Title);
		$Message=str_replace(array("%NAME%","%SURNAME%","%PAGETITLE%","%PAGEURL%","%EMAIL%","%PHONE%","%MESSAGE%","%DATETIME%","%IP%","%USERAGENT%"),array($Name,$SurName,$Page->Title,$OWN_REQUEST,$EMail,$Phone,str_replace(array("\r\n","\n\r","\r","\n"),"<br />",$Message),date("r",$DateTime),$_SERVER["REMOTE_ADDR"],$_SERVER["HTTP_USER_AGENT"]),$TextBlocks->ContactRequestMessage->Description);
		foreach($MailTo as $Mail){
			$Kernel->SendMessage($Mail,$Title,$Message);
		}
		if(isset($_GET["ajax"])){
			echo "<div class=\"message log\">{$TextBlocks->ContactRequestSended->Description}</div>";
			exit;
		}
		else{
			header("Location: ?sended");
			exit;
		}
	}
}
if(isset($_GET["sended"])){
	$Messages["LOG"][]=$TextBlocks->ContactRequestSended->Description;
	$Messages["LOG"]="<p>".join("</p><p>",$Messages["LOG"])."</p>";
}
$AllPages=$DataBase->Query("SELECT *,MenuTitle_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SystemPages} WHERE `Owner`={$Page->FullIds[2]} AND `MenuVisible`='Y' ORDER BY `Position`;");
$FirstPage=current($AllPages);
if($Page->Id==$FirstPage->Owner){
	header("Location: {$ActiveLanguage->LinksPrefix}{$FirstPage->FullAddress}");
	exit;
}
$SimCategories=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Unit_{$ActiveLanguage->Prefix} AS Unit FROM {$GLOBAL->SimCategories} WHERE `Status`='Y' ORDER BY `Position`;",true,array(),"Id",false,false,array("Products"));
$AllTypes=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title,Description_{$ActiveLanguage->Prefix} AS Description FROM {$GLOBAL->SimTypes} WHERE `Status`='Y' ORDER BY `Position`;",true,array(),"Id",false,false,array("Products"));
$AllProducts=$DataBase->Query("
		SELECT
			*,
			Title_{$ActiveLanguage->Prefix} AS Title,
			PriceValue_{$ActiveLanguage->Prefix} AS PriceValue
		FROM
			{$GLOBAL->SimProducts}
		WHERE
			`PageId`={$Page->Id}
		AND 
			`Status`='Y'
		ORDER BY
			`Position`
	;");
foreach($AllProducts as $Product){
	$SimCategories[$Product->CategoryId]->Products[$Product->TypeId][$Product->Id]=$Product;
	$AllTypes[$Product->TypeId]->Products[$Product->Id]=$Product;
	unset($Product);
}
include ".{$DIRS->modules}so/h.inc";
?>
	<section class="simServices body">
		<div class="tabs">
			<?php
			foreach($AllPages as $SubPage){
				$class=$SubPage->Id==$Page->Id?" current":"";
				?>
				<a href="<?=$ActiveLanguage->LinksPrefix.$SubPage->FullAddress?>" class="tab<?=$class?>">
					<span><?=$SubPage->Title?></span>
				</a>
			<?php
			}
			?>
		</div>
		<div class="payTabs">
			<?=$HtmlTags->InputRadio("payType","prepayment",$LangPack->PayTypePrepayment,true,"","payTypePrepayment","radio",true)?>
			<?=$HtmlTags->InputRadio("payType","postpaid",$LangPack->PayTypePostpaid,false,"","payTypePostpaid","radio",true)?>
		</div>
		<div class="tabItems">
			<div class="tab prepayment current">
				<form action="<?=$ActiveLanguage->LinksPrefix?>/basket/?add" method="post" class="basket">
					<div class="line">
						<div class="leftBlock">

							<div class="typeItems">
								<?php
								$ThisItem=0;
								foreach($AllTypes as $Type){
									echo $HtmlTags->InputRadio("type",$Type->Id,$Type->Title,$ThisItem++==0,"","type{$Type->Id}","radio",true);
								}
								?>
							</div>
							<div class="tabItems">
								<?php
								$ThisItem=0;
								foreach($AllTypes as $Type){
									$class=$ThisItem++==0?" current":"";
									?>
									<div class="tab tab<?=$Type->Id?><?=$class?>" data-type="<?=$Type->Id?>">
										<?php
										foreach($SimCategories as $Category){
											if($Category->Type=="M"&&isset($Category->Products[$Type->Id])&&count($Category->Products[$Type->Id])){
												?>
												<dl class="items" data-category="<?=$Category->Id?>">
													<dt><?=$Category->Title?></dt>
													<dd>
														<ul>
															<?php
															if($Type->Id==2&&$Category->Id==4){
																?>
																<li class="head">
																	<span class="title"><?=$LangPack->LabelService?></span>
																	<span class="sub-title"><?=$LangPack->LabelPeriod?></span>
																	<span class="sub-title"><?=$LangPack->LabelPrice?></span>
																</li>
															<?php
															}
															foreach($Category->Products[$Type->Id] as $Product){
																if ($Product->Id == 55) continue;
																$Price="<span>".(empty($Product->PriceValue)?str_replace("%PRICE%",$Kernel->DigitalToPrice($Product->Price),$LangPack->PriceTengeLong):$Product->PriceValue)."</span>";
																$Time=!empty($Product->VaucherTime)?"<span class='time'>".$Product->VaucherTime."</span>":"";
																$TimeAdd=!empty($Product->PriceMinute)?"<span class='time nf'>".$Product->PriceMinute." <span class=\"currency\"><?=$LangPack->LabelPerMinute?></span></span><span class='time nf'>".$Product->PriceSeconds." <span class=\"currency\"><?=$LangPack->LabelPerSeconds?></span></span>":"";
																if(!empty($TimeAdd)){
																	$Price=null;
																}
																?>
																<li><?=$HtmlTags->InputRadio("simproduct",$Product->Id,"<span class='title'>".$Product->Title."</span>"."<div class='right'>".$Time.$Price.$TimeAdd."</div>",false,"","simproduct{$Product->Id}","radio",true)?></li>
																<?php
																unset($Product);
															}
															?>
															<?php if($Type->Id == 1): ?>
																<li>
																	<input type="radio" name="simproduct" value="56" id="simproduct-custom-2" class="radio">
																	<label for="simproduct-custom-2">
																		<span class="title">Произвольная сумма</span>
																		<div class="right">
																			<span>от 4000</span>
																		</div>
																	</label>
																	<input type="number" id="simproduct-custom-input-2" min="4000" name="custom_price_2" style="display: none;">
																</li>
															<?php endif ?>

														</ul>
													</dd>
													<?php if($Category->Id==2){ ?>
														<p class="unit-desc"><?=$Category->Unit?></p>
													<?php } ?>
												</dl>
											<?php
												if(!empty($Type->Description)&&((int)$Category->Id==4)){
													?>
													<div class="description"><?=$Type->Description?></div>
												<?php
												}
											}
											unset($Category);
										}

										?>
									</div>
								<?php
								}
								?>
							</div>
						</div>
						<div class="rightBlock">
							<?php
							$ThisItem=0;
							foreach($AllTypes as $Type){
								$class=$ThisItem++==0?" current":"";
								?>
								<div class="tab tab<?=$Type->Id?><?=$class?>" data-type="<?=$Type->Id?>">
									<?php
									foreach($SimCategories as $Category){
										if($Category->Type=="E"&&isset($Category->Products[$Type->Id])&&count($Category->Products[$Type->Id])){
											?>
											<dl class="items" data-category="<?=$Category->Id?>">
												<dt><?=$Category->Title?></dt>
												<dd>
													<ul>
														<?php if(($Category->Id!=3&&$Category->Id!=6)||$Type->Id==2){ ?>
															<li>
																<span class="title">Абонентская плата (один раз в 24 мес)</span>
																<span class="center">10 unit/24 мес</span>
															</li>
														<?php } ?>
														<?php
														foreach($Category->Products[$Type->Id] as $Product){
															$Price="<span>".(empty($Product->PriceValue)?str_replace("%PRICE%",$Kernel->DigitalToPrice($Product->Price),$LangPack->PriceTengeLong):$Product->PriceValue)."</span>";
															?>
															<li>
																<?php if(
																	(($Category->Id==2||$Category->Id==3||$Category->Id==6)&&$Type->Id==1)
																	||(in_array($Product->Id,array(28,29,30)))){ ?>
																	<span class="title"><?=$Product->Title?></span>
																	<span class="center"><?=$Product->PriceValue?></span>
																<?php }
																else{
																	?>
																	<span class="title"><?=$Product->Title?></span>
																	<span class="sub-title"><?=$Product->PriceMinute?></span>
																	<span class="sub-title"><?=$Product->PriceSeconds?></span>
																<?php } ?>
															</li>
															<?php
															unset($Product);
														}
														?>
													</ul>
												</dd>
											</dl>
										<?php
										}
										unset($Category);
									}
									?>
								</div>
								<?php
								unset($Type);
							}
							?>
						</div>
					</div>
					<div class="submit">
						<div class="info"><?=$TextBlocks->SimFooterDescription->Description?></div>
						<div class="submit"><?=$HtmlTags->InputSubmit($LangPack->BuyTariff,"","","",true,"submit","submit")?></div>
					</div>
				</form>
			</div>
			<div class="tab postpaid">
				<form action="?request" method="post" class="request">
					<div class="description"><?=$TextBlocks->PostpaidDescription->Description?></div>
					<?php
					if(isset($Messages["ERROR"])&&!empty($Messages["ERROR"])){
						?>
						<div class="message error"><?=$Messages["ERROR"]?></div>
					<?php
					}
					if(isset($Messages["LOG"])&&!empty($Messages["LOG"])){
						?>
						<div class="message log"><?=$Messages["LOG"]?></div>
					<?php
					}
					?>
					<div class="line">
						<div class="left">
							<div class="row">
								<label for="name"><?=$LangPack->NameReq?></label>
								<?=$HtmlTags->InputText("name",@$_POST["name"],"","text","name","text",0,false,0,"","","","","","",true)?>
							</div>
							<div class="row">
								<label for="surname"><?=$LangPack->SurNameReq?></label>
								<?=$HtmlTags->InputText("surname",@$_POST["surname"],"","text","surname","text",0,false,0,"","","","","","",true)?>
							</div>
							<div class="row">
								<label for="email"><?=$LangPack->EMailReq?></label>
								<?=$HtmlTags->InputText("email",@$_POST["email"],"","email","email","text",0,false,0,"","","","","","",true)?>
							</div>
							<div class="row">
								<label for="phone"><?=$LangPack->FullPhoneReq?></label>
								<?=$HtmlTags->InputText("phone",@$_POST["phone"],"","text","phone","text",0,false,0,"","","","","","",true)?>
							</div>
						</div>
						<div class="right">
							<label for="message"><?=$LangPack->MessageReq?></label>
							<?=$HtmlTags->textarea("message",@$_POST["message"],40,10,"","","message","text","","",true)?>
						</div>
					</div>
					<div class="submit">
						<?=$HtmlTags->InputSubmit($LangPack->send,"","","",true,"submit","submit")?>
						<div class="info" style="text-align: left"><?=$TextBlocks->SimFooterDescription->Description?></div>
					</div>
				</form>
			</div>
		</div>
	</section>
<? include ".{$DIRS->modules}so/f.inc"; ?>