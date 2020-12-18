<?php
/**Функция Сохранение статистики всех операторов**/
function save_manager_statist($dt)
{
    /**Создаем массивы обработки**/
    $users = array();
    $sobr = array();
    $masters=new masters();
    $masters->set_dt($dt);
    /*Создаем массивы обработки*/

    /**Наполним массив операторов**/
    $r = mysql_query("select * from users where type=1 and active=1");
    while ($a = mysql_fetch_array($r)) {
        array_push($users, $a['id']);
    }
    /*Наполним массив операторов*/

    /**Выесняем Общую сумму со всех мастреров оператора**/
    $increm = 0;
    foreach ($users as $user)
    {
        $sums = array();
        $bonuszp = array();
        $porog = array();
        $bp = getBasePercentmanager();
            $summa = 0;
            /**Выбираем мастеров с которыми работает оператор**/
            $q = "select u.*, m.id_master from users u,masters m where u.type=0 and u.id=m.id_master and m.shown=1 and m.id_manager=$user order by m.sort";
            $r = mysql_query($q);
            while ($a = mysql_fetch_array($r)) {
                $master = $masters->getMasterCom(intval($a['id_master']), $dt);
                $summa += $master['comission'];
            }
            /*Выбираем мастеров с которыми работает оператор*/
            if ($summa != 0) {$bons = round($summa /100*(float)$bp);} else {$bons = 0;}
            array_push($sums, $summa);
            array_push($bonuszp, $bons);
            if ($summa != 0 && $bp != 0) {$kabazya = round($summa);} else {$kabazya = 0;}

            $bonus_rewards=new bonus_rewards(1);
            $bonus_rewards->set_dt($dt);

            $rbx1 = mysql_query("SELECT * FROM bonushousemanager where iduser=$user and daten='$dt'");
            if (mysql_num_rows($rbx1) > 0) {
                $xa=mysql_fetch_array($rbx1);
                if ($summa != 0 && $bp != 0) {$kabazya = round($summa);} else {$kabazya = 0;}
            }

            array_push($porog, $bonus_rewards->getWeekBonus($kabazya));
        $sobr[$increm][0] = $user;
        $sobr[$increm][2] = $sums;
        $sobr[$increm][3] = $bp;
        $sobr[$increm][5] = $bonuszp;
        $sobr[$increm][6] = $porog;
        $increm++;
    }

    /**Забиваем массив в базу**/
    foreach ($sobr as $sob) {
        $uidop = $sob[0]; // iduser
        $base_percent = $sob[3]; // base
        $i = 0;
            $totalx = 0;
            $summaxx = 0;
            $bons = 0;
            $summaxx = $sob[2][$i]; // summa
            $totalx = $sob[5][$i]; // bonuszp
            $bons = $sob[6][$i]; // porog

            $rb = mysql_query("SELECT * FROM bonushousemanager where iduser=$uidop and daten='$dt'");
            if (mysql_num_rows($rb) > 0) {
                $xa=mysql_fetch_array($rb);
                if ($summaxx!=0 && $xa['basproc']!=0) $totalx=$summaxx/100*$xa['basproc'];
                mysql_query("update bonushousemanager set znachbezporoga=$totalx, summa=$summaxx, porog=$bons where iduser=$uidop and daten='$dt'");
            } else {
                mysql_query("insert into bonushousemanager(iduser, daten, znachbezporoga, summa, porog, basproc) values($uidop, '$dt', $totalx, $summaxx, $bons, $base_percent)");
            }
    }
    /*Забиваем массив в базу*/

    /*Выесняем Общую сумму со всех мастреров оператора*/
}
/*Функция Сохранение статистики всех операторов*/


