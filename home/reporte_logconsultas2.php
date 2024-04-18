<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ConsultasRealizadas.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD") || !$_SESSION["FUNC_LOGCONSULTAS"])
{
	exit;
}

$link = conectar();



$todas_las_unidades = "'0'";

$rs1 = sqlsrv_query($link, "SELECT id_unidad from unidades_negocio order by id_unidad");

while ($fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC))
	$todas_las_unidades .= ", '".$fila1["id_unidad"]."'";

?>
<table border="0">
<tr>
	<th>Usuario</th>
	<th>Oficina</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Sector</th>
	<th>Pagaduria</th>
	<th>Ciudad</th>
	<th>Institucion</th>
	<th>F Consulta</th>
</tr>
<?php

$queryDB = "SELECT DISTINCT lc.cedula, lc.id_consulta, lc.nombre, lc.pagaduria, lc.ciudad, lc.institucion, lc.fecha_creacion, us.nombre as nombre_usuario, us.apellido, pa.sector from log_consultas lc INNER JOIN usuarios us ON lc.id_usuario = us.id_usuario LEFT JOIN usuarios_unidades uu on us.id_usuario = uu.id_usuario LEFT JOIN pagadurias pa ON lc.pagaduria = pa.nombre where 1 = 1 AND us.tipo <> 'MASTER'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND (pa.sector = '".$_SESSION["S_SECTOR"]."' OR (pa.sector IS NULL AND us.sector = '".$_SESSION["S_SECTOR"]."'))";
}

$queryDB .= " AND (uu.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_IDUNIDADNEGOCIO"] == $todas_las_unidades)
	$queryDB .= " OR uu.id_unidad_negocio IS NULL";

$queryDB .= ")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND us.tipo = 'COMERCIAL' AND us.id_usuario IN (SELECT id_usuario FROM oficinas_usuarios where id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."'))";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')"; //AND si.telemercadeo = '0'";
	
	//if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
	//	$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1')"; //AND si.telemercadeo = '0'";
	
	//if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
	//	$queryDB .= " AND si.telemercadeo = '1'";
}
	
if ($_REQUEST["id_usuario"])
{
	$queryDB .= " AND lc.id_usuario = '".$_REQUEST["id_usuario"]."'";
}

if ($_REQUEST["id_oficina"])
{
	$queryDB .= " AND lc.id_usuario IN (select id_usuario from oficinas_usuarios where id_oficina = '".$_REQUEST["id_oficina"]."')";
}

if ($_REQUEST["sector"])
{
	$queryDB .= " AND (pa.sector = '".$_REQUEST["sector"]."' OR (pa.sector IS NULL AND us.sector = '".$_REQUEST["sector"]."'))";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND lc.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["ciudad"])
{
	$queryDB .= " AND lc.ciudad = '".$_REQUEST["ciudad"]."'";
}

if ($_REQUEST["institucion"])
{
	$queryDB .= " AND lc.institucion = '".$_REQUEST["institucion"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND DATE(lc.fecha_creacion) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND DATE(lc.fecha_creacion) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

$queryDB .= " order by lc.id_consulta";
$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	

?>
<tr>
	<td><?php echo utf8_decode($fila["nombre_usuario"]." ".$fila["apellido"]) ?></td>
	<td></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["ciudad"]) ?></td>
	<td><?php echo utf8_decode($fila["institucion"]) ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
</tr>
<?php

}

?>
</table>
