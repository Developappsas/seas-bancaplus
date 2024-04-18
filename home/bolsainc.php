<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA") || !$_SESSION["FUNC_BOLSAINCORPORACION"]) {
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<script language="JavaScript" src="../date.js"></script>
<table border="0" cellspacing=1 cellpadding=2>
	<tr>
		<td class="titulo">
			<center><b>Bolsa Incorporaci&oacute;n</b><br><br></center>
		</td>
	</tr>
</table>
<form name="formato2" method="post" action="bolsainc.php">
	<table>
		<tr>
			<td>
				<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2>
						<tr>
							<td valign="bottom">C&eacute;dula/Nombre/No. Libranza<br><input type="text" name="descripcion_busqueda" onBlur="ReplaceComilla(this)" size="30" maxlength="50"></td>
							<?php

							if (!$_SESSION["S_SECTOR"]) {

							?>
								<td valign="bottom">Sector<br>
									<select name="sectorb">
										<option value=""></option>
										<option value="PUBLICO">PUBLICO</option>
										<option value="PRIVADO">PRIVADO</option>
									</select>&nbsp;
								</td>
							<?php

							}

							?>
							<td valign="bottom">Pagadur&iacute;a<br>
								<select name="pagaduriab">
									<option value=""></option>
									<?php

									$queryDB = "SELECT nombre as pagaduria from pagadurias where 1 = 1";

									if ($_SESSION["S_SECTOR"]) {
										$queryDB .= " AND sector = '" . $_SESSION["S_SECTOR"] . "'";
									}

									$queryDB .= " order by pagaduria";

									$rs1 = sqlsrv_query($link, $queryDB);

									while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)) {
										echo "<option value=\"" . $fila1["pagaduria"] . "\">" . stripslashes(utf8_decode($fila1["pagaduria"])) . "</option>\n";
									}

									?>
								</select>&nbsp;
							</td>
							<td valign="bottom">Estado<br>
								<select name="estadob">
									<option value=""></option>
									<option value="EST">PARCIAL</option>
									<option value="DES">VIGENTE</option>
									<option value="CAN">CANCELADO</option>
								</select>&nbsp;
							</td>
							<td valign="bottom">&nbsp;<br><input type="hidden" name="buscar" value="1"><input type="submit" value="Buscar"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php

if (!$_REQUEST["page"]) {
	$_REQUEST["page"] = 0;
}

$x_en_x = 100;

$offset = $_REQUEST["page"] * $x_en_x;

