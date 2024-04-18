
<?php
    session_start();
    include ('../functions.php');
    $link = conectar_utf();

        $unidades1="'0'";
        $consultarUnidadesUsuario1 = "SELECT a.* FROM usuarios a WHERE a.id_usuario IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g')";

        $queryUnidadesUsuario1=sqlsrv_query($link, $consultarUnidadesUsuario1);
        while ($resUnidadesUsuario1=sqlsrv_fetch_array($queryUnidadesUsuario1))
        {
            $unidades1 .= ", '".$resUnidadesUsuario1["id_usuario"]."'";
        }

        $id1 = explode("'0',", $unidades1);
	    $id2 = explode("'", $id1[1]);


        $unidades2="'0'";
        $consultarUnidadesUsuario2 = "SELECT a.* FROM usuarios a WHERE a.id_usuario not IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) and a.subtipo='ANALISTA_CREDITO' and a.disponible in ('s','g')";
        $queryUnidadesUsuario2=sqlsrv_query($link, $consultarUnidadesUsuario2);
        while ($resUnidadesUsuario2=sqlsrv_fetch_array($queryUnidadesUsuario2))
        {
            $unidades2 .= ", '".$resUnidadesUsuario2["id_usuario"]."'";
        }
    
        $id3 = explode("'0',", $unidades2);
	    $id4 = explode("'", $id3[1]);
	
	    //$id_unidad_negocio = $id2[0];
        
    
        
    switch ($_POST["exe"]) 
    {
        case 'consultarBandejaFDC':        
            $data=array();        
            $queryDB = "SELECT CASE WHEN si.id_unidad_negocio NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo = 3) THEN 'KREDIT' ELSE 'FIANTI' END AS unidad, 
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
            LEFT JOIN coordinacion_usuarios cou ON cou.id_usuario_secundario=si.id_comercial
            WHERE sfd.estado in (10) and sfd.vigente = 's'";

            if ($_SESSION["S_SECTOR"]) {
                $queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
            }

            if ($_SESSION["S_TIPO"] == "COMERCIAL") {
                $queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
            } else {
                
                $queryDB .= " AND si.id_unidad_negocio IN (SELECT id_unidad_negocio FROM usuarios_unidades WHERE id_usuario=".$_SESSION["S_IDUSUARIO"].")";		
            }

            if ($_SESSION["S_SUBTIPO"] == "ANALISTA_PREESTUDIO") {
                $queryDB .= " AND (cou.id_usuario_principal = '".$_SESSION["S_IDUSUARIO"]."')";
            }

            if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
                $queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
         
            }
          
            
            $consultarOficinasAnalistas = "SELECT * FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."'";
            $queryOficinaAnalista = sqlsrv_query($link, $consultarOficinasAnalistas);
            if (sqlsrv_num_rows($queryOficinaAnalista) > 0) {
                $queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
            }

            $queryDB .= " order by si.id_simulacion ASC";

            $rs = sqlsrv_query($link, $queryDB);
            $consulta = "";
            $consultarOficinasAnalistas = "";
            while ($fila = sqlsrv_fetch_array($rs)) {
                $reprocesos = "";
                $contarReprocesos = "SELECT id_simulacionsubestado, id_subestado FROM simulaciones_subestados WHERE id_simulacion = '".$fila["id_simulacion"]."'";
                $queryReprocesos=sqlsrv_query($link, $contarReprocesos, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                if (sqlsrv_num_rows($queryReprocesos) == 0) {
                    $reprocesos = "NUEVO";
                }else{
                    $contarReprocesos2=sqlsrv_query($link, $contarReprocesos." and id_subestado in (70,72)", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
                    if (sqlsrv_num_rows($contarReprocesos2) == 1) {
                        $reprocesos="REPROCESO 1";
                    }else if (sqlsrv_num_rows($contarReprocesos2) > 1) {
                        $reprocesos="REPROCESOS";
                    }else{
                        $reprocesos="NUEVO";
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

                $consultarTiempoRadicado = sqlsrv_query($link, "SELECT * FROM simulaciones_fdc WHERE id = (SELECT max(id) FROM simulaciones_fdc WHERE estado = 5 and id_simulacion = '".$fila["id_simulacion"]."')");
                if (sqlsrv_num_rows($consultarTiempoRadicado) > 0) {
                    $resTiempoRadicado = sqlsrv_fetch_array($consultarTiempoRadicado);
                    $tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($resTiempoRadicado["fecha_creacion"]);
                    $fecha_prospeccion = $resTiempoRadicado["fecha_creacion"];
                }else{
                    $tiempo_prospeccion = strtotime(date("Y-m-d H:i:s")) - strtotime($fila["fecha_radicado"]);
                    $fecha_prospeccion = $fila["fecha_radicado"];
                }

                $tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);						
                $tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);					
                $tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
                
                if ($tiempo_prospeccion_horas) {
                    $tiempo_prospeccion_letras .= $tiempo_prospeccion_horas."h ";
                }

                $tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos."m ";
                $tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos."s";
                
                //$id_analista_riesgo_operativo=$fila["id_analista_riesgo_operativo"];

                            
                $data[] = array("id_simulacion" => $fila["id_simulacion"],
                    "cedula"=>'<a href="simulador_externos.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc2">'.$fila["cedula"].'</a>',
                    "nombre"=>$fila["nombre"],
                    "pagaduria"=>$fila["pagaduria"],
                    "comercial"=>($fila["nombre_comercial"]." ".$fila["apellido"]),
                    "oficina"=>$fila["oficina"],
                    "fecha_radicado"=>$fecha_prospeccion,
                    "tiempo_prospeccion"=>$tiempo_prospeccion_letras,
                    "estado"=>$reprocesos,
                    "frente_cliente"=>$imagen_frente,
                    "adjuntos"=>'<a href="adjuntos.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc&page='.$_REQUEST["page"].'"><img src="../images/adjuntar.png" title="Adjuntos"></a>',
                    
                    
                    "consulta" => $consultarOficinasAnalistas,
                    "tipo_comercial2"=>$fila["tipo_comercial2"]
                );
                            
                    
                                
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data,
                "session" => $_SESSION["S_IDUSUARIO"]
            );
            
            echo json_encode($results); 
            break;
        
        default:
            # code...
            break;
    }

?>