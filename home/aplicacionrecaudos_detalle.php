<?php
include('../functions.php');
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}

$queryDB = "select procesado from recaudosplanos" . $sufijo . " where id_recaudoplano = '" . $_REQUEST["id_recaudoplano"] . "'";

$recaudoplano_rs = sqlsrv_query($link, $queryDB);

$recaudoplano = sqlsrv_fetch_array($recaudoplano_rs);

?>
<?php include("top.php"); ?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">
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
		<td valign="top" width="18"><a href="aplicacionrecaudos.php?ext=<?php echo $_REQUEST["ext"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
		<td class="titulo">
			<center><b>Detalle Aplicaci&oacute;n</b><br><br></center>
		</td>
	</tr>
</table>
<?php

if ($_REQUEST["action"]) {
	sqlsrv_query($link, "BEGIN");
	$queryDB = "select * from recaudosplanos_detalle" . $sufijo . " where id_recaudoplano = '" . $_REQUEST["id_recaudoplano"] . "'";
	$queryDB .= " order by id_recaudoplanodetalle";
	$rs2 = sqlsrv_query($link, $queryDB);
	
	while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
		if ($_REQUEST["chk" . $fila2["id_recaudoplanodetalle"]] == "1") {
			if ($_REQUEST["action"] == "aplicar" && $fila2["id_simulacion"]) {
				if ($_SESSION["FUNC_BOLSAINCORPORACION"] && !$_REQUEST["ext"] && strpos($fila2["observacion"], "bolsa de incorp") !== false) {
					$queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from bolsainc_pagos where id_simulacion = '" . $fila2["id_simulacion"] . "'";

					$rs1 = sqlsrv_query($link, $queryDB);
					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
					$consecutivo = $fila1["max_c"];

					sqlsrv_query($link, "insert into bolsainc_pagos (id_simulacion, consecutivo, fecha, valor, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $fila2["id_simulacion"] . "', '" . $consecutivo . "', '" . $fila2["fecha"] . "', '" . $fila2["valor"] . "', 'NOMINA', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

					sqlsrv_query($link, "update simulaciones set saldo_bolsa = saldo_bolsa + " . $fila2["valor"] . " where id_simulacion = '" . $fila2["id_simulacion"] . "'");
				} else {
					$queryDB = "select CASE WHEN MAX(consecutivo) IS NULL THEN '1' ELSE MAX(consecutivo) + 1 END as max_c from pagos" . $sufijo . " where id_simulacion = '" . $fila2["id_simulacion"] . "'";

					$rs1 = sqlsrv_query($link, $queryDB);

					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

					$consecutivo = $fila1["max_c"];

					sqlsrv_query($link, "insert into pagos" . $sufijo . " (id_simulacion, consecutivo, fecha, valor, tipo_recaudo, usuario_creacion, fecha_creacion) values ('" . $fila2["id_simulacion"] . "', '" . $consecutivo . "', '" . $fila2["fecha"] . "', '" . $fila2["valor"] . "', 'NOMINA', '" . $_SESSION["S_LOGIN"] . "', GETDATE())");

					$valor_por_aplicar = $fila2["valor"];

					$queryDB = "select cu.*, si.plazo, DATEDIFF(day, si.fecha_primera_cuota, EOMONTH('" . $fila2["fecha"] . "')) as diferencia_fecha_primera_cuota from cuotas" . $sufijo . " cu INNER JOIN simulaciones" . $sufijo . " si ON cu.id_simulacion = si.id_simulacion where cu.id_simulacion = '" . $fila2["id_simulacion"] . "' and cu.saldo_cuota > 0 order by cu.cuota";

					$rs = sqlsrv_query($link, $queryDB);

					while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
						if ($valor_por_aplicar) {
							if ($fila["saldo_cuota"] <= $valor_por_aplicar) {
								$valor_aplicar_cuota = $fila["saldo_cuota"];
								$pagada = "1";
							} else {
								$valor_aplicar_cuota = $valor_por_aplicar;
								$pagada = "0";
							}

							sqlsrv_query($link, "insert into pagos_detalle" . $sufijo . " (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $fila2["id_simulacion"] . "', '" . $consecutivo . "', '" . $fila["cuota"] . "', '" . $valor_aplicar_cuota . "', '" . $fila["saldo_cuota"] . "')");

							sqlsrv_query($link, "update cuotas" . $sufijo . " set saldo_cuota = saldo_cuota - " . $valor_aplicar_cuota . ", pagada = '" . $pagada . "' where id_simulacion = '" . $fila2["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");

							$valor_por_aplicar -= $valor_aplicar_cuota;

							//Si se recauda el 100% de la primera cuota, se ajusta fecha primera cuota
							if (!$_REQUEST["ext"] && $fila["cuota"] == "1" && $pagada & $fila["diferencia_fecha_primera_cuota"] > 0) {
								$fecha_tmp = $fila2["fecha"];

								$fecha = new DateTime($fecha_tmp);

								sqlsrv_query($link, "update simulaciones set fecha_primera_cuota = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $fila2["id_simulacion"] . "'");

								sqlsrv_query($link, "insert into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('" . $fila2["id_simulacion"] . "', '" . $fecha->format('Y-m-t') . "', 'system', GETDATE())");

								for ($j = 1; $j <= $fila["plazo"]; $j++) {
									$fecha = new DateTime($fecha->format('Y-m-01'));

									sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $fila2["id_simulacion"] . "' AND cuota = '" . $j . "'");

									$fecha->add(new DateInterval('P1M'));
								}
							}
						} else {
							break;
						}
					}

					$queryDB = "select SUM(saldo_cuota) as s from cuotas" . $sufijo . " where id_simulacion = '" . $fila2["id_simulacion"] . "'";

					$rs1 = sqlsrv_query($link, $queryDB);

					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

					if ($fila1["s"] == 0) {
						sqlsrv_query($link, "update simulaciones" . $sufijo . " set estado = 'CAN', retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '" . $fila2["id_simulacion"] . "'");
					}

					if (!$_REQUEST["ext"]) {
						//Para saber si ya hubo recaudo completo en el mes que se aplica el recaudo
						$queryDB = "SELECT valor_cuota - CASE WHEN dbo.fn_total_recaudado_mes(" . $fila2["id_simulacion"] . ", 0, '" . $fila2["fecha"] . "') IS NULL THEN 0 ELSE dbo.fn_total_recaudado_mes(" . $fila2["id_simulacion"] . ", 0, '" . $fila2["fecha"] . "') END as s from cuotas where id_simulacion = '" . $fila2["id_simulacion"] . "' AND FORMAT(fecha, 'Y-m') = FORMAT('" . $fila2["fecha"] . "', 'Y-m')";

						$rs1 = sqlsrv_query($link, $queryDB);
						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

						if ($fila1["s"] <= 0) {
							sqlsrv_query($link, "delete from cuotas_norecaudadas where id_simulacion = '" . $fila2["id_simulacion"] . "' AND fecha = EOMONTH('" . $fila2["fecha"] . "')");
						}
					}

					if (!$_REQUEST["ext"]) {
						$queryDB = "select vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, dbo.fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $fila2["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
					} else {
						$queryDB = "select vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cso, dbo.fn_total_recaudado(si.id_simulacion, 1) as total_recaudado from ventas_detalle" . $sufijo . " vd INNER JOIN ventas" . $sufijo . " ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $fila2["id_simulacion"] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";
					}

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

						sqlsrv_query($link, "update ventas_detalle" . $sufijo . " set cuota_desde = '" . $cuota_desde . "' where id_ventadetalle = '" . $fila["id_ventadetalle"] . "'");
					}
				}

				sqlsrv_query($link, "update recaudosplanos_detalle" . $sufijo . " set aplicado = '1' where id_recaudoplanodetalle = '" . $fila2["id_recaudoplanodetalle"] . "'");
			}
		}
	}

	sqlsrv_query($link, "update recaudosplanos" . $sufijo . " set procesado = '1' where id_recaudoplano = '" . $_REQUEST["id_recaudoplano"] . "'");

	sqlsrv_query($link, "COMMIT");

	if ($_REQUEST["action"] == "aplicar") {
		echo "<script>alert('Los recaudos marcados fueron aplicados'); window.location.href='aplicacionrecaudos.php?ext=" . $_REQUEST["ext"] . "';</script>";
	}
}

