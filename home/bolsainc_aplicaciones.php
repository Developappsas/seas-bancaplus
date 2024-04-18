<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA") || !$_SESSION["FUNC_BOLSAINCORPORACION"]) {
	exit;
}

$link = conectar();

$queryDB = "SELECT si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.tasa_interes, si.retanqueo_total, si.descuento1, DATEDIFF(day, EOMONTH(DATEADD(MONTH, -1, si.fecha_primera_cuota)), si.fecha_desembolso) as dias_desde_desembolso_hasta_un_mes_antes_primera_cuota, si.estado, si.saldo_bolsa from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$simulacion_rs = sqlsrv_query($link, $queryDB);

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs)) {
	exit;
}

$rs1 = sqlsrv_query($link, "SELECT SUM(capital) as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota = valor_cuota");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$saldo_capital_pendiente = $fila1["s"];

if (round($saldo_capital_pendiente) == 0)
	$saldo_capital_pendiente = 0;

$rs1 = sqlsrv_query($link, "SELECT SUM(saldo_cuota) as s from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota > 0");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$total_saldo = $fila1["s"];

if (round($total_saldo) == 0)
	$total_saldo = 0;

$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$tiene_plan_pagos = $fila1["c"];

$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_mora = $fila1["c"];

$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pagada = '1'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_pagadas = $fila1["c"];

switch ($simulacion["opcion_credito"]) {
	case "CLI":
		$opcion_cuota = $simulacion["opcion_cuota_cli"];
		break;
	case "CCC":
		$opcion_cuota = $simulacion["opcion_cuota_ccc"];
		break;
	case "CMP":
		$opcion_cuota = $simulacion["opcion_cuota_cmp"];
		break;
	case "CSO":
		$opcion_cuota = $simulacion["opcion_cuota_cso"];
		break;
}

$interes_diario = ($simulacion["valor_credito"] * $simulacion["tasa_interes"] / 100) / 30;

if ($simulacion["opcion_credito"] == "CLI")
	$simulacion["retanqueo_total"] = 0;

$intereses_anticipados = ($simulacion["valor_credito"] - $simulacion["retanqueo_total"]) * $simulacion["descuento1"] / 100.00;

