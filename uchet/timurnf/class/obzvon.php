<?php

class obzvon extends msqlwork
{
    public $pr_order;
    public $operation;
    public $tema;
    public $statuses;
    public $PHP_SELF;

    function __construct()
    {
        $this->pr_order=new pr_order();
        $this->statuses=array("","Перезвон", "Продажа", "Отказ", "Сама перезв", "Тел. выкл", "Сбросила");
        $this->PHP_SELF = $_SERVER['PHP_SELF'];
    }

    // Берем все записи с таблицы pr orders
    function getAllOrders($cid)
    {
        $res=$this->pr_order->getAllOrders($cid);
        return $res;
    }

    function  getAll2Weeks($cid)
    {
        $this->pr_order->set_dt(date("Y-m-d", strtotime($this->dt."-2 week")));
        $this->pr_order->set_dt_to($this->dt);
        $res=$this->pr_order->getAll2Weeks($cid);
        return $res;
    }

    function getAllPovtor($cid)
    {
        $res=$this->pr_order->getAllPovtor($cid);
        return $res;
    }

    function  getAll1razItishina($cid)
    {
        $this->pr_order->set_dt(date("Y-m-d", strtotime($this->dt."-1 month")));
        $this->pr_order->set_dt_to($this->dt);
        $res=$this->pr_order->getAll1razItishina($cid);
        return $res;
    }

    function getAllMnogo($cid)
    {
        $res=$this->pr_order->getAllMnogo($cid);
        return $res;
    }

    function getAllransheMnogo($cid)
    {
        $this->pr_order->set_dt(date("Y-m-d", strtotime($this->dt."-3 month")));
        $this->pr_order->set_dt_to($this->dt);
        $res=$this->pr_order->getAllransheMnogo($cid);
        return $res;
    }


    // Проверка на операцию
    function ajaxStart()
    {
        switch ($this->operation)
        {
            case "showAll": $this->tema="Обзвон по общему списку"; return $this->writeTemplateAllClients(); break;
            case "1rnedavno": $this->tema="Купили 1 раз, недавно(2 нед. назад)"; return $this->writeTemplateAllClients2week(); break;
            case "povtor": $this->tema="Купили повторно"; return $this->writeTemplateAllClientsPovtor(); break;
            case "1razitishina": $this->tema="Купили 1 раз и больше месяца нет заказов"; return $this->writeTemplateAllClients1razitish(); break;
            case "mnogo": $this->tema="Покупают много и постоянно"; return $this->writeTemplateAllClientsMnogo(); break;
            case "mnogoranshe": $this->tema="Раньше много покупали, а сейчас не покупают"; return $this->writeTemplateAllClientsMnogoRanshe(); break;
        }
    }

