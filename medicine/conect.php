<?php
$dblocation = "localhost"; // Имя сервера
$dbuser = "root";          // Имя пользователя
$dbpasswd = "";            // Пароль
$db = "nauka";
$dbcnx = @mysql_connect($dblocation,$dbuser,$dbpasswd);
mysql_select_db($db);
?>