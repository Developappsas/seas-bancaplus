<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_percentil'])){

        /*$queryTasaComision = "SELECT id_percentil FROM tasas_comisones_percentil WHERE id_percentil = ".$_POST['id_percentil'];
        $conTasaComision = sqlsrv_query($queryTasaComision, $link);

        if (sqlsrv_num_rows($conTasaComision) > 0) {*/

            $queryDelete = "DELETE FROM tasas_comisiones_percentil WHERE id_percentil = ".$_POST['id_percentil'];
            
            if(sqlsrv_query($link, $queryDelete)){
                $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio');
            }else{
                $data = array('code' => 500, 'mensaje' => 'No se Ha podido Eliminar');
            }
        /*}else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }*/
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>