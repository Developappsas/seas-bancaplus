<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=CuotasNoRecaudadas.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>No. Libranza</th>
	<th>Pagaduria</th>
	<th>No. Cuota</th>
	<th>F Cuota</th>
	<th>Vr Cuota</th>
	<th>Vr Recaudado</th>
	<th>Tipo Causal</th>
	<th>Causal</th>
	<th>Usuario</th>
	<th>Fecha</th>
</tr>
<?php

$queryDB = "SELECT si.cedula, si.nombre, si.nro_libranza, si.pagaduria, cu.cuota, cu.fecha, cu.valor_cuota, CASE WHEN fn_total_recaudado_mes(nr.id_simulacion, 0, cu.fecha) IS NULL THEN 0 ELSE fn_total_recaudado_mes(nr.id_simulacion, 0, cu.fecha) END as recaudado, tcn.nombre as tipo_causal, cnr.nombre as causal, nr.usuario_modificacion, nr.fecha_modificacion from cuotas_norecaudadas nr INNER JOIN simulaciones si ON nr.id_simulacion = si.id_simulacion INNER JOIN cuotas cu ON nr.id_simulacion = cu.id_simulacion AND nr.fecha = cu.fecha LEFT JOIN causales_norecaudo cnr ON nr.id_causal = cnr.id_causal LEFT JOIN tipos_causalesnorecaudo tcn ON cnr.id_tipo = tcn.id_tipo where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

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

if ($_REQUEST["id_causal"])
{
	$queryDB .= " AND nr.id_causal = '".$_REQUEST["id_causal"]."'";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND DATE(cu.fecha) >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND DATE(cu.fecha) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

$queryDB .= " order by si.id_simulacion, cu.cuota";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_assoc($rs))
{

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila["cuota"] ?></td>
	<td><?php echo $fila["fecha"] ?></td>
	<td><?php echo $fila["valor_cuota"] ?></td>
	<td><?php echo $fila["recaudado"] ?></td>
	<td><?php echo utf8_decode($fila["tipo_causal"]) ?></td>
	<td><?php echo utf8_decode($fila["causal"]) ?></td>
	<td><?php echo utf8_decode($fila["usuario_modificacion"]) ?></td>
	<td><?php echo $fila["fecha_modificacion"] ?></td>
</tr>
<?php

}

?>
</table>
