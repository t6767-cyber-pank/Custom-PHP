<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once("api/reactionUI.php");
if(isset($_POST['formax'])) {
    $first_name = $_POST['name'];
    $phone = $_POST['phone'];
    $message = $_POST['formax'];
    $captcha = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if(!$captcha){
        echo '<h2>Please check the the captcha form.</h2>';
        exit;
    }
    $secretKey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $ip = $_SERVER['REMOTE_ADDR'];

    // post request to server
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $secretKey, 'response' => $captcha);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseKeys = json_decode($response,true);
    header('Content-type: application/json');
    if($responseKeys["success"]) {
        echo json_encode(array('success' => 'true'));

        $data2=array();
        $data2['message']['message_id']=0;
        $data2['message']['from']['id']="567257249"; // 770642197
        $data2['message']['from']['first_name']="FatherCarlo";
        $data2['message']['chat']['id']=0;
        $data2['message']['text']="/start";
        $reactionUI = new reactionUI($data2);
        $reactionUI->sendToBaseMessage("<b>$message</b> \nИМЯ : <b>$first_name</b>\n 777ТЕЛЕФОН : <b>$phone</b>", null);
        $reactionUI->baza->add_zayavki($message, "<b>$message</b> \nИМЯ : <b>$first_name</b>\n ТЕЛЕФОН : <b>$phone</b>");

        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'xxxxxxxxxx';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'xxxxxxxxxx';                     // SMTP username
            $mail->Password = 'xxxxxxxxxxxxxxx';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port = 25;    // TCP port to connect to
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->setFrom('no-reply@9oweb.kz', "smm");
            $mail->addAddress('smm@9oweb.kz', 'sMM');     // Add a recipient
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $message;
            $mail->Body = "<h1>$message</h1> <i>ИМЯ</i> : <b>$first_name</b> <br><i>ТЕЛЕФОН</i> : <b>$phone</b>";
            $mail->AltBody = '';

            $mail->send();
         //   echo "Сообщение успешно отправлено!";
        } catch (Exception $e) {
         //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo json_encode(array('success' => 'false'));
    }
}
?>
