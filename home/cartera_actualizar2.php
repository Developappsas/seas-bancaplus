<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php
/**
 * 2016-03-22 Campos Dirección, Ciudad, Telefono, Celular y Correo actualizables DESDE LA TABLA SOLICITUD
 * 001, 002
 * 2016-03-31 Grabación de ANOTACIONES por gestion de cartera
 * 003
 */
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION")) {
    exit;
}


$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

?>
<?php include("top.php"); ?>
<?php

//ACTUALIZAR /(SECCION CREDIO)
if ($_REQUEST["action"] == "actualizar") {
	
    $rs = sqlsrv_query($link, "SELECT * from simulaciones".$sufijo." where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'");

    $fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
    $fecha_tmp = $_REQUEST["fecha_primera_cuota"];
	
    $fecha = new DateTime($fecha_tmp);


	if (!$_REQUEST["ext"])
	{
	    // 001
	    $sql = "update simulaciones set  fecha_primera_cuota = '" . $_REQUEST["fecha_primera_cuota"] . "', "
	            . "telefono = '" . $_REQUEST["telefono"] . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	    sqlsrv_query($link, $sql);

	    // 002
	
	    $sql = "update solicitud so inner join simulaciones si "
	            . "ON so.id_simulacion = si.id_simulacion "
	            . "set "
	            . "so.direccion = '" . $_REQUEST["direccion"] . "', "
	            . "so.ciudad = '" . $_REQUEST["ciudad_residencia"] . "', "
	            . "so.tel_residencia = '" . $_REQUEST["telefono"] . "', "
	            . "so.celular = '" . $_REQUEST["movil"] . "', "
	            . "so.email = '" . $_REQUEST["mail"] . "' "
	            . "where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";
	    sqlsrv_query($link, $sql);

		if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA")
		{
			if ($_REQUEST["prepagado_fondeador"] != "1")
				$_REQUEST["prepagado_fondeador"] = "0";

			sqlsrv_query($link, "update simulaciones set prepagado_fondeador = '".$_REQUEST["prepagado_fondeador"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		}

		if ($_REQUEST["fecha_primera_cuotah"] != $_REQUEST["fecha_primera_cuota"])
		{
			sqlsrv_query($link, "insert into simulaciones_primeracuota (id_simulacion, fecha_primera_cuota, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["fecha_primera_cuota"]."', '".$_SESSION["S_LOGIN"]."', getdate())");
		}
	}
	else
	{
		sqlsrv_query($link, "update simulaciones_ext set direccion = '".$_REQUEST["direccion"]."', telefono = '".$_REQUEST["telefono"]."', celular = '".$_REQUEST["movil"]."', email = '".$_REQUEST["mail"]."', ciudad = '".$_REQUEST["ciudad"]."', fecha_primera_cuota = '".$_REQUEST["fecha_primera_cuota"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	}

    for ($j = 1; $j <= $fila["plazo"]; $j++) {
        $fecha = new DateTime($fecha->format('Y-m-01'));

        sqlsrv_query($link, "update cuotas".$sufijo." SET fecha = '" . $fecha->format('Y-m-t') . "' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND cuota = '" . $j . "'");

        $fecha->add(new DateInterval('P1M'));
    }

    $mensaje = "Actualizacion exitosa";
}

//ACTUALIZAR COBRANZA (RESPONSABLE GESTION DE COBRO)
if ($_REQUEST["action"] == "actualizar_cobranza") {
  
	
	// 001
	$sql = "update simulaciones set "
			. "resp_gestion_cobranza = '" . $_REQUEST["responsable_gestion_cobro"] . "', "
			. "detalle_resp_gestion_cobranza = '" . $_REQUEST["detalle_responsable_gestion_cobro"] . "' "
			. "where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

	sqlsrv_query($link, $sql);

  

$mensaje = "Actualizacion exitosa";
}


//APLICAR PREPAGO
if ($_REQUEST["action"] == "aplicar_prepago" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA"))
{
	sqlsrv_query($link, "update cuotas".$sufijo." set saldo_cuota = '0' , pagada = '1' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

	sqlsrv_query($link, "UPDATE simulaciones".$sufijo." set estado = 'CAN', prepago_aprobado = '1', usuario_aprobprep = '".$_SESSION["S_LOGIN"]."', fecha_aprobprep = getdate(), retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

    $mensaje = "Prepago aplicado exitosamente";
}






//RESERVAR PREPAGO
if ($_REQUEST["action"] == "reversar_prepago" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA"))
{
	sqlsrv_query($link, "update simulaciones".$sufijo." set id_compradorprep = null, fecha_prepago = null, valor_prepago = null, valor_liquidacion = null, prepago_intereses = NULL, prepago_seguro = NULL, prepago_cuotasmora = NULL, prepago_segurocausado = NULL, prepago_gastoscobranza = NULL, prepago_totalpagar = NULL, usuario_creacionprep = null, fecha_creacionprep = null where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

	$archivo = sqlsrv_query($link, "select nombre_grabadoprep from simulaciones".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

	$fila1 = sqlsrv_fetch_array($archivo);

	if ($fila1["nombre_grabadoprep"])
		delete_file("simulaciones", $_REQUEST["id_simulacion"]."/varios/".$fila1["nombre_grabadoprep"]);

	sqlsrv_query($link, "update simulaciones".$sufijo." set nombre_originalprep = null, nombre_grabadoprep = null where id_simulacion = '".$_REQUEST["id_simulacion"]."'");

    $mensaje = "Prepago reversado";
}



//BORRARRECAUDO
if ($_REQUEST["action"] == "borrarrecaudo" && ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "CARTERA"))
{
    $queryDB = "SELECT pd.consecutivo, pd.cuota, pd.valor, pg.tipo_recaudo from pagos_detalle".$sufijo." pd INNER JOIN pagos".$sufijo." pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo where pd.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND pd.valor > 0 order by pd.consecutivo, pd.cuota";

    $rs = sqlsrv_query($link, $queryDB);

    while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
	{
        if ($_REQUEST["chk" . $fila["consecutivo"] . "_" . $fila["cuota"]] == "1")
		{
            sqlsrv_query($link, "update pagos_detalle".$sufijo." set valor_anulacion = valor, usuario_anulacion = '" . $_SESSION["S_LOGIN"] . "', fecha_anulacion = getdate(), valor = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and consecutivo = '".$fila["consecutivo"]."' and cuota = '" . $fila["cuota"] . "'");

			if (strpos($fila["tipo_recaudo"], "ABONOCAPITAL") === false)
			{
				$queryDB = "SELECT si.tasa_interes, si.plazo, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu.seguro FROM simulaciones".$sufijo." si INNER JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion WHERE si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cu.cuota = '" . $fila["cuota"] . "'";

				$rs1 = sqlsrv_query($link, $queryDB);
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$tasa_interes = $fila1["tasa_interes"];
				$plazo = $fila1["plazo"];
				$seguro_org = $fila1["seguro"];

				switch($fila1["opcion_credito"]) {
					case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"];
								break;
					case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"];
								break;
					case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"];
								break;
					case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"];
								break;
				}

				$valor_cuota = $opcion_cuota - $fila1["seguro"];

				if($fila["valor"] < $valor_cuota){
					sqlsrv_query($link, "update cuotas".$sufijo." set saldo_cuota = saldo_cuota + " . $fila["valor"] . ", pagada = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
				}else{
					sqlsrv_query($link, "update cuotas".$sufijo." set saldo_cuota = valor_cuota, pagada = '0' where id_simulacion = '" . $_REQUEST["id_simulacion"] . "' and cuota = '" . $fila["cuota"] . "'");
				}
			}
			else{
				$queryDB = "select si.*, cu.seguro from simulaciones".$sufijo." si INNER JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion where si.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND cu.cuota = '1'";

				$rs1 = sqlsrv_query($link, $queryDB);

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);



				$tasa_interes = $fila1["tasa_interes"];

				$plazo = $fila1["plazo"];

				$seguro_org = $fila1["seguro"];

				switch($fila1["opcion_credito"]) {
					case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"];
								break;
					case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"];
								break;
					case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"];
								break;
					case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"];
								break;
				}

				$valor_cuota = $opcion_cuota - $fila1["seguro"];

				$rs1 = sqlsrv_query($link, "SELECT valor_antes_pago, valor_anulacion from pagos_detalle".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$fila["consecutivo"]."' AND cuota = '".$fila["cuota"]."'");

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$saldo = $fila1["valor_antes_pago"];

				$valor_abono = $fila1["valor_anulacion"];

				$rs1 = sqlsrv_query($link, "SELECT MAX(cuota) as m from pagos_detalle".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo < '".$fila["consecutivo"]."' AND valor > 0");

				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

				$cuota_anterior_ajuste_plan_pagos = $fila1["m"];

				for ($i = $cuota_anterior_ajuste_plan_pagos + 1; $i <= $plazo; $i++) {
					if ($saldo > 0) {
						$interes = $saldo * $tasa_interes / 100.00;
						$capital = $valor_cuota - round($interes);
						$seguro = $seguro_org;
						$saldo -= $capital;
						if ($saldo < 0) {
							$capital += $saldo;
							$saldo = 0;
						} else {
							if ($i == $plazo) {
								$valor_cuota += $saldo;
								$capital = $valor_cuota - $interes;
								$saldo = 0;
							}
						}
						$pagada = 0;
					} else {
						$interes = 0;
						$capital = 0;
						$seguro = 0;
						$pagada = 1;
					}

					$total_cuota = round($capital) + round($interes) + round($seguro);

					$saldo_cuota = $total_cuota;

					sqlsrv_query($link, "update cuotas".$sufijo." set capital = '".round($capital)."', interes = '".round($interes)."', seguro = '".round($seguro)."', valor_cuota = '".round($total_cuota)."', saldo_cuota = '".round($saldo_cuota)."', pagada = '".$pagada."' where id_simulacion = '".$_REQUEST["id_simulacion"]."' and cuota = '".$i."'");
				}

	            sqlsrv_query($link, "update cuotas".$sufijo." set abono_capital = abono_capital - ".$fila["valor"]." where id_simulacion = '".$_REQUEST["id_simulacion"]."' and cuota = '".$cuota_anterior_ajuste_plan_pagos."'");

				$rs1 = sqlsrv_query($link, "SELECT * from cuotas".$sufijo." where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND abono_capital != '0'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				if (!sqlsrv_num_rows($rs1))
		            sqlsrv_query($link, "update cuotas".$sufijo." set capital_org = NULL, interes_org = NULL where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
			}
        }
    }

    $mensaje = "Registro eliminado";
}


?>
<script>
    alert('<?php echo $mensaje ?>');
    window.location = 'cartera_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
