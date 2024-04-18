<?php 

include ('../functions.php');

include ('../function_blob_storage.php'); 

$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

?>
<?php include("top.php"); ?>
<?php

$rs = sqlsrv_query($link, "select * from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_JURIDICO")
{
	$queryDB = "SELECT consecutivo from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) order by consecutivo";

	$rs1 = sqlsrv_query($link, $queryDB);
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($_REQUEST["fecha_vencimiento".$fila1["consecutivo"]])
		$fecha_vencimiento = "'".$_REQUEST["fecha_vencimiento".$fila1["consecutivo"]]."'";
	else
	$fecha_vencimiento = "NULL";

sqlsrv_query($link, "update agenda set fecha_vencimiento = ".$fecha_vencimiento." where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila1["consecutivo"]."'");

if ($_REQUEST["pagada".$fila1["consecutivo"]] != "1")
$_REQUEST["pagada".$fila1["consecutivo"]] = "0";

if ($_REQUEST["fecha_girocc".$fila1["consecutivo"]])
$fecha_girocc = "'".$_REQUEST["fecha_girocc".$fila1["consecutivo"]]."'";
else
$fecha_girocc = "NULL";

$existe_registro = sqlsrv_query($link, "select * from tesoreria_cc where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila1["consecutivo"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!(sqlsrv_num_rows($existe_registro)))
{
	sqlsrv_query($link, "insert into tesoreria_cc (id_simulacion, consecutivo, pagada, cuota_retenida, fecha_giro, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".$fila1["consecutivo"]."', '".$_REQUEST["pagada".$fila1["consecutivo"]]."', '".str_replace(",", "", $_REQUEST["cuota_retenida".$fila1["consecutivo"]])."', ".$fecha_girocc.", '".$_SESSION["S_LOGIN"]."', GETDATE())");
} else {
	sqlsrv_query($link, "update tesoreria_cc set pagada = '".$_REQUEST["pagada".$fila1["consecutivo"]]."', cuota_retenida = '".str_replace(",", "", $_REQUEST["cuota_retenida".$fila1["consecutivo"]])."', fecha_giro = ".$fecha_girocc.", usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE() where id_simulacion = '".$_REQUEST["id_simulacion"]."' and consecutivo = '".$fila1["consecutivo"]."'");
}
}


	if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES")
	{
		if (!$_REQUEST["mes_cartera"])
		{
			$fecha_cartera = "NULL";
		}
		else
		{
			$fecha_cartera = "'".$_REQUEST["mes_cartera"]."-01'";
		}
		sqlsrv_query($link, "update simulaciones set fecha_cartera = ".$fecha_cartera." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	}
	else if($_SESSION["S_TIPO"] == "TESORERIA")
	{
		if (!$_REQUEST["mes_cartera"])
		{
			$queryFechaCarteara =sqlsrv_query($link, "SELECT id_simulacion,case when fecha_cartera is null then null else fecha_cartera end as mes_prod FROM simulaciones where id_simulacion=".$_REQUEST["id_simulacion"]);
			
			
			$resFechaCartera=sqlsrv_fetch_array($queryFechaCarteara);
			if ($resFechaCartera["mes_prod"]!=null)
			{
				$queryMesProduccionActual=sqlsrv_query($link, "SELECT * FROM parametros WHERE codigo='MPRCO'");
				$resMesProduccionActual=sqlsrv_fetch_array($queryMesProduccionActual);
				$queryValidarMesProd=sqlsrv_query($link, 'SELECT CASE WHEN FORMAT("'.$resFechaCartera["mes_prod"].'","Y-m-d")< FORMAT("'.$resMesProduccionActual["valor"].'-01","Y-m-d") THEN "SI" ELSE "NO" END AS fecha_prod');
				$resValidarMesProd=sqlsrv_fetch_array($queryValidarMesProd);
				if ($resValidarMesProd["fecha_prod"]=="SI") {
				}else{
					$fecha_cartera = "NULL";
					sqlsrv_query($link, "update simulaciones set fecha_cartera = ".$fecha_cartera." where id_simulacion = '".$_REQUEST["id_simulacion"]."'",);
				}
			}
		} else {
			$fecha_cartera = "'".$_REQUEST["mes_cartera"]."-01'";
			$queryFechaCarteara=sqlsrv_query($link, "SELECT id_simulacion,case when fecha_cartera is null then null else fecha_cartera end as mes_prod FROM simulaciones where id_simulacion=".$_REQUEST["id_simulacion"]);

			
			$resFechaCartera=sqlsrv_fetch_array($queryFechaCarteara);
			if ($resFechaCartera["mes_prod"]==null) {
				
				$queryMesProduccionActual=sqlsrv_query($link, "SELECT * FROM parametros WHERE codigo='MPRCO'");
				$resMesProduccionActual=sqlsrv_fetch_array($queryMesProduccionActual);
				$queryValidarMesProd=sqlsrv_query($link, 'SELECT CASE WHEN FORMAT("'.$_REQUEST["mes_cartera"].'-01","Y-m-d")< DATE_FORMAT("'.$resMesProduccionActual["valor"].'-01","Y-m-d") THEN "SI" ELSE "NO" END AS fecha_prod');
				$resValidarMesProd=sqlsrv_fetch_array($queryValidarMesProd);
				if ($resValidarMesProd["fecha_prod"]=="SI") {
					$mensaje.=" La fecha de produccion especificada es menor a la fecha minima parametrizada.";
				}else{
					sqlsrv_query($link, "update simulaciones set fecha_cartera = ".$fecha_cartera." where id_simulacion = '".$_REQUEST["id_simulacion"]."'",);
				}
				
			}else{
				//VALIDACION FECHA CARTERA
				$mensaje.=" Este credito ya tenia asignada Mes Prod, no puede ser cambiado.";

			}
			
		}
		
		
	}
	
	
		$cont=1;
		$fecha_giro="";
		
	if ($fila["estado_tesoreria"] != "CER") 
	{
		$queryDB = "select * from giros gi where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_giro";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		$giro_elimando = false;
		
		while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC)){

			if ($_REQUEST["chk".$fila1["id_giro"]] == "1" && !$fila1["fecha_giro"]){
				$giro_elimando = true;
				sqlsrv_query($link, "delete from giros where id_giro = '".$fila1["id_giro"]."'");
			}
			else if ($_REQUEST["fecha_giro".$fila1["id_giro"]]){
				if ($cont==1)
				{
					$fecha_giro=$_REQUEST["fecha_giro".$fila1["id_giro"]];
				}
				$cont++;
				if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA"){
					
					if (!$_REQUEST["nro_cheque".$fila1["id_giro"]])
						$nro_cheque = "NULL";
					else
						$nro_cheque = "'".$_REQUEST["nro_cheque".$fila1["id_giro"]]."'";
					
					sqlsrv_query($link, "update giros set nro_cheque = ".$nro_cheque.", id_cuentabancaria = '".$_REQUEST["id_cuentabancaria".$fila1["id_giro"]]."', fecha_giro = '".$_REQUEST["fecha_giro".$fila1["id_giro"]]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = getdate() where id_giro = '".$fila1["id_giro"]."'");
					
					$giro_realizado = 1;
				}
			}
		}

		if($giro_elimando)
		{
			$queryCarteraSaldada = sqlsrv_query($link, "SELECT if(SUM(s.valor_girar) IS NULL, 0, SUM(s.valor_girar)) AS valor_giros FROM giros s WHERE s.id_simulacion = ".$_REQUEST["id_simulacion"]." AND s.clasificacion = 'CCA'");

			$carteraSaldada = sqlsrv_fetch_array($queryCarteraSaldada);

			if($carteraSaldada['valor_giros'] > 0){
				if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 14 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 14, 'system2', GETDATE())");
				}
			}else{
				$conSubestado62 = sqlsrv_query($link, "SELECT TOP 1 id_subestado FROM simulaciones_subestados a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND id_subestado NOT IN(78,14) ORDER BY  id_simulacionsubestado DESC ");

				if(sqlsrv_num_rows($conSubestado62) > 0){
					$nuevoSubestado = sqlsrv_fetch_array($conSubestado62);
					$ultimoSubestado = 0;

					$conSubestadoUlt = sqlsrv_query($link, "SELECT top 1 id_subestado FROM simulaciones_subestados a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." ORDER BY  id_simulacionsubestado DESC");

					if($conSubestadoUlt && sqlsrv_num_rows($conSubestadoUlt) > 0){
						$datosUltSubestado = sqlsrv_fetch_array($conSubestadoUlt);
						$ultimoSubestado = $datosUltSubestado['id_subestado'];
					}

					if($nuevoSubestado['id_subestado'] != $ultimoSubestado){

						if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = ".$nuevoSubestado['id_subestado']." WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
							sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', ".$nuevoSubestado['id_subestado'].", 'system5', getdate())");
							}
						}
					}
				}
			}
		}
	
	
	if ($giro_realizado) 
	{
		sqlsrv_query($link, "update simulaciones set estado_tesoreria = 'PAR', fecha_desembolso = (SELECT MIN(fecha_giro) from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."') where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		if ($_SESSION["FUNC_BOLSAINCORPORACION"]) {
			$rs_fecha_desembolso = sqlsrv_query($link, "SELECT DAY(fecha_desembolso) as dia_desembolso from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");			
			$fila_fecha_desembolso = sqlsrv_fetch_array($rs_fecha_desembolso);			
			if ($fila_fecha_desembolso["dia_desembolso"] <= 15){
				$meses_a_sumar_para_primera_cuota = 3;
			} else{
				$meses_a_sumar_para_primera_cuota = 4;
			}
		}else {
			$meses_a_sumar_para_primera_cuota = 2;
		}
		
		sqlsrv_query($link, "UPDATE simulaciones set fecha_primera_cuota = (SELECT EOMONTH(DATEADD(MONTH,  ".$meses_a_sumar_para_primera_cuota.", MIN(fecha_giro))) from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."') where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND fecha_primera_cuota IS NULL");

		$rs_fecha_primera_cuota = sqlsrv_query($link, "SELECT fecha_primera_cuota from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota);
		
		if ($_REQUEST["fecha_primera_cuotah"] != $fila_fecha_primera_cuota["fecha_primera_cuota"]) {
			sqlsrv_query($link, "INSERT into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$fila_fecha_primera_cuota["fecha_primera_cuota"]."', 'system', GETDATE())");
		}
		
		if (!$fila["fecha_produccion"]){
			sqlsrv_query($link, "UPDATE simulaciones set fecha_produccion = DATEADD(DAY, - DAY(fecha_desembolso) - 1, fecha_desembolso) where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		}
		
		$plazo = $_REQUEST["plazo"];
		$tasa_interes = $_REQUEST["tasa_interes"];
		$saldo = str_replace(",", "", $_REQUEST["valor_credito"]);
		
		if (!$fila["sin_seguro"]){
            $seguro = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));

            $diferencia_seguro = 0;
        }else{
            if ($fila["seguro_parcial"]==1){
                $seguro = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));
            
                $seguro_total = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro_base"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));
                $diferencia_seguro=$seguro_total-$seguro;
            }else{
                $seguro = 0;
                $diferencia_seguro=0;
            }     
        }

		$valor_cuota = str_replace(",", "", $_REQUEST["opcion_cuota"]) - round($seguro);
		$valor_cuota_total = str_replace(",", "", $_REQUEST["opcion_cuota"]);
		$fecha_primera_cuota_tmp = date("Y", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-".date("m", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-01";
		//$fecha_primer_giro = date("Y", strtotime($fecha_giro))."-".date("m", strtotime($fecha_giro))."-01";
		$fecha_primera_cuota = new DateTime($fecha_primera_cuota_tmp);
		$rs1 = sqlsrv_query($link, "select * from cuotas where id_simulacion = '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		$j = 1;
		
		for ($j = 1; $j <= $plazo; $j++) {
			if (!sqlsrv_num_rows($rs1)) {
				$interes = $saldo * $tasa_interes / 100.00;
				$capital = $valor_cuota - $interes;
				$saldo -= $capital;
				if ($j == $plazo) {
					$valor_cuota += $saldo;
					$capital = $valor_cuota - $interes;
					$saldo = 0;
				}
				if ($fila["sin_seguro"]){
					//sqlsrv_query("insert into cuotas_seguro (id_simulacion, cuota, fecha, valor_cuota, saldo_cuota) values ('".$_REQUEST["id_simulacion"]."', '".$j."', '".$fecha_primer_giro->format('Y-m-t')."', '".round($seguro2)."', '".round($seguro2)."')");
					//$fecha_primer_giro->add(new DateInterval('P1M'));
				}

				//sqlsrv_query($link, "insert into cuotas (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$_REQUEST["id_simulacion"]."', '".$j."', '".$fecha_primera_cuota->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($valor_cuota_total)."', '".round($valor_cuota_total)."')");

				sqlsrv_query($link, "insert into cuotas (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota,seguro_pendiente) values ('".$_REQUEST["id_simulacion"]."', '".$j."', '".$fecha_primera_cuota->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($valor_cuota_total)."', '".round($valor_cuota_total)."','".round($diferencia_seguro)."')");
			} else {
		        sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha_primera_cuota->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cuota = '" . $j . "'");
			}
						
			$fecha_primera_cuota->add(new DateInterval('P1M'));			
		}
	}

	if ($fila["id_subestado"]==14 || $fila["id_subestado"]==31 || $fila["id_subestado"]==48)
	{
		$queryCarteraSaldada = sqlsrv_query($link, "SELECT iIF(a.valor_cartera = b.valor_giros, 'SI', 'NO') AS pagada, valor_cartera, valor_giros, cant_giros FROM 
		(SELECT iIF(SUM(a.valor_pagar) IS NULL, 0, SUM(a.valor_pagar)) AS valor_cartera FROM simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0) a,
		(SELECT iIF(SUM(s.valor_girar) IS NULL, 0, SUM(s.valor_girar)) AS valor_giros, COUNT(s.id_giro) AS cant_giros FROM giros s WHERE s.id_simulacion = ".$_REQUEST["id_simulacion"]." AND s.clasificacion = 'CCA') b");

		$carteraSaldada = sqlsrv_fetch_array($queryCarteraSaldada);

		if($carteraSaldada['pagada'] == 'SI'){//Esta saldada la cartera
			if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 78 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
				sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 78, 'system3', GETDATE())");
			}

			//Checkear compras pagadas
			$queryCompra = sqlsrv_query($link, "SELECT a.consecutivo FROM  simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0;", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			if(sqlsrv_num_rows($queryCompra)){
				while ($updPagar = sqlsrv_fetch_array($queryCompra)) {
					sqlsrv_query($link, "update tesoreria_cc SET pagada = 1 WHERE id_simulacion = ".$_REQUEST["id_simulacion"]." AND consecutivo = ".$updPagar["consecutivo"]);
				}
			}

			//Tasa Comisones
			$sqlDatosComi="SELECT id_unidad_negocio, sin_seguro, id_subestado, tasa_interes FROM simulaciones WHERE id_simulacion = ".$_REQUEST["id_simulacion"];
			$queryDatosComi=sqlsrv_query($link, $sqlDatosComi);	
			$respDatosComi = sqlsrv_fetch_array($queryDatosComi);

			$id_unidad_negocio_tasa_comision = $respDatosComi["id_unidad_negocio"];

			if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_fianti)) {
                $id_unidad_negocio_tasa_comision = 4; //Fianti
            }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_atraccion)) {
                $id_unidad_negocio_tasa_comision = 6; //Atraccion
            }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_salvamento)) {
                $id_unidad_negocio_tasa_comision = 2; //Salvamento
            }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_kredit)) {
                $id_unidad_negocio_tasa_comision = 1; //Kredit
            }		

			$sqlTasaComision="SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = ".$id_unidad_negocio_tasa_comision ." AND a.tasa = ".$respDatosComi["tasa_interes"]." AND ((FORMAT(GETDATE(), 'Y-m-d') >= a.fecha_inicio AND FORMAT(GETDATE(), 'Y-m-d') <= a.fecha_fin) OR a.vigente = 1)";

			$queryTasaComision=sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));	

			if (@sqlsrv_num_rows($queryTasaComision)>0){
				$respTasaComision = sqlsrv_fetch_array($queryTasaComision);
				$id_tasa_comision = $respTasaComision["id_tasa_comision"];
				$id_tipo_comision = $respTasaComision["id_tipo"];

				//consultarTasaComisionAnterior
				$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
				$querySimTasaCom=sqlsrv_query($link, $sqlSimTasaCom);
				$respSimTasaCom = mysql_fetch_array($querySimTasaCom);
				$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

				if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){

					sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = $id_tasa_comision, id_tipo_tasa_comision = $id_tipo_comision WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");
					sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", $id_tasa_comision, ".$_SESSION['S_IDUSUARIO'].", GETDATE())");
				}
			}else{
				//consultarTasaComisionAnterior
				$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
				$querySimTasaCom=sqlsrv_query($link, $sqlSimTasaCom);
				$respSimTasaCom = sqlsrv_fetch_array($querySimTasaCom);
				$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

				if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){
					sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = 0 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");
					sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", 0, ".$_SESSION['S_IDUSUARIO'].", GETDATE())");
				}
			}
		}else if(intval($carteraSaldada['cant_giros']) > 0){

			$conSubestado6 = sqlsrv_query($link, "SELECT id_subestado FROM simulaciones WHERE id_subestado = 14 AND id_simulacion = ".$_REQUEST["id_simulacion"]);

			if(sqlsrv_num_rows($conSubestado6) == 0){
				if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 14 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 14, 'system4', GETDATE())");
				}
			}
		}
	}
	
}

