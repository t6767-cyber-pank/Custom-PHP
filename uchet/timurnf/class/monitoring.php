<?php

class components extends msqlwork
{
    public $access;

    function getComponentsShown()
    {
        $access=$this->access;
        switch ($access)
        {
            case 1: $par="access=1"; break;
            case 2: $par="access_manager=1"; break;
            case 3: $par="access_marketolog=1"; break;
            case 4: $par="vremaccess=1"; break;
        }
        $ret=$this->sfwo("*", "monitoring_komponents", "shown>0 and $par", "sort ASC");
        return $ret;
    }

    function getComp($name)
    {
        $ret=$this->sfw("*", "monitoring_komponents", "name='$name'");
        return $ret[0];
    }

    function setZadachaStatus($idcomp, $style)
    {
        $this->utab("monitoring_komponents", "style='$style'", "id=$idcomp");
    }

    function setPosit($idcomp, $style)
    {
        switch ($this->access)
        {
            case 1: $this->utab("monitoring_komponents", "adminStyle='$style'", "id=$idcomp"); break;
            case 2: $this->utab("monitoring_komponents", "managerStyle='$style'", "id=$idcomp"); break;
            case 3: $this->utab("monitoring_komponents", "marketologStyle='$style'", "id=$idcomp"); break;
            case 4: $this->utab("monitoring_komponents", "vremstyle='$style'", "id=$idcomp"); break;
        }
    }

    function setZadachaStatusDay($idcomp, $style, $dt, $status)
    {
        $res=$this->sfw("*", "monitoring_status", "id_comp=$idcomp and dt='$dt'");
        if ((int)$res[0]['id']==0) {
            $this->itab("monitoring_status", "id_comp, dt, status, otmetka", "$idcomp, '$dt', $status, '$style'");
        } else {
            $this->utab("monitoring_status", "otmetka='$style', status=$status", "id_comp=$idcomp and dt='$dt'");
        }
    }

    function getZadachaStatusWeekStyle($idcomp, $dt)
    {
        $res=$this->sfw("*", "monitoring_status", "id_comp=$idcomp and dt='$dt'");
        if ((int)$res[0]['id']==0)  return ""; else return $res[0]['otmetka'];
    }
}

class monitor extends components
{
    public $styleNormal;
    public $styleBad;
    public $webplan;

    function __construct($dt, $access)
    {
        $this->dt = $dt;
        $this->dt_to = $dt;
        $this->styleNormal='T_M_Monitor_Blok';
        $this->styleBad='T_M_Monitor_Blok_red';
        $this->access=$access;
        $this->webplan=new CompRebuild(1);
    }

    function setZadachaStatus($idcomp, $tumbler)
    {
        if ($tumbler>0) {
            $this->setZadachaStatusDay($idcomp, $this->styleNormal, $this->dt, $tumbler);
        } else
        {
            $this->setZadachaStatusDay($idcomp, $this->styleBad, $this->dt, $tumbler);
        }

    }

    function initBlokWindow($comp, $compblock, $col=3, $st="")
    {
        $html="";
        $staccess="";
        switch ($this->access)
        {
            case 1: $staccess=$comp['adminStyle']; break;
            case 2: $staccess=$comp['managerStyle']; break;
            case 3: $staccess=$comp['marketologStyle']; break;
            case 4: $staccess=$comp['vremstyle']; break;
        }
        $styler = $this->getZadachaStatusWeekStyle($comp['id'], $this->dt);
        if ($styler == "") $styler = $comp['style'];
        $html .= "<div class='$styler' id='comp" . $comp['id'] . "' style='$st ".$staccess."'>";
        //$html .= "<div class='col-$col ".$styler."' id='comp" . $comp['id'] . "' style='$st ".$staccess."'>";
        //        $html .="<script>ddrop('comp".$comp['id']."');</script>";

        //$html .= "<button title='Убрать до перезагрузки браузера' onclick='displayNon(".$comp['id'].");' class='T_M_Monitor_Button_X'>_</button>";
        $html .= "<button title='Включить перемещение' id='pos".$comp['id']."' onclick='ddrop(\"comp".$comp['id']."\", \"pos".$comp['id']."\", ".$comp['id'].")' class='T_M_Monitor_Button_X'>P</button>";
        //$html .= "<button title='Задача еще не решена' onclick='StatusView(" . $comp['id'] . ", 0, \"" . $this->dt . "\");' class='T_M_Monitor_Button_X T_M_Monitor_Button_galkared'>0</button>";
        //$html .= "<button title='Задача решена' onclick='StatusView(" . $comp['id'] . ", 1, \"" . $this->dt . "\");' class='T_M_Monitor_Button_X T_M_Monitor_Button_galka'>✓</button>";
        $html .= $compblock;
        $html .= "</div>";
        return $html;
    }

