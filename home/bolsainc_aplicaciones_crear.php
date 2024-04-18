<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA") || !$_SESSION["FUNC_BOLSAINCORPORACION"]) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "BEGIN");

$queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from bolsainc_aplicaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

$rs1 = sqlsrv_query($link, $queryDB);

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$consecutivo = $fila1["max_c"];

sqlsrv_query($link, "insert into bolsainc_aplicaciones (id_simulacion, consecutivo, tipo_aplicacion, fecha, valor, usuario_creacion, fecha_creacion) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo . "', '" . $_REQUEST["tipo_aplicacion"] . "', '" . $_REQUEST["fecha"] . "', '" . str_replace(",", "", $_REQUEST["valor"]) . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

sqlsrv_query($link, "UPDATE simulaciones set saldo_bolsa = saldo_bolsa - " . str_replace(",", "", $_REQUEST["valor"]) . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

if ($_REQUEST["tipo_aplicacion"] == "CUOTA" || $_REQUEST["tipo_aplicacion"] == "ABONOCAPITAL") {
	$queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from pagos where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	$rs1 = sqlsrv_query($link, $queryDB);

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$consecutivo_pago = $fila1["max_c"];

	if ($_REQUEST["tipo_aplicacion"] == "CUOTA")
		$tipo_recaudo = "BOLSA";
	else
		$tipo_recaudo = "BOLSA - ABONOCAPITAL";

	sqlsrv_query($link, "insert into pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo_pago . "', '" . $_REQUEST["fecha"] . "', '" . str_replace(",", "", $_REQUEST["valor"]) . "', '1', '" . $tipo_recaudo . "', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

	sqlsrv_query($link, "update bolsainc_aplicaciones set id_simulacion_pago = '" . $_REQUEST["id_simulacion"] . "', consecutivo_pago = '" . $consecutivo_pago . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and consecutivo = '" . $consecutivo . "'");

	if ($_REQUEST["tipo_aplicacion"] == "CUOTA") {
		$temp_valor = str_replace(",", "", $_REQUEST["valor"]);

		$queryDB = "SELECT cu.*, si.plazo, DATEDIFF(day,si.fecha_primera_cuota, EOMONTH('" . $_REQUEST["fecha"] . "')) as diferencia_fecha_primera_cuota from cuotas cu INNER JOIN simulaciones si ON cu.id_simulacion = si.id_simulacion where cu.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cu.saldo_cuota > 0 order by cu.cuota";

		$rs = sqlsrv_query($link, $queryDB);

		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
			if ($temp_valor > 0) {
				if ($temp_valor > $fila["saldo_cuota"])
					$valor_aplicar = $fila["saldo_cuota"];
				else
					$valor_aplicar = $temp_valor;

				sqlsrv_query($link, "INSERT into pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo_pago . "', '" . $fila["cuota"] . "', '" . $valor_aplicar . "', '" . $fila["saldo_cuota"] . "')");

				if ($valor_aplicar == $fila["saldo_cuota"])
					$pagada = "1";
				else
					$pagada = "0";

				sqlsrv_query($link, "update cuotas set saldo_cuota = saldo_cuota - " . $valor_aplicar . ", pagada = '" . $pagada . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");

				$temp_valor -= $valor_aplicar;

				//Si se recauda el 100% de la primera cuota, se ajusta fecha primera cuota
				if ($fila["cuota"] == "1" && $pagada & $fila["diferencia_fecha_primera_cuota"] > 0) {
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
			} else {
				break;
			}
		}
	} else {
		$queryDB = "select si.*, cu.seguro from simulaciones si INNER JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cu.cuota = '1'";

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

		$queryDB = "select SUM(capital) as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND saldo_cuota = valor_cuota";

		$rs1 = sqlsrv_query($link, $queryDB);

		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

		$saldo_capital = $fila1["s"];

		sqlsrv_query($link, "insert into pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $_REQUEST["id_simulacion"] . "', '" . $consecutivo_pago . "', '0', '" . str_replace(",", "", $_REQUEST["valor"]) . "', '" . $saldo_capital . "')");

		$saldo = $saldo_capital - str_replace(",", "", $_REQUEST["valor"]);

		$queryDB = "select * from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota = valor_cuota order by cuota";

		$rs = sqlsrv_query($link, $queryDB);

		$primera_iteracion = 1;

		while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
			if ($primera_iteracion) {
				sqlsrv_query($link, "update cuotas set abono_capital = abono_capital + " . str_replace(",", "", $_REQUEST["valor"]) . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . ($fila["cuota"] - 1) . "'");

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

			sqlsrv_query($link, "update cuotas set capital_org = (CASE WHEN capital_org IS NULL THEN capital ELSE capital_org END), interes_org = (CASE WHEN interes_org IS NULL THEN interes ELSE interes_org END), capital = '" . round($capital) . "', interes = '" . round($interes) . "', seguro = '" . round($seguro) . "', valor_cuota = '" . round($total_cuota) . "', saldo_cuota = '" . round($saldo_cuota) . "', pagada = '" . $pagada . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
		}
	}

	$queryDB = "SELECT SUM(saldo_cuota) as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	$rs1 = sqlsrv_query($link, $queryDB);

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	if ($fila1["s"] == 0) {
		sqlsrv_query($link, "UPDATE simulaciones set estado = 'CAN', retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");
	}

	//Para saber si ya hubo recaudo completo en el mes que se aplica el recaudo
	$queryDB = "SELECT valor_cuota - CASE WHEN dbo.fn_total_recaudado_mes(" . $_REQUEST["id_simulacion"] . ", 0, '" . $_REQUEST["fecha"] . "') IS NULL THEN 0 ELSE fn_total_recaudado_mes(" . $_REQUEST["id_simulacion"] . ", 0, '" . $_REQUEST["fecha"] . "') END as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND FORMAT(fecha, 'yyyy-MM') = FORMAT('" . $_REQUEST["fecha"] . "', 'yyyy-MM')";

	$rs1 = sqlsrv_query($link, $queryDB);

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	if ($fila1["s"] <= 0) {
		sqlsrv_query($link, "delete from cuotas_norecaudadas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha = EOMONTH('" . $_REQUEST["fecha"] . "')");
	}

	$queryDB = "SELECT vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, dbo.fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";

	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

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

		sqlsrv_query($link, "update ventas_detalle set cuota_desde = '" . $cuota_desde . "' where id_ventadetalle = '" . $fila["id_ventadetalle"] . "'");
	}
}

sqlsrv_query($link, "COMMIT");

$mensaje = "Aplicacion ingresada exitosamente";

?>
<script>
	alert("<?php echo $mensaje ?>");

	window.location = 'bolsainc_aplicaciones.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>