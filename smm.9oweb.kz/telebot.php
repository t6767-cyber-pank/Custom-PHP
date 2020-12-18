<?php
header('Content-type: text/html; charset=utf-8');
// зона времени
date_default_timezone_set("Asia/Almaty");
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
require_once("api/reactionUI.php");
$reactionUI = new reactionUI($data);
$reactionUI->startIO();
?>