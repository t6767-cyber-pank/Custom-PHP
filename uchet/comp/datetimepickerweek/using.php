<?php
class DateTimePickerWeeks {

    //Переменные
    public $path = "/comp/datetimepickerweek/";
    public $idaj="ajax";
    public $DOCUMENT_ROOT;
    public $dt;
    public $dt_to;
    public $url;
    public $operation;

    //Конструктор
    function __construct($DOCUMENT_ROOT,$dt,$url)
    {
        $this->DOCUMENT_ROOT = $DOCUMENT_ROOT;
        $this->dt = $dt;
        $this->url=$url;
    }

    //Назначаем интервал
    function set_dt_to($dt_to)
    {
        $this->dt_to=$dt_to;
    }

    //Назначаем операцию
    function set_operation($operation)
    {
        $this->operation=$operation;
    }

    //Выводит дату понедельника из выбранной даты
    function get_monday ($z='-')
    {
        $monday = date('d.m.Y', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        $mondayV = date('Y-m-d', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    //Выводит дату воскресения из выбранной даты
    function get_sunday ($z='-')
    {
        $sunday= date('d.m.Y', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        $sundayV = date('Y-m-d', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
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

    //Наполним массив обработки дат
    function arraydates()
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->get_monday()."' and '".$this->dt_to."' order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $a['data']);
        }
        return $drar;
    }

    //Выводит шаблон
    function output(){
        echo $this->get_monday($this->dt, ".")." - ".$this->get_sunday($this->dt, ".");
    }

    //Вывод дива с компонентом начало
    function AjaxComponemtStart(){
        echo '<div id="'.$this->idaj.'" style="text-align: center; margin-top: 130px;" >';
    }

    //Вывод дива с компонентом начало
    function AjaxComponemtStartNONE(){
        echo '<div id="'.$this->idaj.'">';
    }
    //Вывод дива с компонентом конец
    function AjaxComponemtEnd(){
        echo '</div>';
    }

    //Иницирует скрипты в заголовок
    function initalHeaders(){
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=$this->path ?>jquery.datetimepicker.css"/>
        <script src="<?=$this->path ?>dtpkernel/jquery.datetimepicker.full.js"></script>
        <script src="<?=$this->path ?>kernel/js/timers.js"></script>
        <script> var d = new Date('<?=$this->dt ?>'); var urli='<?=$this->url ?>'; var idkomponent='#ajax'; var operation='show_masters' </script>
        <script src="<?=$this->path ?>js/main.js"></script>
        <?php
    }

    //Иницирует компонент
    function initalComponent()
    {
        ?>
        <img src="<?=$this->path ?>img/ls.png" onclick="lf(0);" class="strelki" >
        <input type="text" name="search-to-daten" id="search-to-daten" class="datetext" value="<?=$this->output() ?>" />
        <img src="<?=$this->path ?>img/rs.png" onclick="lf(1);" class="strelki" >
        <?php
    }

};


class DateTimePickerInterval {

    //Переменные
    public $path = "/comp/datetimepickerweek/";
    public $idaj="ajax";
    public $DOCUMENT_ROOT;
    public $dt;
    public $dt_to;
    public $url;
    public $operation;
    public $access;

    //Конструктор
    function __construct($DOCUMENT_ROOT,$dt,$url,$access)
    {
        $this->DOCUMENT_ROOT = $DOCUMENT_ROOT;
        $this->dt = $dt;
        $this->url=$url;
        $this->access=$access;
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

    //Назначаем операцию
    function set_operation($operation)
    {
        $this->operation=$operation;
    }

    //Выводит дату понедельника из выбранной даты

    function get_monday ($z='-')
    {
        $monday = date('d.m.Y', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        $mondayV = date('Y-m-d', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    //Выводит дату воскресения из выбранной даты
    function get_sunday ($z='-')
    {
        $sunday= date('d.m.Y', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        $sundayV = date('Y-m-d', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        if ($z=='-'){ return $sundayV; } else { return $sunday; }
    }

    //Выводит дату воскресения из выбранной даты
    function get_sunday_par ($dt, $z='-')
    {
        $sunday= date('d.m.Y', strtotime($dt." +".(7-date('w', strtotime($dt)))." days"));
        $sundayV = date('Y-m-d', strtotime($dt." +".(7-date('w', strtotime($dt)))." days"));
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

    //Наполним массив обработки дат
    function arraydates()
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->get_monday()."' and '".$this->dt_to."' order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $a['data']);
        }
        return $drar;
    }

    //Выводит шаблон
    function output(){
        echo $this->get_monday(".");
    }

    //Выводит шаблон
    function outputTo(){
        echo $this->get_sunday_par($this->dt_to , ".");
    }

    //Вывод дива с компонентом начало
    function AjaxComponemtStart(){
        echo '<div id="'.$this->idaj.'">';
    }
    //Вывод дива с компонентом конец
    function AjaxComponemtEnd(){
        echo '</div>';
    }

    //Иницирует скрипты в заголовок
    function initalHeaders(){
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=$this->path ?>jquery.datetimepicker.css"/>
        <script src="<?=$this->path ?>dtpkernel/jquery.datetimepicker.full.js"></script>
        <script src="<?=$this->path ?>kernel/js/timers.js"></script>
        <script> var d = new Date('<?=$this->dt ?>'); var dx = new Date('<?=$this->dt_to ?>'); var urli='<?=$this->url ?>'; var idkomponent='#ajax'; var operation='<?=$this->operation ?>' </script>
        <script src="<?=$this->path ?>js/maininterval.js"></script>
        <?php
    }

    //Иницирует компонент
    function initalComponent()
    {
        ?>
        Начало: <input type="text" name="search-to-daten" id="search-to-daten" class="datetext T_M_Diag_inputs" value="<?=$this->output() ?>" autocomplete="off"/>
        Конец: <input type="text" name="search-to-datek" id="search-to-datek" class="datetext T_M_Diag_inputs" value="<?=$this->outputTo() ?>" autocomplete="off" />
        <input type="checkbox" name="rash" id="rash" onclick="getRash('<?=$this->url ?>')" <?php if($this->access==0) { echo " disabled ";} ?> checked>расходы
        <button class="T_M_Buttons" onclick="getRash('<?=$this->url ?>')">Обновить</button>
        <?php
    }

};

class DateTimePickerDay {

    //Переменные
    public $path = "/comp/datetimepickerweek/";
    public $idaj="ajax";
    public $DOCUMENT_ROOT;
    public $dt;
    public $dt_to;
    public $url;
    public $operation;

    //Конструктор
    function __construct($DOCUMENT_ROOT,$dt,$url)
    {
        $this->DOCUMENT_ROOT = $DOCUMENT_ROOT;
        $this->dt = $dt;
        $this->url=$url;
    }

    //Назначаем интервал
    function set_dt_to($dt_to)
    {
        $this->dt_to=$dt_to;
    }

    //Назначаем операцию
    function set_operation($operation)
    {
        $this->operation=$operation;
    }

    //Выводит дату понедельника из выбранной даты
    function get_monday ($z='-')
    {
        $monday = date('d.m.Y', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        $mondayV = date('Y-m-d', strtotime($this->dt." -".date('w', strtotime($this->dt))." days +1 days"));
        if ($z=='-'){ return $mondayV; } else { return $monday; }
    }

    //Выводит дату воскресения из выбранной даты
    function get_sunday ($z='-')
    {
        $sunday= date('d.m.Y', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
        $sundayV = date('Y-m-d', strtotime($this->dt." +".(7-date('w', strtotime($this->dt)))." days"));
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

    //Наполним массив обработки дат
    function arraydates()
    {
        $drar=array();
        $q = "select data from timer where data between '".$this->get_monday()."' and '".$this->dt_to."' order by  data asc";
        $r = mysql_query($q);
        while ($a = mysql_fetch_array($r)) {
            array_push($drar, $a['data']);
        }
        return $drar;
    }

    //Выводит шаблон
    function output(){
        echo $this->dt;
    }

    //Вывод дива с компонентом начало
    function AjaxComponemtStart(){
        echo '<div id="'.$this->idaj.'" style="text-align: center; margin-top: 130px;" >';
    }

    //Вывод дива с компонентом начало
    function AjaxComponemtStartNONE(){
        echo '<div id="'.$this->idaj.'">';
    }
    //Вывод дива с компонентом конец
    function AjaxComponemtEnd(){
        echo '</div>';
    }

    //Иницирует скрипты в заголовок
    function initalHeaders(){
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=$this->path ?>jquery.datetimepicker.css"/>
        <script src="<?=$this->path ?>dtpkernel/jquery.datetimepicker.full.js"></script>
        <script src="<?=$this->path ?>kernel/js/timers.js"></script>
        <script> var d = new Date('<?=$this->dt ?>'); var urli='<?=$this->url ?>'; var idkomponent='#ajax'; var operation='show_masters' </script>
        <script src="<?=$this->path ?>js/mainday.js"></script>
        <?php
    }

    //Иницирует компонент
    function initalComponent()
    {
        ?>
        <img src="<?=$this->path ?>img/ls.png" onclick="lf(0);" class="strelki" >
        <input type="text" name="search-to-daten" id="search-to-daten" class="datetext" value="<?=$this->output() ?>" />
        <img src="<?=$this->path ?>img/rs.png" onclick="lf(1);" class="strelki" >
        <?php
    }

};


//Проверяем приемные данные
function reactionStart($par1){
    if (isset($par1)) {return 1;} else { return 0;}
}
?>
