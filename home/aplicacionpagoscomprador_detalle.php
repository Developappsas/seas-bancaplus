<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select procesado from ventas_pagosplanos" . $sufijo . " where id_pagoplano = '" . $_REQUEST["id_pagoplano"] . "'";

$pagoplano_rs = sqlsrv_query($link, $queryDB);

$pagoplano = sqlsrv_fetch_array($pagoplano_rs);

?>
<?php include("top.php"); ?>
<script language="JavaScript">
	function Chequear_Todos() {
		with(document.formato3) {
			for (i = 2; i <= elements.length - 2; i++) {
				elements[i].checked = true;
			}
		}
	}
	//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
	<tr>
		<td valign="top" width="18"><a href="aplicacionpagoscomprador.php?ext=<?php echo $_REQUEST["ext"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Detalle Aplicacion</b><br><br></center>
		</td>
	</tr>
</table>
<?php

if ($_REQUEST["action"]) {
	sqlsrv_query($link, "BEGIN");

	$queryDB = "select ppd.*, pp.fecha from ventas_pagosplanos_detalle" . $sufijo . " ppd INNER JOIN ventas_pagosplanos" . $sufijo . " pp ON ppd.id_pagoplano = pp.id_pagoplano where ppd.id_pagoplano = '" . $_REQUEST["id_pagoplano"] . "'";

	$queryDB .= " order by id_pagoplanodetalle";

	$rs2 = sqlsrv_query($link, $queryDB);

	while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["chk" . $fila2["id_pagoplanodetalle"]] == "1") {
			if ($_REQUEST["action"] == "aplicar" && $fila2["observacion"] == "OK") {
				$queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from ventas_pagos" . $sufijo . " where id_ventadetalle = '" . $fila2["id_ventadetalle"] . "'";

				$rs1 = sqlsrv_query($link, $queryDB);

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$consecutivo = $fila1["max_c"];

				sqlsrv_query($link, "insert into ventas_pagos" . $sufijo . " (id_ventadetalle, consecutivo, fecha, valor, manual, tipo_pago, usuario_creacion, fecha_creacion) values ('" . $fila2["id_ventadetalle"] . "', '" . $consecutivo . "', '" . $fila2["fecha"] . "', '" . $fila2["valor"] . "', '0', NULL, '" . $_SESSION["S_LOGIN"] . "', NOW())");

				$queryDB = "select saldo_cuota from ventas_cuotas" . $sufijo . " where id_ventadetalle = '" . $fila2["id_ventadetalle"] . "' and cuota = '" . $fila2["cuota"] . "'";

				$rs1 = sqlsrv_query($link, $queryDB);

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$saldo_cuota = $fila1["saldo_cuota"];

				sqlsrv_query($link, "insert into ventas_pagosdetalle" . $sufijo . " (id_ventadetalle, consecutivo, cuota, valor, valor_antes_pago) values ('" . $fila2["id_ventadetalle"] . "', '" . $consecutivo . "', '" . $fila2["cuota"] . "', '" . $fila2["valor"] . "', '" . $saldo_cuota . "')");

				if ($fila2["valor"] == $saldo_cuota)
					$pagada = "1";
				else
					$pagada = "0";

				sqlsrv_query($link, "update ventas_cuotas" . $sufijo . " set saldo_cuota = saldo_cuota - " . $fila2["valor"] . ", pagada = '" . $pagada . "' where id_ventadetalle = '" . $fila2["id_ventadetalle"] . "' and cuota = '" . $fila2["cuota"] . "'");

				sqlsrv_query($link, "update ventas_pagosplanos_detalle" . $sufijo . " set aplicado = '1' where id_pagoplanodetalle = '" . $fila2["id_pagoplanodetalle"] . "'");
			}
		}
	}

	sqlsrv_query($link, "update ventas_pagosplanos" . $sufijo . " set procesado = '1' where id_pagoplano = '" . $_REQUEST["id_pagoplano"] . "'");

	sqlsrv_query($link, "COMMIT");

	if ($_REQUEST["action"] == "aplicar") {
		echo "<script>alert('Los pagos marcados fueron aplicados'); window.location.href='aplicacionpagoscomprador.php?ext=" . $_REQUEST["ext"] . "';</script>";
	}
}

