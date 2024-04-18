<?php
    include('../cors.php');
    include_once('../../functions.php');

    $link = conectar_utf();

    if (isset($_POST['codigo']) && isset($_POST['celular']) && isset($_POST['id'])) {
        $celular = $_POST['celular'];
        $codigo = $_POST['codigo'];
        $id = $_POST['id'];
    }else{
        $json_Input = file_get_contents('php://input');
        $parametros = json_decode($json_Input);
        $celular = $parametros->celular;
        $codigo= $parametros->codigo;
        $id = $parametros->id;
    }

    if ($codigo && $correo && $celular && $id) {
        
        $opciones = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n" .
                            "Authorization: Basic ".$basicAuthOTP."\r\n",
                'content' => '{
                    "otp": "'.$codigo.'",
                    "recipient": "57'.$celular.'"
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
