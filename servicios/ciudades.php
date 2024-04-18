<?php    
    include_once ('../functions.php');
    header("Content-Type: application/json; charset=utf-8");  
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
    
    $link = conectar_utf();
    consultarCiudades();


    function consultarCiudades() {
        global $link;        
        $response = array();
        $data = array();

        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        $explode=explode(" ",$token);        
       
        $opciones = array(
            'http'=>array(
            'header'=> "Servicio: GetCities"."\r\n". "Authorization: Bearer ".$explode[1]."\r\n" )
        );

        $contexto = stream_context_create($opciones);

        $json_Input = file_get_contents('https://seas-pruebas-v1.azurewebsites.net/servicios/validador.php', false, $contexto);

        $parametros=json_decode($json_Input);
        
        if ($parametros->code==200)
        {
            $mensaje = '';

            $query="SELECT * FROM ciudades ";
            $responseQuery = sqlsrv_query($link,$query);
                 
            if ( !$responseQuery ) {
                
                header("HTTP/2.0 200 OK");
                $response = array( "code"=>"504","mensaje"=>"Error al ejecutar consulta. Err ".sqlsrv_error($link), "data"=>$response );
                echo json_encode($response);    
                exit;
            }
    
            
            while ($fetchResponse = sqlsrv_fetch_array($responseQuery, SQLSRV_FETCH_ASSOC))
            {
                
    
                array_push($data, array("id"=>$fetchResponse["id"], 'value' => ($fetchResponse["departamento"]." - ".$fetchResponse["municipio"])));
            }
                header("HTTP/2.0 200 OK");
                
                //Generar cadena aleatoria.
                $token = openssl_random_pseudo_bytes(16);
                //Convertir el binario a data hexadecimal.
                $token = bin2hex($token);
                header('Authorization:'.$token);
                $response = array( "code"=>"200","mensaje"=>"Ejecutado Satisfactoriamente", "data"=>$data );
            
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"500","mensaje"=>"Token Invalido","data"=>null );
        }
        echo json_encode($response);

    }
?>