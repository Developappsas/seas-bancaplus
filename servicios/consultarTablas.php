<?php
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
    
    include_once ('../functions.php');
    include_once ('../function_blob_storage.php');
    header("Content-Type: application/json; charset=utf-8");
    
    $link = conectar_utf();
    if ($_POST["exe"]=="consultarComprasCarteras")
    {
        $idSimulacion=$_POST["idSimulacion"];
        consultarComprasCarteras($idSimulacion);
    }
    


    function consultarComprasCarteras($idSimulacion) 
    {
        global $link;  
        
        
        $response = array();
        $data = array();
        $data2 = array();
        $mensaje = '';
        $query2="SELECT nombre_grabado,id_tipo,id_adjunto,id_simulacion, CASE WHEN id_tipo=10 THEN 'COMPROBANTE DE PAGO' END AS nombre_tipo_adjunto FROM adjuntos WHERE id_tipo in (10) and id_simulacion in ('".$idSimulacion."')";
        $responseQuery2=sqlsrv_query($link,$query2);
        while ($fetchResponse2 = sqlsrv_fetch_array($responseQuery2, SQLSRV_FETCH_ASSOC))
        {


            $data2[]=array("tipo_adjunto"=>$fetchResponse2["nombre_tipo_adjunto"],
            "id_adjunto"=>$fetchResponse2["id_adjunto"],
            "icono_archivo"=>'<a target="_blank" id="descargarArchivo" href="'.generateBlobDownloadLinkWithSAS("simulaciones",$fetchResponse2["id_simulacion"]."/adjuntos/".$fetchResponse2["nombre_grabado"]).'" name="'.$fetchResponse2["nombre_grabado"].'" class="btn btn-primary">Descargar</a>');
        }

        $results2 = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data2),
            "iTotalDisplayRecords" => count($data2),
            "aaData" => $data2
        );


        $query="(SELECT TOP 1 a.id_simulacion,a.id_adjunto,a.cuota,a.valor_pagar,b.nombre_grabado,c.nombre AS nombre_entidad 
        FROM simulaciones_comprascartera a 
        LEFT JOIN adjuntos b ON a.id_adjunto=b.id_adjunto 
        LEFT JOIN entidades_desembolso c ON c.id_entidad=a.id_entidad 
        WHERE a.id_simulacion='".$idSimulacion."' AND a.se_compra='SI' AND b.nombre_grabado IS NOT NULL)
        UNION
        (SELECT a.id_simulacion,a.id_giro,'0' AS valor_cuota,a.valor_girar,b.nombre_grabado,'DESEMBOLSO CLIENTE' FROM giros a 
        LEFT JOIN adjuntos b ON a.id_simulacion=b.id_simulacion
        WHERE a.id_simulacion='".$idSimulacion."'  AND a.clasificacion='DSC' AND b.id_tipo=11 ORDER BY b.id_adjunto DESC )
        ";
        $responseQuery = sqlsrv_query($link,$query, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if (sqlsrv_num_rows($responseQuery)>0)
        {
            if ( !$responseQuery ) {
                
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"504","mensaje"=>"Error al ejecutar consulta. Err ".sqlsrv_error($link), "data"=>$response );
                echo json_encode($response);    
                exit;
            }
    
            
            while ($fetchResponse = sqlsrv_fetch_array($responseQuery, SQLSRV_FETCH_ASSOC))
            {
    

                $data[]=array("nombre_entidad"=>$fetchResponse["nombre_entidad"],
                "id_adjunto"=>$fetchResponse["id_adjunto"],
                "cuota"=>"$".number_format($fetchResponse["cuota"], 0, ".", ","),
                "valor_pagar"=>"$".number_format($fetchResponse["valor_pagar"], 0, ".", ","),
                "icono_archivo"=>'<a target="_blank" id="descargarArchivo" href="'.generateBlobDownloadLinkWithSAS("simulaciones",$fetchResponse["id_simulacion"]."/adjuntos/".$fetchResponse["nombre_grabado"]).'" name="'.$fetchResponse["nombre_grabado"].'" class="btn btn-primary">Descargar</a>');
            }

                $results = array(
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                );
                header("HTTP/2.0 200 OK");
                
     
                $response = array( "code"=>"200","mensaje"=>"Ejecutado Satisfactoriamente", "data"=>$results,"data2"=>$results2);
            
        }else{
            $response = array( "code"=>"500","mensaje"=>"No hay resultados pra mostrar", "data"=>"");
        }
            
      
        echo json_encode($response);

    }

?>