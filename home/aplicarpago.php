<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

$sufijo = '';

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "SELECT si.estado from simulaciones" . $sufijo . " si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND (si.estado IN ('DES', 'CAN')";

if (!$_REQUEST["ext"])
	$queryDB .= " OR (si.estado = 'EST' AND si.decision = '" . $label_viable . "' AND ((si.id_subestado IN (" . $subestado_compras_desembolso . ") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN (" . $subestado_desembolso . ", '" . $subestado_desembolso_cliente . "', '" . $subestado_desembolso_pdte_bloqueo . "', '78', ".$subestados_desembolso_nuevos_tesoreria."))))";

$queryDB .= ")";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!sqlsrv_num_rows($rs)) {
	exit;
}

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$rs1 = sqlsrv_query($link, "SELECT SUM(capital) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota = valor_cuota");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$saldo_capital_pendiente = $fila1["s"];

if (round($saldo_capital_pendiente) == 0)
	$saldo_capital_pendiente = 0;

$rs1 = sqlsrv_query($link, "SELECT SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and saldo_cuota > 0 AND fecha <= DATEADD(MONTH,  4, GETDATE())");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$total_saldo = $fila1["s"];

if (round($total_saldo) == 0)
	$total_saldo = 0;

$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_mora = $fila1["c"];

$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pagada = '1'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_pagadas = $fila1["c"];

