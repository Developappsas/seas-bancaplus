<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=DesembolsosComisiones.xls");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_POST['user']) || !isset($_POST['password'])){
	echo "debe loguearse";
	exit();
}

include ('../functions.php'); 
$link = conectar();

$queryPOST = mysqli_query($link, "SELECT usr FROM proveedores WHERE usr = '".$_POST['user']."' AND passwd = MD5('".$_POST['password']."')");

if(!$queryPOST || mysqli_num_rows($queryPOST) == 0){
	echo "error al loguearse";
	exit();
}


	

?>
<table border="0">
<tr>
	<th colspan="7">ASESOR</th>
	<th colspan="11">CLIENTE</th>
</tr>
<tr>
	<th>Cedula Asesor</th>
	<th>Nombre Asesor</th>
	<th>Tipo Asesor</th>
	<th>Oficina</th>
	<th>Comision Pagada</th>
	<th>Fecha Comision Pagada</th>
	<th>Descontar Comision</th>
	<th>Mes Prod</th>
	<th>No. Libranza</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Desembolso Menos Retanqueos</th>
	<th>Tasa</th>
	<th>COMPRAS DE CARTERA</th>
	<th>Unidad de Negocio</th>
	<th>KP PLUS</th>
	<th>SubEstado</th>
	<th>Estado</th>
	<th>Caracteristicas</th>	
	<th>Tipo Comision</th>
	<th>Zona</th>
	<th>Pagaduria</th>
</tr>
<?php

