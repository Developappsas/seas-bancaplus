<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte_Contable.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Id simulacion</th>
	<th>Libranza</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Id Transaccion</th>
	<th>Cod Transaccion</th>
	<th>Cod Transaccion Previa</th>
	<th>Fecha</th>
	<th>Valor de credito</th>
	<th>Detalle</th>
	<th>Observacion</th>
	<th>Debito</th>
	<th>Credito</th>
	<th>Libranza retanqueo</th>
	<th>Entidad CXC</th>
	<th>Estado</th>
	<th>SubEstado</th>
	<th>Origen</th>
	<th>Usuario de creacion</th>
</tr>
<?php

$queryDB = "SELECT ct.cod_transaccion, ct.cod_transaccion_previa, CONCAT(u.nombre, ' ', u.apellido) AS usuario_creacion, si.id_simulacion, si.nombre AS nombre_completo, si.cedula, si.pagaduria, si.estado, su.nombre AS subestado, ct.id_transaccion,  cot.nombre AS origen, ct.fecha AS Fecha_con_tran, ct.valor AS valor_credito, ct.observacion, ctm.id_simulacion_retanqueo, sr.nro_libranza AS nro_libranza_retanqueo , ed.nombre As Nombre_entidad, ctm.auxiliar, ctm.debito, ctm.credito, ctm.observacion AS observacion_movimiento, si.nro_libranza
	FROM contabilidad_transacciones ct 
	LEFT JOIN simulaciones si ON si.id_simulacion = ct.id_simulacion 
	LEFT JOIN subestados su ON su.id_subestado = si.id_subestado 
	LEFT JOIN contabilidad_transacciones_movimientos ctm ON ctm.id_transaccion = ct.id_transaccion 
	LEFT JOIN contabilidad_origenes_transaccion cot ON cot.id_origen = ct.id_origen 
	LEFT JOIN entidades_desembolso ed ON ed.id_entidad = ctm.id_entidad
	LEFT JOIN usuarios u ON ct.usuario_creacion = u.login
	LEFT JOIN simulaciones sr ON sr.id_simulacion = ctm.id_simulacion_retanqueo
	WHERE 1=1 ";

if ($_REQUEST["id_simulacion"]){
	$queryDB .= " AND si.id_simulacion = '".$_REQUEST["id_simulacion"]."' ";
}

if ($_REQUEST["cedula"]){
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

$rs = sqlsrv_query($link, $queryDB);

if ($_REQUEST["pagaduria"]) {
	$queryDB .= " AND si.pagaduria = '" . $_REQUEST["pagaduria"] . "'";
}

if ($_REQUEST["estado"]) {
	$queryDB .= " AND si.estado = '" . $_REQUEST["estado"] . "'";
}

if ($_REQUEST["id_subestado"]) {
	$queryDB .= " AND si.id_subestado = '" . $_REQUEST["id_subestado"] . "'";
}

if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"]) {
	$queryDB .= " AND FORMAT(si.fecha_cartera,'Y-m') >= '" . $_REQUEST["fechacartera_inicialba"] . "-" . $_REQUEST["fechacartera_inicialbm"] . "'";
}

if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"]) {
	$queryDB .= " AND FORMAT(si.fecha_cartera,'Y-m') <= '" . $_REQUEST["fechacartera_finalba"] . "-" . $_REQUEST["fechacartera_finalbm"] . "'";
}

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){ ?>
	<tr>
		<td><?php echo $fila["id_simulacion"] ?></td>
		<td><?php echo $fila["nro_libranza"] ?></td>
		<td><?php echo $fila["cedula"] ?></td>
		<td><?php echo $fila["nombre_completo"] ?></td>
		<td><?php echo $fila["id_transaccion"] ?></td>
		<td><?php echo $fila["cod_transaccion"] ?></td>
		<td><?php echo $fila["cod_transaccion_previa"] ?></td>
		<td><?php echo utf8_decode($fila["Fecha_con_tran"]) ?></td>
		<td><?php echo $fila["valor_credito"] ?></td>
		<td><?php echo $fila["auxiliar"] ?></td>
		<td><?php echo $fila["observacion_movimiento"] ?></td>
		<td><?php echo $fila["debito"] ?></td>
		<td><?php echo $fila["credito"] ?></td>
		<td><?php echo $fila["nro_libranza_retanqueo"] ?></td>
		<td><?php echo $fila["Nombre_entidad"] ?></td>
		<td><?php echo $fila["estado"] ?></td>
		<td><?php echo $fila["subestado"] ?></td>
		<td><?php echo $fila["origen"] ?></td>
		<td><?php echo $fila["usuario_creacion"] ?></td>
	</tr>
	<?php
}
?>
</table>