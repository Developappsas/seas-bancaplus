<?php

    include ('../../../functions.php');
    include ('../../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_reporte'])){

        $queryTasaComision = "SELECT id FROM reportes WHERE id = ".$_POST['id_reporte'];
        $conTasaComision = sqlsrv_query($link, $queryTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conTasaComision) > 0) {

            $queryDelete = "DELETE FROM reportes WHERE id = ".$_POST['id_reporte'];
            
            if(sqlsrv_query($link, $queryDelete)){
                $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
            }else{
                $data = array('code' => 500, 'mensaje' => 'No se Ha podido Eliminar');
            }
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>