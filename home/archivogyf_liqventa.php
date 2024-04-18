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
header("Content-Disposition: attachment; filename=Giros y Finanzas - Liquidacion Venta " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>No</th>
		<th>Cedula</th>
		<th>Nombre</th>
		<th>Libranza</th>
		<th>Pagaduria</th>
		<th>Valor_libranza</th>
		<th>Plazo Original</th>
		<th>Cuotas pagadas</th>
		<th>Cuotas Vendidas</th>
		<th>Vr_Venta</th>
		<th>SALDO K VTA</th>
		<th>VALIDACION K</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.cedula, so.nombre1, so.nombre2, so.apellido1, so.apellido2, si.nro_libranza, si.pagaduria, si.valor_credito, si.plazo, si.estado, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		$queryDB .= " group by si.cedula, so.nombre1, so.nombre2, so.apellido1, so.apellido2, si.nro_libranza, si.pagaduria, si.valor_credito, si.plazo, si.estado, vd.cuota_hasta, vd.cuota_desde order by si.cedula, vd.id_ventadetalle";
	} else {
		$queryDB = "SELECT si.cedula, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.nro_libranza, si.pagaduria, si.valor_credito, si.plazo, si.estado, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, SUM(cu.capital) as saldo_capital from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		$queryDB .= " group by si.cedula, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.nro_libranza, si.pagaduria, si.valor_credito, si.plazo, si.estado, vd.cuota_hasta, vd.cuota_desde order by si.cedula, vd.id_ventadetalle";
	}

	$rs = sqlsrv_query($link, $queryDB);

	$j = 1;

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
		$cuotas_pagas = $fila["plazo"] - $fila["cuotas_vendidas"];

		$saldo_capital = $fila["saldo_capital"];

		if ($fila["cuotas_vendidas"] == $fila["plazo"])
			$saldo_capital = $fila["valor_credito"];

	?>
		<tr>
			<td><?php echo $j ?></td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo utf8_decode($fila["apellido1"] . " " . $fila["apellido2"] . " " . $fila["nombre1"] . " " . $fila["nombre2"]) ?></td>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $cuotas_pagas ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $saldo_capital ?></td>
		</tr>
	<?php

		$j++;
	}
	?>
</table>