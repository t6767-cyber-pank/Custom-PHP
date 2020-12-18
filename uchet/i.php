<?php    
 /* CAT:Bar Chart */ 

 /* pChart library inclusions */ 
 include("class/pData.class.php"); 
 include("class/pDrawEzhProdag.class.php");
 include("class/pImage.class.php");
/* Определяем корень*/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
/* Убираем предупреждения*/
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

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
/* Ловим дату начала и дату конца и город*/
$dateNachala=$_GET["dateN"];
$dateKonca=$_GET["dateK"];
$cityid=(int)$_GET["city"];

//echo $dateNachala."    ".$dateKonca."      ---".$_GET["dateK"];


include("$DOCUMENT_ROOT/mysql_connect.php"); // Подключаем модуль подключения к базе данных
$tovar = array();
$dates = array();
$summaCount=0;
$dateObrabotka=strtotime($dateNachala); // sum(p.price*t.number)
$zap = mysql_query("SELECT * FROM pr_tovar where pokaz>0 order by name");
while ($azap = mysql_fetch_array($zap))
{
    $citystring="";
    if ($cityid>0) { $citystring=" and c.id=".$cityid." "; }
    $qzap = mysql_query("SELECT p.name, sum(t.number) as ttt FROM `pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c WHERE o.id=t.id_order ".$citystring." and t.id_tovar=p.id and t.id_tovar=".$azap['id']." and o.id_city=c.id and o.dt BETWEEN '$dateNachala' and '$dateKonca' group by p.name ORDER BY ttt DESC");
$a = mysql_fetch_array($qzap);
    $x=0;
    $x=$x+$a['ttt'];
 //   $summaCount=$summaCount+$a['ttt'];
    array_push($tovar, $x);  //  Кидаем в массив чата данные с базы
    array_push($dates, $azap['name']);
}

array_multisort($dates,SORT_NUMERIC, SORT_DESC, $tovar);
$tovar1=array_reverse ($tovar);
$dates1=array_reverse ($dates);

 $MyData = new pData();
 $MyData->addPoints($tovar1,"Все города");

 $MyData->setAxisName(0,"");
 $MyData->addPoints($dates1,"Months");
 $MyData->setSerieDescription("Months","Month"); 
 $MyData->setAbscissa("Months");
 /* Create the pChart object */
 $myPicture = new pImage(1200,2030,$MyData);

 /* Turn of Antialiasing */ 
 $myPicture->Antialias = FALSE; 

 /* Add a border to the picture */ 

 /* Set the default font */ 
 $myPicture->setFontProperties(array("FontName"=>"fonts/arial.ttf","FontSize"=>6));

 /* Define the chart area */ 
 $myPicture->setGraphArea(220,40,1150,2000);

 /* Draw the scale */ 
 $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE, "Mode"=>SCALE_MODE_FLOATING, "Pos"=>SCALE_POS_TOPBOTTOM); //, SCALE_MODE_MANUAL , "Pos"=>SCALE_POS_TOPBOTTOM
 $myPicture->drawScale($scaleSettings);

 $myPicture->setFontProperties(array("FontName"=>"fonts/arialbd.ttf","FontSize"=>9));
 //$myPicture->drawLegend(580,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Turn on shadow computing */  
// $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the chart */ 
 $settings = array("Surrounding"=>-30,"InnerSurrounding"=>30, "DisplayValues"=>1, AroundZero=>TRUE);
 $myPicture->drawBarChart($settings);

$myPicture->setFontProperties(array("FontName"=>"fonts/arialbd.ttf","FontSize"=>12));
// Задаем шрифт и размер легенде
$TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT
, "R"=>0, "G"=>0, "B"=>0, "DrawBox"=>0, "BoxAlpha"=>130);

$razmerholstaText=70;
//$myPicture->drawText(25,10,"Период: ".date("d.m.Y", strtotime($dateNachala))."-".date("d.m.Y", strtotime($dateKonca)),$TextSettings);
 /* Render the picture (choose the best way) */ 
 $myPicture->autoOutput("pictures/example.drawBarChart.simple.png"); 
?>