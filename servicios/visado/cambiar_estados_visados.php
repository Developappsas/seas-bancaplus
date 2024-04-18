<?php

    include_once ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();
    header("Content-Type: application/json; charset=utf-8");
    //var_dump($respuestaValidarToken);
        if (isset($_SERVER['REQUEST_METHOD'])) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if(isset($params->operacion)) {
                        switch ($params->operacion) {
                            case 'Reportar Pagaduria':
                                //$query=("SELECT MAX(libranza) as numero_libranza FROM libranza_simulaciones");
                                if(isset($params->idSimulacion) && isset($params->observacion_Visado)) {
                                    $query3="update simulaciones set visado='2',fecha_visado=current_timestamp where id_simulacion = '".$params->idSimulacion."'";
                                    if (sqlsrv_query($link,$query3)) { 
                                        $query4="INSERT INTO simulaciones_observaciones (id_simulacion,observacion,usuario_creacion,fecha_creacion) VALUES ('".$params->idSimulacion."','ANALISTA: ".$params->nombreUsuario.". CREDITO ENVIADO A PAGADURIA PARA PROCESO DE VISADO. OBSERVACION DE ANALISTA: ".$params->observacion_Visado."','system',current_timestamp);";

                                        $data["idSimulacion"]=$params->idSimulacion;
                                        if (sqlsrv_query($link,$query4)) {
                                            $crearRegistroEstadosVisados="INSERT INTO visados_simulaciones (id_simulacion,estado,observaciones,fecha) VALUES ('".$params->idSimulacion."',2,'ANALISTA: ".$params->nombreUsuario.". CREDITO ENVIADO A PAGADURIA PARA PROCESO DE VISADO. OBSERVACION DE ANALISTA: ".$params->observacion_Visado."',current_timestamp)";
				                            sqlsrv_query($link,$crearRegistroEstadosVisados);
                                            $codigo=200; 
                                            $response = array('codigo' => $codigo, 'mensaje' => 'Simulacion Actualizada Satisfactoriamente','data'=>$params->idSimulacion);
                                        }else{
                                            $codigo=400;
                                            $response = array('codigo' => $codigo, 'mensaje' => 'Error al guardar Observacion de credito','data'=>$data);
                                        }
                                    }else{
                                        $codigo=400;
                                        $response = array('codigo' => $codigo, 'mensaje' => 'Error al modificar simulacion','data'=>'');
                                    }
                                }else{
                                    $codigo=404;
                                    $response = array('operacion'=>'Error de Solicitud', 'codigo'=>$codigo, 'mensaje'=>'No se recibieron los parametros esperados');
                                }
                            break;
                
                            case 'Repuesta Pagaduria':
                                //$query=("SELECT MAX(libranza) as numero_libranza FROM libranza_simulaciones");
                                $envio_notificacion=0;
                                if(isset($params->idSimulacion) && isset($params->respuesta) &&  isset($params->observacion_Visado) &&  isset($params->estadoCredito) && isset($params->usuario_Seas)) {
                                    if ($params->respuesta==1) {
                                        $respuesta="APROBADO";
                                        $respuesta2="3";
                                        $estadosNotificacionVencimiento=array(31,48);
                                        if (in_array($params->estadoCredito,$estadosNotificacionVencimiento))
                                        {
                                            $envio_notificacion=1;
                                        }
                                        
                                        $subestado_siguiente=$params->estadoCredito;
                                        $respuesta2=3;
                                    }else{
                                        if ($params->respuesta==2) {
                                            $respuesta="NEGADO";
                                            $respuesta2=4;
                                            $subestado_siguiente="82";
                                        }else if ($params->respuesta==3) {
                                            $respuesta="VALIDAR";
                                            $respuesta2=5;
                                            $subestado_siguiente="80";
                                        }
                                        
                                    }

                                    $actualizarSubestado="INSERT INTO simulaciones_subestados (id_simulacion,id_subestado,usuario_creacion,fecha_creacion) VALUES ('".$params->idSimulacion."', '".$subestado_siguiente."', (select login from usuarios where id_usuario='".$params->usuario_Seas."'), current_timestamp)";

                                    $data["idSimulacion"]=$params->idSimulacion;
                                    if (sqlsrv_query($link,$actualizarSubestado)) {

                                        if ($envio_notificacion==1)
                                        {

                                            $informacionCredito="SELECT * FROM simulaciones WHERE id_simulacion='".$params->idSimulacion."'";
                                            $queryInformacionCredito=sqlsrv_query($link,$informacionCredito);
                                            $resInformacionCredito=sqlsrv_fetch_array($queryInformacionCredito, SQLSRV_FETCH_ASSOC);

                                            $queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
                                                            FROM simulaciones_comprascartera a
                                                            LEFT JOIN agenda b ON a.id_simulacion = b.id_simulacion
                                                            WHERE a.se_compra = 'SI' AND FORMAT(b.fecha_vencimiento, 'yyyy-MM-dd') = FORMAT(GETDATE(), 'yyyy-MM-dd') AND a.id_simulacion = ".$params->idSimulacion." AND a.consecutivo = b.consecutivo";
                                            $conCC = sqlsrv_query($link, $queryBD_CC, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                                            if (sqlsrv_num_rows($conCC)>0)
                                            {
                                                $opciones = array(
                                                    'http'=>array(
                                                        'method' => 'POST',
                                                        'content' => "id_simulacion=".$params->idSimulacion."&nombre=".$resInformacionCredito["nombre"]."&cedula=".$resInformacionCredito["cedula"]
                                                                
                                                    )
                                                );
                                                    
                                                $contexto = stream_context_create($opciones);
                                            
                                                $json_Input = file_get_contents($urlPrincipal.'/servicios/enviar_correo_vencimientos.php', false, $contexto);
                                            
                                                $parametros=json_decode($json_Input);
                                            }
                                        }

                                        $query3="update simulaciones set id_subestado='".$subestado_siguiente."',visado='".$respuesta2."',fecha_visado=current_timestamp where id_simulacion = '".$params->idSimulacion."'";
                                        if (sqlsrv_query($link,$query3)) {

                                            if ($params->respuesta<>2)
                                            {
                                                $query4="INSERT INTO simulaciones_observaciones (id_simulacion,observacion,usuario_creacion,fecha_creacion) VALUES ('".$params->idSimulacion."','ANALISTA: ".$params->nombreUsuario." .CREDITO CON RESPUESTA POR PARTE DE PAGADURIA. RESPUESTA: ".$respuesta.". OBSERVACION DE ANALISTA: ".$params->observacion_Visado."', (select login from usuarios where id_usuario='".$params->usuario_Seas."'), current_timestamp);";
                                                if (!sqlsrv_query($link,$query4)) { 
                                                    if (in_array($subestado_siguiente, $subestados_tesoreria)){
                                                        sqlsrv_query($link, "update simulaciones set estado_tesoreria = 'ABI', fecha_tesoreria = GETDATE() where id_simulacion = '".$params->idSimulacion."' AND estado_tesoreria IS NULL");
                                                    }
                                                    $codigo=400;
                                                    $response = array('codigo' => $codigo, 'mensaje' => 'Error al modificar simulacion','data'=>'');
                                                }
                                            }else{
                                                sqlsrv_query($link, "update simulaciones set decision='NEGADO',estado = 'NEG', id_causal= 3 where id_simulacion = '".$params->idSimulacion."'");
                                                //estado negado
                                                $observacion_negado="El credito actual ha sido guardado con estado NEGADO. Decision: NEGADO";
                                                if ($id_causal<>"NULL")
                                                {
                                                    $queryCausal = sqlsrv_query($link, "select id_causal, nombre from causales where (estado = '1' AND tipo_causal = 'NEGACION') AND id_causal = '3'");
                                                    $resCausal=sqlsrv_fetch_array($queryCausal);
                                                    $observacion_negado.=" Causal: ".$resCausal["nombre"];
                                                }

                                                sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$params->idSimulacion."', '".$observacion_negado."', (select login from usuarios where id_usuario='".$params->usuario_Seas."'), NOW())");
                                                
                                                $rs2 = sqlsrv_query($link, "select cedula, pagaduria, retanqueo1_libranza, retanqueo2_libranza, retanqueo3_libranza from simulaciones where id_simulacion = '".$params->idSimulacion."'");
                                                
                                                $fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
                                                
                                                for ($i = 1; $i <= 3; $i++)
                                                {
                                                    if ($fila2["retanqueo".$i."_libranza"])
                                                    {
                                                        $rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila2["cedula"]."' AND pagaduria = '".$fila2["pagaduria"]."' AND nro_libranza = '".$fila2["retanqueo".$i."_libranza"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                                                        
                                                        if (sqlsrv_num_rows($rs1))
                                                        {
                                                            $fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
                                                            
                                                            sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
                                                        }
                                                    }
                                                }
                                            }
                                            
                                                $crearRegistroEstadosVisados="INSERT INTO visados_simulaciones (id_simulacion,estado,observaciones,fecha) VALUES ('".$params->idSimulacion."','".$respuesta2."', 'ANALISTA: ".$params->nombreUsuario." .CREDITO CON RESPUESTA POR PARTE DE PAGADURIA. RESPUESTA: ".$respuesta.". OBSERVACION DE ANALISTA: ".$params->observacion_Visado."', current_timestamp)";
				                            sqlsrv_query($link,$crearRegistroEstadosVisados);
                                                $codigo=200;
                                                $response = array('codigo' => $codigo, 'mensaje' => 'Simulacion Actualizada Satisfactoriamente','data'=>$params->idSimulacion);
                                            
                                        }else{
                                            $codigo=400;
                                            $response = array('codigo' => $codigo, 'mensaje' => 'Error al modificar simulacion','data'=>'');
                                        }
                                    }else{
                                        $codigo=400;        
                                        $response = array('codigo' => $codigo, 'mensaje' => 'Error al guardar Observacion de credito','data'=>$actualizarSubestado);
                                    }
                                }else{
                                    $codigo=404;
                                    $response = array('operacion'=>'Error de Solicitud', 'codigo'=>$codigo, 'mensaje'=>'No se recibieron los parametros esperados');
                                }                                  
    
                            break;
                            
                            default:
                                $codigo=404;        
                                $response = array('operacion' => 'No Recibida', 'codigo' => $codigo, 'mensaje' => 'No Existen Datos de Entrada');
                            break;
                            
                        }
                    }else{
                        $codigo=404;        
                        $response = array('operacion'=>'Error de Solicitud', 'codigo'=>$codigo, 'mensaje'=>'Operacion No Definida');
                    }
                break;
                
                default:
                    $codigo=404;        
                    $response = array('operacion' => 'Metodo No Definido para esta accion', 'codigo' => $codigo, 'mensaje' => 'Metodo No Definido para esta accion');
                break;
            }
        } else {
            $codigo=404;        
            $response = array('operacion' => 'Metodo No Definido para esta accion', 'codigo' => $codigo, 'mensaje' => 'Metodo No Definido para esta accion');
        }
   
    echo json_encode($response);
	http_response_code("200");
?>