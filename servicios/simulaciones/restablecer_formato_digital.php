<?php
include ('../../functions.php');

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_encode($json_Input, true);

var_dump($params); die;

switch ($params["operacion"]) {
    case 'Reestablecer Fomato Digital':
        
        if (isset($params["id_simulacion"])){
            
            $actualizarSimulaciones="UPDATE simulaciones SET formato_digital=0,id_subestado=3 WHERE id_simulacion='".$params["id_simulacion"]."'";
            
            if (sqlsrv_query($link,$actualizarSimulaciones)){
                
                $crearSubestado="INSERT INTO simulaciones_subestados (id_simulacion,id_subestado,usuario_creacion,fecha_creacion) VALUES ('".
                $params["id_simulacion"]."',3,'".$_SESSION["S_LOGIN"]."',CURRENT_TIMESTAMP)";
                sqlsrv_query($link,$crearSubestado);

                $crearObservacion="INSERT INTO simulaciones_observaciones (id_simulacion,observacion,usuario_creacion,fecha_creacion) VALUES ('".$params["id_simulacion"]."','EL CREDITO HA SIDO RESTABLECIDO PARA SOLICITAR FIRMA DIGITAL, POR VALIDACION DE DOCUMENTACION DIGITAL','".$_SESSION["S_LOGIN"]."',CURRENT_TIMESTAMP)";
                sqlsrv_query($link,$crearObservacion);

                $eliminarFormatoDigital="DELETE FROM formulario_digital WHERE id_simulacion='".$params["id_simulacion"]."'";
                sqlsrv_query($link,$eliminarFormatoDigital);
                $token = openssl_random_pseudo_bytes(64);
                            
                $token = bin2hex($token);
                $crearFormatoDigital="INSERT INTO formulario_digital (id_simulacion,estado_token,token,vigente, en_progreso) values (".$params["id_simulacion"].",0,'".$token."','s', 0)";
                sqlsrv_query($link,$crearFormatoDigital);

                $codigo=200;        
                $response = array('operacion' => 'Se ha realizado proceso satisfactoriamente', 'codigo' => $codigo, 'mensaje' => 'Se ha realizado proceso satisfactoriamente');
            }else{
                $codigo=404;        
                $response = array('operacion' => 'Error al actualizar credito', 'codigo' => $codigo, 'mensaje' => 'Error al actualizar credito');
            }
        }else{
            $codigo=404;        
            $response = array('operacion' => 'No se han recibido datos necesarios', 'codigo' => $codigo, 'mensaje' => 'No se han recibido datos necesarios');
        }
    break;
              
    default:
        $codigo=404;        
        $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;   
}  

echo json_encode($response);
http_response_code("200");
?>