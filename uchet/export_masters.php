<?php
/* Заголовок и модули*/
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
/**Общие подключения к классу для работы с бд**/
chdir(dirname(__FILE__));
include("$DOCUMENT_ROOT/timurnf/class/mysqlwork.php");
require_once ("$DOCUMENT_ROOT/PHPExcel/PHPExcel/IOFactory.php");
require_once("$DOCUMENT_ROOT/PHPExcel/PHPExcel/Writer/Excel5.php");
/*___________________*/

/*Константы*/
$maxj = 1;
$rowCount1 = 0;
$rowCount2 = 0;
$rowCount3 = 0;
$mPercent=0;
/*_________*/

/*Получаем процент менеджера*/
function getBasePercent(){
    $q = "select base_percent from bonus where id=1";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    return (float)($a['base_percent']);
}
$proc=getBasePercent();
/*__________________________*/

/*Получаемые даты и их обработка*/
$m = array();
$dt1 = $_REQUEST['dt1'];
preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt1,$m);
$t1 = mktime(0,0,0,$m[2],$m[3],$m[1]);
$t1 = strtotime(date('o-\\WW',$t1));

$dt2 = $_REQUEST['dt2'];
$m = array();
preg_match('/(\d{4})-(\d{2})-(\d{2})/',$dt2,$m);
$t2 = mktime(0,0,0,$m[2],$m[3],$m[1]);
$t2 = strtotime(date('o-\\WW',$t2))+3600*24;
/*______________________________*/

/*Подключение EXCEL объекта*/
$xls = new PHPExcel();
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();
/*_________________________*/


