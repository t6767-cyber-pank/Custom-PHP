<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
switch ($_POST['operation']) {
    case "upstatus":
        $pr_order= new pr_order();
        $pr_order-> updateStatus($_POST['idproc'], $_POST['stat']);
        $styleRB1="";
        $styleRB2="";
        $styleRB3="";
        $chek1="";
        $chek2="";
        $chek3="";
        switch ($_POST['style'])
        {
            case 1: $styleRB1="box-shadow: 0px 0px 5px 1px green; color: green;"; $chek1="checked"; break;
            case 2: $styleRB2="box-shadow: 0px 0px 5px 1px orange; color: orange;"; $chek2="checked"; break;
            case 3: $styleRB3="box-shadow: 0px 0px 5px 1px orangered; color: orangered;"; $chek3="checked"; break;
        }
        echo "<form>";
        echo "<label style='$styleRB1 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px; margin-right: 7px;'><input type=\"radio\" id=\"contactChoice1\" name=\"contact\" value=\"email\" onclick='upstatus(".$_POST['idproc'].", 1, \"lob".$_POST['idproc']."\", 1)' $chek1>Дозаказ</label>";
        echo "<label style='$styleRB2 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px; margin-right: 7px;'><input type=\"radio\" id=\"contactChoice2\" name=\"contact\" value=\"email\" onclick='upstatus(".$_POST['idproc'].", 2, \"lob".$_POST['idproc']."\", 2)' $chek2>Недозвон</label>";
        echo "<label style='$styleRB3 padding-left: 1px; padding-right: 4px; padding-top: 3px; padding-bottom: 3px; border-radius: 50px;'><input type=\"radio\" id=\"contactChoice3\" name=\"contact\" value=\"email\" onclick='upstatus(".$_POST['idproc'].", 3, \"lob".$_POST['idproc']."\", 3)' $chek3>Отказ</label>";
        echo "</form>";
        break;
}
?>