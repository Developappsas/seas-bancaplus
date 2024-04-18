<?php
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

$separador = ";";

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "select * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

// header('Content-type: text/csv');
// header("Content-Disposition: attachment; filename=Coltefinanciera - Datos Adicionales ".$venta["nro_venta"].".csv");
// header("Pragma: no-cache");
// header("Expires: 0");

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT si.nro_libranza, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vd.cuota_desde, si.plazo, si.valor_credito, pp.nit as nit_pagaduria, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, FORMAT(vd.fecha_primer_pago, 'dd/MM/yyyy') as fecha_vencimiento_primera_cuota_vendida, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_vencimiento, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso,FORMAT(ve.fecha, 'dd/MM/yyyy') as fecha_venta, DATEDIFF(day, ve.fecha, eomonth(DATEADD(month, vd.cuota_desde -1, EOMONTH(DATEADD(MONTH, -1, si.fecha_primera_cuota)))) ) as dias_causados,si.tasa_interes, cu2.capital as capital_primera_cuota_vendida, cu2.interes as interes_primera_cuota_vendida, cu2.seguro, FORMAT(EOMONTh(DATEADD(MONTH,-1,  si.fecha_primera_cuota)), 'dd/MM/yyyy') as fecha_primera_cuota, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = vd.cuota_desde where vd.id_venta ='".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
	
	$queryDB .= " group by si.nro_libranza, vd.cuota_hasta, vd.cuota_desde, si.plazo, si.valor_credito, pp.nit, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, vd.fecha_primer_pago, si.fecha_desembolso, ve.fecha, si.tasa_interes, cu2.capital, cu2.interes, cu2.seguro, si.fecha_primera_cuota";
} else {
	$queryDB = "SELECT si.nro_libranza, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vd.cuota_desde, si.plazo, si.valor_credito, pp.nit as nit_pagaduria, si.opcion_credito, si.opcion_cuota_cso, FORMAT(vd.fecha_primer_pago, 'dd/MM/yyyy') as fecha_vencimiento_primera_cuota_vendida, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_vencimiento, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso, FORMAT(ve.fecha, 'dd/MM/yyyy') as fecha_venta, DATEDIFF(ve.fecha, LAST_DAY(DATE_ADD(LAST_DAY(DATE_ADD(si.fecha_primera_cuota, INTERVAL -1 MONTH)), INTERVAL vd.cuota_desde - 1 MONTH))) as dias_causados, si.tasa_interes, cu2.capital as capital_primera_cuota_vendida, cu2.interes as interes_primera_cuota_vendida, cu2.seguro, FORMAT(LAST_DAY(DATE_ADD(si.fecha_primera_cuota, INTERVAL -1 MONTH)), 'dd/MM/yyyy') as fecha_primera_cuota, SUM(cu.capital) as saldo_capital from ventas_detalle".$sufijo." vd INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas".$sufijo." cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = vd.cuota_desde where vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	$queryDB .= " group by si.nro_libranza, vd.cuota_hasta, vd.cuota_desde, si.plazo, si.valor_credito, pp.nit, si.opcion_credito, si.opcion_cuota_cso, vd.fecha_primer_pago, si.fecha_desembolso, ve.fecha, si.tasa_interes, cu2.capital, cu2.interes, cu2.seguro, si.fecha_primera_cuota";
}

$queryDB .= " order by abs(si.cedula), vd.id_ventadetalle";

$rs = sqlsrv_query($link, $queryDB);


while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	$dias_causados = $fila["dias_causados"];
	
	$numero_pagare = trim(preg_replace("/\D/", "", $fila["nro_libranza"]));
	$nit_originador = "900387878";
	
	$saldo_capital = $fila["saldo_capital"];
	
	if ($fila["cuotas_vendidas"] == $fila["plazo"])
		$saldo_capital = $fila["valor_credito"];
	
	$saldo_interes_causado = round(($saldo_capital * ($fila["tasa_interes"] / 100.00) / 30) * $fila["dias_causados"], 0);
	
	$nit_pagaduria = $fila["nit_pagaduria"];
	$cuotas_pendientes = $fila["cuotas_vendidas"];
	$cuotas_canceladas = $fila["cuota_desde"] - 1;
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}
	
	$valor_cuota_actual = $opcion_cuota - $fila["seguro"];
	$fecha_vencimiento_primera_cuota_vendida = $fila["fecha_vencimiento_primera_cuota_vendida"];
	$fecha_vencimiento = $fila["fecha_vencimiento"];
	$fecha_desembolso = $fila["fecha_desembolso"];
	$total_cuotas = $fila["plazo"];
	$fecha_ult_causacion = $fila["fecha_venta"];
	$tasa_interes_facial = round(((pow(1 + ($fila["tasa_interes"] / 100.00), 12)) - 1) * 100.00, 2);
	$nro_cuotas_vencidas = "0";
	$capital_primera_cuota_vendida = $fila["capital_primera_cuota_vendida"];
	$interes_primera_cuota_vendida = $fila["interes_primera_cuota_vendida"];
	$total_capital_vencido = "0";
	$total_interes_vencido = "0";
	$dias_mora = "0";
	$fecha_ini_mora = "";
	$valor_seguro = $fila["seguro"];
	$valor_seguro_vehiculo = "0";
	$valor_total_seguro = "0";
	$valor_total_seguro_vehiculo = "0";
	$seguro_primera_cuota_vencida = "0";
	$seguro_vehiculo_primera_cuota_vencida = "0";
	$fecha_inicial_primera_cuota = $fila["fecha_primera_cuota"];
	$num_renovaciones = "0";
	$valor_cuota_original = $valor_cuota_actual;
	$tasa_actual = $tasa_interes_facial;
	
	$registro = $dias_causados.$separador.$numero_pagare.$separador.$nit_originador.$separador.$saldo_capital.$separador.$saldo_interes_causado.$separador.$nit_pagaduria.$separador.$cuotas_pendientes.$separador.$cuotas_canceladas.$separador.$valor_cuota_actual.$separador.$fecha_vencimiento_primera_cuota_vendida.$separador.$fecha_vencimiento.$separador.$fecha_desembolso.$separador.$total_cuotas.$separador.$fecha_ult_causacion.$separador.$tasa_interes_facial.$separador.$nro_cuotas_vencidas.$separador.$capital_primera_cuota_vendida.$separador.$interes_primera_cuota_vendida.$separador.$total_capital_vencido.$separador.$total_interes_vencido.$separador.$dias_mora.$separador.$fecha_ini_mora.$separador.$valor_seguro.$separador.$valor_seguro_vehiculo.$separador.$valor_total_seguro.$separador.$valor_total_seguro_vehiculo.$separador.$seguro_primera_cuota_vencida.$separador.$seguro_vehiculo_primera_cuota_vencida.$separador.$fecha_inicial_primera_cuota.$separador.$num_renovaciones.$separador.$valor_cuota_original.$separador.$tasa_actual;
	
	echo $registro."\r\n";
}
?>