?>
<?php include("top2.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		var suma_aplicaciones = 0;

		with(document.formato) {
			if ((tipo_recaudo.value == "") || (fecha.value == "") || (valor_aplicar.value == "") || ((elements[5].value == "") && (tipo_recaudo.value == "NOMINA" || tipo_recaudo.value == "VENTANILLA"))) {
				alert("Los campos marcados con asterisco (*) son obligatorios");
				return false;
			}
			if (valor_aplicar.value == "0") {
				alert("El Valor Total no puede ser cero");
				return false;
			}
			if (tipo_recaudo.value == "ABONOCAPITAL" && parseInt(valor_aplicar.value.replace(/\,/g, '')) > <?php echo $saldo_capital_pendiente ?>) {
				alert("El Valor Total no puede ser mayor al saldo capital pendiente ($<?php echo number_format($saldo_capital_pendiente, 0, ".", ",") ?>)");
				return false;
			}
			if ((tipo_recaudo.value == "VENTANILLA" || tipo_recaudo.value == "ABONOCAPITAL") && archivo.value == "") {
				alert("Debe adjuntar soporte del pago");
				return false;
			}
			<?php

			if ($fila["estado"] != "CAN") {

			?>
				if (tipo_recaudo.value != "ABONOCAPITAL") {
					if (parseInt(valor_aplicar.value.replace(/\,/g, '')) > <?php echo $total_saldo ?>) {
						alert("El Valor Total no puede ser mayor a la suma de los saldos de las cuotas en pantalla ($<?php echo number_format($total_saldo, 0, ".", ",") ?>)");
						return false;
					}
					if (tipo_recaudo.value == "NOMINA" || tipo_recaudo.value == "VENTANILLA") {
						for (i = 6; i <= elements.length - 6; i = i + 2) {
							if (elements[i].value != "") {
								suma_aplicaciones = suma_aplicaciones + parseInt(elements[i].value.replace(/\,/g, ''));
							}
						}
						if (suma_aplicaciones != parseInt(valor_aplicar.value.replace(/\,/g, ''))) {
							alert("La suma de los Valores a Aplicar no coincide con el Valor Total del recaudo");
							return false;
						}
					}
				}


			<?php

			}

			?>
		}
	}

	
	<?php

	if ($fila["estado"] != "CAN") {

	?>

		function distribuir() {
			with(document.formato) {
				if (tipo_recaudo.value == "NOMINA" || tipo_recaudo.value == "VENTANILLA") {
					if (valor_aplicar.value != "0" && parseInt(valor_aplicar.value.replace(/\,/g, '')) != parseInt(valor_aplicarh.value.replace(/\,/g, ''))) {
						valor_aplicarh.value = parseInt(valor_aplicar.value.replace(/\,/g, ''));

						por_distribuir = parseInt(valor_aplicar.value.replace(/\,/g, ''));

						limpiar_distribucion();

						for (i = 6; i <= elements.length - 6; i = i + 2) {
							if (parseInt(elements[i - 1].value) <= por_distribuir) {
								elements[i].value = elements[i - 1].value;

								separador_miles(elements[i]);

								por_distribuir = por_distribuir - parseInt(elements[i - 1].value);
							} else {
								elements[i].value = por_distribuir;

								separador_miles(elements[i]);

								break;
							}
						}
					}
				}
			}
		}

		function limpiar_distribucion() {
			with(document.formato) {
				for (i = 6; i <= elements.length - 6; i = i + 2) {
					elements[i].value = "";
				}
			}
		}

		function deshabilitar_detalle(opcion) {
			with(document.formato) {
				for (i = 6; i <= elements.length - 6; i = i + 2) {
					if (opcion == 1) {
						elements[i].value = "";
						elements[i].style.backgroundColor = "#FFFFFF";
						elements[i].disabled = true;
					} else {
						elements[i].style.backgroundColor = "#EAF1DD";
						elements[i].disabled = false;
					}
				}
			}
		}
	<?php

	}

	?>
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
	<tr>
		<td class="titulo">
			<center><b>Ingresar Recaudo</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="aplicarpago2.php?ext=<?php echo $_REQUEST["ext"] ?>" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td>
				<h2>DATOS RECAUDO</h2>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td>* T RECAUDO</td>
							<td>
								<select name="tipo_recaudo" style="background-color:#EAF1DD;" <?php if ($fila["estado"] != "CAN") { ?> onChange="if (this.value == 'NOMINA_DEV' || this.value == 'VENTANILLA_DEV' || this.value == 'ABONOCAPITAL') { deshabilitar_detalle(1); } else { deshabilitar_detalle(0); }<?php } ?>">
									<option value=""></option>
									<?php

									if ($fila["estado"] != "CAN") {

									?>
										<option value="NOMINA">NOMINA</option>
										<option value="VENTANILLA">VENTANILLA</option>
										<?php if (!$cuotas_mora && $cuotas_pagadas) { ?><option value="ABONOCAPITAL">ABONO A CAPITAL</option><?php } ?>
									<?php

									} else {

									?>
										<option value="NOMINA_DEV">NOMINA DEVOLUCI&Oacute;N</option>
										<option value="VENTANILLA_DEV">VENTANILLA DEVOLUCI&Oacute;N</option>
									<?php

									}

									?>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td>* F RECAUDO</td>
							<td><input type="text" name="fecha" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}"></td>
							<td width="20">&nbsp;</td>
							<td>* VR TOTAL</td>
							<td><input type="text" name="valor_aplicar" size="10" maxlength="10" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } separador_miles(this);<?php if ($fila["estado"] != "CAN") { ?> distribuir();<?php } ?> }" style="text-align:right; background-color:#EAF1DD">
								<input type="hidden" name="valor_aplicarh">
							</td>
							<td width="20">&nbsp;</td>
							<td>SOPORTE</td>
							<td><input type="file" name="archivo" style="text-align:center; background-color:#EAF1DD;"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<?php

	if ($fila["estado"] != "CAN") {

	?>
		<br>
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>No. Cuota</th>
				<th width="90">F Cuota</th>
				<th width="90">Vr Cuota</th>
				<th width="90">Saldo Cuota</th>
				<th width="90">Vr a Aplicar (*)</th>
			</tr>
			<?php

			$queryDB = "SELECT * from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND saldo_cuota > 0 AND fecha <= DATEADD(MONTH,4, GETDATE()) order by cuota";

			$rs1 = sqlsrv_query($link, $queryDB);

			$j = 1;

			while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

			?>
				<tr <?php echo $tr_class ?>>
					<td align="center"><?php echo $fila1["cuota"] ?></td>
					<td align="center"><?php echo $fila1["fecha"] ?></td>
					<td align="right"><?php echo number_format($fila1["valor_cuota"], 0) ?></td>
					<td align="right"><?php echo number_format($fila1["saldo_cuota"], 0) ?></td>
					<td align="right"><input type="hidden" name="saldo_cuota<?php echo $fila1["cuota"] ?>" value="<?php echo $fila1["saldo_cuota"] ?>"><input type="text" name="valor_aplicar<?php echo $fila1["cuota"] ?>" size="15" maxlength="11" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } if (parseInt(this.value) > saldo_cuota<?php echo $fila1["cuota"] ?>.value) { alert('El valor a aplicar no puede ser mayor al saldo de la cuota'); this.value = saldo_cuota<?php echo $fila1["cuota"] ?>.value; } separador_miles(this); }" style="text-align:right; background-color:#EAF1DD"></td>
				</tr>
			<?php

				$j++;
			}

			?>
		</table>
	<?php

	}

	?>
	<br>
	<p align="center">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<input type="submit" value="Ingresar">
	</p>
</form>
<?php include("bottom2.php"); ?>