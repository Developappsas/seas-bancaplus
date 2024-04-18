<?php
/**
* This example shows making an SMTP connection with authentication.
*/
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include_once ('../functions.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
require '../plugins/PHPMailer/src/Exception.php';
require '../plugins/PHPMailer/src/PHPMailer.php';
require '../plugins/PHPMailer/src/SMTP.php';

if(isset($_POST["correo"])){
    
    $id_usuario=$_POST["id_usuario"];
    $id_simulacion=$_POST["id_simulacion"];
    $nombre=$_POST["nombre"];

    $headers = apache_request_headers();
    $token = '';

    if($id_usuario > 0){

        $queryTokenVigente = sqlsrv_query($link,"SELECT token FROM historial_tokens_verificacion_id WHERE id_simulacion = $id_simulacion AND (estado IS NULL OR estado != 1) AND (estado_respuesta IS NULL OR estado_respuesta = 0)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        $i = 0; 
        $tokenVigente = false;
        if(sqlsrv_num_rows($queryTokenVigente) > 0){
            $datosTokenVigente = sqlsrv_fetch_array($queryTokenVigente, SQLSRV_FETCH_ASSOC);
            $token = $datosTokenVigente['token'];
            $tokenVigente = true;
        }else{

            $yaExisteToken = false;
            do {
                $i++;
                $token = openssl_random_pseudo_bytes(64);
                //Convertir el binario a data hexadecimal.
                $token = bin2hex($token);

                $conHistorialToken="SELECT id FROM historial_tokens_verificacion_id WHERE token = '".$token."'";
                $queryEnviar=sqlsrv_query($link,$conHistorialToken, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($queryEnviar)>0){
                    $yaExisteToken = false;
                }else{
                    $yaExisteToken = true;
                }
            } while ($i <= 100 && $yaExisteToken == false);
        }

        if($i == 100 && $token != ''){
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"400","mensaje"=>"Token para correo NO Disponible, Vuelva a Intentarlo");
        }else{

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
            $mail->addAddress($_POST["correo"], $_POST["nombre"]);
            //$mail->addAddress($respuesta["email"], $respuesta["nombre"]);
            //Set the subject line
            $mail->Subject = 'Credito En Proceso: Validación de Identidad';

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body

            $cuerpo=file_get_contents('../plugins/PHPMailer/examples/mail_verificar.html');
            $msg = str_replace(array("{NOMBRE}", "{ENLACE}"),array(utf8_decode($nombre), $urlPrincipal."/vistoValidadorID.php?token=".$token), $cuerpo);
            $mail->msgHTML($msg);

            //send the message, check for errors
            if (!$mail->send()) {
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"400","mensaje"=>"Correo no enviado");
            }else{

                if($tokenVigente){
                    $registroTokenVerificar="UPDATE historial_tokens_verificacion_id SET fecha_reenvio = CURRENT_TIMESTAMP WHERE token = '".$token."'";
                }else{
                    $registroTokenVerificar="INSERT INTO historial_tokens_verificacion_id (id_simulacion, token, id_usuario, estado, fecha) values (".$id_simulacion.",'".$token."', '".$id_usuario."', 0, CURRENT_TIMESTAMP)";
                }

                if (sqlsrv_query($link,$registroTokenVerificar)){
                    header("HTTP/2.0 200 OK");
                    $response = array( "code"=>"200","mensaje"=>"Correo enviado satisfactoriamente");
                }else{
                    header("HTTP/2.0 200 OK");
                    $response = array( "code"=>"400","mensaje"=>"¡Token NO generado!");
                }         
            }
        }
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"400","mensaje"=>"¡Token NO generado, Vuelve a Iniciar Sesion!");
    }
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "code"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);
?>