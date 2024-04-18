<?php
    error_reporting(-1);
    error_reporting(0);
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);

    
    include ('../../functions.php');
    include ('../cors.php');
    $link = conectar_utf();
    
    if(isset($_POST['opcion'])){
        
        $opcion = '';
        
        if(isset($_POST['opcion'])){
            $opcion = $_POST['opcion'];
        }
        
        $queryConsulta = "SELECT si.formato_digital,fd.respuesta_push,fd.en_progreso,fd.sub_estado_trx,fd.estado_token,fd.fecha_leido,fd.observacion_crear_girador,fd.observacion_crear_pagare,fd.observacion_firma_pagare,si.id_simulacion,si.nombre,si.cedula,si.pagaduria, se.nombre AS subestado,un.nombre AS unidad_negocio,em.nombre_empresa,si.nro_libranza,fd.pagare_deceval,fd.fecha_pagare_deceval
        FROM formulario_digital fd
        LEFT JOIN simulaciones si ON fd.id_simulacion=si.id_simulacion
        LEFT JOIN unidades_negocio un ON un.id_unidad=si.id_unidad_negocio
        LEFT JOIN empresas em ON em.id_empresa=un.id_empresa
        LEFT JOIN subestados se ON se.id_subestado=si.id_subestado
        WHERE (si.formato_digital IS NULL OR si.formato_digital = 0) AND ((fd.observacion_firma_pagare=''
        AND ((fd.observacion_crear_girador IS NOT NULL AND fd.observacion_crear_girador<>'') AND (fd.observacion_crear_pagare IS NOT NULL AND fd.observacion_crear_pagare<>''))) OR (fd.observacion_firma_pagare LIKE '%SDL.SE.0118%')) AND se.cod_interno <= 32
        GROUP BY si.id_simulacion, si.formato_digital,
        fd.respuesta_push,
        fd.en_progreso,
        fd.sub_estado_trx,
        fd.estado_token,
        fd.fecha_leido,
        fd.observacion_crear_girador,
        fd.observacion_crear_pagare,
        fd.observacion_firma_pagare,
        si.id_simulacion,
        si.nombre,
        si.cedula,
        si.pagaduria,
        se.nombre ,
        un.nombre,
        em.nombre_empresa,
        si.nro_libranza,
        fd.pagare_deceval,
        fd.fecha_pagare_deceval";

   
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
                    "subestado" => $response["subestado"],
                    "unidad_negocio" => $response["unidad_negocio"],
                    "nombre_empresa" => $response["nombre_empresa"],                                                                                                      
                    "nro_libranza" => $response["nro_libranza"],
                    "pagare_deceval" => $response["pagare_deceval"],
                    "fecha_pagare_deceval" => $response["fecha_pagare_deceval"],
                    "opciones" => "<button type='button' data-bs-toggle='modal' data-bs-target='#modal_firmar_credito' class='btn btn-success btn-sm' style='margin-left: 3px;' onclick='consultarInformacionFirma(".$response["id_simulacion"].", this)' name='".$response["id_simulacion"]."' >Marcar Firmado</button>"
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