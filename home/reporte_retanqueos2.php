<?php 

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Retanqueos.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
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
	<th>No. Libranza</th>
	<th>Vr. Solicitado</th>
	<th>Vr. Credito</th>
	<th>Saldo Capital</th>
	<th>Comprador</th>
	<th>Vr. Retanqueo</th>
	<th>Desembolsado</th>
	<th>Pendiente</th>
</tr>
<?php

$queryDB = "SELECT si.*, pa.sector from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")) AND si.estado_tesoreria != 'CER' AND si.retanqueo_total > 0";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%')";
}

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

$queryDB .= " order by si.fecha_tesoreria, si.id_simulacion";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	for ($i = 1; $i <=3; $i++)
	{
		if ($fila["retanqueo".$i."_valor"] > 0)
		{
			$rs2 = sqlsrv_query($link, "SELECT si.*, vex.comprador from simulaciones si LEFT JOIN (select si.id_simulacion, co.nombre as comprador from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion where si.nro_libranza = '".$fila["retanqueo".$i."_libranza"]."'");
			
			$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
			
			switch($fila2["opcion_credito"])
			{
				case "CLI":	$opcion_desembolso = $fila2["opcion_desembolso_cli"];
							break;
				case "CCC":	$opcion_desembolso = $fila2["opcion_desembolso_ccc"];
							break;
				case "CMP":	$opcion_desembolso = $fila2["opcion_desembolso_cmp"];
							break;
				case "CSO":	$opcion_desembolso = $fila2["opcion_desembolso_cso"];
							break;
			}
			
			$rs1 = sqlsrv_query($link, "SELECT SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN IF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as s from cuotas where id_simulacion = '".$fila2["id_simulacion"]."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$capital_recaudado = $fila1["s"];
			
			$saldo_capital = $fila2["valor_credito"] - $capital_recaudado;
			
			$queryDB1 = "SELECT SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'RET' and fecha_giro IS NOT NULL";
			
			$rs1 = sqlsrv_query($link, $queryDB1);
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$giros_realizados_ret = $fila1["s"];
			
			$saldo_girar_ret = round($fila["retanqueo_total"]) - $giros_realizados_ret;
			
?>
<tr>
	<td><?php echo $fila["id_simulacion"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["sector"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $fila2["nro_libranza"] ?></td>
	<td><?php echo $opcion_desembolso ?></td>
	<td><?php echo $fila2["valor_credito"] ?></td>
	<td><?php echo $saldo_capital ?></td>
	<td><?php echo utf8_decode($fila2["comprador"]) ?></td>
	<td><?php echo $fila["retanqueo".$i."_valor"] ?></td>
	<td><?php echo number_format($giros_realizados_ret, 0, "", "") ?></td>
	<td><?php echo number_format($saldo_girar_ret, 0, "", "") ?></td>
</tr>
<?php

			$total_opcion_desembolso += $opcion_desembolso;
			$total_valor_credito += $fila2["valor_credito"];
			$total_saldo_capital += $saldo_capital;
			$total_valor_retanqueo += $fila["retanqueo".$i."_valor"];
			$total_giros_realizados_ret += round($giros_realizados_ret);
			$total_saldo_girar_ret += round($saldo_girar_ret);
		}
	}
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="7"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_desembolso ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td><b><?php echo $total_saldo_capital ?></b></td>
	<td><b><?php echo $total_valor_retanqueo ?></b></td>
	<td><b><?php echo $total_giros_realizados_ret ?></b></td>
	<td><b><?php echo $total_saldo_girar_ret ?></b></td>
</tr>
</table>