if ($_REQUEST["buscar"]) {
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_primera_cuota, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' WHEN si.estado = 'EST' THEN 'PARCIAL' ELSE 'VIGENTE' END as estado, si.retanqueo_total, si.descuento1, DATEDIFF(day, EOMONTH(DATEadd(MONTH,  -1, si.fecha_primera_cuota)), si.fecha_desembolso) as dias_desde_desembolso_hasta_un_mes_antes_primera_cuota, si.saldo_bolsa from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST'AND si.decision = '" . $label_viable . "'AND ((si.id_subestado IN (" . $subestado_compras_desembolso . ") AND si.estado_tesoreria = 'PAR' )OR (si.id_subestado IN ('" . $subestado_desembolso . "', '" . $subestado_desembolso_cliente . "', '" . $subestado_desembolso_pdte_bloqueo . "'))))) AND si.id_simulacion IN (select id_simulacion from bolsainc_pagos)";

	$queryDB_count = "SELECT COUNT(*) as c from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST'AND si.decision = '" . $label_viable . "'AND ((si.id_subestado IN (" . $subestado_compras_desembolso . ") AND si.estado_tesoreria = 'PAR' )OR (si.id_subestado IN ('" . $subestado_desembolso . "', '" . $subestado_desembolso_cliente . "', '" . $subestado_desembolso_pdte_bloqueo . "'))))) AND si.id_simulacion IN (select id_simulacion from bolsainc_pagos)";

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";

		$queryDB_count .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";

		$queryDB_count .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";

		$queryDB_count .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}

	if ($_REQUEST["descripcion_busqueda"]) {
		$descripcion_busqueda = $_REQUEST["descripcion_busqueda"];

		$queryDB .= " AND (si.cedula = '" . $descripcion_busqueda . "' OR UPPER(si.nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%' OR si.nro_libranza like '%" . $descripcion_busqueda . "%')";

		$queryDB_count .= " AND (si.cedula = '" . $descripcion_busqueda . "' OR UPPER(si.nombre) like '%" . utf8_encode(strtoupper($descripcion_busqueda)) . "%' OR si.nro_libranza like '%" . $descripcion_busqueda . "%')";
	}

	if ($_REQUEST["sectorb"]) {
		$sectorb = $_REQUEST["sectorb"];

		$queryDB .= " AND pa.sector = '" . $sectorb . "'";

		$queryDB_count .= " AND pa.sector = '" . $sectorb . "'";
	}

	if ($_REQUEST["pagaduriab"]) {
		$pagaduriab = $_REQUEST["pagaduriab"];

		$queryDB .= " AND si.pagaduria = '" . $pagaduriab . "'";

		$queryDB_count .= " AND si.pagaduria = '" . $pagaduriab . "'";
	}

	if ($_REQUEST["estadob"]) {
		$estadob = $_REQUEST["estadob"];

		$queryDB .= " AND si.estado = '" . $estadob . "'";

		$queryDB_count .= " AND si.estado = '" . $estadob . "'";
	}

	$queryDB .= " Group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_primera_cuota, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, si.retanqueo_total, si.descuento1, si.saldo_bolsa order by si.fecha_desembolso DESC, si.id_simulacion DESC OFFSET ".$offset." ROWS FETCH NEXT 100 ROWS Only";

	$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	$rs_count = sqlsrv_query($link, $queryDB_count, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
    

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
	
	$fila_count = sqlsrv_fetch_array($rs_count);


	$cuantos = $fila_count["c"];

}



