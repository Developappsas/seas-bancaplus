<?php
include('../../functions.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if(isset($_REQUEST["id_simulacion"]) && isset($_REQUEST["id_usuario"])){

    $creditos=explode(",",$_REQUEST["id_simulacion"]);


    if (!isset($_REQUEST["externo"]) && (!isset($_SESSION["S_LOGIN"]) || !isset($_SESSION["FUNC_FULLSYSTEM"]) || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))) {
    	$response = array("code"=>"404","mensaje"=>"Debe Iniciar Session");
	}else{
	    
		$link = conectar_utf();
		date_default_timezone_set('Etc/UTC');

		global $link;

	    if (count($creditos)>0) {

			$data=array();

		    for ($i=0; $i<count($creditos); $i++) { 

				sqlsrv_query($link, "BEGIN TRANSACTION");

				if(isset($_SESSION["S_LOGIN"])){
					$id_usuario = $_SESSION["S_IDUSUARIO"];
				}else{
					$id_usuario = $_REQUEST["id_usuario"];
				}

				$rs = sqlsrv_query($link, "select * from simulaciones where id_simulacion = '".$creditos[$i]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				if(sqlsrv_num_rows($rs)>0){

					$fila = sqlsrv_fetch_array($rs);

					$id_simulacion = $fila["id_simulacion"];

					switch($fila["opcion_credito"]){
						case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
						$opcion_desembolso = $fila["opcion_desembolso_cli"];
						break;
						case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
						$opcion_desembolso = $fila["opcion_desembolso_ccc"];
						break;
						case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
						$opcion_desembolso = $fila["opcion_desembolso_cmp"];
						break;
						case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
						$opcion_desembolso = $fila["opcion_desembolso_cso"];
						break;
					}

					if (!$fila["sin_seguro"]){
						$seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
						$diferencia_seguro = 0;
					}
					else{
						if ($fila["seguro_parcial"]==1){
			                $seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));

			                $seguro_total = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro_base"] * (1 + ($fila["porcentaje_extraprima"] / 100));
			                $diferencia_seguro=$seguro_total-$seguro;
			            }else{
			                $seguro = 0;
			                $diferencia_seguro=0;
			            } 
					}

					$fecha_tmp = $fila["fecha_primera_cuota"];
					$fecha = new DateTime($fecha_tmp);
					$plazo = $fila["plazo"];
					$tasa_interes = $fila["tasa_interes"];
					$saldo = $fila["valor_credito"];
					$valor_cuota = $opcion_cuota - $seguro;

					$queryDelete = "DELETE FROM cuotas WHERE id_simulacion = '".$id_simulacion."'";
					if(sqlsrv_query($link, $queryDelete)){

						for ($j = 1; $j <= $plazo; $j++){
							
							$fecha = new DateTime($fecha->format('Y-m-01'));
							$interes = $saldo * $tasa_interes / 100;
							$capital = $valor_cuota - $interes;
							$saldo -= $capital;

							if ($j == $plazo){
								$valor_cuota += $saldo;
								$capital = $valor_cuota - $interes;
								$saldo = 0;
							}

							$saldo_cuota = round($opcion_cuota);
							
							sqlsrv_query($link, "INSERT INTO cuotas (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota, seguro_pendiente, fecha_creacion, usuario_creacion) values ('".$id_simulacion."', '".$j."', '".$fecha->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($opcion_cuota)."', '".$saldo_cuota."', '".round($diferencia_seguro)."', GETDATE(), '".$id_usuario."')");

							$fecha->add(new DateInterval('P1M'));
						}


						$rs1 = sqlsrv_query($link, "SELECT id_simulacion FROM cuotas WHERE id_simulacion = '".$creditos[$i]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

						if(sqlsrv_num_rows($rs1)>0){

							$queryPagos = sqlsrv_query($link, "SELECT a.* FROM pagos a LEFT JOIN pagos_detalle b ON b.id_simulacion = a.id_simulacion AND a.consecutivo = b.consecutivo WHERE b.usuario_anulacion IS NULL AND a.id_simulacion = '$creditos[$i]' GROUP BY a.id_simulacion, a.consecutivo", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

							if($queryPagos && sqlsrv_num_rows($queryPagos) > 0){

								$consecutivo = 0;
								$cuota = 1;

								sqlsrv_query($link, "DELETE FROM pagos_detalle_plan_pagos WHERE id_simulacion = '$creditos[$i]'");
								sqlsrv_query($link, "DELETE FROM pagos_plan_pagos WHERE id_simulacion = '$creditos[$i]'");

								sqlsrv_query($link, "INSERT INTO pagos_plan_pagos SELECT * FROM pagos WHERE id_simulacion = '$creditos[$i]'");
								sqlsrv_query($link, "INSERT INTO pagos_detalle_plan_pagos SELECT * FROM pagos_detalle WHERE id_simulacion = '$creditos[$i]'");

								sqlsrv_query($link, "DELETE FROM pagos_detalle WHERE id_simulacion = '$creditos[$i]'");
								sqlsrv_query($link, "DELETE FROM pagos WHERE id_simulacion = '$creditos[$i]'");

								//$queryPagos = sqlsrv_query($link, "DELETE FROM pagos WHERE b.id_simulacion = a.id_simulacion  DELETE FROM pagos_detalle b ON b.id_simulacion = a.id_simulacion AND a.consecutivo = b.consecutivo WHERE b.usuario_anulacion IS NULL AND a.id_simulacion = '$creditos[$i]'");

								while ($datosPago = sqlsrv_fetch_array($queryPagos)) {

									$consecutivo++;

									sqlsrv_query($link, "INSERT INTO pagos (id_simulacion, consecutivo, fecha, valor, manual, tipo_recaudo, usuario_creacion, fecha_creacion, nombre_original, nombre_grabado) values ('" . $creditos[$i] . "', '" . $consecutivo . "', '" .$datosPago["fecha"] . "', '" . $datosPago["valor"] . "', '".$datosPago["manual"]."', '" . $datosPago["tipo_recaudo"] . "', '" . $datosPago['usuario_creacion'] . "', '".$datosPago["fecha_creacion"]."', '".$datosPago["nombre_original"]."', '".$datosPago["nombre_grabado"]."')");

									if ($datosPago["tipo_recaudo"] == "NOMINA" || $datosPago["tipo_recaudo"] == "VENTANILLA" || $datosPago["tipo_recaudo"] == "ABONOCAPITAL") {
										
										if ($datosPago["tipo_recaudo"] == "NOMINA" || $datosPago["tipo_recaudo"] == "VENTANILLA") {

											$valor_aplicar_total = $datosPago["valor"];
											
											$queryDB = "SELECT cu.*, si.plazo, DATEDIFF(si.fecha_primera_cuota, LAST_DAY('" .$datosPago["fecha"] . "')) as diferencia_fecha_primera_cuota FROM cuotas cu INNER JOIN simulaciones si ON cu.id_simulacion = si.id_simulacion WHERE cu.id_simulacion = '" . $creditos[$i] . "' AND cu.saldo_cuota > 0 ORDER BY cu.cuota";

											$rs = sqlsrv_query($link, $queryDB);

											while ($fila = sqlsrv_fetch_array($rs)) {

												$saldo_cuota = intval($fila["saldo_cuota"]);
												$valor_antes_pago = $saldo_cuota;

												while ($valor_aplicar_total > 0 && $saldo_cuota > 0) {

													if ($saldo_cuota <= $valor_aplicar_total) {
														$valor_aplicar_cuota = $saldo_cuota;
														$valor_aplicar_total = $valor_aplicar_total - $saldo_cuota;
														$saldo_cuota = 0;
													}else{
														$valor_aplicar_cuota = $valor_aplicar_total;
														$saldo_cuota = $saldo_cuota - $valor_aplicar_cuota;
														$valor_aplicar_total = 0;
													}

													if ($valor_aplicar_cuota > 0) {
														if(!sqlsrv_query($link, "INSERT INTO pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('".$creditos[$i]."', '".$consecutivo."', '".$fila["cuota"]."', '".$valor_aplicar_cuota."', '".$valor_antes_pago."')")){
														}

														if ($saldo_cuota <= 0){
															$pagada = "1";
														}
														else{
															$pagada = "0";
														}

														sqlsrv_query($link, "UPDATE cuotas set saldo_cuota = saldo_cuota - " . $valor_aplicar_cuota . ", pagada = '" . $pagada . "' where id_simulacion = '" . $creditos[$i] . "' and cuota = '" . $fila["cuota"] . "'");

														//Si se recauda el 100% de la primera cuota, se ajusta fecha primera cuota
														if ($fila["cuota"] == "1" && $pagada & $fila["diferencia_fecha_primera_cuota"] > 0) {
															$fecha_tmp = $datosPago["fecha"];

															$fecha = new DateTime($fecha_tmp);

															sqlsrv_query($link, "UPDATE simulaciones set fecha_primera_cuota = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $creditos[$i] . "'");

															sqlsrv_query($link, "INSERT INTO simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('" . $creditos[$i] . "', '" . $fecha->format('Y-m-t') . "', 'system', GETDATE())");

															for ($j = 1; $j <= $fila["plazo"]; $j++) {
																$fecha = new DateTime($fecha->format('Y-m-01'));

																sqlsrv_query($link, "UPDATE cuotas SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $creditos[$i] . "' AND cuota = '" . $j . "'");

																$fecha->add(new DateInterval('P1M'));
															}
														}

														$valor_antes_pago -= $valor_aplicar_cuota;
													}
												}
											}
										} elseif ($datosPago["tipo_recaudo"] == "ABONOCAPITAL") {
											
											$queryDB = "SELECT si.*, cu.seguro from simulaciones si INNER JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '" . $creditos[$i] . "' AND cu.cuota = '1'";

											$rs = sqlsrv_query($link, $queryDB);

											$fila = sqlsrv_fetch_array($rs);

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

											$queryDB = "SELECT SUM(capital) as s from cuotas where id_simulacion = '" . $creditos[$i] . "' AND saldo_cuota = valor_cuota";

											$rs1 = sqlsrv_query($link, $queryDB);

											$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

											$saldo_capital = $fila1["s"];

											sqlsrv_query($link, "INSERT INTO pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $creditos[$i] . "', '" . $consecutivo . "', '0', '" . $datosPago["valor"] . "', '" . $saldo_capital . "')");

											$saldo = $saldo_capital - $datosPago["valor"];

											$queryDB = "SELECT * from cuotas where id_simulacion = '" . $creditos[$i] . "' and saldo_cuota = valor_cuota order by cuota";

											$rs = sqlsrv_query($link, $queryDB);

											$primera_iteracion = 1;

											while ($fila = sqlsrv_fetch_array($rs)) {
												if ($primera_iteracion) {
													sqlsrv_query($link, "UPDATE cuotas set abono_capital = abono_capital + " . $datosPago["valor"] . " where id_simulacion = '" . $creditos[$i] . "' and cuota = '" . ($fila["cuota"] - 1) . "'");

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

												sqlsrv_query($link, "UPDATE cuotas set capital_org = (CASE WHEN capital_org IS NULL THEN capital ELSE capital_org END), interes_org = (CASE WHEN interes_org IS NULL THEN interes ELSE interes_org END), capital = '" . round($capital) . "', interes = '" . round($interes) . "', seguro = '" . round($seguro) . "', valor_cuota = '" . round($total_cuota) . "', saldo_cuota = '" . round($saldo_cuota) . "', pagada = '" . $pagada . "' where id_simulacion = '" . $creditos[$i] . "' and cuota = '" . $fila["cuota"] . "'");
											}
										}

										$queryDB = "SELECT SUM(saldo_cuota) as s from cuotas where id_simulacion = '" . $creditos[$i] . "'";

										$rs1 = sqlsrv_query($link, $queryDB);

										$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

										if ($fila1["s"] == 0) {
											sqlsrv_query($link, "UPDATE simulaciones set estado = 'CAN', retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '" . $creditos[$i] . "'");
										}

										//Para saber si ya hubo recaudo completo en el mes que se aplica el recaudo
										$queryDB = "SELECT valor_cuota - CASE WHEN fn_total_recaudado_mes(" . $creditos[$i] . ", 0, '" .$datosPago["fecha"] . "') IS NULL THEN 0 ELSE fn_total_recaudado_mes(" . $creditos[$i] . ", 0, '" .$datosPago["fecha"] . "') END as s from cuotas where id_simulacion = '" . $creditos[$i] . "' AND FORMAT(fecha, 'yyyy-MM') = FORMAT('" .$datosPago["fecha"] . "', 'yyyy-MM')";
										$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

										if($rs1 && sqlsrv_num_rows($rs1) > 0){
											$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
											if ($fila1["s"] <= 0) {
												sqlsrv_query($link, "DELETE from cuotas_norecaudadas where id_simulacion = '" . $creditos[$i] . "' AND fecha = EOMONTH('" .$datosPago["fecha"] . "')");
											}
										}
									} else {
										sqlsrv_query($link, "INSERT INTO pagos_detalle (id_simulacion, consecutivo, cuota, valor, valor_antes_pago) values ('" . $creditos[$i] . "', '" . $consecutivo . "', '0', '" . $datosPago["valor"] . "', '" . $datosPago["valor"] . "')");
									}

									$queryDB = "SELECT vd.id_ventadetalle, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, fn_total_recaudado(si.id_simulacion, 0) as total_recaudado from ventas_detalle vd INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion where vd.id_simulacion = '" . $creditos[$i] . "' AND ve.estado = 'ALI' AND ve.fecha_corte IS NULL";

									$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

									if (sqlsrv_num_rows($rs)) {
										$fila = sqlsrv_fetch_array($rs);

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

										sqlsrv_query($link, "UPDATE ventas_detalle set cuota_desde = '" . $cuota_desde . "' where id_ventadetalle = '" . $fila["id_ventadetalle"] . "'");
									}

									sqlsrv_query($link, "COMMIT");
								}

								$data[] = array( 'credito' => $creditos[$i], "code"=>"200", "mensaje"=> 'Plan de Pagos Generado');
							}else{
								sqlsrv_query($link, "COMMIT");
								$data[] = array("code"=>"200","mensaje"=>"Plan de pagos generado, Sin pagos por aplicar" );
							}
						}else{
							$data[] = array("code"=>"501","mensaje"=>"Error, plan de pagos no creado." );
						}
					}else{
						$data[] = array("code"=>"500","mensaje"=>"Error No se Pudo Eliminar el plan de pagos anterior.");
					}
				}else{
					$data[] = array("code"=>"500","mensaje"=>"Error No se Pudo Eliminar el plan de pagos anterior.");
				}
			}

			$response = array("code"=>"200","mensaje"=>"Ejecutado", "data" => $data);
		}else{
		    $response = array("code"=>"404","mensaje"=>"No se recibieron ID Simulacion");
		}
	}
}else{
    $response = array("code"=>"404","mensaje"=>"No se recibieron parametros");
}

echo json_encode($response);