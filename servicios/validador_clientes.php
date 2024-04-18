<?php
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    include_once ('../functions.php');
    header("Content-Type: application/json; charset=utf-8");
    $link = conectar_utf();
 
    validador();

    function validador(){
        global $link;
        $headers = apache_request_headers();
        $token1 = $headers['Authorization'];
        $servicio = $headers['Servicio'];
        $explode=explode(" ",$token1);
        $token=$explode[1];
        
        
        $query_token = ("SELECT * FROM token_clientes WHERE estado = 0 AND token = '$token'");
        $ejecucion = sqlsrv_query($link,$query_token, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if ( sqlsrv_num_rows($ejecucion) > 0) 
        {
            # code...
            
            $query_update = ("UPDATE token_clientes SET estado = 1 WHERE token = '$token'");
            $ejecucion = sqlsrv_query($link,$query_update);    
            if ($ejecucion) {
                
                /*
                HACE FALTA ALMACENAR LO QUE ENVIA EXPERIAN, ESTAMOS EN ESPERA DE REUNION CON EL EQUIPO TECNICO PARA COMPLETAR EL PROCESO DE ALMACENAMIENTO DE RESPUESTA
                */
                
                $data = array( "code" => '200', "message" => 'Informacion almacenada correctamente.' );
            }else{
                $data = array( "code" => '500', "message" => 'En este momento el servicio no puede procesar la informacion, por favor reintente.' );
            }
        }else{
            $data = array( "code" => '504', "message" => 'El codigo enviado no esta disponible o ya fue consumido.', "query_token"=>$query_token );
        }

        header("HTTP/2.0 200 Servicio OK");
        echo json_encode($data);
    }
?>
