<?php 
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Originacion.xls");
header("Pragma: no-cache");
header("Expires: 0");
include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD"))
{
	exit;
}

$link = conectar();



?>
<table border="0">
<tr>
	<th>Cedula</th>
	<th>Nombre</th>
	<th>Pagaduria</th>
	<th>Unidad de Negocio</th>
	<th>Tasa</th>
	<th>Plazo</th>
	<th>Cuota</th>
	<th>Vl.Credito</th>
	<th>Vl.Desembolso</th>
	<th>Vl.Desembolso Menos Retanqueos</th>
	<th>%</th>
	<th>Intereses Anticipados/Aval</th>
	<th>%</th>
	<th>Base Asesoría Financiera</th>
	<th>Servicio Nube</th>
	<th>Asesoría Financiera</th>
	<th>Asesoría Financiera (Vr)</th>
	<th>%</th>
	<th>IVA</th>
	<th>%</th>
	<th>GMF</th>
<?php

$descuentos_adicionales = sqlsrv_query($link, "SELECT * from descuentos_adicionales order by pagaduria, id_descuento");

while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){

?>
	<th>%</th>
	<th><?php echo $fila1["nombre"] ?></th>
<?php

}

?>
	<th>%</th>
	<th>Comisi&oacute;n por Venta (Retanqueos)</th>
	<th>%</th>
	<th>IVA (Comisi&oacute;n por Venta)</th>
	<th>Transferencia</th>
	<th>Mes Produccion</th>
</tr>
<?php

$queryDB = "SELECT si.*, un.nombre as unidad_negocio, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario where si.id_simulacion IS NOT NULL";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION")
{
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";
	
	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";
	
	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
}

if ($_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$fechaprod_inicialbm = $_REQUEST["fecha_inicialbm"];
	
	$fechaprod_inicialba = $_REQUEST["fecha_inicialba"];
	
	$queryDB .= " AND si.fecha_cartera >= '".$fechaprod_inicialba."-".$fechaprod_inicialbm."-01'";
}

if ($_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$fechaprod_finalbm = $_REQUEST["fecha_finalbm"];
	
	$fechaprod_finalba = $_REQUEST["fecha_finalba"];
	
	$queryDB .= " AND si.fecha_cartera <= '".$fechaprod_finalba."-".$fechaprod_finalbm."-01'";
}

$rs = sqlsrv_query($link, $queryDB);

while($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	switch ($fila["estado_tesoreria"])
	{
		case "ABI":	$estado = "ABIERTO"; break;
		case "PAR":	$estado = "PARCIAL"; break;
		case "CER":	$estado = "CERRADO"; break;
	}
	
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					$opcion_desembolso = $fila["opcion_desembolso_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					$opcion_desembolso = $fila["opcion_desembolso_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					$opcion_desembolso = $fila["opcion_desembolso_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					$opcion_desembolso = $fila["opcion_desembolso_cso"];
					break;
	}
	
	if ($fila["opcion_credito"] == "CLI")
		$fila["retanqueo_total"] = 0;
	
		$intereses_anticipados = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00;
	
		$asesoria_financiera = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00, 0);
		$asesoria_financiera_base = $asesoria_financiera;
		$iva = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00, 0);
		$iva_porc = $fila["descuento3"];
		$valor_servicio_nube = 0;
		$asesoria_financiera_nueva = 0;
	
		if($fila["servicio_nube"]){
			$asesoria_financiera = $fila["descuento2_valor"];
			$valor_servicio_nube = $fila["descuento8_valor"];
			$asesoria_financiera_nueva = $fila["descuento9_valor"];
			$iva = $fila["descuento10_valor"];
	
			if($fila["descuento10_valor"] > 0){
				$iva_porc = round($iva / ($fila["valor_credito"] - $fila["retanqueo_total"]) * 1000, 2);
			}else{
				$iva_porc = 0;
			}
		}
		
		$comision_venta = 0;
		
		if ($fila["tipo_producto"] == "1")
		{
			if ($fila["fecha_estudio"] < "2018-01-01")
			{
				$asesoria_financiera += $fila["valor_credito"] * $fila["descuento5"] / 100.00;
			}
			else
			{
				if ($fila["fidelizacion"])
					$comision_venta = $fila["retanqueo_total"] * $fila["descuento5"] / 100.00;
				else
					$comision_venta = $fila["valor_credito"] * $fila["descuento5"] / 100.00;
			}
		}
		
		$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;
		
		$comision_venta_iva = 0;
		
		if ($fila["tipo_producto"] == "1")
		{
			if ($fila["fecha_estudio"] < "2018-01-01")
			{
				$iva += $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			}
			else
			{
				if ($fila["fidelizacion"])
					$comision_venta_iva = $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
				else
					$comision_venta_iva = $fila["valor_credito"] * $fila["descuento6"] / 100.00;
			}
		}
		
		$gmf = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00;
	
