<?php

    include ('../../../functions.php');
    include ('../../cors.php');
    $link = conectar_utf();

    if(isset($_POST['opcion']) && isset($_POST['id_reporte'])){
        
        $queryConsulta = "SELECT * FROM reportes WHERE id = " . $_POST['id_reporte'];
        
        if($_POST['opcion'] == 'consultar_reporte') {

            $conReportes = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
            $reportes = array();
            if (sqlsrv_num_rows($conReportes) > 0) {
                while ($response = sqlsrv_fetch_array($conReportes)) {
                    $reportes[] = array(
                        "id_reporte" => $response["id"],
                        "tipo_reporte" => $response["tipo_reporte"],
                        "descripcion" => $response["descripcion"],
                        "url" => $response["url"],
                        "opciones" =>   "<a class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#modalAddReport' onclick='modalSaveReport(".$response['id'].", \"edit\")'>Editar</a>
                            <a class='btn btn-sm btn-danger' style='margin-left: 3px;' onclick='deleteReport(".$response["id"].")' >Eliminar</a>"
                    );
                }

                $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $reportes);
            }else{
                $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
            }
        }else{
            $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>