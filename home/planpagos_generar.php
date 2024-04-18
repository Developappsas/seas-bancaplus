<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}
	
	
if ($_REQUEST["id_simulacion"])
{
	$rs = sqlsrv_query($link, "SELECT * from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$id_simulacion = $fila["id_simulacion"];
	
	switch($fila["opcion_credito"])
	{
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
	
	$fecha_tmp = $fila["fecha_primera_cuota"];
	
	$fecha = new DateTime($fecha_tmp);
	
	$plazo = $fila["plazo"];
	
	$tasa_interes = $fila["tasa_interes"];
	
	$saldo = $fila["valor_credito"];
	
	$seguro = $_REQUEST["seguro"];
	
	$valor_cuota = $opcion_cuota - $seguro;
}
else if ($_REQUEST["id_ventadetalle"])
{
	$rs = sqlsrv_query($link, "SELECT vd.id_ventadetalle, vd.id_simulacion, ve.modalidad_prima, ve.tasa_venta, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, si.plazo, si.valor_credito, DATEDIFF(day, vd.fecha_primer_pago, ve.fecha) as dias_primer_vcto, SUM(cu.capital) as saldo_capital from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta where vd.id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."' group by vd.id_ventadetalle, vd.id_simulacion, ve.modalidad_prima, ve.tasa_venta, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, si.plazo, si.valor_credito order by si.cedula, vd.id_ventadetalle");
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$id_ventadetalle = $fila["id_ventadetalle"];
}

?>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<th>No. Cuota</th>
	<th width="90">Fecha</th>
	<th width="90">Capital</th>
	<th width="90">Inter&eacute;s</th>
	<th width="90">Seguro de Vida</th>
	<th width="90">Cuota</th>
	<th width="90">Saldo de Capital</th>
</tr>
<tr style='background-color:#F1F1F1;'>
	<td align="center">&nbsp;</td>
	<td align="center">&nbsp;</td>
	<td align="right">&nbsp;</td>
	<td align="right">&nbsp;</td>
	<td align="right">&nbsp;</td>
	<td align="right">&nbsp;</td>
	<td align="right"><?php echo number_format($saldo, 0) ?></td>
</tr>
<?php

if ($_REQUEST["id_simulacion"])
{
	for ($j = 1; $j <= $plazo; $j++)
	{
		$fecha = new DateTime($fecha->format('Y-m-01'));
		
		$interes = $saldo * $tasa_interes / 100;
		
		$capital = $valor_cuota - $interes;
		
		$saldo -= $capital;
		
		if ($j == $plazo)
		{
			$valor_cuota += $saldo;
			
			$capital = $valor_cuota - $interes;
			
			$saldo = 0;
		}
		
		$saldo_cuota = round($opcion_cuota);
		
		if (!$_REQUEST["update"])
			sqlsrv_query($link, "insert into cuotas".$sufijo." (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$id_simulacion."', '".$j."', '".$fecha->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($opcion_cuota)."', '".$saldo_cuota."')");
		else
			sqlsrv_query($link, "update cuotas set capital = '".round($capital)."', interes = '".round($interes)."', seguro = '".round($seguro)."' where id_simulacion = '".$id_simulacion."' AND cuota = '".$j."'");
		
?>
<tr <?php echo $tr_class ?>>
	<td align="center"><?php echo $j ?></td>
	<td align="center"><?php echo $fecha->format('Y-m-t') ?></td>
	<td align="right"><?php echo number_format($capital, 0) ?></td>
	<td align="right"><?php echo number_format($interes, 0) ?></td>
	<td align="right"><?php echo number_format($_REQUEST["seguro"], 0) ?></td>
	<td align="right"><?php echo number_format($capital + $interes + $_REQUEST["seguro"], 0) ?></td>
	<td align="right"><?php echo number_format($saldo, 0) ?></td>
</tr>
<?php

		$fecha->add(new DateInterval('P1M'));
	}
}
else if ($_REQUEST["id_ventadetalle"])
{
	switch($fila["modalidad_prima"])
	{
		case "ANT":	sqlsrv_query($link, "INSERT into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) select '".$id_ventadetalle."', cuota, DATEADD(MONTH,  (cuota - '".$fila["cuota_desde"]."'), '".$fila["fecha_primer_pago"]."'), capital, interes, '0', capital + interes, capital + interes from cuotas where id_simulacion = '".$fila["id_simulacion"]."' AND cuota >= '".$fila["cuota_desde"]."' AND cuota <= '".$fila["cuota_hasta"]."' order by cuota"); break;
		
		case "MDI":	$saldo = $fila["saldo_capital"];
					
					if ($fila["cuotas_vendidas"] == $fila["plazo"])
						$saldo = $fila["valor_credito"];
					
					$tasa_interes = $fila["tasa_venta"];
					
					$queryDB = "select cuota, capital from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND cuota >= '".$fila["cuota_desde"]."' AND cuota <= '".$fila["cuota_hasta"]."' order by cuota";
					
					$rs2 = sqlsrv_query($link, $queryDB);
					
					while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC))
					{
						if ($fila2["cuota"] == $fila["cuota_desde"])
							$dias_vcto = $fila["dias_primer_vcto"];
						else
							$dias_vcto = 30;
						
						$interes = $saldo * ($tasa_interes / 100.00) * ($dias_vcto / 30.00);
						
						$valor_cuota = $fila2["capital"] + $interes;
						
						sqlsrv_query($link, "insert into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$fila["id_ventadetalle"]."', '".$fila2["cuota"]."', DATEADD(MONTH,  ('".$fila2["cuota"]."' - '".$fila["cuota_desde"]."'), '".$fila["fecha_primer_pago"]."'), '".round($fila2["capital"])."', '".round($interes)."', '0', '".round($valor_cuota)."', '".round($valor_cuota)."')");
						
						$saldo -= $fila2["capital"];
					}
					
					break;
		
		case "MDC":	$saldo = $fila["saldo_capital"];
					
					if ($fila["cuotas_vendidas"] == $fila["plazo"])
						$saldo = $fila["valor_credito"];
					
					$tasa_interes = $fila["tasa_venta"];
					
					$valor_cuota = $saldo * ($tasa_interes / 100) / (1 - pow(1 + ($tasa_interes / 100), -1 * $fila["cuotas_vendidas"]));
					
					$j = 1;
					
					for ($j = $fila["cuota_desde"]; $j <= $fila["cuota_hasta"]; $j++)
					{
						$interes = $saldo * $tasa_interes / 100.00;
						
						$capital = $valor_cuota - $interes;
						
						$saldo -= $capital;
						
						if ($j == $fila["cuota_hasta"])
						{
							$valor_cuota += $saldo;
							
							$capital = $valor_cuota - $interes;
							
							$saldo = 0;
						}
						
						sqlsrv_query($link, "insert into ventas_cuotas".$sufijo." (id_ventadetalle, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$id_ventadetalle."', '".$j."', DATEADD(MONTH,  ('".$j."' - '".$fila["cuota_desde"]."'), '".$fila["fecha_primer_pago"]."'), '".round($capital)."', '".round($interes)."', '0', '".round($valor_cuota)."', '".round($valor_cuota)."')");
					}
					
					break;
	}
}

?>
</table>
<script>alert('Plan pagos generado');</script>
