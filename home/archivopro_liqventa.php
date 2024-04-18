<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
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
header("Content-Disposition: attachment; filename=Progresion - Liquidacion Venta " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>LIBRANZA</th>
		<th>CEDULA</th>
		<th>NOMBRE CLIENTE</th>
		<th>PAGADURIA</th>
		<th>PLAZO INICIAL</th>
		<th>VR DESEMBOLSADO</th>
		<th>CUOTAS RECAUDADAS</th>
		<th>CUOTAS RESTANTES</th>
		<th>CUOTA</th>
		<th>SEGURO DE VIDA</th>
		<th>CUOTA CORRIENTE</th>
		<th>CAPITAL</th>
		<th>PA</th>
		<th>FECHA NACIMIENTO</th>
		<th>EDAD</th>
		<th>INGRESOS</th>
		<th>APORTES</th>
		<th>RESERVA INCORPORACION</th>
		<th>TASA</th>
		<th>PRIMER RECAUDO</th>
		<th>INTERESES ANTICIPADOS</th>
		<th>ASESORIA FINANCIERA + IVA</th>
		<th>TRANSFERENCIA</th>
		<th>4 X MIL</th>
		<?php

		$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {

		?>
			<th><?php echo $fila1["nombre"] ?></th>
		<?php

		}

		?>
		<th>COMISION POR VENTA (RETANQUEOS) + IVA</th>
		<th>TOTAL DESCUENTO ANTICIPADO</th>
		<th>COMPRAS DE CARTERA</th>
		<th>VR DESEMSOLSO CLIENTE</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.id_simulacion, si.nro_libranza, si.cedula, si.fecha_estudio, si.nombre, si.pagaduria, si.plazo, si.valor_credito, si.retanqueo_total, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.estado, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.pa, si.fecha_nacimiento, CASE WHEN si.fecha_nacimiento IS NOT NULL  THEN YEAR(GETDATE()) - YEAR(si.fecha_nacimiento) - (IIF (FORMAT(GETDATE(), '%M%d') < FORMAT(si.fecha_nacimiento, '%M%d'), 1, 0 )) ELSE null END as edad, si.total_ingresos, si.total_aportes, si.fidelizacion, si.tasa_interes, vd.fecha_primer_pago, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.tipo_producto, si.desembolso_cliente, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		$queryDB .= " group by si.nro_libranza, si.cedula, si.fecha_estudio, si.nombre, si.pagaduria, si.plazo, si.valor_credito, si.retanqueo_total, vd.cuota_hasta, vd.cuota_desde, si.estado, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.pa, si.fecha_nacimiento, si.total_ingresos, si.total_aportes, si.fidelizacion, si.tasa_interes, vd.fecha_primer_pago, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.tipo_producto, si.desembolso_cliente ,  si.id_simulacion, vd.id_ventadetalle order by si.cedula, vd.id_ventadetalle, si.id_simulacion";
	} else {
		$queryDB = "SELECT si.id_simulacion, si.nro_libranza, si.cedula, si.fecha_estudio, si.nombre, si.pagaduria, si.plazo, si.valor_credito, '0' as retanqueo_total, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.estado, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, 'ESEFECTIVO' as pa, si.fecha_nacimiento, CASE WHEN si.fecha_nacimiento IS NOT NULL  THEN YEAR(GETDATE()) - YEAR(si.fecha_nacimiento) - (IIF (FORMAT(GETDATE(), '%M%d') < FORMAT(si.fecha_nacimiento, '%M%d'), 1, 0 )) ELSE null END as edad, 0 as total_ingresos, 0 as total_aportes, '0' as fidelizacion, si.tasa_interes, vd.fecha_primer_pago, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.tipo_producto, 0 as desembolso_cliente, SUM(cu.capital) as saldo_capital from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas" . $sufijo . " cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		$queryDB .= " group by si.nro_libranza, si.cedula, si.fecha_estudio, si.nombre, si.pagaduria, si.plazo, si.valor_credito, vd.cuota_hasta, vd.cuota_desde, si.estado, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, si.fecha_nacimiento, si.tasa_interes, vd.fecha_primer_pago, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.tipo_producto,  si.id_simulacion, vd.id_ventadetalle  order by si.cedula, vd.id_ventadetalle";
	}


	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		switch ($fila["opcion_credito"]) {
			case "CLI":
				$opcion_cuota = $fila["opcion_cuota_cli"];
				break;
			case "CCC":
				$opcion_cuota = $fila["opcion_cuota_ccc"];
				break;
			case "CMP":
				$opcion_cuota = $fila["opcion_cuota_cmp"];
				break;
			case "CSO":
				$opcion_cuota = $fila["opcion_cuota_cso"];
				break;
		}

		$cuota_corriente = $opcion_cuota - $fila["seguro"];

		$cuotas_pagas = $fila["plazo"] - $fila["cuotas_vendidas"];

		$saldo_capital = $fila["saldo_capital"];

		if ($fila["cuotas_vendidas"] == $fila["plazo"])
			$saldo_capital = $fila["valor_credito"];

		$reserva_incorporacion = $saldo_capital * 0.03;

		$compras_cartera = 0;

		if (!$_REQUEST["ext"]) {
			$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL or (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '" . $fila["id_simulacion"] . "'";

			$rs1 = sqlsrv_query($link, $queryDB);

			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

			if ($fila1["s"])
				$compras_cartera = $fila1["s"];
		}

		if ($fila["opcion_credito"] == "CLI")
			$fila["retanqueo_total"] = 0;

		$intereses_anticipados = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00;

		$asesoria_financiera = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00;

		$comision_venta = 0;

		if ($fila["tipo_producto"] == "1") {
			if ($fila["fecha_estudio"] < "2018-01-01") {
				$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
			} else {
				if ($fila["fidelizacion"])
					$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
				else
					$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
			}
		}

		$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;

		$comision_venta_iva = 0;

		if ($fila["tipo_producto"] == "1") {
			if ($fila["fecha_estudio"] < "2018-01-01") {
				$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			} else {
				if ($fila["fidelizacion"])
					$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
				else
					$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			}
		}

		$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;

		$desembolso_cliente = $fila["valor_credito"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];

		if ($fila["tipo_producto"] == "1")
			if ($fila["fidelizacion"])
				$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			else
				$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;

		$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '" . $fila["id_simulacion"] . "' order by id_descuento");

		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
			$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
		}

		$total_descuento_anticipado = round($intereses_anticipados) + round($asesoria_financiera) + round($iva) + $fila["descuento_transferencia"] + round($gmf) + round($comision_venta) + round($comision_venta_iva);

		if (sqlsrv_num_rows($descuentos_adicionales)) {
			sqlsrv_data_seek($descuentos_adicionales, 0);

			while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
				$total_descuento_anticipado += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
			}
		}

	?>
		<tr>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo utf8_decode($fila["nombre"]) ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $cuotas_pagas ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td><?php echo $fila["seguro"] ?></td>
			<td><?php echo $cuota_corriente ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $fila["pa"] ?></td>
			<td><?php echo $fila["fecha_nacimiento"] ?></td>
			<td><?php echo $fila["edad"] ?></td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo $fila["total_aportes"] ?></td>
			<td><?php echo round($reserva_incorporacion) ?></td>
			<td><?php echo $fila["tasa_interes"] ?></td>
			<td><?php echo $fila["fecha_primer_pago"] ?></td>
			<td><?php echo round($intereses_anticipados) ?></td>
			<td><?php echo round($asesoria_financiera) + round($iva) ?></td>
			<td><?php echo $fila["descuento_transferencia"] ?></td>
			<td><?php echo round($gmf) ?></td>
			<?php

			$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

			while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
				$existe_descuento = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '" . $fila["id_simulacion"] . "' AND id_descuento = '" . $fila1["id_descuento"] . "'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				if (sqlsrv_num_rows($existe_descuento)) {
					$valor_descuento = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
				} else {
					$valor_descuento = 0;
				}

			?>
				<td><?php echo $valor_descuento ?></td>
			<?php

			}

			?>
			<td><?php echo round($comision_venta) + round($comision_venta_iva) ?></td>
			<td><?php echo $total_descuento_anticipado ?></td>
			<td><?php echo $compras_cartera ?></td>
			<td><?php echo $fila["desembolso_cliente"] ?></td>
		</tr>
	<?php

	}

	?>
</table>