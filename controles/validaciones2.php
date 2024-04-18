<?php

include ('../functions.php');

$link = conectar_utf();
$_GET['id_simulacion'];
ValidaValorCredito($_GET['id_simulacion'], $link);
ValidaValorDesembolso($_GET['id_simulacion'], $link);



function ValidaValorCredito($id_simulacion, $link){

	$queryDB = "select opcion_credito, opcion_cuota_cli, opcion_cuota_ccc, opcion_cuota_cmp, opcion_cuota_cso, sin_seguro, valor_credito, valor_por_millon_seguro, valor_por_millon_seguro_base, seguro_parcial, porcentaje_extraprima, tasa_interes, plazo from simulaciones where id_simulacion = '".$id_simulacion."'";
	
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
		echo "Sin Seguro: seguro_vida = ".$seguro_vida." = ".$fila["valor_credito"]." / 1000000.00 * ".$fila["valor_por_millon_seguro"]." * (1 + (".$fila["porcentaje_extraprima"]." / 100))<br>";
	}
	else{

		if(!$fila["seguro_parcial"]){
			echo "Sin Parcial: seguro_vida = 0";
			$seguro_vida = 0;
		}else{
			$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
			echo "Sin Seguro: seguro_vida = ".$seguro_vida." = ".$fila["valor_credito"]." / 1000000.00 * ".$fila["valor_por_millon_seguro"]." * (1 + (".$fila["porcentaje_extraprima"]." / 100))<br>";
		}
	}

	$cuota_corriente = $opcion_cuota - round($seguro_vida);

	if($fila["tasa_interes"] > 0){

		$valor_credito = $cuota_corriente * ((pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"]) - 1) / (($fila["tasa_interes"] / 100.00) * pow(1 + ($fila["tasa_interes"] / 100.00), $fila["plazo"])));

		echo "valor_credito= ".$cuota_corriente." * ((pow(1 + (".$fila["tasa_interes"]." / 100.00), ".$fila["plazo"].") - 1) / ((".$fila["tasa_interes"]." / 100.00) * pow(1 + (".$fila["tasa_interes"]." / 100.00), ".$fila["plazo"].")))<br>";
	}else{
		echo "inconcistencia ValidaValorCredito tasa 0<br>";
		$inconsistencia = 1;
	}

	if (abs(round($valor_credito) - $fila["valor_credito"]) > 100){
		$inconsistencia = 1;

		echo "inconcistencia ValidaValorCredito <br>";
	}

	echo "valor_credito ".round($valor_credito)." <br>";

	echo "valor_creditobd ".$fila["valor_credito"]." <br>";
	
	return $inconsistencia;
}
	
function ValidaValorDesembolso($id_simulacion, $link) {

	$queryDB = "select opcion_credito, retanqueo_total, valor_credito, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6,descuento7, descuento_transferencia, tipo_producto, fidelizacion, desembolso_cliente, descuento9_valor, descuento3_valor, descuento10_valor, servicio_nube, sin_iva_servicio_nube from simulaciones where id_simulacion = '".$id_simulacion."'";
	
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

	echo $fila["valor_credito"]." - ".$fila["retanqueo_total"]." - ".$compras_cartera." - "."((".$fila["valor_credito"]." - ".$fila["retanqueo_total"].") * (".$fila["descuento1"]." / 100.00)) - ((".$fila["valor_credito"]." - ".$fila["retanqueo_total"].") * (".$fila["descuento2"]." / 100.00)) - ((".$fila["valor_credito"]." - ".$fila["retanqueo_total"].") * (".$fila["descuento3"]." / 100.00)) - ((".$fila["valor_credito"]." - ".$fila["retanqueo_total"].") * (".$fila["descuento4"]." / 100.00))  - ((".$fila["valor_credito"]." - ".$fila["retanqueo_total"].") * (".$fila["descuento7"]." / 100.00)) - ".$fila["descuento_transferencia"]." <br>";

	echo "inicio: $desembolso_cliente <br>";

	if ($fila["tipo_producto"] == "1") {
		if ($fila["fidelizacion"]) {
			$desembolso_cliente = $desembolso_cliente - ($fila["retanqueo_total"] * ($fila["descuento5"] / 100.00)) - ($fila["retanqueo_total"] * ($fila["descuento6"] / 100.00));
			echo "Si fidelizacion: $desembolso_cliente <br>";
		}
		else{
			$desembolso_cliente = $desembolso_cliente - ($fila["valor_credito"] * ($fila["descuento5"] / 100.00)) - ($fila["valor_credito"] * ($fila["descuento6"] / 100.00));
			echo "no fidelizacion: $desembolso_cliente <br>";
		}
	}

	$descuentos_adicionales = sqlsrv_query( $link,"select * from simulaciones_descuentos where id_simulacion = '".$id_simulacion."' order by id_descuento");

	echo "Antes de  Whhile: $desembolso_cliente <br>";

	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)) {
		$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
	}

	$aplicar_servicio_nube = $fila["servicio_nube"];

	echo "Antes de  SN: $desembolso_cliente <br>";

	if($aplicar_servicio_nube){
		echo "Entro a aplicar_servicio_nube <br>";
		//if($fila["descuento9_valor"] > 0){
			echo "Entro a descuento9_valor <br>";
			if($fila["sin_iva_servicio_nube"] == 1){
				$desembolso_cliente += ($fila["descuento3_valor"] - $fila["descuento10_valor"]);
				echo "Entro a sin iva <br>";
			}
		//}/*else{
			/*$desembolso_cliente += ($fila["descuento3_valor"]);
			$desembolso_cliente -= $fila["descuento2_valor"] - $fila["descuento9_valor"];
			echo " no ";*/
		//}*/
	}

	echo "Despues de  SN: ".round($desembolso_cliente)." <br>";
	echo "Despues de  SN: ".$fila["desembolso_cliente"]." <br>";

	if (abs(round($desembolso_cliente) - $fila["desembolso_cliente"]) > 100) {
		$inconsistencia = 1;
		echo "inconcistencia ValidaValorDesembolso <br>";
	}
	
	return $inconsistencia;
}
	
?>
