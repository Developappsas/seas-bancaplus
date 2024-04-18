<?php include ('../../functions.php'); ?>
<?php

if (isset($_SESSION["S_LOGIN"])){
	
	$link = conectar();
	if (isset($_REQUEST["id_simulacion"])){
		$rs = sqlsrv_query($link, "SELECT * from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
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

			if (!$fila["sin_seguro"])
				$seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
			else
				$seguro = 0;

			$fecha_tmp = $fila["fecha_primera_cuota"];
			$fecha = new DateTime($fecha_tmp);
			$plazo = $fila["plazo"];
			$tasa_interes = $fila["tasa_interes"];
			$saldo = $fila["valor_credito"];
			$valor_cuota = $opcion_cuota - $seguro;

			$queryDelete = "DELETE FROM cuotas".$sufijo." WHERE id_simulacion = '".$id_simulacion."'";
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
					
					sqlsrv_query($link, "INSERT INTO cuotas".$sufijo." (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$id_simulacion."', '".$j."', '".$fecha->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($opcion_cuota)."', '".$saldo_cuota."')");

					$fecha->add(new DateInterval('P1M'));
				}

				$rs1 = sqlsrv_query($link, "SELECT id_simulacion FROM cuotas".$sufijo." WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'");
				
				if(sqlsrv_num_rows($rs1)>0){
					$data = array('code' => 200, 'mensaje' => 'Plan de Pago creado Correctamente.');
				}else{
					$data = array('code' => 500, 'mensaje' => 'Error, No se creó plan de pago.');
				}
			}else{
				$data = array('code' => 500, 'mensaje' => 'Error, Al eliminar plan de pago anterior');
			}
			$data = array('code' => 200, 'mensaje' => 'Plan de Pago creado Correctamente.');
		}else{
			$data = array('code' => 300, 'mensaje' => 'Error, No se encontraron datos de la simulación');
		}
	}else{
		$data = array('code' => 404, 'mensaje' => 'Error, No hay datos de entrada');
	}
}else{
	$data = array('code' => 500, 'mensaje' => 'Error, Sessión Expirada, Vuelva iniciar sesión.');
}

echo json_encode($data);
?>
