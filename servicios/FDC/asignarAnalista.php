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

if(isset($params["operacion"])){
    switch ($params["operacion"]){
        case 'Asignar Analista':

            $id_simulacion=$params["id_simulacion"];
            $id_analista=$params["id_analista"];

            if ($id_analista<>0){
                $consultarAnalistaActual="SELECT estado,case when id_usuario_asignacion is null then '' else id_usuario_asignacion end as id_analista_riesgo_operativo FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and vigente='s'";


                $queryAnalistaActual=sqlsrv_query($link, $consultarAnalistaActual, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                $cantidadAnalistaActual=sqlsrv_num_rows($queryAnalistaActual);
                $resAnalistaActual=sqlsrv_fetch_array($queryAnalistaActual);
                if ($cantidadAnalistaActual>0){
                    if ($resAnalistaActual["estado"]==1 || $resAnalistaActual["estado"]==5 || $resAnalistaActual["estado"]==2){
    
                    
    
                            if ($resAnalistaActual["id_analista_riesgo_operativo"]<>$id_analista && $id_analista<>''){
                                $actualizarSimulaciones=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo = ".$id_analista.",id_analista_riesgo_crediticio = ".$id_analista." WHERE id_simulacion = '".$id_simulacion."'");
                                $actualizarSimulacionesFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente = 'n' WHERE id_simulacion = '".$id_simulacion."'");
                                $insertSimulacionesFDCNA2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ('".$id_simulacion."','".$id_analista."','1973',CURRENT_TIMESTAMP,'s',2,12)");
                                
                                $updateUsuarioActual=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$id_analista."'");
                                
                                $response = array('codigo' => 200, 'mensaje' => 'Credito Asignado Satisfactoriamente');    
                            }else{
                                $response = array('codigo' => 400, 'mensaje' => 'Error al asignar credito');    
                            }
                    
                    }else{
                        $response = array('codigo' => 400, 'mensaje' => 'Este credito no se encuentra en un estado para reasignar');    
                    }
                }else{
                    $response = array('codigo' => 404, 'mensaje' => 'No existe credito a asignar');    
                }
            }else{
                $consultarAsignacionesAnteriores=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc where id_simulacion='".$id_simulacion."' and estado in (1,5)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($consultarAsignacionesAnteriores)>0)
                {

                    $actualizarSimulacionesFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente = 'n' WHERE id_simulacion = '".$id_simulacion."'");
                    $desasignarSimulacion=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion,197,197,CURRENT_TIMESTAMP,'s',1,21);");
                    $consultaActualizarRegistroAsignado2="UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'";
		
                    if (sqlsrv_query($link, $consultaActualizarRegistroAsignado2)){
                        $response = array('codigo' => 200, 'mensaje' => 'Credito Desasignado Satisfactoriamente');  
                    }else{
                        $response = array('codigo' => 400, 'mensaje' => 'Error al desasignar credito');  
                    }
                  
                }else{
                    $response = array('codigo' => 400, 'mensaje' => 'Este credito no ha sido asignado anteriormente');  
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