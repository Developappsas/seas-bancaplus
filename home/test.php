<?php include ('../functions.php'); ?>
<?php

$link = conectar();

$experian_hdcacierta_parametros = '"idType":"1","idNumber":"80921228","lastName":"ORTIZ","product":"'.$experian_hdcacierta_product.'","userId":"'.$experian_userid.'","password":"'.$experian_password.'"';

$transunion_infocomercial_parametros = '"idType":"1","idNumber":"1010195804","reason":"'.$transunion_reason.'","infoCode":"'.$transunion_infocomercial_product.'","userId":"'.$transunion_userid.'","password":"'.$transunion_password.'"';

$transunion_legalcheck_parametros = '"idType":"1","idNumber":"1010195804","infoCode":"'.$transunion_legalcheck_product.'","userId":"'.$transunion_userid.'","password":"'.$transunion_password.'"';

$transunion_ubicaplus_parametros = '"idType":"1","idNumber":"1010195804","reason":"'.$transunion_reason.'","infoCode":"'.$transunion_ubicaplus_product.'","userId":"'.$transunion_userid.'","password":"'.$transunion_password.'"';

//$xmlstr = WSCentrales($experian_hdcacierta_url, $experian_hdcacierta_parametros);
$xmlstr = WSCentrales($transunion_infocomercial_url, $transunion_infocomercial_parametros);
//$xmlstr = WSCentrales($transunion_legalcheck_url, $transunion_legalcheck_parametros);
//$xmlstr = WSCentrales($transunion_ubicaplus_url, $transunion_ubicaplus_parametros);
$xmlstr = reemplazar_caracteres_WS2($xmlstr);

libxml_use_internal_errors(true);


$xmlstr = reemplazar_caracteres_WS($xmlstr);
echo $xmlstr;

exit;

$objeto_ws = simplexml_load_string(utf8_encode($xmlstr));

if ($objeto_ws === false)
{
	foreach(libxml_get_errors() as $error)
	{
		if (!$experian_hdcacierta_error)
			$experian_hdcacierta_error = "Error cargando XML: ";
		else
			$experian_hdcacierta_error .= "; ";
		
		$experian_hdcacierta_error .= $error->message;
	}
}

if ($experian_hdcacierta_error)
{
	echo $experian_hdcacierta_error;
}
else
{
	//echo $xmlstr;
	//echo $objeto_ws->Tercero->NumeroIdentificacion;
	echo $objeto_ws->Informe->NaturalNacional["nombres"];
	//echo $objeto_ws->Informe["fechaConsulta"];
}

exit;

