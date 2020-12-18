<?php
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
/**Общие подключения к классу для работы с бд**/
chdir(dirname(__FILE__));
include("../timurnf/class/mysqlwork.php");
/*Общие подключения к классу для работы с бд*/

$dt = date("Y-m-d"); // дата сегодняшняя

/**Вбиваем поля зарплат**/
$paymentszp=new paymentszp();
$paymentszp->insertData(1);
$paymentszp->insertData(77777);
$manager=new manager();
$uar=$manager->pay_manager(0);
foreach ($uar as $u)
{
    $paymentszp->insertData($u[0]);
}
$uar=$paymentszp->allSellers();
foreach ($uar as $u)
{
    $paymentszp->insertData($u[0]);
}
$uar=$paymentszp->alloperators();
foreach ($uar as $u)
{
    $paymentszp->insertData($u[0]);
}



/**Выводит дату понедельника и воскресения из выбранной даты**/
function get_monday ($dar)
{
    $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
    $adat = mysql_fetch_array($rdat);
    $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and yearweek=".$adat['yearweek']." and dayweek=1");
    $adat2 = mysql_fetch_array($rdat2);
    return $adat2['data'];
}
/**-------Выводит дату понедельника и воскресения из выбранной даты**/
/*Делаем разбивку на интервал*/
$dt_start=get_monday($dt);
$prev_week = date('Y-m-d', strtotime($dt_start) - (60*60*24*7));
/**-------Делаем разбивку на интервал**/

/**Берем данные с предыдущей недели и прогоняем по ним**/
$query = "select * from costs where (type = 2 || type = 3) and dt = '$prev_week' and isDeleted = 0";
    $r = mysql_query($query);
    while($a = mysql_fetch_array($r)){
      /**Пробегаемся по таблице используя дату понедельника и parent_id**/
      $query = "select * from costs where (type = 2 || type = 3) and dt = '$dt_start' and parentId = ". $a['parentId'];
      $_r = mysql_query($query);
      // Если нет записей в таблице по расходу то добавляем его туда
      if (mysql_num_rows($_r)==0){
        $query = "INSERT INTO `costs`(`name`, `summ`, `type`, `dt`, `parentId`) VALUES ('". $a['name']."','". $a['summ']."',". $a['type'].",'$dt_start',". $a['parentId'].")";
        $_u = mysql_query($query);
      };
      /**-------Пробегаемся по таблице используя дату понедельника и parent_id**/
    }
/**-------Берем данные с предыдущей недели и прогоняем по ним**/
?>