<?php 

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Cartera.xls");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_POST['user']) || !isset($_POST['password'])){
	echo "debe loguearse";
	exit();
}

include ('../functions.php'); 
$link = conectar();

$querySession = sqlsrv_query($link, "SELECT usr FROM proveedores WHERE usr = '".$_POST['user']."' AND passwd = MD5('".$_POST['password']."')");

if(!$querySession || sqlsrv_num_rows($querySession) == 0){
	echo "error al loguearse";
	exit();
}
?>
<table border="0">
<tr>
	<th>Tipo Cartera</th>
	<th>ID</th>
	<th>Cedula</th>
	<th>F. Desembolso Inicial</th>
	<th>F. Desembolso Final</th>
	<th>Mes Prod</th>
	<th>F. Primera Cuota</th>
	<th>Nombre</th>
	<th>Direccion</th>
	<th>Telefono</th>
	<th>Celular</th>
	<th>E.mail</th>
	<th>No. Libranza</th>
	<th>Unidad de Negocio</th>
	<th>Tasa</th>
	<th>Cuota Total</th>
	<th>Cuota Corriente</th>
	<th>Seguro</th>
	<th>Vr. Credito</th>
	<th>Saldo de Kapital</th>
	<?php if ($_POST["FUNC_BOLSAINCORPORACION"]) { ?><th>Bolsa Incorporacion</th><?php } ?>
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Plazo</th>
	<th>Incorporacion</th>
	<th>F. Venta</th>
	<th>F. Primer Vcto.</th>
	<th>Cuotas Vendidas</th>
	<th>Vr. Cuota Vendida</th>
	<th>Vr. Capital Vendido</th>
	<th>No. Venta</th>
	<th>Comprador</th>
	<th>Tipo Venta</th>
	<th>Caracteristica</th>
	<th>Estado</th>
	<th>Comprador Prepago</th>
	<th>F. Prepago</th>
	<th>Vr. Prepago</th>
	<th>Saldo Capital (PP)</th>
	<th>Intereses (PP)</th>
	<th>Seguro (PP)</th>
	<th>Cuotas Mora (PP)</th>
	<th>Interes Mora (PP)</th>
	<th>Gastos Cobranza (PP)</th>
	<th>Total Pagar (PP)</th>
	<th>No. Libranza Cancelacion x Retanqueo</th>
	<th>Vr. Cancelacion x Retanqueo</th>
	<th>Saldo Capital (RET)</th>
	<th>Intereses (RET)</th>
	<th>Seguro (RET)</th>
	<th>Cuotas Mora (RET)</th>
	<th>Interes Mora (RET)</th>
	<th>Gastos Cobranza (RET)</th>
	<th>Total Pagar (RET)</th>
	<th>Prepago Fondeador</th>
	<th>F. Primer Recaudo x Nomina</th>


	<th>Fecha Vencimiento</th>
	
<?php

$fecha_recaudo_tmp = "2014-04-01";

$fecha_recaudo = new DateTime($fecha_recaudo_tmp);

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
	$fecha_actual = new DateTime($_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-01");
	$fecha_corte_query = "'".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}
else {
	$fecha_actual = new DateTime(date('Y-m-01'));
	$fecha_corte_query = "CURDATE()";
}

$fecha_final = $fecha_actual->add(new DateInterval('P1M'));

while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m')) {

?>
	<th><?php echo $fecha_recaudo->format('Y-m') ?></th>
<?php

	$fecha_recaudo->add(new DateInterval('P1M'));
}

?>
	<th>Total Recaudado</th>
	<th>Cuotas Pagadas</th>
	<th>Cuotas Causadas</th>
	<th>Cuotas en Mora</th>
	<th>Interes en Mora</th>
	<th>Saldo en Mora</th>
	<th>Calificacion</th>
	<th>Fecha ult reca</th>
	<th>KP PLUS</th>
	<th>Saldo Seguro Causado</th>
	<th>Responsable Gestion Cobranza</th>
	<th>Puntaje Datacredito</th>
	<th>Tipo Credito</th>
	<th>Asesor</th>	
	<th>Zona</th>	
	<th>Formato Digital</th>
</tr>
<?php