/**Функция Сохранение статистики всех операторов**/
function save_operators_statist($dt)
{
    /**Создаем массивы обработки**/
    $users = array();
    $sobr = array();
    $masters=new masters();
    $masters->set_dt($dt);
    /*Создаем массивы обработки*/

    /**Наполним массив операторов**/
    $r = mysql_query("select * from users where type=7");
    while ($a = mysql_fetch_array($r)) {
        array_push($users, $a['id']);
    }
    /*Наполним массив операторов*/

    /**Выесняем Общую сумму со всех мастреров оператора**/
    $increm = 0;
    foreach ($users as $user)
    {
        $sums = array();
        $bonuszp = array();
        $porog = array();
        $bp = getBasePercent($user);
        $po = getBasePercentoper($user);
            $summa = 0;
            $summa2 = 0;
            /**Выбираем мастеров с которыми работает оператор**/
            $q = "select u.*, m.id_master from users u,masters m where u.type=0 and u.id=m.id_master and m.shown=1 and m.id_uchenik=$user order by m.sort";
            $r = mysql_query($q);
            while ($a = mysql_fetch_array($r)) {
                $master =$masters->getMasterCom(intval($a['id_master']));
                $summa += $master['comission'];
                $summa2 += $master['comission']*$master['city'];
            }
            /*Выбираем мастеров с которыми работает оператор*/
            if ($summa != 0 && $bp != 0) {$bons = round($summa2 / (float)$bp * (float)$po);} else {$bons = 0;}
            array_push($sums, $summa);
            array_push($bonuszp, $bons);
            if ($summa != 0 && $bp != 0) {$kabazya = round($summa / (float)$bp);} else {$kabazya = 0;}


            $rbx1 = mysql_query("SELECT * FROM bonushouseoper where iduser=$user and daten='$dt'");
            if (mysql_num_rows($rbx1) > 0) {
                $xa=mysql_fetch_array($rbx1);
                if ($summa != 0 && $bp != 0) {$kabazya = round($summa / $xa['basproc']);} else {$kabazya = 0;}
            }
                array_push($porog, $summa2);//getWeekBonusOP($kabazya, $user));
        $sobr[$increm][0] = $user;
        $sobr[$increm][2] = $sums;
        $sobr[$increm][3] = $bp;
        $sobr[$increm][4] = $po;
        $sobr[$increm][5] = $bonuszp;
        $sobr[$increm][6] = $porog;
        $increm++;
    }

    /**Забиваем массив в базу**/
    foreach ($sobr as $sob) {
        $uidop = $sob[0]; // iduser
        $base_percent = $sob[3]; // base
        $base_percentoper = $sob[4]; // procentoper
        $i = 0;
            $totalx = 0;
            $summaxx = 0;
            $bons = 0;
            $summaxx = $sob[2][$i]; // summa
            $totalx = $sob[5][$i]; // bonuszp
            $bons = 0; // porog
            $summa2x=$sob[6][$i];
            $rb = mysql_query("SELECT * FROM bonushouseoper where iduser=$uidop and daten='$dt'");
            if (mysql_num_rows($rb) > 0) {
                $xa=mysql_fetch_array($rb);
                if ($summa2x!=0 && $xa['basproc']!=0) $totalx=$summa2x/$xa['basproc']*$xa['procentoperator'];
                mysql_query("update bonushouseoper set znachbezporoga=$totalx, summa=$summaxx, porog=$bons where iduser=$uidop and daten='$dt'");
            } else {
                mysql_query("insert into bonushouseoper(iduser, daten, znachbezporoga, summa, porog, basproc, procentoperator) values($uidop, '$dt', $totalx, $summaxx, $bons, $base_percent, $base_percentoper)");
            }

            $i++;
    }
    /*Забиваем массив в базу*/

    /*Выесняем Общую сумму со всех мастреров оператора*/
}
/*Функция Сохранение статистики всех операторов*/


/**Функция Вычисляем процент для оператора**/
function getBasePercent($id){
    $q = "select b.base_percent from bonus b, bonusoperator op where b.id=op.idbonus and op.iduser=$id limit 1";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    return $a['base_percent'];
}
/*Функция Вычисляем процент для оператора*/

/**Функция Вычисляем процент для Менеджера**/
function getBasePercentmanager(){
    $q = "select base_percent from bonus where id=1";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    return intval($a['base_percent']);
}
/*Функция Вычисляем процент для Менеджера*/

/**Функция Вычисляем процент операторов**/
function getBasePercentoper($id){
    $q = "select procopernew from bonus where id=2";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    return intval($a['procopernew']);
}
/*Вычисляем процент операторов*/

/**Функция Пороговые суммы**/
function getWeekBonusOP($total_comission, $manager_id){
    $q = "SELECT * FROM `bonus_rewards` br, `bonus` b, `bonusoperator` op where b.id=op.idbonus and b.id=br.bonus_id and op.iduser=$manager_id";
    $r = mysql_query($q);
    $rewards = [];
    while ($a = mysql_fetch_array($r)){
        $rewards[] = [
            "summ" => intval($a["summ"]),
            "reward" => intval($a["reward"])
        ];
    }
    $weekBonus = 0;
    foreach($rewards as $reward){
        if ($total_comission >= $reward["summ"]){
            $weekBonus = $reward["reward"];
        }else{
            return $weekBonus;
        }
    }
    return $weekBonus;
}
/*Пороговые суммы*/

/**Позиция ползунка**/
function getCurrentPosition($total_comission, $rewards){
    $numberOfSegments = count($rewards)+1;
    $maxSumm = $rewards[count($rewards)-1]["summ"];
    $currentPosition = 0;
    $lastReward = 0;
	if (count($rewards)>0) $cou=count($rewards); else $cou=1;
    $pixelsInSegment =  ceil(620 / $cou);
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
/*Позиция ползунка*/

/**Недельный бонус менеджера**/
function getWeekBonus($total_comission, $rewards){
    $weekBonus = 0;
    foreach($rewards as $reward){
        if ($total_comission >= $reward["summ"]){
            $weekBonus = $reward["reward"];
        }else{
            return $weekBonus;
        }
    }
    return $weekBonus;
}
/*Недельный бонус менеджера*/
?>