<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

require_once("../cors.php");
include("../../functions.php");

$resultado = array();


$link = conectar_utf();

$queryDB= "SELECT si.fecha_tesoreria, DATE_FORMAT(si.fecha_radicado, '%Y-%m-%d') AS fecha_radicado,  pa.nombre as pagaduria_nombre, si.* ,un.id_unidad, un.nombre as unidad_negocio, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' 
			END as sin_seguro_x, FORMAT(fecha_comision_pagada, 'yyyy-MM-dd') as fecha_comision_pagada_texto, pa.sector, 
			FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, 
			CONCAT(us.nombre,' ',us.apellido) as nombre_comercial, us.cedula as cedula_comercial,
			 us.contrato, us.freelance, us.outsourcing, so.direccion, ci.municipio, so.celular, ofi.nombre as oficina, CASE WHEN si.estado IN ('DES', 'CAN')
			  THEN CASE WHEN fn_fecha_desembolso_final(si.id_simulacion) IS NULL THEN si.fecha_desembolso ELSE fn_fecha_desembolso_final(si.id_simulacion) 
			  END ELSE '' END as fecha_desembolso_final, ca.nombre as caracteristica, so.email, sub.nombre 
			  AS subestado, zon.nombre as zona_descripcion 
			  FROM simulaciones si
			RIGHT JOIN simulaciones_subestados ss ON ss.id_simulacion = si.id_simulacion
			INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad 
			INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre
			LEFT JOIN subestados sub ON sub.id_subestado = si.id_subestado
			INNER JOIN usuarios us ON si.id_comercial = us.id_usuario 
			INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina 
			LEFT JOIN zonas zon ON zon.id_zona=ofi.id_zona 
			LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion 
			LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
			LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica
			WHERE si.estado = 'EST' AND si.decision = 'VIABLE' 
			AND si.fecha_cartera IS null AND si.id_subestado = ss.id_subestado AND (ss.id_subestado ='78' OR ss.id_subestado = '46' OR ss.id_subestado ='84')";
 
			

if ($_POST["cedula"]){
	$queryDB .= " AND (si.cedula = '".$_POST["cedula"]."')";
} 
if($_POST['fecha_subestado_minima']){
	$queryDB.= "  AND (	(ss.fecha_creacion,'yyyy-MM-dd') >='".$_POST['fecha_subestado_minima']."'";
}
if($_POST['fecha_subestado_maxima']){
	$queryDB.= "  AND FORMAT(ss.fecha_creacion,'yyyy-MM-dd') <='".$_POST['fecha_subestado_maxima']."')";
}

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if ($_POST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_POST["sector"]."'";
}
if ($_POST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_POST["pagaduria"]."'";
}
if ($_POST["fechades_inicial"]!='--')
{
	$queryDB .= " AND si.fecha_radicado  >= '".$_POST["fechades_inicial"]."'";
}

if ($_POST["fechades_final"]!='--')
{
	$queryDB .= " AND si.fecha_radicado  <= '".$_POST["fechades_final"]."'";
}

$queryDB .= " AND si.id_unidad_negocio IN (".$_POST["S_IDUNIDADNEGOCIO"].")";


