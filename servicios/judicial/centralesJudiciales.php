<?php
    //Mostrar errores
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    if (isset($_REQUEST["numeroDocumento"]) && isset($_REQUEST["nombre"]) && isset($_REQUEST["idTipo"]) && isset($_REQUEST["tipoDocumento"])) {

        $url = 'https://az-ase-use2-dev-con-kredit-analyzerproxy-k.azurewebsites.net/api/Analizer/';
    
        $numeroDocumento = $_REQUEST["numeroDocumento"];
        $nombreC = $_REQUEST["nombre"];
        $nombre = str_replace('%20', ' ', $nombreC);
        $idTipo = $_REQUEST["idTipo"];
        $tipoDocumento = $_REQUEST["tipoDocumento"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>
            '{
                "idType": '. intval($idTipo) .',
                "documentType": '. intval($tipoDocumento) .',
                "documentNumber": "'. $numeroDocumento .'",
                "name": "'. $nombre .'"
            }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: ARRAffinity=dabbe73db1d23bf0733dce5a2956f7ad70e9c7dc47ca5cb363ebc0e1440b363d; ARRAffinitySameSite=dabbe73db1d23bf0733dce5a2956f7ad70e9c7dc47ca5cb363ebc0e1440b363d'
        ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($result);

        // header("HTTP/2.0 200 OK");
        // $response = array("code" => "300", "mensaje" => "revision data", "data" => $result);
 
        try {
    
            if (isset($json)) {
    
                $nombre = $json[0]->name;
    
                $cenPEPS = $json[1]->searchList;
                $sizePEPS = count($cenPEPS);
                
                $cenNa = $json[2]->searchList;
                $sizeNa = count($cenNa);
    
                $cenIn = $json[3]->searchList;
                $sizeIn = count($cenIn);
    
                $cenRa = $json[4]->searchList;
                $sizeRa = count($cenRa);

                if (isset($nombre)) {
                    try {
                        if ($sizePEPS > 0) {
                            for ($centralPEPS = 0; $centralPEPS < $sizePEPS; $centralPEPS++) {
                                $nombreCentralPEPS = $cenPEPS[$centralPEPS]->listName;
                                $riesgoPEPS = $cenPEPS[$centralPEPS]->inRisk;
                                $nombreEncontradoPEPS = $cenPEPS[$centralPEPS]->queryDetail->foundName;
                                $idEncontradoPEPS = $cenPEPS[$centralPEPS]->queryDetail->foundIdNumber;
                                $enlacePEPS = $cenPEPS[$centralPEPS]->queryDetail->link;
                                $zonaPEPS = $cenPEPS[$centralPEPS]->queryDetail->zone;
                                $actuacionesPEPS = $cenPEPS[$centralPEPS]->queryDetail->actuaciones;
                                $procesosJudicialesPEPS = $cenPEPS[$centralPEPS]->queryDetail->procesosJudiciales;

                                if (isset($riesgoPEPS)) {
                                    $consultaPEPS[] = array(
                                        "central" => $nombreCentralPEPS,
                                        "riesgo" => $riesgoPEPS,
                                        "nombreEncontrado" => $nombreEncontradoPEPS,
                                        "idEncontrado" => $idEncontradoPEPS,
                                        "enlace" => $enlacePEPS,
                                        "zona" => $zonaPEPS,
                                        "procesos" => $procesosJudicialesPEPS,
                                        "actuaciones" => $actuacionesPEPS
                                    );
                                } else {
                                    header("HTTP/2.0 200 OK");
                                    $response = array("code" => "300", "mensaje" => "Revisar caso, no aparecen centrales PEPS para determinar riesgo");
                                }
                            }
                        } else {
                            $consultaPEPS[] = array(
                                "central" => "No aparecen centrales",
                                "riesgo" => "",
                                "nombreEncontrado" => "",
                                "idEncontrado" => "",
                                "enlace" => "",
                                "zona" => "",
                                "procesos" => "",
                                "actuaciones" => ""
                            );
                        }
                    } catch (Exception $e) {
                        header("HTTP/2.0 200 OK");
                        $response = array("code" => "306", "mensaje" => "Error en el proceso de recorrer PEPS");
                    } finally {
                        try {
                            if ($sizeNa > 0) {
                                for ($centralNa = 0; $centralNa < $sizeNa; $centralNa++) {
                                    $nombreCentralNa = $cenNa[$centralNa]->listName;
                                    $riesgoNa = $cenNa[$centralNa]->inRisk;
                                    $nombreEncontradoNa = $cenNa[$centralNa]->queryDetail->foundName;
                                    $idEncontradoNa = $cenNa[$centralNa]->queryDetail->foundIdNumber;
                                    $enlaceNa = $cenNa[$centralNa]->queryDetail->link;
                                    $zonaNa = $cenNa[$centralNa]->queryDetail->zone;
                                    $actuacionesNa = $cenNa[$centralNa]->queryDetail->actuaciones;
                                    $procesosJudicialesNa = $cenNa[$centralNa]->queryDetail->procesosJudiciales;
                                    if (isset($riesgoNa)) {
                                        $consultaNa[] = array(
                                            "central" => $nombreCentralNa,
                                            "riesgo" => $riesgoNa,
                                            "nombreEncontrado" => $nombreEncontradoNa,
                                            "idEncontrado" => $idEncontradoNa,
                                            "enlace" => $enlaceNa,
                                            "zona" => $zonaNa,
                                            "procesos" => $procesosJudicialesNa,
                                            "actuaciones" => $actuacionesNa
                                        );
                                    } else {
                                        header("HTTP/2.0 200 OK");
                                        $response = array("code" => "300", "mensaje" => "Revisar caso, no aparecen centrales PEPS para determinar riesgo");
                                    }
                                }
                            } else {
                                $consultaNa[] = array(
                                    "central" => "No aparecen centrales",
                                    "riesgo" => "",
                                    "nombreEncontrado" => "",
                                    "idEncontrado" => "",
                                    "enlace" => "",
                                    "zona" => "",
                                    "procesos" => "",
                                    "actuaciones" => ""
                                );
                            }
                        } catch (Exception $e) {
                            header("HTTP/2.0 200 OK");
                            $response = array("code" => "306", "mensaje" => "Error en el proceso de recorrer centrales nacionales");
                        } finally {
                            try {
                                if ($sizeRa > 0) {                                            
                                    for ($centralRa = 0; $centralRa < $sizeRa; $centralRa++) {
                                        $nombreCentralRa = $cenRa[$centralRa]->listName;
                                        $riesgoRa = $cenRa[$centralRa]->inRisk;
                                        $nombreEncontradoRa = $cenRa[$centralRa]->queryDetail->foundName;
                                        $idEncontradoRa = $cenRa[$centralRa]->queryDetail->foundIdNumber;
                                        $enlaceRa = $cenRa[$centralRa]->queryDetail->link;
                                        $zonaRa = $cenRa[$centralRa]->queryDetail->zone;
                                        $actuacionesRa = $cenRa[$centralRa]->queryDetail->actuaciones;
                                        $procesosJudicialesRa = $cenRa[$centralRa]->queryDetail->procesosJudiciales;

                                        if (isset($riesgoRa)) {
                                            $consultaRa[] = array(
                                                "central" => $nombreCentralRa,
                                                "riesgo" => $riesgoRa,
                                                "nombreEncontrado" => $nombreEncontradoRa,
                                                "idEncontrado" => $idEncontradoRa,
                                                "enlace" => $enlaceRa,
                                                "zona" => $zonaRa,
                                                "procesos" => $procesosJudicialesRa,
                                                "actuaciones" => $actuacionesRa
                                            );
                                        } else {
                                            header("HTTP/2.0 200 OK");
                                            $response = array("code" => "300", "mensaje" => "Revisar caso, no aparecen centrales PEPS para determinar riesgo");
                                        }
                                    }
                                } else {
                                    $consultaRa[] = array(
                                        "central" => "No aparecen centrales",
                                        "riesgo" => "",
                                        "nombreEncontrado" => "",
                                        "idEncontrado" => "",
                                        "enlace" => "",
                                        "zona" => "",
                                        "procesos" => "",
                                        "actuaciones" => ""
                                    );
                                } 
                            } catch (Exception $e) {
                                header("HTTP/2.0 200 OK");
                                $response = array("code" => "306", "mensaje" => "Error en el proceso de recorrer centrales internacionales");
                            } finally {
                                try {
                                    if ($sizeIn > 0) {                                            
                                        for ($centralIn = 0; $centralIn < $sizeIn; $centralIn++) {
                                            $nombreCentralIn = $cenIn[$centralIn]->listName;
                                            $riesgoIn = $cenIn[$centralIn]->inRisk;
                                            $nombreEncontradoIn = $cenIn[$centralIn]->queryDetail->foundName;
                                            $idEncontradoIn = $cenIn[$centralIn]->queryDetail->foundIdNumber;
                                            $enlaceIn = $cenIn[$centralIn]->queryDetail->link;
                                            $zonaIn = $cenIn[$centralIn]->queryDetail->zone;
                                            $actuacionesIn = $cenIn[$centralIn]->queryDetail->actuaciones;
                                            $procesosJudicialesIn = $cenIn[$centralIn]->queryDetail->procesosJudiciales;

                                            if (isset($riesgoIn)) {
                                                $consultaIn[] = array(
                                                    "central" => $nombreCentralIn,
                                                    "riesgo" => $riesgoIn,
                                                    "nombreEncontrado" => $nombreEncontradoIn,
                                                    "idEncontrado" => $idEncontradoIn,
                                                    "enlace" => $enlaceIn,
                                                    "zona" => $zonaIn,
                                                    "procesos" => $procesosJudicialesIn,
                                                    "actuaciones" => $actuacionesIn
                                                );
                                            } else {
                                                header("HTTP/2.0 200 OK");
                                                $response = array("code" => "300", "mensaje" => "Revisar caso, no aparecen centrales PEPS para determinar riesgo");
                                            }
                                        }
                                    } else {
                                        $consultaIn[] = array(
                                            "central" => "No aparecen centrales",
                                            "riesgo" => "",
                                            "nombreEncontrado" => "",
                                            "idEncontrado" => "",
                                            "enlace" => "",
                                            "zona" => "",
                                            "procesos" => "",
                                            "actuaciones" => ""
                                        );
                                    } 
                                } catch (Exception $e) {
                                    header("HTTP/2.0 200 OK");
                                    $response = array("code" => "306", "mensaje" => "Error en el proceso de recorrer centrales internacionales");
                                } finally {
                                    $respuesta[] = array(
                                        "INFORMACION_GENERAL" => $nombre,
                                        "CENTRALES_NACIONALES" => $consultaNa,
                                        "CENTRALES_INTERNACIONALES" => $consultaIn,
                                        "RAMA_JUDICIAL" => $consultaRa,
                                        "PEPS" => $consultaPEPS
                                    );     
                                    header("HTTP/2.0 200 OK");
                                    $response = array("code" => "200", "mensaje" => "Persona validada con exito", "respuesta" => $respuesta); 
                                }
                            }
                        }
                    }            
                } else {
                    header("HTTP/2.0 200 OK");
                    $response = array("code" => "400", "mensaje" => "No existe, error en la peticion");
                }
                
            } else {
                header("HTTP/2.0 200 OK");
                $response = array("code" => "406", "mensaje" => "No se encuentra contenido del servidor", "message" =>$_REQUEST["nombre"]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }         
    } else {
        header("HTTP/2.0 200 OK");
        $response = array("code" => "400", "mensaje" => "faltan datos para realizar la consulta");
    }
    
    echo json_encode($response);

?>