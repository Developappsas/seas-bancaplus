<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

$separador = ";";

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

header('Content-type: text/csv');
header("Content-Disposition: attachment; filename=Coltefinanciera - Datos Desembolsos " . $venta["nro_venta"] . ".csv");
header("Pragma: no-cache");
header("Expires: 0");

if (!$_REQUEST["ext"]) {
	$queryDB = "SELECT si.nro_libranza, si.cedula, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_vencimiento, si.plazo, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, FORMAT(vd.fecha_primer_pago, 'dd/MM/yyyy') as fecha_vencimiento_primera_cuota_vendida, si.valor_credito, si.tasa_interes, cu2.seguro, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	$queryDB .= " group by si.nro_libranza, si.cedula, si.fecha_desembolso, si.plazo, vd.cuota_hasta, vd.cuota_desde, vd.fecha_primer_pago, si.valor_credito, si.tasa_interes, cu2.seguro";
} else {
	$queryDB = "SELECT si.nro_libranza, si.cedula, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_vencimiento, si.plazo, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, FORMAT(vd.fecha_primer_pago, 'd/m/Y') as fecha_vencimiento_primera_cuota_vendida, si.valor_credito, si.tasa_interes, cu2.seguro, SUM(cu.capital) as saldo_capital from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas" . $sufijo . " cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	$queryDB .= " group by si.nro_libranza, si.cedula, si.fecha_desembolso, si.plazo, vd.cuota_hasta, vd.cuota_desde, vd.fecha_primer_pago, si.valor_credito, si.tasa_interes, cu2.seguro";
}

$queryDB .= " order by abs(si.cedula), vd.id_ventadetalle";

$rs = sqlsrv_query($link, $queryDB);


while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	$numero_pagare = trim(preg_replace("/\D/", "", $fila["nro_libranza"]));
	$nit_originador = "900387878";
	$numero_documento = $fila["cedula"];
	$nit_custodia = "900978303";
	$fecha_desembolso = $fila["fecha_desembolso"];
	$fecha_vencimiento = $fila["fecha_vencimiento"];

	$saldo_capital = $fila["saldo_capital"];

	if ($fila["cuotas_vendidas"] == $fila["plazo"])
		$saldo_capital = $fila["valor_credito"];

	$nro_flujos_descontar = "1";
	$fecha_vencimiento_primera_cuota_vendida = $fila["fecha_vencimiento_primera_cuota_vendida"];
	$total_cuotas = $fila["plazo"];
	$valor_prestado = $fila["valor_credito"];
	$interes_periodo_gracia = "0";
	$dias_gracia = "0";
	$tasa_interes_facial = round(((pow(1 + ($fila["tasa_interes"] / 100.00), 12)) - 1) * 100.00, 2);
	$valor_seguro = $fila["seguro"];
	$valor_otros_conceptos = "0";
	$numero_autorizacion_fenalco = ""; //No est� en la estructura que entreg� Coltefinanciera pero al parecer debe ir

	$registro = $numero_pagare . $separador . $nit_originador . $separador . $numero_documento . $separador . $nit_custodia . $separador . $fecha_desembolso . $separador . $fecha_vencimiento . $separador . $saldo_capital . $separador . $nro_flujos_descontar . $separador . $fecha_vencimiento_primera_cuota_vendida . $separador . $total_cuotas . $separador . $valor_prestado . $separador . $interes_periodo_gracia . $separador . $dias_gracia . $separador . $tasa_interes_facial . $separador . $valor_seguro . $separador . $valor_otros_conceptos . $separador . $numero_autorizacion_fenalco;

	echo $registro . "\r\n";
}
?>