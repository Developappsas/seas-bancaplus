<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=VentaCartera.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")){
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Tipo Carteraa</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Pagaduria</th>
	<th>No. Libranza</th>
	<th>No. Venta</th>
	<th>Tipo Venta</th>
	<th>F. Anuncio</th>
	<th>F. Venta</th>
	<th>F. Corte</th>
	<th>Comprador</th>
	<th>Tasa Venta</th>
	<th>Modalidad Prima</th>
	<th>Estado Venta</th>
	<th>Tasa</th>
	<th>Cuota Total</th>
	<th>Vr. Credito</th>
	<th>Vr. Capital Vendido</th>
	<th>Plazo</th>
	<th>Estado Credito</th>
	<th>Completo</th>
	<th>F. Primer Pago</th>
	<th>Cuota Desde</th>
	<th>Cuota Hasta</th>
	<th>Recomprado</th>
	<th>Recomprado</th>
	<th>Id Simulacion</th>
	<th>F. Estudio</th>
	<th>F. Desembolso</th>
	<th>Mes Prod.</th>
	<th>F. Nacimiento</th>
	<th>Sexo</th>
	<th>Salario Basico</th>
	<th>Aportes (Salud y Pension)</th>
	<th>Subestado</th>
	<th>Pdte. Retanqueos</th>
	<th>Pdte. Compra Cartera</th>
	<th>Formato Digital</th>
	<th>Tipo de Credito</th>
	<th>Aumento Salario Minimo</th>
</tr>
<?php

if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL"){
	$queryDB = "SELECT iif(si.aumento_salario_minimo=1, 'SI', 'NO') AS aumento_salario_minimo2, iif(si.formato_digital = 1, 'SI', 'NO') AS formato_digital, si.retanqueo_total, si.fecha_estudio, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'Y-m') as mes_prod, si.fecha_nacimiento, CASE WHEN so.sexo = 'F' THEN 'FEMENINO' WHEN so.sexo = 'M' THEN 'MASCULINO' ELSE '' END sexo, si.salario_basico, si.aportes, se.nombre as nombre_subestado, si.id_simulacion, 'ORIGINACION' as tipo, ve.id_venta, ve.nro_venta, ve.tipo as tipo_venta, ve.fecha_anuncio, ve.fecha, ve.fecha_corte, co.nombre as comprador, ve.tasa_venta, ve.modalidad_prima, ve.estado, si.cedula, si.nombre as nombre_cliente, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado as estado_credito, vd.id_ventadetalle, vd.completo, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vd.recomprado, SUM(cu.capital) as valor_capital_vendido 
	from ventas ve 
	INNER JOIN ventas_detalle vd ON ve.id_venta = vd.id_venta 
	INNER JOIN compradores co ON ve.id_comprador = co.id_comprador 
	INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta 
	where ve.id_venta IS NOT NULL";
	
	if ($_SESSION["S_SECTOR"]){
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	if ($_REQUEST["id_comprador"]){
		$queryDB .= " AND ve.id_comprador = '".$_REQUEST["id_comprador"]."'";
	}
	
	if ($_REQUEST["nro_venta"]){
		$queryDB .= " AND (ve.nro_venta = '".$_REQUEST["nro_venta"]."' OR si.cedula = '".$_REQUEST["nro_venta"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["nro_venta"]))."%')";
	}
	
	if ($_REQUEST["modalidad_prima"]){
		$queryDB .= " AND ve.modalidad_prima = '".$_REQUEST["modalidad_prima"]."'";
	}
	
	if ($_REQUEST["estado"]){
		$queryDB .= " AND ve.estado = '".$_REQUEST["estado"]."'";
	}
	
	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
		$queryDB .= " AND ve.fecha >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
		$queryDB .= " AND ve.fecha <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$queryDB .= " group by ve.id_venta, ve.nro_venta, ve.tipo, ve.fecha_anuncio, ve.fecha, ve.fecha_corte, co.nombre, ve.tasa_venta, ve.modalidad_prima, ve.estado, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, vd.id_ventadetalle, vd.completo, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, vd.recomprado";
}

if ($_REQUEST["tipo"] == "ALL"){
	$queryDB .= " UNION ";
}

