<?php 

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

if (isset($_REQUEST["exportar"]) && !empty($_REQUEST["exportar"])){
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=Plan Pagos.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
}

include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA")) {
	exit;
}

if ($_REQUEST["id_simulacion"]) {
	$nombre_id = "id_simulacion";
	$id = $_REQUEST["id_simulacion"];
	$tabla_cuotas = "cuotas";
} else if ($_REQUEST["id_ventadetalle"]) {
	$nombre_id = "id_ventadetalle";
	$id = $_REQUEST["id_ventadetalle"];
	$tabla_cuotas = "ventas_cuotas";
}

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}

$queryDB = "select * from cuotas".$sufijo." where ".$nombre_id." = '".$id."' order by cuota";

$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (sqlsrv_num_rows($rs1) > 0 ){
	$plan_pagos_de_cuotas = 1;
}

if ($_REQUEST["id_simulacion"]) {
	$queryDB = "select si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}

	if ($_SESSION["S_TIPO"] == "PROSPECCION"){	
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	}
	
	//echo $queryDB;
	$rs = sqlsrv_query($link, $queryDB);
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	$plazo = $fila["plazo"];
	$tasa_interes = $fila["tasa_interes"];
	$saldo = $fila["valor_credito"];
	
	if($fila["seguro_parcial"] && $fila["sin_seguro"]){
		$seguro_calculado = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		$seguro_total = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro_base"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		$seguro_a_causar = $seguro_total - $seguro_calculado;
	}else{
		$seguro_a_causar = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	}
	
	if (!$fila["sin_seguro"]){
		$seguro = $seguro_a_causar;
	} else{
		if($fila["seguro_parcial"]){
			$seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		}else{
			$seguro = 0;
		}
	}
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
		break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
		break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
		break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
		break;
	}
	
	$valor_cuota = $opcion_cuota - round($seguro);
	
	$pagaduria = $fila["pagaduria"];
}
else if ($_REQUEST["id_ventadetalle"])
{
	$queryDB = "select si.id_origen, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.plazo, si.valor_credito, SUM(cu.capital) as saldo_capital from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where vd.id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."'";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}

	if ($_SESSION["S_TIPO"] == "PROSPECCION"){
		$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	}
	
	//echo $queryDB;

	$rs = sqlsrv_query($link, $queryDB);
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$plazo = $fila["cuotas_vendidas"];
	
	$saldo = $fila["saldo_capital"];
	
	if ($fila["cuotas_vendidas"] == $fila["plazo"])
		$saldo = $fila["valor_credito"];
}

/*if($fila["id_origen"] == 3){

	$rs2 = sqlsrv_query($link, "select COUNT(*) as c from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pagada = '1'");
	$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
	$cuotas_pagadas = $fila2["c"];
	$rs2 = sqlsrv_query($link, "select SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN IF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas" . $sufijo . " where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");
	$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
	$capital_recaudado = $fila2["s"];
	$saldo = $saldo - $capital_recaudado;
	$plazo = $plazo - $cuotas_pagadas;
}*/

