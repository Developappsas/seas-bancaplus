<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Seguro.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Tipo ID</th>
	<th>Documento</th>
	<th>F Nacimiento</th>
	<th>Edad</th>
	<th>G&eacute;nero</th>
	<th>Primer Apellido</th>
	<th>Segundo Apellido</th>
	<th>Nombre</th>
	<th>Valor Aegurado</th>
	
	<th>Porcentaje Extraprima</th>
	<th>Num. Cr&eacute;dito</th>
	<th>Tipo Cartera</th>
	<th>F Desembolso</th>
	<th>Observaciones</th>
	<th>Formulario Seguro</th>
</tr>
<?php
if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	
	$fecha_corte_query = "'".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}
else
{
	
	$fecha_corte_query = "GETDATE()";
}

$queryDB = "SELECT so.tipo_documento, si.cedula, si.fecha_nacimiento, '' as edad, so.sexo, so.apellido1, so.apellido2, so.nombre1, so.nombre2, si.valor_credito, si.porcentaje_extraprima, si.nro_libranza, 'LIB' as tipo_cartera, si.fecha_tesoreria, '' as observaciones, CASE WHEN si.formulario_seguro = 1 THEN 'SI' ELSE 'NO' END as formulario_seguro_x from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"])
{
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"])
{
	$queryDB .= " AND si.fecha_tesoreria >= '".$_REQUEST["fechades_inicialba"]."-".$_REQUEST["fechades_inicialbm"]."-".$_REQUEST["fechades_inicialbd"]."'";
}

if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"])
{
	$queryDB .= " AND si.fecha_tesoreria <= '".$_REQUEST["fechades_finalba"]."-".$_REQUEST["fechades_finalbm"]."-".$_REQUEST["fechades_finalbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
	{
		$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
	}

$queryDB .= " order by si.fecha_creacion, si.nombre, si.cedula";

$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{

?>
<tr>
	<td><?php echo $fila["tipo_documento"] ?></td>
	<td><?php echo $fila["cedula"] ?></td>
	<td><?php echo $fila["fecha_nacimiento"] ?></td>
	<td><?php echo $fila["edad"] ?></td>
	<td><?php echo $fila["sexo"] ?></td>
	<td><?php echo utf8_decode($fila["apellido1"]) ?></td>
	<td><?php echo utf8_decode($fila["apellido2"]) ?></td>
	<td><?php echo utf8_decode($fila["nombre1"]." ".$fila["nombre2"]) ?></td>
	<td><?php echo $fila["valor_credito"] ?></td>
	<td><?php echo $fila["porcentaje_extraprima"] ?></td>
	<td><?php echo $fila["nro_libranza"] ?></td>
	<td><?php echo $fila["tipo_cartera"] ?></td>
	<td><?php echo $fila["fecha_tesoreria"] ?></td>
	<td><?php echo $fila["observaciones"] ?></td>
	<td><?php echo $fila["formulario_seguro_x"] ?></td>
</tr>
<?php

}

?>
</table>
