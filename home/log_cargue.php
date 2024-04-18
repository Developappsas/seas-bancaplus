<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=LogCargue.xls");
header("Pragma: no-cache");
header("Expires: 0");

if ($_REQUEST["archivo"] == "bas")
{

?>
<table border="0">
<tr>
	<th>Estado Cargue</th>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Pagaduria</th>
	<th>Ciudad</th>
	<th>Institucion</th>
	<th>Salario Basico</th>
	<th>Nivel Educativo</th>
	<th>Embargado</th>
	<th>F Nacimiento</th>
	<th>Direccion</th>
	<th>Telefono</th>
	<th>Email</th>
</tr>
<?php

	$nuevos = sqlsrv_query($link, "SELECT emp.*, CASE WHEN emb.c IS NOT NULL THEN 'SI' ELSE 'NO' END as embargo_actual from empleados emp LEFT JOIN (select cedula, pagaduria, count(*) as c from embargos where fechafin IS NULL group by cedula, pagaduria) as emb ON emb.cedula = emp.cedula AND emb.pagaduria = emp.pagaduria where emp.estado_cargue = '1' and emp.pagaduria = '".$_REQUEST["pagaduriabas"]."' order by emp.nombre, emp.cedula");
	
	while ($fila = sqlsrv_fetch_array($nuevos))
	{
	
?>
<tr>
	<td>NUEVO</td>
	<td><?php echo utf8_decode($fila["cedula"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["ciudad"]) ?></td>
	<td><?php echo utf8_decode($fila["institucion"]) ?></td>
	<td><?php echo $fila["salario_basico"] ?></td>
	<td><?php echo utf8_decode($fila["nivel_educativo"]) ?></td>
	<td><?php echo $fila["embargo_actual"] ?></td>
	<td><?php echo $fila["fecha_nacimiento"] ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo utf8_decode($fila["mail"]) ?></td>
</tr>
<?php

	}
	
	$no_actualizados = sqlsrv_query($link, "SELECT emp.*, CASE WHEN emb.c IS NOT NULL THEN 'SI' ELSE 'NO' END as embargo_actual from empleados emp LEFT JOIN (select cedula, pagaduria, count(*) as c from embargos where fechafin IS NULL group by cedula, pagaduria) as emb ON emb.cedula = emp.cedula AND emb.pagaduria = emp.pagaduria where emp.estado_cargue = '0' and emp.pagaduria = '".$_REQUEST["pagaduriabas"]."' order by emp.nombre, emp.cedula");
	
	while ($fila = sqlsrv_fetch_array($no_actualizados))
	{
	
?>
<tr>
	<td>YA NO EXISTE</td>
	<td><?php echo utf8_decode($fila["cedula"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre"]) ?></td>
	<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["ciudad"]) ?></td>
	<td><?php echo utf8_decode($fila["institucion"]) ?></td>
	<td><?php echo $fila["salario_basico"] ?></td>
	<td><?php echo utf8_decode($fila["nivel_educativo"]) ?></td>
	<td><?php echo $fila["embargo_actual"] ?></td>
	<td><?php echo $fila["fecha_nacimiento"] ?></td>
	<td><?php echo utf8_decode($fila["direccion"]) ?></td>
	<td><?php echo utf8_decode($fila["telefono"]) ?></td>
	<td><?php echo utf8_decode($fila["mail"]) ?></td>
</tr>
<?php

	}
	
?>
</table>
<?php

}

if ($_REQUEST["archivo"] == "car")
{

?>
<table border="0">
<tr>
	<th>Cedula</th>
	<th>Vr Credito</th>
	<th>Plazo</th>
	<th>Tasa</th>
	<th>Vr Cuota</th>
	<th>Seguro</th>
	<th>F Prod</th>
	<th>F Primera Cuota</th>
	<th>Observacion</th>
</tr>
<?php

	$rs = sqlsrv_query($link, "select * from tmp_cartera order by consecutivo");
	
	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$observacion = "";		
		$rs1 = sqlsrv_query($link, "select id_simulacion, valor_credito, plazo, tasa_interes, opcion_credito, opcion_cuota_cli, opcion_cuota_ccc, opcion_cuota_cmp, opcion_cuota_cso, estado from simulaciones where cedula = '".$fila["cedula"]."' AND estado IN ('EST', 'DES', 'CAN')");
		if (sqlsrv_num_rows($rs1)) {
			if (sqlsrv_num_rows($rs1) > 1) {
				$rs2 = sqlsrv_query($link, "select id_simulacion, valor_credito, plazo, tasa_interes, opcion_credito, opcion_cuota_cli, opcion_cuota_ccc, opcion_cuota_cmp, opcion_cuota_cso, estado from simulaciones where cedula = '".$fila["cedula"]."' AND estado IN ('EST', 'DES', 'CAN') AND valor_credito = '".$fila["valor_credito"]."'");
				
				if (sqlsrv_num_rows($rs2))
					$fila1 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				else
					$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			} else {
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			}
			
			if ($fila1["estado"] == "EST")
				$observacion .= "Credito no desembolsado. ";
			
			if ($fila1["valor_credito"] != $fila["valor_credito"])
				$observacion .= "No coincide Vr Credito. ";
			
			if ($fila1["plazo"] != $fila["plazo"])
				$observacion .= "No coincide Plazo. ";
			
			if ($fila1["tasa_interes"] != $fila["tasa_interes"])
				$observacion .= "No coincide Tasa. ";
			
			switch($fila1["opcion_credito"])
			{
				case "CLI":	$opcion_cuota = $fila1["opcion_cuota_cli"];
							break;
				case "CCC":	$opcion_cuota = $fila1["opcion_cuota_ccc"];
							break;
				case "CMP":	$opcion_cuota = $fila1["opcion_cuota_cmp"];
							break;
				case "CSO":	$opcion_cuota = $fila1["opcion_cuota_cso"];
							break;
			}
			
			if ($opcion_cuota != $fila["opcion_cuota"])
				$observacion .= "No coincide Vr Cuota. ";
			
			if (!$observacion)
				$observacion = "OK";
		}
		else
		{
			$rs2 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila["cedula"]."'");
			
			if (sqlsrv_num_rows($rs2))
				$observacion = "Credito no desembolsado";
			else
				$observacion = "Credito no encontrado";
		}
		
?>
<tr>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $fila["plazo"] ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo $fila["opcion_cuota"] ?></td>
	<td><?php echo $fila["seguro"] ?></td>
	<td><?php echo $fila["fecha_produccion"] ?></td>
	<td><?php echo $fila["fecha_primera_cuota"] ?></td>
	<td><?php echo $observacion ?></td>
</tr>
<?php

	}
}

?>
