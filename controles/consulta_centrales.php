<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// libxml_use_internal_errors(true);

require_once("./cors.php");
require_once("../functions.php");
require_once("../function_blob_storage.php");

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input);
$response = array();
$proceso = $params->proceso;
$usuario = $params->usuario;
$link = conectar();
$proveedor = '';



switch ($proceso) {
    case 'Cargar_Adjunto':
        $id_registro=$params->id_registro;
        $usuario= $params->usuario;
        $servicio= $params->servicio;
        Cargar_Adjunto ($id_registro,$usuario,$servicio);
        break;
    case 'Consulta Centrales':
        $servicio      = $params->servicio;
        $id_simulacion = $params->id_Simulacion;
        $cedula        = $params->cedula;
        Consulta_Centrales($servicio, $id_simulacion, $cedula);
        break;
    case 'Consulta Disponibilidad':
        $id_simulacion = $params->id_Simulacion;
        $cedula = $params->cedula;
        Consulta_Disponibilidad($id_simulacion,$cedula);
        break;
    case 'Consumir WS':
        $servicio      = $params->servicio;
        $id_simulacion = $params->id_Simulacion;
        $cedula        = $params->cedula;
        $pagaduria     = $params->pagaduria;
        $comercial     = $params->id_comercial;
        $usuario       = $params->usuario;
        $apellido      = $params->lastName;
        Consumir_WS($servicio, $id_simulacion, $cedula, $usuario, $apellido);
        
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        $response = array("resp" => "404", "descripcion" => "Not Found", "mensaje" => "Solicitud no encontrada", "fecha" => date("Y-m-d"), 'Proceso' => $proceso);
        echo json_encode($response);
        break;
}

 

 


