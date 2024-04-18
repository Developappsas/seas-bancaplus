<?php
    include ('../../functions.php');
    include ('../cors.php');

    $link = conectar_utf();

    if(isset($_POST['id_unidad_negocio'])){
        $opcion = ''; 
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        switch ($opcion) {
            case 'agrupar_tasa_interes':

                $dato = '';
                $queryConsulta = "SELECT * FROM tasas2 a GROUP BY a.tasa_interes, a.id_tasa2, a.id_tasa,a.descuento1, a.descuento1_producto, a.sin_seguro,a.descuento2, a.descuento3, a.solo_activos,a.solo_pensionados";
                

                $conTasas = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                $tasas = array();
                if (sqlsrv_num_rows($conTasas) > 0) {
                    while ($response = sqlsrv_fetch_array($conTasas)) {
                        $tasas[] = array(
                            "id_tasa_comision" => $response["id_tasa"],
                            "id_tasa" => $response["id_tasa"],
                            "id_tasa2" => $response["id_tasa2"],
                            "tasa_interes" => $response["tasa_interes"]
                        );
                    }

                    $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas);
                }else{
                    $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
                }
                break;

            default:
                $queryConsulta = "";
                $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
                break;
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
        
    echo json_encode($data);
?>