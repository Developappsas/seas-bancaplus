<?php 
else if ($_POST["exe"]=="consultarRequerimientos")
{
		$queryDB = "SELECT re.*, si.id_analista_riesgo_crediticio, si.id_analista_riesgo_crediticio, si.id_analista_riesgo_operativo, si.cedula, si.nombre, si.nro_libranza, si.pagaduria, si.valor_credito, ti.nombre as tipo, ar.nombre as area from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_reqexcep ti ON re.id_tipo = ti.id_tipo INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

		if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
		{
			$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
		}

		$queryDB .= " where re.estado != 'ANULADO'";

		if ($_SESSION["S_SECTOR"])
		{
			$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL")
		{
			$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
		}
		else
		{
			$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
		}

		if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
		{
			$queryDB .= " AND (CASE re.id_area ";
			$queryDB .= " 	WHEN '".$area_credito."' THEN";
			$queryDB .= " 		CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL THEN";
			$queryDB .= " 			CASE WHEN si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
			$queryDB .= " 		ELSE ";
			$queryDB .= " 			CASE WHEN si.id_analista_riesgo_operativo IS NOT NULL THEN";
			$queryDB .= " 				CASE WHEN si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
			$queryDB .= " 			ELSE 1 = 0 END";
			$queryDB .= " 		END";
		/*	$queryDB .= " 	WHEN '".$area_visado."' THEN";
			$queryDB .= " 		CASE WHEN si.pagaduria IN (select pa.nombre from pagadurias pa INNER JOIN pagadurias_usuarios_visado puv ON puv.id_pagaduria = pa.id_pagaduria AND puv.id_usuario = '".$_SESSION["S_IDUSUARIO"]."') THEN 1 = 1";
			$queryDB .= " 		ELSE 1 = 0 END";*/
		/*	$queryDB .= " 	WHEN '".$area_gestion_comercial."' THEN";
			$queryDB .= " 		CASE WHEN si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1";
			$queryDB .= " 		ELSE 1 = 0 END";*/
			$queryDB .= " 	ELSE 1 = 1 END";
			$queryDB .= " )";
		}


		if ($_REQUEST["descripcion_busqueda"]) {
			$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];			
			$queryDB .= " AND (si.cedula = '".$descripcion_busqueda."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($descripcion_busqueda))."%' OR si.nro_libranza = '".$descripcion_busqueda."')";
		}

		if ($_REQUEST["reqexcepb"]) {
			$reqexcepb = $_REQUEST["reqexcepb"];
			$queryDB .= " AND re.reqexcep = '".$reqexcepb."'";
		}

		if ($_REQUEST["id_tipob"]) {
			$id_tipob = $_REQUEST["id_tipob"]; 
			$queryDB .= " AND re.id_tipo = '".$id_tipob."'";
		}

		if ($_REQUEST["id_areab"]) {
			$id_areab = $_REQUEST["id_areab"];			
			$queryDB .= " AND re.id_area = '".$id_areab."'";			
		}

		if ($_REQUEST["estadob"]) {
			$estadob = $_REQUEST["estadob"];
			$queryDB .= " AND re.estado = '".$estadob."'";
		} else {
			$queryDB .= " AND re.estado = 'PENDIENTE'";
		}

		$queryDB .= " order by re.id_reqexcep";
		echo $queryDB;
		$queryReqEx = sqlsrv_query($queryDB, $link, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		
		while ($resReqEx = sqlsrv_fetch_array($queryReqEx)){
			$data[]=array("id_simulacion"=>$fila["id_simulacion"],
			"cedula"=>'<a href="simulador.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc">'.$fila["cedula"].'</a>',
			"nombre"=>$fila["nombre"],
			"pagaduria"=>$fila["pagaduria"],
			"comercial"=>($fila["nombre_comercial"]." ".$fila["apellido"]),
			"oficina"=>$fila["oficina"],
			"fecha_radicado"=>$fecha_prospeccion,
			"tiempo_prospeccion"=>$tiempo_prospeccion_letras,
			"estado"=>$reprocesos,
			"frente_cliente"=>$imagen_frente,
			"adjuntos"=>'<a href="adjuntos.php?id_simulacion='.$fila["id_simulacion"].'&descripcion_busqueda='.$descripcion_busqueda.'&sectorb='.$sectorb.'&pagaduriab='.$pagaduriab.'&id_comercialb='.$id_comercialb.'&decisionb='.$decisionb.'&id_oficinab='.$id_oficinab.'&visualizarb='.$visualizarb.'&buscar='.$_REQUEST["buscar"].'&back=pilotofdc&page='.$_REQUEST["page"].'"><img src="../images/adjuntar.png" title="Adjuntos"></a>',
			"stop"=>$btnDetener,
			"hist_est"=>"<div class='badge-success' id='btnModalEstados' name='".$fila["id_simulacion"]."' type='button' class='open-modal' data-open='modal1'><center><img src='../images/solicitud.png' title='Estados'></center></div>",
			"hist_proc"=>"<div class='badge-success' id='btnModalHistorial' name='".$fila["id_simulacion"]."' type='button' class='open-modal' data-open='modal1'><center><img src='../images/proceso.png' title='Historial'></center></div>",
			"analista_asignado"=>$analistas,
			"nombre_usuario_asignado"=>$nombreUsuarioSeleccionado)
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data
		);
		
		echo json_encode($results);
	}
?>