<?php
$dt=date("Y-m-d");
$dt_from=date("Y-m-d", strtotime("-10 days"));
require __DIR__ .'/vendor/autoload.php';
use VK\Client\VKApiClient;
$vk = new VKApiClient();

$response = $vk->ads()->getStatistics("xxxxxxxxxxxxxxxxxxxxxxxxxxxx", array(
    'account_id' => '1900002437',
    'ids_type' => 'campaign',
    'ids' => array('1012887015'),
    'period' => 'day',
    'date_from' => $dt_from,
    'date_to' => $dt
));

$response2 = $vk->groups()->getById("xxxxxxxxxxxxxxxxxxxx", array(
    'group_ids' => array('24390135'),
    'fields' => array('members_count')
));
include ('mysql_connect7.php');
$xart=array();
$qX777=mysql_query("select * from vk_usersezh");
while ($xvkus777= mysql_fetch_array($qX777))
{
    array_push($xart, $xvkus777['id_vkuser']);
}

$namec="Алматы";
$mc=1;
$gid=24390135;
$mcount=0;
$tolik="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$vkidcity="1012887015";
//foreach ($response as $res)
//{
//    $vkidcity=$res['id'];
/*    switch ($res['id'])
    {
        case    1012705297: $namec="Балашиха"; $mc=54;          $gid='183304894'; $tolik="bc668f8091a732395f8dd84ac1136693414c679439b855ddc0c11e54f4d9f187dc98ae05a0a899d227ceb"; break;
        case    1012705067: $namec="Караганда татуаж"; $mc=49;  $gid='183642350'; $tolik="fa1ab446e9bb9eaa70f54279dbcc8eae406a136175d427d8608e1c457724952bfed298b9b317dd0ddcb73"; break;
        case    1012644516: $namec="Барнаул"; $mc=11;           $gid='183642339'; $tolik="c9186fd44164b93a14629bb66185b5bfcb3b9bb0edcb4fe770edd9654869eade4d4b75d6091196ecf39b6"; break;
        case    1012644308: $namec="Томск"; $mc=10;             $gid='183642308'; $tolik="6797da8b4f3efa24561f3a5b44c048ff28dbda036599c9909941e5bf0a15ba167edd584594e937f564620"; break;
        case    1012631446: $namec="Волгоград"; $mc=13;         $gid='183642330'; $tolik="2b00e68880ff4b9de38bfbaaf614568bdb3e6fce272672ee41d878c1dc07c4bba9c3010b81305cf137678"; break;
        case    1012596378: $namec="Иркутск";   $mc=9;          $gid='183547014'; $tolik="04c6b6f605583675c2192e618aef5d9cf4d9f79f417c56db40f997e3fa62769e253653f13fd08e0ce075d"; break;
        case    1012589857: $namec="Красноярск"; $mc=8;         $gid='183547048'; $tolik="dd068c1b79d8ba8be52be3612643fa8edbcaa31dcb8444a58af81b54402afabd2c83a9887cf3e7e574d45"; break;
        case    1012555356: $namec="РнД"; $mc=16;               $gid='183379477'; $tolik="97e2eebd97be35c92fda07cd44ec3d740bec56611e20042cae8a7a7ebfc786ca348903e689fe442500005"; break;
        case    1012530143: $namec="Алматы"; $mc=1;             $gid='183404979'; $tolik="1d156725bcb811b1d06a25107dfa7417eeb246ae4a2dc059a4dedb59f3e55239ed9a19474bcc6b7c7b74b"; break;
        case    1012524055: $namec="ЕКБ"; $mc=6;                $gid='183164469'; $tolik="b6651ac2082c2cba639950e4d5714e632b356bb656829ba92d111d57b1165b594a0661954e79a6646f840"; break;
        case    1012497203: $namec="Казань"; $mc=7;             $gid='168390075'; $tolik="44ff987c0bd8c282385580cbcb3d59a31cbb94d97abaf9be5ed2ab23402b4fcd7dc68c3cd49023b59820c"; break;
        case    1012454829: $namec="НСК"; $mc=5;                $gid='182530795'; $tolik="e13538cc1d4e98048a74f116026bc5516661000873236dacb9a2eb2b16d7f22706a67d83c8d6fd99bab87"; break;
        case    1012744437: $namec="Алматы косметолог"; $mc=17; $gid='184380083'; $tolik="49189692d1ee6338e02c167be774e889f42c9d27ca3203fed1e354a168ee2f43b501e0a0cac6b40a2b4fa"; break;
    }
*/
    $responseX = $vk->messages()->getConversations($tolik, array(
        'group_id' => array($gid),
        'count' => 200,
        'extended'=> 1
    ));

    foreach ($responseX["items"] as $resX)
    {
        $idvkuserX=$resX["last_message"]["peer_id"];
        if (in_array($idvkuserX, $xart)) {
            }
         else {
            mysql_query("insert into vk_usersezh(id_vkuser, id_vkgroup, dt, uniq, id_vkcity, id_city) values($idvkuserX, $gid, '$dt', 1, $vkidcity, $mc)");
             echo "<b> новый пользователь".$idvkuserX." город $namec</b>     ".$resX["last_message"]["text"]."<br>";
        }
    }

    foreach ($response2 as $res2)
    {
        if ($gid==$res2["id"]) {
            $mcount=$res2["members_count"];

            $q=mysql_query("select * from cityimportvkezh where vkidcity=$vkidcity and data='$dt'");
            $xfact= mysql_fetch_array($q);
            if ((int)$xfact['id_mcity'] > 0) {

                if ($xfact['userg']!=$mcount) {
                    mysql_query("update cityimportvkezh set gid=$gid, userg=$mcount where vkidcity=$vkidcity and data='$dt'");
                    echo "<b>users</b> update $namec($vkidcity) $dt $mcount<br>";
                }
            } else {
                mysql_query("insert into cityimportvkezh(id_mcity, data, outcome, vkidcity, gid, userg) values($mc, '$dt', 0, $vkidcity, $gid, $mcount)");
                echo "<b>users</b> insert $namec($vkidcity) $data $mcount<br>";
            }
        }
    }

    foreach ($response[0]['stats'] as $st)
    {
        $data=$st['day'];
        $outcome=(float)$st['spent'];
        $q=mysql_query("select * from cityimportvkezh where vkidcity=$vkidcity and data='$data'");
        $xfact= mysql_fetch_array($q);
            if ((int)$xfact['id_mcity'] > 0) {

                if ($xfact['outcome'] != $outcome) {
                    mysql_query("update cityimportvkezh set outcome=$outcome where vkidcity=$vkidcity and data='$data'");
                    echo "<b>outcome</b> update $namec($vkidcity) $data $outcome <br>";
                }
            } else {
                mysql_query("insert into cityimportvkezh(id_mcity, data, outcome, vkidcity) values($mc, '$data', $outcome, $vkidcity)");
                echo "<b>outcome</b> insert $namec($vkidcity) $data $outcome <br>";
            }
    }

?>
