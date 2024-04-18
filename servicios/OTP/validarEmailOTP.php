<?php
    include('../cors.php');
    include_once('../../functions.php');

    $link = conectar_utf();

    if (isset($_POST['codigo']) && isset($_POST['email']) && isset($_POST['id'])) {
        $codigo = $_POST['codigo'];
        $correo = $_POST['email'];
        $id = $_POST['id'];
    }else{
        $json_Input = file_get_contents('php://input');
        $parametros = json_decode($json_Input);
        $correo = $parametros->email;
        $codigo= $parametros->codigo;
        $id = $parametros->id;
    }

    if ($codigo && $correo && $id) {
        
        $opciones = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n" .
                            "Authorization: Basic ".$basicAuthOTP."\r\n",
                'content' => '{
                    "otp": "'.$codigo.'",
                    "recipient": "'.$correo.'"
                }',
            ),
        );

        $contexto = stream_context_create($opciones);
        $json_Input = file_get_contents($rutaOTP."otp-validator", false, $contexto);
        $data = json_decode($json_Input);

        if($data->data->validOtp == true) {
            if(mysqli_query($link, "UPDATE historial_sms_otp SET estado = 1 WHERE id = ".$id)){
                header("HTTP/2.0 200 OK");
                $response = array("code" => "200", "mensaje" => "¡Verificación Exitosa!");
            }else{
                header("HTTP/2.0 200 OK");
                $response = array("code" => "301", "mensaje" => "Upss! Código coincide, pero no se pudo completar el proceso en SEAS, vuelva a cargar la pagina o contacte al equipo de soporte.");
            }
        }else{
            header("HTTP/2.0 200 OK");
            $response = array("code" => "300", "mensaje" => "Error, El codigo no coincide");
        }
    } else {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "404", "mensaje" => "Datos no encontrados ".$codigo);
    }

    echo json_encode($response);
