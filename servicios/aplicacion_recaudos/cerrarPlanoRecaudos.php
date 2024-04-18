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
        case 'Cerrar Plano Recaudo':
            $consultarPlanoRecaudo="SELECT * FROM recaudosplanos WHERE procesado=0 and id_recaudoplano = '" . $params["id_recaudoplano"] . "'";
            $queryPlanoRecaudo=sqlsrv_query($link,$consultarPlanoRecaudo, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryPlanoRecaudo)>0){
                    $consultaActualizarPlano = "UPDATE recaudosplanos set procesado=1 where id_recaudoplano = '" . $params["id_recaudoplano"] . "'";

                    if (sqlsrv_query($link, $consultaActualizarPlano)){
                        $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente');
                    }else{
                        $response = array('codigo' => 400, 'mensaje' => 'Error al actualizar Plano');
                    }
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

