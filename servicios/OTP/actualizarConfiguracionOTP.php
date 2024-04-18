<?php
include('../cors.php');
include_once('../../functions.php');

try {
    $opciones = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n" .
                         "Authorization: Basic ".$basicAuth."\r\n",
            'content' => '{
                "id": "'.$otpConfigID_masivapp.'",
                "clientId": 1,
                "productId": 12,
                "lengthKey": 6,
                "generationsPerMinute": 5,
                "attemptsExpiration": 10,
                "timeExpiration": 300000,
                "containUpperCharacters": false,
                "containNumberCharacters": true,
                "containLowerCharacters": false,
                "otpConfigStatus": "Activa",
                "hideOtpInResponse": true,
                "lockDurationTime": 0,
                "responseLanguage": "ES",
                "statusIdInResponse": false,
                "voiceOtp": false,
                "channelList": [
                    {
                        "name": "SMS",
                        "template": {
                            "message": "Autorizo a KREDIT PLUS S.A. a ser consultado ante centrales de información financiera, validando mi identidad mediante el siguiente código OTP: {{OTP}}."
                        }
                    },
                    {
                        "name": "Voice",
                        "template": {
                            "message": "Autorizo a KREDIT PLUS S.A. a ser consultado ante centrales de información financiera, validando mi identidad mediante el siguiente código otepe: {{OTP}} , recuerda {{OTP}}, te lo repito otra vez {{OTP}}",
                            "voiceId": "Miguel",
                            "voiceRetries": 2,
                            "voiceTimeRetries": 2,
                            "voiceFrom": "3226783420"
                        }
                    },
                    {
                        "name": "Hub Transaccional",
                        "template": {
                            "hubFlowId": "'.$hubFlowId_masivapp.'"
                        }
                    },
                    {
                        "name": "Email",
                        "template": {
                            "emailFrom": "notificaciones@kredit.com.co",
                            "emailSubject": "Codigo OTP",
                            "message": "Autorizo a KREDIT PLUS S.A. a ser consultado ante centrales de información financiera, validando mi identidad mediante el siguiente código OTP: {{OTP}}.",
                            "nameEmailFrom": "Kredit OTP Manager",
                            "replyTo": "notificaciones@kredit.com.co"
                        }
                    }
                ]
            }',
        ),
    );

    $contexto = stream_context_create($opciones);
    $json_Input = file_get_contents("https://otp-manager.masivapp.com/transactional-api/v1/otp-config", false, $contexto);
    $data  = json_decode($json_Input);

    if ($data->statusCode == 200) {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "200", "mensaje" => "Configuacion OTP Creada", "data" => $data->data->otpConfigId);
    } else {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "500", "mensaje" => "Hubo un error al crear el configId", "data" => $json_Input);    
    }
} catch (\Throwable $e) {
    header("HTTP/2.0 200 OK");
    $response = array("code" => "404", "mensaje" => "Hubo un error al enviar el codigo por Email");
}

echo json_encode($response);