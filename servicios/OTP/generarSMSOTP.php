<?php
    include('../cors.php');
    include_once('../../functions.php');

    $link = conectar_utf();

    if (isset($_POST['celular']) && isset($_POST['cedula'])) {
        $celular = $_POST['celular'];
        $cedula = $_POST['cedula'];
    }else{
        $json_Input = file_get_contents('php://input');
        $parametros = json_decode($json_Input);
        $celular = $parametros->celular;
        $cedula = $parametros->cedula;
    }

    if ($celular && $cedula){

        try {
            $otpConfigID = $otpConfigID_masivapp;

            $opciones2 = array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\n" .
                                 "Authorization: Basic ".$basicAuthOTP."\r\n",
                    'content' => '{
                        "otpConfigId" : "'.$otpConfigID.'",
                        "channelData" : {
                            "type" : "SMS",
                            "numberTo" : "57'.$celular.'"
                        }
                    }',
                ),
            );

            $contexto2 = stream_context_create($opciones2);
            $json_Input2 = file_get_contents($rutaOTP."generator", false, $contexto2);
            $data2 = json_decode($json_Input2);
            if ($data2->statusCode == 200) {
                $id_usuario = 0;    
                $otpEnviado = $data2->data->otpKey;
                
                if(isset($_SESSION["S_IDUSUARIO"])){
                    $id_usuario = $_SESSION["S_IDUSUARIO"];
                }

                if(sqlsrv_query($link, "INSERT INTO historial_sms_otp (cedula, celular, id_usuario, otp) values ('".$cedula."', '".$celular."', '".$id_usuario."', '".$otpEnviado."')")){
                    $id_formularioQuery = sqlsrv_query($link, "select scope_identity() as id_formulario");
                    $id_formularioArray =  sqlsrv_fetch_array($id_formularioQuery);
                    $id_formulario = $id_formularioArray['id_formulario'];
                }else{
                    $id_formulario = "";
                }

                header("HTTP/2.0 200 OK");
                $response = array("code" => "200", "mensaje" => "Se ejecutó correctamente la generación de codigo OTP por Email", "id" => $id_formulario);
            } else {
                header("HTTP/2.0 200 OK");
                $response = array("code" => "404", "mensaje" => "No Se Obtuvo Respuesta 2 del servidor");
            }
        } catch (\Throwable $e) {
            header("HTTP/2.0 200 OK");
            $response = array("code" => "404", "mensaje" => "Hubo un error al enviar el codigo por SMS");
        }
    } else {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "404", "mensaje" => "Datos No encontrados");
    }

    echo json_encode($response);
