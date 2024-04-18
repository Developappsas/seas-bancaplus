<?php
include ('../../functions.php');
include ('../cors.php');
header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

//var_dump($params); die;
if (isset($params["operacion"])){
    switch ($params["operacion"]){
        case 'Crear Simulaciones Consultas Log':
            $consultar_credito = sqlsrv_query($link, "SELECT * from simulaciones WHERE id_simulacion='".$params["id_simulacion"]."'");
            $respuesta_credito=sqlsrv_fetch_array($consultar_credito, SQLSRV_FETCH_ASSOC);
            $registrar_log=sqlsrv_query($link,"INSERT INTO simulaciones_consultas_log (id_simulacion,fecha,id_usuario,response,origen) 
            VALUES (".$params["id_simulacion"].",CURRENT_TIMESTAMP,".$params["usuario"].",'".json_encode($respuesta_credito)."','".$params["origen"]."')");
            if ($registrar_log){
                $codigo=200;        
                $response = array( 'codigo' => $codigo, 'mensaje' => 'Registro realizado satisfactoriamente');
            }else{
                $codigo=400;        
                $response = array( 'codigo' => $codigo, 'mensaje' => 'Error al crear Registro');
            }
            
        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;   
    }  
}else{
    $codigo=404;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
http_response_code("200");
echo json_encode($response);

?>