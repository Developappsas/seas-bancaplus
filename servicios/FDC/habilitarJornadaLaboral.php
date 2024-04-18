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
        case 'Habilitar Jornada Laboral':
            $jornadaLaboralFDC=$params["jornada_laboral"];

            $consultarJornadaLaboralActual=sqlsrv_query($link, "SELECT * FROM definicion_tipos WHERE id_tipo=5 and id=1");
            $resJornadaLaboralActual=sqlsrv_fetch_array($consultarJornadaLaboralActual);
            
            if ($resJornadaLaboralActual["descripcion"]==$jornadaLaboralFDC){
                $diferente='n';
            }else{
                $actualizarJornadaLaboral=sqlsrv_query($link, "UPDATE definicion_tipos set descripcion='".$jornadaLaboralFDC."' where id_tipo=5 and id=1");
                $diferente='s';
            }
            $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente');
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