if (!$_REQUEST["ext"]) {

	$queryDB = "select rpd.*, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo from recaudosplanos_detalle rpd LEFT JOIN simulaciones si ON rpd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON rpd.pagaduria = pa.nombre where rpd.id_recaudoplano = '" . $_REQUEST["id_recaudoplano"] . "'";

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND (si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ") OR si.id_unidad_negocio IS NULL)";
	}
} else {
	$queryDB = "select rpd.*, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo from recaudosplanos_detalle" . $sufijo . " rpd LEFT JOIN simulaciones" . $sufijo . " si ON rpd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON rpd.pagaduria = pa.nombre where rpd.id_recaudoplano = '" . $_REQUEST["id_recaudoplano"] . "'";
}
if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND (pa.sector = '" . $_SESSION["S_SECTOR"] . "' OR pa.sector IS NULL)";
}
$queryDB . " order by rpd.id_recaudoplanodetalle";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
if (sqlsrv_num_rows($rs)) {

?>
	<form name="formato3" method="post" action="aplicacionrecaudos_detalle.php?ext=<?php echo $_REQUEST["ext"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_recaudoplano" value="<?php echo $_REQUEST["id_recaudoplano"] ?>">
		<table border="0" cellspacing=1 cellpadding=2 class="tab1" id="tablaAplicacionRecaudoDetalle">
			<tr>
				<th>C&eacute;dula</th>
				<th>Nombre</th>
				<th>No. Libranza</th>
				<th>Tasa</th>
				<th>Cuota</th>
				<th>Vr Cr&eacute;dito</th>
				<th>Pagadur&iacute;a</th>
				<th>Plazo</th>
				<th>Fecha Recaudo</th>
				<th>Vr Recaudo</th>
				<th>Observaci&oacute;n</th>
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
					<td align="center" style="white-space:nowrap;"><?php echo $fila["fecha"] ?></td>
					<td align="right"><?php echo number_format($fila["valor"], 0) ?></td>
					<td><?php echo utf8_decode($fila["observacion"]) ?></td>
					<td align="center"><?php if ($fila["observacion"] == "OK" || strpos($fila["observacion"], "bolsa de incorp") !== false) { ?><input type="checkbox" recaudoplano_detalle="<?php echo $fila["id_recaudoplanodetalle"] ?>" name="chk<?php echo $fila["id_recaudoplanodetalle"] ?>" value="1" <?php if ($fila["aplicado"]) { ?> checked<?php } ?><?php if ($recaudoplano["procesado"]) { ?> disabled<?php } ?>><?php } else {echo "&nbsp;";} ?></td>
				</tr>
			<?php

				$j++;
			}

			?>
			<tr class="tr_bold">
				<td colspan="9">&nbsp;</td>
				<td align="right"><b><?php echo number_format($total_valor, 0) ?></b></td>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<br>
		<?php

		if (!$recaudoplano["procesado"]) {

		?>
			<input  type="button"  value="Aplicar" id="btnAplicar" onclick="procesarPagos(); return false;">
		<?php

		}

		?>
	</form>
