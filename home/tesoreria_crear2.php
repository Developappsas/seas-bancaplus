<?php 

include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO"))
{
	exit;
}

if ($_REQUEST["id_beneficiario"] == "-1")
{
	$beneficiario = $_REQUEST["beneficiario_otro"];
	
	$identificacion = $_REQUEST["nit_otro"];
}
else if ($_REQUEST["id_beneficiario"] == "0")
{
	$beneficiario = $_REQUEST["nombre"];
	
	$identificacion = $_REQUEST["cedula"];
}
else
{
	$queryDB = "select id_entidad, nit, nombre from entidades_desembolso where id_entidad = '".$_REQUEST["id_beneficiario"]."'";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$beneficiario = $fila1["nombre"];
	
	$identificacion = $fila1["nit"];
}

if ($_REQUEST["id_beneficiario"] == "0" || $_REQUEST["id_beneficiario"] == "-1")
{
	if ($_REQUEST["id_banco"])
		$id_banco = "'".$_REQUEST["id_banco"]."'";
	else
		$id_banco = "NULL";
	
	if ($_REQUEST["tipo_cuenta"])
		$tipo_cuenta = "'".$_REQUEST["tipo_cuenta"]."'";
	else
		$tipo_cuenta = "NULL";
	
	if ($_REQUEST["nro_cuenta"])
		$nro_cuenta = "'".utf8_encode($_REQUEST["nro_cuenta"])."'";
	else
		$nro_cuenta = "NULL";
}
else
{
	if ($_REQUEST["id_entidadcuenta"])
	{
		$queryDB = "select id_banco, tipo_cuenta, nro_cuenta from entidades_cuentas where id_entidadcuenta = '".$_REQUEST["id_entidadcuenta"]."'";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$id_banco = "'".$fila1["id_banco"]."'";
		
		$tipo_cuenta = "'".$fila1["tipo_cuenta"]."'";
		
		$nro_cuenta = "'".utf8_encode($fila1["nro_cuenta"])."'";
	}
	else
	{
		$id_banco = "NULL";
		$tipo_cuenta = "NULL";
		$nro_cuenta = "NULL";
	}
}

if ($_REQUEST["referencia"])
	$referencia = "'".utf8_encode($_REQUEST["referencia"])."'";
else
	$referencia = "NULL";

if($_REQUEST['clasificacion'] == 'DSC'){
	$query = "INSERT INTO giros (id_simulacion, id_beneficiario, beneficiario, identificacion, valor_girar, id_banco, tipo_cuenta, nro_cuenta, forma_pago, clasificacion, nro_cheque, id_cuentabancaria, fecha_giro, referencia, usuario_creacion, fecha_creacion, estado) values ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["id_beneficiario"]."', '".utf8_encode(strtoupper($beneficiario))."', '".$identificacion."', '".str_replace(",", "", $_REQUEST["valor_girar"])."', ".$id_banco.", ".$tipo_cuenta.", ".$nro_cuenta.", '".$_REQUEST["forma_pago"]."', '".$_REQUEST["clasificacion"]."', '".$_REQUEST["nro_cheque"]."', '".$_REQUEST["id_cuentabancaria"]."', getdate(), ".$referencia.", '".$_SESSION["S_LOGIN"]."', getdate(), 0)";
}else{
	$query = "INSERT INTO giros (id_simulacion, id_beneficiario, beneficiario, identificacion, valor_girar, id_banco, tipo_cuenta, nro_cuenta, forma_pago, clasificacion, referencia, usuario_creacion, fecha_creacion, estado) values ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["id_beneficiario"]."', '".utf8_encode(strtoupper($beneficiario))."', '".$identificacion."', '".str_replace(",", "", $_REQUEST["valor_girar"])."', ".$id_banco.", ".$tipo_cuenta.", ".$nro_cuenta.", '".$_REQUEST["forma_pago"]."', '".$_REQUEST["clasificacion"]."', ".$referencia.", '".$_SESSION["S_LOGIN"]."', getdate(), 1)";
}
	
