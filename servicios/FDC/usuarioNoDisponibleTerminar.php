<?php
include ('../../functions.php');
include ('../cors.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Usuario No Disponible Terminar':
            $id_usuario=$params["id_usuario"];
             if (sqlsrv_query($link, "UPDATE usuarios SET disponible='t' WHERE id_usuario='".$id_usuario."'"))
             {
                $response = array('codigo' => 200, 'mensaje' => 'Procso Ejecutado Satisfactoriamente');   
             }else{
                $response = array('codigo' => 400, 'mensaje' => 'Error al ejecutar proceso');    
             }
            
    

        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;  
            }
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");
?>