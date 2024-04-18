<?php
header("Content-Type: application/json; charset=utf-8");    
/**
* This example shows making an SMTP connection with authentication.
*/
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include_once ('../functions.php');
$link = conectar_utf();
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
require '../plugins/PHPMailer/src/Exception.php';
require '../plugins/PHPMailer/src/PHPMailer.php';
require '../plugins/PHPMailer/src/SMTP.php';

if(isset($_POST["cedula"])){
    
    $cedula=$_POST["cedula"];
    $id_simulacion=$_POST["id_simulacion"];
    $nombre=$_POST["nombre"];
    $headers = apache_request_headers();

    $mail = new PHPMailer();
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // SMTP::DEBUG_OFF = off (for production use)
    // SMTP::DEBUG_CLIENT = client messages
    // SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    //Set the hostname of the mail server
    $mail->Host = 'smtp.office365.com';

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication
    $mail->Username = "notificaciones@kredit.com.co";

    //Password to use for SMTP authentication
    $mail->Password = $PasswordMail;

    //Set who the message is to be sent from
    $mail->setFrom('notificaciones@kredit.com.co', 'Notificaciones KREDIT PLUS S.A');

    //Set an alternative reply-to address
    $mail->addReplyTo('notificaciones@kredit.com.co', 'Notificaciones KREDIT PLUS S.A');

    //Set who the message is to be sent to

    $mail->addAddress("auxtesoreria2@kredit.com.co", 'Tesoreria');
    $mail->addAddress("tesoreria@kredit.com.co", 'Tesoreria');
    $mail->addAddress("auxtesoreria1@kredit.com.co", 'Tesoreria');
    $mail->addAddress("auxtesoreria@kredit.com.co", 'Tesoreria');
    $mail->addAddress("aprendiztesoreria@kredit.com.co", 'Tesoreria');
    $mail->addAddress("mtroya@kredit.com.co", 'Tesoreria');
    
    //Set the subject line
    $mail->Subject = 'CARTERA A PAGAR CC. '.$cedula.' - '.utf8_decode($nombre);

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body

    //$cuerpo=file_get_contents('../plugins/PHPMailer/examples/mail_verificar.html');
    include '../plugins/PHPMailer/examples/mail_vencimientos.php';
    $msg = str_replace(array("{NOMBRE}", "{CEDULA}", "{SIMULACION}"),array(utf8_decode($nombre), $cedula, $id_simulacion), $cuerpo);
    $mail->msgHTML($msg);

    //send the message, check for errors
    if (!$mail->send()) {
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"400","mensaje"=>"Correo no enviado");
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"200","mensaje"=>"Correo enviado satisfactoriamente");     
    }
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "code"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);
?>