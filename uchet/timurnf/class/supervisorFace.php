<?php
class supervisorFace extends msqlwork
{
    public $m_city;
    public $usersCRM;
    public $masters;
    public $bonus;

    function __construct()
    {
        $this->m_city=new m_city();
        $this->usersCRM=new usersCRM();
        $this->masters=new masters();
        $this->bonus=new bonus();
    }

    // Показать города
    function showCityes()
    {
        $city=$this->m_city->allCities();
        $html="";
        $html.='<div class="left T_M_admin_City">';
        $html.='<h4>Города</h4>';
        $html.="<div id='m_cities'>";
        foreach ($city as $c){
            $m_id = $c['id'];
            $m_name = $c['name'];
            $m_proc = $c['procent'];
            $html.="<div id='".rand()."' class='T_M_margin1010'>";
            $html.="<input type='hidden' class='m_id' value='$m_id'>";
            $html.="<div class='T_M_admin_City_block'>Название: <input type='text' class='m_name' value='".htmlspecialchars($m_name)."'></div>";
            $html.="<div class='T_M_admin_City_block'>Коэффициент: <input type='text' class='m_proc T_M_input_numb' value='".htmlspecialchars($m_proc)."'></div>";
            $html.="<div class='T_M_admin_City_save_btn'><input type='button' class='orange' value='Сохранить' onclick='save_m_city($(this).parent().parent().find(\".m_id\").get(0).value,$(this).parent().parent().find(\".m_name\").get(0).value,$(this).parent().parent().find(\".m_proc\").get(0).value, \"".$_SERVER['PHP_SELF']."\")'></div>";
            $html.="</div>";
        }
        $html.="</div>";
        $html.="<div class='add_link'>";
        $html.="<a href='' onclick='add_m_city(\"".$_SERVER['PHP_SELF']."\");return false;'>Добавить город</a>";
        $html.="</div>";
        $html.="</div>";
        return $html;
    }

    // Показать зависимость мастеров к операторам
    function showOperators()
    {
        $html="<div class='options_block T_M_E_S_Tcenter' style='border: 0px'>";
        $html.="<h2>Операторы и их мастера</h2>";
        $users=$this->usersCRM->getUsersbyType(7);
        $html.="<table class='T_M_show_oper'>";
        foreach ($users as $user) {
            $html.="<tr>";
            $i=0;
            $html.="<td class='T_M_pad5px'>";
            $html.="<b>".$user["name"]."</b>: ";
            $html.="</td>";
            $mast = $this->masters->MastersByOperator($user["id"]);
            $html.="<td class='T_M_pad5px'>";
            foreach ($mast as $mter)
            {
                if ($i==0) $html.=$mter["name"]; else $html.=", ".$mter["name"];
                $i++;
            }
            $html.="</td>";
            $html.="</tr>";
        }
        $html.="</table>";
        $html.="</div>";
        return $html;
    }

    function showSaverBonus()
    {
        $html="<div class='options_block T_M_E_S_Tcenter' style='border: 0px'>";
        $html.="<h2>Бонусы операторов</h2>";
        $bval=$this->bonus->selbonOper();
        $html.="<table class='T_M_B_saver'>";
        $html.="<tr>";
        $html.="<td>1 балл = </td>";
        $html.="<td><input type='text' class='T_M_compW' name='base_percent' id='baseprocid777' value='".$bval['base_percent']."' required/> тг.</td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td>цена за бал операторов = </td>";
        $html.="<td><input type='text' class='T_M_compW' name='base_percent' id='baseball777' value='".$bval['procopernew']."' required/> тг. </td>";
        $html.="</tr>";
        $html.="<tr>";
        $html.="<td><button class='orange' onclick='savebaseproc(\"".$_SERVER['PHP_SELF']."\")'>Сохранить</button></td>";
        $html.="<td><a target='_blank' <a href='/test.php'>Скачать excel расчет</a></td>";
        $html.="</tr>";
        $html.="<tr></tr>";
        $html.="</table>";
        $html.="</div>";
        return $html;
    }

}
?>