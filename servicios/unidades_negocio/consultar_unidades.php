<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    $dato = '';
    $queryConsulta = "SELECT * FROM unidades_negocio";//WHERE estado = 1";

    $conUnidades = sqlsrv_query($link, $queryConsulta,  array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    $unidadesNegocio = array();
    if (sqlsrv_num_rows($conUnidades) > 0) {
        while ($response = sqlsrv_fetch_array($conUnidades)) {
            $unidadesNegocio[] = array(
                "id_unidad" => $response["id_unidad"],
                "unidad" => $response["nombre"]
            );
        }

        $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $unidadesNegocio);
    }else{
        $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
    }
    
    echo json_encode($data);
?>