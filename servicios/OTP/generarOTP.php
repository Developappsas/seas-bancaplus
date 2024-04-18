<?php
include_once('../../functions.php');

$link = conectar_utf();

$rutaOTP = 'https://otp-manager.masivapp.com/transactional-api/v1/';
;

if (isset($_POST['celular'])) {
    $basicAuth = 'S2NyZWRpdF9PVFBfOTlKRkw6ZHNONDZrMGwkRg==';
    $celular = $_POST['celular'];
    $cedula = $_POST['cedula'];
    
    

    //Consultamos si ya esta verificado
    //$consultar = sqlsrv_query($link,"SELECT estado_respuesta FROM historial_tokens_verificacion_id WHERE id_simulacion = ".$id_simulacion." AND estado_respuesta in(2,14)");//2-Verificado o 14-verificado Anteriormente
    // $consultar = sqlsrv_query($link,"SELECT cedula, nombre FROM simulaciones WHERE telefono = @celular GROUP BY cedula UNION SELECT cedula, CONCAT (apellido1, ' ', apellido2, ' ', nombre1, ' ', nombre2) AS nombre FROM solicitud WHERE celular =  @celular OR tel_residencia = @celular  OR telefono_familiar = @celular OR telefono_personal = @celular OR celular_familiar = @celular OR celular_personal = @celular GROUP BY cedula UNION SELECT cedula, nombre FROM empleados WHERE telefono = @celular GROUP BY cedula UNION SELECT cedula, nombre FROM usuarios WHERE telefono = @celular GROUP BY cedula");
    $body = [
        "productId" => 12, 
        "lengthKey" => 6, 
        "generationsPerMinute" => 5, 
        "attemptsExpiration" => 3, 
        "timeExpiration" => 300, 
        "containUpperCharacters" => false, 
        "containNumberCharacters" => true, 
        "containLowerCharacters" => false, 
        "channelList" => [
            [
                "name" => "SMS", 
                "template" => [
                "message" => "Autorizo a KREDIT PLUS S.A. a ser consultado ante centrales de información financiera, validando mi identidad mediante el siguiente código OTP: {{OTP}}." 
                ] 
            ]
        ] 
    ]; 

    $opciones = [
        "http" => [
            'method' => 'POST',
            'header' => "Authorization: Basic $basicAuth"."\r\n"."Content-Type: application/json"."\r\n",
            'content' => json_encode($body)
        ]
    ];

    $contexto = stream_context_create($opciones);
    $json_Input = file_get_contents($rutaOTP."otp-config", false, $contexto);
    
    if ($json_Input) {
        $datos = json_decode($json_Input);

        if($datos->data->otpConfigId != "") {
            $body2 = [
                "otpConfigId" => $datos->data->otpConfigId , 
                "channelData" => [
                    "type" => "SMS", 
                    "numberTo" => "57" . $celular
                ] 
            ]; 

            $opciones2 = [
                "http" => [
                    'method' => 'POST',
                    'header' => "Authorization: Basic $basicAuth"."\r\n"."Content-Type: application/json"."\r\n",
                    'content' => json_encode($body2)
                ]
            ];

            $contexto2 = stream_context_create($opciones2);
            $json_Input2 = file_get_contents($rutaOTP."generator", false, $contexto2);

            if ($json_Input2) {
                $datos2 = json_decode($json_Input2);

                $id_usuario = 0;
                if(isset($_SESSION["S_IDUSUARIO"])){
                    $id_usuario = $_SESSION["S_IDUSUARIO"];
                }

                if(sqlsrv_query($link, "INSERT INTO historial_sms_otp (cedula, celular, id_usuario, otp) values ('".$cedula."', '".$celular."', '".$id_usuario."', '".$datos2->data->otpKey."')")){
                    
                    $id = sqlsrv_query($link, "SELECT SCOPE_IDENTITY() as id");
                    $id2 = sqlsrv_fetch_array($id, SQLSRV_FETCH_ASSOC);
                    $id_formulario = $id2["id"];
                }else{
                    $id_formulario = "";
                }

                header("HTTP/2.0 200 OK");
                $response = array("code" => "200", "mensaje" => "Ejecutado", "id" => $id_formulario);
            } else {
                header("HTTP/2.0 200 OK");
                $response = array("code" => "404", "mensaje" => "No Se Obtuvo Respuesta 2 del servidor");
            }
        }else{

            header("HTTP/2.0 200 OK");
            $response = array("code" => "300", "mensaje" => "No se pudo obtener el ID de OTP ");
        }
    } else {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "404", "mensaje" => "No Se Obtuvo Respuesta 1 del servidor");
    }
} else {
    header("HTTP/2.0 200 OK");
    $response = array("code" => "404", "mensaje" => "Datos No encontrados");
}

echo json_encode($response);