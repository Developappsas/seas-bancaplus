<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

$separador = ";";

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "SELECT * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

header('Content-type: text/csv');
header("Content-Disposition: attachment; filename=Coltefinanciera - Clientes Nuevo ".$venta["nro_venta"].".csv");
header("Pragma: no-cache");
header("Expires: 0");

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT si.id_simulacion, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.sexo, FORMAT(so.fecha_nacimiento, 'dd/MM/yyyy') as fecha_nacimiento, so.lugar_nacimiento, so.tipo_documento, si.cedula, FORMAT(so.fecha_expedicion, 'dd/MM/yyyy') as fecha_expedicion, so.lugar_expedicion, so.estado_civil, so.personas_acargo, so.nivel_estudios, so.email, so.tipo_vivienda, so.direccion, so.residencia_barrio, so.ciudad, ci.municipio, ci.departamento, so.residencia_estrato, so.tel_residencia, so.celular, so.ocupacion, si.nivel_contratacion, so.nombre_empresa, si.pagaduria, so.direccion_trabajo, so.ciudad_trabajo, so.telefono_trabajo, so.tipo_contrato, FORMAT(so.fecha_vinculacion, 'dd/MM/yyyy') as fecha_vinculacion, so.cargo, si.total_ingresos, si.total_egresos, so.total_activos, so.total_pasivos, so.declara_renta, so.moneda_extranjera, si.valor_credito, si.puntaje_datacredito, si.retanqueo1_libranza, si.retanqueo1_valor, si.retanqueo2_libranza, si.retanqueo2_valor, si.retanqueo3_libranza, si.retanqueo3_valor, pp.nit as nit_pagaduria, pp.pa, si.nro_libranza, si.calif_sector_financiero, FORMAT(getdate(), 'dd/MM/yyyy') as fecha_ultima_actualizacion, so.funcionario_publico, so.recursos_publicos, so.personaje_publico 
	from ventas_detalle vd 
	INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion 
	LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria 
	LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
	where vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}
