<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$PHP_SELF = $_SERVER['PHP_SELF'];
/**Общие подключения к файлу подключения БД**/
chdir(dirname(__FILE__));
include ("../../mysql_connect.php");
/**Общие подключения к Классу даты**/
require_once("./dates.php");
require_once("./templater.php");

/**=======================================================Общие классы================================================================================================**/
class msqlwork extends timereal{

    /**Общий запрос Выводит в массив данные **/
    function sfw($selectParams, $fromParams, $whereParams)
    {
        $result=array();
        $query='select '.$selectParams." from ".$fromParams." where ".$whereParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    /**Общий запрос Выводит в массив данные используя конструкйцию Distinct**/
    function sdfw($selectParams, $fromParams, $whereParams)
    {
        $result=array();
        $query='select DISTINCT '.$selectParams." from ".$fromParams." where ".$whereParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    /**Общий запрос Выводит в массив данные используя конструкйцию group by в конце**/
    function sfwg($selectParams, $fromParams, $whereParams, $groupbyParams)
    {
        $result=array();
        $query='select DISTINCT '.$selectParams." from ".$fromParams." where ".$whereParams." GROUP BY ".$groupbyParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    /**Общий запрос Выводит в массив данные используя конструкйцию order by в конце**/
    function sfwo($selectParams, $fromParams, $whereParams, $orderbyParams)
    {
        $result=array();
        $query='select DISTINCT '.$selectParams." from ".$fromParams." where ".$whereParams." ORDER BY ".$orderbyParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    /**Общий запрос Выводит в массив данные используя конструкйцию group by и order by в конце**/
    function sfwgo($selectParams, $fromParams, $whereParams, $groupbyParams, $orderParams)
    {
        $result=array();
        $query='select DISTINCT '.$selectParams." from ".$fromParams." where ".$whereParams." GROUP BY ".$groupbyParams." Order by ".$orderParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    /**Общий запрос Выводит в массив данные используя конструкйцию group by и order by в конце**/
    function sfwgho($selectParams, $fromParams, $whereParams, $groupbyParams, $havindParams, $orderParams)
    {
        $result=array();
        $query='select DISTINCT '.$selectParams." from ".$fromParams." where ".$whereParams." GROUP BY ".$groupbyParams." HAVING ".$havindParams." Order by ".$orderParams;
        $r=mysql_query($query);
        while ($res=mysql_fetch_array($r))
        {
            array_push($result, $res);
        }
        return $result;
    }

    function itab($table, $fields, $vals)
    {
        $query="insert into $table($fields) values($vals)";
        $r=mysql_query($query);
    }

    function itabr($table, $fields, $vals)
    {
        $query="insert into $table($fields) values($vals)";
        $r=mysql_query($query);
        return mysql_insert_id();
    }

    function utab($table, $fields, $where)
    {
        $query="update $table set $fields where $where";
        $r=mysql_query($query);
    }

    function itabdub($table, $fields1, $fields2)
    {
        $q = "insert into $table set $fields1 ON DUPLICATE KEY UPDATE $fields2";
        $r=mysql_query($q);
        return $q;
    }

    function dtab($table, $where)
    {
        $query="delete from $table where $where";
        $r=mysql_query($query);
    }

}

/**=======================================================Классы таблиц================================================================================================**/

class bonus extends msqlwork
{

    function selbonOper()
    {
    return $this->sfw("base_percent, procopernew", "bonus", "id=2")[0];
    }

    function updateBonus($id, $proc)
    {
            $this->utab("bonus", "base_percent=$id, procopernew=$proc", "id<>1");
    }
}

class bonushousemanager extends msqlwork
{
    public $manager_id;

    function __construct($dt, $manager_id)
    {
        $this->dt = $dt;
        $this->manager_id=$manager_id;
    }

    //Выводит бонус менеджера из базы
    function getManagerBonus(){
        /**Запрос данных с таблицы**/
        $dt=$this->dt;
        $id=$this->manager_id;
        $res=$this->sfw("*", "bonushousemanager", "iduser=$id and daten='$dt'");
        return $res[0]['znachbezporoga']+$res[0]['porog'];
    }


    function getSum()
    {
        $manager_id=$this->manager_id;
        $dt=$this->dt;
        $res=$this->sfw("*", "bonushousemanager", "iduser=$manager_id and daten='$dt'");
        return $res[0]['summa'];
    }

    function getManagerBonusOld($manager_id)
    {
        $masters=new masters();
        $masters->set_dt($this->dt);
        $total_comission = 0;
        $percent_comission = 0;
        $base_percent = 5;
        $res=$masters->selectAllMastersByManager($manager_id);
        foreach ($res as $m)
        {
            $master = $masters->getMasterCom(intval($m['id_master']), $this->dt);
            $masterPercent = ceil($base_percent * $master['comission']*0.01);

            $total_comission += $master['comission'];
            $percent_comission += $masterPercent;
        }
        return $percent_comission;
    }
}

class bonushouseoper extends msqlwork
{
    public $manager_id;
    function __construct($dt, $manager_id)
    {
        $this->dt = $dt;
        $this->manager_id=$manager_id;
    }
    //Выводит бонус оператора из базы
    function getOperatorBonus(){
        /**Запрос данных с таблицы**/
        $dt=$this->dt;
        $id=$this->manager_id;
        $res=$this->sfw("*", "bonushouseoper", "iduser=$id and daten='$dt'");
        return $res[0]['znachbezporoga']+$res[0]['porog'];
    }

    function getOperatorBonustoExcel(){
        /**Запрос данных с таблицы**/
        $dt=$this->dt;
        $id=$this->manager_id;
        $res=$this->sfw("*", "bonushouseoper", "iduser=$id and daten='$dt'");
        return $res[0];
    }

    function saveOnSaveUser($idbonus, $idmanager)
    {
        $id=$this->manager_id;
        $idbonus=2;
        $this->itab("bonusoperator", "idbonus, iduser", "$idbonus,$id");
        $this->itab("operatortomanager", "idoperator, idmanager", "$id, $idmanager");
    }

    function update($idbonus, $idmanager)
    {
        $id=$this->manager_id;
        $this->utab("bonusoperator", "idbonus=$idbonus", "iduser=$id");
        $this->utab("operatortomanager", "idmanager=$idmanager", "idoperator=$id");
    }
}

class bonusoperatorezh extends msqlwork{
    public $manager_id;

    function __construct($manager_id)
    {
        $this->manager_id=$manager_id;
    }

    //Выводит бонус менеджера из базы
    function getBonus(){
        /**Запрос данных с таблицы**/
        $id=$this->manager_id;
        $res=$this->sfw("*", "bonusoperatorezh bh, bonusezh b", "bh.iduser=$id and bh.idbonus=b.id");
        return $res[0]['base_percent'];
    }

    function getID(){
        /**Запрос данных с таблицы**/
        $id=$this->manager_id;
        $res=$this->sfw("*", "bonusoperatorezh bh, bonusezh b", "bh.iduser=$id and bh.idbonus=b.id");
        return $res[0]['idbonus'];
    }

    function save($idbonus)
    {
        $id=$this->manager_id;
        $this->itab("bonusoperatorezh", "idbonus, iduser", "$idbonus,$id");
    }

    function update($idbonus)
    {
        $id=$this->manager_id;
        $this->utab("bonusoperatorezh", "idbonus=$idbonus", "iduser=$id");
    }
}

class bonus_rewards extends msqlwork
{
    public $bonus_id;

    function __construct($bonus_id)
    {
        $this->bonus_id=$bonus_id;
    }

    //Выводит пороги
    function getRewards(){
        $rewards1 = [];
        $res=$this->sfw("*", "bonus_rewards", "bonus_id=".$this->bonus_id);
        foreach ($res as $rv)
        {
            $rewards1[] = [
                "summ" => intval($rv["summ"]),
                "reward" => intval($rv["reward"])
            ];
        }
        return $rewards1;
    }

    //Выводит пороги
    function getRewardsWeek(){
        $rewards1 = [];
        $id=$this->bonus_id;
        $dt=$this->dt;
        $res=$this->sfw("*", "bonus_rewards_week", "bonus_id=$id and dt='$dt'");
        foreach ($res as $rv)
        {
            $rewards1[] = [
                "summ" => intval($rv["summ"]),
                "reward" => intval($rv["reward"])
            ];
        }
        if(count($rewards1)==0)
            $rewards1=$this->getRewards();
        return $rewards1;
    }

    function getWeekBonus($total_comission)
    {
        $rewards=$this->getRewardsWeek();
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

    function getGetRevardsbyDt()
    {
        $dt=$this->dt;
        $id=$this->bonus_id;
        $res=$this->sfw("sum(reward) as rew", "bonus_rewards_week", "bonus_id=$id and dt='$dt'");
        return $res[0]['rew'];
    }

    function saveRevards()
    {
        $revards=$this->getRewards();
        $x=$this->getGetRevardsbyDt();
        if ($x==0) {
            foreach ($revards as $rev) {
                $s = $rev['summ'];
                $r = $rev["reward"];
                $dt = $this->dt;
                $id = $this->bonus_id;
                $this->itab("bonus_rewards_week", "bonus_id, summ, reward, dt", "$id, $s, $r, '$dt'");
            }
        }
    }
}

class bonus_rewardsezh extends msqlwork
{
    public $bonus_id;

    function __construct($bonus_id)
    {
        $this->bonus_id=$bonus_id;
    }

    //Выводит пороги
    function getRewards(){
        $res=$this->sfw("*", "bonus_rewardsezh", "bonus_id=".$this->bonus_id);
        return $res;
    }
}

class cityImportVK extends msqlwork{
    /**Вывод всех не повторяющихся городов**/
    function allCityes()
    {
        $city=array();
        $xfact=$this->sdfw("c.name, c.id as cid", "`cityimportvk` ci, `m_city` c", "c.id=ci.`id_mcity`");
        $i=0;
        foreach ($xfact as $xv)
        {
            $city[$i]['id']=$xv['cid'];
            $city[$i]['name']=$xv['name'];
            $i++;
        }
        return $city;
    }

    /**Вывод всех не повторяющихся городов с расходами в рублях за выбранную неделю**/
    function allCityesOutcomesThisWeek()
    {
        $city=array();
        $xfact=$this->sfwg("c.name, c.id as cid, sum(ci.outcome) as sums", "`cityimportvk` ci, `m_city` c", "c.id=ci.`id_mcity` and ci.data BETWEEN '".$this->get_monday()."' and '".$this->get_sunday()."'", "c.name");
        $i=0;
        foreach ($xfact as $xv)
        {
            $city[$i]['id']=$xv['cid'];
            $city[$i]['name']=$xv['name'];
            $city[$i]['sums']=round($xv['sums'],2);
            $i++;
        }
        return $city;
    }
    /**Вывод расходов за день ВК**/
    function CityOutcomeDay($cid, $dar)
    {
        $res=$this->sfw("sum(outcome) as outcome", "cityimportvk", "id_mcity=".$cid." and data='".$dar."'");
        return round((float)$res[0]['outcome'],2);
    }
    /**Вывод расходов города за интервал**/
    function CityOutcomeInterval($cid, $dt, $dt_to){
        $res=$this->sfw("sum(outcome) as outcome", "cityimportvk", "id_mcity=".$cid." and data between '$dt' and '$dt_to'");
        return round($res[0]['outcome'],2);
    }
    /**Вывод всех расходов за интервал**/
    function OutcomeIntervalAll($dt, $dt_to){
        $res=$this->sfw("sum(outcome) as outcome", "cityimportvk", "data between '$dt' and '$dt_to'");
        return $res[0]['outcome'];
    }

    /** Вывод массива оригинала чатов **/
    function getChartsOriginal($cid, $dt){
            $res=$this->sfw("userg", "cityimportvk", "id_mcity=".$cid." and data='".$dt."'+interval -1 day");
            return (int)$res[0]['userg'];
    }
}

class cityImportVKEzh extends msqlwork{

    /**Вывод всех расходов в теньге за выбранную неделю**/
    function CityesOutcomesWeek()
    {
        $outcome=0;
        $xfact=$this->sfw("sum(outcome) as sums", "`cityimportvkezh`", "data BETWEEN '".$this->get_monday()."' and '".$this->get_sunday()."'");
        $i=0;
        foreach ($xfact as $xv)
        {
            $outcome=round($xv['sums'],2);
            $i++;
        }
        $master_week=new master_week();
        $master_week->set_dt($this->dt);
        return round($outcome*$master_week->getCourse());
    }
}

class ezh_city extends msqlwork
{
    function selAllCity()
    {
        $res=$this->sfw("id,name,bonus", "ezh_city", "id>0");
        return $res;
    }
}

class ezh_city_day extends msqlwork{

    /**Выводит массив чатов с VK за неделю**/
    function ezhDirect($kran)
    {
        $cc=array();
        $this->set_dt($this->get_monday());
        $this->set_dt_to($this->get_sunday());
        $dm=$this->arraydates();
        foreach ($dm as $dtx) {
            $contacts = $this->getDirectDay("dt='$dtx'", $kran);
            array_push($cc, $contacts);
        }
        return $cc;
    }

    function getDirectDay($where, $kran)
    {
        $xfact=$this->sfw("*", "ezh_direct", $where);
        if ($kran==1)
        $contacts=$xfact[0]['direct_kz'];
        else
        $contacts=$xfact[0]['direct_ru'];
        return $contacts;
    }

    /**Выводит массив чатов с инстаграма за неделю**/
    function CityesWorkWeek($c_id)
    {
        $cc=array();
        $this->set_dt($this->get_monday());
        $this->set_dt_to($this->get_sunday());
        $dm=$this->arraydates();
        foreach ($dm as $dtx) {
            $contacts = $this->getContactsDay("id_city=$c_id and dt='$dtx'");
            $contacts_old = $this->getContactsDay("id_city=$c_id and dt='$dtx'+interval -1 day");
            if ($contacts == '') { $contacts_str = ''; } else { $contacts_str = $contacts - $contacts_old; }
            array_push($cc, $contacts_str);
        }
        return $cc;
    }

    /**Выводит количество чатов инстаграм в текущей день**/
    function getContactsDay($where)
    {
        $contacts=0;
        $xfact=$this->sfw("contacts", "ezh_city_day", $where);
        $contacts=$xfact[0]['contacts'];
        return $contacts;
    }

    /**Выводит количество чатов инстаграм в текущей день**/
    function getLidfidDay($where)
    {
        $contacts=0;
        $xfact=$this->sfw("lidfit", "ezh_city_day", $where);
        $contacts=$xfact[0]['lidfit'];
        return $contacts;
    }

    function CityesWorkWeekLidfid($c_id)
    {
        $cc=array();
        $this->set_dt($this->get_monday());
        $this->set_dt_to($this->get_sunday());
        $dm=$this->arraydates();
        foreach ($dm as $dtx) {
            $contacts = $this->getLidfidDay("id_city=$c_id and dt='$dtx'");
            array_push($cc, $contacts);
        }
        return $cc;
    }

    /**Выводит максимальный контакт**/
    function getMaxContactsDay($cid)
    {
        $contacts=0;
        $xfact=$this->sfw("max(contacts) as cnt", "ezh_city_day", "id_city=$cid and dt BETWEEN '".$this->get_monday()."' and '".$this->get_sunday()."'");
        $contacts=$xfact[0]['cnt'];
        return $contacts;
    }

    /**Выводит максимальный контакт**/
    function getMaxContactsDayVK()
    {
        $contacts=0;
        $xfact=$this->sfw("sum(contactsvk) as cnt", "ezh_city_day", "id_city>0");
        $contacts=$xfact[0]['cnt'];
        return $contacts;
    }

    /**Выводит массив чатов с VK за неделю**/
    function CityesWorkWeekVK($c_id)
    {
        $cc=array();
        $this->set_dt($this->get_monday());
        $this->set_dt_to($this->get_sunday());
        $dm=$this->arraydates();
        foreach ($dm as $dtx) {
            $contacts = $this->getContactsDayVK("id_city=$c_id and dt='$dtx'");
            array_push($cc, $contacts);
        }
        return $cc;
    }

    /**Выводит количество чатов вк в текущей день**/
    function getContactsDayVK($where)
    {
        $contacts=0;
        $xfact=$this->sfw("sum(contactsvk) as cvk", "ezh_city_day", $where);
        $contacts=$xfact[0]['cvk'];
        return $contacts;
    }

    /**Выводит расходы по городу**/
    function getOutcomeCity($cid)
    {
        $xfact=$this->sfw("c.id,c.name,w.outcome,w.paid", "ezh_city c left join ezh_city_week w on c.id=w.id_city and w.dt='".$this->dt."'", "c.id=$cid");
        return $xfact[0]['outcome'];
    }

    /**Выводит контакты для ввода инсты**/
    function settingsAllOutcomesInsta()
    {
        $html="";
        $templat=new templater();
        $xfact=$this->sfw("c.id,c.name,w.outcome,w.paid", "ezh_city c left join ezh_city_week w on c.id=w.id_city and w.dt='".$this->dt."'", "c.id>0");
        $i=0;
        foreach ($xfact as $xv)
        {
            $c_id = $xv['id'];
            $c_name = $xv['name'];
            $outcome = $xv['outcome'];
            $paid = $xv['paid'];
            $html.= "<div>";
            $html.= "<div class='T_M_left'>";
            $html.= "<div class='T_M_Outcome_Ezh'>Расходы $c_name</div> <input type='text' class='s_outcome' id='s_outcome$c_id' value='".htmlspecialchars($outcome)."'>";
            $html.= "</div>";
            $html.= "<div class='T_M_Insta_cont'>";
            $cc=$this->CityesWorkWeek($c_id);
            $html.= $templat->printTableContacts($cc, "Новые контакты Instagram");
            $html.= "</div>";
            $html.= "<div class='T_M_both'></div>";
            $html.= "</div>";
            $i++;
        }
       return $html;
    }
}

class ezh_city_week extends msqlwork{

    /**Вывод всех Бюджетов ВК теньге за выбранную неделю**/
    function CityesBudgetWeek()
    {
        $outcome=0;
        $xfact=$this->sfw("sum(vkbudget) as sums", "ezh_city_week", "dt='".$this->get_monday()."'");
        $i=0;
        foreach ($xfact as $xv)
        {
            $outcome=round($xv['sums'],2);
            $i++;
        }
        return round($outcome);
    }

    /**Вывод всех выплат за работу ВК теньге за выбранную неделю**/
    function CityesWorkWeek()
    {
        $xfact=$this->sfw("sum(vkrabota) as sums", "ezh_city_week", "dt='".$this->get_monday()."'");
        return round($xfact[0]['sums']);
    }

    /**Вывод всех выплат за работу ВК теньге за выбранную неделю**/
    function CityesOutcomes()
    {
        $xfact=$this->sfw("sum(outcome) as outc", "ezh_city_week", "dt='".$this->get_monday()."'");
        return round($xfact[0]['outc']);
    }
}

class ezh_shop extends msqlwork{

    /**Вывод названия магазина**/
    function Name($id)
    {
        $cname=0;
        $xfact=$this->sfw("id,name", "ezh_shop", "id_marketolog=$id and id=1");
        $cname=$xfact[0]['name'];
        return $cname;
    }
}

class m_city extends msqlwork
{
    /**Вывод всех не повторяющихся городов работающих мастеров которые используют insta**/
    function allCities()
    {
        $xfact=$this->sfw("*", "m_city", "id>0");
        return $xfact;
    }

    function selcoef($id)
    {
        $xfact=$this->sfw("*", "m_city", "id=$id");
        return $xfact[0]["procent"];
    }

    function saveCity($id, $name, $proc){
        $id = intval($id);
        $res=$this->sfw("*", "m_city", "id=$id");

        if ((int)$res[0]['id']==0){ $id=$this->itabr("m_city", "name, procent", "'$name', $proc"); }else
            { if ($name == ''){ $this->dtab('m_city', "id=$id"); } else{ $this->utab('m_city', "name='$name', procent=$proc", "id=$id"); } }
        return $id;
    }

    function UpdateCity($id, $proc){
        if ($id>0 &&  $proc>0){ $this->utab('m_city', "procent=$proc", "id=$id"); }
        return $id;
    }

    /**Вывод всех не повторяющихся городов работающих мастеров которые используют insta**/
    function allCitiesMasters()
    {
        $xfact=$this->sfwo("DISTINCT c.name, c.id", "masters m, m_city c", "m.id_m_city=c.id and m.shown>0", "c.id");
        return $xfact;
    }
}

class m_city_day extends msqlwork{
    /**Выводит оригинал чатов инсты по городу**/
    function chatsoriginal($cid){
        $res=$this->sfw("chats", "m_city_day", "id_m_city=".$cid." and dt='".$this->get_sunday_last()."'");
        return $res[0]['chats'];
    }

    /**Выводит оригинал чатов инсты по городу**/
    function chatsoriginalToch($cid){
        $res=$this->sfw("chats", "m_city_day", "id_m_city=".$cid." and dt='".date("Y-m-d", strtotime($this->dt." -1day"))."'");
        return $res[0]['chats'];
    }

    /**Выводит сколько чатов по городу**/
    function chatsCount($cid){
        $res=$this->sfw("COUNT(chats) as chatcount", "m_city_day", "id_m_city=".$cid." and dt BETWEEN '".$this->dt."' and '".$this->dt_to."'");
        return $res[0]['chatcount'];
    }
    /**Выводит массив чатов по городу**/
    function chats($cid){
        $res=$this->sfw("chats, dt, lidfit", "m_city_day", "id_m_city=".$cid." and dt BETWEEN '".$this->dt."' and '".$this->dt_to."'");
        return $res;
    }
    /**Выводит прирост чатов за интервал неделю месяц**/
    function getChatsPrirostInterval($cid)
    {
        $cor=$this->chatsoriginalToch($cid);
        $chats=$this->chats($cid);
        $i=0;
        $sum=0;
        foreach ($chats as $ch)
        {
            $sum+=$ch["chats"]-$cor;
            $cor=$ch["chats"];
            $i++;
        }
        return $sum;
    }
}

class m_city_day_vk extends msqlwork{
    /**Выводит сколько чатов в день по городу**/
    function chatsDay($cid, $dt){
        $res=$this->sfw("sum(chatsvk) as cvk", "m_city_day_vk", "id_m_city=".$cid." and dt='".$dt."'");
        return $res[0]['cvk'];
    }
    /**Вывод чатов города по интервалу**/
    function chatsInterval($cid, $dt, $dt_to){
        $res=$this->sfw("sum(chatsvk) as cvk", "m_city_day_vk", "id_m_city=".$cid." and dt between '$dt' and '$dt_to'");
        return $res[0]['cvk'];
    }
    /**Вывод всех городов чатов по интервалу**/
    function chatsIntervalAll($dt, $dt_to){
        $res=$this->sfw("sum(chatsvk) as cvk", "m_city_day_vk", "dt between '$dt' and '$dt_to'");
        return $res[0]['cvk'];
    }

    /**Выводит сколько чатов по городу**/
    function chatsCount($cid){
        $res=$this->sfw("COUNT(chatsvk) as chatcount", "m_city_day_vk", "id_m_city=".$cid." and dt BETWEEN '".$this->dt."' and '".$this->dt_to."'");
        return $res[0]['chatcount'];
    }
}

class managerMark extends msqlwork
{
    function saveMark($id, $markColor)
    {
        if ($this->selectMark($id)==0) {
            $id_master = $this->itabr("manager_mark", "	id_city, markColor, dt", "$id, '$markColor', '" . $this->get_monday() . "'");
        }
        else
        {
            $this->utab("manager_mark", "markColor='$markColor'", "id_city=".$id." and dt='".$this->dt."'");
        }
    }

    function selectMark($id)
    {
        $res=$this->sfw("*", "manager_mark", "id_city=".$id." and dt='".$this->dt."'");
        return (int)$res[0]['id'];
    }

    function getMark($id)
    {
        $res=$this->sfw("*", "manager_mark", "id_city=".$id." and dt='".$this->dt."'");
        return $res[0]['markColor'];
    }
}

class masters extends msqlwork{
    public $perem;

    /**Вывод всех мастеров с именами**/
    function sellAllMasters()
    {
        $res=$this->sfwo("u.name, m.id, u.id as iduser", "users u, masters m", "m.id_master=u.id", "m.sort ASC");
        return $res;
    }

    /**Вывод всех мастеров с именами**/
    function sellAllMastersShown()
    {
        $res=$this->sfwo("u.name, m.id, u.id as iduser", "users u, masters m", "m.id_master=u.id and m.shown>0", "m.sort ASC");
        return $res;
    }

    /**Вывод всех не повторяющихся городов работающих мастеров которые используют ВК**/
    function allCitiesUseVK()
    {
        $cityes=array();
        $xfact=$this->sfwgo("c.name, m.id_m_city", "m_city c, masters m", "m.id_m_city=c.id and m.usevk>0", "m.id_m_city", "c.name asc");
        $i=0;
        foreach ($xfact as $xv)
        {
            $cityes[$i][0]=$xv['id_m_city'];
            $cityes[$i][1]=$xv['name'];
            $i++;
        }
        return $cityes;
    }

    /**Вывод всех не повторяющихся городов работающих мастеров которые используют ВК**/
    function allCitiesUseVKOSort()
    {
        $cityes=array();
        $xfact=$this->sfwgo("c.name, m.id_m_city", "m_city c, masters m", "m.id_m_city=c.id and m.usevk>0", "m.id_m_city", "m.sort asc");
        $i=0;
        foreach ($xfact as $xv)
        {
            $cityes[$i][0]=$xv['id_m_city'];
            $cityes[$i][1]=$xv['name'];
            $i++;
        }
        return $cityes;
    }

    /**Вывод всех не повторяющихся городов работающих мастеров которые используют insta**/
    function allCitiesUseInst()
    {
        $cityes=array();
        $xfact=$this->sfwgo("c.name, m.id_m_city", "m_city c, masters m", "m.id_m_city=c.id", "m.id_m_city", "m.sort asc");
        $i=0;
        foreach ($xfact as $xv)
        {
            $cityes[$i][0]=$xv['id_m_city'];
            $cityes[$i][1]=$xv['name'];
            $i++;
        }
        return $cityes;
    }

    /**Вывод всех не повторяющихся городов работающих мастеров которые используют insta**/
    function allCitiesUseInstShown()
    {
        $cityes=array();
        $xfact=$this->sfwgo("c.name, c.id", "m_city c, masters m", "m.id_m_city=c.id and m.shown>0", "m.id_m_city", "m.sort asc");
        return $xfact;
    }

    /**Вывод всех не повторяющихся работающих мастеров которые используют ВК**/
    function allMastersUseVK()
    {
        $vkmasters=array();
        $xfact=$this->sfwg("m.id, m.id_m_city", "masters m", "m.usevk>0 and m.shown>0", "m.id");
        $i=0;
        foreach ($xfact as $xv)
        {
            $vkmasters[$i]['idmaster']=$xv['id'];
            $vkmasters[$i]['idcity']=$xv['id_m_city'];
            $i++;
        }
        return $vkmasters;
    }
    /**Вывод по индексу города не повторяющихся работающих мастеров которые используют ВК**/
    function MasterUseVK($cid)
    {
        $vkmasters=array();
        $xfact=$this->sfw("m.id, m.id_m_city", "masters m", "m.usevk>0 and m.shown>0 and m.id_m_city=$cid");
        $i=0;
        foreach ($xfact as $xv)
        {
            $vkmasters[$i]['id']=$xv['id'];
            $vkmasters[$i]['city']=$xv['id_m_city'];
            $i++;
        }
        return $vkmasters;
    }

    /** Выведет всех активных мастеров по городу **/
    function MastersByCity($cid)
    {
        $res=$this->sfwo('u.name, m.id', "users u, masters m", "u.id=m.id_master and m.id_m_city=$cid and m.shown>0", "u.name ASC");
        return $res;
    }
    /** Выведет всех активных мастеров по городу **/
    function MastersByCityVK($cid)
    {
        $res=$this->sfwo('u.name, m.id', "users u, masters m", "u.id=m.id_master and m.id_m_city=$cid and m.usevk>0 and m.shown>0", "u.name ASC");
        return $res;
    }

    function selectAllMastersByManager($manager_id)
    {
        $res=$this->sfwo("*", "users u,masters m", "u.type=0 and u.id=m.id_master and m.id_manager=$manager_id", "m.sort");
        return $res;
    }

    function selectAllMastersByManagerAndCity($manager_id, $city_id)
    {
        $res=$this->sfwo("u.*, m.id_master", "users u,masters m", "u.type=0 and m.shown=1 and u.id=m.id_master and m.id_manager=$manager_id and id_m_city=$city_id", "m.sort");
        return $res;
    }


    /** Выведет имя мастера по ид **/
    function MastersById($id)
    {
        $res=$this->sfwo('u.name', "users u, masters m", "u.id=m.id_master and m.id=$id", "u.name ASC");
        return $res[0]['name'];
    }

    /** Выведет имя мастера по ид **/
    function MastersByOperator($id)
    {
        $res=$this->sfwo("u.name,u.id,m.id_m_city, m.id as mider", "users u, masters m", "u.id=m.id_master and m.id_uchenik=$id", "m.sort ASC");
        return $res;
    }

    function getActiveOperatorsByMasters($manager_id)
    {
        return $this->sfwgo("u.id as ids, u.name as names", "users u,masters m", "m.shown=1 and u.active>0 and u.type=7 and m.id_manager=$manager_id and m.id_uchenik>0 and m.id_uchenik=u.id", "u.name", "m.sort");
    }

    function getMasterByIdUser($id)
    {
        $res=$this->sfw('*', "masters m", "id_master=$id");
        return $res[0];
    }

    /**Вывод комиссии для менеджера**/

    function getMastersPar($id)
    {
        $res=$this->sfw('m.id,u.name,m.by_percent,m.percent_val, c.procent', "users u, masters m, m_city c", "c.id=m.id_m_city and u.id=m.id_master and u.type=0 and u.id=$id");
        return $res[0];
    }

    /** Выводит комиссию с мастера для менеджера**/
    function getMasterCom($id){
        $bonuses = [
            "comission" => 0,
            "name" => "",
            "city" => 0
        ];
        $dt=$this->dt;
        $pars=$this->getMastersPar($id);
        $m_id = intval($pars['id']);
        $name = $pars['name'];
        $m_by_percent = $pars['by_percent'];
        $m_percent_val = $pars['percent_val'];
        $city=$pars['procent'];
        $master_week=new master_week();
        $master_week->set_dt($dt);
        $res=$master_week->getMasterInfo($m_id);
        $course = $res['course'];
        $sum_no_self = intval($res['sum_no_self']);
        $procedures=new procedures();
        $procedures->set_dt($dt);
        $sum_comission=$procedures->getComissionMaserProc($m_id);
        if($m_by_percent==1){
            $sum_comission = $sum_no_self*$m_percent_val/100;
        }
        if ($course>0)$sum_comission *= $course;
        $bonuses["comission"] = intval($sum_comission);
        $bonuses["name"] = $name;
        $bonuses["city"] = $city;
        return $bonuses;
    }

    /** Выводит комиссию с мастера для оператора **/
    function getMasterComOper($id){
        $bonuses = [
            "comission" => 0,
            "name" => ""
        ];
        $dt=$this->dt;
        $pars=$this->getMastersPar($id);
        $m_id = intval($pars['id']);
        $name = $pars['name'];
        $m_by_percent = $pars['by_percent'];
        $m_percent_val = $pars['percent_val'];
        $master_week=new master_week();
        $master_week->set_dt($dt);
        $res=$master_week->getMasterInfo($m_id);
        $course = $res['course'];
        $sum_no_self = intval($res['sum_no_self']);
        $procedures=new procedures();
        $procedures->set_dt($dt);
        $sum_comission=$procedures->getComissionMaserProc($m_id);
        if($m_by_percent==1){
            $sum_comission = $sum_no_self*$m_percent_val/100;
        }
        if ($course>0)$sum_comission *= $course;
        $bonuses["comission"] = intval($sum_comission);
        $bonuses["name"] = $name;
        return $bonuses;
    }


    /** Выводит комиссию с мастера для менеджера**/
    function getMasterComWeekDay($id, $field){
        $bonuses = [
            "comission" => 0,
            "name" => ""
        ];
        $dt=$this->dt;
        $pars=$this->getMastersPar($id);
        $m_id = intval($pars['id']);
        $name = $pars['name'];
        $m_by_percent = $pars['by_percent'];
        $m_percent_val = $pars['percent_val'];
        $master_week=new master_week();
        $master_week->set_dt($dt);
        $res=$master_week->getMasterInfoToMon($m_id);
        $course = $res['course'];
        switch ($field)
        {
            case "visitors_mo": $sum_no_self = intval($res['sum_no_self_mo']); break;
            case "visitors_tu": $sum_no_self = intval($res['sum_no_self_tu']); break;
            case "visitors_we": $sum_no_self = intval($res['sum_no_self_we']); break;
            case "visitors_th": $sum_no_self = intval($res['sum_no_self_th']); break;
            case "visitors_fr": $sum_no_self = intval($res['sum_no_self_fr']); break;
            case "visitors_sa": $sum_no_self = intval($res['sum_no_self_sa']); break;
            case "visitors_su": $sum_no_self = intval($res['sum_no_self_su']); break;
        }
        $procedures=new procedures();
        $procedures->set_dt($dt);
        $sum_comission=$procedures->getComissionMaserProcWeekDay($m_id, $field);
        if($m_by_percent==1){
            $sum_comission = $sum_no_self*$m_percent_val/100;
        }
        if ($course>0)$sum_comission *= $course;
        $bonuses["comission"] = intval($sum_comission);
        $bonuses["name"] = $name;
        return $bonuses;
    }

    /**Вывод должников**/
    function getDolg($lstr, $rstr)
    {
        $html="";
        $mdolg=$this->sellAllMasters();
        $master_week=new master_week();
        $master_week->set_dt($this->dt);
        $this->perem=0;
        foreach ($mdolg as $mas)
        {
            $vi=$master_week->getCountRecords($mas['id']);
            if ($vi>0) {
                $mk=$this->getMasterCom($mas['iduser']);
                $this->perem+=$mk["comission"];
                $html .= $lstr.$mk['name'].$rstr;
            }
        }
        return $html;
    }

    /**Вывод должников**/
    function getDolg2($lstr, $rstr)
    {
        $html="";
        $mdolg=$this->sellAllMasters();
        $master_week=new master_week();
        $master_week->set_dt($this->dt);
        $this->perem=0;
        foreach ($mdolg as $mas)
        {
            $vi=$master_week->getCountRecordsNaProverke($mas['id']);
            if ($vi>0) {
                $mk=$this->getMasterCom($mas['iduser']);
                $this->perem+=$mk["comission"];
                $html .= $lstr.$mk['name'].$rstr;
            }
        }
        return $html;
    }


    function maxSort()
    {
        $res=$this->sfw("max(sort) as sor", "masters", "id>0");
        return $res[0]["sor"];
    }

    function saveMaster($id)
    {
        $id_master = $this->itabr("masters", "id_master", "$id");
        $sort = $this->maxSort();
        $this->utab("masters", "sort=$sort", "id=$id_master");
        return $id_master;
    }

    function save_MasterAll($type, $id, $email, $id_manager, $id_marketolog, $id_uchenik, $use_course, $use_vk, $currency_id, $by_percent, $percent_val, $shown, $id_m_city)
    {
        // Добавление мастера
        $res = $this->getMasterByIdUser($id);
        if ((int)$res['id'] == 0) {
            $id_master = $this->saveMaster($id);
            if($use_course>0) {
                $res1 = $this->sfw("*", "master_week", "id_master=127 and dt='" . $this->get_monday() . "'");
                $this->itabr("master_week", "id_master, dt, contacts, outcome, sum_no_self, paid, course, bill_checked, sent, closed, html, param1, param2, param3, param4, param5, param6, param7, files, sum_no_self_mo, sum_no_self_tu, sum_no_self_we, sum_no_self_th, sum_no_self_fr, sum_no_self_sa, sum_no_self_su, opaid, outcomevk, outcomeworkvk, budgetvk", "$id_master, '" . $this->get_monday() . "', 0, 0, 0, 0, " . $res1[0]['course'] . ", 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0");
                $dtxx = date("Y-m-d", strtotime($this->get_monday() . " - 1 week"));
                $res2 = $this->sfw("*", "master_week", "id_master=127 and dt='" . $dtxx . "'");
                $this->itabr("master_week", "id_master, dt, contacts, outcome, sum_no_self, paid, course, bill_checked, sent, closed, html, param1, param2, param3, param4, param5, param6, param7, files, sum_no_self_mo, sum_no_self_tu, sum_no_self_we, sum_no_self_th, sum_no_self_fr, sum_no_self_sa, sum_no_self_su, opaid, outcomevk, outcomeworkvk, budgetvk", "$id_master, '" . $dtxx . "', 0, 0, 0, 0, " . $res2[0]['course'] . ", 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0");
            }
        } else {
            $id_master = $res['id'];
        }
        $email = mysql_real_escape_string($email);
        $id_manager = intval($id_manager);
        $id_marketolog = intval($id_marketolog);
        $id_uchenik = intval($id_uchenik);
        $use_course = intval($use_course);
        $use_vk = intval($use_vk);
        $currency_id = intval($currency_id);
        $by_percent = intval($by_percent);
        $percent_val = floatval($percent_val);
        $shown = intval($shown);
        $id_m_city = intval($id_m_city);
        if ((int)$use_course>0) $cur=1; else $cur=0;

        $this->utab("masters", "email='$email', id_manager=$id_manager, id_marketolog=$id_marketolog, id_uchenik=$id_uchenik, use_course=$use_course, currency_id=$cur, usevk=$use_vk, currency_id=$currency_id, by_percent=$by_percent, percent_val=$percent_val, shown=$shown, id_m_city=$id_m_city", "id=$id_master");
        return $id_master;
    }
}

class master_procedure_day extends msqlwork
{
    // Выводит количество записей для каждого мастера
    function getReatingMasters($id)
    {
        $this->set_dt($this->get_sunday());
        $res=$this->sfw("sum(mp.records) as rec, sum(mp.recordsvk) as recvk, m.usevk", "masters m, master_procedure_day mp", "m.id=$id and m.id=mp.id_master and mp.dt='".$this->dt."'");
        return $res[0];
    }

    function getMasters($id)
    {
        $this->set_dt($this->get_sunday());
        $res=$this->sfw("sum(mp.records) as rec, sum(mp.recordsvk) as recvk, m.usevk", "masters m, master_procedure_day mp", "m.id=$id and m.id=mp.id_master and mp.dt='".$this->dt."'");
        return $res[0];
    }

    function recs($m_id, $p_id)
    {
        $res=$this->sfw("records", "master_procedure_day", "id_master=$m_id and id_procedure=$p_id and dt='".$this->get_sunday()."'");
        return $res;
    }
}

class master_procedure_week extends msqlwork
{
    // Выводит количество записей для каждого мастера
    function getReatingMasters($id)
    {
        $this->set_dt($this->get_monday());
        $res=$this->sfw("sum(visitors) as vis", "master_procedure_week", "id_master=$id and dt='".$this->dt."'");
        return $res[0];
    }
}

class master_week extends msqlwork{
    /**Выводит инфу о мастере**/
    function getMasterInfo($m_id)
    {
        $dt=$this->dt;
        $res=$this->sfw("outcome,paid,course,bill_checked,closed,html,sum_no_self,files", "master_week", "id_master=$m_id and dt='$dt'");
        return $res[0];
    }

    function getMasterInfoToMon($m_id)
    {
        $dt=$this->dt;
        $res=$this->sfw("*", "master_week", "id_master=$m_id and dt='$dt'");
        return $res[0];
    }

    /**Вывод курса по дате**/
    function getCourse()
    {
        $xfact=$this->sfw("max(course) as crs", "master_week", "dt='".$this->get_monday()."'");
        if ((float)$xfact[0]['crs']!=0) return $xfact[0]['crs']; else return 1;
    }
    /**Вывод расходов на работу ВК за неделю**/
    function outcomeVorkVk(){
        $res=$this->sfw("sum(outcomeworkvk) as er", "master_week", "dt='$this->dt'");
        return (int)$res[0]['er'];
    }

    /** Вывод бюджета города ВК **/
    function getWeekBudgetCity($cid)
    {
            $res = $this->sfw("sum(budgetvk) as bvk", "master_week mw, masters m", "mw.id_master=m.id and m.id_m_city=".$cid." and dt='".$this->dt."'");
            return (int)$res[0]['bvk'];
    }

    /** Данные мастера **/
    function getMasterById($id)
    {
        $res = $this->sfw("*", "master_week", "id_master=$id and dt='".$this->get_monday()."'");
        return $res[0];
    }

    /** Выдаст данные о расходах за неделю по городу **/
    function getOutcomesInsta($cid, $dt)
    {
        $res = $this->sfw("sum(mw.outcome) as outc", "masters m, master_week mw", "m.id=mw.id_master and mw.dt='$dt' and id_m_city=$cid");
        return $res[0]['outc'];
    }

    /** Выдаст данные о расходах за неделю по городу **/
    function getOutcomesVK($cid, $dt)
    {
        $res = $this->sfw("sum(mw.outcomevk) as outc", "masters m, master_week mw", "m.id=mw.id_master and mw.dt='$dt' and id_m_city=$cid");
        return $res[0]['outc'];
    }

    /** Выдаст данные о расходах за неделю**/
    function getAllOutcomesInsta($dt)
    {
        $res = $this->sfw("sum(mw.outcome) as outc", "masters m, master_week mw", "m.id=mw.id_master and mw.dt='$dt'");
        return $res[0]['outc'];
    }

    /** Выдаст данные о расходах за неделю**/
    function getAllOutcomesVK($dt)
    {
        $res = $this->sfw("sum(mw.outcomevk) as outc", "masters m, master_week mw", "m.id=mw.id_master and mw.dt='$dt'");
        return $res[0]['outc'];
    }

    /**Количество посетителей мастера**/
    function getCountRecords($idm)
    {
        $res=$this->sfw("count(w.visitors) as vis", "procedures p, master_procedure_week w, master_week mw", "p.id=w.id_procedure and w.dt='".$this->get_monday()."' and w.dt=mw.dt and mw.id_master=p.`id_master` and p.`id_master`=".$idm."  and `mw`.`bill_checked`<1 and w.visitors>0");
        return $res[0]["vis"];
    }

    /**Количество посетителей мастера**/
    function getCountRecordsNaProverke($idm)
    {
        $res=$this->sfw("count(w.visitors) as vis", "procedures p, master_procedure_week w, master_week mw", "p.id=w.id_procedure and w.dt='".$this->get_monday()."' and w.dt=mw.dt and mw.id_master=p.`id_master` and p.`id_master`=".$idm."  and `mw`.`bill_checked`=1 and w.visitors>0");
        return $res[0]["vis"];
    }



    /**Обновление информации в таблице расходов ВК**/
    function updateOutcomeVk()
    {
        $civk=new cityImportVK();
        $masters=new masters();

        $civk->set_dt($this->dt);
        $masters->set_dt($this->dt);

        $cityes=$civk->allCityesOutcomesThisWeek();
        foreach ($cityes as $xv)
        {
            $mas=($masters->MasterUseVK($xv['id']));
            $delitel=count($mas);
            foreach ($mas as $m)
            {
                if ($this->getCourse()==0) {$curs=1;} else {$curs=$this->getCourse();}
                $summa=round($xv['sums']*$curs);
                if ($summa!=0) {$summa=round($summa/$delitel);}
                echo $xv['name']."   ".$summa."<br>";
                $res=$this->getMasterById($m['id']);
                if ((int)$res['id_master']>0 )
                {
                    $this->utab("master_week", "outcomevk=$summa", "id_master=".$m['id']." and dt='".$this->get_monday()."'");
                }
            }
        }
    }
}

class paymentszp extends msqlwork
{
    /**Проверка на оплату зарплаты**/
    function payzp($komu){
        $res=$this->sfw("id, paid", "paymentszp", "date='".$this->dt."' and komu=$komu");
        if (isset($res[0]['paid'])) return $res[0]['paid']; else return 2;
    }
    /**Вбиваем данные в таблицу по оплате**/
    function insertData($komu)
    {
        $res=$this->sfw("*", "paymentszp", "date='".$this->get_monday()."' and komu=$komu");
        if ((int)$res[0]['id']==0) $this->itab("paymentszp", "date, paid, komu", "'".$this->get_monday()."', 0, $komu");
        return (int)$res[0]['id'];
    }
    /**Все продовцы**/
    function allSellers()
    {
        $users=new usersCRM();
        $res=$users->getUsersbyType(6);
        return $res;
    }
    /**Все операторы**/
    function alloperators()
    {
        $users=new usersCRM();
        $res=$users->getUsersbyType(7);
        return $res;
    }
}

class pr_city extends msqlwork
{
    function getCityByName($name)
    {
        $res=$this->sfw("*", "pr_city", "name='$name'");
        return $res[0];
    }

    function getAllCity()
    {
        $res=$this->sfwo("*", "pr_city", "id>0", "name");
        return $res;
    }
}

class pr_order extends msqlwork
{
/**Вывод общей суммы без доставки за определенную дату**/
function getSumsVkOrdersBezDost(){
   $res=0;
   $pars=$this->sfwg("o.id, o.dt, o.skidka, (sum(p.price*po.number)-o.skidka) as sums", "`pr_order` o, pr_order_tovar po, pr_tovar p", "o.vk_zakaz>0 and o.id=po.id_order and p.id=po.id_tovar and o.dt='".$this->dt."'", "o.id");
   foreach ($pars as $p)
   {
       $res=$res+(int)$p["sums"];
   }
   return $res;
}

    function updateStatus($id, $stat)
    {
        $this->utab("pr_order", "status=$stat", "id=$id");
    }

/**Вывод общей суммы без доставки за период**/
function getSumsPeriodVkOrdersBezDost(){
        $res=0;
        $pars=$this->sfwg("o.id, o.dt, o.skidka, (sum(p.price*po.number)-o.skidka) as sums", "`pr_order` o, pr_order_tovar po, pr_tovar p", "o.vk_zakaz>0 and o.id=po.id_order and p.id=po.id_tovar and o.dt between '".$this->dt."' and '".$this->dt_to."'", "o.id");
        foreach ($pars as $p)
        {
            $res=$res+(int)$p["sums"];
        }
        return $res;
}
/**Вывод общей количества продонного товара за период только выполненные**/
    function getSumsPeriodNumberOrdersDone($cid){
        $pars=$this->sfwg("o.id, o.dt, sum(po.number) as sums", "`pr_order` o, pr_order_tovar po", "o.id_city=$cid and o.id=po.id_order and o.done>0 and o.dt between '".$this->dt."' and '".$this->dt_to."'", "o.id");
        $sum=0;
        foreach ($pars as $p)
        {
            $sum+=$p['sums'];
        }
        return $sum;
    }
/**Вывод общей количества продонного товара за период только выполненные VK**/
    function getSumsPeriodNumberOrdersDoneVK(){
        $pars=$this->sfwg("o.id, o.dt, sum(po.number) as sums", "`pr_order` o, pr_order_tovar po", "vk_zakaz>0 and o.id=po.id_order and o.done>0 and o.dt between '".$this->dt."' and '".$this->dt_to."'", "o.id");
        $sum=0;
        foreach ($pars as $p)
        {
            $sum+=$p['sums'];
        }
        return $sum;
    }
/**Вывод количества заказов для города**/
    function getSumsPeriodOrdersDone($cid){
        $pars=$this->sfw("count(id) as sums", "`pr_order`", "id_city=$cid and done>0 and dt between '".$this->dt."' and '".$this->dt_to."'");
        return $pars[0]['sums'];
    }

/**Вывод количества заказов vk для города**/
    function getSumsPeriodOrdersDoneVK(){
        $pars=$this->sfw("count(id) as sums", "`pr_order`", "vk_zakaz>0 and done>0 and dt between '".$this->dt."' and '".$this->dt_to."'");
        return $pars[0]['sums'];
    }
/**Вывод маржи за период по всем городам**/
    function getSummMargaperiod()
    {
        $res=$this->sfwg("p.name, p.price, p.self_price, sum(t.number) as cnt", "`pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c", "o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and o.dt BETWEEN '".$this->dt."' and '".$this->dt_to."' and o.done=1", "p.name");
        $itogo=0;
        foreach ($res as $r)
        {
            $itog=($r['price']-(int)$r['self_price'])*$r['cnt'];
            $itogo=$itogo+$itog;
        }
        return $itogo;
    }
/**Вывод скидок за период по всем городам**/
    function skidkaPeriod(){
        $res=$this->sfw("sum(skidka) as cnt", "`pr_order` o", "dt BETWEEN '".$this->dt."' and '".$this->dt_to."' and done=1");
        return $res[0]['cnt'];
    }

    /**Вывод маржи за период по всем городам**/
    function getSummOrderbyCity($cid)
    {
        $res=$this->sfwg("p.name, p.price, p.self_price, sum(t.number) as cnt", "`pr_order` o, pr_order_tovar t, pr_tovar p, `pr_city` c", "o.id=t.id_order and t.id_tovar=p.id and o.id_city=c.id and c.id=$cid and o.dt BETWEEN '".$this->dt."' and '".$this->dt_to."' and o.done=1", "p.name");
        $itogo=0;
        foreach ($res as $r)
        {
            $itog=$r['price']*$r['cnt'];
            $itogo=$itogo+$itog;
        }
        return $itogo;
    }
    /**Вывод скидок за период по городу**/
    function skidkaPeriodcity($cid){
        $res=$this->sfw("sum(skidka) as cnt", "`pr_order` o", "dt BETWEEN '".$this->dt."' and '".$this->dt_to."' and done=1 and o.id_city=$cid");
        return $res[0]['cnt'];
    }

    /**Выводит всех клиентов**/
    function getAllOrders($cid)
    {
        $res=$this->sfwgo("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid", "c.id", "c.name ASC");
        return $res;
    }

    /**Выводит всех клиентов кто первый раз купил 2 недели назад**/
    function getAll2Weeks($cid)
    {
        $res=$this->sfwgho("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid", "c.id", "count(o.id)=1 and dtx>'".$this->get_mondayPar( $this->dt)."'", "c.name ASC");
        return $res;
    }

    /**Выводит всех клиентов кто купил повторно**/
    function getAllPovtor($cid)
    {
        $res=$this->sfwgho("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid", "c.id", "count(o.id)=2", "c.name ASC");
        return $res;
    }

    /**Выводит всех клиентов кто купил 1 раз и больше месяца нет заказов**/
    function getAll1razItishina($cid)
    {
        $res=$this->sfwgho("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid and o.dt<'".$this->dt."'", "c.id", "count(o.id)=1", "c.name ASC");
        return $res;
    }

    /**Выводит всех клиентов кто много и постоянно покупает**/
    function getAllMnogo($cid)
    {
        $res=$this->sfwgho("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid", "c.id", "count(o.id)>3", "c.name ASC");
        return $res;
    }

    /**Выводит всех клиентов кто раньше покупал много а сейчас не покупают**/
    function getAllransheMnogo($cid)
    {
        $res=$this->sfwgho("c.id as cid, c.phone, c.name, c.address, o.id_city, max(o.dt) as dtx, count(o.id) as orc, o.done", "pr_client c, pr_order o", "o.id_client=c.id and o.done>0 and o.id_city=$cid and o.dt<'".$this->dt."'", "c.id", "count(o.id)>3", "c.name ASC");
        return $res;
    }

}

class pr_zvon extends msqlwork
{
    public $idclient;

    function __construct($idclient)
    {
        $this->idclient=$idclient;
    }

    function getZvon()
    {
        $res=$this->sfwo("*", "pr_zvon", "idclient=".$this->idclient, "id DESC");
        return $res[0];
    }

    function setZvon($status, $tema)
    {
        $this->itab("pr_zvon", "idclient, status, tema, data", $this->idclient.", '$status', '$tema', '".$this->dt."'");
    }
}

class procedures extends msqlwork
{
    function getProcNameById($id)
    {
        $res=$this->sfw("name", "procedures", "id=$id");
        return $res[0]["name"];
    }

    function getProcsMaster($mid)
    {
        $res=$this->sfw("*", "procedures", "id_master=$mid and active>0 order by sort desc");
        return $res;
    }

    function getProcsMasterInScores($mid)
    {
        $res=$this->sfw("id, name", "procedures", "id_master=$mid and active>0 and count_in_scores>0 order by sort desc");
        return $res;
    }
    // вывод сколько пришло на процедуру
    function getComissionMaserProc($m_id)
    {
        $dt=$this->dt;
        $sum_comission=0;
        $rx=$this->sfw("p.name,p.price,p.bonus,p.topmanager_bonus,p.comission, p.bals,p.count_in_scores,w.visitors", "procedures p left join master_procedure_week w on p.id=w.id_procedure", "dt='$dt' and p.id_master=$m_id");
        foreach ($rx as $a)
        {
            $comission = intval($a['comission']);
            $visitors = intval($a['visitors']);
            if ($visitors>0){
                $sum_comission += $visitors*$comission;
            }
        }
        return $sum_comission;
    }

    // вывод сколько пришло на процедуру
    function getComissionMaserProcWeekDay($m_id, $field) // visitors_mo
    {
        $dt=$this->dt;
        $sum_comission=0;
        $rx=$this->sfw("p.name,p.price,p.bonus,p.comission, p.count_in_scores,w.visitors,w.$field", "procedures p left join master_procedure_week w on p.id=w.id_procedure", "dt='$dt' and p.id_master=$m_id");
        foreach ($rx as $a)
        {
            $comission = intval($a['comission']);
            $visitors = intval($a["$field"]);
            if ($visitors>0){
                $sum_comission += $visitors*$comission;
            }
        }
        return $sum_comission;
    }

    function SaveProc($type, $id, $id_master, $proc)
    {
        // Добавление процедуры
            if (isset($proc)){
                foreach($proc as $k=>$v){
                    if ($k==0)continue;
                    $p_id = intval($v['id']);
                    $sorter=$v['sortproc'];
                    $name = mysql_real_escape_string($v['name']);
                    if($v['price']!='')$price = intval($v['price']);else $price = 'NULL';
                    if($v['comission']!='')$comission = intval($v['comission']);else $comission = 'NULL';
                    $bonus = 0;
                    if($v['balls']!='')$balls = intval($v['balls']);else $balls = '0';
                    $topmanager_bonus = 0;
                    if($v['scores']!='')$scores = intval($v['scores']);else $scores = 'NULL';
                    if($v['archiv']!='')$archivProc = intval($v['archiv']);else $archivProc = 'NULL';
                    if ($p_id>0){
                        if ($name!='' || intval($v['price'])!=0 || intval($v['comission'])!=0 || intval($v['scores'])!=0){
                            $this->utab("procedures", "name='$name',price=$price,comission=$comission,count_in_scores=$scores, active=$archivProc, bals=$balls, sort=$sorter", "id=$p_id");
                        }else{
                            $this->dtab("procedures", "id=$p_id");
                        }
                    }else{
                        if ($name!='' || intval($v['price'])!=0 || intval($v['comission'])!=0 || intval($v['scores'])!=0){
                            $this->itab("procedures", "id_master,name,price,comission,bonus,topmanager_bonus,count_in_scores, bals, sort", "$id_master,'$name',$price,$comission,$bonus,$topmanager_bonus,$scores, $balls, $sorter");
                            $p_id = mysql_insert_id();
                        }
                    }
                }
            }
    }
}

class usersCRM extends msqlwork
{
    // Выводим всех менеджеров кто на процентах
    function getManagersInprocent()
    {
        $res = $this->sfwo("*", "users", "inprocent>0", "inprocent DESC");
        return $res;
    }

    // Выводим всех менеджеров у кого 100 процентов
    function get100percMenegers()
    {
        $res = $this->sfwo("*", "users", "inprocent=100", "inprocent DESC");
        return $res;
    }

    // Выводит пользователя по id
    function getUserbyID($id)
    {
        $res = $this->sfw("*", "users", "id=$id");
        return $res[0];
    }

    // Выводит всех продавцов
    function getUsersbyType($type)
    {
        $res = $this->sfw("*", "users", "type=$type and active>0");
        return $res;
    }

    function saveU($name, $password, $type, $inprocent)
    {
        $name = mysql_real_escape_string($name);
        $password = mysql_real_escape_string($password);
        $this->itab("users", "name,password,type, inprocent", "'$name','$password',$type, $inprocent");
        return $id = mysql_insert_id();
    }

    function updateUsers($name, $password, $active, $inprocent, $id)
    {
        $name = mysql_real_escape_string($name);
        $password = mysql_real_escape_string($password);
        $this->utab("users", "name='$name',password='$password', active=$active, inprocent=$inprocent", "id=$id");
    }

    function deleteUser($id)
    {
        $this->dtab("users", "id=$id");
    }

    function save_user($id, $idbonus, $active, $inprocent, $idmanager, $name, $password, $type)
    {
        $bonushouseoper = new bonushouseoper($this->dt, $id);
        $bonusoperatorezh = new bonusoperatorezh($id);
        // добавление пользователя
        if ($id == 0) {
            if ($name != '' || $password != '') {
                $id = $this->saveU($name, $password, $type, $inprocent);
                if ($type == 6 && $id != 0) {
                    $bonusoperatorezh->manager_id = $id;
                    $bonusoperatorezh->save($idbonus);
                }
                if ($type == 7 && $id != 0) {
                    $bonushouseoper->manager_id = $id;
                    $bonushouseoper->saveOnSaveUser($idbonus, $idmanager);
                }
            }
        } else {
            if ($name != '' || $password != '') {
                $this->updateUsers($name, $password, $active, $inprocent, $id);
                if ($type == 6) {
                    $bonusoperatorezh->update($idbonus);
                } else {
                    $bonushouseoper->update($idbonus, $idmanager);
                }
            } else {
                $this->deleteUser($id);
            }
        }
        return $id;
    }

    function save_Master($type, $id, $email, $id_manager, $id_marketolog, $id_uchenik, $use_course, $use_vk, $currency_id, $by_percent, $percent_val, $shown, $id_m_city)
    {
        // Добавление мастера
        if ($type == 0 && $id != 0) {
            $masters = new masters ();
            $id_master=$masters->save_MasterAll($type, $id, $email, $id_manager, $id_marketolog, $id_uchenik, $use_course, $use_vk, $currency_id, $by_percent, $percent_val, $shown, $id_m_city);
        }
        return $id_master;
    }

        function addProc($type, $id, $id_master, $proc)
        {
            // Добавление процедуры
            if ($type==0 && $id!=0) {
            $procedures=new procedures();
            $procedures->SaveProc($type, $id, $id_master, $proc);
        }
    }

}

    /**=======================================================Классы по назначению================================================================================================**/
class zsp extends msqlwork
{
    public $master;
    public $cimVK;
    public $cityes;
    public $mastweek;
    public $m_city_day_vk;

    // Конструктор
    function __construct()
    {
        // Подключаем класс masters
        $this->master=new masters();
        $this->cimVK=new cityImportVK();
        $this->cityes=$this->master->allCitiesUseVK();
        $this->mastweek=new master_week();
        $this->m_city_day_vk=new m_city_day_vk();
    }

    //Вывод массива всех городов
  function allCityUseVk()
  {
      return $this->cityes;
  }

  // Задаем другим классам текущую дату
  function setDtClass()
  {
      $this->cimVK->set_dt($this->dt);
      $this->mastweek->set_dt($this->dt);
      $this->m_city_day_vk->set_dt($this->dt);
  }

  // Массив Оригиналов чатов для всех городов
  function getOriginalChatsArray()
  {
      $arr=array();
      $this->setDtClass();
      $xr=0;
      foreach ($this->cityes as $cid) {
          $arr[$xr]=$this->cimVK->getChartsOriginal($cid[0], $this->dt);
          $xr++;
      }
      return $arr;
  }
  // Массив бюджетов ВК
  function getBudgerVKArray()
    {
        $arr=array();
        $this->setDtClass();
        $xr=0;
        foreach ($this->cityes as $cid) {
            $arr[$xr]=$this->mastweek->getWeekBudgetCity($cid[0]);
            $xr++;
        }
        return $arr;
    }

    // бюджет конкретного города ВК
    function getBudgeVKCity($cid)
    {
       $this->setDtClass();
        return $this->mastweek->getWeekBudgetCity($cid);
    }
    // бюджет конкретного города ВК
    function getOriginalChatVKCity($cid, $dt)
    {
        return $this->cimVK->getChartsOriginal($cid, $dt);
    }

    // Вывести курс недели
    function getCourseWeek()
    {
        $this->setDtClass();
        return $this->mastweek->getCourse();
    }
    // Вывод чатов в день по городу
    function chatsDayVK($cid, $dt){
        return $this->m_city_day_vk->chatsDay($cid, $dt);
    }
    // Вывод чатов по интервалу по городу
    function chatsIntervalVK($cid)
    {
        return $this->m_city_day_vk->chatsInterval($cid, $this->dt, $this->dt_to);
    }
    // Вывод всех чатов по интервалу
    function chatsIntervalVKAll()
    {
        return $this->m_city_day_vk->chatsIntervalAll($this->dt, $this->dt_to);
    }
    //Расходы вк в День
    function CityOutcomeDay($cid, $dar)
    {
        return $this->cimVK->CityOutcomeDay($cid, $dar);
    }
    //Вывод расходов города за интервал
    function OutcomeIntervalVK($cid)
    {
        return $this->cimVK->CityOutcomeInterval($cid, $this->dt, $this->dt_to);
    }
    // Вывод всех расходов по интервалу
    function OutcomeIntervalVKAll()
    {
        return $this->cimVK->OutcomeIntervalAll($this->dt, $this->dt_to);
    }
}

class manager extends msqlwork
{
    /**Расчет процентного содержания сумм зарплат менеджеров за неделю. $total - сумма комиссии**/
    function pay_manager($total)
    {
        $uCRM=new usersCRM();
        $uar=array();
        $uarcount=0;
        $sumatra=0;
        $ruserrs = $uCRM->getManagersInprocent();
        $num_rows = count($ruserrs);
        /**Распределяем сумму согласно количеству и процентовки**/
        foreach ($ruserrs as $user)
        {
            $uar[$uarcount][0]=$user['id'];
            $uar[$uarcount][1]=$user['name'];
            $uar[$uarcount][2]=$user['inprocent'];
            $uar[$uarcount][3]=round($total/$num_rows/100*$user['inprocent']);
            $sumatra+=round($total/$num_rows/100*$user['inprocent']);
            $uarcount++;
        }
        $pipec=$total-$sumatra;
        if($pipec>0) {
            $ruserrs =  $uCRM->get100percMenegers();
            $num_rows = count($ruserrs);
            $pipec=$pipec/$num_rows;
        }
        else {
            $pipec=0;
        }
        /**Делим остаток суммы между теми у кого 100%**/
        $ix=0;
        foreach ($uar as $u)
        {
            $proc=$u[2];
            if ($proc==100) $uar[$ix][3]=$u[3]+$pipec;
            $ix++;
        }
        return $uar;
    }
}

class payzp extends msqlwork
{
    public $razdel;
    public $url;

    function __construct($dt, $razdel, $url)
    {
        $this->dt = $dt;
        $this->razdel=$razdel;
        $this->url=$url;
    }
    // Вывод суммы расходов beauty Anton
    function Outcome()
    {
        $mw=new master_week();
        $mw->set_dt($this->dt);
        return $mw->outcomeVorkVk();
    }
    // Выводит расходы для ежа
    function OutcomeEzh()
    {
        $ezhcw=new ezh_city_week();
        $ezhcw->set_dt($this->dt);
        return $ezhcw->CityesWorkWeek();
    }
    //Вывод показателя оплаты
    function pay($komu)
    {
        $pz=new paymentszp();
        $pz->set_dt($this->dt);
        return $pz->payzp($komu);
    }

    //Вывод недельного показателя оплаты
    function paySellerEzh()
    {
        $ezh_city=new ezh_city();
        $cityes=$ezh_city->selAllCity();
        $prc=new pr_city();
        $allPay=0;
        $pr_order=new pr_order();
        $pr_order->set_dt($this->get_monday());
        $pr_order->set_dt_to($this->get_sunday());
        foreach ($cityes as $city)
        {
            $pr=$prc->getCityByName($city["name"]);
            if ($city["name"]!=$pr["name"]) continue;
            $count=$pr_order->getSumsPeriodNumberOrdersDone($pr["id"]);
            $allPay=$allPay+$count*(int)$city['bonus'];
        }
        return round($allPay);
    }

    // Вывод недельного показателя новых бонусов
    function paySellerN($id)
    {
        $pr_order=new pr_order();
        $bonusoperatorezh=new bonusoperatorezh($id);
        $bonus_rewardsezh=new bonus_rewardsezh($bonusoperatorezh->getID());
        $bonus=$bonusoperatorezh->getBonus();
        $pr_order->set_dt($this->get_monday());
        $pr_order->set_dt_to($this->get_sunday());
        $s=$pr_order->getSummMargaperiod();
        $rewards=$bonus_rewardsezh->getRewards();
        $porog=0;
        foreach ($rewards as $r)
        {
            if ($r["summ"]<$s) $porog=$r["reward"];
        }
        if ($s!=0) {$s=round(($s-$pr_order->skidkaPeriod())/100*$bonus)+$porog;} else $s=0;
        return $s;
    }

    function paySellerN2bezProc($id)
    {
        $pr_order=new pr_order();
        $bonusoperatorezh=new bonusoperatorezh($id);
        $bonus_rewardsezh=new bonus_rewardsezh($bonusoperatorezh->getID());
        $bonus=$bonusoperatorezh->getBonus();
        $pr_order->set_dt($this->get_monday());
        $pr_order->set_dt_to($this->get_sunday());
        $s=$pr_order->getSummMargaperiod();
        $rewards=$bonus_rewardsezh->getRewards();
        if ($s!=0) {$s=round($s/100*$bonus);} else $s=0;
        return $s;
    }

    function checkPaid($paid, $spay, $funcY, $komu, $returnblock)
    {
        $html="";
        $func = $funcY;
        $razdel=$this->razdel;
        $url=$this->url;
        if ($paid == 1) {
            $html .= "<div class='T_M_N_B_4'><button class='T_M_N_B_BUTTON_PAYED' onclick='$func(\"".$this->dt."\", 0, \"$razdel\", \"$url\", $komu, 0, \"$returnblock\");return false;'>Оплачено</button></div>";
        } elseif ($paid == 0) {
            $html .= "<div class='T_M_N_B_4'><button class='T_M_N_B_BUTTON_PAY' onclick='$func(\"".$this->dt."\", $spay, \"$razdel\", \"$url\", $komu, 1, \"$returnblock\");return false;'>Оплатить</button></div>";
        }
        elseif ($paid == 2) {
            $html .= "<div class='T_M_N_B_4'>Нет записи</div>";
        }
        return $html;
    }

    // Отрисовка шаблона Антона
    function showTemplate($komu, $m_name,$spay, $funcY, $returnblock)
    {
        $paid=$this->pay($komu);
        $html="";
        $html .= "<div class='T_M_N_B_bezborder'>";
        $html .= "<div class='T_M_N_B_1'>$m_name</div>";
        $html .= "<div class='T_M_N_B_2'><b>".$spay."</b></div>";
        $html .= "<div class='T_M_N_B_3'>";
        $html .= $this->checkPaid($paid, $spay, $funcY, $komu, $returnblock);
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='T_M_both'></div>";
        return $html;
    }

    // Отрисовка шаблона Антона
    function showTemplateNone($komu, $m_name,$spay, $funcY, $returnblock)
    {
        $paid=$this->pay($komu);
        $html="";
        $html .= "<div class='T_M_N_B_bezborder'>";
        $html .= "<div class='T_M_N_B_1'>$m_name</div>";
        $html .= "<div class='T_M_N_B_2'><b>".$spay."</b></div>";
        $html .= "<div class='T_M_N_B_3'>";
//        $html .= $this->checkPaid($paid, $spay, $funcY, $komu, $returnblock);
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='T_M_both'></div>";
        return $html;
    }

    // Отрисовка шаблона Ежа
    function showTemplateEzh($komu, $m_name,$spay, $funcY, $returnblock, $prefix, $show)
    {
        $paid=$this->pay($komu);
        $html="";
        $html .= "<div class='T_M_N_B_bezborder'>";
        $html .= "<div class='T_M_N_B_1'>$prefix Продавец ($m_name)</div>";
        $html .= "<div class='T_M_N_B_2'><b>".$spay."</b></div>";
        $html .= "<div class='T_M_N_B_3'>";
        if ($show>0) $html .= $this->checkPaid($paid, $spay, $funcY, $komu, $returnblock);
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='T_M_both'></div>";
        return $html;
    }

    // Отрисовка шаблона
    function showTemplateManager($manager_id,$func, $returnblock)
    {
        $userCRM=new usersCRM();
        $user=$userCRM->getUserbyID($manager_id);
        $bonusHous=new bonushousemanager($this->dt, $manager_id);
        $total=$bonusHous->getManagerBonus();
        $manager=new manager();
        $uar=$manager->pay_manager($total);
        $m_name = "Менеджер (".$user['name'].")";
        $html = "";
        $html .= "<div class='T_M_N_B_1'>$m_name</div>";
        $html .= "<div class='T_M_N_B_2'><b>$total</b></div>";
        $html .= "<div class='T_M_N_B_3'>";
        $html .= "</div>";
        $html .= "<div class='T_M_N_B_5'>";
        $html .= "<table class='T_M_N_B_TABLE'>";
        foreach ($uar as $u)
        {
            $idu=$u[0];
            $nm=$u[1];
            $proc=$u[2];
            $sum=$u[3];
            $html .= "<tr>";
            $html .= "<td class='T_M_N_B_TABLE_TD1'>".$nm." ($proc%)</td>";
            $html .= "<td class='T_M_N_B_TABLE_TD2'>$sum</td>";
            $paid=$this->pay($idu);
            $html .= "<td>".$this->checkPaid($paid, $sum, $func, $idu, $returnblock)."</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
        $html .= "<div class='T_M_both'></div>";

        return $html;
    }

    // Отрисовка шаблона
    function showTemplateOperator($manager_id,$func, $returnblock)
    {
        $userCRM=new usersCRM();
        $user=$userCRM->getUserbyID($manager_id);
        $bonusHous=new bonushouseoper($this->dt, $manager_id);
        $spay=$bonusHous->getOperatorBonus();
        $html="";
        $paid=$this->pay($manager_id);
        $html="";
        $html .= "<div class='T_M_N_B_bezborder'>";
        $html .= "<div class='T_M_N_B_1'>Оператор (".$user['name'].")</div>";
        $html .= "<div class='T_M_N_B_2'><b>".$spay."</b></div>";
        $html .= "<div class='T_M_N_B_3'>";
        $html .= $this->checkPaid($paid, $spay, $func, $manager_id, $returnblock);
        $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class='T_M_both'></div>";
        return $html;
    }

    // Происходит оплата или снятие оплаты с кнопки
    function op_pay_vka($id, $sum, $pid)
    {
        $dt=$this->dt;
        $q = "update paymentszp set paid=$pid, summa=$sum where date='$dt' and komu=$id";
        mysql_query($q);
        if ($this->razdel == 8){
            $usersCRM =new usersCRM();
            $user=$usersCRM->getUserbyID($id);
            if ($id==1 || $id==77777) return $this->showVKRabota(); //else return $this->showManagers();
            if ($user['type']==1) return $this->showManagers();
            if ($user['type']==6) return $this->showEzhSeller($id, $user['name']);
            if ($user['type']==7) return $this->showOperator($id);
        }
        return 0;
    }

    // Отрисовать блок работы
    function showVKRabota(){
        $m_name = "ВК работа (Общий)";
        $html= $this->showTemplate(1, $m_name, $this->Outcome()+$this->OutcomeEzh(),'pay_vka', "#vkrab");
        $m_name = "ВК работа (Beauty)";
        $html.= $this->showTemplateNone(1, $m_name, $this->Outcome(),'pay_vka', "#vkrab");
        $m_name = "ВК работа (Ёж)";
        $html.=$this->showTemplateNone(77777, $m_name, $this->OutcomeEzh(),'pay_vka', "#vkrab");
        return $html;
    }

    // Отрисовать блок Менеджеров
    function showManagers(){
        $html=$this->showTemplateManager(119, 'pay_vka', "#master119");
        return $html;
    }

    // Отрисовать блок старых бонусов Ежа
    function showEzhSeller($id, $name){
        //$html= $this->showTemplateEzh($id, $name, $this->paySellerEzh(),'pay_vka', "#prodavec$id", "Старые бонусы",1);
        $html= $this->showTemplateEzh($id, $name, $this->paySellerN($id),'pay_vka', "#prodavec$id","Новые бонусы",1);
        return $html;
    }
    // Отрисовать блок Операторов
    function showOperator($id){
        $html= $this->showTemplateOperator($id,'pay_vka', "#operator$id");
        return $html;
    }

}

class logs extends msqlwork
{
    public $userId;
    function __construct($user, $dt)
    {
        $this->userId = $user;
        $this->dt = $dt;
    }

    function showLog($mast)
    {
        $res=$this->sfwo("*", "logs", "id>0 and description like '%$mast%'", "id DESC");
        return $res;
    }

    function seveLog($table, $query, $description)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))        // Определяем IP
        { $ip=$_SERVER['HTTP_CLIENT_IP']; }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
        else { $ip=$_SERVER['REMOTE_ADDR']; }
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'YandexBot')) {$bot='YandexBot';} //Выявляем поисковых ботов
        elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot')) {$bot='Googlebot';}
        else { $bot=$_SERVER['HTTP_USER_AGENT']; }

        $this->itab("logs", "id_user, dt, tablename, query, description, ip, useragent", $this->userId.", '".$this->dt."', '$table', '$query', '$description', '$ip', '$bot'");
    }
}

class stats extends msqlwork
{
    public $iduser;
    public $id_master;
    public $city_id;
    public $post;

    function __construct($post)
    {
        $this->post=$post;
        $this->dt = $this->post['dt'];
    }

    function save_sum_no_self($mox,$tux,$wex,$thx,$frx,$sax,$sux)
    {
        if (isset($mox) || isset($tux) || isset($wex) || isset($thx) || isset($frx) || isset($sax) || isset($sux)){
            if ($mox=='') $mox = 'NULL';
            if ($tux=='') $tux = 'NULL';
            if ($wex=='') $wex = 'NULL';
            if ($thx=='') $thx = 'NULL';
            if ($frx=='') $frx = 'NULL';
            if ($sax=='') $sax = 'NULL';
            if ($sux=='') $sux = 'NULL';
            $sum_no_self=$mox+$tux+$wex+$thx+$frx+$sax+$sux;
            $mid=$this->id_master;
            $dt=$this->dt;
            $this->itabdub("master_week", "id_master=$mid,sum_no_self=$sum_no_self,sum_no_self_mo=$mox,sum_no_self_tu=$tux,sum_no_self_we=$wex,sum_no_self_th=$thx,sum_no_self_fr=$frx,sum_no_self_sa=$sax,sum_no_self_su=$sux,dt='$dt'", "sum_no_self=$sum_no_self,sum_no_self_mo=$mox,sum_no_self_tu=$tux,sum_no_self_we=$wex,sum_no_self_th=$thx,sum_no_self_fr=$frx,sum_no_self_sa=$sax,sum_no_self_su=$sux");
        }

    }

    function save_zapis($id_procedure, $mon,$tu,$we,$th,$fr,$sa,$su,$monvk,$tuvk,$wevk,$thvk,$frvk,$savk,$suvk)
    {
        $dt2=$this->get_sunday();
        $id_master=$this->id_master;
        $vi=$mon+$tu+$we+$th+$fr+$sa+$su;
        $vvk=$monvk+$tuvk+$wevk+$thvk+$frvk+$savk+$suvk;
        $this->itabdub("master_procedure_day", "zap_mon=$mon, zap_tu=$tu, zap_we=$we, zap_th=$th, zap_fr=$fr, zap_sa=$sa, zap_su=$su, records=$vi, recordsvk=$vvk, zap_monvk=$monvk, zap_tuvk=$tuvk, zap_wevk=$wevk, zap_thvk=$thvk, zap_frvk=$frvk, zap_savk=$savk, zap_suvk=$suvk,id_master=$id_master,id_procedure=$id_procedure,dt='$dt2'", "records=$vi, zap_mon=$mon, zap_tu=$tu, zap_we=$we, zap_th=$th, zap_fr=$fr, zap_sa=$sa, zap_su=$su, recordsvk=$vvk, zap_monvk=$monvk, zap_tuvk=$tuvk, zap_wevk=$wevk, zap_thvk=$thvk, zap_frvk=$frvk, zap_savk=$savk, zap_suvk=$suvk");
    }

    function save_visitors($id_procedure,$movis,$tuvis,$wevis,$thvis,$frvis,$savis,$suvis)
    {
        //$this->iduser=$this->post['idr']; // ид менеджера или опера для логов
        if ($movis=='') $movis = 'NULL';
        if ($tuvis=='') $tuvis = 'NULL';
        if ($wevis=='') $wevis = 'NULL';
        if ($thvis=='') $thvis = 'NULL';
        if ($frvis=='') $frvis = 'NULL';
        if ($savis=='') $savis = 'NULL';
        if ($suvis=='') $suvis = 'NULL';
        $id_master=$this->id_master;
        $dt=$this->dt;
        $visitors=(int)$movis+(int)$tuvis+(int)$wevis+(int)$thvis+(int)$frvis+(int)$savis+(int)$suvis;
        $this->itabdub("master_procedure_week", "visitors=$visitors,visitors_mo=$movis,visitors_tu=$tuvis,visitors_we=$wevis,visitors_th=$thvis,visitors_fr=$frvis,visitors_sa=$savis,visitors_su=$suvis,id_master=$id_master,id_procedure=$id_procedure,dt='$dt'", "visitors=$visitors,visitors_mo=$movis,visitors_tu=$tuvis,visitors_we=$wevis,visitors_th=$thvis,visitors_fr=$frvis,visitors_sa=$savis,visitors_su=$suvis");

        if ($movis=='NULL') $movis = '';
        if ($tuvis=='NULL') $tuvis = '';
        if ($wevis=='NULL') $wevis = '';
        if ($thvis=='NULL') $thvis = '';
        if ($frvis=='NULL') $frvis = '';
        if ($savis=='NULL') $savis = '';
        if ($suvis=='NULL') $suvis = '';
        date_default_timezone_set("Asia/Bishkek");
        $dtCC=date("Y-m-d H:i:s"); // дата для лога
        $dt=date("d.m.Y H:i:s");
        $masters=new masters();
        $procedures=new procedures();
        $logs=new logs($this->iduser, $dtCC);
        $logs->seveLog('master_procedure_week', "visitors=$visitors,visitors_mo=$movis,visitors_tu=$tuvis,visitors_we=$wevis,visitors_th=$thvis,visitors_fr=$frvis,visitors_sa=$savis,visitors_su=$suvis", "<table><tr><td>Мастер = <b>".$masters->MastersById("$id_master")."</b> </td></tr><tr><td>Процедура = <b>".$procedures->getProcNameById($id_procedure)."</b></td></tr></table>Общее кол-во пришедших= $visitors <br><table><tr><td style=\'border: 1px solid; padding: 4px;\'>пн=$movis </td> <td style=\'border: 1px solid; padding: 4px;\'> вт=$tuvis </td><td style=\'border: 1px solid; padding: 4px;\'> ср=$wevis </td><td style=\'border: 1px solid; padding: 4px;\'> чт=$thvis </td><td style=\'border: 1px solid; padding: 4px;\'> пт=$frvis </td><td style=\'border: 1px solid; padding: 4px;\'> сб=$savis </td><td style=\'border: 1px solid; padding: 4px;\'> вс=$suvis </td><tr></tr></table>дата = ".$dt);
    }

    function getMID()
    {
        return $this->sfw("id_master", "masters", "id=".$this->id_master)[0]["id_master"];
    }

    // Общая функция сохранения бля так тебе
    function saveSelfZapVis()
    {
        date_default_timezone_set("Asia/Bishkek");
        $this->set_dt_to($dtCC=date("Y-m-d H:i:s")); // дата для лога
        $this->id_master=intval($this->post['id_master']);
        $this->city_id = intval($this->post['city_id']);
        $mox=$this->post['sum_no_self'.$this->id_master.'_mo'];
        $tux=$this->post['sum_no_self'.$this->id_master.'_tu'];
        $wex=$this->post['sum_no_self'.$this->id_master.'_we'];
        $thx=$this->post['sum_no_self'.$this->id_master.'_th'];
        $frx=$this->post['sum_no_self'.$this->id_master.'_fr'];
        $sax=$this->post['sum_no_self'.$this->id_master.'_sa'];
        $sux=$this->post['sum_no_self'.$this->id_master.'_su'];
        $this->save_sum_no_self($mox,$tux,$wex,$thx,$frx,$sax,$sux);
        $mx = array();
        foreach($this->post as $k=>$v){ if (preg_match('/p_(\d+)_(\d+)/',$k, $m)) { array_push($mx, $m[1]); } } // Ловим id процедур
        foreach ($mx as $id_procedure)
        {
            $mon=(int)$this->post['p_'.$id_procedure.'_mon_zap'];
            $tu=(int)$this->post['p_'.$id_procedure.'_tu_zap'];
            $we=(int)$this->post['p_'.$id_procedure.'_we_zap'];
            $th=(int)$this->post['p_'.$id_procedure.'_th_zap'];
            $fr=(int)$this->post['p_'.$id_procedure.'_fr_zap'];
            $sa=(int)$this->post['p_'.$id_procedure.'_sa_zap'];
            $su=(int)$this->post['p_'.$id_procedure.'_su_zap'];
            $monvk=(int)$this->post['p_'.$id_procedure.'_mon_zapvk'];
            $tuvk=(int)$this->post['p_'.$id_procedure.'_tu_zapvk'];
            $wevk=(int)$this->post['p_'.$id_procedure.'_we_zapvk'];
            $thvk=(int)$this->post['p_'.$id_procedure.'_th_zapvk'];
            $frvk=(int)$this->post['p_'.$id_procedure.'_fr_zapvk'];
            $savk=(int)$this->post['p_'.$id_procedure.'_sa_zapvk'];
            $suvk=(int)$this->post['p_'.$id_procedure.'_su_zapvk'];
            $this->save_zapis($id_procedure, $mon,$tu,$we,$th,$fr,$sa,$su,$monvk,$tuvk,$wevk,$thvk,$frvk,$savk,$suvk);
            $movis=$this->post['px_'.$id_procedure.'_mo'];
            $tuvis=$this->post['px_'.$id_procedure.'_tu'];
            $wevis=$this->post['px_'.$id_procedure.'_we'];
            $thvis=$this->post['px_'.$id_procedure.'_th'];
            $frvis=$this->post['px_'.$id_procedure.'_fr'];
            $savis=$this->post['px_'.$id_procedure.'_sa'];
            $suvis=$this->post['px_'.$id_procedure.'_su'];
            $this->save_visitors($id_procedure,$movis,$tuvis,$wevis,$thvis,$frvis,$savis,$suvis);
        }
        /**Сохранение бонусов в таблицу**/
        save_operators_statist($this->dt, $this->dt);
        save_manager_statist($this->dt);
        $bonus_rewards=new bonus_rewards(1);
        $bonus_rewards->set_dt($this->dt);
        $bonus_rewards->saveRevards();
    }
}

?>
