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
        case 'Usuario No Disponible Reasignar':
            $id_usuario=$params["id_usuario"];
            $actualizarUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='n' WHERE id_usuario='".$id_usuario."'");
            $consultarAsignacionesUsuario="SELECT * FROM simulaciones_fdc WHERE estado=2 and vigente='s' and id_usuario_asignacion='".$id_usuario."'";
            $queryAsignacionesUsuario=sqlsrv_query($link, $consultarAsignacionesUsuario);
        
            if (sqlsrv_num_rows($queryAsignacionesUsuario)>0){
        
                while ($resAsignacionesUsuario=sqlsrv_fetch_array($queryAsignacionesUsuario)){  
                   
        
                    if ($params["jornada_laboral"]=="s"){
                        if ($params["id_empresa"]<>"ANTIFRAUDE")
                        {   
                            $opciones = array(
                                'http'=>array(
                                    'method' => 'POST',
                                    'header'  => 'Content-Type: application/json',
                                    'content' => json_encode(array("id_simulacion"=>$resAsignacionesUsuario["id_simulacion"],"operacion"=>"Determinar Usuario Asignar"))
                                            
                                )
                            );
                                
                            $contexto = stream_context_create($opciones);
                            
                            $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/determinarUsuarioAsignar.php', false, $contexto);
                            $datosUsuarioAsignar=json_decode($json_Input,true);
                            $idUsuarioAsignar = $datosUsuarioAsignar["datos"];
                        }else{
             
                            $opciones = array(
                                'http'=>array(
                                    'method' => 'POST',
                                    'header'  => 'Content-Type: application/json',
                                    'content' => json_encode(array("id_simulacion"=>$resAsignacionesUsuario["id_simulacion"],"operacion"=>"Asignar Usuario Firma Garantias"))
                                            
                                )
                            );
                                
                            $contexto = stream_context_create($opciones);
                            
                            $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/asignarUsuarioFirmaGarantias.php', false, $contexto);
                            $datosUsuarioAsignar=json_decode($json_Input,true);
                            $idUsuarioAsignar = $datosUsuarioAsignar["datos"];
    
    
            
                          
                        }
                        
                    }else{
                        $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
                        sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
        
                        $cambiarEstadoSimulacionFDC="DELETE FROM simulaciones_fdc WHERE id = '".$resAsignacionesUsuario["id"]."'";
                        sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
                        $consultarMaxSimulacionFdc="SELECT max(id) as id_fdc FROM simulaciones_fdc WHERE id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
                        $queryMaxSimulacionFDC=sqlsrv_query($link, $consultarMaxSimulacionFdc);
                        $resMaxSimulacionFDC=sqlsrv_fetch_array($queryMaxSimulacionFDC);
                        $asignarAnalista="UPDATE simulaciones_fdc SET vigente='s' WHERE id='".$resMaxSimulacionFDC["id_fdc"]."'";
                        sqlsrv_query($link, $asignarAnalista);
                        $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null,id_analista_gestion_comercial=null where id_simulacion='".$resAsignacionesUsuario["id_simulacion"]."'";
                        sqlsrv_query($link, $actualizarSimulacion);
        
                       
                    }

                    $response = array('codigo' => 200, 'mensaje' => 'Procso Ejecutado Satisfactoriamente');   
                }
            }else{
                $response = array('codigo' => 404, 'mensaje' => 'No hay creditos para reasignar');   
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