    // Вывод всего списка
    function getAllClients()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
                $i = 1;
                foreach ($this->getAllOrders($c['id']) as $orda) {
                    $cid=$orda["cid"];
                    $pr_zvon = new pr_zvon($cid);
                    $zv = $pr_zvon->getZvon();
                    $class="";
                    $classL="";
                    switch ($zv["status"])
                    {
                        case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                        case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                        case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                        case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                        case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                        case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    }
                    $html.='<div class="row T_M_Ezh_obzvon_padding">';
                    $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                    $html.=$i . ".";
                    $html.="</div>";
                    $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                    $html.=$orda["name"];
                    $html.="</div>";
                    $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                    $html.=$orda["phone"];
                    $html.="</div>";
                    $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                    $html.=$orda["orc"];
                    $html.="</div>";
                    $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                    $html.=date("d.m.Y", strtotime($orda["dtx"]));
                    $html.="</div>";
                    $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                    if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                    $html.="</div>";
                    $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                    $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                    foreach ($this->statuses as $stas) {
                        $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                        $html.="<option $style value='$stas'>$stas</option>";
                    }
                    $html.="</select>";
                    $html.="</div>";
                    $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                    $html.=$zv["tema"];
                    $html.="</div>";
                    $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                    $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                    $html.="</div>";
                    $html.="</div>";
                    $i++;
                }
            $html.="</div>";
        }
        return $html;
    }

    // Вывод всего списка
    function getAllClients2week()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
            $i = 1;
            foreach ($this->getAll2Weeks($c['id']) as $orda) {
                $class="";
                $classL="";
                $cid=$orda["cid"];
                $pr_zvon = new pr_zvon($cid);
                $zv = $pr_zvon->getZvon();
                switch ($zv["status"])
                {
                    case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                    case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                    case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                    case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                }
                $html.='<div class="row T_M_Ezh_obzvon_padding">';
                $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                $html.=$i . ".";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["name"];
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["phone"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=$orda["orc"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=date("d.m.Y", strtotime($orda["dtx"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                foreach ($this->statuses as $stas) {
                    $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                    $html.="<option $style value='$stas'>$stas</option>";
                }
                $html.="</select>";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$zv["tema"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                $html.="</div>";
                $html.="</div>";
                $i++;
            }
            $html.="</div>";
        }
        return $html;
    }

    // Вывод всего списка
    function getAllClientsPovtor()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
            $i = 1;
            foreach ($this->getAllPovtor($c['id']) as $orda) {
                $class="";
                $classL="";
                $cid=$orda["cid"];
                $pr_zvon = new pr_zvon($cid);
                $zv = $pr_zvon->getZvon();
                switch ($zv["status"])
                {
                    case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                    case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                    case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                    case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                }
                $html.='<div class="row T_M_Ezh_obzvon_padding">';
                $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                $html.=$i . ".";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["name"];
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["phone"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=$orda["orc"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=date("d.m.Y", strtotime($orda["dtx"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                foreach ($this->statuses as $stas) {
                    $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                    $html.="<option $style value='$stas'>$stas</option>";
                }
                $html.="</select>";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$zv["tema"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                $html.="</div>";
                $html.="</div>";
                $i++;
            }
            $html.="</div>";
        }
        return $html;
    }

    // Вывод всего списка
    function getAllClients1raziumolk()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
            $i = 1;
            foreach ($this->getAll1razItishina($c['id']) as $orda) {
                $class="";
                $classL="";
                $cid=$orda["cid"];
                $pr_zvon = new pr_zvon($cid);
                $zv = $pr_zvon->getZvon();
                switch ($zv["status"])
                {
                    case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                    case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                    case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                    case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                }
                $html.='<div class="row T_M_Ezh_obzvon_padding">';
                $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                $html.=$i . ".";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["name"];
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["phone"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=$orda["orc"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=date("d.m.Y", strtotime($orda["dtx"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                foreach ($this->statuses as $stas) {
                    $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                    $html.="<option $style value='$stas'>$stas</option>";
                }
                $html.="</select>";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$zv["tema"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                $html.="</div>";
                $html.="</div>";
                $i++;
            }
            $html.="</div>";
        }
        return $html;
    }

    // Вывод всего списка
    function getAllMnogoIPost()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
            $i = 1;
            foreach ($this->getAllMnogo($c['id']) as $orda) {
                $cid=$orda["cid"];
                $pr_zvon = new pr_zvon($cid);
                $zv = $pr_zvon->getZvon();
                $class="";
                $classL="";
                switch ($zv["status"])
                {
                    case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                    case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                    case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                    case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                }
                $html.='<div class="row T_M_Ezh_obzvon_padding">';
                $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                $html.=$i . ".";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["name"];
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["phone"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=$orda["orc"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=date("d.m.Y", strtotime($orda["dtx"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                foreach ($this->statuses as $stas) {
                    $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                    $html.="<option $style value='$stas'>$stas</option>";
                }
                $html.="</select>";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$zv["tema"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                $html.="</div>";
                $html.="</div>";
                $i++;
            }
            $html.="</div>";
        }
        return $html;
    }

    // Вывод всего списка
    function getAllMnogoRansheBilo()
    {
        $html="";
        $pr_city = new pr_city();
        $city = $pr_city->getAllCity();
        foreach ($city as $c) {
            $html.="<h1 class='T_M_E_S_Tcenter T_M_Ezh_obzvon_header'>".$c['name']."</h1>";
            $html.="<div class='container-fluid T_M_Ezh_obzvon_box'>";
            $html.="<div class='row T_M_Ezh_obzvon_padding'>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>№ п/п</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Имя клиента</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Телефон</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Кол-во<br>заказов</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Последний<br>заказ</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Дата<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Статус</b></div>";
            $html.="<div class='col-2 T_M_E_S_Tcenter'><b>Тема<br>обзвона</b></div>";
            $html.="<div class='col-1 T_M_E_S_Tcenter'><b>Позвонить</b></div>";
            $html.="</div>";
            $i = 1;
            foreach ($this->getAllransheMnogo($c['id']) as $orda) {
                $cid=$orda["cid"];
                $pr_zvon = new pr_zvon($cid);
                $zv = $pr_zvon->getZvon();
                $class="";
                $classL="";
                switch ($zv["status"])
                {
                    case "Продажа": $class="T_O_Ezh_obzvon_button_green"; $classL="T_O_Ezh_obzvon_button_greenL"; break;
                    case "Перезвон": $class="T_O_Ezh_obzvon_button_orange"; $classL="T_O_Ezh_obzvon_button_orangeL"; break;
                    case  "Отказ": $class="T_O_Ezh_obzvon_button_red"; $classL="T_O_Ezh_obzvon_button_redL"; break;
                    case  "Сама перезв": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Тел. выкл": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                    case  "Сбросила": $class="T_O_Ezh_obzvon_button_none"; $classL="T_O_Ezh_obzvon_button_noneL"; break;
                }
                $html.='<div class="row T_M_Ezh_obzvon_padding">';
                $html.="<div class='col-1 T_M_E_S_Tcenter $classL'>";
                $html.=$i . ".";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["name"];
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$orda["phone"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=$orda["orc"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.=date("d.m.Y", strtotime($orda["dtx"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                if ($zv["data"] != "") $html.=date("d.m.Y", strtotime($zv["data"]));
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<select class='T_M_EZH_obzvon_select' id='sel$cid'>";
                foreach ($this->statuses as $stas) {
                    $style = ""; if ($stas == $zv["status"]) $style = " selected ";
                    $html.="<option $style value='$stas'>$stas</option>";
                }
                $html.="</select>";
                $html.="</div>";
                $html.="<div class='col-2 T_M_E_S_Tcenter'>";
                $html.=$zv["tema"];
                $html.="</div>";
                $html.="<div class='col-1 T_M_E_S_Tcenter'>";
                $html.="<button class='$class T_O_Ezh_obzvon_button' onclick='Call(".$cid.", \"".$this->tema."\", \"".$this->dt."\", \"".$this->operation."\", \"".$this->PHP_SELF."\");'>Позвонить</button>";
                $html.="</div>";
                $html.="</div>";
                $i++;
            }
            $html.="</div>";
        }
        return $html;
    }

    function writeMenu()
    {
        $html="";
        $html.="<div class='container T_M_Ezh_obzvon_header'>";
        $html.="<div class='row'>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"showAll\", \"".$this->PHP_SELF."\")'>Все контакты</button></div>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"1rnedavno\", \"".$this->PHP_SELF."\")'>Купили 1 раз недавно</button></div>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"povtor\", \"".$this->PHP_SELF."\")'>Купили повторно</button></div>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"1razitishina\", \"".$this->PHP_SELF."\")'>Купили 1 раз и больше месяца нет заказов</button></div>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"mnogo\", \"".$this->PHP_SELF."\")'>Покупают много и постоянно(больше 3х раз)</button></div>";
        $html.="<div class='col T_M_E_S_Tcenter'><button class='T_M_Ezh_obzvon_button' onclick='menu(\"mnogoranshe\", \"".$this->PHP_SELF."\")'>Раньше покупали много</button></div>";
        $html.="</div>";
        $html.="</div>";
        return $html;
    }

    function writeTemplateAllClients()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllClients();
        return $html;
    }

    function writeTemplateAllClients2week()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllClients2week();
        return $html;
    }

    function writeTemplateAllClientsPovtor()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllClientsPovtor();
        return $html;
    }

    function writeTemplateAllClients1razitish()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllClients1raziumolk();
        return $html;
    }

    function writeTemplateAllClientsMnogo()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllMnogoIPost();
        return $html;
    }

    function writeTemplateAllClientsMnogoRanshe()
    {
        $html="";
        $html.=$this->writeMenu();
        $html.=$this->getAllMnogoRansheBilo();
        return $html;
    }

}
?>