if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL"){
	$queryDB .= "SELECT iif(si.formato_digital = 1, 'SI', 'NO') AS formato_digital, si.retanqueo_total, si.fecha_estudio, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'Y-m') as mes_prod, si.fecha_nacimiento, CASE WHEN so.sexo = 'F' THEN 'FEMENINO' WHEN so.sexo = 'M' THEN 'MASCULINO' ELSE '' END sexo, si.salario_basico, si.aportes, se.nombre as nombre_subestado, si.id_simulacion, 'EXTERNA' as tipo, ve.id_venta, ve.nro_venta, ve.tipo as tipo_venta, ve.fecha_anuncio, ve.fecha, ve.fecha_corte, co.nombre as comprador, ve.tasa_venta, ve.modalidad_prima, ve.estado, si.cedula, si.nombre as nombre_cliente, si.nro_libranza, si.tasa_interes, si.opcion_credito, 0 as opcion_cuota_cli, 0 as opcion_cuota_ccc, 0 as opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado as estado_credito, vd.id_ventadetalle, vd.completo, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, vd.recomprado, SUM(cu.capital) as valor_capital_vendido 
	from ventas_ext ve 
	INNER JOIN ventas_detalle_ext vd ON ve.id_venta = vd.id_venta 
	INNER JOIN compradores co ON ve.id_comprador = co.id_comprador 
	INNER JOIN simulaciones_ext si ON vd.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	LEFT JOIN cuotas_ext cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta 
	where ve.id_venta IS NOT NULL";
	
	if ($_SESSION["S_SECTOR"]){
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
	
	if ($_REQUEST["id_comprador"]){
		$queryDB .= " AND ve.id_comprador = '".$_REQUEST["id_comprador"]."'";
	}
	
	if ($_REQUEST["nro_venta"]){
		$queryDB .= " AND (ve.nro_venta = '".$_REQUEST["nro_venta"]."' OR si.cedula = '".$_REQUEST["nro_venta"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["nro_venta"]))."%')";
	}
	
	if ($_REQUEST["modalidad_prima"]){
		$queryDB .= " AND ve.modalidad_prima = '".$_REQUEST["modalidad_prima"]."'";
	}
	
	if ($_REQUEST["estado"]){
		$queryDB .= " AND ve.estado = '".$_REQUEST["estado"]."'";
	}
	
	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"]){
		$queryDB .= " AND ve.fecha >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
		$queryDB .= " AND ve.fecha <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$queryDB .= " group by ve.id_venta, ve.nro_venta, ve.tipo, ve.fecha_anuncio, ve.fecha, ve.fecha_corte, co.nombre, ve.tasa_venta, ve.modalidad_prima, ve.estado, si.cedula, si.nombre, si.nro_libranza, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cso, si.valor_credito, si.pagaduria, si.plazo, si.estado, vd.id_ventadetalle, vd.completo, vd.fecha_primer_pago, vd.cuota_desde, vd.cuota_hasta, vd.recomprado";
}

