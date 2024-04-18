<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Inventario_titulos.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_TIPO"] != "TESORERIA"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
    <th>ID Simulacion</th>
	<th>LBZ</th>
	<th>Comprador</th>
    <th>Unidad Negocio</th>

    <th>Cedula</th>
	<th>Nombre</th>
	
	<th>Pagaduria</th>
    <th>Comercial</th>
	<th>Oficina</th>
	<th>Valor Credito</th>
	<th>Valor Credito Menos Retanqueos</th>
	<th>Valor Desembolso Cliente</th>
    <th>Valor Desembolso Cliente Pagado</th>
    <th>Valor Desembolso Cliente Pendiente</th>
    <th>Valor Compra Cartera</th>
    <th>Valor Cartera Pagado</th>
    <th>Valor Cartera Pendiente</th>
	

</tr>
<?php
$queryDB = "SELECT si.nro_libranza,un.nombre as unidad_negocio,sub.nombre as subestado2,CASE WHEN uc.freelance=1 or uc.outsourcing=1 THEN 'TERCEROS' ELSE 'PLANTA' END AS tipo_comercial, FORMAT(si.fecha_radicado,'Y%m-d') as fecha_radicacion, FORMAT(si.fecha_radicado,'H:i') as hora_radicacion,si.*,ofi.nombre as oficina,CONCAT(uc.nombre,' ',uc.apellido) as nombre_comercial, vex.comprador
FROM simulaciones si 
LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre 
LEFT JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
LEFT JOIN usuarios uc ON uc.id_usuario=si.id_comercial 
LEFT JOIN unidades_negocio un ON un.id_unidad=si.id_unidad_negocio
LEFT JOIN subestados sub ON sub.id_subestado=si.id_subestado 
LEFT JOIN (select si.id_simulacion, co.nombre as comprador from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion
where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";
$val=0;

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND FORMAT(si.fecha_tesoreria,'Y-m-d') >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND FORMAT(si.fecha_tesoreria,'Y-m-d') <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
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
	
	$consultarComprasCartera="SELECT SUM(valor_pagar) as valor_compras_cartera FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' and se_compra='SI'";
    $queryComprasCartera=sqlsrv_query($link, $consultarComprasCartera);
    $resComprasCartera=sqlsrv_fetch_array($queryComprasCartera);


    $consultarComprasCarteraPagado="SELECT SUM(valor_girar) as valor_pagado_compras_cartera FROM giros WHERE id_simulacion='".$fila["id_simulacion"]."' and clasificacion='CCA'";
    $queryComprasCarteraPagado=sqlsrv_query($link, $consultarComprasCarteraPagado);
    $resComprasCarteraPagado=sqlsrv_fetch_array($queryComprasCarteraPagado);

    $consultarDesembolsoPagado="SELECT SUM(valor_girar) as valor_pagado_desembolso FROM giros WHERE id_simulacion='".$fila["id_simulacion"]."' and clasificacion='DSC'";
    $queryDesembolsoPagado=sqlsrv_query($link, $consultarDesembolsoPagado);
    $resDesembolsoPagado=sqlsrv_fetch_array($queryDesembolsoPagado);
    
	
	
			?>
			<tr>
				<td><?php echo  $fila["id_simulacion"]; ?></td>
				<td><?php echo  $fila["nro_libranza"]; ?></td>
				<td><?php echo $fila["comprador"]; ?></td>
				<td><?php echo $fila["unidad_negocio"]; ?></td>
				<td><?php echo $fila["cedula"]; ?></td>
				<td><?php echo utf8_decode($fila["nombre"]); ?></td>
				<td><?php echo utf8_decode($fila["pagaduria"]); ?></td>
				<td><?php echo utf8_decode($fila["nombre_comercial"]); ?></td>
				<td><?php echo utf8_decode($fila["oficina"]); ?></td>
				<td><?php echo $fila["valor_credito"]; ?></td>
				<td><?php echo $sin_retanqueos; ?></td>
				<td><?php echo $fila["desembolso_cliente"];?></td>
                <td><?php echo $resDesembolsoPagado["valor_pagado_desembolso"];?></td>
                <td><?php echo $fila["desembolso_cliente"]-$resDesembolsoPagado["valor_pagado_desembolso"];?></td>
                <td><?php echo $resComprasCartera["valor_compras_cartera"];?></td>
				<td><?php echo $resComprasCarteraPagado["valor_pagado_compras_cartera"];?></td>
				<td><?php echo $resComprasCartera["valor_compras_cartera"]-$resComprasCarteraPagado["valor_pagado_compras_cartera"];?></td>
				
			</tr>
			<?php
		
			
		
			
	//	}
		
	//  }
?>

    <?php
}
?>
    
</table>
