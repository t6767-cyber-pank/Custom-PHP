<?php
class CompRebuild extends msqlwork
{
    public $comp;
    function __construct($comp)
    {
        $this->comp=$comp;
    }

// Инициализация стилей потом поменять надо это пиздоблядство в отдельный файл когда разберу
function initStyles()
{
    $style="";
    // Посмотри пожалуйста этот код как сможешь я понимаю ты не соображаешь сейчас поэтому он может быть не самым идеальным
    switch ($this->comp) {
        case 1:  // Выводиться у маркетолога
            $style .= "<style>";
            $style .= ".root{ width: 100%; background-color: #FFF; padding: 0px 80px; max-width: 800px;}";
            $style .= ".tbx{ width: 100%; background-color: #FFF; border-collapse: collapse;}";
            $style .= ".table td{ vertical-align: top; }";
            $style .= ".progress-container{ position: relative;}";
            $style .= ".progress-container .progress{ position: absolute; left: 0; top: 0; height: 44px; background-color: #61d836; border-right: 2px solid #61d836;}";
            $style .= ".slider-td{ padding-right: 20px;}";
            $style .= ".slider tr:nth-child(1){ background-color: #f8ba00;}";
            $style .= ".slider td{ border: none;}";
            $style .= ".slider tr:nth-child(1) td, .slider tr:nth-child(2) td{ padding: 13px 10px;}";
            $style .= ".slider tr:nth-child(1) td:not(:first-child):before{ content: ''; border: 1px solid #FFF; margin: 15px 0 0 -11px; height: 15px; position: absolute;}";
            $style .= ".masters{ margin-top: 50px;}";
            $style .= ".masters tr:nth-child(2n){ background-color: #f2f2f2; }";
            $style .= ".masters td, .bonuses td{ padding: 10px; }";
            $style .= ".masters td{ font-size: 13px; }";
            $style .= ".masters td:nth-child(2){ border-right: 20px solid #FFF; }";
            $style .= ".bonuses td{ font-size: 12px; line-height: 24px; }";
            $style .= ".price-progress-container{ position: relative; height: 55px;}";
            $style .= ".price-progress-container .price{ padding: 14px 10px; position: absolute; font-weight: bold; margin-bottom: -5px; border-left: 2px solid #61d836; font-size: 1.7rem; height: 60px;}";
            $style .= ".price-progress-container .price > .left{ position: absolute; transform: translateX(-100%); left: -10px;}";
            $style .= ".value{ position: relative;}";
            $style .= ".bonuses td:first-child{ text-align: right; font-weight: bold; font-size: 20px;}";
            $style .= ".table.no-width{ width: initial;}";
            $style.="</style>";
            break;
        case 2: // Вывод стилей операторского блока
            $style .= "<style>";
            $style .= ".root{ width: 100%; background-color: #FFF; padding: 5px 50px; max-width: 1100px;}";
            $style .= ".table{ width: 100%; background-color: #FFF; border-collapse: collapse;}";
            $style .= ".table td{ vertical-align: top; }";
            $style .= ".progress-container{ position: relative;}";
            $style .= ".progress-container .progress{ position: absolute; left: 0; top: 0; height: 44px; background-color: #61d836; border-right: 2px solid #61d836;}";
            $style .= ".slidex-td{ padding-right: 20px;}";
            $style .= ".slidex tr:nth-child(1){ background-color: #f8ba00;}";
            $style .= ".slidex td{ border: none;}";
            $style .= ".slidex tr:nth-child(1) td, .slidex tr:nth-child(2) td{ padding: 13px 10px;}";
            $style .= ".slidex tr:nth-child(1) td:not(:first-child):before{ position: absolute;}";
            $style .= ".masters{ margin-top: 50px;}";
            $style .= ".masters tr:nth-child(2n){ background-color: #f2f2f2; }";
            $style .= ".masters td, .bonuses td{ padding: 10px; }";
            $style .= ".masters td{ font-size: 13px; }";
            $style .= ".masters td:nth-child(2){ border-right: 20px solid #FFF; }";
            $style .= ".bonuses td{ font-size: 12px; line-height: 24px; }";
            $style .= ".price-progress-container{ position: relative; height: 55px;}";
            $style .= ".price-progress-container .price{ padding: 14px 10px; position: absolute; font-weight: bold; margin-bottom: -5px; border-left: 2px solid #61d836; font-size: 1.7rem; height: 60px;}";
            $style .= ".price-progress-container .price2{ padding: 0px 5px; position: absolute; font-weight: bold; margin-bottom: 0px; border-right: 2px solid #ffead9; font-size: 20px; height: 24px; width: 35px; text-align: right;}";
            $style .= ".price-progress-container .price3{ padding: 0px 5px; position: absolute; font-weight: bold; margin-bottom: 0px; font-size: 12px; height: 24px; width: 100px; text-align: left;}";
            $style .= ".price-progress-container .price > .left{ position: absolute; transform: translateX(-100%); left: -10px;}";
            $style .= ".value{ position: relative;}";
            $style .= ".bonuses td:first-child{ text-align: right; font-weight: bold; font-size: 20px;}";
            $style .= ".table.no-width{ width: initial; height: 44px;}";
            $style.="</style>";
            break;
    }
    return $style;
}
// Понятное дело рисует этот долбанутый любимый слайдер саята
function DrawSlider()
{
    $currentPosition=$this->getCurrentPosition();
    $totalCom=$this->getTotalComission();
    $html="";
    $html.="<div class='root'>";
    $html.="<div class='price-progress-container'><span class='price' style='left: $currentPosition;'><span class='left'>$totalCom</span></span></div>";
    $html.="<table class='table main tbx'>";
    $html.="<tr>";
    $html.="<td class='slider-td' style='padding: 0px !important; border-top: 0px !important;'>".$this->sliderTample()."</td>";
    $html.="</tr>";
    $html.="</table>";
    $html.="</div>";
    return $html;
}
// Ну по ходу все же операторы в плане графики отдельное государство при чем такое как Нигер или Гондурас
function DrawSliderOperator($currentPosition, $totalComissionTmpl, $sliderTmpl, $totalBonusTmpl, $currentPx2, $currentPxR, $currentPx3, $perMasterTmpl)
{
    $html="";
    $html.="<div class='root'>";
    $html.="<div class='price-progress-container'>";
    $html.="<span class='price' style='left: $currentPosition;'><span class='left'>$totalComissionTmpl</span>Баллов</span>";
    $html.="</div>";
    $html.="<table class='table main'>";
    $html.="<tr>";
    $html.="<td style='width:70%;' class='slidex-td'>";
    $html.=$sliderTmpl;
    $html.="</td>";
    $html.="<td>";
    $html.="<table class='table bonuses'>";
    $html.="<tr style='background-color: #e2e2e2;'>";
    $html.="<td>";
    $html.="$totalBonusTmpl тг";
    $html.="</td>";
    $html.="<td>";
    $html.="<strong style='font-size: 18px;'>ИТОГО</strong>";
    $html.="</td>";
    $html.="</tr>";
    $html.="</table>";
    $html.="</td>";
    $html.="</tr>";
    $html.="<tr colspan='2'>";
    $html.="<td>";
    $html.="<div class='price-progress-container'>";
    $html.="<span class='price2' style='left: ".$currentPx2."px; top: -25px;'><span>$currentPxR</span></span>";
    $html.="<span class='price3' style='left: ".$currentPx3."px; top: 0px;'>прошлая неделя</span>";
    $html.="</div>";
    $html.="</td>";
    $html.="</tr>";
    $html.="<tr>";
    $html.="<td colspan='2'>";
    $html.=$perMasterTmpl;
    $html.="</td>";
    $html.="</tr>";
    $html.="</table>";
    $html.="</div>";
    return $html;
}

// Ебучий слайдер порогов
function sliderTample()
{
    $currentPosition=$this->getCurrentPosition();
    $sliderTmpl = "<table class='table slider no-width tbx'><tr><td width='100' class='progress-container'><div class='progress' style='width: $currentPosition;'></div></td>";
    $rewards=$this->getRewardsManager();
    $totalCom=$this->getTotalComission();
    $pixelPerSegment = ceil(600 / count($rewards))-17;
    $prev = 0;
    foreach($rewards as $reward){
        $summ = $reward["summ"];
        $weight = ($totalCom > $prev && $totalCom < $summ) ?  "bold" : "normal";
        $sliderTmpl .= " <td width='$pixelPerSegment'><span class='value' style='font-weight:$weight;'>$summ</span></td>";
        $prev = $summ;
    }
    $sliderTmpl .= "</tr><tr><td></td>";
    $prev = 0;
    foreach($rewards as $reward){
        $reward_summ = $reward["reward"];
        $summ = $reward["summ"];
        $weight = ($totalCom > $prev && $totalCom < $summ) ?  "bold" : "normal";
        $sliderTmpl .= "<td style='font-weight:$weight;'>$reward_summ</td>";
        $prev = $summ;
    }

    $sliderTmpl .= "</tr></table>";
    return $sliderTmpl;
}

