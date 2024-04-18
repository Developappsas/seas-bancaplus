<?php
    include ('../../functions.php');
    $link = conectar();

    if(isset($_REQUEST['tipo'])){

    	$arrayDatos = array();

		$arrayDatos[0][] = "Tipo Cartera";
		$arrayDatos[0][] = "ID";
		$arrayDatos[0][] = "Cedula";
		$arrayDatos[0][] = "F. Desembolso Inicial";
		$arrayDatos[0][] = "F. Desembolso Final";
		$arrayDatos[0][] = "Mes Prod";
		$arrayDatos[0][] = "F. Primera Cuota";
		$arrayDatos[0][] = "Nombre";
		$arrayDatos[0][] = "Direccion";
		$arrayDatos[0][] = "Telefono";
		$arrayDatos[0][] = "Celular";
		$arrayDatos[0][] = "E.mail";
		$arrayDatos[0][] = "No. Libranza";
		$arrayDatos[0][] = "Unidad de Negocio";
		$arrayDatos[0][] = "Tasa";
		$arrayDatos[0][] = "Cuota Total";
		$arrayDatos[0][] = "Cuota Corriente";
		$arrayDatos[0][] = "Seguro";
		$arrayDatos[0][] = "Vr. Credito";
		$arrayDatos[0][] = "Saldo de Kapital";

		if ($_SESSION["FUNC_BOLSAINCORPORACION"]) { 
			$arrayDatos[0][] = "Bolsa Incorporacion";
		} 

		$arrayDatos[0][] = "Sector";
		$arrayDatos[0][] = "Pagaduria";
		$arrayDatos[0][] = "Plazo";
		$arrayDatos[0][] = "Incorporacion";
		$arrayDatos[0][] = "F. Venta";
		$arrayDatos[0][] = "F. Primer Vcto.";
		$arrayDatos[0][] = "Cuotas Vendidas";
		$arrayDatos[0][] = "Vr. Cuota Vendida";
		$arrayDatos[0][] = "Vr. Capital Vendido";
		$arrayDatos[0][] = "No. Venta";
		$arrayDatos[0][] = "Comprador";
		$arrayDatos[0][] = "Tipo Venta";
		$arrayDatos[0][] = "Caracteristica";
		$arrayDatos[0][] = "Estado";
		$arrayDatos[0][] = "Comprador Prepago";
		$arrayDatos[0][] = "F. Prepago";
		$arrayDatos[0][] = "Vr. Prepago";
		$arrayDatos[0][] = "Saldo Capital (PP)";
		$arrayDatos[0][] = "Intereses (PP)";
		$arrayDatos[0][] = "Seguro (PP)";
		$arrayDatos[0][] = "Cuotas Mora (PP)";
		$arrayDatos[0][] = "Interes Mora (PP)";
		$arrayDatos[0][] = "Gastos Cobranza (PP)";
		$arrayDatos[0][] = "Total Pagar (PP)";
		$arrayDatos[0][] = "No. Libranza Cancelacion x Retanqueo";
		$arrayDatos[0][] = "Vr. Cancelacion x Retanqueo";
		$arrayDatos[0][] = "Saldo Capital (RET)";
		$arrayDatos[0][] = "Intereses (RET)";
		$arrayDatos[0][] = "Seguro (RET)";
		$arrayDatos[0][] = "Cuotas Mora (RET)";
		$arrayDatos[0][] = "Interes Mora (RET)";
		$arrayDatos[0][] = "Gastos Cobranza (RET)";
		$arrayDatos[0][] = "Total Pagar (RET)";
		$arrayDatos[0][] = "Prepago Fondeador";
		$arrayDatos[0][] = "F. Primer Recaudo x Nomina";
		$arrayDatos[0][] = "Fecha Vencimiento";

		$fecha_recaudo_tmp = "2014-04-01";
		$fecha_recaudo = new DateTime($fecha_recaudo_tmp);

		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
			$fecha_actual = new DateTime($_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-01");
			$fecha_corte_query = "'".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
		else{
			$fecha_actual = new DateTime(date('Y-m-01'));
			$fecha_corte_query = "GETDATE()";
		}

		$fecha_final = $fecha_actual->add(new DateInterval('P1M'));
		while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m')){
			$arrayDatos[0][] = $fecha_recaudo->format('Y-m');
			$fecha_recaudo->add(new DateInterval('P1M'));
		}

		$arrayDatos[0][] = "Total Recaudado";
		$arrayDatos[0][] = "Cuotas Pagadas";
		$arrayDatos[0][] = "Cuotas Causadas";
		$arrayDatos[0][] = "Cuotas en Mora";
		$arrayDatos[0][] = "Interes en Mora";
		$arrayDatos[0][] = "Saldo en Mora";
		$arrayDatos[0][] = "Calificacion";
		$arrayDatos[0][] = "Fecha ult reca";
		$arrayDatos[0][] = "KP PLUS";
		$arrayDatos[0][] = "Saldo Seguro Causado";
		$arrayDatos[0][] = "Responsable Gestion Cobranza";

		if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL"){

			$queryDB = "SELECT si.resp_gestion_cobranza,'ORIGINACION' as tipo, si.id_simulacion, si.cedula, si.fecha_desembolso as fecha_desembolso, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN dbo.fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE dbo.fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.fecha_primera_cuota, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre as unidad_negocio, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, dbo.fn_bolsa_pagos(si.id_simulacion, ".$fecha_corte_query.") - dbo.fn_bolsa_aplicaciones(si.id_simulacion, ".$fecha_corte_query.") as saldo_bolsa, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, 0 as cobro_adicional_en_cuota, si.pagaduria, si.plazo, cu2.seguro, CASE WHEN ( SELECT TOP 1 tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion) IS NOT NULL THEN 'SI' ELSE 'NO' END as incorporacion, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, ed.nombre as comprador_prepago, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, CASE WHEN si.prepagado_fondeador = '1' THEN 'SI' ELSE 'NO' END as prepagado_fondeador, dbo.fn_fecha_primer_recaudo(si.id_simulacion, 0, ".$fecha_corte_query.") as fecha_primer_recaudo, dbo.fn_total_recaudado_query(si.id_simulacion, 0, ".$fecha_corte_query.") as total_recaudado_query, si.fecha_creacion as fecha_creacion, dbo.fn_cuotas_causadas(si.id_simulacion, 0, ".$fecha_corte_query.") as cuotas_causadas, ca.nombre as caracteristica, pa.sector, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN entidades_desembolso ed ON ed.id_entidad = si.id_compradorprep LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion where (si.estado IN ('DES', 'CAN') OR (si.estado IN ('EST') AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN ('".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."')))))";
			
			if ($_SESSION["S_SECTOR"]){
				$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
			}
			
			$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
			
			if ($_REQUEST["cedula"]){
				$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
			}
			
			if ($_REQUEST["sector"]){
				$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
			}
			
			if ($_REQUEST["pagaduria"]){
				$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
			}
			
			if ($_REQUEST["incorporacion"]){
				$queryDB .= " AND (SELECT TOP 1 tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion )";
				
				if ($_REQUEST["incorporacion"] == "SI")
					$queryDB .= " IS NOT NULL";
				else
					$queryDB .= " IS NULL";
			}
			
			if ($_REQUEST["estado"]){
				//$queryDB .= " AND si.estado = '".$_REQUEST["estado"]."'";
			}
			
			if ($_REQUEST["calificacion"] != ""){
				//if ($_REQUEST["calificacion"] == "-1")
				//	$queryDB .= " AND si.estado = 'CAN'";
				//else
				//	$queryDB .= " AND si.estado <> 'CAN'";
			}
			
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
				$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
			}
			
			$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, si.pagaduria, si.plazo, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, cu2.seguro, pa.nombre";
		}

		if ($_REQUEST["tipo"] == "ALL"){
			$queryDB .= " UNION ";
		}

		if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL"){

			$queryDB .= "SELECT 'EXTERNA' as tipo, si.id_simulacion, si.cedula, si.fecha_desembolso as fecha_desembolso, si.fecha_desembolso as fecha_desembolso_final, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.fecha_primera_cuota, si.nombre, si.direccion, si.telefono, si.celular, si.email, si.nro_libranza, '' as unidad_negocio, si.tasa_interes, si.opcion_credito, 0 as opcion_cuota_cli, 0 as opcion_desembolso_cli, 0 as opcion_cuota_ccc, 0 as opcion_desembolso_ccc, 0 as opcion_cuota_cmp, 0 as opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, 0 as saldo_bolsa, si.valor_por_millon_seguro, 0 as sin_seguro, si.porcentaje_extraprima, si.cobro_adicional_en_cuota, si.pagaduria, si.plazo, cu2.seguro, CASE WHEN (SELECT tipo_recaudo from pagos_ext where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion LIMIT 1) IS NOT NULL THEN 'SI' ELSE 'NO' END as incorporacion, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, ed.nombre as comprador_prepago, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, NULL as prepago_intereses, NULL as prepago_seguro, NULL as prepago_cuotasmora, NULL as prepago_gastoscobranza, NULL as prepago_totalpagar, NULL as retanqueo_id_simulacion_cancelacion, '' as retanqueo_libranza_cancelacion, 0 as retanqueo_valor_cancelacion, NULL as retanqueo_valor_liquidacion, NULL as retanqueo_intereses, NULL as retanqueo_seguro, NULL as retanqueo_cuotasmora, NULL as retanqueo_gastoscobranza, NULL as retanqueo_totalpagar, CASE WHEN si.prepagado_fondeador = '1' THEN 'SI' ELSE 'NO' END as prepagado_fondeador, dbo.fn_fecha_primer_recaudo(si.id_simulacion, 1, ".$fecha_corte_query.") as fecha_primer_recaudo, dbo.fn_total_recaudado_query(si.id_simulacion, 1, ".$fecha_corte_query.") as total_recaudado_query, si.fecha_creacion as fecha_creacion, dbo.fn_cuotas_causadas(si.id_simulacion, 1, ".$fecha_corte_query.") as cuotas_causadas, '' as caracteristica, pa.sector, 'NO' as sin_seguro_x from simulaciones_ext si LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN cuotas_ext cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN entidades_desembolso ed ON ed.id_entidad = si.id_compradorprep where (si.estado IN ('DES', 'CAN'))";
			
			if ($_SESSION["S_SECTOR"]){
				$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
			}
			
			if ($_REQUEST["cedula"]){
				$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
			}
			
			if ($_REQUEST["sector"]){
				$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
			}
			
			if ($_REQUEST["pagaduria"]){
				$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
			}
			
			if ($_REQUEST["incorporacion"]){
				$queryDB .= " AND (SELECT TOP 1 tipo_recaudo from pagos_ext where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion )";
				
				if ($_REQUEST["incorporacion"] == "SI")
					$queryDB .= " IS NOT NULL";
				else
					$queryDB .= " IS NULL";
			}
			
			if ($_REQUEST["estado"]){
				//$queryDB .= " AND si.estado = '".$_REQUEST["estado"]."'";
			}
			
			if ($_REQUEST["calificacion"] != ""){
				//if ($_REQUEST["calificacion"] == "-1")
				//	$queryDB .= " AND si.estado = 'CAN'";
				//else
				//	$queryDB .= " AND si.estado <> 'CAN'";
			}
			
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
				$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
			}
			
			$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, si.direccion, si.telefono, si.celular, si.email, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.valor_por_millon_seguro, si.porcentaje_extraprima, si.pagaduria, si.plazo, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, cu2.seguro, pa.nombre";
		}

		$rs = sqlsrv_query($queryDB, $link);

		$queryDBPrueba = $queryDB;

		$conFila = 1;
		while (@$fila = sqlsrv_fetch_array($rs)){

			$consultarUltimaCuotaPlanPagos="SELECT top 1 * FROM cuotas WHERE id_simulacion='".$fila["id_simulacion"]."' ORDER BY cuota DESC ";
			$queryUltimaCuotaPlanPagos=sqlsrv_query($link, $consultarUltimaCuotaPlanPagos);
			$resUltimaCuotaPlanPagos=sqlsrv_fetch_array($queryUltimaCuotaPlanPagos);

			$sufijo = "";
			
			if ($fila["tipo"] == "EXTERNA")
				$sufijo = "_ext";
			
			$fecha_venta = "";
			$fecha_primer_pago = "";
			$cuotas_vendidas = "";
			$valor_cuota_vendida = "";
			$valor_capital_vendido = "";
			$nro_venta = "";
			$comprador = "";
			$tipo_venta = "";
			
			$queryDB = "SELECT ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vc.valor_cuota as valor_cuota_vendida, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, SUM(cu.capital) as valor_capital_vendido from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN ventas_cuotas".$sufijo." vc ON vd.id_ventadetalle = vc.id_ventadetalle AND vc.cuota = vd.cuota_desde LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where si.id_simulacion = '".$fila["id_simulacion"]."' AND ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0' AND ve.fecha <= ".$fecha_corte_query." GROUP BY ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, vc.valor_cuota, ve.nro_venta, co.nombre, ve.modalidad_prima";
			
			$rs2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs2)){
				
				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				$fecha_venta = $fila2["fecha_venta"];
				$fecha_primer_pago = $fila2["fecha_primer_pago"];
				$cuotas_vendidas = $fila2["cuotas_vendidas"];
				$valor_cuota_vendida = $fila2["valor_cuota_vendida"];
				$valor_capital_vendido = $fila2["valor_capital_vendido"];
				
				if ($fila2["cuotas_vendidas"] == $fila["plazo"])
					$valor_capital_vendido = $fila["valor_credito"];
				
				$nro_venta = $fila2["nro_venta"];
				$comprador = $fila2["comprador"];
				
				$tipo_venta = "";
				
				switch($fila2["modalidad_prima"]){
					case "ANT":	$tipo_venta = "PRIMA ANTICIPADA";
								break;
					case "MDI":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN INTERESES";
								break;
					case "MDC":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN CUOTA";
								break;
				}
			}
			
			$comprador_prepago = $fila["comprador_prepago"];
			$fecha_prepago = $fila["fecha_prepago"];
			$valor_prepago = $fila["valor_prepago"];
			$valor_liquidacion = "";
			$prepago_intereses = "";
			$prepago_seguro = "";
			$prepago_cuotasmora = "";
			$prepago_gastoscobranza = "";
			$prepago_totalpagar = "";
			
			if ($fila["fecha_prepago"]){
				$valor_liquidacion = $fila["valor_liquidacion"];
				$prepago_intereses = $fila["prepago_intereses"];
				$prepago_seguro = $fila["prepago_seguro"];
				$prepago_cuotasmora = $fila["prepago_cuotasmora"];
				$prepago_gastoscobranza = $fila["prepago_gastoscobranza"];
				$prepago_totalpagar = $fila["prepago_totalpagar"];
			}
			
			$retanqueo_id_simulacion_cancelacion = $fila["retanqueo_id_simulacion_cancelacion"];
			$retanqueo_libranza_cancelacion = $fila["retanqueo_libranza_cancelacion"];
			$retanqueo_valor_cancelacion = $fila["retanqueo_valor_cancelacion"];
			
			$retanqueo_valor_liquidacion = "";
			$retanqueo_intereses = "";
			$retanqueo_seguro = "";
			$retanqueo_cuotasmora = "";
			$retanqueo_gastoscobranza = "";
			$retanqueo_totalpagar = "";
			
			if ($fila["retanqueo_libranza_cancelacion"]){
				$retanqueo_valor_liquidacion = $fila["retanqueo_valor_liquidacion"];
				$retanqueo_intereses = $fila["retanqueo_intereses"];
				$retanqueo_seguro = $fila["retanqueo_seguro"];
				$retanqueo_cuotasmora = $fila["retanqueo_cuotasmora"];
				$retanqueo_gastoscobranza = $fila["retanqueo_gastoscobranza"];
				$retanqueo_totalpagar = $fila["retanqueo_totalpagar"];
			}
			
			//Si est� filtrado por fecha de corte
			if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
				//Si est� filtrado por fecha de corte y no sale venta por el query, se comprueba si hay ventas recompradas
				if (!$nro_venta){
					$queryDB = "SELECT TOP 1 ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vc.valor_cuota as valor_cuota_vendida, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, SUM(cu.capital) as valor_capital_vendido from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN ventas_cuotas".$sufijo." vc ON vd.id_ventadetalle = vc.id_ventadetalle AND vc.cuota = vd.cuota_desde LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where si.id_simulacion = '".$fila["id_simulacion"]."' AND ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '1' AND ve.fecha <= ".$fecha_corte_query." GROUP BY ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, vc.valor_cuota, ve.nro_venta, co.nombre, ve.modalidad_prima order by ve.fecha DESC ";
					
					$rs2 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($rs2)){

						$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
						
						$fecha_venta = $fila2["fecha_venta"];
						$fecha_primer_pago = $fila2["fecha_primer_pago"];
						$cuotas_vendidas = $fila2["cuotas_vendidas"];
						$valor_cuota_vendida = $fila2["valor_cuota_vendida"];
						$valor_capital_vendido = $fila2["valor_capital_vendido"];
						
						if ($fila2["cuotas_vendidas"] == $fila["plazo"])
							$valor_capital_vendido = $fila["valor_credito"];
						
						$nro_venta = $fila2["nro_venta"];
						$comprador = $fila2["comprador"];
						
						$tipo_venta = "";
						
						switch($fila2["modalidad_prima"]){
							case "ANT":	
								$tipo_venta = "PRIMA ANTICIPADA";
								break;
							case "MDI":	
								$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN INTERESES";
								break;
							case "MDC":
								$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN CUOTA";
								break;
						}
					}
				}
				
				if ($fila["fecha_prepago"] && $fila["fecha_prepago"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]){
					$comprador_prepago = "";
					$fecha_prepago = "";
					$valor_prepago = "";
					$valor_liquidacion = "";
					$prepago_intereses = "";
					$prepago_seguro = "";
					$prepago_cuotasmora = "";
					$prepago_gastoscobranza = "";
					$prepago_totalpagar = "";
					
					$fila["estado"] = "VIGENTE";
				}
				
				if ($retanqueo_id_simulacion_cancelacion && $fila["tipo"] == "ORIGINACION" ){
					
					$queryDB = "select MAX(fecha_giro) as fecha_desembolso_final from giros where id_simulacion = '".$retanqueo_id_simulacion_cancelacion."'";
					$rs2 = sqlsrv_query($link, $queryDB);
					$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
					
					if ($fila2["fecha_desembolso_final"] && $fila2["fecha_desembolso_final"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]){
						$retanqueo_valor_cancelacion = "";
						$retanqueo_valor_liquidacion = "";
						$retanqueo_intereses = "";
						$retanqueo_libranza_cancelacion = "";
						$retanqueo_seguro = "";
						$retanqueo_cuotasmora = "";
						$retanqueo_gastoscobranza = "";
						$retanqueo_totalpagar = "";
						
						$fila["estado"] = "VIGENTE";
					}
				}
				
				if ($fila["estado"] == "CANCELADO" && !$fila["fecha_prepago"]){
					$queryDB = "select MAX(pa.fecha) as fecha_ultimo_recaudo from pagos".$sufijo." pa INNER JOIN pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pd.valor > 0 AND pa.tipo_recaudo NOT IN ('NOMINA_DEV', 'VENTANILLA_DEV')";
					
					$rs2 = sqlsrv_query($link, $queryDB);
					
					$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
					
					if ($fila2["fecha_ultimo_recaudo"] && $fila2["fecha_ultimo_recaudo"] > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]){
						$fila["estado"] = "VIGENTE";
					}
				}
			}
			
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
			
			//Se calculan las cuotas_pagadas y las cuotas_mora al principio para saber si se muestra o no el registro cuando se filtra por calificacion. Luego se recalculan m�s abajo
			if ($fila["estado"] != "CANCELADO"){
				$cuotas_pagadas_query = number_format($fila["total_recaudado_query"] / $opcion_cuota, 2);
				
				if (number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2) > 0)
					$cuotas_mora = number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2);
				else
					$cuotas_mora = 0;
			}
			
			if ($_REQUEST["estado"] != ""){
				if (($_REQUEST["estado"] == "DES" && $fila["estado"] != "VIGENTE") || ($_REQUEST["estado"] == "CAN" && $fila["estado"] != "CANCELADO")){
					continue;
				}
			}
			
			if ($_REQUEST["calificacion"] != ""){
				if (($_REQUEST["calificacion"] == "-1" && $fila["estado"] != "CANCELADO") || ($_REQUEST["calificacion"] != "-1" && (ceil($cuotas_mora) != $_REQUEST["calificacion"] || $fila["estado"] == "CANCELADO"))){
					continue;
				}
			}
			
			if ($fila["seguro"] || $fila["seguro"] == "0")
				$seguro = $fila["seguro"];
			else
				if (!$fila["sin_seguro"])
					$seguro = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100)));
				else
					$seguro = 0;

			$cuota_corriente = $opcion_cuota - $seguro - $fila["cobro_adicional_en_cuota"] ;
			
			$capital_recaudado = 0;
			
			if ($fila["estado"] != "CANCELADO"){
				$queryDB = "select SUM(capital + abono_capital) as capital_recaudado from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota <= '".floor($cuotas_pagadas_query)."' AND pagada = '1'";
				
				$rs2 = sqlsrv_query($link, $queryDB);
				
				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				
				if ($fila2["capital_recaudado"])
					$capital_recaudado = $fila2["capital_recaudado"];
				
				if (floor($cuotas_pagadas_query) != ceil($cuotas_pagadas_query)){
					$aplicado_a_ultima_cuota = $fila["total_recaudado_query"] - (floor($cuotas_pagadas_query) * $opcion_cuota);
					
					//Si capital no es mayor que cero significa que se realiz� en abono a capital y esa cuota no hace parte del plan de pagos regenerado
					$queryDB = "select * from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota = '".ceil($cuotas_pagadas_query)."' AND capital > 0";
					
					$rs3 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($rs3))
					{
						$fila3 = sqlsrv_fetch_array($rs3);
						
						if ($aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] > 0)
							$capital_recaudado += $aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] + $fila3["abono_capital"];
						else
							$capital_recaudado += $fila3["abono_capital"];
					}
				}
				
				$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
			}
			else{
				$saldo_capital = 0;
			}

			$arrayDatos[$conFila][] = $fila["tipo"];
			$arrayDatos[$conFila][] = $fila["id_simulacion"];
			$arrayDatos[$conFila][] = $fila["cedula"];
			$arrayDatos[$conFila][] = $fila["fecha_desembolso"];
			$arrayDatos[$conFila][] = $fila["fecha_desembolso_final"];
			$arrayDatos[$conFila][] = $fila["mes_prod"];
			$arrayDatos[$conFila][] = $fila["fecha_primera_cuota"];
			$arrayDatos[$conFila][] = utf8_decode($fila["nombre"]);
			$arrayDatos[$conFila][] = utf8_decode($fila["direccion"]);
			$arrayDatos[$conFila][] = utf8_decode($fila["telefono"]);
			$arrayDatos[$conFila][] = utf8_decode($fila["celular"]);
			$arrayDatos[$conFila][] = utf8_decode($fila["email"]);
			$arrayDatos[$conFila][] = $fila["nro_libranza"];
			$arrayDatos[$conFila][] = utf8_decode($fila["unidad_negocio"]);
			$arrayDatos[$conFila][] = $fila["tasa_interes"];
			$arrayDatos[$conFila][] = $opcion_cuota;
			$arrayDatos[$conFila][] = $cuota_corriente;
			$arrayDatos[$conFila][] = $seguro;
			$arrayDatos[$conFila][] = $fila["valor_credito"];
			$arrayDatos[$conFila][] = $saldo_capital;
			
			if ($_SESSION["FUNC_BOLSAINCORPORACION"]) { 
				$arrayDatos[$conFila][] = $fila["saldo_bolsa"];
			}

			$arrayDatos[$conFila][] = $fila["sector"];
			$arrayDatos[$conFila][] = utf8_decode($fila["pagaduria"]);
			$arrayDatos[$conFila][] = $fila["plazo"];
			$arrayDatos[$conFila][] = $fila["incorporacion"];
			$arrayDatos[$conFila][] = $fecha_venta;
			$arrayDatos[$conFila][] = $fecha_primer_pago;
			$arrayDatos[$conFila][] = $cuotas_vendidas;
			$arrayDatos[$conFila][] = $valor_cuota_vendida;
			$arrayDatos[$conFila][] = $valor_capital_vendido;
			$arrayDatos[$conFila][] = $nro_venta;
			$arrayDatos[$conFila][] = utf8_decode($comprador);
			$arrayDatos[$conFila][] = $tipo_venta;
			$arrayDatos[$conFila][] = utf8_decode($fila["caracteristica"]);
			$arrayDatos[$conFila][] = $fila["estado"];
			$arrayDatos[$conFila][] = utf8_decode($comprador_prepago);
			$arrayDatos[$conFila][] = $fecha_prepago;
			$arrayDatos[$conFila][] = $valor_prepago;
			$arrayDatos[$conFila][] = $valor_liquidacion;
			$arrayDatos[$conFila][] = $prepago_intereses;
			$arrayDatos[$conFila][] = $prepago_seguro;
			$arrayDatos[$conFila][] = $prepago_cuotasmora;
			$arrayDatos[$conFila][] = ($prepago_cuotasmora*$prepago_intereses);
			$arrayDatos[$conFila][] = $prepago_gastoscobranza;
			$arrayDatos[$conFila][] = $prepago_totalpagar;
			$arrayDatos[$conFila][] = $retanqueo_libranza_cancelacion;
			$arrayDatos[$conFila][] = $retanqueo_valor_cancelacion;
			$arrayDatos[$conFila][] = $retanqueo_valor_liquidacion;
			$arrayDatos[$conFila][] = $retanqueo_intereses;
			$arrayDatos[$conFila][] = $retanqueo_seguro;
			$arrayDatos[$conFila][] = $retanqueo_cuotasmora;
			$arrayDatos[$conFila][] = ($retanqueo_cuotasmora*$retanqueo_intereses);

			$arrayDatos[$conFila][] = $retanqueo_gastoscobranza;
			$arrayDatos[$conFila][] = $retanqueo_totalpagar;
			$arrayDatos[$conFila][] = $fila["prepagado_fondeador"];
			$arrayDatos[$conFila][] = $fila["fecha_primer_recaudo"];
			$arrayDatos[$conFila][] = $resUltimaCuotaPlanPagos["fecha"];

			$queryDB = "SELECT FORMAT(pa.fecha, 'yyyy-MM') as fecha, SUM(pd.valor) as valor_recaudado from pagos".$sufijo." pa inner join pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pa.fecha <= ".$fecha_corte_query." group by FORMAT(pa.fecha, 'yyyy-MM') order by FORMAT(pa.fecha, 'yyyy-MM')";
			
			$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($rs1)){
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
				
				$fecha = $fila1["fecha"];
			}
			
			$j = 0;
			
			$total_recaudado = 0;
			
			$fecha_recaudo_tmp = "2014-04-01";
			
			$fecha_recaudo = new DateTime($fecha_recaudo_tmp);
			
			while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m')){
				
				$valor_recaudado = 0;
				
				if ($fecha_recaudo->format('Y-m') == $fecha){
					
					$valor_recaudado = $fila1["valor_recaudado"];					
					$total_recaudado += $fila1["valor_recaudado"];					
					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);					
					$fecha = $fila1["fecha"];
				}
				else{
					$valor_recaudado = "";
				}

				$arrayDatos[$conFila][] = $valor_recaudado;

				if ($valor_recaudado){
					$total_valor_recaudado[$j] += $valor_recaudado;
				}
				
				$fecha_recaudo->add(new DateInterval('P1M'));
				
				$j++;
			}
			
			if ($fila["estado"] == "CANCELADO"){
				$cuotas_pagadas = "NA";
				$cuotas_causadas = "NA";
				$cuotas_mora = "NA";
				$saldo_mora = "NA";
				$seguro_causado = "NA";
			}
			else{
				$cuotas_pagadas = number_format($total_recaudado / $opcion_cuota, 2);
				$cuotas_causadas = $fila["cuotas_causadas"];
				
				if (number_format($cuotas_causadas - $cuotas_pagadas, 2) > 0){
					$cuotas_mora = number_format($cuotas_causadas - $cuotas_pagadas, 2);
					$saldo_mora = ($cuotas_causadas * $opcion_cuota) - $total_recaudado;
				}
				else{
					$cuotas_mora = 0;
					$saldo_mora = 0;
				}
				
				if ($fila["sin_seguro_x"] == "SI")
					$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;
				else
					$seguro_causado = 0;
			}
			
			if ($fila["estado"] == "CANCELADO")
				$calificacion = "CANCELADO";
			else if ($cuotas_mora){
				$limite1_calificacion = (ceil($cuotas_mora) * 30) - 29;
				$limite2_calificacion = ceil($cuotas_mora) * 30;
				
				$calificacion = $limite1_calificacion." a ".$limite2_calificacion;
			}
			else
				$calificacion = "AL DIA";

			$arrayDatos[$conFila][] = $total_recaudado;
			$arrayDatos[$conFila][] = $cuotas_pagadas;
			$arrayDatos[$conFila][] = $cuotas_causadas;
			$arrayDatos[$conFila][] = $cuotas_mora;
			$arrayDatos[$conFila][] = ((number_format($saldo_capital * $fila["tasa_interes"] / 100.00, 0, ".", ","))*$cuotas_mora);
			$arrayDatos[$conFila][] = $saldo_mora;
			$arrayDatos[$conFila][] = $calificacion;

			$queryDB1 = "SELECT TOP 1 FORMAT(pa.fecha, 'Y-m-d') as fecha from pagos".$sufijo." pa inner join pagos_detalle".$sufijo." pd ON pa.id_simulacion = pd.id_simulacion AND pa.consecutivo = pd.consecutivo where pa.id_simulacion = '".$fila["id_simulacion"]."' AND pa.fecha < ".$fecha_corte_query." group by FORMAT(pa.fecha, 'Y-m-d') order by FORMAT(pa.fecha, 'Y-m-d') desc ";
			$rs2 = sqlsrv_query($link, $queryDB1, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET)	);
			
			if (sqlsrv_num_rows($rs2)){
				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				$fecharecau = $fila2["fecha"];
			}
			else 
				$fecharecau="1900-01-01";	

			$arrayDatos[$conFila][] = $fecharecau;
			$arrayDatos[$conFila][] = $fila["sin_seguro_x"];
			$arrayDatos[$conFila][] = $seguro_causado;

			if ($fila["resp_gestion_cobranza"]==""){
				$cobranza="NO APLICA";
			}else{
				$consultaCobranza="SELECT * FROM resp_gestion_cobros WHERE id_resp_cobros='".$fila["resp_gestion_cobranza"]."'";
				$queryCobranza=sqlsrv_query($con, $consultaCobranza);
				$resCobranza=sqlsrv_fetch_array($queryCobranza);
				$cobranza=$resCobranza." - ".$fila["detalle_resp_gestion_cobranza"];
			}

			$arrayDatos[$conFila][] = $cobranza;

			$total_opcion_cuota += $opcion_cuota;
			$total_cuota_corriente += $cuota_corriente;
			$total_seguro += $seguro;
			$total_valor_credito += $fila["valor_credito"];
			$total_saldo_capital += $saldo_capital;
			$total_saldo_bolsa += $fila["saldo_bolsa"];
			
			if ($valor_cuota_vendida)
				$total_valor_cuota_vendida += $valor_cuota_vendida;
			
			if ($valor_capital_vendido)
				$total_valor_capital_vendido += $valor_capital_vendido;
			
			$total_valor_prepago += $valor_prepago;
			$total_valor_liquidacion += $valor_liquidacion;
			$total_prepago_intereses += $prepago_intereses;
			$total_prepago_seguro += $prepago_seguro;
			$total_prepago_gastoscobranza += $prepago_gastoscobranza;
			$total_prepago_totalpagar += $prepago_totalpagar;
			
			if ($retanqueo_valor_cancelacion)
				$total_retanqueo_valor_cancelacion += $retanqueo_valor_cancelacion;
			
			$total_retanqueo_valor_liquidacion += $retanqueo_valor_liquidacion;
			$total_retanqueo_intereses += $retanqueo_intereses;
			$total_retanqueo_seguro += $retanqueo_seguro;
			$total_retanqueo_gastoscobranza += $retanqueo_gastoscobranza;
			$total_retanqueo_totalpagar += $retanqueo_totalpagar;

			$total_total_recaudado += $total_recaudado;
			
			if ($saldo_mora != "NA")
				$total_saldo_mora += $saldo_mora;
			
			if ($seguro_causado != "NA")
				$total_seguro_causado += $seguro_causado;

			
			$conFila++;
		}

		$arrayDatos[$conFila][] = 'TOTALES';

		for ($i=1; $i <= 13; $i++) { 
			$arrayDatos[$conFila][] = '';
		}

		$arrayDatos[$conFila][] = 'TOTALES';

		$arrayDatos[$conFila][] = $total_opcion_cuota;
		$arrayDatos[$conFila][] = $total_cuota_corriente;
		$arrayDatos[$conFila][] = $total_seguro;
		$arrayDatos[$conFila][] = $total_valor_credito;
		$arrayDatos[$conFila][] = $total_saldo_capital;

		if ($_SESSION["FUNC_BOLSAINCORPORACION"]) { 
			$arrayDatos[$conFila][] = $total_saldo_bolsa;
		}

		for ($i=1; $i <= 7; $i++) { 
			$arrayDatos[$conFila][] = '';
		}

		$arrayDatos[$conFila][] = $total_valor_cuota_vendida;
		$arrayDatos[$conFila][] = $total_valor_capital_vendido;

		for ($i=1; $i <= 7; $i++) { 
			$arrayDatos[$conFila][] = '';
		}

		$arrayDatos[$conFila][] = $total_valor_prepago;
		$arrayDatos[$conFila][] = $total_valor_liquidacion;
		$arrayDatos[$conFila][] = $total_prepago_intereses;
		$arrayDatos[$conFila][] = $total_prepago_seguro;
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = $total_prepago_gastoscobranza;
		$arrayDatos[$conFila][] = $total_prepago_totalpagar;
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = $total_retanqueo_valor_cancelacion;
		$arrayDatos[$conFila][] = $total_retanqueo_valor_liquidacion;
		$arrayDatos[$conFila][] = $total_retanqueo_intereses;
		$arrayDatos[$conFila][] = $total_retanqueo_seguro;
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = $total_retanqueo_gastoscobranza;
		$arrayDatos[$conFila][] = $total_retanqueo_totalpagar;
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';


		for ($i = 0; $i < $j; $i++){
			$arrayDatos[$conFila][] = $total_valor_recaudado[$i];
		}

		$arrayDatos[$conFila][] = $total_total_recaudado;
		
		for ($i=1; $i <= 4; $i++) { 
			$arrayDatos[$conFila][] = '';
		}

		$arrayDatos[$conFila][] = $total_saldo_mora;
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = '';
		$arrayDatos[$conFila][] = $total_seguro_causado;
		$arrayDatos[$conFila][] = '';

        if(count($arrayDatos) > 0){
            $data = array('code' => 200, 'mensaje' => 'Resultado satisfactorio', 'sql' => $queryDBPrueba, 'data' => $arrayDatos);                            
        }else{
            $data = array('code' => 300, 'mensaje' => 'No Hay Datos Para mostrar');
        }
    }else{
        $data = array('code' => 404, 'mensaje' => 'No Hay Datos De Entrada');
    }
    
    echo json_encode($data);
?>