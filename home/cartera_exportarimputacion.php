<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Reporte Imputacion de Pagos.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"  &&  $_SESSION["S_SUBTIPO"] != "COORD_VISADO")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"]){
	$sufijo = "_ext";
}

function f_diferencial($f_ext, $f_sufijo, $f_id_simulacion, $f_consecutivo, $f_cuota, $f_link) {
	if ($f_ext) {
		$queryDB = "SELECT pd.id_simulacion, pd.consecutivo, vcf.pago_fondeador as valor_cuota_vendida, pd.cuota, pd.valor, pd.valor_antes_pago, cu.valor_cuota, cu.capital, cu.interes, cu.seguro, pg.tipo_recaudo from pagos_detalle pd INNER JOIN pagos pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo LEFT JOIN cuotas cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota INNER JOIN simulaciones si ON pg.id_simulacion = si.id_simulacion LEFT JOIN vwventacartera vc ON pd.id_simulacion = vc.id_simulacion AND pd.cuota = vc.cuota LEFT JOIN ventas_cuotas_fondeador vcf ON pd.id_simulacion = vcf.id_simulacion AND pd.cuota = vcf.cuota where pd.valor > 0";
	} else {
		$queryDB = "SELECT pd.id_simulacion, pd.consecutivo, vcf.pago_fondeador as valor_cuota_vendida, pd.cuota, pd.valor, pd.valor_antes_pago, cu.valor_cuota, cu.capital, cu.interes, cu.seguro, pg.tipo_recaudo from pagos_detalle".$f_sufijo." pd INNER JOIN pagos".$f_sufijo." pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo LEFT JOIN cuotas".$f_sufijo." cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota INNER JOIN simulaciones".$f_sufijo." si ON pg.id_simulacion = si.id_simulacion LEFT JOIN vwventacartera".$f_sufijo." vc ON pd.id_simulacion = vc.id_simulacion AND pd.cuota = vc.cuota LEFT JOIN ventas_cuotas_fondeador".$f_sufijo." vcf ON pd.id_simulacion = vcf.id_simulacion AND pd.cuota = vcf.cuota where pd.valor > 0";
	}
	
	$queryDB .= " AND pd.id_simulacion = '".$f_id_simulacion."'";
	
	$queryDB .= " order by pd.consecutivo, pd.cuota";

	//echo $queryDB;
	
	$rs_f = sqlsrv_query($f_link,$queryDB);
	
	while ($fila_f = sqlsrv_fetch_array($rs_f)) {
		$seguro_ya_aplicado = 0;
		$interes_ya_aplicado = 0;
		$capital_ya_aplicado = 0;
		$valor_recaudo = $fila_f["valor"];
		
		//Valor aplicado en recaudo anterior
		$valor_ya_aplicado = $fila_f["valor_cuota"] - $fila_f["valor_antes_pago"];
		
		if ($valor_ya_aplicado > 0)
		{
			if ($valor_ya_aplicado <= $fila_f["seguro"])
				$seguro_ya_aplicado = $valor_ya_aplicado;
			else
				$seguro_ya_aplicado = $fila_f["seguro"];
			
			$valor_ya_aplicado -= $seguro_ya_aplicado;
		}
		
		if ($valor_ya_aplicado > 0)
		{
			if ($valor_ya_aplicado <= $fila_f["interes"])
				$interes_ya_aplicado = $valor_ya_aplicado;
			else
				$interes_ya_aplicado = $fila_f["interes"];
			
			$valor_ya_aplicado -= $interes_ya_aplicado;
		}
		
		if ($valor_ya_aplicado > 0)
		{
			if ($valor_ya_aplicado <= $fila_f["capital"])
				$capital_ya_aplicado = $valor_ya_aplicado;
			else
				$capital_ya_aplicado = $fila_f["capital"];
		}
		
		$seguro = $fila_f["seguro"] - $seguro_ya_aplicado;
		
		if ($valor_recaudo <= $seguro)
			$seguro = $valor_recaudo;
			
		$valor_recaudo -= $seguro;
		
		$interes = $fila_f["interes"] - $interes_ya_aplicado;
		
		if ($valor_recaudo <= $interes)
			$interes = $valor_recaudo;
			
		$valor_recaudo -= $interes;
		
		$capital = $fila_f["capital"] - $capital_ya_aplicado;
		
		if ($valor_recaudo <= $capital)
			$capital = $valor_recaudo;
		
		if (strpos($fila_f["tipo_recaudo"], "ABONOCAPITAL") !== false){
			$capital = $fila_f["valor"];
		}
		
		if ($cuota_biz != $fila_f["cuota"]) {
			$diferencial_ya_aplicado = 0;			
			$cuota_biz = $fila_f["cuota"];
		}
		
		$diferencial = $fila_f["valor"] - $seguro + $interes_ya_aplicado + $capital_ya_aplicado - $diferencial_ya_aplicado;
		
		if ($diferencial > $fila_f["valor_cuota_vendida"])
			$diferencial -= $fila_f["valor_cuota_vendida"];
		else
			$diferencial = 0;
		
		$diferencial_ya_aplicado += $diferencial;
		
		if ($f_consecutivo == $fila_f["consecutivo"] && $f_cuota == $fila_f["cuota"])
			break;
	}
	
	return $diferencial;
}

