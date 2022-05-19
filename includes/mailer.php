<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);

try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->Debugoutput = 'html';
        
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'localhost';                     //Set the SMTP server to send through
        $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPAuth = false;
        //$mail->SMTPSecure = 'tls';
        
        $mail->setFrom('mail@gsb-lycee.ga', 'GSB Mailer');
        $mail->addAddress("espriityt@gmail.com");
        
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Nouvelle connexion';
        echo( __DIR__ . "/mails/");
        $mail->msgHTML(file_get_contents('login.html'), __DIR__ . '/mails/');
        //$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
        $mail->send();

        echo("mail sent successfully");
} catch(Exception $e){
    die($e);
}