if (!$_REQUEST["ext"]) {
	$queryDB = "select ppd.*, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, vc.cuota, vc.fecha as fecha_vencimiento, vc.saldo_cuota from ventas_pagosplanos_detalle ppd LEFT JOIN ventas_cuotas vc ON ppd.id_ventadetalle = vc.id_ventadetalle AND ppd.cuota = vc.cuota LEFT JOIN ventas_detalle vd ON vc.id_ventadetalle = vd.id_ventadetalle LEFT JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where ppd.id_pagoplano = '" . $_REQUEST["id_pagoplano"] . "' order by ppd.id_pagoplanodetalle";
} else {
	$queryDB = "select ppd.*, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, vc.cuota, vc.fecha as fecha_vencimiento, vc.saldo_cuota from ventas_pagosplanos_detalle" . $sufijo . " ppd LEFT JOIN ventas_cuotas" . $sufijo . " vc ON ppd.id_ventadetalle = vc.id_ventadetalle AND ppd.cuota = vc.cuota LEFT JOIN ventas_detalle" . $sufijo . " vd ON vc.id_ventadetalle = vd.id_ventadetalle LEFT JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion where ppd.id_pagoplano = '" . $_REQUEST["id_pagoplano"] . "' order by ppd.id_pagoplanodetalle";
}

$rs = sqlsrv_query($link, $queryDB);

if (sqlsrv_num_rows($rs)) {

?>
	<form name="formato3" method="post" action="aplicacionpagoscomprador_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_pagoplano" value="<?php echo $_REQUEST["id_pagoplano"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>Cedula</th>
				<th>Nombre</th>
				<th>No.<br>Libranza</th>
				<th>Tasa</th>
				<th>Cuota</th>
				<th>Vr Credito</th>
				<th>Pagaduria</th>
				<th>Plazo</th>
				<th>No.<br>Cuota</th>
				<th>F.<br>Vencto</th>
				<th>Vr<br>Vencto</th>
				<th>Vr Pago</th>
				<th>Observacion</th>
				<th>Aplicar<br><input type="checkbox" name="chkall" onClick="Chequear_Todos();"></th>
			</tr>
			<?php

			$j = 1;

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
				}

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

				if ($fila["observacion"] == "OK")
					$total_valor += round($fila["valor"]);

			?>
				<tr <?php echo $tr_class ?>>
					<td><?php echo $fila["cedula"] ?></td>
					<td><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td align="center"><?php echo $fila["nro_libranza"] ?></td>
					<td align="right"><?php echo $fila["tasa_interes"] ?></td>
					<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
					<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<td align="right"><?php echo $fila["plazo"] ?></td>
					<td align="center"><?php echo $fila["cuota"] ?></td>
					<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_vencimiento"] ?></td>
					<td align="right"><?php echo number_format($fila["saldo_cuota"], 0) ?></td>
					<td align="right"><?php if ($fila["observacion"] != "OK") { ?><strike><?php } ?><?php echo number_format($fila["valor"], 0) ?></strike></td>
					<td><?php echo utf8_decode($fila["observacion"]) ?></td>
					<td align="center"><?php if ($fila["observacion"] == "OK") { ?><input type="checkbox" name="chk<?php echo $fila["id_pagoplanodetalle"] ?>" value="1" <?php if ($fila["aplicado"]) { ?> checked<?php } ?><?php if ($pagoplano["procesado"]) { ?> disabled<?php } ?>><?php } else {
																																																																						echo "&nbsp;";
																																																																					} ?></td>
				</tr>
			<?php

				$j++;
			}

			?>
			<tr class="tr_bold">
				<td colspan="11">&nbsp;</td>
				<td align="right"><b><?php echo number_format($total_valor, 0) ?></b></td>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<br>
		<?php

		if (!$pagoplano["procesado"]) {

		?>
			<p align="center"><input type="submit" value="Aplicar" onClick="document.formato3.action.value='aplicar'"></p>
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