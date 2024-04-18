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
    
    $user = addslashes($_POST['user']);
    $password = addslashes($_POST['password']);
    
    login($user, $password);

    function login($user, $password){

        //$headers = apache_request_headers();

        //echo json_encode($headers);
        //exit;

        global $link;
        $variable=json_encode($_REQUEST); 

        $actualizarRegistro="UPDATE proveedores SET respuesta_login='".$variable."' WHERE id_proveedor=2";
        $queryRegistro=sqlsrv_query($link,$actualizarRegistro);
        $val1=0;
        $val2=0;
        $mensaje="";
        if ($user == null) 
        {
            $val1=1;
            $mensaje.="Debe Ingresar Usuario. ";
        }else{
            $val1=0;
        }

        if ($password == null) 
        {
            $val2=1;
            $mensaje.="Debe Ingresar Contraseña.";
        }else{
            $val2=0;
        }


        if ($val1 == 1 || $val2 == 1)
        {
            header("HTTP/2.0 200 OK");
            $data =array("code"=>"403", "token"=>null);
           
            
        }else{
           

            
            $consultarUsuario = ("SELECT * FROM proveedores where estado=1 and usr = '".$user."' and passwd = '".md5($password)."'");

            $queryUsuario = sqlsrv_query($link,$consultarUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if ( sqlsrv_num_rows($queryUsuario) > 0) 
            {
                
                
                
                $response = sqlsrv_fetch_array($queryUsuario, SQLSRV_FETCH_ASSOC);

                $consultarTokenExistente=("SELECT * FROM token_proveedores WHERE id_proveedor='".$response["id_proveedor"]."' and estado=0");
                $queryTokenExistente=sqlsrv_query($link,$consultarTokenExistente, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($queryTokenExistente)> 0) 
                {
                    $resTokenExistente=sqlsrv_fetch_array($queryTokenExistente, SQLSRV_FETCH_ASSOC);
                    header("HTTP/2.0 200 OK");
                    header('Authorization:'.$resTokenExistente["token"]);
                    $data = array( "code"=>'200', "token"=>$resTokenExistente["token"]);  
                }
                else
                {
                    //echo json_encode($response);
                    //Generar cadena aleatoria.
                    $token = openssl_random_pseudo_bytes(64);
                    //Convertir el binario a data hexadecimal.
                    $token = bin2hex($token);
                    header('Authorization:'.$token);
                    

                    $query = ("INSERT INTO token_proveedores (id_proveedor, token) VALUES ('".$response["id_proveedor"]."', '$token')");
                    $ejecucion = sqlsrv_query($link,$query);
                    if ($ejecucion) {                       
                        header("HTTP/2.0 200 OK");
                        $data = array( "code"=>'200', "token"=>$token);
                        
                    }else{
                        header("HTTP/2.0 200 OK");
                        $data = array( "code"=>'504',"token"=>"");
                        
                    }
                }
                
            }else{
                header("HTTP/2.0 200 OK");
                $data = array("code"=>"500", "token" =>null);
                
            }
        }

        echo json_encode($data);
    }
?>
