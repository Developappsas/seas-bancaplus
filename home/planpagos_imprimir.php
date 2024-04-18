<?php include ('../functions.php'); ?>
<?php

$link = conectar_utf();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA"))
{
	exit;
}

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<style type="text/css">
	table {
		*border-collapse: collapse; /* IE7 and lower */
		border-spacing: 0; 
	}
	th:first-child {
		border-radius: 6px 0 0 0;
	}

	th:last-child {
		border-radius: 0 6px 0 0;
	}

	th:only-child{
		border-radius: 6px 6px 0 0;
	}
	tr:first-child {
		border-radius: 6px 0 0 0;
	}

	tr:last-child {
		border-radius: 0 6px 0 0;
	}

	tr:only-child{
		border-radius: 6px 6px 0 0;
	}
	td:first-child {
		border-radius: 6px 0 0 0;
	}

	td:last-child {
		border-radius: 0 6px 0 0;
	}

	td:only-child{
		border-radius: 6px 6px 0 0;
	}
</style>
<link href="../style_impresion.css" rel="stylesheet" type="text/css">
<input type="hidden" name="action" value="">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">

<?php

$queryDB = "select si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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

if ($_SESSION["S_TIPO"] == "PROSPECCION")
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

$rs1 = sqlsrv_query($link, $queryDB);

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

switch($fila1["opcion_credito"]) {
	case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"]; break;
	case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"]; break;
	case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"]; break;
	case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"]; break;
}

$cuota = $opcion_cuota;

$pagaduria = $fila1["pagaduria"];

