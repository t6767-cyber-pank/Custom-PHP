<?php
class InterFC extends msqlwork
{
    public $requesr;

    function __construct($requesr)
    {
        $this->requesr=$requesr;
    }

    function getTopBlockStart($class="T_M_Top_block")
    {
        return "<div class='$class' id='top_block'>";
    }

    function getTopBlockEnd()
    {
        return "</div>";
    }

    function getStyleMenu($req, $name, $hreef, $style, $target)
    {
        $html="<span class='$style'>";
        if ($this->requesr==$req)
             {
                 $html.="<b class='T_M_menue_item_act'>";
                 $html.=$name;
                 $html.="</b>";
             }
        else
             {
                 $html.="<a $target class='T_M_menue_a' href='$hreef'>";
                 $html.=$name;
                 $html.="</a>";
             }
        $html.="</span>";
        return $html;
    }


    function GetMenue($idmenu=1)
    {
        $html="";
        switch ($idmenu) {
            case 1:
                $html .= "<div id='menu' class='T_M_menue'>";
                $html .= $this->getStyleMenu(1, "Статистика по неделям", "/index.php?razdel=1", "", "");
                $html .= $this->getStyleMenu(7, "Прибыль мастеров", "/index.php?razdel=7", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(2, "Аналитика", "/index.php?razdel=2", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(3, "Настройки", "/index.php?razdel=3", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(4, "Диаграммы", "/index.php?razdel=4", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(77, "Диаграмма чатов", "/diagramchats.php?razdel=77", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(8, "Бонусы", "/index.php?razdel=8", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(9, "План продаж", "/index.php?razdel=9", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(6, "Магазин \"Еж принес\"", "/index.php?razdel=6", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(404, "Заказы", "/program.php?razdel=4", "T_M_menue_span", "target='_blank'");
                $html .= $this->getStyleMenu(404, "Аналитика ВК", "/zsortproc.php", "T_M_menue_span", "target='_blank'");
                $html .= $this->getStyleMenu(700, "Мониторинг", "/monitoring/monitor.php?razdel=700", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(701, "Мониторинг2", "/monitoring/monitorvrem.php?razdel=701", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(404, "Выход", "/index.php?logout=1", "T_M_menue_exit", "");
                $html .= "</div>";
        break;
            case 2:
                $html .= "<div id='menu' class='T_M_menue'>";
                $html .= $this->getStyleMenu(701, "Статистика", "/statistic/index.php?razdel=701", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(0, "Аналитика", "/index.php", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(1, "Диаграммы", "/index.php?razdel=1", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(2, "Статистика", "/index.php?razdel=2", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(77, "Диаграмма чатов", "/diagramchats.php?razdel=77", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(404, "Аналитика ВК", "/zsortproc.php", "T_M_menue_span", "target='_blank'");
                $html .= $this->getStyleMenu(700, "Мониторинг", "/monitoring/monitor.php?razdel=700", "T_M_menue_span", "");
                $html .= $this->getStyleMenu(404, "Выход", "/index.php?logout=1", "T_M_menue_exit", "");
                $html .= "</div>";
                break;
            case 3:
                $html .= "<div id='menu' class='T_M_menue3'>";
                $html .= $this->getStyleMenu(1, "Недельный план", "/index.php", "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(2, "Статистика", "/index.php?razdel=2", "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(77, "Диаграммы", "/diagramchats2.php?name=".$_COOKIE['name'], "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(700, "Мониторинг", "/monitoring/monitor.php?razdel=700", "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(800, "Коммиссии", "/masterscomissions.php", "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(801, "Статус записей", "/managerplan/index.php?razdel=801", "T_M_menue_span2", "");
                $html .= $this->getStyleMenu(404, "Выход", "/index.php?logout=1", "T_M_menue_exit", "");
                $html .= "</div>";
                break;
        }
        return $html;
    }
}
?>