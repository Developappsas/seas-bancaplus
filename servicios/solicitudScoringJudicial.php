<?php    
    include_once ('../functions.php');
    header("Content-Type: application/json; charset=utf-8");  
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    $link = conectar_utf();
    $action=$_GET["Action"];
    $id_simulacion=$_GET["id_simulacion"];
    switch ($action)
    {
        case "CONSULTAR":
            consultarSolicitudScoringJudicial();
            break;
        case "MODIFICAR":
            ModificarClientesReporteSeguro($id_simulacion);
            break;
    }
    function ModificarClientesReporteSeguro($id_simulacion)
    {
        global $link;
        $actualizarDescargaReporteSeguro="UPDATE simulaciones SET reportado_colmena='1' WHERE id_simulacion='".$id_simulacion."'";
        if (sqlsrv_query($link,$actualizarDescargaReporteSeguro))
        {
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"200","mensaje"=>"Proceso OK ");
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"400","mensaje"=>"Error ");
        }
        echo json_encode($response);   
    }


    function consultarSolicitudScoringJudicial()
    {
        
        global $link;
        $response = array();
        $data = array();
        $consultarClientes="SELECT si.cedula, si.id_simulacion,ci.departamento,so.nombre1,so.nombre2,so.apellido1,so.apellido2
        FROM simulaciones si 
        INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
        LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
        LEFT JOIN ciudades ci ON ci.cod_municipio=so.ciudad
        WHERE si.id_simulacion in (552610,552160,551698)";
        $queryClientes=sqlsrv_query($link,$consultarClientes);
        if ( !$queryClientes ) {
                
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"504","mensaje"=>"Error al ejecutar consulta. Err ".sqlsrv_error($link), "data"=>$response );
            echo json_encode($response);    
            exit;
        }

        while ($resClientes=sqlsrv_fetch_array($queryClientes, SQLSRV_FETCH_ASSOC))
        {
            //$data["data"]= array("id_simulacion"=>$resClientes["id_simulacion"], 'cedula' => $resClientes["cedula"],'lbr'=>$resClientes["nro_libranza"]);
            
            //array_push($data,array("id_simulacion"=>$resClientes["id_simulacion"], 'cedula' => $resClientes["cedula"],'lbr'=>$resClientes["nro_libranza"]));
            $data[]=array("id_simulacion"=>$resClientes["id_simulacion"],
            "cedula"=>$resClientes["cedula"],
            "primer_nombre"=>$resClientes["nombre1"],
            "segundo_nombre"=>$resClientes["nombre2"],
            "primer_apellido"=>$resClientes["apellido1"],
            "segundo_apellido"=>$resClientes["apellido2"],
            "departamento"=>$resClientes["departamento"]);
         
            
            
        }
        echo json_encode($data);    
        header("HTTP/2.0 200 OK");
        
        
    }
?>