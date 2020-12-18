<?php
class Baza extends PDO
{
	public function __construct($file)
    {
        // парсим файл подключения
        $settings = parse_ini_file($file, TRUE);
        // Создаем подключение к БД
        $dns = $settings['database']['driver'].':host=' . $settings['database']['host'].((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '').';dbname='.$settings['database']['schema'].';charset=utf8';
        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }
	
	public function saveToBase($name, $res)
    {
        $sql = "INSERT INTO log (name, text) VALUES ('$name','$res')";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function saveAuth($iduser, $data, $nameuser, $hints, $role)
    {
        $sql = "INSERT INTO auth (iduser, data, nameuser, hints, role) VALUES ('$iduser', '$data', '$nameuser', $hints, $role)";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function updateDateAuth($id, $data)
    {
        $sql = "update auth set data='$data' where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function updateHintsAuth($id, $hints)
    {
        $sql = "update auth set hints=$hints where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function updateRoleAuth($id, $role)
    {
        $sql = "update auth set role=$role where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    function getAuth($id)
    {
        $stmt = $this->query("SELECT * FROM auth where iduser='$id' and data> NOW() - INTERVAL 100 MINUTE");
        $row = $stmt->fetch();
        return $row;
    }

    function getPadarazAuth($id)
    {
        $stmt = $this->query("SELECT * FROM auth where iduser='$id'");
        $row = $stmt->fetch();
        return $row;
    }

    function addReg_users($id_telegram, $name_telegram, $login, $date_reg)
    {
        $sql = "INSERT INTO reg_users (id_telegram, name_telegram, login, date_reg) VALUES ('$id_telegram', '$name_telegram', '$login', '$date_reg')";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function getAllRegs()
    {
        $stmt = $this->query("SELECT * FROM reg_users");
        $arr=array();
        while ($row = $stmt->fetch())
        {
            array_push($arr, $row);
        }
        return $arr;
    }

    function add_zayavki($formname, $message)
    {
        $dt=date("Y-m-d H:i:s");
        $sql = "INSERT INTO zayavki(formname, message, daterequest) VALUES ('$formname', '$message', '$dt')";
        $query = $this->prepare($sql);
        $query->execute();
    }
}
?>