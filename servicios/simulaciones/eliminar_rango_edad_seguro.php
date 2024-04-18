<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_edad_rango_seguro'])){

        $queryTasaComision = "SELECT id_edad_rango_seguro FROM edad_rango_seguro WHERE id_edad_rango_seguro = ".$_POST['id_edad_rango_seguro'];
        $conTasaComision = sqlsrv_query($link, $queryTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conTasaComision) > 0) {

            $queryDelete = "DELETE FROM edad_rango_seguro WHERE id_edad_rango_seguro = ".$_POST['id_edad_rango_seguro'];
            
            if(sqlsrv_query($link, $queryDelete)){
                $data = array('codigo' => 200, 'mensaje' => 'Resultado satisfactorio');
            }else{
                $data = array('codigo' => 500, 'mensaje' => 'No se Ha podido Eliminar');
            }
        }else{
            $data = array('codigo' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('codigo' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>