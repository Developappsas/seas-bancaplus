<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")) {
	exit;
}

$link = conectar();

$queryDB = "select * from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

$simulacion_rs = sqlsrv_query($link, $queryDB);

$simulacion = sqlsrv_fetch_array($simulacion_rs);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Archivo Visado " . $simulacion["cedula"] . "-" . $simulacion["nro_libranza"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>No</th>
		<th>Estado</th>
		<th>Fecha consulta</th>
		<th>Cedula</th>
		<th>Nombre</th>
		<th>Pagaduria</th>
		<th>Tipo de credito</th>
		<th>Cupo Lib Inversion</th>
		<th>Cuota Compra</th>
		<th>Compras</th>
		<th>Vr. Credito</th>
		<th>Vr. Desembolso</th>
		<th>Plazo</th>
		<th>Cuota Cred.</th>
		<th>Cupo</th>
		<th>Fecha resp.</th>
		<th>Fecha Vinculacion</th>
		<th>Tipo Vinculacion</th>
	</tr>
	<?php

	$queryDB = "select si.* from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$queryDB = "SELECT ent.nombre as nombre_entidad, scc.se_compra, scc.entidad, scc.cuota from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '" . $fila["id_simulacion"] . "'";

		$rs1 = sqlsrv_query($link, $queryDB);

		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
			if ($fila1["se_compra"] == "SI" && ($fila1["nombre_entidad"] || $fila1["entidad"])) {
				if ($cuotas_cc)
					$cuotas_cc .= " - ";

				$cuotas_cc .= "$" . number_format($fila1["cuota"], 0);

				if ($entidades_cc)
					$entidades_cc .= ", ";

				$entidades_cc .= $fila1["nombre_entidad"] . " " . $fila1["entidad"];
			}
		}

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

	?>
		<tr valign="top">
			<td></td>
			<td>1 Consulta</td>
			<td><?php echo date("d/m/Y") ?></td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo utf8_decode($fila["nombre"]) ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td></td>
			<td><?php echo $fila["opcion_cuota_cli"] ?></td>
			<td><?php echo $cuotas_cc ?></td>
			<td><?php echo $entidades_cc ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $fila["desembolso_cliente"] ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	<?php

	}

	?>
</table>