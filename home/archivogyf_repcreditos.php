<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Giros y Finanzas - Reporte Creditos " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>Agencia del Credito</th>
		<th>Linea de Credito</th>
		<th>Numero de Obligacion</th>
		<th>Nit del Cliente</th>
		<th>Codigo de Moneda</th>
		<th>Codigo de Plan de Pagos</th>
		<th>Fecha de Inicio del Credito</th>
		<th>Fecha Proximo Vto Interes</th>
		<th>Fecha Proximo Vto Capital</th>
		<th>Fecha de Vto Final</th>
		<th>Plazo</th>
		<th>Periodicidad de Capital</th>
		<th>Periodicidad de Interos</th>
		<th>Valor del Gradiente</th>
		<th>Cantidad de Cuotas Gradiente</th>
		<th>Tipo de Tasa</th>
		<th>Puntos adicionales</th>
		<th>Tipo de Mora</th>
		<th>Porcentaje de Seguro</th>
		<th>Nit de la compania Aseguradora</th>
		<th>Valor Fijo del Seguro</th>
		<th>Saldo del Credito</th>
		<th>Monto del Credito</th>
		<th>Codigo Ciiu</th>
		<th>Numero del Cupo</th>
		<th>Tipo de Credito</th>
		<th>Tipo de Garantia</th>
		<th>Modalidad del Credito</th>
		<th>Periodo de Gracia</th>
		<th>Calificacion del Credito</th>
		<th>Nro de Pagare</th>
		<th>Destino Economico</th>
		<th>Estado de Migracion</th>
	</tr>
	<?php

	$queryDB = "SELECT right(replicate('0', 5) + '201', 5), 
	right(replicate('0', 4) + '71', 4) as linea_credito, si.nro_libranza, 
	right(replicate('0', 15) + si.cedula, 15) as nit_cliente, 
	right(replicate('0', 2) + '0', 2) as codigo_moneda, 
	right(replicate('0', 4) + '7', 4) as codigo_plan_pagos, ve.fecha as fecha_venta, vd.fecha_primer_pago, 

	DATEADD(MONTH,  (vd.cuota_hasta - vd.cuota_desde + 1), vd.fecha_primer_pago) as fecha_vcto_final, 
	right(replicate('0', 4) + (vd.cuota_hasta - vd.cuota_desde + 1), 4) as cuotas_vendidas, 
	right(replicate('0', 4) + '1', 4) as periodicidad_capital, 
	right(replicate('0', 4) + '1', 4) as periodicidad_interes, 
	right(replicate('0', 14) + '0', 14) as valor_gradiente, 
	right(replicate('0', 4) + '0', 4) as cantidad_cuotas_gradiente, 
	right(replicate('0', 4) + '3', 4) as tipo_tasa, si.tasa_interes, 
	right(replicate('0', 2) + '1', 2) as tipo_mora, 
	right(replicate('0', 8) + '0', 8) as porcentaje_seguro, 
	right(replicate('0', 13) + '0', 13) as nit_aseguradora, 
	right(replicate('0', 9) + '0', 9) as valor_fijo_seguro, 
	right(replicate('0', 9) + (si.valor_credito * 100), 9) as saldo_credito, 
	right(replicate('0', 9) + '0', 9) as codigo_ciiu, 
	right(replicate('0', 12) + '0', 12) as numero_cupo, '1' as tipo_credito, '2' as tipo_garantia, 'V' as modalidad_credito, 
	right(replicate('0', 2) + '0', 2) as periodo_gracia, 'A' as calificacion_credito, 
	right(replicate('0', 6) + '10', 6) as destino_economico, '0' as estado_migracion from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN ventas" . $sufijo . " ve ON ve.id_venta = vd.id_venta where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if (!$_REQUEST["ext"]) {
		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}
	}

	$queryDB .= " order by si.cedula, vd.id_ventadetalle";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$letras = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", " ", "-");

		$nro_obligacion = str_ireplace($letras, "", $fila["nro_libranza"]);

		$ceros = "";

		for ($i = 1; $i <= 17 - strlen($nro_obligacion); $i++) {
			$ceros .= "0";
		}

		$nro_obligacion = $ceros . $nro_obligacion;

		$tasa_anual = round($fila["tasa_interes"] * 12, 2) * 100;

	?>
		<tr>
			<td style="mso-number-format:'@';"><?php echo $fila["agencia_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["linea_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $nro_obligacion ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["nit_cliente"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["codigo_moneda"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["codigo_plan_pagos"] ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_venta"])) ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_primer_pago"])) ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_primer_pago"])) ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_vcto_final"])) ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cuotas_vendidas"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["periodicidad_capital"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["periodicidad_interes"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_gradiente"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cantidad_cuotas_gradiente"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_tasa"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $tasa_anual ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_mora"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["porcentaje_seguro"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["nit_aseguradora"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_fijo_seguro"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["saldo_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["saldo_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["codigo_ciiu"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["numero_cupo"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_garantia"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["modalidad_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["periodo_gracia"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["calificacion_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $nro_obligacion ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["destino_economico"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["estado_migracion"] ?></td>
		</tr>
	<?php

	}

	?>
</table>