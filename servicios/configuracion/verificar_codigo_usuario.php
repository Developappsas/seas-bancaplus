<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../../functions.php');
include ('../cors.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["codigo_usuario"])){

    if($consultaUsuario = sqlsrv_query($link, "SELECT * FROM usuarios WHERE codigo_usuario = '".$params["codigo_usuario"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET))){
        
        if(sqlsrv_num_rows($consultaUsuario) > 0){
            $datos = sqlsrv_fetch_array($consultaUsuario);

            sqlsrv_query($link, "UPDATE usuarios SET codigo_usuario = NULL WHERE id_usuario = '".$datos["id_usuario"]."'");
            header("HTTP/2.0 200 OK");
            $response = array("codigo"=>"200","mensaje"=>"Proceso Exitoso", 'data' => array('id_usuario' => $datos["id_usuario"], 'nombres' => trim($datos["nombre"]), 'apellidos' => trim($datos["apellido"]), 'cedula' => trim($datos["cedula"]), 'celular' => trim($datos["telefono"]), 'correo' => trim($datos["email"])));
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "codigo"=>"300","mensaje"=>"Clave Dinamica no encontrada, Genere una nueva en SEAS y vuelva a intentarlo.");
        }
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "codigo"=>"500","mensaje"=>"Error, No se pudo Validar el codigo");
    }
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "codigo"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);
?>