$todos_pagados = "1";
$queryDB = "select consecutivo, se_compra, id_entidad, entidad from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by consecutivo";
$rs2 = sqlsrv_query($link, $queryDB);
while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)) {
	if ($fila2["se_compra"] == "SI" && ($fila2["id_entidad"] || $fila2["entidad"])) {
		$cc_tmp = sqlsrv_query($link, "select pagada from tesoreria_cc where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila2["consecutivo"]."' AND pagada = '0'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($cc_tmp)) {
			$todos_pagados = "0";
			break;
		}
	}
}

$cierra_tesoreria = 0;
$rs1 = sqlsrv_query($link, "select SUM(valor_girar) as s from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."' and fecha_giro IS NOT NULL AND estado = 1");
$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
$giros_realizados = $fila1["s"];
$saldo_girar = str_replace(",", "", $_REQUEST["opcion_desembolso"]) - $giros_realizados;

/****************** CERRAMOS EL CREDITO *******************/
/******** USUARIO TESORERIA O ADMIN + COMPRAS PAGADAS + MES CARTERA + ESTADO TESORERIA DIFERENTE A CERRADO + DESEMB SALDADO ***************/

if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && $todos_pagados && $_REQUEST["mes_cartera"] && $fila["estado_tesoreria"] != "CER") {
	if ($saldo_girar <= 1) {
		$cierra_tesoreria = 1;	
		sqlsrv_query($link, "update simulaciones set estado_tesoreria = 'CER', estado = 'DES', fecha_desembolso = (SELECT MIN(fecha_giro) from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."'), id_subestado = NULL where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		if ($_SESSION["FUNC_BOLSAINCORPORACION"]) {
			$rs_fecha_desembolso = sqlsrv_query($link, "SELECT DAY(fecha_desembolso) as dia_desembolso from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
			$fila_fecha_desembolso = sqlsrv_fetch_array($rs_fecha_desembolso);
			if ($fila_fecha_desembolso["dia_desembolso"] <= 15){
				$meses_a_sumar_para_primera_cuota = 3;
			}else{
				$meses_a_sumar_para_primera_cuota = 4;
			}
		} else {
			$meses_a_sumar_para_primera_cuota = 2;
		}
		
		sqlsrv_query($link, "UPDATE simulaciones set fecha_primera_cuota = (
			SELECT EOMONTH(DATEADD(MONTH,".$meses_a_sumar_para_primera_cuota." MIN(fecha_giro))) from giros where id_simulacion = '".$_REQUEST["id_simulacion"]."') where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND fecha_primera_cuota IS NULL");
		$rs_fecha_primera_cuota = sqlsrv_query($link, "SELECT fecha_primera_cuota from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		$fila_fecha_primera_cuota = sqlsrv_fetch_array($rs_fecha_primera_cuota);

		$plazo = $_REQUEST["plazo"];
		$tasa_interes = $_REQUEST["tasa_interes"];
		$saldo = str_replace(",", "", $_REQUEST["valor_credito"]);
		
		if (!$fila["sin_seguro"]){
            $seguro = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));

            $diferencia_seguro = 0;
        }else{
            if ($fila["seguro_parcial"]==1){
                $seguro = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));
            
                $seguro_total = str_replace(",", "", $_REQUEST["valor_credito"]) / 1000000.00 * $_REQUEST["valor_por_millon_seguro_base"] * (1 + ($_REQUEST["porcentaje_extraprima"] / 100));
                $diferencia_seguro=$seguro_total-$seguro;
            }else{
                $seguro = 0;
                $diferencia_seguro=0;
            }
            
        }

		
		$valor_cuota = str_replace(",", "", $_REQUEST["opcion_cuota"]) - round($seguro);
		$valor_cuota_total = str_replace(",", "", $_REQUEST["opcion_cuota"]);
		$fecha_primera_cuota_tmp = date("Y", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-".date("m", strtotime($fila_fecha_primera_cuota["fecha_primera_cuota"]))."-01";
		$fecha_primera_cuota = new DateTime($fecha_primera_cuota_tmp);		
		$rs1 = sqlsrv_query($link, "select * from cuotas where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		$j = 1;
		
		for ($j = 1; $j <= $plazo; $j++) {
			if (!sqlsrv_num_rows($rs1)) {
				$interes = $saldo * $tasa_interes / 100.00;
				$capital = $valor_cuota - $interes;
				$saldo -= $capital;
				if ($j == $plazo) {
					$valor_cuota += $saldo;
					$capital = $valor_cuota - $interes;
					$saldo = 0;
				}
				
				sqlsrv_query($link, "insert into cuotas (id_simulacion, cuota, fecha, capital, interes, seguro, valor_cuota, saldo_cuota) values ('".$_REQUEST["id_simulacion"]."', '".$j."', '".$fecha_primera_cuota->format('Y-m-t')."', '".round($capital)."', '".round($interes)."', '".round($seguro)."', '".round($valor_cuota_total)."', '".round($valor_cuota_total)."')");
			} else {
		        sqlsrv_query($link, "update cuotas SET fecha = '" . $fecha_primera_cuota->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cuota = '" . $j . "'");
			}
			
			$fecha_primera_cuota->add(new DateInterval('P1M'));
		}
		
		for ($i = 1; $i <= 3; $i++) {
			if ($fila["retanqueo".$i."_libranza"] && $fila["retanqueo".$i."_valor"] != "0") {
				$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila["cedula"]."' AND nro_libranza = '".$fila["retanqueo".$i."_libranza"]."' and estado = 'DES'");
				if (sqlsrv_num_rows($rs1)){
					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
					sqlsrv_query($link, "update cuotas set saldo_cuota = '0', pagada = '1' where id_simulacion = '".$fila1["id_simulacion"]."'");
					sqlsrv_query($link, "update simulaciones set estado = 'CAN', retanqueo_id_simulacion_cancelacion = '".$_REQUEST["id_simulacion"]."', retanqueo_libranza_cancelacion = '".$fila["nro_libranza"]."', retanqueo_valor_cancelacion = '".$fila["retanqueo".$i."_valor"]."' where id_simulacion = '".$fila1["id_simulacion"]."'");
				}
			}
		}
		$id_simul = $_REQUEST["id_simulacion"];

		$ultima_caracterizacion = sqlsrv_query($link, "SELECT top 1  id_transaccion, cod_transaccion from contabilidad_transacciones where id_simulacion = '".$id_simul."' AND id_origen = '1' order by id_transaccion DESC ");

		if(sqlsrv_num_rows($ultima_caracterizacion) > 0){

			$filaUltCaract = sqlsrv_fetch_array($ultima_caracterizacion);

			sqlsrv_query($link, "BEGIN TRANSACTION");

			$query_simulacion = sqlsrv_query($link, "SELECT * FROM simulaciones a WHERE a.id_simulacion = " . $id_simul);

			$datos_simul = sqlsrv_fetch_array($query_simulacion);

			sqlsrv_query($link, "insert into contabilidad_transacciones (id_origen, id_simulacion, cod_transaccion, fecha, valor, observacion, estado, usuario_creacion, fecha_creacion) values ('1', '".$id_simul."', UPPER(MD5('".$id_simul."-".date("Y-m-d H:i:s")."')), GETDATE(), '".str_replace(",", "", $datos_simul["valor_credito"])."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]."', 'PEN', '".$_SESSION["S_LOGIN"]."', GETDATE())");

			$rs4 = sqlsrv_query($link, "select MAX(id_transaccion) as m from contabilidad_transacciones");

			$fila4 = sqlsrv_fetch_array($rs4);

			$id_trans = $fila4["m"];

			sqlsrv_query($link, "COMMIT");

			sqlsrv_query($link, "update contabilidad_transacciones set cod_transaccion_previa = '".$filaUltCaract["cod_transaccion"]."' where id_transaccion = '".$id_trans."'");

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, id_entidad, auxiliar, debito, credito, observacion) select '".$id_trans."', id_simulacion_retanqueo, id_entidad, auxiliar, credito, debito, CONCAT('REVERSION - ', observacion) from contabilidad_transacciones_movimientos where id_transaccion = '".$filaUltCaract["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' order by id_transaccion_movimiento");

			$query_simulacion = sqlsrv_query($link, "SELECT * FROM simulaciones a WHERE a.id_simulacion = " . $id_simul);

			$datos_simul = sqlsrv_fetch_array($query_simulacion);

			$desembolso_cliente = str_replace(",", "", $datos_simul["valor_credito"]);

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '01. CARTERA LIBRANZAS (CRE)', '".str_replace(",", "", $datos_simul["valor_credito"])."', '0', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - CXC')");

			if ($datos_simul["descuento1"]){
				$intereses_anticipados = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento1"] / 100.00);

				$desembolso_cliente -= $intereses_anticipados;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '02. INTERESES ANTICIPADOS (CRE)', '0', '".$intereses_anticipados."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - INTERESES ANTICIPADOS')");
			}

			if ($datos_simul["descuento2"]){
				$asesoria_financiera = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento2"] / 100.00);

				$desembolso_cliente -= $asesoria_financiera;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '03. ASESORIA FINANCIERA (CRE)', '0', '".$asesoria_financiera."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - ASESORIA FINANCIERA')");
			}

			if ($datos_simul["descuento3"]){
				$iva = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento3"] / 100.00);

				$desembolso_cliente -= $iva;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '04. IVA ASESORIA FINANCIERA (CRE)', '0', '".$iva."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - IVA ASESORIA FINANCIERA')");
			}

			if ($datos_simul["descuento4"]){
				$gmf = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento4"] / 100.00);

				$desembolso_cliente -= $gmf;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '05. GMF (CRE)', '0', '".$gmf."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - GMF')");
			}

			$descuentos_adicionales = sqlsrv_query($link, "select da.nombre, sd.porcentaje from simulaciones_descuentos sd INNER JOIN descuentos_adicionales da ON sd.id_descuento = da.id_descuento where sd.id_simulacion = '".$id_simul."' order by sd.id_descuento");

			while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
				$descuentos_adicional = 0;

				if ($fila1["porcentaje"]){
					$descuentos_adicional = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $fila1["porcentaje"] / 100.00);

					$desembolso_cliente -= $descuentos_adicional;

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '06. ".$fila1["nombre"]." (CRE)', '0', '".$descuentos_adicional."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - ".$fila1["nombre"]."')");
				}
			}

			if ($datos_simul["descuento5"] AND $datos_simul["tipo_producto"] == "1"){
				$comision_venta = round(str_replace(",", "", $datos_simul["valor_credito"]) * $datos_simul["descuento5"] / 100.00);

				$desembolso_cliente -= $comision_venta;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '07. COMISION POR VENTA (CRE)', '0', '".$comision_venta."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - COMISION POR VENTA')");
			}

			if ($datos_simul["descuento6"] AND $datos_simul["tipo_producto"] == "1"){
				$comision_venta_iva = round(str_replace(",", "", $datos_simul["valor_credito"]) * $datos_simul["descuento6"] / 100.00);

				$desembolso_cliente -= $comision_venta_iva;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '08. IVA COMISION POR VENTA (CRE)', '0', '".$comision_venta_iva."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - IVA COMISION POR VENTA')");
			}

			if ($datos_simul["descuento_transferencia"]){
				$desembolso_cliente -= str_replace(",", "", $datos_simul["descuento_transferencia"]);

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '09. TRANSFERENCIA (CRE)', '0', '".str_replace(",", "", $datos_simul["descuento_transferencia"])."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - TRANSFERENCIA')");
			}

			$ultimo_consecutivo_compra_cartera = 1;
			
			$queryDB = "select scc.consecutivo, scc.id_entidad, scc.entidad, scc.cuota, scc.valor_pagar, scc.se_compra, ad.nombre_grabado from simulaciones_comprascartera scc LEFT join adjuntos ad ON scc.id_adjunto = ad.id_adjunto where scc.id_simulacion = '".$_REQUEST["id_simulacion"]."' order by scc.consecutivo";
			
			$rs2 = sqlsrv_query($link, $queryDB);
			
			while ($fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC)){
				$ultimo_consecutivo_compra_cartera = $fila2["consecutivo"];

				if ($fila2["se_compra"] == "SI" && ($fila2["id_entidad"] || $fila2["entidad"])){
					$entidad_desembolso = sqlsrv_query($link, "select nombre as nombre_entidad from entidades_desembolso where id_entidad = '".$fila2["id_entidad"]."'");
					$fila4 = sqlsrv_fetch_array($entidad_desembolso);
					$nombre_entidad = $fila4["nombre_entidad"];
					$desembolso_cliente -= str_replace(",", "", $fila2["valor_pagar"]);

					$auxiliar = "10. COMPRA CARTERA (CRT)";
					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_entidad, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila2["id_entidad"]."', '".$auxiliar."', '0', '".str_replace(",", "", $fila2["valor_pagar"])."', 'CREDITO LIBRANZA ".$fila2["nro_libranza"]." - COMPRA CARTERA ".utf8_encode($nombre_entidad." ".$fila2["entidad"])."')");
				}
			}

			for ($i = 1; $i <= 3; $i++){
				if ($datos_simul["retanqueo".$i."_libranza"] && $datos_simul["retanqueo".$i."_valor"]){
					$retanqueo_valor_cancelacion = str_replace(",", "", $datos_simul["retanqueo".$i."_valor"]);

					$rs1 = sqlsrv_query($link, "select id_simulacion, retanqueo_valor_cancelacion, retanqueo_valor_liquidacion, retanqueo_intereses, retanqueo_seguro, retanqueo_cuotasmora, retanqueo_segurocausado, retanqueo_gastoscobranza from simulaciones where cedula = '".$datos_simul["cedula"]."' AND pagaduria = '".$datos_simul["pagaduria"]."' AND nro_libranza = '".$datos_simul["retanqueo".$i."_libranza"]."'");

					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

					$retanqueo_valor_liquidacion = $fila1["retanqueo_valor_liquidacion"];
					$retanqueo_intereses = $fila1["retanqueo_intereses"];
					$retanqueo_seguro = $fila1["retanqueo_seguro"];
					$retanqueo_cuotasmora = $fila1["retanqueo_cuotasmora"];
					$retanqueo_segurocausado = $fila1["retanqueo_segurocausado"];
					$retanqueo_gastoscobranza = $fila1["retanqueo_gastoscobranza"];

					if ($retanqueo_valor_liquidacion){
						if ($retanqueo_valor_liquidacion > $retanqueo_valor_cancelacion)
							$retanqueo_valor_liquidacion = $retanqueo_valor_cancelacion;

						$desembolso_cliente -= $retanqueo_valor_liquidacion;

						$retanqueo_valor_cancelacion -= $retanqueo_valor_liquidacion;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '11. RETANQUEO - CAPITAL (CRE)', '0', '".$retanqueo_valor_liquidacion."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - CAPITAL RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
					}

					if ($retanqueo_seguro && $retanqueo_valor_cancelacion){
						if (!$retanqueo_cuotasmora)
							$seguro = $retanqueo_seguro;
						else
							$seguro = $retanqueo_seguro * $retanqueo_cuotasmora;

						if ($seguro > $retanqueo_valor_cancelacion)
							$seguro = $retanqueo_valor_cancelacion;

						$desembolso_cliente -= $seguro;

						$retanqueo_valor_cancelacion -= $seguro;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '12. RETANQUEO - SEGURO (CRE)', '0', '".$seguro."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - SEGURO RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
					}

					if ($retanqueo_segurocausado && $retanqueo_valor_cancelacion){
						if ($retanqueo_segurocausado > $retanqueo_valor_cancelacion)
							$retanqueo_segurocausado = $retanqueo_valor_cancelacion;

						$desembolso_cliente -= $retanqueo_segurocausado;

						$retanqueo_valor_cancelacion -= $retanqueo_segurocausado;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '13. RETANQUEO - SEGURO CAUSADO (CRE)', '0', '".$retanqueo_segurocausado."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - SEGURO CAUSADO RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
					}

					if ($retanqueo_intereses && $retanqueo_valor_cancelacion){
						if (!$retanqueo_cuotasmora)
							$intereses = $retanqueo_intereses;
						else
							$intereses = $retanqueo_intereses * $retanqueo_cuotasmora;

						if ($intereses > $retanqueo_valor_cancelacion)
							$intereses = $retanqueo_valor_cancelacion;

						$desembolso_cliente -= $intereses;

						$retanqueo_valor_cancelacion -= $intereses;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '14. RETANQUEO - INTERESES (CRE)', '0', '".$intereses."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - INTERESES RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
					}

					if ($retanqueo_valor_cancelacion){
						$desembolso_cliente -= $retanqueo_valor_cancelacion;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '15. RETANQUEO - GASTOS COBRANZA (CRE)', '0', '".$retanqueo_valor_cancelacion."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - GASTOS COBRANZA RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
					}
				}
			}

			if ($desembolso_cliente){
				if ($desembolso_cliente > 0){
					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '16. DESEMBOLSO CLIENTE (CRE)', '0', '".$desembolso_cliente."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - DESEMBOLSO CLIENTE')");
				}
				else{
					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '17. AJUSTE AL PESO (CRE)', '".abs($desembolso_cliente)."', '0', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - AJUSTE AL PESO')");
				}
			}
		}
	}
}

