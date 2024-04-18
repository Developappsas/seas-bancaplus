<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Prospeccion.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();
?>
<table border="0">
<tr>
	<th>Comercial</th>
	<th>Oficina</th>
	<th>Cedula</th>
	<th>F Estudio</th>
	<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?><th>F Desemb</th><?php } ?>
	<th>Mes Prod</th>
	<th>Nombre</th>
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Unidad de Negocio</th>
	<th>Tasa Interes</th>
	<th>Plazo</th>
	<th>Total Cuota</th>
	<th>Total Retanqueos</th>
	<th>Cuota</th>
	<th>Cuota Corriente</th>
	<th>Seguro</th>
	<th>Costos Administrativos</th>
	<th>Valor Desembolso</th>
	<th>Valor Desembolso Menos Retanqueos</th>
	<th>Desembolso Cliente</th>
	<th>Estado</th>
	<th>Decision</th>
	<th>No. Libranza</th>
	<th>Subestado</th>
	<?php if ($_SESSION["FUNC_MUESTRACAMPOS1"]) { ?><th>Valor Credito</th><?php } ?>
	<th>F Radicado</th>
	<th>Usuario Radicado</th>
	<th>Perfil Radicado</th>
	<th>F Prospeccion</th>
	<th>Tiempo Prospeccion</th>
	<th>Usuario Prospeccion</th>
	<th>Frente al Cliente</th>
	<th>Tipo Causal</th>
	<th>Causal</th>
	<th>Tipo Comercial</th>
	<th>Sub Tipo</th>
	<th>Unidad de Negocio Comercial</th>
	<th>Planta-Terceros</th>
	<th>KP PLUS</th>
</tr>
<?php


$queryDB = "SELECT si.*, pa.sector, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, us.nombre as nombre_comercial, us.apellido, ofi.nombre as oficina, se.nombre as nombre_subestado, CASE WHEN si.tipo_producto = 1 THEN 'SI' ELSE 'NO' END as recuperate, cu2.seguro, us2.tipo as tipo_radicado, us2.subtipo as subtipo_radicado, cau.tipo_causal, cau.nombre as causal, us.tipo as tipocomercial, us.subtipo as subtipocomercial, un.nombre as unidad_negocio, case when concat(us.nombre, us.apellido) like'%agencia%' then 'Agencia' when concat(us.nombre, us.apellido) like'%outso%' then 'Terceros' when concat(us.nombre, us.apellido) like'%freel%' then 'Terceros' else 'Planta' end as tipocomer, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN usuarios us2 ON si.usuario_radicado = us2.login left join causales as cau on cau.id_causal=si.id_causal where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["id_oficina"])
{
	$queryDB .= " AND si.id_oficina = '".$_REQUEST["id_oficina"]."'";
}

if ($_REQUEST["id_comercial"])
{
	$queryDB .= " AND si.id_comercial = '".$_REQUEST["id_comercial"]."'";
}

if ($_REQUEST["decision"])
{
	$queryDB .= " AND si.decision = '".$_REQUEST["decision"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND si.fecha_estudio >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND si.fecha_estudio <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

$queryDB .= " order by si.fecha_radicado, si.nombre, si.cedula";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
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
	
	if ($fila["opcion_credito"] == "CLI")
		$fila["retanqueo_total"] = 0;
	
	if ($fila["seguro"] || $fila["seguro"] == "0")
		$seguro = $fila["seguro"];
	else
		if (!$fila["sin_seguro"])
			$seguro = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100)));
		else
			$seguro = 0;
	
	$cuota_corriente = $opcion_cuota - $seguro;
	