/*Заголовок мастер*/
$sheet->setCellValueByColumnAndRow(0,1,"Мастер");
$sheet->getStyleByColumnAndRow(0,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyleByColumnAndRow(0,1)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$sheet->getStyleByColumnAndRow(0,1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$sheet->getStyleByColumnAndRow(0,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyleByColumnAndRow(0,1)->getFill()->getStartColor()->setRGB('E0E0E0');
$sheet->getStyleByColumnAndRow(0,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
/*________________*/


$sheet->freezePane('A2');
$sheet->freezePane('B1');

/*Вывод мастеров*/
$r = mysql_query("select u.*, m.shown from users u,masters m where u.id=m.id_master and u.type=0 order by m.shown desc, m.sort");
$i = 2;
while ($a = mysql_fetch_array($r)){
  $id = $a['id'];
  $name = $a['name'];
  $sheet->setCellValueByColumnAndRow(0,$i,$name);
  $sheet->getStyleByColumnAndRow(0,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  $sheet->getStyleByColumnAndRow(0,$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
	if ($a['shown'] != 1){
	  $sheet->getStyleByColumnAndRow(0,$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	  $sheet->getStyleByColumnAndRow(0,$i)->getFill()->getStartColor()->setRGB('#aaaaaa');
	}
    /*Вывод пустого поля*/
    $sheet->setCellValueByColumnAndRow(1,$i,"");
    $sheet->getStyleByColumnAndRow(1,$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->getStyleByColumnAndRow(1,$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $sheet->getStyleByColumnAndRow(1,$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
    $sheet->getStyleByColumnAndRow(1,$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow(1,$i)->getFill()->getStartColor()->setRGB('A0A0A0');
    /*_________________*/
    $i++;
}
$sheet->getColumnDimension('A')->setAutoSize(true);
/*______________*/

/*Счетчики*/
$t = $t1;
$i = 1;
$space = 2;
/*________*/

/*Вывод заголовка пустого поля*/
$sheet->setCellValueByColumnAndRow($i,1,"");
$sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('A0A0A0');
$cell = $sheet->getCellByColumnAndRow($i, 1);
$c = $cell->getColumn();
$sheet->getColumnDimension($c)->setWidth(3);
$i++;
/*____________________________*/

/*Перебор по датам*/
while($t<$t2){
/*вывод заголовков*/
  //вывод заголовка даты
  $dt = date("Y-m-d",$t);
  $sheet->setCellValueByColumnAndRow($i,1,PHPExcel_Shared_Date::PHPToExcel($t+3600*24*7-18*3600));
  $sheet->getStyleByColumnAndRow($i,1)->getNumberFormat()->setFormatCode('[$-FC19]dd\ mmmm\ yyyy\ \г\._);@');
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $cell = $sheet->getCellByColumnAndRow($i, 1);
  $c = $cell->getColumn();
  $sheet->getColumnDimension($c)->setWidth(20);
  $i++;
  //вывод заголовка расходов
  $sheet->setCellValueByColumnAndRow($i,1,"Расходы INSTAGRAMM");
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setWrapText(true);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $cell = $sheet->getCellByColumnAndRow($i, 1);
  $c = $cell->getColumn();
  $sheet->getColumnDimension($c)->setWidth(14);
  $i++;
    //вывод заголовка расходов VK
    $sheet->setCellValueByColumnAndRow($i,1,"Расходы VK");
    $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setWrapText(true);
    $cell = $sheet->getCellByColumnAndRow($i, 1);
    $c = $cell->getColumn();
    $sheet->getColumnDimension($c)->setWidth(9);
    $i++;
    //вывод заголовка расходов работы VK
    $sheet->setCellValueByColumnAndRow($i,1,"Работа VK");
    $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setWrapText(true);
    $cell = $sheet->getCellByColumnAndRow($i, 1);
    $c = $cell->getColumn();
    $sheet->getColumnDimension($c)->setWidth(8);
    $i++;
  //Вывод заголовка бонусов
  $sheet->setCellValueByColumnAndRow($i,1,"Бонусы менеджеров");
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setWrapText(true);
    $c = $cell->getColumn();
    $sheet->getColumnDimension($c)->setWidth(13);
  $i++;
  //Вывод заголовка бонусов операторов
  $sheet->setCellValueByColumnAndRow($i,1,"Бонусы операторов");
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('E0E0E0');
  $sheet->getStyleByColumnAndRow($i,1)->getAlignment()->setWrapText(true);
    $c = $cell->getColumn();
    $sheet->getColumnDimension($c)->setWidth(13);
  $i++;
/*________________*/

/*Вывод заголовка пустого поля*/
  $sheet->setCellValueByColumnAndRow($i,1,"");
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
  $sheet->getStyleByColumnAndRow($i,1)->getFill()->getStartColor()->setRGB('A0A0A0');
  $cell = $sheet->getCellByColumnAndRow($i, 1);
  $c = $cell->getColumn();
  $sheet->getColumnDimension($c)->setWidth(3);
  $i++;
/*_________________*/

//Прирошение счетчика даты
  $t+=7*24*3600;
// Объявление счетчика
  $j = 2;
	$r = mysql_query("select m.id,m.by_percent,m.percent_val, m.id_uchenik from users u,masters m where u.id=m.id_master and u.type=0 order by m.shown desc, m.sort");
/*перебор по всем мастерам*/
  while ($a = mysql_fetch_array($r)){
    $m_id = $a['id'];
    $m_by_percent = $a['by_percent'];
    $m_percent_val = $a['percent_val'];
    if ($t<strtotime("2019-01-01")) {
        /* старый код*/
            $r2 = mysql_query("select t.* from topmanagers t,masters m where m.id_topmanager=t.id_user and m.id=$m_id");
            $a2 = mysql_fetch_array($r2);
            $tm_bonus1 = $a2['bonus1'];
            $tm_bonus2 = $a2['bonus2'];
        /*_____________*/
    }
    $r1 = mysql_query("select outcome,course,bill_checked,sum_no_self, outcomevk, outcomeworkvk from master_week where id_master=$m_id and dt='$dt'");
    $a1 = mysql_fetch_array($r1);
    $outcome = intval($a1['outcome']);
    $outcomevk = intval($a1['outcomevk']);
    $outcomeworkvk=intval($a1['outcomeworkvk']);
    $course = $a1['course'];
    $bill_checked = $a1['bill_checked'];
    $sum_no_self = intval($a1['sum_no_self']);

    $sum_visitors = 0;
    $sum = 0;
    $sum_comission = 0;
    $sum_bonus = 0;
    $r1 = mysql_query("select p.name,p.price,p.bonus,p.topmanager_bonus,p.comission,p.count_in_scores,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$dt' where p.id_master=$m_id");
    while($a1 = mysql_fetch_array($r1)){
      $comission = intval($a1['comission']);
      $visitors = intval($a1['visitors']);
        if ($t<strtotime("2019-01-01")) {
            /*старый код*/
                          $bonus = intval($a1['bonus']);
                                  $topmanager_bonus = intval($a1['topmanager_bonus']);
                                  if($a1['count_in_scores']==1){
                                    if ($topmanager_bonus == 0){
                                      $bonus+=$tm_bonus1;
                                    }else{
                                      $bonus+=$topmanager_bonus;
                                    }
                                  }else {
                                    if ($topmanager_bonus == 0){
                                      $bonus+=$tm_bonus2;
                                    }else{
                                      $bonus+=$topmanager_bonus;
                                    }
                                  }
                    /*___________*/
        }
      if ($visitors>0){
        $sum_comission += $visitors*$comission;
          if ($t<strtotime("2019-01-01")) {
        $sum_bonus += $visitors*$bonus; // старый код
          }
      }
    }
    if($m_by_percent==1){
      $sum_comission1 = $sum_no_self*$m_percent_val/100;
    }else{
      $sum_comission1 = $sum_comission;
    }
    if ($course>0)$sum_comission1 *= $course;
    $sheet->setCellValueByColumnAndRow($i-7,$j,$sum_comission1);
    if($bill_checked!=2 && ($sum_comission!=0 || $sum_comission1 != 0)){
      $sheet->getStyleByColumnAndRow($i-7,$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
      $sheet->getStyleByColumnAndRow($i-7,$j)->getFill()->getStartColor()->setARGB('F0F0F000');
    }
    $sheet->setCellValueByColumnAndRow($i-6,$j,-$outcome);
      $sheet->setCellValueByColumnAndRow($i-5,$j,-$outcomevk);
      $sheet->setCellValueByColumnAndRow($i-4,$j,-$outcomeworkvk);
    $bp=$sum_comission1*$proc*0.01;
      if ($t<strtotime("2019-01-01")) {
    $sheet->setCellValueByColumnAndRow($i-3,$j,$sum_bonus); // старый код $sum_bonus
      } else {
          $sheet->setCellValueByColumnAndRow($i - 3, $j, -$bp);
      }

      $qbp = "select b.base_percent, b.id, b.procentoperator from bonus b, bonusoperator op where b.id=op.idbonus and iduser=".$a['id_uchenik']." limit 1";
      $rbp = mysql_query($qbp);
      $abp = mysql_fetch_array($rbp);
      $base_percent = (float)$abp['base_percent'];
      $procentoperator = (float)$abp['procentoperator'];

      if ($sum_comission1!=0 && $base_percent!=0)
      {$mPercent = $sum_comission1/$base_percent*$procentoperator;} else $mPercent=0;
      if ($t<strtotime("2019-07-07")) {$mPercent=0;}

    $sheet->setCellValueByColumnAndRow($i-2,$j,round($mPercent)*(-1));
    $sheet->setCellValueByColumnAndRow($i-1,$j,'');

    $sheet->getStyleByColumnAndRow($i-1,$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow($i-1,$j)->getFill()->getStartColor()->setRGB('A0A0A0');

    $j++;
  }
/*____________________________*/

/*Расчет растояния вниз сколько отступать после вывода блоков расходов*/
	$maxj = ($maxj < $j) ? $j : $maxj; 
	$_r1 = mysql_query("SELECT * FROM `costs` WHERE type = 1 AND dt='".$dt."' and isDeleted=0");
	$count = mysql_num_rows($_r1);
	$rowCount1 = ($rowCount1 < $count) ? $count : $rowCount1;
	
	$_r2 = mysql_query("SELECT * FROM `costs` WHERE type = 2 AND dt='".$dt."' and isDeleted=0");
	$count = mysql_num_rows($_r2);
	$rowCount2 = ($rowCount2 < $count) ? $count : $rowCount2;
	
	$_r3 = mysql_query("SELECT * FROM `costs` WHERE type = 3 AND dt='".$dt."' and isDeleted=0");
	$count = mysql_num_rows($_r3);
	$rowCount3 = ($rowCount1 < $count) ? $count : $rowCount3;
/*___________________________________________________________________*/

}
/*_________________*/

/*Вывод расходов*/
$t = $t1;
$i = 2;
$lastRow = $j + $space;
while($t < $t2){
  	$dt = date("Y-m-d", $t);
	$t+= 7*24*3600;
	$j = $lastRow;
	
	$sheet->setCellValueByColumnAndRow(0,$j,"РАСХОДЫ");
  	$_r1 = mysql_query("SELECT * FROM `costs` WHERE type = 1 AND dt='".$dt."' and isDeleted=0");
	while ($_a = mysql_fetch_array($_r1)){
		$sheet->setCellValueByColumnAndRow($i,$j,$_a["name"]);
		$sheet->setCellValueByColumnAndRow($i+1,$j,-$_a["summ"]);
		$j++;
	}
    $i += 7;
}
/*______________*/

/*Вывод пороговых сумм*/
/*
$i = 0;
$j=$lastRow+2;
$sheet->setCellValueByColumnAndRow($i,$j ,"Пороговые суммы");
$t = $t1;
$i = 2;
$lastRow = $j;
while($t < $t2){
$week_bonus_sum = 0;
$week_bonus_sum_oper = 0;
$dt = date("Y-m-d", $t);
$t+= 7*24*3600;
$j = $lastRow;



$ru = mysql_query("select * from users where type in(1) order by type desc,id");

while ($au = mysql_fetch_array($ru)){
$manager_id = intval($au['id']);
$total_com=0;
$qc = "select u.*, m.id_master from users u,masters m where u.type=0 and m.shown=1 and u.id=m.id_master and m.id_manager=$manager_id order by m.sort";
$rc = mysql_query($qc);
while ($ac = mysql_fetch_array($rc)){
$masters=new masters();
$masters->set_dt($dt);
$master = $masters->getMasterCom(intval($ac['id_master']));
$total_com += $master['comission'];
}

$week_bonus = 0;
$q = "select * from bonus_rewards where bonus_id = 1";
$r = mysql_query($q);
$rewards = [];
while ($a = mysql_fetch_array($r)){
$rewards[] = [
"summ" => intval($a["summ"]),
"reward" => intval($a["reward"])
];
}
foreach($rewards as $reward){
if ($total_com >= $reward["summ"]){
$week_bonus = $reward["reward"];
}
}
$week_bonus_sum+=$week_bonus;

}


$ru = mysql_query("select * from users where type in(7) order by type desc,id");

while ($au = mysql_fetch_array($ru)){
$manager_id = intval($au['id']);
$total_com=0;

$qbp = "select b.base_percent, b.id, b.procentoperator from bonus b, bonusoperator op where b.id=op.idbonus and iduser=".$manager_id." limit 1";
$rbp = mysql_query($qbp);
$abp = mysql_fetch_array($rbp);
$bonusoperId = $abp['id'];
$basepr=$abp['base_percent'];

$qc = "select u.*, m.id_master from users u,masters m where u.type=0 and m.shown=1 and u.id=m.id_master and m.id_uchenik=$manager_id order by m.sort";
$rc = mysql_query($qc);
while ($ac = mysql_fetch_array($rc)){
$masters=new masters();
$masters->set_dt($dt);
$master = $masters->getMasterCom(intval($ac['id_master']));
if (($basepr!=0) && ($master['comission']!=0))
$total_com += $master['comission']/$basepr; else $total_com += 0;
}

$week_bonus_oper = 0;
$q = "select * from bonus_rewards where bonus_id = $bonusoperId";
$r = mysql_query($q);
$rewards = [];
while ($a = mysql_fetch_array($r)){
$rewards[] = [
"summ" => intval($a["summ"]),
"reward" => intval($a["reward"])
];
}
foreach($rewards as $reward){
if ($total_com >= $reward["summ"]){
$week_bonus_oper = $reward["reward"];
}
}
$week_bonus_sum_oper+=$week_bonus_oper;
}

if ($t<strtotime("2019-01-01")) { $week_bonus_sum=0; }
if ($t<strtotime("2019-07-07")) { $week_bonus_sum_oper=0; }
$days = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($dt)), date('Y', strtotime($dt)));
$sheet->setCellValueByColumnAndRow($i,$j,"Менеджеры");
$sheet->setCellValueByColumnAndRow($i+4,$j,$week_bonus_sum*(-1));

$sheet->setCellValueByColumnAndRow($i,$j+1,"Операторы");
$sheet->setCellValueByColumnAndRow($i+5,$j+1,$week_bonus_sum_oper*(-1));
$j++;
$i += 7;
}*/
/*____________________*/


/*Вывод зп*/

$t = $t1;
$i = 2;
$lastRow = $lastRow + $rowCount1 + $space+2;
while($t < $t2){
  	$dt = date("Y-m-d", $t);
	$t+= 7*24*3600;
	$j = $lastRow;
	
	$sheet->setCellValueByColumnAndRow(0,$j,"ЗАРПЛАТЫ");
	$_r1 = mysql_query("SELECT * FROM `costs` WHERE type = 2 AND dt='".$dt."' and isDeleted=0");
	while ($_a = mysql_fetch_array($_r1)){
		$days = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($dt)), date('Y', strtotime($dt)));
		$costPerWeek = ceil($_a["summ"] / $days * 7);
		$sheet->setCellValueByColumnAndRow($i,$j,$_a["name"]);
		$sheet->setCellValueByColumnAndRow($i+1,$j,-$costPerWeek);
		$j++;
	}
    $i += 7;
}
/*___________*/

/*Вывод рег расходов*/
$t = $t1;
$i = 2;
$lastRow = $lastRow + $rowCount2 + $space;
while($t < $t2){
  	$dt = date("Y-m-d", $t);
	$t+= 7*24*3600;
	$j = $lastRow;
	
	$sheet->setCellValueByColumnAndRow(0,$j,"РЕГ РАСХОДЫ");
	$_r1 = mysql_query("SELECT * FROM `costs` WHERE type = 3 AND dt='".$dt."' and isDeleted=0");
	while ($_a = mysql_fetch_array($_r1)){
		$days = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($dt)), date('Y', strtotime($dt)));
		$costPerWeek = ceil($_a["summ"] / $days * 7);
		$sheet->setCellValueByColumnAndRow($i,$j,$_a["name"]);
		$sheet->setCellValueByColumnAndRow($i+1,$j,-$costPerWeek);
		$j++;
	}
    $i += 7;
}
/*___________________*/

/*Вывод документа в виде .xsl*/
mysql_close($conn);
header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" );
header ( "Pragma: no-cache" );
header ( "Content-type: application/vnd.ms-excel" );
header ( "Content-Disposition: attachment; filename=masters.xls" );
$objWriter = new PHPExcel_Writer_Excel5($xls);
$objWriter->save('php://output');
/*___________________________*/
?>