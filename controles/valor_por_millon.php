<?php include ('../functions.php'); ?>
<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

$link = conectar();

if (date_create($_REQUEST["fecha_estudio"]) >= date_create("2021-10-01") || $_REQUEST["cod_interno_subestado"] < 50 || $_REQUEST["cod_interno_subestado"] >= 999){

	$valor_por_millon = "0";

	if(date_create($_REQUEST["fecha_estudio"]) >= date_create("2024-01-01")){

		$fecha_estudio = new DateTime($_REQUEST["fecha_estudio"]);
		$fecha_nacimiento = new DateTime($_REQUEST["fecha_nacimiento"]);
		$diff_fechas = $fecha_nacimiento->diff($fecha_estudio);
		
		$rs1 = sqlsrv_query($link, "SELECT * FROM edad_rango_seguro WHERE (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin) OR (".$diff_fechas->y." BETWEEN edad_rango_inicio AND edad_rango_fin)");

		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

		if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
			$valor_por_millon = $fila1["valor_por_millon_parcial"];
		}else{
			$valor_por_millon = $fila1["valor_por_millon"];
		}
	}

	if(date_create($_REQUEST["fecha_estudio"]) < date_create("2024-01-01") || $valor_por_millon == 0){
		$rs = sqlsrv_query($link, "SELECT valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones,valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial, valor_por_millon_seguro_colpensiones_parcial FROM unidades_negocio WHERE id_unidad = '".$_REQUEST["id_unidad_negocio"]."'");

		$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

		if ($_REQUEST["nivel_contratacion"] == "PENSIONADO"){
			if ($_REQUEST["pagaduria"] == "COLPENSIONES"){
				if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
					$valor_por_millon = $fila["valor_por_millon_seguro_colpensiones_parcial"];
				}else{
					$valor_por_millon = $fila["valor_por_millon_seguro_colpensiones"];
				}
			}else{
				if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
					$valor_por_millon = $fila["valor_por_millon_seguro_pensionados_parcial"];
				}else{
					$valor_por_millon = $fila["valor_por_millon_seguro_pensionados"];
				}
			}	
		}
		else{
			if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
				$valor_por_millon = $fila["valor_por_millon_seguro_activos_parcial"];
			}
			else{
				$valor_por_millon = $fila["valor_por_millon_seguro_activos"];
			}
		}
	}

	if(isset($_REQUEST["id_simulacion"]) && !empty($_REQUEST["id_simulacion"])){

		$rs1 = sqlsrv_query($link, "SELECT valor_por_millon_seguro_parcial, valor_por_millon_seguro_base FROM simulaciones WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."' AND id_unidad_negocio = '".$_REQUEST["id_unidad_negocio"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

		if($rs1){
			if(sqlsrv_num_rows($rs1) > 0){

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
					if($fila1["valor_por_millon_seguro_parcial"] > 0){
						$valor_por_millon = $fila1["valor_por_millon_seguro_parcial"];
					}
				}
				else{
					if($fila1["valor_por_millon_seguro_base"] > 0){
						$valor_por_millon = $fila1["valor_por_millon_seguro_base"];
					}
				}
			}
		}
	}

	if(in_array($_REQUEST["id_unidad_negocio"], $array_unidad_negocio_valor_x_millon)) {
		$rs = sqlsrv_query($link, "SELECT valor_por_millon_seguro_activos, valor_por_millon_seguro_pensionados, valor_por_millon_seguro_colpensiones,valor_por_millon_seguro_activos_parcial, valor_por_millon_seguro_pensionados_parcial, valor_por_millon_seguro_colpensiones_parcial FROM unidades_negocio WHERE id_unidad = '".$_REQUEST["id_unidad_negocio"]."'");

		$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

		if ($_REQUEST["nivel_contratacion"] == "PENSIONADO"){
			if ($_REQUEST["pagaduria"] == "COLPENSIONES"){
				if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
					$valor_por_millon = $fila["valor_por_millon_seguro_colpensiones_parcial"];
				}else{
					$valor_por_millon = $fila["valor_por_millon_seguro_colpensiones"];
				}
			}else{
				if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
					$valor_por_millon = $fila["valor_por_millon_seguro_pensionados_parcial"];
				}else{
					$valor_por_millon = $fila["valor_por_millon_seguro_pensionados"];
				}
			}	
		}
		else{
			if ($_REQUEST["seguro_parcial"]==1 && $_REQUEST["sin_seguro"]==1){
				$valor_por_millon = $fila["valor_por_millon_seguro_activos_parcial"];
			}
			else{
				$valor_por_millon = $fila["valor_por_millon_seguro_activos"];
			}
		}
	}
}
else{
	$valor_por_millon = $_REQUEST["valor_por_millon_simulacion"];
}

echo $valor_por_millon;

exit;

?>