<?php

/* подключаем модули компонента pchart*/
chdir(dirname(__FILE__));
require_once("./class/pData.class.php");
require_once("./class/pDraw.class.php");
require_once("./class/pImage.class.php");



class diagrams extends msqlwork
{
    public $m_city_day;
    public $m_city_dayVK;
    public $masters;
    public $master_week;
    public $access;
    function __construct($dt, $td_to)
    {
    $this->set_dt($dt);
    $this->set_dt($this->get_monday());
    $timereal=new timereal();
    $timereal->set_dt($td_to);
    $this->set_dt_to($timereal->get_sunday());
    $this->m_city_day=new m_city_day();
    $this->m_city_day->set_dt($this->dt);
    $this->m_city_day->set_dt_to($this->dt_to);
    $this->m_city_dayVK=new m_city_day_vk();
    $this->m_city_dayVK->set_dt($this->dt);
    $this->m_city_dayVK->set_dt_to($this->dt_to);
    $this->masters=new masters();
    $this->masters->set_dt($this->dt);
    $this->masters->set_dt_to($this->dt_to);
    $this->master_week=new master_week();
    $this->master_week->set_dt($this->dt);
    $this->master_week->set_dt_to($this->dt_to);
    }

    function getAllCityes()
    {
        $city=$this->masters->allCitiesUseInst();
        $acity=array();
        foreach ($city as $c)
        {
            $this->m_city_day->set_dt($this->dt);
            $this->m_city_day->set_dt_to($this->dt_to);
            if ($this->getChatCountInst($c[0])>3) {
                array_push($acity, $c);
            }
        }
        return $acity;
    }

    function getAllCitiesVK()
    {
        $city=$this->masters->allCitiesUseVKOSort();
        $acity=array();
        foreach ($city as $c)
        {
            $this->m_city_dayVK->set_dt($this->dt);
            $this->m_city_dayVK->set_dt_to($this->dt_to);
            if ($this->getChatCountVK($c[0])>3) {
                array_push($acity, $c);
            }
        }
        return $acity;
    }

    function getChatCountInst($cid)
    {
        return $this->m_city_day->chatsCount($cid);
    }

    function getChatCountVK($cid)
    {
        return $this->m_city_dayVK->chatsCount($cid);
    }

    // Выдас массив всех чаты за период
    function getallChats($cid)
    {
        $chatsOutput=array();
        $date=$this->arraydatesByDW(7);
        foreach ($date as $dt)
        {
            $this->m_city_day->set_dt($this->get_mondayPar($dt));
            $this->m_city_day->set_dt_to($this->get_sundayPars($dt));
            $charts=$this->m_city_day->chats($cid);
            $original=(int)$this->m_city_day->chatsoriginal($cid);
            $sumchats=0;
            foreach ($charts as $chart)
            {
                    $sumchats=$sumchats+$chart['lidfit']+((int)$chart['chats']-$original);
                    $original=$chart['chats'];
            }
            array_push($chatsOutput, $sumchats);
        }

        return $chatsOutput;
    }

    // Выдас массив всех чаты за период
    function getallChatsVK($cid)
    {
        $chatsOutput=array();
        $date=$this->arraydatesByDW(7);
        foreach ($date as $dt)
        {
            $this->m_city_dayVK->set_dt($this->get_mondayPar($dt));
            $this->m_city_dayVK->set_dt_to($this->get_sundayPars($dt));
            $charts=$this->m_city_dayVK->chatsInterval($cid, $this->m_city_dayVK->dt, $this->m_city_dayVK->dt_to);
            array_push($chatsOutput, (int)$charts);
        }

        return $chatsOutput;
    }


    // Выдас всех чаты за период
    function getallChatsSum($cid)
    {
        $chats=$this->getallChats($cid);
        $s=0;
        foreach ($chats as $ch)
        {
            $s+=$ch;
        }
        return $s;
    }

    // Выдас всех чаты за период
    function getallChatsSumVK($cid)
    {
        $chats=$this->getallChatsVK($cid);
        $s=0;
        foreach ($chats as $ch)
        {
            $s+=$ch;
        }
        return $s;
    }

