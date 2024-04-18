<?php 
    include_once ('../functions.php');
    include ('../controles/FDC.php');
    $link = conectar_utf();
    header("Content-Type: application/json; charset=utf-8");  
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    
    $json = file_get_contents('php://input',true);

    if($json){

        $data=json_decode($json);
        $md5valor="";
        $identificacionCliente="";
        $fecha_pagare="";
        $id_pagare="";
        $idSimulacion="";        
        $formato_digital=0;
        $idFormato = 0;
        $sub_estado_trx = "";
        $estado_trx = "";
        $mensaje_pagare_girador = "";
        $mensaje_firma_pagare = '';               
        $mensaje_crear_pagare = '';
        $fecha_pagare = '';
        $id_trx = 0;

        if(is_array($data)){
            foreach ($data as $valor) {
                if(isset($valor->id)){
                    if ($valor->id=="MD5TRX"){
                        $md5valor=$valor->value;
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="NI2"){
                        $identificacionCliente=$valor->value;            
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="CP_fechaGrabacionPagare"){
                        $fecha_pagare=$valor->value;
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="CP_idPagareDeceval"){
                        $id_pagare=$valor->value;
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="NCFOD2"){
                        $idFormato=$valor->value;   
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="NSL1"){
                        $idSimulacion=$valor->value;   
                    }
                }

                if(isset($valor->id)){                
                    if ($valor->id=="CPC_mensajeRespuesta"){
                        $mensaje_crear_pagare=$valor->value;
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="CGD_mensajeRespuesta"){
                        $mensaje_pagare_girador=$valor->value;
                    }
                }

                if(isset($valor->id)){
                    if ($valor->id=="FCC_mensaje"){
                        $mensaje_firma_pagare = $valor->value;
                        if ($valor->value=="SDL.SE.0000: Exitoso."){
                            $formato_digital=1;
                        }
                    }
                }

                if (isset($valor->id)) {
                    if ($valor->id == "SETRX1") {
                        $sub_estado_trx = $valor->value;
                    }
                }

                if (isset($valor->id)) {
                    if ($valor->id == "TRA_STATE_NAME") {
                        $estado_trx = $valor->value;
                    }
                }

                if (isset($valor->id)) {
                    if ($valor->id == "TRA_ID") {
                        $id_trx = $valor->value;
                    }
                }                
            }

            $actualizarSimulaciones=sqlsrv_query($link,"UPDATE simulaciones SET formato_digital='".$formato_digital."' WHERE id_simulacion='".$idSimulacion."'");

            if($id_pagare != ''){
                $en_progreso = ", en_progreso = 0";
            }else{
                $en_progreso = "";
            }

            $guardarRespuestaExperian = "UPDATE formulario_digital SET fecha_recepcion = GETDATE(), sub_estado_trx = '".$sub_estado_trx."', observacion_firma_pagare = '".$mensaje_firma_pagare."', observacion_crear_girador = '".$mensaje_pagare_girador."', observacion_crear_pagare = '".$mensaje_crear_pagare."', firma_experian = '".$md5valor."', pagare_deceval = '".$id_pagare."', respuesta_push = '".$json."', fecha_pagare_deceval = '".$fecha_pagare."' ".$en_progreso." WHERE id_simulacion = '".$idSimulacion."'";

            $conSubEstadoSimul=sqlsrv_query($link, "SELECT id_subestado, estado FROM simulaciones WHERE id_simulacion = ".$idSimulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            $id_subestado = 0;
            if($conSubEstadoSimul && sqlsrv_num_rows($conSubEstadoSimul) > 0){
                $infoSubestado = sqlsrv_fetch_array($conSubEstadoSimul, SQLSRV_FETCH_ASSOC);
                $id_subestado = $infoSubestado["id_subestado"];
            }
            
            if(($id_subestado == 3 || $id_subestado == 77) && $estadoSeas == 'EST'){ $response = array( "code"=>"200","mensaje"=>"Informacion almacenada satisfactoriamente 1.", "query"=>$guardarRespuestaExperian  );
                if($formato_digital == 1){
                    if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 72 WHERE id_simulacion  = '".$idSimulacion."'")){
                        sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$idSimulacion."', 72, 'system', GETDATE())");//3.2 VALIDACION DE IDENTIDAD Y GARANTIAS
                    }

                    asignarUsuarioFirmaGarantias($idSimulacion);

                    /*$querySimulacion=sqlsrv_query($link, "select * from simulaciones where id_simulacion='".$idSimulacion."'");
                    $resSimulacion=sqlsrv_fetch_array($querySimulacion, SQLSRV_FETCH_ASSOC);

                    $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$idSimulacion."' and estado<>100";
                    $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc);
                    if (sqlsrv_num_rows($querySimulacionesFdc)>0){
                        $consultarUltimoAnalistaEstudio=sqlsrv_query($link, "SELECT case when id_usuario_asignacion is null then 0 when id_usuario_asignacion = 197 then 0 else id_usuario_asignacion end as id_usuario_asignacion FROM simulaciones_fdc WHERE id_simulacion = '".$idSimulacion."' and estado=2 order by id desc limit 1");
                        $resUltimoAnalistaEstudio=sqlsrv_fetch_array($consultarUltimoAnalistaEstudio, SQLSRV_FETCH_ASSOC);

                        sqlsrv_query($link, "START TRANSACTION");
                        $consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");

                        $resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral, SQLSRV_FETCH_ASSOC);
                        $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$idSimulacion."'");
                        $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($idSimulacion,0,197,current_timestamp(),'s',5,".$resSimulacion["id_subestado"].",2)");

                        if ($resJornadaLaboral["descripcion"]=="s"){
                            $consultarEstadoUsuarioNuevo=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalistaEstudio["id_usuario_asignacion"]."' and disponible <> ('n')");
                            if (sqlsrv_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalistaEstudio["id_usuario_asignacion"]<>0){
                                $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarEstadoUsuarioNuevo, SQLSRV_FETCH_ASSOC);

                                $consultarLimiteCreditosUsuario=sqlsrv_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
                                FROM
                                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos
                                FROM usuarios a 
                                LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
                                WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') a,
                                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos
                                FROM usuarios a LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND DATE_FORMAT(fecha_creacion,'%Y-%m-%d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos");
                                if(sqlsrv_num_rows($consultarLimiteCreditosUsuario)>0){
                                    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$idSimulacion."'");
                                    $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$idSimulacion.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp(),'s',2,3)");
                                    $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$idSimulacion."'");
                                    
                                    if ($resEstadoUsuarioNuevo["estado"]=="s" || $resEstadoUsuarioNuevo["estado"]=="g"){
                                        //$actualizarUsuario=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."'");
                                    }

                                    $consultarEstadoUsuario=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resSimulacion["id_analista_riesgo_operativo"]."' and vigente='s' and estado='2' and id_simulacion<>'".$idSimulacion."'");
                                    if (sqlsrv_num_rows($consultarEstadoUsuario)>0){
                                        //$actualizarUsuario2=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resSimulacion["id_analista_riesgo_operativo"]."'");                             
                                    }
                                }else{
                                    $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo = null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$idSimulacion."'");       
                                }
                            }else{
                                $idUsuarioAsignar = usuarioParaAsignar($idSimulacion);
                                if ($idUsuarioAsignar<>0){
                                    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$idSimulacion."'");
                                    $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($idSimulacion,$idUsuarioAsignar,197,current_timestamp(),'s',2,4)");
                                    $actualizarUsuario=sqlsrv_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
                                    $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."' WHERE id_simulacion='".$idSimulacion."'");
                                }
                            }
                        }else{
                            $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$idSimulacion."'");       
                        }
                        sqlsrv_query($link, "COMMIT");
                    }else{
                        $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$idSimulacion."'");
                        $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($idSimulacion, 0, 197, current_timestamp(), 's', 1 , 1)");
                    }*/
                }else{
                    if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 77 WHERE id_simulacion  = '".$idSimulacion."'")){
                        sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$idSimulacion."', 77, 'system', GETDATE())");//3.1 PDTE FIRMA FISICA O COLMENA
                    }
                }
            }

            header("HTTP/2.0 200 Servicio OK");
            if (sqlsrv_query($link, $guardarRespuestaExperian)){
                $response = array( "code"=>"200","mensaje"=>"Informacion almacenada satisfactoriamente 1.", "query"=>$guardarRespuestaExperian  );
            }else{
                $response = array( "code"=>"500","mensaje"=>"Error al comsumir servicio.","error"=>sqlsrv_error($link), "query"=>$guardarRespuestaExperian );
            }
        }else{
            header("HTTP/2.0 200 OK");
            $response = array( "code"=>"404","mensaje"=>"No Se Obtuvo Respuesta del servidor de Escala");
        }
    }else{
        header("HTTP/2.0 200 OK");
        $response = array( "code"=>"404","mensaje"=>"No Se Obtuvo Respuesta del servidor de Escala");
    }

    

    echo json_encode($response);
?>