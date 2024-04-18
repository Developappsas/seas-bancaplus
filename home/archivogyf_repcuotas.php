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
header("Content-Disposition: attachment; filename=Giros y Finanzas - Reporte Cuotas " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>Agencia del Credito</th>
		<th>Linea de Credito</th>
		<th>Numero de Obligacion</th>
		<th>Fecha de Vencimiento</th>
		<th>Tipo de Vencimiento</th>
		<th>Valor Capital</th>
		<th>Valor Interes Corriente</th>
		<th>Valor Mora</th>
		<th>Valor Seguros</th>
		<th>Estado de Migracion</th>
	</tr>
	<?php

	$queryDB = "SELECT 
	right(replicate('0', 5) + '201', 5) as agencia_credito, 
	right(replicate('0', 4) + '71', 4) as linea_credito, si.nro_libranza, vd.fecha_primer_pago, 
	right(replicate('0', 3) + '1', 3) as tipo_vencimiento, 
	right(replicate('0', 14) + '0', 14) as valor_capital, 
	right(replicate('0', 14) + '0', 14) as valor_interes, 
	right(replicate('0', 14) + '0', 14) as valor_mora, 
	right(replicate('0', 14) + '0', 14) as valor_seguros, '0' as estado_migracion from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

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

	?>
		<tr>
			<td style="mso-number-format:'@';"><?php echo $fila["agencia_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["linea_credito"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $nro_obligacion ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_primer_pago"])) ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_vencimiento"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_capital"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_interes"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_mora"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["valor_seguros"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["estado_migracion"] ?></td>
		</tr>
	<?php

	}

	?>
</table>