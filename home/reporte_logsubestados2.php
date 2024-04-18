<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Historial Subestados.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>ID</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Asesor</th>
	<th>Tipo Asesor</th>	
	<th>Contrato</th>
	<th>Oficina</th>
	<th>No. Libranza</th>
	<th>Vr. Credito</th>
	<th>Desembolso Menos Retanqueos</th>
	<th>Estado</th>
	<th>Etapa</th>
	<th>Subestado</th>
	<th>Usuario</th>
	<th>Fecha</th>
	<th>Mes Prod</th>
</tr>
<?php

$queryDB = "SELECT si.id_simulacion, si.cedula, si.nombre, si.pagaduria, si.opcion_credito, si.opcion_desembolso_cli, si.opcion_desembolso_ccc, si.opcion_desembolso_cmp, si.opcion_desembolso_cso, si.valor_credito, si.estado, si.nro_libranza, si.retanqueo_total, pa.sector, us.nombre as nombre_comercial, us.apellido, us.contrato, us.freelance, us.outsourcing, ofi.nombre as oficina, et.nombre as nombre_etapa, se.nombre as subestado, ss.usuario_creacion, ss.fecha_creacion, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod from simulaciones_subestados ss INNER JOIN simulaciones si ON ss.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN subestados se ON ss.id_subestado = se.id_subestado INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa where si.estado NOT IN ('DST', 'DSS', 'ANU')";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
}

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND DATE_FORMAT(ss.fecha_creacion, '%Y-%m-%d') >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND DATE_FORMAT(ss.fecha_creacion, '%Y-%m-%d') <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if ($_REQUEST["id_subestado"])
{
	$queryDB .= " AND ss.id_subestado = '".$_REQUEST["id_subestado"]."'";
}

$queryDB .= " order by ss.fecha_creacion, si.cedula";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$tipo_comercial = 'PLANTA';
	
	if ($fila["freelance"]) {
		$tipo_comercial = 'FREELANCE';
	}
	
	if ($fila["outsourcing"]) {
		$tipo_comercial = 'OUTSOURCING';
	}
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"];
					break;
		case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"];
					break;
		case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"];
					break;
		case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"];
					break;
	}
	
	if ($fila["opcion_credito"] == "CLI")
		$fila["retanqueo_total"] = 0;
	
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
	
?>
<tr>
	<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]) ?></td>
	<td><?php echo $tipo_comercial ?></td>
	<td><?php echo $fila["contrato"] ?></td>
	<td><?php echo utf8_decode($fila["oficina"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $opcion_desembolso - $fila["retanqueo_total"] ?></td>
	<td><?php echo $estado ?></td>
	<td><?php echo utf8_decode($fila["nombre_etapa"]) ?></td>
	<td><?php echo utf8_decode($fila["subestado"]) ?></td>
	<td><?php echo $fila["usuario_creacion"] ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>	
</tr>
<?php

}

?>
</table>
