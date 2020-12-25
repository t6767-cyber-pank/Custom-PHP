<?php
	if($GLOBAL->UserInfo->RightsBaskets!=="Y"){
		header("Location: /admin/");
		exit;
	}
ini_set("display_errors",1);
error_reporting(E_ALL);
$AllBrands=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title,VoucherType_{$SortLanguage->Prefix} AS VoucherType,PhoneLabel_{$SortLanguage->Prefix} AS PhoneLabel,UnitTitle_{$SortLanguage->Prefix} AS UnitTitle FROM {$GLOBAL->Brands} WHERE `Status`='Y' ORDER BY `Position`;");
$AllSimPackages=$DataBase->Query("SELECT *,Period_{$SortLanguage->Prefix} AS Period,Message_{$SortLanguage->Prefix} AS Message FROM {$GLOBAL->SimPackages} WHERE `Status`='Y' ORDER BY `Position`;",true,array(),array("BrandId","Id"));

	class LangPack{
		function LangPack(){
			global $GLOBAL,$SortLanguage,$DataBase;
			$AllLabels=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->LangPack};");
			foreach($AllLabels as $Label){
				$name=trim($Label->Name);
				if(!empty($name))
					$this->$name=$Label->Title;

				unset($Label);
			}
			unset($AllLabels);
		}
	}

	$LangPack=new LangPack();

	$ParentId=0;
	$Crumbs=array("Управление заказами");
	$ShowBaskets=true;
	$BasketExist=false;

	$AllBasketStatuses=array(
		'NP'=>"Не отправлен",
		'P'=>"Оплата получена",
		'PV'=>"Ожидание оплаты",
		'D'=>"Доставлен"
	);

	$AllDeliveryTypes=array(
		'PICKUP'=>"Самовывоз",
		'COURIER'=>"Курьером",
		'EXPRESS'=>"Экспесс доставка"
	);

	$DeliveryTimes=array(
		'ANY'=>$LangPack->DeliveryTimeAny,
		'MORNING'=>$LangPack->DeliveryTimeMorning,
		'DAY'=>$LangPack->DeliveryTimeDay,
		'EVENING'=>$LangPack->DeliveryTimeEvening
	);

	$PayMethods=array(
		'CASH'=>"Наличными курьеру",
		'BANK'=>"Счет на оплату",
		'ONLINE'=>"Онлайн оплата"
	);

	$Direction="DESC";
	$PageDirection="DESC";

	$SortField="DateTime";
	$SortMethod="DateTime {$Direction}";

	if(isset($_GET["Sort"])){
		$Direction=(isset($_GET["Direction"]) && $_GET["Direction"]=="DESC")?"DESC":"ASC";
		$PageDirection=$Direction;
		$Sort=$_GET["Sort"];

		$SortField=$Sort;

		$SortMethod="{$Sort} {$Direction}";

		$Direction=$Direction=="DESC"?"ASC":"DESC";
	}

	$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";

	$Filters=array();
	$FiltersURL=array();

	if(isset($_GET["Filter"])){
		$Id=(int) @$_GET["Id"];
		if(!empty($Id)){
			$Filters[]="`Id`={$Id}";
			$FiltersURL[]="Id={$Id}";
		}
		$Status=$Kernel->PostToSQL(@$_GET["Status"]);
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
		$TotalToPay=(int) @$_GET["TotalToPay"];
		if(!empty($TotalToPay)){
			$Filters[]="`TotalToPay`={$TotalToPay}";
			$FiltersURL[]="TotalToPay={$TotalToPay}";
		}
		$BusinessType=@$_GET["BusinessType"];
		$BusinessType=in_array($BusinessType,array('PRIVATE','BUSINESS'))?$BusinessType:"";
		if(!empty($BusinessType)){
			$Filters[]="`BusinessType`='%{$BusinessType}%'";
			$FiltersURL[]="BusinessType={$BusinessType}";
		}
	}

	$Filters[]="`TotalToPay`>0";

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllBaskets=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets}{$Filters} ORDER BY {$SortMethod};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($BasketInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE Id={$Id};",false)){
			$Kernel->GetBasketItems($BasketInfo);
			if(empty($BasketInfo->UserId)){
				$BasketInfo->UserInfo=new stdClass();
				$BasketInfo->UserInfo->Id=0;
				$BasketInfo->UserInfo->Name="Не зарегестрированный пользователь";
			}else{
				$BasketInfo->UserInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE Id={$BasketInfo->UserId};",false);

				$BasketInfo->UserInfo->FullName=array();
				if(!empty($BasketInfo->UserInfo->SurName))
					$BasketInfo->UserInfo->FullName[]=$BasketInfo->UserInfo->SurName;
				if(!empty($BasketInfo->UserInfo->Name))
					$BasketInfo->UserInfo->FullName[]=$BasketInfo->UserInfo->Name;
				if(!empty($BasketInfo->UserInfo->SecondName))
					$BasketInfo->UserInfo->FullName[]=$BasketInfo->UserInfo->SecondName;

				$BasketInfo->UserInfo->FullName=join(" ",$BasketInfo->UserInfo->FullName);

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
					$AllProducts=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->Products} WHERE Id IN ({$ProductIds});",true,array(),"Id",false,false,array("PhotoId"=>"int"));
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
					$AllSimProducts=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->SimProducts} WHERE Id IN ({$SimProductIds});");
				}
			}

			if(!empty($BasketInfo->PromoCode)){
				$BasketInfo->PromoCodeInfo=$DataBase->Query("SELECT *,Title_{$SortLanguage->Prefix} AS Title FROM {$GLOBAL->PromoCodes} WHERE Id={$BasketInfo->PromoCode};",false);
			}

			$BasketExist=true;
		}
	}

	if(isset($_GET["EditForm"]) && $BasketExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Edit"]) && $BasketExist){
		$SQL=array();

		$Status=@$_POST["Status"];
		$Status=in_array($Status,array('NP','P','PV','D'))?$Status:"PV";
		$SQL[]="`Status`='{$Status}'";

		$PayDateTime=@$_POST["PayDateTime"];
		$PayDateTime=explode(" ",$PayDateTime);
		$PayDateTime[0]=@$PayDateTime[0];
		$PayDateTime[1]=@$PayDateTime[1];
		$PayDateTime[0]=explode("/",$PayDateTime[0]);
		$PayDateTime[1]=explode(":",$PayDateTime[1]);
		$PayDateTime=mktime((int) @$PayDateTime[1][0],(int) @$PayDateTime[1][1],0,(int) @$PayDateTime[0][1],(int) @$PayDateTime[0][0],(int) @$PayDateTime[0][2]);
		$SQL[]="`PayDateTime`={$PayDateTime}";

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET {$SQL} WHERE Id={$BasketInfo->Id};");

		header("Location: ?EditForm&Id={$BasketInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		foreach($AllBaskets as $Basket){
			if(isset($_POST["Chk{$Basket->Id}"]) && $_POST["Chk{$Basket->Id}"]==$Basket->Id){
				$DelIds[$Basket->Id]=$Basket->Id;
			}

			unset($Basket);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->Baskets} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$BasketCount=$DataBase->Query("SELECT COUNT(*) AS Baskets FROM {$GLOBAL->Baskets};",false);
	$BasketCount=(int) @$BasketCount->Baskets;

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранные заказы" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего заказов: <strong><?=$BasketCount?></strong></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col width="0" />
					<col width="0" />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Ф.И.О.<?=$SortField=="Name"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=TotalToPay&Direction=<?=$Direction?><?=$FiltersURL?>">Сумма заказа<?=$SortField=="TotalToPay"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=TotalSumm&Direction=<?=$Direction?><?=$FiltersURL?>">Сумма к оплате<?=$SortField=="TotalSumm"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=DateTime&Direction=<?=$Direction?><?=$FiltersURL?>">Дата заказа<?=$SortField=="DateTime"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Status&Direction=<?=$Direction?><?=$FiltersURL?>">Статус<?=$SortField=="Status"?$DirectionArrow:""?></a></th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllBaskets as $Basket){
			$Name=array();
			if(!empty($Basket->Name))
				$Name[]=$Basket->Name;
			if(!empty($Basket->SurName))
				$Name[]=$Basket->SurName;
			$Name=join(" ",$Name);
			if($Basket->TypeBasket=="F"){
				$Name="<em>Пополнение баланса</em>";
			}
?>
					<tr fid="<?=$Basket->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$Basket->Id}",$Basket->Id,"",false,"","Chk{$Basket->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$Basket->Id?>"><?=$Basket->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$Basket->Id?>"><?=$Name?></a></td>
						<td><a href="?EditForm&Id=<?=$Basket->Id?>"><?=$Kernel->DigitalToPrice($Basket->TotalToPay)?> тг.</a></td>
						<td><a href="?EditForm&Id=<?=$Basket->Id?>"><?=$Kernel->DigitalToPrice($Basket->TotalSumm)?> тг.</a></td>
						<td><a href="?EditForm&Id=<?=$Basket->Id?>"><?=date("d.m.y H:i",$Basket->DateTime)?></a></td>
						<td><?=$AllBasketStatuses[$Basket->Status]?></td>
					</tr>
<?php
			unset($Basket);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$BasketInfo->Id}":"Add"?>" method="post">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> заказа:</h1>
			<div class="buttons">
				<a href="?Id=<?=$ParentId?>" class="back">Вернуться</a>
				<button class="button add"><?=$IsEdit?"Применить":"Добавить"?></button>
			</div>
		</div>
		<div class="container">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" class="tl tr">Информация</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Дата и время формирования:</th>
						<td><?=date("d.m.Y H:i",$BasketInfo->DateTime)?>
					</tr>
					<tr>
						<th>Пользователь:</th>
						<td>