    function initalize()
    {
        $html="";
        foreach ($this->getComponentsShown() as $comp) {
            switch ($comp['name'])
            {
                case "bonusoper": $html.=$this->initBlokWindow($comp, $this->showBonusesOperToMonitor($comp['title']), 3, "height: 270; width: 245px;"); break;
                case "reatingmanagers": $html.=$this->initBlokWindow($comp, $this->showSumsWeek($comp['title']), 6, "height: 235px; width: 570px;"); break;
                case "reatingmanagerspolgoda": $html.=$this->initBlokWindow($comp, $this->showSumsWeek($comp['title'], 26), 6, "height: 795; width: 570px;"); break;
                case "dolgmasters": $html.=$this->initBlokWindow($comp, $this->showDolgMasters($comp['title'],$comp['id']), 3, "width: 267px;"); break;
                case "reatingrecords": $html.=$this->initBlokWindow($comp, $this->showReatingRecords($comp['title']), 3, "width: 315px; height: 350px; overflow: auto;"); break;
                case "leads": $html.=$this->initBlokWindow($comp, $this->showLeads($comp['title']), 3, "width: 490px; height: 350px; overflow: auto;"); break;
                case "showcomis": $html.=$this->initBlokWindow($comp, $this->showCommission($comp['title']), 3, "width: 1000px; height: 200px; overflow: auto;"); break;
                case "leadsmalochats": $html.=$this->initBlokWindow($comp, $this->showLeadsMaloChatov($comp['title']), 3, "width: 350px; height: 350px; overflow: auto;"); break;
                case "reatingrecordsmalo": $html.=$this->initBlokWindow($comp, $this->showReatingRecordsmalo($comp['title']), 3, "width: 315px; height: 350px; overflow: auto;"); break;
            }
        }
        return $html;
    }