if(sqlsrv_query($link, $query)){

	$mensaje = 'Giro adicionado exitosamente. \n';

	$ultima_caracterizacion = sqlsrv_query($link, "select top 1 id_transaccion, cod_transaccion from contabilidad_transacciones where id_simulacion = '".$id_simul."' AND id_origen = '1' order by id_transaccion DESC" , array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

	if(sqlsrv_num_rows($ultima_caracterizacion) > 0){

		$filaUltCaract = sqlsrv_fetch_array($ultima_caracterizacion);

		sqlsrv_query($link, "BEGIN TRANSACTION");

		$query_simulacion = sqlsrv_query($link, "SELECT * FROM simulaciones a WHERE a.id_simulacion = " . $id_simul);

		$datos_simul = sqlsrv_fetch_array($query_simulacion);

		sqlsrv_query($link, "insert into contabilidad_transacciones (id_origen, id_simulacion, cod_transaccion, fecha, valor, observacion, estado, usuario_creacion, fecha_creacion) values 
		('1', '".$id_simul."',
		 UPPER(".MD5($id_simul."-".date("Y-m-d H:i:s"))."), GETDATE(), '".str_replace(",", "", $datos_simul["valor_credito"])."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]."', 'PEN', '".$_SESSION["S_LOGIN"]."', GETDATE())");

		$rs4 = sqlsrv_query($link, "select MAX(id_transaccion) as m from contabilidad_transacciones");

		$fila4 = sqlsrv_fetch_array($rs4);

		$id_trans = $fila4["m"];

		sqlsrv_query($link, "COMMIT");

		sqlsrv_query($link, "update contabilidad_transacciones set cod_transaccion_previa = '".$filaUltCaract["cod_transaccion"]."' where id_transaccion = '".$id_trans."'");

		sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, id_entidad, auxiliar, debito, credito, observacion) select '".$id_trans."', id_simulacion_retanqueo, id_entidad, auxiliar, credito, debito, CONCAT('REVERSION - ', observacion) from contabilidad_transacciones_movimientos where id_transaccion = '".$filaUltCaract["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' order by id_transaccion_movimiento");

		$query_simulacion = sqlsrv_query($link, "SELECT * FROM simulaciones a WHERE a.id_simulacion = " . $id_simul);

		$datos_simul = sqlsrv_fetch_array($query_simulacion);

		$desembolso_cliente = str_replace(",", "", $datos_simul["valor_credito"]);

		sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, 
		credito, observacion) values ('".$id_trans."', '01. CARTERA LIBRANZAS (CRT)', '".str_replace(",", "", $datos_simul["valor_credito"])."', '0', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - CXC')");

		if ($datos_simul["descuento1"]){
			$intereses_anticipados = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento1"] / 100.00);

			$desembolso_cliente -= $intereses_anticipados;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '02. INTERESES ANTICIPADOS (CRT)', '0', '".$intereses_anticipados."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - INTERESES ANTICIPADOS')");
		}

		if ($datos_simul["descuento2"]){
			$asesoria_financiera = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento2"] / 100.00);

			$desembolso_cliente -= $asesoria_financiera;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '03. ASESORIA FINANCIERA (CRT)', '0', '".$asesoria_financiera."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - ASESORIA FINANCIERA')");
		}

		if ($datos_simul["descuento3"]){
			$iva = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento3"] / 100.00);

			$desembolso_cliente -= $iva;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '04. IVA ASESORIA FINANCIERA (CRT)', '0', '".$iva."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - IVA ASESORIA FINANCIERA')");
		}

		if ($datos_simul["descuento4"]){
			$gmf = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $datos_simul["descuento4"] / 100.00);

			$desembolso_cliente -= $gmf;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '05. GMF (CRT)', '0', '".$gmf."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - GMF')");
		}

		$descuentos_adicionales = sqlsrv_query($link, "select da.nombre, sd.porcentaje from simulaciones_descuentos sd INNER JOIN descuentos_adicionales da ON sd.id_descuento = da.id_descuento where sd.id_simulacion = '".$id_simul."' order by sd.id_descuento");

		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
			$descuentos_adicional = 0;

			if ($fila1["porcentaje"]){
				$descuentos_adicional = round((str_replace(",", "", $datos_simul["valor_credito"]) - str_replace(",", "", $datos_simul["retanqueo_total"])) * $fila1["porcentaje"] / 100.00);

				$desembolso_cliente -= $descuentos_adicional;

				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '06. ".$fila1["nombre"]." (CRT)', '0', '".$descuentos_adicional."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - ".$fila1["nombre"]."')");
			}
		}

		if ($datos_simul["descuento5"] AND $datos_simul["tipo_producto"] == "1"){
			$comision_venta = round(str_replace(",", "", $datos_simul["valor_credito"]) * $datos_simul["descuento5"] / 100.00);

			$desembolso_cliente -= $comision_venta;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '07. COMISION POR VENTA (CRT)', '0', '".$comision_venta."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - COMISION POR VENTA')");
		}

		if ($datos_simul["descuento6"] AND $datos_simul["tipo_producto"] == "1"){
			$comision_venta_iva = round(str_replace(",", "", $datos_simul["valor_credito"]) * $datos_simul["descuento6"] / 100.00);

			$desembolso_cliente -= $comision_venta_iva;

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '08. IVA COMISION POR VENTA (CRT)', '0', '".$comision_venta_iva."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - IVA COMISION POR VENTA')");
		}

		if ($datos_simul["descuento_transferencia"]){
			$desembolso_cliente -= str_replace(",", "", $datos_simul["descuento_transferencia"]);

			sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '09. TRANSFERENCIA (CRT)', '0', '".str_replace(",", "", $datos_simul["descuento_transferencia"])."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - TRANSFERENCIA')");
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

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '11. RETANQUEO - CAPITAL (CRT)', '0', '".$retanqueo_valor_liquidacion."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - CAPITAL RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
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

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '12. RETANQUEO - SEGURO (CRT)', '0', '".$seguro."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - SEGURO RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
				}

				if ($retanqueo_segurocausado && $retanqueo_valor_cancelacion){
					if ($retanqueo_segurocausado > $retanqueo_valor_cancelacion)
						$retanqueo_segurocausado = $retanqueo_valor_cancelacion;

					$desembolso_cliente -= $retanqueo_segurocausado;

					$retanqueo_valor_cancelacion -= $retanqueo_segurocausado;

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '13. RETANQUEO - SEGURO CAUSADO (CRT)', '0', '".$retanqueo_segurocausado."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - SEGURO CAUSADO RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
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

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '14. RETANQUEO - INTERESES (CRT)', '0', '".$intereses."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - INTERESES RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
				}

				if ($retanqueo_valor_cancelacion){
					$desembolso_cliente -= $retanqueo_valor_cancelacion;

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '15. RETANQUEO - GASTOS COBRANZA (CRT)', '0', '".$retanqueo_valor_cancelacion."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - GASTOS COBRANZA RETANQUEO LIBRANZA ".$datos_simul["retanqueo".$i."_libranza"]."')");
				}
			}
		}

		if ($desembolso_cliente){
			if ($desembolso_cliente > 0){
				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '16. DESEMBOLSO CLIENTE (CRT)', '0', '".$desembolso_cliente."', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - DESEMBOLSO CLIENTE')");
			}
			else{
				sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '17. AJUSTE AL PESO (CRT)', '".abs($desembolso_cliente)."', '0', 'CREDITO LIBRANZA ".$datos_simul["nro_libranza"]." - AJUSTE AL PESO')");
			}
		}
	}

	// migrar
	if($_REQUEST["clasificacion"] == 'CCA'){

		//if ($fila["id_subestado"]==14 || $fila["id_subestado"]==31 || $fila["id_subestado"]==48){

			$queryCarteraSaldada = sqlsrv_query($link, "SELECT iIF(a.valor_cartera = b.valor_giros, 'SI', 'NO') AS pagada, valor_cartera, valor_giros FROM 
			(SELECT iIF(SUM(a.valor_pagar) IS NULL, 0, SUM(a.valor_pagar)) AS valor_cartera FROM simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0) a,
			(SELECT iIF(SUM(s.valor_girar) IS NULL, 0, SUM(s.valor_girar)) AS valor_giros FROM giros s WHERE s.id_simulacion = ".$_REQUEST["id_simulacion"]." AND s.clasificacion = 'CCA') b");
			$carteraSaldada = sqlsrv_fetch_array($queryCarteraSaldada);

			if($carteraSaldada['pagada'] == 'SI'){//Esta saldada la cartera
				$mensaje .= 'La cartera esta Completa, Permanecerá en Estado 6.0 Hasta que se complete el giro\n';
			}else{
				$mensaje .= 'La cartera NO esta Completa, Permanecerá en Estado 6.0\n';
			}

			$mensaje .= 'Total Cartera: $ '.$carteraSaldada['valor_cartera'].'\n';
			$mensaje .= 'Total Giros: $ '.$carteraSaldada['valor_giros'];

			$conSubestado6 = sqlsrv_query($link, "SELECT id_subestado FROM simulaciones WHERE id_subestado = 14 AND id_simulacion = ".$_REQUEST["id_simulacion"], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			if(sqlsrv_num_rows($conSubestado6) == 0){
				if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 14 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 14, 'system1', getdate())");
				}
			}
		//}
	}else if($_REQUEST["clasificacion"] == 'DSC'){
		$conValidarGiroFinalDSC = sqlsrv_query($link, "SELECT if(b.desembolso_cliente IS NULL, 0, b.desembolso_cliente) AS desembolso_cliente, iif(sum(a.valor_girar) IS NULL, 0, SUM(a.valor_girar)) AS girado FROM simulaciones b LEFT JOIN giros a ON a.id_simulacion = b.id_simulacion AND a.clasificacion = 'DSC' WHERE b.id_simulacion = '".$_REQUEST['id_simulacion']."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

        if($conValidarGiroFinalDSC && sqlsrv_num_rows($conValidarGiroFinalDSC)>0){
            $datosValidarGiroFinalDSC = sqlsrv_fetch_array($conValidarGiroFinalDSC);

            if((intval($datosValidarGiroFinalDSC["desembolso_cliente"]) - intval($datosValidarGiroFinalDSC["girado"])) <= 100){
                if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 84 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
                    sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 84, 'system', getdate())");
                }
            }
        }
	}
}else{
	$mensaje = "No se Pudo Adicionar Giro";
}
?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'tesoreria_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>