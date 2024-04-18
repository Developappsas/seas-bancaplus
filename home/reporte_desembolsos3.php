<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Desembolsos.xls");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_POST['user']) || !isset($_POST['password'])){
	echo "debe loguearse";
	exit();
}

include ('../functions.php'); 
$link = conectar();

$queryPOST = sqlsrv_query($link, "SELECT usr FROM proveedores WHERE usr = '".$_POST['user']."' AND passwd = MD5('".$_POST['password']."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(!$queryPOST || sqlsrv_num_rows($queryPOST) == 0){
	echo "error al loguearse";
	exit();
}



?>
<table border="0">
<tr>
	<th>ID</th>
	<th>Cedula</th>
	<th>F. Tesoreria</th>
	<th>Subestado</th>
	<th>F. Desembolso Inicial</th>
	<th>F. Desembolso Final</th>
	<th>Mes Prod</th>
	<th>Nombre</th>
	<th>Dir. Residencia</th>
	<th>Ciudad Residencia</th>
	<th>No. Celular</th>
	<th>No. Libranza</th>
	<th>Unidad de Negocio</th>
	<th>Tasa</th>
	<th>Cuota Total</th>
	<th>Cuota Corriente</th>
	<th>Seguro</th>
	<th>Vr. Credito</th>
	<th>Desembolso Neto</th>
	<th>Desembolso Menos Retanqueos</th>
	<th>Costos Administrativos</th>	
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Plazo</th>
	<th>Cedula</th>
	<th>Asesor</th>
	
	<th>Tipo Asesor</th>
	<th>Contrato</th>
	<th>Telemercadeo</th>
	<th>Oficina</th>
	<th>Compras de Cartera</th>
	<th>Retanqueos</th>
	<th>Intereses Anticipados/Aval</th>
	<th>Base Asesor&iacute;a Financiera</th>
	<th>Servicio Nube</th>
	<th>Asesor&iacute;a Financiera</th>
	<th>IVA</th>
	<th>GMF</th>
	
<?php
$ccpagada="";
$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
{

?>
	<th><?php echo $fila1["nombre"] ?></th>
<?php

}

?>
	<th>Comision por Venta (Retanqueos)</th>
	<th>IVA (Comision por Venta)</th>
	<th>Transferencia</th>
	<th>Desembolso Cliente</th>
	<th>Comision Pagada</th>
	<th>Fecha Comision Pagada</th>
	<th>Caracteristica</th>
	<th>Estado</th>
	<th>KP PLUS</th>
	<th>E-mail</th>
	<th>Comision a Descontar</th>
	<th>COMPRA DE CARTERA</th>
	<th>Formato Digital</th>
	<th>Facturado</th>
	<th>Zona</th>
	<th>Fecha Tesoreria Final</th>
	<th>Aumento Salario Minimo</th>
</tr>
<?php

$queryDB = "SELECT  iIF(si.aumento_salario_minimo=1, 'SI', 'NO') AS aumento_salario_minimo2, si.servicio_nube, si.descuento1_valor, si.descuento2_valor, si.descuento3_valor, si.descuento4_valor, si.descuento5_valor, si.descuento6_valor, si.descuento7_valor, si.descuento8_valor, si.descuento9_valor, si.descuento10_valor, CASE WHEN si.formato_digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital_descripcion,si.formato_digital,zon.nombre as zona_descripcion, se.nombre as nombre_subestado,si.*, un.nombre as unidad_negocio, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x, FORMAT(fecha_comision_pagada, 'Y-m-d') as fecha_comision_pagada_texto, pa.sector, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, us.nombre as nombre_comercial, us.cedula as cedula_comercial,us.apellido, us.contrato, us.freelance, us.outsourcing, so.direccion, ci.municipio, so.celular, ofi.nombre as oficina, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN dbo.fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE dbo.fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, ca.nombre as caracteristica, so.email from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica LEFT JOIN subestados se ON se.id_subestado=si.id_subestado  LEFT JOIN zonas zon ON zon.id_zona=ofi.id_zona  where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