if (!$_REQUEST["exportar"]){

	?>
	<?php include("top.php"); ?>
	<link href="../plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
	<link href="../plugins/fontawesome/css/fontawesome.min.css" rel="stylesheet">
	<link href="../plugins/fontawesome/css/solid.min.css" rel="stylesheet">

	<table border="0" cellspacing=1 cellpadding=2 width="95%">
		<tr>
			<td valign="top" width="18">
				<a href="<?php echo $_REQUEST["back"] ?>.php?ext=<?php echo $_REQUEST["ext"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif">
				</a>
			</td>

			<td class="titulo">
				<center>
					<b>Plan de Pagos</b>
				</center>
				<br>
				<center style="display: flex; justify-content: center;">
					<ul class="toolbar-ap">
						<li><a <?php if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?> onclick="reajustarPlanPago(<?=$_REQUEST["id_simulacion"]?>); return false;" <?php } ?> title="Reajustar Plan Pago"><i class="fa-solid fa-money-check-dollar"></i></a></li>

						<?php if (!$_REQUEST["id_ventadetalle"]) { ?>
							<li>
								<?php if (!$fila["sin_seguro"]) { ?>
									<a title="Imprimir Plan pago" onClick="window.open('planpagos_imprimir.php?ext=<?php echo $_REQUEST["ext"] ?>&<?php echo $nombre_id ?>=<?php echo $id ?>', 'PLANPAGOS','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=600,top=0,left=0')"><i class="fa-solid fa-print"></i></a>
								<?php } else { ?>
									<a title="Imprimir Plan pago" onClick="window.open('planpagosform.php?ext=<?php echo $_REQUEST["ext"] ?>&<?php echo $nombre_id ?>=<?php echo $id ?>', 'PLANPAGOSFORM','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=420,top=0,left=0');"><i class="fa-solid fa-print"></i></a><?php 
								} ?>
							</li>
						<?php  } ?>

						<li>
							<?php
							if ($_REQUEST["id_ventadetalle"] || !$fila["sin_seguro"]) { ?>
								<a class="fin" title="Exportar a Excel" href="planpagos.php?ext=<?php echo $_REQUEST["ext"] ?>&<?php echo $nombre_id ?>=<?php echo $id ?>&exportar=1"><i class="fa-solid fa-file-excel"></i></a><?php 
							} else { ?>
								<a class="fin" title="Exportar Excel" href="#" onClick="window.open('planpagosform.php?ext=<?php echo $_REQUEST["ext"] ?>&<?php echo $nombre_id ?>=<?php echo $id ?>&exportar=1', 'PLANPAGOSFORMEXCEL','toolbars=yes,scrollbars=yes,resizable=yes,width=800,height=420,top=0,left=0');"><i class="fa-solid fa-file-excel"></i></a><?php 
							} ?>
						</li>
					</ul>
				</center>
				<br>
			</td>

			<?php if (!$_REQUEST["id_ventadetalle"]) { ?> <td valign="top" width="18">&nbsp;</td> <td width="10">&nbsp;</td><?php } ?>
			<td valign="top" width="18">&nbsp;</td>
		</tr>
	</table>

	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>

	<?php 
	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR") { ?>
		<script type="text/javascript">

			function reajustarPlanPago(id, id_usuario){
				Swal.fire({
					title: '¿Está seguro que desea Reajustar El Plan De Pago?',
					showConfirmButton: false,
					showDenyButton: true,
					showCancelButton: true,
					denyButtonText: `Continuar`,
				}).then((result) => {
					if (result.isDenied) {

						Swal.fire({
							title: 'Aplicando Plan de Pago...',
							allowOutsideClick: false,
							didOpen: () => { Swal.showLoading() }
						})

						$.ajax({
							url: '../servicios/cartera/generarPlanDePagoConRecaudos.php',
							type: 'POST',
							data: { id_simulacion : id, id_usuario: id_usuario },
							dataType : 'json',
							success: function(json) {

								Swal.close();

								if(json.code != ''){

									if(json.code == 200){
										if(json.data[0].code != ''){
											if(json.data[0].code == 200){
												Swal.fire('Plan De Pago Reajustado Exitosamente', '', 'success');
												setTimeout(function(){
													window.location.reload();
												}, 2000);
											}else{
												Swal.fire(json.data[0].mensaje, '', 'error');
											}
										}else {
											Swal.fire('No se Pudo Reajustar Plan de Pagos', '', 'error')
										}
									}else{
										Swal.fire(json.mensaje, '', 'error');
									}
								}else {
									Swal.fire('No se Pudo Reajustar Plan de Pagos', '', 'error')
								}
							}
						});
					}
				})
			}
		</script>
		<?php
	}
}

