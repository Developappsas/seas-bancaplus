<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../functions.php');
include('../function_blob_storage.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
 


if ($_SESSION["FUNC_BOLSAINCORPORACION"] && !$_REQUEST["ext"]) {
	$queryDB = "SELECT dbo.fn_total_recaudado(id_simulacion, 0) as total_recaudado, FORMAT(fecha_primera_cuota, 'Y-m') as mes_primera_cuota, FORMAT(CONVERT(date,'" . $_REQUEST["fecha"] . "', 126), 'yyyy-MM') as mes_recaudo from simulaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";
	
	$rs = sqlsrv_query($link, $queryDB);
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	$tiene_recaudo = $fila["total_recaudado"];

	if (!$tiene_recaudo) {
		echo "true";
		if (str_replace(",", "", $_REQUEST["valor_aplicar1"]) < str_replace(",", "", $_REQUEST["saldo_cuota1"])) {
			if ($fila["mes_recaudo"] < $fila["mes_primera_cuota"]) {
				sqlsrv_query($link, "BEGIN");

				$queryDB = "SELECT CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from bolsainc_pagos where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

				$rs1 = sqlsrv_query($link, $queryDB);

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$consecutivo = $fila1["max_c"];

				sqlsrv_query($link, "insert into bolsainc_pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '" . $_REQUEST["fecha"] . "', '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "', '1', '" . $_REQUEST["tipo_recaudo"] . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

				if (strcmp($_FILES["archivo"]["name"], "")) {
					$uniqueID = date("YmdHis");

					sqlsrv_query($link, "update bolsainc_pagos set nombre_original = '" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', nombre_grabado = '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND consecutivo = '" . $consecutivo . "'");

					$fechaa = new DateTime();
					$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

					$metadata1 = array(
						'id_simulacion' => $_REQUEST["id_simulacion"],
						'descripcion' => "Soporte recaudo bolsa incorparacion manual",
						'usuario_creacion' => $_SESSION["S_LOGIN"],
						'fecha_creacion' => $fechaFormateada
					);

					upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
				}

				sqlsrv_query($link, "UPDATE simulaciones set saldo_bolsa = saldo_bolsa + " . str_replace(",", "", $_REQUEST["valor_aplicar"]) . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

				sqlsrv_query($link, "COMMIT");

				$mensaje = "El recaudo no cumple las condiciones para ser aplicado. Sera llevado a la bolsa de incorporacion";
			} else {
				$aplicar_pago = 1;
			}
		} else {
			$aplicar_pago = 1;
		}
	} else {
		echo "false";
		$aplicar_pago = 1;
	}
} else {
	echo "prueba numero 3";
	$aplicar_pago = 1;
}

if ($aplicar_pago) {
	sqlsrv_query($link, "BEGIN");

	$queryDB = "SELECT CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from pagos" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	$rs1 = sqlsrv_query($link, $queryDB);

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$consecutivo = $fila1["max_c"];

	sqlsrv_query($link, "insert into pagos" . $sufijo . " (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '" . $_REQUEST["fecha"] . "', '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "', '1', '" . $_REQUEST["tipo_recaudo"] . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

	if (strcmp($_FILES["archivo"]["name"], "")) {
		$uniqueID = date("YmdHis");

		sqlsrv_query($link, "update pagos" . $sufijo . " set nombre_original = '" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "', nombre_grabado = '" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]) . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND consecutivo = '" . $consecutivo . "'");

		$fechaa = new DateTime();
		$fechaFormateada = $fechaa->format("d-m-Y H:i:s");

		$metadata1 = array(
			'id_simulacion' => $_REQUEST["id_simulacion"],
			'descripcion' => "Soporte recaudo manual",
			'usuario_creacion' => $_SESSION["S_LOGIN"],
			'fecha_creacion' => $fechaFormateada
		);

		upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $uniqueID . "_" . reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);

	
		
		var_dump($metadata1);
		echo "<br>";
		var_dump($_FILES);
	}
	exit("\n ya");
	if ($_REQUEST["tipo_recaudo"] == "NOMINA" || $_REQUEST["tipo_recaudo"] == "VENTANILLA" || $_REQUEST["tipo_recaudo"] == "ABONOCAPITAL") {
		if ($_REQUEST["tipo_recaudo"] == "NOMINA" || $_REQUEST["tipo_recaudo"] == "VENTANILLA") {
			$queryDB = "select cu.*, si.plazo, DATEDIFF(DAY, EOMONTH('" . $_REQUEST["fecha"] . "'), si.fecha_primera_cuota) as diferencia_fecha_primera_cuota from cuotas" . $sufijo . " cu INNER JOIN simulaciones" . $sufijo . " si ON cu.id_simulacion = si.id_simulacion where cu.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cu.saldo_cuota > 0 AND cu.fecha <= DATEADD(MONTH,  4, GETDATE()) order by cu.cuota";
			
			$rs = sqlsrv_query($link, $queryDB);

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				if ($_REQUEST["valor_aplicar" . $fila["cuota"]]) {
					sqlsrv_query($link, "insert into pagos_detalle" . $sufijo . " (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '" . $fila["cuota"] . "', '" . str_replace(",", "", $_REQUEST["valor_aplicar" . $fila["cuota"]]) . "', '" . str_replace(",", "", $_REQUEST["saldo_cuota" . $fila["cuota"]]) . "')");

					if (str_replace(",", "", $_REQUEST["valor_aplicar" . $fila["cuota"]]) == str_replace(",", "", $_REQUEST["saldo_cuota" . $fila["cuota"]]))
						$pagada = "1";
					else
						$pagada = "0";

					sqlsrv_query($link, "update cuotas" . $sufijo . " set saldo_cuota = saldo_cuota - " . str_replace(",", "", $_REQUEST["valor_aplicar" . $fila["cuota"]]) . ", pagada = '" . $pagada . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");

					//Si se recauda el 100% de la primera cuota, se ajusta fecha primera cuota
					if (!$_REQUEST["ext"] && $fila["cuota"] == "1" && $pagada & $fila["diferencia_fecha_primera_cuota"] > 0) {
						$fecha_tmp = $_REQUEST["fecha"];

						$fecha = new DateTime($fecha_tmp);

						sqlsrv_query($link, "update simulaciones set fecha_primera_cuota = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

						sqlsrv_query($link, "insert into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('" . $_REQUEST["id_simulacion"] . "', '" . $fecha->format('Y-m-t') . "', 'system', GETDATE())");

						for ($j = 1; $j <= $fila["plazo"]; $j++) {
							$fecha = new DateTime($fecha->format('Y-m-01'));

							sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cuota = '" . $j . "'");

							$fecha->add(new DateInterval('P1M'));
						}
					}
				}
			}
		} elseif ($_REQUEST["tipo_recaudo"] == "ABONOCAPITAL") {
			$queryDB = "select si.*, cu.seguro from simulaciones" . $sufijo . " si INNER JOIN cuotas" . $sufijo . " cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cu.cuota = '1'";

			$rs = sqlsrv_query($link, $queryDB);

			$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

			$tasa_interes = $fila["tasa_interes"];

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

			$valor_cuota = $opcion_cuota - $fila["seguro"];

			$queryDB = "select SUM(capital) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND saldo_cuota = valor_cuota";

			$rs1 = sqlsrv_query($link, $queryDB);

			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

			$saldo_capital = $fila1["s"];

			sqlsrv_query($link, "insert into pagos_detalle" . $sufijo . " (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '0', '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "', '" . $saldo_capital . "')");

			$saldo = $saldo_capital - str_replace(",", "", $_REQUEST["valor_aplicar"]);

			$queryDB = "select * from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota = valor_cuota order by cuota";

			$rs = sqlsrv_query($link, $queryDB);

			$primera_iteracion = 1;

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				if ($primera_iteracion) {
					sqlsrv_query($link, "update cuotas" . $sufijo . " set abono_capital = abono_capital + " . str_replace(",", "", $_REQUEST["valor_aplicar"]) . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . ($fila["cuota"] - 1) . "'");

					$primera_iteracion = 0;
				}

				if ($saldo > 0) {
					$interes = $saldo * $tasa_interes / 100.00;

					$capital = $valor_cuota - round($interes);

					$seguro = $fila["seguro"];

					$saldo -= $capital;

					if ($saldo < 0) {
						$capital += $saldo;
						$saldo = 0;
					}

					$pagada = 0;
				} else {
					$interes = 0;
					$capital = 0;
					$seguro = 0;
					$pagada = 1;
				}

				$total_cuota = round($capital) + round($interes) + round($seguro);

				$saldo_cuota = $total_cuota;

				sqlsrv_query($link, "UPDATE cuotas" . $sufijo . " set capital_org = (CASE WHEN capital_org IS NULL THEN capital ELSE capital_org END), interes_org = (CASE WHEN interes_org IS NULL THEN interes ELSE interes_org END), capital = '" . round($capital) . "', interes = '" . round($interes) . "', seguro = '" . round($seguro) . "', valor_cuota = '" . round($total_cuota) . "', saldo_cuota = '" . round($saldo_cuota) . "', pagada = '" . $pagada . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
			}
		}

		$queryDB = "SELECT SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

		$rs1 = sqlsrv_query($link, $queryDB);

		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

		if ($fila1["s"] == 0) {
			sqlsrv_query($link, "UPDATE simulaciones" . $sufijo . " set estado = 'CAN', retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");
		}

		if (!$_REQUEST["ext"]) {
			//Para saber si ya hubo recaudo completo en el mes que se aplica el recaudo
			$queryDB = "SELECT valor_cuota - CASE WHEN dbo.fn_total_recaudado_mes(" . $_REQUEST["id_simulacion"] . ", 0, '" . $_REQUEST["fecha"] . "') IS NULL THEN 0 ELSE dbo.fn_total_recaudado_mes(" . $_REQUEST["id_simulacion"] . ", 0, '" . $_REQUEST["fecha"] . "') END as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND FORMAT(fecha, 'yyyy-MM') =
			 FORMAT(CONVERT(DATE,'" . $_REQUEST["fecha"] . "', 126), 'yyyy-MM')";

			$rs1 = sqlsrv_query($link, $queryDB);
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

			if ($fila1["s"] <= 0) {
				sqlsrv_query($link, "DELETE from cuotas_norecaudadas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha = EOMONTH('" . $_REQUEST["fecha"] . "')");
			}
		}
	} else {
		sqlsrv_query($link, "INSERT into pagos_detalle" . $sufijo . " (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '0', '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "', '" . str_replace(",", "", $_REQUEST["valor_aplicar"]) . "')");
	}

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, dbo.fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
	} else {
		$queryDB = "select vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cso, dbo.fn_total_recaudado(si.id_simulacion, 1) as total_recaudado from ventas_detalle" . $sufijo . " vd INNER JOIN ventas" . $sufijo . " ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
	}

	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)	);

	if (sqlsrv_num_rows($rs)) {
		$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

		$opcion_cuota = "0";

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

		$cuota_desde = ceil($fila["total_recaudado"] / $opcion_cuota) + 1;

		sqlsrv_query($link, "update ventas_detalle" . $sufijo . " set cuota_desde = '" . $cuota_desde . "' where id_ventadetalle = '" . $fila["id_ventadetalle"] . "'");
	}

	sqlsrv_query($link, "COMMIT");

	$mensaje = "Recaudo ingresado exitosamente";
}
exit();
?>
<script>
	alert("<?php echo $mensaje ?>");

	opener.location.href = 'cartera_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

	window.close();
</script>