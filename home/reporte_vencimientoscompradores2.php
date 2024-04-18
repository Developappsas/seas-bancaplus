<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=VencimientosCompradores.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Tipo Cartera</th>
	<th>Cedula</th>
	<th>Mes Prod</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tasa</th>
	<th>Cuota Total</th>
	<th>Vr. Credito</th>
	<th>Pagaduria</th>
	<th>Plazo</th>
	<th>F. Venta</th>
	<th>F. Primer Vcto.</th>
	<th>Cuotas Vendidas</th>
	<th>No. Venta</th>
	<th>Comprador</th>
	<th>Tipo Venta</th>
	<th>Tasa Venta</th>
	<th>ID VD</th>
	<th>No. Cuota</th>
	<th>F. Vencimiento</th>
	<th>Vr. Vencimiento</th>
	<th>Vr. Recaudado</th>
	<th>Diferencia</th>
</tr>
<?php

if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB = "SELECT 'ORIGINACION' as tipo, si.id_simulacion, si.cedula, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, ve.tasa_venta, vc.id_ventadetalle, vc.cuota, vc.fecha as fecha_vcto, vc.saldo_cuota, SUM(pd.valor) as valor_recaudado from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN ventas_detalle vd ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador INNER JOIN ventas_cuotas vc ON vd.id_ventadetalle = vc.id_ventadetalle LEFT JOIN pagos pag ON si.id_simulacion = pag.id_simulacion AND FORMAT(vc.fecha, 'yyyy-MM') = FORMAT(DATEADD(MONTH, 1, pag.fecha), 'yyyy-MM') LEFT JOIN pagos_detalle pd ON pag.id_simulacion = pd.id_simulacion AND pag.consecutivo = pd.consecutivo where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0' AND vc.saldo_cuota > 0";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	if ($_REQUEST["nro_venta"])
	{
		$queryDB .= " AND ve.nro_venta = '".$_REQUEST["nro_venta"]."'";
	}
	
	if ($_REQUEST["id_comprador"])
	{
		$queryDB .= " AND ve.id_comprador = '".$_REQUEST["id_comprador"]."'";
	}
	
	if ($_REQUEST["fechavcto_inicialbm"] && $_REQUEST["fechavcto_inicialba"])
	{
		$queryDB .= " AND FORMAT(vc.fecha, 'yyyy-MM') >= '".$_REQUEST["fechavcto_inicialba"]."-".$_REQUEST["fechavcto_inicialbm"]."'";
	}
	
	if ($_REQUEST["fechavcto_finalbm"] && $_REQUEST["fechavcto_finalba"])
	{
		$queryDB .= " AND FORMAT(vc.fecha, 'yyyy-MM') <= '".$_REQUEST["fechavcto_finalba"]."-".$_REQUEST["fechavcto_finalbm"]."'";
	}
	
	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
	{
		$queryDB .= " AND si.fecha_cartera >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."-01'";
	}
	
	if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
	{
		$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."-01'";
	}
	
	$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_cartera, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, ve.nro_venta, co.nombre, ve.modalidad_prima, ve.tasa_venta, vc.id_ventadetalle, vc.cuota, vc.fecha, vc.saldo_cuota";
}

if ($_REQUEST["tipo"] == "ALL")
{
	$queryDB .= " UNION ";
}

