<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Notificacion Fiduciaria.xls");
header("Pragma: no-cache");
header("Expires: 0");
//error_reporting(E_ALL);
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}

// $queryDB = "select * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

// $venta_rs = sqlsrv_query($link, $queryDB);

// $venta = sqlsrv_fetch_array($venta_rs);

?>
<table border="0">
	<tr>
		<th>ORIGINADOR</th>
		<th>OPERACION</th>
		<th>CANT</th>
		<th>CEDULA</th>
		<th>PRIMER NOMBRE</th>
		<th>SEGUNDO NOMBRE</th>
		<th>1 APELLIDO</th>
		<th>2 APELLIDO</th>
		<th>DIRECCION</th>
		<th>TELEFONO</th>
		<th>SALARIO</th>
		<th>VR CUOTA</th>
		<th>ORIGINADOR</th>
		<th>TASA NOMINAL</th>
		<th>CUOTAS FALTANTES</th>
		<th>PAGARE</th>
		<th>PLAZO INICIAL</th>
		<th>SALDO CAPITAL</th>
		<th>MONTO INICIAL</th>
		<th>FECHA VENCIMIENTO</th>
		<th>FECHA DESESMBOLSO</th>
		<th>NIT EMISOR</th>
		<th>EMISOR</th>
		<th>TASA FONDEADOR</th>
		<th>TASA ORIGINADOR</th>
		<th>F. NACIMIENTO DEUDOR</th>
		<th>VALOR DESCUENTOS</th>
		<th>CALIFICACION DEUDOR</th>
		<th>INTERES CAUSADO</th>
		<th>CIUDAD</th>
		<th>DEPARTAMENTO</th>
		<th>SEGURO</th>
		<th>CUOTA CORRIENTE</th>
		
		<th>COMPRADOR</th>
		<th>SUBESTADO</th>
		<th>ID SIMULACION</th>
		<th>FECHA PREPAGO</th>
		<th>ESTADO CARTERA</th>
		<th>MES PRODUCCION</th>
	</tr>
	<?php

		



		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			$fecha_corte_query = "'".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}else{
			$fecha_corte_query="0";
		}

		$queryDB = "select  FORMAT(si.fecha_cartera,'Y-m') as fecha_cartera,si.fecha_nacimiento,ve.fecha as fecha_venta, vd.fecha_primer_pago, 
		DATEADD(MONTH, (vd.cuota_hasta - vd.cuota_desde), vd.fecha_primer_pago) as fecha_vcto_final,
		(vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas,se.nombre as subestado,CASE WHEN si.estado='CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END AS estado_cartera,se.nombre as subestado,co.nombre as comprador,ci.municipio, ci.departamento,si.total_ingresos,pa.pagaduria,pa.nit as nit_pagaduria,so.nombre1,so.nombre2,so.apellido1,so.apellido2,CONCAT(ase.nombre,' ',ase.apellido) as nombre_comercial, CASE WHEN si.formato_digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital_descripcion,si.puntaje_datacredito,si.retanqueo1_libranza,si.retanqueo2_libranza,si.retanqueo3_libranza,si.resp_gestion_cobranza,'ORIGINACION' as tipo, si.id_simulacion, si.cedula, si.fecha_desembolso as fecha_desembolso, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN dbo.fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE dbo.fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, FORMAT(si.fecha_cartera, 'Y-m') as mes_prod, si.fecha_primera_cuota, si.nombre, so.direccion, si.telefono, so.celular, so.email, si.nro_libranza, un.nombre as unidad_negocio, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, dbo.fn_bolsa_pagos(si.id_simulacion, ".$fecha_corte_query.") - dbo.fn_bolsa_aplicaciones(si.id_simulacion, ".$fecha_corte_query.") as saldo_bolsa, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, 0 as cobro_adicional_en_cuota, si.pagaduria, si.plazo, cu2.seguro, CASE WHEN (SELECT TOP 1 tipo_recaudo from pagos where tipo_recaudo = 'NOMINA' AND id_simulacion = si.id_simulacion) IS NOT NULL THEN 'SI' ELSE 'NO' END as incorporacion, CASE WHEN si.estado = 'CAN' THEN 'CANCELADO' ELSE 'VIGENTE' END as estado, ed.nombre as comprador_prepago, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, CASE WHEN si.prepagado_fondeador = '1' THEN 'SI' ELSE 'NO' END as prepagado_fondeador, 
		dbo.fn_fecha_primer_recaudo(si.id_simulacion, 0, ".$fecha_corte_query.") as fecha_primer_recaudo, dbo.fn_total_recaudado_query(si.id_simulacion, 0, ".$fecha_corte_query.") as total_recaudado_query, si.fecha_creacion as fecha_creacion, dbo.fn_cuotas_causadas(si.id_simulacion, 0, ".$fecha_corte_query.") as cuotas_causadas, ca.nombre as caracteristica, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x 
		FROM simulaciones si 
		INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
		INNER JOIN pagaduriaspa pa ON si.pagaduria = pa.pagaduria 
		LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' 
		LEFT JOIN entidades_desembolso ed ON ed.id_entidad = si.id_compradorprep 
		LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica 
		LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
		LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
		LEFT JOIN usuarios ase ON ase.id_usuario=si.id_comercial 
		LEFT JOIN ventas_detalle vd on vd.id_simulacion=si.id_simulacion 
		LEFT JOIN ventas ve ON vd.id_venta = ve.id_venta 
		LEFT JOIN compradores co ON ve.id_comprador=co.id_comprador 
		LEFT JOIN subestados se ON se.id_subestado=si.id_subestado
		where ve.estado='VEN' AND (si.estado IN ('DES', 'CAN') OR (si.estado IN ('EST') AND si.decision = '".$label_viable."' AND ((si.id_subestado IN (".$subestado_compras_desembolso.") AND si.estado_tesoreria = 'PAR') OR (si.id_subestado IN ('".$subestado_desembolso."', '".$subestado_desembolso_cliente."', '".$subestado_desembolso_pdte_bloqueo."',".$subestados_desembolso_nuevos_tesoreria.")))))";


		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
		$queryDB .= " AND si.fecha_cartera <= ".$fecha_corte_query."";
		}

		$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre, so.direccion, si.telefono, so.celular, si.fecha_nacimiento,so.email, si.nro_libranza, un.nombre, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.valor_por_millon_seguro, si.sin_seguro, si.porcentaje_extraprima, si.pagaduria, si.plazo, si.fecha_prepago, si.valor_prepago, si.valor_liquidacion, si.prepago_intereses, si.prepago_seguro, si.prepago_cuotasmora, si.prepago_gastoscobranza, si.prepago_totalpagar, si.retanqueo_id_simulacion_cancelacion, si.retanqueo_libranza_cancelacion, si.retanqueo_valor_cancelacion, si.retanqueo_valor_liquidacion, si.retanqueo_intereses, si.retanqueo_seguro, si.retanqueo_cuotasmora, si.retanqueo_gastoscobranza, si.retanqueo_totalpagar, cu2.seguro, pa.pagaduria, ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, se.nombre, co.nombre, ci.municipio, ci.departamento,si.total_ingresos,pa.pagaduria,pa.nit, si.estado, so.nombre1,so.nombre2,so.apellido1,so.apellido2, ase.nombre,ase.apellido, si.formato_digital, si.puntaje_datacredito,si.retanqueo1_libranza,si.retanqueo2_libranza,si.retanqueo3_libranza,si.resp_gestion_cobranza, si.fecha_primera_cuota, ed.nombre, si.prepagado_fondeador, si.fecha_creacion,ca.nombre";

		
		
		
	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		switch ($fila["opcion_credito"]) {
			case "CLI": $opcion_cuota = $fila["opcion_cuota_cli"]; break;
			case "CCC": $opcion_cuota = $fila["opcion_cuota_ccc"]; break;
			case "CMP": $opcion_cuota = $fila["opcion_cuota_cmp"]; break;
			case "CSO": $opcion_cuota = $fila["opcion_cuota_cso"]; break;
		}

		if ($fila["estado"] != "CANCELADO")
		{
			$cuotas_pagadas_query = number_format($fila["total_recaudado_query"] / $opcion_cuota, 2);
			
			if (number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2) > 0)
				$cuotas_mora = number_format($fila["cuotas_causadas"] - $cuotas_pagadas_query, 2);
			else
				$cuotas_mora = 0;
		}
		
		if ($_REQUEST["estado"] != "")
		{
			if (($_REQUEST["estado"] == "DES" && $fila["estado"] != "VIGENTE") || ($_REQUEST["estado"] == "CAN" && $fila["estado"] != "CANCELADO"))
			{
				continue;
			}
		}
		
		if ($_REQUEST["calificacion"] != "")
		{
			if (($_REQUEST["calificacion"] == "-1" && $fila["estado"] != "CANCELADO") || ($_REQUEST["calificacion"] != "-1" && (ceil($cuotas_mora) != $_REQUEST["calificacion"] || $fila["estado"] == "CANCELADO")))
			{
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
	
		$saldo_capital = 0;
		if ($fila["estado"] != "CANCELADO"){
			//Query anterior cambiado a solicitud de manuel pizarro
			$queryDB = "select SUM(capital + abono_capital) as capital_recaudado from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota <= '".floor($cuotas_pagadas_query)."' AND pagada = '1'";
			
			$rs2 = sqlsrv_query($link, $queryDB);
			
			$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
			
			if ($fila2["capital_recaudado"])
				$capital_recaudado = $fila2["capital_recaudado"];
			
			if (floor($cuotas_pagadas_query) != ceil($cuotas_pagadas_query)){
				$aplicado_a_ultima_cuota = $fila["total_recaudado_query"] - (floor($cuotas_pagadas_query) * $opcion_cuota);
				
				//Si capital no es mayor que cero significa que se realiz� en abono a capital y esa cuota no hace parte del plan de pagos regenerado
				$queryDB = "select * from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota = '".ceil($cuotas_pagadas_query)."' AND capital > 0";
				
				$rs3 = sqlsrv_query($link, $queryDB);
				
				if (sqlsrv_num_rows($rs3)){
					$fila3 = sqlsrv_fetch_array($rs3, SQLSRV_FETCH_ASSOC);
					
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

	?>
		<tr>
			<td>Kredit Plus SA</td>
			<td>Compra</td>
			<td>1</td>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["nombre1"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["nombre2"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["apellido1"])) ?></td>
			<td><?php echo strtoupper(utf8_decode($fila["apellido2"])) ?></td>
			<td><?php echo utf8_decode($fila["direccion"]) ?></td>
			<td><?php echo utf8_decode($fila["telefono"]) ?></td>
			<td><?php echo $fila["total_ingresos"] ?></td>
			<td><?php echo $opcion_cuota ?></td>
			<td>Kredit Plus SA</td>
			<td><?php echo $fila["tasa_interes"] . '%' ?></td>
			<td><?php echo $fila["cuotas_vendidas"] ?></td>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["plazo"] ?></td>
			<td><?php echo $saldo_capital ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo $fila["fecha_primer_pago"] ?></td>
			<td><?php echo $fila["fecha_desembolso"] ?></td>
			<td><?php echo $fila["nit_pagaduria"] ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td></td>
			<td><?php echo $fila["tasa_interes"] . '%' ?></td>
			<td><?php echo $fila["fecha_nacimiento"] ?></td>
			<td></td>
			<td><?php echo $fila["puntaje_datacredito"] ?></td>
			<td></td>
			<td><?php echo utf8_decode($fila["municipio"]) ?></td>
			<td><?php echo utf8_decode($fila["departamento"]) ?></td>
			<td><?php echo $fila["seguro"] ?></td>
			<td><?php echo $cuota_corriente ?></td>
			<td><?php echo $fila["comprador"] ?></td>
			<td><?php echo $fila["subestado"] ?></td>
			<td><?php echo $fila["id_simulacion"] ?></td>
			<td><?php echo $fila["fecha_prepago"] ?></td>
			<td><?php echo $fila["estado_cartera"] ?></td>
			<td><?php echo $fila["fecha_cartera"] ?></td>
			

		</tr>
	<?php

	}

	?>
</table>