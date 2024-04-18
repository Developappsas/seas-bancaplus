<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$link = conectar();

$separador = ";";

if (isset($_REQUEST["ext"]) && !empty($_REQUEST["ext"])){
	$sufijo = "_ext";
}else{
	$sufijo = "";
}
	
$queryDB = "select * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";
$venta_rs = sqlsrv_query($link, $queryDB);
$venta = sqlsrv_fetch_array($venta_rs);


header('Content-type: text/csv');
header("Content-Disposition: attachment; filename=Archivo_iris ".$venta["nro_venta"].".csv");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_REQUEST["ext"])){

	$queryDB = "SELECT si.id_simulacion, si.cedula, so.apellido1, so.apellido2, so.nombre1, so.nombre2, so.fecha_nacimiento, CASE WHEN so.residencia_estrato = 'NA' THEN 0 WHEN so.residencia_estrato = 'N/A' THEN 0 WHEN so.residencia_estrato IS NULL THEN 0 ELSE so.residencia_estrato END estrato, so.estado_civil, 1 AS categoria_vivienda, CASE WHEN so.tipo_vivienda = 'PROPIA' THEN 1 WHEN so.tipo_vivienda = 'FAMILIAR' THEN 3 ELSE 2 END tipo_vivienda, iIF(so.sexo = 'M',1,0) AS jefe_hogar, so.nivel_estudios, so.ciudad, so.direccion, so.residencia_barrio, so.email, so.tel_residencia, so.celular, so.sexo, so.profesion, so.nivel_estudios, so.personas_acargo, pp.nit AS id_pagaduria, so.nombre_familiar, so.ciudad_familiar, so.direccion_familiar, so.telefono_familiar, so.nombre_personal, so.ciudad_personal, so.direccion_personal, so.telefono_personal, si.total_ingresos, si.total_egresos, si.puntaje_datacredito, 1 AS consultas_centrales, si.nro_libranza, si.valor_credito, si.plazo, 'M' AS periocidad, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_seguro, 'P' AS modalidad, si.fecha_desembolso, 'F' AS tipo_tasa, si.fecha_primera_cuota, DATE_FORMAT(vd.fecha_primer_pago, 'Y-m-d') as fecha_primer_pago, pa.nombre AS empresa_labora, CASE WHEN si.nivel_contratacion = 'PENSIONADO' THEN 3 ELSE 6 END tipo_contrato, IF (si.fecha_inicio_labor IS NULL, 0, DATEDIFF(MONTH, si.fecha_inicio_labor, GETDATE())) AS meses_trabajando, si.salario_basico, CASE WHEN si.nivel_contratacion = 'ADMINISTRATIVO' THEN 1 ELSE 0 END cargo_administrativo, so.ciudad_trabajo, so.direccion_trabajo, if(so.telefono_trabajo IS NULL, '', so.telefono_trabajo) AS telefono_trabajo, CASE WHEN si.nivel_contratacion = 'PENSIONADO' THEN 2 ELSE 1 END actividad_economica, CASE WHEN si.formato_digital IS NULL THEN 'FISICO' WHEN si.formato_digital = 1 THEN 'DIGITAL' ELSE 'FISICO' END canal, iif(fd.pagare_deceval IS NULL, '', fd.pagare_deceval) AS pagare_deceval, so.ciudad, ci.municipio, ci.departamento, so.ocupacion, pp.pa, si.calif_sector_financiero, FORMAT(GETDATE(), 'd/m/Y') AS fecha_ultima_actualizacion, so.total_activos, so.total_pasivos, FORMAT(so.fecha_vinculacion, 'Y-m-d') as fecha_vinculacion
			FROM ventas_detalle vd
			INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion
			INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre
			LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion
			LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria
			LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio
			LEFT JOIN formulario_digital fd ON fd.id_simulacion = vd.id_simulacion
			WHERE vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"]){
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}

	if ($_SESSION["S_TIPO"] == "COMERCIAL"){
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}
else{
	$queryDB = "SELECT si.id_simulacion, si.cedula, so.apellido1, so.apellido2, so.nombre1, so.nombre2, so.fecha_nacimiento, CASE WHEN so.residencia_estrato = 'NA' THEN 0 WHEN so.residencia_estrato = 'N/A' THEN 0 WHEN so.residencia_estrato IS NULL THEN 0 ELSE so.residencia_estrato END estrato, so.estado_civil, 1 AS categoria_vivienda, CASE WHEN so.tipo_vivienda = 'PROPIA' THEN 1 WHEN so.tipo_vivienda = 'FAMILIAR' THEN 3 ELSE 2 END tipo_vivienda, iIF(so.sexo = 'M',1,0) AS jefe_hogar, so.nivel_estudios, so.ciudad, so.direccion, so.residencia_barrio, so.email, so.tel_residencia, so.celular, so.sexo, so.profesion, so.nivel_estudios, so.personas_acargo, pp.nit AS id_pagaduria, so.nombre_familiar, so.ciudad_familiar, so.direccion_familiar, so.telefono_familiar, so.nombre_personal, so.ciudad_personal, so.direccion_personal, so.telefono_personal, si.total_ingresos, si.total_egresos, si.puntaje_datacredito, 1 AS consultas_centrales, si.nro_libranza, si.valor_credito, si.plazo, 'M' AS periocidad, si.tasa_interes, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, si.valor_seguro, 'P' AS modalidad, si.fecha_desembolso, 'F' AS tipo_tasa, si.fecha_primera_cuota, FORMAT(vd.fecha_primer_pago, 'Y-m-d') as fecha_primer_pago, pa.nombre AS empresa_labora, CASE WHEN si.nivel_contratacion = 'PENSIONADO' THEN 3 ELSE 6 END tipo_contrato, IF (si.fecha_inicio_labor IS NULL, 0, DATEDIFF(MONTH, si.fecha_inicio_labor, GETDATE())) AS meses_trabajando, si.salario_basico, CASE WHEN si.nivel_contratacion = 'ADMINISTRATIVO' THEN 1 ELSE 0 END cargo_administrativo, so.ciudad_trabajo, so.direccion_trabajo, if(so.telefono_trabajo IS NULL, '', so.telefono_trabajo) AS telefono_trabajo, CASE WHEN si.nivel_contratacion = 'PENSIONADO' THEN 2 ELSE 1 END actividad_economica, CASE WHEN si.formato_digital IS NULL THEN 'FISICO' WHEN si.formato_digital = 1 THEN 'DIGITAL' ELSE 'FISICO' END canal, iif(fd.pagare_deceval IS NULL, '', fd.pagare_deceval) AS pagare_deceval, so.ciudad, ci.municipio, ci.departamento, so.ocupacion, pp.pa, si.calif_sector_financiero, FORMAT(GETDATE(), 'd/m/Y') AS fecha_ultima_actualizacion, so.total_activos, so.total_pasivos, FORMAT(so.fecha_vinculacion, 'Y-m-d') as fecha_vinculacion

			FROM ventas_detalle".$sufijo." vd 
			INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion 
			LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre 
			LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria 
			LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion
			LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio
			LEFT JOIN formulario_digital fd ON fd.id_simulacion = vd.id_simulacion
			WHERE vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	if ($_SESSION["S_SECTOR"]){
		$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
	}
}

