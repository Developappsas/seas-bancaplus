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
        case 'Desasignar Credito':
            $variable=0;
            $idSimulacion=$_POST["id_simulacion"];
            $idUsuario=$_POST["id_analista"];
            $consultarEstadoActualCredito="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$idSimulacion."' and estado=2 and vigente='s'";
            $queryEstadoActualCredito=sqlsrv_query($link,$consultarEstadoActualCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            if (sqlsrv_num_rows($queryEstadoActualCredito)>0)
            {
                $consultaEliminarUltimoRegistroFDC=sqlsrv_query($link, "DELETE FROM simulaciones_fdc where id_simulacion='".$idSimulacion."' and vigente='s'");
                $consultaUltimoRegistroAsignado=sqlsrv_query($link, "SELECT TOP 1 * FROM simulaciones_fdc where id_simulacion='".$idSimulacion."' and estado in (1,5) order by id desc", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        
                if (sqlsrv_num_rows($consultaUltimoRegistroAsignado)>0){
                    $resUltimoRegistroAsignado=sqlsrv_fetch_array($consultaUltimoRegistroAsignado);
                    $consultaActualizarRegistroAsignado="UPDATE simulaciones_fdc SET vigente='s' WHERE id='".$resUltimoRegistroAsignado["id"]."'";
                    $consultaActualizarRegistroAsignado2="UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$idSimulacion."'";
                    $queryActualizarRegistroAsignado2=sqlsrv_query($link, $consultaActualizarRegistroAsignado2);
        
                    if (sqlsrv_query($link, $consultaActualizarRegistroAsignado)){
                        $response = array('codigo' => 200, 'mensaje' => 'Proceso Ejecutado Satisfactoriamente');
                    }else{
                        $response = array('codigo' => 400, 'mensaje' => 'Error al ejecutar proceso');
                    }
                }
            }else{
                $response = array('codigo' => 404, 'mensaje' => 'Credito no se encuentra en estado para desasignar');
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