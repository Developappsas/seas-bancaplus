<?php
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['id_unidad_negocio'])){

        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        $tasas = array();
            $queryConsulta = ("SELECT e.id_percentil, e.id_tipo_contrato, a.id_tasa_comision, a.id_unidad_negocio, c.nombre, e.posicion AS percentil, e.rango_inicial, e.rango_final, e.valor,
            CASE when e.id_tipo_contrato = 3 then 'OUTSOURSING' when e.id_tipo_contrato = 2 then 'FREELANCE' ELSE 'PLANTA' END tipo_contrato_dec
            , a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente
            FROM tasas_comisiones a
            LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio
            LEFT JOIN tasas_comisiones_percentil e ON e.id_tasa_comision = a.id_tasa_comision
            WHERE a.marca_unidad_negocio = ".$_POST['id_unidad_negocio']." AND e.id_tipo_contrato = ".$_POST['id_tipo_contrato']." ");

        switch ($opcion) {
            case 'percentil_comision':
                $queryConsulta .= "AND  e.id_percentil = ".$_POST["id_percentil"];
            break;

            default:
                $queryConsulta .= "";
                break;
        }

        $conTasasComisiones = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        
        if (sqlsrv_num_rows($conTasasComisiones) > 0) {
            while ($response = sqlsrv_fetch_array($conTasasComisiones)) {
                $tasas[] = array(
                    "id_percentil" => $response["id_percentil"],
                    "id_tasa_comision" => $response["id_tasa_comision"],
                    "id_tipo_contrato" => $response["id_tipo_contrato"],
                    "id_unidad_negocio" => $response["id_unidad_negocio"],
                    "unidad_negocio" => $response["nombre"],
                    "percentil" => $response["percentil"],
                    "rango_inicial" => $response["rango_inicial"],
                    "rango_final" => $response["rango_final"],
                    "valor" => $response["valor"],
                    "tasa_interes" => $response["tasa_interes"],
                    "kp_plus" => $response["kp_plus"],
                    "tipo_contrato" => $response["tipo_contrato_dec"],
                    "id_tipo" => $response["id_tipo"],
                    "fecha_inicio" => $response["fecha_inicio"],
                    "fecha_fin" => $response["fecha_fin"],
                    "vigente" => $response["vigente"],
                    "opciones" =>   "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalSavePercentil' onclick='modalSavePercentil(".$_POST['id_tipo_contrato'].", \"edit\", ".$response["id_percentil"].")' name='".$response["id_percentil"]."' >Editar</a>
                        <a class='btn btn-sm btn-danger' style='margin-left: 3px;' onclick='deletePercentil(".$response["id_percentil"].",".$_POST['id_tipo_contrato'].")' name='".$response["id_percentil"]."' >Eliminar</a>"
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $tasas);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>