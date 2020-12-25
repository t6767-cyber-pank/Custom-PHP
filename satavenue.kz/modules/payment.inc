<?php
require_once("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/kkb.utils.php");
$self=$_SERVER["PHP_SELF"];
$path1="/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/config.txt";
$result=0;
function xmlAttribute($object, $attribute){
	if(isset($object[$attribute]))
		return (string) $object[$attribute];
	else
		return null;
}

ini_set("display_errors",1);
error_reporting(E_ALL);
ini_set("error_log","/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/tmp/log.log");
$payCheck=false;
/*
?>
<form action="/payed/" method="post">
	<textarea name="response"><?=isset($_POST["response"])?$_POST["response"]:""?></textarea>
	<input type="submit" value="submit" />
</form>
<?php
*/
if(isset($_POST["response"])){$response=$_POST["response"];}else{$response="[NO DATA FROM KKB]";}
$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Test} SET `Data`='result: {$response}'");
$result=process_response(stripslashes($response),$path1);
if(is_array($result)){
	if(in_array("ERROR",$result)){
		if($result["ERROR_TYPE"]=="ERROR"){
			$string="System error:".$result["ERROR"];
		}elseif($result["ERROR_TYPE"]=="system"){
			$string="Bank system error > Code: ".$result["ERROR_CODE"]." Text: ".$result["ERROR_CHARDATA"]." Time: ".$result["ERROR_TIME"]." Order_ID: ".$result["RESPONSE_ORDER_ID"]."";
		}elseif($result["ERROR_TYPE"]=="auth"){
			$string="Bank system user autentication error > Code: ".$result["ERROR_CODE"]." Text: ".$result["ERROR_CHARDATA"]." Time: ".$result["ERROR_TIME"]." Order_ID: ".$result["RESPONSE_ORDER_ID"]."";
		}
		$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Test} SET `Data`='error: {$string}'");
	}
	if(in_array("DOCUMENT",$result)){
		foreach($result as $key=>$value){
			if($key=="CHECKRESULT"){
				$orderId=(int)$result["ORDER_ORDER_ID"];
				$str=$orderId."=>".$value;
				$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Test} SET `Data`='check: {$str}'");
				if($value=="[SIGN_GOOD]"){
					$payCheck=true;
				}
			}
		}
	}
}else{$string="System error".$result;}
$DataBase->InsertQuery("INSERT INTO {$GLOBAL->Test} SET `Data`='{$result}'");
$responseDataFull=$responseData=simplexml_load_string($response);
$result=join("||",$result);
//$responseDataFull=$responseData=simplexml_load_string('<document><bank name="Kazkommertsbank JSC"><customer name="SOME NAME" mail="daemonhk@mail.ru" phone=""><merchant cert_id="00C182B189" name="null"><order order_id="000233" amount="1500" currency="398"><department merchant_id="92061101" amount="1500"/></order></merchant><merchant_sign type="RSA"/></customer><customer_sign type="RSA"/><results timestamp="2015-05-26 09:48:23"><payment merchant_id="92061101" card="440564-XX-XXXX-6150" amount="1500" reference="150526094823" approval_code="094823" recur="1" response_code="00" Secure="Yes" card_bin="KAZ" c_hash="13988BBF7C6649F799F36A4808490A3E"/></results></bank><bank_sign cert_id="00C18327E8" type="SHA/RSA">FiQMzQSPcRFMxix8V/ZH8f0vA6y4pWGCS0JesclVOaPalLLJ8nYMecOAxqITRuqoV3BneQ0yGAWB75NZArKBWsdrM7k8m1zL1pONe3rRGL4PrV0Y3Cgbb2SWQzBDBJ7Wtp5OR3QHAdb4bspyabhhs3ipy1Xd9lIzk/WM63y5Abc=</bank_sign></document>');
$customerEmail=$responseData->bank->customer->attributes()->mail;
$customerEmail=(array)$customerEmail;
$customerEmailUn=$customerEmail[0];
$customerEmail=urlencode($customerEmail[0]);
$orderId=$responseData->bank->customer->merchant->order->attributes()->order_id;
$orderAmount=$responseData->bank->customer->merchant->order->attributes()->amount;
$orderId=(int)$orderId;
//$responseData=(array)$responseData->bank_sign;
$sign=$responseData->bank_sign;
$xml=$responseDataFull;
$kkb=new KKBSign();
$kkb->invert();
if(!$kkb->load_private_key("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/cert.prv","WDfUveEf9i3")){
//if(!$kkb->load_private_key("/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/test/cert.prv","1q2w3e4r")){
	if($kkb->ecode>0){return $kkb->estatus;}else{}
}
$sign=$kkb->sign($xml);
$sign=base64_decode($sign);
$trueSign=$kkb->check_sign($xml, $sign, "/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/kkbca.pem");
//$trueSign=$kkb->check_sign($xml, $sign, "/var/www/vhosts/13/107443/webspace/httpdocs/iec-telecom.kz/pay/test/kkbca_test.pub");
if($payCheck){
	$string=$responseDataFull;
	$datePay=time();
	$OrderInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->Baskets} WHERE `Id`={$orderId}",false);
	$DataBase->UpdateQuery("UPDATE {$GLOBAL->Baskets} SET `PayDateTime`={$datePay},`Status`='P',`PayMethod`='ONLINE' WHERE `Id`={$orderId}");

	$EMail=urldecode($OrderInfo->EMail);
	//send mail for user and client
}
return 0;