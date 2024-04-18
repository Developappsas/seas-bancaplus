<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Notificacion Fiduciaria " . $_REQUEST["id_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
error_reporting(E_ALL);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}

$queryDB = "SELECT * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

?>
<table border="0">
	<tr>
		<th>ORIGINADOR</th>
		<th>OPERACION</th>
		<th>CANT</th>
		<th>CEDULA</th>
		<th>PRIMER NOMBRE</th>
		<th>SEGUNDO NOMBRE</th>
		<th>1 APELLIDO</th>
		<th>2 APELLIDO</th>
		<th>DIRECCION</th>
		<th>TELEFONO</th>
		<th>SALARIO</th>
		<th>VR CUOTA</th>
		<th>ORIGINADOR</th>
		<th>TASA NOMINAL</th>
		<th>CUOTAS FALTANTES</th>
		<th>PAGARE</th>
		<th>PLAZO INICIAL</th>
		<th>SALDO CAPITAL</th>
		<th>MONTO INICIAL</th>
		<th>FECHA VENCIMIENTO</th>
		<th>FECHA DESESMBOLSO</th>
		<th>NIT EMISOR</th>
		<th>EMISOR</th>
		<th>TASA FONDEADOR</th>
		<th>TASA ORIGINADOR</th>
		<th>F. NACIMIENTO DEUDOR</th>
		<th>VALOR DESCUENTOS</th>
		<th>CALIFICACION DEUDOR</th>
		<th>INTERES CAUSADO</th>
		<th>CIUDAD</th>
		<th>DEPARTAMENTO</th>
		<th>SEGURO</th>
		<th>CUOTA CORRIENTE</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.calif_sector_financiero,si.cedula, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.direccion, so.celular as telefono, ci.municipio, ci.departamento, si.total_ingresos, si.nro_libranza, si.pa, si.tasa_interes, si.plazo, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, si.pagaduria, pp.nit as nit_pagaduria, so.fecha_nacimiento, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, co.nombre as comprador, ve.fecha as fecha_venta, vd.fecha_primer_pago, 
		DATEADD(month, (vd.cuota_hasta - vd.cuota_desde), vd.fecha_primer_pago) as fecha_vcto_final, si.plazo, si.puntaje_datacredito, SUM(cu.capital) as saldo_capital 
			FROM ventas_detalle vd 
			INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion 
			INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
			INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador 
			LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta 
			LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' 
			LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
			LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		$queryDB .= " group by si.cedula, si.fecha_desembolso, si.fecha_cartera, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.direccion, so.celular, ci.municipio, ci.departamento, si.total_ingresos, si.nro_libranza, si.pa, si.tasa_interes, si.plazo, si.puntaje_datacredito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, pp.nit, si.pagaduria, so.fecha_nacimiento, vd.cuota_hasta, vd.cuota_desde, co.nombre, ve.fecha, vd.fecha_primer_pago, vd.id_ventadetalle, si.plazo  ORDER BY si.cedula, vd.id_ventadetalle";
	} else {
		$queryDB = "SELECT si.cedula, si.fecha_desembolso,  FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.direccion, si.telefono, si.ciudad as municipio, si.departamento, 0 as total_ingresos, si.nro_libranza, 'ESEFECTIVO' as pa, si.tasa_interes, si.plazo, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, si.pagaduria, pp.nit as nit_pagaduria, so.fecha_nacimiento, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, co.nombre as comprador, ve.fecha as fecha_venta, vd.fecha_primer_pago, DATEADD(month, (vd.cuota_hasta - vd.cuota_desde), vd.fecha_primer_pago) as fecha_vcto_final, si.plazo, '' as puntaje_datacredito, SUM(cu.capital) as saldo_capital from ventas_detalle$sufijo vd 
			INNER JOIN simulaciones$sufijo si ON vd.id_simulacion = si.id_simulacion 
			LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre 
			INNER JOIN ventas$sufijo ve ON vd.id_venta = ve.id_venta 
			INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN cuotas$sufijo cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta 
			LEFT JOIN cuotas$sufijo cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' 
			LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		$queryDB .= " group by si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.direccion, si.telefono, si.ciudad, si.departamento, si.nro_libranza, si.tasa_interes, si.plazo, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, pp.nit, si.pagaduria, so.fecha_nacimiento, vd.cuota_hasta, vd.cuota_desde, co.nombre, ve.fecha, vd.fecha_primer_pago, vd.id_ventadetalle, si.plazo  ORDER BY si.cedula, vd.id_ventadetalle";
	}

	
	$rs = sqlsrv_query($link, $queryDB);
	
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		switch ($fila["opcion_credito"]) {
			case "CLI": $opcion_cuota = $fila["opcion_cuota_cli"]; break;
			case "CCC": $opcion_cuota = $fila["opcion_cuota_ccc"]; break;
			case "CMP": $opcion_cuota = $fila["opcion_cuota_cmp"]; break;
			case "CSO": $opcion_cuota = $fila["opcion_cuota_cso"]; break;
		}

		$cuota_corriente = $opcion_cuota - $fila["seguro"];
		$saldo_capital = $fila["saldo_capital"];
		if ($fila["cuotas_vendidas"] == $fila["plazo"]){
			$saldo_capital = $fila["valor_credito"];
		}

	?>
		<tr>
			<td>Kredit Plus SA</td>
			<td>Compra</td>
			<td>1</td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["nombre1"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["nombre2"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["apellido1"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["apellido2"])) ?></td>
			<td><?php echo utf8_decode($fila["direccion"]) ?></td>
			<td><?php echo utf8_decode($fila["telefono"]) ?></td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td>Kredit Plus SA</td>
			<td><?php echo $fila["tasa_interes"] . '%' ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $fila["fecha_primer_pago"] ?></td>
			<td><?php echo $fila["fecha_desembolso"] ?></td>
			<td><?php echo $fila["nit_pagaduria"] ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td></td>
			<td><?php echo $fila["tasa_interes"] . '%' ?></td>
			<td><?php echo $fila["fecha_nacimiento"] ?></td>
			<td></td>
			<td><?php echo $fila["puntaje_datacredito"] ?></td>
			<td></td>
			<td><?php echo utf8_decode($fila["municipio"]) ?></td>
			<td><?php echo utf8_decode($fila["departamento"]) ?></td>
			<td><?php echo $fila["seguro"] ?></td>
			<td><?php echo $cuota_corriente ?></td>
			<td><?php echo $fila["calif_sector_financiero"] ?></td>

		</tr>
	<?php

	}

	?>
</table>