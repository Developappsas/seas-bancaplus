<?php

    include ('../functions.php');
    include ('../function_blob_storage.php');

    $link = conectar_utf();

    if(isset($_POST['id_simulacion']) && isset($_POST['reqexcepReq']) && isset($_POST['observacionReq']) && isset($_POST['idTipoReq']) && isset($_POST['idAreaReq'])){

        $id_simulacion = $_POST['id_simulacion'];
        $reqexcep = $_POST['reqexcepReq'];
        $id_tipo = $_POST['idTipoReq'];
        $id_area = $_POST['idAreaReq'];
        $observacion = $_POST['observacionReq'];

        $id_subestado = 83;//6.1 RECHAZADO PDTE CERTIFICADO BANCARIO.

        sqlsrv_query($link, "START TRANSACTION");

        if(sqlsrv_query($link, "INSERT INTO simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) VALUES ('".$id_simulacion."', '[".$reqexcep."] ".utf8_encode($observacion)."', '".$_SESSION["S_LOGIN"]."', GETDATE())")){

            $id = sqlsrv_query($link, "SELECT SCOPE_IDENTITY() as id;");
            $id2 = sqlsrv_fetch_array($id, SQLSRV_FETCH_ASSOC);
            $id_observacion_pregunta = $id2['id'];

            if ($_REQUEST["fecha_vencimiento"]){
                $fecha_vencimiento = "'".$_REQUEST["fecha_vencimiento"]."'";
            }
            else{
                $fecha_vencimiento = "NULL";
            }

            if(sqlsrv_query($link, "INSERT INTO req_excep (id_simulacion, reqexcep, id_tipo, id_area, fecha_vencimiento, observacion, id_observacion_pregunta, estado, usuario_creacion, fecha_creacion) VALUES ('".$id_simulacion."', '".$reqexcep."', '".$id_tipo."', '".$id_area."', ".$fecha_vencimiento.", '".utf8_encode($observacion)."', '".$id_observacion_pregunta."', 'PENDIENTE', '".$_SESSION["S_LOGIN"]."', GETDATE())")){
            
            $id = sqlsrv_query($link, "SELECT SCOPE_IDENTITY() as id;");
            $id2 = sqlsrv_fetch_array($id, SQLSRV_FETCH_ASSOC);
            $id_reqexcep = $id2['id'];
              

                if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = ".$id_subestado." WHERE id_simulacion  = '".$id_simulacion."'")){
                    sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$id_simulacion."', ".$id_subestado.", 'system', getdate())");//6.1 RECHAZADO PDTE CERTIFICADO BANCARIO.
                }

                sqlsrv_query($link, "COMMIT");


                /*********************************************************************************/
                /******************ASIGNAR ANALISTA REVISION FIRMA GARANTIAS******************/

                $consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$id_simulacion."' and estado<>100";
                $querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc);

                if (sqlsrv_num_rows($querySimulacionesFdc)>0){

                    sqlsrv_query($link, "START TRANSACTION");

                    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion,0,197,current_timestamp(),'s',5,".$id_subestado.",2)");

                    $consultarUsuarioNuevo=sqlsrv_query($link, "SELECT a.id_usuario, a.nombre, a.cantidad_asignado, b.cantidad_terminado, (a.cantidad_asignado+b.cantidad_terminado) AS cantidad_total
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

                      WHERE a.id_usuario=b.id_usuario  ORDER BY (a.cantidad_asignado+b.cantidad_terminado) LIMIT 1", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

                    if (sqlsrv_num_rows($consultarUsuarioNuevo)>0){
                        $resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarUsuarioNuevo, SQLSRV_FETCH_ASSOC);
                        $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                        $crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp,'s',2,3)");
                        $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$id_simulacion."'");

                        $consultarEstadoUsuario=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resEstadoUsuarioNuevo["id_usuario"]."' and vigente='s' and estado='2' and id_simulacion<>'".$id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        if (sqlsrv_num_rows($consultarEstadoUsuario)>0){
                            //$actualizarUsuario2=sqlsrv_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$id_analista_riesgo_operativo."'");
                        }
                    }else{
                      $actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$id_simulacion."'");       
                    }

                    sqlsrv_query($link, "COMMIT");
                }else{
                    $actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$id_simulacion."'");
                    $crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion, 0, 197, current_timestamp, 's', 1 , 1)");

                    $data = array('code' => 200, 'mensaje' => 'Proceso exitoso, Sin asignar Analista');
                }

                if (strcmp($_FILES["archivoReq"]["name"], "")){
                    $uniqueID = uniqid();
                    
                    if ($_REQUEST["descripcion"]){
                        $descripcion = "'".$_REQUEST["descripcion"]."'";
                    } else{
                        $descripcion = $_FILES["archivoReq"]["name"];
                    }

                    sqlsrv_query($link, "INSERT INTO req_excep_adjuntos (id_reqexcep, descripcion, nombre_original, nombre_grabado, usuario_creacion, fecha_creacion) VALUES ('".$id_reqexcep."', '".utf8_encode($descripcion)."', '".reemplazar_caracteres_no_utf($_FILES["archivoReq"]["name"])."', '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivoReq"]["name"])."', '".$_SESSION["S_LOGIN"]."', getdate())");
                    
                    $fechaa = new DateTime();
                    $fechaFormateada = $fechaa->format("d-m-Y H:i:s");
                    
                    $metadata1 = array(
                        'id_simulacion' => $id_simulacion,
                        'descripcion' => reemplazar_caracteres_no_utf($descripcion),
                        'usuario_creacion' => $_SESSION["S_LOGIN"],
                        'fecha_creacion' => $fechaFormateada
                    );
                    
                    upload_file($_FILES["archivoReq"], "simulaciones", $id_simulacion."/varios/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivoReq"]["name"]), $metadata1);
                }

                $data = array('code' => 200, 'mensaje' => 'Resultado Satisfactorio', 'id_reqexcep' => $id_reqexcep);                   
            } else{
                $data = array('code' => 500, 'mensaje' => 'Error al ingresar el requerimiento ', 'error' => sqlsrv_error($link));
            }
        }else{
            $data = array('code' => 300, 'mensaje' => 'Error al ingresar el requerimiento', 'error' => sqlsrv_error($link));
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>