if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL") {
	$queryDB = "select CONCAT(ase.nombre,' ',ase.apellido) as nombre_comercial,zo.nombre as zona_descripcion, CASE WHEN si.formato_digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital_descripcion,si.puntaje_datacredito,si.retanqueo1_libranza,si.retanqueo2_libranza,si.retanqueo3_libranza,si.resp_gestion_cobranza,'ORIGINACION' as tipo, si.id_simulacion, si.cedula, si.fecha_desembolso as fecha_desembolso, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, DATE_FORMAT(si.fecha_cartera, '%Y-%m') as mes_prod, si.fecha_primera_cuota, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre as unidad_negocio, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, fn_bolsa_pagos(si.id_simulacion, ".$fecha_corte_query.") - fn_bolsa_aplicaciones(si.id_simulacion, ".$fecha_corte_query.") as saldo_bolsa, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, 0 as cobro_adicional_en_cuota, si.pagaduria, si.plazo, cu2.seguro, CASE WHEN (SELECT tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion LIMIT 1) IS NOT NULL THEN 'SI' ELSE 'NO' END as incorporacion, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, ed.nombre as comprador_prepago, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, CASE WHEN si.prepagado_fondeador = '1' THEN 'SI' ELSE 'NO' END as prepagado_fondeador, fn_fecha_primer_recaudo(si.id_simulacion, 0, ".$fecha_corte_query.") as fecha_primer_recaudo, fn_total_recaudado_query(si.id_simulacion, 0, ".$fecha_corte_query.") as total_recaudado_query, si.fecha_creacion as fecha_creacion, fn_cuotas_causadas(si.id_simulacion, 0, ".$fecha_corte_query.") as cuotas_causadas, ca.nombre as caracteristica, pa.sector, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x 
		FROM simulaciones si 
		INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
		INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
		LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1'
		LEFT JOIN entidades_desembolso ed ON ed.id_entidad = si.id_compradorprep 
		LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica 
		LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
		LEFT JOIN oficinas ofi ON ofi.id_oficina = si.id_oficina
		LEFT JOIN zonas zo ON zo.id_zona = ofi.id_zona 
		LEFT JOIN usuarios ase ON ase.id_usuario=si.id_comercial
		WHERE (si.estado IN ('DES', 'CAN') OR (si.estado IN ('EST') AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN ('".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."',".$subestados_desembolso_nuevos_tesoreria.")))))";
	
	if ($_POST["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_POST["S_SECTOR"]."'";
	}
	
	if ($_REQUEST["unidad_negocio"]){	
		$queryDB .= " AND si.id_unidad_negocio = " . $_REQUEST["unidad_negocio"];
	}else{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_REQUEST["S_IDUNIDADNEGOCIO"].")";
	}
	
	if ($_REQUEST["cedula"]) {
		$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}
	
	if ($_REQUEST["sector"]) {
		$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
	}
	
	if ($_REQUEST["pagaduria"]) {
		$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}
	
	if ($_REQUEST["incorporacion"]) {
		$queryDB .= " AND (SELECT tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion LIMIT 1)";
		
		if ($_REQUEST["incorporacion"] == "SI")
			$queryDB .= " IS NOT NULL";
		else
			$queryDB .= " IS NULL";
	}
	
	if ($_REQUEST["estado"]) {
//		$queryDB .= " AND si.estado = '".$_REQUEST["estado"]."'";
	}
	
	if ($_REQUEST["calificacion"] != "") {
/*		if ($_REQUEST["calificacion"] == "-1")
			$queryDB .= " AND si.estado = 'CAN'";
		else
			$queryDB .= " AND si.estado <> 'CAN'";*/
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, si.pagaduria, si.plazo, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, cu2.seguro, pa.nombre";
}

if ($_REQUEST["tipo"] == "ALL") {
	$queryDB .= " UNION ";
}

if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL") {
	$queryDB .= "digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital_descripcion,si.puntaje_datacredito,si.retanqueo1_libranza,si.retanqueo2_libranza,si.retanqueo3_libranza, si.resp_gestion_cobranza,'ORIGINACION' as tipo, si.id_simulacion, si.cedula, si.fecha_desembolso as fecha_desembolso, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN dbo.fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE dbo.fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.fecha_primera_cuota, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre as unidad_negocio, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, dbo.fn_bolsa_pagos(si.id_simulacion, ".$fecha_corte_query.") - dbo.fn_bolsa_aplicaciones(si.id_simulacion, ".$fecha_corte_query.") as saldo_bolsa, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, 0 as cobro_adicional_en_cuota, si.pagaduria, si.plazo, cu2.seguro, CASE WHEN (SELECT TOP 1 tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion ) IS NOT NULL THEN 'SI' ELSE 'NO' END as incorporacion, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, ed.nombre as comprador_prepago, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, CASE WHEN si.prepagado_fondeador = '1' THEN 'SI' ELSE 'NO' END as prepagado_fondeador, dbo.fn_fecha_primer_recaudo(si.id_simulacion, 0, ".$fecha_corte_query.") as fecha_primer_recaudo, dbo.fn_total_recaudado_query(si.id_simulacion, 0, ".$fecha_corte_query.") as total_recaudado_query, si.fecha_creacion as fecha_creacion, dbo.fn_cuotas_causadas(si.id_simulacion, 0, ".$fecha_corte_query.") as cuotas_causadas, ca.nombre as caracteristica, pa.sector, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x 
	from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN entidades_desembolso ed ON ed.id_entidad = si.id_compradorprep LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion
	LEFT JOIN oficinas ofi ON ofi.id_oficina = si.id_oficina
		LEFT JOIN zonas zo ON zo.id_zona = ofi.id_zona 
		LEFT JOIN usuarios ase ON ase.id_usuario=si.id_comercial where (si.estado IN ('DES', 'CAN'))";
	
	if ($_POST["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_POST["S_SECTOR"]."'";
	}
	if ($_REQUEST["unidad_negocio"]){	
		$queryDB .= " AND si.id_unidad_negocio = " . $_REQUEST["unidad_negocio"];
	}else{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_REQUEST["S_IDUNIDADNEGOCIO"].")";
	}
	
	if ($_REQUEST["cedula"]) {
		$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}
	
	if ($_REQUEST["sector"]) {
		$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
	}
	
	if ($_REQUEST["pagaduria"]) {
		$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}
	
	if ($_REQUEST["incorporacion"]) {
		$queryDB .= " AND (SELECT tipo_recaudo from pagos_ext where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion LIMIT 1)";
		
		if ($_REQUEST["incorporacion"] == "SI")
			$queryDB .= " IS NOT NULL";
		else
			$queryDB .= " IS NULL";
	}
	
	if ($_REQUEST["estado"]) {
//		$queryDB .= " AND si.estado = '".$_REQUEST["estado"]."'";
	}
	
	if ($_REQUEST["calificacion"] != "") {
/*		if ($_REQUEST["calificacion"] == "-1")
			$queryDB .= " AND si.estado = 'CAN'";
		else
			$queryDB .= " AND si.estado <> 'CAN'";*/
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, si.pagaduria, si.plazo, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, cu2.seguro, pa.nombre, ase.nombre, ase.apellido, zo.nombre, si.formato_digital, si.puntaje_datacredito, si.retanqueo1_libranza, si.retanqueo2_libranza, si.retanqueo3_libranza, si.resp_gestion_cobranza, si.estado, si.fecha_primera_cuota, ed.nombre, si.prepagado_fondeador, si.fecha_creacion, ca.nombre, pa.sector";
}

//echo $queryDB;

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {

	$consultarUltimaCuotaPlanPagos="SELECT * FROM cuotas WHERE id_simulacion='".$fila["id_simulacion"]."' ORDER BY cuota DESC LIMIT 1";
	$queryUltimaCuotaPlanPagos=sqlsrv_query($link, $consultarUltimaCuotaPlanPagos);
	$resUltimaCuotaPlanPagos=sqlsrv_fetch_array($queryUltimaCuotaPlanPagos);

	$sufijo = "";
	
	if ($fila["tipo"] == "EXTERNA")
		$sufijo = "_ext";
	
	$fecha_venta = "";
	$fecha_primer_pago = "";
	$cuotas_vendidas = 0;
	$valor_cuota_vendida = 0;
	$valor_capital_vendido = 0;
	$nro_venta = "";
	$comprador = "";
	$tipo_venta = "";
	
	$queryDB = "select ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vc.valor_cuota as valor_cuota_vendida, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, SUM(cu.capital) as valor_capital_vendido from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN ventas_cuotas".$sufijo." vc ON vd.id_ventadetalle = vc.id_ventadetalle AND vc.cuota = vd.cuota_desde LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where si.id_simulacion = '".$fila["id_simulacion"]."' AND ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0' AND ve.fecha <= ".$fecha_corte_query." GROUP BY ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, vc.valor_cuota, ve.nro_venta, co.nombre, ve.modalidad_prima";
	
	$rs2 = sqlsrv_query($link, $queryDB);
	
	if (sqlsrv_num_rows($rs2)) {
		$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
		
		$fecha_venta = $fila2["fecha_venta"];
		$fecha_primer_pago = $fila2["fecha_primer_pago"];
		$cuotas_vendidas = $fila2["cuotas_vendidas"];
		$valor_cuota_vendida = $fila2["valor_cuota_vendida"];
		$valor_capital_vendido = $fila2["valor_capital_vendido"];
		
		if ($fila2["cuotas_vendidas"] == $fila["plazo"])
			$valor_capital_vendido = $fila["valor_credito"];
		
		$nro_venta = $fila2["nro_venta"];
		$comprador = $fila2["comprador"];
		
		$tipo_venta = "";
		
		switch($fila2["modalidad_prima"])
		{
			case "ANT":	$tipo_venta = "PRIMA ANTICIPADA";
						break;
			case "MDI":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN INTERESES";
						break;
			case "MDC":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN CUOTA";
						break;
		}
	}
	
	$comprador_prepago = $fila["comprador_prepago"];
	$fecha_prepago = $fila["fecha_prepago"];
	$valor_prepago = $fila["valor_prepago"];
	
	$valor_liquidacion = 0;
	$prepago_intereses = 0;
	$prepago_seguro = 0;
	$prepago_cuotasmora = 0;
	$prepago_gastoscobranza = 0;
	$prepago_totalpagar = 0;
	
	if ($fila["fecha_prepago"]) {
		$valor_liquidacion = $fila["valor_liquidacion"];
		$prepago_intereses = $fila["prepago_intereses"];
		$prepago_seguro = $fila["prepago_seguro"];
		$prepago_cuotasmora = $fila["prepago_cuotasmora"];
		$prepago_gastoscobranza = $fila["prepago_gastoscobranza"];
		$prepago_totalpagar = $fila["prepago_totalpagar"];
	}
	
	$retanqueo_id_simulacion_cancelacion = $fila["retanqueo_id_simulacion_cancelacion"];
	$retanqueo_libranza_cancelacion = $fila["retanqueo_libranza_cancelacion"];
	$retanqueo_valor_cancelacion = $fila["retanqueo_valor_cancelacion"];
	
	$retanqueo_valor_liquidacion = 0;
	$retanqueo_intereses = 0;
	$retanqueo_seguro = 0;
	$retanqueo_cuotasmora = 0;
	$retanqueo_gastoscobranza = 0;
	$retanqueo_totalpagar = 0;
	
	if ($fila["retanqueo_libranza_cancelacion"]) {
		$retanqueo_valor_liquidacion = $fila["retanqueo_valor_liquidacion"];
		$retanqueo_intereses = $fila["retanqueo_intereses"];
		$retanqueo_seguro = $fila["retanqueo_seguro"];
		$retanqueo_cuotasmora = $fila["retanqueo_cuotasmora"];
		$retanqueo_gastoscobranza = $fila["retanqueo_gastoscobranza"];
		$retanqueo_totalpagar = $fila["retanqueo_totalpagar"];
	}
	
	//Si est� filtrado por fecha de corte
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
		//Si est� filtrado por fecha de corte y no sale venta por el query, se comprueba si hay ventas recompradas
		if (!$nro_venta)
		{
			$queryDB = "select ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vc.valor_cuota as valor_cuota_vendida, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, SUM(cu.capital) as valor_capital_vendido from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN ventas_cuotas".$sufijo." vc ON vd.id_ventadetalle = vc.id_ventadetalle AND vc.cuota = vd.cuota_desde LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where si.id_simulacion = '".$fila["id_simulacion"]."' AND ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '1' AND ve.fecha <= ".$fecha_corte_query." GROUP BY ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, vc.valor_cuota, ve.nro_venta, co.nombre, ve.modalidad_prima order by ve.fecha DESC LIMIT 1";
			
			$rs2 = sqlsrv_query($link, $queryDB);
			
			if (sqlsrv_num_rows($rs2))
			{
				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				
				$fecha_venta = $fila2["fecha_venta"];
				$fecha_primer_pago = $fila2["fecha_primer_pago"];
				$cuotas_vendidas = $fila2["cuotas_vendidas"];
				$valor_cuota_vendida = $fila2["valor_cuota_vendida"];
				$valor_capital_vendido = $fila2["valor_capital_vendido"];
				
				if ($fila2["cuotas_vendidas"] == $fila["plazo"])
					$valor_capital_vendido = $fila["valor_credito"];
				
				$nro_venta = $fila2["nro_venta"];
				$comprador = $fila2["comprador"];
				
				$tipo_venta = "";
				
				switch($fila2["modalidad_prima"])
				{
					case "ANT":	$tipo_venta = "PRIMA ANTICIPADA";
								break;
					case "MDI":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN INTERESES";
								break;
					case "MDC":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN CUOTA";
								break;
				}
			}
		}
		
		if ($fila["fecha_prepago"] && $fila["fecha_prepago"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
		{
			$comprador_prepago = "";
			$fecha_prepago = "";
			$valor_prepago = 0;
			$valor_liquidacion = 0;
			$prepago_intereses = 0;
			$prepago_seguro = 0;
			$prepago_cuotasmora = 0;
			$prepago_gastoscobranza = 0;
			$prepago_totalpagar = 0;
			
			$fila["estado"] = "VIGENTE";
		}
		
		if ($retanqueo_id_simulacion_cancelacion && $fila["tipo"] == "ORIGINACION" )
		{
			$queryDB = "select MAX(fecha_giro) as fecha_desembolso_final from giros where id_simulacion = '".$retanqueo_id_simulacion_cancelacion."'";
			
			$rs2 = sqlsrv_query($link, $queryDB);
			
			$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
			
			if ($fila2["fecha_desembolso_final"] && $fila2["fecha_desembolso_final"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
			{
				$retanqueo_valor_cancelacion = 0;
				$retanqueo_valor_liquidacion = 0;
				$retanqueo_intereses = 0;
				$retanqueo_libranza_cancelacion = 0;
				$retanqueo_seguro = 0;
				$retanqueo_cuotasmora = 0;
				$retanqueo_gastoscobranza = 0;
				$retanqueo_totalpagar = 0;
				
				$fila["estado"] = "VIGENTE";
			}
		}
		
		if ($fila["estado"] == "CANCELADO" && !$fila["fecha_prepago"])
		{
			$queryDB = "select MAX(pa.fecha) as fecha_ultimo_recaudo from pagos".$sufijo." pa INNER JOIN pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pd.valor > 0 AND pa.tipo_recaudo NOT IN ('NOMINA_DEV', 'VENTANILLA_DEV')";
			
			$rs2 = sqlsrv_query($link, $queryDB);
			
			$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
			
			if ($fila2["fecha_ultimo_recaudo"] && $fila2["fecha_ultimo_recaudo"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
			{
				$fila["estado"] = "VIGENTE";
			}
		}
	}
	
	switch($fila["opcion_credito"]) {
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					$opcion_desembolso = $fila["opcion_desembolso_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					$opcion_desembolso = $fila["opcion_desembolso_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					$opcion_desembolso = $fila["opcion_desembolso_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					$opcion_desembolso = $fila["opcion_desembolso_cso"];
					break;
	}
	
	//Se calculan las cuotas_pagadas y las cuotas_mora al principio para saber si se muestra o no el registro cuando se filtra por calificacion. Luego se recalculan m�s abajo
	if ($fila["estado"] != "CANCELADO") {
		$cuotas_pagadas_query = number_format($fila["total_recaudado_query"] / $opcion_cuota, 2);
		
		if (number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2) > 0)
			$cuotas_mora = number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2);
		else
			$cuotas_mora = 0;
	}
	
	if ($_REQUEST["estado"] != "") {
		if (($_REQUEST["estado"] == "DES" && $fila["estado"] != "VIGENTE") || ($_REQUEST["estado"] == "CAN" && $fila["estado"] != "CANCELADO"))
		{
			continue;
		}
	}
	
	if ($_REQUEST["calificacion"] != "") {
		if (($_REQUEST["calificacion"] == "-1" && $fila["estado"] != "CANCELADO") || ($_REQUEST["calificacion"] != "-1" && (ceil($cuotas_mora) != $_REQUEST["calificacion"] || $fila["estado"] == "CANCELADO")))
		{
			continue;
		}
	}
	
	if ($fila["seguro"] || $fila["seguro"] == "0")
		$seguro = $fila["seguro"];
	else
		if (!$fila["sin_seguro"])
			$seguro = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100)));
		else
			$seguro = 0;

	$cuota_corriente = $opcion_cuota - $seguro - $fila["cobro_adicional_en_cuota"] ;
	
	$capital_recaudado = 0;

	$saldo_capital = 0;
	if ($fila["estado"] != "CANCELADO"){
		//Query anterior cambiado a solicitud de manuel pizarro
		$queryDB = "select SUM(capital + abono_capital) as capital_recaudado from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota <= '".floor($cuotas_pagadas_query)."' AND pagada = '1'";
		
		$rs2 = sqlsrv_query($link, $queryDB);
		
		$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
		
		if ($fila2["capital_recaudado"])
			$capital_recaudado = $fila2["capital_recaudado"];
		
		if (floor($cuotas_pagadas_query) != ceil($cuotas_pagadas_query)){
			$aplicado_a_ultima_cuota = $fila["total_recaudado_query"] - (floor($cuotas_pagadas_query) * $opcion_cuota);
			
			//Si capital no es mayor que cero significa que se realiz� en abono a capital y esa cuota no hace parte del plan de pagos regenerado
			$queryDB = "select * from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota = '".ceil($cuotas_pagadas_query)."' AND capital > 0";
			
			$rs3 = sqlsrv_query($link, $queryDB);
			
			if (sqlsrv_num_rows($rs3)){
				$fila3 = sqlsrv_fetch_array($rs3, SQLSRV_FETCH_ASSOC);
				
				if ($aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] > 0)
					$capital_recaudado += $aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] + $fila3["abono_capital"];
				else
					$capital_recaudado += $fila3["abono_capital"];
			}
		}
		
		$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
	}
	else{
		$saldo_capital = 0;
	}

	$tipo_crediton="";
	$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
			$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito);
			if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
				$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
				$queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
				$resComprasCC=sqlsrv_fetch_array($queryComprasCC);
				if ($resComprasCC["cuota"]>0){
					if ($fila["retanqueo1_libranza"]=="" || $fila["retanqueo2_libranza"]=="" || $fila["retanqueo3_libranza"]==""){
						$tipo_crediton="COMPRAS DE CARTERA";	
					}else{
						$tipo_crediton="COMPRAS CON RETANQUEO";	
					}
				}
				else
				{
					if ($resComprasCC["valor_pagar"]>0){
						$tipo_crediton="LIBRE CON SANEAMIENTO";	
					}else{
						if ($fila["retanqueo1_libranza"]<>"" || $fila["retanqueo2_libranza"]<>"" || $fila["retanqueo3_libranza"]<>""){
							$tipo_crediton="LIBRE INVERSION CON RETANQUEO";	
						}
					}
				}
			}else{
				$tipo_crediton="LIBRE INVERSION";
			}
?>
<tr>
	<td><?php echo $fila["tipo"] ?></td>
	<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["fecha_desembolso"] ?></td>
	<td><?php echo $fila["fecha_desembolso_final"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo $fila["fecha_primera_cuota"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo utf8_decode($fila["celular"]) ?></td>
	<td><?php echo utf8_decode($fila["email"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $cuota_corriente ?></td>
	<td><?php echo $seguro ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $saldo_capital ?></td>

	<?php if ($_POST["FUNC_BOLSAINCORPORACION"]) { ?><td><?php echo $fila["saldo_bolsa"] ?></td><?php } ?>
	
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo $fila["incorporacion"] ?></td>
	<td><?php echo $fecha_venta ?></td>
	<td><?php echo $fecha_primer_pago ?></td>
	<td><?php echo $cuotas_vendidas ?></td>
	<td><?php echo $valor_cuota_vendida ?></td>
	<td><?php echo $valor_capital_vendido ?></td>
	<td><?php echo $nro_venta ?></td>
	<td><?php echo utf8_decode($comprador) ?></td>
	<td><?php echo $tipo_venta ?></td>
	<td><?php echo utf8_decode($fila["caracteristica"]) ?></td>
	<td><?php echo $fila["estado"] ?></td>
	<td><?php echo utf8_decode($comprador_prepago) ?></td>
	<td><?php echo $fecha_prepago ?></td>
	<td><?php echo $valor_prepago ?></td>
	<td><?php echo $valor_liquidacion ?></td>
	<td><?php echo $prepago_intereses ?></td>
	<td><?php echo $prepago_seguro ?></td>
	<td><?php echo $prepago_cuotasmora ?></td>
	<td><?php echo ($prepago_cuotasmora*$prepago_intereses) ?></td>
	<td><?php echo $prepago_gastoscobranza ?></td>
	<td><?php echo $prepago_totalpagar ?></td>
	<td><?php echo $retanqueo_libranza_cancelacion ?></td>
	<td><?php echo $retanqueo_valor_cancelacion ?></td>
	<td><?php echo $retanqueo_valor_liquidacion ?></td>
	<td><?php echo $retanqueo_intereses ?></td>
	<td><?php echo $retanqueo_seguro ?></td>
	<td><?php echo $retanqueo_cuotasmora ?></td>
	<td><?php echo ($retanqueo_cuotasmora*$retanqueo_intereses) ?></td>
	<td><?php echo $retanqueo_gastoscobranza ?></td>
	<td><?php echo $retanqueo_totalpagar ?></td>
	<td><?php echo $fila["prepagado_fondeador"] ?></td>
	<td><?php echo $fila["fecha_primer_recaudo"] ?></td>
	<td><?php echo $resUltimaCuotaPlanPagos["fecha"] ?></td>	
<?php

	$queryDB = "select DATE_FORMAT(pa.fecha, '%Y-%m') as fecha, SUM(pd.valor) as valor_recaudado from pagos".$sufijo." pa inner join pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pa.fecha <= ".$fecha_corte_query." group by DATE_FORMAT(pa.fecha, '%Y-%m') order by DATE_FORMAT(pa.fecha, '%Y-%m')";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	if (sqlsrv_num_rows($rs1)) {
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);		
		$fecha = $fila1["fecha"];
	}
	
	$j = 0;	
	$total_recaudado = 0;	
	$fecha_recaudo_tmp = "2014-04-01";	
	$fecha_recaudo = new DateTime($fecha_recaudo_tmp);	
	while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m')) {
		if ($fecha_recaudo->format('Y-m') == $fecha) {
			$valor_recaudado = $fila1["valor_recaudado"];			
			$total_recaudado += $fila1["valor_recaudado"];			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);			
			$fecha = $fila1["fecha"];
		} else {
			$valor_recaudado = "";
		}
		
?>
	<td><?php echo $valor_recaudado ?></td>
<?php
		
		if ($valor_recaudado){
			$total_valor_recaudado[$j] += $valor_recaudado;
		}
		
		$fecha_recaudo->add(new DateInterval('P1M'));
		
		$j++;
	}
	
	if ($fila["estado"] == "CANCELADO") {
		$cuotas_pagadas = "NA";
		$cuotas_causadas = "NA";
		$cuotas_mora = "NA";
		$saldo_mora = "NA";
		$seguro_causado = "NA";
	} else {
		$cuotas_pagadas = number_format($total_recaudado / $opcion_cuota, 2);
		$cuotas_causadas = $fila["cuotas_causadas"];
		
		if (number_format($cuotas_causadas - $cuotas_pagadas, 2) > 0) {
			$cuotas_mora = number_format($cuotas_causadas - $cuotas_pagadas, 2);			
			$saldo_mora = ($cuotas_causadas * $opcion_cuota) - $total_recaudado;
		} else {
			$cuotas_mora = 0;
			$saldo_mora = 0;
		}
		
		if ($fila["sin_seguro_x"] == "SI"){
			$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;
		} else{
			$seguro_causado = 0;
		}
	}
	
	if ($fila["estado"] == "CANCELADO")
		$calificacion = "CANCELADO";
	else if ($cuotas_mora) {
		$limite1_calificacion = (ceil($cuotas_mora) * 30) - 29;
		$limite2_calificacion = ceil($cuotas_mora) * 30;
		
		$calificacion = $limite1_calificacion." a ".$limite2_calificacion;
	} else
		$calificacion = "AL DIA";
	
	
?>
	<td><?php echo $total_recaudado ?></td>
	<td><?php echo $cuotas_pagadas ?></td>
	<td><?php echo $cuotas_causadas ?></td>
	<td><?php echo $cuotas_mora ?></td>
	<td><?php echo ((number_format($saldo_capital * $fila["tasa_interes"] / 100.00, 0, ".", ","))*$cuotas_mora) ?></td>
	<td><?php echo $saldo_mora ?></td>
	<td><?php echo $calificacion ?></td>
	
	
<?php
	$queryDB1 = "select DATE_FORMAT(pa.fecha, '%Y-%m-%d') as fecha from pagos".$sufijo." pa inner join pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pa.fecha < ".$fecha_corte_query." group by DATE_FORMAT(pa.fecha, '%Y-%m-%d') order by DATE_FORMAT(pa.fecha, '%Y-%m-%d') desc limit 1";
	
	$rs2 = sqlsrv_query($link, $queryDB1);
	
	if (sqlsrv_num_rows($rs2)) {
		$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
		
		$fecharecau = $fila2["fecha"];
	}
	else 
		$fecharecau="1900-01-01";
?>
	
	<td><?php echo $fecharecau ?></td>
	<td><?php echo $fila["sin_seguro_x"] ?></td>
	<td><?php echo $seguro_causado ?></td>
	<?php
	if ($fila["resp_gestion_cobranza"]=="") {
		$cobranza="NO APLICA";
	}else{
		$consultaCobranza="SELECT * FROM resp_gestion_cobros WHERE id_resp_cobros='".$fila["resp_gestion_cobranza"]."'";
		$queryCobranza=sqlsrv_query($link, $consultaCobranza,$con);
		$resCobranza=sqlsrv_fetch_array($queryCobranza);
		$cobranza=$resCobranza." - ".$fila["detalle_resp_gestion_cobranza"];
	}
	?>
	<td><?php echo $cobranza ?></td>
	<td><?php echo $fila["puntaje_datacredito"] ?></td>
	<td><?php echo $tipo_crediton ?></td>
	<td><?php echo $fila["nombre_comercial"]; ?></td>
	<td><?php echo $fila["zona_descripcion"]; ?></td>
	<td><?php echo $fila["formato_digital_descripcion"]; ?></td>
</tr>
<?php

	$total_opcion_cuota += $opcion_cuota;
	$total_cuota_corriente += $cuota_corriente;
	$total_seguro += $seguro;
	$total_valor_credito += $fila["valor_credito"];
	$total_saldo_capital += $saldo_capital;
	$total_saldo_bolsa += $fila["saldo_bolsa"];
	
	if ($valor_cuota_vendida)
		$total_valor_cuota_vendida += $valor_cuota_vendida;
	
	if ($valor_capital_vendido)
		$total_valor_capital_vendido += $valor_capital_vendido;
	
	$total_valor_prepago += $valor_prepago;
	$total_valor_liquidacion += $valor_liquidacion;
	$total_prepago_intereses += $prepago_intereses;
	$total_prepago_seguro += $prepago_seguro;
	$total_prepago_gastoscobranza += $prepago_gastoscobranza;
	$total_prepago_totalpagar += $prepago_totalpagar;
	
	if ($retanqueo_valor_cancelacion)
		$total_retanqueo_valor_cancelacion += $retanqueo_valor_cancelacion;
	
	$total_retanqueo_valor_liquidacion += $retanqueo_valor_liquidacion;
	$total_retanqueo_intereses += $retanqueo_intereses;
	$total_retanqueo_seguro += $retanqueo_seguro;
	$total_retanqueo_gastoscobranza += $retanqueo_gastoscobranza;
	$total_retanqueo_totalpagar += $retanqueo_totalpagar;

	$total_total_recaudado += $total_recaudado;
	
	if ($saldo_mora != "NA")
		$total_saldo_mora += $saldo_mora;
	
	if ($seguro_causado != "NA")
		$total_seguro_causado += $seguro_causado;
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="15"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_cuota ?></b></td>
	<td><b><?php echo $total_cuota_corriente ?></b></td>
	<td><b><?php echo $total_seguro ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td><b><?php echo $total_saldo_capital ?></b></td>
	<?php if ($_POST["FUNC_BOLSAINCORPORACION"]) { ?><td><b><?php echo $total_saldo_bolsa ?></b></td><?php } ?>
	<td colspan="7">&nbsp;</td>
	<td><b><?php echo $total_valor_cuota_vendida ?></b></td>
	<td><b><?php echo $total_valor_capital_vendido ?></b></td>
	<td colspan="7">&nbsp;</td>
	<td><b><?php echo $total_valor_prepago ?></b></td>
	<td><b><?php echo $total_valor_liquidacion ?></b></td>
	<td><b><?php echo $total_prepago_intereses ?></b></td>
	<td><b><?php echo $total_prepago_seguro ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_prepago_gastoscobranza ?></b></td>
	<td><b><?php echo $total_prepago_totalpagar ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_retanqueo_valor_cancelacion ?></b></td>
	<td><b><?php echo $total_retanqueo_valor_liquidacion ?></b></td>
	<td><b><?php echo $total_retanqueo_intereses ?></b></td>
	<td><b><?php echo $total_retanqueo_seguro ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_retanqueo_gastoscobranza ?></b></td>
	<td><b><?php echo $total_retanqueo_totalpagar ?></b></td>
	<td colspan="2">&nbsp;</td>
<?php

for ($i = 0; $i < $j; $i++) {

?>
	<td><b><?php echo $total_valor_recaudado[$i] ?></b></td>
<?php

}

?>
	<td><b><?php echo $total_total_recaudado ?></b></td>
	<td colspan="3">&nbsp;</td>
	<td><b><?php echo $total_saldo_mora ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_seguro_causado ?></b></td>
</tr>
</table>