$respuesta = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
if($respuesta){
	  if(sqlsrv_num_rows($respuesta)>0){
		while($fila = sqlsrv_fetch_array($respuesta)){
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
			
			if (!$fila["sin_seguro"])
				$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
			else
				$seguro_vida = 0;
			
			$cuota_corriente = $opcion_cuota - round($seguro_vida);
			
			$compras_cartera = 0;
			
			$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL OR (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
			
			$rs1 = sqlsrv_query($link, $queryDB);
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			if ($fila1["s"])
				$compras_cartera = $fila1["s"];
			
			if ($fila["opcion_credito"] == "CLI")
				$fila["retanqueo_total"] = 0;
			
			$intereses_anticipados = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00);
			
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
			
			if(!$fila["servicio_nube"]){
				$iva = ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00;
			}
			
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
			
			$administrativos = round($intereses_anticipados) + round($asesoria_financiera) + round($iva) + round($gmf) + round($comision_venta) + round($comision_venta_iva);
			
			$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
			
			while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
			{
				$administrativos += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
			}
			
			$desembolso_neto = round($fila["valor_credito"]) - $administrativos;
			
			if($fila["servicio_nube"]){
				$desembolso_cliente = $fila["desembolso_cliente"];
			}else{
				$desembolso_cliente = $fila["valor_credito"] - $fila["retanqueo_total"] - $compras_cartera - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00) - (($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00) - $fila["descuento_transferencia"];
			}
			
			if ($fila["tipo_producto"] == "1"){
				if ($fila["fidelizacion"]){
					$desembolso_cliente = $desembolso_cliente - $fila["retanqueo_total"] * $fila["descuento5"] / 100.00 - $fila["retanqueo_total"] * $fila["descuento6"] / 100.00;
				}
				else{
					$desembolso_cliente = $desembolso_cliente - $fila["valor_credito"] * $fila["descuento5"] / 100.00 - $fila["valor_credito"] * $fila["descuento6"] / 100.00;
				}
			}
			
			if (sqlsrv_num_rows($descuentos_adicionales))
			{
				sqlsrv_data_seek($descuentos_adicionales, 0);
				
				while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
				{
					$desembolso_cliente -= ($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00;
				}
			}
			
			switch ($fila["estado_tesoreria"])
			{
				case "ABI":	$estado = "ABIERTO"; break;
				case "PAR":	$estado = "PARCIAL"; break;
				case "CER":	$estado = "CERRADO"; break;
			}

		 	$tipo_comercial = 'PLANTA';
			
			if ($fila["freelance"]) {
				$tipo_comercial = 'FREELANCE';
			}
			
			if ($fila["outsourcing"]) {
				$tipo_comercial = 'OUTSOURCING';
			}
			
			if ($fila["telemercadeo"])
				$telemercadeo = "SI";
			else
				$telemercadeo = "NO";
			
			if ($fila["comision_pagada"]){
				$comision_pag = "SI";
			}
			else{
				$comision_pag = "NO";
			}
			$consultarTesoreriaCC="SELECT * FROM tesoreria_cc a LEFT JOIN simulaciones_comprascartera b ON a.consecutivo=b.consecutivo AND a.id_simulacion=b.id_simulacion  WHERE a.id_simulacion='".$fila["id_simulacion"]."' and b.id_entidad NOT IN (413,104,218) AND a.consecutivo NOT IN (SELECT consecutivo FROM simulaciones_comprascartera WHERE se_compra='SI' AND id_entidad=834 AND valor_pagar=0 AND id_simulacion='".$fila["unidad_negocio"]."')";
			$queryTesorariaCC=sqlsrv_query($link, $consultarTesoreriaCC);
			if (sqlsrv_num_rows($queryTesorariaCC)>0)
			{
				$consultarTesoreriaCCNoPagada=$consultarTesoreriaCC." AND pagada=0";
				$queryTesorariaCCNoPagada=sqlsrv_query($link, $consultarTesoreriaCCNoPagada);
				if (sqlsrv_num_rows($queryTesorariaCCNoPagada)>0)
				{
					$ccpagada="NO";
				}else{
					$ccpagada="SI";
				}
			}else{
				$ccpagada="NO APLICA";
			} 

			if ($fila["comision_pagada"]){
				$comision_pag = "SI";
			}
			else{
				$comision_pag = "NO";
			}
			$queryTasaC = "SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, if(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE id_tasa_comision = ".$fila['id_tasa_comision'];
						
				$rsTC = sqlsrv_query($link, $queryTasaC, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				$tipo_tasa_comision = '';

				if (sqlsrv_num_rows($rsTC)>0){
					
					$fTC = sqlsrv_fetch_array($rsTC);
					
					if($fTC['marca_unidad_negocio'] == 1){ 
						$textoTipoComsion = 'F'; 
					}else{ 
						$textoTipoComsion = 'K'; 
					}

					$tipo_tasa_comision = 'TIPO '.$textoTipoComsion.' '.$fTC['id_tipo'];
				}

		 	$resultado[] =array(
		 		'Cedula Asesor'=>$fila['cedula_comercial'],
				'Nombre Asesor'=>$fila['nombre_comercial'],
				'Tipo Asesor'=>$tipo_comercial,
				'Oficina'=>$fila['oficina'],
				'Comision Pagada'=>$comision_pag,
				'Fecha Comision Pagada'=>$fila['fecha_comision_pagada_texto'],
				'Descontar Comision'=>$fila['valor_comision_descontar'],
				'Mes Prod'=>$fila['mes_prod'],
				'No. Libranza'=>$fila['nro_libranza'],
				'Cedula'=>$fila['cedula'],
				'Nombre'=>ltrim($fila['nombre']),
				'Desembolso Menos Retanqueos'=>$desembolso_neto - $fila["retanqueo_total"],
				'Tasa'=>$fila['tasa_interes'],
				'COMPRAS DE CARTERA'=>$ccpagada,
				'Unidad de Negocio'=>$fila['unidad_negocio'],
				'KP PLUS'=>$fila['sin_seguro_x'],
				'SubEstado'=>$fila['subestado'],
				'Estado'=>$estado,
				'Caracteristicas'	=>$fila['caracteristica'],
				'Tipo Comision'=>$tipo_tasa_comision,
				'Zona'=>$fila['zona_descripcion'],
				'Pagaduria'=>$fila['pagaduria_nombre'],
				'fecha_tesoreria'=>$fila['fecha_tesoreria'],
				'fecha_radicado'=>$fila['fecha_radicado']
		 	);

		 }

		 $response=array(
		 	'estado'=>200,
			'mensaje'=>'Consulta exitosa',
			'data'=>$resultado
		 );
	}else{
		$response=array(
		 	'estado'=>300,
			'mensaje'=>'Consulta exitosa, no hay registros disponibles'
	 	);
	}
}else{
	$response = array(
		'estado'=>404,
		'mensaje'=>'Consulta Fallida',
		'query'=>$queryDB
	);
}
 
echo json_encode($response);

?>