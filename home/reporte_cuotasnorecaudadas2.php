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
	<th>Vr Cuota</th>
<?php

$fecha_recaudo_tmp = "2021-01-01";

$fecha_recaudo = new DateTime($fecha_recaudo_tmp);

$fecha_final = new DateTime(date('Y-m-01'));	

while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m'))
{

?>
	<th><?php echo $fecha_recaudo->format('Y-m') ?></th>
	<th>Tipo Causal</th>
	<th>Causal</th>
<?php

	$fecha_recaudo->add(new DateInterval('P1M'));
}

?>
</tr>
<?php

$queryDB = "SELECT DISTINCT si.id_simulacion, si.cedula, si.nombre, si.nro_libranza, si.pagaduria, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.fecha_primera_cuota from simulaciones si INNER JOIN cuotas_norecaudadas nr ON si.id_simulacion = nr.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where 1 = 1";

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

$queryDB .= " order by si.id_simulacion";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}

?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo $opcion_cuota ?></td>
<?php



	$queryDB = "SELECT FORMAT(cu.fecha, 'yyyy-MM') as fecha, CASE WHEN dbo.fn_total_recaudado_mes(cu.id_simulacion, 0, cu.fecha) IS NULL THEN 0 ELSE dbo.fn_total_recaudado_mes(cu.id_simulacion, 0, cu.fecha) END as valor_recaudado, tcn.nombre as tipo_causal, cnr.nombre as causal from cuotas cu LEFT JOIN cuotas_norecaudadas nr ON cu.id_simulacion = nr.id_simulacion AND cu.fecha = nr.fecha LEFT JOIN causales_norecaudo cnr ON nr.id_causal = cnr.id_causal LEFT JOIN tipos_causalesnorecaudo tcn ON cnr.id_tipo = tcn.id_tipo where cu.id_simulacion = '".$fila["id_simulacion"]."' AND cu.fecha > '2021-01-01' AND cu.fecha < '".date('Y-m-01')."' order by FORMAT(cu.fecha, 'yyyy-MM')";

	

	$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	if ($rs1 == false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			 foreach( $errors as $error ) {
					echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
					echo "code: ".$error[ 'code']."<br />";
					echo "message: ".$error[ 'message']."<br />";
				}
			 }
		}
	

	if (sqlsrv_num_rows($rs1))
	{
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
		$fecha = $fila1["fecha"];
		
	}
	
	$j = 0;
	
	$fecha_recaudo_tmp = "2021-01-01";

	$fecha_recaudo = new DateTime($fecha_recaudo_tmp);

	$fecha_final = new DateTime(date('Y-m-01'));


	while ($fecha_recaudo->format('Y-m') != $fecha_final->format('Y-m'))
	{
		
		if ($fecha_recaudo->format('Y-m') == $fecha)
		{
			$valor_recaudado = $fila1["valor_recaudado"];
			
			$tipo_causal = $fila1["tipo_causal"];
			
			$causal = $fila1["causal"];
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$fecha = $fila1["fecha"];
		}
		else
		{
			$valor_recaudado = "";
			$tipo_causal = "";
			$causal = "";
		}
		
?>
	<td><?php echo $valor_recaudado ?></td>
	<td><?php echo utf8_decode($tipo_causal) ?></td>
	<td><?php echo utf8_decode($causal) ?></td>
<?php

		if ($valor_recaudado)
			$total_valor_recaudado[$j] += $valor_recaudado;
		
		$fecha_recaudo->add(new DateInterval('P1M'));
		
		$j = $j + 3;
	}

?>
</tr>
<?php

}

?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="5"><b>TOTALES</b></td>
<?php

for ($i = 0; $i < $j; $i++)
{

?>
	<td><b><?php echo $total_valor_recaudado[$i] ?></b></td>
<?php

}

?>
</tr>
</table>
