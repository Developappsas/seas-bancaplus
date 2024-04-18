<?php

    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();

    if(isset($_POST['opcion'])){

        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }

        $queryConsulta = "SELECT si.formato_digital, si.id_simulacion, si.nombre, si.cedula, si.pagaduria, se.nombre AS subestado, un.nombre as unidad_negocio, em.nombre_empresa, si.nro_libranza, s.email, si.id_unidad_negocio, s.telefono_personal
        FROM simulaciones si
        LEFT JOIN solicitud s on s.id_simulacion = si.id_simulacion 
        LEFT JOIN unidades_negocio un ON un.id_unidad = si.id_unidad_negocio 
        LEFT JOIN empresas em ON em.id_empresa = un.id_empresa 
        LEFT JOIN subestados se ON se.id_subestado = si.id_subestado 
        WHERE si.solicitar_firma = 1";

        switch ($opcion) {
            case 'agrupar_tipos':
                $queryConsulta .= "";
                break;
            
            case 'tasa_comision':
                $queryConsulta .= " ";
                break;

            case 'tasa_comision_tipo':
                $queryConsulta .= "";
                break;

            default:
                $queryConsulta .= "";
                break;
        }

        $conCreditos = sqlsrv_query($link, $queryConsulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
        $creditos = array();
        if (sqlsrv_num_rows($conCreditos) > 0) {
            while ($response = sqlsrv_fetch_array($conCreditos)) {
                $creditos[] = array(
                    "id_simulacion" => $response["id_simulacion"],
                    "nombre" => $response["nombre"],
                    "cedula" => $response["cedula"],
                    "pagaduria" => $response["pagaduria"],
                    "unidad_negocio" => $response["unidad_negocio"],
                    "nombre_empresa" => $response["nombre_empresa"],
                    "nro_libranza" => $response["nro_libranza"],
                    "telefono" => $response["telefono_personal"],
                    "opciones" => "<button type='button' class='btn btn-success btn-sm' style='margin-left: 3px;' onclick='duplicar_credito(".$response["id_simulacion"].", this)' name='".$response["id_simulacion"]."' >Prospectar</button>",
                    "opciones2" => "<button type='button' class='btn btn-warning btn-sm' style='margin-left: 3px;' onclick='SolicitarFirmar(".$response["id_simulacion"].", `".$response['email']."`, this)' name='".$response["id_simulacion"]."' >Solicitar Firma</button>",
                    "opciones3" => '<a target="_blank" href="solicitud.php?id_unidad_negocio='.$response["id_unidad_negocio"].'&id_simulacion='.$response["id_simulacion"].'&fecha_inicialbd=&fecha_inicialbm=&fecha_inicialba=&fecha_finalbd=&fecha_finalbm=&fecha_finalba=&fechades_inicialbd=&fechades_inicialbm=&fechades_inicialba=&fechades_finalbd=&fechades_finalbm=&fechades_finalba=&fechaprod_inicialbm=&fechaprod_inicialba=&fechaprod_finalbm=&fechaprod_finalba=&cedula_busqueda='.$response["cedula"].'&unidadnegociob=&sectorb=&pagaduriab=&tipo_comercialb=&id_comercialb=&estadob=&decisionb=&id_subestadob=&id_oficinab=&tipo_pagareb=&visualizarb=&calificacionb=&statusb=&buscar=&page="><img src="../images/solicitud.png" title="Solicitud Cr&eacute;dito"></a>'
                );
            }

            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'data' => $creditos);
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para Mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Se Rebieron Datos');
    }
    
    echo json_encode($data);
?>