if (!$_REQUEST["exportar"]){

	?>
	<form name="formato3" method="post" action="planpagos.php?ext=<?php echo $_REQUEST["ext"] ?>">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="descripcion_busqueda2" value="<?php echo $_REQUEST["descripcion_busqueda2"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<?php

	}

	?>
	<table border="0" cellspacing=1 cellpadding=2 class="tab1">
		<tr>
			<th>No. Cuota</th>
			<th width="90">Fecha</th>
			<th width="90">Capital</th>
			<th width="90">Inter&eacute;s</th>
			<th width="90">Seguro de Vida</th>
			<?php if ($pagaduria == "COLPENSIONESXXX") { ?><th width="90">Soporte Colpensiones</th><?php } ?>
			<th width="90">Total Cuota</th>
			<?php if ($_REQUEST["id_simulacion"] && $fila["sin_seguro"] && (!$_REQUEST["exportar"] || $_REQUEST["dirigido_a"] == "CLIENTE")) { ?><th width="90">Seguro Causado</th><?php } ?>
			<th width="90">Saldo de Capital</th>
		</tr>
		<tr>
			<td align="center">&nbsp;</td>
			<td align="center">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right">&nbsp;</td><?php } ?>
			<td align="right">&nbsp;</td>
			<?php if ($_REQUEST["id_simulacion"] && $fila["sin_seguro"] && (!$_REQUEST["exportar"] || $_REQUEST["dirigido_a"] == "CLIENTE")) { ?><td align="right">&nbsp;</td><?php } ?>
			<td align="right"><?php echo number_format($saldo, 0) ?></td>
		</tr>
		<?php

		if ($_REQUEST["id_simulacion"]){
			$rs_fecha_primera_cuota = sqlsrv_query($link, "SELECT fecha_primera_cuota from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

			$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota);

			if (!$fila_fecha_primera_cuota["fecha_primera_cuota"]){
				$rs_fecha_primera_cuota2 = sqlsrv_query($link, "SELECT ISNULL(EOMONTH(DATEADD(MONTH, 2, MIN(fecha_giro))), EOMONTH(DATEADD(MONTH,  2, GETDATE()))) as fecha_primera_cuota from  giros where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

				$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota2);
			}

			$fecha_primera_cuota_tmp = date("Y", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-".date("m", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-01";

			$fecha_primera_cuota = new DateTime($fecha_primera_cuota_tmp);
		}

		$soporte_colpensiones = 500 * $saldo / 1000000;

		$j = 1;

		$queryDB = "select * from cuotas".$sufijo." where ".$nombre_id." = '".$id."' order by cuota";
		$rs1 = sqlsrv_query($link, $queryDB);

		for ($j = 1; $j <= $plazo; $j++) {
			if ($plan_pagos_de_cuotas){
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			} 

			if (($j % 2) != 0){
				$tr_class = " style='background-color:#F1F1F1;'";
			}
			else{
				$tr_class = " style='background-color:#FFFFFF;'";
			}

			if ($plan_pagos_de_cuotas){
				$fecha = $fila1["fecha"];
				$valor_cuota = $fila1["valor_cuota"];
				$seguro = $fila1["seguro"];
				$interes = $fila1["interes"];
				$capital = $fila1["capital"];
				if($fila["seguro_parcial"] && $fila["sin_seguro"]){
					$seguro_causado = $fila1["cuota"] * $fila1["seguro_pendiente"];
				}else{
					$seguro_causado = $fila1["cuota"] * round($seguro_a_causar);
				}
				$abono_capital = $fila1["abono_capital"];
			}
			else{
				$fecha = $fecha_primera_cuota->format('Y-m-t');
				$interes = $saldo * $tasa_interes / 100.00;
				$capital = $valor_cuota - $interes;
				$seguro_causado = $j * round($seguro_a_causar);
				$abono_capital = 0;
			}

			$total_cuota = round($capital) + round($interes) + round($seguro);

			if (!$total_cuota)
				$seguro_causado = 0;

			$saldo -= $capital;

			if ($j == $plazo){
				if (!$plan_pagos_de_cuotas){
					$valor_cuota += $saldo;
					$capital = $valor_cuota - $interes;
				}

				$saldo = 0;
			}
			?>
			<tr <?php echo $tr_class ?>>
				<td align="center"><?php echo $j ?></td>
				<td align="center"><?php echo $fecha ?></td>
				<td align="right"><?php echo number_format($capital, 0) ?></td>
				<td align="right"><?php echo number_format($interes, 0) ?></td>
				<td align="right"><?php if ($pagaduria != "COLPENSIONESXXX") { echo number_format($seguro, 0); } else { echo number_format($seguro - $soporte_colpensiones, 0); }?></td>
				<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right"><?php echo number_format($soporte_colpensiones, 0) ?></td><?php } ?>
				<td align="right"><?php echo number_format($total_cuota, 0) ?></td>
				<?php if ($_REQUEST["id_simulacion"] && $fila["sin_seguro"] && (!$_REQUEST["exportar"] || $_REQUEST["dirigido_a"] == "CLIENTE")) { ?><td align="right"><?php echo number_format($seguro_causado, 0) ?></td><?php } ?>
				<td align="right"><?php echo number_format($saldo, 0) ?></td>
			</tr>
			<?php

			if ($abono_capital){
				$saldo -= $abono_capital;

				?>
				<tr style='background-color:#FFF5B5;'>
					<td colspan="2">ABONO A CAPITAL</td>
					<td align="right"><?php echo number_format($abono_capital, 0) ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right">&nbsp;</td><?php } ?>
					<td align="right">&nbsp;</td>
					<?php if ($_REQUEST["id_simulacion"] && $fila["sin_seguro"] && (!$_REQUEST["exportar"] || $_REQUEST["dirigido_a"] == "CLIENTE")) { ?><td align="right">&nbsp;</td><?php } ?>
					<td align="right"><?php echo number_format($saldo, 0) ?></td>
				</tr>
				<?php

			}

			if ($_REQUEST["id_simulacion"])
				$fecha_primera_cuota->add(new DateInterval('P1M'));
		}

		?>
	</table>
	<?php

	if (!$_REQUEST["exportar"])
	{

		?>
		<br>
	</form>
	<?php include("bottom.php"); ?>
	<?php

}

?>