<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Incorporacion.xls");
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
	<th>No. Incorporacion</th>
	<th>Valor Cuota</th>
	<th>Observacion</th>
	<th>Estado</th>
	<th>Usuario Creacion</th>
	<th>Fecha Creacion</th>
	<th>Usuario Apr/Neg</th>
	<th>Fecha Apr/Neg</th>
</tr>
<?php

$queryDB = "SELECT si.cedula, si.nombre, si.nro_libranza, sinc.* from simulaciones_incorporacion sinc INNER JOIN simulaciones si ON sinc.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where 1 = 1";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["estado"])
{
	$queryDB .= " AND sinc.estado = '".$_REQUEST["estado"]."'";
}

$queryDB .= " order by si.id_simulacion DESC, sinc.id_incorporacion";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["nro_incorporacion"]) ?></td>
	<td><?php echo $fila["valor_cuota"] ?></td>
	<td><?php echo utf8_decode($fila["observacion"]) ?></td>
	<td><?php echo $fila["estado"] ?></td>
	<td><?php echo utf8_decode($fila["usuario_creacion"]) ?></td>
	<td><?php echo $fila["fecha_creacion"] ?></td>
	<td><?php echo utf8_decode($fila["usuario_modificacion"]) ?></td>
	<td><?php echo $fila["fecha_modificacion"] ?></td>
</tr>
<?php

}

?>
</table>
