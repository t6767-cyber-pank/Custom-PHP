<?php
if(isset($_POST["id"])) {
    $id = $_POST["id"];
    require_once("./classes/testUI.php");
    $pizdyulator = new pizdyulator($id);
    $pizdyulator->showDevelopTemplate();
}
else {
    echo "нет доступа";
}
?>
