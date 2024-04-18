<?php
include_once('../../functions.php');

$link = conectar_utf();

$rutaOTP = 'https://otp-manager.masivapp.com/transactional-api/v1/';

if (isset($_POST['codigo'])) {
    $basicAuth = 'S2NyZWRpdF9PVFBfOTlKRkw6ZHNONDZrMGwkRg==';
    
    $celular = $_POST['celular'];
    $otp = $_POST['codigo'];

    //Consultamos si ya esta verificado
    //$consultar = sqlsrv_query($link,"SELECT estado_respuesta FROM historial_tokens_verificacion_id WHERE id_simulacion = ".$id_simulacion." AND estado_respuesta in(2,14)");//2-Verificado o 14-verificado Anteriormente
    // $consultar = sqlsrv_query($link,"SELECT cedula, nombre FROM simulaciones WHERE telefono = @celular GROUP BY cedula UNION SELECT cedula, CONCAT (apellido1, ' ', apellido2, ' ', nombre1, ' ', nombre2) AS nombre FROM solicitud WHERE celular =  @celular OR tel_residencia = @celular  OR telefono_familiar = @celular OR telefono_personal = @celular OR celular_familiar = @celular OR celular_personal = @celular GROUP BY cedula UNION SELECT cedula, nombre FROM empleados WHERE telefono = @celular GROUP BY cedula UNION SELECT cedula, nombre FROM usuarios WHERE telefono = @celular GROUP BY cedula");
    $body = [
        "otp" => $otp, 
        "recipient" => "57" . $celular
    ];  

    $opciones = [
        "http" => [
            'method' => 'POST',
            'header' => "Authorization: Basic $basicAuth"."\r\n"."Content-Type: application/json"."\r\n",
            'content' => json_encode($body)
        ]
    ];

    
    $contexto = stream_context_create($opciones);
    $json_Input = file_get_contents($rutaOTP."otp-validator", false, $contexto);

    if ($json_Input) {
        $datos = json_decode($json_Input);

        if($datos->data->validOtp) {

            if(sqlsrv_query($link, "UPDATE historial_sms_otp SET estado = 1 WHERE id = ".$_POST['id'])){
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
        $response = array("code" => "404", "mensaje" => "No se obtuvo respuesta del servidor");
    }
} else {
    header("HTTP/2.0 200 OK");
    $response = array("code" => "404", "mensaje" => "Datos no encontrados".$_POST['codigo']);
}

echo json_encode($response);