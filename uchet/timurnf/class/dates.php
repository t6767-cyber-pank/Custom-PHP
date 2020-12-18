<?php
class timereal {
    public $dt;
    public $dt_to;

    function __construct()
    {
        $this->dt = date('Y-m-d');
        $this->dt_to = date('Y-m-d');
    }

    //Назначаем интервал
    function set_dt($dt)
    {
        $this->dt=$dt;
    }

    //Назначаем интервал
    function set_dt_to($dt_to)
    {
        $this->dt_to=$dt_to;
    }

    function monthToWord($dt)
    {
        $d=date("d", strtotime($dt));
        $m=date("m", strtotime($dt));
        switch ($m)
        {
            case "01" : $mon="янв."; break;
            case "02" : $mon="фев."; break;
            case "03" : $mon="марта"; break;
            case "04" : $mon="апр."; break;
            case "05" : $mon="мая"; break;
            case "06" : $mon="июля"; break;
            case "07" : $mon="июня"; break;
            case "08" : $mon="авг."; break;
            case "09" : $mon="сент."; break;
            case "10" : $mon="окт."; break;
            case "11" : $mon="нояб."; break;
            case "12" : $mon="декаб."; break;
        }
        return $d." ".$mon;
    }

    //Выводит дату понедельника из выбранной даты
    function get_monday ($z='-')
    {
        $monday = date('d.m.Y', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        $mondayV = date('Y-m-d', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    //Выводит дату понедельника из выбранной даты
    function get_mondayPar ($pars, $z='-')
    {
        $monday = date('d.m.Y', strtotime($pars." -".date('w', strtotime($pars))." days +1 days"));
        $mondayV = date('Y-m-d', strtotime($pars." -".date('w', strtotime($pars))." days +1 days"));
        if (date('w', strtotime($pars))==0)
        {
            $monday = date('d.m.Y', strtotime($pars." -".date('w', strtotime($pars))." days -6 days"));
            $mondayV = date('Y-m-d', strtotime($pars." -".date('w', strtotime($pars))." days -6 days"));
        }
        //echo $pars." | ".date('w', strtotime($pars))." | ".$monday."<br>";
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    //Выводит дату воскресения из выбранной даты
    function get_sunday ($z='-')
    {
        $sunday= date('d.m.Y', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        $sundayV = date('Y-m-d', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        if ($z=='-'){ return $sundayV; } else { return $sunday; }
    }

    function get_sundayPars ($par, $z='-')
    {
        $sunday= date('d.m.Y', strtotime($par." +".(7-date('w', strtotime($par)))." days"));
        $sundayV = date('Y-m-d', strtotime($par." +".(7-date('w', strtotime($par)))." days"));
        if (date('w', strtotime($par))==0)
        {
            $sunday= date('d.m.Y', strtotime($par." +".(date('w', strtotime($par)))." days"));
            $sundayV = date('Y-m-d', strtotime($par." +".(date('w', strtotime($par)))." days"));
        }
        if ($z=='-'){ return $sundayV; } else { return $sunday; }
    }

    //Выводит дату прошлого воскресения из выбранной даты
    function get_sunday_last ($z='-')
    {
        $dar=$this->get_monday ('-');
        $sunday= date('d.m.Y', strtotime($dar." -1 days"));
        $sundayV = date('Y-m-d', strtotime($dar." -1 days"));
        if ($z=='-'){ return $sundayV; } else { return $sunday; }
    }

    //Выводит дату прошлого воскресения из выбранной даты
    function get_sunday_lastPar ($par, $z='-')
    {
        $dar=$this->get_mondayPar ($par, '-');
        $sunday= date('d.m.Y', strtotime($dar." -1 days"));
        $sundayV = date('Y-m-d', strtotime($dar." -1 days"));
        if ($z=='-'){ return $sundayV; } else { return $sunday; }
    }

    //Наполним массив обработки дат
    function arraydates()
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->dt."' and '".$this->dt_to."' order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $a['data']);
        }
        return $drar;
    }

    //Наполним массив воскресений
    function arraydatesByDW($dayweek)
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->dt."' and '".$this->dt_to."' and dayweek=$dayweek order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $a['data']);
        }
        return $drar;
    }

    //Наполним массив воскресений
    function arraydatesByDWselmonth($dayweek)
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->dt."' and '".$this->dt_to."' and dayweek=$dayweek order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $this->selmonth($a['data']));
        }
        return $drar;
    }
    // Выбор месяца
    function selmonth($x){
        $month=date("m", strtotime($x));
        $day=date("d", strtotime($x));
        $return="";
        switch($month)
        {
            case "01" : $return="Янв"; break;
            case "02" : $return="Фев"; break;
            case "03" : $return="Март"; break;
            case "04" : $return="Апр"; break;
            case "05" : $return="Мая"; break;
            case "06" : $return="Июня"; break;
            case "07" : $return="Июля"; break;
            case "08" : $return="Авг"; break;
            case "09" : $return="Сент"; break;
            case "10" : $return="Окт"; break;
            case "11" : $return="Нояб"; break;
            case "12" : $return="Дек"; break;
        }
        return $day." ".$return;
    }

    //Выводит дату начала месяца 1 число.
    function get_monthstart ($pars, $z='-')
    {
        $dar=$pars;
        $rdat = mysql_query("SELECT * FROM `timer` WHERE data='".$dar."'");
        $adat = mysql_fetch_array($rdat);
        $rdat2 = mysql_query("SELECT * FROM `timer` WHERE year=".$adat['year']." and month=".$adat['month']." and day=1");
        $adat2 = mysql_fetch_array($rdat2);
        if ($z=='-'){ return $adat2['data']; } else { return date("d.m.Y", strtotime($adat2['data'])); }
    }

    //Выводит дату понедельника 2 недели назад.
    function get_2weeksAGoMonday ($z='-')
    {
        $monday = date('d.m.Y', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days -2 week"));
        $mondayV = date('Y-m-d', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days -2 week"));
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    // Вывести массив 4 недель с 1 числа
    function arraywork4week()
    {
        $drar=array();
        $dt_month_Start=$this->get_mondayPar($this->get_monthstart($this->dt));
        array_push($drar, $dt_month_Start);
        $q = "select yearweek, year from timer where data='$dt_month_Start'";
        $r = mysql_query($q);
        $a = mysql_fetch_array($r);
        $week=$a['yearweek'];
        $year=$a['year'];
        $day=1;

        for ($i=1; $i<4; $i++)
        {
            $week=$week+1;
            $q = "select data from timer where year=$year and yearweek=$week and dayweek=$day";
            $r = mysql_query($q);
            $a = mysql_fetch_array($r);
            array_push($drar, $a['data']);
        }
        return $drar;
    }
}
?>
