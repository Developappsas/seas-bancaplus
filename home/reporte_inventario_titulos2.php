<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Inventario_titulos.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
    <th>ID Simulacion</th>
    <th>Unidad Negocio</th>

    <th>Cedula</th>
	<th>Nombre</th>
	
	<th>Pagaduria</th>
    <th>Comercial</th>
	<th>Oficina</th>
	<th>Valor Credito</th>
	<th>Valor Credito Menos Retanqueos</th>
	<th>Estado</th>
    <th>Fecha</th>
    <th>Estado Inventario</th>
    <th>Analista</th>
	<th>Legajo</th>
	<th>Observacion</th>

</tr>
<?php
$queryDB = "SELECT ic.legajo,un.nombre as unidad_negocio,sub.nombre as subestado2,CASE WHEN uc.freelance=1 or uc.outsourcing=1 THEN 'TERCEROS' ELSE 'PLANTA' END AS tipo_comercial,FORMAT(si.fecha_radicado,'Y-m-d') as fecha_radicacion, FORMAT(si.fecha_radicado,'H:i') as hora_radicacion,si.*,ofi.nombre as oficina,CONCAT(uc.nombre,' ',uc.apellido) as nombre_comercial,ti.descripcion as estado_inventario,CONCAT(ua.nombre,' ',ua.apellido) as nombre_analista,ic.fecha as fecha_inventario,ic.observacion
FROM simulaciones si 
LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre 
LEFT JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
LEFT JOIN usuarios uc ON uc.id_usuario=si.id_comercial 
LEFT JOIN unidades_negocio un ON un.id_unidad=si.id_unidad_negocio
LEFT JOIN subestados sub ON sub.id_subestado=si.id_subestado 
LEFT JOIN inventario_creditos ic ON ic.id_simulacion=si.id_simulacion 
LEFT JOIN tipificacion_inventario_creditos ti ON ti.id_tipificacion_credito=ic.estado
LEFT JOIN usuarios ua ON ua.id_usuario=ic.id_usuario
WHERE ic.vigente='s'";
$val=0;

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND FORMAT(ic.fecha,'Y-m-d') >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND FORMAT(ic.fecha,'Y-m-d') <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}




//echo $queryDB;
$rs = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$val_reg=0;
	

    


    switch ($fila["estado"])
	{
		case "ING":	$estado = "INGRESADO"; break;
		case "EST":	$estado = "EN ESTUDIO"; break;
		case "NEG":	$estado = "NEGADO"; break;
		case "DST":	$estado = "DESISTIDO"; break;
		case "DSS":	$estado = "DESISTIDO SISTEMA"; break;
		case "DES":	$estado = "DESEMBOLSADO"; break;
		case "CAN":	$estado = "CANCELADO"; break;
		case "ANU":	$estado = "ANULADO"; break;
	}


	$sin_retanqueos=0;
	$opcion_desembolso_cli=$fila["opcion_desembolso_cli"];
	$opcion_desembolso_ccc=$fila["opcion_desembolso_ccc"];
	$opcion_desembolso_cmp=$fila["opcion_desembolso_cmp"];
	$opcion_desembolso_cso=$fila["opcion_desembolso_cso"];
	$retanqueo_total=$fila["retanqueo_total"];
	switch($fila["opcion_credito"])
	{
		case "CLI":	$sin_retanqueos = $opcion_desembolso_cli; break;
		case "CCC":	$sin_retanqueos = number_format($opcion_desembolso_ccc - $retanqueo_total, 0, ".", ","); break;
		case "CMP":	$sin_retanqueos = number_format($opcion_desembolso_cmp - $retanqueo_total, 0, ".", ","); break;
		case "CSO":	$sin_retanqueos = number_format($opcion_desembolso_cso - $retanqueo_total, 0, ".", ","); break;
	}
	
	//$consultarSiguienteEstado="SELECT * FROM simulaciones_fdc where id_simulacion=".$fila["id_simulacion"]." and id>".$fila["id_sfdc"]." ORDER BY id desc limit 1"; 
	//$querySiguienteEstado=sqlsrv_query($link, $consultarSiguienteEstado);
	//if (mysql_num_rows($querySiguienteEstado)>0)
	//{
	//	$resSiguienteEstado=sqlsrv_fetch_array($querySiguienteEstado);
	//	if ($resSiguienteEstado["estado"]==4)
	//	{
	
	
			?>
			<tr>
				<td><?php echo  $fila["id_simulacion"]; ?></td>
				<td><?php echo $fila["unidad_negocio"]; ?></td>
				<td><?php echo $fila["cedula"]; ?></td>
				<td><?php echo utf8_decode($fila["nombre"]); ?></td>
				<td><?php echo utf8_decode($fila["pagaduria"]); ?></td>
				<td><?php echo utf8_decode($fila["nombre_comercial"]); ?></td>
				<td><?php echo utf8_decode($fila["oficina"]); ?></td>
				<td><?php echo $fila["valor_credito"]; ?></td>
				<td><?php echo $sin_retanqueos; ?></td>
				<td><?php echo $fila["subestado2"];?></td>
                <td><?php echo $fila["fecha_inventario"];?></td>
                <td><?php echo $fila["estado_inventario"];?></td>
                <td><?php echo $fila["nombre_analista"];?></td>
				<td><?php echo $fila["legajo"];?></td>
				<td><?php echo $fila["observacion"];?></td>
				
			</tr>
			<?php
		
			
		
			
	//	}
		
	//  }
?>

    <?php
}
?>
    
</table>