<?php
		if(empty($BasketInfo->UserId)){
			echo $BasketInfo->UserInfo->Name;
		}else{
?>
							<a href="/admin/users/?Id=<?=$BasketInfo->UserId?>&EditForm" target="_blank"><?=$BasketInfo->UserInfo->FullName?></a>
<?php
		}
?>
						</td>
					</tr>
					<tr>
						<th>Статус заказа:</th>
						<td>
<?php
		foreach($AllBasketStatuses as $Value=>$Title){
			echo $HtmlTags->InputRadio("Status",$Value,$Title,$IsEdit?$BasketInfo->Status==$Value:false,"","Status{$Value}","checkbox-slider",true);
		}
?>
						</td>
					</tr>
					<tr>
						<th>Дата оплаты:</th>
						<td>
							<?=$HtmlTags->InputText("PayDateTime",$IsEdit?date("d/m/Y H:i",$BasketInfo->PayDateTime):0,"","text","PayDateTime","text datetimepicker")?>
						</td>
					</tr>
					<?php if($BasketInfo->TypeBasket=="B"){ ?>
					<tr>
						<th>Всего наименований:</th>
						<td><?=$Kernel->DigitalToPrice($BasketInfo->TotalItems)?></td>
					</tr>
					<?php } ?>
<?php
		if(!empty($BasketInfo->PromoCode)){
?>
					<tr>
						<th>Промо-код:</th>
						<td><a href="/admin/products/promo-codes/?Id=<?=$BasketInfo->PromoCode?>&EditForm" target="_blank"><?=$BasketInfo->PromoCodeInfo->Code?> / <?=$BasketInfo->PromoCodeInfo->Title?></a></td>
					</tr>
<?php
		}