$rs1 = sqlsrv_query($link, "SELECT SUM(valor) as s from bolsainc_aplicaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND tipo_aplicacion = 'INTERES_ANTICIPADO'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$intereses_anticipados += $fila1["s"];

$dias_cubiertos_por_interes_anticipado = round($intereses_anticipados) / $interes_diario;

$dias_pdtes_por_cubrir_de_interes_anticipado = $simulacion["dias_desde_desembolso_hasta_un_mes_antes_primera_cuota"] - $dias_cubiertos_por_interes_anticipado;

$interes_anticipado_pdte_por_cubrir = $dias_pdtes_por_cubrir_de_interes_anticipado * $interes_diario;

?>
<?php include("top.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((tipo_aplicacion.value == "") || (fecha.value == "") || (valor.value == "")) {
				alert("Todos los campos son obligatorios");
				return false;
			}
			if (valor.value == "0") {
				alert("El valor no puede ser cero");
				return false;
			}
			if (parseInt(valor.value.replace(/\,/g, '')) > <?php echo $simulacion["saldo_bolsa"] ?>) {
				alert("El valor no puede ser mayor al saldo de la bolsa de incorporacion ($<?php echo number_format($simulacion["saldo_bolsa"], 0, ".", ",") ?>)");
				return false;
			}
			if (tipo_aplicacion.value == "ABONOCAPITAL" && parseInt(valor.value.replace(/\,/g, '')) > <?php echo $saldo_capital_pendiente ?>) {
				alert("El valor no puede ser mayor al saldo capital pendiente ($<?php echo number_format($saldo_capital_pendiente, 0, ".", ",") ?>)");
				return false;
			}
			if (tipo_aplicacion.value == "CUOTA" && parseInt(valor.value.replace(/\,/g, '')) > <?php echo $total_saldo ?>) {
				alert("El valor no puede ser mayor al saldo total del credito ($<?php echo number_format($total_saldo, 0, ".", ",") ?>)");
				return false;
			}
			if (tipo_aplicacion.value == "INTERES_ANTICIPADO" && parseInt(valor.value.replace(/\,/g, '')) > <?php echo round($interes_anticipado_pdte_por_cubrir) ?>) {
				alert("El valor no puede ser mayor al valor de interes anticipado pendiente ($<?php echo number_format($interes_anticipado_pdte_por_cubrir, 0, ".", ",") ?>)");
				return false;
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="bolsainc.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Aplicaciones Bolsa</b><br><br></center>
		</td>
	</tr>
</table>
<?php

if ($simulacion["estado"] != "CAN" && $simulacion["saldo_bolsa"] > 0 && $_SESSION["S_SOLOLECTURA"] != "1") {

?>
	<form name=formato method=post action="bolsainc_aplicaciones_crear.php" onSubmit="return chequeo_forma()">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
			<tr>
				<td>
					<div class="box1 clearfix">
						<table border="0" cellspacing=1 cellpadding=2>
							<tr>
								<td valign="bottom">Tipo Aplicaci&oacute;n<br>
									<select name="tipo_aplicacion" style="background-color:#EAF1DD;">
										<option value=""></option>
										<?php if ($tiene_plan_pagos) { ?><option value="CUOTA">CUOTA</option><?php } ?>
										<?php if (!$cuotas_mora && $cuotas_pagadas) { ?><option value="ABONOCAPITAL">ABONO A CAPITAL</option><?php } ?>
										<option value="INTERES_ANTICIPADO">INTERES ANTICIPADO</option>
										<option value="SEGURO_PERSONAL">SEGURO PERSONAL</option>
									</select>&nbsp;&nbsp;&nbsp;
								</td>
								<td>F Aplicaci&oacute;n<br><input type="text" name="fecha" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}">&nbsp;&nbsp;&nbsp;</td>
								<td>Valor<br><input type="text" name="valor" size="10" maxlength="10" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
								<td valign="bottom">&nbsp;<br><input type="submit" value="Ingresar"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<hr noshade size=1 width=350>
	<br>
<?php

}

if ($_REQUEST["action"]) {
	$queryDB = "SELECT bap.* from bolsainc_aplicaciones bap INNER JOIN simulaciones si ON bap.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where bap.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND bap.valor > 0";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	$queryDB .= " order by bap.consecutivo";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["chk" . $fila["consecutivo"]] == "1") {
			if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA")) {
				sqlsrv_query($link, "update bolsainc_aplicaciones set valor_anulacion = valor, usuario_anulacion = '" . $_SESSION["S_LOGIN"] . "', fecha_anulacion = GETDATE(), valor = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and consecutivo = '" . $fila["consecutivo"] . "'");

				sqlsrv_query($link, "update simulaciones set saldo_bolsa = saldo_bolsa + " . $fila["valor"] . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

				if ($fila["tipo_aplicacion"] == "CUOTA" || $fila["tipo_aplicacion"] == "ABONOCAPITAL") {
					sqlsrv_query($link, "update pagos_detalle set valor_anulacion = valor, usuario_anulacion = '" . $_SESSION["S_LOGIN"] . "', fecha_anulacion = GETDATE(), valor = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and consecutivo = '" . $fila["consecutivo_pago"] . "'");

					if ($fila["tipo_aplicacion"] == "CUOTA") {
						$rs2 = sqlsrv_query($link, "select * from pagos_detalle where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND consecutivo = '" . $fila["consecutivo_pago"] . "'");

						while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
							sqlsrv_query($link, "update cuotas set saldo_cuota = saldo_cuota + " . $fila2["valor_anulacion"] . ", pagada = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila2["cuota"] . "'");
						}
					} else {
						$queryDB = "SELECT si.*, cu.seguro from simulaciones si INNER JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cu.cuota = '1'";

						$rs1 = sqlsrv_query($link, $queryDB);

						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						$tasa_interes = $fila1["tasa_interes"];

						$plazo = $fila1["plazo"];

						$seguro_org = $fila1["seguro"];

						switch ($fila1["opcion_credito"]) {
							case "CLI":
								$opcion_cuota = $fila1["opcion_cuota_cli"];
								break;
							case "CCC":
								$opcion_cuota = $fila1["opcion_cuota_ccc"];
								break;
							case "CMP":
								$opcion_cuota = $fila1["opcion_cuota_cmp"];
								break;
							case "CSO":
								$opcion_cuota = $fila1["opcion_cuota_cso"];
								break;
						}

						$valor_cuota = $opcion_cuota - $fila1["seguro"];

						$rs1 = sqlsrv_query($link, "SELECT valor_antes_pago, valor_anulacion from pagos_detalle where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND consecutivo = '" . $fila["consecutivo_pago"] . "' AND cuota = '0'");

						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						$saldo = $fila1["valor_antes_pago"];

						$valor_abono = $fila1["valor_anulacion"];

						$rs1 = sqlsrv_query($link, "SELECT MAX(cuota) as m from pagos_detalle where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND consecutivo < '" . $fila["consecutivo_pago"] . "' AND valor > 0");

						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						$cuota_anterior_ajuste_plan_pagos = $fila1["m"];

						for ($i = $cuota_anterior_ajuste_plan_pagos + 1; $i <= $plazo; $i++) {
							if ($saldo > 0) {
								$interes = $saldo * $tasa_interes / 100.00;

								$capital = $valor_cuota - round($interes);

								$seguro = $seguro_org;

								$saldo -= $capital;

								if ($saldo < 0) {
									$capital += $saldo;
									$saldo = 0;
								} else {
									if ($i == $plazo) {
										$valor_cuota += $saldo;

										$capital = $valor_cuota - $interes;

										$saldo = 0;
									}
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

							sqlsrv_query($link, "UPDATE cuotas set capital = '" . round($capital) . "', interes = '" . round($interes) . "', seguro = '" . round($seguro) . "', valor_cuota = '" . round($total_cuota) . "', saldo_cuota = '" . round($saldo_cuota) . "', pagada = '" . $pagada . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $i . "'");
						}

						sqlsrv_query($link, "UPDATE cuotas set abono_capital = abono_capital - " . $fila["valor"] . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $cuota_anterior_ajuste_plan_pagos . "'");

						$rs1 = sqlsrv_query($link, "SELECT * from cuotas where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND abono_capital != '0'");

						if (!sqlsrv_num_rows($rs1))
							sqlsrv_query($link, "UPDATE cuotas set capital_org = NULL, interes_org = NULL where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");
					}
				}
			}
		}
	}
}

$queryDB = "SELECT bap.* from bolsainc_aplicaciones bap INNER JOIN simulaciones si ON bap.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where bap.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND bap.valor > 0";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$queryDB .= " order by bap.consecutivo";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs)) {
?>
	<form name="formato3" method="post" action="bolsainc_aplicaciones.php">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>T Aplicaci&oacute;n</th>
				<th>F Aplicaci&oacute;n</th>
				<th>Valor</th>
				<th>Usuario</th>
				<th>Fecha</th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/delete.png" title="Borrar Aplicaci&oacute;n"></th><?php } ?>
			</tr>
			<?php

			$rs1 = sqlsrv_query($link, "SELECT top 1 consecutivo, consecutivo_pago from bolsainc_aplicaciones where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND valor > 0 AND tipo_aplicacion IN ('CUOTA', 'ABONOCAPITAL') order by consecutivo DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			if (sqlsrv_num_rows($rs1)) {
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$consecutivo_ultima_aplicacion = $fila1["consecutivo"];

				$rs2 = sqlsrv_query($link, "SELECT * from pagos_detalle where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND valor > 0 AND consecutivo > '" . $fila1["consecutivo_pago"] . "' LIMIT 1");

				if (sqlsrv_num_rows($rs2)) {
					$hay_recaudos_posteriores = 1;
				}
			} else {
				$consecutivo_ultima_aplicacion = 0;
			}

			$j = 1;

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

			?>
				<tr <?php echo $tr_class ?>>
					<td><?php echo $fila["tipo_aplicacion"] ?></td>
					<td><?php echo $fila["fecha"] ?></td>
					<td align="right"><?php echo number_format($fila["valor"], 0) ?></td>
					<td><?php echo $fila["usuario_creacion"] ?></td>
					<td><?php echo $fila["fecha_creacion"] ?></td>
					<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="checkbox" name="chk<?php echo $fila["consecutivo"] ?>" value="1" <?php if (($fila["tipo_aplicacion"] == "CUOTA" || $fila["tipo_aplicacion"] == "ABONOCAPITAL") && ($hay_recaudos_posteriores || $fila["consecutivo"] != $consecutivo_ultima_aplicacion)) {
																																																															echo " disabled";
																																																														} ?>></td><?php } ?>
				</tr>
			<?php

				$j++;
			}

			?>
		</table>
		<br>
		<?php

		if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") {

		?>
			<p align="center"><input type="submit" value="Borrar" onClick="document.formato3.action.value='borrar'"></p>
		<?php

		}

		?>
	</form>
<?php

} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>