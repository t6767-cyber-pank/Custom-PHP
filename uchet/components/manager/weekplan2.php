<?php
if ($operation=='show_week_plan'){
    $id = intval($_POST['id']);
    $dt = $_POST['dt'];
    $dt = preg_replace('/<.*?>/','',$dt);
    $dt = str_replace('"','',$dt);
    $dt = str_replace("'",'',$dt);
    $m = array();
    preg_match('/(\d{2})\.(\d{2})\.(\d{4})/',$dt,$m);
    $dt = $m[3].'-'.$m[2].'-'.$m[1];
    $html = show_weekplan($dt, $id);
    print $html;
    exit;
}



function show_weekplan($dt, $manager_id){
    /**Подключение шаблона**/
    $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
    $tmpl_url = "$DOCUMENT_ROOT/components/manager/weekplan.html";
    $tmpl = file_get_contents($tmpl_url);

    /**Запрос данных с таблицы**/
    $rvb = mysql_query("select * from bonushousemanager where iduser=$manager_id and daten='$dt'");
    $avb = mysql_fetch_array($rvb);
    $sumvb=$avb['summa'];
    $base_percent = $avb['basproc'];
    $total_comission = $sumvb;

    /************   MASTER TEMPLATE    ****************** */
    $perMasterTmpl = "<table id='masters' class='table masters' style='display:".($_REQUEST["razdel"]=="9" ? "none" : "table").";'>";
    $perMasterTmpl .=   "<tr>";
    $perMasterTmpl .=       "<td></td>";
    $perMasterTmpl .=       "<td style='width:19%'>Комиссия <br/> за неделю</td>";
    $perMasterTmpl .=       "<td style='width:31%'>Бонусы <br/> за неделю</td>";
    $perMasterTmpl .=   "</tr>";

    $q = "select u.*, m.id_master from users u,masters m where u.type=0 and m.shown=1 and u.id=m.id_master and m.id_manager=$manager_id order by m.sort";
    $r = mysql_query($q);
    $masters=new masters();
    $masters->set_dt($dt);
    while ($a = mysql_fetch_array($r)){
        $master = $masters->getMasterCom(intval($a['id_master']));
        $masterPercent = ceil($base_percent * $master['comission']*0.01);
        $perMasterTmpl .=   "<tr><td>".$master['name']."</td><td>".$master['comission']."</td><td>$masterPercent</td></tr>";
    }
    $perMasterTmpl .=   "</table>";


    /************   SLIDER TEMPLATE    ****************** */

    $sliderTmpl = "<table class='table slider no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: currentPosition;'></div></td>";

    $bonus_rewards=new bonus_rewards(1);
    $bonus_rewards->set_dt($dt);
    $rewards=$bonus_rewards->getRewardsWeek();

    $currentPosition = getCurrentPosition($total_comission, $rewards);

    $pixelPerSegment = ceil(600 / count($rewards))-17;
    $prev = 0;
    foreach($rewards as $reward){
        $summ = $reward["summ"];
        $weight = ($total_comission > $prev && $total_comission < $summ) ?  "bold" : "normal";
        $sliderTmpl .= " <td width='$pixelPerSegment'><span class='value' style='font-weight:$weight;'>$summ</span></td>";
        $prev = $summ;
    }
    $sliderTmpl .= "</tr><tr><td></td>";
    $prev = 0;
    foreach($rewards as $reward){
        $reward_summ = $reward["reward"];
        $summ = $reward["summ"];
        $weight = ($total_comission > $prev && $total_comission < $summ) ?  "bold" : "normal";
        $sliderTmpl .= "<td style='font-weight:$weight;'>$reward_summ</td>";
        $prev = $summ;
    }

    $sliderTmpl .= "</tr></table>";

    $tmpl = str_replace("sliderTmpl", $sliderTmpl, $tmpl);
    $tmpl = str_replace("perMasterTmpl", $perMasterTmpl, $tmpl);
    $tmpl = str_replace("totalComissionTmpl", $sumvb, $tmpl);
    $tmpl = str_replace("percentComissionTmpl", $avb['znachbezporoga'], $tmpl);
    $tmpl = str_replace("weekBonusTmpl", $avb['porog'], $tmpl);
    $tmpl = str_replace("currentPosition", $currentPosition, $tmpl);
    $tmpl = str_replace("totalBonusTmpl", $avb['znachbezporoga']+$avb['porog'], $tmpl);
    $tmpl = str_replace("5%", $avb['basproc']."%", $tmpl);
    return $tmpl;
}

?>