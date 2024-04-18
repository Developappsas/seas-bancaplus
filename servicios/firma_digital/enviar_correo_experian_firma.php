<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include_once ('../../functions.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

//Create a new PHPMailer instance
require '../../plugins/PHPMailer/src/Exception.php';
require '../../plugins/PHPMailer/src/PHPMailer.php';
require '../../plugins/PHPMailer/src/SMTP.php';

$json_Input = file_get_contents('php://input');

if($json_Input){
    $dataJson=json_decode($json_Input,true);

    $id_usuario = $dataJson["id_usuario"];
    $generar_pagare = $dataJson["pagare"];
    $id_simulacion = $dataJson["id_simulacion"];
    $token = $dataJson["token"];
    $reenviar = $dataJson["reenviar"];
    $id_formulario = '';
    $numero_libranza = '';
    $libranza = 0;

    $prefijo_libranza = '';
    $id_unidad_negocio = '';

    $headers = apache_request_headers();
    $token1 = $headers['Authorization'];
    $servicio = $headers['Servicio'];
    $explode=explode(" ",$token1);
    $enviar_correo = false;
    $en_progreso = 0;

    $querySimu = sqlsrv_query($link, "SELECT formato_digital, id_unidad_negocio, libranza, nro_libranza, solicitar_firma FROM simulaciones WHERE id_simulacion = '".$id_simulacion."'");
    
    $resSimulacion = sqlsrv_fetch_array($querySimu);

    if(isset($resSimulacion["solicitar_firma"]) && $resSimulacion["solicitar_firma"] == 1){

        $consultarFormatoDigital = "SELECT * FROM formulario_digital WHERE id_simulacion = '".$id_simulacion."'";
        $queryFormatoDigital = sqlsrv_query($link, $consultarFormatoDigital, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $var = 0;
        if (sqlsrv_num_rows($queryFormatoDigital) > 0){//existe
            $datoFormatoDigital = sqlsrv_fetch_array($queryFormatoDigital);
            
            $var =1;
            $token = openssl_random_pseudo_bytes(64);
            //Convertir el binario a data hexadecimal.
            $token = bin2hex($token);

            if(!(($resSimulacion["formato_digital"] === NULL || $resSimulacion["formato_digital"] == 0) && (
                        ($datoFormatoDigital["observacion_firma_pagare"] == '' && 
                            (($datoFormatoDigital["observacion_crear_girador"] !== NULL && $datoFormatoDigital["observacion_crear_girador"] != '') && ($datoFormatoDigital["observacion_firma_pagare"] !== NULL && $datoFormatoDigital["observacion_firma_pagare"] != ''))
                        ) || (strpos($datoFormatoDigital["observacion_firma_pagare"], 'SDL.SE.0118') !== false)
                    )
                )
            ){

                $query = ("UPDATE formulario_digital SET observacion_crear_girador = NULL, observacion_crear_pagare = NULL, observacion_firma_pagare = NULL, pagare_deceval = NULL, fecha_envio = GETDATE(), estado_token = 0, en_progreso = 0, fecha_leido = NULL, token = '".$token."' WHERE id_simulacion = '".$id_simulacion."'");
                $actualizarFormularioDigital = sqlsrv_query($link, $query);
                
                if($actualizarFormularioDigital) {

                    $id_formulario = $datoFormatoDigital['id'];
                    $numero_libranza = $resSimulacion["nro_libranza"];
                    $enviar_correo = true;
                }
            }else{
                $enviar_correo = false;
                $en_progreso = 1;
                $var =6;
            }
        }else{
            $var = 7.6;

            $prefijo_libranza = '';
            $id_unidad_negocio = '';
            $queryUndNegocio = sqlsrv_query($link, "SELECT b.id_unidad_negocio, b.libranza, b.nro_libranza, a.prefijo_libranza from unidades_negocio a join simulaciones b on a.id_unidad = b.id_unidad_negocio where b.id_simulacion='".$id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if($queryUndNegocio && sqlsrv_num_rows($queryUndNegocio) > 0){
                
                $dataUniNegocio = sqlsrv_fetch_array($queryUndNegocio);
                $var =7;

                $numero_libranza = $dataUniNegocio["nro_libranza"];

                $token = openssl_random_pseudo_bytes(64);
                //Convertir el binario a data hexadecimal.
                $token = bin2hex($token);
                sqlsrv_more_results($link);
                if(sqlsrv_query($link, "INSERT INTO formulario_digital (id_simulacion, estado_token, token, vigente, en_progreso) values (".$id_simulacion.",0,'".$token."','s',0)")){
                    $enviar_correo = true;
                    $id_formulario = sqlsrv_insert_id($link);
                }
            }
        }

        if($enviar_correo && $id_formulario != ''){
        
            $data=array('id_formulario' => $id_formulario,
                "id_simulacion" =>$id_simulacion
            );

            $opciones = array(
                'http'=>array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' =>   json_encode($data)
                )
            );
            
            $contexto = stream_context_create($opciones);
            //$json_Input = file_get_contents($urlPrincipal.'/servicios/pull.php', false, $contexto);
            $json_Input = file_get_contents($urlPrincipal.'/servicios/pull.php', false, $contexto);

            if ($json_Input) {

                $respuesta=(json_decode($json_Input,true));

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
                $mail->addAddress($respuesta["email"], $respuesta["nombre"]);
                //Set the subject line
                $mail->Subject = 'Continua el proceso de tu credito. No. '.$respuesta["id_simulacion"];
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $cuerpo=file_get_contents('../../plugins/PHPMailer/examples/contents.html');
                $msg = str_replace(array("{NOMBRE}", "{PAGADURIA}", "{ENLACE}"),array($respuesta["nombre"], $respuesta["pagaduria"], $urlPrincipal."/sendScalaForm.php?token=".$token), $cuerpo);
                $mail->msgHTML($msg);
                //send the message, check for errors
                if (!$mail->send()) {
                    header("HTTP/2.0 200 OK");                
                    $response = array("var" => $var, "code"=>"500","mensaje"=>"Correo no enviado.", "id" => $id_simulacion, "data"=>$data, "error" => $mail->error);
                }else{
                    
                    $actualizarFechaEnvio = "UPDATE formulario_digital SET token = '".$token."', estado_token = 0, fecha_envio = GETDATE(), fecha_leido = null, en_progreso = 0, id_usuario_envio = '".$_SESSION["S_IDUSUARIO"]."' WHERE id_simulacion = '".$id_simulacion."'";
                    if (sqlsrv_query($link,$actualizarFechaEnvio)){

                        header("HTTP/2.0 200 OK");
                        $response = array("var" => $var, "code"=>"200","mensaje"=>"Correo enviado satisfactoriamente. Nro: ".$numero_libranza);
                    }else{
                        header("HTTP/2.0 200 OK");
                        $response = array("var" => $var, "code"=>"500","mensaje"=>"Correo enviado satisfactoriamente. No se actualizo estado de token . Nro: ".$numero_libranza);
                    }                
                }
            }else{
                $response = array("var" => $var, "code"=>"404","mensaje"=>"No se pudo crear el Formulario");
            }
        }else{

            if($en_progreso != 0){
                header("HTTP/2.0 200 OK");
                $response = array("var" => $var, "code"=>"300","mensaje"=>"Tiene un intento en Progreso! Espere la respuesta del pagaré para volverlo a intentar, por favor indique al equipo de soporte de este caso para validarlo de manera manual");
            }else{
                header("HTTP/2.0 200 OK");
                $response = array("var" => $var, "code"=>"403","mensaje"=>"Credito no tiene token disponible, es posible que no haya leido el anterior.", "progreso" => $en_progreso, 'enviar_correo'=>$enviar_correo, 'id_formulario'=> $id_formulario);
            }
        }
    }else{
        $response = array("var" => $var, "code"=>"400","mensaje"=>"El Credito NO se encuentra disponible para firma");
    }
}else{
    $response = array("var" => $var, "code"=>"404","mensaje"=>"No se recibieron parametros" );
}

echo json_encode($response);
?>