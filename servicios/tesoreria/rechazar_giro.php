<?php
    include ('../../functions.php');

    $link = conectar_utf();

    if(isset($_POST['id_giro'])){

        $id_simulacion = $_POST['id_simulacion'];
        $id_giro = $_POST["id_giro"];

        $queryGiro = "SELECT * FROM giros WHERE id_giro = '".$_POST['id_giro']."'";
        $conGiro = sqlsrv_query($link, $queryGiro, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if (sqlsrv_num_rows($conGiro) > 0) {
            $datosGiro = sqlsrv_fetch_array($conGiro);

            if($datosGiro["estado"] == 1){
                $data = array('code' => 301, 'mensaje' => 'El Giro ya fue Procesado');
            }else{
                $queryUpdateGiro = "DELETE FROM giros WHERE id_giro = '".$id_giro."'";
                if(sqlsrv_query($link, $queryUpdateGiro)){

                    if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 85 WHERE id_simulacion  = '".$id_simulacion."'")){
                        sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$id_simulacion."', 85, 'system', GETDATE())");//6.7 RECHAZADO PDTE CERTIFICADO BANCARIO.
                    }

                    $data = array('code' => 200, 'mensaje' => 'Resultado Satisfactorio');

                }else{
                    $data = array('code' => 500, 'mensaje' => 'No se pudo actualizar como aprobado.');
                }
            }
        }else{
            $data = array('code' => 300, 'mensaje' => 'El Giro No Existe');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>