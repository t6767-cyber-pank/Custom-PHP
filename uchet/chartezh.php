<?php
include("class/pData.class.php");
include("class/pDrawEzh2.class.php");
include("class/pImage.class.php");
// Подключение классов диаграмм pChart
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT']; // Определяем корень
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE); // Убираем предупреждения

$dateNachala=date("Y-m-d", strtotime($_GET["dateN"]));
$dateKonca=date("Y-m-d", strtotime($_GET["dateK"]));
$city=$_GET["city"];

$dn=strtotime($dateNachala);
$dk=strtotime($dateKonca);

switch (date("D", strtotime($dateNachala)))
{
	case "Mon" : $dn=$dn-0; break;
	case "Tue" : $dn=$dn-86400; break;
	case "Wed" : $dn=$dn-86400*2; break;
	case "Thu" : $dn=$dn-86400*3; break;
	case "Fri" : $dn=$dn-86400*4; break;
	case "Sat" : $dn=$dn-86400*5; break;
	case "Sun" : $dn=$dn-86400*6; break;	
}


switch (date("D", strtotime($dateKonca)))
{
    case "Mon" : $dk=$dk+86400*6; break;
    case "Tue" : $dk=$dk+86400*5; break;
    case "Wed" : $dk=$dk+86400*4; break;
    case "Thu" : $dk=$dk+86400*3; break;
    case "Fri" : $dk=$dk+86400*2; break;
    case "Sat" : $dk=$dk+86400; break;
    case "Sun" : $dk=$dk+0; break;
}

function selmonth($x){
    $return="";
	switch($x)
	{
case "01" : $return="Янв"; break;	
case "02" : $return="Фев"; break;
case "03" : $return="Март"; break;
case "04" : $return="Апр"; break;
case "05" : $return="Мая"; break;
case "06" : $return="Июня"; break;
case "07" : $return="Июля"; break;
case "08" : $return="Авг"; break;
case "09" : $return="Сент"; break;
case "10" : $return="Окт"; break;
case "11" : $return="Нояб"; break;
case "12" : $return="Дек"; break;
	}
	return $return;
  }

$dateNachala=date("Y-m-d",$dn);
/* Дата начала понедельник на сегодня 2019-02-11 2019-04-17 */
$dateNachalaVoskreseniya=date("Y-m-d",$dn-86400);
$dateKonca=date("Y-m-d",$dk);
$chatOriginal="";
$chatPrev="";

$dateObrabotka=strtotime($dateNachala);

include("$DOCUMENT_ROOT/mysql_connect.php"); // Подключаем модуль подключения к базе данных

$r = mysql_query("SELECT * FROM `ezh_city_day` d, ezh_city m where d.id_city=m.id and m.name='".$city."' and dt='".$dateNachalaVoskreseniya."' order by m.name, `dt`");
while ($a = mysql_fetch_array($r))  // Переборка данных с базы в данные приемлимые обработке
{
    $chatOriginal=$a['contacts'];
	$chatPrev=$chatOriginal;
}
$chats = array(); // Пустой массив для сбора данных о чатах
$dates = array(); // Пустой массив для сбора данных о датах
$rashodi = array(); // Пустой массив для сбора данных о чатах
$sborvnedelyu=0;
$sborvnedelyuRash=0;
$summaChat=0;
$summaRash=0;

$dateNachalaSQLZap="";
$dateKoncaSQLZap="";
while ($dateObrabotka!=strtotime($dateKonca)+86400)
{
    if (date("D",$dateObrabotka)=="Mon")
    {
        $dateNachalaSQLZap=date("Y-m-d",$dateObrabotka);
        $dateKoncaSQLZap=date("Y-m-d",$dateObrabotka+86400*6);
        $qzap = mysql_query("SELECT * FROM `ezh_city_day` d, ezh_city m where d.id_city=m.id and m.name='".$city."' and dt BETWEEN '".$dateNachalaSQLZap."' AND '".$dateKoncaSQLZap."' order by `dt`");
        while ($a = mysql_fetch_array($qzap))
        {
            $contactsbd=(int)$a['contacts'];
			if ($chatOriginal=="") {$chatOriginal=$chatPrev;} else { $chatPrev=$chatOriginal; }
			if ($chatOriginal<=0) {$chatOriginal=$chatPrev;}
            $push=0;
            if ($contactsbd<1) {$push=0; $chatOriginal=$chatPrev; } else { $push=(int)$a['contacts']-(int)$chatOriginal; $chatOriginal=$a['contacts']; }
            $sborvnedelyu=$sborvnedelyu+$push;
            $summaChat=$summaChat+$push;
        }
    }

    $qzapRash = mysql_query("SELECT * FROM ezh_city_week ev, ezh_city m where ev.id_city=m.id and m.name='".$city."' and dt='".date("Y-m-d",$dateObrabotka)."'");
    $arash = mysql_fetch_array($qzapRash);
    $sborvnedelyuRash=$sborvnedelyuRash+(int)$arash['outcome'];

    if (date("D",$dateObrabotka)=="Sun")
    {
        array_push($chats, $sborvnedelyu);  //  Кидаем в массив чата данные с базы
        if ($sborvnedelyuRash>0) { $sborvnedelyuRashVBd=$sborvnedelyuRash/1000; } else { $sborvnedelyuRashVBd=0; }
        array_push($rashodi, $sborvnedelyuRashVBd);
        array_push($dates, date("d", strtotime($dateKoncaSQLZap))." ".selmonth(date("m", strtotime($dateKoncaSQLZap)))); // Кидаем в массив дат даты с базы
        $summaRash=$summaRash+$sborvnedelyuRash;
        $sborvnedelyu=0;
        $sborvnedelyuRash=0;
    }
    $dateObrabotka=$dateObrabotka+86400;
}

