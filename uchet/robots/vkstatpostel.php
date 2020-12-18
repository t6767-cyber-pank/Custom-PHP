<?
/**Общие подключения к базе**/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
chdir(dirname(__FILE__));
include '../mysql_connect.php';
/*Общие подключения к базе*/

/**Подготавливаем даты**/
$dt_to = date("Y-m-d");
$dt_from = date("Y-m-d",strtotime($dt_to."-1 days"));
/*Подготавливаем даты*/

/**Подготавливаем массивы**/
$drar = array(); // Массив дат
$crar = array(); // Массив городов
/*Подготавливаем массивы*/

/**Наполним массив обработки дат**/
$r = mysql_query("select data from timer where data between '$dt_from' and '$dt_to' order by  data asc");
while ($a = mysql_fetch_array($r)) { array_push($drar, $a['data']); }
/*Наполним массив обработки дат*/

/**Наполним массив обработки городов**/
$r = mysql_query("select distinct id_city from vk_users");
while($a = mysql_fetch_array($r)){ array_push($crar, $a['id_city']); }
/*Наполним массив обработки городов*/

/**Делаем сверку с существующими записями по датам и городам и вставляем или обновляем таблицу контактов ВК**/
foreach ($drar as $dt)
{
    foreach ($crar as $city)
    {
        $rx = mysql_query("select sum(uniq) as unic from vk_users where id_city=$city and dt='$dt'");
        $ax1 = mysql_fetch_array($rx);
        $chat=(int)$ax1['unic'];
        $rx = mysql_query("select * from m_city_day_vk where id_m_city=$city and dt='$dt'");
        if (mysql_num_rows($rx)==0)
        {
            mysql_query("insert into m_city_day_vk(id_m_city, dt, chatsvk) values($city, '$dt', $chat)");
            echo "insert into m_city_day_vk(id_m_city, dt, chatsvk) values($city, '$dt', $chat)<br>";
        }
        else
        {
            mysql_query("UPDATE m_city_day_vk set chatsvk=$chat where id_m_city=$city and dt='$dt'");
            echo "UPDATE m_city_day_vk set chatsvk=$chat where id_m_city=$city and dt='$dt'<br>";
        }
    }

    $rx = mysql_query("select sum(uniq) as unic from vk_usersezh where id_city=1 and dt='$dt'");
    $ax1 = mysql_fetch_array($rx);
    $chat=(int)$ax1['unic'];
    $rx = mysql_query("select * from ezh_city_day where id_city=1 and dt='$dt'");
    if (mysql_num_rows($rx)==0)
    {
        mysql_query("insert into ezh_city_day(id_city, dt, contacts, contactsvk) values(1, '$dt', 0, $chat)");
        echo "insert into ezh_city_day(id_city, dt, contacts, contactsvk) values(1, '$dt', 0, $chat)<br>";
    }
    else
    {
        mysql_query("UPDATE ezh_city_day set contactsvk=$chat where id_city=1 and dt='$dt'");
        echo "UPDATE ezh_city_day set contactsvk=$chat where id_city=1 and dt='$dt'<br>";
    }
}
/*Делаем сверку с существующими записями по датам и городам и вставляем или обновляем таблицу контактов ВК*/
?>