if ($cuantos){
	if ($cuantos > $x_en_x) {
		echo "<table><tr><td><p align=\"center\"><b>P&aacute;ginas";

		$i = 1;
		$final = 0;

		while ($final < $cuantos) {
			$link_page = $i - 1;
			$final = $i * $x_en_x;
			$inicio = $final - ($x_en_x - 1);

			if ($final > $cuantos) {
				$final = $cuantos;
			}

			if ($link_page != $_REQUEST["page"]) {
				echo " <a href=\"bolsainc.php?descripcion_busqueda=" . $descripcion_busqueda . "&sectorb=" . $sectorb . "&pagaduriab=" . $pagaduriab . "&estadob=" . $estadob . "&buscar=" . $_REQUEST["buscar"] . "&page=$link_page\">$i</a>";
			} else {
				echo " " . $i;
			}

			$i++;
		}

		if ($_REQUEST["page"] != $link_page) {
			$siguiente_page = $_REQUEST["page"] + 1;

			echo " <a href=\"bolsainc.php?descripcion_busqueda=" . $descripcion_busqueda . "&sectorb=" . $sectorb . "&pagaduriab=" . $pagaduriab . "&estadob=" . $estadob . "&buscar=" . $_REQUEST["buscar"] . "&page=" . $siguiente_page . "\">Siguiente</a></p></td></tr>";
		}

		echo "</table><br>";
	}

?>
	<form name="formato3" method="post" action="bolsainc.php">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $descripcion_busqueda ?>">
		<input type="hidden" name="sectorb" value="<?php echo $sectorb ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $pagaduriab ?>">
		<input type="hidden" name="estadob" value="<?php echo $estadob ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1">
			<tr>
				<th>C&eacute;dula</th>
				<th>F. Desemb</th>
				<th>F. Primera<br>Cuota</th>
				<th>Nombre</th>
				<th>No. Libranza</th>
				<th>Tasa</th>
				<th>Cuota</th>
				<th>Vr Cr&eacute;dito</th>
				<th>Pagadur&iacute;a</th>
				<th>Plazo</th>
				<th>Estado</th>
				<th>Vr/D&iacute;as Int.<br>Anticipado Pdte</th>
				<th>Recaudado<br>Bolsa</th>
				<th>Aplicado</th>
				<th>Saldo<br>Bolsa</th>
			</tr>
			<?php

			$j = 1;

			while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
				$tr_class = "";

				if (($j % 2) == 0) {
					$tr_class = " style='background-color:#F1F1F1;'";
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

				$interes_diario = ($fila["valor_credito"] * $fila["tasa_interes"] / 100) / 30;

				if ($fila["opcion_credito"] == "CLI")
					$fila["retanqueo_total"] = 0;

				$intereses_anticipados = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00;

				$rs1 = sqlsrv_query($link, "select SUM(valor) as s from bolsainc_aplicaciones where id_simulacion = '" . $fila["id_simulacion"] . "' AND tipo_aplicacion = 'INTERES_ANTICIPADO'");

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$intereses_anticipados += $fila1["s"];

				$dias_cubiertos_por_interes_anticipado = round($intereses_anticipados) / $interes_diario;

				$dias_pdtes_por_cubrir_de_interes_anticipado = $fila["dias_desde_desembolso_hasta_un_mes_antes_primera_cuota"] - $dias_cubiertos_por_interes_anticipado;

				$interes_anticipado_pdte_por_cubrir = $dias_pdtes_por_cubrir_de_interes_anticipado * $interes_diario;

				$rs1 = sqlsrv_query($link, "select SUM(valor) as s from bolsainc_pagos where id_simulacion = '" . $fila["id_simulacion"] . "'");

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$recaudado_bolsa = $fila1["s"];

				$rs1 = sqlsrv_query($link, "select SUM(valor) as s from bolsainc_aplicaciones where id_simulacion = '" . $fila["id_simulacion"] . "'");

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$aplicado = $fila1["s"];

			?>
				<tr <?php echo $tr_class ?>>
					<td><?php echo $fila["cedula"] ?></td>
					<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_desembolso"] ?></td>
					<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha_primera_cuota"] ?></td>
					<td><?php echo utf8_decode($fila["nombre"]) ?></td>
					<td align="center"><?php echo $fila["nro_libranza"] ?></td>
					<td align="right"><?php echo $fila["tasa_interes"] ?></td>
					<td align="right"><?php echo number_format($opcion_cuota, 0) ?></td>
					<td align="right"><?php echo number_format($fila["valor_credito"], 0) ?></td>
					<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
					<td align="right"><?php echo $fila["plazo"] ?></td>
					<td align="center"><?php echo $fila["estado"] ?></td>
					<td align="right"><?php echo number_format($interes_anticipado_pdte_por_cubrir, 0) ?><br>(<?php echo number_format($dias_pdtes_por_cubrir_de_interes_anticipado, 2) ?> d&iacute;as)</td>
					<td align="right"><a href="bolsainc_pagos.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo number_format($recaudado_bolsa, 0) ?></a></td>
					<td align="right"><a href="bolsainc_aplicaciones.php?id_simulacion=<?php echo $fila["id_simulacion"] ?>&descripcion_busqueda=<?php echo $descripcion_busqueda ?>&sectorb=<?php echo $sectorb ?>&pagaduriab=<?php echo $pagaduriab ?>&estadob=<?php echo $estadob ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><?php echo number_format($aplicado, 0) ?></a></td>
					<td align="right"><?php echo number_format($fila["saldo_bolsa"], 0) ?></td>
				</tr>
			<?php

				$j++;
			}

			?>
		</table>
		<br>
	</form>
<?php

} else {
	if ($_REQUEST["buscar"]) {
		$mensaje = "No se encontraron registros";
	}

	echo "<table><tr><td>" . $mensaje . "</td></tr></table>";
}

?>
<?php include("bottom.php"); ?>