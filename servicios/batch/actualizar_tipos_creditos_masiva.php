<?php 
	include ('../../functions.php'); 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	$link = conectar();

	$consultarCreditos = "SELECT * FROM simulaciones WHERE decision = 'VIABLE' AND tipo_credito_id is null";
	$queryCreditos = sqlsrv_query($link,$consultarCreditos);
	$contador = 1;
	while ($resCreditos = sqlsrv_fetch_array($queryCreditos, SQLSRV_FETCH_ASSOC)) {
		$consultarComprasCarteraCredito = "SELECT * FROM simulaciones_comprascartera WHERE id_simulacion = '".$resCreditos["id_simulacion"]."' AND se_compra = 'SI'";
		$queryComprasCarteraCredito = sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
			$consultarComprasCC = "SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion = '".$resCreditos["id_simulacion"]."' AND se_compra = 'SI'";
			$queryComprasCC = sqlsrv_query($link, $consultarComprasCC);
			$resComprasCC = sqlsrv_fetch_array($queryComprasCC, SQLSRV_FETCH_ASSOC);
			if ($resComprasCC["cuota"] > 0) {
				if ($fila["retanqueo1_libranza"] == "" || $fila["retanqueo2_libranza"] == "" || $fila["retanqueo3_libranza"] == "") {
					$tipo_crediton="COMPRAS DE CARTERA";
					$tipo_credito_id = 2;	
				}else{
					$tipo_crediton = "COMPRAS CON RETANQUEO";	
					$tipo_credito_id = 3;
				}
			}else{
				if ($resComprasCC["valor_pagar"] > 0) {
					$tipo_crediton = "LIBRE CON SANEAMIENTO";	
					$tipo_credito_id = 4;
				}else{
					if ($fila["retanqueo1_libranza"] <> "" || $fila["retanqueo2_libranza"] <> "" || $fila["retanqueo3_libranza"] <> ""){
						$tipo_crediton = "LIBRE INVERSION CON RETANQUEO";	
						$tipo_credito_id = 5;
					}
				}					
			}
		}else{
			$tipo_crediton="LIBRE INVERSION";
			$tipo_credito_id=1;
		}

		$actualizarTipoCredito="UPDATE simulaciones SET tipo_credito_id = '".$tipo_credito_id."' WHERE id_simulacion = '".$resCreditos["id_simulacion"]."'";
		if (sqlsrv_query($link,$actualizarTipoCredito)){
			echo $mensaje = "Actualizacion exitosa ".$contador.". Credito: ".$resCreditos["id_simulacion"]." - TIPO CREDITO: ".$tipo_credito_id." / ".$tipo_crediton;
			echo "<br>"; 
		}else{
			echo $mensaje = "Actualizacion fallida ".$contador.". Credito: ".$resCreditos["id_simulacion"];
			echo "<br>";
		}

		$contador++;
				
	}
	
	echo "Actualizacion exitosa";
?>

