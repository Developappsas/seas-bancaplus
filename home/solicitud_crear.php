<?php 

include ('../functions.php'); 

if (!$_SESSION["S_LOGIN"] || !($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "NEXA"))
{
	exit;
}

?>
<?php include("top.php"); ?>
<?php
$link = conectar_utf();

sqlsrv_query($link, "INSERT INTO solicitud_log SELECT * , '".$_SESSION["S_LOGIN"]."', '".$_REQUEST["section"]."', GETDATE() FROM solicitud WHERE id_simulacion ='".$_REQUEST["id_simulacion"]."'");


if ($_REQUEST["section"] == "0") {
	$sql_update = "UPDATE solicitud SET seccion_info_personal=1,fecha = '".$_REQUEST["fecha"]."', asesor = '".$_REQUEST["asesor"]."', tipo_documento = '".$_REQUEST["tipo_documento"]."', fecha_expedicion = '".$_REQUEST["fecha_expedicion"]."', lugar_expedicion = '".$_REQUEST["lugar_expedicion"]."',  lugar_nacimiento = '".$_REQUEST["lugar_nacimiento"]."', estado_civil = '".$_REQUEST["estado_civil"]."', residencia_pais = '".($_REQUEST["residencia_pais"])."', ciudad = '".$_REQUEST["ciudad"]."', residencia_departamento = '".($_REQUEST["residencia_departamento"])."', tipo_vivienda = '".$_REQUEST["tipo_vivienda"]."', arrendador_nombre = '".($_REQUEST["arrendador_nombre"])."', arrendador_telefono = '".$_REQUEST["arrendador_telefono"]."', arrendador_ciudad = '".$_REQUEST["arrendador_ciudad"]."', residencia_barrio = '".($_REQUEST["residencia_barrio"])."', direccion = '".($_REQUEST["direccion"])."', residencia_estrato = '".$_REQUEST["residencia_estrato"]."', anios = '".$_REQUEST["anios"]."', meses = '".$_REQUEST["meses"]."', tel_residencia = '".$_REQUEST["tel_residencia"]."', lugar_correspondencia = '".$_REQUEST["lugar_correspondencia"]."',  eps = '".($_REQUEST["eps"])."', personas_acargo = '".$_REQUEST["personas_acargo"]."', personas_acargo_adultos = '".$_REQUEST["personas_acargo_adultos"]."', personas_acargo_menores = '".$_REQUEST["personas_acargo_menores"]."', profesion = '".($_REQUEST["profesion"])."', nivel_estudios = '".$_REQUEST["nivel_estudios"]."', peso = '".$_REQUEST["peso"]."', estatura = '".$_REQUEST["estatura"]."', sexo = '".$_REQUEST["sexo"]."'";

		if(date_create("2024-01-17") > date_create($_REQUEST["fecha_radicado"])){//Validar Campos bloqueados de prospeccion
		$sql_update.= ", nombre1 = '".ltrim($_REQUEST["nombre1"])."', nombre2 = '".(ltrim($_REQUEST["nombre2"]))."', apellido1 = '".ltrim($_REQUEST["apellido1"])."', apellido2 = '".ltrim($_REQUEST["apellido2"])."', fecha_nacimiento = '".$_REQUEST["fecha_nacimiento"]."', direccion = '".($_REQUEST["direccion"])."', celular = '".$_REQUEST["celular"]."', email = '".($_REQUEST["email"])."'";
	}


	sqlsrv_query($link, "UPDATE simulaciones set telefono = '".$_REQUEST["tel_residencia"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	
	$mensaje = "Informacion personal actualizada exitosamente";
}

if ($_REQUEST["section"] == "1") {
	if ($_REQUEST["conyugue_lugar_nacimiento"])
		$conyugue_lugar_nacimiento = "'".$_REQUEST["conyugue_lugar_nacimiento"]."'";
	else
		$conyugue_lugar_nacimiento = "NULL";
	
	if ($_REQUEST["conyugue_fecha_vinculacion"])
		$conyugue_fecha_vinculacion = "'".$_REQUEST["conyugue_fecha_vinculacion"]."'";
	else
		$conyugue_fecha_vinculacion = "NULL";
	
	if ($_REQUEST["conyugue_total_ingresos"])
		$conyugue_total_ingresos = "'".$_REQUEST["conyugue_total_ingresos"]."'";
	else
		$conyugue_total_ingresos = "NULL";
	
	$sql_update = "UPDATE solicitud set seccion_datos_conyugue=1,nombre_conyugue = '".($_REQUEST["nombre_conyugue"])."', conyugue_nombre_2 = '".($_REQUEST["conyugue_nombre_2"])."', conyugue_apellido_1 = '".($_REQUEST["conyugue_apellido_1"])."', conyugue_apellido_2 = '".($_REQUEST["conyugue_apellido_2"])."', conyugue_tipo_documento = '".$_REQUEST["conyugue_tipo_documento"]."', cedula_conyugue = '".$_REQUEST["cedula_conyugue"]."', conyugue_fecha_expedicion = '".$_REQUEST["conyugue_fecha_expedicion"]."', conyugue_lugar_expedicion = '".$_REQUEST["conyugue_lugar_expedicion"]."', conyugue_fecha_nacimiento = '".$_REQUEST["conyugue_fecha_nacimiento"]."', conyugue_sexo = '".$_REQUEST["conyugue_sexo"]."', conyugue_lugar_nacimiento = ".$conyugue_lugar_nacimiento.", conyugue_celular = '".$_REQUEST["conyugue_celular"]."', conyugue_nombre_empresa = '".($_REQUEST["conyugue_nombre_empresa"])."', conyugue_cargo = '".($_REQUEST["conyugue_cargo"])."', conyugue_fecha_vinculacion = ".$conyugue_fecha_vinculacion.", conyugue_total_ingresos = ".$conyugue_total_ingresos.", conyugue_ocupacion = '".$_REQUEST["conyugue_ocupacion"]."', conyugue_dependencia = '".$_REQUEST["conyugue_dependencia"]."', conyugue_nivel_estudios = '".$_REQUEST["conyugue_nivel_estudios"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Datos del conyugue actualizados exitosamente";
}

if ($_REQUEST["section"] == "2")
{
	if ($_REQUEST["fecha_vinculacion"])
		$fecha_vinculacion = "'".$_REQUEST["fecha_vinculacion"]."'";
	else
		$fecha_vinculacion = "NULL";
	
	$sql_update = "UPDATE solicitud set seccion_actividad_laboral=1,ocupacion = '".$_REQUEST["ocupacion"]."', declara_renta = '".$_REQUEST["declara_renta"]."', funcionario_publico = '".$_REQUEST["funcionario_publico"]."', recursos_publicos = '".$_REQUEST["recursos_publicos"]."', personaje_publico = '".$_REQUEST["personaje_publico"]."', actividad_economica_principal = '".($_REQUEST["actividad_economica_principal"])."', nombre_empresa = '".($_REQUEST["nombre_empresa"])."', cargo = '".($_REQUEST["cargo"])."', fecha_vinculacion = ".$fecha_vinculacion.", direccion_trabajo = '".($_REQUEST["direccion_trabajo"])."', ciudad_trabajo = '".($_REQUEST["ciudad_trabajo"])."', nit_empresa = '".($_REQUEST["nit_empresa"])."', telefono_trabajo = '".$_REQUEST["telefono_trabajo"]."', extension = '".$_REQUEST["extension"]."', tipo_empresa = '".$_REQUEST["tipo_empresa"]."', actividad_economica_empresa = '".$_REQUEST["actividad_economica_empresa"]."', tipo_contrato = '".$_REQUEST["tipo_contrato"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Actividad laboral actualizada exitosamente";
}

if ($_REQUEST["section"] == "3")
{
	if ($_REQUEST["honorarios_comisiones"] == "")
		$_REQUEST["honorarios_comisiones"] = "0";
	
	if ($_REQUEST["otros_ingresos"] == "")
		$_REQUEST["otros_ingresos"] = "0";
	
	if ($_REQUEST["activos_fijos"] == "")
		$_REQUEST["activos_fijos"] = "0";
	
	if ($_REQUEST["total_activos"] == "")
		$_REQUEST["total_activos"] = "0";
	
	if ($_REQUEST["valor_arrendo"] == "")
		$_REQUEST["valor_arrendo"] = "0";
	
	if ($_REQUEST["pasivos_corrientes"] == "")
		$_REQUEST["pasivos_corrientes"] = "0";
	
	if ($_REQUEST["otros_pasivos"] == "")
		$_REQUEST["otros_pasivos"] = "0";
	
	$sql_update = "UPDATE solicitud set seccion_info_financiera=1,ingresos_laborales = '".$_REQUEST["ingresos_laborales"]."', honorarios_comisiones = '".$_REQUEST["honorarios_comisiones"]."', otros_ingresos = '".$_REQUEST["otros_ingresos"]."', detalle_ingresos = '".($_REQUEST["detalle_ingresos"])."', total_ingresos = '".$_REQUEST["total_ingresos"]."', activos_fijos = '".$_REQUEST["activos_fijos"]."', total_activos = '".$_REQUEST["total_activos"]."', gastos_familiares = '".$_REQUEST["gastos_familiares"]."', valor_arrendo = '".$_REQUEST["valor_arrendo"]."', pasivos_financieros = '".$_REQUEST["pasivos_financieros"]."', pasivos_corrientes = '".$_REQUEST["pasivos_corrientes"]."', otros_pasivos = '".$_REQUEST["otros_pasivos"]."', detalle_otros_pasivos = '".($_REQUEST["detalle_otros_pasivos"])."', total_pasivos = '".$_REQUEST["total_pasivos"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Informacion financiera actualizada exitosamente";
}

if ($_REQUEST["section"] == "4")
{
	$sql_update = "UPDATE solicitud set seccion_referencias=1,nombre_familiar = '".($_REQUEST["nombre_familiar"])."', parentesco_familiar = '".$_REQUEST["parentesco_familiar"]."', telefono_familiar = '".$_REQUEST["telefono_familiar"]."', direccion_familiar = '".($_REQUEST["direccion_familiar"])."', ciudad_familiar = '".$_REQUEST["ciudad_familiar"]."', celular_familiar = '".$_REQUEST["celular_familiar"]."', nombre_personal = '".($_REQUEST["nombre_personal"])."', parentesco_personal = '".$_REQUEST["parentesco_personal"]."', telefono_personal = '".$_REQUEST["telefono_personal"]."', direccion_personal = '".($_REQUEST["direccion_personal"])."', ciudad_personal = '".$_REQUEST["ciudad_personal"]."', celular_personal = '".$_REQUEST["celular_personal"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Referencias actualizadas exitosamente";
}

if ($_REQUEST["section"] == "5")
{
	if ($_REQUEST["tipo_transaccion_exportacion"])
		$tipo_transaccion .= $_REQUEST["tipo_transaccion_exportacion"];
	
	if ($_REQUEST["tipo_transaccion_importacion"])
	{
		if ($tipo_transaccion)
			$tipo_transaccion .= ",";
		
		$tipo_transaccion .= $_REQUEST["tipo_transaccion_importacion"];
	}
	
	if ($_REQUEST["tipo_transaccion_inversiones"])
	{
		if ($tipo_transaccion)
			$tipo_transaccion .= ",";
		
		$tipo_transaccion .= $_REQUEST["tipo_transaccion_inversiones"];
	}
	
	if ($_REQUEST["tipo_transaccion_prestamo"])
	{
		if ($tipo_transaccion)
			$tipo_transaccion .= ",";
		
		$tipo_transaccion .= $_REQUEST["tipo_transaccion_prestamo"];
	}
	
	if ($_REQUEST["tipo_transaccion_otra"])
	{
		if ($tipo_transaccion)
			$tipo_transaccion .= ",";
		
		$tipo_transaccion .= $_REQUEST["tipo_transaccion_otra"];
	}
	
	$tipo_transaccion .= "|".($_REQUEST["tipo_transaccion_otra_cual"]);
	
	$sql_update = "update solicitud set actividades_apnfd='".$_REQUEST["actividades_apnfd"]."',criptomoneda='".$_REQUEST["criptomoneda"]."',seccion_datos_internacionales=1,moneda_extranjera = '".$_REQUEST["moneda_extranjera"]."', cuentas_exterior = '".$_REQUEST["cuentas_exterior"]."', tipo_transaccion = '".$tipo_transaccion."'";
	
	for ($i = 1; $i <=3; $i++)
	{
		if ($i != 1)
			$j = $i;
		
		if ($_REQUEST["monto_operaciones".$j])
			$monto_operaciones = "'".$_REQUEST["monto_operaciones".$j]."'";
		else
			$monto_operaciones = "NULL";
			
		$sql_update .= ", banco".$j." = '".($_REQUEST["banco".$j])."', num_cuenta".$j." = '".($_REQUEST["num_cuenta".$j])."', tipo_producto_operaciones".$j." = '".($_REQUEST["tipo_producto_operaciones".$j])."', monto_operaciones".$j." = ".$monto_operaciones.", moneda_operaciones".$j." = '".($_REQUEST["moneda_operaciones".$j])."', ciudad_operaciones".$j." = '".($_REQUEST["ciudad_operaciones".$j])."', pais_operaciones".$j." = '".($_REQUEST["pais_operaciones".$j])."'";
	}
	
	$sql_update .= " where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Datos de operaciones internacionales actualizados exitosamente";
}

if ($_REQUEST["section"] == "6")
{
	$sql_update = "UPDATE solicitud set seccion_facta=1,ciudadania_extranjera = '".$_REQUEST["ciudadania_extranjera"]."', residencia_extranjera = '".$_REQUEST["residencia_extranjera"]."', impuestos_extranjera = '".$_REQUEST["impuestos_extranjera"]."', representacion_extranjera = '".$_REQUEST["representacion_extranjera"]."'";
	
	for ($i = 1; $i <=2; $i++)
	{
		$sql_update .= ", poder_pais".$i." = '".($_REQUEST["poder_pais".$i])."', poder_identificacion".$i." = '".($_REQUEST["poder_identificacion".$i])."', poder_objeto".$i." = '".($_REQUEST["poder_objeto".$i])."'";
	}
	
	$sql_update .= " where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Datos declaracion FACTA - CRS actualizados exitosamente";
}

if ($_REQUEST["section"] == "7")
{
	if ($_REQUEST["apoderado_fecha_inicio"])
		$apoderado_fecha_inicio = "'".$_REQUEST["apoderado_fecha_inicio"]."'";
	else
		$apoderado_fecha_inicio = "NULL";
	
	if ($_REQUEST["apoderado_fecha_final"])
		$apoderado_fecha_final = "'".$_REQUEST["apoderado_fecha_final"]."'";
	else
		$apoderado_fecha_final = "NULL";
	
	$sql_update = "UPDATE solicitud set seccion_apoderado=1,apoderado_nombre1 = '".($_REQUEST["apoderado_nombre1"])."', apoderado_nombre2 = '".($_REQUEST["apoderado_nombre2"])."', apoderado_apellido1 = '".($_REQUEST["apoderado_apellido1"])."', apoderado_apellido2 = '".($_REQUEST["apoderado_apellido2"])."', apoderado_tipo_documento = '".$_REQUEST["apoderado_tipo_documento"]."', apoderado_nro_documento = '".$_REQUEST["apoderado_nro_documento"]."', apoderado_celular = '".$_REQUEST["apoderado_celular"]."', apoderado_telefono = '".$_REQUEST["apoderado_telefono"]."', apoderado_email = '".($_REQUEST["apoderado_email"])."', apoderado_direccion = '".($_REQUEST["apoderado_direccion"])."', apoderado_recursos_publicos = '".$_REQUEST["apoderado_recursos_publicos"]."', apoderado_funcionario_publico = '".$_REQUEST["apoderado_funcionario_publico"]."', apoderado_personaje_publico = '".$_REQUEST["apoderado_personaje_publico"]."', apoderado_fecha_inicio = ".$apoderado_fecha_inicio.", apoderado_fecha_final = ".$apoderado_fecha_final." where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Informacion apoderado actualizada exitosamente";
}

if ($_REQUEST["section"] == "8")
{
	$sql_update = "update solicitud set cargo_publico='".$_REQUEST["cargo_publico"]."',seccion_varios=1,instruccion_desembolso = '".$_REQUEST["instruccion_desembolso"]."', fuentes_actividades_licitas = '".($_REQUEST["fuentes_actividades_licitas"])."', clave = '".($_REQUEST["clave"])."', condiciones_seguros = '".$_REQUEST["condiciones_seguros"]."', primas_seguros = '".$_REQUEST["primas_seguros"]."', cancelacion_valores = '".$_REQUEST["cancelacion_valores"]."', ampliacion_plazo = '".$_REQUEST["ampliacion_plazo"]."', descuentos_anticipados = '".$_REQUEST["descuentos_anticipados"]."', resultado_entrevista = '".$_REQUEST["resultado_entrevista"]."', fecha_entrevista = '".$_REQUEST["fecha_entrevista"]."', observaciones = '".($_REQUEST["observaciones"])."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
	
	$mensaje = "Informacion actualizada exitosamente";
}
if ($_REQUEST["section"] == "9")
{
	sqlsrv_query($link, "DELETE FROM solicitud_preguntas_seguro WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	
	$respuestasGuardadas = 0;

	for ($i=1; $i <= 8; $i++) { 

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', ".$i.", '".$_REQUEST["seguro_respuesta_".$i]."')")){
			$respuestasGuardadas++;
		}
	}

	if($_REQUEST["seguro_respuesta_9a"] != ''){
		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 9, '".$_REQUEST["seguro_respuesta_9a"]."')")){
			$respuestasGuardadas++;
		}

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 10, '".$_REQUEST["seguro_respuesta_9b"]."')")){
			$respuestasGuardadas++;
		}

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 11, '".$_REQUEST["seguro_respuesta_9c"]."')")){
			$respuestasGuardadas++;
		}

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 12, '".$_REQUEST["seguro_respuesta_9d"]."')")){
			$respuestasGuardadas++;
		}

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 13, '".$_REQUEST["seguro_respuesta_9e"]."')")){
			$respuestasGuardadas++;
		}

		if(sqlsrv_query($link, "INSERT INTO solicitud_preguntas_seguro (id_simulacion, id_pregunta, respuesta) VALUES ('".$_REQUEST["id_simulacion"]."', 14, '".$_REQUEST["seguro_respuesta_9f"]."')")){
			$respuestasGuardadas++;
		}
	}

	$beneficiariosGuardadas = 0;

	sqlsrv_query($link, "DELETE FROM solicitud_beneficiarios_seguro WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'");

	for ($i=1; $i <= 4; $i++) { 
		if($_REQUEST["nombre1_".$i] != ''){
			if(sqlsrv_query($link, "INSERT INTO solicitud_beneficiarios_seguro (id_simulacion, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, id_parentesco, porcentaje) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["nombre1_".$i]."', '".$_REQUEST["nombre2_".$i]."', '".$_REQUEST["apellido1_".$i]."', '".$_REQUEST["apellido2_".$i]."', '".$_REQUEST["parentesco_".$i]."', '".$_REQUEST["porcentaje_".$i]."')")){
				$beneficiariosGuardadas++;
			}
		}
	}

	if($respuestasGuardadas >= 8){
		$sql_update = "update solicitud set seccion_seguro=1 where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
		$mensaje = "Informacion actualizada exitosamente";
	}else{
		$mensaje = "Falta Informacion Por almacenar";
		$sql_update="";
	}
}
 
sqlsrv_query($link, $sql_update);

?>	
<script>
alert("<?php echo $mensaje ?>");

window.location = 'solicitud.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&section=<?php echo $_REQUEST["section"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
