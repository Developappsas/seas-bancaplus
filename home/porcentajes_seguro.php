<?php

function PorcentajeSeguro($valor_por_millon, $plazo, $tasa_interes, $porcentaje_extraprima, $sin_seguro, $seguro_parcial = 0){
	
	if (!$sin_seguro || ($sin_seguro && $seguro_parcial)){
		$opcion_cuota = 100000;
		$seguro_vida = 0;
		$cuota_corriente = $opcion_cuota - $seguro_vida;
		
		$porcentaje_seguro = $seguro_vida / $opcion_cuota * 100;
		
		$diferencia = 1;
		
		$nro_iteraciones = 0;
		
		while (abs($diferencia) > 0.0000000000000001){

			$valor_credito = $cuota_corriente * ((pow(1 + ($tasa_interes / 100.00), $plazo) - 1) / (($tasa_interes / 100.00) * pow(1 + ($tasa_interes / 100.00), $plazo)));
			
			$seguro_vida = $valor_credito / 1000000.00 * $valor_por_millon * (1 + ($porcentaje_extraprima / 100));
			
			$cuota_corriente = $opcion_cuota - $seguro_vida;
			
			$porcentaje_seguro_biz = $seguro_vida / $opcion_cuota * 100;
			
			$diferencia = $porcentaje_seguro_biz - $porcentaje_seguro;
			
			$porcentaje_seguro = $porcentaje_seguro_biz;
			
			$nro_iteraciones++;
			
			if ($nro_iteraciones >= 100)
				break;
		}
	}
	else{
		$porcentaje_seguro = 0;
	}
	
	if(is_nan(round($porcentaje_seguro, 15))){
		return 0;
	}else{
		return round($porcentaje_seguro, 15);
	}
}

if (isset($_REQUEST["calculo_ajax"])){
	$seguro_parcial = 0;
	if(isset($_REQUEST["seguro_parcial"])){
		$seguro_parcial = $_REQUEST["seguro_parcial"];
	}

	echo PorcentajeSeguro($_REQUEST["valor_por_millon"], $_REQUEST["plazo"], $_REQUEST["tasa_interes"], $_REQUEST["porcentaje_extraprima"], $_REQUEST["sin_seguro"], $seguro_parcial);
}
	
?>