$queryDB = "select pa.nombre as pagaduria_nombre,si.*, un.id_unidad, un.nombre as unidad_negocio, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x, DATE_FORMAT(fecha_comision_pagada, '%Y-%m-%d') as fecha_comision_pagada_texto, pa.sector, DATE_FORMAT(si.fecha_cartera, '%Y-%m') as mes_prod, CONCAT(us.nombre,' ',us.apellido) as nombre_comercial, us.cedula as cedula_comercial, us.contrato, us.freelance, us.outsourcing, so.direccion, ci.municipio, so.celular, ofi.nombre as oficina, CASE WHEN si.estado IN ('DES', 'CAN') THEN CASE WHEN fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE fn_fecha_desembolso_final(si.id_simulacion) END ELSE '' END as fecha_desembolso_final, ca.nombre as caracteristica, so.email, sub.nombre AS subestado, zon.nombre as zona_descripcion
from simulaciones si 
INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre
LEFT JOIN subestados sub ON sub.id_subestado = si.id_subestado
INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
LEFT JOIN zonas zon ON zon.id_zona=ofi.id_zona 
LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica
WHERE (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.",'78')))";

if ($POST["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '".$_POST["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_POST["S_IDUNIDADNEGOCIO"].")";

if ($_POST["S_TIPO"] == "GERENTECOMERCIAL" || $_POST["S_TIPO"] == "DIRECTOROFICINA" || $_POST["S_TIPO"] == "PROSPECCION") {
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_POST["S_IDUSUARIO"]."')";
	
	if ($_POST["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_POST["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_POST["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_POST["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
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

$rs = mysqli_query($link, $queryDB);

while ($fila = mysqli_fetch_assoc($rs))
{
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
	
	$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL || (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
	
	$rs1 = mysqli_query($link, $queryDB);
	
	$fila1 = mysqli_fetch_assoc($rs1);
	
	if ($fila1["s"])
		$compras_cartera = $fila1["s"];
	
	if ($fila["opcion_credito"] == "CLI")
		$fila["retanqueo_total"] = 0;
	
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
	
	if ($fila["tipo_producto"] == "1")
	{
		if ($fila["fecha_estudio"] < "2018-01-01")
		{
			$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
		}
		else
		{
			if ($fila["fidelizacion"])
				$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
			else
				$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
		}
	}
	
	if(!$fila["servicio_nube"]){
		$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;
	}
	
	$comision_venta_iva = 0;
	
	if ($fila["tipo_producto"] == "1")
	{
		if ($fila["fecha_estudio"] < "2018-01-01")
		{
			$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
		else
		{
			if ($fila["fidelizacion"])
				$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			else
				$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		}
	}
	
	$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;
	
	$administrativos = round($intereses_anticipados) + round($asesoria_financiera) + round($iva) + round($gmf) + round($comision_venta) + round($comision_venta_iva);
	
	$descuentos_adicionales = mysqli_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
	
	while ($fila1 = mysqli_fetch_assoc($descuentos_adicionales))
	{
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
	
	if (mysqli_num_rows($descuentos_adicionales))
	{
		mysqli_data_seek($descuentos_adicionales, 0);
		
		while ($fila1 = mysqli_fetch_assoc($descuentos_adicionales))
		{
			$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
		}
	}
	
	switch ($fila["estado_tesoreria"])
	{
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
	
	if ($fila["telemercadeo"])
		$telemercadeo = "SI";
	else
		$telemercadeo = "NO";
	
	if ($fila["comision_pagada"])
		$comision_pag = "SI";
	else
		$comision_pag = "NO";
	
?>
<tr>
	<td><?php echo $fila["cedula_comercial"] ?></td>
	<td><?php echo $fila["nombre_comercial"] ?></td>
	
	<td><?php echo $tipo_comercial ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>

	<td><?php echo $comision_pag ?></td>
	<td><?php echo $fila["fecha_comision_pagada_texto"] ?></td>
	
	
	<td><?php echo utf8_decode($fila["valor_comision_descontar"]) ?></td>

	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>

	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>

	<td><?php echo $desembolso_neto - $fila["retanqueo_total"] ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php $consultarTesoreriaCC="SELECT * FROM tesoreria_cc a LEFT JOIN simulaciones_comprascartera b ON a.consecutivo=b.consecutivo AND a.id_simulacion=b.id_simulacion  WHERE a.id_simulacion='".$fila["id_simulacion"]."' and b.id_entidad NOT IN (413,104,218) AND a.consecutivo NOT IN (SELECT consecutivo FROM simulaciones_comprascartera WHERE se_compra='SI' AND id_entidad=834 AND valor_pagar=0 AND id_simulacion='".$fila["id_simulacion"]."')";
	$queryTesorariaCC=mysqli_query($link, $consultarTesoreriaCC);
	if (mysqli_num_rows($queryTesorariaCC)>0)
	{
		$consultarTesoreriaCCNoPagada=$consultarTesoreriaCC." AND pagada=0";
		$queryTesorariaCCNoPagada=mysqli_query($link, $consultarTesoreriaCCNoPagada);
		if (mysqli_num_rows($queryTesorariaCCNoPagada)>0)
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

	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["sin_seguro_x"] ?></td>
	<td><?php echo $fila["subestado"] ?></td>	
	<td><?php echo $estado ?></td>						
	<td><?php echo utf8_decode($fila["caracteristica"]) ?></td>

	<?php 
		
		$queryTasaC = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, if(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE id_tasa_comision = ".$fila['id_tasa_comision'];
				
		$rsTC = mysqli_query($link, $queryTasaC);

		$tipo_tasa_comision = '';

		if (mysqli_num_rows($rsTC)>0){
			
			$fTC = mysqli_fetch_assoc($rsTC);
			
			if($fTC['marca_unidad_negocio'] == 1){ 
				$textoTipoComsion = 'F'; 
			}else{ 
				$textoTipoComsion = 'K'; 
			}

			$tipo_tasa_comision = 'TIPO '.$textoTipoComsion.' '.$fTC['id_tipo'];
		}
	?>

	<td><?=$tipo_tasa_comision?></td>
	<td><?=$fila["zona_descripcion"]?></td>
	<td><?=$fila["pagaduria_nombre"]?></td>
	<?php
}	
?>
</tr>
</table>

