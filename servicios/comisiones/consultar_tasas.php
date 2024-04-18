<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_unidad_negocio'])){

        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        $dato = '';
        $queryConsulta = "SELECT a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa as tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente
            FROM tasas_comisiones a
            LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio
            WHERE a.marca_unidad_negocio = ".$_POST['id_unidad_negocio']." ";

        switch ($opcion) {
            case 'agrupar_tipos':
                $queryConsulta .= "GROUP BY a.id_tipo,  a.id_tasa_comision,a.id_unidad_negocio, c.nombre, a.tasa ,a.kp_plus,a.fecha_inicio,a.fecha_fin, a.vigente";
                break;
            
            case 'tasa_comision':
                $queryConsulta .= "AND  a.id_tasa_comision = ".$_POST["id_tasa_comision"];

                $queryMaxPosicion = "SELECT iIF(max(posicion) IS NULL, 0, (max(posicion)+1)) AS proxima_posicion FROM tasas_comisiones_percentil WHERE id_tasa_comision = ".$_POST['id_tasa_comision'];
                $conMaxPosicion = sqlsrv_query($link, $queryMaxPosicion);
                $resMaxPos = sqlsrv_fetch_array($conMaxPosicion);

                $dato = $resMaxPos["proxima_posicion"];

                break;

            case 'tasa_comision_tipo':
                $queryConsulta .= "AND  a.id_tipo = ".$_POST["id_tipo"];
                break;

            default:
                $queryConsulta .= "";
                break;
        }
        
        $conTasasComisiones = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $tasas = array();
        if (sqlsrv_num_rows($conTasasComisiones) > 0) {
            while ($response = sqlsrv_fetch_array($conTasasComisiones)) {
                $tasas[] = array(
                    "id_tasa_comision" => $response["id_tasa_comision"],
                    "id_unidad_negocio" => $response["id_unidad_negocio"],
                    "unidad_negocio" => $response["unidad_negocio"],
                    "tasa_interes" => $response["tasa_interes"],
                    "kp_plus" => $response["kp_plus"],
                    "id_tipo" => $response["id_tipo"],
                    "fecha_inicio" => $response["fecha_inicio"],                                                                                                            
                    "fecha_fin" => $response["fecha_fin"],
                    "vigente" => $response["vigente"],
                    "opciones" =>   "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddTasa' onclick='modalSaveTasas(".$_POST['id_unidad_negocio'].", \"edit\", ".$response["id_tasa_comision"].")' name='".$response["id_tasa_comision"]."' >Editar</a>
                        <a class='btn btn-sm btn-danger' style='margin-left: 3px;' onclick='deleteTasa(".$response["id_tasa_comision"].", ".$_POST['id_unidad_negocio'].")' name='".$response["id_tasa_comision"]."' >Eliminar</a>"
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas, 'dato' => $dato);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>