if ($_POST["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_POST["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_POST["S_IDUNIDADNEGOCIO"].")";

if ($_POST["S_TIPO"] == "GERENTECOMERCIAL" || $_POST["S_TIPO"] == "DIRECTOROFICINA" || $_POST["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_POST["S_IDUSUARIO"]."')";

	
	if ($_POST["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
										
	if ($_POST["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo in ('0','1')";
										
	if ($_POST["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
										
	if ($_POST["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo IN ('0','1')";
										
	if ($_POST["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
}

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"])
{
	$queryDB .= " AND si.fecha_desembolso >= '".$_REQUEST["fechades_inicialba"]."-".$_REQUEST["fechades_inicialbm"]."-".$_REQUEST["fechades_inicialbd"]."'";
}

if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"])
{
	$queryDB .= " AND si.fecha_desembolso <= '".$_REQUEST["fechades_finalba"]."-".$_REQUEST["fechades_finalbm"]."-".$_REQUEST["fechades_finalbd"]."'";
}

if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
{
	$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."'";
}

if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
{
	$queryDB .= " AND DATE_FORMAT(si.fecha_cartera,'%Y-%m') <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."'";
}

if ($_REQUEST["estado"])
{
	$queryDB .= " AND si.estado_tesoreria = '".$_REQUEST["estado"]."'";
}

$queryDB .= " order by si.fecha_desembolso, si.fecha_creacion";


// echo $queryDB; die;
//echo $queryDB;
$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	
	$rptaFacturado="NO"; 
	$consultarFacturado = "SELECT TOP 1 * FROM hst_facturacion_creditos WHERE id_simulacion = '".$fila["id_simulacion"]."' ORDER BY id DESC"; 
	$queryFacturado = sqlsrv_query($link,$consultarFacturado, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if($queryFacturado) { 
		if (sqlsrv_num_rows($queryFacturado) > 0) { 
			$resFacturado = sqlsrv_fetch_array($queryFacturado); 
			if ($resFacturado["facturado"] == 1){ 
				$rptaFacturado = "SI"; 
			}
		}
	}

	$val=0;
		$fecha_ultima_tesoreria="";
		$consultarUltimoFechaTesoreria=sqlsrv_query($link,"SELECT top 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0) {
			$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
			$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
			$val=1;
		}

		if ($val==0){
			$consultarUltimoFechaTesoreria=sqlsrv_query($link,"SELECT TOP 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0)
			{
				$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
				$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
				$val=1;
			}
		}


	switch ($fila["estado_tesoreria"])
	{
		case "ABI":	$estado = "ABIERTO"; break;
		case "PAR":	$estado = "PARCIAL"; break;
		case "CER":	$estado = "CERRADO"; break;
	}
	
	switch($fila["opcion_credito"])
	{
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
		$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	else
		$seguro_vida = 0;
	
	$cuota_corriente = $opcion_cuota - round($seguro_vida);
	
	$compras_cartera = 0;
	
	$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
	
	$rs1 = sqlsrv_query($link, $queryDB);
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	if ($fila1["s"])
		$compras_cartera = $fila1["s"];
	
	if ($fila["opcion_credito"] == "CLI"){
		$fila["retanqueo_total"] = 0;
	}
	
	$intereses_anticipados = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00);

	$asesoria_financiera = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00, 0);
	$asesoria_financiera_base = $asesoria_financiera;
	$iva = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00, 0);
	$iva_porc = $fila["descuento3"];
	$valor_servicio_nube = 0;
	$asesoria_financiera_nueva = 0;

	if($fila["servicio_nube"]){
		$asesoria_financiera = $fila["descuento2_valor"];
		$valor_servicio_nube = $fila["descuento8_valor"];
		$asesoria_financiera_nueva = $fila["descuento9_valor"];
		$iva = $fila["descuento10_valor"];

		if($fila["descuento10_valor"] > 0){
			$iva_porc = round($iva / ($fila["valor_credito"] - $fila["retanqueo_total"]) * 1000, 2);
		}else{
			$iva_porc = 0;
		}
	}
	
	$comision_venta = 0;
	
	if ($fila["tipo_producto"] == "1"){
		if ($fila["fecha_estudio"] < "2018-01-01"){
			$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
		}
		else{
			if ($fila["fidelizacion"]){
				$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
			}
			else{
				$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
			}
		}
	}
	
	if(!$fila["servicio_nube"]){
		$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;
	}
	
	$comision_venta_iva = 0;
	
	if ($fila["tipo_producto"] == "1"){
		if ($fila["fecha_estudio"] < "2018-01-01"){
			$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
		else{
			if ($fila["fidelizacion"]){
				$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			}
			else{
				$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			}
		}
	}
	
	$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;
	
	$administrativos = round($intereses_anticipados) + round($asesoria_financiera) + round($iva) + round($gmf) + round($comision_venta) + round($comision_venta_iva);

	$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
		$administrativos += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
	}
	
	$desembolso_neto = round($fila["valor_credito"]) - $administrativos;
	
	if($fila["servicio_nube"]){
		$desembolso_cliente = $fila["desembolso_cliente"];
	}else{
		$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];
	}
	
	if ($fila["tipo_producto"] == "1"){
		if ($fila["fidelizacion"]){
			$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
		}
		else{
			$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
	}
	
	if (sqlsrv_num_rows($descuentos_adicionales)){
		sqlsrv_data_seek($descuentos_adicionales, 0);
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
			$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
		}
	}
	
	switch ($fila["estado_tesoreria"]){
		case "ABI":	$estado = "ABIERTO"; break;
		case "PAR":	$estado = "PARCIAL"; break;
		case "CER":	$estado = "CERRADO"; break;
	}
	
	$tipo_comercial = 'PLANTA';
	
	if ($fila["freelance"]) {
		$tipo_comercial = 'FREELANCE';
	}
	
	if ($fila["outsourcing"]) {
		$tipo_comercial = 'OUTSOURCING';
	}
	
	if ($fila["telemercadeo"]){
		$telemercadeo = "SI";
	}
	else{
		$telemercadeo = "NO";
	}
	
	if ($fila["comision_pagada"]){
		$comision_pag = "SI";
	}
	else{
		$comision_pag = "NO";
	}
?>
<tr>
	<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["fecha_tesoreria"] ?></td>
	<td><?php echo $fila["nombre_subestado"] ?></td>
	<td><?php echo $fila["fecha_desembolso"] ?></td>
	<td><?php echo $fila["fecha_desembolso_final"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["municipio"]) ?></td>
	<td><?php echo utf8_decode($fila["celular"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $cuota_corriente ?></td>
	<td><?php echo round($seguro_vida) ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $desembolso_neto ?></td>
	<td><?php echo $desembolso_neto - $fila["retanqueo_total"] ?></td>
	<td><?php echo $administrativos ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo utf8_decode($fila["cedula_comercial"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo $tipo_comercial ?></td>
	<td><?php echo $fila["contrato"] ?></td>
	<td><?php echo $telemercadeo ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td><?php echo $compras_cartera ?></td>
	<td><?php echo $fila["retanqueo_total"] ?></td>
	<td><?php echo round($intereses_anticipados) ?></td>
	<td><?php echo round($asesoria_financiera) ?></td>
	<td><?php echo round($valor_servicio_nube) ?></td>
	<td><?php echo round($asesoria_financiera_nueva) ?></td>
	<td><?php echo round($iva) ?></td>
	<td><?php echo round($gmf) ?></td>
<?php

	$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");
	
	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
	{
		$existe_descuento = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' AND id_descuento = '".$fila1["id_descuento"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($existe_descuento))
		{
			$valor_descuento = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
			
			$total_descuentos_adicionales[$fila1["id_descuento"]] += $valor_descuento;
		}
		else
		{
			$valor_descuento = 0;
		}
		
?>
	<td><?php echo $valor_descuento ?></td>
<?php

	}
	
?>
	<td><?php echo round($comision_venta) ?></td>
	<td><?php echo round($comision_venta_iva) ?></td>
	<td><?php echo $fila["descuento_transferencia"] ?></td>
	<td><?php echo round($desembolso_cliente) ?></td>
	<td><?php echo $comision_pag ?></td>
	<td><?php echo $fila["fecha_comision_pagada_texto"] ?></td>
	<td><?php echo utf8_decode($fila["caracteristica"]) ?></td>
	<td><?php echo $estado ?></td>
	<td><?php echo $fila["sin_seguro_x"] ?></td>
	<td><?php echo utf8_decode($fila["email"]) ?></td>
	<td><?php echo utf8_decode($fila["valor_comision_descontar"]) ?></td>
	<td><?php $consultarTesoreriaCC="SELECT * FROM tesoreria_cc a LEFT JOIN simulaciones_comprascartera b ON a.consecutivo=b.consecutivo AND a.id_simulacion=b.id_simulacion  WHERE a.id_simulacion='".$fila["id_simulacion"]."' and b.id_entidad NOT IN (413,104,218) AND a.consecutivo NOT IN (SELECT consecutivo FROM simulaciones_comprascartera WHERE se_compra='SI' AND id_entidad=834 AND valor_pagar=0 AND id_simulacion='".$fila["id_simulacion"]."')";
	$queryTesorariaCC=sqlsrv_query($link, $consultarTesoreriaCC, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if (sqlsrv_num_rows($queryTesorariaCC)>0)
	{
		$consultarTesoreriaCCNoPagada=$consultarTesoreriaCC." AND pagada=0";
		$queryTesorariaCCNoPagada=sqlsrv_query($link, $consultarTesoreriaCCNoPagada, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($queryTesorariaCCNoPagada)>0)
		{
			$ccpagada="NO";
		}else{
			$ccpagada="SI";
		}
	}else{
		$ccpagada="NO APLICA";
	}
	echo $ccpagada;
	?></td>
	<td><?php echo $fila["formato_digital_descripcion"] ?></td>
	<td><?php echo $rptaFacturado ?></td>
	
	<td><?php echo $fila["zona_descripcion"] ?></td>
	<td><?php echo $fecha_ultima_tesoreria ?></td>
	<td><?php echo $fila["aumento_salario_minimo2"] ?></td>
</tr>
<?php

	$total_opcion_cuota += $opcion_cuota;
	$total_cuota_corriente += $cuota_corriente;
	$total_seguro_vida += round($seguro_vida);
	$total_valor_credito += $fila["valor_credito"];
	$total_desembolso_neto += $desembolso_neto;
	$total_sin_retanqueos += ($desembolso_neto - $fila["retanqueo_total"]);
	$total_administrativos += $administrativos;	
	
	$total_compras_cartera += $compras_cartera;
	$total_retanqueos += $fila["retanqueo_total"];
	$total_intereses_anticipados += round($intereses_anticipados);
	$total_asesoria_financiera += round($asesoria_financiera);
	$total_valor_servicio_nube += round($valor_servicio_nube);
	$total_asesoria_financiera_nueva += round($asesoria_financiera_nueva);
	$total_iva += round($iva);
	$total_gmf += round($gmf);
	$total_comision_venta += round($comision_venta);
	$total_comision_venta_iva += round($comision_venta_iva);
	$total_descuento_transferencia += $fila["descuento_transferencia"];
	$total_desembolso_cliente += round($desembolso_cliente);
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="14"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_cuota ?></b></td>
	<td><b><?php echo $total_cuota_corriente ?></b></td>
	<td><b><?php echo $total_seguro_vida ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td><b><?php echo $total_desembolso_neto ?></b></td>
	<td><b><?php echo $total_sin_retanqueos ?></b></td>
	<td><b><?php echo $total_administrativos ?></b></td>	
	<td colspan="9">&nbsp;</b></td>
	<td><b><?php echo $total_compras_cartera ?></b></td>
	<td><b><?php echo $total_retanqueos ?></b></td>
	<td><b><?php echo $total_intereses_anticipados ?></b></td>
	<td><b><?php echo $total_asesoria_financiera ?></b></td>
	<td><b><?php echo $total_valor_servicio_nube ?></b></td>
	<td><b><?php echo $total_asesoria_financiera_nueva ?></b></td>
	<td><b><?php echo $total_iva ?></b></td>
	<td><b><?php echo $total_gmf ?></b></td>
	
<?php

$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
{

?>
	<td><b><?php echo $total_descuentos_adicionales[$fila1["id_descuento"]] ?></b></td>
<?php

}

?>
	<td><b><?php echo $total_comision_venta ?></b></td>
	<td><b><?php echo $total_comision_venta_iva ?></b></td>
	<td><b><?php echo $total_descuento_transferencia ?></b></td>
	<td><b><?php echo $total_desembolso_cliente ?></b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
	<td>&nbsp;</b></td>
</tr>
</table>

