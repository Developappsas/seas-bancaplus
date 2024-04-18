<?php
    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

    include ('../../functions.php');
    include ('../../cors.php');

    header("Content-Type: application/json; charset=utf-8");    
    $link = conectar_utf();
    date_default_timezone_set('Etc/UTC');

    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input, true);

    if (isset($params["observacion_respuesta"]) && isset($params["id_simulacion"]) && isset($params["id_requerimiento"])){

        $id_simulacion = $params['id_simulacion'];
        $id_requerimiento = $params['id_requerimiento'];
        $reqexcep = 'REQUERIMIENTO';
        $id_tipo = 6;//CAMBIO DE CONDICIONES DE SOLICITUD
        $observacion = $params["observacion_respuesta"];
        $id_area = 1;//Credito

        sqlsrv_query($link, "BEGIN TRANSACTION");

    	if(sqlsrv_query($link,"UPDATE simulaciones_requisitos SET respuesta  = '".$observacion."', id_usuario_respuesta = '".$params['id_usuario']."', fecha_respuesta = GETDATE() WHERE id_simulacion = '".$id_simulacion."' AND requerimiento_id = '".$id_requerimiento."'")){

            if(sqlsrv_query($link, "INSERT INTO simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) VALUES ('".$id_simulacion."', '[".$reqexcep."] ".utf8_encode($observacion)."', '".$params['id_usuario']."', GETDATE())")){

                $id_observacion_pregunta = sqlsrv_insert_id($link);

                if ($params["fecha_vencimiento"]){
                    $fecha_vencimiento = "'".$params["fecha_vencimiento"]."'";
                }
                else{
                    $fecha_vencimiento = "NULL";
                }

                if(sqlsrv_query($link, "INSERT INTO req_excep (id_simulacion, reqexcep, id_tipo, id_area, fecha_vencimiento, observacion, id_observacion_pregunta, estado, usuario_creacion, fecha_creacion) VALUES ('".$id_simulacion."', '".$reqexcep."', '".$id_tipo."', '".$id_area."', ".$fecha_vencimiento.", '".utf8_encode($observacion)."', '".$id_observacion_pregunta."', 'PENDIENTE', '".$params['id_usuario']."', GETDATE())")){
                
                    $id_reqexcep1 = sqlsrv_query($link, "SELECT @@IDENTITY as id");
                    $id_reqexcep2= sqlsrv_fetch_array($id_reqexcep1, SQLSRV_FETCH_ASSOC);
                    $id_reqexcep = $id_reqexcep2['id'];

                    sqlsrv_query($link, "COMMIT");

                    if (isset($params['id_adjunto']) && !empty($params['id_adjunto'])){                        
                        sqlsrv_query($link, "INSERT INTO req_excep_adjuntos (id_reqexcep, descripcion, nombre_original, nombre_grabado, usuario_creacion, fecha_creacion) 
                            SELECT '".$id_reqexcep."', descripcion, nombre_original, nombre_grabado, usuario_creacion, GETDATE() 
                            FROM adjuntos 
                            WHERE id_adjunto = '".$params['id_adjunto']."'");
                    }

                    $data = array('codigo' => 200, 'mensaje' => 'Resultado Satisfactorio', 'id_reqexcep' => $id_reqexcep);                   
                } else{
                    $data = array('codigo' => 500, 'mensaje' => 'Error al ingresar el requerimiento ', 'error' => sqlsrv_errors());
                }
            }else{
                $data = array('codigo' => 300, 'mensaje' => 'Error al ingresar el requerimiento', 'error' => sqlsrv_errors());
            }
    	}else{
    		$data = array('codigo' => 500, 'mensaje' => 'Error Al responder Requisito'."UPDATE simulaciones_requisitos SET respuesta  = '".$observacion."', id_usuario_respuesta = '".$params['id_usuario']."', fecha_respuesta = GETDATE() WHERE id_simulacion = '".$id_simulacion."' AND requerimiento_id = '".$id_requerimiento."'", "error" => sqlsrv_errors());
    	}
    }else{
        $data = array('codigo' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }

    echo json_encode($data);
?>