$queryDB .= " order by  nro_libranza, CAST(si.cedula as unsigned), vd.id_ventadetalle";

$rs = sqlsrv_query($link, $queryDB);
$contador = 0;

$registro = "Num".$separador."IdTercero".$separador."TipoIdentificacion".$separador."PrimerApellido".$separador."SegundoApellido".$separador."PrimerNombre".$separador."OtrosNombres".$separador."FechaNacimiento".$separador."Estrato".$separador."EstadoCivil".$separador."CategoriaVivienda".$separador."TipoVivienda".$separador."EsJefeHogar".$separador."NivelEstudio".$separador."CiudadResidencia".$separador."DireccionResidencia".$separador."BarrioResidencia".$separador."Correo".$separador."Telefono1".$separador."Telefono2".$separador."Celular".$separador."Sexo".$separador."Profesion".$separador."Escolaridad".$separador."ParentescoJefeHogar".$separador."NumeroPersonasACargo".$separador."UltimoNivelEstudios".$separador."IdPagaduria".$separador."IdCoorperativa".$separador."PrimerApellidoRef1".$separador."SegundoApellidoRef1".$separador."PrimerNombreRef1".$separador."SegundoNombreRef1".$separador."CiudadResidenciaRef1".$separador."DireccionResidenciaRef1".$separador."BarrioResidenciaRef1".$separador."CorreoRef1".$separador."TelefonoRef1".$separador."PrimerApellidoRef2".$separador."SegundoApellidoRef2".$separador."PrimerNombreRef2".$separador."SegundoNombreRef2".$separador."CiudadResidenciaRef2".$separador."DireccionResidenciaRef2".$separador."BarrioResidenciaRef2".$separador."CorreoRef2".$separador."TelefonoRef2".$separador."Ingresos".$separador."Egresos".$separador."Endeudamiento".$separador."CalificacionCentrales".$separador."CantidadObligaciones Mora".$separador."CantidadObligacionesReestructuradas".$separador."Scoring".$separador."FechaScoring".$separador."TieneCarteraCastigada".$separador."TieneEmbargos".$separador."CalificacionBajaDeudor".$separador."CalificacionBajaCodeudor".$separador."CupoTarjetasCredito".$separador."SaldoTarjetasCredito".$separador."DiasMoraMasAlto".$separador."CuotasCreditos".$separador."ConsultasEnCentrales".$separador."NumeroObligacion".$separador."MontoInicial".$separador."Saldo".$separador."PlazoInicial".$separador."PlazoRestante".$separador."Periodicidad".$separador."Tasa".$separador." Cuota".$separador."Valor del Seguro".$separador."Modalidad".$separador."FechaDesembolso".$separador."TipoTasa".$separador."FechaPrimerPago".$separador."EmpresaDondeLabora".$separador."TipoContrato".$separador."AntiguedadLaboral(meses)".$separador."SalarioMensual".$separador."Deducciones".$separador."TieneCargoAdministrativo".$separador."CiudadEmpresa".$separador."DireccionEmpresa".$separador."BarrioEmpresa".$separador."CorreoEmpresa".$separador."TelefonoEmpresa".$separador."ActividadEconomica".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Calificacion cliente".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Dias mora operacion".$separador."Mecanismo Normalizacion".$separador."Tasa 2".$separador."No de Venta de Originador".$separador."ID_OLIMPIA".$separador."SECTOR".$separador."Canal".$separador."CODIGO DECEVAL".$separador;

