<?php
    
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_SESSION['S_IDUSUARIO'])){
        
        $queryConsulta = "SELECT * FROM edad_rango_seguro ";

        if(isset($_POST['id_edad_rango_seguro']) && !empty($_POST['id_edad_rango_seguro'])){
            $queryConsulta .= " WHERE id_edad_rango_seguro = '".$_POST['id_edad_rango_seguro']."'";
        }

        $conRangos = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $edad_rango_seguro = array();
        if (sqlsrv_num_rows($conRangos) > 0) {
            while ($response = sqlsrv_fetch_array($conRangos, SQLSRV_FETCH_ASSOC)) {
                $edad_rango_seguro[] = array(
                    "id_edad_rango_seguro" => $response["id_edad_rango_seguro"],
                    "edad_rango_inicio" => $response["edad_rango_inicio"],
                    "edad_rango_fin" => $response["edad_rango_fin"],
                    "valor_por_millon" => $response["valor_por_millon"],
                    "valor_por_millon_parcial" => $response["valor_por_millon_parcial"],
                    "estado" => $response["estado"],
                    "usuario_creacion" => $response["usuario_creacion"],
                    "fecha_creacion" => $response["fecha_creacion"],
                    "opciones" =>   "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddTasa' onclick='modalSaveRangoEdad(".$response['id_edad_rango_seguro'].")' name='".$response["id_edad_rango_seguro"]."' >Editar</a>
                        <a class='btn btn-sm btn-danger' style='margin-left: 3px;' onclick='deleteRangoEdad(".$response["id_edad_rango_seguro"].")'>Eliminar</a>"
                );
            }

            $data = array('codigo' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $edad_rango_seguro);
        }else{
            $data = array('codigo' => 300, 'mensaje' => 'Error, Rango No encontrado');
        }
    }else{
        $data = array('codigo' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>