function Consulta_Disponibilidad($id_simulacion,$cedula)
{
    global $link;
 
        //$query = ("SELECT DISTINCT id_consulta, id_simulacion, cedula, proveedor, servicio, parametros, respuesta, error_xml, usuario_creacion, fecha_creacion FROM consultas_externas WHERE id_simulacion = '$id_simulacion' and fecha_creacion in ( SELECT max(fecha_creacion) FROM consultas_externas WHERE id_simulacion ='$id_simulacion' group by proveedor, servicio );");
        $respuestaCentrales=array();
        $data=array();
        
        $consultaBase="SELECT TOP 1 puntaje_datacredito,DATEDIFF(DAY, CURRENT_TIMESTAMP,fecha_creacion) AS dias,id_consulta,id_simulacion,cedula,proveedor,servicio,usuario_creacion,fecha_creacion,parametros,Salida_respuesta,error_xml,pdf_respuesta,Url_respuesta FROM consultas_externas WHERE (cedula=".$cedula." or id_simulacion='".$id_simulacion."') and Codigo_respuesta='0' ";

        $consultaEXPERIAN=$consultaBase." AND servicio='HDC_ACIERTA' ORDER BY id_consulta DESC";

        $queryEXPERIAN = sqlsrv_query($link, $consultaEXPERIAN, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($queryEXPERIAN) > 0) 
        {
             while ($res = sqlsrv_fetch_array($queryEXPERIAN)) {
                //$dias = date_diff(new DateTime($res["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
                if ($res["dias"]<30) 
                {$reconsultaexperian="reconsulta";}
                else if ($res["dias"]>30)
                {$reconsultaexperian="nueva_consulta";}
                $data_Experian = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultaexperian,
                    "parametros" => $res["parametros"],
                    //"Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }
           
        }else{
            $data_Experian = "0";
        }


        $consultaINFORMACION_COMERCIAL=$consultaBase." AND servicio='INFORMACION_COMERCIAL'";
        $queryINFORMACION_COMERCIAL = sqlsrv_query($link, $consultaINFORMACION_COMERCIAL, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if (sqlsrv_num_rows($queryINFORMACION_COMERCIAL) > 0) 
        {
             while ($res = sqlsrv_fetch_array($queryINFORMACION_COMERCIAL)) {
                //$dias = date_diff(new DateTime($res["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
            

                if ($res["dias"]<30) 
                {$reconsultainformacion="reconsulta";}
                else if ($res["dias"]>30)
                {$reconsultainformacion="nueva_consulta";}
                $data_Informacion_Comercial = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultainformacion,
                    "parametros" => $res["parametros"],
                    //"Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    // "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }
            
        }else{
            $data_Informacion_Comercial = 0;
        }


        $consultaLEGALCHECK=$consultaBase." AND servicio='LEGALCHECK'";
        $queryLEGALCHECK = sqlsrv_query($link, $consultaLEGALCHECK, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if (sqlsrv_num_rows($queryLEGALCHECK) > 0) 
        {
             while ($res = sqlsrv_fetch_array($queryLEGALCHECK)) {
                //$dias = date_diff(new DateTime($res["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
        
                if ($res["dias"]<30) 
                {$reconsultalegal="reconsulta";}
                else if ($res["dias"]>30)
                {$reconsultalegal="nueva_consulta";}
                $data_LEGALCHECK = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultalegal,
                    "parametros" => $res["parametros"],
                    "Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    // "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }
            
        }else{
            $data_LEGALCHECK = 0;
        }

        $consultaUBICAPLUS=$consultaBase." AND servicio='UBICAPLUS'";
        $queryUBICAPLUS = sqlsrv_query($link, $consultaUBICAPLUS, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if (sqlsrv_num_rows($queryUBICAPLUS) > 0) 
        {
             while ($res = sqlsrv_fetch_array($queryUBICAPLUS)) {
                //$dias = date_diff(new DateTime($res["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
   

                 if ($res["dias"]<30) 
                {$reconsultaUP="reconsulta";}
                else if ($res["dias"]>30)
                {$reconsultaUP="nueva_consulta";}
                $data_UBICAPLUS = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultaUP,
                    "parametros" => $res["parametros"],
                    //"Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    // "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }
            
        }else{
            $data_UBICAPLUS = 0;
        }


        $consultaCREDITVISION=$consultaBase." AND servicio='CREDITVISION'";
        $queryCREDITVISION = sqlsrv_query($link, $consultaCREDITVISION, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if (sqlsrv_num_rows($queryCREDITVISION) > 0) 
        {
             while ($res = sqlsrv_fetch_array($queryCREDITVISION)) {
                //$dias = date_diff(new DateTime($res["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
               
                if ($res["dias"]==0) 
                {$reconsultaVision="nueva_consulta";}
                else if ($res["dias"]<30) 
                {$reconsultaVision="reconsulta";}
                else if ($res["dias"]>30)
                {$reconsultaVision="nueva_consulta";}
                $data_CREDITVISION = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultaVision,
                    "parametros" => $res["parametros"],
                    //"Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    // "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }
            
        }else{
            $data_CREDITVISION = 0;
        }

        $consultaLEGALCHECK=$consultaBase." AND servicio='LEGALCHECK'";
        $queryLEGALCHECK = sqlsrv_query($link, $consultaLEGALCHECK, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($queryLEGALCHECK) > 0) {
            while ($res = sqlsrv_fetch_array($queryLEGALCHECK)) {
                if ($res["dias"]<30) {
                    $reconsultaUP="reconsulta";
                } else if ($res["dias"]>30){
                    $reconsultaUP="nueva_consulta";
                }

    

                $data_LEGALCHECK = array(
                    "id_Consulta" => $res["id_consulta"],
                    "id_Simulacion" => $res["id_simulacion"],
                    "cedula" => $res["cedula"],
                    "proveedor" => $res["proveedor"],
                    "servicio" => $res["servicio"],
                    "usuario_creacion" => $res["usuario_creacion"],
                    "fecha_creacion" => $res["fecha_creacion"],
                    "fecha_vence" => $reconsultaUP,
                    "parametros" => $res["parametros"],
                    //"Salida_respuesta" => $res["Salida_respuesta"],
                    "error_xml" => $res["error_xml"],
                    "puntaje_datacredito" => $res["puntaje_datacredito"],
                    "pdf_respuesta" => generateBlobDownloadLinkWithSAS("simulaciones",$res["id_simulacion"]."/adjuntos/".$res["pdf_respuesta"]),
                    "pdf_respuesta2"=>$res["Url_respuesta"],
                    "pdf_respuesta3"=>$res["pdf_respuesta"]
                );
            }            
        }else{
            $data_LEGALCHECK = 0;
        }

    $response = array("EXPERIAN" => $data_Experian,"INFORMACION_COMERCIAL" => $data_Informacion_Comercial,"UBICAPLUS"=>$data_UBICAPLUS,"CREDITVISION"=>$data_CREDITVISION, "LEGALCHECK" => $data_LEGALCHECK);

    
    header("HTTP/2.0 200 OK");
    echo json_encode($response);
    
}

function Consulta_Centrales($servicio, $id_simulacion, $cedula) 
{
    global $link;
    $query = ("SELECT * from consultas_externas where id_simulacion = '$id_simulacion' and servicio = '$servicio' and cedula = '$cedula' order by fecha_creacion desc");
    $ejecucion_query = sqlsrv_query($link, $query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
    if ($ejecucion_query) {
        while ($resp = sqlsrv_fetch_array($ejecucion_query)) {
            $dias = date_diff(new DateTime($resp["fecha_creacion"]), new DateTime(date('Y-m-d h:i:s')))->days;
            if ($dias < 30) {
                $response = array("estado" => 203, "descripcion" => "Non-Authoritative Information", "mensaje" => "No se puede volver a descargar esta informacion, porque es menor a 30 dias");
            } else {
                $response = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se debe solicitar el segundo apellido del cliente");
            }
        }
    } else {
        $response = array("estado" => 500, "descripcion" => "Falla en el servicio", "mensaje" => "Ocurrio un error a nivel de base de datos, por favor notifica a sistemas");
    }
    echo json_encode($response);
}

function Cargar_Adjunto ($id_registro,$usuario,$servicio)
{
    global $link;
    $pdf_respuesta=0;
    $tipo_adjunto=0;
    $desc_tipo_adjunto="";
    switch ($servicio) {
        case 'HDC_ACIERTA':
           
            $pdf_respuesta=1;
            $tipo_adjunto=2;
            $desc_tipo_adjunto="Consulta Datacredito";
            break;
        case 'INFORMACION_COMERCIAL':
         
            $pdf_respuesta=1;
            $tipo_adjunto=3;
            $desc_tipo_adjunto="Consulta Transunion";
            break;
      
        case 'UBICAPLUS':
          
            $pdf_respuesta=1;
            $tipo_adjunto=54;
            $desc_tipo_adjunto="Consulta UbicaPlus";
            break;
            case 'CREDITVISION':        
                $pdf_respuesta=0;
                $tipo_adjunto=69;
                $desc_tipo_adjunto="Consulta Credit Vision";
                break;
            
            case 'LEGALCHECK':
                $pdf_respuesta=1;
                $tipo_adjunto=6;
                $desc_tipo_adjunto="Legal Check Descargado";
                break;
    
            default:
                $response = array("estado" => 404, "mensaje" => "No se encontro informacion del servicio solicitado, por favor notifique esto al departamento de sistemas e indique el Id de simulacion");
                break;
        }

    if ($pdf_respuesta==1)
    {
        
        $respuesta=json_decode($response_WS);
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
        $queryRespuestaWS=sqlsrv_query($link, $consultarRespuestaWS, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $resRespuestaWS=sqlsrv_fetch_array($queryRespuestaWS);
        
        if ($resRespuestaWS["pdf_respuesta"]=="")
        {
            upload_file2($resRespuestaWS["Url_respuesta"], "simulaciones", $resRespuestaWS["id_simulacion"]."/adjuntos/".$nombreArc, $metadata1);
        
            sqlsrv_query($link, "insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$resRespuestaWS["id_simulacion"]."', '".$tipo_adjunto."', '".$desc_tipo_adjunto."', '".$nombreArc."', '".$nombreArc."', '1', '".$usuario."', NOW())");
            
            sqlsrv_query($link, "UPDATE consultas_externas set pdf_respuesta='".$nombreArc."' WHERE id_consulta='".$id_registro."'");


            $resRespuestaWS["Url_respuesta"] = generateBlobDownloadLinkWithSAS("simulaciones", $resRespuestaWS["id_simulacion"] . "/adjuntos/" . $nombreArc);

            $response = array("estado" => 503, "descripcion" => "Ya el archivo fue cargado en adjuntos anteriormente", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "resp"=>$resRespuestaWS["Url_respuesta"]);
        }else{
            $queryConAdj2 = sqlsrv_query($link, "SELECT TOP 1 * FROM adjuntos WHERE id_simulacion = '".$resRespuestaWS["id_simulacion"]."' AND id_tipo = '".$tipo_adjunto."' ORDER BY id_adjunto DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));  

            if($queryConAdj2 && sqlsrv_num_rows($queryConAdj2) > 0){
                $datosAdjunto = sqlsrv_fetch_array($queryConAdj2);

                $resRespuestaWS["Url_respuesta"] = generateBlobDownloadLinkWithSAS("simulaciones", $resRespuestaWS["id_simulacion"] . "/adjuntos/" . $datosAdjunto["nombre_grabado"]);
            }

            $response = array("estado" => 503, "descripcion" => "Ya el archivo fue cargado en adjuntos anteriormente", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "resp"=>$resRespuestaWS["Url_respuesta"]);
        }
        
    }else{
        $response = array(
            "estado" => 500, 
            "descripcion" => "Este tipo de consulta no requiere ser cargado en adjuntos", 
            "mensaje" => "Error"
        );
    }

    header("HTTP/2.0 200 OK");
    echo json_encode($response);
}


function Consumir_WS($servicio, $id_simulacion, $cedula, $usuario,  $apellido) {
    global $link;
    global $experian_userid;
    global $experian_password;
    global $experian_hdcacierta_url;
    global $experian_hdcacierta_product;
    global $transunion_userid;
    global $transunion_password;
    global $transunion_reason;
    global $transunion_infocomercial_url;
    global $transunion_infocomercial_product;
    global $transunion_legalcheck_url;
    global $transunion_legalcheck_product;
    global $transunion_ubicaplus_url;
    global $transunion_ubicaplus_product;

    // echo '<br/>'. $link;
    // echo '<br/>'. $experian_userid;
    // echo '<br/>'. $experian_password;
    // echo '<br/>'. $experian_hdcacierta_url;
    // echo '<br/>'. $experian_hdcacierta_product;
    // echo '<br/>'. $transunion_userid;
    // echo '<br/>'. $transunion_password;
    // echo '<br/>'. $transunion_reason;
    // echo '<br/>'. $transunion_infocomercial_url;
    // echo '<br/>'. $transunion_infocomercial_product;
    // echo '<br/>'. $transunion_legalcheck_url;
    // echo '<br/>'. $transunion_legalcheck_product;
    // echo '<br/>'. $transunion_ubicaplus_url;
    // echo '<br/>'. $transunion_ubicaplus_product;
    // exit;

    $pdf_respuesta=0;
    $tipo_adjunto=0;
    $desc_tipo_adjunto="";

    switch ($servicio) {
        case 'HDC_ACIERTA':
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
            break;
        case 'INFORMACION_COMERCIAL':
            $proveedor = 'INFORMACION_COMERCIAL';
            $parametros = '"idType":"1","idNumber":"' . trim($cedula) . '","reason":"' . $transunion_reason . '","infoCode":"' . $transunion_infocomercial_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';
            //$response_WS = WSCentrales(url_servicios_centrales($proveedor,"10000021","GALLEGO",'0','JSON','1'), $parametros);
            $response_WS = WSCentrales(url_servicios_centrales($proveedor,trim($cedula),strtoupper($apellido),'0','JSON','1'), $parametros);
            

            if ($response_WS) {
                header("HTTP/1.1 200 Ok");
                $result_ws = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "WS" => $response_WS);
            } else {
                header("HTTP/1.1 200 Ok");
                $result_ws = array("estado" => 503, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible.", "WS_Response" => $response_WS, "WS_Parametros" => $parametros, "WS_URL" => $transunion_infocomercial_url);
            }
            $pdf_respuesta=1;
            $tipo_adjunto=3;
            $desc_tipo_adjunto="Consulta Transunion";
            break;
       
        case 'UBICAPLUS':
            
            $proveedor = 'UBICAPLUS';
            $parametros = '"idType":"1","idNumber":"' . trim($cedula) . '","reason":"' . $transunion_reason . '","infoCode":"' . $transunion_ubicaplus_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';
            $response_WS = WSCentrales(url_servicios_centrales($proveedor,trim($cedula),strtoupper($apellido),'0','JSON','1'), $parametros);
            if ($response_WS) {
                $result_ws = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "WS" => $response_WS);
            } else {
                $result_ws = array("estado" => 503, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible.", "WS_Response" => $response_WS, "WS_Parametros" => $parametros, "WS_URL" => $transunion_ubicaplus_url);
            }
            $pdf_respuesta=1;
            $tipo_adjunto=54;
            $desc_tipo_adjunto="Consulta UbicaPlus";
            break;
        case 'CREDITVISION':
            
                $proveedor = 'CREDITVISION';
                $parametros = '"idType":"1","idNumber":"' . $cedula . '","reason":"' . $transunion_reason . '","infoCode":"' . $transunion_ubicaplus_product . '","userId":"' . $transunion_userid . '","password":"' . $transunion_password . '"';
                $response_WS = WSCentrales(url_servicios_centrales($proveedor,$cedula,strtoupper($apellido),'0','JSON','1'), $parametros);
                if ($response_WS) {
                    $result_ws = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "WS" => $response_WS);
                } else {
                    $result_ws = array("estado" => 503, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible.", "WS_Response" => $response_WS, "WS_Parametros" => $parametros, "WS_URL" => $transunion_ubicaplus_url);
                }
                break;
                $pdf_respuesta=0;
            $tipo_adjunto=69;
            $desc_tipo_adjunto="Consulta Credit Vision";
            break;
            
        case 'LEGALCHECK':
            $proveedor = 'LEGALCHECK';
            $parametros = '"url":"' . url_servicios_centrales($proveedor,trim($cedula), strtoupper($apellido),'0','JSON','1') . '"';
    
            $response_WS = WSCentrales(url_servicios_centrales($proveedor,trim($cedula),strtoupper($apellido),'0','JSON','1'), $parametros);
            if ($response_WS) {
                header("HTTP/1.1 200 Ok");
                $result_ws = array("estado" => 200, "descripcion" => "Ok", "mensaje" => "Se consumio el servicio y se obtuvo una respuesta satisfactoria.", "WS" => $response_WS);
            } else {
                header("HTTP/1.1 200 Ok");
                $result_ws = array("estado" => 404, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible.", "WS_Response" => $response_WS, "WS_Parametros" => $parametros, "WS_URL" => $transunion_legalcheck_url);
            }
            $pdf_respuesta=1;
            $tipo_adjunto=2;
            $desc_tipo_adjunto="Consulta LEGALCHECK";
            break;
        default:
            $response = array("estado" => 404, "mensaje" => "No se encontro informacion del servicio solicitado, por favor notifique esto al departamento de sistemas e indique el Id de simulacion");
            break;
    }


    if ($result_ws["estado"]=='200'){

        $respuestaWS=json_decode($response_WS,true);
        $data=array("Codigo"=>$respuestaWS["Codigo"],
            "Descripcion"=>$respuestaWS["Descripcion"],
            "Salida"=>str_replace('"',"'",$respuestaWS["Salida"]),
            "Url"=>str_replace('\\',"/",$respuestaWS["Url"])
        );

        $json=json_encode($data);
        $query = ("insert into consultas_externas (id_simulacion, cedula, proveedor, servicio, error_xml, usuario_creacion, fecha_creacion, Salida_respuesta, Codigo_respuesta, Descripcion_respuesta, Url_respuesta) values ('".$id_simulacion."', '".$cedula."', '".$proveedor."', '".$servicio."',  '".$error."', '".$usuario."', CURRENT_TIMESTAMP, '".preg_replace("[\n|\r|\n\r]", "", $respuestaWS["Salida"])."','".$respuestaWS["Codigo"]."','".$respuestaWS["Descripcion"]."','".str_replace('\\',"/",$respuestaWS["Url"])."')");

        if (sqlsrv_query($link, $query)) {
            
            $ultimoId=sqlsrv_query($link, "SELECT SCOPE_IDENTITY() AS ultimoConsultaExterna");
            $ultimoId2 = sqlsrv_fetch_array($ultimoId);
            $ultimoConsultaExterna =  $ultimoId2['ultimoConsultaExterna'];
            
            // $ultimoConsultaExterna=sqlsrv_insert_id($link);
            $val=0;

            if ($respuestaWS["Codigo"]<>'0')  {
                $response = array(
                    "estado" => 500, "descripcion" => "Error", "mensaje" => "No se obtuvo respuesta del servicio. Descripcion Error: ".$respuestaWS["Descripcion"], "parametros" => $parametros,  
                    "response" => $result_ws
                );
            }else{
                $ultimoId=sqlsrv_query($link, "SELECT SCOPE_IDENTITY() AS ultimoConsultaExterna");
                $ultimoId2 = sqlsrv_fetch_array($ultimoId);
                $ultimoConsultaExterna =  $ultimoId2['ultimoConsultaExterna'];

                // $ultimoConsultaExterna=sqlsrv_insert_id($link);
        
                $data=json_decode($respuestaWS["Salida"]);
                switch ($servicio) {
                    case 'HDC_ACIERTA':
                        $consulta="UPDATE consultas_externas set puntaje_datacredito='".$data->RESPUESTA->Informes->Informe->Score->puntaje."',parametros='".$parametros."' WHERE id_consulta='$ultimoConsultaExterna'";
                        sqlsrv_query($link, $consulta);        
                        $consulta2="UPDATE simulaciones set puntaje_datacredito='".$data->RESPUESTA->Informes->Informe->Score->puntaje."' WHERE id_simulacion='$id_simulacion'";
                        sqlsrv_query($link, $consulta2);
                    break;                   
                }
               
                $response = array(
                    "estado" => 200, "descripcion" => "Ok", "mensaje" => "Consulta hecha satisfactoriamente","parametros" => $parametros,  "response" => $result_ws
                );              
            }    
        } else {
            //error
            $response = array("estado" => 503, "descripcion" => "Falla", "mensaje" => "El servicio no se encuentra disponible. por favor notifica al aquipo de sistemas sobre este error", "response" => null, "parametros"=> $query);
        }
    }else{
        
        $response = array("estado" => 404, "descripcion" => "Error", "mensaje" => "El servicio no se encuentra disponible. por favor notifica al aquipo de sistemas sobre este error", "response" => $result_ws, "parametros"=> null);
    }
    
    header("HTTP/2.0 200 OK");
    echo json_encode($response);
}

?>