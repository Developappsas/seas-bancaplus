<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
* This example shows making an SMTP connection with authentication.
*/
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include_once ('../../functions.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
require '../../plugins/PHPMailer/src/Exception.php';
require '../../plugins/PHPMailer/src/PHPMailer.php';
require '../../plugins/PHPMailer/src/SMTP.php';

if(isset($_POST["usuario"]) && isset($_POST["documento"])){
    
    $usuario=$_POST["usuario"];
    $documento=$_POST["documento"];

    $headers = apache_request_headers();
    $queryUsuario = sqlsrv_query($link, "SELECT email, nombre, apellido FROM usuarios WHERE login = '$usuario' AND cedula = '$documento'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

    if(sqlsrv_num_rows($queryUsuario) == 0){
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"300","mensaje"=>"Datos Incorrectos, No Existe Usuario");
    }else{

        $datosCorreo = sqlsrv_fetch_array($queryUsuario, SQLSRV_FETCH_ASSOC);
        $correo = $datosCorreo['email'];
        $nombre = $datosCorreo['nombre'];

        $clave = openssl_random_pseudo_bytes(6);
        //Convertir el binario a data hexadecimal.
        $clave = bin2hex($clave);

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
        $mail->Password =$PasswordMail;

        //Set who the message is to be sent from
        $mail->setFrom('notificaciones@kredit.com.co', 'Notificaciones KREDIT PLUS S.A');

        //Set an alternative reply-to address
        $mail->addReplyTo('notificaciones@kredit.com.co', 'Notificaciones KREDIT PLUS S.A');

        //Set who the message is to be sent to
        $mail->addAddress($correo, $nombre);
        //Set the subject line
        $mail->Subject = utf8_decode("Recupera Tu Contraseña (Contraseña Temporal)");

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body

        $cuerpo=file_get_contents('../../plugins/PHPMailer/examples/mail_recuperar_clave.html');
        $msg = str_replace(array("{NOMBRE}", "{CLAVE}"),array(utf8_decode($nombre), $clave), $cuerpo);
        $mail->msgHTML($msg);

        //send the message, check for errors
        if (!$mail->send()) {
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"400","mensaje"=>"Correo no enviado, Contacte Al administrador.");
        }else{
            
        
                
            if(sqlsrv_query($link, "UPDATE usuarios SET cambio_clave = 0,  [password] = '".md5($clave)."'  WHERE [login] = '".$usuario."' AND cedula = '".$documento."'")){
            
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"200","mensaje"=>"Correo enviado satisfactoriamente", "dato" => $correo );
            }else{
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"400","mensaje"=>"Correo no enviado, Error al Generar Contraseña.");
            }
        }
    }
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "code"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);

?>