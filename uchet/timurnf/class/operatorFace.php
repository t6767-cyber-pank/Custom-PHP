<?php
class operatorFace extends msqlwork
{
    public $operation;
    public $masters;
    function __construct()
    {
        $this->masters=new masters();
    }
    function show_weekplan($manager_id){
        $this->masters->set_dt($this->dt);
        $bonushouseoper=new bonushouseoper($this->dt, $manager_id);
        $compRebuild=new CompRebuild(2);
        /**Запрос данных с таблицы**/
        $bho=$bonushouseoper->getOperatorBonustoExcel();
        if($bho['summa']>0 && $bho['basproc']>0) {$sumvb=round($bho['summa']/$bho['basproc']);} else {$sumvb=0;}
        /**Шаблон мастеров**/
        $perMasterTmpl = "<table class='table masters T_M_dtable' id='masters'><tr><td></td><td class='T_M_w31'>Сумма баллов <br/> за неделю</td></tr>";
        $mastr=$this->masters->MastersByOperator($manager_id);
        foreach ($mastr as $mas) {
            $master = $this->masters->getMasterCom(intval($mas['id']));
            $kom=0;
            if ($master['comission']!=0 && $bho['basproc']!=0) { $kom += $master['comission'] / $bho['basproc']; }
            $perMasterTmpl .= "<tr><td>" . $master['name']."</td><td>".round($kom,1)."</td></tr>";
        }
        $perMasterTmpl .= "</table>";
        $bonushouseoper->set_dt(date("Y-m-d", strtotime($this->dt."-1 week")));
        $bho2=$bonushouseoper->getOperatorBonustoExcel();
        if($bho2['summa']>0 && $bho2['basproc']>0) {$sumvb2=round($bho2['summa']/$bho2['basproc']);} else {$sumvb2=0;}
        if ($sumvb<750) $currentPosition = $sumvb."px"; else $currentPosition = "750px";
        if ($sumvb2<750) {$currentPosition2 = $sumvb2-45; } else {$currentPosition2 = "701";}
        $totalBonusTmpl=$bho['znachbezporoga']+$bho['porog'];
        $currentPosition3=$currentPosition2+42;
        /**Шаблон слайдера**/
        $sliderTmpl = "<table class='table slidex no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: $currentPosition;'></div></td>";
        $sliderTmpl .= " <td width='".$sumvb2."px' class='T_M_h19'><span class='value'></span></td><td width='".(800-$sumvb2)."px' class='T_M_h19'></td>";
        $sliderTmpl .= "</tr></table>";
        /**Вывод данных в шаблон**/
        $tmpl=$compRebuild->initStyles();
        $tmpl.=$compRebuild->DrawSliderOperator($currentPosition, $sumvb, $sliderTmpl, $totalBonusTmpl, $currentPosition2, $sumvb2, $currentPosition3, $perMasterTmpl);
        return $tmpl;
    }
}
?>