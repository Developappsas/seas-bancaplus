<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=GirosCliente.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
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
	<th>Vr. Solicitado</th>
	<th>Vr. Credito</th>
	<th>Beneficiario</th>
	<th>Nit</th>
	<th>Forma Pago</th>
	<th>Valor Giro</th>
	<th>Banco</th>
	<th>Tipo Cuenta</th>
	<th>Nro Cuenta</th>
	<th>Nro Cheque</th>
	<th>Cuenta Giro</th>
	<th>F Giro</th>
</tr>
<?php

$queryDB = "SELECT si.*, pa.sector, gi.*, ba.nombre as banco, cb.nombre as cuenta_giro from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN giros gi ON si.id_simulacion = gi.id_simulacion AND gi.clasificacion = 'DSC' LEFT JOIN bancos ba ON gi.id_banco = ba.id_banco LEFT JOIN cuentas_bancarias cb ON gi.id_cuentabancaria = cb.id_cuenta where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

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

$queryDB .= " order by si.id_simulacion, gi.id_giro";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
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
	
	$tipo_cuenta = "";
	
	switch ($fila["tipo_cuenta"])
	{
		case "AHO":	$tipo_cuenta = "AHORROS"; break;
		case "CTE":	$tipo_cuenta = "CORRIENTE"; break;
	}
	
	switch ($fila["forma_pago"])
	{
		case "CHE":	$forma_pago = "CHEQUE"; break;
		case "CHG":	$forma_pago = "CHEQUE GERENCIA"; break;
		case "EFE":	$forma_pago = "EFECTIVO"; break;
		case "TRA":	$forma_pago = "TRANSFERENCIA"; break;
	}
	
?>
<tr>
	<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $opcion_desembolso ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo utf8_decode($fila["beneficiario"]) ?></td>
	<td><?php echo $fila["identificacion"] ?></td>
	<td><?php echo $forma_pago ?></td>
	<td><?php echo $fila["valor_girar"] ?></td>
	<td><?php echo $fila["banco"] ?></td>
	<td><?php echo $tipo_cuenta ?></td>
	<td><?php echo utf8_decode($fila["nro_cuenta"]) ?></td>
	<td><?php echo utf8_decode($fila["nro_cheque"]) ?></td>
	<td><?php echo utf8_decode($fila["cuenta_giro"]) ?></td>
	<td><?php echo $fila["fecha_giro"] ?></td>
</tr>
<?php

	$total_opcion_desembolso += $opcion_desembolso;
	$total_valor_credito += $fila["valor_credito"];
	$total_valor_girar += $fila["valor_girar"];
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="5"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_desembolso ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_valor_girar ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