/*header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=DiferenciasCalculo.xls");
header("Pragma: no-cache");
header("Expires: 0");

$queryDB = "select si.id_simulacion, si.cedula, si.nombre, si.pagaduria, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.sin_seguro, si.valor_credito, si.valor_por_millon_seguro, si.porcentaje_extraprima, si.tasa_interes, si.plazo, si.entidad1, si.id_entidad1, si.valor_pagar1, si.se_compra1, si.entidad2, si.id_entidad2, si.valor_pagar2, si.se_compra2, si.entidad3, si.id_entidad3, si.valor_pagar3, si.se_compra3, si.entidad4, si.id_entidad4, si.valor_pagar4, si.se_compra4, si.entidad5, si.id_entidad5, si.valor_pagar5, si.se_compra5, si.entidad6, si.id_entidad6, si.valor_pagar6, si.se_compra6, si.entidad7, si.id_entidad7, si.valor_pagar7, si.se_compra7, si.entidad8, si.id_entidad8, si.valor_pagar8, si.se_compra8, si.entidad9, si.id_entidad9, si.valor_pagar9, si.se_compra9, si.entidad10, si.id_entidad10, si.valor_pagar10, si.se_compra10, si.retanqueo_total, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.tipo_producto, si.fecha_estudio, si.fidelizacion, si.desembolso_cliente, si.descuento_transferencia, si.estado, se.nombre as subestado from simulaciones si left join subestados se on si.id_subestado = se.id_subestado where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria."))) order by si.id_simulacion DESC";

$rs = mysql_query($queryDB, $link);

echo "<html><body><table><tr><th>ID</th><th>F Estudio</th><th>Cedula</th><th>Nombre</th><th>Pagaduria</th><th>Estado</th><th>Subestado</th><th>Cuota Total</th><th>Cuota Corriente</th><th>Seguro</th><th>Tasa</th><th>Plazo</th><th>Vr x Millon</th><th>% Extraprima</th><th>Vr Credito SEAS</th><th>Vr Credito calculado</th><th>Diferencia</th><th>Vr Desembolso Simulador</th><th>Vr Desembolso Tesoreria</th><th>Diferencia</th>";

while ($fila = mysql_fetch_array($rs))
{
	$inconsistencia = 0;
	
	$compras_cartera = 0;
	
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

	if (!$fila["sin_seguro"])
		$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	else
		$seguro_vida = 0;

	$cuota_corriente = $opcion_cuota - round($seguro_vida);

	$valor_credito = $cuota_corriente * ((pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"]) - 1) / (($fila["tasa_interes"] / 100.00) * pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"])));

	for ($i = 1; $i <= 10; $i++)
	{
		if ($fila["se_compra".$i] == "SI" && ($fila["id_entidad".$i] || $fila["entidad".$i]))
		{
			$compras_cartera += $fila["valor_pagar".$i];
		}
	}

	if ($fila["opcion_credito"] == "CLI")
		$fila["retanqueo_total"] = 0;

	$intereses_anticipados = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00;

	$asesoria_financiera = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00;

	$comision_venta = 0;

	if ($fila["tipo_producto"] == "1")
	{
		if ($fila["fecha_estudio"] < "2018-01-01")
		{
			$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
		}
		else
		{
			if ($fila["fidelizacion"])
				$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
			else
				$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
		}
	}

	$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;

	$comision_venta_iva = 0;

	if ($fila["tipo_producto"] == "1")
	{
		if ($fila["fecha_estudio"] < "2018-01-01")
		{
			$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
		else
		{
			if ($fila["fidelizacion"])
				$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			else
				$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
	}

	$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;

	$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];

	if ($fila["tipo_producto"] == "1")
		if ($fila["fidelizacion"])
			$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
		else
			$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;

	$descuentos_adicionales = mysql_query("select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento", $link);

	while ($fila1 = mysql_fetch_array($descuentos_adicionales))
	{
		$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
	}

	if (abs(round($valor_credito) - $fila["valor_credito"]) > 10000)
		$inconsistencia = 1;
	
	if (abs(round($desembolso_cliente) - $fila["desembolso_cliente"]) > 10000)
		$inconsistencia = 1;
	
	if ($inconsistencia)
		echo "<tr><td>".$fila["id_simulacion"]."</td><td>".$fila["fecha_estudio"]."</td><td>".$fila["cedula"]."</td><td>".utf8_decode($fila["nombre"])."</td><td>".utf8_decode($fila["pagaduria"])."</td><td>".$fila["estado"]."</td><td>".utf8_decode($fila["subestado"])."</td><td>".round($opcion_cuota)."</td><td>".round($cuota_corriente)."</td><td>".round($seguro_vida)."</td><td>".$fila["tasa_interes"]."</td><td>".$fila["plazo"]."</td><td>".$fila["valor_por_millon_seguro"]."</td><td>".$fila["porcentaje_extraprima"]."</td><td>".$fila["valor_credito"]."</td><td>".round($valor_credito)."</td><td>".abs(round($valor_credito) - $fila["valor_credito"])."</td><td>".$fila["desembolso_cliente"]."</td><td>".round($desembolso_cliente)."</td><td>".abs(round($desembolso_cliente) - $fila["desembolso_cliente"])."</td></tr>";
}

echo "</table></body></html>";

exit;*/

?>