    function getallOutcomesVKSum($cid)
    {
        $OutcomesOutput=0;
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getOutcomesVK($cid, $dt);
            $OutcomesOutput+=$x;
        }
        return $OutcomesOutput;
    }

    function getallOutcomesSum($cid)
    {
        $OutcomesOutput=0;
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getOutcomesInsta($cid, $dt);
            $OutcomesOutput+=$x;
        }
        return $OutcomesOutput;
    }

    function getallOutcomes($cid)
    {
        $OutcomesOutput=array();
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getOutcomesInsta($cid, $dt);
            if ($x>0) $x=$x/1000;
            array_push($OutcomesOutput, $x);
        }
        return $OutcomesOutput;
    }

    function getallOutcomesVK($cid)
    {
        $OutcomesOutput=array();
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getOutcomesVK($cid, $dt);
            if ($x>0) $x=$x/1000;
            array_push($OutcomesOutput, round($x));
        }
        return $OutcomesOutput;
    }

    function getallOutcomesInterval()
    {
        $summ=0;
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getAllOutcomesInsta($dt);
            $summ+=$x;
        }
        return $summ;
    }


    function getallOutcomesIntervalVK()
    {
        $summ=0;
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getAllOutcomesVK($dt);
            $summ+=$x;
        }
        return $summ;
    }


    function getallOutcomesALLCity()
    {
        $OutcomesOutput=array();
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getAllOutcomesInsta($dt);
            if ($x>0) $x=$x/1000;
            array_push($OutcomesOutput, $x);
        }
        return $OutcomesOutput;
    }

    function getallOutcomesALLCityVK()
    {
        $OutcomesOutput=array();
        $date=$this->arraydatesByDW(1);
        foreach ($date as $dt) {
            $x=$this->master_week->getAllOutcomesVK($dt);
            if ($x>0) $x=$x/1000;
            array_push($OutcomesOutput, round($x));
        }
        return $OutcomesOutput;
    }

    function getChatsAllOfAll(){
        $city=$this->getAllCityes();
        $sum=0;
        foreach ($city as $c)
        {
            $chats=$this->getallChats($c[0]);
            foreach ($chats as $ch)
            {
                $sum+=$ch;
            }
        }
        return $sum;
     }

    function getChatsAllOfAllVK(){
        $city=$this->getAllCitiesVK();
        $sum=0;
        foreach ($city as $c)
        {
            $chats=$this->getallChatsVK($c[0]);
            foreach ($chats as $ch)
            {
                $sum+=$ch;
            }
        }
        return $sum;
    }

        // сохраняет все диаграммы инсты
    function showAllCity($rash)
    {
        $city=$this->getAllCityes();
        foreach ($city as $c)
        {
         $chats=$this->getallChats($c[0]);
         $outcome=$this->getallOutcomes($c[0]);
         $this->drawChats($chats, $rash, $c[0], 'inst', $outcome);
        }
    }

    // Сохраняет все города ВК
    function showAllCityVK($rash)
    {
        $city=$this->getAllCitiesVK();
        foreach ($city as $c)
        {
            $chats=$this->getallChatsVK($c[0]);
            $outcome=$this->getallOutcomesVK($c[0]);
            $this->drawChats($chats, $rash, $c[0], 'vk', $outcome);
        }
    }

    function showAllCitySummary($rash)
    {
        $city=$this->getAllCityes();
        $chatsAll=array();
        foreach ($city as $c)
        {
            $chats=$this->getallChats($c[0]);
            array_push($chatsAll, $chats);
        }
        $chatsReturn=array();
        foreach ($chatsAll as $ch)
        {
            $i=0;
            foreach ($ch as $cc)
            {
                $chatsReturn[$i]=(int)$chatsReturn[$i]+(int)$cc;
                $i++;
            }
        }
        $outcome=$this->getallOutcomesALLCity();
        $this->drawChats($chatsReturn, $rash, 0, 'inst', $outcome);
    }

    function showAllCitySummaryVK($rash)
    {
        $city=$this->getAllCitiesVK();
        $chatsAll=array();
        foreach ($city as $c)
        {
            $chats=$this->getallChatsVK($c[0]);
            array_push($chatsAll, $chats);
        }
        $chatsReturn=array();
        foreach ($chatsAll as $ch)
        {
            $i=0;
            foreach ($ch as $cc)
            {
                $chatsReturn[$i]=(int)$chatsReturn[$i]+(int)$cc;
                $i++;
            }
        }
        $outcome=$this->getallOutcomesALLCityVK();
        $this->drawChats($chatsReturn, $rash, 0, 'vk', $outcome);
    }

    function drawChats($chats, $rash, $city, $napravlenie, $outcome=array())
    {
        $dates=$this->arraydatesByDWselmonth(7);
        $razmerPodgon=count($chats); // Узнаем количество записей чтобы динамически формировать размер рисунка
        $razmerholsta=1000; //$razmerPodgon*90; // Умножаем количество записей на количество пикселей для отображения
        $visotaholsta=300;
        $myData = new pData();
        $myData->addPoints($chats,"TTT");
        if ($rash>0) $myData->addPoints($outcome,"TT2");
        $myData->addPoints($dates,"Absissa");
        $myData->setAbscissa("Absissa");
        $myPicture = new pImage($razmerholsta,$visotaholsta,$myData);
        if ($city==0) $myPicture->setScale(1000); else $myPicture->setScale(70);
// размер картинки
        $myPicture->setFontProperties(array("FontName"=>"fonts/arialbd.ttf","FontSize"=>12));
// Задаем шрифт и размер легенде
        $TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT
        , "R"=>0, "G"=>0, "B"=>0, "DrawBox"=>0, "BoxAlpha"=>130);
        $razmerholstaText=70;
        $razmerholstaArea=$razmerholsta-25;
        $myPicture->setGraphArea(-1,50,$razmerholstaArea,$visotaholsta-16);
