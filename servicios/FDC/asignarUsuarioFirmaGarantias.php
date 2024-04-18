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
        case 'Asignar Usuario Firma Garantias':
    
            $id_subestado = 72;//3.2 FIRMA DE GARANTIAS
            $id_simulacion=$params["id_simulacion"];
            $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and estado<>100";
            $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

            if (sqlsrv_num_rows($querySimulacionesFdc)>0){

                sqlsrv_query($link, "START TRANSACTION");

                $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion,0,197,current_timestamp,'s',5,".$id_subestado.",72)");

                $consultarUsuarioNuevo=sqlsrv_query($link, "SELECT TOP 1 a.id_usuario, a.nombre, a.cantidad_asignado, b.cantidad_terminado, (a.cantidad_asignado+b.cantidad_terminado) AS cantidad_total
                FROM
                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
                    WHERE a.revision_garantias = 1  and a.subtipo in ('ANALISTA_CREDITO','ANALISTA_VEN_CARTERA') and a.estado = 1 GROUP BY a.id_usuario, a.nombre, a.apellido) a,

                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd')) b 
                    ON a.id_usuario=b.id_usuario_creacion
                    WHERE a.revision_garantias = 1  and  a.subtipo in ('ANALISTA_CREDITO','ANALISTA_VEN_CARTERA') and a.estado = 1 GROUP BY a.id_usuario, a.nombre, a.apellido) b 

                WHERE a.id_usuario=b.id_usuario  ORDER BY (a.cantidad_asignado+b.cantidad_terminado) ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                if (sqlsrv_num_rows($consultarUsuarioNuevo)>0){
                $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarUsuarioNuevo, SQLSRV_FETCH_ASSOC);
                $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp,'s',2,73)");
                $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$id_simulacion."'");

                $consultarEstadoUsuario=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resEstadoUsuarioNuevo["id_usuario"]."' and vigente='s' and estado='2' and id_simulacion<>'".$id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($consultarEstadoUsuario)>0){
                    //$actualizarUsuario2=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$id_analista_riesgo_operativo."'");
                }
                }else{
                $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'");       
                }

                sqlsrv_query($link, "COMMIT");

                $response = array('codigo' => 200, 'mensaje' => 'Proceso exitoso');
            }else{
                $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion, 0, 197, current_timestamp, 's', 1 , 74)");

                $response = array('codigo' => 200, 'mensaje' => 'Proceso exitoso, Sin asignar Analista');
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