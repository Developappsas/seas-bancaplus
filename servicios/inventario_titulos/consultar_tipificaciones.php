<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

       $opcion = '';
        

        $dato = '';
        $queryConsulta = "SELECT * FROM tipificacion_inventario_creditos";
 
        $conPagadurias = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conPagadurias) > 0) {
            while ($response = sqlsrv_fetch_array($conPagadurias, SQLSRV_FETCH_ASSOC)) {
                $tasas[] = array(
                    "id" => $response["id_tipificacion_credito"],
                    "descripcion" => $response["descripcion"]
                    
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
  
    
    echo json_encode($data);
?>