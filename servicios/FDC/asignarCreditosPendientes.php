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
        case 'Asignar Creditos Pendientes':
    



      
        if ($params["jornada_laboral"]=="s"){
            $usuariosDeshabilitar=$params["analistas"];
            $usuariosDeshabilitarDecode=json_decode($usuariosDeshabilitar);
        
        if (count($usuariosDeshabilitarDecode)>0){

            foreach ($usuariosDeshabilitarDecode as $usuariosDeshabilitarEach) {      
                if ($params["id_empresa"]<>"ANTIFRAUDE")
                {
                    $consultarUsuarioEmpresa="SELECT id FROM empresa_usuario_fdc WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."'";
                    $consultarNuevaUsuarioEmpresa=$consultarUsuarioEmpresa."  and id_empresa='".$usuariosDeshabilitarEach->id_empresa."'";
                    $queryNuevaUsuarioEmpresa=sqlsrv_query($link,$consultarNuevaUsuarioEmpresa, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    if (sqlsrv_num_rows($queryNuevaUsuarioEmpresa)==0)
                    {
                        $consultarOtrasEmpresas=$consultarUsuarioEmpresa." and id_empresa<>'".$usuariosDeshabilitarEach->id_empresa."'";
                        $queryOtraUsuarioEmpresa=sqlsrv_query($link,$consultarOtrasEmpresas, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        if (sqlsrv_num_rows($queryOtraUsuarioEmpresa)>0)
                        {
                            sqlsrv_query($link,"DELETE FROM empresa_usuario_fdc WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."' and id_empresa<>'".$usuariosDeshabilitarEach->id_empresa."'");
                        }

                        sqlsrv_query($link,"INSERT INTO empresa_usuario_fdc (id_usuario,id_empresa) VALUES ('".$usuariosDeshabilitarEach->id_analista."','".$usuariosDeshabilitarEach->id_empresa."')");

                    }
  
                }
                

                $consultaActualizarUsuarios=sqlsrv_query($link, "UPDATE usuarios SET disponible='".$usuariosDeshabilitarEach->estado."',cantidad_creditos='".$usuariosDeshabilitarEach->cantidad_creditos."' WHERE id_usuario='".$usuariosDeshabilitarEach->id_analista."'");
            }
        }


            if ($params["id_empresa"]<>"ANTIFRAUDE")
            {
                $consultarCreditosReprocesos=sqlsrv_query($link, "SELECT * 
                FROM simulaciones_fdc a 
                INNER JOIN simulaciones b ON a.id_simulacion=b.id_simulacion 
                INNER JOIN empresa_unegocio_fdc c ON c.id_unidad_negocio=b.id_unidad_negocio
                WHERE a.vigente='s' AND a.estado=5  AND c.id_empresa='".$params["id_empresa"]."' and (b.id_subestado is null or b.id_subestado not in (".implode(",", $subestados_validar_grantias)."))", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
                if (sqlsrv_num_rows($consultarCreditosReprocesos)>0){
    
                    while ($resCreditosReprocesos=sqlsrv_fetch_array($consultarCreditosReprocesos)){
                        $idUsuarioAsignar=0;
                        $consultarUltimoAnalista=sqlsrv_query($link, "SELECT id,id_usuario_asignacion FROM simulaciones_fdc where estado=2 and id<'".$resCreditosReprocesos["id"]."' and id_simulacion='".$resCreditosReprocesos["id_simulacion"]."' ORDER BY id DESC LIMIT 1");
                        $resUltimoAnalista=sqlsrv_fetch_array($consultarUltimoAnalista);
                        $consultarEstadoUltimoUsuarioAsignado=sqlsrv_query($link, "SELECT * FROM usuarios WHERE disponible in ('s','g') and estado=1 and id_usuario='".$resUltimoAnalista["id_usuario_asignacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
                        if (sqlsrv_num_rows($consultarEstadoUltimoUsuarioAsignado)>0){
    
                            $consultarEstadoUsuarioNuevo=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalista["id_usuario_asignacion"]."' and disponible <> ('n')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
                            if (sqlsrv_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalista["id_usuario_asignacion"]<>0){
                                $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarEstadoUsuarioNuevo, SQLSRV_FETCH_ASSOC);
    
                                $consultarLimiteCreditosUsuario=sqlsrv_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos FROM (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') a, (SELECT a.`id_usuario`,a.`nombre`,a.`apellido`,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'yyyy-MM-dd')=format(GETDATE(), 'yyyy-MM-dd') b ON a.id_usuario=b.id_usuario_creacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    
                                if(sqlsrv_num_rows($consultarLimiteCreditosUsuario)>0){
                                    $idUsuarioAsignar=$resUltimoAnalista["id_usuario_asignacion"];
                                }

                                if ($idUsuarioAsignar<>0){
                                    $cambiarEstadoSimulacionFDC="UPDATE simulaciones_fdc set vigente='n' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
                                    sqlsrv_query($link, $cambiarEstadoSimulacionFDC);
                                    $asignarAnalista="INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,id_usuario_asignacion,fecha_creacion,vigente,estado,val) values ('".$resCreditosReprocesos["id_simulacion"]."',197,'".$idUsuarioAsignar."',CURRENT_TIMESTAMP(),'s',2,14)";
                                    sqlsrv_query($link, $asignarAnalista);
                                    $actualizarSimulacion="UPDATE simulaciones set id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."',id_analista_gestion_comercial='".$idUsuarioAsignar."' where id_simulacion='".$resCreditosReprocesos["id_simulacion"]."'";
                                    sqlsrv_query($link, $actualizarSimulacion);
                                    $actualizarEstadoUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
                          
                                } 
                            }          
                        }else{
                            //PROCESO 1 terminar
                            $opciones = array(
                                'http'=>array(
                                    'method' => 'POST',
                                    'header'  => 'Content-Type: application/json',
                                    'content' => json_encode(array("id_simulacion"=>$resCreditosReprocesos["id_simulacion"],"operacion"=>"Determinar Usuario Asignar"))
                                            
                                )
                            );
                                
                            $contexto = stream_context_create($opciones);
                            
                            $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/determinarUsuarioAsignar.php', false, $contexto);
                            $datosUsuarioAsignar=json_decode($json_Input,true);
                            $idUsuarioAsignar = $datosUsuarioAsignar["datos"];

                        }
    
                            
                    }
                }
    
                $consultarCreditosSinAsignar=sqlsrv_query($link, "SELECT * 
                FROM simulaciones_fdc a 
                INNER JOIN simulaciones b ON a.id_simulacion=b.id_simulacion 
                INNER JOIN empresa_unegocio_fdc c ON c.id_unidad_negocio=b.id_unidad_negocio
                WHERE a.vigente='s' AND a.estado=1 AND c.id_empresa='".$params["id_empresa"]."'  and (b.id_subestado is null or b.id_subestado not in (".implode(",", $subestados_validar_grantias).")) ORDER BY b.fecha_radicado ASC");
                if (sqlsrv_num_rows($consultarCreditosSinAsignar)>0){ 
                    while ($resCreditosSinAsignar=sqlsrv_fetch_array($consultarCreditosSinAsignar)){
                        $idUsuarioAsignar=0;
                        $opciones = array(
                            'http'=>array(
                                'method' => 'POST',
                                'header'  => 'Content-Type: application/json',
                                'content' => json_encode(array("id_simulacion"=>$resCreditosSinAsignar["id_simulacion"],"operacion"=>"Determinar Usuario Asignar"))
                                        
                            )
                        );
                            
                        $contexto = stream_context_create($opciones);
                        
                        $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/determinarUsuarioAsignar.php', false, $contexto);
                        $datosUsuarioAsignar=json_decode($json_Input,true);
                        $idUsuarioAsignar = $datosUsuarioAsignar["datos"];

                 
                    }
                }
            }
            else{
                $consultarCreditosAntifraudes=sqlsrv_query($link, "SELECT b.* 
                FROM simulaciones_fdc a 
                INNER JOIN simulaciones b ON a.id_simulacion=b.id_simulacion 
                WHERE a.vigente='s' AND a.estado in (1,2,5,3) and b.id_subestado in (".implode(",", $subestados_validar_grantias).")");
                while ($resCreditosAntifraudes=sqlsrv_fetch_array($consultarCreditosAntifraudes))
                {
                    $opciones = array(
                        'http'=>array(
                            'method' => 'POST',
                            'header'  => 'Content-Type: application/json',
                            'content' => json_encode(array("id_simulacion"=>$resCreditosAntifraudes["id_simulacion"],"operacion"=>"Asignar Usuario Firma Garantias"))
                                    
                        )
                    );
                        
                    $contexto = stream_context_create($opciones);
                    
                    $json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/asignarUsuarioFirmaGarantias.php', false, $contexto);
                }
                
            }
         
            $mensaje="Proceso ejecutado Satisfactoriamente";
            
        }else{
            $mensaje="Jornada laboral inactiva";
        }
        
            $response = array('codigo' => 200, 'mensaje' => $mensaje);  

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