<?php
header('Content-type: text/html; charset=utf-8');
$dblocation = "localhost"; // Имя сервера
$dbuser = "root";          // Имя пользователя
$dbpasswd = "";            // Пароль
$db = "integra";
$dbcnx = @mysql_connect($dblocation,$dbuser,$dbpasswd);
mysql_select_db($db);
?>