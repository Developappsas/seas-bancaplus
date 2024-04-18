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
header("Content-Disposition: attachment; filename=Giros y Finanzas - Base Venta " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th colspan="17">DATOS GENERALES</th>
		<th colspan="4">CAMPO GYF</th>
		<th colspan="2">COMPRA 1</th>
		<th colspan="2">COMPRA 2</th>
		<th colspan="2">COMPRA 3</th>
		<th colspan="2">COMPRA 4</th>
		<th colspan="2">COMPRA 5</th>
		<th colspan="2">COMPRA 6</th>
		<th colspan="2">REFINANCIACION</th>
		<th colspan="2">SANEAMIENTO 1</th>
		<th colspan="2">SANEAMIENTO 2</th>
		<th colspan="2">SANEAMIENTO 3</th>
		<th colspan="2">SANEAMIENTO 4</th>
		<th colspan="2">SANEAMIENTO 5</th>
		<th colspan="2">SANEAMIENTO 6</th>
	</tr>
	<tr>
		<th>CONSECUTIVO</th>
		<th>LIBRANZA</th>
		<th>Fecha desembolso</th>
		<th>Documento</th>
		<th>Apellidos_Nombres</th>
		<th>Pagaduria</th>
		<th>Ingresos del cliente</th>
		<th>Tipo de cliente o vinculacion</th>
		<th>Fecha vinculacion</th>
		<th>Embargo</th>
		<th>Total egresos</th>
		<th>Salario libre</th>
		<th>Valor visado</th>
		<th>Cuota $</th>
		<th>Plazo</th>
		<th>No. de cuotas pagas</th>
		<th>Monto</th>
		<th>Causal_de_negacion</th>
		<th>Observacion_Unidad_de_Otorgamiento</th>
		<th>Decision_Scoring</th>
		<th>Monto</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
		<th>ENTIDAD</th>
		<th>VALOR</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.id_simulacion, si.nro_libranza, si.fecha_desembolso, si.cedula, so.nombre1, so.nombre2, so.apellido1, so.apellido2, si.pagaduria, si.total_ingresos, si.nivel_contratacion, si.fecha_inicio_labor, si.embargo_actual, si.total_egresos, si.salario_libre, si.valor_visado, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.plazo, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.valor_credito from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}
	} else {
		$queryDB = "SELECT si.id_simulacion, si.nro_libranza, si.fecha_desembolso, si.cedula, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.pagaduria, 0 as total_ingresos, '' as nivel_contratacion, '' as fecha_inicio_labor, '' as embargo_actual, 0 as total_egresos, 0 as salario_libre, 0 as valor_visado, si.opcion_credito, si.opcion_cuota_cso, si.plazo, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.valor_credito from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";
	}

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	$queryDB .= " order by si.cedula, vd.id_ventadetalle";

	$rs = sqlsrv_query($link, $queryDB);

	$j = 1;

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

		$cuotas_pagas = $fila["plazo"] - $fila["cuotas_vendidas"];

		$com = 0;

		$san = 0;

		for ($i = 0; $i < 6; $i++) {
			$entidadcom[$i] = "";
			$valorcom[$i] = "";
			$entidadsan[$i] = "";
			$valorsan[$i] = "";
		}

		if (!$_REQUEST["ext"]) {
			$queryDB = "select ent.nombre as nombre_entidad, scc.se_compra, scc.entidad, scc.cuota, scc.valor_pagar from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '" . $fila["id_simulacion"] . "'";

			$rs1 = sqlsrv_query($link, $queryDB);

			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
				if ($fila1["se_compra"] == "SI" && ($fila1["nombre_entidad"] || $fila1["entidad"])) {
					if ($fila1["cuota"] <> "0") {
						$entidadcom[$com] = $fila1["nombre_entidad"] . " " . $fila1["entidad"];

						$valorcom[$com] = $fila1["cuota"];

						$com++;
					} else {
						$entidadsan[$san] = $fila1["nombre_entidad"] . " " . $fila1["entidad"];

						$valorsan[$san] = $fila1["valor_pagar"];

						$san++;
					}
				}
			}
		}

	?>
		<tr>
			<td><?php echo $j ?></td>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["fecha_desembolso"] ?></td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo utf8_decode($fila["apellido1"] . " " . $fila["apellido2"] . " " . $fila["nombre1"] . " " . $fila["nombre2"]) ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo utf8_decode($fila["nivel_contratacion"]) ?></td>
			<td><?php echo $fila["fecha_inicio_labor"] ?></td>
			<td><?php echo utf8_decode($fila["embargo_actual"]) ?></td>
			<td><?php echo $fila["total_egresos"] ?></td>
			<td><?php echo $fila["salario_libre"] ?></td>
			<td><?php echo $fila["valor_visado"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $cuotas_pagas ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo utf8_decode($entidadcom[0]) ?></td>
			<td><?php echo $valorcom[0] ?></td>
			<td><?php echo utf8_decode($entidadcom[1]) ?></td>
			<td><?php echo $valorcom[1] ?></td>
			<td><?php echo utf8_decode($entidadcom[2]) ?></td>
			<td><?php echo $valorcom[2] ?></td>
			<td><?php echo utf8_decode($entidadcom[3]) ?></td>
			<td><?php echo $valorcom[3] ?></td>
			<td><?php echo utf8_decode($entidadcom[4]) ?></td>
			<td><?php echo $valorcom[4] ?></td>
			<td><?php echo utf8_decode($entidadcom[5]) ?></td>
			<td><?php echo $valorcom[5] ?></td>
			<td></td>
			<td></td>
			<td><?php echo utf8_decode($entidadsan[0]) ?></td>
			<td><?php echo $valorsan[0] ?></td>
			<td><?php echo utf8_decode($entidadsan[1]) ?></td>
			<td><?php echo $valorsan[1] ?></td>
			<td><?php echo utf8_decode($entidadsan[2]) ?></td>
			<td><?php echo $valorsan[2] ?></td>
			<td><?php echo utf8_decode($entidadsan[3]) ?></td>
			<td><?php echo $valorsan[3] ?></td>
			<td><?php echo utf8_decode($entidadsan[4]) ?></td>
			<td><?php echo $valorsan[4] ?></td>
			<td><?php echo utf8_decode($entidadsan[5]) ?></td>
			<td><?php echo $valorsan[5] ?></td>
		</tr>
	<?php

		$j++;
	}

	?>
</table>