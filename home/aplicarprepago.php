<?php
include('../functions.php');
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select si.* from simulaciones" . $sufijo . " si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND si.estado = 'DES'";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs)) {
	exit;
}

$rs1 = sqlsrv_query($link, "select SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN iIF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$capital_recaudado = $fila1["s"];

$saldo_capital = $fila["valor_credito"] - $capital_recaudado;

$intereses = $saldo_capital * $fila["tasa_interes"] / 100.00;

if (!$fila["sin_seguro"])
	$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
else
	$seguro_vida = 0;

$rs1 = sqlsrv_query($link, "select COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha <= GETDATE()");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_causadas = $fila1["c"];

if ($fila["sin_seguro"])
	$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;

$rs1 = sqlsrv_query($link, "select COUNT(*) as c, SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'");

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$cuotas_mora = $fila1["c"];

if (!$cuotas_mora)
	$total_pagar = $saldo_capital * (1 + $fila["tasa_interes"] / 100.00) + $seguro_vida + $seguro_causado;
else
	$total_pagar = $saldo_capital + ((($saldo_capital * $fila["tasa_interes"] / 100.00) + $seguro_vida) * $cuotas_mora) + $seguro_causado;

if ($cuotas_mora > 2) {
	$gastos_cobranza = $total_pagar * 0.2;

	$total_pagar += $gastos_cobranza;
}

?>
<?php include("top2.php"); ?>
<script language="JavaScript">
	function chequeo_forma() {
		with(document.formato) {
			if ((id_compradorprep.value == "") || (fecha.value == "") || (valor_aplicar.value == "") || (archivo.value == "")) {
				alert("Los campos marcados con asterisco (*) son obligatorios");
				return false;
			}
			if (valor_aplicar.value == "0") {
				alert("El Valor Total no puede ser cero");
				return false;
			}
			//ELIMINAR SECCION PARA APLICAR PREPAGOS
			if (parseInt(valor_aplicar.value.replace(/\,/g, '')) < <?php echo $saldo_capital + $seguro_causado ?>) {
				alert("El valor prepagado no puede ser menor al saldo capital<?php if ($fila["sin_seguro"]) {
																					echo " mas el seguro causado";
																				} ?> ($<?php echo number_format($saldo_capital + $seguro_causado, 0, ".", ",") ?>)");
				return false;
			}

		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
	<tr>
		<td class="titulo">
			<center><b>Ingresar Prepago</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato" method="post" action="aplicarprepago2.php?ext=<?php echo $_REQUEST["ext"] ?>" enctype="multipart/form-data" onSubmit="return chequeo_forma()">
	<table border="0" cellspacing=1 cellpadding=2>
		<tr>
			<td>
				<h2>DATOS PREPAGO</h2>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td>* COMPRADOR</td>
							<td>
								<select name="id_compradorprep" style="width:130px; background-color:#EAF1DD;">
									<option value=""></option>
									<?php

									$queryDB = "select id_entidad, nombre from entidades_desembolso order by nombre";

									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
										echo "<option value=\"" . $fila1["id_entidad"] . "\">" . utf8_decode($fila1["nombre"]) . "</option>\n";
									}

									?>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<td>* F PREPAGO</td>
							<td><input type="text" name="fecha" size="10" style="text-align:center; background-color:#EAF1DD;" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}"></td>
							<td width="20">&nbsp;</td>
							<td>* VR PREPAGADO</td>
							<td><input type="text" name="valor_aplicar" size="10" maxlength="10" onfocus="this.value = this.value.replace(/\,/g, '')" onBlur="if(isnumber(this.value)==false) {this.value='0'; return false} else { if (this.value == '') { this.value = '0'; } separador_miles(this); }" style="text-align:right; background-color:#EAF1DD">
								<input type="hidden" name="valor_liquidacion" value="<?php echo $saldo_capital ?>">
								<input type="hidden" name="prepago_intereses" value="<?php echo round($intereses) ?>">
								<input type="hidden" name="prepago_seguro" value="<?php echo round($seguro_vida) ?>">
								<input type="hidden" name="prepago_cuotasmora" value="<?php echo $cuotas_mora ?>">
								<input type="hidden" name="prepago_segurocausado" value="<?php echo round($seguro_causado) ?>">
								<input type="hidden" name="prepago_gastoscobranza" value="<?php echo round($gastos_cobranza) ?>">
								<input type="hidden" name="prepago_totalpagar" value="<?php echo round($total_pagar) ?>">
							</td>
							<td>* SOPORTE</td>
							<td><input type="file" name="archivo" style="text-align:center; background-color:#EAF1DD;"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
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