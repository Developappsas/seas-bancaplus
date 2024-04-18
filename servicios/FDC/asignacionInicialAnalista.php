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
        case 'Asignar Analista Inicial':
            $id_simulacion=$params["id_simulacion"];
         
            
                if ($id_simulacion<>0){
                    $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");

                    $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral, SQLSRV_FETCH_ASSOC);
                    if ($resJornadaLaboral["descripcion"]=="s"){
                        $opciones = array(
                            'http'=>array(
                                'method' => 'POST',
                                'header'  => 'Content-Type: application/json',
                                'content' => json_encode(array("id_simulacion"=>$id_simulacion,"operacion"=>"Determinar Usuario Asignar"))
                                        
                            )
                        );
                            
                        $contexto = stream_context_create($opciones);
                        
                        $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/determinarUsuarioAsignar.php', false, $contexto);
                        $datosUsuarioAsignar=json_decode($json_Input,true);
                        $idUsuarioAsignar = $datosUsuarioAsignar["datos"];
                    $response = array('codigo' => 200, 'mensaje' => 'Procso Ejecutado Satisfactoriamente');   
                }else{
                    $response = array('codigo' => 200, 'mensaje' => 'Procso Ejecutado Satisfactoriamente');   
                }
            }
            else{
                $response = array('codigo' => 200, 'mensaje' => 'Procso Ejecutado Satisfactoriamente');   
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