?>
<table border="0">
<tr>
	<th>Id</th>
<?php

if (!$_REQUEST["id_simulacion"])
{

?>
	<th>C&eacute;dula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota</th>
	<th>Vr Cr&eacute;dito</th>
	<th>Pagadur&iacute;a</th>
	<th>Plazo</th>
	<th>Comprador</th>
	<th>Cuota Comprador</th>
<?php

}

?>
	<th>Cuota</th>
	<th>F Cuota</th>
	<th>F Recaudo</th>
	<th>Valor Recaudado</th>
	<th>Seguro</th>
	<th>Inter&eacute;s</th>
	<th>Capital</th>
	<?php if (!$_REQUEST["id_simulacion"]) { ?><th>Diferencial</th><?php } ?>
	<th>Tipo Recaudo</th>
</tr>
<?php

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, pd.consecutivo, vc.comprador, vcf.pago_fondeador as valor_cuota_vendida, pd.cuota, pd.valor, pg.fecha as fecha_recaudo, cu.fecha as fecha_cuota, pg.tipo_recaudo, pd.valor_antes_pago, cu.valor_cuota, cu.capital, cu.interes, cu.seguro from pagos_detalle pd INNER JOIN pagos pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo LEFT JOIN cuotas cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota INNER JOIN simulaciones si ON pg.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN vwventacartera vc ON pd.id_simulacion = vc.id_simulacion AND pd.cuota = vc.cuota LEFT JOIN ventas_cuotas_fondeador vcf ON pd.id_simulacion = vcf.id_simulacion AND pd.cuota = vcf.cuota where pd.valor > 0";

	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
}
else
{
	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, pd.consecutivo, vc.comprador, vcf.pago_fondeador as valor_cuota_vendida, pd.cuota, pd.valor, pg.fecha as fecha_recaudo, cu.fecha as fecha_cuota, pg.tipo_recaudo, pd.valor_antes_pago, cu.valor_cuota, cu.capital, cu.interes, cu.seguro from pagos_detalle".$sufijo." pd INNER JOIN pagos".$sufijo." pg ON pd.id_simulacion = pg.id_simulacion AND pd.consecutivo = pg.consecutivo LEFT JOIN cuotas".$sufijo." cu ON pd.id_simulacion = cu.id_simulacion AND pd.cuota = cu.cuota INNER JOIN simulaciones".$sufijo." si ON pg.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN vwventacartera".$sufijo." vc ON pd.id_simulacion = vc.id_simulacion AND pd.cuota = vc.cuota LEFT JOIN ventas_cuotas_fondeador".$sufijo." vcf ON pd.id_simulacion = vcf.id_simulacion AND pd.cuota = vcf.cuota where pd.valor > 0";
}

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["id_simulacion"])
{
	$queryDB .= " AND pd.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND pg.fecha >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND pg.fecha <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if (!$_REQUEST["id_simulacion"])
{
	$queryDB .= " order by pg.fecha, pd.id_simulacion, pd.consecutivo, pd.cuota";
}
else
{
	$queryDB .= " order by pd.consecutivo, pd.cuota";
}

$rs1 = sqlsrv_query( $link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	switch($fila1["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"]; break;
		case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"]; break;
		case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"]; break;
		case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"]; break;
	}
	
	$seguro_ya_aplicado = 0;
	$interes_ya_aplicado = 0;
	$capital_ya_aplicado = 0;
	$valor_recaudo = $fila1["valor"];
	
	//Valor aplicado en recaudo anterior
	$valor_ya_aplicado = $fila1["valor_cuota"] - $fila1["valor_antes_pago"];
	
	if ($valor_ya_aplicado > 0)
	{
		if ($valor_ya_aplicado <= $fila1["seguro"])
			$seguro_ya_aplicado = $valor_ya_aplicado;
		else
			$seguro_ya_aplicado = $fila1["seguro"];
		
		$valor_ya_aplicado -= $seguro_ya_aplicado;
	}
	
	if ($valor_ya_aplicado > 0)
	{
		if ($valor_ya_aplicado <= $fila1["interes"])
			$interes_ya_aplicado = $valor_ya_aplicado;
		else
			$interes_ya_aplicado = $fila1["interes"];
		
		$valor_ya_aplicado -= $interes_ya_aplicado;
	}
	
	if ($valor_ya_aplicado > 0)
	{
		if ($valor_ya_aplicado <= $fila1["capital"])
			$capital_ya_aplicado = $valor_ya_aplicado;
		else
			$capital_ya_aplicado = $fila1["capital"];
	}
	
	$seguro = $fila1["seguro"] - $seguro_ya_aplicado;
	
	if ($valor_recaudo <= $seguro)
		$seguro = $valor_recaudo;
	
	$valor_recaudo -= $seguro;
	
	$interes = $fila1["interes"] - $interes_ya_aplicado;
	
	if ($valor_recaudo <= $interes)
		$interes = $valor_recaudo;
	
	$valor_recaudo -= $interes;
	
	$capital = $fila1["capital"] - $capital_ya_aplicado;
	
	if ($valor_recaudo <= $capital)
		$capital = $valor_recaudo;
	
	if (strpos($fila1["tipo_recaudo"], "ABONOCAPITAL") !== false)
		$capital = $fila1["valor"];
	
	if (!$_REQUEST["id_simulacion"])
	{
		$diferencial = f_diferencial($_REQUEST["ext"], $sufijo, $fila1["id_simulacion"], $fila1["consecutivo"], $fila1["cuota"], $link);
	}
	else
	{
		if ($cuota_biz != $fila1["cuota"])
		{
			$diferencial_ya_aplicado = 0;
			
			$cuota_biz = $fila1["cuota"];
		}
		
		$diferencial = $fila1["valor"] - $seguro + $interes_ya_aplicado + $capital_ya_aplicado - $diferencial_ya_aplicado;
		
		if ($diferencial > $fila1["valor_cuota_vendida"])
			$diferencial -= $fila1["valor_cuota_vendida"];
		else
			$diferencial = 0;
		
		$diferencial_ya_aplicado += $diferencial;
	}
	
	$total_valor_recaudado += $fila1["valor"];
	$total_seguro += $seguro;
	$total_interes += $interes;
	$total_capital += $capital;
	$total_diferencial += $diferencial;
	
?>
<tr>
	<td><?php echo $fila1["id_simulacion"] ?></td>
	
<?php

	if (!$_REQUEST["id_simulacion"])
	{
	
?>
	<td><?php echo $fila1["cedula"] ?></td>
	<td><?php echo utf8_decode($fila1["nombre"]) ?></td>
	<td><?php echo $fila1["nro_libranza"] ?></td>
	<td><?php echo $fila1["tasa_interes"] ?></td>
	<td><?php echo number_format($opcion_cuota, 0) ?></td>
	<td><?php echo number_format($fila1["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila1["pagaduria"]) ?></td>
	<td><?php echo $fila1["plazo"] ?></td>
	<td><?php echo utf8_decode($fila1["comprador"]) ?></td>
	<td><?php echo number_format($fila1["valor_cuota_vendida"], 0) ?></td>
<?php

	}
	
?>
	<td><?php echo $fila1["cuota"] ?></td>
	<td><?php echo $fila1["fecha_cuota"] ?></td>
	<td><?php echo $fila1["fecha_recaudo"] ?></td>
	<td><?php echo round($fila1["valor"]) ?></td>
	<td><?php echo round($seguro) ?></td>
	<td><?php echo round($interes) ?></td>
	<td><?php echo round($capital) ?></td>
	<?php if (!$_REQUEST["id_simulacion"]) { ?><td><?php echo round($diferencial) ?></td><?php } ?>
	<td><?php echo $fila1["tipo_recaudo"] ?></td>
</tr>
<?php

}
///BOLSA DE INCORPORACIONES



	$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, 
	si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, 
	si.valor_credito, si.pagaduria, si.plazo, bi.valor, bi.fecha AS fecha_recaudo, 
	'BOLSA DE INCORPORACIONES' AS tipo_recaudo 
	FROM bolsainc_pagos bi 
	LEFT JOIN cuotas cu ON bi.id_simulacion = cu.id_simulacion AND bi.valor = cu.cuota
	INNER JOIN simulaciones si ON bi.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	 WHERE bi.valor > 0 ";

	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";



if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_REQUEST["id_simulacion"])
{
	$queryDB .= " AND pd.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND bi.fecha >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND bi.fecha <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if (!$_REQUEST["id_simulacion"])
{
	$queryDB .= " order by bi.fecha, bi.id_simulacion, bi.consecutivo";
}


$rs1 = sqlsrv_query($link,$queryDB);

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
{
	switch($fila1["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"]; break;
		case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"]; break;
		case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"]; break;
		case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"]; break;
	}
	
?>
<tr>
	<td><?php echo $fila1["id_simulacion"] ?></td>
	
<?php

	if (!$_REQUEST["id_simulacion"])
	{
	
?>
	<td><?php echo $fila1["cedula"] ?></td>
	<td><?php echo utf8_decode($fila1["nombre"]) ?></td>
	<td><?php echo $fila1["nro_libranza"] ?></td>
	<td><?php echo $fila1["tasa_interes"] ?></td>
	<td><?php echo number_format($opcion_cuota, 0) ?></td>
	<td><?php echo number_format($fila1["valor_credito"], 0) ?></td>
	<td><?php echo utf8_decode($fila1["pagaduria"]) ?></td>
	<td><?php echo $fila1["plazo"] ?></td>
	<td><?php echo utf8_decode($fila1["comprador"]) ?></td>
	<td><?php echo number_format($fila1["valor_cuota_vendida"], 0) ?></td>
<?php

	}
	
?>
	<td><?php echo $fila1["cuota"] ?></td>
	<td><?php echo $fila1["fecha_cuota"] ?></td>
	<td><?php echo $fila1["fecha_recaudo"] ?></td>
	<td><?php echo round($fila1["valor"]) ?></td>
	<td></td>
	<td></td>
	<td></td>
	<?php if (!$_REQUEST["id_simulacion"]) { ?><td></td><?php } ?>
	<td><?php echo $fila1["tipo_recaudo"] ?></td>
</tr>
<?php

}

?>



<tr><td>&nbsp;</td></tr>
<tr>
<?php

	if (!$_REQUEST["id_simulacion"])
		$colspan = 14;
	else
		$colspan = 4;
		
?>
	<td colspan="<?php echo $colspan ?>"><b>TOTALES</b></td>
	<td><b><?php echo $total_valor_recaudado ?></b></td>
	<td><b><?php echo $total_seguro ?></b></td>
	<td><b><?php echo $total_interes ?></b></td>
	<td><b><?php echo $total_capital ?></b></td>
	<?php if (!$_REQUEST["id_simulacion"]) { ?><td><b><?php echo $total_diferencial ?></b></td><?php } ?>
	<td>&nbsp;</td>
</tr>
</table>
