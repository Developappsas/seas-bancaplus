<?php 
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Tesoreria.xls");
header("Pragma: no-cache");
header("Expires: 0");

include ('../functions.php'); 
?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["REPORTE_TESORERIA_USUARIO"] != 1)) {
	echo "Su sesion a caducado o no tiene acceso a este reporte";
	echo "Dudas o solicitudes al correo: soporte@kredit.com.co";
	exit;

}


$link = conectar();

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $urlPrincipal.'/servicios/Simulaciones/Crear_Simulaciones_Consultas_Log.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{"id_simulacion":"","origen":"REPORTE TESORERIA","usuario":"'.$_SESSION["S_IDUSUARIO"].'","operacion":"Crear Simulaciones Consultas Log"}',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
    ),
  ));
  $response = curl_exec($curl);
  
  curl_close($curl);


?>
<table border="0">
<tr>
	<th>Cédula</th>
	<th>Mes Prod</th>
	<th>Nombre</th>
	<th>Sector</th>
	<th>Pagaduría</th>
	<th>Unidad de Negocio</th>
	<th>Vr. Solicitado</th>
	<th>Vr. Crédito</th>
	<th>Comprador</th>
	<th>Estado</th>
	<th>Subestado</th>
	<th>Fecha Tesorer&iacute;a</th>
	<th>Desembolsado</th>
	<th>Pdte. Retanqueos</th>
	<th>Pdte. Desembolso</th>
	<th>Cuota Retenida</th>
	<th>Pdte. Compra Cartera</th>
	<th>Comisi&oacute;n Originar</th>
	<th>Comisi&oacute;n Venta (Retanqueos)</th>
	<th>Comisi&oacute;n Pagada</th>
	<th>Fecha Comisi&oacute;n Pagada</th>
	<th>Total Giro</th>
	<th>Id</th>
	<th>No. Crédito</th>
	<th>Telefono</th>
	<th>Entidad</th>
	<th>Tipo de cuenta</th>
	<th>Numero de cuenta</th>
	<th>Comision Descontar</th>
	<th>Tasa</th>
	<th>Oficina</th>
	<th>Desembolso - Retanqueo</th>
	<th>Visado</th>
	<th>Incorporacion</th>
	<th>Formato Digital</th>
	<th>Asesor</th>
	<th>Zona</th>
	<th>F. Tesoreria Final</th>


</tr>
<?php

//Agregamos subestado 6.2}
$subestados_tesoreria .= ",'78'";

$queryDB = "SELECT si.servicio_nube, si.desembolso_cliente, si.descuento1_valor, si.descuento2_valor, si.descuento3_valor, si.descuento4_valor, si.descuento5_valor, si.descuento6_valor, si.descuento7_valor, si.descuento8_valor, si.descuento9_valor, si.descuento10_valor, si.retanqueo1_libranza,si.retanqueo2_libranza,si.retanqueo3_libranza, CONCAT(ase.nombre,' ',ase.apellido) AS nombre_comercial,zon.nombre as zona_descripcion,CASE WHEN si.formato_digital='1' THEN 'SI' ELSE 'NO' END AS formato_digital_descripcion,of1.nombre as oficina,ba.nombre as banco_nombre, si.*, un.nombre as unidad_negocio, FORMAT(fecha_cartera, 'Y-m') as mes_prod, pa.sector, se.nombre as nombre_subestado, FORMAT(fecha_comision_pagada, 'Y-m-d') as fecha_comision_pagada_texto, vex.comprador 
from simulaciones si 
	INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	LEFT JOIN bancos ba on si.id_banco = ba.id_banco 
	LEFT JOIN subestados se ON si.id_subestado = se.id_subestado 
	LEFT JOIN (select si.id_simulacion, co.nombre as comprador from ventas_detalle vd 
	INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion 
	INNER JOIN ventas ve ON vd.id_venta = ve.id_venta 
	INNER JOIN compradores co ON ve.id_comprador = co.id_comprador 
where ve.tipo = 'VENTA' AND ve.estado IN ('VEN') AND vd.recomprado = '0') vex ON vex.id_simulacion = si.id_simulacion 
	INNER JOIN oficinas of1 ON of1.id_oficina=si.id_oficina 
	INNER JOIN zonas zon ON zon.id_zona=of1.id_zona 
	INNER JOIN usuarios ase ON ase.id_usuario=si.id_comercial 
where (si.estado IN ('DES', 'CAN') OR (si.estado = 'EST' AND si.decision = '".$label_viable."' AND si.id_subestado IN (".$subestados_tesoreria.")))";



if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";

