<?php
require_once("./classes/testUI.php");
$testUI = new testUI($_POST['lang']);
$testUI->saveToBase($_POST['q1'], $_POST['q2'], $_POST['q3'], $_POST['q4'], $_POST['q5'], $_POST['q6'], round($_POST['res'],2));
?>