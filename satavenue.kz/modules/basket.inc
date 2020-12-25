<?php
/*ini_set("display_errors",1);
error_reporting(E_ALL);*/
	$BasketStep=1;

	if(isset($_GET["step"])){
		$Step=(int) $_GET["step"];

		if($Step>=1 && $Step<=3){
			$BasketStep=$Step;
		}
	}

	if(isset($_GET["clear"])){
		$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id};");
		$Kernel->CalculateBasket($BasketInfo);

		header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
		exit;
	}

	if($BasketStep==3 && isset($_GET["id"])){
		$Id=$_GET["id"];
		if(preg_match("'^([\d\D]{32})$'si",$Id)){
			if($BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE MD5(`Id`)='{$Id}' AND `Status` IN ('P','PV');",false)){
				$Kernel->GetBasketItems($BasketInfo);
			}else{
				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}
		}else{
			header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
			exit;
		}
	}

	if(isset($_GET["add"])){
		if(isset($_POST["product"]) && (!isset($_POST["optionId"]) || (isset($_POST["optionId"]) && preg_match("'^p([\d]{1,11})$'si",$_POST["optionId"])))){
			$ProductId=(int) $_POST["product"];
			$Amount=(int) @$_POST["amount"];
			$Amount=empty($Amount)?1:$Amount;

			if($ProductInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Products} WHERE Id={$ProductId};",false)){
				$Amount=$ProductInfo->MaxAmount<$Amount?$ProductInfo->MaxAmount:$Amount;

				if($BasketItem=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id} AND `ProductId`={$ProductInfo->Id} AND `OptionId`=0 AND `SimProductId`=0;",false)){
					$Amount=$Amount+(int)$BasketItem->Amount;
					$Amount=$ProductInfo->MaxAmount<$Amount?$ProductInfo->MaxAmount:$Amount;
					$DataBase->UpdateQuery("UPDATE {$GLOBAL->BasketItems} SET `Amount`={$Amount},`Price`={$ProductInfo->Price} WHERE Id={$BasketItem->Id};");
				}else{
					$DataBase->InsertQuery("INSERT INTO {$GLOBAL->BasketItems} SET `BasketId`={$BasketInfo->Id},`UserId`={$UserId},`DateTime`={$DateTime},`Amount`={$Amount},`ProductId`={$ProductInfo->Id},`Price`={$ProductInfo->Price};");
				}

				$Kernel->CalculateBasket($BasketInfo);

				$Kernel->GetResponse("OK:SEP:".$Kernel->DigitalToPrice($BasketInfo->TotalItems).":SEP:".str_replace("%PRICE%",$Kernel->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg));

				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}else{
				$Kernel->GetResponse("ERROR");
			}
		}elseif(isset($_POST["simproduct"])){
			$ProductId=(int) $_POST["simproduct"];
			$Amount=(int) @$_POST["amount"];
			$Amount=empty($Amount)?1:$Amount;

			if($ProductInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SimProducts} WHERE Id={$ProductId};",false)){
				// Костыль на добавление произвольной суммы в корзину
				if ($ProductInfo->Id == 56) {
					$CustomPrice = $LangPack->MinimumCustomPrice;
					if (isset($_POST['custom_price_2']) && intval($_POST['custom_price_2']) > $LangPack->MinimumCustomPrice)
						$CustomPrice = intval($_POST['custom_price_2']);
					$ProductInfo->Price = $CustomPrice;
				}
				if($BasketItem=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id} AND `SimProductId`={$ProductInfo->Id} AND `OptionId`=0 AND `ProductId`=0;",false)){
					$Amount=$Amount+(int)$BasketItem->Amount;
					$DataBase->UpdateQuery("UPDATE {$GLOBAL->BasketItems} SET `Amount`={$Amount},`Price`={$ProductInfo->Price} WHERE Id={$BasketItem->Id};");
				}else{
					$DataBase->InsertQuery("INSERT INTO {$GLOBAL->BasketItems} SET `BasketId`={$BasketInfo->Id},`UserId`={$UserId},`DateTime`={$DateTime},`Amount`={$Amount},`SimProductId`={$ProductInfo->Id},`Price`={$ProductInfo->Price};");
				}


				$Kernel->CalculateBasket($BasketInfo);

				$Kernel->GetResponse("OK:SEP:".$Kernel->DigitalToPrice($BasketInfo->TotalItems).":SEP:".str_replace("%PRICE%",$Kernel->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg));

				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}else{
				$Kernel->GetResponse("ERROR");
			}
		}elseif(isset($_POST["type"])){
			$Kernel->GetResponse($LangPack->PleaseChooseSimProduct);
		}elseif(isset($_POST["optionId"])){
			$ProductId=(int) $_POST["product"];
			$SimId=(int) @$_POST["optionId"];

			$Amount=(int) @$_POST["amount"];
			$Amount=empty($Amount)?1:$Amount;

			if($SimInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SimProducts} WHERE Id={$SimId};",false)){
				if($ProductInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Products} WHERE Id={$ProductId};",false)){
					if($OptionInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->ProductOptions} WHERE `ProductId`={$ProductInfo->Id} AND `SimProductId`={$SimInfo->Id};",false)){
						$Amount=$ProductInfo->MaxAmount<$Amount?$ProductInfo->MaxAmount:$Amount;

						if($BasketItem=$DataBase->Query("SELECT * FROM {$GLOBAL->BasketItems} WHERE `BasketId`={$BasketInfo->Id} AND `ProductId`={$ProductInfo->Id} AND `OptionId`={$SimInfo->Id};",false)){
							$Amount=$Amount+(int)$BasketItem->Amount;
							$Amount=$ProductInfo->MaxAmount<$Amount?$ProductInfo->MaxAmount:$Amount;
							$DataBase->UpdateQuery("UPDATE {$GLOBAL->BasketItems} SET `Amount`={$Amount},`Price`={$OptionInfo->Price} WHERE Id={$BasketItem->Id};");
						}else{
							$DataBase->InsertQuery("INSERT INTO {$GLOBAL->BasketItems} SET `BasketId`={$BasketInfo->Id},`UserId`={$UserId},`DateTime`={$DateTime},`Amount`={$Amount},`ProductId`={$ProductInfo->Id},`OptionId`={$OptionInfo->Id},`SimProductId`={$SimInfo->Id},`Price`={$OptionInfo->Price};");
						}

						$Kernel->CalculateBasket($BasketInfo);

						$Kernel->GetResponse("OK");

						header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
						exit;
					}else{
						$Kernel->GetResponse("ERROR");
					}
				}else{
					$Kernel->GetResponse("ERROR");
				}
			}else{
				$Kernel->GetResponse("ERROR");
			}
		}
	}

	$ProductIds=array();
	$SimProductIds=array();
	$OptionIds=array();
	foreach($BasketInfo->Items as $Item){
		if(!empty($Item->ProductId))
			$ProductIds[$Item->ProductId]=$Item->ProductId;
		if(!empty($Item->SimProductId))
			$SimProductIds[$Item->SimProductId]=$Item->SimProductId;
		if(!empty($Item->OptionId))
			$OptionIds[$Item->OptionId]=$Item->OptionId;

		unset($Item);
	}

	if(count($ProductIds)){
		$ProductIds=join(",",$ProductIds);
		$AllProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE Id IN ({$ProductIds});",true,array(),"Id",false,false,array("PhotoId"=>"int"));
		$ProductIds=array_keys($AllProducts);
		$ProductIds=join(",",$ProductIds);
		$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Owner` IN ({$ProductIds}) AND `Type`='PRODUCT' ORDER BY `Position`;");
		foreach($Photos as $Photo){
			if(empty($AllProducts[$Photo->Owner]->PhotoId))
				$AllProducts[$Photo->Owner]->PhotoId=$Photo->Id;

			unset($Photo);
		}
		unset($Photos);
	}
	if(count($SimProductIds)){
		$SimProductIds=join(",",$SimProductIds);
		$AllSimProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SimProducts} WHERE Id IN ({$SimProductIds});");
	}

	if(!empty($BasketInfo->PromoCode)){
		if($PromoCodeInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->PromoCodes} WHERE `Id`={$BasketInfo->PromoCode};",false)){
			$BasketInfo->DiscountPrice=round($BasketInfo->TotalToPay/100*(100-$PromoCodeInfo->Discount),0);
		}
	}

	if(isset($_GET["update"])){
		foreach($BasketInfo->Items as $Item){
			$Amount=(int) @$_POST["amount{$Item->Id}"];
			if(!empty($Amount)){
				if(!empty($Item->ProductId)){
					$Amount=$AllProducts[$Item->ProductId]->MaxAmount<$Amount?$AllProducts[$Item->ProductId]->MaxAmount:$Amount;
				}

				$DataBase->UpdateQuery("UPDATE {$GLOBAL->BasketItems} SET `Amount`={$Amount} WHERE Id={$Item->Id};");
			}else{
				$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->BasketItems} WHERE Id={$Item->Id};");
			}

			unset($Item);
		}

		$Kernel->CalculateBasket($BasketInfo);

		$PromoCode=$Kernel->PostToSQL(@$_POST["promo"]);
		if(!empty($PromoCode) && $PromoCodeInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->PromoCodes} WHERE `Code`='{$PromoCode}';",false)){
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET `PromoCode`={$PromoCodeInfo->Id} WHERE Id={$BasketInfo->Id};");
			$BasketInfo->DiscountPrice=round($BasketInfo->TotalToPay/100*(100-$PromoCodeInfo->Discount),0);
			$BasketInfo->PromoCode=$PromoCodeInfo->Id;
		}

		if(!empty($BasketInfo->PromoCode)){
			if($PromoCodeInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->PromoCodes} WHERE `Id`={$BasketInfo->PromoCode};",false)){
				$BasketInfo->DiscountPrice=round($BasketInfo->TotalToPay/100*(100-$PromoCodeInfo->Discount),0);
			}
		}

		$Kernel->PrintBasketStatusBar($BasketInfo);
		echo ":SEP:";
		foreach($BasketInfo->Items as $Item){
			$Kernel->PrintBasketItem($Item);
			unset($Item);
		}

		echo ":SEP:".$Kernel->DigitalToPrice($BasketInfo->TotalItems).":SEP:".str_replace("%PRICE%",$Kernel->DigitalToPrice($BasketInfo->TotalToPay),$LangPack->PriceTg);

		exit;
	}

	if($BasketStep==2 && isset($_GET["pay"])){
		/*ini_set("display_errors",1);
		error_reporting(E_ALL);*/
		$SQL=array();
		$UserUpdateSQL=array();

		$errorMsg=array();

		$BusinessType=strtoupper(@$_POST["type"]);
		if(!isset($BusinessTypes[$BusinessType]))
			$errorMsg[]="type";
		$SQL[]="`BusinessType`='{$BusinessType}'";

		$DeliveryType=strtoupper(@$_POST["delivery"]);
		if(!in_array($DeliveryType,array('PICKUP','COURIER','EXPRESS')))
			$errorMsg[]="deliverytype";
		$SQL[]="`DeliveryType`='{$DeliveryType}'";

		$DeliveryAddressSame="N";
		if($DeliveryType=="COURIER"){
			if($BusinessType=="PRIVATE"){
				$DeliveryCity=$Kernel->PostToSQL(@$_POST["city"]);
				if(empty($DeliveryCity))
					$errorMsg[]="city";
				$DeliveryStreet=$Kernel->PostToSQL(@$_POST["street"]);
				if(empty($DeliveryStreet))
					$errorMsg[]="street";
				$DeliveryHouse=$Kernel->PostToSQL(@$_POST["house"]);
				if(empty($DeliveryHouse))
					$errorMsg[]="house";
				$DeliveryApartment=$Kernel->PostToSQL(@$_POST["apartment"]);
				if(empty($DeliveryApartment))
					$errorMsg[]="apartment";
				$DeliveryTime=strtoupper(@$_POST["time"]);
				if(!in_array($DeliveryTime,array('ANY','MORNING','DAY','EVENING')))
					$errorMsg[]="time";
			}

			if($BusinessType=="BUSINESS"){
				$DeliveryAddressSame=(isset($_POST["deliveryLegalCourierAddress"]) && $_POST["deliveryLegalCourierAddress"]=="y")?"Y":"N";
				$SQL[]="`DeliveryAddressSame`='{$DeliveryAddressSame}'";

				if($DeliveryAddressSame=="N"){
					$LegalIndex=$Kernel->PostToSQL(@$_POST["legalIndex"]);
					if(empty($LegalIndex))
						$errorMsg[]="legalIndex";
					$LegalRegion=$Kernel->PostToSQL(@$_POST["legalRegion"]);
					if(empty($LegalRegion))
						$errorMsg[]="legalRegion";
					$LegalCity=$Kernel->PostToSQL(@$_POST["legalCity"]);
					if(empty($LegalCity))
						$errorMsg[]="legalCity";
					$LegalDistrict=$Kernel->PostToSQL(@$_POST["legalDistrict"]);
					if(empty($LegalDistrict))
						$errorMsg[]="legalDistrict";
					$LegalStreet=$Kernel->PostToSQL(@$_POST["legalStreet"]);
					if(empty($LegalStreet))
						$errorMsg[]="legalStreet";
					$LegalHouse=$Kernel->PostToSQL(@$_POST["legalHouse"]);
					if(empty($LegalHouse))
						$errorMsg[]="legalHouse";
					$LegalHull=$Kernel->PostToSQL(@$_POST["legalHull"]);
					$LegalOffice=$Kernel->PostToSQL(@$_POST["legalOffice"]);
				}
				$DeliveryTime=strtoupper(@$_POST["courierLegalTime"]);
				if(!in_array($DeliveryTime,array('ANY','MORNING','DAY','EVENING')))
					$errorMsg[]="time";
			}
		}elseif($DeliveryType=="EXPRESS"){
			if($BusinessType=="PRIVATE"){
				$DeliveryCity=$Kernel->PostToSQL(@$_POST["cityExpress"]);
				if(empty($DeliveryCity))
					$errorMsg[]="cityExpress";
				$DeliveryStreet=$Kernel->PostToSQL(@$_POST["streetExpress"]);
				if(empty($DeliveryStreet))
					$errorMsg[]="streetExpress";
				$DeliveryHouse=$Kernel->PostToSQL(@$_POST["houseExpress"]);
				if(empty($DeliveryHouse))
					$errorMsg[]="houseExpress";
				$DeliveryApartment=$Kernel->PostToSQL(@$_POST["apartmentExpress"]);
				if(empty($DeliveryApartment))
					$errorMsg[]="apartmentExpress";
				$DeliveryTime=strtoupper(@$_POST["timeExpress"]);
				if(!in_array($DeliveryTime,array('ANY','MORNING','DAY','EVENING')))
					$errorMsg[]="timeExpress";
			}

			if($BusinessType=="BUSINESS"){
				$DeliveryAddressSame=(isset($_POST["deliveryLegalExpressAddress"]) && $_POST["deliveryLegalExpressAddress"]=="y")?"Y":"N";
				$SQL[]="`DeliveryAddressSame`='{$DeliveryAddressSame}'";

				if($DeliveryAddressSame=="N"){
					$LegalIndex=$Kernel->PostToSQL(@$_POST["legalIndexExpress"]);
					if(empty($LegalIndex))
						$errorMsg[]="legalIndexExpress";
					$LegalRegion=$Kernel->PostToSQL(@$_POST["legalRegionExpress"]);
					if(empty($LegalRegion))
						$errorMsg[]="legalRegionExpress";
					$LegalCity=$Kernel->PostToSQL(@$_POST["legalCityExpress"]);
					if(empty($LegalCity))
						$errorMsg[]="legalCityExpress";
					$LegalDistrict=$Kernel->PostToSQL(@$_POST["legalDistrictExpress"]);
					if(empty($LegalDistrict))
						$errorMsg[]="legalDistrictExpress";
					$LegalStreet=$Kernel->PostToSQL(@$_POST["legalStreetExpress"]);
					if(empty($LegalStreet))
						$errorMsg[]="legalStreetExpress";
					$LegalHouse=$Kernel->PostToSQL(@$_POST["legalHouseExpress"]);
					if(empty($LegalHouse))
						$errorMsg[]="legalHouseExpress";
					$LegalHull=$Kernel->PostToSQL(@$_POST["legalHullExpress"]);
					$LegalOffice=$Kernel->PostToSQL(@$_POST["legalOfficeExpress"]);
				}
				$DeliveryTime=strtoupper(@$_POST["timeLegalExpress"]);
				if(!in_array($DeliveryTime,array('ANY','MORNING','DAY','EVENING')))
					$errorMsg[]="time";
			}
		}

		if($DeliveryType!=="PICKUP"){
			if($BusinessType=="PRIVATE"){
				$SQL[]="`DeliveryCity`='{$DeliveryCity}'";
				$SQL[]="`DeliveryStreet`='{$DeliveryStreet}'";
				$SQL[]="`DeliveryHouse`='{$DeliveryHouse}'";
				$SQL[]="`DeliveryApartment`='{$DeliveryApartment}'";
				$SQL[]="`DeliveryTime`='{$DeliveryTime}'";

				if($IsAuth && empty($GLOBAL->UserInfo->City))
					$UserUpdateSQL[]="`City`='{$DeliveryCity}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Street))
					$UserUpdateSQL[]="`Street`='{$DeliveryStreet}'";
				if($IsAuth && empty($GLOBAL->UserInfo->House))
					$UserUpdateSQL[]="`House`='{$DeliveryHouse}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Apartment))
					$UserUpdateSQL[]="`Apartment`='{$DeliveryApartment}'";
			}
			if($BusinessType=="BUSINESS" && $DeliveryAddressSame=="N"){
				$SQL[]="`LegalIndex`='{$LegalIndex}'";
				$SQL[]="`LegalRegion`='{$LegalRegion}'";
				$SQL[]="`LegalCity`='{$LegalCity}'";
				$SQL[]="`LegalDistrict`='{$LegalDistrict}'";
				$SQL[]="`LegalStreet`='{$LegalStreet}'";
				$SQL[]="`LegalHouse`='{$LegalHouse}'";
				$SQL[]="`LegalHull`='{$LegalHull}'";
				$SQL[]="`LegalOffice`='{$LegalOffice}'";
				$SQL[]="`DeliveryTime`='{$DeliveryTime}'";

				if($IsAuth && empty($GLOBAL->UserInfo->Index))
					$UserUpdateSQL[]="`Index`='{$LegalIndex}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Region))
					$UserUpdateSQL[]="`Region`='{$LegalRegion}'";
				if($IsAuth && empty($GLOBAL->UserInfo->City))
					$UserUpdateSQL[]="`City`='{$LegalCity}'";
				if($IsAuth && empty($GLOBAL->UserInfo->District))
					$UserUpdateSQL[]="`District`='{$LegalDistrict}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Street))
					$UserUpdateSQL[]="`Street`='{$LegalStreet}'";
				if($IsAuth && empty($GLOBAL->UserInfo->House))
					$UserUpdateSQL[]="`House`='{$LegalHouse}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Hull))
					$UserUpdateSQL[]="`Hull`='{$LegalHull}'";
				if($IsAuth && empty($GLOBAL->UserInfo->Office))
					$UserUpdateSQL[]="`Office`='{$LegalOffice}'";
			}
		}

		$PayType=strtoupper(@$_POST["payment"]);
		if(!in_array($PayType,array('CASH','PREPAYMENT')))
			$errorMsg[]="payment";
		$SQL[]="`PayType`='{$PayType}'";

		$PayMethod=strtoupper(@$_POST["method"]);
		$PayMethod=$PayType=="CASH"?$PayType:$PayMethod;
		if(!in_array($PayMethod,array('CASH','BANK','ONLINE')))
			$errorMsg[]="method";
		$SQL[]="`PayMethod`='{$PayMethod}'";

		if($PayType=="PREPAYMENT" && $PayMethod=="BANK" && $BusinessType=="PRIVATE"){
			$INN=$Kernel->PostToSQL(@$_POST["inn"]);
			if(empty($INN))
				$errorMsg[]="inn";
			$SQL[]="`INN`='{$INN}'";
			if($IsAuth && empty($GLOBAL->UserInfo->INN))
				$UserUpdateSQL[]="`INN`='{$INN}'";
			$BIN=$Kernel->PostToSQL(@$_POST["bin"]);
			if(empty($BIN))
				$errorMsg[]="bin";
			$SQL[]="`BIN`='{$BIN}'";
			if($IsAuth && empty($GLOBAL->UserInfo->BIN))
				$UserUpdateSQL[]="`BIN`='{$BIN}'";
			$Address=$Kernel->PostToSQL(@$_POST["address"]);
			if(empty($Address))
				$errorMsg[]="address";
			$SQL[]="`Address`='{$Address}'";
			if($IsAuth && empty($GLOBAL->UserInfo->Address))
				$UserUpdateSQL[]="`Address`='{$Address}'";
		}
		
		$Name=$Kernel->PostToSQL(@$_POST["name"]);
		if(empty($Name))
			$errorMsg[]="name";
		$SQL[]="`Name`='{$Name}'";
		if($IsAuth && empty($GLOBAL->UserInfo->Name))
			$UserUpdateSQL[]="`Name`='{$Name}'";
		$SurName=$Kernel->PostToSQL(@$_POST["surname"]);
		if(empty($SurName))
			$errorMsg[]="surname";
		$SQL[]="`SurName`='{$SurName}'";
		if($IsAuth && empty($GLOBAL->UserInfo->SurName))
			$UserUpdateSQL[]="`SurName`='{$SurName}'";
		$EMail=@$_POST["email"];
		if(empty($EMail) || !filter_var($EMail, FILTER_VALIDATE_EMAIL))
			$errorMsg[]="email";
		$SQL[]="`EMail`='{$EMail}'";
		$Phone=$Kernel->PostToSQL(@$_POST["phone"]);
		if(empty($Phone))
			$errorMsg[]="phone";
		$SQL[]="`Phone`='{$Phone}'";
		if($IsAuth && empty($GLOBAL->UserInfo->Phone))
			$UserUpdateSQL[]="`Phone`='{$Phone}'";


		if($IsAuth && count($UserUpdateSQL)){
			$UserUpdateSQL=join(",",$UserUpdateSQL);
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET {$UserUpdateSQL} WHERE Id={$GLOBAL->UserInfo->Id};");
		}

		$CheckConditions=(isset($_POST["cond"]) && $_POST["cond"]=="y");	
		if(!$CheckConditions)
			$errorMsg[]="cond";

		if(count($errorMsg)){
			$errorMsg=join(",",$errorMsg);
			$Kernel->GetResponse("ERROR:SEP:".$errorMsg);
		}else{
			/*$Status="P";
			if($PayMethod=="ONLINE" || $PayMethod=="BANK")
				$Status="PV";
			else
				$SQL[]="`PayDateTime`={$DateTime}";*/
			if($PayMethod!="ONLINE"){
				$SQL[]="`Status`='PV'";
			}


			if(isset($BasketInfo->DiscountPrice))
				$SQL[]="`TotalSumm`={$BasketInfo->DiscountPrice}";
			else
				$SQL[]="`TotalSumm`={$BasketInfo->TotalToPay}";
			

			$SQL=join(",",$SQL);

			$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET {$SQL} WHERE Id={$BasketInfo->Id};");

			$MailTo=array("iecmarketing@iec-telecom.kz","info-kz@iec-telecom.com","adelia.safina@iec-telecom.com","request@satavenue.kz", "eduardeliseev.m@gmail.com");

			$Title = 'Новый заказ на сайте';

			$Message = 'Пользователь оформил заказ на сайте. 
			Информация о его заказе <a href="http://satavenue.kz/admin/baskets/?EditForm&Id=' . $BasketInfo->Id . '">Здесь</a>';

			foreach ($MailTo as $Mail) {
				$Kernel->SendMessage($Mail, $Title, $Message);
			}

			$BasketId=md5($BasketInfo->Id);
			if($PayMethod=="ONLINE"){
				require_once("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/kkb.utils.php");
				$pathConfig="/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/config.txt";
				$currencyId=398;
?>
				<form id="redirect" name="redirect" action="https://epay.kkb.kz/jsp/process/logon.jsp" method="post">
					<input id="base64" name="Signed_Order_B64" value="<?=process_request($BasketInfo->Id,$currencyId,$BasketInfo->TotalToPay,$pathConfig)?>" type="hidden">
					<input name="Language" value="rus" type="hidden">
					<input id="BackLink" name="BackLink" value="http://www.satavenue.kz/basket/?send&payOnline&id=<?=$BasketInfo->Id?>" type="hidden">
					<input id="PostLink" name="PostLink" value="http://www.satavenue.kz/payed/" type="hidden">
					<input id="FailurePostLink" name="FailurePostLink" value="http://www.satavenue.kz/basket/?send&payOnline&error" type="hidden">
				</form>
				<script type="text/javascript">
					document.redirect.submit();
				</script>
<?php
				exit;
			}elseif($PayMethod=="BANK"){
				$Response="BANK:SEP:{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?id={$BasketId}&invoice";
				$Kernel->GetResponse($Response);
				$Response="{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?id={$BasketId}&invoice";

				header("Location: {$Response}");
				exit;
			}else{
				$Response="OK:SEP:{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?step=3&id={$BasketId}";
				$Kernel->GetResponse($Response);
				$Response="{$ActiveLanguage->LinksPrefix}{$Page->FullAddress}?step=3&id={$BasketId}";
				
				header("Location: {$Response}");
				exit;
			}
		}
	}

	if(isset($_GET["invoice"]) && isset($_GET["id"])){
		$Id=$_GET["id"];
		if(preg_match("'^([\d\D]{32})$'si",$Id)){
			if($BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE MD5(`Id`)='{$Id}' AND `Status`='PV' AND `PayMethod`='BANK';",false)){
				$ProductIds=array();
				$SimProductIds=array();
				$OptionIds=array();

				$AllProducts=array();
				$AllSimProducts=array();

				$Kernel->GetBasketItems($BasketInfo);

				foreach($BasketInfo->Items as $Item){
					if(!empty($Item->ProductId))
						$ProductIds[$Item->ProductId]=$Item->ProductId;
					if(!empty($Item->SimProductId))
						$SimProductIds[$Item->SimProductId]=$Item->SimProductId;
					if(!empty($Item->OptionId))
						$OptionIds[$Item->OptionId]=$Item->OptionId;

					unset($Item);
				}

				if(count($ProductIds)){
					$ProductIds=join(",",$ProductIds);
					$AllProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE Id IN ({$ProductIds});",true,array(),"Id",false,false,array("PhotoId"=>"int"));
					$ProductIds=array_keys($AllProducts);
					$ProductIds=join(",",$ProductIds);
					$Photos=$DataBase->Query("SELECT * FROM {$GLOBAL->Photos} WHERE `Owner` IN ({$ProductIds}) AND `Type`='PRODUCT' ORDER BY `Position`;");
					foreach($Photos as $Photo){
						if(empty($AllProducts[$Photo->Owner]->PhotoId))
							$AllProducts[$Photo->Owner]->PhotoId=$Photo->Id;

						unset($Photo);
					}
					unset($Photos);
				}
				if(count($SimProductIds)){
					$SimProductIds=join(",",$SimProductIds);
					$AllSimProducts=$DataBase->Query("SELECT *,Title_{$ActiveLanguage->Prefix} AS Title FROM {$GLOBAL->SimProducts} WHERE Id IN ({$SimProductIds});");
				}

				include_once './PHPExcel/PHPExcel/IOFactory.php';
				$PHPExcel = PHPExcel_IOFactory::load('./imgs/invoice.xls');
				$PHPExcel->setActiveSheetIndex(0);
				$Sheet = $PHPExcel->getActiveSheet();
				$Worksheet = $PHPExcel->getActiveSheet();

				$InvoiceNumber=$Worksheet->getCellByColumnAndRow(1,16)->getValue();
				$Worksheet->getCellByColumnAndRow(1,16)->setValue($InvoiceNumber."WS".$Kernel->AddZero($BasketInfo->Id,10)." от ".date("d",$BasketInfo->DateTime)." ".mb_strtolower($AllMonths[date("n",$BasketInfo->DateTime)][1],"UTF-8")." ".date("Y",$BasketInfo->DateTime));

				$BuyerInfo=array();
				$BuyerInfo[]="БИН / ИИН {$BasketInfo->BIN}";
				if($BasketInfo->BusinessType=="PRIVATE"){
					$BuyerInfo[]="{$BasketInfo->Organization}";
					$BuyerInfo[]="Республика Казахстан";
					if(!empty($BasketInfo->Region))
						$BuyerInfo[]=$BasketInfo->Region;
					if(!empty($BasketInfo->District))
						$BuyerInfo[]=$BasketInfo->District;
					$BuyerInfo[]="г. {$BasketInfo->City}";
					$BuyerInfo[]="{$BasketInfo->Street}";
					$BuyerInfo[]="{$BasketInfo->House}";
					if(!empty($BasketInfo->Hull))
						$BuyerInfo[]="корпус {$BasketInfo->Hull}";
					if(!empty($BasketInfo->Office))
						$BuyerInfo[]="офис/квартира {$BasketInfo->Office}";
					if(!empty($BasketInfo->Building))
						$BuyerInfo[]="строение {$BasketInfo->Building}";
					if(!empty($BasketInfo->Phone))
						$BuyerInfo[]="тел.: {$BasketInfo->Phone}";
				}elseif($BasketInfo->BusinessType=="BUSINESS"){
					$BuyerInfo[]="{$BasketInfo->Organization}";
					$BuyerInfo[]="Республика Казахстан";
					if(!empty($BasketInfo->LegalRegion))
						$BuyerInfo[]=$BasketInfo->LegalRegion;
					if(!empty($BasketInfo->LegalDistrict))
						$BuyerInfo[]=$BasketInfo->LegalDistrict;
					$BuyerInfo[]="г. {$BasketInfo->LegalCity}";
					$BuyerInfo[]="{$BasketInfo->LegalStreet}";
					$BuyerInfo[]="{$BasketInfo->LegalHouse}";
					if(!empty($BasketInfo->LegalHull))
						$BuyerInfo[]="корпус {$BasketInfo->LegalHull}";
					if(!empty($BasketInfo->LegalOffice))
						$BuyerInfo[]="офис/квартира {$BasketInfo->LegalOffice}";
					if(!empty($BasketInfo->LegalBuilding))
						$BuyerInfo[]="строение {$BasketInfo->LegalBuilding}";
					if(!empty($BasketInfo->LegalPhone))
						$BuyerInfo[]="тел.: {$BasketInfo->Phone}";
				}
				$BuyerInfo=join(", ",$BuyerInfo);
				$Worksheet->getCellByColumnAndRow(5,22)->setValue($BuyerInfo);
				$Worksheet->getRowDimension(22)->setRowHeight(45);
				$Worksheet->getStyleByColumnAndRow(5,22)->getAlignment()->setWrapText(true);

				$LeftAlign = array(
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						)
				);
				$RightAlign = array(
						'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						)
				);

				$Worksheet->getStyleByColumnAndRow(1,27)->getFont()->setBold(false)->setSize(8);
				$Worksheet->getStyleByColumnAndRow(3,27)->getFont()->setBold(false)->setSize(8);
				$Worksheet->getStyleByColumnAndRow(19,27)->getFont()->setBold(false)->setSize(8);
				$Worksheet->getStyleByColumnAndRow(23,27)->getFont()->setBold(false)->setSize(8);
				$Worksheet->getStyleByColumnAndRow(26,27)->getFont()->setBold(false)->setSize(8);
				$Worksheet->getStyleByColumnAndRow(32,27)->getFont()->setBold(false)->setSize(8);

				$Worksheet->getStyleByColumnAndRow(3,27)->applyFromArray($LeftAlign);
				$Worksheet->getStyleByColumnAndRow(19,27)->applyFromArray($RightAlign);
				$Worksheet->getStyleByColumnAndRow(23,27)->applyFromArray($LeftAlign);
				$Worksheet->getStyleByColumnAndRow(26,27)->applyFromArray($RightAlign);
				$Worksheet->getStyleByColumnAndRow(32,27)->applyFromArray($RightAlign);
				
				for($i=1;$i<count($BasketInfo->Items);$i++){
					$Worksheet->insertNewRowBefore(28,1);
					$Worksheet->mergeCellsByColumnAndRow(1,28,2,28);
					$Worksheet->mergeCellsByColumnAndRow(3,28,18,28);
					$Worksheet->mergeCellsByColumnAndRow(19,28,22,28);
					$Worksheet->mergeCellsByColumnAndRow(23,28,25,28);
					$Worksheet->mergeCellsByColumnAndRow(26,28,31,28);
					$Worksheet->mergeCellsByColumnAndRow(32,28,37,28);

					unset($Item);
				}

				$ThisItem=1;
				$ThisRow=27;
				foreach($BasketInfo->Items as $Item){
					$Title="";

					if(!empty($Item->ProductId) && !empty($Item->SimProductId)){
						$Title="{$AllProducts[$Item->ProductId]->Title} + {$AllSimProducts[$Item->SimProductId]->Title}";
					}elseif(!empty($Item->ProductId)){
						$Title=$AllProducts[$Item->ProductId]->Title;
					}elseif(!empty($Item->SimProductId)){
						$Title=$AllSimProducts[$Item->SimProductId]->Title;
					}

					$Worksheet->getCellByColumnAndRow(1,$ThisRow)->setValue($ThisItem);
					$Worksheet->getCellByColumnAndRow(3,$ThisRow)->setValue($Title);
					$Worksheet->getCellByColumnAndRow(19,$ThisRow)->setValue($Item->Amount.",000");
					$Worksheet->getCellByColumnAndRow(23,$ThisRow)->setValue("шт.");
					$Worksheet->getCellByColumnAndRow(26,$ThisRow)->setValue($Kernel->DigitalToPrice($Item->Price).",00");
					$Worksheet->getCellByColumnAndRow(32,$ThisRow)->setValue($Kernel->DigitalToPrice($Item->Amount*$Item->Price).",00");

					$ThisRow++;
					$ThisItem++;

					unset($Item);
				}

				$ThisRow++;
				$Nds=round($BasketInfo->TotalToPay/112*12,2);
				$Nds=explode(".",$Nds);
				$Nds[0]=$Kernel->DigitalToPrice($Nds[0]);
				$Nds=join(",",$Nds);
				$Worksheet->getCellByColumnAndRow(32,$ThisRow++)->setValue($Kernel->DigitalToPrice($BasketInfo->TotalToPay).",00");
				$Worksheet->getCellByColumnAndRow(32,$ThisRow++)->setValue($Nds);

				$TotalAmount=$Worksheet->getCellByColumnAndRow(1,32)->getValue();
				$Worksheet->getCellByColumnAndRow(1,32)->setValue($TotalAmount.$Kernel->DigitalToPrice($BasketInfo->TotalItems).", на сумму ".$Kernel->DigitalToPrice($BasketInfo->TotalToPay).",00 KZT");
				$TotalSumm=$Worksheet->getCellByColumnAndRow(1,33)->getValue();
				$Worksheet->getCellByColumnAndRow(1,33)->setValue($TotalSumm.$Kernel->mb_ucfirst($Kernel->num2str($BasketInfo->TotalToPay)));

				$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');

				header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
				header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
				header ( "Cache-Control: no-cache, must-revalidate" );
				header ( "Pragma: no-cache" );
				header ( "Content-type: application/vnd.ms-excel" );
				header ( "Content-Disposition: attachment; filename=".$BasketInfo->Id.'-'.date("dmy Hi").'.xls');

				$objWriter->save('php://output');

				exit;
			}else{
				header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
				exit;
			}
		}else{
			header("Location: {$ActiveLanguage->LinksPrefix}{$Page->FullAddress}");
			exit;
		}
	}

if(isset($_GET["getPayRequest"])){
	require_once("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/kkb.utils.php");
	$pathConfig="/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/test/config.txt";
	$currencyId=398;
	//$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET `Status`='Y' WHERE `Id`={$BasketInfo->Id}");
	echo process_request($BasketInfo->Id,$currencyId,$BasketInfo->TotalToPay,$pathConfig);
	exit;
}

	include ".{$DIRS->modules}so/h.inc";
?>
	<section class="basketInfo body">
<?php
	if(count($BasketInfo->Items)==0){
		if(isset($_GET["payOnline"])){
			if(isset($_GET["id"])){
				$id=(int)$_GET["id"];
				$idMD5=md5($id);
				$OrderInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `Id`={$id} AND `UserId`={$GLOBAL->UserInfo->Id}",false);
				if(!empty($OrderInfo)){
					if($OrderInfo->Status=="P" && !empty($OrderInfo->PayDateTime) && $OrderInfo->PayMethod=="ONLINE"){
						header("Location: ?step=3&id={$idMD5}");
						exit;
					}else{
						header("Location: ?send&payOnline&error");
						exit;
					}
				}else{
					header("Location: ?payOnline&error");
					exit;
				}
			}
			if(isset($_GET["success"])){
				?>
				<div class="minimal"><?=$LangPack->TestKKBPaySuccess?></div>
			<?php
			}elseif(isset($_GET["error"])){
				?>
				<div class="minimal"><?=$LangPack->TestKKBPayError?></div>
			<?php
			}
		}
	}else{
?>
		<div class="basketSteps">
			<div class="step step1<?=$BasketStep==1?" current":""?>">
				<?=$LangPack->YourBasket?>
			</div>
			<div class="step step2<?=$BasketStep==2?" current":""?>">
				<?=$LangPack->BasketDetails?>
			</div>
			<div class="step step3<?=$BasketStep==3?" current":""?>">
				<?=$LangPack->BasketPayed?>
			</div>
		</div>
<?php
		if($BasketStep==1){
?>
		<form action="?step=2" method="post" class="step1">
			<div class="list">
<?php
			foreach($BasketInfo->Items as $Item){
				$Kernel->PrintBasketItem($Item);
				unset($Item);
			}
?>
			</div>
<?$Kernel->PrintBasketStatusBar($BasketInfo)?>
			<div class="controls">
				<a href="?clear" class="clear"><?=$LangPack->ClearBasket?></a>
				<?=$HtmlTags->InputSubmit($LangPack->BasketOrder,"","","",true,"submit","submit")?>
			</div>
		</form>
<?php
		}elseif($BasketStep==2){
?>
		<div class="info"><?=$Kernel->PrintBasketStatusBar($BasketInfo,"SHORT")?></div>
		<div class="tabs" id="basketStep2">
		</div>
		<div class="tabItems">
			<div id="private" class="tab current">
				<form action="?step=2&pay" method="post" class="step2 recaptchaForm">
					<?=$HtmlTags->InputText("type","private","","hidden")?>
					<div class="delivery">
						<div class="title"><?=$LangPack->DeliveryMethod?></div>
						<?=$HtmlTags->InputRadio("delivery","pickup",$LangPack->DeliveryPickup,false,"","deliveryPickup","radio",true)?>
						<?=$HtmlTags->InputRadio("delivery","courier",$LangPack->DeliveryCourier,true,"","deliveryCourier","radio",true)?>
					</div>
					<div class="deliveryForm">
						<div class="deliveryPickup">
							<div class="description"><?=$TextBlocks->DeliveryPickup->Description?></div>
						</div>
						<div class="deliveryCourier current">
							<div class="description"><?=$TextBlocks->DeliveryCourier->Description?></div>
							<div class="form">
								<div class="title"><?=$LangPack->DeliveryFormTitle?></div>
								<div class="grid items">
									<div class="row">
										<label for="courierCity"><?=$LangPack->City?></label>
										<?=$HtmlTags->InputText("city",$IsAuth?$GLOBAL->UserInfo->City:"","","text","courierCity","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="courierStreet"><?=$LangPack->Street?></label>
										<?=$HtmlTags->InputText("street",$IsAuth?$GLOBAL->UserInfo->Street:"","","text","courierStreet","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="courierHouse"><?=$LangPack->House?></label>
										<?=$HtmlTags->InputText("house",$IsAuth?$GLOBAL->UserInfo->House:"","","text","courierHouse","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="courierApartment"><?=$LangPack->Apartment?></label>
										<?=$HtmlTags->InputText("apartment",$IsAuth?$GLOBAL->UserInfo->Apartment:"","","text","courierApartment","text",0,false,0,"","","","","","",true)?>
									</div>
								</div>
								<div class="title" style="display: none;"><?=$LangPack->DeliveryTimeTitle?></div>
								<div class="grid" style="display: none;">
									<div class="row"><?=$HtmlTags->InputRadio("time","any",$LangPack->DeliveryTimeAny,true,"","timeAnyCourier","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("time","morning",$LangPack->DeliveryTimeMorning,false,"","timeMorningCourier","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("time","day",$LangPack->DeliveryTimeDay,false,"","timeDayCourier","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("time","evening",$LangPack->DeliveryTimeEvening,false,"","timeEveningCourier","radio",true)?></div>
								</div>
							</div>
						</div>
						<div class="deliveryExpress">
							<div class="description"><?=$TextBlocks->DeliveryExpress->Description?></div>
							<div class="form">
								<div class="title"><?=$LangPack->DeliveryFormTitle?></div>
								<div class="grid items">
									<div class="row">
										<label for="expressCity"><?=$LangPack->City?></label>
										<?=$HtmlTags->InputText("cityExpress",$IsAuth?$GLOBAL->UserInfo->City:"","","text","expressCity","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="expressStreet"><?=$LangPack->Street?></label>
										<?=$HtmlTags->InputText("streetExpress",$IsAuth?$GLOBAL->UserInfo->Street:"","","text","expressStreet","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="expressHouse"><?=$LangPack->House?></label>
										<?=$HtmlTags->InputText("houseExpress",$IsAuth?$GLOBAL->UserInfo->House:"","","text","expressHouse","text",0,false,0,"","","","","","",true)?>
									</div>
									<div class="row">
										<label for="expressApartment"><?=$LangPack->Apartment?></label>
										<?=$HtmlTags->InputText("apartmentExpress",$IsAuth?$GLOBAL->UserInfo->Apartment:"","","text","expressApartment","text",0,false,0,"","","","","","",true)?>
									</div>
								</div>
								<div class="title"><?=$LangPack->DeliveryTimeTitle?></div>
								<div class="grid">
									<div class="row"><?=$HtmlTags->InputRadio("timeExpress","any",$LangPack->DeliveryTimeAny,true,"","timeAnyExpress","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("timeExpress","morning",$LangPack->DeliveryTimeMorning,false,"","timeMorningExpress","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("timeExpress","day",$LangPack->DeliveryTimeDay,false,"","timeDayExpress","radio",true)?></div>
									<div class="row"><?=$HtmlTags->InputRadio("timeExpress","evening",$LangPack->DeliveryTimeEvening,false,"","timeEveningExpress","radio",true)?></div>
								</div>
								<a href="<?=$ActiveLanguage->LinksPrefix?>/delivery/" target="_blank"><?=$LangPack->DeliveryCostView?></a>
							</div>
						</div>
					</div>
					<input type="hidden" name="payment" value="prepayment" id="paymentPrepayment" class="hidden">
					<div class="payTabs">
						<div class="payCash"></div>
						<div class="payPrepayment current">
							<div class="payMethods">
								<div style="display: none"><?=$HtmlTags->InputRadio("method","cash",$LangPack->PayMethodCash,false,"","methodPayCash","radio",true)?></div>
								<?=$HtmlTags->InputRadio("method","online",$LangPack->PayMethodOnline,true,"","methodPayOnline","radio",true)?>
							</div>
							<div class="payTabs">
								<div class="methodCash current"><?=$TextBlocks->PaymentMethodCash->Description?></div>
								<div class="methodOnline"><?=$TextBlocks->PaymentMethodOnline->Description?></div>
								<input id="base64" name="Signed_Order_B64" value="" type="hidden">
								<input name="Language" value="rus" type="hidden">
								<input id="BackLink" name="BackLink" value="http://www.satavenue.kz/basket/?send&payOnline&id=<?=$BasketInfo->Id?>" type="hidden">
								<input id="PostLink" name="PostLink" value="http://www.satavenue.kz/payed/" type="hidden">
								<input id="FailurePostLink" name="FailurePostLink" value="http://www.satavenue.kz/basket/?send&payOnline&error" type="hidden">
							</div>
						</div>
					</div>
					<div class="fields">
						<div class="row">
							<label for="name"><?=$LangPack->NameReq?></label>
							<?=$HtmlTags->InputText("name",$IsAuth?$GLOBAL->UserInfo->Name:"","","text","name","text text-pattern",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="surname"><?=$LangPack->SurNameReq?></label>
							<?=$HtmlTags->InputText("surname",$IsAuth?$GLOBAL->UserInfo->SurName:"","","text","surname","text text-pattern",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="email"><?=$LangPack->EMailReq?></label>
							<?=$HtmlTags->InputText("email",$IsAuth?urldecode($GLOBAL->UserInfo->EMail):"","","email","email","text email-pattern",0,false,0,"","","","","","",true)?>
						</div>
						<div class="row">
							<label for="phone"><?=$LangPack->PhoneReq?></label>
							<?=$HtmlTags->InputText("phone",$IsAuth?$GLOBAL->UserInfo->Phone:"","","tel","phone","text phone-pattern",0,false,0,"","","","","","",true)?>
						</div>
						<div class="checkbox">
							<?=$HtmlTags->InputCheckbox("cond","y",$TextBlocks->BasketConditions->Description,false,"","cond","",true,"checkbox",true,false,true)?>
						</div>
						<div class="checkbox	">
							<div id="recaptchaForm"></div>
							<div id="recaptchaErrorForm"></div>
						</div>
						<div class="submit">
							<a href="<?=$ActiveLanguage->LinksPrefix.$Page->FullAddress?>" class="back"><?=$LangPack->BackToBasket?></a>
							<?=$HtmlTags->InputSubmit($LangPack->BasketOrder,"","","",true,"submit","submit")?>
						</div>
					</div>
				</form>
			</div>
		</div>
<?php
		}elseif($BasketStep==3){
?>
		<h2 class="title thank"><?=$LangPack->ThankForBasketRequest?></h2>
		<div class="list">
<?php
			foreach($BasketInfo->Items as $Item){
				$Kernel->PrintBasketItem($Item,false);
				unset($Item);
			}
?>
		</div>
<?$Kernel->PrintBasketStatusBar($BasketInfo,"GLOBAL",false)?>
		<div class="basketNotify"><?=$TextBlocks->FinalBasketNotify->Description?></div>
<?php
		}
	}
?>
	</section>
<? include ".{$DIRS->modules}so/f.inc";?>