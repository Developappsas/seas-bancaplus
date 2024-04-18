<?php
/**
* This example shows making an SMTP connection with authentication.
*/
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include_once ('../functions.php');
header("Content-Type: application/json; charset=utf-8");  
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$link = conectar_utf();
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
require '../plugins/PHPMailer/src/Exception.php';
require '../plugins/PHPMailer/src/PHPMailer.php';
require '../plugins/PHPMailer/src/SMTP.php';

$id=$_POST["id"];

enviar_correo($id);

function enviar_correo($id) 
{
    global $link; 
    
    $consultarClientes="SELECT * FROM clientes where numero_identificacion='".$id."'";
    $queryClientes=sqlsr_query($link,$consultarClientes);
    $val=0;
    $idRegistro="";
    $cadenaGen="";
    if ( sqlsrv_num_rows($queryClientes) > 0) 
    {
        
        $resClientes=sqlsrv_fetch_array($queryClientes, SQLSRV_FETCH_ASSOC);
        $idRegistro=$resClientes["id"];
    }else{
        $consultaInformacionCliente="SELECT TOP 1 id_simulacion,email FROM solicitud WHERE cedula='".$id."' ORDER BY id_simulacion desc ";
        $queryInfo=sqlsrv_query($link,$consultaInformacionCliente);
        $resInfo=sqlsrv_fetch_array($queryInfo, SQLSRV_FETCH_ASSOC);
        
        $consultaCrearCliente="INSERT INTO clientes (correo,numero_identificacion,origen,fecha_creacion,id_usuario) VALUES ('".$resInfo["email"]."','".$id."',1,current_timestamp,1)";
        sqlsrv_query($link,$consultaCrearCliente);

        $idRegistro1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
        $idRegistro2 = sqlsrv_fetch_array($idRegistro1, SQLSRV_FETCH_ASSOC);
        $idRegistro = $idRegistro2['id'];
    }
    

    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        // Output: 54esmdr0qf
        
        $cadenaGen=substr(str_shuffle($permitted_chars), 0, 10);


        
        $actualizarClaveCliente="UPDATE log_claves_clientes SET vigente='n' WHERE id_cliente='".$idRegistro."'";
        $queryClaveCliente=sqlsrv_query($link,$actualizarClaveCliente);

        $crearRegistroCliente="INSERT INTO log_claves_clientes (id_cliente,clave,vigente,fecha,id_usuario,origen) VALUES ('".$idRegistro."','".md5($cadenaGen)."','s',current_timestamp,197,'w')";
        if (sqlsrv_query($link,$crearRegistroCliente))
        {
           $val=1; 
        }else{
            $val=3; 
        }
    if ($val==1)
    {

        $consultarInformacionCliente="SELECT a.*,CONCAT(a.primer_nombre,' ',a.segundo_nombre,' ',a.primer_apellido,' ',a.segundo_apellido) as nombre_completo_cliente FROM clientes a LEFT JOIN log_claves_clientes b ON a.id=b.id_cliente where a.id='".$idRegistro."' and b.vigente='s'";
        $queryInformacionClienteS=sqlsrv_query($link,$consultarInformacionCliente);
        $resInformacionClientes=sqlsrv_fetch_array($queryInformacionClienteS, SQLSRV_FETCH_ASSOC);
     
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
        $mail->addAddress($resInformacionClientes["correo"], ($resInformacionClientes["nombre_completo_cliente"]));
        //$mail->addAddress($respuesta["email"], $respuesta["nombre"]);
        //Set the subject line
        $mail->Subject = 'Ingreso a CrediGestion';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body

        $cuerpo=file_get_contents('../plugins/PHPMailer/examples/contents2.html');
        
        $msg = str_replace(array("{NOMBRE}","{USUARIO}", "{PASSWD}", "{ENLACE}"),array($resInformacionClientes["nombre_completo_cliente"],$resInformacionClientes["numero_identificacion"], $cadenaGen, "enlace"), $cuerpo);
        $mail->msgHTML($msg);
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"503","mensaje"=>"Correo no enviado..".$mail->ErrorInfo);
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"200","mensaje"=>"Correo enviado satisfactoriamente" );
    
        }
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"500","mensaje"=>"Error al generar informacion del cliente. Err ".$val);
    }
    echo json_encode($response);
}


?>