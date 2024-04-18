<?php 
	include ('../functions.php'); 
	include ('../controles/FDC.php');

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "TESORERIA")
{
	exit;
}

$link = conectar_utf();

if ($_REQUEST["id_comercial"] && $_REQUEST["cedula"] && $_REQUEST["nombre"] && $_REQUEST["pagaduria"] && $_REQUEST["fecha_estudio"])
{
	$procesar_simulacion = 1;
	
	if ($_REQUEST["telemercadeo"] != "1")
	{
		$_REQUEST["telemercadeo"] = "0";
	}
	
	if ($_REQUEST["fecha_nacimiento"])
		$fecha_nacimiento = "'".$_REQUEST["fecha_nacimiento"]."'";
	else
		$fecha_nacimiento = "NULL";
	
	if ($_REQUEST["fecha_inicio_labor"])
		$fecha_inicio_labor = "'".$_REQUEST["fecha_inicio_labor"]."'";
	else
		$fecha_inicio_labor = "NULL";
	
	if ($_REQUEST["sin_aportes"] != "1")
	{
		$_REQUEST["sin_aportes"] = "0";
	}
	
	if ($_REQUEST["sin_seguro"] != "1")
	{
		$_REQUEST["sin_seguro"] = "0";
	}
	
	if ($_REQUEST["id_plan_seguro"])
		$id_plan_seguro = "'".$_REQUEST["id_plan_seguro"]."'";
	else
		$id_plan_seguro = "NULL";
	
	if ($_REQUEST["nro_compra_cartera_seguro"])
		$nro_compra_cartera_seguro = "'".$_REQUEST["nro_compra_cartera_seguro"]."'";
	else
		$nro_compra_cartera_seguro = "NULL";
	
	for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++)
	{
		if ($_REQUEST["id_entidad".$i])
			$id_entidad[$i] = "'".$_REQUEST["id_entidad".$i]."'";
		else
			$id_entidad[$i] = "NULL";
	}
	
	if ($_REQUEST["nro_libranza"])
		$nro_libranza = "'".$_REQUEST["nro_libranza"]."'";
	else
		$nro_libranza = "NULL";

	

	if ($_REQUEST["valor_comision_descontar"]){
		$valor_comision_descontar = str_replace(",", "", $_REQUEST["valor_comision_descontar"]);
	} else{
		$valor_comision_descontar = "NULL";		
	}
		
	
	if ($_REQUEST["fecha_llamada_clientef"])
	{
		if (substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) == "12" && $_REQUEST["fecha_llamada_clientej"] == "AM")
			$_REQUEST["fecha_llamada_clienteh"] = "00".substr($_REQUEST["fecha_llamada_clienteh"], 2, 3);
		
		if (substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) != "12" && $_REQUEST["fecha_llamada_clientej"] == "PM")
		{
			$hora = substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) + 12;
			
			$_REQUEST["fecha_llamada_clienteh"] = $hora.substr($_REQUEST["fecha_llamada_clienteh"], 2, 3);
		}
		
		$fecha_llamada_cliente = "'".$_REQUEST["fecha_llamada_clientef"]." ".$_REQUEST["fecha_llamada_clienteh"]."'";
	}
	else
		$fecha_llamada_cliente = "NULL";
	
	if ($_REQUEST["nro_cuenta"])
		$nro_cuenta = "'".$_REQUEST["nro_cuenta"]."'";
	else
		$nro_cuenta = "NULL";
	
	if ($_REQUEST["tipo_cuenta"])
		$tipo_cuenta = "'".$_REQUEST["tipo_cuenta"]."'";
	else
		$tipo_cuenta = "NULL";
	
	if ($_REQUEST["id_banco"])
		$id_banco = "'".$_REQUEST["id_banco"]."'";
	else
		$id_banco = "NULL";
	
	if ($_REQUEST["id_subestado"])
		$id_subestado = "'".$_REQUEST["id_subestado"]."'";
	else
		$id_subestado = "NULL";
	
	if ($_REQUEST["id_causal"])
		$id_causal = "'".$_REQUEST["id_causal"]."'";
	else
		$id_causal = "NULL";
	
	if ($_REQUEST["id_caracteristica"])
		$id_caracteristica = "'".$_REQUEST["id_caracteristica"]."'";
	else
		$id_caracteristica = "NULL";
	
	if ($_REQUEST["calificacion"])
		$calificacion = "'".$_REQUEST["calificacion"]."'";
	else
		$calificacion = "NULL";
	
	if ($_REQUEST["formulario_seguro"] != "1")
	{
		$_REQUEST["formulario_seguro"] = "0";
	}
	
	if ($_REQUEST["id_analista_gestion_comercial"])
		$id_analista_gestion_comercial = "'".$_REQUEST["id_analista_gestion_comercial"]."'";
	else
		$id_analista_gestion_comercial = "NULL";
	
	if ($_REQUEST["id_analista_riesgo_operativo"])
		$id_analista_riesgo_operativo = "'".$_REQUEST["id_analista_riesgo_operativo"]."'";
	else
		$id_analista_riesgo_operativo = "NULL";
	
	
		
	
	if ($_REQUEST["id_analista_riesgo_crediticio"])
		$id_analista_riesgo_crediticio = "'".$_REQUEST["id_analista_riesgo_crediticio"]."'";
	else
		$id_analista_riesgo_crediticio = "NULL";
	
	if ($_REQUEST["dia_confirmacion"])
		$dia_confirmacion = "'".$_REQUEST["dia_confirmacion"]."'";
	else
		$dia_confirmacion = "NULL";
	
	if ($_REQUEST["dia_vencimiento"])
		$dia_vencimiento = "'".$_REQUEST["dia_vencimiento"]."'";
	else
		$dia_vencimiento = "NULL";
	
	if ($_REQUEST["status"])
		$status = "'".$_REQUEST["status"]."'";
	else
		$status = "NULL";
	
	if ($_REQUEST["bloqueo_cuota"] != "1")
	{
		$_REQUEST["bloqueo_cuota"] = "0";
	}
	
	
	if (!$_REQUEST["id_simulacion"])
	{
		$existe_en_empleados = mysqli_query($link, "select cedula from empleados where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."'");
		
		if (mysqli_num_rows($existe_en_empleados))
		{
			$existe_en_empleados_creacion = mysqli_query($link, "select * from empleados_creacion where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."'");
			
			if (mysqli_num_rows($existe_en_empleados_creacion))
			{
				$fila1 = mysqli_fetch_array($existe_en_empleados_creacion);
				
				if ($fila1["fecha_modificacion"])
					$empleado_manual = 0;
				else
					$empleado_manual = 1;
			}
			else
			{
				$empleado_manual = 0;
			}
			
			$existe_recien_creada = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND id_comercial = '".$_REQUEST["id_comercial"]."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."') AND UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(fecha_creacion) <= 60");

			if (mysqli_num_rows($existe_recien_creada))
			{
				echo "<script>function myFunction() { alert('Simulacion guardada exitosamente'); window.location = '".$_REQUEST["back"].".php?descripcion_busqueda=".$_REQUEST["cedula"]."&buscar=1'; } setTimeout(myFunction, 1000)</script>";
				
				exit;
			}
				
			$omitir_validacion_30_dias = 1;

			// Para superar la coyontura de prospecciones a reproceso se baja el llmite de 30 a 10 dias
			$existe_simulacion = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND DATEDIFF(CURDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST')");
			
			if (mysqli_num_rows($existe_simulacion))
			{
				$existe_simulacion2 = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND DATEDIFF(CURDATE(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST') AND id_comercial = '".$_REQUEST["id_comercial"]."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."')");
				
				if (!mysqli_num_rows($existe_simulacion2))
					$omitir_validacion_30_dias = 0;
			}

			if (!mysqli_num_rows($existe_simulacion) || $omitir_validacion_30_dias)
			{
				mysqli_query($link, "START TRANSACTION");
				
				mysqli_query($link, "insert into simulaciones (id_comercial, id_oficina, telemercadeo, fecha_estudio, cedula, nombre, pagaduria, pa, ciudad, institucion, nivel_educativo, fecha_nacimiento, telefono, meses_antes_65, fecha_inicio_labor, medio_contacto, salario_basico, adicionales, bonificacion, total_ingresos, aportes, otros_aportes, total_aportes, total_egresos, salario_minimo, ingresos_menos_aportes, salario_libre, nivel_contratacion, embargo_actual, historial_embargos, embargo_alimentos, embargo_centrales, descuentos_por_fuera, cartera_mora, valor_cartera_mora, puntaje_datacredito, puntaje_cifin, valor_descuentos_por_fuera, calif_sector_financiero, calif_sector_real, calif_sector_cooperativo, id_unidad_negocio, tasa_interes, plazo, id_plan_seguro, valor_seguro, nro_compra_cartera_seguro, tipo_credito, suma_al_presupuesto, total_cuota, total_valor_pagar, total_se_compra, retanqueo1_libranza, retanqueo1_cuota, retanqueo1_valor, retanqueo2_libranza, retanqueo2_cuota, retanqueo2_valor, retanqueo3_libranza, retanqueo3_cuota, retanqueo3_valor, retanqueo_total_cuota, retanqueo_total, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso, desembolso_cliente, decision, decision_sistema, valor_visado, bloqueo_cuota, bloqueo_cuota_valor, fecha_llamada_cliente, nro_cuenta, tipo_cuenta, id_banco, id_subestado, id_causal, id_caracteristica, calificacion, dia_confirmacion, dia_vencimiento, status, valor_credito, resumen_ingreso, incor, comision, utilidad_neta, sobre_el_credito, estado, tipo_producto, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6, descuento_transferencia, porcentaje_seguro, valor_por_millon_seguro, porcentaje_extraprima, formulario_seguro, sin_aportes, sin_seguro, empleado_manual, iva, usuario_radicado, fecha_radicado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_comercial"]."', (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."'), '".$_REQUEST["telemercadeo"]."', '".$_REQUEST["fecha_estudio"]."', '".$_REQUEST["cedula"]."', '".utf8_encode($_REQUEST["nombre"])."', '".utf8_encode($_REQUEST["pagaduria"])."', '".utf8_encode($_REQUEST["pa"])."', '".utf8_encode($_REQUEST["ciudad"])."', '".utf8_encode($_REQUEST["institucion"])."', '".utf8_encode($_REQUEST["nivel_educativo"])."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".$_REQUEST["meses_antes_65"]."', ".$fecha_inicio_labor.", '".$_REQUEST["medio_contacto"]."', '".str_replace(",", "", $_REQUEST["salario_basico"])."', '".str_replace(",", "", $_REQUEST["adicionales"])."', '".str_replace(",", "", $_REQUEST["bonificacion"])."', '".str_replace(",", "", $_REQUEST["total_ingresos"])."', '".str_replace(",", "", $_REQUEST["aportes"])."', '".str_replace(",", "", $_REQUEST["otros_aportes"])."', '".str_replace(",", "", $_REQUEST["total_aportes"])."', '".str_replace(",", "", $_REQUEST["total_egresos"])."', '".str_replace(",", "", $_REQUEST["salario_minimo"])."', '".str_replace(",", "", $_REQUEST["ingresos_menos_aportes"])."', '".str_replace(",", "", $_REQUEST["salario_libre"])."', '".utf8_encode($_REQUEST["nivel_contratacion"])."', '".$_REQUEST["embargo_actual"]."', '".$_REQUEST["historial_embargos"]."', '".$_REQUEST["embargo_alimentos"]."', '".$_REQUEST["embargo_centrales"]."', '".$_REQUEST["descuentos_por_fuera"]."', '".$_REQUEST["cartera_mora"]."', '".str_replace(",", "", $_REQUEST["valor_cartera_mora"])."', '".$_REQUEST["puntaje_datacredito"]."', '".$_REQUEST["puntaje_cifin"]."', '".str_replace(",", "", $_REQUEST["valor_descuentos_por_fuera"])."', '".$_REQUEST["calif_sector_financiero"]."', '".$_REQUEST["calif_sector_real"]."', '".$_REQUEST["calif_sector_cooperativo"]."', '".$_REQUEST["id_unidad_negocio"]."', '".$_REQUEST["tasa_interes"]."', '".$_REQUEST["plazo"]."', ".$id_plan_seguro.", '".str_replace(",", "", $_REQUEST["valor_seguro"])."', ".$nro_compra_cartera_seguro.", '".utf8_encode($_REQUEST["tipo_credito"])."', '".str_replace(",", "", $_REQUEST["suma_al_presupuesto"])."', '".str_replace(",", "", $_REQUEST["total_cuota"])."', '".str_replace(",", "", $_REQUEST["total_valor_pagar"])."', '".$_REQUEST["total_se_compra"]."', '".$_REQUEST["retanqueo1_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo1_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo1_valor"])."', '".$_REQUEST["retanqueo2_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo2_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo2_valor"])."', '".$_REQUEST["retanqueo3_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo3_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo3_valor"])."', '".str_replace(",", "", $_REQUEST["retanqueo_total_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo_total"])."', '".$_REQUEST["opcion_credito"]."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cli"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cli"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_ccc"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_ccc"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cmp"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cmp"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cso"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cso"])."', '".str_replace(",", "", $_REQUEST["desembolso_cliente"])."', '".$_REQUEST["decision"]."', '".$_REQUEST["decision_sistema"]."', '".str_replace(",", "", $_REQUEST["valor_visado"])."', '".$_REQUEST["bloqueo_cuota"]."', '".str_replace(",", "", $_REQUEST["bloqueo_cuota_valor"])."', ".$fecha_llamada_cliente.", ".$nro_cuenta.", ".$tipo_cuenta.", ".$id_banco.", ".$id_subestado.", ".$id_causal.", ".$id_caracteristica.", ".$calificacion.", ".$dia_confirmacion.", ".$dia_vencimiento.", ".$status.", '".str_replace(",", "", $_REQUEST["valor_credito"])."', '".str_replace(",", "", $_REQUEST["resumen_ingreso"])."', '".str_replace(",", "", $_REQUEST["incor"])."', '".str_replace(",", "", $_REQUEST["comision"])."', '".str_replace(",", "", $_REQUEST["utilidad_neta"])."', '".$_REQUEST["sobre_el_credito"]."', 'ING', '".$_REQUEST["tipo_producto"]."', '".$_REQUEST["descuento1"]."', '".$_REQUEST["descuento2"]."', '".$_REQUEST["descuento3"]."', '".$_REQUEST["descuento4"]."', '".$_REQUEST["descuento5"]."', '".$_REQUEST["descuento6"]."', '".$_REQUEST["descuento_transferencia"]."', '".$_REQUEST["porcentaje_seguro"]."', '".$_REQUEST["valor_por_millon_seguro"]."', '".$_REQUEST["porcentaje_extraprimah"]."', '".$_REQUEST["formulario_seguro"]."', '".$_REQUEST["sin_aportes"]."', '".$_REQUEST["sin_seguro"]."', '".$empleado_manual."', '".$_REQUEST["iva"]."', '".$_SESSION["S_LOGIN"]."', NOW(), 'system', NOW())");

			
				
				//$rs = mysqli_query("select MAX(id_simulacion) as m from simulaciones");
				mysqli_query($link, "SET @id_simulacion = LAST_INSERT_ID()");
				$consultarFDC=mysqli_query($link, "SELECT * FROM simulaciones_fdc where id_simulacion=@id_simulacion");
				if (mysqli_num_rows($consultarFDC)==0)
				{
					$consultarUsuarioSecundario="SELECT * FROM coordinacion_usuarios WHERE id_usuario_secundario='".$_REQUEST["id_comercial"]."'";
					$queryUsuarioSecundario=mysqli_query($link, $consultarUsuarioSecundario);
					if (mysqli_num_rows($queryUsuarioSecundario)==0)
					{
						mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES (@id_simulacion,1972,CURRENT_TIMESTAMP(),'s',1,1)");	

						echo "<br> Sql: "."insert into simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES (@id_simulacion,1972,CURRENT_TIMESTAMP(),'s',1,1)". " ---- Error: ".mysqli_error($link);
					}
					
				}
				
		
				mysqli_query($link, "COMMIT");

				$consultaID="SELECT @id_simulacion as id_simulacion";
				$queryMultiSet2=mysqli_query($link, $consultaID);
				$resMultiSet=mysqli_fetch_array($queryMultiSet2);
	
				$id_simul = $resMultiSet["id_simulacion"];
	
				
				for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++)
				{
					if ($id_entidad[$i] != "NULL" OR $_REQUEST["entidad".$i] OR $_REQUEST["cuota".$i] != "0" OR $_REQUEST["valor_pagar".$i] != "0" OR $_REQUEST["fecha_vencimiento".$i])
					{
						mysqli_query($link, "insert into simulaciones_comprascartera (id_simulacion, consecutivo, id_entidad, entidad, cuota, valor_pagar, se_compra, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$i."', ".$id_entidad[$i].", '".utf8_encode($_REQUEST["entidad".$i])."', '".str_replace(",", "", $_REQUEST["cuota".$i])."', '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', '".$_REQUEST["se_compra".$i]."', '".$_SESSION["S_LOGIN"]."', NOW())");
					}
				}
				
				if ($_REQUEST["observaciones"])
					mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".($_REQUEST["observaciones"])."', '".$_SESSION["S_LOGIN"]."', NOW())");
				
				if (mysqli_num_rows($existe_simulacion))
					mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".utf8_encode("El credito actual ha sido estudiado con menos de 30 dias lo cual no cumple con las politicas de fabrica. Por favor evaluar si es un credito dividido por superar los 80 millones o un credito menor a 30 dias")."', 'system', NOW())");
				
				mysqli_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, ciudad, email, clave) values ('".$id_simul."', '".$_REQUEST["cedula"]."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".utf8_encode($_REQUEST["celular"])."', '".utf8_encode($_REQUEST["direccion"])."', '".utf8_encode($_REQUEST["ciudad_residencia"])."', '".utf8_encode($_REQUEST["mail"])."', '".utf8_encode($_REQUEST["clave"])."')");
				
				$descuentos_adicionales = mysqli_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagaduria"]."' and estado = '1' order by id_descuento");
				
				while ($fila1 = mysqli_fetch_array($descuentos_adicionales))
				{
					mysqli_query($link, "insert into simulaciones_descuentos (id_simulacion, id_descuento, porcentaje) values ('".$id_simul."', '".$fila1["id_descuento"]."', '".$_REQUEST["descuentoadicional".$fila1["id_descuento"]]."')");
				}
				
				$rs1 = mysqli_query($link, "select id_oficina from simulaciones where id_simulacion = '".$id_simul."'");
				
				$fila1 = mysqli_fetch_array($rs1);
				
				$_REQUEST["id_oficina"] = $fila1["id_oficina"];
				
				if ($_REQUEST["id_cazador"])
				{
					mysqli_query($link, "update cazador set sub_estado = 'En proceso' where id_cazador = '".$_REQUEST["id_cazador"]."'");
				}
			}
			else
			{
				$mensaje = "Hay un estudio ingresado hace menos de 30 dias asociado a esa cedula";
				
				$procesar_simulacion = 0;
			}
		}
		else
		{
			$mensaje = "La cedula digitada no existe en la base de datos. Simulacion no guardada";
		}
	}
	else
	{
		$queryAnalistaActual=mysqli_query($link, "select * from simulaciones where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		$resAnalistaActual=mysqli_fetch_array($queryAnalistaActual);
		$id_analista_riesgo_operativoh=$resAnalistaActual["id_analista_riesgo_operativo"];
		//$consultaValMinAsesoriaFinanciera = mysqli_query("select * from definicion_tipos where id_tipo = '6' and id=1");
			//$resValMinAsesoriaFinanciera=mysqli_fetch_array($consultaValMinAsesoriaFinanciera);

			$valAnalistasKreditPlus=0;

			//$queryAnalistasKreditPlus=mysqli_query("SELECT id_usuario FROM usuarios a WHERE id_usuario NOT IN (SELECT descripcion FROM definicion_tipos WHERE id_tipo=4) AND subtipo='ANALISTA_CREDITO' AND estado=1 AND id_usuario='".$_SESSION["S_IDUSUARIO"]."'");
			$queryAnalistasKreditPlus=mysqli_query($link, "SELECT * FROM usuarios WHERE subtipo='COORD_CREDITO' AND id_usuario='".$_SESSION["S_IDUSUARIO"]."'");
			//if ((mysqli_num_rows($queryAnalistasKreditPlus)>0 || in_array($_SESSION["S_IDUSUARIO"],$usuarios_permiso_gastosadmin)) && $_REQUEST["descuento2"]<$resValMinAsesoriaFinanciera["descripcion"])
			//{
				IF (mysqli_num_rows($queryAnalistasKreditPlus)>0)
				{
					$valAnalistasKreditPlus=1;
				}
				
				//$mensaje="No tiene autorizacion para cambiar Asesoria financiera por debajo de: ".$resValMinAsesoriaFinanciera["descripcion"];
			//}

		$consultarFormularioDigitalDiligenciado=mysqli_query($link, "SELECT * FROM solicitud WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		$resFormularioDigitalDiligenciado=mysqli_fetch_Array($consultarFormularioDigitalDiligenciado);
			
	
		if (($_REQUEST["id_subestado"]=="79") && ($resFormularioDigitalDiligenciado["seccion_info_personal"] == "0" || $resFormularioDigitalDiligenciado["seccion_actividad_laboral"] == "0" || $resFormularioDigitalDiligenciado["seccion_info_financiera"] == "0" || $resFormularioDigitalDiligenciado["seccion_referencias"] == "0" || $resFormularioDigitalDiligenciado["seccion_datos_internacionales"] == "0" || $resFormularioDigitalDiligenciado["seccion_facta"] == "0" || $resFormularioDigitalDiligenciado["seccion_varios"] == "0"))
		{
			$mensaje="Debe diligenciar complementamente formulario de Solicitud para continuar el proceso";
			$cambio_estado=0;
		}else{
			
			$cambioEstado="UPDATE simulaciones SET id_subestado = ".$id_subestado." WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'";
			mysqli_query($link, $cambioEstado);
			$cambio_estado=1;
			//CAMBIAR A 1 PARA FUNCIONAR FOMRULARIO DIGITAL
		}

		if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "OPERACIONES") && ($_REQUEST["estado"] == "ING") && !($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" && !$_REQUEST["id_subestado"]))
			$estado = "EST";
		else
			$estado = $_REQUEST["estado"];
		//mysqli_query("update simulaciones set id_comercial = '".$_REQUEST["id_comercial"]."', telemercadeo = '".$_REQUEST["telemercadeo"]."', fecha_estudio = '".$_REQUEST["fecha_estudio"]."', cedula = '".$_REQUEST["cedula"]."', nombre = '".utf8_encode($_REQUEST["nombre"])."', pagaduria = '".utf8_encode($_REQUEST["pagaduria"])."', pa = '".utf8_encode($_REQUEST["pa"])."', ciudad = '".utf8_encode($_REQUEST["ciudad"])."', institucion = '".utf8_encode($_REQUEST["institucion"])."', nivel_educativo = '".utf8_encode($_REQUEST["nivel_educativo"])."', fecha_nacimiento = ".$fecha_nacimiento.",
		//if ($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES") {

				mysqli_query($link, "UPDATE simulaciones set valor_comision_descontar = '".$valor_comision_descontar."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");				

				mysqli_query($link, "UPDATE simulaciones set fecha_estudio = '".$_REQUEST["fecha_estudio"]."', pagaduria = '".$_REQUEST["pagaduria"]."', pa = '".$_REQUEST["pa"]."', ciudad = '".$_REQUEST["ciudad"]."', institucion = '".$_REQUEST["institucion"]."', nivel_educativo = '".$_REQUEST["nivel_educativo"]."', fecha_nacimiento = ".$fecha_nacimiento.", meses_antes_65 = '".$_REQUEST["meses_antes_65"]."', fecha_inicio_labor = ".$fecha_inicio_labor.", medio_contacto = '".$_REQUEST["medio_contacto"]."', telefono = '".$_REQUEST["telefono"]."', salario_basico = '".str_replace(",", "", $_REQUEST["salario_basico"])."', adicionales = '".str_replace(",", "", $_REQUEST["adicionales"])."', bonificacion = '".str_replace(",", "", $_REQUEST["bonificacion"])."', total_ingresos = '".str_replace(",", "", $_REQUEST["total_ingresos"])."', aportes = '".str_replace(",", "", $_REQUEST["aportes"])."', otros_aportes = '".str_replace(",", "", $_REQUEST["otros_aportes"])."', total_aportes = '".str_replace(",", "", $_REQUEST["total_aportes"])."', total_egresos = '".str_replace(",", "", $_REQUEST["total_egresos"])."', salario_minimo = '".str_replace(",", "", $_REQUEST["salario_minimo"])."', ingresos_menos_aportes = '".str_replace(",", "", $_REQUEST["ingresos_menos_aportes"])."', salario_libre = '".str_replace(",", "", $_REQUEST["salario_libre"])."', nivel_contratacion = '".$_REQUEST["nivel_contratacion"]."', embargo_actual = '".$_REQUEST["embargo_actual"]."', historial_embargos = '".$_REQUEST["historial_embargos"]."', embargo_alimentos = '".$_REQUEST["embargo_alimentos"]."', embargo_centrales = '".$_REQUEST["embargo_centrales"]."', descuentos_por_fuera = '".$_REQUEST["descuentos_por_fuera"]."', cartera_mora = '".$_REQUEST["cartera_mora"]."', valor_cartera_mora = '".str_replace(",", "", $_REQUEST["valor_cartera_mora"])."', puntaje_datacredito = '".$_REQUEST["puntaje_datacredito"]."', puntaje_cifin = '".$_REQUEST["puntaje_cifin"]."', valor_descuentos_por_fuera = '".str_replace(",", "", $_REQUEST["valor_descuentos_por_fuera"])."', calif_sector_financiero = '".$_REQUEST["calif_sector_financiero"]."', calif_sector_real = '".$_REQUEST["calif_sector_real"]."', calif_sector_cooperativo = '".$_REQUEST["calif_sector_cooperativo"]."', id_unidad_negocio = '".$_REQUEST["id_unidad_negocio"]."', tasa_interes = '".$_REQUEST["tasa_interes"]."', plazo = '".$_REQUEST["plazo"]."', id_plan_seguro = ".$id_plan_seguro.", valor_seguro = '".str_replace(",", "", $_REQUEST["valor_seguro"])."', nro_compra_cartera_seguro = ".$nro_compra_cartera_seguro.", tipo_credito = '".$_REQUEST["tipo_credito"]."', suma_al_presupuesto = '".str_replace(",", "", $_REQUEST["suma_al_presupuesto"])."', total_cuota = '".str_replace(",", "", $_REQUEST["total_cuota"])."', total_valor_pagar = '".str_replace(",", "", $_REQUEST["total_valor_pagar"])."', total_se_compra = '".$_REQUEST["total_se_compra"]."', retanqueo1_libranza = '".$_REQUEST["retanqueo1_libranza"]."', retanqueo1_cuota = '".str_replace(",", "", $_REQUEST["retanqueo1_cuota"])."', retanqueo1_valor = '".str_replace(",", "", $_REQUEST["retanqueo1_valor"])."', retanqueo2_libranza = '".$_REQUEST["retanqueo2_libranza"]."', retanqueo2_cuota = '".str_replace(",", "", $_REQUEST["retanqueo2_cuota"])."', retanqueo2_valor = '".str_replace(",", "", $_REQUEST["retanqueo2_valor"])."', retanqueo3_libranza = '".$_REQUEST["retanqueo3_libranza"]."', retanqueo3_cuota = '".str_replace(",", "", $_REQUEST["retanqueo3_cuota"])."', retanqueo3_valor = '".str_replace(",", "", $_REQUEST["retanqueo3_valor"])."', retanqueo_total_cuota = '".str_replace(",", "", $_REQUEST["retanqueo_total_cuota"])."', retanqueo_total = '".str_replace(",", "", $_REQUEST["retanqueo_total"])."', opcion_credito = '".$_REQUEST["opcion_credito"]."', opcion_cuota_cli = '".str_replace(",", "", $_REQUEST["opcion_cuota_cli"])."', opcion_desembolso_cli = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cli"])."', opcion_cuota_ccc = '".str_replace(",", "", $_REQUEST["opcion_cuota_ccc"])."', opcion_desembolso_ccc = '".str_replace(",", "", $_REQUEST["opcion_desembolso_ccc"])."', opcion_cuota_cmp = '".str_replace(",", "", $_REQUEST["opcion_cuota_cmp"])."', opcion_desembolso_cmp = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cmp"])."', opcion_cuota_cso = '".str_replace(",", "", $_REQUEST["opcion_cuota_cso"])."', opcion_desembolso_cso = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cso"])."', desembolso_cliente = '".str_replace(",", "", $_REQUEST["desembolso_cliente"])."', decision = '".$_REQUEST["decision"]."', decision_sistema = '".$_REQUEST["decision_sistema"]."', valor_visado = '".str_replace(",", "", $_REQUEST["valor_visado"])."', bloqueo_cuota = '".$_REQUEST["bloqueo_cuota"]."', bloqueo_cuota_valor = '".str_replace(",", "", $_REQUEST["bloqueo_cuota_valor"])."', fecha_llamada_cliente = ".$fecha_llamada_cliente.", nro_cuenta = ".$nro_cuenta.", tipo_cuenta = ".$tipo_cuenta.", id_banco = ".$id_banco.", id_subestado = ".$id_subestado.", id_causal = ".$id_causal.", id_caracteristica = ".$id_caracteristica.", calificacion = ".$calificacion.", dia_confirmacion = ".$dia_confirmacion.", dia_vencimiento = ".$dia_vencimiento.", status = ".$status.", valor_credito = '".str_replace(",", "", $_REQUEST["valor_credito"])."', resumen_ingreso = '".str_replace(",", "", $_REQUEST["resumen_ingreso"])."', incor = '".str_replace(",", "", $_REQUEST["incor"])."', comision = '".str_replace(",", "", $_REQUEST["comision"])."', utilidad_neta = '".str_replace(",", "", $_REQUEST["utilidad_neta"])."', sobre_el_credito = '".$_REQUEST["sobre_el_credito"]."', estado = '".$estado."', tipo_producto = '".$_REQUEST["tipo_producto"]."', descuento1 = '".$_REQUEST["descuento1"]."', descuento3 = '".$_REQUEST["descuento3"]."', descuento4 = '".$_REQUEST["descuento4"]."', descuento5 = '".$_REQUEST["descuento5"]."', descuento6 = '".$_REQUEST["descuento6"]."', descuento_transferencia = '".$_REQUEST["descuento_transferencia"]."', porcentaje_seguro = '".$_REQUEST["porcentaje_seguro"]."', valor_por_millon_seguro = '".$_REQUEST["valor_por_millon_seguro"]."', porcentaje_extraprima = '".$_REQUEST["porcentaje_extraprimah"]."', formulario_seguro = '".$_REQUEST["formulario_seguro"]."', sin_aportes = '".$_REQUEST["sin_aportes"]."', sin_seguro = '".$_REQUEST["sin_seguro"]."', iva = '".$_REQUEST["iva"]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = NOW() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
				
				if ($valAnalistasKreditPlus==0){
					$actualizarAserFinanciera="UPDATE simulaciones SET descuento2 = '".$_REQUEST["descuento2"]."' WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'";
					$queryActualizarAserFinanciera=mysqli_query($link, $actualizarAserFinanciera);
				}
				
			//}
		//}else{
			//mysqli_query("update simulaciones set fecha_estudio = '".$_REQUEST["fecha_estudio"]."', pagaduria = '".utf8_encode($_REQUEST["pagaduria"])."', pa = '".utf8_encode($_REQUEST["pa"])."', ciudad = '".utf8_encode($_REQUEST["ciudad"])."', institucion = '".utf8_encode($_REQUEST["institucion"])."', nivel_educativo = '".utf8_encode($_REQUEST["nivel_educativo"])."', fecha_nacimiento = ".$fecha_nacimiento.", meses_antes_65 = '".$_REQUEST["meses_antes_65"]."', fecha_inicio_labor = ".$fecha_inicio_labor.", medio_contacto = '".$_REQUEST["medio_contacto"]."', telefono = '".utf8_encode($_REQUEST["telefono"])."', salario_basico = '".str_replace(",", "", $_REQUEST["salario_basico"])."', adicionales = '".str_replace(",", "", $_REQUEST["adicionales"])."', bonificacion = '".str_replace(",", "", $_REQUEST["bonificacion"])."', total_ingresos = '".str_replace(",", "", $_REQUEST["total_ingresos"])."', aportes = '".str_replace(",", "", $_REQUEST["aportes"])."', otros_aportes = '".str_replace(",", "", $_REQUEST["otros_aportes"])."', total_aportes = '".str_replace(",", "", $_REQUEST["total_aportes"])."', total_egresos = '".str_replace(",", "", $_REQUEST["total_egresos"])."', salario_minimo = '".str_replace(",", "", $_REQUEST["salario_minimo"])."', ingresos_menos_aportes = '".str_replace(",", "", $_REQUEST["ingresos_menos_aportes"])."', salario_libre = '".str_replace(",", "", $_REQUEST["salario_libre"])."', nivel_contratacion = '".utf8_encode($_REQUEST["nivel_contratacion"])."', embargo_actual = '".$_REQUEST["embargo_actual"]."', historial_embargos = '".$_REQUEST["historial_embargos"]."', embargo_alimentos = '".$_REQUEST["embargo_alimentos"]."', embargo_centrales = '".$_REQUEST["embargo_centrales"]."', descuentos_por_fuera = '".$_REQUEST["descuentos_por_fuera"]."', cartera_mora = '".$_REQUEST["cartera_mora"]."', valor_cartera_mora = '".str_replace(",", "", $_REQUEST["valor_cartera_mora"])."', puntaje_datacredito = '".$_REQUEST["puntaje_datacredito"]."', puntaje_cifin = '".$_REQUEST["puntaje_cifin"]."', valor_descuentos_por_fuera = '".str_replace(",", "", $_REQUEST["valor_descuentos_por_fuera"])."', calif_sector_financiero = '".$_REQUEST["calif_sector_financiero"]."', calif_sector_real = '".$_REQUEST["calif_sector_real"]."', calif_sector_cooperativo = '".$_REQUEST["calif_sector_cooperativo"]."', id_unidad_negocio = '".$_REQUEST["id_unidad_negocio"]."', tasa_interes = '".$_REQUEST["tasa_interes"]."', plazo = '".$_REQUEST["plazo"]."', id_plan_seguro = ".$id_plan_seguro.", valor_seguro = '".str_replace(",", "", $_REQUEST["valor_seguro"])."', nro_compra_cartera_seguro = ".$nro_compra_cartera_seguro.", tipo_credito = '".utf8_encode($_REQUEST["tipo_credito"])."', suma_al_presupuesto = '".str_replace(",", "", $_REQUEST["suma_al_presupuesto"])."', total_cuota = '".str_replace(",", "", $_REQUEST["total_cuota"])."', total_valor_pagar = '".str_replace(",", "", $_REQUEST["total_valor_pagar"])."', total_se_compra = '".$_REQUEST["total_se_compra"]."', retanqueo1_libranza = '".$_REQUEST["retanqueo1_libranza"]."', retanqueo1_cuota = '".str_replace(",", "", $_REQUEST["retanqueo1_cuota"])."', retanqueo1_valor = '".str_replace(",", "", $_REQUEST["retanqueo1_valor"])."', retanqueo2_libranza = '".$_REQUEST["retanqueo2_libranza"]."', retanqueo2_cuota = '".str_replace(",", "", $_REQUEST["retanqueo2_cuota"])."', retanqueo2_valor = '".str_replace(",", "", $_REQUEST["retanqueo2_valor"])."', retanqueo3_libranza = '".$_REQUEST["retanqueo3_libranza"]."', retanqueo3_cuota = '".str_replace(",", "", $_REQUEST["retanqueo3_cuota"])."', retanqueo3_valor = '".str_replace(",", "", $_REQUEST["retanqueo3_valor"])."', retanqueo_total_cuota = '".str_replace(",", "", $_REQUEST["retanqueo_total_cuota"])."', retanqueo_total = '".str_replace(",", "", $_REQUEST["retanqueo_total"])."', opcion_credito = '".$_REQUEST["opcion_credito"]."', opcion_cuota_cli = '".str_replace(",", "", $_REQUEST["opcion_cuota_cli"])."', opcion_desembolso_cli = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cli"])."', opcion_cuota_ccc = '".str_replace(",", "", $_REQUEST["opcion_cuota_ccc"])."', opcion_desembolso_ccc = '".str_replace(",", "", $_REQUEST["opcion_desembolso_ccc"])."', opcion_cuota_cmp = '".str_replace(",", "", $_REQUEST["opcion_cuota_cmp"])."', opcion_desembolso_cmp = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cmp"])."', opcion_cuota_cso = '".str_replace(",", "", $_REQUEST["opcion_cuota_cso"])."', opcion_desembolso_cso = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cso"])."', desembolso_cliente = '".str_replace(",", "", $_REQUEST["desembolso_cliente"])."', decision = '".$_REQUEST["decision"]."', decision_sistema = '".$_REQUEST["decision_sistema"]."', valor_visado = '".str_replace(",", "", $_REQUEST["valor_visado"])."', bloqueo_cuota = '".$_REQUEST["bloqueo_cuota"]."', bloqueo_cuota_valor = '".str_replace(",", "", $_REQUEST["bloqueo_cuota_valor"])."', fecha_llamada_cliente = ".$fecha_llamada_cliente.", nro_cuenta = ".$nro_cuenta.", tipo_cuenta = ".$tipo_cuenta.", id_banco = ".$id_banco.", id_subestado = ".$id_subestado.", id_causal = ".$id_causal.", id_caracteristica = ".$id_caracteristica.", calificacion = ".$calificacion.", dia_confirmacion = ".$dia_confirmacion.", dia_vencimiento = ".$dia_vencimiento.", status = ".$status.", valor_credito = '".str_replace(",", "", $_REQUEST["valor_credito"])."', resumen_ingreso = '".str_replace(",", "", $_REQUEST["resumen_ingreso"])."', incor = '".str_replace(",", "", $_REQUEST["incor"])."', comision = '".str_replace(",", "", $_REQUEST["comision"])."', utilidad_neta = '".str_replace(",", "", $_REQUEST["utilidad_neta"])."', sobre_el_credito = '".$_REQUEST["sobre_el_credito"]."', estado = '".$estado."', tipo_producto = '".$_REQUEST["tipo_producto"]."', descuento3 = '".$_REQUEST["descuento3"]."', descuento4 = '".$_REQUEST["descuento4"]."', descuento5 = '".$_REQUEST["descuento5"]."', descuento6 = '".$_REQUEST["descuento6"]."', descuento_transferencia = '".$_REQUEST["descuento_transferencia"]."', porcentaje_seguro = '".$_REQUEST["porcentaje_seguro"]."', valor_por_millon_seguro = '".$_REQUEST["valor_por_millon_seguro"]."', porcentaje_extraprima = '".$_REQUEST["porcentaje_extraprimah"]."', formulario_seguro = '".$_REQUEST["formulario_seguro"]."', sin_aportes = '".$_REQUEST["sin_aportes"]."', sin_seguro = '".$_REQUEST["sin_seguro"]."', iva = '".$_REQUEST["iva"]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = NOW() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
			
		//}


		for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++)
		{
			if ($id_entidad[$i] != "NULL" OR $_REQUEST["entidad".$i] OR $_REQUEST["cuota".$i] != "0" OR $_REQUEST["valor_pagar".$i] != "0" OR $_REQUEST["fecha_vencimiento".$i] OR $_REQUEST["nombre_grabado".$i])
			{
				$comprascartera_tmp = mysqli_query($link, "select * from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
				
				if (mysqli_num_rows($comprascartera_tmp))
				{
					mysqli_query($link, "update simulaciones_comprascartera set id_entidad = ".$id_entidad[$i].", entidad = '".utf8_encode($_REQUEST["entidad".$i])."', cuota = '".str_replace(",", "", $_REQUEST["cuota".$i])."', valor_pagar = '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', se_compra = '".$_REQUEST["se_compra".$i]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
				}
				else
				{
					mysqli_query($link, "insert into simulaciones_comprascartera (id_simulacion, consecutivo, id_entidad, entidad, cuota, valor_pagar, se_compra, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".$i."', ".$id_entidad[$i].", '".utf8_encode($_REQUEST["entidad".$i])."', '".str_replace(",", "", $_REQUEST["cuota".$i])."', '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', '".$_REQUEST["se_compra".$i]."', '".$_SESSION["S_LOGIN"]."', NOW())");
				}
			}
			else
			{
				mysqli_query($link, "delete from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
			}

			if($id_subestado == 14 || $id_subestado == 78){
				//Si Se encuentra En Compras de cartera.

				$queryCarteraSaldada = mysqli_query($link, "SELECT IF(a.valor_cartera = b.valor_giros, 'SI', 'NO') AS pagada, valor_cartera, valor_giros, cant_giros FROM 
				(SELECT IF(SUM(a.valor_pagar) IS NULL, 0, SUM(a.valor_pagar)) AS valor_cartera FROM simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0) a,
				(SELECT IF(SUM(s.valor_girar) IS NULL, 0, SUM(s.valor_girar)) AS valor_giros, COUNT(s.id_giro) AS cant_giros FROM giros s WHERE s.id_simulacion = ".$_REQUEST["id_simulacion"]." AND s.clasificacion = 'CCA') b");

				$carteraSaldada = mysqli_fetch_array($queryCarteraSaldada);

				if($carteraSaldada['pagada'] == 'SI'){//Esta saldada la cartera
					if(mysqli_query($link, "UPDATE simulaciones SET id_subestado = 78 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
						mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 78, 'system3', NOW())");
					}

					//Checkear compras pagadas
					$queryCompra = mysqli_query($link, "SELECT a.consecutivo FROM  simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0;");

					if(mysqli_num_rows($queryCompra)){
						while ($updPagar = mysqli_fetch_array($queryCompra)) {
							mysqli_query($link, "update tesoreria_cc SET pagada = 1 WHERE id_simulacion = ".$_REQUEST["id_simulacion"]." AND consecutivo = ".$updPagar["consecutivo"]);
						}
					}

					//Tasa Comisones
					$sqlDatosComi="SELECT id_unidad_negocio, sin_seguro, id_subestado, tasa_interes FROM simulaciones WHERE id_simulacion = ".$_REQUEST["id_simulacion"];
					$queryDatosComi=mysqli_query($link, $sqlDatosComi);	
					$respDatosComi = mysqli_fetch_array($queryDatosComi);

					$id_unidad_negocio_tasa_comision = $respDatosComi["id_unidad_negocio"];

					if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_fianti)) {
	                    $id_unidad_negocio_tasa_comision = 4; //Fianti
	                }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_atraccion)) {
	                    $id_unidad_negocio_tasa_comision = 6; //Atraccion
	                }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_salvamento)) {
	                    $id_unidad_negocio_tasa_comision = 2; //Salvamento
	                }else if (in_array($id_unidad_negocio_tasa_comision, $array_unidad_negocio_comision_kredit)) {
	                    $id_unidad_negocio_tasa_comision = 1; //Kredit
	                }	

					$sqlTasaComision="SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, if(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = ".$id_unidad_negocio_tasa_comision ." AND a.tasa = ".$respDatosComi["tasa_interes"]." AND ((DATE_FORMAT(NOW(), '%Y-%m-%d') >= a.fecha_inicio AND DATE_FORMAT(NOW(), '%Y-%m-%d') <= a.fecha_fin) OR a.vigente = 1)";

					$queryTasaComision=mysqli_query($link, $sqlTasaComision);	

					if (@mysqli_num_rows($queryTasaComision)>0){
						$respTasaComision = mysqli_fetch_array($queryTasaComision);
						$id_tasa_comision = $respTasaComision["id_tasa_comision"];
						$id_tipo_comision = $respTasaComision["id_tipo"];

						//consultarTasaComisionAnterior
						$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
						$querySimTasaCom=mysqli_query($link, $sqlSimTasaCom);
						$respSimTasaCom = mysqli_fetch_assoc($querySimTasaCom);
						$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

						if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){

							mysqli_query($link, "UPDATE simulaciones SET id_tasa_comision = $id_tasa_comision, id_tipo_tasa_comision = $id_tipo_comision WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");

							mysqli_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", $id_tasa_comision, ".$_SESSION['S_IDUSUARIO'].", NOW())");
						}
					}else{
						//consultarTasaComisionAnterior
						$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
						$querySimTasaCom=mysqli_query($link, $sqlSimTasaCom);
						$respSimTasaCom = mysqli_fetch_assoc($querySimTasaCom);
						$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

						if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){
							mysqli_query($link, "UPDATE simulaciones SET id_tasa_comision = 0 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");
							mysqli_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", 0, ".$_SESSION['S_IDUSUARIO'].", NOW())");
						}
					}
				}else if(intval($carteraSaldada['cant_giros']) > 0){

					$conSubestado6 = mysqli_query($link, "SELECT id_subestado FROM simulaciones WHERE id_subestado = 14 AND id_simulacion = ".$_REQUEST["id_simulacion"]);

					if(mysqli_num_rows($conSubestado6) == 0){
						if(mysqli_query($link, "UPDATE simulaciones SET id_subestado = 14 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
							mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 14, 'system4', NOW())");
						}
					}
				}
			}
		}
		
		if (!$_REQUEST["fecha_prospeccion"])
			mysqli_query($link, "update simulaciones set usuario_prospeccion = '".$_SESSION["S_LOGIN"]."', fecha_prospeccion = NOW() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		if ($_REQUEST["observaciones"])
			mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".($_REQUEST["observaciones"])."', '".$_SESSION["S_LOGIN"]."', NOW())");
		
		if ($_REQUEST["valor_credito_anterior"])
			mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".utf8_encode("EL CREDITO FUE RELIQUIDADO POR CAMBIO EN EL VALOR POR MILLON DEL SEGURO".chr(13).chr(13)."VALORES ANTES DE LA RELIQUIDACION:".chr(13)."VALOR CREDITO: $".$_REQUEST["valor_credito_anterior"].chr(13)."DESEMBOLSO CLIENTE: $".$_REQUEST["desembolso_cliente_anterior"].chr(13).chr(13)."VALORES DESPUES DE LA RELIQUIDACION:".chr(13)."VALOR CREDITO: $".$_REQUEST["valor_credito"].chr(13)."DESEMBOLSO CLIENTE: $".$_REQUEST["desembolso_cliente"])."', 'system', NOW())");
		
		//ACTUALIZACION DE GESTION OPERATIVA: JAIRO ZAPATA
		//echo $id_analista_riesgo_operativoh."---<br>";

		//$consultarCantidadRegistros=mysqli_Query("SELECT * FROM simulaciones_fdc where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		//if (mysqli_num_rows($consultarCantidadRegistros)==0){
		//	$ingresarNuevoAnalistaSimulacionFdc=mysqli_query("INSERT INTO simulaciones_fdc (id_simulacion,fecha_creacion,id_usuario_asignacion,id_usuario_creacion,vigente,estado) VALUES ('".$_REQUEST["id_simulacion"]."','".$fila["fecha_creacion"]."','197','197','n','1')");
		//}


		//if ($id_analista_riesgo_operativoh<>$id_analista_riesgo_operativo)
		//{
		//	$actualizarEstadosSimulacionFdc=mysqli_query("UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		//	$ingresarNuevoAnalistaSimulacionFdc=mysqli_query("INSERT INTO simulaciones_fdc (id_simulacion,fecha_creacion,id_usuario_asignacion,id_usuario_creacion,vigente,estado) VALUES ('".$_REQUEST["id_simulacion"]."',CURRENT_TIMESTAMP(),'".$_REQUEST["id_analista_riesgo_operativo"]."','197','s','2')");
		//	$actualizarSimulaciones=mysqli_query("UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		//	$actualizarSimulaciones=mysqli_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$_REQUEST["id_analista_riesgo_operativo"]."'");
			
		//}else{
		//	$actualizarEstadosSimulacionFdc=mysqli_query("UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		//	$ingresarNuevoAnalistaSimulacionFdc=mysqli_query("INSERT INTO simulaciones_fdc (id_simulacion,fecha_creacion,id_usuario_asignacion,id_usuario_creacion,vigente,estado) VALUES ('".$_REQUEST["id_simulacion"]."',CURRENT_TIMESTAMP(),'".$_REQUEST["id_analista_riesgo_operativo"]."','197','s','2')");
		//	$actualizarSimulaciones=mysqli_query("UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		//	$actualizarSimulaciones=mysqli_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$_REQUEST["id_analista_riesgo_operativo"]."'");
		//}


		$existe_en_solicitud = mysqli_query($link, "select id_simulacion from solicitud where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		if (mysqli_num_rows($existe_en_solicitud))
		{
			//mysqli_query("update solicitud set cedula = '".$_REQUEST["cedula"]."', fecha_nacimiento = ".$fecha_nacimiento.", tel_residencia = '".utf8_encode($_REQUEST["telefono"])."', celular = '".utf8_encode($_REQUEST["celular"])."', direccion = '".utf8_encode($_REQUEST["direccion"])."', ciudad = '".utf8_encode($_REQUEST["ciudad_residencia"])."', email = '".utf8_encode($_REQUEST["mail"])."', clave = '".utf8_encode($_REQUEST["clave"])."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");


			mysqli_query($link, "update solicitud set clave = '".utf8_encode($_REQUEST["clave"])."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		}
		else
		{
			mysqli_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, ciudad, email, clave) values ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["cedula"]."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".utf8_encode($_REQUEST["celular"])."', '".utf8_encode($_REQUEST["direccion"])."', '".utf8_encode($_REQUEST["ciudad_residencia"])."', '".utf8_encode($_REQUEST["mail"])."', '".utf8_encode($_REQUEST["clave"])."')");
		}
		
		$descuentos_adicionales = mysqli_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");
		
		while ($fila1 = mysqli_fetch_array($descuentos_adicionales))
		{
			mysqli_query($link, "update simulaciones_descuentos set porcentaje = '".$_REQUEST["descuentoadicional".$fila1["id_descuento"]]."' where id_simulaciondescuento = '".$fila1["id_simulaciondescuento"]."'");
		}
		
		$id_simul = $_REQUEST["id_simulacion"];
	}
	
	if (!$mensaje && $procesar_simulacion)
	{
		if ($_REQUEST["id_plan_seguroh"] != $_REQUEST["id_plan_seguro"])
		{
			mysqli_query($link, "insert into simulaciones_seguro (id_simulacion, id_plan_seguro, valor_seguro, id_perfil, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', ".$id_plan_seguro.", '".str_replace(",", "", $_REQUEST["valor_seguro"])."', '".$_SESSION["S_IDPERFIL"]."', '".$_SESSION["S_LOGIN"]."', NOW())");
		}

		if ($_REQUEST["sin_seguroh2"] != $_REQUEST["sin_seguro"])
		{
			mysqli_query($link, "insert into simulaciones_sinseguro (id_simulacion, sin_seguro, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["sin_seguro"]."', '".$_SESSION["S_LOGIN"]."', NOW())");
		}
		
		if ($_REQUEST["salario_minimoh"] != str_replace(",", "", $_REQUEST["salario_minimo"]))
		{
			mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', 'CAMBIO DE SALARIO MINIMO NUEVO $".$_REQUEST["salario_minimo"]." ANTIGUO $ ".number_format($_REQUEST["salario_minimoh"], 0, ".", ",")."' , '".$_SESSION["S_LOGIN"]."', NOW())");
		}

		if ($nro_libranza == "NULL")
		{
			$actualiza_nro_libranza = 1;
		}
		else
		{
			$existe_en_simulaciones = mysqli_query($link, "select id_simulacion from simulaciones where nro_libranza = ".strtoupper($nro_libranza)." AND id_simulacion != '".$id_simul."'");
			
			if (mysqli_num_rows($existe_en_simulaciones))
			{
				$mensaje = "El No. de Libranza ingresado ya esta registrado por lo tanto este campo NO fue actualizado. ";
			}
			else
			{
				$actualiza_nro_libranza = 1;
			}
		}
		
		//if ($actualiza_nro_libranza)
		//{

		//	mysqli_query("update simulaciones set nro_libranza = ".strtoupper($nro_libranza)." where id_simulacion = '".$id_simul."'");
		//}
		//echo "subestado: ".$_REQUEST["id_subestado"]."--subestadoh:".$_REQUEST["id_subestadoh"]."<br>";

		//ASIGNACION NUMERO DE LIBRANZA
		if (($_REQUEST["id_subestado"]=="56" || $_REQUEST["id_subestado"]=="70" || $_REQUEST["id_subestado"]=="79") && $nro_libranza == "NULL" && $cambio_estado==1)
		{
			mysqli_query($link, "START TRANSACTION");
			$consultarPrefijo="SELECT * from unidades_negocio where id_unidad='".$_REQUEST["id_unidad_negocio"]."'";
			$queryPrefijo=mysqli_query($link, $consultarPrefijo);
			$resPrefijo=mysqli_fetch_array($queryPrefijo);
			
			$consultarLibranza="SELECT max(libranza) as libranza FROM simulaciones WHERE libranza is not null";
			$queryLibranza=mysqli_query($link, $consultarLibranza);
			$resLibranza=mysqli_fetch_array($queryLibranza);

			if ($resLibranza["libranza"]==0)
			{
				$numero_libranza=1;
			}else{
				$numero_libranza=$resLibranza["libranza"]+1;
			}

		
			mysqli_query($link, "update simulaciones set usuario_libranza='".$_SESSION["S_IDUSUARIO"]."',fecha_libranza=current_timestamp(),libranza='".$numero_libranza."',nro_libranza = '".strtoupper($resPrefijo["prefijo_libranza"]." ".$numero_libranza)."' where id_simulacion = '".$id_simul."'");
			mysqli_query($link, "COMMIT");
			
		}
		
		$consultarOficinaFormatoDigital="SELECT * FROM oficinas WHERE id_oficina=(SELECT id_oficina FROM simulaciones WHERE id_simulacion='".$id_simul."') AND escala='1'";
		//echo $consultarOficinaFormatoDigital;
		
		$queryOficinaFormatoDigital=mysqli_query($link, $consultarOficinaFormatoDigital);
		//echo "cantidad: ".mysqli_num_rows($queryOficinaFormatoDigital);
		if (mysqli_num_rows($queryOficinaFormatoDigital)>0)
		{
			
			if (in_array($_REQUEST["id_subestado"], $subestados_formulario_digital) && $cambio_estado==1) 
			{
				
				$consultarFormatoDigital="SELECT * FROM formulario_digital WHERE id_simulacion='".$id_simul."'";
				//echo $consultarFormatoDigital;
				$queryFormatoDigital=mysqli_query($link, $consultarFormatoDigital);
				if (mysqli_num_rows($queryFormatoDigital)==0)
				{
					$token = openssl_random_pseudo_bytes(64);
						//Convertir el binario a data hexadecimal.
					$token = bin2hex($token);

					$crearTokenFormatoDigital="INSERT INTO formulario_digital (id_simulacion,estado_token,token,vigente) values (".$id_simul.",0,'".$token."','s')";
					if (mysqli_query($link, $crearTokenFormatoDigital))
					{
						
						$data=array(
							'token' => $token,
							'pagare' => "NO",
							'id_usuario'=>$_SESSION["S_IDUSUARIO"],
							"id_simulacion"=>$id_simul,
							"reenviar"=>"NO"
								
						);
						$opciones = array(
							'http'=>array(
								'method' => 'POST',
								'content' => json_encode($data)
									
							)
						);
						
						$contexto = stream_context_create($opciones);
				
						$json_Input = file_get_contents($urlPrincipal.'/servicios/enviar_correo_experian.php', false, $contexto);
						
				
						$parametros=json_decode($json_Input);
					}
				}
			}
		}
		
		echo "linea 640 antes de subestados_tesoreria_ccvecimientos";
		if (in_array($_REQUEST["id_subestado"], $subestados_tesoreria_ccvecimientos)) 
		{
			echo "linea 640 antes de subestados_tesoreria_ccvecimientos";
			echo $queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
							FROM simulaciones_comprascartera a
							LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion
							WHERE a.se_compra='SI' AND b.fecha_vencimiento=CURRENT_DATE() AND a.id_simulacion=".$id_simul." AND a.consecutivo=b.consecutivo";
			$conCC = mysqli_query($link, $queryBD_CC);
			echo "Error subestados_tesoreria_ccvecimientos : " . mysqli_error($link);
			if (mysqli_num_rows($conCC)>0)
			{
				$opciones = array(
					'http'=>array(
						'method' => 'POST',
						'content' => "id_simulacion=".$id_simul."&nombre=".$_REQUEST["nombre"]."&cedula=".$_REQUEST["cedula"]
								
					)
				);
					
				$contexto = stream_context_create($opciones);
			
				$json_Input = file_get_contents($urlPrincipal.'/servicios/enviar_correo_vencimientos.php', false, $contexto);
			
				$parametros=json_decode($json_Input);
			}
		}	

		echo "Antes de comparar sub estado<br>"	;
		
		if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && $_REQUEST["id_subestado"]){		
			echo "Desspues de comparar estado<br>";
			
			if(in_array($_REQUEST["id_subestado"], $subestados_estudio)){

				echo "Desspues de comparar array estado <br>";

				$consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' and estado<>100";
				$querySimulacionesFdc=mysqli_query($link, $consultarSimulacionesFdc);
				if (mysqli_num_rows($querySimulacionesFdc)>0){

					echo "Desspues de comparar simulaciones fdc estado <br>";


					$consultarUltimoAnalistaEstudio=mysqli_query($link, "SELECT case when id_usuario_asignacion is null then 0 when id_usuario_asignacion = 197 then 0 else id_usuario_asignacion end as id_usuario_asignacion FROM simulaciones_fdc WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."' and estado=2 order by id desc limit 1");
					$resUltimoAnalistaEstudio=mysqli_fetch_array($consultarUltimoAnalistaEstudio);
					if ($_REQUEST["id_analista_riesgo_operativo"]!=$resAnalistaActual["id_analista_riesgo_operativo"]){	
						$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");							
					}

					$id_simulacion_fdc = $_REQUEST["id_simulacion"];

					mysqli_query($link, "START TRANSACTION");
					$consultarJornadaLaboral=mysqli_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
  
					$resJornadaLaboral=mysqli_fetch_array($consultarJornadaLaboral);
					$actualizarEstadosFDC=mysqli_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
					$crearEstadoTerminado=mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion_fdc,0,197,current_timestamp(),'s',5,".$_REQUEST["id_subestado"].",2)");

					echo "<br> Sql: "."INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion_fdc,0,197,current_timestamp(),'s',5,".$_REQUEST["id_subestado"].",2)". " ---- Error: ".mysqli_error($link);

					if ($resJornadaLaboral["descripcion"]=="s"){

						echo "Desspues desicion estado <br>";
						
						$consultarEstadoUsuarioNuevo=mysqli_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalistaEstudio["id_usuario_asignacion"]."' and disponible <> ('n')");
						if (mysqli_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalistaEstudio["id_usuario_asignacion"]<>0){

							echo "Desspues consultarEstadoUsuarioNuevo lina 705 <br>";

							$resEstadoUsuarioNuevo=mysqli_fetch_array($consultarEstadoUsuarioNuevo);

							$consultarLimiteCreditosUsuario=mysqli_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
							FROM
							(SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos
							FROM usuarios a 
							LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
							WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') a,
							(SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos
							FROM usuarios a 
							LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND DATE_FORMAT(fecha_creacion,'%Y-%m-%d') = CURRENT_DATE()) b ON a.id_usuario=b.id_usuario_creacion
							 WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."') b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos");
							if(mysqli_num_rows($consultarLimiteCreditosUsuario)>0){
								echo "Desspues consultarLimiteCreditosUsuario lina 720 <br>";

								$actualizarEstadosFDC=mysqli_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");

								$id_simulacion_fdc = $_REQUEST["id_simulacion"];
								$crearEstadoTerminado2=mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion_fdc.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp(),'s',2,3)");

								echo "<br> Sql: "."INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES (".$id_simulacion_fdc.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp(),'s',2,3)". " ---- Error: ".mysqli_error($link);

								$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
								
								if ($resEstadoUsuarioNuevo["estado"]=="s" || $resEstadoUsuarioNuevo["estado"]=="g"){
									//$actualizarUsuario=mysqli_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."'");
								}

								$consultarEstadoUsuario=mysqli_query($link, "SELECT * FROM simulaciones_fdc WHERE id_usuario_asignacion='".$resAnalistaActual["id_analista_riesgo_operativo"]."' and vigente='s' and estado='2' and id_simulacion<>'".$_REQUEST["id_simulacion"]."'");
								if (mysqli_num_rows($consultarEstadoUsuario)>0){
									//$actualizarUsuario2=mysqli_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$resAnalistaActual["id_analista_riesgo_operativo"]."'");								
								}
							}else{
								$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");		
							}
						}else{

							echo "Desspues consultarEstadoUsuarioNuevo lina 744 <br>";

							$idUsuarioAsignar = usuarioParaAsignar($_REQUEST["id_simulacion"]);
							$id_simulacion_fdc = $_REQUEST["id_simulacion"];
							if ($idUsuarioAsignar<>0){

								echo "Desspues idUsuarioAsignar lina 750 <br>";
								$actualizarEstadosFDC=mysqli_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
								$crearEstadoTerminado2=mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion_fdc,$idUsuarioAsignar,197,current_timestamp(),'s',2,4)");

								echo "<br> Sql: "."INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion_fdc,$idUsuarioAsignar,197,current_timestamp(),'s',2,4)".mysqli_error($link);

								$actualizarUsuario=mysqli_query($link, "UPDATE usuarios SET disponible='g' WHERE id_usuario='".$idUsuarioAsignar."'");
								$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$idUsuarioAsignar."',id_analista_riesgo_crediticio='".$idUsuarioAsignar."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
							}else{
								echo "Desspues SIno idUsuarioAsignar lina 759 <br>";
							}
						}
					}
					else{
						echo "Desspues sino  desicion estado <br>";
						$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");		
					}
					mysqli_query($link, "COMMIT");
				}else{

					echo "Desspues de sino comparar simulaciones fdc estado <br>";


					$id_simulacion_fdc = $_REQUEST["id_simulacion"];
					$actualizarEstadosFDC=mysqli_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
					$crearEstadoTerminado=mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion_fdc, 0, 197, current_timestamp(), 's', 1 , 1)");
					echo "<br> Sql: "."INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion_fdc, 0, 197, current_timestamp(), 's', 1 , 1)".mysqli_error($link);

				}
			}
			else{	
				echo "Desspues SINO de comparar array estado linea 781<br>";
				$consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'";
				$querySimulacionesFdc=mysqli_query($link, $consultarSimulacionesFdc);
				
				if (mysqli_num_rows($querySimulacionesFdc)>0){
					echo "Desspues de querySimulacionesFdc linea 786<br>";
					mysqli_query($link, "START TRANSACTION");

					$consultarUltEstadoFDC="SELECT * FROM simulaciones_fdc WHERE id_simulacion=".$_REQUEST["id_simulacion"]." and vigente='s'";
					$consultarUltEstadoTerminado=$consultarUltEstadoFDC." and estado=4";
					$queryUltEstadoFDC=mysqli_query($link, $consultarUltEstadoTerminado);

					if (mysqli_num_rows($queryUltEstadoFDC)>0){
						echo "Desspues queryUltEstadoFDC linea 794<br>";
						//$actualizarEstadosFDC=mysqli_query("UPDATE simulaciones_fdc SET id_subestado='".$_REQUEST["id_subestado"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' and estado=4 and vigente='s'");						
					}
					else{
						echo "Desspues SINO queryUltEstadoFDC linea 797<br>";
						$consultarUltEstadoEstudio=$consultarUltEstadoFDC." and estado in(1,2,5) ";
						$queryUltEstadoFDCEstudio=mysqli_query($link, $consultarUltEstadoEstudio);
						if (mysqli_num_rows($queryUltEstadoFDCEstudio)>0) {
							echo "Desspues  queryUltEstadoFDCEstudio linea 802<br>";
							$resUltEstadoFDCEstudio=mysqli_fetch_array($queryUltEstadoFDCEstudio);
							$actualizarEstadosFDC=mysqli_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
							echo "<br> Sql: "."UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'".mysqli_error($link);

							$crearEstadoTerminado=mysqli_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES (".$_REQUEST["id_simulacion"].",0,".$resUltEstadoFDCEstudio["id_usuario_asignacion"].",current_timestamp(),'s',4,".$_REQUEST["id_subestado"].",5)");

							echo "<br> Sql: "."INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES (".$_REQUEST["id_simulacion"].",0,".$resUltEstadoFDCEstudio["id_usuario_asignacion"].",current_timestamp(),'s',4,".mysqli_error($link);


							$consultarAsignacionesUsuario=mysqli_query($link, "SELECT * FROM simulaciones_fdc WHERE estado=2 and vigente='s' and id_usuario_asignacion='".$_REQUEST["id_analista_riesgo_operativo"]."' and id_simulacion<>'".$_REQUEST["id_simulacion"]."'");
							if (mysqli_num_rows($consultarAsignacionesUsuario)>0){
						
								//$actualizarUsuario=mysqli_query("UPDATE usuarios SET disponible='g' WHERE id_usuario='".$_REQUEST["id_analista_riesgo_operativo"]."'");
							}
						}else{
							echo "Desspues SINO queryUltEstadoFDCEstudio linea 802<br>";
						}
					}
					mysqli_query($link, "COMMIT");
				}				
			}
		
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"]){
				mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["id_subestado"]."', '".$_SESSION["S_LOGIN"]."', NOW())");
			}			
			
			$subestado_cambiado = 1;
		}else{
			if ($id_analista_riesgo_operativoh<>$_REQUEST["id_analista_riesgo_operativo"]){
				$actualizarAnalista=mysqli_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
			}	

			echo "linea 835<br>"	;		
		}

		//if (($_REQUEST["id_subestado"] == $subestado_firmado || $_REQUEST["id_subestado"] == $subestado_valid_doc_proforense) && $_REQUEST["tipo_comercial"] == "PLANTA" && !$_REQUEST["id_analista_gestion_comercial"])
		//{
			//$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_GEST_COM' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') AND id_usuario > (select id_usuario from asignaciones_analistas where tipo = 'AGC' AND sector = '".$_REQUEST["sector"]."') LIMIT 1");
			
			//if (mysqli_num_rows($siguiente_analista))
			//{
			//	$fila1 = mysqli_fetch_array($siguiente_analista);
				
			//	$id_siguiente_analista = $fila1["id_usuario"];
			//}
			//else
			//{
			//	$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_GEST_COM' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') order by id_usuario LIMIT 1");
				
			//	if (mysqli_num_rows($siguiente_analista))
			//	{
			//		$fila1 = mysqli_fetch_array($siguiente_analista);
					
			//		$id_siguiente_analista = $fila1["id_usuario"];
			//	}
			//}
			
			//if ($id_siguiente_analista)
			//{
			//	mysqli_query("update simulaciones set id_analista_gestion_comercial = '".$id_siguiente_analista."' where id_simulacion = '".$id_simul."'");
				
			//	mysqli_query("update asignaciones_analistas set id_usuario = '".$id_siguiente_analista."' where tipo = 'AGC' AND sector = '".$_REQUEST["sector"]."'");
			//}
		//}
		
		//if (!$_REQUEST["id_analista_riesgo_operativo"] && !$_REQUEST["id_analista_riesgo_crediticio"])
		//{
			//$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') AND id_usuario > (select id_usuario from asignaciones_analistas where tipo = 'PFC' AND sector = '".$_REQUEST["sector"]."') LIMIT 1");
			
			//if (mysqli_num_rows($siguiente_analista))
			//{
			//	$fila1 = mysqli_fetch_array($siguiente_analista);
				
			//	$id_siguiente_analista = $fila1["id_usuario"];
			//}
			//else
			//{
			//	$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') order by id_usuario LIMIT 1");
				
			//	if (mysqli_num_rows($siguiente_analista))
			//	{
			//		$fila1 = mysqli_fetch_array($siguiente_analista);
					
			//		$id_siguiente_analista = $fila1["id_usuario"];
			//	}
			//}
			
			//if ($id_siguiente_analista)
			//{
			//	mysqli_query("update simulaciones set id_analista_riesgo_operativo = '".$id_siguiente_analista."', id_analista_riesgo_crediticio = '".$id_siguiente_analista."' where id_simulacion = '".$id_simul."'");
				
			//	mysqli_query("update asignaciones_analistas set id_usuario = '".$id_siguiente_analista."' where tipo = 'PFC' AND sector = '".$_REQUEST["sector"]."'");
			//}
		//}
		
		if ($_REQUEST["id_subestado"] == $subestado_radicado || $_REQUEST["id_subestado"] == $subestado_soportes_completos)
		{
			//if (!$_REQUEST["id_analista_riesgo_operativo"] && !$_REQUEST["id_analista_riesgo_crediticio"])
			//{
			//	$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') AND id_usuario > (select id_usuario from asignaciones_analistas where tipo = 'ACR' AND sector = '".$_REQUEST["sector"]."') LIMIT 1");
				
			//	if (mysqli_num_rows($siguiente_analista))
			//	{
			//		$fila1 = mysqli_fetch_array($siguiente_analista);
					
			//		$id_siguiente_analista = $fila1["id_usuario"];
			//	}
			//	else
			//	{
			//		$siguiente_analista = mysqli_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') order by id_usuario LIMIT 1");
					
			//		if (mysqli_num_rows($siguiente_analista))
			//		{
			//			$fila1 = mysqli_fetch_array($siguiente_analista);
						
			//			$id_siguiente_analista = $fila1["id_usuario"];
			//		}
			//	}
				
			//	if ($id_siguiente_analista)
			//	{
			//		mysqli_query("update simulaciones set id_analista_riesgo_operativo = '".$id_siguiente_analista."', id_analista_riesgo_crediticio = '".$id_siguiente_analista."' where id_simulacion = '".$id_simul."'");
					
			//		mysqli_query("update asignaciones_analistas set id_usuario = '".$id_siguiente_analista."' where tipo = 'ACR' AND sector = '".$_REQUEST["sector"]."'");
			//	}
			//}
			
			mysqli_query($link, "update solicitud set tasa_usura = '".$_REQUEST["tasa_usura"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		}
		
		if (str_replace(",", "", $_REQUEST["valor_visado"]) != "0" && $_REQUEST["id_subestado"] == $subestado_aprobado)
		{
			switch($_REQUEST["opcion_credito"])
			{
				case "CLI":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cli"]);
							break;
				case "CCC":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_ccc"]);
							break;
				case "CMP":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cmp"]);
							break;
				case "CSO":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cso"]);
							break;
			}
			
			if (str_replace(",", "", $_REQUEST["valor_visado"]) < $opcion_cuota)
				$nuevo_subestado = $subestado_procesado;
			else
				$nuevo_subestado = $subestado_visado;
			
			mysqli_query($link, "update simulaciones set id_subestado = '".$nuevo_subestado."' where id_simulacion = '".$id_simul."'");
			
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"])
			{
				mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$nuevo_subestado."', 'system', NOW())");
			}
			
			
			$subestado_cambiado = 1;
		}
		
		if (str_replace(",", "", $_REQUEST["valor_visado"]) != "0" && $_REQUEST["id_subestado"] == $subestado_desembolso_pdte_bloqueo)
		{
			mysqli_query($link, "update simulaciones set id_subestado = '".$subestado_desembolso_cliente."' where id_simulacion = '".$id_simul."'");
			
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"])
			{
				mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$subestado_desembolso_cliente."', 'system', NOW())");
			}
			$subestado_cambiado = 1;
		}
		
		if (!$subestado_cambiado && $_REQUEST["id_subestado"])
		{
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"])
			{
				mysqli_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["id_subestado"]."', '".$_SESSION["S_LOGIN"]."', NOW())");
			}
		}
		
		if ($_REQUEST["id_subestado"] == $subestado_aprobado)
		{
			mysqli_query($link, "delete from giros where id_simulacion = '".$id_simul."' AND fecha_giro IS NULL");
			
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"])
				mysqli_query($link,"update simulaciones set fecha_aprobado = CURDATE() where id_simulacion = '".$id_simul."'");
		}
		
		//Solo se actualiza la fecha de aprobado si es NULL, es decir, si pasa directamente a otros subestados aprobados
		if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && ($_REQUEST["id_subestado"] == $subestado_aprobado_pdte_visado || $_REQUEST["id_subestado"] == $subestado_aprobado_pdte_incorp || $_REQUEST["id_subestado"] == $subestado_visado))
			mysqli_query($link, "update simulaciones set fecha_aprobado = CURDATE() where id_simulacion = '".$id_simul."' AND fecha_aprobado IS NULL");
		
		//Solo se actualiza el estado de tesoreria si es NULL, es decir, si no se ha establecido antes
		if ($nuevo_subestado == $subestado_confirmado || ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && $_REQUEST["id_subestado"] == $subestado_confirmado))
			mysqli_query($link, "update simulaciones set estado_tesoreria = 'ABI', fecha_tesoreria = CURDATE() where id_simulacion = '".$id_simul."' AND estado_tesoreria IS NULL");

		$varexplode=explode(",",$subestado_compras_desembolso);
		
		//Solo se actualiza el estado de tesoreria si es NULL, es decir, si pasa directamente a Desembolso
		if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && ($_REQUEST["id_subestado"] == $subestado_tesoreria_con_pdtes || $_REQUEST["id_subestado"] == $varexplode[0] || $_REQUEST["id_subestado"] == $varexplode[1] || $_REQUEST["id_subestado"] == $subestado_desembolso || $_REQUEST["id_subestado"] == $subestado_desembolso_cliente || $_REQUEST["id_subestado"] == $subestado_desembolso_pdte_bloqueo))
			mysqli_query($link, "update simulaciones set estado_tesoreria = 'ABI', fecha_tesoreria = CURDATE() where id_simulacion = '".$id_simul."' AND estado_tesoreria IS NULL");
		
		for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++)
		{
			if ($_REQUEST["id_entidad".$i])
			{
				$entidad_desembolso = mysqli_query($link, "select nombre as nombre_entidad from entidades_desembolso where id_entidad = '".$_REQUEST["id_entidad".$i]."'");
				
				$fila1 = mysqli_fetch_array($entidad_desembolso);
				
				$nombre_entidad = $fila1["nombre_entidad"];
			}
			else
			{
				$nombre_entidad = "";
			}
			
			if ($_REQUEST["se_compra".$i] == "SI" && ($_REQUEST["id_entidad".$i] || $_REQUEST["entidad".$i]))
			{
				if ($_REQUEST["fecha_solicitudcarta".$i])
					$fecha_solicitudcarta = "'".$_REQUEST["fecha_solicitudcarta".$i]."'";
				else
					$fecha_solicitudcarta = "NULL";
				
				if ($_REQUEST["fecha_entrega".$i])
					$fecha_entrega = "'".$_REQUEST["fecha_entrega".$i]."'";
				else
					$fecha_entrega = "NULL";
				
				if ($_REQUEST["fecha_vencimiento".$i])
					$fecha_vencimiento = "'".$_REQUEST["fecha_vencimiento".$i]."'";
				else
					$fecha_vencimiento = "NULL";
				
				$agenda_tmp = mysqli_query($link, "select * from agenda where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
				
				if (mysqli_num_rows($agenda_tmp))
				{
					mysqli_query($link, "update agenda set entidad = '".utf8_encode($nombre_entidad." ".$_REQUEST["entidad".$i])."', fecha_vencimiento = ".$fecha_vencimiento." where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
				}
				else
				{
					mysqli_query($link, "insert into agenda (id_simulacion, consecutivo, entidad, dias_entrega, dias_vigencia, estado, fecha_sugerida, fecha_solicitud, fecha_entrega, fecha_vencimiento) values ('".$id_simul."', '".$i."', '".utf8_encode($nombre_entidad." ".$_REQUEST["entidad".$i])."', '".$_REQUEST["dias_entregah".$i]."', '".$_REQUEST["dias_vigenciah".$i]."', 'NO SOLICITADA', CURDATE(), ".$fecha_solicitudcarta.", ".$fecha_entrega.", ".$fecha_vencimiento.")");
				}
				
				mysqli_query($link, "insert into tesoreria_cc (id_simulacion, consecutivo, pagada, cuota_retenida, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$i."', '0', '0', '".$_SESSION["S_LOGIN"]."', NOW())");
			}
			else
			{
				mysqli_query($link, "delete from agenda where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
				
				mysqli_query($link, "delete from tesoreria_cc where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
			}
		}
		
		for ($i = 1; $i <= 3; $i++)
		{
			if ($_REQUEST["retanqueo".$i."_libranzah"] && ($_REQUEST["retanqueo".$i."_libranza"] != $_REQUEST["retanqueo".$i."_libranzah"]))
			{
				$queryDB = "select id_simulacion from simulaciones where id_simulacion = '".$id_simul."' AND (retanqueo1_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."' OR retanqueo2_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."' OR retanqueo3_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."')";

				$esta_en_otra_posicion_retanqueo = mysqli_query($link, $queryDB);

				if (!mysqli_num_rows($esta_en_otra_posicion_retanqueo))
				{
					$rs1 = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."'");
					
					if (mysqli_num_rows($rs1))
					{
						$fila1 = mysqli_fetch_array($rs1);
						
						mysqli_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
					}
				}
			}
			
			if ($_REQUEST["retanqueo".$i."_libranza"])
			{
				$rs1 = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo".$i."_libranza"]."'");
				
				if (mysqli_num_rows($rs1))
				{
					$fila1 = mysqli_fetch_array($rs1);
					
					mysqli_query($link, "update simulaciones set retanqueo_valor_liquidacion = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_valor_liquidacion"])."', retanqueo_intereses = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_intereses"])."', retanqueo_seguro = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_seguro"])."', retanqueo_cuotasmora = '".$_REQUEST["retanqueo".$i."_cuotasmora"]."', retanqueo_segurocausado = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_segurocausado"])."', retanqueo_gastoscobranza = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_gastoscobranza"])."', retanqueo_totalpagar = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_totalpagar"])."' where id_simulacion = '".$fila1["id_simulacion"]."'");
				}
			}
		}
		
		if ($_REQUEST["decision"] == $label_negado)
		{
			mysqli_query($link, "update simulaciones set decision='NEGADO',estado = 'NEG' where id_simulacion = '".$id_simul."'");
			//estado negado
			$observacion_negado="El credito actual ha sido guardado con estado NEGADO. Decision: ".$_REQUEST["decision"];
			if ($id_causal<>"NULL")
			{
				$queryCausal = mysqli_query($link, "select id_causal, nombre from causales where (estado = '1' AND tipo_causal = 'NEGACION') AND id_causal = '".$id_causal."'");
				$resCausal=mysqli_fetch_array($queryCausal);
				$observacion_negado.="Causal: ".$resCausal["nombre"];
			}

			mysqli_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$observacion_negado."', '".$_SESSION["S_LOGIN"]."', NOW())");
			
			$rs2 = mysqli_query($link, "select cedula, pagaduria, retanqueo1_libranza, retanqueo2_libranza, retanqueo3_libranza from simulaciones where id_simulacion = '".$id_simul."'");
			
			$fila2 = mysqli_fetch_array($rs2);
			
			for ($i = 1; $i <= 3; $i++)
			{
				if ($fila2["retanqueo".$i."_libranza"])
				{
					$rs1 = mysqli_query($link, "select id_simulacion from simulaciones where cedula = '".$fila2["cedula"]."' AND pagaduria = '".$fila2["pagaduria"]."' AND nro_libranza = '".$fila2["retanqueo".$i."_libranza"]."'");
					
					if (mysqli_num_rows($rs1))
					{
						$fila1 = mysqli_fetch_array($rs1);
						
						mysqli_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
					}
				}
			}
		}

		$consultarSimulacionDatos="SELECT * FROM simulaciones WHERE id_simulacion='".$id_simul."'";
		$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$id_simul."' AND se_compra='SI'";
		$queryComprasCarteraCredito=mysqli_query($link, $consultarComprasCarteraCredito);
		$subestadoBloqueoComision = 48;
		$querySimulacionDatos=mysqli_query($link, $consultarSimulacionDatos);
		
		if (mysqli_num_rows($queryComprasCarteraCredito)>0) {
			$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$id_simul."' AND se_compra='SI'";
			$queryComprasCC=mysqli_query($link, $consultarComprasCC);
			$resComprasCC=mysqli_fetch_array($queryComprasCC);
			$resSimulacionDatos=mysqli_fetch_assoc($querySimulacionDatos);
			if ($resComprasCC["cuota"]>0){
                if ($resSimulacionDatos["retanqueo1_libranza"]=="" && $resSimulacionDatos["retanqueo2_libranza"]=="" && $resSimulacionDatos["retanqueo3_libranza"]==""){
                    $tipo_crediton="COMPRAS DE CARTERA";    
                    $subestadoBloqueoComision = 78;
                }else{
                    $tipo_crediton="COMPRAS CON RETANQUEO";
                    $subestadoBloqueoComision = 48;
                }   
            }else{
                if ($resComprasCC["valor_pagar"]>0){
                    $tipo_crediton="LIBRE CON SANEAMIENTO";
                    $subestadoBloqueoComision = 78;
                }else{
					if ($resSimulacionDatos["retanqueo1_libranza"]<>"" || $resSimulacionDatos["retanqueo2_libranza"]<>"" || $resSimulacionDatos["retanqueo3_libranza"]<>""){
						$subestadoBloqueoComision = 48;
					}
				}         
            }           
        }else{
            $tipo_crediton="LIBRE INVERSION";                    
            $subestadoBloqueoComision = 46;
        }

		$sqlEstBloqueoComision="SELECT a.id_subestado from simulaciones_subestados a WHERE a.id_simulacion = $id_simul AND a.id_subestado IN($subestadoBloqueoComision)";
		$queryEstBloqueoComision=mysqli_query($link, $sqlEstBloqueoComision);
		
		if (mysqli_num_rows($queryEstBloqueoComision) == 0){//No ha Pasado por estados: 48,78

			$sqlTasaComision="SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, if(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a WHERE a.id_tasa_comision ='". $_REQUEST["tipo_tasa_comision"]."'";

			$queryTasaComision=mysqli_query($link, $sqlTasaComision);

			if (mysqli_num_rows($queryTasaComision)>0){
				$respTasaComision = mysqli_fetch_array($queryTasaComision);
				$id_tasa_comision = $respTasaComision["id_tasa_comision"];
				$id_tipo_comision = $respTasaComision["id_tipo"];

				//consultarTasaComisionAnterior
				$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
				$querySimTasaCom=mysqli_query($link, $sqlSimTasaCom);
				$respSimTasaCom = mysqli_fetch_assoc($querySimTasaCom);
				$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

				if($id_tasa_comision_anterior != $id_tasa_comision){

					mysqli_query($link, "UPDATE simulaciones SET id_tasa_comision = $id_tasa_comision, id_tipo_tasa_comision = $id_tipo_comision WHERE id_simulacion  = '".$id_simul."'");
					mysqli_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", $id_tasa_comision, ".$_SESSION['S_IDUSUARIO'].", NOW())");
				}
			}else{
				mysqli_query($link, "UPDATE simulaciones SET id_tasa_comision = 0 WHERE id_simulacion  = '".$id_simul."'");
				mysqli_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", 0, ".$_SESSION['S_IDUSUARIO'].", NOW())");
			}
		}
		
		$mensaje .= "Simulacion guardada exitosamente";
	}
}

?>
<script>
alert("<?php echo $mensaje ?>");

<?php

if (!$_REQUEST["id_cazador"])
{
	if (!$_REQUEST["buscar"] && $_REQUEST["back"] != "prospecciones" && $_REQUEST["back"] != "pilotofdc")
	{
		$_REQUEST["buscar"] = "1";
		
		$_REQUEST["descripcion_busqueda"] = $_REQUEST["cedula"];
	}
	
?>
//window.location = '<?php echo $_REQUEST["back"] ?>.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
<?php

}
else
{

?>
//window.location = 'cazador.php';
<?php

}

?>
</script>
