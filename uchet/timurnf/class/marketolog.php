<?php
class marketolog extends msqlwork
{

    public $cimp;
    public $ecw;
    public $ecd;
    public $ezh_shop;
    public $templat;
    public $master_week;

    function __construct($dt)
    {
        $this->dt = $dt;
        $this->cimp = new cityImportVKEzh();
        $this->ecw=new ezh_city_week();
        $this->ecd=new ezh_city_day();
        $this->ezh_shop=new ezh_shop();
        $this->master_week=new master_week();
        $this->cimp->set_dt($dt);
        $this->ecw->set_dt($dt);
        $this->ecd->set_dt($dt);
        $this->master_week->set_dt($dt);
        $this->templat=new templater();
    }

    function drawResults()
    {
        $arr=array();
        $arr[0]=(int)$this->master_week->getAllOutcomesInsta($this->dt);
        $arr[1]=(int)$this->master_week->getAllOutcomesVK($this->dt);
        $arr[2]=(int)$this->master_week->outcomeVorkVk();
        $arr[3]=(int)$this->ecw->CityesOutcomes();
        $arr[4]=(int)$this->cimp->CityesOutcomesWeek();
        $arr[5]=(int)$this->ecw->CityesWorkWeek();
        return $this->templat->printItog($arr);
    }

    function showTopBlock($id)
    {
        $html="";
        $html.='<div class="currencies">';
        $html.='<ul class="T_M_list_none">';
        $cur=$this->sfw("*", "currencies", "id>0");
        foreach ($cur as $_a) {
            $id = $_a['id'];
            $name = $_a['name'];
            $html.="<li class='currency T_M_Curency' id='currency_$id'>";
            $html.=$name." ";
            $html.="<input type='text' class='T_M_input_numb' onkeyup='change_currencies($id, $(this).val());' />";
            $html.="</li>";
        }
        $html.="<li class='vkrabota T_M_Curency' id='vkrabota_$id'>   Расходы на работу ВК  ";
        $html.='<input type="text" id="rvk000" class="T_M_input_numb T_M_margin60" />   Бюджет ВК  ';
        $html.='<input type="text" id="rvk111" class="T_M_input_numb T_M_margin60" />  ';
        $html.="<button style='width: 230px;' onclick='change_vkrabota($id, document.getElementById(\"rvk000\").value, document.getElementById(\"rvk111\").value);'>Заполнить расходы на работу ВК</button>";
        $html.="</li>";
        $html.="</ul>";
        $html.=$this->drawResults();
        $html.="</div>";
        return $html;
    }
    function showEzhBlock($id)
    {
        $html="";
        $html.="<div class='T_M_GREEN_BORDER_EZH'>";
        $html.="<div class='T_M_zag_Ezh_marketolog'><b>".$this->ezh_shop->Name($id)."</b></div>";
        $html.="<div style='width: 75%;'>".$this->templat->printTableContactsStat2ToMarketolog($this->ecd->ezhDirect(1), "INSTAGRAM DIRECT Казахстан")."</div>";
        $html.="<div style='width: 75%;'>".$this->templat->printTableContactsStat2ToMarketolog($this->ecd->ezhDirect(0), "INSTAGRAM DIRECT Россия")."</div>";
        $html.="<div class='T_M_VK_INPUTDIV'>";
        $html.="<div class='T_M_DIV_OBSH_RASH_VK'>Общие расходы ВК</div> <input type='text' class='outcomevk' id='outcomevk' value='".$this->cimp->CityesOutcomesWeek()."' disabled>";
        $html.="<div class='T_M_DIV_OBSH_VK'>Общий бюджет ВК</div> <input type='text' class='budgetvk' id='budgetvk' value='".$this->ecw->CityesBudgetWeek()."' >";
        $html.="<div class='T_M_DIV_OBSH_VK'>Общая ВК работа</div><input type='text' class='rabotavk' id='rabotavk' value='".$this->ecw->CityesWorkWeek()."' >";
        $html.="<br>";
        $html.="<div class='T_M_kont_VK'>";
        $ccVK=$this->ecd->CityesWorkWeekVK(1);
        $html.=$this->templat->printTableContacts($ccVK,"Новые контакты ВК");
        $html.="</div>";
        $html.="</div>";
        $html.="<div class='T_M_left'>";
        $html.=$this->ecd->settingsAllOutcomesInsta();
        $html.="</div>";
        $html.="<div class='T_M_both'></div>";
        $html.="</div>";
        return $html;
    }

    function showButtonsave()
    {
        $html="";
        $html.="<input type='hidden' id='dt' value='".$this->dt."'>";
        $html.="<input type='button' value='Сохранить' onclick='save_outcome()' class='orange T_M_Marketolog_Save'>";
        return $html;
    }

}
?>