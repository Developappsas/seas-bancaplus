<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Cierre.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"  && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}
$link = conectar(); ?>

<table border="0">
	<tr>
		<th>Cédula</th>
		<th>Mes Prod</th>
		<th>Nombre</th>
		<th>Pagaduría</th>
		<th>Unidad de Negocio</th>
		<th>Vr. Crédito</th>
		<th>Subestado</th>
		<th>Id</th>
		<th>No. Crédito</th>
		<th>Oficina</th>
		<th>Desembolso - Retanqueo</th>
	</tr>
<?php
//Agregamos subestado 6.2}
$subestados_tesoreria .= ",'78'";

$queryDB = "SELECT ofi.nombre as oficina, si.*, un.nombre as unidad_negocio, FORMAT(fecha_cartera, 'yyyy-MM') as mes_prod, pa.sector, se.nombre as nombre_subestado, FORMAT(fecha_comision_pagada, 'YYYY-MM-DD') as fecha_comision_pagada_texto 
from simulaciones si 
INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre
LEFT JOIN subestados se ON si.id_subestado = se.id_subestado 
INNER JOIN oficinas ofi ON ofi.id_oficina=si.id_oficina 
where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

if ($_SESSION["S_SECTOR"]){
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"]){
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["sector"]){
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"]){
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["estado"]){
	$queryDB .= " AND si.estado_tesoreria = '".$_REQUEST["estado"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
//	$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}


if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]){
	$queryDB .= " AND si.fecha_tesoreria <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"]){
	$queryDB .= " AND ((FORMAT(si.fecha_cartera,'yyyy-MM') >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."'";
}

if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"]){
	
	$queryDB .= " AND ";
	if (!$_REQUEST["fechacartera_inicialbm"] && !$_REQUEST["fechacartera_inicialba"]){
		$queryDB .= " ( ";
	}else{
		$queryDB.=" FORMAT(si.fecha_cartera,'yyyy-MM') <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."')";
	}
}else{
	if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"]){
		$queryDB.=")";
	}
}

$queryDB.= " OR (si.fecha_cartera is null)";

if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"]){
	$queryDB.=")";
}

$queryDB .= " order by si.fecha_tesoreria DESC, si.id_simulacion DESC";
//echo $queryDB;

$rs = sqlsrv_query($link, $queryDB);
while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
	switch($fila["opcion_credito"]){
		case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"]; $fila["retanqueo_total"] = 0;
			break;
		case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"];
			break;
		case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"];
			break;
		case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"];
			break;
	}

	$valor_desembolso_retanqueo=$opcion_desembolso-$fila["retanqueo_total"]; ?>

	<tr>
		<td><?php echo $fila["cedula"] ?></td>
		<td><?php echo $fila["mes_prod"] ?></td>
		<td><?php echo utf8_decode($fila["nombre"]) ?></td>
		<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
		<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
		<td><?php echo $fila["valor_credito"] ?></td>
		<td><?php echo utf8_decode($fila["nombre_subestado"]) ?></td>
		<td><?php echo $fila["id_simulacion"] ?></td>
		<td><?php echo $fila["nro_libranza"] ?></td>
	    <td><?php echo $fila["oficina"] ?></td>
		<td><?php echo $valor_desembolso_retanqueo ?></td>	
	</tr>
	<?php
} ?>
</table>