$razmerPodgon=count($chats); // Узнаем количество записей чтобы динамически формировать размер рисунка

$razmerholsta=900; //$razmerPodgon*90; // Умножаем количество записей на количество пикселей для отображения
$visotaholsta=300; // 350
$myData = new pData();
$myData->addPoints($chats,"TTT");
$myData->addPoints($rashodi,"rashod");

$myData->addPoints($dates,"Absissa");
$myData->setAbscissa("Absissa");

$myPicture = new pImage($razmerholsta,$visotaholsta,$myData);
// размер картинки
//$Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>1, "DashR"=>275, "DashG"=>275, "DashB"=>275);
//$myPicture->drawFilledRectangle(0,0,$razmerholsta,300,$Settings);
// Размер картинки

//$Settings = array("StartR"=>255, "StartG"=>255, "StartB"=>255, "EndR"=>255, "EndG"=>255, "EndB"=>255, "Alpha"=>50);
//$myPicture->drawGradientArea(0,0,$razmerholsta,300,DIRECTION_VERTICAL,$Settings);
// размер картинки 
//$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

$myPicture->setFontProperties(array("FontName"=>"fonts/arialbd.ttf","FontSize"=>12));
// Задаем шрифт и размер легенде
$TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT
, "R"=>0, "G"=>0, "B"=>0, "DrawBox"=>0, "BoxAlpha"=>130);

$razmerholstaText=70;
$myPicture->drawText(10,5,"Чатов: ".$summaChat."  Расходы: ".$summaRash,$TextSettings);
// размер картинки положение текста заголовка

//$myPicture->setShadow(FALSE);

$razmerholstaArea=$razmerholsta-25;
$myPicture->setGraphArea(-1,50,$razmerholstaArea,$visotaholsta-16);
// размер изображения -25px
$myPicture->setFontProperties(array("R"=>130,"G"=>130,"B"=>130,"FontName"=>"fonts/arial.ttf","FontSize"=>8));

/* Цвет и настройки палок пунктирных */
$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_MANUAL //SCALE_MODE_START0
, "LabelingMethod"=>LABELING_ALL, MinDivHeight=>1, XMargin=>30,  YMargin=>0, DrawXLines=>FALSE, AxisR=>255 , AxisG=>255 , AxisB=>255 // RemoveXAxis=>TRUE убрать абсцису
, "LabelSkip"=>0, "GridR"=>127, "GridG"=>127, "GridB"=>127, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>0, "LabelRotation"=>0, "DrawArrows"=>0, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>0, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>NONE);
$myPicture->drawScale($Settings);
/* Цвет и настройки палок пунктирных */

//$Config = array("DisplayValues"=>0, "PlotSize"=>5, "PlotBorder"=>0, "BorderSize"=>0);
//$myPicture->drawPlotChart($Config);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>155,"G"=>187,"B"=>89,"Alpha"=>50)); // #9BBB59;

// Рисуем чат
$Config = array("DisplayValues"=>1, DisplayOffset=>17, AroundZero=>TRUE);
$myPicture->drawAreaChart($Config);
//$myPicture->drawLineChart($Config);
//$Config = array("FontR"=>255, "FontG"=>0, "FontB"=>0, "FontName"=>"fonts/arial.ttf", "FontSize"=>0, "Margin"=>0, "Alpha"=>100, "BoxSize"=>0, "Style"=>LEGEND_ROUND
//, "Mode"=>LEGEND_HORIZONTAL
//);
//$razmerholstaLegend=$razmerholsta-83;
//$myPicture->drawLegend($razmerholstaLegend,16,$Config);
// размер изображения -63
$myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>155,"G"=>187,"B"=>89,"Alpha"=>50)); // #9BBB59;
$Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>0, "DashR"=>255, "DashG"=>255, "DashB"=>255, "Alpha"=>50, "DashAlpha"=>0);

$minus=104; 
if ($razmerPodgon==4){ $minus=325; }
if ($razmerPodgon==5){ $minus=258; }
if ($razmerPodgon==6){ $minus=216; } 
if ($razmerPodgon==7){ $minus=190; } 
if ($razmerPodgon==8){ $minus=170; } 
if ($razmerPodgon==9){ $minus=155; }
if ($razmerPodgon==10){ $minus=145; } 
if ($razmerPodgon==11){ $minus=135; } 
if ($razmerPodgon==12){ $minus=127; }
if ($razmerPodgon==13){ $minus=122; }
if ($razmerPodgon==14){ $minus=116; }
if ($razmerPodgon==15){ $minus=111; }
if ($razmerPodgon==16){ $minus=107; }
if ($razmerPodgon==17){ $minus=105; }
$myPicture->drawFilledRectangle($razmerholsta-$minus,284,$razmerholsta-57,50,$Settings);


$myPicture->stroke();
?>