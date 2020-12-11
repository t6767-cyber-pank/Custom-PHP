<?php
class lang  extends PDO
{
    public $lang;

	public function __construct($lang, $file = './settings/my_setting.ini')
    {
        $this->lang=$lang;
        if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable11 to open ' . $file . '.');
        $dns = $settings['database']['driver'].':host=' . $settings['database']['host'].((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '').';dbname='.$settings['database']['schema'];
        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }
}
class testUI extends lang
{
    public $questionField;
    public $ansField;
    public $resultText;
    public $contentField;

    public $statFieldContUs;
    public $statFieldGetTest;
    public function __construct($lang, $file = './settings/my_setting.ini')
    {
        parent::__construct($lang, $file);
        switch ($this->lang)
        {
            case 1:
                $this->questionField='qRus';
                $this->ansField='answerRUS';
                $this->resultText='Результат';
                $this->contentField="ru";

                $this->statFieldContUs="Связаться с нами";
                $this->statFieldGetTest="Заказать тест";
                break;
            case 2:
                $this->questionField='qKaz';
                $this->ansField='answerKAZ';
                $this->resultText='Нәтиже';
                $this->contentField="kz";

                $this->statFieldContUs="Бізге хабарласыңыз";
                $this->statFieldGetTest="Тестке тапсырыс беріңіз";
                break;
        }
    }

    function getContentById($id)
    {
        $str='';
        $stmt = $this->query("SELECT * FROM content where id=$id");
        while ($row = $stmt->fetch()) {
        $str=$row[$this->contentField];
        }
        return $str;
    }

    function showAllQ()
    {
        $html="";
        $questionid=0;
        $stmt = $this->query("SELECT * FROM question");
        while ($row = $stmt->fetch()) {
            $questionid=$row["id"];
            $html.="<div id='q".$row["id"]."' style='display: none;'>";
            $html.="<div class='container'>";
            $html.="<div class='row'>";
            $html.="<div class='col voproser' id='qget".$row["id"]."'>";
            $html.=$row[$this->questionField];
            $html.="</div>";
            $html.="</div>";
            $html.="</div>";
            $html.="<div class='container'>";
            $html.="<div class='row'>";
            $stmtAns = $this->query("SELECT * FROM answers where id_q=".$row["id"]);
            while ($row = $stmtAns->fetch()) {
                $html.="<div class='col btndiv'>";
                $html.="<input type='radio' id='a".$row["id"]."' onclick='answerSelect($questionid, ".$row["id"].", ".$row["next_q"].", ".$row["ball"].");' name='contact' value='".$row[$this->ansField]."'> <label for='a".$row["id"]."'>".$row[$this->ansField]."</label>";
                $html.="</div>";
            }
            $html.="</div>";
            $html.="</div>";
            $html.="<div class='col btndiv'>";
            $html.="<button id='a".$row["id"]."' class='btn btn-primary' onclick='answerCL($questionid);'>";
            $html.="Далее";
            $html.="</button>";
            $html.="</div>";
            $html.="</div>";
        }
        $html.="<div id='qres' style='display: none;'>";
        $html.="<div class='container'>";
        $html.="<div class='row'>";
        $html.="<div class='col' id='qresinscontener'>";
        $html.="<h2>".$this->resultText.":</h2>";
        $html.="</div>";
        $html.="</div>";
        $html.="</div>";
        $html.="</div>";
        return $html;
    }

    function saveToBase($q1, $q2, $q3, $q4, $q5, $q6, $res)
    {
        $lang=$this->lang;
        $sql = "INSERT INTO statistic (q1, q2, q3, q4, q5, q6, res, lang) VALUES ($q1, $q2, $q3, $q4, $q5, $q6, '$res', $lang)";
        $query = $this->prepare($sql);
        $query->execute();
    }
}
?>