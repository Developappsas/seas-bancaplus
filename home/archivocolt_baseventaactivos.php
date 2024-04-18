<?php include('../functions.php'); 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
header("Content-Disposition: attachment; filename=Coltefinanciera - Base Venta Activos " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>Cedula</th>
		<th>No. de pagare</th>
		<th>Nombre</th>
		<th>Actividad econimica</th>
		<th>Tipo de contrato</th>
		<th>Antiguedad en la actividad (meses)</th>
		<th>Fecha de nacimiento</th>
		<th>Genero</th>
		<th>Estado civil</th>
		<th>No. de personas a cargo</th>
		<th>Tipo de vivienda</th>
		<th>Tiempo en la vivienda (meses)</th>
		<th>Ciudad de residencia</th>
		<th>Departamento de residencia</th>
		<th>Numero de telefono fijo deudor</th>
		<th>Direccion de residencia deudor</th>
		<th>Numero de telefono fijo referencia familiar</th>
		<th>Direccion de residencia referencia familiar</th>
		<th>Numero de telefono fijo referencia personal</th>
		<th>Direccion de residencia referencia personal</th>
		<th>Nombre Pagaduria</th>
		<th>Nit Pagaduria</th>
		<th>CIIU</th>
		<th>Valor ingresos ($)</th>
		<th>Deducciones de ley ($)</th>
		<th>Otras deducciones ($)</th>
		<th>Valor inicial credito ($)</th>
		<th>Saldo capital ($)</th>
		<th>Cuota credito ($)</th>
		<th>Plazo inicial credito (meses)</th>
		<th>Plazo restante credito (meses)</th>
		<th>Fecha desembolso</th>
		<th>Fecha primer descuento</th>
		<th>Fecha final</th>
		<th>Tasa efectiva anual</th>
		<th>Coincidencia en listas de control?</th>
		<th>Puntaje Acierta deudor</th>
		<th>Tipo contratacion</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT si.id_simulacion, si.cedula, si.nro_libranza, si.nombre, si.nivel_contratacion, so.ocupacion, so.tipo_contrato, so.fecha_vinculacion, (year(GETDATE()) - year(so.fecha_vinculacion)) * 12 + (month(GETDATE()) - month(so.fecha_vinculacion)) as meses_vinculacion, FORMAT(si.fecha_nacimiento, 'dd/MM/yyyy') as fecha_nacimiento, so.sexo, so.estado_civil, so.personas_acargo, so.tipo_vivienda, so.anios * 12 + so.meses as tiempo_vivienda, ci.municipio, ci.departamento, so.tel_residencia, so.celular, so.direccion, so.telefono_familiar, so.direccion_familiar, so.telefono_personal, so.direccion_personal, si.pagaduria, pp.nit as nit_pagaduria, si.total_ingresos, si.aportes, si.valor_credito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.plazo, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso, FORMAT(si.fecha_primera_cuota, 'dd/MM/yyyy') as fecha_primera_cuota, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_final, si.tasa_interes, si.puntaje_datacredito, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '" . $_REQUEST["id_venta"] . "' AND si.nivel_contratacion <> 'PENSIONADO'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}

		$queryDB .= " group by si.id_simulacion, si.cedula, si.nro_libranza, si.nombre, si.nivel_contratacion, so.ocupacion, so.tipo_contrato, so.fecha_vinculacion, si.fecha_nacimiento, so.sexo, so.estado_civil, so.personas_acargo, so.tipo_vivienda, so.anios, so.meses, ci.municipio, ci.departamento, so.tel_residencia, so.celular, so.direccion, so.telefono_familiar, so.direccion_familiar, so.telefono_personal, so.direccion_personal, si.pagaduria, pp.nit, si.total_ingresos, si.aportes, si.valor_credito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.plazo, si.fecha_desembolso, si.fecha_primera_cuota, si.tasa_interes, si.puntaje_datacredito, vd.cuota_hasta, vd.cuota_desde, vd.id_ventadetalle order by si.cedula, vd.id_ventadetalle";
	} else {
		$queryDB = "SELECT si.id_simulacion, si.cedula, si.nro_libranza, si.nombre, '1' as ocupacion, '' as tipo_contrato, '' as fecha_vinculacion, '' as meses_vinculacion, FORMAT(si.fecha_nacimiento, 'dd/MM/yyyy') as fecha_nacimiento, '' as sexo, '' as estado_civil, '' as personas_acargo, '' as tipo_vivienda, '' as tiempo_vivienda, si.ciudad as municipio, si.departamento, si.telefono as tel_residencia, si.celular, si.direccion, '' as telefono_familiar, '' as direccion_familiar, '' as telefono_personal, '' as direccion_personal, si.pagaduria, pp.nit as nit_pagaduria, 0 as total_ingresos, 0 as aportes, si.valor_credito, si.opcion_credito, si.opcion_cuota_cso, si.plazo, FORMAT(si.fecha_desembolso, 'dd/MM/yyyy') as fecha_desembolso, FORMAT(si.fecha_primera_cuota, 'dd/MM/yyyy') as fecha_primera_cuota, FORMAT(MAX(cu.fecha), 'dd/MM/yyyy') as fecha_final, si.tasa_interes, '' as puntaje_datacredito, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, SUM(cu.capital) as saldo_capital from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '" . $_REQUEST["id_venta"] . "' AND si.nivel_contratacion <> 'PENSIONADO'";

		if ($_SESSION["S_SECTOR"]) {
			$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
		}

		$queryDB .= " group by si.id_simulacion, si.cedula, si.nro_libranza, si.nombre, si.fecha_nacimiento, si.ciudad, si.departamento, si.telefono, si.celular, si.direccion, si.pagaduria, si.nivel_contratacion, pp.nit, si.valor_credito, si.opcion_credito, si.opcion_cuota_cso, si.plazo, si.fecha_desembolso, si.fecha_primera_cuota, si.tasa_interes, vd.cuota_hasta, vd.cuota_desde, vd.id_ventadetalle order by si.cedula, vd.id_ventadetalle";
	}

	$rs = sqlsrv_query($link, $queryDB);
	if ($rs == false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
				echo $queryDB;
			}
		}
	}


	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$ocupacion_nombre = "";

		switch ($fila["ocupacion"]) {
			case "1":
				$ocupacion_nombre = "EMPLEADO";
				break;
			case "2":
				$ocupacion_nombre = "EMPLEADO SOCIO";
				break;
			case "3":
				$ocupacion_nombre = "INDEPENDIENTE";
				break;
			case "4":
				$ocupacion_nombre = "HOGAR";
				break;
			case "5":
				$ocupacion_nombre = "PENSIONADO-JUBILADO";
				break;
			case "6":
				$ocupacion_nombre = "ESTUDIANTE";
				break;
			case "7":
				$ocupacion_nombre = "RENTISTA CAPITAL";
				break;
			case "8":
				$ocupacion_nombre = "TAXISTA";
				break;
			case "9":
				$ocupacion_nombre = "TRANSPORTADOR";
				break;
			case "99":
				$ocupacion_nombre = "NINGUNA";
				break;
		}

		$tipo_contrato_mombre = "";

		if ($fila["ocupacion"] == "1") {
			switch ($fila["tipo_contrato"]) {
				case "1":
					$tipo_contrato_mombre = "INDEFINIDO";
					break;
				case "2":
					$tipo_contrato_mombre = "TEMPORAL";
					break;
				case "4":
					$tipo_contrato_mombre = "CONTRATISTA";
					break;
				case "5":
					$tipo_contrato_mombre = "FIJO";
					break;
			}
		}

		$meses_vinculacion = "";

		if ($fila["fecha_vinculacion"] && $fila["fecha_vinculacion"] != "0000-00-00") {
			$diff_dias_ultimo_mes = date("j", strtotime(date("Y-m-d"))) - date("j", strtotime($fila["fecha_vinculacion"]));

			$meses_vinculacion = $diff_dias_ultimo_mes >= 0 ? $fila["meses_vinculacion"] : ($fila["meses_vinculacion"] - 1);
		}

		$sexo_nombre = "";

		switch ($fila["sexo"]) {
			case "M":
				$sexo_nombre = "Masculino";
				break;
			case "F":
				$sexo_nombre = "Femenino";
				break;
		}

		if ($fila["tel_residencia"])
			$telefono_fijo = utf8_decode($fila["tel_residencia"]);
		else
			$telefono_fijo = utf8_decode($fila["celular"]);

		$otras_deducciones = 0;

		if (!$_REQUEST["ext"]) {
			$queryDB = "SELECT SUM(CASE WHEN se_compra = 'NO' AND (id_entidad IS NOT NULL || (entidad IS NOT NULL AND entidad <> '')) THEN cuota ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '" . $fila["id_simulacion"] . "'";

			$rs1 = sqlsrv_query($link, $queryDB);

			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

			if ($fila1["s"])
				$otras_deducciones = $fila1["s"];
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

		$saldo_capital = $fila["saldo_capital"];

		if ($fila["cuotas_vendidas"] == $fila["plazo"])
			$saldo_capital = $fila["valor_credito"];

		$tasa_efectiva_anual = ((pow(1 + ($fila["tasa_interes"] / 100.00), 12)) - 1) * 100.00;

	?>
		<tr>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo trim(preg_replace("/\D/", "", $fila["nro_libranza"])) ?></td>
			<td><?php echo utf8_decode($fila["nombre"]) ?></td>
			<td><?php echo $ocupacion_nombre ?></td>
			<td><?php echo $tipo_contrato_mombre ?></td>
			<td><?php echo $meses_vinculacion ?></td>
			<td><?php echo $fila["fecha_nacimiento"] ?></td>
			<td><?php echo $sexo_nombre ?></td>
			<td><?php echo $fila["estado_civil"] ?></td>
			<td><?php echo $fila["personas_acargo"] ?></td>
			<td><?php echo $fila["tipo_vivienda"] ?></td>
			<td><?php echo $fila["tiempo_vivienda"] ?></td>
			<td><?php echo utf8_decode($fila["municipio"]) ?></td>
			<td><?php echo utf8_decode($fila["departamento"]) ?></td>
			<td><?php echo $telefono_fijo ?></td>
			<td><?php echo utf8_decode($fila["direccion"]) ?></td>
			<td><?php echo utf8_decode($fila["telefono_familiar"]) ?></td>
			<td><?php echo utf8_decode($fila["direccion_familiar"]) ?></td>
			<td><?php echo utf8_decode($fila["telefono_personal"]) ?></td>
			<td><?php echo utf8_decode($fila["direccion_personal"]) ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo $fila["nit_pagaduria"] ?></td>
			<td>10</td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo $fila["aportes"] ?></td>
			<td><?php echo $otras_deducciones ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $fila["fecha_desembolso"] ?></td>
			<td><?php echo $fila["fecha_primera_cuota"] ?></td>
			<td><?php echo $fila["fecha_final"] ?></td>
			<td><?php echo round($tasa_efectiva_anual, 2) ?></td>
			<td>No</td>
			<td><?php echo $fila["puntaje_datacredito"] ?></td>
			<td><?php echo $fila["nivel_contratacion"] ?></td>
		</tr>
	<?php

	}



	?>
</table>