<?php
include('../functions.php');
include('../function_blob_storage.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA") || !$_SESSION["FUNC_BOLSAINCORPORACION"]) {
	exit;
}

$link = conectar();

$queryDB = "select si.saldo_bolsa from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$simulacion_rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$simulacion = sqlsrv_fetch_array($simulacion_rs);

if (!sqlsrv_num_rows($simulacion_rs)) {
	exit;
}

?>
<?php include("top.php"); ?>

<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="bolsainc.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Recaudado Bolsa</b><br><br></center>
		</td>
	</tr>
</table>
<?php

if ($_REQUEST["action"]) {
	$queryDB = "SELECT bpa.* from bolsainc_pagos bpa INNER JOIN simulaciones si ON bpa.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where bpa.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND bpa.valor > 0";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	$queryDB .= " order by bpa.consecutivo";

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["chk" . $fila["consecutivo"]] == "1") {
			if ($_REQUEST["action"] == "borrar" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA")) {
				if ($simulacion["saldo_bolsa"] - $fila["valor"] >= 0) {
					sqlsrv_query($link, "update bolsainc_pagos set valor_anulacion = valor, usuario_anulacion = '" . $_SESSION["S_LOGIN"] . "', fecha_anulacion = GETDATE(), valor = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and consecutivo = '" . $fila["consecutivo"] . "'");

					sqlsrv_query($link, "UPDATE simulaciones set saldo_bolsa = saldo_bolsa - " . $fila["valor"] . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");
				} else {
					echo "<script>alert('El recaudo no puede ser borrado (La suma de recaudos aplicados debe ser mayor o igual a lo aplicado)')</script>";
				}
			}
		}
	}
}

$queryDB = "SELECT bpa.* from bolsainc_pagos bpa INNER JOIN simulaciones si ON bpa.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where bpa.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND bpa.valor > 0";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

$queryDB .= " order by bpa.consecutivo";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs)) {
?>
	<form name="formato3" method="post" action="bolsainc_pagos.php">
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
				<th>T Recaudo</th>
				<th>F Recaudo</th>
				<th>Valor</th>
				<th>Usuario</th>
				<th>Fecha</th>
				<th><img src="../images/archivo.png" title="Soporte"></th>
				<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><th><img src="../images/delete.png" title="Borrar Recaudo"></th><?php } ?>
			</tr>
			<?php

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {

			?>
				<tr>
					<td><?php echo $fila["tipo_recaudo"] ?></td>
					<td><?php echo $fila["fecha"] ?></td>
					<td align="right"><?php echo number_format($fila["valor"], 0) ?></td>
					<td><?php echo $fila["usuario_creacion"] ?></td>
					<td><?php echo $fila["fecha_creacion"] ?></td>
					<td align=center><?php if ($fila["nombre_grabado"]) { ?><a href="#" onClick="window.open('<?php echo generateBlobDownloadLinkWithSAS("simulaciones", $_REQUEST["id_simulacion"] . "/varios/" . $fila["nombre_grabado"]) ?>','ADJUNTO<?php echo $fila["consecutivo"] ?>', 'toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0');"><img src="../images/archivo.png" title="Soporte"></a><?php } else {
																																																																																																								echo "&nbsp;";
																																																																																																							} ?></td>
					<?php if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA") && $_SESSION["S_SOLOLECTURA"] != "1") { ?><td align="center"><input type="checkbox" name="chk<?php echo $fila["consecutivo"] ?>" value="1" disabled></td><?php } ?>
				</tr>
			<?php

				$consecutivo = $fila["consecutivo"];
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
<script>
	document.formato3.chk<?php echo $consecutivo ?>.disabled = false;
</script>
<?php include("bottom.php"); ?>