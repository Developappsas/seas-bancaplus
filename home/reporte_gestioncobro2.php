<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=GestionCobro.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Tipo Cartera</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Tipo Gestion</th>
	<th>Observacion</th>
	<th>F Compromiso</th>
	<th>Usuario</th>
	<th>Fecha</th>
</tr>
<?php

if ($_REQUEST["tipo"] == "ORI" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB = "SELECT 'ORIGINACION' as tipo, si.cedula, si.nombre, si.nro_libranza, tg.nombre as tipo_gestion, gc.* from gestion_cobro gc INNER JOIN simulaciones si ON gc.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_gestioncobro tg ON gc.id_tipo = tg.id_tipo where 1 = 1";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	
	if ($_REQUEST["cedula"])
	{
		$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}
	
	if ($_REQUEST["pagaduria"])
	{
		$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}
	
	if ($_REQUEST["id_tipo"])
	{
		$queryDB .= " AND gc.id_tipo = '".$_REQUEST["id_tipo"]."'";
	}
	
	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
	{
		$queryDB .= " AND DATE(gc.fecha_creacion) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		$queryDB .= " AND DATE(gc.fecha_creacion) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	if ($_REQUEST["fechacomp_inicialbd"] && $_REQUEST["fechacomp_inicialbm"] && $_REQUEST["fechacomp_inicialba"])
	{
		$queryDB .= " AND DATE(gc.fecha_compromiso) >= '".$_REQUEST["fechacomp_inicialba"]."-".$_REQUEST["fechacomp_inicialbm"]."-".$_REQUEST["fechacomp_inicialbd"]."'";
	}
	
	if ($_REQUEST["fechacomp_finalbd"] && $_REQUEST["fechacomp_finalbm"] && $_REQUEST["fechacomp_finalba"])
	{
		$queryDB .= " AND DATE(gc.fecha_compromiso) <= '".$_REQUEST["fechacomp_finalba"]."-".$_REQUEST["fechacomp_finalbm"]."-".$_REQUEST["fechacomp_finalbd"]."'";
	}
}

if ($_REQUEST["tipo"] == "ALL")
{
	$queryDB .= " UNION ";
}

if ($_REQUEST["tipo"] == "EXT" || $_REQUEST["tipo"] == "ALL")
{
	$queryDB .= "select 'EXTERNA' as tipo, si.cedula, si.nombre, si.nro_libranza, tg.nombre as tipo_gestion, gc.* from gestion_cobro_ext gc INNER JOIN simulaciones_ext si ON gc.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN tipos_gestioncobro tg ON gc.id_tipo = tg.id_tipo where 1 = 1";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_REQUEST["cedula"])
	{
		$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
	}
	
	if ($_REQUEST["pagaduria"])
	{
		$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
	}
	
	if ($_REQUEST["id_tipo"])
	{
		$queryDB .= " AND gc.id_tipo = '".$_REQUEST["id_tipo"]."'";
	}
	
	if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
	{
		$queryDB .= " AND DATE(gc.fecha_creacion) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
	}
	
	if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		$queryDB .= " AND DATE(gc.fecha_creacion) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}
	
	if ($_REQUEST["fechacomp_inicialbd"] && $_REQUEST["fechacomp_inicialbm"] && $_REQUEST["fechacomp_inicialba"])
	{
		$queryDB .= " AND DATE(gc.fecha_compromiso) >= '".$_REQUEST["fechacomp_inicialba"]."-".$_REQUEST["fechacomp_inicialbm"]."-".$_REQUEST["fechacomp_inicialbd"]."'";
	}
	
	if ($_REQUEST["fechacomp_finalbd"] && $_REQUEST["fechacomp_finalbm"] && $_REQUEST["fechacomp_finalba"])
	{
		$queryDB .= " AND DATE(gc.fecha_compromiso) <= '".$_REQUEST["fechacomp_finalba"]."-".$_REQUEST["fechacomp_finalbm"]."-".$_REQUEST["fechacomp_finalbd"]."'";
	}
}

$queryDB .= " order by fecha_creacion DESC";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["tipo"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["tipo_gestion"]) ?></td>
	<td><?php echo utf8_decode($fila["observacion"]) ?></td>
	<td><?php echo $fila["fecha_compromiso"] ?></td>
	<td><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
</tr>
<?php

}

?>
</table>
