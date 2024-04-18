<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();
    $json_Input = file_get_contents('php://input');
    $params = json_decode($json_Input);

    if(isset($params->id_simulacion) && isset($params->nro_afiliacion) && isset($params->numero_cuotas) && isset($params->valor_cuota)){

        $id_simulacion = $params->id_simulacion;
        $consecutivo = 1;
        $nro_libranza_sim = "";

        $query_libranza = sqlsrv_query($link, "SELECT nro_libranza FROM simulaciones WHERE id_simulacion = '".$id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if(sqlsrv_num_rows($query_libranza) > 0){
            $datos_simul = sqlsrv_fetch_array($query_libranza);
            $nro_libranza_sim = intval(preg_replace('/[^0-9]+/', '', $datos_simul["nro_libranza"]), 10);;
        }

        $query_consec = sqlsrv_query($link,"SELECT MAX(consecutivo) AS con FROM incorporaciones_parciales a WHERE a.id_simulacion = ".$id_simulacion, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        if($query_consec && sqlsrv_num_rows($query_consec) > 0){
            $datosCons = sqlsrv_fetch_array($query_consec);
            $consecutivo = 1 + intval($datosCons["con"]);
        }

        $query_insert = "INSERT INTO incorporaciones_parciales (id_simulacion, consecutivo, nro_libranza, nro_afiliacion, cuotas, valor_cuota, observacion, id_usuario, fecha_creacion) VALUES ('".$id_simulacion."', ".$consecutivo.", '".$nro_libranza_sim."', '".$params->nro_afiliacion."', '".$params->numero_cuotas."', '".$params->valor_cuota."', '".$params->observacion."', '".$_SESSION['S_IDUSUARIO']."', GETDATE())";
        if(sqlsrv_query($link,$query_insert)){

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
        }else{
            $data = array('code' => 500, 'mensaje' => 'No se pudo actualizar como firmado.');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>