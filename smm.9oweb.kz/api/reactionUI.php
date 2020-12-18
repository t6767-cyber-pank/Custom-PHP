<?php

require_once("Baza.php");

class reactionUI
{
    // Глобальные переменные
	public $iduser;
	public $url;
    public $chatID;
    public $unm;
    public $dt;
    public $ms;
    public $idmsg;
    public $baza;
    public $menu;
    public $aurh;
    public $secure;
    public $passw;

	// Создадим конструктор ебаный Лего
	public function __construct($data)
    {
        // Объявляем глобальные переменные
        $this->idmsg=$data['message']['message_id'];
        $this->iduser=$data['message']['from']['id']; //"567257249";
        $this->unm=$data['message']['from']['first_name'];
        $this->dt=date("Y-m-d H:i:s", $data['message']['date']);
        $this->ms=$data['message']['text'];
        $this->chatID=$data['message']['chat']['id'];
        $this->url='https://api.telegram.org/xxxxxxxxxxxxxxxxxxxxx/sendMessage';
        $this->urldoc='https://api.telegram.org/xxxxxxxxxxxxxxxxxxxxxx/sendDocument';
        $this->menu=[["Регистрация"]];
        $this->baza=new Baza("my_setting.ini");
        $this->aurh=$this->baza->getAuth($this->iduser);
        $this->passw="777999";
        if ($this->aurh['id']!=null) $this->secure=1; else $this->secure=0;
        $authPidaraz=$this->baza->getPadarazAuth($this->iduser);
        if ($authPidaraz['hints']>2) $this->secure=99;
    }

    public function startIO()
    {
        switch ($this->secure)
        {
            case 99:
                $this->badRobot();
                break;
            default :
            {
                switch ($this->ms) {
                    case "/start": $this->start(); break;
                    case "Регистрация": $this->registration(); break;
                    default:
                    {
                        switch ($this->aurh['role']) {
                            case 0:
                                    if ($this->passw == $this->ms) {
                                        $this->baza->updateRoleAuth($this->aurh['id'], 1);
                                        $this->sendMessage("Введите новый логин", $this->menu);
                                    } else {
                                        $x = (int)$this->aurh['hints'];
                                        $x++;
                                        $this->baza->updateHintsAuth($this->aurh['id'], $x);
                                        $this->sendMessage("Ты ваще знаешь секретный код? \n Если не знаешь не вводи лишний раз. \n <b>Не надо!</b>", $this->menu);
                                        $this->sendMessage("Введите секретный код", $this->menu);
                                    }
                                    break;
                            case 1:
                                $this->baza->addReg_users($this->aurh['iduser'], $this->aurh['nameuser'], $this->ms, $this->dt);
                                $this->baza->updateRoleAuth($this->aurh['id'], 2);
                                $this->sendMessage("Вы зарегестрированы ваш логин <b>".$this->ms."</b>", $this->menu);
                                break;
                            default :
                            {
                                $this->sendMessage("Введите корректную комманду", $this->menu);
                            }
                        }
                        break;
                    }
                }
            }
        }
    }

    public function badRobot()
    {
        $this->sendMessage("Иди нахер ".$this->unm.", хакер чертов. \n Армия терминаторов едет к тебе. \n <b>Они выебут тебя</b>.", $this->menu);
    }

    public function start()
    {
        $this->sendMessage("Боеготовность 100%", $this->menu);
    }

    public function registration()
    {
        if ($this->secure == 0) {
            $this->baza->saveAuth($this->iduser, $this->dt, $this->unm, 0, 0);
        } else {
            $this->baza->updateDateAuth($this->aurh['id'], $this->dt);
        }
        switch ($this->aurh['role'])
        {
            case 0: $this->sendMessage("Введите секретный код", $this->menu); break;
            case 1: $this->sendMessage("Введите новый логин", $this->menu); break;
            default:
            {
                $this->sendMessage("Вы уже зарегестрированы в системе", $this->menu); break;
            }
        }

    }

    public function sendToBaseMessage($ms)
    {
        $arr=$this->baza->getAllRegs();
        foreach ($arr as $user)
        {
            $this->iduser=$user['id_telegram'];
            $this->sendMessage($ms, null);
        }
    }

    function sendMessage($message,$buttons = null) {
        $data = array(
            'text' => $message,
            'parse_mode' => 'HTML',
            'chat_id' => $this->iduser
        );

        if($buttons != null) {
            $data['reply_markup'] = [
                'keyboard' => $buttons,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
                'parse_mode' => 'HTML',
                'selective' => true
            ];
        }
        $data_string = json_encode($data);
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function securityStrip($str)
    {
        //$str=trim($str);
        //$str=strip_tags($str);
        $str=str_replace(chr(13),'',$str);
        $str=str_replace(chr(10),'',$str);
        $str=str_replace(" ",'%20',$str);
        $str=str_replace("+",'%2b',$str);
        return $str;
    }

    public function sendFile($IDName, $sort) {
        $file_url=$this->securityStrip("https://amanbol.kz/api/exel.php?user=$IDName&sort=$sort");
        $ch = curl_init($file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
        file_put_contents(basename($file_url), $html);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL =>  $this->urldoc.'?caption='.date("d.m.Y").'&chat_id='.$this->chatID,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: multipart/form-data'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'document' => curl_file_create(basename($file_url), mime_content_type(basename($file_url)), "$sort.".date('d-m-Y').".xlsx")
            ]
        ]);
        $data = curl_exec($curl);
        curl_close($curl);
        unlink(basename($file_url));
        return $data;
    }
}
?>