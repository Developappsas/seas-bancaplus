<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ComprasCartera.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_REQUEST["id_simulacion"]){
	if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")){
		exit;
	}
}
else{
	if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_SUBTIPO"] == "ANALISTA_VISADO" || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA" || $_SESSION["S_SUBTIPO"] == "COORD_VISADO")
	{
		exit;
	}
}

$link = conectar();



?>
<table border="0">
	<tr>
		<th>ID Simulacion</th>
		<th>Cedula</th>
		<th>Nombre</th>
		<th>No. Libranza</th>
		<th>Tasa</th>
		<th>Pagaduria</th>
		<th>Oficina</th>
		<th>Entidad</th>
		<th>Vr. Pagar</th>
		<th>F. Giro</th>
		<th>F. Certificacion</th>
		<th>Pagada</th>
		<th>Subestado</th>
		<th>F. Tesoreria Final</th>
		<!--<th>Tipo Pago</th>-->
	</tr>
	<?php

	$queryDB = "(SELECT gi.fecha_giro as fecha_pagado,si.tasa_interes,si.nro_libranza,se.nombre AS subestado,'' AS fecha_vencimiento, CASE WHEN gi.clasificacion='DSC' THEN 'CLIENTE' ELSE gi.beneficiario END as nombre_entidad,si.id_simulacion,si.nro_libranza,si.cedula,si.nombre,pa.nombre AS pagaduria,ofi.nombre AS nombre_oficina,gi.valor_girar as valor, CASE WHEN gi.clasificacion='DSC' THEN 'DESEMBOLSO CLIENTE' ELSE 'COMPRA DE CARTERA' END AS tipo_giro
	, (SELECT top 1 fecha_creacion FROM simulaciones_subestados WHERE id_subestado IN (46,48) AND id_simulacion = si.id_simulacion ORDER BY id_simulacionsubestado DESC ) AS fecha_tesoreria_final,'SI' as pagada
	FROM simulaciones si 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
	INNER JOIN giros gi ON gi.id_simulacion=si.id_simulacion 
	LEFT JOIN subestados se ON se.id_subestado=si.id_subestado
	WHERE ";
	
	$queryDB .= " si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	if ($_REQUEST["id_simulacion"]) {
		$queryDB .= " AND si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	}

	if ($_REQUEST["pagaduria"]) {
		$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}

	if ($_REQUEST["cedula"]) {
		$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}


	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) {
		$queryDB .= " AND gi.fecha_giro >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}

	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND gi.fecha_giro <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}

	$queryDB.=")";
	
	$queryDB2=" (SELECT '' as fecha_pagado,si.tasa_interes,si.nro_libranza,se.nombre AS subestado,ag.fecha_vencimiento AS fecha_vencimiento,ag.entidad as nombre_entidad,si.id_simulacion,si.nro_libranza,si.cedula,si.nombre,pa.nombre AS pagaduria,ofi.nombre AS nombre_oficina,scc.valor_pagar  as valor,  'COMPRA DE CARTERA' AS tipo_giro
	, (SELECT top 1 fecha_creacion FROM simulaciones_subestados WHERE id_subestado IN (46,48) AND id_simulacion = si.id_simulacion ORDER BY id_simulacionsubestado DESC ) AS fecha_tesoreria_final,'NO' as pagada
	FROM simulaciones si 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
	INNER JOIN agenda ag ON ag.id_simulacion=si.id_simulacion 
	INNER JOIN tesoreria_cc tcc ON tcc.id_simulacion=si.id_simulacion AND tcc.consecutivo=ag.consecutivo
	INNER JOIN simulaciones_comprascartera scc ON scc.id_simulacion=si.id_simulacion AND scc.consecutivo=ag.consecutivo 
	LEFT JOIN subestados se ON se.id_subestado=si.id_subestado
	WHERE tcc.pagada=0";

	$queryDB2 .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	if ($_REQUEST["id_simulacion"]) {
		$queryDB2 .= " AND si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	}

	if ($_REQUEST["pagaduria"]) {
		$queryDB2 .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}

	if ($_REQUEST["cedula"]) {
		$queryDB2 .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}


	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]) {
		$queryDB2 .= " AND ag.fecha_vencimiento >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}

	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB2 .= " AND ag.fecha_vencimiento <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$queryDB2 .= ")";

	//echo $queryDB;
	$queryEjecutar="";
	if (!$_REQUEST["estado"])
	{ 
		$queryEjecutar=$queryDB." UNION ".$queryDB2;
	}
	else if ($_REQUEST["estado"] == "SI"){
		$queryEjecutar=$queryDB;
	}else if ($_REQUEST["estado"] == "NO"){
		$queryEjecutar=$queryDB2;
	}



	$rs = sqlsrv_query($link, $queryEjecutar);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		switch($fila["opcion_credito"]) {
			case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"]; $opcion_desembolso = $fila["opcion_desembolso_cli"]; break;
			case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"]; $opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
			case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"]; $opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
			case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"]; $opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
		}


					?>
					<tr>
						<td><?php echo $fila["id_simulacion"] ?></td>						
						<td><?php echo $fila["cedula"] ?></td>
						<td><?php echo utf8_decode($fila["nombre"]) ?></td>
						<td><?php echo $fila["nro_libranza"] ?></td>
						<td><?php echo $fila["tasa_interes"] ?></td>
						<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
						<td><?php echo utf8_decode($fila["nombre_oficina"]) ?></td>
						<td><?php echo utf8_decode($fila["nombre_entidad"]) ?></td>
						<td><?php echo $fila["valor"] ?></td>
						<td><?php echo $fila["fecha_pagado"] ?></td>
						<td><?php echo $fila["fecha_vencimiento"] ?></td>
						<td><?php echo $fila["pagada"] ?></td>
						<td><?php echo $fila["subestado"] ?></td>
				
						<td><?php echo $fila["fecha_tesoreria_final"] ?></td>
						<!--<td><?php echo $fila["tipo_giro"] ?></td>-->
					</tr>
					<?php

					$total_cuota_retenida += $cuota_retenida;
					$total_valor_pagar += $fila2["valor_pagar"];
				
			}
		


	
	?>
	<tr>
		<td colspan="9"><b>TOTALES</b></td>
		<td><b><?php echo $total_cuota_retenida ?></b></td>
		<td><b><?php echo $total_valor_pagar ?></b></td>
	</tr>
</table>

