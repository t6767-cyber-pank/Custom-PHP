<?php
/*
$languages = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp");
foreach ($languages->Valute as $lang) {
if ($lang["ID"] == 'R01335') { //тип валюты
$koeficient1 = round(str_replace(',','.',$lang->Value), 2); //ее значение
$koeficient1a = $lang->Nominal.' '.$lang->Name.' = '.$koeficient1.' руб.'; //запоминаем номинал
$ktip=(float)$lang->Nominal/(float)$koeficient1;
} }
echo $koeficient1a;
echo "<br>".$ktip;
*/
chdir(dirname(__FILE__));
include './mysql_connect.php';
$html = file_get_contents('http://www.mig.kz/');
//var_dump($html);
preg_match_all( '#<td class="sell delta-[a-z]+">(.+?)</td>#is', $html, $matches );
preg_match_all( '#<td class="currency">(.+?)</td>#is', $html, $matches2 );
//sell delta-positive
//sell delta-negative
preg_match_all( '#<td class="sell delta-[a-z]+">(.+?)</td>#is', $html, $matches3 );

$i=0;

//print_r($matches);
//print_r($matches2);

foreach ( $matches2[1] as $value )
{
echo "<br>".$value;	
if ($value=="RUB")
{   echo "  ".$matches[1][$i];
    $dt=date("Y-m-d");
    mysql_query("insert into cron_test(date, course) values('$dt', '".$matches[1][$i]."')");
    echo "insert into cron_test(date, course) values('$dt', '".$matches[1][$i]."')<br>";
}
$i++;
}

//print_r($matches3);
?>