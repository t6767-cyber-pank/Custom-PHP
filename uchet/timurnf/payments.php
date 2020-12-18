<?php
/**Расчет процентного содержания сумм зарплат менеджеров за неделю. $total - сумма комиссии**/
function pay_manager($total)
{
$uar=array();
$uarcount=0;
$sumatra=0;
$ruserrs = mysql_query("select * from users where inprocent>0 order by inprocent DESC");
$num_rows = mysql_num_rows($ruserrs);
            /**Распределяем сумму согласно количеству и процентовки**/
while ($as=mysql_fetch_array($ruserrs))
{
    $uar[$uarcount][0]=$as['id'];
    $uar[$uarcount][1]=$as['name'];
    $uar[$uarcount][2]=$as['inprocent'];
    $uar[$uarcount][3]=round($total/$num_rows/100*$as['inprocent']);
    $sumatra+=round($total/$num_rows/100*$as['inprocent']);
    $uarcount++;
}
            /*Распределяем сумму согласно количеству и процентовки*/
            /**Делим остаток суммы между теми у кого 100%**/
$pipec=$total-$sumatra;
if($pipec>0) {
    $ruserrs = mysql_query("select * from users where inprocent=100 order by inprocent DESC");
    $num_rows = mysql_num_rows($ruserrs);
    $pipec=$pipec/$num_rows;
}
else {
    $pipec=0;
}

$ix=0;
foreach ($uar as $u)
{
    $nm=$u[1];
    $proc=$u[2];
    if ($proc==100) $uar[$ix][3]=$u[3]+$pipec;
    $ix++;
}
            /*Делим остаток суммы между теми у кого 100%*/
return $uar;
}
/*Расчет процентного содержания сумм зарплат менеджеров за неделю*/
?>