    // Идет на вывод в другие места
    function showBonusesOper()
    {
        $html="";
        $dates=$this->arraywork4week();
        $usersCRM=new usersCRM();
        $users=$usersCRM->getUsersbyType(7);
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header'><span>Зарплата операторов за 4 недели </span><br>".date("d.m.Y", strtotime($dates[0]))." - ".date("d.m.Y", strtotime($this->get_sundayPars($dates[3])))."</span>";
        $html.= "<table>";
        $html.= "<th class='T_M_Monitor_table_th'>Оператор</th>";
        $html.= "<th class='T_M_Monitor_table_th2'>Сумма</th>";
        foreach ($users as $user)
        {
            $html.= "<tr>";
            $html.= "<td>".$user['name']."</td>";
            $sum=0;
            foreach ($dates as $dts)
            {
                $bonushouseoper=new bonushouseoper($dts, $user['id']);
                $sum+=$bonushouseoper->getOperatorBonus();
            }
            $red=($sum<30000)? $red='T_M_Monitor_RED' : $red="";
            $red=($sum>50000)? $red='T_M_Monitor_RED' : $red=$red;
            $html.= "<td class='T_M_Monitor_table_td ".$red."'>".$sum."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showBonusesOperToMonitor($title)
    {
        $html="";
        $dates=$this->arraywork4week();
        $usersCRM=new usersCRM();
        $users=$usersCRM->getUsersbyType(7);
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span><br>";
        $html.= $this->monthToWord($dates[0])." - ".$this->monthToWord($dates[3])."</span>";
        $html.= "<table class='TM_Table_w_100 TM_Font_standart'>";
        $html.= "<th class='T_M_Monitor_table_th'>Оператор</th>";
        $html.= "<th class='T_M_Monitor_table_th2'>Сумма</th>";
        foreach ($users as $user)
        {
            $html.= "<tr>";
            $html.= "<td>".$user['name']."</td>";
            $sum=0;
            foreach ($dates as $dts)
            {
                $bonushouseoper=new bonushouseoper($dts, $user['id']);
                $sum+=$bonushouseoper->getOperatorBonus();
            }
            $red=($sum<30000)? $red='T_M_Monitor_RED' : $red="";
            $red=($sum>50000)? $red='T_M_Monitor_RED' : $red=$red;
            $red="";
            $html.= "<td class='T_M_Monitor_table_td ".$red."'>".$sum."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showSumsWeek($title, $standart=5)
    {
        $html="";
        $masters=new masters();
        $timereal=new timereal();
        $timereal->dt_to=$this->get_mondayPar($this->dt);
        $xd=date("Y-m-d", strtotime($this->dt." -$standart week"));
        $timereal->dt=$this->get_mondayPar($xd);
        $dates=array_reverse($timereal->arraydatesByDW(1));
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_Monitor_styler reder'>Дата</th>";
        $html.= "<th class='T_M_Monitor_styler'>пн</th>";
        $html.= "<th class='T_M_Monitor_styler'>вт</th>";
        $html.= "<th class='T_M_Monitor_styler'>ср</th>";
        $html.= "<th class='T_M_Monitor_styler'>чт</th>";
        $html.= "<th class='T_M_Monitor_styler'>пт</th>";
        $html.= "<th class='T_M_Monitor_styler'>сб</th>";
        $html.= "<th class='T_M_Monitor_styler'>вс</th>";
        $html.= "<th class='T_M_Monitor_styler'>Сумма</th>";
        $reversedDates = array_reverse($dates);
        foreach ($reversedDates as $dt)
        {
            $masters->set_dt($dt);
            $mast=$masters->sellAllMastersShown();
            $sum_mo=0;
            $sum_tu=0;
            $sum_we=0;
            $sum_th=0;
            $sum_fr=0;
            $sum_sa=0;
            $sum_su=0;
            $sum_moX=0;
            $sum_tuX=0;
            $sum_weX=0;
            $sum_thX=0;
            $sum_frX=0;
            $sum_saX=0;
            $sum_suX=0;
            foreach ($mast as $m)
            {
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_mo");
                $sum_mo+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_tu");
                $sum_tu+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_we");
                $sum_we+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_th");
                $sum_th+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_fr");
                $sum_fr+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_sa");
                $sum_sa+=$mx["comission"];
                $mx=$masters->getMasterComWeekDay($m['iduser'], "visitors_su");
                $sum_su+=$mx["comission"];
            }
            if ($dt==date("Y-m-d", strtotime($this->get_monday())))
            {
                $mastersX=new masters();
                $mastersX->set_dt($dates[1]);
                $mast=$masters->sellAllMastersShown();
                foreach ($mast as $m) {
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_mo");
                    $sum_moX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_tu");
                    $sum_tuX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_we");
                    $sum_weX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_th");
                    $sum_thX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_fr");
                    $sum_frX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_sa");
                    $sum_saX += $mx["comission"];
                    $mx = $mastersX->getMasterComWeekDay($m['iduser'], "visitors_su");
                    $sum_suX += $mx["comission"];
                }
                $dtstyle=" style='background: cornflowerblue;'";
                if ($sum_mo<$sum_moX) $mostyle=" style='background: coral;'"; else $mostyle=" style='background: chartreuse;'";
                if ($sum_tu<$sum_tuX) $tustyle=" style='background: coral;'"; else $tustyle=" style='background: chartreuse;'";
                if ($sum_we<$sum_weX) $westyle=" style='background: coral;'"; else $westyle=" style='background: chartreuse;'";
                if ($sum_th<$sum_thX) $thstyle=" style='background: coral;'"; else $thstyle=" style='background: chartreuse;'";
                if ($sum_fr<$sum_frX) $frstyle=" style='background: coral;'"; else $frstyle=" style='background: chartreuse;'";
                if ($sum_sa<$sum_saX) $sastyle=" style='background: coral;'"; else $sastyle=" style='background: chartreuse;'";
                if ($sum_su<$sum_suX) $sustyle=" style='background: coral;'"; else $sustyle=" style='background: chartreuse;'";
                if (($sum_mo+$sum_tu+$sum_we+$sum_th+$sum_fr+$sum_sa+$sum_su)<($sum_moX+$sum_tuX+$sum_weX+$sum_thX+$sum_frX+$sum_saX+$sum_suX)) $sumstyle=" style='background: coral;'"; else $sumstyle=" style='background: chartreuse;'";
            }
            else
            {
                $dtstyle="";
                $mostyle="";
                $tustyle="";
                $westyle="";
                $thstyle="";
                $frstyle="";
                $sastyle="";
                $sustyle="";
                $sumstyle="";
            }

            $dtstyle="";
            $mostyle="";
            $tustyle="";
            $westyle="";
            $thstyle="";
            $frstyle="";
            $sastyle="";
            $sustyle="";
            $sumstyle="";

            $html.= "<tr>";
            $allsums=$sum_mo+$sum_tu+$sum_we+$sum_th+$sum_fr+$sum_sa+$sum_su;
            if ($standart==5)
            {
                $sum_tu+=$sum_mo;
                $sum_we+=$sum_tu;
                $sum_th+=$sum_we;
                $sum_fr+=$sum_th;
                $sum_sa+=$sum_fr;
                $sum_su+=$sum_sa;
                $allsums=$sum_su;
            }
            $html.= "<td class='T_M_Monitor_styler' $dtstyle>".$this->monthToWord($dt)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $mostyle>".round($sum_mo/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $tustyle>".round($sum_tu/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $westyle>".round($sum_we/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $thstyle>".round($sum_th/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $frstyle>".round($sum_fr/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $sastyle>".round($sum_sa/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $sustyle>".round($sum_su/1000)."</td>";
            $html.= "<td class='T_M_Monitor_styler' $sumstyle>".round($allsums/1000)."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showDolgMasters($title, $idcomp)
    {
        $html="";
        $masters=new masters();
        $masters->set_dt(date("Y-m-d", strtotime($this->get_monday()."-1 week")));
        $mastOutput=$masters->getDolg("<tr><td class='T_M_E_S_Tcenter'>", "</td></tr>");
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span><br>";
        $html.= "За прошлую неделю</span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_E_S_Tcenter'>Итого: ".$masters->perem."</th>";
            $html.= "<tr>";
            $html.= $mastOutput;
            $html.= "</tr>";
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showReatingRecords($title)
    {
        $html="";
        $masters=new masters();
        $arm=$masters->sellAllMastersShown();
        $master_procedure_day=new master_procedure_day();
        $master_procedure_day->set_dt($this->get_sunday());
        $master_procedure_week=new master_procedure_week();
        $master_procedure_week->set_dt($this->get_monday());
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_E_S_Tcenter'>Мастер</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Записи INST</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Записи ВК</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Пришедшие</th>";
        foreach ($arm as $a)
        {
            $x=$master_procedure_day->getReatingMasters($a['id']);
            $v=$master_procedure_week->getReatingMasters($a['id']);
            if ($x['usevk']>0) $rvk=intval($x['recvk']); else $rvk="";
            $html.= "<tr>";
            $html.="<td class='T_M_Monitor_styler'>".$a['name']."</td>";
            $html.="<td class='T_M_Monitor_styler'>".intval($x['rec'])."</td>";
            $html.="<td class='T_M_Monitor_styler'>".$rvk."</td>";
            $html.="<td class='T_M_Monitor_styler'>".intval($v['vis'])."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showReatingRecordsmalo($title)
    {
        $html="";
        $masters=new masters();
        $arm=$masters->sellAllMastersShown();
        $master_procedure_day=new master_procedure_day();
        $master_procedure_day->set_dt($this->get_sunday());
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_E_S_Tcenter'>Мастер</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Записи INST</th>";
        foreach ($arm as $a)
        {
            $x=$master_procedure_day->getReatingMasters($a['id']);
            if (intval($x['rec'])<1) { $style=" style='background: red; ' ";} else { $style=""; }
            $html.= "<tr>";
            $html.="<td class='T_M_Monitor_styler'>".$a['name']."</td>";
            $html.="<td class='T_M_Monitor_styler' $style>".intval($x['rec'])."</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }


    // Идет на вывод в мониторинг
    function showLeads($title)
    {
        $html="";
        $masters=new masters();
        $city=$masters->allCitiesUseInstShown();
        $m_city_day=new m_city_day();
        $m_city_day_vk=new m_city_day_vk();
        $master_procedure_day=new master_procedure_day();
        $master_procedure_week=new master_procedure_week();
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_E_S_Tcenter'>Город</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Дата</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Чаты<br>INST</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Записи<br>INST</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Чаты<br>ВК</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Записи<br>ВК</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Приш.</th>";
        foreach ($city as $cit) {
            $name=$cit["name"];
                for ($i=0; $i<3; $i++) {
                    $m_city_day->set_dt($m_city_day->get_mondayPar(date("Y-m-d", strtotime($this->dt."-$i week"))));
                    $m_city_day->set_dt_to($m_city_day->get_sundayPars(date("Y-m-d", strtotime($this->dt."-$i week"))));
                    $master_procedure_day->set_dt($master_procedure_day->get_sundayPars(date("Y-m-d", strtotime($this->dt."-$i week"))));
                    $master_procedure_week->set_dt($master_procedure_week->get_mondayPar(date("Y-m-d", strtotime($this->dt."-$i week"))));

                    $mastcid=$masters->MastersByCity($cit["id"]);
                    $sumrec=0;
                    $sumrecVK=0;
                    $sumv=0;
                    foreach ($mastcid as $mast)
                    {
                        $sumrec+=$master_procedure_day->getReatingMasters($mast["id"])['rec'];
                        $sumrecVK+=$master_procedure_day->getReatingMasters($mast["id"])['recvk'];
                        $sumv+=$master_procedure_week->getReatingMasters($mast["id"])['vis'];
                    }
                    $st="";
                    switch ($i)
                    {
                        case 0: $st=" style='border-left: 2px solid; border-right: 2px solid; border-top: 2px solid;'"; break;
                        case 1: $st=" style='border-left: 2px solid; border-right: 2px solid; '"; break;
                        case 2: $st=" style='border-left: 2px solid; border-right: 2px solid; border-bottom: 2px solid;'"; break;
                    }
                    if ($sumrecVK==0) $sumrecVK="";
                $html.= "<tr $st>";
                $html.="<td class='T_M_Monitor_styler'><b>".$name."</b></td>";
                $html.="<td class='T_M_Monitor_styler'>".$this->monthToWord($m_city_day->dt)."</td>";
                $html.="<td class='T_M_Monitor_styler'>".$m_city_day->getChatsPrirostInterval($cit["id"])."</td>";
                $html.="<td class='T_M_Monitor_styler'>".$sumrec."</td>";
                $html.="<td class='T_M_Monitor_styler'>".$m_city_day_vk->chatsInterval($cit["id"], $m_city_day->dt, $m_city_day->dt_to)."</td>";
                $html.="<td class='T_M_Monitor_styler'>".$sumrecVK."</td>";
                $html.="<td class='T_M_Monitor_styler'>".$sumv."</td>";
                $html.= "</tr>";
                $name="";
            }
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showLeadsMaloChatov($title)
    {
        $html="";
        $masters=new masters();
        $city=$masters->allCitiesUseInstShown();
        $m_city_day=new m_city_day();
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.= "<table class='TM_Font_standart TM_Table_w_100'>";
        $html.= "<th class='T_M_E_S_Tcenter'>Город</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Дата</th>";
        $html.= "<th class='T_M_E_S_Tcenter'>Чаты INST</th>";
        $style="";
        foreach ($city as $cit) {
                $name=$cit["name"];
                $m_city_day->set_dt($m_city_day->get_mondayPar(date("Y-m-d", strtotime($this->dt))));
                $m_city_day->set_dt_to($m_city_day->get_sundayPars(date("Y-m-d", strtotime($this->dt))));
                if ((int)$m_city_day->getChatsPrirostInterval($cit["id"])<2) { $style=" style='background: red; ' ";} else { $style=""; }
                $html.= "<tr>";
                $html.="<td class='T_M_Monitor_styler'><b>".$name."</b></td>";
                $html.="<td class='T_M_Monitor_styler'>".$this->monthToWord($m_city_day->dt)."</td>";
                $html.="<td class='T_M_Monitor_styler' $style>".$m_city_day->getChatsPrirostInterval($cit["id"])."</td>";
                $html.= "</tr>";
                $name="";
        }
        $html.= "</table>";
        $html.= "</div>";
        return $html;
    }

    // Идет на вывод в мониторинг
    function showCommission($title)
    {
        $html="";
        $this->webplan->set_dt($this->get_monday());
        $html.=$this->webplan->initStyles();
        $html.= "<div id='opermonitor'>";
        $html.= "<span class='T_M_Monitor_header TM_Font_standart'><span class='T_M_Monitor_header_in_block'>$title</span></span>";
        $html.=$this->webplan->DrawSlider();
        $html.= "</div>";
        return $html;
    }
}
?>