    // Получаем общую сумму за неделю
    function getTotalComission()
    {
        $bonushousemanager=new bonushousemanager($this->dt, 119);
        return $bonushousemanager->getSum();
    }

    // Получаем пороги текущей недели
    function getRewardsManager()
    {
        $bonus_rewards=new bonus_rewards(1);
        $bonus_rewards->set_dt($this->dt);
        return $bonus_rewards->getRewardsWeek();
    }

    /**Позиция ползунка херня какая то**/
    function getCurrentPosition(){
        $total_comission=$this->getTotalComission();
        $rewards=$this->getRewardsManager();
        $numberOfSegments = count($rewards)+1;
        $maxSumm = $rewards[count($rewards)-1]["summ"];
        $currentPosition = 0;
        $lastReward = 0;
        $pixelsInSegment =  ceil(620 / count($rewards));
        for ($i=0; $i < count($rewards); $i++) {
            $pixels = ($i == 0) ? 120 : $pixelsInSegment;
            if ($total_comission >= $rewards[$i]["summ"]){
                $currentPosition += $pixels;
                if ($i == (count($rewards)-1)) $currentPosition += ceil($pixelsInSegment/2);
            }else{
                $currentPosition += ceil( ($pixels / ($rewards[$i]["summ"] -  $lastReward)) * ($total_comission - $lastReward));
                break;
            }
            $lastReward = $rewards[$i]["summ"];
        }
        return $currentPosition . "px";
    }

}