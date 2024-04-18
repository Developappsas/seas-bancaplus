<?php
include ('../../functions.php');
include ('../cors.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header("Content-Type: application/json; charset=utf-8");    
$link = conectar_utf();
date_default_timezone_set('Etc/UTC');

$json_Input = file_get_contents('php://input');
$params = json_decode($json_Input, true);

if (isset($params["operacion"])){
    switch ($params["operacion"]) {
        case 'Consultar Bandeja FDC':
            $data = array();

            if ( $_SESSION['S_REVISION_GARANTIAS'] == '1' || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || (($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_TIPO"] == "OPERACIONES"  || $_SESSION["S_TIPO"] == "ADMINISTRADOR")  && $params["id_empresa"]<>'0'))
            {
                $queryDB = "SELECT une.nombre as unidad_negocio_descripcion,sfd.id as id_sfdc,CASE WHEN si.id_unidad_negocio NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo = 3) THEN 'KREDIT' ELSE 'FIANTI' END AS unidad, 
                CASE WHEN (us.freelance=1 and us.outsourcing=0) then 'FREELANCE' WHEN (us.freelance=0 and us.outsourcing=1) then 'OUTSOURCING' ELSE 'PLANTA' END AS tipo_comercial2,
                sfd.estado as estado_sfd,si.id_analista_riesgo_operativo,si.id_simulacion, si.cedula, si.empleado_manual, si.nombre, si.pagaduria, 
                us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, si.opcion_credito, si.opcion_desembolso_cli, si.opcion_desembolso_ccc, 
                si.opcion_desembolso_cmp, si.opcion_desembolso_cso, si.retanqueo_total, si.valor_credito, si.decision, si.frente_al_cliente, 
                si.fecha_radicado,case when sfd.id_usuario_asignacion is null then '0' when sfd.id_usuario_asignacion = 197 then '0' else sfd.id_usuario_asignacion end as id_usuario_asignacion,si.id_oficina,si.id_unidad_negocio 
                FROM simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
                INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre
                INNER JOIN usuarios us ON si.id_comercial = us.id_usuario
                INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
                LEFT JOIN simulaciones_fdc sfd ON sfd.id_simulacion = si.id_simulacion 
                LEFT JOIN unidades_negocio une ON une.id_unidad=si.id_unidad_negocio ";
                if ($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_TIPO"] == "OPERACIONES"  || $_SESSION["S_TIPO"] == "ADMINISTRADOR") {
                    $queryDB .= " LEFT JOIN empresa_unegocio_fdc eun ON eun.id_unidad_negocio=si.id_unidad_negocio ";
                }
                
                $queryDB.="WHERE sfd.estado in (1,2,5,3) and sfd.vigente = 's'";

            

                
                if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION['S_REVISION_GARANTIAS'] == '1' ) {
                    $queryDB .= " AND (sfd.id_usuario_asignacion = '".$_SESSION["S_IDUSUARIO"]."')";
                }

                if (($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "ADMINISTRADOR")) {
                    if ($params["id_empresa"]<>"ANTIFRAUDE")
                    {
                        $queryDB .= " AND eun.id_empresa=".$params["id_empresa"]." and (si.id_subestado is null or si.id_subestado not in (".implode(",", $subestados_validar_grantias)."))";
                    }else{
                        $queryDB.=" and (si.id_subestado in (".implode(",", $subestados_validar_grantias)."))";
                    }
                    
                }
                
        
    
                $queryDB .= " order by si.id_simulacion ASC";
        
                //echo $queryDB;
        
                $rs = sqlsrv_query($link, $queryDB);
                $consulta = "";
                $consultarOficinasAnalistas = "";
                
                while ($fila = sqlsrv_fetch_array($rs)) {
                    $reprocesos = "";
                    $contarReprocesos = "SELECT * FROM simulaciones_fdc WHERE estado=4 and id_subestado<>28 and id_simulacion = '" . $fila["id_simulacion"] . "' and id<'" . $fila["id_sfdc"] . "'";
                    $queryReprocesos = sqlsrv_query($link, $contarReprocesos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    if (sqlsrv_num_rows($queryReprocesos) == 0) {
                        $reprocesos = "NUEVO";
                    } else {
                        if (sqlsrv_num_rows($queryReprocesos) == 1) {
                            $reprocesos = "REPROCESO 1";
                        } else if (sqlsrv_num_rows($queryReprocesos) > 1) {
                            $reprocesos = "REPROCESOS";
                        } else {
                            $reprocesos = "NUEVO";
                        }
                    }
                    /* $tr_class = "";
                        if (($j % 2) == 0) {
                            $tr_class = " style='background-color:#F1F1F1;'";
                        }                            
                        switch ($fila["opcion_credito"]) {
                            case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"]; break;
                            case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
                            case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
                            case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
                        }
                                        
                        if ($fila["opcion_credito"] == "CLI") {
                            $fila["retanqueo_total"] = 0;
                        }
        
                        if ($fila["frente_al_cliente"] == "SI") { 
                                $imagen_frente='<img src="../images/frentecliente.png" title="Frente al Cliente">';
                            }else{
                                    $imagen_frente='';
                            }
                        */
        
                    $tiempo_prospeccion_letras = "";
        
                    $consultarTiempoRadicado = sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id = (SELECT max(id) FROM simulaciones_fdc WHERE estado = 5 and id_simulacion = '" . $fila["id_simulacion"] . "')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        $arr = 0;
                    if (sqlsrv_num_rows($consultarTiempoRadicado) > 0) {
                        $resTiempoRadicado = sqlsrv_fetch_array($consultarTiempoRadicado);
                        $tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($resTiempoRadicado["fecha_creacion"]);
                        $fecha_prospeccion = $resTiempoRadicado["fecha_creacion"];
                        $arr = date("Y-m-d H:i:s")." aaaa ".$resTiempoRadicado["fecha_creacion"];
                    } else {
                        $tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($fila["fecha_radicado"]);
                        $fecha_prospeccion = $fila["fecha_radicado"];
                        $arr = date("Y-m-d H:i:s")." bbbb ".$fila["fecha_radicado"];
                    }
        
                    $tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);
                    $tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);
                    $tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
        
                    if ($tiempo_prospeccion_horas) {
                        $tiempo_prospeccion_letras .= $tiempo_prospeccion_horas . "h ";
                    }
        
                    $tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos . "m ";
                    $tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos . "s";
        
                    //$id_analista_riesgo_operativo=$fila["id_analista_riesgo_operativo"];
                    $id_analista_riesgo_operativo = $fila["id_usuario_asignacion"];
        
                    if ($fila["id_usuario_asignacion"] == 0 || $fila["estado_sfd"] <> 2) {
                    } else {
                        $btnDetener = '<td align="center"><a id="btnDesasignar" name="' . $fila["id_simulacion"] . '"><img src="../images/dot_rojo.png" title="Desasignar"></a></td>';
                    }
        
                    $analistas = '';
                    if ($_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_REVISION_GARANTIAS"] == "1") {
                        $analistas = '<select disabled id="usuarios_riesgo_operativo" name="' . $fila["id_simulacion"] . '" style=" background-color:#EAF1DD">';
                    } else {
                        $analistas = '<select id="usuarios_riesgo_operativo" name="' . $fila["id_simulacion"] . '" style=" background-color:#EAF1DD">';
                    }
        
                    
                    if ($_SESSION["S_TIPO"] == "ADMINISTRADOR"  || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" )
                    {
                        if ($params["id_empresa"]<>'ANTIFRAUDE')
                        {
                            $consultarUsuariosInicio2 = "SELECT id_usuario FROM empresa_usuario_fdc WHERE id_empresa=".$params["id_empresa"];
                        }else{
                            $consultarUsuariosInicio2 = "SELECT id_usuario FROM usuarios WHERE  revision_garantias='1'";
                        }
                    

                        $consultarUsuariosInicio = "SELECT distinct * FROM usuarios WHERE (id_usuario IN (".$consultarUsuariosInicio2." AND disponible IN ('s','g')) or id_usuario=".$id_analista_riesgo_operativo.")";
            
                        $rs1 = sqlsrv_query($link, $consultarUsuariosInicio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        if (sqlsrv_num_rows($rs1) <= 0) {
                            $analistas .= '<option value="0">SIN ANALISTAS PARA ASIGNAR</option>';
                        } else {
                            $analistas .= '<option value="0">SIN ANALISTAS PARA ASIGNAR</option>';
                            $nombreUsuarioSeleccionado = "";
                            while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
                                if ($fila1["id_usuario"] == $id_analista_riesgo_operativo) {
                                    $selected_ciudad = " selected";
                                    $nombreUsuarioSeleccionado = $fila1["nombre"] . " " . $fila1["apellido"];
                                } else {
                                    $selected_ciudad = "";
                                }
                                $analistas .= "<option value='" . $fila1["id_usuario"] . "'" . $selected_ciudad . ">" . $fila1["nombre"] . " " . $fila1["apellido"] . "</option>";
                            }
                        }

                    }else{
                        $consultarUsuariosInicio = "SELECT distinct * FROM usuarios WHERE (id_usuario=".$id_analista_riesgo_operativo.")";
            
                        $rs1 = sqlsrv_query($link, $consultarUsuariosInicio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                        if (sqlsrv_num_rows($rs1) <= 0) {
                            $analistas .= '<option value="0">SIN ANALISTAS PARA ASIGNAR</option>';
                        } else {
                            $nombreUsuarioSeleccionado = "";
                            while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
                                if ($fila1["id_usuario"] == $id_analista_riesgo_operativo) {
                                    $selected_ciudad = " selected";
                                    $nombreUsuarioSeleccionado = $fila1["nombre"] . " " . $fila1["apellido"];
                                } else {
                                    $selected_ciudad = "";
                                }
                                $analistas .= "<option value='" . $fila1["id_usuario"] . "'" . $selected_ciudad . ">" . $fila1["nombre"] . " " . $fila1["apellido"] . "</option>";
                            }
                        }
                        
                    }
                    
                    $analistas .= '</select>';
        
                    $data[] = array(
                        "id_simulacion" => $fila["id_simulacion"],
                        "cedula" => '<a href="simulador.php?id_simulacion=' . $fila["id_simulacion"] . '&descripcion_busqueda=' . $descripcion_busqueda . '&sectorb=' . $sectorb . '&pagaduriab=' . $pagaduriab . '&id_comercialb=' . $id_comercialb . '&decisionb=' . $decisionb . '&id_oficinab=' . $id_oficinab . '&visualizarb=' . $visualizarb . '&buscar=' . $_REQUEST["buscar"] . '&back=pilotofdc">' . $fila["cedula"] . '</a>',
                        "nombre" => $fila["nombre"],
                        "pagaduria" => $fila["pagaduria"],
                        "comercial" => ($fila["nombre_comercial"] . " " . $fila["apellido"]),
                        "oficina" => $fila["oficina"],
                        "fecha_radicado" => $fecha_prospeccion,
                        "tiempo_prospeccion" => $tiempo_prospeccion_letras,
                        "estado" => $reprocesos,
                        "frente_cliente" => $imagen_frente,
                        "adjuntos" => '<a href="adjuntos.php?id_simulacion=' . $fila["id_simulacion"] . '&descripcion_busqueda=' . $descripcion_busqueda . '&sectorb=' . $sectorb . '&pagaduriab=' . $pagaduriab . '&id_comercialb=' . $id_comercialb . '&decisionb=' . $decisionb . '&id_oficinab=' . $id_oficinab . '&visualizarb=' . $visualizarb . '&buscar=' . $_REQUEST["buscar"] . '&back=pilotofdc&page=' . $_REQUEST["page"] . '"><img src="../images/adjuntar.png" title="Adjuntos"></a>',
                        "stop" => $btnDetener,
                        "hist_est" => "<div class='badge-success' id='btnModalEstados' name='" . $fila["id_simulacion"] . "' type='button' class='open-modal' data-open='modal1'><center><img src='../images/solicitud.png' title='Estados'></center></div>",
                        "hist_proc" => "<div class='badge-success' id='btnModalHistorial' name='" . $fila["id_simulacion"] . "' type='button' class='open-modal' data-open='modal1'><center><img src='../images/proceso.png' title='Historial'></center></div>",
                        "analista_asignado" => $analistas,
                        "nombre_usuario_asignado" => $nombreUsuarioSeleccionado,
                        "consulta" => $consultarOficinasAnalistas,
                        "tipo_comercial2" => $fila["tipo_comercial2"],
                        "unidad_negocio" => $fila["unidad_negocio_descripcion"],
                  
                       
                    );
                }
            }
          
    
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data       
            );
            $response = array('codigo' => 200, 'mensaje' => 'Consulta Ejecutada Satisfactoriamente','datos'=>($results));

            

        break;
        default:
            $codigo=404;        
            $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
        break;  
            }
}else{
    $codigo=400;        
    $response = array('operacion' => 'Operacion errada', 'codigo' => $codigo, 'mensaje' => 'Operacion errada');
}
echo json_encode($response);
http_response_code("200");
?>