<?php

$basePath = dirname(__DIR__, 1);
require $basePath.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

//include phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class MailService{

    function __construct() {
        //Create an instance; passing `true` enables exceptions
        $this->mail = new PHPMailer(TRUE);

        //Server settings
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $this->mail->isSMTP();                                            //Send using SMTP
        $this->mail->Host       = $_ENV["smtpServer"];                    //Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->mail->Username   = $_ENV["devMailAcc"];                    //SMTP username
        $this->mail->Password   = $_ENV["devMailPassword"];               //SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $this->mail->Port       = 465;      //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $this->mail->SMTPDebug=0;           //disable show config                   
        
    }

    function sendContactMail($mailAdress, $name, $message, $userEmailAcceptance){
        
        //make sure alle line breaks work and are shown correct in gmail
        $convMessage = nl2br($message);

        try{
            $this->mail->setFrom($mailAdress, $name);
            $this->mail->addAddress($_ENV["devMailAcc"], $_ENV["devMailName"]);     //Add a recipient

            //Content
            $this->mail->isHTML(true);                                  //Set email format to HTML
            $this->mail->Subject = "New Message from ".$name;
            $this->mail->Body    = "$convMessage"."<br>---------------------------<br>Given mail address for answering: ".$mailAdress."<br><br>User wants to be contacted via mail: ".$userEmailAcceptance;
            $this->mail->send();
            return 0;
        } catch (Exception $e) {
            echo ": Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }

}








?>