if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB .= "SELECT 'EXTERNA' as tipo, si.id_simulacion, si.cedula, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, 0 as opcion_cuota_cli, 0 as opcion_desembolso_cli, 0 as opcion_cuota_ccc, 0 as opcion_desembolso_ccc, 0 as opcion_cuota_cmp, 0 as opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, ve.fecha as fecha_venta, vd.fecha_primer_pago, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, ve.nro_venta, co.nombre as comprador, ve.modalidad_prima, ve.tasa_venta, vc.id_ventadetalle, vc.cuota, vc.fecha as fecha_vcto, vc.saldo_cuota, SUM(pd.valor) as valor_recaudado from simulaciones_ext si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN ventas_detalle_ext vd ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas_ext ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador INNER JOIN ventas_cuotas_ext vc ON vd.id_ventadetalle = vc.id_ventadetalle LEFT JOIN pagos_ext pag ON si.id_simulacion = pag.id_simulacion AND FORMAT(vc.fecha, 'yyyy-MM') = DATE_FORMAT(DATEADD(MONTH,  1, pag.fecha), 'yyyy-MM') LEFT JOIN pagos_detalle_ext pd ON pag.id_simulacion = pd.id_simulacion AND pag.consecutivo = pd.consecutivo where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0' AND vc.saldo_cuota > 0";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_REQUEST["nro_venta"])
	{
		$queryDB .= " AND ve.nro_venta = '".$_REQUEST["nro_venta"]."'";
	}
	
	if ($_REQUEST["id_comprador"])
	{
		$queryDB .= " AND ve.id_comprador = '".$_REQUEST["id_comprador"]."'";
	}
	
	if ($_REQUEST["fechavcto_inicialbm"] && $_REQUEST["fechavcto_inicialba"])
	{
		$queryDB .= " AND FORMAT(vc.fecha, 'yyyy-MM') >= '".$_REQUEST["fechavcto_inicialba"]."-".$_REQUEST["fechavcto_inicialbm"]."'";
	}
	
	if ($_REQUEST["fechavcto_finalbm"] && $_REQUEST["fechavcto_finalba"])
	{
		$queryDB .= " AND FORMAT(vc.fecha, 'yyyy-MM') <= '".$_REQUEST["fechavcto_finalba"]."-".$_REQUEST["fechavcto_finalbm"]."'";
	}
	
	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
	{
		$queryDB .= " AND si.fecha_cartera >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."-01'";
	}
	
	if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
	{
		$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."-01'";
	}
	
	$queryDB .= " group by si.id_simulacion, si.cedula, si.fecha_cartera, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.valor_credito, si.pagaduria, si.plazo, ve.fecha, vd.fecha_primer_pago, vd.cuota_hasta, vd.cuota_desde, ve.nro_venta, co.nombre, ve.modalidad_prima, ve.tasa_venta, vc.id_ventadetalle, vc.cuota, vc.fecha, vc.saldo_cuota";
}

$queryDB .= " order by cedula, fecha_venta, fecha_vcto";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}
	
	$tipo_venta = "";
	
	switch($fila["modalidad_prima"])
	{
		case "ANT":	$tipo_venta = "PRIMA ANTICIPADA";
					break;
		case "MDI":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN INTERESES";
					break;
		case "MDC":	$tipo_venta = "PRIMA MENSUAL DIFERENCIA EN CUOTA";
					break;
	}
	
	$diferencia = $fila["valor_recaudado"] - $fila["saldo_cuota"];
	
?>
<tr>
	<td><?php echo $fila["tipo"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo $fila["fecha_venta"] ?></td>
	<td><?php echo $fila["fecha_primer_pago"] ?></td>
	<td><?php echo $fila["cuotas_vendidas"] ?></td>
	<td><?php echo $fila["nro_venta"] ?></td>
	<td><?php echo utf8_decode($fila["comprador"]) ?></td>
	<td><?php echo $tipo_venta ?></td>
	<td><?php echo $fila["tasa_venta"] ?></td>
	<td><?php echo $fila["id_ventadetalle"] ?></td>
	<td><?php echo $fila["cuota"] ?></td>
	<td><?php echo $fila["fecha_vcto"] ?></td>
	<td><?php echo $fila["saldo_cuota"] ?></td>
	<td><?php echo $fila["valor_recaudado"] ?></td>
	<td><?php echo $diferencia ?></td>
</tr>
<?php

	$total_saldo_cuota += $fila["saldo_cuota"];
	$total_valor_recaudado += $fila["valor_recaudado"];
	$total_diferencia += $diferencia;
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="20"><b>TOTALES</b></td>
	<td><b><?php echo $total_saldo_cuota ?></b></td>
	<td><b><?php echo $total_valor_recaudado ?></b></td>
	<td><b><?php echo $total_diferencia ?></b></td>
</tr>
</table>