// размер изображения -25px
        $myPicture->setFontProperties(array("R"=>130,"G"=>130,"B"=>130,"FontName"=>"fonts/arial.ttf","FontSize"=>8));
        /* Цвет и настройки палок пунктирных */
        $Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
        , "Mode"=>SCALE_MODE_MANUAL
        , "LabelingMethod"=>LABELING_ALL, MinDivHeight=>1, XMargin=>30,  YMargin=>0, DrawXLines=>FALSE, AxisR=>255 , AxisG=>255 , AxisB=>255
        , "LabelSkip"=>0, "GridR"=>127, "GridG"=>127, "GridB"=>127, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>0, "LabelRotation"=>0, "DrawArrows"=>0, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>0, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>NONE);
        $myPicture->drawScale($Settings);

        $myPicture->setShadow(true,array("X"=>1,"Y"=>1,"R"=>155,"G"=>187,"B"=>89,"Alpha"=>50));
// Рисуем чат
        $Threshold[] = array("Min"=>0,"Max"=>10,"R"=>240,"G"=>191,"B"=>20,"Alpha"=>70);
        $myPicture->drawAreaChart(array("DisplayValues"=>1, DisplayOffset=>17, AroundZero=>TRUE));
// размер изображения -63
        $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>155,"G"=>187,"B"=>89,"Alpha"=>50));
        $Settings = array("R"=>255, "G"=>255, "B"=>255, "Dash"=>0, "DashR"=>255, "DashG"=>255, "DashB"=>255, "Alpha"=>50, "DashAlpha"=>0);

        $minus=104;
        switch ($razmerPodgon)
        {
            case 4: $minus=360; break;
            case 5: $minus=285; break;
            case 6: $minus=240; break;
            case 7: $minus=210; break;
            case 8: $minus=185; break;
            case 9: $minus=170; break;
            case 10: $minus=155; break;
            case 11: $minus=145; break;
            case 12: $minus=135; break;
            case 13: $minus=130; break;
            case 14: $minus=125; break;
            case 15: $minus=120; break;
            case 16: $minus=115; break;
            case 17: $minus=110; break;
        }
        $myPicture->drawFilledRectangle($razmerholsta-$minus,284,$razmerholsta-57,50,$Settings);
        //$myPicture->stroke();
        chdir(dirname(__FILE__));
        if ($rash>0) {
            $myPicture->render($_SERVER['DOCUMENT_ROOT'] . "/img/diagrams/rash$napravlenie/$city.png");
        }
        else
        {
            $myPicture->render($_SERVER['DOCUMENT_ROOT'] . "/img/diagrams/obsh$napravlenie/$city.png");
        }
    }

}
?>