$mensaje .= "Actualización exitosa";

if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "TESORERIA") && !$_REQUEST["mes_cartera"])
	$mensaje .= ". Recuerde que para poder cerrar la operación en tesorería debe establecer el Mes de Cartera";



if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CARTERA") && ($fila["id_subestado"] == $subestado_desembolso || $fila["id_subestado"] == $subestado_desembolso_cliente || $fila["estado"] == "DES"))
{
	sqlsrv_query($link, "BEGIN TRANSACTION");
	
	$rs1 = sqlsrv_query($link, "select consecutivo from tesoreria_cc where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by consecutivo");
	
	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if (strcmp($_FILES["archivo".$fila1["consecutivo"]]["name"], ""))
		{
			$uniqueID = uniqid();
			
			sqlsrv_query($link, "insert into adjuntos (id_simulacion, id_tipo, descripcion, nombre_original, nombre_grabado, privado, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$tipoadjunto_pys."', 'PAZ Y SALVO ".utf8_encode($_REQUEST["entidad".$fila1["consecutivo"]])."', '".reemplazar_caracteres_no_utf($_FILES["archivo".$fila1["consecutivo"]]["name"])."', '".$uniqueID."_".$_REQUEST["id_simulacion"]."_".reemplazar_caracteres_no_utf($_FILES["archivo".$fila1["consecutivo"]]["name"])."', '0', '".$_SESSION["S_LOGIN"]."', GETDATE())");
			
			$rs2 = sqlsrv_query($link, "select MAX(id_adjunto) as m from adjuntos");
			
			$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
			
			$id_adjun = $fila2["m"];
			
			$fechaa =new DateTime();
			$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
			
			$metadata1 = array(
				'id_simulacion' => $_REQUEST["id_simulacion"],
				'descripcion' => "PAZ Y SALVO ".reemplazar_caracteres_no_utf($_REQUEST["entidad".$fila1["consecutivo"]]),
				'usuario_creacion' => $_SESSION["S_LOGIN"],
				'fecha_creacion' => $fechaFormateada
			);
			
			upload_file($_FILES["archivo".$fila1["consecutivo"]], "simulaciones", $_REQUEST["id_simulacion"]."/adjuntos/".$uniqueID."_".$_REQUEST["id_simulacion"]."_".reemplazar_caracteres_no_utf($_FILES["archivo".$fila1["consecutivo"]]["name"]), $metadata1);
			
			sqlsrv_query($link, "update tesoreria_cc set id_adjunto = '".$id_adjun."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = getdate() where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila1["consecutivo"]."'");
		}
	}
	
	sqlsrv_query($link, "COMMIT");
}

?>
<script>

alert('<?php echo $mensaje; ?>');

<?php

if (!$sale_de_tesoreria)
{

?>
window.location = 'tesoreria_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
<?php

}
else
{

?>
window.location = 'tesoreria.php?descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
<?php

}

?>
</script>
