<?php include ('../functions.php'); ?>
<?php
sqlsrv_query($link, "SET SQL_BIG_SELECTS=1");
if (!$_SESSION["S_LOGIN"] || 
	($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" 
		&& $_SESSION["S_TIPO"] != "GERENTECOMERCIAL" && $_SESSION["S_TIPO"] != "OFICINA"
		&& $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" 
		&& $_SESSION["S_SUBTIPO"] != "ANALISTA_VALIDACION" && $_SESSION["S_SUBTIPO"] != "ANALISTA_BD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();
ini_set('max_execution_time', 0);
ini_set('memory_limit','-1');




$queryDB = "select si.id_simulacion, si.fecha_estudio, si.cedula, si.nombre, si.pagaduria, si.institucion, si.fecha_nacimiento, si.meses_antes_65, si.salario_basico, si.adicionales, si.total_ingresos, si.aportes, si.otros_aportes, si.total_aportes, si.total_egresos, si.ingresos_menos_aportes, si.salario_libre, si.nivel_contratacion, si.embargo_actual, si.historial_embargos, si.embargo_alimentos, si.descuentos_por_fuera, si.cartera_mora, si.valor_cartera_mora, si.puntaje_datacredito, si.puntaje_cifin, si.valor_descuentos_por_fuera, si.tasa_interes, si.plazo, si.tipo_credito, si.suma_al_presupuesto, si.total_cuota, si.total_valor_pagar, si.opcion_credito, si.opcion_cuota_cli, si.opcion_desembolso_cli, si.opcion_cuota_ccc, si.opcion_desembolso_ccc, si.opcion_cuota_cmp, si.opcion_desembolso_cmp, si.opcion_cuota_cso, si.opcion_desembolso_cso, si.desembolso_cliente, si.decision, si.valor_credito, si.resumen_ingreso, si.incor, si.comision, si.utilidad_neta, si.sobre_el_credito, si.usuario_creacion, si.fecha_creacion, si.usuario_modificacion, si.fecha_modificacion, si.estado, si.calificacion, si.fecha_desembolso, si.telefono, si.bonificacion, si.nro_libranza, si.descuento1, si.descuento2, si.descuento3, si.descuento4, si.descuento5, si.descuento6, si.descuento_transferencia, si.valor_visado, si.valor_por_millon_seguro, si.porcentaje_extraprima, si.fecha_inicio_labor, si.pa, si.fecha_llamada_cliente, si.retanqueo1_libranza, si.retanqueo1_valor, si.retanqueo2_libranza, si.retanqueo2_valor, si.retanqueo3_libranza, si.retanqueo3_valor, si.retanqueo_total, un.nombre as unidad_negocio, si.fidelizacion, si.bloqueo_cuota_valor, si.medio_contacto, si.calif_sector_financiero, si.calif_sector_real, si.calif_sector_cooperativo, si.embargo_centrales, si.total_se_compra, si.usuario_desistimiento, si.fecha_desistimiento, si.usuario_incorporacion, si.fecha_incorporacion, si.fecha_aprobado, si.usuario_validacion, si.fecha_validacion, si.valor_seguro, so.sexo, CASE WHEN si.telemercadeo = 1 THEN 'SI' ELSE 'NO' END as telemercadeo_x, pa.sector, so.clave, ps.nombre as plan_seguro, DATE_FORMAT(si.fecha_cartera, '%Y-%m') as mes_prod, us.nombre as nombre_comercial, us.apellido, us.contrato, us.freelance, us.outsourcing, ofi.nombre as oficina, et.nombre as nombre_etapa, se.nombre as nombre_subestado, CASE WHEN si.sin_aportes = 1 THEN 'SI' ELSE 'NO' END as sin_aportes_ley, si.sin_seguro, CASE WHEN si.tipo_producto = 1 THEN 'SI' ELSE 'NO' END as recuperate, so.celular, so.direccion, so.ciudad as ciudad_residencia, ci.municipio, so.email, cu2.seguro, cau.nombre as causal, ca.nombre as caracteristica, CASE WHEN si.bloqueo_cuota = 1 THEN 'SI' ELSE 'NO' END as bloqueo_cuota_x, CASE WHEN si.formulario_seguro = 1 THEN 'SI' ELSE 'NO' END as formulario_seguro_x, CASE WHEN si.incorporado = 1 THEN 'SI' ELSE 'NO' END as incorporado_x, CASE si.validado WHEN 1 THEN 'VALIDADO' WHEN 2 THEN 'VALIDADO CON PDTES' ELSE 'NO VALIDADO' END as validado_x, us2.nombre as nombre_analista_riesgo_operativo, us2.apellido as apellido_analista_riesgo_operativo, us3.nombre as nombre_analista_riesgo_crediticio, us3.apellido as apellido_analista_riesgo_crediticio, us4.nombre as nombre_analista_gestion_comercial, us4.apellido as apellido_analista_gestion_comercial, CASE WHEN si.sin_seguro = 1 THEN 'SI' ELSE 'NO' END as sin_seguro_x from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario INNER JOIN oficinas ofi ON si.id_oficina = ofi.id_oficina LEFT JOIN subestados se ON si.id_subestado = se.id_subestado LEFT JOIN solicitud so ON si.id_simulacion = so.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN planes_seguro ps ON si.id_plan_seguro = ps.id_plan LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN causales cau ON si.id_causal = cau.id_causal LEFT JOIN caracteristicas ca ON si.id_caracteristica = ca.id_caracteristica LEFT JOIN etapas_subestados es ON se.id_subestado = es.id_subestado LEFT JOIN etapas et ON es.id_etapa = et.id_etapa LEFT JOIN usuarios us2 ON si.id_analista_riesgo_operativo = us2.id_usuario LEFT JOIN usuarios us3 ON si.id_analista_riesgo_crediticio = us3.id_usuario LEFT JOIN usuarios us4 ON si.id_analista_gestion_comercial = us4.id_usuario where 1 = 1";

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

if ($_REQUEST["sector"])
{
	$queryDB .= " AND pa.sector = '".$_REQUEST["sector"]."'";
}

if ($_REQUEST["pagaduria"])
{
	$queryDB .= " AND si.pagaduria = '".$_REQUEST["pagaduria"]."'";
}

if ($_REQUEST["id_oficina"])
{
	$queryDB .= " AND si.id_oficina = '".$_REQUEST["id_oficina"]."'";
}

if ($_REQUEST["institucion"])
{
	$queryDB .= " AND si.institucion = '".$_REQUEST["institucion"]."'";
}

if ($_REQUEST["edadd"])
{
	$queryDB .= " AND (CURDATE() - INTERVAL ".$_REQUEST["edadd"]." YEAR) >= si.fecha_nacimiento";
}

if ($_REQUEST["edadh"])
{
	$queryDB .= " AND (CURDATE() - INTERVAL ".$_REQUEST["edadh"]." YEAR) <= si.fecha_nacimiento";
}

if ($_REQUEST["salario_basicod"])
{
	$queryDB .= " AND si.salario_basico >= ".str_replace(",", "", $_REQUEST["salario_basicod"]);
}

if ($_REQUEST["salario_basicoh"])
{
	$queryDB .= " AND si.salario_basico <= ".str_replace(",", "", $_REQUEST["salario_basicoh"]);
}

if ($_REQUEST["embargo_actual"])
{
	$queryDB .= " AND si.embargo_actual = '".$_REQUEST["embargo_actual"]."'";
}

if ($_REQUEST["nivel_educativo"])
{
	$queryDB .= " AND si.nivel_educativo = '".$_REQUEST["nivel_educativo"]."'";
}

if ($_REQUEST["id_comercial"])
{
	$queryDB .= " AND si.id_comercial = '".$_REQUEST["id_comercial"]."'";
}

if ($_REQUEST["estado"])
{
	$queryDB .= " AND si.estado = '".$_REQUEST["estado"]."'";
}

if ($_REQUEST["decision"])
{
	$queryDB .= " AND si.decision = '".$_REQUEST["decision"]."'";
}

if ($_REQUEST["id_subestado"])
{
	$queryDB .= " AND si.id_subestado = '".$_REQUEST["id_subestado"]."'";
}

if ($_REQUEST["calificacion"])
{
	$queryDB .= " AND si.calificacion = '".$_REQUEST["calificacion"]."'";
}

if ($_REQUEST["solo_produccionb"])
{
	$queryDB .= " AND (si.estado IN ('DES', 'CAN') OR (si.estado IN ('ING', 'EST') AND si.decision = '".$label_viable."'))";
}

if ($_REQUEST["fecha_inicialbd"] && $_REQUEST["fecha_inicialbm"] && $_REQUEST["fecha_inicialba"])
{
	$queryDB .= " AND si.fecha_estudio >= '".$_REQUEST["fecha_inicialba"]."-".$_REQUEST["fecha_inicialbm"]."-".$_REQUEST["fecha_inicialbd"]."'";
}

if ($_REQUEST["fecha_finalbd"] && $_REQUEST["fecha_finalbm"] && $_REQUEST["fecha_finalba"])
{
	$queryDB .= " AND si.fecha_estudio <= '".$_REQUEST["fecha_finalba"]."-".$_REQUEST["fecha_finalbm"]."-".$_REQUEST["fecha_finalbd"]."'";
}

if ($_REQUEST["fechades_inicialbd"] && $_REQUEST["fechades_inicialbm"] && $_REQUEST["fechades_inicialba"])
{
	$queryDB .= " AND si.fecha_desembolso >= '".$_REQUEST["fechades_inicialba"]."-".$_REQUEST["fechades_inicialbm"]."-".$_REQUEST["fechades_inicialbd"]."'";
}

if ($_REQUEST["fechades_finalbd"] && $_REQUEST["fechades_finalbm"] && $_REQUEST["fechades_finalba"])
{
	$queryDB .= " AND si.fecha_desembolso <= '".$_REQUEST["fechades_finalba"]."-".$_REQUEST["fechades_finalbm"]."-".$_REQUEST["fechades_finalbd"]."'";
}

if ($_REQUEST["fechacartera_inicialbm"] && $_REQUEST["fechacartera_inicialba"])
{
	$queryDB .= " AND si.fecha_cartera >= '".$_REQUEST["fechacartera_inicialba"]."-".$_REQUEST["fechacartera_inicialbm"]."-01'";
}

if ($_REQUEST["fechacartera_finalbm"] && $_REQUEST["fechacartera_finalba"])
{
	$queryDB .= " AND si.fecha_cartera <= '".$_REQUEST["fechacartera_finalba"]."-".$_REQUEST["fechacartera_finalbm"]."-01'";
}

$queryDB .= " order by si.fecha_creacion, si.nombre, si.cedula";

$rs = sqlsrv_query($link, $queryDB);
$data=array();	
while ($fila = sqlsrv_fetch_assoc($rs))
{
    $res=array();
	$tipo_comercial = 'PLANTA';
	
	if ($fila["freelance"]) {
		$tipo_comercial = 'FREELANCE';
	}
	
	if ($fila["outsourcing"]) {
		$tipo_comercial = 'OUTSOURCING';
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
	
	if ($fila["seguro"] || $fila["seguro"] == "0")
		$seguro = $fila["seguro"];
	else
		if (!$fila["sin_seguro"])
			$seguro = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100)));
		else
			$seguro = 0;
	
	$cuota_corriente = $opcion_cuota - $seguro;
	
	$registro = "\"".$fila["id_simulacion"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"]))."\"".$separador_csv."\"".$tipo_comercial."\"".$separador_csv."\"".$fila["contrato"]."\"".$separador_csv."\"".$fila["telemercadeo_x"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["oficina"]))."\"".$separador_csv."\"".$fila["cedula"]."\"".$separador_csv."\"".$fila["fecha_estudio"]."\"";
	
	if ($_SESSION["FUNC_FDESEMBOLSO"])
		$registro .= $separador_csv."\"".$fila["fecha_desembolso"]."\"";
		
	$registro .= $separador_csv."\"".$fila["mes_prod"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre"]))."\"".$separador_csv."\"".$fila["fecha_nacimiento"]."\"".$separador_csv."\"".$fila["sexo"]."\"".$separador_csv."\"".$fila["sector"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["pagaduria"]))."\"";
	
	if (!$_REQUEST["resumidob"])
	{
		$registro .= $separador_csv."\"".$fila["pa"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["institucion"]))."\"".$separador_csv."\"".$fila["meses_antes_65"]."\"".$separador_csv."\"".$fila["fecha_inicio_labor"]."\"".$separador_csv."\"".$fila["medio_contacto"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["telefono"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["celular"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["direccion"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["municipio"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["email"]))."\"".$separador_csv.$fila["sin_aportes_ley"].$separador_csv."\"".$fila["salario_basico"]."\"".$separador_csv."\"".$fila["adicionales"]."\"";
		
		if ($_SESSION["FUNC_MUESTRACAMPOS2"])
			$registro .= $separador_csv."\"".$fila["bonificacion"]."\"";
		
		$registro .= $separador_csv."\"".$fila["total_ingresos"]."\"".$separador_csv."\"".$fila["aportes"]."\"".$separador_csv."\"".$fila["otros_aportes"]."\"".$separador_csv."\"".$fila["total_aportes"]."\"".$separador_csv."\"".$fila["total_egresos"]."\"".$separador_csv."\"".$fila["ingresos_menos_aportes"]."\"".$separador_csv."\"".$fila["salario_libre"]."\"".$separador_csv."\"".$fila["nivel_contratacion"]."\"".$separador_csv."\"".$fila["embargo_actual"]."\"".$separador_csv."\"".$fila["historial_embargos"]."\"".$separador_csv."\"".$fila["embargo_centrales"]."\"".$separador_csv."\"".$fila["clave"]."\"".$separador_csv."\"".$fila["puntaje_datacredito"]."\"".$separador_csv."\"".$fila["puntaje_cifin"]."\"".$separador_csv."\"".$fila["calif_sector_financiero"]."\"".$separador_csv."\"".$fila["calif_sector_real"]."\"".$separador_csv."\"".$fila["calif_sector_cooperativo"]."\"";
	}
	
	$registro .= $separador_csv."\"".utf8_decode($fila["unidad_negocio"])."\"".$separador_csv."\"".$fila["tasa_interes"]."\"".$separador_csv."\"".$fila["plazo"]."\"";
	
	if (!$_REQUEST["resumidob"])
	{
		$registro .= $separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["plan_seguro"]))."\"".$separador_csv."\"".$fila["valor_seguro"]."\"";
		
		$queryDB = "select scc.consecutivo, ent.nombre as nombre_entidad, scc.se_compra, scc.entidad, scc.cuota, scc.valor_pagar from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '".$fila["id_simulacion"]."' order by scc.consecutivo";
		
		$rs2 = sqlsrv_query($link, $queryDB);
		
		$fila2 = sqlsrv_fetch_assoc($rs2);
		
		for ($i = 1; $i <= 10; $i++)
		{
			if ($fila2["consecutivo"] && $fila2["consecutivo"] == $i)
			{
				$nombre_entidad_biz = $fila2["nombre_entidad"];
				$entidad_biz = $fila2["entidad"];
				$cuota_biz = $fila2["cuota"];
				$valor_pagar_biz = $fila2["valor_pagar"];
				$se_compra_biz = $fila2["se_compra"];
				
				$fila2 = sqlsrv_fetch_assoc($rs2);
			}
			else
			{
				$nombre_entidad_biz = "";
				$entidad_biz = "";
				$cuota_biz = "0";
				$valor_pagar_biz = "0";
				$se_compra_biz = "SI";
			}
			
			//$registro .= $separador_csv."\"".str_replace("\"", "'", utf8_decode($nombre_entidad_biz))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($entidad_biz))."\"".$separador_csv."\"".$cuota_biz."\"".$separador_csv."\"".$valor_pagar_biz."\"".$separador_csv."\"".$se_compra_biz."\"";
		}
	}
	
	$registro .= $separador_csv."\"".$fila["total_cuota"]."\"";
	
	if (!$_REQUEST["resumidob"])
		$registro .= $separador_csv."\"".$fila["total_valor_pagar"]."\"".$separador_csv."\"".$fila["total_se_compra"]."\"".$separador_csv."\"".$fila["retanqueo1_libranza"]."\"".$separador_csv."\"".$fila["retanqueo1_valor"]."\"".$separador_csv."\"".$fila["retanqueo2_libranza"]."\"".$separador_csv."\"".$fila["retanqueo2_valor"]."\"".$separador_csv."\"".$fila["retanqueo3_libranza"]."\"".$separador_csv."\"".$fila["retanqueo3_valor"]."\"";
		
	$registro .= $separador_csv."\"".$fila["retanqueo_total"]."\"";
	
	if (!$_REQUEST["resumidob"])
		$registro .= $separador_csv."\"".$fila["opcion_credito"]."\"";
	
	$registro .= $separador_csv."\"".$opcion_cuota."\"".$separador_csv."\"".$cuota_corriente."\"".$separador_csv."\"".$seguro."\"".$separador_csv."\"".$fila["descuento1"]."\"".$separador_csv."\"".round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento1"] / 100.00, 0)."\"".$separador_csv."\"".$fila["descuento2"]."\"".$separador_csv."\"".round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento2"] / 100.00, 0)."\"".$separador_csv."\"".$fila["descuento3"]."\"".$separador_csv."\"".round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento3"] / 100.00, 0)."\"".$separador_csv."\"".$fila["descuento4"]."\"".$separador_csv."\"".round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila["descuento4"] / 100.00, 0)."\"";
	
	if ($fila["recuperate"] == "SI")
	{
		$registro .= $separador_csv."\"".$fila["descuento5"]."\"";
		
		if ($fila["fidelizacion"])
			$registro .= $separador_csv."\"".round($fila["retanqueo_total"] * $fila["descuento5"] / 100.00, 0)."\"";
		else
			$registro .= $separador_csv."\"".round($fila["valor_credito"] * $fila["descuento5"] / 100.00, 0)."\"";
		
		$registro .= $separador_csv."\"".$fila["descuento6"]."\"";
		
		if ($fila["fidelizacion"])
			$registro .= $separador_csv."\"".round($fila["retanqueo_total"] * $fila["descuento6"] / 100.00, 0)."\"";
		else
			$registro .= $separador_csv."\"".round($fila["valor_credito"] * $fila["descuento6"] / 100.00, 0)."\"";
	}
	else
	{
		$registro .= $separador_csv."\"\"".$separador_csv."\"\"".$separador_csv."\"\"".$separador_csv."\"\"";
	}
	
	$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales order by pagaduria, id_descuento");
	
	while ($fila1 = sqlsrv_fetch_assoc($descuentos_adicionales))
	{
		$existe_descuento = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' AND id_descuento = '".$fila1["id_descuento"]."'");
		
		if (sqlsrv_num_rows($existe_descuento))
		{
			$porcentaje = $fila1["porcentaje"];
			$valor_descuento = round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
			
			$total_descuentos_adicionales[$fila1["id_descuento"]] += $valor_descuento;
		}
		else
		{
			$porcentaje = 0;
			$valor_descuento = 0;
		}
		
		$registro .= $separador_csv."\"".$porcentaje."\"".$separador_csv."\"".$valor_descuento."\"";
	}
	
	if ($fila["fidelizacion"])
		$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento3"] + $fila["descuento4"]) / 100.00 + $fila["retanqueo_total"] * (($fila["recuperate"] == "SI"?$fila["descuento5"]:0) + ($fila["recuperate"] == "SI"?$fila["descuento6"]:0)) / 100.00;
	else
		$administrativos = ($fila["valor_credito"] - $fila["retanqueo_total"]) * ($fila["descuento1"] + $fila["descuento2"] + $fila["descuento3"] + $fila["descuento4"]) / 100.00 + $fila["valor_credito"] * (($fila["recuperate"] == "SI"?$fila["descuento5"]:0) + ($fila["recuperate"] == "SI"?$fila["descuento6"]:0)) / 100.00;
	
	$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$fila["id_simulacion"]."' order by id_descuento");
	
	while ($fila1 = sqlsrv_fetch_assoc($descuentos_adicionales))
	{
		$administrativos += round(($fila["valor_credito"] - $fila["retanqueo_total"]) * $fila1["porcentaje"] / 100.00);
	}
	
	$registro .= $separador_csv."\"".$fila["descuento_transferencia"]."\"".$separador_csv."\"".$fila["recuperate"]."\"".$separador_csv."\"".round($administrativos, 0)."\"".$separador_csv."\"".$opcion_desembolso."\"".$separador_csv."\"".($opcion_desembolso - $fila["retanqueo_total"])."\"".$separador_csv."\"".$fila["desembolso_cliente"]."\"".$separador_csv."\"".$fila["estado"]."\"".$separador_csv."\"".$fila["decision"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["causal"]))."\"".$separador_csv."\"".$fila["nro_libranza"]."\"".$separador_csv."\"".$fila["valor_visado"]."\"".$separador_csv."\"".$fila["bloqueo_cuota_x"]."\"".$separador_csv."\"".$fila["bloqueo_cuota_valor"]."\"".$separador_csv."\"".$fila["fecha_llamada_cliente"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre_etapa"]))."\"".$separador_csv."\"".$fila["nombre_subestado"]."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["caracteristica"]))."\"".$separador_csv."\"".$fila["validado_x"]."\"".$separador_csv."\"".$fila["usuario_validacion"]."\"".$separador_csv."\"".$fila["fecha_validacion"]."\"".$separador_csv."\"".$fila["incorporado_x"]."\"".$separador_csv."\"".$fila["porcentaje_extraprima"]."\"".$separador_csv."\"".$fila["formulario_seguro_x"]."\"";
	
	if ($_SESSION["FUNC_MUESTRACAMPOS1"])
		$registro .= $separador_csv."\"".$fila["valor_credito"]."\"";

	if (!$_REQUEST["resumidob"])
		$registro .= $separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre_analista_gestion_comercial"]." ".$fila["apellido_analista_gestion_comercial"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre_analista_riesgo_operativo"]." ".$fila["apellido_analista_riesgo_operativo"]))."\"".$separador_csv."\"".str_replace("\"", "'", utf8_decode($fila["nombre_analista_riesgo_crediticio"]." ".$fila["apellido_analista_riesgo_crediticio"]))."\"";
	
	$registro .= $separador_csv."\"".$fila["fecha_aprobado"]."\"";

	if (!$_REQUEST["resumidob"])
		$registro .= $separador_csv."\"".$fila["usuario_incorporacion"]."\"".$separador_csv."\"".$fila["fecha_incorporacion"]."\"".$separador_csv."\"".$fila["usuario_desistimiento"]."\"".$separador_csv."\"".$fila["fecha_desistimiento"]."\"".$separador_csv."\"".$fila["usuario_creacion"]."\"".$separador_csv."\"".$fila["fecha_creacion"]."\"".$separador_csv."\"".$fila["usuario_modificacion"]."\"".$separador_csv."\"".$fila["fecha_modificacion"]."\"";
		
	$registro .= $separador_csv."\"".$fila["sin_seguro_x"]."\"";
    $data[]=array(trim("id_simulacion")=>trim(($fila["id_simulacion"])),
    trim("fecha_estudio")=>trim($fila["fecha_estudio"]),
    trim("cedula")=>trim($fila["cedula"]),
    trim("nombre")=>trim($fila["nombre"]),
    trim("pagaduria")=>trim($fila["pagaduria"]),
    trim("institucion")=>trim($fila["institucion"])
  );
}

$results = array(
    trim("sEcho") => trim("1"),
    trim("iTotalRecords") => trim(count($data)),
    trim("iTotalDisplayRecords") => trim(count($data)),
    trim("aaData") => $data
  );

echo json_encode($results); 
?>
