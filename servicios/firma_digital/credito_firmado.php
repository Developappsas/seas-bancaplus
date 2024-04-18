<?php

    //include_once ('.././controles/FDC.php');
    include ('../../functions.php');

    $link = conectar_utf();

    function asignarUsuarioFirmaGarantias($id_simulacion){
        global $link;

        $id_subestado = 72;//3.2 FIRMA DE GARANTIAS

        $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and estado<>100";
        $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($querySimulacionesFdc)>0){

            sqlsrv_query($link, "START TRANSACTION", $link);

            $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
            $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion,0,197,current_timestamp,'s',5,".$id_subestado.",72)");

            $consultarUsuarioNuevo=sqlsrv_query($link, "SELECT top 1 a.id_usuario, a.nombre, a.cantidad_asignado, b.cantidad_terminado, (a.cantidad_asignado+b.cantidad_terminado) AS cantidad_total
                FROM
                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
                    WHERE a.revision_garantias = 1 GROUP BY a.id_usuario) a,

                (SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado
                    FROM usuarios a 
                    LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = GETDATE()) b 
                    ON a.id_usuario=b.id_usuario_creacion
                    WHERE a.revision_garantias = 1 GROUP BY a.id_usuario) b 

                WHERE a.id_usuario=b.id_usuario  ORDER BY (a.cantidad_asignado+b.cantidad_terminado)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

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

            $data = array('code' => 200, 'mensaje' => 'Proceso exitoso');
        }else{
            $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
            $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion, 0, 197, current_timestamp, 's', 1 , 74)");

            $data = array('code' => 200, 'mensaje' => 'Proceso exitoso, Sin asignar Analista');
        }
    }

    if(isset($_POST['id_simulacion'])){

        $idSimulacion = $_POST['id_simulacion'];
        $formato_digital = 1;
        $val = 0;

        $queryTasaComision = "SELECT id_simulacion FROM formulario_digital WHERE id_simulacion = ".$_POST['id_simulacion'];
        $conTasaComision = sqlsrv_query($link, $queryTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conTasaComision) > 0) {

            $val = 1;

            $queryUpdateFor = "UPDATE formulario_digital SET estado_token = '".$_POST["estado_token"]."', fecha_envio = '".$_POST["fecha_envio"]."', fecha_leido = '".$_POST["fecha_leido"]."', en_progreso = '".$_POST["en_progreso"]."', pagare_deceval = '".$_POST["pagare_deceval"]."', fecha_pagare_deceval = '".$_POST["fecha_pagare_deceval"]."', firma_experian = '".$_POST["firma_experian"]."', sub_estado_trx = '".$_POST["sub_estado_trx"]."', observacion_crear_pagare = '".$_POST["observacion_crear_pagare"]."', observacion_crear_girador = '".$_POST["observacion_crear_girador"]."', observacion_firma_pagare = '".$_POST["observacion_firma_pagare"]."' WHERE id_simulacion = '".$_POST["id_simulacion"]."'";

            if(sqlsrv_query($link, $queryUpdateFor)){

                $queryUpdate = "UPDATE simulaciones SET formato_digital = $formato_digital WHERE id_simulacion = ".$_POST['id_simulacion'];
            
                if(sqlsrv_query($link, $queryUpdate)){

                    $conSubEstadoSimul=sqlsrv_query($link, "SELECT id_subestado FROM simulaciones WHERE id_simulacion = ".$idSimulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    $id_subestado = 0;
                    if($conSubEstadoSimul && sqlsrv_num_rows($conSubEstadoSimul) > 0){
                        $infoSubestado = sqlsrv_fetch_array($conSubEstadoSimul, SQLSRV_FETCH_ASSOC);
                        $id_subestado = $infoSubestado["id_subestado"];
                        $val = 2;
                    }
                    if($id_subestado == 3 || $id_subestado == 77 || $id_subestado == 72){

                        $val = 3;
                        if($id_subestado != 72){
                            if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 72 WHERE id_simulacion  = '".$idSimulacion."'")){
                                sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$idSimulacion."', 72, 'system', GETDATE())");//3.2 VALIDACION DE IDENTIDAD Y GARANTIAS
                                $val = 4;
                            }
                        }

                        asignarUsuarioFirmaGarantias($idSimulacion);
                    }

                    $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'val' => $val);
                }else{
                    $data = array('code' => 500, 'mensaje' => 'No se pudo actualizar como firmado.');
                }
            }else{
                $data = array('code' => 500, 'mensaje' => 'No se pudo actualizar como firmado.');
            }
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>