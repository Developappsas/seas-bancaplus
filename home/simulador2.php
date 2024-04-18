<?php 

include ('../functions.php'); 
include ('../controles/FDC.php');

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] == "TESORERIA"){
	exit;
}

$link = conectar_utf();
?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
	<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>

<?php	

if ($_REQUEST["id_comercial"] && $_REQUEST["cedula"] && $_REQUEST["nombre"] && $_REQUEST["pagaduria"] && $_REQUEST["fecha_estudio"]){

	$procesar_simulacion = 1;
	
	if ($_REQUEST["telemercadeo"] != "1") {
		$_REQUEST["telemercadeo"] = "0";
	}
	
	if ($_REQUEST["fecha_nacimiento"]){
		$fecha_nacimiento = "'".$_REQUEST["fecha_nacimiento"]."'";
	}
	else{
		$fecha_nacimiento = "NULL";
	}
	
	if ($_REQUEST["fecha_inicio_labor"]){
		$fecha_inicio_labor = "'".$_REQUEST["fecha_inicio_labor"]."'";
	}
	else{
		$fecha_inicio_labor = "NULL";
	}
	
	if ($_REQUEST["sin_aportes"] != "1"){
		$_REQUEST["sin_aportes"] = "0";
	}
	
	if ($_REQUEST["sin_seguro"] != "1"){
		$_REQUEST["sin_seguro"] = "0";
	}

	if ($_REQUEST["aumento_salario_minimo"] != "1"){
		$_REQUEST["aumento_salario_minimo"] = "0";
	}

	if (!isset($_REQUEST["sin_iva_servicio_nube"])){
		$_REQUEST["sin_iva_servicio_nube"] = "0";
	}
	
	if ($_REQUEST["id_plan_seguro"]){
		$id_plan_seguro = "'".$_REQUEST["id_plan_seguro"]."'";
	}
	else{
		$id_plan_seguro = "NULL";
	}
	
	if ($_REQUEST["nro_compra_cartera_seguro"]){
		$nro_compra_cartera_seguro = "'".$_REQUEST["nro_compra_cartera_seguro"]."'";
	}
	else{
		$nro_compra_cartera_seguro = "NULL";
	}
		
	
	for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++){

		if ($_REQUEST["id_entidad".$i]){
			$id_entidad[$i] = "'".$_REQUEST["id_entidad".$i]."'";
		}
		else{
			$id_entidad[$i] = "NULL";
		}
	}
	
	if ($_REQUEST["nro_libranza"]){
		$nro_libranza = "'".$_REQUEST["nro_libranza"]."'";
	}
	else{
		$nro_libranza = "NULL";
	}

	if ($_REQUEST["valor_comision_descontar"]){
		$valor_comision_descontar = str_replace(",", "", $_REQUEST["valor_comision_descontar"]);
	} else{
		$valor_comision_descontar = "NULL";		
	}
	
	if ($_REQUEST["fecha_llamada_clientef"]){

		if (substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) == "12" && $_REQUEST["fecha_llamada_clientej"] == "AM"){
			$_REQUEST["fecha_llamada_clienteh"] = "00".substr($_REQUEST["fecha_llamada_clienteh"], 2, 3);
		}
		
		if (substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) != "12" && $_REQUEST["fecha_llamada_clientej"] == "PM"){

			$hora = substr($_REQUEST["fecha_llamada_clienteh"], 0, 2) + 12;			
			$_REQUEST["fecha_llamada_clienteh"] = $hora.substr($_REQUEST["fecha_llamada_clienteh"], 2, 3);
		}
		
		$fecha_llamada_cliente = "'".$_REQUEST["fecha_llamada_clientef"]." ".$_REQUEST["fecha_llamada_clienteh"]."'";
	}
	else{
		$fecha_llamada_cliente = "NULL";
	}
	
	if ($_REQUEST["nro_cuenta"]){
		$nro_cuenta = "'".$_REQUEST["nro_cuenta"]."'";
	}
	else{
		$nro_cuenta = "NULL";
	}
	
	if ($_REQUEST["tipo_cuenta"]){
		$tipo_cuenta = "'".$_REQUEST["tipo_cuenta"]."'";
	}
	else{
		$tipo_cuenta = "NULL";
	}
	
	if ($_REQUEST["id_banco"]){
		$id_banco = "'".$_REQUEST["id_banco"]."'";
	}
	else{
		$id_banco = "NULL";
	}
	
	if ($_REQUEST["id_subestado"]){
		$id_subestado = "'".$_REQUEST["id_subestado"]."'";
	}
	else{
		$id_subestado = "NULL";
	}
	
	if ($_REQUEST["id_causal"]){
		$id_causal = "'".$_REQUEST["id_causal"]."'";
	}
	else{
		$id_causal = "NULL";
	}
	
	if ($_REQUEST["id_caracteristica"]){
		$id_caracteristica = "'".$_REQUEST["id_caracteristica"]."'";
	}
	else{
		$id_caracteristica = "NULL";
	}
	
	if ($_REQUEST["calificacion"]){
		$calificacion = "'".$_REQUEST["calificacion"]."'";
	}
	else{
		$calificacion = "NULL";
	}
	
	if ($_REQUEST["formulario_seguro"] != "1"){
		$_REQUEST["formulario_seguro"] = "0";
	}
	
	if ($_REQUEST["id_analista_gestion_comercial"]){
		$id_analista_gestion_comercial = "'".$_REQUEST["id_analista_gestion_comercial"]."'";
	}
	else{
		$id_analista_gestion_comercial = "NULL";
	}
	
	if ($_REQUEST["id_analista_riesgo_operativo"]){
		$id_analista_riesgo_operativo = "'".$_REQUEST["id_analista_riesgo_operativo"]."'";
	}
	else{
		$id_analista_riesgo_operativo = "NULL";
	}
		
	if ($_REQUEST["id_analista_riesgo_crediticio"]){
		$id_analista_riesgo_crediticio = "'".$_REQUEST["id_analista_riesgo_crediticio"]."'";
	}
	else{
		$id_analista_riesgo_crediticio = "NULL";
	}
	
	if ($_REQUEST["dia_confirmacion"]){
		$dia_confirmacion = "'".$_REQUEST["dia_confirmacion"]."'";
	}
	else{
		$dia_confirmacion = "NULL";
	}
	
	if ($_REQUEST["dia_vencimiento"]){
		$dia_vencimiento = "'".$_REQUEST["dia_vencimiento"]."'";
	}
	else{
		$dia_vencimiento = "NULL";
	}
	
	if ($_REQUEST["status"]){
		$status = "'".$_REQUEST["status"]."'";
	}
	else{
		$status = "NULL";
	}
	
	if ($_REQUEST["bloqueo_cuota"] != "1"){
		$_REQUEST["bloqueo_cuota"] = "0";
	}
	
	
	if (!$_REQUEST["id_simulacion"])
	 {
		$existe_en_empleados = sqlsrv_query($link, "select cedula from empleados where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($existe_en_empleados))
		{
			$existe_en_empleados_creacion = sqlsrv_query($link, "select * from empleados_creacion where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."'");
			
			if (sqlsrv_num_rows($existe_en_empleados_creacion)){
				$fila1 = sqlsrv_fetch_array($existe_en_empleados_creacion);
				
				if ($fila1["fecha_modificacion"]){
					$empleado_manual = 0;
				}
				else{
					$empleado_manual = 1;
				}
			}
			else
			{
				$empleado_manual = 0;
			}
			
			$existe_recien_creada = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND id_comercial = '".$_REQUEST["id_comercial"]."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."') AND DATEDIFF(SECOND,'1970-01-01', GETUTCDATE()) - DATEDIFF(SECOND,'1970-01-01', fecha_creacion) <= 60", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			if (sqlsrv_num_rows($existe_recien_creada)){
				echo "<script>function myFunction() { alert('Simulacion guardada exitosamente'); window.location = '".$_REQUEST["back"].".php?descripcion_busqueda=".$_REQUEST["cedula"]."&buscar=1'; } setTimeout(myFunction, 1000)</script>";
				
				exit;
			}
				
			$omitir_validacion_30_dias = 1;

			// Para superar la coyontura de prospecciones a reproceso se baja el llmite de 30 a 10 dias
			$existe_simulacion = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND DATEDIFF(getdate(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_simulacion)){
				$existe_simulacion2 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND DATEDIFF(getdate(), fecha_estudio) <= 30 AND estado IN ('ING', 'EST') AND id_comercial = '".$_REQUEST["id_comercial"]."' AND id_oficina IN (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (!sqlsrv_num_rows($existe_simulacion2))
					$omitir_validacion_30_dias = 0;
			}
			if (!sqlsrv_num_rows($existe_simulacion) || $omitir_validacion_30_dias)
			{
				sqlsrv_query($link, "START TRANSACTION");
				
				sqlsrv_query($link, "insert into simulaciones (id_comercial, id_oficina, telemercadeo, fecha_estudio, cedula, nombre, pagaduria, pa, ciudad, institucion, nivel_educativo, fecha_nacimiento, telefono, meses_antes_65, fecha_inicio_labor, medio_contacto, salario_basico, adicionales, bonificacion, total_ingresos, aportes, otros_aportes, total_aportes, total_egresos, salario_minimo, ingresos_menos_aportes, salario_libre, nivel_contratacion, embargo_actual, historial_embargos, embargo_alimentos, embargo_centrales, descuentos_por_fuera, cartera_mora, valor_cartera_mora, puntaje_datacredito, puntaje_cifin, valor_descuentos_por_fuera, calif_sector_financiero, calif_sector_real, calif_sector_cooperativo, id_unidad_negocio, tasa_interes, plazo, id_plan_seguro, valor_seguro, nro_compra_cartera_seguro, tipo_credito, suma_al_presupuesto, total_cuota, total_valor_pagar, total_se_compra, retanqueo1_libranza, retanqueo1_cuota, retanqueo1_valor, retanqueo2_libranza, retanqueo2_cuota, retanqueo2_valor, retanqueo3_libranza, retanqueo3_cuota, retanqueo3_valor, retanqueo_total_cuota, retanqueo_total, opcion_credito, opcion_cuota_cli, opcion_desembolso_cli, opcion_cuota_ccc, opcion_desembolso_ccc, opcion_cuota_cmp, opcion_desembolso_cmp, opcion_cuota_cso, opcion_desembolso_cso, desembolso_cliente, decision, decision_sistema, valor_visado, bloqueo_cuota, bloqueo_cuota_valor, fecha_llamada_cliente, nro_cuenta, tipo_cuenta, id_banco, id_subestado, id_causal, id_caracteristica, calificacion, dia_confirmacion, dia_vencimiento, status, valor_credito, resumen_ingreso, incor, comision, utilidad_neta, sobre_el_credito, estado, tipo_producto, descuento1, descuento2, descuento3, descuento4, descuento5, descuento6, descuento_transferencia, 
				descuento1_valor,
				descuento2_valor,
				descuento3_valor,
				descuento4_valor,
				descuento5_valor,
				descuento6_valor,
				descuento8_valor,
				descuento9_valor,
				descuento10_valor,
				sin_iva_servicio_nube,
				aumento_salario_minimo
				
				porcentaje_seguro, valor_por_millon_seguro, porcentaje_extraprima, formulario_seguro, sin_aportes, sin_seguro, empleado_manual, iva, usuario_radicado, fecha_radicado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_comercial"]."', (select id_oficina from oficinas_usuarios where id_usuario = '".$_REQUEST["id_comercial"]."'), '".$_REQUEST["telemercadeo"]."', '".$_REQUEST["fecha_estudio"]."', '".$_REQUEST["cedula"]."', '".utf8_encode($_REQUEST["nombre"])."', '".utf8_encode($_REQUEST["pagaduria"])."', '".utf8_encode($_REQUEST["pa"])."', '".utf8_encode($_REQUEST["ciudad"])."', '".utf8_encode($_REQUEST["institucion"])."', '".utf8_encode($_REQUEST["nivel_educativo"])."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".$_REQUEST["meses_antes_65"]."', ".$fecha_inicio_labor.", '".$_REQUEST["medio_contacto"]."', '".str_replace(",", "", $_REQUEST["salario_basico"])."', '".str_replace(",", "", $_REQUEST["adicionales"])."', '".str_replace(",", "", $_REQUEST["bonificacion"])."', '".str_replace(",", "", $_REQUEST["total_ingresos"])."', '".str_replace(",", "", $_REQUEST["aportes"])."', '".str_replace(",", "", $_REQUEST["otros_aportes"])."', '".str_replace(",", "", $_REQUEST["total_aportes"])."', '".str_replace(",", "", $_REQUEST["total_egresos"])."', '".str_replace(",", "", $_REQUEST["salario_minimo"])."', '".str_replace(",", "", $_REQUEST["ingresos_menos_aportes"])."', '".str_replace(",", "", $_REQUEST["salario_libre"])."', '".utf8_encode($_REQUEST["nivel_contratacion"])."', '".$_REQUEST["embargo_actual"]."', '".$_REQUEST["historial_embargos"]."', '".$_REQUEST["embargo_alimentos"]."', '".$_REQUEST["embargo_centrales"]."', '".$_REQUEST["descuentos_por_fuera"]."', '".$_REQUEST["cartera_mora"]."', '".str_replace(",", "", $_REQUEST["valor_cartera_mora"])."', '".$_REQUEST["puntaje_datacredito"]."', '".$_REQUEST["puntaje_cifin"]."', '".str_replace(",", "", $_REQUEST["valor_descuentos_por_fuera"])."', '".$_REQUEST["calif_sector_financiero"]."', '".$_REQUEST["calif_sector_real"]."', '".$_REQUEST["calif_sector_cooperativo"]."', '".$_REQUEST["id_unidad_negocio"]."', '".$_REQUEST["tasa_interes"]."', '".$_REQUEST["plazo"]."', ".$id_plan_seguro.", '".str_replace(",", "", $_REQUEST["valor_seguro"])."', ".$nro_compra_cartera_seguro.", '".utf8_encode($_REQUEST["tipo_credito"])."', '".str_replace(",", "", $_REQUEST["suma_al_presupuesto"])."', '".str_replace(",", "", $_REQUEST["total_cuota"])."', '".str_replace(",", "", $_REQUEST["total_valor_pagar"])."', '".$_REQUEST["total_se_compra"]."', '".$_REQUEST["retanqueo1_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo1_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo1_valor"])."', '".$_REQUEST["retanqueo2_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo2_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo2_valor"])."', '".$_REQUEST["retanqueo3_libranza"]."', '".str_replace(",", "", $_REQUEST["retanqueo3_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo3_valor"])."', '".str_replace(",", "", $_REQUEST["retanqueo_total_cuota"])."', '".str_replace(",", "", $_REQUEST["retanqueo_total"])."', '".$_REQUEST["opcion_credito"]."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cli"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cli"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_ccc"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_ccc"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cmp"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cmp"])."', '".str_replace(",", "", $_REQUEST["opcion_cuota_cso"])."', '".str_replace(",", "", $_REQUEST["opcion_desembolso_cso"])."', '".str_replace(",", "", $_REQUEST["desembolso_cliente"])."', '".$_REQUEST["decision"]."', '".$_REQUEST["decision_sistema"]."', '".str_replace(",", "", $_REQUEST["valor_visado"])."', '".$_REQUEST["bloqueo_cuota"]."', '".str_replace(",", "", $_REQUEST["bloqueo_cuota_valor"])."', ".$fecha_llamada_cliente.", ".$nro_cuenta.", ".$tipo_cuenta.", ".$id_banco.", ".$id_subestado.", ".$id_causal.", ".$id_caracteristica.", ".$calificacion.", ".$dia_confirmacion.", ".$dia_vencimiento.", ".$status.", '".str_replace(",", "", $_REQUEST["valor_credito"])."', '".str_replace(",", "", $_REQUEST["resumen_ingreso"])."', '".str_replace(",", "", $_REQUEST["incor"])."', '".str_replace(",", "", $_REQUEST["comision"])."', '".str_replace(",", "", $_REQUEST["utilidad_neta"])."', '".$_REQUEST["sobre_el_credito"]."', 'ING', '".$_REQUEST["tipo_producto"]."', '".$_REQUEST["descuento1"]."', '".$_REQUEST["descuento2"]."', '".$_REQUEST["descuento3"]."', '".$_REQUEST["descuento4"]."', '".$_REQUEST["descuento5"]."', '".$_REQUEST["descuento6"]."', '".$_REQUEST["descuento_transferencia"]."', 
				'".str_replace(",", "", $_REQUEST["descuento1_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento2_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento3_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento4_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento5_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento6_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento8_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento9_valor"])."',
				'".str_replace(",", "", $_REQUEST["descuento10_valor"])."',
				'".$_REQUEST["sin_iva_servicio_nube"]."',
				'".$_REQUEST["aumento_salario_minimo"]."',
				'".$_REQUEST["porcentaje_seguro"]."', '".$_REQUEST["valor_por_millon_seguro"]."', '".$_REQUEST["porcentaje_extraprimah"]."', '".$_REQUEST["formulario_seguro"]."', '".$_REQUEST["sin_aportes"]."', '".$_REQUEST["sin_seguro"]."', '".$empleado_manual."', '".$_REQUEST["iva"]."', '".$_SESSION["S_LOGIN"]."', getate(), '".$_SESSION["S_LOGIN"]."', getate())");

			
				
				//$rs = sqlsrv_query("select MAX(id_simulacion) as m from simulaciones");
				// sqlsrv_query($link, "SET @id_simulacion = LAST_INSERT_ID()");
				$resId = sqlsrv_query($link, "SELECT scope_identity() as id");
				$resId2 = sqlsrv_fetch_array($resId);
				$ultima_id_simulacion = $resId2['id'];
				echo $ultima_id_simulacion;

				$consultarFDC=sqlsrv_query($link, "SELECT * FROM simulaciones_fdc where id_simulacion='".$ultima_id_simulacion."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				



				if (sqlsrv_num_rows($consultarFDC)==0){
					sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES (@id_simulacion,1972,CURRENT_TIMESTAMP,'s',1,1)");	
				}
				
		
				sqlsrv_query($link, "COMMIT");

				// $consultaID="SELECT ".." as id_simulacion";
				// $queryMultiSet2=sqlsrv_query($link, $consultaID);
				// $resMultiSet=sqlsrv_fetch_array($queryMultiSet2);
	
				 $id_simul = $ultima_id_simulacion;
	
				
				for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++){

					if ($id_entidad[$i] != "NULL" OR $_REQUEST["entidad".$i] OR $_REQUEST["cuota".$i] != "0" OR $_REQUEST["valor_pagar".$i] != "0" OR $_REQUEST["fecha_vencimiento".$i]){

						sqlsrv_query($link, "insert into simulaciones_comprascartera (id_simulacion, consecutivo, id_entidad, entidad, cuota, valor_pagar, se_compra, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$i."', ".$id_entidad[$i].", '".utf8_encode($_REQUEST["entidad".$i])."', '".str_replace(",", "", $_REQUEST["cuota".$i])."', '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', '".$_REQUEST["se_compra".$i]."', '".$_SESSION["S_LOGIN"]."', getdate())");
					}
				}
				
				if ($_REQUEST["observaciones"])
					sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".($_REQUEST["observaciones"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
				
				if (sqlsrv_num_rows($existe_simulacion))
					sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".utf8_encode("El credito actual ha sido estudiado con menos de 30 dias lo cual no cumple con las politicas de fabrica. Por favor evaluar si es un credito dividido por superar los 80 millones o un credito menor a 30 dias")."', 'system', GETDATE())");
				
				sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, ciudad, email, clave, sexo) values ('".$id_simul."', '".$_REQUEST["cedula"]."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".utf8_encode($_REQUEST["celular"])."', '".utf8_encode($_REQUEST["direccion"])."', '".utf8_encode($_REQUEST["ciudad_residencia"])."', '".utf8_encode($_REQUEST["mail"])."', '".utf8_encode($_REQUEST["clave"])."', '".$_REQUEST["sexo"]."')");
				
				$descuentos_adicionales = sqlsrv_query($link, "select * from descuentos_adicionales where pagaduria = '".$_REQUEST["pagaduria"]."' and estado = '1' order by id_descuento");
				
				while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
				{
					sqlsrv_query($link, "insert into simulaciones_descuentos (id_simulacion, id_descuento, porcentaje) values ('".$id_simul."', '".$fila1["id_descuento"]."', '".$_REQUEST["descuentoadicional".$fila1["id_descuento"]]."')");
				}
				
				$rs1 = sqlsrv_query($link, "select id_oficina from simulaciones where id_simulacion = '".$id_simul."'");
				
				$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
				
				$_REQUEST["id_oficina"] = $fila1["id_oficina"];
				
				if ($_REQUEST["id_cazador"]){
					sqlsrv_query($link, "update cazador set sub_estado = 'En proceso' where id_cazador = '".$_REQUEST["id_cazador"]."'");
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
		$queryAnalistaActual=sqlsrv_query($link, "select * from simulaciones where id_simulacion='".$_REQUEST["id_simulacion"]."'");
		$resAnalistaActual=sqlsrv_fetch_array($queryAnalistaActual);
		$id_analista_riesgo_operativoh=$resAnalistaActual["id_analista_riesgo_operativo"];
		$valAnalistasKreditPlus=0;

			
			$queryAnalistasKreditPlus=sqlsrv_query($link, "SELECT * FROM usuarios WHERE subtipo='COORD_CREDITO' AND id_usuario='".$_SESSION["S_IDUSUARIO"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
				if(sqlsrv_num_rows($queryAnalistasKreditPlus)>0){
					$valAnalistasKreditPlus=1;
				}
				
				

		// $consultarFormularioDigitalDiligenciado=sqlsrv_query($link, "SELECT * FROM solicitud WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
		// $resFormularioDigitalDiligenciado=sqlsrv_fetch_Array($consultarFormularioDigitalDiligenciado);
			
	
		// if (($_REQUEST["id_subestado"]=="79") && ($resFormularioDigitalDiligenciado["seccion_info_personal"] == "0" || $resFormularioDigitalDiligenciado["seccion_actividad_laboral"] == "0" || $resFormularioDigitalDiligenciado["seccion_info_financiera"] == "0" || $resFormularioDigitalDiligenciado["seccion_referencias"] == "0" || $resFormularioDigitalDiligenciado["seccion_datos_internacionales"] == "0" || $resFormularioDigitalDiligenciado["seccion_facta"] == "0" || $resFormularioDigitalDiligenciado["seccion_varios"] == "0"))
		// {
		// 	$mensaje="Debe diligenciar complementamente formulario de Solicitud para continuar el proceso";
		// 	$cambio_estado=0;
		// }else{
			
		// 	$cambioEstado="UPDATE simulaciones SET id_subestado = ".$id_subestado." WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'";
		// 	sqlsrv_query($link, $cambioEstado);
		// 	$cambio_estado=1;
		// 	//CAMBIAR A 1 PARA FUNCIONAR FOMRULARIO DIGITAL
		// }


		if($_REQUEST["id_subestado"] == 72){

			$conTieneFirmaDigital = sqlsrv_query($link, "SELECT formato_digital FROM simulaciones WHERE id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND formato_digital = '1'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

			if($conTieneFirmaDigital && sqlsrv_num_rows($conTieneFirmaDigital) > 0 ){//el credito es digital y esta firmado asi que podemos continuar
				//Continua con el cambio de estado
				$cambio_estado=1;
				
			}else{//el credito es Fisico y esta firmado comprobamos adjuntos completos
				
				if($_REQUEST["nivel_contratacion"] == 'PENSIONADO' && !in_array($_REQUEST['pagaduria'], $array_pagadurias_excluidas_formato_seguro) ){ 
					$tipoadjuntos_requeridosFirma .= ",30";
				}
				
				$conAdjuntosPendientes=sqlsrv_query($link, "SELECT b.id_tipo, b.nombre, a.id_adjunto, a.id_simulacion FROM tipos_adjuntos b LEFT JOIN adjuntos a ON a.id_tipo = b.id_tipo AND a.id_simulacion = '".$_REQUEST["id_simulacion"]."' WHERE b.estado = 1 AND b.id_tipo IN(".$tipoadjuntos_requeridosFirma.") AND a.id_simulacion IS NULL GROUP BY b.id_tipo", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				if($conAdjuntosPendientes && sqlsrv_num_rows($conAdjuntosPendientes) > 0){
					$_REQUEST["id_subestado"] = $id_subestado = $_REQUEST["id_subestadoh"]; //Moficamos al valor anterior para no cambia sub estado
					$mensaje.='No se pudo cambiar de SubEstado la simulación, se requieren Adjuntos faltantes:';
					$mensaje.='<ul>';
					while($resAdPend=sqlsrv_fetch_Array($conAdjuntosPendientes)){
						$mensaje.= "<li>".$resAdPend["nombre"].'</li>';
					}	
					$mensaje.='</ul>';
					$cambio_estado=0;							
				}else{//Continua con el cambio de estado
					$cambio_estado=1;
				}
			}
		}else{//Continua con el cambio de estado

			$queryCodInternoSub = sqlsrv_query($link, "select cod_interno from subestados where id_subestado = '".$_REQUEST["id_subestado"]."'");
			$datosCodIntenoSub = sqlsrv_fetch_array($queryCodInternoSub);       
			$cod_interno_subestado = $datosCodIntenoSub["cod_interno"];
			$querySimulFechaIncioValidacion = sqlsrv_query($link, "SELECT id_simulacion, FORMAT(fecha_creacion, 'Y-m-d') from simulaciones where FORMAT(fecha_creacion, 'Y-m-d') > '2023-02-01' AND id_simulacion = '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			$inicioValidacionSuperaUbica = 0;

			if($querySimulFechaIncioValidacion){ $inicioValidacionSuperaUbica = sqlsrv_num_rows($querySimulFechaIncioValidacion); }   

			if (($cod_interno_subestado == 30) && ($_REQUEST["supera_direccion_ubica"] == '' || $_REQUEST["supera_telefono_ubica"] == '') && $inicioValidacionSuperaUbica > 0){
				$_REQUEST["id_subestado"] = $id_subestado = $_REQUEST["id_subestadoh"]; //Moficamos al valor anterior para no cambia sub estado
				$mensaje.='No se pudo cambiar de SubEstado la simulación, se requieren llenar los Campos:';
				$mensaje.='<ul>';
					if($_REQUEST["supera_direccion_ubica"] == '') { $mensaje.= '<li>Supera Dirección ubica</li>'; }
					if($_REQUEST["supera_telefono_ubica"] == '') { $mensaje.= '<li>Supera Teléfono ubica</li>'; }
				$mensaje.='</ul>';
				$cambio_estado=0;
			}else{

				if (($cod_interno_subestado < 999 && $cod_interno_subestado > 35) && ($_REQUEST["supera_direccion_ubica"] == '' || $_REQUEST["supera_telefono_ubica"] == '' || $_REQUEST["supera_proforense"] == '' || $_REQUEST["pagare_autenticado"] == '' || $_REQUEST["supera_evidente"] == '') && $inicioValidacionSuperaUbica > 0){
					$_REQUEST["id_subestado"] = $id_subestado = $_REQUEST["id_subestadoh"]; //Moficamos al valor anterior para no cambia sub estado
					$mensaje.='No se pudo cambiar de SubEstado la simulación, se requieren llenar los Campos:';
					$mensaje.='<ul>';
						if($_REQUEST["supera_direccion_ubica"] == '') { $mensaje.= '<li>Supera Dirección ubica</li>'; }
						if($_REQUEST["supera_telefono_ubica"] == '') { $mensaje.= '<li>Supera Teléfono ubica</li>'; }
						if($_REQUEST["supera_proforense"] == '') { $mensaje.= '<li>Supera Proforense</li>'; }
						if($_REQUEST["pagare_autenticado"] == '') { $mensaje.= '<li>Supera Autenticado</li>'; }
						if($_REQUEST["supera_evidente"] == '') { $mensaje.= '<li>Supera Evidente</li>'; }
					$mensaje.='</ul>';
					$cambio_estado=0;
				}else{
					$cambio_estado=1; //CAMBIAR A 1 PARA FUNCIONAR FOMRULARIO DIGITAL
				}
			}
		}
		
		if (($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_TIPO"] == "OFICINA" || $_SESSION["S_TIPO"] == "OPERACIONES") && ($_REQUEST["estado"] == "ING") && !($_SESSION["S_SUBTIPO"] == "COORD_CREDITO" && !$_REQUEST["id_subestado"])){
			$estado = "EST";
		}
		else{
			$estado = $_REQUEST["estado"];
		}

		sqlsrv_query($link, "INSERT INTO simulaciones_log SELECT *, '".$_SESSION["S_LOGIN"]."', getdate() FROM simulaciones WHERE id_simulacion ='".$_REQUEST["id_simulacion"]."'");
		sqlsrv_query($link, "INSERT INTO simulaciones_comprascartera_log SELECT *, '".$_SESSION["S_LOGIN"]."', getdate() FROM simulaciones_comprascartera WHERE id_simulacion ='".$_REQUEST["id_simulacion"]."'");
		sqlsrv_query($link, "INSERT INTO tesoreria_cc_log SELECT *, '".$_SESSION["S_LOGIN"]."', getdate() FROM tesoreria_cc WHERE id_simulacion ='".$_REQUEST["id_simulacion"]."'");
			
		sqlsrv_query($link, "UPDATE simulaciones set valor_comision_descontar = '".$valor_comision_descontar."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");

		if($_REQUEST["proposito"] == ''){
			$_REQUEST["proposito"] = 0;
		}

			
		$query = "UPDATE simulaciones set proposito_credito = '".$_REQUEST["proposito"]."', supera_direccion_ubica = '".$_REQUEST["supera_direccion_ubica"]."', supera_telefono_ubica = '".$_REQUEST["supera_telefono_ubica"]."', supera_proforense = '".$_REQUEST["supera_proforense"]."', pagare_autenticado = '".$_REQUEST["pagare_autenticado"]."', supera_evidente ='". $_REQUEST["supera_evidente"]."', fecha_estudio = '".$_REQUEST["fecha_estudio"]."', pagaduria = '".$_REQUEST["pagaduria"]."', pa = '".$_REQUEST["pa"]."', ciudad = '".$_REQUEST["ciudad"]."', institucion = '".$_REQUEST["institucion"]."', nivel_educativo = '".$_REQUEST["nivel_educativo"]."', fecha_nacimiento = ".$fecha_nacimiento.", meses_antes_65 = '".$_REQUEST["meses_antes_65"]."', fecha_inicio_labor = ".$fecha_inicio_labor.", medio_contacto = '".$_REQUEST["medio_contacto"]."', telefono = '".$_REQUEST["telefono"]."', salario_basico = '".str_replace(",", "", $_REQUEST["salario_basico"])."', adicionales = '".str_replace(",", "", $_REQUEST["adicionales"])."', bonificacion = '".str_replace(",", "", $_REQUEST["bonificacion"])."', total_ingresos = '".str_replace(",", "", $_REQUEST["total_ingresos"])."', aportes = '".str_replace(",", "", $_REQUEST["aportes"])."', otros_aportes = '".str_replace(",", "", $_REQUEST["otros_aportes"])."', total_aportes = '".str_replace(",", "", $_REQUEST["total_aportes"])."', total_egresos = '".str_replace(",", "", $_REQUEST["total_egresos"])."', salario_minimo = '".str_replace(",", "", $_REQUEST["salario_minimo"])."', ingresos_menos_aportes = '".str_replace(",", "", $_REQUEST["ingresos_menos_aportes"])."', salario_libre = '".str_replace(",", "", $_REQUEST["salario_libre"])."', nivel_contratacion = '".$_REQUEST["nivel_contratacion"]."', embargo_actual = '".$_REQUEST["embargo_actual"]."', historial_embargos = '".$_REQUEST["historial_embargos"]."', embargo_alimentos = '".$_REQUEST["embargo_alimentos"]."', embargo_centrales = '".$_REQUEST["embargo_centrales"]."', descuentos_por_fuera = '".$_REQUEST["descuentos_por_fuera"]."', cartera_mora = '".$_REQUEST["cartera_mora"]."', valor_cartera_mora = '".str_replace(",", "", $_REQUEST["valor_cartera_mora"])."', puntaje_datacredito = '".$_REQUEST["puntaje_datacredito"]."', puntaje_cifin = '".$_REQUEST["puntaje_cifin"]."', valor_descuentos_por_fuera = '".str_replace(",", "", $_REQUEST["valor_descuentos_por_fuera"])."', calif_sector_financiero = '".$_REQUEST["calif_sector_financiero"]."', calif_sector_real = '".$_REQUEST["calif_sector_real"]."', calif_sector_cooperativo = '".$_REQUEST["calif_sector_cooperativo"]."', id_unidad_negocio = '".$_REQUEST["id_unidad_negocio"]."', tasa_interes = '".$_REQUEST["tasa_interes"]."', plazo = '".$_REQUEST["plazo"]."', id_plan_seguro = ".$id_plan_seguro.", valor_seguro = '".str_replace(",", "", $_REQUEST["valor_seguro"])."', nro_compra_cartera_seguro = ".$nro_compra_cartera_seguro.", tipo_credito = '".$_REQUEST["tipo_credito"]."', suma_al_presupuesto = '".str_replace(",", "", $_REQUEST["suma_al_presupuesto"])."', total_cuota = '".str_replace(",", "", $_REQUEST["total_cuota"])."', total_valor_pagar = '".str_replace(",", "", $_REQUEST["total_valor_pagar"])."', total_se_compra = '".$_REQUEST["total_se_compra"]."', retanqueo1_libranza = '".$_REQUEST["retanqueo1_libranza"]."', retanqueo1_cuota = '".str_replace(",", "", $_REQUEST["retanqueo1_cuota"])."', retanqueo1_valor = '".str_replace(",", "", $_REQUEST["retanqueo1_valor"])."', retanqueo2_libranza = '".$_REQUEST["retanqueo2_libranza"]."', retanqueo2_cuota = '".str_replace(",", "", $_REQUEST["retanqueo2_cuota"])."', retanqueo2_valor = '".str_replace(",", "", $_REQUEST["retanqueo2_valor"])."', retanqueo3_libranza = '".$_REQUEST["retanqueo3_libranza"]."', retanqueo3_cuota = '".str_replace(",", "", $_REQUEST["retanqueo3_cuota"])."', retanqueo3_valor = '".str_replace(",", "", $_REQUEST["retanqueo3_valor"])."', retanqueo_total_cuota = '".str_replace(",", "", $_REQUEST["retanqueo_total_cuota"])."', retanqueo_total = '".str_replace(",", "", $_REQUEST["retanqueo_total"])."', opcion_credito = '".$_REQUEST["opcion_credito"]."', opcion_cuota_cli = '".str_replace(",", "", $_REQUEST["opcion_cuota_cli"])."', opcion_desembolso_cli = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cli"])."', opcion_cuota_ccc = '".str_replace(",", "", $_REQUEST["opcion_cuota_ccc"])."', opcion_desembolso_ccc = '".str_replace(",", "", $_REQUEST["opcion_desembolso_ccc"])."', opcion_cuota_cmp = '".str_replace(",", "", $_REQUEST["opcion_cuota_cmp"])."', opcion_desembolso_cmp = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cmp"])."', opcion_cuota_cso = '".str_replace(",", "", $_REQUEST["opcion_cuota_cso"])."', opcion_desembolso_cso = '".str_replace(",", "", $_REQUEST["opcion_desembolso_cso"])."', desembolso_cliente = '".str_replace(",", "", $_REQUEST["desembolso_cliente"])."', decision = '".$_REQUEST["decision"]."', decision_sistema = '".$_REQUEST["decision_sistema"]."', valor_visado = '".str_replace(",", "", $_REQUEST["valor_visado"])."', bloqueo_cuota = '".$_REQUEST["bloqueo_cuota"]."', bloqueo_cuota_valor = '".str_replace(",", "", $_REQUEST["bloqueo_cuota_valor"])."', fecha_llamada_cliente = ".$fecha_llamada_cliente.", nro_cuenta = ".$nro_cuenta.", tipo_cuenta = ".$tipo_cuenta.", id_banco = ".$id_banco.", id_subestado = ".$id_subestado.", id_causal = ".$id_causal.", id_caracteristica = ".$id_caracteristica.", calificacion = ".$calificacion.", dia_confirmacion = ".$dia_confirmacion.", dia_vencimiento = ".$dia_vencimiento.", status = ".$status.", valor_credito = '".str_replace(",", "", $_REQUEST["valor_credito"])."', resumen_ingreso = '".str_replace(",", "", $_REQUEST["resumen_ingreso"])."', incor = '".str_replace(",", "", $_REQUEST["incor"])."', comision = '".str_replace(",", "", $_REQUEST["comision"])."', utilidad_neta = '".str_replace(",", "", $_REQUEST["utilidad_neta"])."', sobre_el_credito = '".$_REQUEST["sobre_el_credito"]."', estado = '".$estado."', tipo_producto = '".$_REQUEST["tipo_producto"]."', descuento1 = '".$_REQUEST["descuento1"]."', descuento3 = '".$_REQUEST["descuento3"]."', descuento4 = '".$_REQUEST["descuento4"]."', descuento5 = '".$_REQUEST["descuento5"]."', descuento6 = '".$_REQUEST["descuento6"]."', 

		descuento1_valor = '".str_replace(",", "", $_REQUEST["descuento1_valor"])."', 
		descuento2_valor = '".str_replace(",", "", $_REQUEST["descuento2_valor"])."', 
		descuento3_valor = '".str_replace(",", "", $_REQUEST["descuento3_valor"])."', 
		descuento4_valor = '".str_replace(",", "", $_REQUEST["descuento4_valor"])."', 
		descuento5_valor = '".str_replace(",", "", $_REQUEST["descuento5_valor"])."', 
		descuento6_valor = '".str_replace(",", "", $_REQUEST["descuento6_valor"])."', 
		descuento8_valor = '".str_replace(",", "", $_REQUEST["descuento8_valor"])."', 
		descuento9_valor = '".str_replace(",", "", $_REQUEST["descuento9_valor"])."', 
		descuento10_valor = '".str_replace(",", "", $_REQUEST["descuento10_valor"])."', 
		sin_iva_servicio_nube = '".$_REQUEST["sin_iva_servicio_nube"]."',
		aumento_salario_minimo = '".$_REQUEST["aumento_salario_minimo"]."',
			
		descuento_transferencia = '".$_REQUEST["descuento_transferencia"]."', porcentaje_seguro = '".$_REQUEST["porcentaje_seguro"]."', valor_por_millon_seguro = '".$_REQUEST["valor_por_millon_seguro"]."', porcentaje_extraprima = '".$_REQUEST["porcentaje_extraprimah"]."', formulario_seguro = '".$_REQUEST["formulario_seguro"]."', sin_aportes = '".$_REQUEST["sin_aportes"]."', sin_seguro = '".$_REQUEST["sin_seguro"]."', iva = '".$_REQUEST["iva"]."', usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = getdate() where id_simulacion = '".$_REQUEST["id_simulacion"]."'";
		
		
		
		$ejecutar = sqlsrv_query($link, $query);

		if ($valAnalistasKreditPlus==0){
			$actualizarAserFinanciera="UPDATE simulaciones SET descuento2 = '".$_REQUEST["descuento2"]."' WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'";
			$queryActualizarAserFinanciera=sqlsrv_query($link, $actualizarAserFinanciera);
		}
				
		

		for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++)
		{
			if ($id_entidad[$i] != "NULL" OR $_REQUEST["entidad".$i] OR $_REQUEST["cuota".$i] != "0" OR $_REQUEST["valor_pagar".$i] != "0" OR $_REQUEST["fecha_vencimiento".$i] OR $_REQUEST["nombre_grabado".$i]){
				
				$comprascartera_tmp = sqlsrv_query($link, "select * from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
				
				if (sqlsrv_num_rows($comprascartera_tmp)){
					sqlsrv_query($link, "update simulaciones_comprascartera set id_entidad = ".$id_entidad[$i].", 
						entidad = '".utf8_encode($_REQUEST["entidad".$i])."', 
						cuota = '".str_replace(",", "", $_REQUEST["cuota".$i])."', 
						valor_pagar = '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', 
						se_compra = '".$_REQUEST["se_compra".$i]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
					// exit("update simulaciones_comprascartera set id_entidad = ".$id_entidad[$i].", 
					// 	entidad = '".utf8_encode($_REQUEST["entidad".$i])."', 
					// 	cuota = '".str_replace(",", "", $_REQUEST["cuota".$i])."', 
					// 	valor_pagar = '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', 
					// 	se_compra = '".$_REQUEST["se_compra".$i]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
				}
				else{
					sqlsrv_query($link, "insert into simulaciones_comprascartera (id_simulacion, consecutivo, id_entidad, entidad, cuota, valor_pagar, se_compra, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".$i."', ".$id_entidad[$i].", '".utf8_encode($_REQUEST["entidad".$i])."', '".str_replace(",", "", $_REQUEST["cuota".$i])."', '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', '".$_REQUEST["se_compra".$i]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
				}
			}
			else
			{
				sqlsrv_query($link, "delete from simulaciones_comprascartera where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$i."'");
			}

			if($id_subestado == 14 || $id_subestado == 78){
				//Si Se encuentra En Compras de cartera.

				$queryCarteraSaldada = sqlsrv_query($link, "SELECT IF(a.valor_cartera = b.valor_giros, 'SI', 'NO') AS pagada, valor_cartera, valor_giros, cant_giros FROM 
				(SELECT iIF(SUM(a.valor_pagar) IS NULL, 0, SUM(a.valor_pagar)) AS valor_cartera FROM simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0) a,
				(SELECT iIF(SUM(s.valor_girar) IS NULL, 0, SUM(s.valor_girar)) AS valor_giros, COUNT(s.id_giro) AS cant_giros FROM giros s WHERE s.id_simulacion = ".$_REQUEST["id_simulacion"]." AND s.clasificacion = 'CCA') b");

				$carteraSaldada = sqlsrv_fetch_array($queryCarteraSaldada);

				if($carteraSaldada['pagada'] == 'SI'){//Esta saldada la cartera
					if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 78 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
						sqlsrv_query($link, "INSERT into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 78, 'system3', GETDATE())");
					}

					//Checkear compras pagadas
					$queryCompra = sqlsrv_query($link, "SELECT a.consecutivo FROM  simulaciones_comprascartera a WHERE a.id_simulacion = ".$_REQUEST["id_simulacion"]." AND se_compra = 'SI' AND a.valor_pagar > 0;");

					if(sqlsrv_num_rows($queryCompra)){
						while ($updPagar = sqlsrv_fetch_array($queryCompra)) {
							sqlsrv_query($link, "update tesoreria_cc SET pagada = 1 WHERE id_simulacion = ".$_REQUEST["id_simulacion"]." AND consecutivo = ".$updPagar["consecutivo"]);
						}
					}

					//Tasa Comisones
					$sqlDatosComi="SELECT id_unidad_negocio, sin_seguro, id_subestado, tasa_interes FROM simulaciones WHERE id_simulacion = ".$_REQUEST["id_simulacion"];
					$queryDatosComi=sqlsrv_query($link, $sqlDatosComi);	
					$respDatosComi = sqlsrv_fetch_array($queryDatosComi);

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

					$sqlTasaComision="SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, c.nombre AS unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a LEFT JOIN unidades_negocio c ON c.id_unidad = a.id_unidad_negocio WHERE a.id_unidad_negocio = ".$id_unidad_negocio_tasa_comision ." AND a.tasa = ".$respDatosComi["tasa_interes"]." AND ((FORMAT(GETDATE(), 'Y-m-d') >= a.fecha_inicio AND FORMAT(GETDATE(), 'Y-m-d') <= a.fecha_fin) OR a.vigente = 1)";

					$queryTasaComision=sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));	

					if (@sqlsrv_num_rows($queryTasaComision)>0){
						$respTasaComision = sqlsrv_fetch_array($queryTasaComision);
						$id_tasa_comision = $respTasaComision["id_tasa_comision"];
						$id_tipo_comision = $respTasaComision["id_tipo"];

						//consultarTasaComisionAnterior
						$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
						$querySimTasaCom=sqlsrv_query($link, $sqlSimTasaCom);
						$respSimTasaCom = sqlsrv_fetch_array($querySimTasaCom);
						$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

						if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){

							sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = $id_tasa_comision, id_tipo_tasa_comision = $id_tipo_comision WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");

							sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", $id_tasa_comision, ".$_SESSION['S_IDUSUARIO'].", GETDATE())");
						}
					}else{
						//consultarTasaComisionAnterior
						$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
						$querySimTasaCom=sqlsrv_query($link, $sqlSimTasaCom);
						$respSimTasaCom = sqlsrv_fetch_array($querySimTasaCom);
						$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

						if($id_tasa_comision_anterior != $respTasaComision["id_tasa_comision"]){
							sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = 0 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'");
							sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", 0, ".$_SESSION['S_IDUSUARIO'].", GETDATE())");
						}
					}
				}else if(intval($carteraSaldada['cant_giros']) > 0){

					$conSubestado6 = sqlsrv_query($link, "SELECT id_subestado FROM simulaciones WHERE id_subestado = 14 AND id_simulacion = ".$_REQUEST["id_simulacion"], array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					if(sqlsrv_num_rows($conSubestado6) == 0){
						if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 14 WHERE id_simulacion  = '".$_REQUEST["id_simulacion"]."'")){
							sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', 14, 'system4', GETDATE())");
						}
					}
				}
			}
		}
		
		if (!$_REQUEST["fecha_prospeccion"])
			sqlsrv_query($link, "update simulaciones set usuario_prospeccion = '".$_SESSION["S_LOGIN"]."', fecha_prospeccion = GETDATE() where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		
		if ($_REQUEST["observaciones"])
			sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".($_REQUEST["observaciones"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
		
		if ($_REQUEST["valor_credito_anterior"]){
			sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '".utf8_encode("EL CREDITO FUE RELIQUIDADO POR CAMBIO EN EL VALOR POR MILLON DEL SEGURO".chr(13).chr(13)."VALORES ANTES DE LA RELIQUIDACION:".chr(13)."VALOR CREDITO: $".$_REQUEST["valor_credito_anterior"].chr(13)."DESEMBOLSO CLIENTE: $".$_REQUEST["desembolso_cliente_anterior"].chr(13).chr(13)."VALORES DESPUES DE LA RELIQUIDACION:".chr(13)."VALOR CREDITO: $".$_REQUEST["valor_credito"].chr(13)."DESEMBOLSO CLIENTE: $".$_REQUEST["desembolso_cliente"])."', 'system', GETDATE())");
		}
		
		
		


		$existe_en_solicitud = sqlsrv_query($link, "select id_simulacion from solicitud where id_simulacion = '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
		
		if (sqlsrv_num_rows($existe_en_solicitud)){
			sqlsrv_query($link, "update solicitud set clave = '".utf8_encode($_REQUEST["clave"])."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
		}
		else{
			sqlsrv_query($link, "insert into solicitud (id_simulacion, cedula, fecha_nacimiento, tel_residencia, celular, direccion, ciudad, email, clave) values ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["cedula"]."', ".$fecha_nacimiento.", '".utf8_encode($_REQUEST["telefono"])."', '".utf8_encode($_REQUEST["celular"])."', '".utf8_encode($_REQUEST["direccion"])."', '".utf8_encode($_REQUEST["ciudad_residencia"])."', '".utf8_encode($_REQUEST["mail"])."', '".utf8_encode($_REQUEST["clave"])."')");
		}
		
		$descuentos_adicionales = sqlsrv_query($link, "select * from simulaciones_descuentos where id_simulacion = '".$_REQUEST["id_simulacion"]."' order by id_descuento");
		
		while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales))
		{
			sqlsrv_query($link, "update simulaciones_descuentos set porcentaje = '".$_REQUEST["descuentoadicional".$fila1["id_descuento"]]."' where id_simulaciondescuento = '".$fila1["id_simulaciondescuento"]."'");
		}
		
		$id_simul = $_REQUEST["id_simulacion"];
		$estadosModuloVisado=array(4); //Mover a functions o parametros de SEAS.    

		if (in_array($_REQUEST["id_subestado"], $estadosModuloVisado)) {
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url_layer_security.'Login',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'{"usuario":"'.$usuario_seas_modulo_incorporaciones.'","clave":"'.$passwd_seas_modulo_incorporaciones.'"}',
				CURLOPT_HTTPHEADER => array( 'Content-Type: application/json' )
			));
	
			$responseLogin = curl_exec($curl);
			curl_close($curl);
			$respuestaArray = json_decode($responseLogin,true);
			
			if ($respuestaArray["codigo"]=="200") {
	
	
				$data = array();
				$consultarInformacionCredito = "SELECT c.identificacion as identificacion_pagaduria, b.nombre1, b.nombre2, b.apellido1, b.apellido2, a.id_simulacion, a.nro_libranza, a.cedula, a.plazo from simulaciones a LEFT JOIN solicitud b ON a.id_simulacion = b.id_simulacion LEFT JOIN pagadurias c ON a.pagaduria = c.nombre where a.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

				$queryInformacionCredito = sqlsrv_query($link, $consultarInformacionCredito);
				$resInformacionCredito = sqlsrv_fetch_array($queryInformacionCredito);				
				$cuota = 0;

				switch($_REQUEST["opcion_credito"]) {
					case "CLI":	$cuota = $_REQUEST["opcion_cuota_cli"]; break;
					case "CCC":	$cuota = $_REQUEST["opcion_cuota_ccc"]; break;
					case "CMP":	$cuota = $_REQUEST["opcion_cuota_cmp"]; break;
					case "CSO":	$cuota = $_REQUEST["opcion_cuota_cso"]; break;
				}				
	
				$consultarComprasCarteraCredito = "SELECT concat(c.nombre,'-',a.entidad) as nombre_entidad,a.*,b.fecha_vencimiento FROM simulaciones_comprascartera a LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion AND a.consecutivo=b.consecutivo LEFT JOIN entidades_desembolso c ON c.id_entidad=a.id_entidad WHERE a.id_simulacion='".$_REQUEST["id_simulacion"]."' AND a.se_compra='SI' AND a.cuota>0";

				$queryComprasCarteraCredito = sqlsrv_query($link, $consultarComprasCarteraCredito);
					
					
				if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
					$cadena_compras="";
					while ($resSimulacionesDatos=sqlsrv_fetch_array($queryComprasCarteraCredito)) {					
						if ($resSimulacionesDatos["fecha_vencimiento"]==null || $resSimulacionesDatos["fecha_vencimiento"]=="") {
							$cadena_compras.='{
								"compra_Entidad_Nombre":"'.$resSimulacionesDatos["nombre_entidad"].'",
								"compra_Valor_Pagar":'.$resSimulacionesDatos["valor_pagar"].',
								"compra_Valor_Cuota":'.$resSimulacionesDatos["cuota"].'
							},';
						}else{
							$cadena_compras.='{
								"compra_Entidad_Nombre":"'.$resSimulacionesDatos["nombre_entidad"].'",
								"compra_Valor_Pagar":'.$resSimulacionesDatos["valor_pagar"].',
								"compra_Valor_Cuota":'.$resSimulacionesDatos["cuota"].',
								"compra_Fecha_Vencimiento":"'.$resSimulacionesDatos["fecha_vencimiento"].'"
							},';
						}					
					}
				}
	
				$cadena_Credito.='{"credito_Id":'.$_REQUEST["id_simulacion"].',
					"cliente_Nombres":"'.($resInformacionCredito["nombre1"]." ".$resInformacionCredito["nombre2"]).'",
					"cliente_Apellidos":"'.($resInformacionCredito["apellido1"]." ".$resInformacionCredito["apellido2"]).'",
					"credito_Libranza":"'.$resInformacionCredito["nro_libranza"].'",
					"cliente_Identificacion":"'.$resInformacionCredito["cedula"].'",
					"credito_Pagaduria":"'.$resInformacionCredito["identificacion_pagaduria"].'",
					"credito_Valor":'.str_replace(",", "", $_REQUEST["valor_credito"]).',
					"credito_Valor_Cuota":'.str_replace(",", "", $cuota).',
					"credito_Plazo":'.$resInformacionCredito["plazo"].',
					"credito_Valor_Menos_Retanqueo":'.str_replace(",", "", $_REQUEST["sin_retanqueos"]).',
					"credito_Compras_Cartera":['.substr($cadena_compras,0,-1).']}';
				
				$curl = curl_init();
				
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url_app_visados.'Creditos/Crear_Creditos',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>$cadena_Credito,
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer '.$respuestaArray["data"]["usuario_Token"],
						'Content-Type: application/json'
					)
				));
	
				
	
				$response = curl_exec($curl);
				curl_close($curl);
				
				$responseCrearCredito=json_decode($response,true);
				
				if ($responseCrearCredito["codigo"]==200){
					$actualizarEstadoVisado="UPDATE simulaciones SET visado='1',fecha_visado=current_timestamp WHERE id_simulacion='".$id_simul."'";
					sqlsrv_query($link,$actualizarEstadoVisado);
	
					$crearRegistroEstadosVisados="INSERT INTO visados_simulaciones (id_simulacion,estado,observaciones,fecha) VALUES ('".$id_simul."',1,'CREDITO EN PROCESO DE VISADO',current_timestamp)";
					sqlsrv_query($link,$crearRegistroEstadosVisados);
				}
			}
		}
	}
	
	
	 if (!$mensaje && $procesar_simulacion){
		if ($_REQUEST["id_plan_seguroh"] != $_REQUEST["id_plan_seguro"]){
			sqlsrv_query($link, "insert into simulaciones_seguro (id_simulacion, id_plan_seguro, valor_seguro, id_perfil, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', ".$id_plan_seguro.", '".str_replace(",", "", $_REQUEST["valor_seguro"])."', '".$_SESSION["S_IDPERFIL"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
		}

		if ($_REQUEST["sin_seguroh2"] != $_REQUEST["sin_seguro"]){
			sqlsrv_query($link, "insert into simulaciones_sinseguro (id_simulacion, sin_seguro, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["sin_seguro"]."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
		}
		
		if ($_REQUEST["salario_minimoh"] != str_replace(",", "", $_REQUEST["salario_minimo"])){
			sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', 'CAMBIO DE SALARIO MINIMO NUEVO $".$_REQUEST["salario_minimo"]." ANTIGUO $ ".number_format($_REQUEST["salario_minimoh"], 0, ".", ",")."' , '".$_SESSION["S_LOGIN"]."', GETDATE())");
		}

		if ($nro_libranza == "NULL"){
			$actualiza_nro_libranza = 1;
		}
		else{
			$existe_en_simulaciones = sqlsrv_query($link, "select id_simulacion from simulaciones where nro_libranza = ".strtoupper($nro_libranza)." AND id_simulacion != '".$id_simul."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($existe_en_simulaciones)){
				$mensaje = "El No. de Libranza ingresado ya esta registrado por lo tanto este campo NO fue actualizado. ";
			}else{
				$actualiza_nro_libranza = 1;
			}
		}
		
		
	

		// ASIGNACION NUMERO DE LIBRANZA
		if (($_REQUEST["id_subestado"]=="56" || $_REQUEST["id_subestado"]=="70" || $_REQUEST["id_subestado"]=="3") && $nro_libranza == "NULL" && $cambio_estado==1){
				
			$queryUndNegocio = sqlsrv_query($link, "SELECT prefijo_libranza from unidades_negocio WHERE id_unidad = '".$_REQUEST["id_unidad_negocio"]."'");

			if($queryUndNegocio && sqlsrv_num_rows($queryUndNegocio) > 0){
				$dataUniNegocio = sqlsrv_fetch_array($queryUndNegocio);
				$prefijo_libranza = $dataUniNegocio["prefijo_libranza"];

				$insertLibranza = sqlsrv_query($link, "INSERT INTO libranza_simulaciones (id_unidad_negocio, id_simulacion, id_usuario_creacion, fecha_creacion, fuente) VALUES('".$_REQUEST["id_unidad_negocio"]."', '".$id_simul."', '".$_SESSION["S_IDUSUARIO"]."', getdate(), 1)");
				
				if($insertLibranza){

					// $libranza = sqlsrv_insert_id($link);
					$libranza3 = sqlsrv_query($link, "SELECT scope_identity() as id");
					$libranza2 = sqlsrv_fetch_array($libranza3);
					$libranza = $libranza2['id'];
					$numero_libranza = strtoupper($prefijo_libranza) . " " . $libranza;

					if(sqlsrv_query($link, "UPDATE simulaciones SET usuario_libranza = '".$_SESSION["S_IDUSUARIO"]."', fecha_libranza = getdate(), libranza = '".$libranza."', nro_libranza = '".$numero_libranza."' WHERE id_simulacion = '".$id_simul."'")){
						
						$enviar_correo = true;
						sqlsrv_query($link, "UPDATE libranza_simulaciones SET nro_libranza = '".$numero_libranza."' WHERE id_libranza = '".$libranza."'");                                
					}
				}

			}			
		}
	
	

		
		
		
		$consultarOficinaFormatoDigital="SELECT * FROM oficinas WHERE id_oficina=(SELECT id_oficina FROM simulaciones WHERE id_simulacion='".$id_simul."') AND escala='1'";
			
	        $queryOficinaFormatoDigital=sqlsrv_query($link, $consultarOficinaFormatoDigital, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			

	        if (sqlsrv_num_rows($queryOficinaFormatoDigital)>0){
				

	            if ((in_array($_REQUEST["id_subestado"], $subestados_preaprobado) || in_array($_REQUEST["id_subestado"], $subestados_formulario_digital)) && $cambio_estado == 1){
					

	                $token = openssl_random_pseudo_bytes(64);
	                //Convertir el binario a data hexadecimal.
	                $token = bin2hex($token);
	                $consultarFormatoDigital="SELECT id FROM formulario_digital WHERE id_simulacion = '".$id_simul."'";
	                $queryFormatoDigital=sqlsrv_query($link, $consultarFormatoDigital, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					

	                if (sqlsrv_num_rows($queryFormatoDigital)==0){
	                    $crearTokenFormatoDigital="INSERT INTO formulario_digital (id_simulacion,estado_token,token,vigente, en_progreso) values (".$id_simul.",0,'".$token."','s', 0)";

	                }
					
	            }

	            if (in_array($_REQUEST["id_subestado"], $subestados_formulario_digital) && $cambio_estado==1) {
	                $consultarFormatoDigital="SELECT formato_digital FROM simulaciones WHERE id_simulacion = '".$id_simul."' AND formato_digital = 1";
	                $queryFormatoDigital=sqlsrv_query($link, $consultarFormatoDigital, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	                if (sqlsrv_num_rows($queryFormatoDigital)==0){
	                    if(isset($crearTokenFormatoDigital)){
		                    if (sqlsrv_query($link, $crearTokenFormatoDigital)){
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
	                }else{
	                	$firmado_digital = 1;
						$cambio_estado_32 = true;                    
	                }
				}
	        }
			
			
			if (in_array($_REQUEST["id_subestado"], $subestados_tesoreria_ccvecimientos)) {
				$queryBD_CC = "SELECT a.entidad,a.cuota,a.valor_pagar,a.id_adjunto
								FROM simulaciones_comprascartera a
								LEFT JOIN agenda b ON a.id_simulacion=b.id_simulacion
								WHERE a.se_compra='SI' AND b.fecha_vencimiento=GETDATE() AND a.id_simulacion=".$id_simul." AND a.consecutivo=b.consecutivo";
				$conCC = sqlsrv_query($link, $queryBD_CC);
				
				if (sqlsrv_num_rows($conCC)>0){
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
			 
			
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && $_REQUEST["id_subestado"]){	
						
				
				
				if(in_array($_REQUEST["id_subestado"], $subestados_estudio)){
					
					$consultarSimulacionesFdc="SELECT * FROM simulaciones_fdc WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' and estado<>100";
					$querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					if (sqlsrv_num_rows($querySimulacionesFdc)>0){
						$consultarUltimoAnalistaEstudio=sqlsrv_query($link, "SELECT top 1 case when id_usuario_asignacion is null then 0 when id_usuario_asignacion = 197 then 0 else id_usuario_asignacion end as id_usuario_asignacion FROM simulaciones_fdc WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."' and estado=2 order by id desc ");
						$resUltimoAnalistaEstudio=sqlsrv_fetch_array($consultarUltimoAnalistaEstudio);
						if ($_REQUEST["id_analista_riesgo_operativo"]!=$resAnalistaActual["id_analista_riesgo_operativo"]){	
							$actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");							
						}

						$id_simulacion_fdc = $_REQUEST["id_simulacion"];

						sqlsrv_query($link, "START TRANSACTION");
						$consultarJornadaLaboral=sqlsrv_query($link, "SELECT * FROM definicion_tipos where id_tipo=5 and id=1");
	  
						$resJornadaLaboral=sqlsrv_fetch_array($consultarJornadaLaboral);
						$actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
						$crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES ($id_simulacion_fdc,0,197,current_timestamp,'s',5,".$_REQUEST["id_subestado"].",2)");
						

						if ($resJornadaLaboral["descripcion"]=="s"){
							
							$consultarEstadoUsuarioNuevo=sqlsrv_query($link, "SELECT * FROM usuarios WHERE id_usuario='".$resUltimoAnalistaEstudio["id_usuario_asignacion"]."' and disponible <> ('n')", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
							if (sqlsrv_num_rows($consultarEstadoUsuarioNuevo)>0 && $resUltimoAnalistaEstudio["id_usuario_asignacion"]<>0){
								$resEstadoUsuarioNuevo=sqlsrv_fetch_array($consultarEstadoUsuarioNuevo);

								$consultarLimiteCreditosUsuario=sqlsrv_query($link, "SELECT a.id_usuario,a.cantidad_asignado,b.cantidad_terminado,(a.cantidad_asignado+b.cantidad_terminado) AS cantidad_creditos,a.num_creditos
								FROM
								(SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_asignado,a.cantidad_creditos AS num_creditos
								FROM usuarios a 
								LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=2 AND vigente='s') b ON a.id_usuario=b.id_usuario_asignacion
								WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."'   group by a.id_usuario,a.nombre,a.apellido,a.cantidad_creditos ) a,
								(SELECT a.id_usuario,a.nombre,a.apellido,COUNT(b.id) AS cantidad_terminado,a.cantidad_creditos AS num_creditos
								FROM usuarios a 
								LEFT JOIN  (SELECT * FROM simulaciones_fdc WHERE estado=4 AND id_subestado not in (28,53) AND FORMAT(fecha_creacion,'Y-m-d') = FORMAT(GETDATE(),'Y-m-d')) b ON a.id_usuario=b.id_usuario_creacion
								 WHERE a.id_usuario='".$resEstadoUsuarioNuevo["id_usuario"]."'   group by a.id_usuario,a.nombre,a.apellido,a.cantidad_creditos  ) b WHERE a.id_usuario=b.id_usuario AND (a.cantidad_asignado+b.cantidad_terminado)<a.num_creditos", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
								
								if(sqlsrv_num_rows($consultarLimiteCreditosUsuario)>0){
									$actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");

									$id_simulacion_fdc = $_REQUEST["id_simulacion"];
									$crearEstadoTerminado2=sqlsrv_query($link, "INSERT INTO simulaciones_fdc 
									(id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado, val) VALUES 
									(".$id_simulacion_fdc.",".$resEstadoUsuarioNuevo["id_usuario"].",19700,current_timestamp,'s',2,3)");
									$actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$resEstadoUsuarioNuevo["id_usuario"]."',id_analista_riesgo_crediticio='".$resEstadoUsuarioNuevo["id_usuario"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
									
								}else{
									$actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");		
								}
							}else{
								
								$opciones = array(
									'http'=>array(
										'method' => 'POST',
										'header'  => 'Content-Type: application/json',
										'content' => json_encode(array("id_simulacion"=>$_REQUEST["id_simulacion"],"operacion"=>"Determinar Usuario Asignar"))
												
									)
								);
									
								$contexto = stream_context_create($opciones);
								
								$json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/determinarUsuarioAsignar.php', false, $contexto);
								$datosUsuarioAsignar=json_decode($json_Input,true);
								$idUsuarioAsignar = $datosUsuarioAsignar["datos"];

								
								$id_simulacion_fdc = $_REQUEST["id_simulacion"];
								
								
							}
						}
						else{
							$actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo=null,id_analista_riesgo_crediticio=null WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");		
						}
						sqlsrv_query($link, "COMMIT");
					}else{
						$id_simulacion_fdc = $_REQUEST["id_simulacion"];
						$actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
						$crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,val) VALUES ($id_simulacion_fdc, 0, 197, current_timestamp, 's', 1 , 1)");
					}
				}else if(in_array($_REQUEST["id_subestado"], $subestados_validar_grantias)){//VALIDACION DE IDENTIDAD Y GARANTIAS
					
					$opciones = array(
						'http'=>array(
							'method' => 'POST',
							'header'  => 'Content-Type: application/json',
							'content' => json_encode(array("id_simulacion"=>$_REQUEST["id_simulacion"],"operacion"=>"Asignar Usuario Firma Garantias"))
									
						)
					);
						
					$contexto = stream_context_create($opciones);
					
					$json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/asignarUsuarioFirmaGarantias.php', false, $contexto);
					
				}else{	

					$consultarSimulacionesFdc="SELECT top 1 * FROM simulaciones_fdc WHERE id_simulacion='".$_REQUEST["id_simulacion"]."' ORDER BY id DESC ";
					$querySimulacionesFdc=sqlsrv_query($link, $consultarSimulacionesFdc, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($querySimulacionesFdc)>0){
						$ultimo_simulaciones_fdc = sqlsrv_fetch_array($querySimulacionesFdc);
						sqlsrv_query($link, "START TRANSACTION");

						$consultarUltEstadoFDC="SELECT * FROM simulaciones_fdc WHERE id_simulacion=".$_REQUEST["id_simulacion"]." and vigente='s'";
						$consultarUltEstadoTerminado=$consultarUltEstadoFDC." and estado=4";
						$queryUltEstadoFDC=sqlsrv_query($link, $consultarUltEstadoTerminado, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

						if (sqlsrv_num_rows($queryUltEstadoFDC)==0){

							if($_REQUEST['id_subestado'] == 85){//  pasar de 6.1 a 6.7 PNDTE. GIRO DESEMBOLSO
								$queryUltEstadoFDCEstudio=sqlsrv_query($link, $consultarUltEstadoFDC." AND id_subestado = 83");
								$resUltEstadoFDCEstudio=sqlsrv_fetch_array($queryUltEstadoFDCEstudio);
								
								$actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
								$crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES (".$_REQUEST["id_simulacion"].",0,".$resUltEstadoFDCEstudio["id_usuario_asignacion"].",current_timestamp,'s',4,".$_REQUEST["id_subestado"].",85)");
							}else{

								$consultarUltEstadoEstudio=$consultarUltEstadoFDC." and estado in(1,2,5) ";
								$queryUltEstadoFDCEstudio=sqlsrv_query($link, $consultarUltEstadoEstudio, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

								if (sqlsrv_num_rows($queryUltEstadoFDCEstudio)>0) {
									$resUltEstadoFDCEstudio=sqlsrv_fetch_array($queryUltEstadoFDCEstudio);
									$actualizarEstadosFDC=sqlsrv_query($link, "UPDATE simulaciones_fdc SET vigente='n' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
									$crearEstadoTerminado=sqlsrv_query($link, "INSERT INTO simulaciones_fdc (id_simulacion,id_usuario_asignacion,id_usuario_creacion,fecha_creacion,vigente,estado,id_subestado,val) VALUES (".$_REQUEST["id_simulacion"].",0,".$resUltEstadoFDCEstudio["id_usuario_asignacion"].",current_timestamp,'s',4,".$_REQUEST["id_subestado"].",5)");
								}
							}
						}

						sqlsrv_query($link, "COMMIT");
					}
				}
				


				sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["id_subestado"]."', '".$_SESSION["S_LOGIN"]."', getdate())");			
				
				if(isset($cambio_estado_32)){//cambio automaticamente de 3 a 3.2

					if(sqlsrv_query($link, "UPDATE simulaciones SET id_subestado = 72 WHERE id_simulacion  = '".$_REQUEST['id_simulacion']."'")){
						sqlsrv_query($link, "INSERT INTO simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) values ('".$_REQUEST['id_simulacion']."', 72, 'system', getdate())");//3.2 VALIDACION DE IDENTIDAD Y GARANTIAS

						$opciones = array(
							'http'=>array(
								'method' => 'POST',
								'header'  => 'Content-Type: application/json',
								'content' => json_encode(array("id_simulacion"=>$_REQUEST["id_simulacion"],"operacion"=>"Asignar Usuario Firma Garantias"))
										
							)
						);
							
						$contexto = stream_context_create($opciones);
						
						$json_Input = file_get_contents($urlPrincipal.'/servicios/FDC/asignarUsuarioFirmaGarantias.php', false, $contexto);
					}
				}
				
				$subestado_cambiado = 1;
			}else{
				
				if ($id_analista_riesgo_operativoh<>$_REQUEST["id_analista_riesgo_operativo"]){
					$actualizarAnalista=sqlsrv_query($link, "UPDATE simulaciones SET id_analista_riesgo_operativo='".$_REQUEST["id_analista_riesgo_operativo"]."',id_analista_riesgo_crediticio='".$_REQUEST["id_analista_riesgo_operativo"]."' WHERE id_simulacion='".$_REQUEST["id_simulacion"]."'");
				}			
			}
			

		

			
			if ($_REQUEST["id_subestado"] == $subestado_radicado || $_REQUEST["id_subestado"] == $subestado_soportes_completos){
				/*if (!$_REQUEST["id_analista_riesgo_operativo"] && !$_REQUEST["id_analista_riesgo_crediticio"]){
					$siguiente_analista = sqlsrv_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') AND id_usuario > (select id_usuario from asignaciones_analistas where tipo = 'ACR' AND sector = '".$_REQUEST["sector"]."') LIMIT 1");
					
					if (sqlsrv_num_rows($siguiente_analista)){
						$fila1 = sqlsrv_fetch_array($siguiente_analista);
						
						$id_siguiente_analista = $fila1["id_usuario"];
					}
					else{
						$siguiente_analista = sqlsrv_query("select id_usuario from usuarios where subtipo = 'ANALISTA_CREDITO' AND estado = '1' AND (sector IS NULL OR sector = '".$_REQUEST["sector"]."') order by id_usuario LIMIT 1");
						
						if (sqlsrv_num_rows($siguiente_analista)){
							$fila1 = sqlsrv_fetch_array($siguiente_analista);
							
							$id_siguiente_analista = $fila1["id_usuario"];
						}
					}
					
					if ($id_siguiente_analista){
						sqlsrv_query("update simulaciones set id_analista_riesgo_operativo = '".$id_siguiente_analista."', id_analista_riesgo_crediticio = '".$id_siguiente_analista."' where id_simulacion = '".$id_simul."'");
						
						sqlsrv_query("update asignaciones_analistas set id_usuario = '".$id_siguiente_analista."' where tipo = 'ACR' AND sector = '".$_REQUEST["sector"]."'");
					}
				}*/
				
				sqlsrv_query($link, "update solicitud set tasa_usura = '".$_REQUEST["tasa_usura"]."' where id_simulacion = '".$_REQUEST["id_simulacion"]."'");
			}
			
			if (str_replace(",", "", $_REQUEST["valor_visado"]) != "0" && $_REQUEST["id_subestado"] == $subestado_aprobado){
				
				switch($_REQUEST["opcion_credito"]){
					case "CLI":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cli"]);
								break;
					case "CCC":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_ccc"]);
								break;
					case "CMP":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cmp"]);
								break;
					case "CSO":	$opcion_cuota = str_replace(",", "", $_REQUEST["opcion_cuota_cso"]);
								break;
				}
				
				if (str_replace(",", "", $_REQUEST["valor_visado"]) < $opcion_cuota){
					$nuevo_subestado = $subestado_procesado;
				}
				else{
					$nuevo_subestado = $subestado_visado;
				}
				
				sqlsrv_query($link, "update simulaciones set id_subestado = '".$nuevo_subestado."' where id_simulacion = '".$id_simul."'");
				
				if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"]){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$nuevo_subestado."', 'system', getdate())");
				}
					
				$subestado_cambiado = 1;
			}
			
			if (str_replace(",", "", $_REQUEST["valor_visado"]) != "0" && $_REQUEST["id_subestado"] == $subestado_desembolso_pdte_bloqueo){

				sqlsrv_query($link, "update simulaciones set id_subestado = '".$subestado_desembolso_cliente."' where id_simulacion = '".$id_simul."'");
				
				if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"]){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$subestado_desembolso_cliente."', 'system', getdate())");
				}
				$subestado_cambiado = 1;
			}
			
			if (!$subestado_cambiado && $_REQUEST["id_subestado"]){
				
				if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"]){
					sqlsrv_query($link, "insert into simulaciones_subestados (id_simulacion, id_subestado, usuario_creacion, fecha_creacion) VALUES ('".$id_simul."', '".$_REQUEST["id_subestado"]."', '".$_SESSION["S_LOGIN"]."', getdate())");
				}
			}
			
			if ($_REQUEST["id_subestado"] == $subestado_aprobado){
				sqlsrv_query($link, "delete from giros where id_simulacion = '".$id_simul."' AND fecha_giro IS NULL");
				
				if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"]){
					sqlsrv_query($link,"update simulaciones set fecha_aprobado = GETDATE() where id_simulacion = '".$id_simul."'");
				}
			}
			
			//Solo se actualiza la fecha de aprobado si es NULL, es decir, si pasa directamente a otros subestados aprobados
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && ($_REQUEST["id_subestado"] == $subestado_aprobado_pdte_visado || $_REQUEST["id_subestado"] == $subestado_aprobado_pdte_incorp || $_REQUEST["id_subestado"] == $subestado_visado)){
				sqlsrv_query($link, "update simulaciones set fecha_aprobado = GETDATE() where id_simulacion = '".$id_simul."' AND fecha_aprobado IS NULL");
			}
			
			//Solo se actualiza el estado de tesoreria si es NULL, es decir, si no se ha establecido antes
			if ($nuevo_subestado == $subestado_confirmado || ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && $_REQUEST["id_subestado"] == $subestado_confirmado)){
				sqlsrv_query($link, "update simulaciones set estado_tesoreria = 'ABI', fecha_tesoreria = GETDATE() where id_simulacion = '".$id_simul."' AND estado_tesoreria IS NULL");
			}

			$varexplode=explode(",",$subestado_compras_desembolso);
			
			//Solo se actualiza el estado de tesoreria si es NULL, es decir, si pasa directamente a Desembolso
			if ($_REQUEST["id_subestado"] != $_REQUEST["id_subestadoh"] && ($_REQUEST["id_subestado"] == $subestado_tesoreria_con_pdtes || $_REQUEST["id_subestado"] == $varexplode[0] || $_REQUEST["id_subestado"] == $varexplode[1] || $_REQUEST["id_subestado"] == $subestado_desembolso || $_REQUEST["id_subestado"] == $subestado_desembolso_cliente || $_REQUEST["id_subestado"] == $subestado_desembolso_pdte_bloqueo)){
				sqlsrv_query($link, "update simulaciones set estado_tesoreria = 'ABI', fecha_tesoreria = GETDATE() where id_simulacion = '".$id_simul."' AND estado_tesoreria IS NULL");
			}
			
			for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++){
				
				if ($_REQUEST["id_entidad".$i]){
					$entidad_desembolso = sqlsrv_query($link, "select nombre as nombre_entidad from entidades_desembolso where id_entidad = '".$_REQUEST["id_entidad".$i]."'");
					
					$fila1 = sqlsrv_fetch_array($entidad_desembolso);
					
					$nombre_entidad = $fila1["nombre_entidad"];
				}
				else{
					$nombre_entidad = "";
				}
				
				if ($_REQUEST["se_compra".$i] == "SI" && ($_REQUEST["id_entidad".$i] || $_REQUEST["entidad".$i])){

					if ($_REQUEST["fecha_solicitudcarta".$i]){
						$fecha_solicitudcarta = "'".$_REQUEST["fecha_solicitudcarta".$i]."'";
					}
					else{
						$fecha_solicitudcarta = "NULL";
					}
					
					if ($_REQUEST["fecha_entrega".$i]){
						$fecha_entrega = "'".$_REQUEST["fecha_entrega".$i]."'";
					}
					else{
						$fecha_entrega = "NULL";
					}
					
					if ($_REQUEST["fecha_vencimiento".$i]){
						$fecha_vencimiento = "'".$_REQUEST["fecha_vencimiento".$i]."'";
					}
					else{
						$fecha_vencimiento = "NULL";
					}
					
					$agenda_tmp = sqlsrv_query($link, "select * from agenda where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($agenda_tmp)){
						sqlsrv_query($link, "update agenda set entidad = '".utf8_encode($nombre_entidad." ".$_REQUEST["entidad".$i])."', fecha_vencimiento = ".$fecha_vencimiento." where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
					}
					else{
						sqlsrv_query($link, "insert into agenda (id_simulacion, consecutivo, entidad, dias_entrega, dias_vigencia, estado, fecha_sugerida, fecha_solicitud, fecha_entrega, fecha_vencimiento) values ('".$id_simul."', '".$i."', '".utf8_encode($nombre_entidad." ".$_REQUEST["entidad".$i])."', '".$_REQUEST["dias_entregah".$i]."', '".$_REQUEST["dias_vigenciah".$i]."', 'NO SOLICITADA', GETDATE(), ".$fecha_solicitudcarta.", ".$fecha_entrega.", ".$fecha_vencimiento.")");
					}
					
					sqlsrv_query($link, "insert into tesoreria_cc (id_simulacion, consecutivo, pagada, cuota_retenida, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$i."', '0', '0', '".$_SESSION["S_LOGIN"]."', getdate())");
				}
				else{
					sqlsrv_query($link, "delete from agenda where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
					sqlsrv_query($link, "delete from tesoreria_cc where id_simulacion = '".$id_simul."' AND consecutivo = '".$i."'");
				}
			}
			
			for ($i = 1; $i <= 3; $i++){
				
				if ($_REQUEST["retanqueo".$i."_libranzah"] && ($_REQUEST["retanqueo".$i."_libranza"] != $_REQUEST["retanqueo".$i."_libranzah"])){
					$queryDB = "select id_simulacion from simulaciones where id_simulacion = '".$id_simul."' AND (retanqueo1_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."' OR retanqueo2_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."' OR retanqueo3_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."')";

					$esta_en_otra_posicion_retanqueo = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					if (!sqlsrv_num_rows($esta_en_otra_posicion_retanqueo)){

						$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo".$i."_libranzah"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						
						if (sqlsrv_num_rows($rs1)){
							
							$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
							sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
						}
					}
				}
				
				if ($_REQUEST["retanqueo".$i."_libranza"]){

					$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo".$i."_libranza"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
					
					if (sqlsrv_num_rows($rs1)){
						
						$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);					
						sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_valor_liquidacion"])."', retanqueo_intereses = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_intereses"])."', retanqueo_seguro = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_seguro"])."', retanqueo_cuotasmora = '".$_REQUEST["retanqueo".$i."_cuotasmora"]."', retanqueo_segurocausado = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_segurocausado"])."', retanqueo_gastoscobranza = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_gastoscobranza"])."', retanqueo_totalpagar = '".str_replace(",", "", $_REQUEST["retanqueo".$i."_totalpagar"])."' where id_simulacion = '".$fila1["id_simulacion"]."'");
					}
				}
			}
			
			if ($_REQUEST["decision"] == $label_negado){
				//estado negado
				sqlsrv_query($link, "update simulaciones set decision='NEGADO',estado = 'NEG' where id_simulacion = '".$id_simul."'");

				$observacion_negado="El credito actual ha sido guardado con estado NEGADO. Decision: ".$_REQUEST["decision"];
				if ($id_causal<>"NULL"){
					$queryCausal = sqlsrv_query($link, "select id_causal, nombre from causales where (estado = '1' AND tipo_causal = 'NEGACION') AND id_causal = '".$id_causal."'");
					$resCausal=sqlsrv_fetch_array($queryCausal);
					$observacion_negado.="Causal: ".$resCausal["nombre"];
				}

				sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$id_simul."', '".$observacion_negado."', '".$_SESSION["S_LOGIN"]."', getdate())");
				
				$rs2 = sqlsrv_query($link, "select cedula, pagaduria, retanqueo1_libranza, retanqueo2_libranza, retanqueo3_libranza from simulaciones where id_simulacion = '".$id_simul."'");
				
				$fila2 = sqlsrv_fetch_array($rs2, SQLSRV_FETCH_ASSOC);
				
				for ($i = 1; $i <= 3; $i++){
					if ($fila2["retanqueo".$i."_libranza"]){
						
						$rs1 = sqlsrv_query($link, "select id_simulacion from simulaciones where cedula = '".$fila2["cedula"]."' AND pagaduria = '".$fila2["pagaduria"]."' AND nro_libranza = '".$fila2["retanqueo".$i."_libranza"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
						
						if (sqlsrv_num_rows($rs1)){
							
							$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);	
							sqlsrv_query($link, "update simulaciones set retanqueo_valor_liquidacion = null, retanqueo_intereses = null, retanqueo_seguro = null, retanqueo_cuotasmora = null, retanqueo_segurocausado = null, retanqueo_gastoscobranza = null, retanqueo_totalpagar = null where id_simulacion = '".$fila1["id_simulacion"]."'");
						}
					}
				}
			}
			
			$cod_interno_subestado = sqlsrv_query($link, "select cod_interno from subestados where id_subestado = '".$_REQUEST["id_subestado"]."'");

			$fila3 = sqlsrv_fetch_array($cod_interno_subestado);

			if ($fila3["cod_interno"] < 999 && $fila3["cod_interno"] >= $cod_interno_subestado_caracterizacion){
				$registrar_caracterizacion = 0;

				$ultima_caracterizacion = sqlsrv_query($link, "select top 1 id_transaccion, cod_transaccion from contabilidad_transacciones where id_simulacion = '".$id_simul."' AND id_origen = '1' order by id_transaccion DESC");

				$fila2 = sqlsrv_fetch_array($ultima_caracterizacion);

				if (!$fila2["cod_transaccion"]){
					$registrar_caracterizacion = 1;
				}
				else{
					$hay_para_reversar = sqlsrv_query($link, "SELECT TOP 1 id_transaccion_movimiento from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' ", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					if (!(sqlsrv_num_rows($hay_para_reversar))){
						$registrar_caracterizacion = 1;
					}
					else{
						$ultimo_log = sqlsrv_query($link, "select top 1 cambios from simulaciones_logs where id_simulacion = '".$id_simul."' order by id_log DESC ");

						$fila4 = sqlsrv_fetch_array($ultimo_log);

						$datos_anteriores = json_decode($fila4["cambios"], true);

						$cambio = array();

						if ($_REQUEST["valor_credito"] != $datos_anteriores["valor_credito"])
							$cambio["valor_credito"] = "1";

						if ($_REQUEST["retanqueo_total"] != $datos_anteriores["retanqueo_total"])
							$cambio["retanqueo_total"] = "1";

						if ($_REQUEST["descuento1"] != $datos_anteriores["descuento1"])
							$cambio["descuento1"] = "1";

						if ($_REQUEST["descuento2"] != $datos_anteriores["descuento2"])
							$cambio["descuento2"] = "1";

						if ($_REQUEST["descuento3"] != $datos_anteriores["descuento3"])
							$cambio["descuento3"] = "1";

						if ($_REQUEST["descuento4"] != $datos_anteriores["descuento4"])
							$cambio["descuento4"] = "1";

						$descuentos_adicionales = sqlsrv_query($link, "select id_descuento from simulaciones_descuentos where id_simulacion = '".$id_simul."' order by id_descuento");

						while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
							if ($_REQUEST["descuentoadicional".$fila1["id_descuento"]] != $datos_anteriores["descuentoadicional".$fila1["id_descuento"]])
								$cambio["descuentoadicional".$fila1["id_descuento"]] = "1";
						}

						if ($_REQUEST["tipo_producto"] != $datos_anteriores["tipo_producto"])
							$cambio["tipo_producto"] = "1";

						if ($_REQUEST["descuento5"] != $datos_anteriores["descuento5"])
							$cambio["descuento5"] = "1";

						if ($_REQUEST["descuento6"] != $datos_anteriores["descuento6"])
							$cambio["descuento6"] = "1";

						if ($_REQUEST["descuento_transferencia"] != $datos_anteriores["descuento_transferencia"])
							$cambio["descuento_transferencia"] = "1";

						if ($_REQUEST["ultimo_consecutivo_compra_cartera"] != $datos_anteriores["ultimo_consecutivo_compra_cartera"])
							$cambio["ultimo_consecutivo_compra_cartera"] = "1";

						for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++){
							if ($_REQUEST["se_compra".$i] != $datos_anteriores["se_compra".$i])
								$cambio["se_compra".$i] = "1";

							if ($_REQUEST["id_entidad".$i] != $datos_anteriores["id_entidad".$i])
								$cambio["id_entidad".$i] = "1";

							if ($_REQUEST["entidad".$i] != $datos_anteriores["entidad".$i])
								$cambio["entidad".$i] = "1";

							if ($_REQUEST["valor_pagar".$i] != $datos_anteriores["valor_pagar".$i])
								$cambio["valor_pagar".$i] = "1";
						}

						for ($i = 1; $i <= 3; $i++){
							if ($_REQUEST["retanqueo".$i."_libranza"] != $datos_anteriores["retanqueo".$i."_libranza"])
								$cambio["retanqueo".$i."_libranza"] = "1";

							if ($_REQUEST["retanqueo".$i."_valor"] != $datos_anteriores["retanqueo".$i."_valor"])
								$cambio["retanqueo".$i."_valor"] = "1";
						}

						if ($_REQUEST["desembolso_cliente"] != $datos_anteriores["desembolso_cliente"])
							$cambio["desembolso_cliente"] = "1";

						if (in_array("1", $cambio, false)){
							$registrar_reversion = 1;

							$registrar_caracterizacion = 1;
						}
					}
				}

				//$registrar_caracterizacion = 0;

				if ($registrar_caracterizacion){
					sqlsrv_query($link, "START TRANSACTION");

					sqlsrv_query($link, "insert into contabilidad_transacciones (id_origen, id_simulacion, cod_transaccion, fecha, valor, observacion, estado, usuario_creacion, fecha_creacion) values ('1', '".$id_simul."', UPPER(MD5('".$id_simul."-".date("Y-m-d H:i:s")."')), GETDATE(), '".str_replace(",", "", $_REQUEST["valor_credito"])."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]."', 'PEN', '".$_SESSION["S_LOGIN"]."', getdate())");

					$rs4 = sqlsrv_query($link, "select MAX(id_transaccion) as m from contabilidad_transacciones");

					$fila4 = sqlsrv_fetch_array($rs4);

					$id_trans = $fila4["m"];

					sqlsrv_query($link, "COMMIT");

					if ($registrar_reversion){
						sqlsrv_query($link, "update contabilidad_transacciones set cod_transaccion_previa = '".$fila2["cod_transaccion"]."' where id_transaccion = '".$id_trans."'");

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, id_entidad, auxiliar, debito, credito, observacion) select '".$id_trans."', id_simulacion_retanqueo, id_entidad, auxiliar, credito, debito, CONCAT('REVERSION - ', observacion) from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' order by id_transaccion_movimiento");
					}

					$desembolso_cliente = str_replace(",", "", $_REQUEST["valor_credito"]);

					sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '01. CARTERA LIBRANZAS (CPU)', '".str_replace(",", "", $_REQUEST["valor_credito"])."', '0', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - CXC')");

					if ($_REQUEST["descuento1"]){
						$intereses_anticipados = round((str_replace(",", "", $_REQUEST["valor_credito"]) - str_replace(",", "", $_REQUEST["retanqueo_total"])) * $_REQUEST["descuento1"] / 100.00);

						$desembolso_cliente -= $intereses_anticipados;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '02. INTERESES ANTICIPADOS (CPU)', '0', '".$intereses_anticipados."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - INTERESES ANTICIPADOS')");
					}

					if ($_REQUEST["descuento2"]){
						$asesoria_financiera = round((str_replace(",", "", $_REQUEST["valor_credito"]) - str_replace(",", "", $_REQUEST["retanqueo_total"])) * $_REQUEST["descuento2"] / 100.00);

						$desembolso_cliente -= $asesoria_financiera;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '03. ASESORIA FINANCIERA (CPU)', '0', '".$asesoria_financiera."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - ASESORIA FINANCIERA')");
					}

					if ($_REQUEST["descuento3"]){
						$iva = round((str_replace(",", "", $_REQUEST["valor_credito"]) - str_replace(",", "", $_REQUEST["retanqueo_total"])) * $_REQUEST["descuento3"] / 100.00);

						$desembolso_cliente -= $iva;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '04. IVA ASESORIA FINANCIERA (CPU)', '0', '".$iva."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - IVA ASESORIA FINANCIERA')");
					}

					if ($_REQUEST["descuento4"]){
						$gmf = round((str_replace(",", "", $_REQUEST["valor_credito"]) - str_replace(",", "", $_REQUEST["retanqueo_total"])) * $_REQUEST["descuento4"] / 100.00);

						$desembolso_cliente -= $gmf;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '05. GMF (CPU)', '0', '".$gmf."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - GMF')");
					}

					$descuentos_adicionales = sqlsrv_query($link, "select da.nombre, sd.porcentaje from simulaciones_descuentos sd INNER JOIN descuentos_adicionales da ON sd.id_descuento = da.id_descuento where sd.id_simulacion = '".$id_simul."' order by sd.id_descuento");

					while ($fila1 = sqlsrv_fetch_array($descuentos_adicionales)){
						$descuentos_adicional = 0;

						if ($fila1["porcentaje"]){
							$descuentos_adicional = round((str_replace(",", "", $_REQUEST["valor_credito"]) - str_replace(",", "", $_REQUEST["retanqueo_total"])) * $fila1["porcentaje"] / 100.00);

							$desembolso_cliente -= $descuentos_adicional;

							sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '06. ".$fila1["nombre"]." (CPU)', '0', '".$descuentos_adicional."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - ".$fila1["nombre"]."')");
						}
					}

					if ($_REQUEST["descuento5"] AND $_REQUEST["tipo_producto"] == "1"){
						$comision_venta = round(str_replace(",", "", $_REQUEST["valor_credito"]) * $_REQUEST["descuento5"] / 100.00);

						$desembolso_cliente -= $comision_venta;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '07. COMISION POR VENTA (CPU)', '0', '".$comision_venta."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - COMISION POR VENTA')");
					}

					if ($_REQUEST["descuento6"] AND $_REQUEST["tipo_producto"] == "1"){
						$comision_venta_iva = round(str_replace(",", "", $_REQUEST["valor_credito"]) * $_REQUEST["descuento6"] / 100.00);

						$desembolso_cliente -= $comision_venta_iva;

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '08. IVA COMISION POR VENTA (CPU)', '0', '".$comision_venta_iva."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - IVA COMISION POR VENTA')");
					}

					if ($_REQUEST["descuento_transferencia"]){
						$desembolso_cliente -= str_replace(",", "", $_REQUEST["descuento_transferencia"]);

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '09. TRANSFERENCIA (CPU)', '0', '".str_replace(",", "", $_REQUEST["descuento_transferencia"])."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - TRANSFERENCIA')");
					}

					for ($i = 1; $i <= $_REQUEST["ultimo_consecutivo_compra_cartera"]; $i++){
						if ($_REQUEST["se_compra".$i] == "SI" && ($_REQUEST["id_entidad".$i] || $_REQUEST["entidad".$i])){
							if ($_REQUEST["valor_pagar".$i]){
								$entidad_desembolso = sqlsrv_query($link, "select nombre as nombre_entidad from entidades_desembolso where id_entidad = '".$_REQUEST["id_entidad".$i]."'");

								$fila4 = sqlsrv_fetch_array($entidad_desembolso);

								$nombre_entidad = $fila4["nombre_entidad"];

								$desembolso_cliente -= str_replace(",", "", $_REQUEST["valor_pagar".$i]);
								
								$auxiliar = "10. COMPRA CARTERA (CPU)";

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_entidad, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$_REQUEST["id_entidad".$i]."', '".$auxiliar."', '0', '".str_replace(",", "", $_REQUEST["valor_pagar".$i])."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - COMPRA CARTERA ".utf8_encode($nombre_entidad." ".$_REQUEST["entidad".$i])."')");
							}
						}
					}

					for ($i = 1; $i <= 3; $i++){
						if ($_REQUEST["retanqueo".$i."_libranza"] && $_REQUEST["retanqueo".$i."_valor"]){
							$retanqueo_valor_cancelacion = str_replace(",", "", $_REQUEST["retanqueo".$i."_valor"]);

							$rs1 = sqlsrv_query($link, "select id_simulacion, retanqueo_valor_cancelacion, retanqueo_valor_liquidacion, retanqueo_intereses, retanqueo_seguro, retanqueo_cuotasmora, retanqueo_segurocausado, retanqueo_gastoscobranza from simulaciones where cedula = '".$_REQUEST["cedula"]."' AND pagaduria = '".$_REQUEST["pagaduria"]."' AND nro_libranza = '".$_REQUEST["retanqueo".$i."_libranza"]."'");

							$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

							$retanqueo_valor_liquidacion = $fila1["retanqueo_valor_liquidacion"];
							$retanqueo_intereses = $fila1["retanqueo_intereses"];
							$retanqueo_seguro = $fila1["retanqueo_seguro"];
							$retanqueo_cuotasmora = $fila1["retanqueo_cuotasmora"];
							$retanqueo_segurocausado = $fila1["retanqueo_segurocausado"];
							$retanqueo_gastoscobranza = $fila1["retanqueo_gastoscobranza"];

							if ($retanqueo_valor_liquidacion){
								if ($retanqueo_valor_liquidacion > $retanqueo_valor_cancelacion)
									$retanqueo_valor_liquidacion = $retanqueo_valor_cancelacion;

								$desembolso_cliente -= $retanqueo_valor_liquidacion;

								$retanqueo_valor_cancelacion -= $retanqueo_valor_liquidacion;

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '11. RETANQUEO - CAPITAL (CPU)', '0', '".$retanqueo_valor_liquidacion."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - CAPITAL RETANQUEO LIBRANZA ".$_REQUEST["retanqueo".$i."_libranza"]."')");
							}

							if ($retanqueo_seguro && $retanqueo_valor_cancelacion){
								if (!$retanqueo_cuotasmora)
									$seguro = $retanqueo_seguro;
								else
									$seguro = $retanqueo_seguro * $retanqueo_cuotasmora;

								if ($seguro > $retanqueo_valor_cancelacion)
									$seguro = $retanqueo_valor_cancelacion;

								$desembolso_cliente -= $seguro;

								$retanqueo_valor_cancelacion -= $seguro;

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '12. RETANQUEO - SEGURO (CPU)', '0', '".$seguro."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - SEGURO RETANQUEO LIBRANZA ".$_REQUEST["retanqueo".$i."_libranza"]."')");
							}

							if ($retanqueo_segurocausado && $retanqueo_valor_cancelacion){
								if ($retanqueo_segurocausado > $retanqueo_valor_cancelacion)
									$retanqueo_segurocausado = $retanqueo_valor_cancelacion;

								$desembolso_cliente -= $retanqueo_segurocausado;

								$retanqueo_valor_cancelacion -= $retanqueo_segurocausado;

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '13. RETANQUEO - SEGURO CAUSADO (CPU)', '0', '".$retanqueo_segurocausado."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - SEGURO CAUSADO RETANQUEO LIBRANZA ".$_REQUEST["retanqueo".$i."_libranza"]."')");
							}

							if ($retanqueo_intereses && $retanqueo_valor_cancelacion){
								if (!$retanqueo_cuotasmora)
									$intereses = $retanqueo_intereses;
								else
									$intereses = $retanqueo_intereses * $retanqueo_cuotasmora;

								if ($intereses > $retanqueo_valor_cancelacion)
									$intereses = $retanqueo_valor_cancelacion;

								$desembolso_cliente -= $intereses;

								$retanqueo_valor_cancelacion -= $intereses;

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '14. RETANQUEO - INTERESES (CPU)', '0', '".$intereses."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - INTERESES RETANQUEO LIBRANZA ".$_REQUEST["retanqueo".$i."_libranza"]."')");
							}

							if ($retanqueo_valor_cancelacion){
								$desembolso_cliente -= $retanqueo_valor_cancelacion;

								sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, auxiliar, debito, credito, observacion) values ('".$id_trans."', '".$fila1["id_simulacion"]."', '15. RETANQUEO - GASTOS COBRANZA (CPU)', '0', '".$retanqueo_valor_cancelacion."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - GASTOS COBRANZA RETANQUEO LIBRANZA ".$_REQUEST["retanqueo".$i."_libranza"]."')");
							}
						}
					}

					if ($desembolso_cliente){
						if ($desembolso_cliente > 0){
							sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '16. DESEMBOLSO CLIENTE (CPU)', '0', '".$desembolso_cliente."', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - DESEMBOLSO CLIENTE')");
						}
						else{
							sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, auxiliar, debito, credito, observacion) values ('".$id_trans."', '17. AJUSTE AL PESO (CPU)', '".abs($desembolso_cliente)."', '0', 'CREDITO LIBRANZA ".$_REQUEST["nro_libranza"]." - AJUSTE AL PESO')");
						}
					}
				}
			}
			else{
				$ultima_caracterizacion = sqlsrv_query($link, "SELECT top 1 id_transaccion, cod_transaccion, observacion from contabilidad_transacciones where id_simulacion = '".$id_simul."' AND id_origen = '1' order by id_transaccion DESC");
				

				$fila2 = sqlsrv_fetch_array($ultima_caracterizacion);
			

				if ($fila2["cod_transaccion"]){
					$hay_para_reversar = sqlsrv_query($link, "SELECT top 1 id_transaccion_movimiento from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

					if (sqlsrv_num_rows($hay_para_reversar)){
						sqlsrv_query($link, "START TRANSACTION");

						sqlsrv_query($link, "insert into contabilidad_transacciones (id_origen, id_simulacion, cod_transaccion, fecha, valor, observacion, estado, usuario_creacion, fecha_creacion) values ('1', '".$id_simul."', UPPER(MD5('".$id_simul."-".date("Y-m-d H:i:s")."')), GETDATE(), '0', '".$fila2["observacion"]."', 'PEN', '".$_SESSION["S_LOGIN"]."', getdate())");

						$rs4 = sqlsrv_query($link, "select MAX(id_transaccion) as m from contabilidad_transacciones");

						$fila4 = sqlsrv_fetch_array($rs4);

						$id_trans = $fila4["m"];

						sqlsrv_query($link, "COMMIT");

						sqlsrv_query($link, "update contabilidad_transacciones set cod_transaccion_previa = '".$fila2["cod_transaccion"]."' where id_transaccion = '".$id_trans."'");

						sqlsrv_query($link, "insert into contabilidad_transacciones_movimientos (id_transaccion, id_simulacion_retanqueo, id_entidad, auxiliar, debito, credito, observacion) select '".$id_trans."', id_simulacion_retanqueo, id_entidad, auxiliar, credito, debito, CONCAT('REVERSION - ', observacion) from contabilidad_transacciones_movimientos where id_transaccion = '".$fila2["id_transaccion"]."' AND observacion NOT LIKE 'REVERSION%' order by id_transaccion_movimiento");
					}
				}
			}

			sqlsrv_query($link, "insert into simulaciones_logs (id_simulacion, archivo_ruta, cambios, usuario_creacion, fecha_creacion) values ('".$id_simul."', 'simulador2.php', '".json_encode($_REQUEST)."', '".$_SESSION["S_LOGIN"]."', getdate())");

			$consultarSimulacionDatos="SELECT * FROM simulaciones WHERE id_simulacion='".$id_simul."'";
			$consultarComprasCarteraCredito="SELECT * FROM simulaciones_comprascartera WHERE id_simulacion='".$id_simul."' AND se_compra='SI'";
			$queryComprasCarteraCredito=sqlsrv_query($link, $consultarComprasCarteraCredito, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			$subestadoBloqueoComision = 48;
			$querySimulacionDatos=sqlsrv_query($link, $consultarSimulacionDatos);
			
			if (sqlsrv_num_rows($queryComprasCarteraCredito)>0) {
				$consultarComprasCC="SELECT sum(cuota) AS cuota,sum(valor_pagar) AS valor_pagar FROM simulaciones_comprascartera WHERE id_simulacion='".$id_simul."' AND se_compra='SI'";
				$queryComprasCC=sqlsrv_query($link, $consultarComprasCC);
				$resComprasCC=sqlsrv_fetch_array($queryComprasCC);
				$resSimulacionDatos=sqlsrv_fetch_array($querySimulacionDatos);
				
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
			$queryEstBloqueoComision=sqlsrv_query($link, $sqlEstBloqueoComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
			
			if (sqlsrv_num_rows($queryEstBloqueoComision) == 0){//No ha Pasado por estados: 48,78

				$sqlTasaComision="SELECT a.marca_unidad_negocio, a.id_tasa_comision, a.id_unidad_negocio, a.tasa AS tasa_interes, a.kp_plus, a.id_tipo, a.fecha_inicio, iif(a.fecha_fin IS NULL, '',a.fecha_fin) AS fecha_fin, a.vigente FROM tasas_comisiones a WHERE a.id_tasa_comision ='". $_REQUEST["tipo_tasa_comision"]."'";

				$queryTasaComision=sqlsrv_query($link, $sqlTasaComision, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

				if (sqlsrv_num_rows($queryTasaComision)>0){
					$respTasaComision = sqlsrv_fetch_array($queryTasaComision);
					$id_tasa_comision = $respTasaComision["id_tasa_comision"];
					$id_tipo_comision = $respTasaComision["id_tipo"];

					//consultarTasaComisionAnterior
					$sqlSimTasaCom="SELECT a.id_tasa_comision FROM simulaciones a WHERE a.id_simulacion =". $_REQUEST["id_simulacion"];
					$querySimTasaCom=sqlsrv_query($link, $sqlSimTasaCom);
					$respSimTasaCom = sqlsrv_fetch_array($querySimTasaCom);
					$id_tasa_comision_anterior = $respSimTasaCom["id_tasa_comision"];

					if($id_tasa_comision_anterior != $id_tasa_comision){

						sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = $id_tasa_comision, id_tipo_tasa_comision = $id_tipo_comision WHERE id_simulacion  = '".$id_simul."'");
						sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", $id_tasa_comision, ".$_SESSION['S_IDUSUARIO'].", getdate())");
					}
				}else{
					sqlsrv_query($link, "UPDATE simulaciones SET id_tasa_comision = 0 WHERE id_simulacion  = '".$id_simul."'");
					sqlsrv_query($link, "INSERT INTO simulaciones_tasa_comision (id_simulacion, id_tasa_comision, id_usuario, fecha) VALUES(".$_REQUEST["id_simulacion"].", 0, ".$_SESSION['S_IDUSUARIO'].", getdate())");
				}
			}
			
			$mensaje .= "Simulacion guardada exitosamente.";
		}
	}
		
	?>
<link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<script type="text/javascript" src="../plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../plugins/sweetalert2/sweetalert2.min.js"></script>
<script>
	$(document).ready(function () {
			Swal.fire({
				icon: 'warning',
				html: '<h3 style="text-align: left !important;"><?=$mensaje?>!</h3>',
				showCancelButton: false,
				confirmButtonText: 'ACEPTAR',
				lowOutsideClick: false,
				focusConfirm: false,
			}).then((result) => {
				<?php
				if (!$_REQUEST["id_cazador"]){
					if (!$_REQUEST["buscar"] && $_REQUEST["back"] != "prospecciones" && $_REQUEST["back"] != "pilotofdc"){
						$_REQUEST["buscar"] = "1";
						$_REQUEST["descripcion_busqueda"] = $_REQUEST["cedula"];
					} ?>
					window.location = 'simulaciones.php?buscar=simulaciones&id_simulacion_buscar=<?=$_REQUEST["id_simulacion"]?>';
					<?php
				} else{ 
					?>
					window.location = 'cazador.php';
					<?php
				}
				?>

			})
		});
		
</script>

