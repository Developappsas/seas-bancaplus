<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "SELECT * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Fic Colectivo II - Base Venta " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>NO. LIBRANZA</th>
		<th>CEDULA</th>
		<th>NOMBRE CLIENTE</th>
		<th>PAGADURIA</th>
		<th>NIT PAGADURIA</th>
		<th>PLAZO INICIAL</th>
		<th>VALOR DESEMBOLSADO</th>
		<th>CUOTAS RECAUDADAS</th>
		<th>CUOTAS RESTANTES</th>
		<th>FECHA VENCIMIENTO</th>
		<th>CUOTA</th>
		<th>SEGURO DE VIDA</th>
		<th>CUOTA CORRIENTE</th>
		<th>CAPITAL</th>
		<th>PA</th>
		<th>FECHA DE NACIMIENTO</th>
		<th>EDAD</th>
		<th>INGRESOS</th>
		<th>FECHA DESEMBOLSO</th>
		<th>MES DE PRODUCCION</th>
		<th>TASA DEL CREDITO</th>
		<th>SALDO DE CAPITAL</th>
		<th>TENEDOR DE LA CARTERA</th>
		<th>TASA DE VENTA AL TENEDOR</th>
		<th>DIRECCION</th>
		<th>TELEFONO</th>
		<th>CIUDAD</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.cedula, si.nombre, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, so.direccion, so.tel_residencia, ci.municipio, ci.departamento, si.total_ingresos, si.estado, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, cu2.seguro, si.pagaduria, pp.nit as nit_pagaduria, si.tasa_interes, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.nro_libranza, si.pa, si.fecha_nacimiento, CASE WHEN si.fecha_nacimiento IS NOT NULL AND si.fecha_nacimiento <> '0000-00-00' THEN YEAR(GETDATE()) - YEAR(si.fecha_nacimiento) - (DATE_FORMAT(GETDATE(), 'MM-dd') < FORMAT(si.fecha_nacimiento, 'MM-dd')) ELSE '' END as edad, si.plazo, si.valor_credito, SUM(cu.capital) as saldo_capital, vd.fecha_primer_pago, si.fecha_desembolso from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		$queryDB .= " group by si.cedula, si.nombre, so.direccion, so.tel_residencia, ci.municipio, ci.departamento, si.total_ingresos, si.estado, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, cu2.seguro, si.pagaduria, pp.nit, si.tasa_interes, vd.cuota_hasta, vd.cuota_desde, si.nro_libranza, si.pa, si.fecha_nacimiento, si.plazo, si.valor_credito, vd.fecha_primer_pago, si.fecha_desembolso order by si.cedula, vd.id_ventadetalle";
	} else {
		$queryDB = "SELECT si.cedula, si.nombre, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.direccion, si.telefono as tel_residencia, si.ciudad as municipio, si.departamento, 0 as total_ingresos, si.estado, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, cu2.seguro, si.pagaduria, pp.nit as nit_pagaduria, si.tasa_interes, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.nro_libranza, '' as pa, si.fecha_nacimiento, CASE WHEN si.fecha_nacimiento IS NOT NULL AND si.fecha_nacimiento <> '0000-00-00' THEN YEAR(GETDATE()) - YEAR(si.fecha_nacimiento) - (FORMAT(GETDATE(), 'MM-dd') < DATE_FORMAT(si.fecha_nacimiento, '%m%d')) ELSE '' END as edad, si.plazo, si.valor_credito, SUM(cu.capital) as saldo_capital, vd.fecha_primer_pago, si.fecha_desembolso from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria LEFT JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas" . $sufijo . " cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		$queryDB .= " group by si.cedula, si.nombre, si.direccion, si.telefono, si.ciudad, si.departamento, si.estado, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, cu2.seguro, si.pagaduria, pp.nit, si.tasa_interes, vd.cuota_hasta, vd.cuota_desde, si.nro_libranza, si.fecha_nacimiento, si.plazo, si.valor_credito, vd.fecha_primer_pago, si.fecha_desembolso order by si.cedula, vd.id_ventadetalle";
	}

	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		switch ($fila["opcion_credito"]) {
			case "CLI":
				$opcion_cuota = $fila["opcion_cuota_cli"];
				$opcion_desembolso = $fila["opcion_desembolso_cli"];
				break;
			case "CCC":
				$opcion_cuota = $fila["opcion_cuota_ccc"];
				$opcion_desembolso = $fila["opcion_desembolso_ccc"];
				break;
			case "CMP":
				$opcion_cuota = $fila["opcion_cuota_cmp"];
				$opcion_desembolso = $fila["opcion_desembolso_cmp"];
				break;
			case "CSO":
				$opcion_cuota = $fila["opcion_cuota_cso"];
				$opcion_desembolso = $fila["opcion_desembolso_cso"];
				break;
		}

		$cuota_corriente = $opcion_cuota - $fila["seguro"];

		$cuotas_pagas = $fila["plazo"] - $fila["cuotas_vendidas"];

		$saldo_capital = $fila["saldo_capital"];

		if ($fila["cuotas_vendidas"] == $fila["plazo"])
			$saldo_capital = $fila["valor_credito"];

	?>
		<tr>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo utf8_decode($fila["nombre"]) ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo $fila["nit_pagaduria"] ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $cuotas_pagas ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $fila["fecha_primer_pago"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td><?php echo $fila["seguro"] ?></td>
			<td><?php echo $cuota_corriente ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $fila["pa"] ?></td>
			<td><?php echo $fila["fecha_nacimiento"] ?></td>
			<td><?php echo $fila["edad"] ?></td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo $fila["fecha_desembolso"] ?></td>
			<td><?php echo $fila["mes_prod"] ?></td>
			<td><?php echo $fila["tasa_interes"] ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td></td>
			<td></td>
			<td><?php echo utf8_decode($fila["direccion"]) ?></td>
			<td><?php echo utf8_decode($fila["tel_residencia"]) ?></td>
			<td><?php echo utf8_decode($fila["municipio"]) ?></td>
		</tr>
	<?php

	}

	?>
</table>