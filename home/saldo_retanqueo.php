<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "TESORERIA") {
	exit;
}

$link = conectar();

if ($_REQUEST["id_simulacion"]) {
	
	$rs = sqlsrv_query($link, "SELECT cedula, pagaduria from simulaciones where id_simulacion = '".$_REQUEST["id_simulacion"]."'");	
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);	
	$_REQUEST["cedula"] = $fila["cedula"];	
	$_REQUEST["pagaduria"] = $fila["pagaduria"];
}

$es_del_cliente = sqlsrv_query($link, "SELECT * from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo_libranza"]."'");

if (!sqlsrv_num_rows($es_del_cliente)) {
	echo "mensaje=El credito no existe, no pertenece al cliente o es de una pagaduria diferente";	
	$libranza_invalida = 1;
}

$esta_desembolsada = sqlsrv_query($link, "SELECT * from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo_libranza"]."' AND estado = 'DES'");


if (!$libranza_invalida && !sqlsrv_num_rows($esta_desembolsada)){
	echo "mensaje=El credito no esta en estado Desembolsado";	
	$libranza_invalida = 1;
}

$queryDB = "SELECT * from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND (retanqueo1_libranza = '".$_REQUEST["retanqueo_libranza"]."' OR retanqueo2_libranza = '".$_REQUEST["retanqueo_libranza"]."' OR retanqueo3_libranza = '".$_REQUEST["retanqueo_libranza"]."') AND estado IN ('ING', 'EST', 'DES', 'CAN')";

if ($_REQUEST["id_simulacion"])
	$queryDB .= " AND id_simulacion != '".$_REQUEST["id_simulacion"]."'";

$esta_en_otro_retanqueo = sqlsrv_query($link,$queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if (!$libranza_invalida && sqlsrv_num_rows($esta_en_otro_retanqueo)) {
	echo "mensaje=La libranza esta en el retanqueo de otro credito";	
	$libranza_invalida = 1;
}

if (!$libranza_invalida) {
	
	$fila = sqlsrv_fetch_array($es_del_cliente);	
	if (!$fila["sin_seguro"]){
		$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	} else {
		$seguro_vida = 0;
	}
	
	$rs1 = sqlsrv_query($link,"SELECT SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN IF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."'");
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$rs1 = sqlsrv_query($link, "select SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN IF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."'");
		
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	
	$capital_recaudado = $fila1["s"];
	
	$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
	
	$intereses = $saldo_capital * $fila["tasa_interes"] / 100.00;
	

	$rs1 = sqlsrv_query($link,"SELECT COUNT(*) as c from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND fecha <= getdate()");

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c from cuotas".$sufijo." where id_simulacion = '".$fila["id_simulacion"]."' AND fecha <= getdate()");

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);


	$cuotas_causadas = $fila1["c"];

	if ($fila["sin_seguro"])
		$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;


	$rs1 = sqlsrv_query( $link,"SELECT COUNT(*) as c, SUM(saldo_cuota) as s from cuotas".$sufijo." where id_simulacion = '" . $fila["id_simulacion"] . "' AND fecha < getdate() AND pagada = '0'");
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$rs1 = sqlsrv_query($link, "SELECT COUNT(*) as c, SUM(saldo_cuota) as s from cuotas".$sufijo." where id_simulacion = '" . $fila["id_simulacion"] . "' AND fecha < GETDATE() AND pagada = '0'");
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$cuotas_mora = $fila1["c"];
	
	$total_mora = $fila1["s"];
	
	if (!$cuotas_mora)
		$total_pagar = $saldo_capital * (1 + $fila["tasa_interes"] / 100.00) + $seguro_vida + $seguro_causado;
	else
		$total_pagar = $saldo_capital + ((($saldo_capital * $fila["tasa_interes"] / 100.00) + $seguro_vida) * $cuotas_mora) + $seguro_causado;
	
	if ($cuotas_mora > 2)
	{
		$gastos_cobranza = $total_pagar * 0.2;
		
		$total_pagar += $gastos_cobranza;
	}
	
	//$retanqueo_valor = round($total_pagar) + round($total_mora);
	$retanqueo_valor = round($total_pagar);
	
	switch($fila["opcion_credito"]) {
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"]; break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"]; break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"]; break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"]; break;
	}
	
	echo "retanqueo".$_REQUEST["nro_retanqueo"]."_cuota=".number_format($opcion_cuota, 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_valor=".number_format($retanqueo_valor, 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_valor_liquidacion=".number_format($saldo_capital, 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_intereses=".number_format(round($intereses), 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_seguro=".number_format(round($seguro_vida), 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_cuotasmora=".$cuotas_mora."|retanqueo".$_REQUEST["nro_retanqueo"]."_segurocausado=".number_format(round($seguro_causado), 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_gastoscobranza=".number_format(round($gastos_cobranza), 0, ".", ",")."|retanqueo".$_REQUEST["nro_retanqueo"]."_totalpagar=".number_format(round($total_pagar), 0, ".", ",");
}

exit;

?>