echo $registro."\r\n";

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){

	$contador++;
	$numero_documento = $fila["cedula"];
	$tipo_persona = 1; //1- Persona natural, 3 persona juridica

	$primer_apellido = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["apellido1"]))), 0, 35);
	$segundo_apellido = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["apellido2"]))), 0, 35);
	$primer_nombre = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["nombre1"]))), 0, 35);
	$segundo_nombre = substr(trim(preg_replace("/[^".$normal_characters_nombres."/", "", reemplazar_caracteres_no_utf($fila["nombre2"]))), 0, 35);
	
	$fecha_nacimiento = $fila["fecha_nacimiento"];
	$estrato = is_numeric(intval(trim($fila["estrato"]))) ? intval(trim($fila["estrato"])) : 0;
	$estado_civil = "";

	$nit_pagaduria = '';
	$total_activos = 0;
	$total_pasivos = 0;
	
	switch($fila["estado_civil"]){
		case "SOLTERO": $estado_civil = "1"; break;
		case "CASADO": $estado_civil = "2"; break;
		case "UNION LIBRE": $estado_civil = "3"; break;
		case "SEPARADO": $estado_civil = "4"; break;
		case "VIUDO": $estado_civil = "5"; break;
		case "DIVORCIADO": $estado_civil = "6"; break;
	}

	$categoria_vivienda	= $fila["categoria_vivienda"];
	$tipo_vivienda = $fila["tipo_vivienda"];
	$jefe_hogar = $fila["jefe_hogar"];
	
	$ciudad_residencia = str_pad($fila["ciudad"], 5, 0, STR_PAD_LEFT);	
	
	if (strlen(trim($fila["direccion"])) < 8)
		$direccion_residencia = $fila["municipio"]." ".$fila["departamento"];
	else
		$direccion_residencia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 70);

	$barrio_residencia = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["residencia_barrio"]))), 0, 50);
	
	if (!$barrio_residencia)
		$barrio_residencia = $fila["municipio"]." ".$fila["departamento"];
	
	if (strlen(trim($fila["email"])) < 7 || substr(trim($fila["email"]), 0, 1) == "@" || strpos($fila["email"], "@") === false)
		$email_personal = "";
	else
		$email_personal = substr(trim(preg_replace("/[^".$normal_characters."/", "", reemplazar_caracteres_no_utf($fila["email"]))), 0, 50);

	if (strlen(substr(trim($fila["celular"]), 0, 10)) == 10)
		$celular = is_numeric(substr(trim($fila["celular"]), 0, 10)) ? substr(trim($fila["celular"]), 0, 10) : 0;
	else
		$celular = "";
	
	if (!$celular)
		$celular = "3502953532"; //Cel Kredit Plus

	if (strlen(trim($fila["tel_residencia"])) < 7)
		$telefono_residencia = $celular;
	else
		$telefono_residencia = is_numeric(substr(trim($fila["tel_residencia"]), 0, 10)) ? substr(trim($fila["tel_residencia"]), 0, 10) : $celular;
	
	if (!$telefono_residencia)
		$telefono_residencia = "3160818"; //Tel Kredit Plus

	if (strlen(trim($fila["telefono_trabajo"])) < 7)
		$telefono_trabajo = $celular;
	else
		$telefono_trabajo = is_numeric(substr(trim($fila["telefono_trabajo"]), 0, 10)) ? substr(trim($fila["telefono_trabajo"]), 0, 10) : $celular;
	
	if (!$telefono_trabajo)
		$telefono_trabajo = "3160818"; //Tel Kredit Plus
	
	$genero	= $fila["sexo"];
	$profesion = "9999"; //9999	OTROS
	$parentesco_jefe_hogar = 0;
	$personas_cargo = is_numeric(intval(trim($fila["personas_acargo"]))) ? intval(trim($fila["personas_acargo"])) : 0;

	$escolaridad = "";
	$nivel_estudios = "6";
	
	switch($fila["nivel_estudios"]){
		case "PRIMARIA": $escolaridad = "2"; break;
		case "BACHILLER": $escolaridad = "3"; $nivel_estudios = "1"; break;
		case "TECNICO": $escolaridad = "4"; $nivel_estudios = "2"; break;
		case "TECNOLOGO": $escolaridad = "5"; $nivel_estudios = "3"; break;
		case "UNIVERSITARIO": $escolaridad = "7"; $nivel_estudios = "4"; break;
		case "ESPECIALIZACION": $escolaridad = "6"; $nivel_estudios = "5"; break;
		case "MAESTRIA": $escolaridad = "6"; $nivel_estudios = "5"; break;
		case "DOCTORADO": $escolaridad = "6"; $nivel_estudios = "5"; break;
	}
	
	$nit_pagaduria = substr(trim($fila["id_pagaduria"]), 0, 9);
	$id_coperativa = 0;

	// $nombre_referencia_familiar = $fila["nombre_familiar"];

	$nombreFa1 ="";
	$nombreFa2 ="";
	$apellidoFa1 ="";
	$apellidoFa2 ="";

	$nombreReferenciaFamiliar = explode(" ", $fila["nombre_familiar"]);
	if(count($nombreReferenciaFamiliar) == 4){
		$nombreFa1 = $nombreReferenciaFamiliar[0];
		$nombreFa2 =$nombreReferenciaFamiliar[1];
		$apellidoFa1 =$nombreReferenciaFamiliar[2];
		$apellidoFa2 =$nombreReferenciaFamiliar[3];
		
	}else{
		$nombreFa1 = $fila["nombre_familiar"];
	}

	$ciudad_referencia_familiar = $fila["ciudad_familiar"];
	$direccion_referencia_familiar = $fila["direccion_familiar"];
	$barrio_referencia = "";
	$correo_referencia = "";
	$telefono_referencia_familiar = $fila["telefono_familiar"];

	// $nombre_referencia_personal = $fila["nombre_personal"];
	$nombreRef1 = "";
	$nombreRef2 = "";
	$apellidoRef1 = "";
	$apellidoRef2 ="";

	$nombreReferenciaPersonal = explode(" ", $fila["nombre_personal"]);

	if(count($nombreReferenciaPersonal) == 4){
		$nombreRef1 = $nombreReferenciaPersonal[0];
		$nombreRef2 = $nombreReferenciaPersonal[1];
		$apellidoRef1 = $nombreReferenciaPersonal[2];
		$apellidoRef2 = $nombreReferenciaPersonal[3];
	}else{
		$nombreRef1 = $fila["nombre_personal"];
	}

	$ciudad_referencia_personal = $fila["ciudad_personal"];
	$direccion_referencia_personal = $fila["direccion_personal"];
	$telefono_referencia_personal = $fila["telefono_personal"];

	$salario_mensual = $fila["total_ingresos"];
	$total_ingresos = $fila["total_ingresos"];
	$gastos_familiares = round($fila["total_ingresos"] * 0.2);
	$deducciones_nomina = $fila["total_egresos"];
	$total_egresos = $gastos_familiares + $deducciones_nomina;
	$total_activos = is_numeric(substr(trim($fila["total_activos"]), 0, 9)) ? substr(trim($fila["total_activos"]), 0, 9) : 0;
	$total_pasivos = is_numeric(substr(trim($fila["total_pasivos"]), 0, 9)) ? substr(trim($fila["total_pasivos"]), 0, 9) : 0;
	$fecha_scoring= "";

	$query = "SELECT fecha_creacion FROM consultas_externas WHERE (cedula= ".$numero_documento." ) and Codigo_respuesta='0' AND servicio='HDC_ACIERTA' ORDER BY id_consulta DESC LIMIT 1";

	if($respuesta = sqlsrv_query($link, $query)){
		if(sqlsrv_num_rows($respuesta) > 0){
			$dtScoring = sqlsrv_fetch_array($respuesta);
			$fecha_scoring = $dtScoring['fecha_creacion'];
		}	
	}

	if ($fila["puntaje_datacredito"] >= 600)
		$calificacion_cliente = "A";
	else
		$calificacion_cliente = "B";

	$tipo_cliente_data = $fila["calif_sector_financiero"];
	
	if (!$tipo_cliente_data)
		$tipo_cliente_data = $calificacion_cliente;

	if ($tipo_cliente_data == 'NA' || $tipo_cliente_data == 'N/A')
		$tipo_cliente_data = "";
	
	$puntaje_acierta = $fila["puntaje_datacredito"];
	$consulta_centrales_si = 1;
	$nro_libranza = trim(preg_replace("/\D/", "", $fila["nro_libranza"]));
	$cupo_solicitado = $fila["valor_credito"];//BN

	$plazo = $fila["plazo"];
	$periodicidad = "M";
	if($fila["tasa_interes"] > 0){
		$tasa_interes = (round(floatval($fila["tasa_interes"]), 2) * 12) / 100  ;
	}else{
		$tasa_interes = $fila["tasa_interes"];
	}

	switch($fila["opcion_credito"]){
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}
	
	$valor_cuota_actual = $opcion_cuota - $fila["valor_seguro"];
	$valor_seguro = $fila["valor_seguro"];
	$modalidad = "P";//CONSUMO
	$fecha_desembolso = $fila["fecha_desembolso"];
	$tipo_tasa = "F";//FIJA
	$empresa_labora = substr(trim($fila["empresa_labora"]), 0, 150);
	$tipo_contrato = $fila["tipo_contrato"];
	$meses_trabajando = $fila["meses_trabajando"];
	$cargo_administrativo = $fila["cargo_administrativo"];

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
	
	if (strlen(trim($fila["telefono_trabajo"])) < 7){
		$telefono_oficina = "";
	}else{
			
		if(is_numeric(substr(trim($fila["telefono_trabajo"]), 0, 10))){
			$telefono_oficina = substr(trim($fila["telefono_trabajo"]), 0, 10);
		}else{ 
			if(is_numeric(substr(trim($fila["celular"]), 0, 10))){
				$telefono_oficina = substr(trim($fila["celular"]), 0, 10);
			}else{ 
				$telefono_oficina = 0;
			}
		}
	}

	$email_laboral = "";
	$actividad_economica = $fila["actividad_economica"];
	$dias_mora_operacion= "0";
	$mecanismo = "";
	$tasa2 = "0";
	$nro_venta = $venta["nro_venta"];
	$id_olimpia = "";
	$sector_texto = "";
	$canal = $fila["canal"];
	$pagare_deceval = $fila["pagare_deceval"];

	$queryDB = "SELECT FORMAT(MAX(cu.fecha), 'Y-m-d') as fecha_vencimiento, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, FORMAT(vd.fecha_primer_pago, 'Y-m-d') as fecha_vencimiento_primera_cuota_vendida, DATEDIFF(ve.fecha, 
    EOMONTH(DATEADD(  MONTH, vd.cuota_desde - 1, EOMONTH(DATEADD(MONTH,-1, si.fecha_primera_cuota))))) as dias_causados, vd.cuota_desde, DATE_FORMAT(ve.fecha, 'Y-m-d') as fecha_venta, cu2.capital as capital_primera_cuota_vendida, cu2.interes as interes_primera_cuota_vendida, cu2.seguro, FORMAT(EOMONTH(DATEADD(MONTH,-1, si.fecha_primera_cuota)), 'Y-m-d') as fecha_primera_cuota, SUM(cu.capital) as saldo_capital
		FROM ventas_detalle vd
		INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion
		INNER JOIN ventas ve ON vd.id_venta = ve.id_venta 
		LEFT JOIN cuotas cu ON vd.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta 
		LEFT JOIN cuotas cu2 ON vd.id_simulacion = cu2.id_simulacion AND cu2.cuota = vd.cuota_desde
		WHERE vd.id_venta = '".$_REQUEST["id_venta"]."' AND vd.id_simulacion = '".$fila["id_simulacion"]."'";
			
	$rs1 = sqlsrv_query($link, $queryDB);
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$saldo_capital = $fila1["saldo_capital"];//BO
	
	$plazo_restante = 0;
	if(intval($fila1["cuotas_vendidas"]) >= 0){
		$plazo_restante = intval($fila["plazo"]) - intval($fila1["cuotas_vendidas"]);
	}
	
	if ($fila1["cuotas_vendidas"] == $fila["plazo"]){
		$saldo_capital = $fila["valor_credito"];
		$plazo_restante = 0;
	}


	$fecha_primer_pago = $fila["fecha_primer_pago"];
	$fecha_ingreso = $fila["fecha_vinculacion"];
	
	if (!$fecha_ingreso){
		$fecha_ingreso = "2013-04-08";
	}
	
	$nit_originador = "900387878";
	$razon_social_originador = "KREDIT PLUS SAS";
	$nit_cooperativa = "";	
	$indicador_reestructurado = "";
	$numero_renovaciones = 0;
	$fecha_ultima_actualizacion = $fila["fecha_ultima_actualizacion"];
	$campo_vacio = "";
	

	$registro = $contador.$separador.$numero_documento.$separador.$tipo_persona.$separador.$primer_apellido.$separador.$segundo_apellido.$separador.$primer_nombre.$separador.$segundo_nombre.$separador.$fecha_nacimiento.$separador.$estrato.$separador.$estado_civil.$separador.$categoria_vivienda.$separador.$tipo_vivienda.$separador.$jefe_hogar.$separador.$nivel_estudios.$separador.$ciudad_residencia.$separador.$direccion_residencia.$separador.$barrio_residencia.$separador.$email_personal.$separador.$telefono_residencia.$separador.$telefono_trabajo.$separador.$celular.$separador.$genero.$separador.$profesion.$separador.$escolaridad.$separador.$parentesco_jefe_hogar.$separador.$personas_cargo.$separador.$nivel_estudios.$separador.$nit_pagaduria.$separador.$id_coperativa.$separador.$apellidoFa1.$separador.$apellidoFa2.$separador.$nombreFa1.$separador.$nombreFa2.$separador.$ciudad_referencia_familiar.$separador.$direccion_referencia_familiar.$separador.$barrio_referencia.$separador.$correo_referencia.$separador.$telefono_referencia_familiar.$separador.$apellidoRef1.$separador.$apellidoRef2.$separador.$nombreRef1.$separador.$nombreRef2.$separador.$ciudad_referencia_personal.$separador.$direccion_referencia_personal.$separador.$barrio_referencia.$separador.$correo_referencia.$separador.$telefono_referencia_personal.$separador.$total_ingresos.$separador.$total_egresos.$separador.$campo_vacio.$separador.$tipo_cliente_data.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$puntaje_acierta.$separador.$fecha_scoring.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$campo_vacio.$separador.$consulta_centrales_si.$separador.$nro_libranza.$separador.$cupo_solicitado.$separador.$saldo_capital.$separador.$plazo.$separador.$plazo_restante.$separador.$periodicidad.$separador.$tasa_interes.$separador.$valor_cuota_actual.$separador.$valor_seguro.$separador.$modalidad.$separador.$fecha_desembolso.$separador.$tipo_tasa.$separador.$fecha_primer_pago.$separador.$empresa_labora.$separador.$tipo_contrato.$separador.$meses_trabajando.$separador.$salario_mensual.$separador.$deducciones_nomina.$separador.$cargo_administrativo.$separador.$ciudad_laboral.$separador.$direccion_laboral.$separador.$barrio_laboral.$separador.$email_laboral.$separador.$telefono_trabajo.$separador.$actividad_economica.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$campo_vacio.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$dias_mora_operacion.$separador.$campo_vacio.$separador.$tasa2.$separador.$nro_venta.$separador.$id_olimpia.$separador.$sector_texto.$separador.$canal.$separador.$pagare_deceval.$separador;


	

		echo $registro."\r\n";
	
	
}

?>