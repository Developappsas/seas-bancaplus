<?php    
    include_once ('../functions.php');
    header("Content-Type: application/json; charset=utf-8");    
    //Mostrar errores
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
       
    $link = conectar_utf();
    
    $user = addslashes($_POST['usuario']);
    $password = addslashes($_POST['clave']);
    
    login($user, $password);

    function login($user, $password){

        //$headers = apache_request_headers();

        //echo json_encode($headers);
        //exit;

        global $link;
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
            $data =array("data"=>"","code"=>"403", "token"=>null, "message"=>"Conexion no valida con el servicio. Err: ".$mensaje);
           
            
        }else{
           

            
            $consultarUsuario = ("SELECT *,CONCAT(a.primer_nombre,' ',a.segundo_nombre,' ',a.primer_apellido,' ',a.segundo_apellido) as nombre_completo_cliente,a.numero_identificacion,a.id FROM clientes a LEFT JOIN log_claves_clientes b on a.id=b.id_cliente where a.numero_identificacion = '".$user."' and b.clave = '".md5($password)."' and b.vigente='s'");

            $queryUsuario = sqlsrv_query($link,$consultarUsuario, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            
            if ( sqlsrv_num_rows($queryUsuario) > 0) 
            {
                $resUsuario=sqlsrv_fetch_array($queryUsuario, SQLSRV_FETCH_ASSOC);
                
                header("HTTP/2.0 200 OK");
                    //echo json_encode($response);
                    //Generar cadena aleatoria.
                    $token = openssl_random_pseudo_bytes(64);
                    //Convertir el binario a data hexadecimal.
                    $token = bin2hex($token);
                    header('Authorization:'.$token);
                    $datos_cliente=array("nombre"=>$resUsuario["nombre_completo_cliente"],"numero_identificacion"=>$resUsuario["numero_identificacion"],"id_usuario"=>$resUsuario["id"]);

                    $query = ("INSERT INTO token_clientes (id_cliente, token,origen,estado,fecha_creacion) VALUES ('".$resUsuario["id"]."', '$token','w',0,current_timestamp)");
                    $ejecucion = sqlsrv_query($link,$query);
                    if ($ejecucion) {                       
                        header("HTTP/2.0 200 OK");
                        $data = array( "code"=>'200', "token"=>$token, "message"=> "Login correcto","data"=>$datos_cliente);    
                    }else{
                        header("HTTP/2.0 200 OK");
                        $data = array( "code"=>'504', "message"=>"El servidor no pudo generar un clave de acceso unico, por favor reintente","token"=>$query);
                        
                    }


                    

                  
                
                
            }else{
                header("HTTP/2.0 200 OK");
                $data = array("data"=>"","code"=>"500", "message"=>"Servicio no disponible para este usuario. Error ".sqlsrv_error($link)."--".$consultarUsuario, "token" =>null);
                
            }
        }

        echo json_encode($data);
    }
?>
