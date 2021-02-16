<?php
if(isset($_GET["id"])) {
    $id = $_GET["id"];
    require_once("./classes/testUI.php");
    $pizdyulator = new pizdyulator($id);
    $pizdyulator->showTemplate();
}
else {
    echo "нет данных";
}
//echo "<pre>";
//var_dump($pizdyulator->DB->GetJSONArrayToShow(1));
//echo "</pre>";
?>
