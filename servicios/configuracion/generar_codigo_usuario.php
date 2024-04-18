<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ('../../functions.php');
include ('../../cors.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if(isset($params["id_usuario"])){
    
    $id_usuario=$params["id_usuario"];
    $documento=$params["documento"];

    $headers = apache_request_headers();

    $codigoGenerado = false;
    $intentos = 0;
    $key = '';

    while (!$codigoGenerado && $intentos < 3) {
        $pattern = '1234567890';
        $max = strlen($pattern)-1;
        
        for($i=0;$i < 6;$i++){
            $key .= $pattern[mt_rand(0,$max)];
        }
        
        if($key != ''){
            if(sqlsrv_query($link, "UPDATE usuarios SET codigo_usuario = '$key' WHERE id_usuario = '$id_usuario'")){
                $codigoGenerado = true;
            }else{
                $intentos++;
            }
        }else{
            $intentos++;
        }
    }

    if($codigoGenerado){
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"200","mensaje"=>"Proceso Exitoso", 'data' => $key);
    }else{
        if($intentos>0){
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"300","mensaje"=>"Error, Codigo no disponible vuelva a intentarlo en un momento.");
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"500","mensaje"=>"Error, No se pudo Crear .");
        }
    }
}else{
    header("HTTP/2.0 200 OK");
    $response = array( "code"=>"404","mensaje"=>"Datos no encontrados");
}

echo json_encode($response);
?>