?>
<table border="0" cellspacing=3 cellpadding=0 width="850px" align="center">
	<tr>
		<td>
			<img align src="../images/logo.png" height="80">
		<!--
			<img align = "right" src="../images/estructurar.png" width="200" height="80">-->
		</td>
	</tr>
	<tr><td colspan="4" align="center"><h4>PLAN DE PAGOS 2</h4></b></td></tr>
	<tr>
		<td>
			<table border="0" cellspacing=1 cellpadding=2 align="center">
				<tr style="background:#E0ECFF;">
					<td  class="admintable" width="20%"><label class="admintable"><b>Nombre:</label></td>
						<td class="admintable" width="30%"><?php echo strtoupper(($fila1["nombre"])) ?></td>
						<td class="admintable" width="20">&nbsp;</td>
						<td class="admintable" width="20%"><label class="admintable"><b>Valor Credito:</label> 
							<td class="admintable">$ <?php echo number_format($fila1["valor_credito"],0)?></td>
						</tr>
						<tr >
							<td  class="admintable"><label class="admintable"><b>Cedula:</label></td>
								<td class="admintable"><?php echo number_format($fila1["cedula"], 0, "", ".") ?></td>
								<td class="admintable" width="20">&nbsp;</td>
								<td class="admintable"><label class="admintable"><b>Tasa:</label> 
									<td class="admintable"><?php echo $fila1["tasa_interes"] ?></td>
								</tr>
								<tr style="background:#E0ECFF;">
									<td  class="admintable"><label class="admintable"><b>Credito No:</label></td>
										<td class="admintable"><?php echo strtoupper($fila1["nro_libranza"]) ?></td>
										<td class="admintable" width="20">&nbsp;</td>
										<td class="admintable"><label class="admintable"><b>Plazo:</label> 
											<td class="admintable"><?php echo $fila1["plazo"] ?></td>
										</tr>
										<tr >
											<td  class="admintable"><label class="admintable"><b>Fecha Desembolso:</label></td>
												<td class="admintable"><?php echo $fila1["fecha_desembolso"] ?></td>
												<td class="admintable" width="20">&nbsp;</td>
												<td class="admintable"><label class="admintable"><b>Valor Cuota:</label> 
													<td class="admintable">$ <?php echo number_format($cuota,0) ?></td>
												</tr>
											</table> 
										</td>
									</tr>
									<tr>
										<td>
											<br>
											<br>
										</td>
									</tr>
									<tr>
										<td>
											<table border="0" cellspacing=1 cellpadding=2 align="center">
												<tr class="admintable">
													<th style='background-color:#E0ECFF;'><h5>No. Cuota</h5></th>
													<th style='background-color:#E0ECFF;' width="90"><h5>Fecha</h5></th>
													<th style='background-color:#E0ECFF;' width="90"><h5>Capital</h5></th>
													<th style='background-color:#E0ECFF;' width="90"><h5>Inter&eacute;s</h5></th>
													<th style='background-color:#E0ECFF;' width="90"><h5>Seguro de Vida</h5></th>
													<?php if ($pagaduria == "COLPENSIONESXXX") { ?><th style='background-color:#E0ECFF;' width="90"><h5>Soporte Colpensiones</h5></th><?php } ?>
													<th style='background-color:#E0ECFF;' width="90"><h5>Total Cuota</h5></th>
													<?php if ($fila1["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><th style='background-color:#E0ECFF;' width="90"><h5>Seguro Causado</h5></th><?php } ?>
													<th style='background-color:#E0ECFF;' width="90"><h5>Saldo de Capital</h5></th>
													<?php if ($fila1["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><th style='background-color:#E0ECFF;' width="90"><h5>Saldo de Capital + Seguro Causado</h5></th><?php } ?>
												</tr>
												<?php

												$queryDB = "select * from cuotas".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by cuota";

												$rs1 = sqlsrv_query($link, $queryDB);

												if (sqlsrv_num_rows($rs1))
													$plan_pagos_de_cuotas = 1;

												$queryDB = "select si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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

												if ($_SESSION["S_TIPO"] == "PROSPECCION")
													$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

												$rs = sqlsrv_query($link, $queryDB);

												$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

												$plazo = $fila["plazo"];

												$tasa_interes = $fila["tasa_interes"];

												$saldo = $fila["valor_credito"];

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

												if($fila["seguro_parcial"] && $fila["sin_seguro"]){
													$seguro_calculado = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));

													$seguro_total = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro_base"] * (1 + ($fila["porcentaje_extraprima"] / 100));

													$seguro_a_causar = $seguro_total - $seguro_calculado;

												}else{
													$seguro_a_causar = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
												}

												if (!$fila["sin_seguro"]){
													$seguro = $seguro_a_causar;
												}
												else{
													if($fila["seguro_parcial"]){
														$seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
													}else{
														$seguro = 0;
													}
												}

												switch($fila["opcion_credito"]){
													case "CLI": $opcion_cuota = $fila["opcion_cuota_cli"];
													break;
													case "CCC": $opcion_cuota = $fila["opcion_cuota_ccc"];
													break;
													case "CMP": $opcion_cuota = $fila["opcion_cuota_cmp"];
													break;
													case "CSO": $opcion_cuota = $fila["opcion_cuota_cso"];
													break;
												}

												$valor_cuota = $opcion_cuota - round($seguro);

												$soporte_colpensiones = 500 * $saldo / 1000000;

												?>
												<tr  class="admintable" >
													<td align="center">&nbsp;</td>
													<td align="center">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<td align="right">&nbsp;</td>
													<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right">&nbsp;</td><?php } ?>
													<td align="right">&nbsp;</td>
													<?php if ($fila["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><td align="right">&nbsp;</td><?php } ?>
													<td align="right">$ <?php echo number_format($saldo, 0) ?></td>
												</tr>
												<?php

												$rs_fecha_primera_cuota = sqlsrv_query($link, " select fecha_primera_cuota from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

												$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota);

												if (!$fila_fecha_primera_cuota["fecha_primera_cuota"])
												{
													$rs_fecha_primera_cuota2 = sqlsrv_query($link, "SELECT ISNULL(EOMONTH(DATEADD(MONTH, 2, MIN(fecha_giro))), EOMONTH(DATEADD(MONTH,  2, GETDATE()))) as fecha_primera_cuota from  giros where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

													$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota2);
												}

												$fecha_primera_cuota_tmp = date("Y", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-".date("m", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-01";

												$fecha_primera_cuota = new DateTime($fecha_primera_cuota_tmp);

												$j = 1;

												for ($j = 1; $j <= $plazo; $j++)
												{
													if ($plan_pagos_de_cuotas)
														$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

													if (($j % 2) == 0)
														$tr_class = " style='background-color:#E0ECFF;'";
													else
														$tr_class = " style='background-color:#FFFFFF;'";

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

													if (!$total_cuota){
														$seguro_causado = 10;
													}

													$saldo -= $capital;

													if ($j == $plazo){
														if (!$plan_pagos_de_cuotas){
															$valor_cuota += $saldo;
															$capital = $valor_cuota - $interes;
														}

														$saldo = 0;
													}

													?>
													<tr <?php echo $tr_class ?> class="admintable" >
														<td align="center"><?php echo $j ?></td>
														<td align="center"><?php echo $fecha ?></td>
														<td align="right">$ <?php echo number_format($capital, 0) ?></td>
														<td align="right">$ <?php echo number_format($interes, 0) ?></td>
														<td align="right">$ <?php if ($pagaduria != "COLPENSIONESXXX") { echo number_format($seguro, 0); } else { echo number_format($seguro - $soporte_colpensiones, 0); }?></td>
														<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right">$ <?php echo number_format($soporte_colpensiones, 0) ?></td><?php } ?>
														<td align="right">$ <?php echo number_format($total_cuota, 0) ?></td>
														<?php if ($fila["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><td align="right">$ <?php echo number_format($seguro_causado, 0) ?></td><?php } ?>
														<td align="right">$ <?php echo number_format($saldo, 0) ?></td>
														<?php if ($fila["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><td align="right">$ <?php echo number_format($seguro_causado+$saldo, 0) ?></td><?php } ?>
													</tr>
													<?php

													if ($abono_capital)
													{
														$saldo -= $abono_capital;

														?>
														<tr style='background-color:#FFF5B5;' class="admintable" >
															<td colspan="2">ABONO A CAPITAL</td>
															<td align="right">$ <?php echo number_format($abono_capital, 0) ?></td>
															<td align="right">&nbsp;</td>
															<td align="right">&nbsp;</td>
															<?php if ($pagaduria == "COLPENSIONESXXX") { ?><td align="right">&nbsp;</td><?php } ?>
															<td align="right">&nbsp;</td>
															<?php if ($fila["sin_seguro"] && $_REQUEST["dirigido_a"] == "CLIENTE") { ?><td align="right">&nbsp;</td><?php } ?>
															<td align="right">$ <?php echo number_format($saldo, 0) ?></td>
														</tr>
														<?php

													}

													$fecha_primera_cuota->add(new DateInterval('P1M'));
												}

												?>    
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<br>
										</td>
									</tr>