?>
					<?php if($BasketInfo->TypeBasket=="B"){ ?>
					<tr>
						<th>Кто покупатель:</th>
						<td><?=$BasketInfo->BusinessType=="PRIVATE"?"Частное лицо":"Юридическое лицо"?></td>
					</tr>
					<tr>
						<th>Вид доставки:</th>
						<td><?=$AllDeliveryTypes[$BasketInfo->DeliveryType]?></td>
					</tr>
					<?php } ?>
<?php
		if(!empty($BasketInfo->DeliveryCity)){
?>
					<tr>
						<th>Город доставки:</th>
						<td><?=$BasketInfo->DeliveryCity?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->DeliveryStreet)){
?>
					<tr>
						<th>Улица доставки:</th>
						<td><?=$BasketInfo->DeliveryStreet?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->DeliveryHouse)){
?>
					<tr>
						<th>Номер дома:</th>
						<td><?=$BasketInfo->DeliveryHouse?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->DeliveryApartment)){
?>
					<tr>
						<th>Номер квартиры/офиса:</th>
						<td><?=$BasketInfo->DeliveryApartment?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->DeliveryTime)){
?>
					<tr>
						<th>Время доставки:</th>
						<td><?=$DeliveryTimes[$BasketInfo->DeliveryTime]?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->PayType)){
?>
					<tr>
						<th>Метод оплаты:</th>
						<td><?=$BasketInfo->PayType=="CASH"?"Наличными":"Предоплата"?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->PayMethod)){
?>
					<tr>
						<th>Способ оплаты:</th>
						<td><?=$PayMethods[$BasketInfo->PayMethod]?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->INN)){
?>
					<tr>
						<th>ИИН:</th>
						<td><?=$BasketInfo->INN?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->BIN)){
?>
					<tr>
						<th>БИН:</th>
						<td><?=$BasketInfo->BIN?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Address)){
?>
					<tr>
						<th>Адрес:</th>
						<td><?=$BasketInfo->Address?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Name)){
			$Name=array();
			if(!empty($BasketInfo->SurName))
				$Name[]=$BasketInfo->SurName;
			if(!empty($BasketInfo->Name))
				$Name[]=$BasketInfo->Name;
			if(!empty($BasketInfo->SecondName))
				$Name[]=$BasketInfo->SecondName;
			$Name=join(" ",$Name);
?>
					<tr>
						<th>Контактное лицо:</th>
						<td><?=$Name?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->EMail)){
?>
					<tr>
						<th>EMail:</th>
						<td><?=urldecode($BasketInfo->EMail)?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Phone)){
?>
					<tr>
						<th>Телефон:</th>
						<td><?=$BasketInfo->Phone?></td>
					</tr>
<?php
		}