?>
<tr<?php echo $tr_class ?>>
<td><?php echo strtoupper($fila["cedula"]) ?></td>
	<td><?php echo strtoupper($fila["nombre"]) ?></td>
	<td><?php echo strtoupper($fila["pagaduria"]) ?></td>
	<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
	<td><?php echo $fila["tasa_interes"] ?></td>
	<td><?php echo strtoupper($fila["plazo"]) ?></td>
	<td><?php echo $opcion_cuota ?></td>
	<td><?php echo number_format($fila["valor_credito"], 0, ".", ",") ?></td>
	<td><?php echo number_format($opcion_desembolso, 0, ".", ",") ?></td>
	<td><?php echo number_format($opcion_desembolso - $fila["retanqueo_total"], 0, ".", ",") ?></td>
	<td><?php echo $fila["descuento1"]?></td>
	<td><?php echo number_format($intereses_anticipados, 0, ".", ",") ?></td>
	<td><?php echo $fila["descuento2"]?></td>	
	<td><?php echo number_format($asesoria_financiera_base, 0, ".", ",") ?></td>
	<td><?php echo number_format($asesoria_financiera, 0, ".", ",") ?></td>
	<td><?php echo number_format($valor_servicio_nube, 0, ".", ",") ?></td>
	<td><?php echo number_format($asesoria_financiera_nueva, 0, ".", ",") ?></td>
	<td><?php echo $fila["descuento3"]?></td>
	<td><?php echo number_format($iva, 0, ".", ",") ?></td>
	<td><?php echo $fila["descuento4"]?></td>
	<td><?php echo number_format($gmf, 0, ".", ",") ?></td>
<?php

	$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");
	
	while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
	{
		$existe_descuento = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' AND id_descuento = '".$fila1["id_descuento"]."'");
		
		if (sqlsrv_num_rows($existe_descuento))
		{
			$porcentaje = $fila1["porcentaje"];
			$valor_descuento = number_format(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00, 0, ".", ",");
			
			$total_descuentos_adicionales[$fila1["id_descuento"]] += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
		}
		else
		{
			$porcentaje = 0;
			$valor_descuento = 0;
		}
		
?>
	<td><?php echo $porcentaje ?></td>
	<td><?php echo $valor_descuento ?></td>
<?php

	}
	
?>
	<td><?php if ($fila["tipo_producto"] == "1" && $fila["fecha_estudio"] >= "2018-01-01") { echo $fila["descuento5"]; } ?></td>
	<td><?php echo number_format($comision_venta, 0, ".", ",") ?></td>
	<td><?php if ($fila["tipo_producto"] == "1" && $fila["fecha_estudio"] >= "2018-01-01") { echo $fila["descuento6"]; } ?></td>
	<td><?php echo number_format($comision_venta_iva, 0, ".", ",") ?></td>
	<td><?php echo $fila["descuento_transferencia"] ?></td>
	<td><?php echo $fila["mes_prod"] ?></td>
</tr>	
<?php

$total_opcion_cuota += $opcion_cuota;
$total_valor_credito += round($fila["valor_credito"]);
$total_opcion_desembolso += round($opcion_desembolso);
$total_sin_retanqueos += round($opcion_desembolso - $fila["retanqueo_total"]);
$total_intereses_anticipados += round($intereses_anticipados);

$total_asesoria_financiera_base += $asesoria_financiera_base;
$total_asesoria_financiera += $asesoria_financiera;
$total_valor_servicio_nube += $valor_servicio_nube;
$total_asesoria_financiera_nueva += $asesoria_financiera_nueva;

$total_asesoria_financiera += round($asesoria_financiera);
$total_iva += round($iva);
$total_gmf += round($gmf);
$total_comision_venta += round($comision_venta);
$total_comision_venta_iva += round($comision_venta_iva);
$total_descuento_transferencia += $fila["descuento_transferencia"];
}

?>
<tr><td>&nbsp;</td></tr>
<tr>
<td colspan="6"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_cuota ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td><b><?php echo $total_opcion_desembolso ?></b></td>
	<td><b><?php echo $total_sin_retanqueos ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_intereses_anticipados ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_asesoria_financiera_base ?></b></td>
	<td><b><?php echo $total_asesoria_financiera ?></b></td>
	<td><b><?php echo $total_valor_servicio_nube ?></b></td>
	<td><b><?php echo $total_asesoria_financiera_nueva ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_iva ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_gmf ?></b></td>
<?php

$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");

while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
{

?>
	<td>&nbsp;</td>
	<td><b><?php echo $total_descuentos_adicionales[$fila1["id_descuento"]] ?></b></td>
<?php

}

?>
	<td>&nbsp;</td>
	<td><b><?php echo $total_comision_venta ?></b></td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_comision_venta_iva ?></b></td>
	<td><b><?php echo $total_descuento_transferencia ?></b></td>
	<td>&nbsp;</td>
</tr>
</table>
