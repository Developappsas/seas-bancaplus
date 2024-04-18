<?php

function ValidaValorCredito($id_simulacion, $link){

	$queryDB = "SELECT opcion_credito, opcion_cuota_cli, opcion_cuota_ccc, opcion_cuota_cmp, opcion_cuota_cso, sin_seguro, valor_credito, valor_por_millon_seguro, valor_por_millon_seguro_base, seguro_parcial, porcentaje_extraprima, tasa_interes, plazo from simulaciones where id_simulacion = '".$id_simulacion."'";
	
	$rs = sqlsrv_query($link,$queryDB);

	$fila = sqlsrv_fetch_array($rs);

	switch($fila["opcion_credito"]) {
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}

	if (!$fila["sin_seguro"]){
		$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	}
	else{
		if(!$fila["seguro_parcial"]){
			$seguro_vida = 0;
		}else{
			$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
		}
	}
	
	$cuota_corriente = $opcion_cuota - round($seguro_vida);

	if($fila["tasa_interes"] > 0){
		$valor_credito = $cuota_corriente * ((pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"]) - 1) / (($fila["tasa_interes"] / 100.00) * pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"])));
	}else{
		$valor_credito = 0;
	}

	if (abs(round($valor_credito) - $fila["valor_credito"]) > 100){
		$inconsistencia = 1;
	}
	
	return $inconsistencia;
}
	
function ValidaValorDesembolso($id_simulacion, $link) {

	$queryDB = "SELECT opcion_credito, retanqueo_total, valor_credito, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6,descuento7, descuento_transferencia, tipo_producto, fidelizacion, desembolso_cliente, descuento9_valor, descuento3_valor, descuento10_valor, servicio_nube, sin_iva_servicio_nube from simulaciones where id_simulacion = '".$id_simulacion."'";
	
	$rs = sqlsrv_query($link,$queryDB);

	$fila = sqlsrv_fetch_array($rs);

	$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$id_simulacion."'";

	$rs1 = sqlsrv_query($link,$queryDB);

	$fila1 = sqlsrv_fetch_array($rs1);

	if ($fila1["s"]) {
		$compras_cartera = $fila1["s"];
	}

	if ($fila["opcion_credito"] == "CLI") {
		$fila["retanqueo_total"] = 0;
	}

	$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] / 100.00)) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento2"] / 100.00)) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento3"] / 100.00)) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento4"] / 100.00))  - (($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento7"] / 100.00)) - $fila["descuento_transferencia"];

	if ($fila["tipo_producto"] == "1") {
		if ($fila["fidelizacion"]) {
			$desembolso_cliente = $desembolso_cliente - ($fila["retanqueo_total"] * ($fila["descuento5"] / 100.00)) - ($fila["retanqueo_total"] * ($fila["descuento6"] / 100.00));
		}
		else{
			$desembolso_cliente = $desembolso_cliente - ($fila["valor_credito"] * ($fila["descuento5"] / 100.00)) - ($fila["valor_credito"] * ($fila["descuento6"] / 100.00));
		}
	}

	$descuentos_adicionales = sqlsrv_query( $link,"SELECT * from simulaciones_descuentos where id_simulacion = '".$id_simulacion."' order by id_descuento");

	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
		$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
	}

	$aplicar_servicio_nube = $fila["servicio_nube"];

	if($aplicar_servicio_nube){
		//if($fila["descuento9_valor"] > 0){
			if($fila["sin_iva_servicio_nube"] == 1){
				$desembolso_cliente += ($fila["descuento3_valor"] - $fila["descuento10_valor"]);
			}
		//}/*else{
			/*$desembolso_cliente += ($fila["descuento3_valor"]);
			$desembolso_cliente -= $fila["descuento2_valor"] - $fila["descuento9_valor"];*/
		//}*/
	}

	if (abs(round($desembolso_cliente) - $fila["desembolso_cliente"]) > 100) {
		$inconsistencia = 1;
	}
	
	return $inconsistencia;
}
	
?>
