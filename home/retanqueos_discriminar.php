<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$i = 0;

$queryDB = "SELECT si.id_simulacion, si.valor_credito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.tasa_interes, si.sin_seguro, si.valor_por_millon_seguro, si.porcentaje_extraprima, si.retanqueo_valor_cancelacion, dbo.fn_total_recaudado_query(si.id_simulacion, 0, DATEADD(DAY,  -1, si2.fecha_estudio)) as total_recaudado_query, dbo.fn_cuotas_causadas(si.id_simulacion, 0, si2.fecha_estudio) as cuotas_causadas from simulaciones si INNER JOIN simulaciones si2 ON si.retanqueo_id_simulacion_cancelacion = si2.id_simulacion where si.retanqueo_libranza_cancelacion IS NOT NULL
UNION
	select si.id_simulacion, si.valor_credito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.tasa_interes, si.sin_seguro, si.valor_por_millon_seguro, si.porcentaje_extraprima, si2.retanqueo_valor as retanqueo_valor_cancelacion, dbo.fn_total_recaudado_query(si.id_simulacion, 0, DATEADD(DAY, -1, si2.fecha_estudio)) as total_recaudado_query, dbo.fn_cuotas_causadas(si.id_simulacion, 0, si2.fecha_estudio) as cuotas_causadas from simulaciones si INNER JOIN (
	select cedula, pagaduria, retanqueo1_libranza as nro_libranza, retanqueo1_valor as retanqueo_valor, fecha_estudio from simulaciones where retanqueo1_libranza <> '' and estado in ('EST') and retanqueo1_libranza IN (select nro_libranza from simulaciones where estado not in ('CAN') and valor_liquidacion is null)
UNION
	select cedula, pagaduria, retanqueo2_libranza as nro_libranza, retanqueo2_valor as retanqueo_valor, fecha_estudio from simulaciones where retanqueo2_libranza <> '' and estado in ('EST') and retanqueo2_libranza IN (
	select nro_libranza from simulaciones where estado not in ('CAN') and valor_liquidacion is null)
UNION
	select cedula, pagaduria, retanqueo3_libranza as nro_libranza, retanqueo3_valor as retanqueo_valor, fecha_estudio from simulaciones where retanqueo3_libranza <> '' and estado in ('EST') and retanqueo3_libranza IN (
	select nro_libranza from simulaciones where estado not in ('CAN') and valor_liquidacion is null)
) as si2 ON si.cedula = si2.cedula AND si.pagaduria = si2.pagaduria AND si.nro_libranza = si2.nro_libranza
order by 1";

$rs = sqlsrv_query($link, $queryDB);
if ($rs == false) {
	 if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				 echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				 echo "code: ".$error[ 'code']."<br />";
				 echo "message: ".$error[ 'message']."<br />";
			 }
		 }
	}

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
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
	
	if (!$fila["total_recaudado_query"])
		$fila["total_recaudado_query"] = 0;
	
	$cuotas_pagadas_query = number_format($fila["total_recaudado_query"] / $opcion_cuota, 2);
	
	$capital_recaudado = 0;
	
	$queryDB = "SELECT SUM(capital + abono_capital) as capital_recaudado from cuotas where id_simulacion = '".$fila["id_simulacion"]."' AND cuota <= '".floor($cuotas_pagadas_query)."' AND pagada = '1'";
	
	$rs2 = sqlsrv_query($link, $queryDB);
	
	$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
	
	if ($fila2["capital_recaudado"])
		$capital_recaudado = $fila2["capital_recaudado"];
	
	if (floor($cuotas_pagadas_query) != ceil($cuotas_pagadas_query))
	{
		$aplicado_a_ultima_cuota = $fila["total_recaudado_query"] - (floor($cuotas_pagadas_query) * $opcion_cuota);
		
		//Si capital no es mayor que cero significa que se realiz� en abono a capital y esa cuota no hace parte del plan de pagos regenerado
		$queryDB = "SELECT * from cuotas where id_simulacion = '".$fila["id_simulacion"]."' AND cuota = '".ceil($cuotas_pagadas_query)."' AND capital > 0";
		
		$rs3 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($rs3))
		{
			$fila3 = sqlsrv_fetch_array($rs3, SQLSRV_FETCH_ASSOC);
			
			if ($aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] > 0)
				$capital_recaudado += $aplicado_a_ultima_cuota - $fila3["interes"] - $fila3["seguro"] + $fila3["abono_capital"];
			else
				$capital_recaudado += $fila3["abono_capital"];
		}
	}
	
	$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
	
	if ($saldo_capital != $fila["retanqueo_valor_cancelacion"])
	{
		$intereses = $saldo_capital * $fila["tasa_interes"] / 100.00;
		
		if (!$fila["sin_seguro"])
			$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		else
			$seguro_vida = 0;

		$cuotas_causadas = $fila["cuotas_causadas"];

		$seguro_causado = 0;
		
		if ($fila["sin_seguro"])
			$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;

		if (number_format($cuotas_causadas - $cuotas_pagadas_query, 2) > 0)
			$cuotas_mora = ceil($cuotas_causadas - $cuotas_pagadas_query);
		else
			$cuotas_mora = 0;
		
		if (!$cuotas_mora)
			$total_pagar = $saldo_capital * (1 + $fila["tasa_interes"] / 100.00) + $seguro_vida + $seguro_causado;
		else
			$total_pagar = $saldo_capital + ((($saldo_capital * $fila["tasa_interes"] / 100.00) + $seguro_vida) * $cuotas_mora) + $seguro_causado;

		$gastos_cobranza = 0;
		
		if ($cuotas_mora > 2)
		{
			$gastos_cobranza = $total_pagar * 0.2;
			
			$total_pagar += $gastos_cobranza;
		}
		
		if (round($total_pagar) - $fila["retanqueo_valor_cancelacion"] == round($gastos_cobranza))
		{
			$total_pagar -= $gastos_cobranza;
			
			$gastos_cobranza = 0;
		}
	}
	else
	{
		$intereses = 0;
		$seguro_vida = 0;
		$cuotas_mora = 0;
		$seguro_causado = 0;
		$gastos_cobranza = 0;
		$total_pagar = $saldo_capital;
	}
	
	$retanqueo_valor_liquidacion = $saldo_capital;
	$retanqueo_intereses = round($intereses);
	$retanqueo_seguro = round($seguro_vida);
	$retanqueo_cuotasmora = $cuotas_mora;
	$retanqueo_segurocausado = round($seguro_causado);
	$retanqueo_gastoscobranza = round($gastos_cobranza);
	$retanqueo_totalpagar = round($total_pagar);
	
	sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = '".$retanqueo_valor_liquidacion."', retanqueo_intereses = '".$retanqueo_intereses."', retanqueo_seguro = '".$retanqueo_seguro."', retanqueo_cuotasmora = '".$retanqueo_cuotasmora."', retanqueo_segurocausado = '".$retanqueo_segurocausado."', retanqueo_gastoscobranza = '".$retanqueo_gastoscobranza."', retanqueo_totalpagar = '".$retanqueo_totalpagar."', valor_liquidacion = null, prepago_intereses = null, prepago_seguro = null, prepago_cuotasmora = null, prepago_segurocausado = null, prepago_gastoscobranza = null, prepago_totalpagar = null where id_simulacion = '".$fila["id_simulacion"]."'");
	
	$i++;


}
echo "<script>alert('Proceso realizado, ".$i." creditos actualizados');</script>";
?>
