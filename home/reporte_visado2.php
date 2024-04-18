<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Visado.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>

<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
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
	<th>Tipo Visado</th>
	<th>Valor Visado</th>
	<th>Visador</th>
	<th>Observacion</th>
	<th>Usuario</th>
	<th>Fecha</th>
</tr>
<?php

$queryDB = "SELECT si.cedula, si.nombre, si.nro_libranza, vi.nombre as visador, sv.* from simulaciones_visado sv INNER JOIN simulaciones si ON sv.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN visadores vi ON sv.id_visador = vi.id_visador where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["tipo_visado"])
{
	$queryDB .= " AND sv.tipo_visado = '".$_REQUEST["tipo_visado"]."'";
}

if ($_REQUEST["id_visador"])
{
	$queryDB .= " AND sv.id_visador = '".$_REQUEST["id_visador"]."'";
}

$queryDB .= " order by sv.id_visado";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["tipo_visado"] ?></td>
	<td><?php echo $fila["valor_visado"] ?></td>
	<td><?php echo utf8_decode($fila["visador"]) ?></td>
	<td><?php echo utf8_decode($fila["observacion"]) ?></td>
	<td><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
</tr>
<?php

}

?>
</table>
