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
        $masters=new masters();
        $masters->set_dt($dt);
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
        while ($a = mysql_fetch_array($r)){
            $master = $masters->getMasterCom(intval($a['id_master']));
            $masterPercent = ceil($base_percent * $master['comission']*0.01);
            $perMasterTmpl .=   "<tr><td>".$master['name']."</td><td>".$master['comission']."</td><td>$masterPercent</td></tr>";
       }
        $perMasterTmpl .=   "</table>";
            /************   SLIDER TEMPLATE    ****************** */

        $sliderTmpl = "<table class='table slider no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: currentPosition;'></div></td>";
        $q = "select * from bonus_rewards where bonus_id = 1";
        $r = mysql_query($q);
        $rewards = [];
        while ($a = mysql_fetch_array($r)){
            $rewards[] = [
                "summ" => intval($a["summ"]),
                "reward" => intval($a["reward"])
            ];
        }

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
        include($_SERVER['DOCUMENT_ROOT']."/timurnf/payments.php");
        $uar=pay_manager($avb['znachbezporoga']+$avb['porog']);
        $html='';
        $html .= "<table style='margin-top: 20px;' id='managerstable'>";
        foreach ($uar as $u)
        {
            $idu=$u[0];
            $nm=$u[1];
            $proc=$u[2];
            $sum=round($u[3]);
            $html .= "<tr>";
            $html .= "<td style='width: 212px;'>".$nm." ($proc%)</td>";
            $html .= "<td style='width: 100px;'>$sum тг.</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";

        $sliderTmpl.=$html;

        $tmpl = str_replace("sliderTmpl", $sliderTmpl, $tmpl);
        $tmpl = str_replace("perMasterTmpl", $perMasterTmpl, $tmpl);
        $tmpl = str_replace("totalComissionTmpl", $sumvb, $tmpl);
        $tmpl = str_replace("percentComissionTmpl", $avb['znachbezporoga'], $tmpl);
        $tmpl = str_replace("weekBonusTmpl", $avb['porog'], $tmpl);
        $tmpl = str_replace("currentPosition", $currentPosition, $tmpl);
        $tmpl = str_replace("totalBonusTmpl", $avb['znachbezporoga']+$avb['porog'], $tmpl);
        $tmpl = str_replace("5%", $avb['basproc']."%", $tmpl);
///////////////////////////////////////////////      Операторы    ///////////////////////////////////////////////
        $tmpl.="<h1 class='T_M_mar_wi'>Операторы</h1>";
        $compRebuild=new CompRebuild(2);
        $tmpl.=$compRebuild->initStyles();
        $opachki=$masters->getActiveOperatorsByMasters($manager_id);
        $manid=$manager_id;
        foreach ($opachki as $az){
        $manager_id = $az['ids'];
        $tmpl.= "<div class='T_M_managers_opbl'>";
        $tmpl.= "<div class='T_M_managers_opblock'><h2 class='T_M_mar0'><strong>" . $az['names'] . "</strong></h2></div>";
        /**Подключение шаблона**/
            $perMasterTmpl = "";
            $bonushouseoper=new bonushouseoper($dt, $manager_id);
            $bho=$bonushouseoper->getOperatorBonustoExcel();
            if($bho['summa']>0 && $bho['basproc']>0) {$sumvb=round($bho['summa']/$bho['basproc']);} else {$sumvb=0;}
            $bonushouseoper->set_dt(date("Y-m-d", strtotime($dt."-1 week")));
            $bho2=$bonushouseoper->getOperatorBonustoExcel();
            if($bho2['summa']>0 && $bho2['basproc']>0) {$sumvb2=round($bho2['summa']/$bho2['basproc']);} else {$sumvb2=0;}
            if ($sumvb<750) $currentPosition = $sumvb."px"; else $currentPosition = "750px";
            if ($sumvb2<750) {$currentPosition2 = $sumvb2-45; } else {$currentPosition2 = "701";}
            $sliderTmpl = "<table class='table slidex no-width'><tr><td width='100' class='progress-container'><div class='progress' style='width: $currentPosition;'></div></td>";
            $sliderTmpl .= " <td width='".$sumvb2."px' height='19px'><span class='value'></span></td><td width='".(800-$sumvb2)."px' height='19px'></td>";
            $sliderTmpl .= "</tr></table>";
            $totalBonusTmpl=$bho['znachbezporoga']+$bho['porog'];
            $currentPosition3=$currentPosition2+42;
            $tmpl1=$compRebuild->DrawSliderOperator($currentPosition, $sumvb, $sliderTmpl, $totalBonusTmpl, $currentPosition2, $sumvb2, $currentPosition3, $perMasterTmpl);
            $tmpl.= $tmpl1;

                  $date1=date("Y-m-d", strtotime("-3 month"));
                  $date2=date("Y-m-d");
            $r = mysql_query("SELECT DISTINCT c.name, c.id as cityid, usevk FROM users u, `m_city` c, `masters` m WHERE u.id=m.id_master and m.`id_m_city`=c.id and `id_manager`=".$manid." and `id_uchenik`=$manager_id  ORDER BY m.`sort` ASC");
            while ($a = mysql_fetch_array($r)) {
                $q = mysql_query("SELECT COUNT(chats) as chatcount FROM `m_city_day` d, m_city m where d.`id_m_city`=m.id and m.name='".$a['name']."' and dt BETWEEN '".$date1."' AND '".$date2."' order by `id_m_city`, `dt`");
                $qsrav = mysql_fetch_array($q);
                if ($qsrav["chatcount"]>4) {
                    $tmpl .= "<p style='padding: 0px 176px; padding-bottom: 20px; background: white; margin-top: 5px; margin-bottom: 0px;'><br><span style='font-size: 20px'>".$a['name']."</span><br><span style=\" background: linear-gradient( #400080, transparent), linear-gradient( 200deg, #d047d1, #ff0000,    #ffff00); color: white; padding: 3px;\">INSTAGRAM</span><br><img src='chartkonver.php?dateN=$date1&dateK=$date2&city=".$a['name']."'";
                $tmpl .= "alt='".$a['name']."' class='right' id='content' /></p>";
                if ($a['usevk']>0)
                {
                $tmpl .= "<p style='padding: 0px 150px; padding-bottom: 20px; background: white; margin-top: 5px; margin-bottom: 0px;'><br><span style='font-size: 20px'>".$a['name']."</span><br><span style=\" background: #2B587A; color: white; padding: 3px;\">ВК</span><br><img src='chartkonvervk.php?dateN=$date1&dateK=$date2&city=".$a['name']."'";
                $tmpl .= "alt='".$a['name']."' class='right' id='content' /></p>";
                }
            }}
            $tmpl.= "</div>";
        }

        return $tmpl;
    }

?>