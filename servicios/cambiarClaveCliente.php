<?php
/**
* This example shows making an SMTP connection with authentication.
*/
//Import the PHPMailer class into the global namespace
include_once ('../functions.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that

$usuario=$_POST["usuario"];
$clave=$_POST["clave"];

cambiar_clave_cliente($usuario,$clave);

function cambiar_clave_cliente($usuario,$clave) 
{
    global $link; 
    
    $consultarClientes="SELECT * FROM clientes where id='".$usuario."'";
    $queryClientes=sqlsrv_query($link,$consultarClientes, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    $val=0;
    $idRegistro="";
    $cadenaGen="";
    if ( sqlsrv_num_rows($queryClientes) > 0) 
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        // Output: 54esmdr0qf
        
        $cadenaGen=substr(str_shuffle($permitted_chars), 0, 10);


        $resClientes=sqlsrv_fetch_array($queryClientes, SQLSRV_FETCH_ASSOC);
        $idRegistro=$resClientes["id"];
        $actualizarClaveCliente="UPDATE log_claves_clientes SET vigente='n' WHERE id_cliente='".$resClientes["id"]."'";
        $queryClaveCliente=sqlsrv_query($link,$actualizarClaveCliente);

        $crearRegistroCliente="INSERT INTO log_claves_clientes (id_cliente,clave,vigente,fecha,id_usuario,origen) VALUES ('".$resClientes["id"]."','".md5($clave)."','s',current_timestamp,197,'w')";
        if (sqlsrv_query($link,$crearRegistroCliente))
        {
           $val=1; 
        }else{
            $val=3; 
        }
     
    }

    if ($val==1)
    {
     
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"500","mensaje"=>"Correo no enviado");
      
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"500","mensaje"=>"Error al generar informacion del cliente. Err ".$val);
    }
    
    echo json_encode($response);
}


?>