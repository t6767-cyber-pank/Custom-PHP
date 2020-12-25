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
										<?php if ($Type->Id == 1): ?>
											<div class="tariff-warning">
												<div class="tw_title">Уважаемые абоненты, </div>
												<p>С 1 апреля 2019 года вступают в силу новые условия для сим-карт Thuraya. <br>Для владельцев сим-карт Thuraya: которые пополнят(-ли) счет в период с 5 марта 2017 года по 31 марта 2019 года и не произвели пополнение счета до 31 декабря 2019 года - баланс истекает 31 марта 2020 года. </p>
												<table>
													<thead>
														<tr>
															<th>Последнее пополнение клиента</th>
															<th>Последняя активность клиента</th>
															<th>Существующий баланс клиента</th>
															<th>Дата истечения баланса</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>5 марта 2017 года — 31 марта 2019 года</td>
															<td>Любая дата</td>
															<td>Любой баланс</td>
															<td>31 марта 2020 года</td>
														</tr>
													</tbody>
												</table>
												<p>В случае пополнения любого номинала скретч-карты после 31 марта 2019 года будет применяться как новый срок действия пополнения и срок действия баланса будет действовать в рамках выбранного номинала скретч карты. </p>
											</div>
										<?php endif ?>
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
																<li>
																<table width="100%" style="font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; font-size: 14px; border-collapse: collapse; text-align: center;">
																<tr>
																<?php 
																$divis="";
																$xkl=80;
																if ($Time<>"") { $divis="</td><td width='15%'>"; $xkl=65; } ?>
																<td width="<?=$xkl?>%">
																<?=$HtmlTags->InputRadio("simproduct",$Product->Id,$Product->Title."".$divis."<div class='right'>".$Time."</td><td width='20%'>".$Price.$TimeAdd."</div>",false,"","simproduct{$Product->Id}","radio",true)?>
																</td>
																</tr>
																</table>
																</li>
																<?php
																unset($Product);
															}
															?>
															
															<?php if($Type->Id == 1): ?>
																<li>
																<style>

th, td:first-child {
background: #008598;
color: white !important;
padding: 10px 20px !important;
color: #FFFFFF !important;
}
th, td {
border-style: solid !important;
border-width: 0 1px 1px 0 !important;
border-color: white !important;
font-weight: 700 !important;
}
td label{
color: #FFFFFF !important;
}

td {
background: #D8E6F3;
}
th:first-child, td:first-child {
text-align: left !important;
}
ul li input.radio:checked + label:after{
    background: #FFFFFF !important;
}
section.simServices > div.tabItems > div.tab div.line div.leftBlock dl.items dd ul li + li {
    border-top: none;
}
section.simServices > div.tabItems > div.tab div.line div.leftBlock dl.items dd ul li {
    padding: 0px 0;
}
</style>
																	<table width="100%" style="font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif; font-size: 14px; border-collapse: collapse; text-align: center;">
<tr>
																	<td width="70%"><input type="radio" name="simproduct" value="56" id="simproduct-custom-2" class="radio">
																	<label for="simproduct-custom-2" style="color: #ffffff;">
																		Произвольная сумма</td><td>
																			<span>от <?=$LangPack->SimPackage28Minimum?></span>
																	</label></td>
																	<td><input type="number" id="simproduct-custom-input-2" min="<?=$LangPack->SimPackage28Minimum?>" name="custom_price_2" style="display: none;"></td>
																	
</tr></table>
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
                                                } else {
                                                    ?>
                                                    <br/>
                                                    <div class="items" data-type="1" style="padding-top: 390px !important;">
                                                        <img src="/images/Card_711_3d.png" style="display: inline; float: left; margin-right: 15px; width: 50%;">
                                       
														<h2 style="font-size: 18px; color: #0097ac; ">ПРЕИМУЩЕСТВА SIM КАРТЫ 711:</h2>
                                                        <p>
                                                        <ul>
                                                            <li>специальный тариф 1 юнит = 1 минута разговоров на городские и сотовые операторы по Всему Миру с территории Казахстана, Узбекистана и Кыргызстана.</li>
                                                            <li>стоимость 1 минуты от 434 тенге.</li>
                                                            <li>гибкая система пополнения от 5 000 тенге</li>
                                                            <li>абонентская плата — 39 юнит, списывается с баланса раз в 12 мес. и срок действия СИМ продлевается на следующие 12 мес.</li>
                                                            <li>загруженный баланс действует согласно сроку пополнения номинала</li>
                                                            <li>широкая сеть терминалов по приему платежей в терминалах Qiwi, kassa24.kz</li>
                                                            <li>онлайн с карт Visa и MasterCard на satavenue.kz</li>
                                                            <li>техническая поддержка 24/7 +971502424238 <img src="/images/whatsapp.png" alt="" style="width: 17px;height: 17px;"></li>
                                                            <li>сервисный центр по диагностике и ремонту оборудования г.Алматы, ул. Карибжанова 43/56</li>
                                                        </ul>
                                                        </p>
                                                    </div>
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
																<span class="title"><?=$LangPack->IridiumUnitsPerPeriodTitle?></span>
																<span class="center"><?=$LangPack->IridiumUnitsPerPeriod?></span>
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
						<div class="info">
						<p><?=$TextBlocks->SimFooterDescription->Description?></p>
                            </div>
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