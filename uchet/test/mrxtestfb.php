<?php
$id='353793348848469';
$secret='e635c8507dada89be957936cde8b0a5a';
$url="http://localhost/mrx.php";
$access_token="EAAFBxe7hy1UBAJOCv6FzRHmO5fOdcG5fesRdUDzziE48tqBqRxGYUn5ZAZCr9JPgsYcM6scN00Q2bftcybQiBHrC4wENCwCoJqT2KmCZAmmfsVrk17SgIFeyu58vQ0OmYiLNfp73EA8oT1wmblEWr8qGldaQ3j1gmXW815VEwZDZD";
$tolkUser="353793348848469|ix_rM4uKCc17VCZADsthNGAPQh4";
?>
<a target="_blank" href="https://www.facebook.com/v4.0/dialog/oauth?client_id=<?=$id ?>&redirect_uri=<?=$url ?>&response_type=code&scope=public_profile, email, user_location">fb</a>
<?php if (!$_GET['code']) echo "error code"; else echo "<p>".$_GET['code']."</p>";
$tolkin=json_decode(file_get_contents("https://graph.facebook.com/v4.0/oauth/access_token?client_id=$id&redirect_uri=$url&client_secret=$secret&code=".$_GET['code']),true);

if(!$tolkin) echo "error tolkin";
var_dump($tolkin);

$data=json_decode(file_get_contents("https://graph.facebook.com/v4.0/me?client_id=$id&redirect_uri=$url&client_secret=$secret&code=".$_GET['code']."&access_token=".$tolkin['access_token']."&fields=id,name,gender,location"),true);
if(!$data) echo "error data";
$data['avatar']="https://graph.facebook.com/v4.0/".$data['id']."/picture?width=200&height=200";
var_dump($data);

echo "<br>77777777777777777777777<br>";

$data=json_decode(file_get_contents("https://graph.facebook.com/v4.0/23843664055090379/insights?&access_token=".$tolkin['access_token']."&date_preset=today&level=campaign&columns=name"),true);
if(!$data) echo "error data";
var_dump($data);
/*
-d "date_preset=last_7_days" \
-d "access_token=<ACCESS_TOKEN>" \
"https://graph.facebook.com/<API_VERSION>/<AD_CAMPAIGN_ID>/insights"
*/
?>