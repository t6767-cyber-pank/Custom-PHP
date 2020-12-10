<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
if(isset($_POST['formax'])) {
    $first_name = $_POST['name'];
    $phone = $_POST['phone'];
    $message = $_POST['formax'];
    $captcha = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if(!$captcha){
        echo '<h2>Please check the the captcha form.</h2>';
        exit;
    }
    $secretKey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
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
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'mail.9oweb.kz';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'no-reply@9oweb.kz';                     // SMTP username
            $mail->Password = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';                               // SMTP password
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