$queryDB .= " order by fecha_anuncio, fecha, CASE WHEN nro_venta IS NULL THEN '999999999' ELSE nro_venta END, id_venta, ABS(cedula), id_ventadetalle";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	switch ($fila["modalidad_prima"])	
	{
		case "ANT":	$modalidad = "PRIMA ANTICIPADA"; break;
		case "MDI":	$modalidad = "PRIMA MENSUAL DIFERENCIA EN INTERESES"; break;
		case "MDC":	$modalidad = "PRIMA MENSUAL DIFERENCIA EN CUOTA"; break;
	}
	
	switch ($fila["estado"])
	{
		case "ALI":	$estado_venta = "ALISTADA"; break;
		case "VEN":	$estado_venta = "VENDIDA"; break;
		case "ANU":	$estado_venta = "ANULADA"; break;
	}
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"]; break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"]; break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"]; break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"]; break;
	}
	
	$valor_capital_vendido = $fila["valor_capital_vendido"];
	
	if ($fila["cuotas_vendidas"] == $fila["plazo"]){
		$valor_capital_vendido = $fila["valor_credito"];
	}
		
	
	switch ($fila["estado_credito"])
	{
		case "EST":	$estado_credito = "PARCIAL"; break;
		case "DES":	$estado_credito = "DESEMBOLSADO"; break;
		case "CAN":	$estado_credito = "CANCELADO"; break;
	}
	
	if ($fila["completo"] == "1"){
		$completo = "SI";
	}else{
		$completo = "NO";
	}
		
	
	if ($fila["recomprado"] == "1"){
		$recomprado = "SI";
	}else{
		$recomprado = "NO";
	}
	
	$compras_cartera = 0;
		
	$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";

	$rs1 = sqlsrv_query($link, $queryDB);	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	if ($fila1["s"]){
		$compras_cartera = $fila1["s"];
	}
	
	if ($fila["opcion_credito"] == "CLI"){
		$fila["retanqueo_total"] = 0;
	}
		
	$queryDB1 = "select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'CCA' and fecha_giro IS NOT NULL";
		
	//Si esta filtrado por fecha de corte
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
		$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	$rs1 = sqlsrv_query($link, $queryDB1);	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);	
	$giros_realizados_cca = $fila1["s"];
	$saldo_girar_cca = round($compras_cartera) - $giros_realizados_cca;

	$queryDB1 = "select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'RET' and fecha_giro IS NOT NULL";
		
	//Si est� filtrado por fecha de corte
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
		$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	$rs1 = sqlsrv_query($link, $queryDB1);	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);	
	$giros_realizados_ret = $fila1["s"];	
	$saldo_girar_ret = round($fila["retanqueo_total"]) - $giros_realizados_ret;

	$tipo_crediton="";
	$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
	$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito);
	if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
		$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
		$queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
		$resComprasCC=sqlsrv_fetch_array($queryComprasCC);
		
		if ($resComprasCC["cuota"]>0){
			if ($fila["retanqueo1_libranza"]=="" || $fila["retanqueo2_libranza"]=="" || $fila["retanqueo3_libranza"]==""){
				$tipo_crediton="COMPRAS DE CARTERA";	
			}else{
				$tipo_crediton="COMPRAS CON RETANQUEO";	
			}
		}
		else{
			if ($resComprasCC["valor_pagar"]>0){
				$tipo_crediton="LIBRE CON SANEAMIENTO";	
			}else{
				if ($fila["retanqueo1_libranza"]<>"" || $fila["retanqueo2_libranza"]<>"" || $fila["retanqueo3_libranza"]<>""){
					$tipo_crediton="LIBRE INVERSION CON RETANQUEO";	
				}
			}
		}
	}else{
		$tipo_crediton="LIBRE INVERSION";
	}
	
?>
<tr>
<td><?php echo $fila["tipo"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre_cliente"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["nro_venta"] ?></td>
	<td><?php echo $fila["tipo_venta"] ?></td>
	<td><?php echo $fila["fecha_anuncio"] ?></td>
	<td><?php echo $fila["fecha"] ?></td>
	<td><?php echo $fila["fecha_corte"] ?></td>
	<td><?php echo utf8_decode($fila["comprador"]) ?></td>
	<td><?php echo $fila["tasa_venta"] ?></td>
	<td><?php echo $modalidad ?></td>
	<td><?php echo $estado_venta ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $valor_capital_vendido ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo $estado_credito ?></td>
	<td><?php echo $completo ?></td>
	<td><?php echo $fila["fecha_primer_pago"] ?></td>
	<td><?php echo $fila["cuota_desde"] ?></td>
	<td><?php echo $fila["cuota_hasta"] ?></td>
	<td><?php echo $recomprado ?></td>
	<td><?php echo $fila["id_simulacion"] ?></td>

	<td><?php echo $fila["fecha_estudio"] ?></td>
	<td><?php echo $fila["fecha_desembolso"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo $fila["fecha_nacimiento"] ?></td>
	<td><?php echo $fila["sexo"] ?></td>
	<td><?php echo $fila["salario_basico"] ?></td>
	<td><?php echo $fila["aportes"] ?></td>
	<td><?php echo $fila["nombre_subestado"] ?></td>
	<td><?php echo number_format($saldo_girar_ret, 0, "", "") ?></td>
	<td><?php echo number_format($saldo_girar_cca, 0, "", "") ?></td>
	<td><?php echo $fila["formato_digital"] ?></td>
	<td><?php echo $tipo_crediton ?></td>
	<td><?php echo $fila["aumento_salario_minimo2"] ?></td>
</tr>
<?php

	$total_opcion_cuota += $opcion_cuota;
	$total_valor_credito += $fila["valor_credito"];
	$total_capital_vendido += $valor_capital_vendido;
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="15"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_cuota ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td><b><?php echo $total_capital_vendido ?></b></td>
	<td colspan="7">&nbsp;</td>
</tr>
</table>
