<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    libxml_use_internal_errors(true);

    require_once("./cors.php");
    require_once("../functions.php");
    require_once("../function_blob_storage.php");
    $id_simulacion=$_GET["id_simulacion"];
    $response = array();
    $link = conectar();
    $consultarDatosCredito="SELECT a.id_simulacion,b.nombre1,b.nombre2,b.apellido1,b.apellido2,a.cedula FROM simulaciones a LEFT JOIN solicitud b ON a.id_simulacion=b.id_simulacion WHERE a.id_simulacion='".$id_simulacion."'";

    $queryDatosCredito=sqlsrv_query($link, $consultarDatosCredito);
    $resDatosCredito=sqlsrv_fetch_array($queryDatosCredito);
    $cedula=$resDatosCredito["cedula"];
    $apellido=strtoupper(trim($resDatosCredito["apellido1"]));
    $proveedor = 'EXPERIAN';
    
    $parametros = '"url":"' . url_servicios_centrales($proveedor,trim($cedula), strtoupper($apellido),'0','JSON','1') . '"';

    //$response_WS = WSCentrales(url_servicios_centrales($proveedor,"1014228486","LEAL",'0','JSON','1'), $parametros);
    $response_WS = WSCentrales(url_servicios_centrales($proveedor,trim($cedula),strtoupper($apellido),'0','JSON','1'), $parametros);
    if ($response_WS) {
        header("HTTP/1.1 200 Ok");
        $result_ws = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "WS" => $response_WS);
    } else {
        header("HTTP/1.1 200 Ok");
        $result_ws = array("estado" => 404, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible.", "WS_Response" => $response_WS, "WS_Parametros" => $parametros, "WS_URL" => $experian_hdcacierta_url);
    }
    $pdf_respuesta=1;
    $tipo_adjunto=2;
    $desc_tipo_adjunto="Consulta Datacredito";

    if ($result_ws["estado"]=='200')
    {
        $respuestaWS=json_decode($response_WS,true);
        $data=array("Codigo"=>$respuestaWS["Codigo"],
                    "Descripcion"=>$respuestaWS["Descripcion"],
                    "Salida"=>str_replace('"',"'",$respuestaWS["Salida"]),
                    "Url"=>str_replace('\\',"/",$respuestaWS["Url"])
                    );
        $json=json_encode($data);
        $query = ('INSERT into consultas_externas (id_simulacion, cedula, proveedor, servicio, error_xml, usuario_creacion, fecha_creacion,Salida_respuesta,Codigo_respuesta,Descripcion_respuesta,Url_respuesta) values ("'.$id_simulacion.'", "'.$cedula.'", "'.$proveedor.'", "'.$servicio.'",  "'.$error.'", "'.$usuario.'",CURRENT_TIMESTAMP,"'.str_replace('"',"'",preg_replace("[\n|\r|\n\r]", "", $respuestaWS["Salida"])).'","'.$respuestaWS["Codigo"].'","'.$respuestaWS["Descripcion"].'","'.str_replace('\\',"/",$respuestaWS["Url"]).'")');

        if (sqlsrv_query($link, $query)) {
           
            $ultimoConsultaExterna=sqlsrv_insert_id($link);
                
                //$data=json_decode($respuestaWS["Salida"]);
                
            $val=0;
            if ($respuestaWS["Codigo"]<>'0')
            {
                $response = array(
                    "estado" => 500, "descripcion" => "Error", "mensaje" => "No se obtuvo respuesta del servicio. Descripcion Error: ".$respuestaWS["Descripcion"],
                    "query" => $query, "parametros" => $parametros,  "response" => $result_ws
                );
            }else{
                $id_registro=sqlsrv_insert_id($link);
                $response = array(
                    "estado" => 200, "descripcion" => "Ok", "mensaje" => "Consulta hecha satisfactoriamente","query" => $query, "parametros" => $parametros,  "response" => $result_ws
                );
                $data=json_decode($respuestaWS["Salida"]);

                $tipo_adjunto=2;
                $desc_tipo_adjunto="Consulta Datacredito";
                    
        
        
                    
                $nombreArc=md5(rand()+$id_simulacion).".pdf";
                    
                $fechaa =new DateTime();
                $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
                                    
                $metadata1 = array(
                    'id_simulacion' => $id_simulacion,
                    'descripcion' => ($nombreArc),
                    'usuario_creacion' => "system",
                    'fecha_creacion' => $fechaFormateada
                );
                $consultarRespuestaWS="SELECT * FROM consultas_externas WHERE id_consulta='".$id_registro."'";
                $queryRespuestaWS=sqlsrv_query($link, $consultarRespuestaWS);
                $resRespuestaWS=sqlsrv_fetch_array($queryRespuestaWS);
                    
                if ($resRespuestaWS["pdf_respuesta"]=="")
                {
                    upload_file2($resRespuestaWS["Url_respuesta"], "simulaciones", $resRespuestaWS["id_simulacion"]."/adjuntos/".$nombreArc, $metadata1);
                    
                    sqlsrv_query($link, "INSERT into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$resRespuestaWS["id_simulacion"]."', '".$tipo_adjunto."', '".$desc_tipo_adjunto."', '".$nombreArc."', '".$nombreArc."', '1', '".$usuario."', GETDATE())");
                        
                    sqlsrv_query($link, "UPDATE consultas_externas set pdf_respuesta='".$nombreArc."' WHERE id_consulta='".$id_registro."'");
                    $response = array(
                        "estado" => 200, "descripcion" => "Ok. Archivo guardado en Adjuntos", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.","resp"=>$resRespuestaWS["Url_respuesta"]
                    );
                }else{
                    $response = array(
                        "estado" => 503, "descripcion" => "Ya el archivo fue cargado en adjuntos anteriormente", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.","resp"=>$resRespuestaWS["Url_respuesta"]
                        );
                }

                
               
                
            }
                //$consulta="UPDATE consultas_externas set parametros='".$parametros."',Salida_respuesta='".$respuestaWS["Salida"]."' WHERE id_consulta='$ultimoConsultaExterna'";
                //sqlsrv_query($consulta,$link);
        
        }else{
                //error
            $response = array("estado" => 503, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible. por favor notifica al aquipo de sistemas sobre este error12321", "response" => null, "parametros"=> $query);
        }
    }else{
        $response = array("estado" => 404, "descripcion" => "Error", "mensaje" => "El servicio no se encuentra disponible. por favor notifica al aquipo de sistemas sobre este error", "response" => $result_ws, "parametros"=> null);
    }

    
    header("HTTP/2.0 200 OK");
    echo json_encode($response);
?>