if ($_REQUEST["cedula"]) {
	$queryDB .= " AND (si.cedula = '".$_REQUEST["cedula"]."' OR UPPER(si.nombre) like '%".utf8_encode(strtoupper($_REQUEST["cedula"]))."%' OR si.nro_libranza = '".$_REQUEST["cedula"]."')";
}

if ($_REQUEST["sector"]) {
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"]) {
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["estado"]) {
	$queryDB .= " AND si.estado_tesoreria = '".$_REQUEST["estado"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
//	$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"]) {
	$queryDB .= " AND si.fecha_tesoreria <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

$queryDB .= " order by si.fecha_tesoreria DESC, si.id_simulacion DESC";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));



if ($rs == false) {
    if( ($errors = sqlsrv_errors() ) != null) {
        foreach( $errors as $error ) {
            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            echo "code: ".$error[ 'code']."<br />";
            echo "message: ".$error[ 'message']."<br />";
        }
    }
}



 
if(sqlsrv_num_rows($rs)>0){

	while (@$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
		$val=0;
		$fecha_ultima_tesoreria="";
		$consultarUltimoFechaTesoreria=sqlsrv_query($link,"SELECT TOP 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=46 ORDER BY id_simulacionsubestado DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0)
		{
			$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
			$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
			$val=1;
		}

		if ($val==0){
			$consultarUltimoFechaTesoreria=sqlsrv_query($link,"SELECT TOP 1 * FROM simulaciones_subestados WHERE id_simulacion='".$fila["id_simulacion"]."' AND id_subestado=48 ORDER BY id_simulacionsubestado DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			if (sqlsrv_num_rows($consultarUltimoFechaTesoreria)>0)
			{
				$resUltimoFechaTesoreria=sqlsrv_fetch_array($consultarUltimoFechaTesoreria);
				$fecha_ultima_tesoreria=$resUltimoFechaTesoreria["fecha_creacion"];
				$val=1;
			}
		}

		switch($fila["opcion_credito"])	{
			case "CLI":	$opcion_desembolso = $fila["opcion_desembolso_cli"];$fila["retanqueo_total"] = 0; break;
			case "CCC":	$opcion_desembolso = $fila["opcion_desembolso_ccc"]; break;
			case "CMP":	$opcion_desembolso = $fila["opcion_desembolso_cmp"]; break;
			case "CSO":	$opcion_desembolso = $fila["opcion_desembolso_cso"]; break;
		}

		$valor_desembolso_retanqueo=$opcion_desembolso-$fila["retanqueo_total"];
		
		$queryDB1 = "select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and fecha_giro IS NOT NULL";
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
		
		$rs1 = sqlsrv_query($link, $queryDB1);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$desembolsado = $fila1["s"];
		
		$compras_cartera = 0;
		
		$queryDB = "SELECT SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
		
		$rs1 = sqlsrv_query($link, $queryDB);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		if ($fila1["s"]){
			$compras_cartera = $fila1["s"];
		}
			
		
		if ($fila["opcion_credito"] == "CLI"){
			$fila["retanqueo_total"] = 0;
		}
			
		
		if($fila["servicio_nube"]){
			$desembolso_cliente = $fila["desembolso_cliente"];
		}else{
			$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];
		}




		if ($fila["tipo_producto"] == "1")
			if ($fila["fidelizacion"])
				$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
			else
				$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;
		
		$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
		{
			$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
		}
		
		if ($fila["bloqueo_cuota"])
		{
			$retenciones_cuota = $fila["bloqueo_cuota_valor"];
		}
		else
		{
			$rs1 = sqlsrv_query($link, "SELECT SUM(cuota_retenida) as s from tesoreria_cc where id_simulacion = '".$fila["id_simulacion"]."'");
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			$retenciones_cuota = $fila1["s"];
		}
		
		$queryDB1 = "SELECT SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'DSC' and fecha_giro IS NOT NULL";
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
		
		$rs1 = sqlsrv_query($link, $queryDB1);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_dsc = $fila1["s"];
		
		$saldo_girar_dsc = round($desembolso_cliente) - $retenciones_cuota - $giros_realizados_dsc;
		
		$queryDB1 = "select SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'CCA' and fecha_giro IS NOT NULL";
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
		
		$rs1 = sqlsrv_query($link, $queryDB1);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_cca = $fila1["s"];
		
		$saldo_girar_cca = round($compras_cartera) - $giros_realizados_cca;
		
		$queryDB1 = "SELECT SUM(valor_girar) as s from giros where id_simulacion = '".$fila["id_simulacion"]."' and clasificacion = 'RET' and fecha_giro IS NOT NULL";
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			$queryDB1 .= " AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
		}
		
		$rs1 = sqlsrv_query($link, $queryDB1);
		
		$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
		
		$giros_realizados_ret = $fila1["s"];
		
		$saldo_girar_ret = round($fila["retanqueo_total"]) - $giros_realizados_ret;
		
		if ($fila["sector"] == "PRIVADO")
		{
			$comision = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00 + ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento3"] - ($fila["descuento1"] * $fila["iva"] / 100.00)) / 100.00);
		}
		else
		{
			$comision = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00 + ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00);
		}
		
		$comision_venta = 0;
		
		if ($fila["tipo_producto"] == "1")
		{
			if ($fila["fecha_estudio"] < "2018-01-01")
			{
				$comision += round($fila["valor_credito"] * $fila["descuento5"] / 100.00 + $fila["valor_credito"] * $fila["descuento6"] / 100.00);
			}
			else
			{
				if ($fila["fidelizacion"])
					$comision_venta = round($fila["retanqueo_total"] * $fila["descuento5"] / 100.00 + $fila["retanqueo_total"] * $fila["descuento6"] / 100.00);
				else
					$comision_venta = round($fila["valor_credito"] * $fila["descuento5"] / 100.00 + $fila["valor_credito"] * $fila["descuento6"] / 100.00);
			}
		}
		
		$total_giro = $opcion_desembolso + round($comision) + round($comision_venta);
		
		switch ($fila["estado_tesoreria"])
		{
			case "ABI":	$estado = "ABIERTO"; break;
			case "PAR":	$estado = "PARCIAL"; break;
			case "CER":	$estado = "CERRADO"; break;
		}
		
		$subestado = $fila["nombre_subestado"];
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			if ($estado == "CERRADO")
			{
				$giros_tmp = sqlsrv_query($link, "select id_giro from giros where id_simulacion = '".$fila["id_simulacion"]."' AND fecha_giro IS NOT NULL AND DATE(fecha_giro) > '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'");
				
				if (sqlsrv_num_rows($giros_tmp))
					$estado = "PARCIAL";
			}
			
			if ($estado == "PARCIAL")
			{
				$giros_tmp = sqlsrv_query($link, "select id_giro from giros where id_simulacion = '".$fila["id_simulacion"]."' AND fecha_giro IS NOT NULL AND DATE(fecha_giro) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'");
				
				if (!sqlsrv_num_rows($giros_tmp))
					$estado = "ABIERTO";
			}
			
			if ($estado == "ABIERTO" || $estado == "PARCIAL")
			{
				$historial_tmp = sqlsrv_query($link, "SELECT TOP 1 se.nombre from simulaciones_subestados ss INNER JOIN subestados se ON ss.id_subestado = se.id_subestado where ss.id_simulacion = '".$fila["id_simulacion"]."' AND ss.id_subestado IN (".$subestados_tesoreria.") AND DATE(ss.fecha_creacion) <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."' ORDER BY ss.fecha_creacion DESC ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($historial_tmp))
				{
					$fila1 = sqlsrv_fetch_array($historial_tmp);
					
					$subestado = $fila1["nombre"];
				}
				else
				{
					$subestado = "";
				}
			}
		}
		
		if ($fila["comision_pagada"])
			$comision_pag = "SI";
		else
			$comision_pag = "NO";
		
		$fecha_comision_pagada_texto = $fila["fecha_comision_pagada_texto"];
		
		//Si est� filtrado por fecha de corte
		if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
		{
			if ($fecha_comision_pagada_texto && $fecha_comision_pagada_texto > $_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"])
			{
				$fecha_comision_pagada_texto = "";
				
				$comision_pag = "NO";
			}
		}

		$tipo_crediton="";
		$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";
		$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

		if (sqlsrv_num_rows($queryComprasCarteraCredito)>0){

			$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$fila["id_simulacion"]."' AND se_compra='SI'";

			$queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
			$resComprasCC=sqlsrv_fetch_array($queryComprasCC);

			if ($resComprasCC["cuota"]>0){
				if ($fila["retanqueo1_libranza"]=="" || $fila["retanqueo2_libranza"]=="" || $fila["retanqueo3_libranza"]==""){
					$tipo_crediton="COMPRAS DE CARTERA";	
				}else{
					$tipo_crediton="COMPRAS CON RETANQUEO";	
				}
			}
			else{
				if ($resComprasCC["valor_pagar"]>0){
					$tipo_crediton="LIBRE CON SANEAMIENTO";	
				}else{
					if($fila["retanqueo1_libranza"]<>"" || $fila["retanqueo2_libranza"]<>"" || $fila["retanqueo3_libranza"]<>""){
						$tipo_crediton="LIBRE INVERSION CON RETANQUEO";	
					}
				}
			}
		}else{
			$tipo_crediton="LIBRE INVERSION";
		}
		?>
		<tr>
			<td><?php echo $fila["cedula"] ?></td>
			<td><?php echo $fila["mes_prod"] ?></td>
			<td><?php echo utf8_decode($fila["nombre"]) ?></td>
			<td><?php echo $fila["sector"] ?></td>
			<td><?php echo utf8_decode($fila["pagaduria"]) ?></td>
			<td><?php echo utf8_decode($fila["unidad_negocio"]) ?></td>
			<td><?php echo $opcion_desembolso ?></td>
			<td><?php echo $fila["valor_credito"] ?></td>
			<td><?php echo utf8_decode($fila["comprador"]) ?></td>
			<td><?php echo $estado ?></td>
			<td><?php echo utf8_decode($subestado) ?></td>
			<td><?php echo $fila["fecha_tesoreria"] ?></td>
			<td><?php echo number_format($desembolsado, 0, "", "") ?></td>
			<td><?php echo number_format($saldo_girar_ret, 0, "", "") ?></td>
			<td><?php echo number_format($saldo_girar_dsc, 0, "", "") ?></td>
			<td><?php echo number_format($retenciones_cuota, 0, "", "") ?></td>
			<td><?php echo number_format($saldo_girar_cca, 0, "", "") ?></td>
			<td><?php echo number_format($comision, 0, "", "") ?></td>
			<td><?php echo number_format($comision_venta, 0, "", "") ?></td>
			<td><?php echo $comision_pag ?></td>
			<td><?php echo $fecha_comision_pagada_texto ?></td>
			<td><?php echo number_format($total_giro, 0, "", "") ?></td>
			<td><?php echo $fila["id_simulacion"] ?></td>
			<td><?php echo $fila["nro_libranza"] ?></td>
			<td><?php echo $fila["telefono"] ?></td>
			<td><?php echo $fila["banco_nombre"] ?></td>
			<td><?php echo $fila["tipo_cuenta"] ?></td>
			<td><?php echo $fila["nro_cuenta"].'*' ?></td>
			<td><?php echo $fila["valor_comision_descontar"] ?></td>
			<td><?php echo $fila["tasa_interes"] ?></td>
			<td><?php echo $fila["oficina"] ?></td>
			
			<td><?php echo $valor_desembolso_retanqueo ?></td>
			

			<?php
			if ($fila["visado"]=="s"){
				?><td>SI</td><?PHP
			}else{
				?><td>NO</td><?PHP
			}

			if ($fila["incorporacion"]=="s"){
				?><td>SI</td><?PHP
			}else{
				?><td>NO</td><?PHP
			}


			
			?>
			<td><?php echo $fila["formato_digital_descripcion"]; ?></td>
			
						
			<td><?php echo $fila["nombre_comercial"] ?></td>
			<td><?php echo $fila["zona_descripcion"] ?></td>
			<td><?php echo $fecha_ultima_tesoreria ?></td>
			<td><?php echo $tipo_crediton ?></td>
		</tr>	
		<?php

		$total_opcion_desembolso += $opcion_desembolso;
		$total_valor_credito += $fila["valor_credito"];
		$total_desembolsado += round($desembolsado);
		$total_saldo_girar_ret += round($saldo_girar_ret);
		$total_saldo_girar_dsc += round($saldo_girar_dsc);
		$total_retenciones_cuota += round($retenciones_cuota);
		$total_saldo_girar_cca += round($saldo_girar_cca);
		$total_comision += round($comision);
		$total_comision_venta += round($comision_venta);
		$total_total_giro += round($total_giro);
	}
}
?>
<tr><td>&nbsp;</td></tr>
<tr>
	<td colspan="6"><b>TOTALES</b></td>
	<td><b><?php echo $total_opcion_desembolso ?></b></td>
	<td><b><?php echo $total_valor_credito ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_desembolsado ?></b></td>
	<td><b><?php echo $total_saldo_girar_ret ?></b></td>
	<td><b><?php echo $total_saldo_girar_dsc ?></b></td>
	<td><b><?php echo $total_retenciones_cuota ?></b></td>
	<td><b><?php echo $total_saldo_girar_cca ?></b></td>
	<td><b><?php echo $total_comision ?></b></td>
	<td><b><?php echo $total_comision_venta ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><b><?php echo $total_total_giro ?></b></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