if($BasketInfo->TypeBasket=="B"){
?>
					<tr>
						<th></th>
						<td>
							<strong>
<?php
		if($BasketInfo->DeliveryAddressSame=="Y"){
?>
								Адрес доставки совпадает с юридическим адресом
<?php
		}else{
?>
								Адрес доставки отличается от юридического адреса
<?php
		}
?>
							</strong>
						</td>
					</tr>
<?php
}
		if(!empty($BasketInfo->ShortOrganization)){
?>
					<tr>
						<th>Короткое название компании:</th>
						<td><?=$BasketInfo->ShortOrganization?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Organization)){
?>
					<tr>
						<th>Полное название компании:</th>
						<td><?=$BasketInfo->Organization?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->CompanyIIN)){
?>
					<tr>
						<th>ИИН компании:</th>
						<td><?=$BasketInfo->CompanyIIN?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->KPP)){
?>
					<tr>
						<th>КПП:</th>
						<td><?=$BasketInfo->KPP?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->OKPO)){
?>
					<tr>
						<th>ОКПО:</th>
						<td><?=$BasketInfo->OKPO?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->BIK)){
?>
					<tr>
						<th>БИК:</th>
						<td><?=$BasketInfo->BIK?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->PaymentBank)){
?>
					<tr>
						<th>Банк плательщика:</th>
						<td><?=$BasketInfo->PaymentBank?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->CorrespondentAccount)){
?>
					<tr>
						<th>Корреспондентский счет:</th>
						<td><?=$BasketInfo->CorrespondentAccount?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->IIN)){
?>
					<tr>
						<th>ИИН:</th>
						<td><?=$BasketInfo->IIN?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Index)){
?>
					<tr>
						<th>Индекс:</th>
						<td><?=$BasketInfo->Index?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Region)){
?>
					<tr>
						<th>Регион:</th>
						<td><?=$BasketInfo->Region?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->District)){
?>
					<tr>
						<th>Район:</th>
						<td><?=$BasketInfo->District?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->City)){
?>
					<tr>
						<th>Город:</th>
						<td><?=$BasketInfo->City?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Street)){
?>
					<tr>
						<th>Улица:</th>
						<td><?=$BasketInfo->Street?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->House)){
?>
					<tr>
						<th>Номер дома:</th>
						<td><?=$BasketInfo->House?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Hull)){
?>
					<tr>
						<th>Корпус:</th>
						<td><?=$BasketInfo->Hull?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Office)){
?>
					<tr>
						<th>Номер офиса/квартиры:</th>
						<td><?=$BasketInfo->Office?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->Building)){
?>
					<tr>
						<th>Строение:</th>
						<td><?=$BasketInfo->Building?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->PaymentAccount)){
?>
					<tr>
						<th>Расчетный счет:</th>
						<td><?=$BasketInfo->PaymentAccount?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalIndex)){
?>
					<tr>
						<th>Индекс:</th>
						<td><?=$BasketInfo->LegalIndex?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalRegion)){
?>
					<tr>
						<th>Регион:</th>
						<td><?=$BasketInfo->LegalRegion?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalCity)){
?>
					<tr>
						<th>Город:</th>
						<td><?=$BasketInfo->LegalCity?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalDistrict)){
?>
					<tr>
						<th>Район:</th>
						<td><?=$BasketInfo->LegalDistrict?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalStreet)){
?>
					<tr>
						<th>Улица:</th>
						<td><?=$BasketInfo->LegalStreet?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalHouse)){
?>
					<tr>
						<th>Номер дома:</th>
						<td><?=$BasketInfo->LegalHouse?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalHull)){
?>
					<tr>
						<th>Корпус:</th>
						<td><?=$BasketInfo->LegalHull?></td>
					</tr>
<?php
		}
		if(!empty($BasketInfo->LegalOffice)){
?>
					<tr>
						<th>Номер офиса/квартиры:</th>
						<td><?=$BasketInfo->LegalOffice?></td>
					</tr>
<?php
		}
		if($BasketInfo->TypeBasket=="F"){
?>
			<tr>
				<th>Брендц:</th>
				<td><?=$AllBrands[$BasketInfo->FundsBrand]->Title?></td>
			</tr>
			<tr>
				<th>Кол-во единиц:</th>
				<td><?=$AllSimPackages[$BasketInfo->FundsBrand][$BasketInfo->PackageId]->Units?></td>
			</tr>
			<tr>
				<th>Телефон отправителя:</th>
				<td><?=$BasketInfo->FundsFrom?></td>
			</tr>
			<tr>
				<th>Телефон получателя:</th>
				<td><?=$BasketInfo->FundsTo?></td>
			</tr>
			<tr>
				<th></th>
				<td></td>
			</tr>
<?php
		}
?>
				</tbody>
			</table>
			<table class="table">
				<colgroup>
					<col />
					<col width="40%" />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th colspan="10">Корзина</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($BasketInfo->Items as $Item){
			$Kernel->PrintBasketAdminItem($Item);

			unset($Item);
		}
?>
				</tbody>
			</table>
		</div>
	</section>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>