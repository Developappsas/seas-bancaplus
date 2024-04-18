<?php    
    include_once ('../functions.php');
    
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
       
    $link = conectar_utf();
    
    $user = $_POST['user'];
    $password = $_POST['password'];
    $clientId = $_POST['clientId'];
    
    login($user, $password, $clientId);

    function login($user, $password, $clientId){

        //$headers = apache_request_headers();

        //echo json_encode($headers);
        //exit;

        global $link;
        if ($user != null && $password != null ){

            $consultarUsuario = ("SELECT c.*, b.id_proveedor FROM proveedores a LEFT JOIN proveedores_api b ON a.id_proveedor = b.id_proveedor LEFT JOIN api c ON c.id_api = b.id_api WHERE a.tipo_proveedor = 0 and a.estado = 1 and a.usr = '".$user."' and a.passwd = '".md5($password)."'");

            $queryUsuario = sqlsrv_query($link,$consultarUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if ( sqlsrv_num_rows($queryUsuario) > 0 && $clientId == '15' ) {
                header("Content-Type: application/json; charset=utf-8");
                
                $response = sqlsrv_fetch_array($queryUsuario, SQLSRV_FETCH_ASSOC);
                //echo json_encode($response);
                //Generar cadena aleatoria.
                $token = openssl_random_pseudo_bytes(64);
                //Convertir el binario a data hexadecimal.
                $token = bin2hex($token);
                header('Authorization:'.$token);
                $data = array( "code"=>'200', "token"=>$token);

                $query = ("INSERT INTO token_proveedores (id_proveedor, token) VALUES ('".$response["id_proveedor"]."', '$token')");
                $ejecucion = sqlsrv_query($link,$query);
                if ($ejecucion) {                       
                    header("HTTP/2.0 200 Servicio OK");
                    echo json_encode($data);
                }else{
                    header("HTTP/2.0 200 Servicio OK");
                    echo json_encode(array("code"=>"500", "message"=>"El servidor no pudo generar un clave de acceso unico, por favor reintente"));
                }
            }else{
                header("HTTP/2.0 200 Servicio OK");
                echo json_encode(array("code"=>"404", "message"=>"Servicio no disponible para este usuario", "error" => sqlsrv_error($link), "query" =>$consultarUsuario));
            }
        }else{
            header("HTTP/2.0 200 Servicio OK");
            echo json_encode(array("code"=>"404", "message"=>"Conexion no valida con el servicio"));
        }
    }
?>