else
{
	$queryDB = "SELECT si.id_simulacion, si.nombre1, si.nombre2, si.apellido1, si.apellido2, '' as sexo, FORMAT(so.fecha_nacimiento, 'dd/MM/yyyy') as fecha_nacimiento, '' as lugar_nacimiento, '' as tipo_documento, si.cedula, '' as fecha_expedicion, '' as lugar_expedicion, '' as estado_civil, '' as personas_acargo, '' as nivel_estudios, '' as email, '' as tipo_vivienda, si.direccion, '' as residencia_barrio, '' as ciudad, '' as municipio, '' as departamento, '' as residencia_estrato, si.telefono as tel_residencia, si.celular, '' as ocupacion, '' as nivel_contratacion, '' as nombre_empresa, si.pagaduria, '' as direccion_trabajo, '' as ciudad_trabajo, '' as telefono_trabajo, '' as tipo_contrato, '' as fecha_vinculacion, '' as cargo, 0 as total_ingresos, 0 as total_egresos, 0 as total_activos, 0 as total_pasivos, '' as declara_renta, '' as moneda_extranjera, si.plazo, si.valor_credito, '' as puntaje_datacredito, '' as retanqueo1_libranza, 0 as retanqueo1_valor, '' as retanqueo2_libranza, 0 as retanqueo2_valor, '' as retanqueo3_libranza, 0 as retanqueo3_valor, pp.nit as nit_pagaduria, pp.pa, si.nro_libranza, '' as calif_sector_financiero, FORMAT(CURDATE(), 'dd/MM/yyyy') as fecha_ultima_actualizacion, '' as funcionario_publico, '' as recursos_publicos, '' as personaje_publico 
	from ventas_detalle".$sufijo." vd 
	INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion 
	INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre 
	LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion 
	LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria 
	LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio 
	where vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"])
	{
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
}

$queryDB .= " order by CAST(si.cedula as unsigned), vd.id_ventadetalle";

$rs = sqlsrv_query($link, $queryDB);



while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$primer_nombre = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["nombre1"]))), 0, 35);
	$segundo_nombre = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["nombre2"]))), 0, 35);
	$primer_apellido = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["apellido1"]))), 0, 35);
	$segundo_apellido = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["apellido2"]))), 0, 35);
	
	$genero	= $fila["sexo"];
	$fecha_nacimiento = $fila["fecha_nacimiento"];
	$pais_nacimiento = "COL";
	$ciudad_nacimiento = str_pad($fila["lugar_nacimiento"], 5, 0, STR_PAD_LEFT);
	
	$tipo_identificacion = "";
	
	switch($fila["tipo_documento"])
	{
		case "CEDULA": $tipo_identificacion = "C"; break;
		case "CEDULA EXTRANGERIA": $tipo_identificacion = "CE"; break;
		case "TARJETA IDENTIDAD": $tipo_identificacion = "T"; break;
		case "REGISTRO CIVIL": $tipo_identificacion = "RC"; break;
	}
	
	$numero_documento = $fila["cedula"];
	$fecha_expedicion_documento = $fila["fecha_expedicion"];
	$ciudad_expedicion_documento = str_pad($fila["lugar_expedicion"], 5, 0, STR_PAD_LEFT);
	
	$estado_civil = "";
	
	switch($fila["estado_civil"])
	{
		case "SOLTERO": $estado_civil = "1"; break;
		case "UNION LIBRE": $estado_civil = "4"; break;
		case "CASADO": $estado_civil = "2"; break;
		case "DIVORCIADO": $estado_civil = "7"; break;
		case "SEPARADO": $estado_civil = "5"; break;
		case "VIUDO": $estado_civil = "3"; break;
	}
	
	$personas_cargo = is_numeric(intval(trim($fila["personas_acargo"]))) ? intval(trim($fila["personas_acargo"])) : 0;
	$profesion = "87";
	
	$nivel_estudios = "";
	
	switch($fila["nivel_estudios"])
	{
		case "PRIMARIA": $nivel_estudios = "1"; break;
		case "BACHILLER": $nivel_estudios = "2"; break;
		case "TECNICO": $nivel_estudios = "5"; break;
		case "TECNOLOGO": $nivel_estudios = "5"; break;
		case "UNIVERSITARIO": $nivel_estudios = "3"; break;
		case "ESPECIALIZACION": $nivel_estudios = "6"; break;
		case "MAESTRIA": $nivel_estudios = "6"; break;
		case "DOCTORADO": $nivel_estudios = "6"; break;
	}
	
	if (strlen(trim($fila["email"])) < 7 || substr(trim($fila["email"]), 0, 1) == "@" || strpos($fila["email"], "@") === false)
		$email_personal = "";
	else
		$email_personal = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["email"]))), 0, 50);
	
	$tipo_vivienda = "";
	
	switch($fila["tipo_vivienda"])
	{
		case "FAMILIAR": $tipo_vivienda = "F"; break;
		case "ARRENDADA": $tipo_vivienda = "A"; break;
		case "PROPIA": $tipo_vivienda = "P"; break;
	}
	
	if (strlen(trim($fila["direccion"])) < 8)
		$direccion_residencia = $fila["municipio"]." ".$fila["departamento"];
	else
		$direccion_residencia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 70);
	
	$barrio_residencia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["residencia_barrio"]))), 0, 50);
	
	if (!$barrio_residencia)
		$barrio_residencia = $fila["municipio"]." ".$fila["departamento"];
	
	$ciudad_residencia = str_pad($fila["ciudad"], 5, 0, STR_PAD_LEFT);
	
	if (strlen(trim($fila["direccion"])) < 8)
		$direccion_correspondencia = $fila["municipio"]." ".$fila["departamento"];
	else
		$direccion_correspondencia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 70);
	
	$barrio_correspondecia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["residencia_barrio"]))), 0, 50);
	
	if (!$barrio_correspondecia)
		$barrio_correspondecia = $barrio_residencia;
	
	$ciudad_correspondencia = str_pad($fila["ciudad"], 5, 0, STR_PAD_LEFT);
	$estrato = is_numeric(intval(trim($fila["residencia_estrato"]))) ? intval(trim($fila["residencia_estrato"])) : 0;
	
	if (strlen(substr(trim($fila["celular"]), 0, 10)) == 10)
		$celular = is_numeric(substr(trim($fila["celular"]), 0, 10)) ? substr(trim($fila["celular"]), 0, 10) : 0;
	else
		$celular = "";
	
	if (!$celular)
		$celular = "3502953532"; //Cel Kredit Plus
	
	if (strlen(trim($fila["tel_residencia"])) < 7)
		$telefono_residencia = $celular;
	else
		$telefono_residencia = is_numeric(substr(trim($fila["tel_residencia"]), 0, 10)) ? substr(trim($fila["tel_residencia"]), 0, 10):$celular;
	
	if (!$telefono_residencia)
		$telefono_residencia = "3160818"; //Tel Kredit Plus
	
	$ocupacion = "";
	
	switch($fila["ocupacion"])
	{
		case "1":	$ocupacion = "E"; break;
		case "2":	$ocupacion = "E"; break;
		case "3":	$ocupacion = "I"; break;
		case "4":	$ocupacion = "A"; break;
		case "5":	$ocupacion = "P"; break;
		case "6":	$ocupacion = "S"; break;
		case "7":	$ocupacion = "R"; break;
		case "8":	$ocupacion = "E"; break;
		case "9":	$ocupacion = "E"; break;
		case "99":	$ocupacion = "D"; break;
	}
	
	if (!$ocupacion)
		$ocupacion = "E";
	
	if ($fila["nivel_contratacion"] == "PENSIONADO")
		$ocupacion = "P";
	
	if ($ocupacion != "P")
		$empresa_labora = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["nombre_empresa"]))), 0, 150);
	else
		$empresa_labora = substr(trim($fila["pagaduria"]), 0, 150);
	
	if (!$empresa_labora)
		$empresa_labora = substr(trim($fila["pagaduria"]), 0, 150);
	
	if (strlen(trim($fila["direccion_trabajo"])) < 8)
		$direccion_laboral = "";
	else
		$direccion_laboral = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["direccion_trabajo"]))), 0, 70);
	
	if (!$direccion_laboral)
		$direccion_laboral = $direccion_residencia;
	
	if (strlen(trim($fila["ciudad_trabajo"])) < 3)
		$barrio_laboral = "";
	else
		$barrio_laboral = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["ciudad_trabajo"]))), 0, 50);
	
	if (!$barrio_laboral)
		$barrio_laboral = $barrio_residencia;
	
	$ciudad_laboral = $ciudad_residencia;
	
	if (strlen(trim($fila["telefono_trabajo"])) < 7)
		$telefono_oficina = "";
	else
		$telefono_oficina = is_numeric(substr(trim($fila["telefono_trabajo"]), 0, 10)) ? substr(trim($fila["telefono_trabajo"]), 0, 10) : (is_numeric(substr(trim($fila["celular"]), 0, 10)) ? substr(trim($fila["celular"]), 0, 10) : 0);
 	
	$extension_oficina = "";
	$email_laboral = "";
	
	$tipo_contrato = "";
	
	if ($ocupacion != "P")
	{
		switch($fila["tipo_contrato"])
		{
			case "1":	$tipo_contrato = "I"; break;
			case "2":	$tipo_contrato = "F"; break;
			case "4":	$tipo_contrato = "F"; break;
			case "5":	$tipo_contrato = "F"; break;
		}
	}
	else
		$tipo_contrato = "N";
	
	if (!$tipo_contrato)
		$tipo_contrato = "F";
	
	$fecha_ingreso = $fila["fecha_vinculacion"];
	
	if (!$fecha_ingreso)
		$fecha_ingreso = "08/04/2013";
	
	if ($ocupacion != "P")
		$cargo_actual = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["cargo"]))), 0, 20);
	else
		$cargo_actual = "PENSIONADO";
	
	if (!$cargo_actual)
		$cargo_actual = "DOCENTE";
	
	$salario_mensual = $fila["total_ingresos"];
	$honorarios_comisiones = "0";
	$otros_ingresos = "0";
	$total_ingresos = $fila["total_ingresos"];
	$gastos_familiares = round($fila["total_ingresos"] * 0.2);
	$arrendamiento = "0";
	$otros_creditos = "0";
	$deducciones_nomina = $fila["total_egresos"];
	$otros_egresos = "0";
	$total_egresos = $gastos_familiares + $deducciones_nomina;
	$total_activos = is_numeric(substr(trim($fila["total_activos"]), 0, 9)) ? substr(trim($fila["total_activos"]), 0, 9) : 0;
	$total_pasivos = is_numeric(substr(trim($fila["total_pasivos"]), 0, 9)) ? substr(trim($fila["total_pasivos"]), 0, 9) : 0;
	$codigo_ciiu = ($ocupacion == "P") ? "6604" : "10";
	$declarante = ($fila["declara_renta"] == "SI") ? "SI" : "NO";
	$operaciones_moneda_extranjera = ($fila["moneda_extranjera"] == "SI") ? "SI" : "NO";
	$importaciones = "NO";
	$credito_documentario = "NO";
	$giros_directos = "NO";
	$negociacion_divisas = "NO";
	$leasing_importacion = "NO";
	$giros_financiados = "NO";
	$exportaciones = "NO";
	$inversiones = "NO";
	$otros = ($fila["moneda_extranjera"] == "SI") ? "SI" : "NO";
	$salario = ($ocupacion == "P") ? "NO" : "SI";
	$patrimonio = "NO";
	$venta_bienes = "NO";
	$honorarios_comisiones2 = "NO";
	$ingresos_por_actividad = "NO";
	$prestamo_bancario = "NO";
	$rifas = "NO";
	$pension = ($ocupacion == "P") ? "SI" : "NO";
	$herencia = "NO";
	$liquidacion_prestaciones = "NO";
	$liquidacion_sucesiones = "NO";
	$otros2 = "NO";
	$pension = ($ocupacion == "P") ? "SI" : "NO";
	$clase_cliente = ($ocupacion == "P") ? "7" : "5";
	$cupo_solicitado = $fila["valor_credito"];
	$puntaje_acierta = $fila["puntaje_datacredito"];
	
	if ($fila["puntaje_datacredito"] >= 600)
		$calificacion_cliente = "A";
	else
		$calificacion_cliente = "B";
	
	$total_retanqueos = 0;
	
	for ($i = 1; $i <= 3; $i++)
	{
		if ($fila["retanqueo".$i."_libranza"] && $fila["retanqueo".$i."_valor"])
		{
			$total_retanqueos += $fila["retanqueo".$i."_valor"];
		}
	}
	
	if ($total_retanqueos)
	{
		$tipo_libranza = "3";
	}
	else
	{
		$total_compras_cartera = 0;
		
		if (!$_REQUEST["ext"])
		{
			$queryDB = "select SUM(CASE WHEN se_compra = 'SI' AND (id_entidad IS NOT NULL or (entidad IS NOT NULL AND entidad <> '')) THEN valor_pagar ELSE 0 END) as s from simulaciones_comprascartera where id_simulacion = '".$fila["id_simulacion"]."'";
			
			$rs1 = sqlsrv_query($link, $queryDB);
			
			$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
			
			if ($fila1["s"])
				$total_compras_cartera = $fila1["s"];
		}
		
		if ($total_compras_cartera)
			$tipo_libranza = "1";
		else
			$tipo_libranza = "4";
	}
	
	$nit_originador = "900387878";
	$razon_social_originador = "KREDIT PLUS SAS";
	$nit_cooperativa = "";
	$razon_social_cooperativa = "";
	$nit_pagaduria = substr(trim($fila["nit_pagaduria"]), 0, 9);
	$razon_social_pagaduria = substr(trim($fila["pagaduria"]), 0, 150);
	$nit_fideicomiso = ($fila["pa"] == "ESEFECTIVO") ? "901076840" : "901077484";
	$razon_social_fideicomiso = ($fila["pa"] == "ESEFECTIVO") ? "PATRIMONIO AUTONOMO ESEFECTIVO" : "PATRIMONIO AUTONOMO KREDIT";
	$codigo_fideicomiso = ($fila["pa"] == "ESEFECTIVO") ? "71864" : "71866";
	$nombre_fideicomiso = ($fila["pa"] == "ESEFECTIVO") ? "PATRIMONIO AUTONOMO ESEFECTIVO" : "PATRIMONIO AUTONOMO KREDIT";
	$numero_autorizacion_fenalco = "";
	$numero_pagare = trim(preg_replace("/\D/", "", $fila["nro_libranza"]));
	$tipo_cliente = "N";
	$nit_establecimiento_comercio = "";
	
	$tipo_cliente_data = $fila["calif_sector_financiero"];
	
	if (!$tipo_cliente_data)
		$tipo_cliente_data = $calificacion_cliente;
	
	$indicador_reestructurado = "";
	$numero_renovaciones = 0;
	$fecha_ultima_actualizacion = $fila["fecha_ultima_actualizacion"];
	
	if ($fila["funcionario_publico"])
		$cliente_es_pep = $fila["funcionario_publico"];
	else
		$cliente_es_pep = "NO";
	
	if ($fila["recursos_publicos"])
		$manejo_recursos_publicos = $fila["recursos_publicos"];
	else
		$manejo_recursos_publicos = "NO";
	
	if ($fila["funcionario_publico"])
		$ejerce_poder_publico = $fila["funcionario_publico"];
	else
		$ejerce_poder_publico = "NO";
	
	if ($fila["personaje_publico"])
		$reconocimiento_publico = $fila["personaje_publico"];
	else
		$reconocimiento_publico = "NO";


	$grupo_etnico="7";
	$medio_contacto="";
	
	$registro = $primer_nombre.$separador.$segundo_nombre.$separador.$primer_apellido.$separador.$segundo_apellido.$separador.$genero.$separador.$fecha_nacimiento.$separador.$pais_nacimiento.$separador.$ciudad_nacimiento.$separador.$tipo_identificacion.$separador.$numero_documento.$separador.$fecha_expedicion_documento.$separador.$ciudad_expedicion_documento.$separador.$estado_civil.$separador.$personas_cargo.$separador.$profesion.$separador.$nivel_estudios.$separador.$email_personal.$separador.$tipo_vivienda.$separador.$direccion_residencia.$separador.$barrio_residencia.$separador.$ciudad_residencia.$separador.$direccion_correspondencia.$separador.$barrio_correspondecia.$separador.$ciudad_correspondencia.$separador.$estrato.$separador.$telefono_residencia.$separador.$celular.$separador.$ocupacion.$separador.$empresa_labora.$separador.$direccion_laboral.$separador.$barrio_laboral.$separador.$ciudad_laboral.$separador.$telefono_oficina.$separador.$extension_oficina.$separador.$email_laboral.$separador.$tipo_contrato.$separador.$fecha_ingreso.$separador.$cargo_actual.$separador.$salario_mensual.$separador.$honorarios_comisiones.$separador.$otros_ingresos.$separador.$total_ingresos.$separador.$gastos_familiares.$separador.$arrendamiento.$separador.$otros_creditos.$separador.$deducciones_nomina.$separador.$otros_egresos.$separador.$total_egresos.$separador.$total_activos.$separador.$total_pasivos.$separador.$codigo_ciiu.$separador.$declarante.$separador.$operaciones_moneda_extranjera.$separador.$importaciones.$separador.$credito_documentario.$separador.$giros_directos.$separador.$negociacion_divisas.$separador.$leasing_importacion.$separador.$giros_financiados.$separador.$exportaciones.$separador.$inversiones.$separador.$otros.$separador.$salario.$separador.$patrimonio.$separador.$venta_bienes.$separador.$honorarios_comisiones2.$separador.$ingresos_por_actividad.$separador.$prestamo_bancario.$separador.$rifas.$separador.$pension.$separador.$herencia.$separador.$liquidacion_prestaciones.$separador.$liquidacion_sucesiones.$separador.$otros2.$separador.$clase_cliente.$separador.$cupo_solicitado.$separador.$puntaje_acierta.$separador.$calificacion_cliente.$separador.$tipo_libranza.$separador.$nit_originador.$separador.$razon_social_originador.$separador.$nit_cooperativa.$separador.$razon_social_cooperativa.$separador.$nit_pagaduria.$separador.$razon_social_pagaduria.$separador.$nit_fideicomiso.$separador.$razon_social_fideicomiso.$separador.$codigo_fideicomiso.$separador.$nombre_fideicomiso.$separador.$numero_autorizacion_fenalco.$separador.$numero_pagare.$separador.$tipo_cliente.$separador.$nit_establecimiento_comercio.$separador.$tipo_cliente_data.$separador.$indicador_reestructurado.$separador.$numero_renovaciones.$separador.$fecha_ultima_actualizacion.$separador.$cliente_es_pep.$separador.$manejo_recursos_publicos.$separador.$ejerce_poder_publico.$separador.$reconocimiento_publico.$separador.$grupo_etnico.$separador.$medio_contacto.$separador;
	
	//$registro = $primer_nombre.$separador.$segundo_nombre.$separador.$primer_apellido.$separador.$segundo_apellido.$separador.$genero.$separador.$fecha_nacimiento.$separador.$pais_nacimiento.$separador.$ciudad_nacimiento.$separador.$tipo_identificacion.$separador.$numero_documento.$separador.$fecha_expedicion_documento.$separador.$ciudad_expedicion_documento.$separador.$estado_civil.$separador.$personas_cargo.$separador.$profesion.$separador.$nivel_estudios.$separador.$email_personal.$separador.$tipo_vivienda.$separador.$direccion_residencia.$separador.$barrio_residencia.$separador.$ciudad_residencia.$separador.$direccion_correspondencia.$separador.$barrio_correspondecia.$separador.$ciudad_correspondencia.$separador.$estrato.$separador.$telefono_residencia.$separador.$celular.$separador.$ocupacion.$separador.$empresa_labora.$separador.$direccion_laboral.$separador.$barrio_laboral.$separador.$ciudad_laboral.$separador.$telefono_oficina.$separador.$extension_oficina.$separador.$email_laboral.$separador.$tipo_contrato.$separador.$fecha_ingreso.$separador.$cargo_actual.$separador.$salario_mensual.$separador.$honorarios_comisiones.$separador.$otros_ingresos.$separador.$total_ingresos.$separador.$gastos_familiares.$separador.$arrendamiento.$separador.$otros_creditos.$separador.$deducciones_nomina.$separador.$otros_egresos.$separador.$total_egresos.$separador.$total_activos.$separador.$total_pasivos.$separador.$codigo_ciiu.$separador.$declarante.$separador.$operaciones_moneda_extranjera.$separador.$importaciones.$separador.$credito_documentario.$separador.$giros_directos.$separador.$negociacion_divisas.$separador.$leasing_importacion.$separador.$giros_financiados.$separador.$exportaciones.$separador.$inversiones.$separador.$otros.$separador.$salario.$separador.$patrimonio.$separador.$venta_bienes.$separador.$honorarios_comisiones2.$separador.$ingresos_por_actividad.$separador.$prestamo_bancario.$separador.$rifas.$separador.$pension.$separador.$herencia.$separador.$liquidacion_prestaciones.$separador.$liquidacion_sucesiones.$separador.$otros2.$separador.$clase_cliente.$separador.$cupo_solicitado.$separador.$puntaje_acierta.$separador.$calificacion_cliente.$separador.$tipo_libranza.$separador.$total_compras_cartera.$separador.$nit_originador.$separador.$razon_social_originador.$separador.$nit_cooperativa.$separador.$razon_social_cooperativa.$separador.$nit_pagaduria.$separador.$razon_social_pagaduria.$separador.$nit_fideicomiso.$separador.$razon_social_fideicomiso.$separador.$codigo_fideicomiso.$separador.$nombre_fideicomiso.$separador.$numero_autorizacion_fenalco.$separador.$numero_pagare.$separador.$tipo_cliente.$separador.$nit_establecimiento_comercio.$separador.$tipo_cliente_data.$separador.$indicador_reestructurado.$separador.$numero_renovaciones.$separador.$fecha_ultima_actualizacion.$separador.$cliente_es_pep.$separador.$manejo_recursos_publicos.$separador.$ejerce_poder_publico.$separador.$reconocimiento_publico.$separador;
	
	echo $registro."\r\n";
}

?>