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
header("Content-Disposition: attachment; filename=Giros y Finanzas - Reporte Direcciones " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>Numero Interno</th>
		<th>Numero Consecutivo</th>
		<th>Direccion</th>
		<th>Ciudad</th>
		<th>Departamento</th>
		<th>Pais</th>
		<th>Codigo Postal</th>
		<th>Tipo Direccion</th>
		<th>Telefono1</th>
		<th>Extension1</th>
		<th>Telefono2</th>
		<th>Extension2</th>
		<th>Telefono3</th>
		<th>Extension3</th>
		<th>Num Celular</th>
		<th>Numero Fax</th>
		<th>Numero Beeper</th>
		<th>Codigo Beeper</th>
		<th>Email</th>
		<th>Zona Postal</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT 
		right(replicate('0', 17) + si.cedula, 17) as cedula, 
		right(replicate('0', 3) + '0', 3) as consecutivo, so.direccion, so.ciudad, substring(so.ciudad, 1, 2) as departamento, 
		right(replicate('0', 5) + '1', 5) as pais, 
		right(replicate('0', 5) + '0', 5) as cod_postal, 
		right(replicate('0', 3) + '3', 3) as tipo_direccion, 
		right(replicate('0', 10) + so.tel_residencia, 10) as telefono1, 
		right(replicate('0', 5) + '0', 5) as ext1, 
		right(replicate('0', 10) + '0', 10) as telefono2, 
		right(replicate('0', 5) + '0', 5) as ext2, 
		right(replicate('0', 10) + '0', 10) as telefono3, 
		right(replicate('0', 5) + '0', 5) as ext3, 
		right(replicate('0', 10) + so.celular, 10) as celular, 
		right(replicate('0', 10) + '0', 10) as fax, 
		right(replicate('0', 10) + '0', 10) as num_beeper, 
		right(replicate('0', 10) + '0', 10) as cod_beeper, so.email as email, 
		right(replicate('0', 8) + '0', 8) as zona_postal from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}
	} else {
		$queryDB = "SELECT 
		right(replicate('0', 17) + si.celular, 17) as cedula, 
		right(replicate('0', 3) + '0', 3) as consecutivo, si.direccion, '00000' as ciudad, '00' as departamento, 
		right(replicate('0', 5) + '1', 5) as pais, 
		right(replicate('0', 5) + '0', 5) as cod_postal, 
		right(replicate('0', 3) + '3',3 ) as tipo_direccion, 
		right(replicate('0', 10) + si.telefono , 10) as telefono1, 
		right(replicate('0', 5) + '0', 5) as ext1, 
		right(replicate('0', 10) + '0', 10) as telefono2, 
		right(replicate('0', 5) + '0', 5) as ext2, 
		right(replicate('0', 10) + '0', 10) as telefono3, 
		right(replicate('0', 5) + '0', 5) as ext3, 
		LPAD(si.celular, 10, '0') as celular, 
		right(replicate('0', 10) + '0', 10) as fax, 
		right(replicate('0', 10) + '0', 10) as num_beeper, 
		right(replicate('0', 10) + '0', 10) as cod_beeper, si.email as email, 
		right(replicate('0', 8) + '0', 8) as zona_postal from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";
	}

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	$queryDB .= " order by si.cedula, vd.id_ventadetalle";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	?>
		<tr>
			<td style="mso-number-format:'@';"><?php echo $fila["cedula"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["consecutivo"] ?></td>
			<td><?php echo strtoupper($fila["direccion"]) ?></td>
			<td><?php echo strtoupper($fila["ciudad"]) ?></td>
			<td><?php echo strtoupper($fila["departamento"]) ?></td>
			<td><?php echo strtoupper($fila["pais"]) ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_postal"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_direccion"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["telefono1"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ext1"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["telefono2"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ext2"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["telefono3"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ext3"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["celular"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fax"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["num_beeper"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_beeper"] ?></td>
			<td><?php echo $fila["email"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["zona_postal"] ?></td>
		</tr>
	<?php

	}

	?>
</table>