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
            consultarClientesReporteSeguro();
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


    function consultarClientesReporteSeguro()
    {
        global $link;
        $response = array();
        $data = array();
        $consultarClientes="SELECT si.cedula, CASE WHEN si.fecha_nacimiento IS NULL THEN 'n' ELSE TIMESTAMPDIFF(YEAR,si.fecha_nacimiento,GETDATE()) END AS edad,
        si.valor_credito, si.id_simulacion,
        si.nro_libranza
        FROM simulaciones si 
        INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
        LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
        WHERE (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = 'VIABLE' AND si.id_subestado IN (14))) AND si.fecha_nacimiento IS NOT NULL 
        AND TIMESTAMPDIFF(YEAR,si.fecha_nacimiento,GETDATE())>60 AND si.valor_credito>50000000 AND si.reportado_colmena=0";
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
            "lbr"=>$resClientes["nro_libranza"]);
         
            
            
        }
        echo json_encode($data);    
        header("HTTP/2.0 200 OK");
        
        
    }
?>