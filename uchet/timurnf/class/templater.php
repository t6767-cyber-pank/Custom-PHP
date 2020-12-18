<?php
class templater {
   /**Вывод Таблицы контакты для маркетолога берет в себя массив данных**/
    function printTableContacts($cc, $namefield)
    {
        $html ="";
        $html .= "<table frame='none' rules='void'>";
        $html .= "<tr>";
        $html .= "<td class='T_M_new_contacts'>$namefield</td>";
        $arr = array('','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
        for ($i = 1;$i<count($arr);$i++){
            $html .= "<td class='T_M_headerDayWeekS'>".$arr[$i]."</td>";
        }
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td></td>";
        for($i=0;$i<=6;$i++){
            $html .= "<td align='center'>".$cc[$i]."</td>";
        }
        $html .= "</tr>";
        $html .= "</table>";
        return $html;
    }

    /**Вывод Таблицы контакты для маркетолога берет в себя массив данных**/
    function printTableContactsStat($cc, $namefield, $max, $mx=null)
    {
        $html ="";
        $html .= "<table frame='none' rules='void' class='T_M_N_B_TABLE_width'>";
        $html .= "<tr>";
        $html .= "<td class='T_M_new_contacts2'>";
        $html .= "</td>";
        $arr = array('','','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
        for ($i = 1;$i<count($arr);$i++){
            $html .= "<td class='T_M_headerDayWeekS2'>".$arr[$i]."</td>";
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'> Всего контактов по городу";
        $html .= "</td>";
        $html .= "<td class='T_M_N_B_TABLE_td_center'> Контактов за неделю";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td></td>";
        $x=0;
        $xlid=0;
        $html .= "<td class='T_M_new_contacts2'>$namefield";
        $html .= "</td>";
        for($i=0;$i<=6;$i++){
            $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".(int)$cc[$i]."</b>";
            $html .= "</td>";
            $x=$x+(int)$cc[$i];
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".$max."</b>";
        $html .= "</td>";
        $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".($x)."</b>";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "</table>";
        return $html;
    }

    function printTableContactsStat2($cc, $namefield)
    {
        $html ="";
        $html .= "<table frame='none' rules='void' class='T_M_N_B_TABLE_width'>";
        $html .= "<tr>";
        $html .= "<td class='T_M_new_contacts2'>";
        $html .= "</td>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        $html .= "<td></td>";

        $arr = array('','','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
        for ($i = 1;$i<count($arr);$i++){
            $html .= "<td class='T_M_headerDayWeekS2'>".$arr[$i]."</td>";
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'> Итого";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td></td>";
        $x=0;
        $html .= "<td class='T_M_new_contacts2' style='width: 250px;'>$namefield";
        $html .= "</td>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        for($i=0;$i<=6;$i++){
            $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".(int)$cc[$i]."</b>";
            $html .= "</td>";
            $x=$x+(int)$cc[$i];
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".($x)."</b>";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "</table>";
        return $html;
    }

    function printTableContactsStat2ToMarketolog($cc, $namefield)
    {
        $html ="";
        $html .= "<table frame='none' rules='void' class='T_M_N_B_TABLE_width'>";
        $html .= "<tr>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        $html .= "<td></td>";

        $arr = array('','','Пн','Вт','Ср','Чт','Пн','Сб','Вс');
        for ($i = 1;$i<count($arr);$i++){
            $html .= "<td class='T_M_headerDayWeekS2'>".$arr[$i]."</td>";
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'> Итого";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $x=0;
        $html .= "<td class='T_M_new_contacts2' style='width: 250px;'>$namefield";
        $html .= "</td>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        $html .= "<td></td>";
        for($i=0;$i<=6;$i++){
            $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".(int)$cc[$i]."</b>";
            $html .= "</td>";
            $x=$x+(int)$cc[$i];
        }
        $html .= "<td class='T_M_N_B_TABLE_td_center'><b>".($x)."</b>";
        $html .= "</td>";
        $html .= "</tr>";
        $html .= "</table>";
        return $html;
    }

    function printItog($arr)
    {
        $rasinst=$arr[0];
        $rasvk=$arr[1];
        $rasworkvk=$arr[2];
        $rasinstezh=$arr[3];
        $rasvkezh=$arr[4];
        $rasvkworkezh=$arr[5];
        $html ="";
        $html .='<div class="T_M_float_left">';
        $html .="<table>";
        $html .="<tr>";
        $html .="<td class='T_M_325'>Расходы на рекламу мастеров Instagram</td><td class='T_M_text_right'><b>$rasinst</b></td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td class='T_M_325'>Расходы на рекламу мастеров ВК</td><td class='T_M_text_right'><b>$rasvk</b></td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td class='T_M_325'>Расходы на работу по продвижению ВК</td><td class='T_M_text_right'><b>$rasworkvk</b></td>";
        $html .="</tr>";
        $html .="</table>";
        $html .='</div>';
        $html .='<div class="T_M_float_left">';
        $html .="<table>";
        $html .="<td class='T_M_325'>Расходы на рекламу Ежа Instagram</td><td class='T_M_text_right'><b>$rasinstezh</b></td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td class='T_M_325'>Расходы на рекламу Ежа ВК</td><td class='T_M_text_right'><b>$rasvkezh</b></td>";
        $html .="</tr>";
        $html .="<tr>";
        $html .="<td class='T_M_325'>Расходы на работу по продвижению ВК Ежа</td><td class='T_M_text_right'><b>$rasvkworkezh</b></td>";
        $html .="</tr>";
        $html .="</table>";
        $html .='</div>';
        return $html;
    }
}
?>
