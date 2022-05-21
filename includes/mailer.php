<?php
// require smtp configuration
require($_SERVER["DOCUMENT_ROOT"]. "/config/configSMTP.php");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendLoginConnectionMail($IPAdress, $mail){


    $datetime = new DateTime();
    $date = $datetime->format("d-m-Y H:i:s");

    $logincontent = file_get_contents('mail_template/login.html', FILE_USE_INCLUDE_PATH);
    $content = str_replace(array("%IP%", "%DATA%"), array($IPAdress, $date), $logincontent);

    $mail = new PHPMailer(true);

    try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            //$mail->Debugoutput = 'html';
            
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
            $mail->Port       = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPAuth = SMTP_AUTH;
            $mail->SMTPSecure = SMTP_ENCRYPT;
            
            $mail->setFrom('mail@gsb-lycee.ga', 'GSB Mailer');
            $mail->addAddress($mail);
            
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Nouvelle connexion';
            $mail->msgHTML($content);
            //$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            
            $mail->send();
    
    } catch(Exception $e){
        die($e);
    }
}

