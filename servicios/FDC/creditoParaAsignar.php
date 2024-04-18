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
        case 'Credito Para Asignar':
            $consultarCreditoAsignar="SELECT TOP 1 a.id_simulacion FROM simulaciones a LEFT JOIN simulaciones_fdc b ON a.id_simulacion=b.id_simulacion WHERE a.id_unidad_negocio IN (SELECT eun.id_unidad_negocio
            FROM empresa_unegocio_fdc eun 
            JOIN empresa_usuario_fdc eus ON eun.id_empresa=eus.id_empresa 
            WHERE eus.id_usuario='".$params["id_usuario"]."') AND b.estado IN (1,5) AND b.vigente='s' ORDER BY b.fecha_creacion ASC ";

            $queryCreditoAsignar=sqlsrv_query($link, $consultarCreditoAsignar, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryCreditoAsignar)>0)     {
                $resCreditoAsignar=sqlsrv_fetch_array($queryCreditoAsignar);
                $idSimulacion=$resCreditoAsignar["id_simulacion"];
            }else{
                $idSimulacion=0;
            }
            $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente',"datos"=>$idSimulacion);
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