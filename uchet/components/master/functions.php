<?php
function SolveProfit($master_id, $weeks, $m_by_percent,$m_percent_val){
	$total_sum = 0;
	$total_sum_comission = 0;
	foreach ($weeks as $key => $week) {
		$sum = 0;
		$sum_comission = 0;
		if ($m_by_percent != 1){
			$r = mysql_query("select p.name,p.price,p.bonus,p.comission,w.visitors from procedures p left join master_procedure_week w on p.id=w.id_procedure and dt='$week' where p.id_master=$master_id");
			while($a = mysql_fetch_array($r)){
			    $price = intval($a['price']);
			    $visitors = intval($a['visitors']);
				$comission = intval($a['comission']);
			    if ($visitors>0){
			      $sum += $visitors*$price;
			      $sum_comission += $visitors*$comission;
			    }
			  }
			 $total_sum += $sum;
			 $total_sum_comission += $sum_comission;
		}else{
  			$r = mysql_query("select outcome,paid,course,bill_checked,sum_no_self from master_week where id_master=$master_id and dt='$week'");
  			if (mysql_num_rows($r)>0){
  				$a = mysql_fetch_array($r);
    			$sum_no_self = intval($a['sum_no_self']);
    			$course = $a['course'];
  			}else{
    			$sum_no_self=0;
    			$course=0;
  			}
  			$sum_comission = $sum_no_self*$m_percent_val/100;

  			if ($course>0) $sum_no_self *= $course;
  			if ($course>0) $sum_comission *= $course;
			$total_sum += $sum_no_self;
			$total_sum_comission += $sum_comission;
		}
	}
    return number_format($total_sum - $total_sum_comission, 0, '.', ' ');
}

function GetAllWeeksOfCurrentMonth($dt_start){
	$dt_end = date('Y-m-d',strtotime($dt_start) + 60*60*24*6);
	$allWeeksOfCurrentMonth = [];
	$dt_start = strtotime($dt_start);
	$dt_end = strtotime($dt_end);
	$month_index_dt_start = (int) date('m',$dt_start);
	$month_index_dt_end = (int) date('m',$dt_end);
	$month_index = $month_index_dt_end >= $month_index_dt_start ? $month_index_dt_end : $month_index_dt_start;

	$weeks = [];
	$first_day = date('Y-m', $dt_end) .'-'. '01';
	$last_day = date("Y-m-t", strtotime($first_day));
	$day = date('N', strtotime($first_day));
	$month_start = date('Y-m-d', strtotime($first_day) - ($day-1) * 60*60*24);
	$day = date('N', strtotime($last_day));
	if ($day == '7'){
		$month_end = date('Y-m-d', strtotime($last_day) - ($day-1) * 60*60*24);
	}else{
		$month_end = date('Y-m-d', strtotime($last_day) - ($day-1) * 60*60*24 - 7 * 60*60*24);
	}
	$weeks_bettween_two_dates = ceil((strtotime($month_end) - strtotime($month_start)+1) / (60*60*24*7));
	array_push($weeks, $month_start);
	for ($i=1; $i <= $weeks_bettween_two_dates-2; $i++) { 
		$week_start = date('Y-m-d', strtotime($month_start) + 60*60*24*7*$i);
		array_push($weeks, $week_start);
	}
	array_push($weeks, $month_end);
	return $weeks;
}
function GetAllLast12Weeks($dt_start){
	$dt_start = strtotime($dt_start);
	$weeks = [];
	for ($i=0; $i < 12; $i++) { 
		$week_start = date('Y-m-d', $dt_start - 60*60*24*7*$i);
		array_push($weeks, $week_start);
	}
	return $weeks;
}
function GetAll($dt_start){
	$dt_start = strtotime($dt_start);
	$weeks = [];
	$weeks_bettween_two_dates = ceil(($dt_start - strtotime("2017-07-03")+1) / (60*60*24*7));
	for ($i=0; $i < $weeks_bettween_two_dates; $i++) { 
		$week_start = date('Y-m-d', $dt_start - 60*60*24*7*$i);
		array_push($weeks, $week_start);
	}
	return $weeks;
}

function GetMasterProfit($master_id, $dt, $m_by_percent, $m_percent_val, $period){
    $result = 0;
    switch ($period) {
        case 'current_month':
        	$weeks = GetAllWeeksOfCurrentMonth($dt);
            break;
        case 'last_12_weeks':
        	$weeks = GetAllLast12Weeks($dt);
            break;
        case 'all':
        	$weeks = GetAll($dt);
            break;
        
        default:
        	$weeks = GetAll($dt);
            break;
    }
    $result = SolveProfit($master_id, $weeks, $m_by_percent,$m_percent_val);
    return $result;
}