?>
<tr>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["fecha_estudio"] ?></td>
	<?php if ($_SESSION["FUNC_FDESEMBOLSO"]) { ?><td><?php echo $fila["fecha_desembolso"] ?></td><?php } ?>
	<td><?php echo $fila["mes_prod"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo $fila["total_cuota"] ?></td>
	<td><?php echo $fila["retanqueo_total"] ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo $cuota_corriente ?></td>
	<td><?php echo $seguro ?></td>
<?php

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
	
	$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
	
	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
	{
		$administrativos += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
	}
	
	switch ($fila["estado"])
	{
		case "ING":	$estado = "INGRESADO"; break;
		case "EST":	$estado = "EN ESTUDIO"; break;
		case "NEG":	$estado = "NEGADO"; break;
		case "DST":	$estado = "DESISTIDO"; break;
		case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
		case "DES":	$estado = "DESEMBOLSADO"; break;
		case "CAN":	$estado = "CANCELADO"; break;
		case "ANU":	$estado = "ANULADO"; break;
	}
	
	$tiempo_prospeccion = "";
	$tiempo_prospeccion_letras = "";
	
	if ($fila["fecha_prospeccion"]){
		$tiempo_prospeccion = strtotime($fila["fecha_prospeccion"]) - strtotime($fila["fecha_radicado"]);
		$tiempo_prospeccion_horas = intval($tiempo_prospeccion / 3600);
		$tiempo_prospeccion_minutos = intval(($tiempo_prospeccion - ($tiempo_prospeccion_horas * 3600)) / 60);
		$tiempo_prospeccion_segundos = $tiempo_prospeccion - $tiempo_prospeccion_minutos * 60 - $tiempo_prospeccion_horas * 3600;
		if ($tiempo_prospeccion_horas){
			$tiempo_prospeccion_letras .= $tiempo_prospeccion_horas."h ";
		}
		
		$tiempo_prospeccion_letras .= $tiempo_prospeccion_minutos."m ";
		$tiempo_prospeccion_letras .= $tiempo_prospeccion_segundos."s";
	}
	
	if ($fila["subtipo_radicado"] == "ANALISTA_REFERENCIA"){
		$fila["subtipo_radicado"] = "ANALISTA REFERENCIACION";
	}
	
	if ($fila["subtipo_radicado"] == "ANALISTA_GEST_COM"){
		$fila["subtipo_radicado"] = "ANALISTA GESTION COMERCIAL";
	}
	
	if ($fila["subtipo_radicado"] == "ANALISTA_VEN_CARTERA"){
		$fila["subtipo_radicado"] = "ANALISTA VENTA CARTERA";
	}
	
	if ($fila["subtipo_radicado"] == "ANALISTA_BD"){
		$fila["subtipo_radicado"] = "ANALISTA BASE DE DATOS";
	}
	
	if ($fila["subtipo_radicado"] == "COORD_PROSPECCION"){
		$fila["subtipo_radicado"] = "COORDINADOR PROSPECCION";
	}
	
	if ($fila["subtipo_radicado"] == "COORD_VISADO"){
		$fila["subtipo_radicado"] = "COORDINADOR VISADO";
	}
	
	if ($fila["subtipo_radicado"] == "COORD_CREDITO"){
		$fila["subtipo_radicado"] = "COORDINADOR CREDITO";
	}
	
	if ($fila["tipo_radicado"] == "GERENTECOMERCIAL"){
		$fila["tipo_radicado"] = "GERENTE REGIONAL";
	}
	
	if ($fila["tipo_radicado"] == "DIRECTOROFICINA"){
		$fila["tipo_radicado"] = "DIRECTOR OFICINA";
	}
	
	if ($fila["tipo_radicado"] == "CARTERA"){
		$fila["tipo_radicado"] = "DIRECTOR DE CARTERA";
	}
	
	if ($fila["tipo_radicado"] == "OPERACIONES"){
		$fila["tipo_radicado"] = "DIRECTOR DE OPERACIONES";
	}
	
	$i = 0;
	
	$unidades_asociadas = "";
	
	$queryDB = "select un.nombre from usuarios_unidades uu INNER JOIN unidades_negocio un ON uu.id_unidad_negocio = un.id_unidad where uu.id_usuario = '".$fila["id_comercial"]."' order by un.id_unidad";

	$rs1 = sqlsrv_query($link, $queryDB);

	while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	{
		if ($i){
			$unidades_asociadas .= ", ";
		}
			
		$unidades_asociadas .= utf8_decode($fila1["nombre"]);
		$i++;
	}
	
?>
	<td><?php echo round($administrativos, 0) ?></td>
	<td><?php echo $opcion_desembolso ?></td>
	<td><?php echo $opcion_desembolso - $fila["retanqueo_total"] ?></td>
	<td><?php echo $fila["desembolso_cliente"] ?></td>
	<td><?php echo $estado ?></td>
	<td><?php echo $fila["decision"] ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["nombre_subestado"] ?></td>
	<?php if ($_SESSION["FUNC_MUESTRACAMPOS1"]) { ?><td><?php echo $fila["valor_credito"] ?></td><?php } ?>
	<td><?php echo $fila["fecha_radicado"] ?></td>
	<td><?php echo $fila["usuario_radicado"] ?></td>
	<td><?php echo $fila["tipo_radicado"] ?><?php if ($fila["subtipo_radicado"]) { echo "/".$fila["subtipo_radicado"]; } ?></td>
	<td><?php echo $fila["fecha_prospeccion"] ?></td>
	<td><?php if ($tiempo_prospeccion) { echo round($tiempo_prospeccion / 60, 2); } ?></td>
	<td><?php echo $fila["usuario_prospeccion"] ?></td>
	<td><?php echo $fila["frente_al_cliente"] ?></td>
	<td><?php echo $fila["tipo_causal"] ?></td>
	<td><?php echo $fila["causal"] ?></td>
	<td><?php echo $fila["tipocomercial"] ?></td>
	<td><?php echo $fila["subtipocomercial"] ?></td>
	<td><?php echo utf8_decode($unidades_asociadas) ?></td>
	<td><?php echo $fila["tipocomer"] ?></td>
	<td><?php echo $fila["sin_seguro_x"] ?></td>
</tr>
<?php
}
?>
</table>