<?php

} else {
	echo "<table><tr><td>No se encontraron registros</td></tr></table>";
}

?>

<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
	<script type="text/javascript" src="../plugins/DataTables/datatables.min.js"></script>
	<script type="text/javascript" src="../plugins/fontawesome/js/fontawesome.min.js"></script>    
<script type="text/javascript">
	function procesarPagos(){
		var peticion = 0;//para no leer el titulo
		var data2=[];
		if ($("#tablaAplicacionRecaudoDetalle input[type=checkbox]:checked").length>0){
			Swal.fire({
				title: 'Por favor aguarde unos segundos',
				text: 'Procesando...'
			});

			Swal.showLoading();
			$("#tablaAplicacionRecaudoDetalle input[type=checkbox]:checked").each(function() { // Itera sobre cada fila de la tabla
				//$(this).find("td").each(function() { // Itera sobre cada celda de la fila actual
				var row = $(this).closest("tr")[0];
				//console.log($(this).attr("recaudoplano_detalle"))
				data2.push($(this).attr("recaudoplano_detalle"))
			});

			enviarAjax();
            function enviarAjax(){
				Swal.update({
					title: 'Cargando...',
					text: 'Ejecutado ' + (peticion+1) + ' de ' + data2.length
				});
				Swal.showLoading();
            	if(peticion < data2.length){
					var data = {
						operacion : "Aplicar Recaudos Detalle",
						id_recaudoplanodetalle : data2[peticion]
					}					
					$.ajax({
						url: '../servicios/aplicacion_recaudos/aplicarRecaudosDetalle.php',
						type: 'POST',
						data: JSON.stringify(data),
						dataType : 'json',
						method: 'POST',
						success: function(json) {
							if(json.codigo == 200){
								peticion++;
								enviarAjax();
								$(this).prop("checked", true);
								$(this).prop("disabled", true);
							}else {
								Swal.fire(json.mensaje, '', 'error')
							}
						}
					});
				}else{
					
					Swal.fire({
						title: 'Proceso Ejecutado Satisfactoriamente',
						icon: 'success',
						allowOutsideClick: false,
						showCancelButton: false,
						showConfirmButton: true
					}).then((result) => {
						if (result.isConfirmed) {
							location.reload();
							
						}
						
					})


					var data = {
						operacion : "Cerrar Plano Recaudo",
						id_recaudoplano : <?php echo $_REQUEST["id_recaudoplano"];?>
					}
												
					$.ajax({
						url: '../servicios/aplicacion_recaudos/cerrarPlanoRecaudos.php',
						type: 'POST',
						data: JSON.stringify(data),
						dataType : 'json',
						method: 'POST',
						success: function(json) {
							console.log(json)
							if(json.codigo == 200){
							}else {
								Swal.fire(json.mensaje, '', 'error')
							}
						}
					});
				}
			}
		}else{
			Swal.fire("Debe seleccionar un credito para aplicar recaudo", '', 'error')
		}
	}
</script>
<?php include("bottom.php"); ?>