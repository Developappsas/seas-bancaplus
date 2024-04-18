<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include ('../functions.php'); 


if (!$_SESSION["S_LOGIN"] || !($_SESSION["S_TIPO"] == "ADMINISTRADOR" || $_SESSION["S_TIPO"] == "OPERACIONES" || $_SESSION["S_TIPO"] == "COMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_GEST_COM" || $_SESSION["S_SUBTIPO"] == "ANALISTA_REFERENCIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VALIDACION" || $_SESSION["S_SUBTIPO"] == "ANALISTA_CREDITO" || $_SESSION["S_SUBTIPO"] == "ANALISTA_TESORERIA" || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_SUBTIPO"] == "COORD_PROSPECCION" || $_SESSION["S_SUBTIPO"] == "COORD_CREDITO" || $_SESSION["S_SUBTIPO"] == "NEXA"))
{
    exit;
}

if (!$_REQUEST["section"])
	$_REQUEST["section"] = "0";

?>
<?php include("top.php"); 
$link = conectar_utf();
?>

<link href="../style_captura.css" rel="stylesheet" type="text/css">
<link href="../jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css">
<script src="../jquery-1.9.1.js"></script>
<script src="../jquery-ui-1.10.3.custom.js"></script>  
<script>

$( function() {
	$("#accordion").accordion({
		heightStyle: "content",
		collapsible: true,
		active: <?php echo $_REQUEST["section"] ?>
	});

	$("#accordion").on( "accordionbeforeactivate", function( event, ui ) {
    	$(ui.oldPanel).find('input').each(function (index, element) {
			if ($(element).prop('name') == "edicion_habilitada") {
				if ($(element).prop('value') == "1") {
					alert("Debe guardar los datos que edito antes de cambiar de seccion");
					event.preventDefault();
					return false;
				}
			}
	    })
	});
});

$.datepicker.regional['es'] = {
	monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
	dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'],
	dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
	dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
	weekHeader: 'Sm',
	dateFormat: 'yy-mm-dd',
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearRange: '-100:+0',
	changeMonth: true,
	changeYear: true,
};

$.datepicker.setDefaults($.datepicker.regional['es']);

$(function () {
	$("#fecha").datepicker();
	$("#fecha_expedicion").datepicker();
	$("#fecha_nacimiento").datepicker();
	$("#conyugue_fecha_expedicion").datepicker();
	$("#conyugue_fecha_nacimiento").datepicker();
	$("#conyugue_fecha_vinculacion").datepicker();
	$("#fecha_vinculacion").datepicker();
	$("#apoderado_fecha_inicio").datepicker();
	$("#apoderado_fecha_final").datepicker();
	$("#fecha_entrevista").datepicker();
	$("#seguro_respuesta_9b").datepicker();
});

function SumarIngresos() { 
	var n1 = 0;
	var n2 = 0;
	var n3 = 0;
	
	if (document.formato4.ingresos_laborales.value != "")
		n1 = parseInt(document.formato4.ingresos_laborales.value);
		
	if (document.formato4.honorarios_comisiones.value != "")
		n2 = parseInt(document.formato4.honorarios_comisiones.value);
		
	if (document.formato4.otros_ingresos.value != "")
		n3 = parseInt(document.formato4.otros_ingresos.value); 
		
	document.formato4.total_ingresos.value = n1 + n2 + n3;
} 

function SumarActivos() { 
	var n1 = 0;
	
	if (document.formato4.activos_fijos.value != "")
		n1 = parseInt(document.formato4.activos_fijos.value);
		
	document.formato4.total_activos.value = n1;
} 

function SumarPasivos() { 
	var n1 = 0;
	var n2 = 0;
	var n3 = 0;
	
	if (document.formato4.pasivos_financieros.value != "")
		n1 = parseInt(document.formato4.pasivos_financieros.value);
		
	if (document.formato4.pasivos_corrientes.value != "")
		n2 = parseInt(document.formato4.pasivos_corrientes.value);
		
	if (document.formato4.otros_pasivos.value != "")
		n3 = parseInt(document.formato4.otros_pasivos.value); 
		
	document.formato4.total_pasivos.value = n1 + n2 + n3;
} 

function HabilitarEdicion(formato, habilitar) {
	with (formato) {

		var fecha_radicado_parse = Date.parse(formato.fecha_radicado.value);
		var fecha_bloqueo_parse = Date.parse('2024-01-17');
		
		(fecha_bloqueo_parse <= fecha_radicado_parse) ? bloquearCamposProspeccion = true : bloquearCamposProspeccion = false;

 		for(i = 35; i <= formato.elements.length - 3; i++) {
			if (habilitar == "1") {
				if(formato.elements[i].classList.contains('camposBloqueados') && bloquearCamposProspeccion){
					if(formato.elements[i].id == 'fecha_expedicion' && formato.elements[i].value == ''){
						formato.elements[i].style = "pointer-events:visible;";
					}else{
						formato.elements[i].style = "pointer-events:none;";
					}					
				}
				formato.elements[i].disabled = false;
			}
			else {
				formato.elements[i].disabled = true;
			}
		}
 	}
}

function chequeo_forma() {
	with (document.formato) {
		var arroba = email.value.indexOf("@");
		var substr = email.value.substring(arroba + 1, 100);
		var otra_arroba = substr.indexOf("@");
		var espacio = email.value.indexOf(" ");
		var punto = email.value.lastIndexOf(".");
		var ultimo = email.value.length-1;

		if ((tipo_vivienda.value == "ARRENDADA") && (arrendador_nombre.value == "" || arrendador_telefono.value == "" || arrendador_ciudad.value == "")) {
			alert("Debe digitar el nombre, telefono y ciudad del arrendador");
			return false;
		}
		if ((fecha.value == "") || (asesor.value == "") || (nombre1.value == "") || (apellido1.value == "") || (tipo_documento.value == "") || (cedula.value == "") || (fecha_expedicion.value == "") || (lugar_expedicion.value == "") || (fecha_nacimiento.value == "") || (sexo.value == "") || (lugar_nacimiento.value == "") || (estado_civil.value == "") || (residencia_pais.value == "") || (ciudad.value == "") || (residencia_departamento.value == "") || (tipo_vivienda.value == "") || (residencia_barrio.value == "") || (direccion.value == "") || (residencia_estrato.value == "") || (anios.value == "") || (meses.value == "") || (celular.value == "") || (lugar_correspondencia.value == "") || (email.value == "") || (personas_acargo.value == "") || (personas_acargo_adultos.value == "") || (personas_acargo_menores.value == "") || (nivel_estudios.value == "") || (peso.value == "") || (nivel_estudios.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
		if ((email.value != "") && (arroba < 1 || otra_arroba != -1 || punto - arroba < 2 || ultimo - punto > 3 || ultimo - punto < 2 || espacio != -1)) {
			alert("El correo electronico no es valido. Debe corregir la informacion.");
			email.value = "";
			email.focus();
			return false;
		}
		if (parseInt(personas_acargo.value) != (parseInt(personas_acargo_adultos.value) + parseInt(personas_acargo_menores.value))) {
			alert("La suma de personas a cargo adultos y menores debe ser igual al total de personas a cargo");
			return false;
		}
	}
}

function chequeo_forma2() {
	with (document.formato2) {
		if ((nombre_conyugue.value != "") && (conyugue_apellido_1.value == "" || conyugue_tipo_documento.value == "" || cedula_conyugue.value == "" || conyugue_fecha_expedicion.value == "" || conyugue_lugar_expedicion.value == "" || conyugue_fecha_nacimiento.value == "" || conyugue_sexo.value == "" || conyugue_celular.value == "")) {
			alert("Debe digitar los datos del conyugue: El nombre, apellido(s), tipo de identificacion, numero identificacion, fecha de expedicion del documento, lugar de expedicion del documento, fecha de nacimiento, sexo y telefono celular");
			return false;
		}
	}
}

function chequeo_forma3() {
	with (document.formato3) {
		if ((ocupacion.value == "1") && (nombre_empresa.value == "" || cargo.value == "" || fecha_vinculacion.value == "" || direccion_trabajo.value == "" || ciudad_trabajo.value == "" || nit_empresa.value == "" || telefono_trabajo.value == "" || tipo_empresa.value == "" || actividad_economica_empresa.value == "" || tipo_contrato.value == "")) {
			alert("Si es empleado, debe digitar la informacion asociada a la empresa");
			return false;
		}
		if ((ocupacion.value == "") || (declara_renta.value == "") || (funcionario_publico.value == "") || (recursos_publicos.value == "") || (personaje_publico.value == "") || (actividad_economica_principal.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
	}
}

function chequeo_forma4() {
	var arriendo = 0;
	
	with (document.formato4) {
		if (otros_ingresos.value != "" && detalle_ingresos.value == "") {
			alert("Debe indicar cuales son los otros ingresos");
			return false;
		}
		if (otros_pasivos.value != "" && detalle_otros_pasivos.value == "") {
			alert("Debe indicar cuales son los otros pasivos");
			return false;
		}
		if ((ingresos_laborales.value == "") || (activos_fijos.value == "") || (gastos_familiares.value == "") || (pasivos_financieros.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
		if (valor_arrendo.value != "") {
			arriendo = parseInt(valor_arrendo.value); 
		}
		if ((parseInt(gastos_familiares.value) + arriendo) > parseInt(total_ingresos.value)) {
			alert("La suma de gastos familiares y arriendo/cuota viviende no puede ser mayor al total de ingresos");
			return false;
		}
	}
}

function chequeo_forma5() {
	with (document.formato5) {
		if ((nombre_familiar.value == "") || (parentesco_familiar.value == "") || (direccion_familiar.value == "") || (ciudad_familiar.value == "") || (celular_familiar.value == "") || (nombre_personal.value == "") || (parentesco_personal.value == "") || (direccion_personal.value == "") || (ciudad_personal.value == "") || (celular_personal.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
	}
}

function chequeo_forma6() {
	with (document.formato6) {
		if (moneda_extranjera.value == "SI" && tipo_transaccion_exportacion.checked == false && tipo_transaccion_importacion.checked == false && tipo_transaccion_inversiones.checked == false && tipo_transaccion_prestamo.checked == false && tipo_transaccion_otra.checked == false) {
			alert("Si realiza operaciones en moneda extranjera, debe establecer al menos un tipo de transaccion");
			return false;
		}
		if (tipo_transaccion_otra.checked == true && tipo_transaccion_otra_cual.value == "") {
			alert("Debe escribir cual tipo de transaccion");
			return false;
		}
		if ((cuentas_exterior.value == "SI") && (banco.value == "" || num_cuenta.value == "" || tipo_producto_operaciones.value == "" || monto_operaciones.value == "" || moneda_operaciones.value == "" || ciudad_operaciones.value == "" || pais_operaciones.value == "")) {
			alert("Si posee cuentas en el exterior, debe diligenciar todos los campos de la cuenta en productos en moneda extranjera");
			return false;
		}
		if ((actividades_apnfd.value == "") || (criptomoneda.value == "") || (moneda_extranjera.value == "") || (cuentas_exterior.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
	}
}

function chequeo_forma7() {
	with (document.formato7) {
		if ((impuestos_extranjera.value == "SI") && (poder_pais1.value == "" || poder_identificacion1.value == "")) {
			alert("Si esta obligado tributariamente en el exterior, debe diligenciar el pais y la identificacion tributaria correspondiente");
			return false;
		}
		if ((ciudadania_extranjera.value == "") || (residencia_extranjera.value == "") || (impuestos_extranjera.value == "") || (representacion_extranjera.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
	}
}

function chequeo_forma8() {
	with (document.formato8) {
		var arroba = apoderado_email.value.indexOf("@");
		var substr = apoderado_email.value.substring(arroba + 1, 100);
		var otra_arroba = substr.indexOf("@");
		var espacio = apoderado_email.value.indexOf(" ");
		var punto = apoderado_email.value.lastIndexOf(".");
		var ultimo = apoderado_email.value.length-1;
		
		if ((apoderado_email.value != "") && (arroba < 1 || otra_arroba != -1 || punto - arroba < 2 || ultimo - punto > 3 || ultimo - punto < 2 || espacio != -1)) {
			alert("El E-mail no es valido. Debe corregir la informacion.");
			apoderado_email.value = "";
			apoderado_email.focus();
			return false;
		}
	}
}

function chequeo_forma9() {
	with (document.formato9) {
		if ((instruccion_desembolso.value == "") || (cargo_publico.value=="") || (fuentes_actividades_licitas.value == "") || (clave.value == "") || (condiciones_seguros.value == "") || (primas_seguros.value == "") || (cancelacion_valores.value == "") || (ampliacion_plazo.value == "") || (descuentos_anticipados.value == "") || (resultado_entrevista.value == "") || (fecha_entrevista.value == "") || (observaciones.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios");
			return false;
		}
	}
}

function chequeo_forma10() {
	with (document.formato10) {
		if ((seguro_respuesta_1.value == "") || (seguro_respuesta_2.value == "") || (seguro_respuesta_3.value == "") || (seguro_respuesta_4.value == "") || (seguro_respuesta_5.value == "") || (seguro_respuesta_6.value == "") || (seguro_respuesta_7.value == "") || (seguro_respuesta_8.value == "")) {
			alert("Los campos marcados con asterisco (*) son obligatorios (Preguntas de Declaración de Asegurabilidad)");
			return false;
		}else{

			if ((seguro_respuesta_1.value == "SI") || (seguro_respuesta_2.value == "SI") || (seguro_respuesta_3.value == "SI") || (seguro_respuesta_4.value == "SI") || (seguro_respuesta_5.value == "SI") || (seguro_respuesta_6.value == "SI") || (seguro_respuesta_7.value == "SI") || (seguro_respuesta_8.value == "SI")) {

				if((seguro_respuesta_9a.value == "") || (seguro_respuesta_9b.value == "") || (seguro_respuesta_9c.value == "") || (seguro_respuesta_9d.value == "") || (seguro_respuesta_9e.value == "") || (seguro_respuesta_9f.value == "")){
					alert('En caso de haber marcado "Si" a alguna de las preguntas, llene los campos que estan al final de formulario (Declaración de Asegurabilidad)');
					return false;
				}
			}
		}
	}
}
//-->
</script>
<table border="0" cellspacing=1 cellpadding=2 width="95%">
<tr>
    <td width="18"><a href="simulaciones.php?fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&fechades_inicialbd=<?php echo $_REQUEST["fechades_inicialbd"] ?>&fechades_inicialbm=<?php echo $_REQUEST["fechades_inicialbm"] ?>&fechades_inicialba=<?php echo $_REQUEST["fechades_inicialba"] ?>&fechades_finalbd=<?php echo $_REQUEST["fechades_finalbd"] ?>&fechades_finalbm=<?php echo $_REQUEST["fechades_finalbm"] ?>&fechades_finalba=<?php echo $_REQUEST["fechades_finalba"] ?>&fechaprod_inicialbm=<?php echo $_REQUEST["fechaprod_inicialbm"] ?>&fechaprod_inicialba=<?php echo $_REQUEST["fechaprod_inicialba"] ?>&fechaprod_finalbm=<?php echo $_REQUEST["fechaprod_finalbm"] ?>&fechaprod_finalba=<?php echo $_REQUEST["fechaprod_finalba"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&unidadnegociob=<?php echo $_REQUEST["unidadnegociob"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&tipo_comercialb=<?php echo $_REQUEST["tipo_comercialb"] ?>&id_comercialb=<?php echo $_REQUEST["id_comercialb"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&decisionb=<?php echo $_REQUEST["decisionb"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&visualizarb=<?php echo $_REQUEST["visualizarb"] ?>&calificacionb=<?php echo $_REQUEST["calificacionb"] ?>&statusb=<?php echo $_REQUEST["statusb"] ?>&id_oficinab=<?php echo $_REQUEST["id_oficinab"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>"><img src="../images/back_01.gif"></a></td>
    <td class="titulo"><center><b class="tilenews2">Formato de solicitud de nuevo credito </b><br><br></center></td>
</tr>
</table>
<?php
	$rsFE = sqlsrv_query($link, "SELECT FORMAT(fecha_radicado, 'yyyy-MM-dd ') AS fecha_radicado from simulaciones WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'");
	$filaFE = sqlsrv_fetch_array($rsFE);
?>
<div id="accordion" style="width:95%">
	<h3>Informaci&oacute;n Personal</h3>
	<div id="section1">
		<form name="formato" method="post" autocomplete="off" action="solicitud_crear.php" onSubmit="return chequeo_forma()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="0">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
<?php


$consulta = "select id_simulacion from solicitud where id_simulacion = '".$_REQUEST["id_simulacion"]."'";

$x = sqlsrv_query($link, $consulta, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if(!(sqlsrv_num_rows($x)))
{
	$queryDB = "SELECT un.id_empresa, si.otp_verificado, si.cedula, si.fecha_nacimiento FROM simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad WHERE si.id_simulacion = '".$_REQUEST["id_simulacion"]."' " ;
}
else
{
	$queryDB = "SELECT so.*, un.id_empresa, si.otp_verificado FROM solicitud so INNER JOIN simulaciones si ON so.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad WHERE so.id_simulacion = '".$_REQUEST["id_simulacion"]."'";
}

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

$rs1 = sqlsrv_query($link, $queryDB);

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

$formato_digital = 0;
$otp_verificado = 1;
$conFormatoDigital = sqlsrv_query($link, "SELECT formato_digital, otp_verificado FROM simulaciones WHERE id_simulacion = '".$_REQUEST["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

if($conFormatoDigital && sqlsrv_num_rows($conFormatoDigital) > 0){
	$resFormatoDigital = sqlsrv_fetch_array($conFormatoDigital);
	$formato_digital = $resFormatoDigital["formato_digital"];
	$otp_verificado = $resFormatoDigital["otp_verificado"];
}

?>
		<table border="0" cellspacing=1 cellpadding=2 >
		<tr>
			<td>
				<div class="box1 clearfix">
				<table border="0" cellspacing=1 cellpadding=2 >
				<tr>
		        	<td><label  class="negrita">* Fecha</label></td>
		            <td><input  size="10" type="text" name="fecha" id="fecha" value="<?php echo $fila1["fecha"] ?>" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
					<td><label  class="negrita">* Asesor: </label></td>   
					<td><select name="asesor" id="asesor" style="width:250px" disabled>
							<option value=""></option>
							<?php

							$queryDB = "SELECT a.id_usuario, a.nombre, a.apellido from simulaciones b JOIN usuarios a ON a.id_usuario = b.id_comercial WHERE b.id_simulacion = '".$_REQUEST["id_simulacion"]."' UNION SELECT a.id_usuario, a.nombre, a.apellido from solicitud b JOIN usuarios a ON a.id_usuario = b.asesor WHERE b.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

							$rz1 = sqlsrv_query($link, $queryDB);

							while ($fila = sqlsrv_fetch_array($rz1)){
							    if ($fila["id_usuario"] == $fila1["asesor"] ){
							        $selected_comercial = " selected";
							    }
							    else{
									$selected_comercial = "";
							    }
								
							    echo "<option value=\"".$fila["id_usuario"]."\" ".$selected_comercial.">".($fila["nombre"])." ".($fila["apellido"])."</option>\n";
							}

							?>
							</select>&nbsp;&nbsp;
					</td>
		            <td valign="top">
		            	1. Diligencie completamente la solicitud.<br>
						2. Escriba los nombres y apellidos igual a como aparecen en el documento del cliente.</br>
		    	        3. Anexe fotocopia de la c&eacute;dula AMPLIADA AL 150%
					</td>
				</tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
		        <table>
		        <tr>
		        	<td><label class="negrita">* Primer Nombre:</label></td>
		            <td><input class="camposBloqueados" type="text" id="nombre1" name="nombre1" value="<?php echo ($fila1["nombre1"]) ?>" maxlength="20" disabled></td>
		            <td><label class="negrita">Segundo Nombre:</label></td>
		            <td><input class="camposBloqueados" type="text" id="nombre2" name="nombre2" value="<?php echo ($fila1["nombre2"]) ?>" maxlength="20" disabled></td>
		            <td><label class="negrita">* Primer Apellido:</label></td>
		            <td><input class="camposBloqueados" type="text" id="apellido1" name="apellido1" value="<?php echo ($fila1["apellido1"]) ?>" maxlength="20" disabled></td>
		            <td><label class="negrita">Segundo Apellido:</label></td>
		            <td><input class="camposBloqueados" type="text" id="apellido2" name="apellido2" value="<?php echo ($fila1["apellido2"]) ?>" maxlength="20" disabled></td>
				</tr>
		        <tr>
		        	<td><label class="negrita">* Tipo de Identificaci&oacute;n:</label></td>                    
		            <td><select name="tipo_documento" id="tipo_documento" style="width:147px" disabled>
						<?php

						echo "<option value=''></option>";

						$selected = $fila1['tipo_documento']=='CEDULA'?'selected':'';
						echo "<option value='CEDULA' $selected>CEDULA</option>";

						$selected = $fila1['tipo_documento']=='REGISTRO CIVIL'?'selected':'';
						echo "<option value='REGISTRO CIVIL' $selected>REGISTRO CIVIL</option>";

						$selected = $fila1['tipo_documento']=='TARJETA IDENTIDAD'?'selected':'';
						echo "<option value='TARJETA IDENTIDAD' $selected>TARJETA IDENTIDAD</option>";

						$selected = $fila1['tipo_documento']=='CEDULA EXTRANGERIA'?'selected':'';
						echo "<option value='CEDULA EXTRANGERIA' $selected>CEDULA EXTRANJERIA</option>";

						?>
						</select>
					</td>
					<td><label class="negrita">* N&uacute;mero:</label></td>
		            <td><input class="camposBloqueados" type="text" id="cedula" name="cedula" value="<?php echo $fila1["cedula"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20"  disabled></td>
		            <td><label class="negrita">* Fecha de Expedici&oacute;n:</label></td>
		            <td><input class="camposBloqueados" type="text" name="fecha_expedicion" value="<?php echo $fila1["fecha_expedicion"] ?>" id="fecha_expedicion" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}"  disabled></td>
		            <td><label class="negrita">* Lugar de Expedici&oacute;n:</label></td>rita">* Lugar de Expedici&oacute;n:</label></td>
		            <td>
		            	<select id="lugar_expedicion" name="lugar_expedicion" style="width:147px" disabled>
		            		<option value=""></option>
							<?php

							$queryDB = "select cod_municipio,municipio from ciudades order by municipio";

							$rz2 = sqlsrv_query($link, $queryDB);

							while ($fila = sqlsrv_fetch_array($rz2))
							{
								if ($fila["cod_municipio"] == $fila1["lugar_expedicion"] )
							    	$selected_municipio = " selected";
								else
							    	$selected_municipio = "";
								
								echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
							}

							?>
						</select>
					</td>
				</tr>
		        <tr>
		        	<td><label class="negrita">* Fecha de Nacimiento:</label></td>
		            <td><input class="camposBloqueados" type="text" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $fila1["fecha_nacimiento"] ?>" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}"  disabled></td>
		            <td><label class="negrita">* Sexo:</label></td>
		            <td>
		            	<select name="sexo" id="sexo" style="width:147px" disabled>
							<?php

							$sexo_m = "";
							$sexo_f = "";

							if ($fila1["sexo"] == "M")
								$sexo_m = " selected";
							else if ($fila1["sexo"] == "F")
								$sexo_f = " selected";

							?>
							<option value=""></option>
		                    <option value="M"<?php echo $sexo_m ?>>M</option>
		                    <option value="F"<?php echo $sexo_f ?>>F</option>
						</select>
						<input type="text" style="display:none;" />
					</td>
					<td><label class="negrita">* Lugar de Nacimiento:</label></td>
		            <td><select id="lugar_nacimiento" name="lugar_nacimiento" style="width:147px" disabled>
		            		<option value=""></option>
							<?php

							$queryDB = "select cod_municipio,municipio,departamento from ciudades order by municipio";
							$rz1 = sqlsrv_query($link, $queryDB);

							while ($fila = sqlsrv_fetch_array($rz1)){
								if ($fila["cod_municipio"] == $fila1["lugar_nacimiento"] )
									$selected_municipio = " selected";
								else
									$selected_municipio = "";
								
								echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"]." - ".$fila["departamento"])."</option>\n";
							}

							?>
						</select>
					</td>
					<td><label class="negrita">* Estado Civil:</label></td>
					<?php

					$estado_soltero = "";
					$estado_unionlibre = "";
					$estado_casado = "";
					$estado_separado = "";
					$estado_viudo = "";

					if ($fila1["estado_civil"] == "SOLTERO")
						$estado_soltero = " selected";
					else if ($fila1["estado_civil"] == "UNION LIBRE")
						$estado_unionlibre = " selected";
					else if ($fila1["estado_civil"] == "CASADO")
						$estado_casado = " selected";
					else if ($fila1["estado_civil"] == "DIVORCIADO")
						$estado_divorciado = " selected";
					else if ($fila1["estado_civil"] == "SEPARADO")
						$estado_separado = " selected";
					else if ($fila1["estado_civil"] == "VIUDO")
						$estado_viudo = " selected";

					?>
					<td>
						<select name="estado_civil" id="estado_civil" style="width:147px" disabled>
							<option value=""></option>
		                    <option value="SOLTERO"<?php echo $estado_soltero ?>>SOLTERO</option>
		                    <option value="UNION LIBRE"<?php echo $estado_unionlibre ?>>UNION LIBRE</option>
		                    <option value="CASADO"<?php echo $estado_casado ?>>CASADO</option>
		                    <option value="DIVORCIADO"<?php echo $estado_divorciado ?>>DIVORCIADO</option>
		                    <option value="SEPARADO"<?php echo $estado_separado ?>>SEPARADO</option>
		                    <option value="VIUDO"<?php echo $estado_viudo ?>>VIUDO</option>
						</select>
					</td>                        
				</tr>   
				<tr>
					<td><label class="negrita">* Pa&iacute;s de Residencia:</label></td>
		            <td><input type="text" id="residencia_pais" name="residencia_pais" value="<?php echo ($fila1["residencia_pais"]) ?>" maxlength="45" disabled></td>
		            <td><label  class="negrita">* Ciudad:</label></td>
		            <td>
		            	<select id="ciudad" name="ciudad" style="width:147px" disabled>
		            		<option value=""></option>
							<?php
							$queryDB = "SELECT cod_municipio,CONCAT(departamento,'-',municipio) AS municipio FROM ciudades ORDER BY departamento ASC";
							$rz1 = sqlsrv_query($link, $queryDB);

							while ($fila = sqlsrv_fetch_array($rz1)) {
								if ($fila["cod_municipio"] == $fila1["ciudad"] )
							    	$selected_municipio = " selected";
								else
							    	$selected_municipio = "";
								
								echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
							}
							?>
						</select>
					</td>
		            <td><label class="negrita">* Departamento:</label></td>
		            <td><input type="text" id="residencia_departamento" name="residencia_departamento" value="<?php echo ($fila1["residencia_departamento"]) ?>" maxlength="45" disabled></td>
		        	<td><label class="negrita">* Tipo de Vivienda:</label></td>                        
		            <td><select name="tipo_vivienda" id="tipo_vivienda" style="width:147px" disabled>
							<?php

							$vivienda_familiar = "";
							$vivienda_arrendada = "";
							$vivienda_propia = "";

							if ($fila1["tipo_vivienda"] == "FAMILIAR")
								$vivienda_familiar = " selected";
							else if ($fila1["tipo_vivienda"] == "ARRENDADA")
								$vivienda_arrendada = " selected";
							else if ($fila1["tipo_vivienda"] == "PROPIA")
								$vivienda_propia = " selected";

							?>
							<option value=""></option>
		                    <option value="FAMILIAR"<?php echo $vivienda_familiar ?>>FAMILIAR</option>
		                    <option value="ARRENDADA"<?php echo $vivienda_arrendada ?>>ARRENDADA</option>
		                    <option value="PROPIA"<?php echo $vivienda_propia ?>>PROPIA</option>
						</select>
					</td>
				</tr>
				<tr>
		            <td><label class="negrita">Apellidos y Nombre del Arrendador:</label></td>
		            <td><input type="text" id="arrendador_nombre" name="arrendador_nombre" value="<?php echo ($fila1["arrendador_nombre"]) ?>" maxlength="40" disabled></td>
		            <td><label class="negrita">Tel&eacute;fono del Arrendador:</label></td>
		            <td><input type="text" id="arrendador_telefono" name="arrendador_telefono" value="<?php echo $fila1["arrendador_telefono"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		            <td><label  class="negrita">Ciudad Arrendador:</label></td>
		            <td>
		            	<select id="arrendador_ciudad" name="arrendador_ciudad" style="width:147px" disabled>
		            		<option value=""></option>
							<?php
								$queryDB = "select cod_municipio,municipio from ciudades order by municipio";
								$rz1 = sqlsrv_query($link, $queryDB);
								while ($fila = sqlsrv_fetch_array($rz1)) {
									if ($fila["cod_municipio"] == $fila1["arrendador_ciudad"] )
								    	$selected_municipio = " selected";
								    else
								    	$selected_municipio = "";
									
								    echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
								}
							?>
						</select>
					</td>
		            <td><label class="negrita">* Barrio:</label></td>
		            <td><input type="text" id="residencia_barrio" name="residencia_barrio" value="<?php echo ($fila1["residencia_barrio"]) ?>" maxlength="45" disabled></td>        
				</tr>
				<tr>
		        	<td><label class="negrita">* Direcci&oacute;n Residencia:</label></td>
		            <td colspan='3'><input class="camposBloqueados" size="65" type="text" id="direccion" name="direccion" value="<?php echo ($fila1["direccion"]) ?>" maxlength="60" ></td>
		            <td><label class="negrita">* Estrato:</label></td>
		            <td><input type="text" id="residencia_estrato" name="residencia_estrato" value="<?php echo $fila1["residencia_estrato"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="1" disabled></td>
		            <td><label class="negrita">Tiempo de Residencia Actual</label></td>
		            <td>
		            	<table><tbody>
						<tr>
		                	<td><label class="negrita">* A&ntilde;os</label></td>
		                    <td><input type="text" id="anios" style="width: 20px;" class="requerido" name="anios" value="<?php echo $fila1["anios"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="2" disabled></td>
		                    <td><label class="negrita">* Meses</label></td>
		                    <td><input type="text" id="meses" style="width: 20px;" class="requerido" name="meses" value="<?php echo $fila1["meses"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { if (parseInt(this.value) > 11) { alert('El valor ingresado no es valido'); this.value=''; return false; } }" maxlength="2" disabled></td>
		                </tr>
		                </tbody></table>
					</td>
				</tr>
				<tr>
		        	<td><label class="negrita">Tel&eacute;fono Residencia:</label></td>
		            <td><input type="text" id="tel_residencia" name="tel_residencia" value="<?php echo $fila1["tel_residencia"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>

		             <td><label class="negrita">* Tel&eacute;fono Celular:</label></td>
		            <td><input type="text" class="camposBloqueados" id="celular" name="celular" value="<?php echo $fila1["celular"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" ></td>

		            <td><label class="negrita">* Lugar env&iacute;o correspondencia:</label></td>
		            <td>
		            	<select name="lugar_correspondencia" id="lugar_correspondencia" style="width:147px" disabled>
							<?php
								$lugar_casa = "";
								$lugar_oficina = "";
								$lugar_mail = "";

								if ($fila1["lugar_correspondencia"] == "CASA")
									$lugar_casa = " selected";
								else if ($fila1["lugar_correspondencia"] == "OFICINA")
									$lugar_oficina = " selected";
								else if ($fila1["lugar_correspondencia"] == "EMAIL")
									$lugar_mail = " selected";
							?>		
							<option value=""></option>
		                    <option value="CASA"<?php echo $lugar_casa ?>>CASA</option>
		                    <option value="OFICINA"<?php echo $lugar_oficina ?>>OFICINA</option>
		                    <option value="EMAIL"<?php echo $lugar_mail ?>>CORREO ELECTR&Oacute;NICO</option>
						</select>
					</td>
		        	<td><label class="negrita">* Correo Electr&oacute;nico: </label></td>
		            <td><input class="camposBloqueados" type="text" id="email" name="email" value="<?php echo ($fila1["email"]) ?>" maxlength="50" ></td>
				</tr>
		        <tr>
		        	<td><label  class="negrita">Nombre de su E.P.S:</label></td>
		            <td><input type="text" id="eps" name="eps" value="<?php echo ($fila1["eps"]) ?>" maxlength="50" disabled></td>
		            <td>
		            	<table>
				            <tbody>
								<tr>
						            <td><label class="negrita">* Estatura:</label></td>
						            <td><input type="text" id="estatura" name="estatura" value="<?php echo $fila1["estatura"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" size="3" maxlength="3" disabled> Cm</td>
						        </tr>
						    </tbody>
						</table>
					</td>
					<td>
		            	<table>
				            <tbody>
								<tr>
						            <td><label class="negrita">* Peso:</label></td>
						            <td><input type="text" id="peso" name="peso" value="<?php echo $fila1["peso"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" size="3" maxlength="3" disabled>Kg</td>   
						        </tr>
						    </tbody>
						</table>
					</td>
					<td><label class="negrita">* No de personas a cargo:</label></td>
		            <td>
		            	<table>
				            <tbody>
								<tr>
						            <td><input type="text" id="personas_acargo" name="personas_acargo" value="<?php echo $fila1["personas_acargo"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" size="3" maxlength="3" disabled></td>        
						            <td><label class="negrita">* Adultos:</label></td>
						            <td><input type="text" id="personas_acargo_adultos" size="3" name="personas_acargo_adultos" value="<?php echo $fila1["personas_acargo_adultos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="3" disabled></td>        				            
						        </tr>
					        </tbody>
					    </table>
					</td>
					<td colspan="3">
		            	<table>
				            <tbody>
								<tr>
						            <td><label class="negrita">* Menores:</label></td>
						            <td><input type="text" id="personas_acargo_menores" size="3" name="personas_acargo_menores" value="<?php echo $fila1["personas_acargo_menores"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="3" disabled></td>		
						        </tr>
						    </tbody>
						</table>
					</td>
				</tr>
		        <tr>
		            <td><label class="negrita">Profesi&oacute;n:  </label></td>
		            <td><input type="text" id="profesion" name="profesion" value="<?php echo ($fila1["profesion"]) ?>" maxlength="45" disabled></td>
		            <td><label class="negrita">* Nivel de Estudios: </label></td>
		            <td>
		            	<select name="nivel_estudios" id="nivel_estudios" style="width:147px" disabled>
							<?php
								$estudios_primaria = "";
								$estudios_bachiller = "";
								$estudios_tecnico = "";
								$estudios_tecnologo = "";
								$estudios_universitario = "";
								$estudios_especializacion = "";
								$estudios_maestria = "";
								$estudios_doctorado = "";

								if ($fila1["nivel_estudios"] == "PRIMARIA")
									$estudios_primaria = " selected";
								else if ($fila1["nivel_estudios"] == "BACHILLER")
									$estudios_bachiller = " selected";
								else if ($fila1["nivel_estudios"] == "TECNICO")
									$estudios_tecnico = " selected";
								else if ($fila1["nivel_estudios"] == "TECNOLOGO")
									$estudios_tecnologo = " selected";
								else if ($fila1["nivel_estudios"] == "UNIVERSITARIO")
									$estudios_universitario = " selected";
								else if ($fila1["nivel_estudios"] == "ESPECIALIZACION")
									$estudios_especializacion = " selected";
								else if ($fila1["nivel_estudios"] == "MAESTRIA")
									$estudios_maestria = " selected";
								else if ($fila1["nivel_estudios"] == "DOCTORADO")
									$estudios_doctorado = " selected";
							?>
							<option value=""></option>
		                    <option value="PRIMARIA"<?php echo $estudios_primaria ?>>PRIMARIA</option>
		                    <option value="BACHILLER"<?php echo $estudios_bachiller ?>>BACHILLER</option>
		                    <option value="TECNICO"<?php echo $estudios_tecnico ?>>TECNICO</option>
		                    <option value="TECNOLOGO"<?php echo $estudios_tecnologo ?>>TECNOLOGO</option>
		                    <option value="UNIVERSITARIO"<?php echo $estudios_universitario ?>>UNIVERSITARIO</option>
		                    <option value="ESPECIALIZACION"<?php echo $estudios_especializacion ?>>ESPECIALIZACION</option>
		                    <option value="MAESTRIA"<?php echo $estudios_maestria ?>>MAESTRIA</option>
		                    <option value="DOCTORADO"<?php echo $estudios_doctorado ?>>DOCTORADO</option>
						</select>
					</td>
				</tr>
		        </table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR")
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato, '1') ;document.formato.edicion_habilitada.value='1'; document.formato.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato, '0'); document.formato.edicion_habilitada.value='0'; document.formato.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Datos del Conyugue</h3>
	<div id="section2">
		<form name="formato2" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma2()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="1">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
			    <div class="box1 clearfix">
		    	<table>
			    <tr>
		    		<td><label class="negrita">Primer Nombre:</label></td>
		        	<td><input type="text" id="nombre_conyugue" name="nombre_conyugue" value="<?php echo ($fila1["nombre_conyugue"]) ?>" maxlength="20" disabled></td>
			        <td><label class="negrita">Segundo Nombre:</label></td>
		    	    <td><input type="text" id="conyugue_nombre_2" name="conyugue_nombre_2" value="<?php echo ($fila1["conyugue_nombre_2"]) ?>" maxlength="20" disabled></td>
		        	<td><label class="negrita">Primer Apellido:</label></td>
			        <td><input type="text" id="conyugue_apellido_1" name="conyugue_apellido_1" value="<?php echo ($fila1["conyugue_apellido_1"]) ?>" maxlength="20" disabled></td>
		    	    <td><label class="negrita">Segundo Apellido:</label></td>
		        	<td><input type="text" id="conyugue_apellido_2" name="conyugue_apellido_2" value="<?php echo ($fila1["conyugue_apellido_2"]) ?>" maxlength="20" disabled></td>
				</tr>
		    	<tr>
		    		<td><label class="negrita">Tipo de Identificaci&oacute;n:</label></td>                    
			        <td><select name="conyugue_tipo_documento" id="conyugue_tipo_documento" style="width:147px" disabled>
<?php

echo "<option value=''></option>";

$selected = $fila1['conyugue_tipo_documento']=='CEDULA'?'selected':'';
echo "<option value='CEDULA' $selected>CEDULA</option>";

$selected = $fila1['conyugue_tipo_documento']=='REGISTRO CIVIL'?'selected':'';
echo "<option value='REGISTRO CIVIL' $selected>REGISTRO CIVIL</option>";

$selected = $fila1['conyugue_tipo_documento']=='TARJETA IDENTIDAD'?'selected':'';
echo "<option value='TARJETA IDENTIDAD' $selected>TARJETA IDENTIDAD</option>";

$selected = $fila1['conyugue_tipo_documento']=='CEDULA EXTRANGERIA'?'selected':'';
echo "<option value='CEDULA EXTRANGERIA' $selected>CEDULA EXTRANJERIA</option>";

?>
						</select>
					</td>
					<td><label class="negrita">N&uacute;mero:</label></td>
		            <td><input type="text" id="cedula_conyugue" name="cedula_conyugue" value="<?php echo $fila1["cedula_conyugue"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		            <td><label class="negrita">Fecha de Expedici&oacute;n:</label></td>
		            <td><input type="text" name="conyugue_fecha_expedicion" value="<?php echo $fila1["conyugue_fecha_expedicion"] ?>" id="conyugue_fecha_expedicion" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
		            <td><label class="negrita">Lugar de Expedici&oacute;n:</label></td>
		            <td><select id="conyugue_lugar_expedicion" name="conyugue_lugar_expedicion" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$queryDB = "select cod_municipio,municipio from ciudades order by municipio";

$rz2 = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rz2))
{
	if ($fila["cod_municipio"] == $fila1["conyugue_lugar_expedicion"] )
    	$selected_municipio = " selected";
    else
    	$selected_municipio = "";
	
    echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
}

?>
						</select>
					</td>
				</tr>
		        <tr>
		        	<td><label class="negrita">Fecha de Nacimiento:</label></td>
		            <td><input type="text" id="conyugue_fecha_nacimiento" name="conyugue_fecha_nacimiento" value="<?php echo $fila1["conyugue_fecha_nacimiento"] ?>" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
		            <td><label class="negrita">Sexo:</label></td>
		            <td><select name="conyugue_sexo" id="conyugue_sexo" style="width:147px" disabled>
<?php

$sexo_m = "";
$sexo_f = "";

if ($fila1["conyugue_sexo"] == "M")
	$sexo_m = " selected";
else if ($fila1["conyugue_sexo"] == "F")
	$sexo_f = " selected";

?>
							<option value=""></option>
		                    <option value="M"<?php echo $sexo_m ?>>M</option>
		                    <option value="F"<?php echo $sexo_f ?>>F</option>
						</select>
					</td>
		            <td><label class="negrita">Lugar de Nacimiento:</label></td>
		            <td><select id="conyugue_lugar_nacimiento" name="conyugue_lugar_nacimiento" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$queryDB = "select cod_municipio,municipio from ciudades order by municipio";

$rz1 = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rz1))
{
	if ($fila["cod_municipio"] == $fila1["conyugue_lugar_nacimiento"] )
    	$selected_municipio = " selected";
	else
    	$selected_municipio = "";
	
    echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
}

?>
						</select>
					</td>
		            <td><label class="negrita">Tel&eacute;fono Celular:</label></td>
		            <td><input type="text" name="conyugue_celular" id="conyugue_celular" value="<?php echo $fila1["conyugue_celular"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
				</tr>   
				<tr>
		            <td><label class="negrita">Lugar donde Trabaja:</label></td>
		            <td><input type="text" name="conyugue_nombre_empresa" id="conyugue_nombre_empresa" value="<?php echo ($fila1["conyugue_nombre_empresa"]) ?>" maxlength="50" disabled></td>
		        	<td><label class="negrita">Cargo:</label></td>
		            <td><input type="text" name="conyugue_cargo" id="conyugue_cargo"value="<?php echo ($fila1["conyugue_cargo"]) ?>" maxlength="50" disabled></td>
		            <td><label class="negrita">Fecha de Vinculaci&oacute;n:</label></td>
		            <td><input type="text" name="conyugue_fecha_vinculacion" id="conyugue_fecha_vinculacion" value="<?php echo $fila1["conyugue_fecha_vinculacion"] ?>" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
					<td><label class="negrita">Total de Ingresos:</label></td>
					<td><input type="text" name="conyugue_total_ingresos" id="conyugue_total_ingresos" value="<?php echo $fila1["conyugue_total_ingresos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="10" disabled></td>
				</tr>   
				<tr>
					<td><label class="negrita">Ocupaci&oacute;n:</label></td>                        
				    <td><select name="conyugue_ocupacion" id="conyugue_ocupacion" style="width:147px" disabled>
<?php

echo "<option value=''></option>";

$selected = $fila1['conyugue_ocupacion']=='EMPLEADO'?'selected':'';
echo "<option value='EMPLEADO' $selected>EMPLEADO</option>";

$selected = $fila1['conyugue_ocupacion']=='INDEPENDIENTE'?'selected':'';
echo "<option value='INDEPENDIENTE' $selected>INDEPENDIENTE</option>";

$selected = $fila1['conyugue_ocupacion']=='PENSIONADO'?'selected':'';
echo "<option value='PENSIONADO' $selected>PENSIONADO</option>";

$selected = $fila1['conyugue_ocupacion']=='AMA DE CASA'?'selected':'';
echo "<option value='AMA DE CASA' $selected>AMA DE CASA</option>";

$selected = $fila1['conyugue_ocupacion']=='ESTUDIANTE'?'selected':'';
echo "<option value='ESTUDIANTE' $selected>ESTUDIANTE</option>";

$selected = $fila1['conyugue_ocupacion']=='RENTISTA CAPITAL'?'selected':'';
echo "<option value='RENTISTA CAPITAL' $selected>RENTISTA CAPITAL</option>";

?>
						</select>
					</td>
		            <td><label class="negrita">Dependencia Econ&oacute;mica:</label></td>                        
		            <td><select name="conyugue_dependencia" id="conyugue_dependencia" style="width:147px" disabled>
<?php

echo "<option value=''></option>";

$selected = $fila1['conyugue_dependencia']=='SI'?'selected':'';
echo "<option value='SI' $selected>SI</option>";

$selected = $fila1['conyugue_dependencia']=='NO'?'selected':'';
echo "<option value='NO' $selected>NO</option>";

?>
						</select>
					</td>
		            <td><label class="negrita">Nivel de Estudios: </label></td>
		            <td><select name="conyugue_nivel_estudios" id="conyugue_nivel_estudios" style="width:147px" disabled>
<?php

$estudios_primaria = "";
$estudios_bachiller = "";
$estudios_tecnico = "";
$estudios_tecnologo = "";
$estudios_universitario = "";
$estudios_especializacion = "";
$estudios_maestria = "";
$estudios_doctorado = "";

if ($fila1["conyugue_nivel_estudios"] == "PRIMARIA")
	$estudios_primaria = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "BACHILLER")
	$estudios_bachiller = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "TECNICO")
	$estudios_tecnico = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "TECNOLOGO")
	$estudios_tecnologo = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "UNIVERSITARIO")
	$estudios_universitario = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "ESPECIALIZACION")
	$estudios_especializacion = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "MAESTRIA")
	$estudios_maestria = " selected";
else if ($fila1["conyugue_nivel_estudios"] == "DOCTORADO")
	$estudios_doctorado = " selected";

?>
							<option value=""></option>
		                    <option value="PRIMARIA"<?php echo $estudios_primaria ?>>PRIMARIA</option>
		                    <option value="BACHILLER"<?php echo $estudios_bachiller ?>>BACHILLER</option>
		                    <option value="TECNICO"<?php echo $estudios_tecnico ?>>TECNICO</option>
		                    <option value="TECNOLOGO"<?php echo $estudios_tecnologo ?>>TECNOLOGO</option>
		                    <option value="UNIVERSITARIO"<?php echo $estudios_universitario ?>>UNIVERSITARIO</option>
		                    <option value="ESPECIALIZACION"<?php echo $estudios_especializacion ?>>ESPECIALIZACION</option>
		                    <option value="MAESTRIA"<?php echo $estudios_maestria ?>>MAESTRIA</option>
		                    <option value="DOCTORADO"<?php echo $estudios_doctorado ?>>DOCTORADO</option>
						</select>
					</td>
				</tr>
<!--		        <tr>
		            <td><label class="negrita">Direccion Lugar de Trabajo:</label></td>
		            <td><input type="text" name="conyugue_direccion_trabajo" id="conyugue_direccion_trabajo" value="<?php echo ($fila1["conyugue_direccion_trabajo"]) ?>"></td>
		            <td><label class="negrita">Telefono empresa:</label></td>
		            <td><input type="text" name="conyugue_telefono_trabajo" id="conyugue_telefono_trabajo" value="<?php echo $fila1["conyugue_telefono_trabajo"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		            <td><label class="negrita">Ciudad:</label></td>
		            <td><input type="text" name="conyugue_ciudad_trabajo" id="conyugue_ciudad_trabajo" value="<?php echo $fila1["conyugue_ciudad_trabajo"] ?>"></td>
				</tr>-->
		        </table>
				</div>
			</td>
		</tr>
		</table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato2.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato2, '1'); document.formato2.edicion_habilitada.value='1'; document.formato2.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato2, '0'); document.formato2.edicion_habilitada.value='0'; document.formato2.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Actividad Laboral</h3>
	<div id="section3">
		<form name="formato3" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma3()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="2">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
				<tr>
		        	<td><label class="negrita">* Ocupaci&oacute;n:</label></td>
<!--		            <td>
		            	<table><tbody>
						<tr>-->
		                	<td><select name="ocupacion" id="ocupacion" style="width:147px" disabled>
<?php

$ocupacion_empleado = "";
$ocupacion_empleado_socio = "";
$ocupacion_independiente = "";
$ocupacion_hogar = "";
$ocupacion_pensionado = "";
$ocupacion_estudiante = "";
$ocupacion_rentista = "";
$ocupacion_taxista = "";
$ocupacion_transportador = "";
$ocupacion_ninguna = "";

if ($fila1["ocupacion"] == 1)
	$ocupacion_empleado = " selected";
else if ($fila1["ocupacion"] == 2)
	$ocupacion_empleado_socio = " selected";
else if ($fila1["ocupacion"] == 3)
	$ocupacion_independiente = " selected";
else if ($fila1["ocupacion"] == 4)
	$ocupacion_hogar = " selected";
else if ($fila1["ocupacion"] == 5)
	$ocupacion_jubilado = " selected";
else if ($fila1["ocupacion"] == 6)
	$ocupacion_estudiante = " selected";
else if ($fila1["ocupacion"] == 7)
	$ocupacion_rentista = " selected";
else if ($fila1["ocupacion"] == 8)
	$ocupacion_taxista = " selected";
else if ($fila1["ocupacion"] == 9)
	$ocupacion_transportador = " selected";
else if ($fila1["ocupacion"] == 99)
	$ocupacion_ninguna = " selected";

?>
									<option value=""></option>
		                            <option value="1"<?php echo $ocupacion_empleado ?>>EMPLEADO</option>
		                            <!--<option value="2"<?php echo $ocupacion_empleado_socio ?>>EMPLEADO SOCIO</option>-->
		                            <option value="3"<?php echo $ocupacion_independiente ?>>INDEPENDIENTE</option>
		                            <option value="5"<?php echo $ocupacion_jubilado ?>>PENSIONADO</option>
		                            <option value="4"<?php echo $ocupacion_hogar ?>>AMA DE CASA</option>
		                            <option value="6"<?php echo $ocupacion_estudiante ?>>ESTUDIANTE</option>
		                            <option value="7"<?php echo $ocupacion_rentista ?>>RENTISTA CAPITAL</option>
		                            <!--<option value="8"<?php echo $ocupacion_taxista ?>>TAXISTA</option>
		                            <option value="9"<?php echo $ocupacion_transportador ?>>TRANSPORTADOR</option>
		                            <option value="99"<?php echo $ocupacion_ninguna ?>>NINGUNA</option>-->
		                        </select>&nbsp;
							</td>
<!--		                    <td><select name="lugar_ocupacion" id="lugar_ocupacion" style="width:80px;">-->
<?php

/*$pagaduria_cali = "";
$pagaduria_tumaco = "";
$pagaduria_narino = "";
$pagaduria_pasto = "";
$pagaduria_jamundi = "";
$pagaduria_valle = "";
$pagaduria_buga = "";
$pagaduria_cauca = "";
$pagaduria_tolima = "";
$pagaduria_ibague = "";
$pagaduria_yumbo = "";
$pagaduria_huila = "";
$pagaduria_colpension = "";
$pagaduria_fiduprevisora = "";
$pagaduria_fopep = "";
$pagaduria_positiva = "";

$pagaduria_otro = "";

if ($fila1["lugar_ocupacion"] == "BUGA")
	$pagaduria_buga = " selected";
else if ($fila1["lugar_ocupacion"] == "CALI")
	$pagaduria_cali = " selected";
else if ($fila1["lugar_ocupacion"] == "CAUCA")
	$pagaduria_cauca = " selected";
else if ($fila1["lugar_ocupacion"] == "COLPENSION")
	$pagaduria_colpension = " selected";
else if ($fila1["lugar_ocupacion"] == "CONSORCIO FIDUCIARIO")
	$pagaduria_consorciofiduciario = " selected";
else if ($fila1["lugar_ocupacion"] == "FIDUPREVISORA")
	$pagaduria_fiduprevisora = " selected";
else if ($fila1["lugar_ocupacion"] == "FOPEP")
	$pagaduria_fopep = " selected";
else if ($fila1["lugar_ocupacion"] == "HUILA")
	$pagaduria_huila = " selected";
else if ($fila1["lugar_ocupacion"] == "IBAGUE")
	$pagaduria_ibague = " selected";
else if ($fila1["lugar_ocupacion"] == "JAMUNDI")
	$pagaduria_jamundi = " selected";
else if ($fila1["lugar_ocupacion"] == "NARINO")
	$pagaduria_narino = " selected";
else if ($fila1["lugar_ocupacion"] == "PASTO")
	$pagaduria_pasto = " selected";
else if ($fila1["lugar_ocupacion"] == "POSITIVA")
	$pagaduria_positiva = " selected";
else if ($fila1["lugar_ocupacion"] == "TOLIMA")
	$pagaduria_tolima = " selected";
else if ($fila1["lugar_ocupacion"] == "TUMACO")
	$pagaduria_tumaco = " selected";
else if ($fila1["lugar_ocupacion"] == "VALLE")
	$pagaduria_valle = " selected";
else if ($fila1["lugar_ocupacion"] == "YUMBO")
	$pagaduria_yumbo = " selected";
else
	$pagaduria_otro = " selected";*/
	
?>
<!--		                            <option value=""></option>
		                            <option value="BUGA"<?php //echo $pagaduria_buga ?>>BUGA</option>
		                            <option value="CALI"<?php //echo $pagaduria_cali ?>>CALI</option>
		                            <option value="CAUCA"<?php //echo $pagaduria_cauca ?>>CAUCA</option>
		                            <option value="COLPENSION"<?php //echo $pagaduria_colpension ?>>COLPENSION</option>
		                            <option value="CONSORCIO FIDUCIARIO"<?php //echo $pagaduria_consorciofiduciario ?>>CONSORCIO FIDUCIARIO</option>
		                            <option value="FIDUPREVISORA"<?php //echo $pagaduria_fiduprevisora ?>>FIDUPREVISORA</option>
		                            <option value="FOPEP"<?php //echo $pagaduria_fopep ?>>FOPEP</option>
		                            <option value="HUILA"<?php //echo $pagaduria_huila ?>>HUILA</option>
		                            <option value="IBAGUE"<?php //echo $pagaduria_ibague ?>>IBAGUE</option>
		                            <option value="JAMUNDI"<?php //echo $pagaduria_jamundi ?>>JAMUNDI</option>
		                            <option value="NARINO"<?php //echo $pagaduria_narino ?>>NARI&Ntilde;O</option>
		                            <option value="PASTO"<?php //echo $pagaduria_pasto ?>>PASTO</option>
		                            <option value="POSITIVA"<?php //echo $pagaduria_positiva ?>>POSITIVA</option>
		                            <option value="TOLIMA"<?php //echo $pagaduria_tolima ?>>TOLIMA</option>
		                            <option value="TUMACO"<?php //echo $pagaduria_tumaco ?>>TUMACO</option>
		                            <option value="VALLE"<?php //echo $pagaduria_valle ?>>VALLE</option>
		                            <option value="YUMBO"<?php //echo $pagaduria_yumbo ?>>YUMBO</option>
		                            <option value="OTRO"<?php //echo $pagaduria_otro ?>>OTRO</option>
		                        </select>
							</td>
						</tr>
		                </tbody></table>
					</td>-->
		        	<td><label class="negrita">* Declara Renta:</label></td>
		            <td><select name="declara_renta" id="declara_renta" style="width:147px" disabled>
							<option value=""></option>
<?php

$declara_renta_si = "";
$declara_renta_no = "";

if ($fila1["declara_renta"] == "SI")
	$declara_renta_si = " selected";
else if ($fila1["declara_renta"] == "NO")
	$declara_renta_no = " selected";

?>
							<option value="SI"<?php echo $declara_renta_si ?>>SI</option>
		                    <option value="NO"<?php echo $declara_renta_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">* Las decisiones a su Cargo Influyen en<br>la Pol&iacute;tica o Impactan en la Sociedad:</label></td>
		            <td><select name="funcionario_publico" id="funcionario_publico" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$funcionario_publico_si = "";
$funcionario_publico_no = "";

if ($fila1["funcionario_publico"] == "SI")
	$funcionario_publico_si = " selected";
else if ($fila1["funcionario_publico"] == "NO")
	$funcionario_publico_no = " selected";

?>
							<option value="SI"<?php echo $funcionario_publico_si ?>>SI</option>
		                    <option value="NO"<?php echo $funcionario_publico_no ?>>NO</option>
						</select>
					</td>
				</tr>
		        <tr>
		            <td><label class="negrita">* Usted Maneja Recursos P&uacute;blicos?</label></td>
		            <td><select name="recursos_publicos" id="recursos_publicos" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$recursos_si = "";
$recursos_no = "";

if ($fila1["recursos_publicos"] == "SI")
	$recursos_si = " selected";
else if ($fila1["recursos_publicos"] == "NO")
	$recursos_no = " selected";

?>
							<option value="SI"<?php echo $recursos_si ?>>SI</option>
		                    <option value="NO"<?php echo $recursos_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">* La Sociedad lo Identifica como<br>Personaje P&uacute;blico:</label></td>
		            <td><select name="personaje_publico" id="personaje_publico" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$personaje_publico_si = "";
$personaje_publico_no = "";

if ($fila1["personaje_publico"] == "SI")
	$personaje_publico_si = " selected";
else if ($fila1["personaje_publico"] == "NO")
	$personaje_publico_no = " selected";

?>
							<option value="SI"<?php echo $personaje_publico_si ?>>SI</option>
		                    <option value="NO"<?php echo $personaje_publico_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">* Actividad Econ&oacute;mica Principal:</label></td>
					<td><input type="text" name="actividad_economica_principal" id="actividad_economica_principal" value="<?php echo ($fila1["actividad_economica_principal"]) ?>" maxlength="50" disabled></td>
				</tr>
		        <tr>
		            <td><label class="negrita">Nombre de la Empresa donde Trabaja:</label></td>
					<td><input type="text" name="nombre_empresa" id="nombre_empresa" value="<?php echo ($fila1["nombre_empresa"]) ?>" maxlength="50" disabled></td>
		            <td><label class="negrita">Cargo:</label></td>
		            <td><input type="text" name="cargo" id="cargo"value="<?php echo ($fila1["cargo"]) ?>" maxlength="50" disabled></td>
		            <td><label class="negrita">Fecha de Vinculaci&oacute;n:</label></td>
		            <td><input type="text" name="fecha_vinculacion" id="fecha_vinculacion" value="<?php echo $fila1["fecha_vinculacion"] ?>" id="fecha_vinculacion" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" style="width:147px" disabled></td>
				</tr>
		        <tr>
		            <td><label class="negrita">Direcci&oacute;n Lugar de Trabajo:</label></td>
		            <td><input type="text" name="direccion_trabajo" id="direccion_trabajo" value="<?php echo ($fila1["direccion_trabajo"]) ?>" maxlength="50" disabled></td>
		            <td><label class="negrita">Ciudad:</label></td>
		            <td><input type="text" name="ciudad_trabajo" id="ciudad_trabajo" value="<?php echo ($fila1["ciudad_trabajo"]) ?>" maxlength="50" disabled></td>
		            <td><label class="negrita">NIT de la Empresa:</label></td>
		            <td><input type="text" name="nit_empresa" id="nit_empresa" value="<?php echo ($fila1["nit_empresa"]) ?>" maxlength="20" disabled></td>
		        </tr>
		        <tr>
		            <td><label class="negrita">Tel&eacute;fono de Trabajo:</label></td>
		            <td><input type="text" name="telefono_trabajo" id="telefono_trabajo" value="<?php echo $fila1["telefono_trabajo"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		            <td><label class="negrita">Extensi&oacute;n:</label></td>
		            <td><input type="text" name="extension" id="extension" value="<?php echo $fila1["extension"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="10" disabled></td>
		            <td><label class="negrita">Tipo de Empresa:</label></td>
		            <td><select name="tipo_empresa" id="tipo_empresa" style="width:147px" disabled>
<?php

$tipo_empresa_publica = "";
$tipo_empresa_privada = "";
$tipo_empresa_mixta = "";

if ($fila1["tipo_empresa"] == "PUBLICA")
	$tipo_empresa_publica = " selected";
else if ($fila1["tipo_empresa"] == "PRIVADA")
	$tipo_empresa_privada = " selected";
else if ($fila1["tipo_empresa"] == "MIXTA")
	$tipo_empresa_mixta = " selected";

?>
							<option value=""></option>
		                    <option value="PUBLICA"<?php echo $tipo_empresa_publica ?>>PUBLICA</option>
		                    <option value="PRIVADA"<?php echo $tipo_empresa_privada ?>>PRIVADA</option>
		                    <option value="MIXTA"<?php echo $tipo_empresa_mixta ?>>MIXTA</option>
						</select>
					</td>
		        </tr>
				<tr>
		            <td><label class="negrita">Actividad Econ&oacute;mica:</label></td>
		            <td><select name="actividad_economica_empresa" id="actividad_economica_empresa" style="width:147px" disabled>
<?php

$actividad_economica_empresa_servicios = "";
$actividad_economica_empresa_comercial = "";
$actividad_economica_empresa_construccion = "";
$actividad_economica_empresa_industrial = "";
$actividad_economica_empresa_agropecuaria = "";
$actividad_economica_empresa_otra = "";

if ($fila1["actividad_economica_empresa"] == "SERVICIOS")
	$actividad_economica_empresa_servicios = " selected";
else if ($fila1["actividad_economica_empresa"] == "COMERCIAL")
	$actividad_economica_empresa_comercial = " selected";
else if ($fila1["actividad_economica_empresa"] == "CONSTRUCCION")
	$actividad_economica_empresa_construccion = " selected";
else if ($fila1["actividad_economica_empresa"] == "INDUSTRIAL")
	$actividad_economica_empresa_industrial = " selected";
else if ($fila1["actividad_economica_empresa"] == "AGROPECUARIA")
	$actividad_economica_empresa_agropecuaria = " selected";
else if ($fila1["actividad_economica_empresa"] == "OTRA")
	$actividad_economica_empresa_otra = " selected";

?>
							<option value=""></option>
		                    <option value="SERVICIOS"<?php echo $actividad_economica_empresa_servicios ?>>SERVICIOS</option>
		                    <option value="COMERCIAL"<?php echo $actividad_economica_empresa_comercial ?>>COMERCIAL</option>
		                    <option value="CONSTRUCCION"<?php echo $actividad_economica_empresa_construccion ?>>CONSTRUCCION</option>
		                    <option value="INDUSTRIAL"<?php echo $actividad_economica_empresa_industrial ?>>INDUSTRIAL</option>
		                    <option value="AGROPECUARIA"<?php echo $actividad_economica_empresa_agropecuaria ?>>AGROPECUARIA</option>
		                    <option value="OTRA"<?php echo $actividad_economica_empresa_otra ?>>OTRA</option>
						</select>
					</td>
					<td><label class="negrita">Tipo de Contrato:</label></td>
		            <td><select name="tipo_contrato" id="tipo_contrato" style="width:147px" disabled>
					<?php
						$tipo_contrato_1 = "";
						$tipo_contrato_2 = "";
						$tipo_contrato_3 = "";
						$tipo_contrato_4 = "";
						$tipo_contrato_5 = "";

                            if ($fila1["tipo_contrato"] == "1")
                                $tipo_contrato_1 = " selected";
                            else if ($fila1["tipo_contrato"] == "2")
                                $tipo_contrato_2 = " selected";
                            else if ($fila1["tipo_contrato"] == "3")
                                $tipo_contrato_3 = " selected";
                            else if ($fila1["tipo_contrato"] == "4")
                                $tipo_contrato_4 = " selected";
                            else if ($fila1["tipo_contrato"] == "5")
                                $tipo_contrato_5 = " selected";
                            ?>
                            <option value=""></option>
                            <option value="1"<?php echo $tipo_contrato_1 ?>>INDEFINIDO</option>
                            <!--<option value="2"<?php echo $tipo_contrato_2 ?>>T&Eacute;RMINO TEMPORAL</option>-->
                            <option value="2"<?php echo $tipo_contrato_2 ?>>CONTRATISTA</option>
                            <option value="3"<?php echo $tipo_contrato_3 ?>>FIJO</option>
Contraer

 
						</select>
					</td>
<!--		            <td><label class="negrita">Independiente o empleado Socio:</label></td>
		            <td><input type="text" id="actividad" name="actividad" value="<?php echo $fila1["actividad"] ?>"></td>-->
				</tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato3.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato3, '1'); document.formato3.edicion_habilitada.value='1'; document.formato3.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato3, '0'); document.formato3.edicion_habilitada.value='0'; document.formato3.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
	</tr>
		</table>
		</form>
	</div>
	<h3>Informaci&oacute;n Financiera</h3>
	<div id="section4">
		<form name="formato4" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma4()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="3">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
		        <table>
				<tr>
					<td>
		            	<table><tbody>
						<tr>
				            <td><label class="negrita">* Ingresos Laborales:</label></td>
				            <td><input type="text" name="ingresos_laborales" id="ingresos_laborales" value="<?php echo $fila1["ingresos_laborales"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarIngresos() }" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Honorarios y Comisiones:</label></td>
				            <td><input type="text" name="honorarios_comisiones" id="honorarios_comisiones" value="<?php echo $fila1["honorarios_comisiones"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarIngresos() }" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Otros Ingresos:</label></td>
				            <td><input type="text" name="otros_ingresos" id="otros_ingresos" value="<?php echo $fila1["otros_ingresos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarIngresos() }" maxlength="10" disabled></td>
				            <td><label class="negrita">Cu&aacute;les?</label></td>
				            <td><input type="text" name="detalle_ingresos" id="detalle_ingresos" value="<?php echo ($fila1["detalle_ingresos"]) ?>" maxlength="50" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Total Ingresos:</label></td>
				            <td><input type="text" name="total_ingresos" id="total_ingresos" value="<?php echo $fila1["total_ingresos"] ?>" readonly disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">* Activos Fijos<br>(Ahorros, Inversiones, Propiedades, Otros)</label></td>
				            <td><input type="text" name="activos_fijos" id="activos_fijos" value="<?php echo $fila1["activos_fijos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarActivos() }" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Total Activos</label></td>
				            <td><input type="text" name="total_activos" id="total_activos" value="<?php echo $fila1["total_activos"] ?>" readonly disabled></td>
						</tr>
		                </tbody></table>
					</td>
					<td>
						<table><tbody>
						<tr>
				            <td><label class="negrita">* Gastos Familiares:</label></td>
				            <td><input type="text" name="gastos_familiares" id="gastos_familiares" value="<?php echo $fila1["gastos_familiares"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Arrendamiento o cuota vivienda:</label></td>
				            <td><input type="text" name="valor_arrendo" id="valor_arrendo" value="<?php echo $fila1["valor_arrendo"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">* Pasivos Financieros<br>(Deudas Financieras)</label></td>
				            <td><input type="text" name="pasivos_financieros" id="pasivos_financieros" value="<?php echo $fila1["pasivos_financieros"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarPasivos() }" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Pasivos Corrientes<br>(Deudas con Terceros)</label></td>
				            <td><input type="text" name="pasivos_corrientes" id="pasivos_corrientes" value="<?php echo $fila1["pasivos_corrientes"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarPasivos() }" maxlength="10" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Otros Pasivos</label></td>
				            <td><input type="text" name="otros_pasivos" id="otros_pasivos" value="<?php echo $fila1["otros_pasivos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false} else { SumarPasivos() }" maxlength="10" disabled></td>
				            <td><label class="negrita">Cu&aacute;les?</label></td>
				            <td><input type="text" name="detalle_otros_pasivos" id="detalle_otros_pasivos" value="<?php echo ($fila1["detalle_otros_pasivos"]) ?>" maxlength="50" disabled></td>
						</tr>
						<tr>
				            <td><label class="negrita">Total Pasivos:</label></td>
				            <td><input type="text" name="total_pasivos" id="total_pasivos" value="<?php echo $fila1["total_pasivos"] ?>" readonly disabled></td>
						</tr>
						</tbody></table>
					</td>
<!--		        <tr>
		            <td><label class="negrita">* Total Egresos:</label></td>
		            <td><input type="text" name="total_egresos" id="total_egresos" value="<?php echo $fila1["total_egresos"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		        </tr>
		        <tr>
		            <td><label class="negrita">Cuanto paga de hipoteca?:</label></td>
		            <td><input type="text" name="valor_hipoteca" id="valor_hipoteca" value="<?php echo $fila1["valor_hipoteca"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		        </tr>
		        <tr>
		            <td><label class="negrita">Valor comercial de su vivienda:</label></td>
		            <td><input type="text" name="valor_vivienda" id="valor_vivienda" value="<?php echo $fila1["valor_vivienda"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		            <td><label class="negrita">Saldo credito hipotecario:</label></td>
		            <td><input type="text" name="saldo_credito" id="saldo_credito" value="<?php echo $fila1["saldo_credito"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}"></td>
		            <td><label class="negrita">Quiere comprar Vivienda</label></td>
		            <td><select name="compra_vivienda" id="compra_vivienda">
<?php

/*$compra_si = "";
$compra_no = "";

if ($fila1["compra_vivienda"] == "SI")
	$compra_si = " selected";
else if ($fila1["compra_vivienda"] == "NO")
	$compra_no = " selected";*/

?>
<!--							<option value=""></option>
		                    <option value="SI"<?php //echo $compra_si ?>>SI</option>
		                    <option value="NO"<?php //echo $compra_no ?>>NO</option>
						</select>
					</td>
		        </tr>
		        <tr>
		            <td><label class="negrita">Donde le gustaria vivir?</label></td>
		            <td><input type="text" name="lugar_vivienda" id="lugar_vivienda" value="<?php echo $fila1["lugar_vivienda"] ?>"></td>
		            <td><label class="negrita">Interesa cupo extra libranza</label></td>
		            <td><select name="extra_libranza" id="extra_libranza">-->
<?php

/*$extra_si = "";
$extra_no = "";

if ($fila1["extra_libranza"] == "SI")
	$extra_si = " selected";
else if ($fila1["extra_libranza"] == "NO")
	$extra_no = " selected";*/

?>
<!--							<option value=""></option>
		                    <option value="SI"<?php //echo $extra_si ?>>SI</option>
		                    <option value="NO"<?php //echo $extra_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">Que le interesa mas?</label></td>
		            <td><select name="otro" id="otro">-->
<?php

/*$interesa_vivienda = "";
$interesa_vehiculo = "";
$interesa_viaje = "";
$interesa_educacion = "";
$interesa_tcredito = "";

if ($fila1["otro"] == "VIVIENDA")
	$interesa_vivienda = " selected";
else if ($fila1["otro"] == "VEHICULO")
	$interesa_vehiculo = " selected";
else if ($fila1["otro"] == "VIAJE")
	$interesa_viaje = " selected";
else if ($fila1["otro"] == "EDUCACION")
	$interesa_educacion = " selected";
else if ($fila1["otro"] == "T.CREDITO")
	$interesa_tcredito = " selected";*/

?>
<!--							<option value=""></option>
		                    <option value="VIVIENDA"<?php //echo $interesa_vivienda ?>>VIVIENDA</option>
		                    <option value="VEHICULO"<?php //echo $interesa_vehiculo ?>>VEHICULO</option>
		                    <option value="VIAJE"<?php //echo $interesa_viaje ?>>VIAJE</option>
		                    <option value="EDUCACION"<?php //echo $interesa_educacion ?>>EDUCACION</option>
		                    <option value="T.CREDITO"<?php //echo $interesa_tcredito ?>>T.CREDITO</option>
						</select>
					</td>
				</tr>-->
				</tr>
		        </table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato4.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato4, '1'); document.formato4.edicion_habilitada.value='1'; document.formato4.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato4, '0'); document.formato4.edicion_habilitada.value='0'; document.formato4.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Referencias</h3>
	<div id="section5">
		<form name="formato5" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma5()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="4">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
		        <tr>
		            <td><label class="negrita">* FAMILIAR: Nombre y Apellidos (que no viva con usted):</label></td>
		            <td><input type="text" size="40" name="nombre_familiar" id="nombre_familiar" value="<?php echo ($fila1["nombre_familiar"]) ?>" maxlength="40" disabled></td>
<?php

switch ($fila1["parentesco_familiar"])
{
	case "ABUELO(A)": $parentescof_ab = " selected"; break;
	case "COMPANERO(A)": $parentescof_ca = " selected"; break;
	case "CONYUGE": $parentescof_cy = " selected"; break;
	case "CONYUGE SOLIDARIO(A)": $parentescof_cs = " selected"; break;
	case "CUNADO(A)": $parentescof_cu = " selected"; break;
	case "HERMANASTRO(A)": $parentescof_hd = " selected"; break;
	case "HERMANO(A)": $parentescof_he = " selected"; break;
	case "HIJA": $parentescof_ha = " selected"; break;
	case "HIJASTRO(A)": $parentescof_ht = " selected"; break;
	case "HIJO": $parentescof_ho = " selected"; break;
	case "MADRASTRA": $parentescof_md = " selected"; break;
	case "MADRE": $parentescof_ma = " selected"; break;
	case "NIETO(A)": $parentescof_nt = " selected"; break;
	case "NUERA": $parentescof_nu = " selected"; break;
	case "OTRO": $parentescof_ot = " selected"; break;
	case "PADRASTRO": $parentescof_pd = " selected"; break;
	case "PADRE": $parentescof_pa = " selected"; break;
	case "PRIMO(A)": $parentescof_pr = " selected"; break;
	case "SOBRINO(A)": $parentescof_sb = " selected"; break;
	case "SUEGRO(A)": $parentescof_su = " selected"; break;
	case "TIO(A)": $parentescof_ti = " selected"; break;
	case "YERNO": $parentescof_ye = " selected"; break;
}

?>
					<td><label class="negrita">* Parentesco:</label></td>
		            <td><select id="parentesco_familiar" name="parentesco_familiar" style="width:147px" disabled>
							<option value=""></option>
							<option value="ABUELO(A)"<?php echo $parentescof_ab ?>>ABUELO(A)</option>
		                    <option value="COMPANERO(A)"<?php echo $parentescof_ca ?>>COMPA&Ntilde;ERO(A)</option>
							<option value="CONYUGE"<?php echo $parentescof_cy ?>>CONYUGE</option>
							<option value="CONYUGE SOLIDARIO(A)"<?php echo $parentescof_cs ?>>CONYUGE SOLIDARIO(A)</option>
							<option value="CUNADO(A)"<?php echo $parentescof_cu ?>>CU&Ntilde;ADO(A)</option>
							<option value="HERMANASTRO(A)"<?php echo $parentescof_hd ?>>HERMANASTRO(A)</option>
							<option value="HERMANO(A)"<?php echo $parentescof_he ?>>HERMANO(A)</option>
							<option value="HIJA"<?php echo $parentescof_ha ?>>HIJA</option>
							<option value="HIJASTRO(A)"<?php echo $parentescof_ht ?>>HIJASTRO(A)</option>
							<option value="HIJO"<?php echo $parentescof_ho ?>>HIJO</option>
							<option value="MADRASTRA"<?php echo $parentescof_md ?>>MADRASTRA</option>
							<option value="MADRE"<?php echo $parentescof_ma ?>>MADRE</option>
							<option value="NIETO(A)"<?php echo $parentescof_nt ?>>NIETO(A)</option>
							<option value="NUERA"<?php echo $parentescof_nu ?>>NUERA</option>
							<option value="OTRO"<?php echo $parentescof_ot ?>>OTRO</option>
							<option value="PADRASTRO"<?php echo $parentescof_pd ?>>PADRASTRO</option>
							<option value="PADRE"<?php echo $parentescof_pa ?>>PADRE</option>
							<option value="PRIMO(A)"<?php echo $parentescof_pr ?>>PRIMO(A)</option>
							<option value="SOBRINO(A)"<?php echo $parentescof_sb ?>>SOBRINO(A)</option>
							<option value="SUEGRO(A)"<?php echo $parentescof_su ?>>SUEGRO(A)</option>
							<option value="TIO(A)"<?php echo $parentescof_ti ?>>TIO(A)</option>
							<option value="YERNO"<?php echo $parentescof_ye ?>>YERNO</option>
					</td>
		            <td><label class="negrita">Tel&eacute;fono Fijo:</label></td>
		            <td><input type="text" name="telefono_familiar" id="telefono_familiar" value="<?php echo $fila1["telefono_familiar"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		        </tr>
				<tr>
		            <td><label class="negrita">* Direcci&oacute;n:</label></td>
		            <td><input type="text" size="40" name="direccion_familiar" id="direccion_familiar" value="<?php echo ($fila1["direccion_familiar"]) ?>" maxlength="60" disabled></td>
		            <td><label class="negrita">* Ciudad:</label></td>
					<td><select id="ciudad_familiar" name="ciudad_familiar" style="width:147px" disabled>
							<option value=""></option>
<?php

$queryDB = "select cod_municipio,concat(departamento,'-',municipio) as municipio,departamento from ciudades order by departamento";

$rz2 = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rz2))
{
	if ($fila["cod_municipio"] == $fila1["ciudad_familiar"] )
    	$selected_municipio = " selected";
	else
    	$selected_municipio = "";
	
    echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
}

?>
						</select>
		            </td>
		            <td><label class="negrita">* Tel&eacute;fono Celular:</label></td>
		            <td><input type="text" name="celular_familiar" id="celular_familiar" value="<?php echo $fila1["celular_familiar"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		         </tr>
		         <tr>
		            <td><label class="negrita">* PERSONAL: Nombre y Apellidos (que no viva con usted):</label></td>
		            <td><input type="text" size="40" name="nombre_personal" id="nombre_personal" value="<?php echo ($fila1["nombre_personal"]) ?>" maxlength="40" disabled></td>
		            <td><label class="negrita">* Parentesco:</label></td>
		            <td><input type="text" name="parentesco_personal" id="parentesco_personal" value="<?php echo $fila1["parentesco_personal"] ?>" maxlength="20" disabled></td>
		            <td><label class="negrita">Tel&eacute;fono Fijo:</label></td>
		            <td><input type="text" name="telefono_personal" id="telefono_personal" value="<?php echo $fila1["telefono_personal"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		        </tr>
		        <tr>
		            <td><label class="negrita">* Direcci&oacute;n:</label></td>
		            <td><input type="text" size="40" name="direccion_personal" id="direccion_personal" value="<?php echo ($fila1["direccion_personal"]) ?>" maxlength="60" disabled></td>
		            <td><label class="negrita">* Ciudad:</label></td>
					<td><select id="ciudad_personal" name="ciudad_personal" style="width:147px" disabled>
							<option value=""></option>
<?php

$queryDB = "select cod_municipio,municipio from ciudades order by municipio";

$rz2 = sqlsrv_query($link, $queryDB);

while ($fila = sqlsrv_fetch_array($rz2))
{
	if ($fila["cod_municipio"] == $fila1["ciudad_personal"] )
    	$selected_municipio = " selected";
	else
    	$selected_municipio = "";
	
    echo "<option value=\"".$fila["cod_municipio"]."\" ".$selected_municipio.">".($fila["municipio"])."</option>\n";
}

?>
						</select>
		            </td>
		            <td><label class="negrita">* Tel&eacute;fono Celular:</label></td>
		            <td><input type="text" name="celular_personal" id="celular_personal" value="<?php echo $fila1["celular_personal"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		         </tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato5.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato5, '1'); document.formato5.edicion_habilitada.value='1'; document.formato5.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato5, '0'); document.formato5.edicion_habilitada.value='0'; document.formato5.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Datos de Operaciones Internacionales</h3>
	<div id="section6">
		<form name="formato6" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma6()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="5">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
		        <tr>
		            <td><label class="negrita">* Realiza operaciones en Moneda Extranjera?</label></td>
		            <td><select name="moneda_extranjera" id="moneda_extranjera" style="width:147px" disabled>
<?php

$extranjera_si = "";
$extranjera_no = "";

if ($fila1["moneda_extranjera"] == "SI")
	$extranjera_si = " selected";
else if ($fila1["moneda_extranjera"] == "NO")
	$extranjera_no = " selected";

?>
							<option value=""></option>
		                    <option value="SI"<?php echo $extranjera_si ?>>SI</option>
		                    <option value="NO"<?php echo $extranjera_no ?>>NO</option>
		                </select>
					</td>
		            <td><label class="negrita">* Posee cuentas en el exterior?</label></td>
		            <td><select name="cuentas_exterior" id="cuentas_exterior" style="width:147px" disabled>
<?php

$cuentas_exterior_si = "";
$cuentas_exterior_no = "";

if ($fila1["cuentas_exterior"] == "SI")
	$cuentas_exterior_si = " selected";
else if ($fila1["cuentas_exterior"] == "NO")
	$cuentas_exterior_no = " selected";

?>
							<option value=""></option>
		                    <option value="SI"<?php echo $cuentas_exterior_si ?>>SI</option>
		                    <option value="NO"<?php echo $cuentas_exterior_no ?>>NO</option>
		                </select>
					</td>
		        </tr>
				<tr>
		            <td><label class="negrita">* Realiza actividades con criptomonedas?</label></td>
		            <td><select name="criptomoneda" id="criptomoneda" style="width:147px" disabled>
							<?php

							$criptomoneda_si = "";
							$criptomoneda_no = "";

							if ($fila1["criptomoneda"] == "SI")
								$criptomoneda_si = " selected";
							else if ($fila1["criptomoneda"] == "NO")
								$criptomoneda_no = " selected";

							?>
							<option value=""></option>
		                    <option value="SI"<?php echo $criptomoneda_si ?>>SI</option>
		                    <option value="NO"<?php echo $criptomoneda_no ?>>NO</option>
		                </select>
					</td>
		            <td><label class="negrita">* Desarrolla actividades APNFD?</label></td>
		            <td><select name="actividades_apnfd" id="actividades_apnfd" style="width:147px" disabled>
							<?php

							$actividades_apnfd_si = "";
							$actividades_apnfd_no = "";

							if ($fila1["actividades_apnfd"] == "SI")
								$actividades_apnfd_si = " selected";
							else if ($fila1["actividades_apnfd"] == "NO")
								$actividades_apnfd_no = " selected";

							?>
							<option value=""></option>
		                    <option value="SI"<?php echo $actividades_apnfd_si ?>>SI</option>
		                    <option value="NO"<?php echo $actividades_apnfd_no ?>>NO</option>
		                </select>
					</td>
		        </tr>
<?php

$tipo_transaccion_array = explode("|", ($fila1["tipo_transaccion"]));

$tipo_transaccion_opciones = $tipo_transaccion_array[0];

$tipo_transaccion_otra_cual = $tipo_transaccion_array[1];

?>
				<tr>
		            <td><label class="negrita">Si su Actividad Econ&oacute;mica implica transacciones<br>en Moneda Extranjera, Se&ntilde;ale los tipos de transacci&oacute;n:</label></td>
		            <td colspan="3">
						<table><tbody>
						<tr>
							<td><input type="checkbox" name="tipo_transaccion_exportacion" id="tipo_transaccion_exportacion" value="EXPORTACION"<?php if (strpos($tipo_transaccion_opciones, "EXPORTACION") !== false) { ?> checked<?php } ?> disabled>EXPORTACION&nbsp;</td>
							<td><input type="checkbox" name="tipo_transaccion_importacion" id="tipo_transaccion_importacion" value="IMPORTACION"<?php if (strpos($tipo_transaccion_opciones, "IMPORTACION") !== false) { ?> checked<?php } ?> disabled>IMPORTACION&nbsp;</td>
							<td><input type="checkbox" name="tipo_transaccion_inversiones" id="tipo_transaccion_inversiones" value="INVERSIONES"<?php if (strpos($tipo_transaccion_opciones, "INVERSIONES") !== false) { ?> checked<?php } ?> disabled>INVERSIONES&nbsp;</td>
							<td><input type="checkbox" name="tipo_transaccion_prestamo" id="tipo_transaccion_prestamo" value="PRESTAMO EN MONEDA EXTRANJERA"<?php if (strpos($tipo_transaccion_opciones, "PRESTAMO EN MONEDA EXTRANJERA") !== false) { ?> checked<?php } ?> disabled>PRESTAMO EN MONEDA EXTRANJERA&nbsp;</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="tipo_transaccion_otra" id="tipo_transaccion_otra" value="OTRA"<?php if (strpos($tipo_transaccion_opciones, "OTRA") !== false) { ?> checked<?php } ?> disabled>OTRA&nbsp;</td>
						</tr>
						<tr>
				            <td><label class="negrita">Cu&aacute;l?</label></td>
							<td colspan="3"><input type="text" name="tipo_transaccion_otra_cual" id="tipo_transaccion_otra_cual" value="<?php echo $tipo_transaccion_otra_cual ?>" maxlength="50" disabled></td>
						</tr>
						</tbody></table>
					</td>
				</tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td style="text-align: center;" class="tilenews2">Productos en Moneda Extranjera</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
				<tr>
					<td>&nbsp;</td>
		            <td><label class="negrita">Nombre de la Entidad</label></td>
		         	<td><label class="negrita">No. de Cuenta/Producto</label></td>
		            <td><label class="negrita">Tipo de Producto</label></td>
		            <td><label class="negrita">Monto de Operaci&oacute;n</label></td>
		            <td><label class="negrita">Moneda</label></td>
		            <td><label class="negrita">Ciudad</label></td>
		            <td><label class="negrita">Pa&iacute;s</label></td>
				</tr>
<?php

for ($i = 1; $i <= 3; $i++)
{
	if ($i != 1)
		$j = $i;
	
?>
		        <tr>
					<td><?php echo $i ?></td>
		            <td><input type="text" name="banco<?php echo $j ?>" id="banco<?php echo $j ?>" value="<?php echo ($fila1["banco".$j]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="num_cuenta<?php echo $j ?>" id="num_cuenta<?php echo $j ?>" value="<?php echo ($fila1["num_cuenta".$j]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="tipo_producto_operaciones<?php echo $j ?>" id="tipo_producto_operaciones<?php echo $j ?>" value="<?php echo ($fila1["tipo_producto_operaciones".$j]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="monto_operaciones<?php echo $j ?>" id="monto_operaciones<?php echo $j ?>" value="<?php echo $fila1["monto_operaciones".$j] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="10" disabled></td>
		            <td><input type="text" name="moneda_operaciones<?php echo $j ?>" id="moneda_operaciones<?php echo $j ?>" value="<?php echo ($fila1["moneda_operaciones".$j]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="ciudad_operaciones<?php echo $j ?>" id="ciudad_operaciones<?php echo $j ?>" value="<?php echo ($fila1["ciudad_operaciones".$j]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="pais_operaciones<?php echo $j ?>" id="pais_operaciones<?php echo $j ?>" value="<?php echo ($fila1["pais_operaciones".$j]) ?>" maxlength="50" disabled></td>
				</tr>
<?php

}

?>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato6.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato6, '1'); document.formato6.edicion_habilitada.value='1'; document.formato6.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato6, '0'); document.formato6.edicion_habilitada.value='0'; document.formato6.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Declaraci&oacute;n FACTA - CRS</h3>
	<div id="section7">
		<form name="formato7" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma7()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="6">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
		        <tr>
		            <td><label class="negrita">* Es usted una persona con ciudadan&iacute;a Estadounidense o de otro pa&iacute;s?</label></td>
		            <td><select name="ciudadania_extranjera" id="ciudadania_extranjera" style="width:147px" disabled>
<?php

$ciudadania_extranjera_si = "";
$ciudadania_extranjera_no = "";

if ($fila1["ciudadania_extranjera"] == "SI")
	$ciudadania_extranjera_si = " selected";
else if ($fila1["ciudadania_extranjera"] == "NO")
	$ciudadania_extranjera_no = " selected";

?>
							<option value=""></option>
		                    <option value="SI"<?php echo $ciudadania_extranjera_si ?>>SI</option>
		                    <option value="NO"<?php echo $ciudadania_extranjera_no ?>>NO</option>
		                </select>
					</td>
		            <td><label class="negrita">* Es usted residente en los Estados Unidos o posee Green Card?</label></td>
		            <td><select name="residencia_extranjera" id="residencia_extranjera" style="width:147px" disabled>
<?php

$residencia_extranjera_si = "";
$residencia_extranjera_no = "";

if ($fila1["residencia_extranjera"] == "SI")
	$residencia_extranjera_si = " selected";
else if ($fila1["residencia_extranjera"] == "NO")
	$residencia_extranjera_no = " selected";

?>
							<option value=""></option>
		                    <option value="SI"<?php echo $residencia_extranjera_si ?>>SI</option>
		                    <option value="NO"<?php echo $residencia_extranjera_no ?>>NO</option>
		                </select>
					</td>
		        </tr>
		        <tr>
		            <td><label class="negrita">* Es usted sujeto obligado tributariamente en los Estados Unidos u otro pa&iacute;s?</label></td>
		            <td><select name="impuestos_extranjera" id="impuestos_extranjera" style="width:147px" disabled>
							<?php

							$impuestos_extranjera_si = "";
							$impuestos_extranjera_no = "";

							if ($fila1["impuestos_extranjera"] == "SI")
								$impuestos_extranjera_si = " selected";
							else if ($fila1["impuestos_extranjera"] == "NO")
								$impuestos_extranjera_no = " selected";

							?>
							<option value=""></option>
		                    <option value="SI"<?php echo $impuestos_extranjera_si ?>>SI</option>
		                    <option value="NO"<?php echo $impuestos_extranjera_no ?>>NO</option>
		                </select>
					</td>
		            <td><label class="negrita">* Ha otorgado poderes de representaci&oacute;n legal o autorizaci&oacute;n<br>de firma vigente concedido a una persona que viva en el exterior?</label></td>
		            <td><select name="representacion_extranjera" id="representacion_extranjera" style="width:147px" disabled>
<?php

$representacion_extranjera_si = "";
$representacion_extranjera_no = "";

if ($fila1["representacion_extranjera"] == "SI")
	$representacion_extranjera_si = " selected";
else if ($fila1["representacion_extranjera"] == "NO")
	$representacion_extranjera_no = " selected";

?>
							<option value=""></option>
		                    <option value="SI"<?php echo $representacion_extranjera_si ?>>SI</option>
		                    <option value="NO"<?php echo $representacion_extranjera_no ?>>NO</option>
		                </select>
					</td>
		        </tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>
				<div class="box1 clearfix">
				<table>
				<tr>
					<td>&nbsp;</td>
		            <td><label class="negrita">Pa&iacute;s</label></td>
		         	<td><label class="negrita">Identificaci&oacute;n Tributaria</label></td>
		            <td><label class="negrita">Objeto del Poder</label></td>
				</tr>
<?php

for ($i = 1; $i <= 2; $i++)
{

?>
		        <tr>
					<td><?php echo $i ?></td>
		            <td><input type="text" name="poder_pais<?php echo $i ?>" id="poder_pais<?php echo $i ?>" value="<?php echo ($fila1["poder_pais".$i]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="poder_identificacion<?php echo $i ?>" id="poder_identificacion<?php echo $i ?>" value="<?php echo ($fila1["poder_identificacion".$i]) ?>" maxlength="50" disabled></td>
		            <td><input type="text" name="poder_objeto<?php echo $i ?>" id="poder_objeto<?php echo $i ?>" value="<?php echo ($fila1["poder_objeto".$i]) ?>" maxlength="50" disabled></td>
				</tr>
<?php

}

?>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato7.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato7, '1'); document.formato7.edicion_habilitada.value='1'; document.formato7.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato7, '0'); document.formato7.edicion_habilitada.value='0'; document.formato7.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Informaci&oacute;n Apoderado o Representante</h3>
	<div id="section8">
		<form name="formato8" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma8()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="7">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
		<tr>
			<td>
			    <div class="box1 clearfix">
		    	<table>
			    <tr>
		    		<td><label class="negrita">Primer Nombre:</label></td>
		        	<td><input type="text" id="apoderado_nombre1" name="apoderado_nombre1" value="<?php echo ($fila1["apoderado_nombre1"]) ?>" maxlength="20" disabled></td>
			        <td><label class="negrita">Segundo Nombre:</label></td>
		    	    <td><input type="text" id="apoderado_nombre2" name="apoderado_nombre2" value="<?php echo ($fila1["apoderado_nombre2"]) ?>" maxlength="20" disabled></td>
		        	<td><label class="negrita">Primer Apellido:</label></td>
			        <td><input type="text" id="apoderado_apellido1" name="apoderado_apellido1" value="<?php echo ($fila1["apoderado_apellido1"]) ?>" maxlength="20" disabled></td>
		    	    <td><label class="negrita">Segundo Apellido:</label></td>
		        	<td><input type="text" id="apoderado_apellido2" name="apoderado_apellido2" value="<?php echo ($fila1["apoderado_apellido2"]) ?>" maxlength="20" disabled></td>
				</tr>
		    	<tr>
		    		<td><label class="negrita">Tipo de Identificaci&oacute;n:</label></td>                    
			        <td><select name="apoderado_tipo_documento" id="apoderado_tipo_documento" style="width:147px" disabled>
<?php

echo "<option value=''></option>";

$selected = $fila1['apoderado_tipo_documento']=='CEDULA'?'selected':'';
echo "<option value='CEDULA' $selected>CEDULA</option>";

$selected = $fila1['apoderado_tipo_documento']=='TARJETA IDENTIDAD'?'selected':'';
echo "<option value='TARJETA IDENTIDAD' $selected>TARJETA IDENTIDAD</option>";

$selected = $fila1['apoderado_tipo_documento']=='REGISTRO CIVIL'?'selected':'';
echo "<option value='REGISTRO CIVIL' $selected>REGISTRO CIVIL</option>";

$selected = $fila1['apoderado_tipo_documento']=='CEDULA EXTRANGERIA'?'selected':'';
echo "<option value='CEDULA EXTRANGERIA' $selected>CEDULA EXTRANJERIA</option>";

?>
						</select>
					</td>
					<td><label class="negrita">No. documento:</label></td>
		            <td><input type="text" id="apoderado_nro_documento" name="apoderado_nro_documento" value="<?php echo $fila1["apoderado_nro_documento"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		            <td><label class="negrita">Celular:</label></td>
		            <td><input type="text" name="apoderado_celular" id="apoderado_celular" value="<?php echo $fila1["apoderado_celular"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
		            <td><label class="negrita">Tel&eacute;fono:</label></td>
		            <td><input type="text" name="apoderado_telefono" id="apoderado_telefono" value="<?php echo $fila1["apoderado_telefono"] ?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="20" disabled></td>
				</tr>
		        <tr>
		        	<td><label class="negrita">E-mail:</label></td>
		            <td><input type="text" id="apoderado_email" name="apoderado_email" value="<?php echo ($fila1["apoderado_email"]) ?>" maxlength="50" disabled></td>
		        	<td><label class="negrita">Direcci&oacute;n:</label></td>
		            <td colspan='3'><input size="57" type="text" id="apoderado_direccion" name="apoderado_direccion" value="<?php echo ($fila1["apoderado_direccion"]) ?>" maxlength="60" disabled></td>
		            <td><label class="negrita">Administra recursos p&uacute;blicos?</label></td>
		            <td><select name="apoderado_recursos_publicos" id="apoderado_recursos_publicos" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$apoderado_recursos_publicos_si = "";
$apoderado_recursos_publicos_no = "";

if ($fila1["apoderado_recursos_publicos"] == "SI")
	$apoderado_recursos_publicos_si = " selected";
else if ($fila1["apoderado_recursos_publicos"] == "NO")
	$apoderado_recursos_publicos_no = " selected";

?>
							<option value="SI"<?php echo $apoderado_recursos_publicos_si ?>>SI</option>
		                    <option value="NO"<?php echo $apoderado_recursos_publicos_no ?>>NO</option>
						</select>
					</td>
				</tr>
		        <tr>
		            <td><label class="negrita">Ejerce poder p&uacute;blico?</label></td>
		            <td><select name="apoderado_funcionario_publico" id="apoderado_funcionario_publico" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$apoderado_funcionario_publico_si = "";
$apoderado_funcionario_publico_no = "";

if ($fila1["apoderado_funcionario_publico"] == "SI")
	$apoderado_funcionario_publico_si = " selected";
else if ($fila1["apoderado_funcionario_publico"] == "NO")
	$apoderado_funcionario_publico_no = " selected";

?>
							<option value="SI"<?php echo $apoderado_funcionario_publico_si ?>>SI</option>
		                    <option value="NO"<?php echo $apoderado_funcionario_publico_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">Tiene reconocimiento p&uacute;blico?</label></td>
		            <td><select name="apoderado_personaje_publico" id="apoderado_personaje_publico" style="width:147px" disabled>
		            		<option value=""></option>
<?php

$apoderado_personaje_publico_si = "";
$apoderado_personaje_publico_no = "";

if ($fila1["apoderado_personaje_publico"] == "SI")
	$apoderado_personaje_publico_si = " selected";
else if ($fila1["apoderado_personaje_publico"] == "NO")
	$apoderado_personaje_publico_no = " selected";

?>
							<option value="SI"<?php echo $apoderado_personaje_publico_si ?>>SI</option>
		                    <option value="NO"<?php echo $apoderado_personaje_publico_no ?>>NO</option>
						</select>
					</td>
		            <td><label class="negrita">Fecha de Inicio:</label></td>
		            <td><input type="text" name="apoderado_fecha_inicio" value="<?php echo $fila1["apoderado_fecha_inicio"] ?>" id="apoderado_fecha_inicio" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
		            <td><label class="negrita">Fecha Final:</label></td>
		            <td><input type="text" name="apoderado_fecha_final" value="<?php echo $fila1["apoderado_fecha_final"] ?>" id="apoderado_fecha_final" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
				</tr>
		        </table>
				</div>
			</td>
		</tr>
		</table>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
<?php

if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" )
{

?>
			<td align="center">
				<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato8.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato8, '1'); document.formato8.edicion_habilitada.value='1'; document.formato8.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato8, '0'); document.formato8.edicion_habilitada.value='0'; document.formato8.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
			</td>
			<td align="center">
		    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
			</td>
<?php

}

?>
		</tr>
		</table>
		</form>
	</div>
	<h3>Varios</h3>
	<div id="section9">
		<form name="formato9" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma9()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
		<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
		<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
		<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
		<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
		<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
		<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
		<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
		<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
		<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
		<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
		<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
		<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
		<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
		<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
		<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
		<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
		<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
		<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
		<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
		<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
		<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
		<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
		<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
		<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
		<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
		<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
		<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
		<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
		<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
		<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
		<input type="hidden" name="section" value="8">
		<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
		<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
		<table>
			<tr>
				<td>
				    <div class="box1 clearfix">
			    	<table>
				    <tr>
			    		<td><label class="negrita">* Instrucci&oacute;n de Desembolso:</label></td>                    
				        <td>
				        	<select name="instruccion_desembolso" id="instruccion_desembolso" style="width:147px" disabled>
								<?php
								echo "<option value=''></option>";
								$selected = $fila1['instruccion_desembolso']=='CUENTA'?'selected':'';
								echo "<option value='CUENTA' $selected>CUENTA</option>";
								$selected = $fila1['instruccion_desembolso']=='GIRO PIN'?'selected':'';
								echo "<option value='GIRO PIN' $selected>GIRO PIN</option>";
								?>
							</select>
						</td>
			        	<td><label class="negrita">* Fuente de Fondos/Actividades L&iacute;citas:</label></td>
			            <td colspan='3'><input size="55" type="text" id="fuentes_actividades_licitas" name="fuentes_actividades_licitas" value="<?php echo ($fila1["fuentes_actividades_licitas"]) ?>" maxlength="60" disabled></td>
			    		<td><label class="negrita">* Clave Consulta:</label></td>
			        	<td><input type="text" id="clave" name="clave" value="<?php echo ($fila1["clave"]) ?>" maxlength="20" disabled></td>
					</tr>
			        </table>
					</div>
				</td>
			</tr>
		</table>
		<table>
		<tr>
			<td style="text-align: center;" class="tilenews2">Conocimiento Mejorado De Personas Expuestas Politica y Publicamente</td>
		</tr>
		</table>
		<table border="0" cellspacing=1 cellpadding=2 >
			<tr>
				<td>
					<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2 >
					<tr>
					<td><label class="negrita">* Ha ocupado cargos publicos</label></td>
		            <td><select name="cargo_publico" id="cargo_publico" style="width:147px" disabled>
		            		<option value=""></option>
								<?php

								$cargos_publicos_si = "";
								$cargos_publicos_no = "";

								if ($fila1["cargo_publico"] == "SI")
									$cargos_publicos_si = " selected";
								else if ($fila1["cargo_publico"] == "NO")
									$cargos_publicos_no = " selected";

								?>
							<option value="SI"<?php echo $cargos_publicos_si ?>>SI</option>
		                    <option value="NO"<?php echo $cargos_publicos_no ?>>NO</option>
						</select>
					</td>
					</tr>
					
					</table>
					</div>
				</td>
			</tr>
		</table>
		<table>
		<tr>
			<td style="text-align: center;" class="tilenews2">Conocimiento de los Compromisos y Seguros Adquiridos</td>
		</tr>
		</table>
		<table border="0" cellspacing=1 cellpadding=2 >
		<tr>
			<td>
				<div class="box1 clearfix">
				<table border="0" cellspacing=1 cellpadding=2 >
				<tr>
		            <td><label class="negrita">* Conoce y entiende las caracter&iacute;sticas, condiciones y coberturas del(los) seguro(s) solicitado(s),<br>y le explicaron el costo del (los) seguro(s) solicitado(s) c/u.</label></td>
		            <td>
		            	<select name="condiciones_seguros" id="condiciones_seguros" style="width:147px" disabled>
		            		<option value=""></option>
		            		<?php
		            		$condiciones_seguros_si = "";
							$condiciones_seguros_no = "";

							if ($fila1["condiciones_seguros"] == "SI")
								$condiciones_seguros_si = " selected";
							else if ($fila1["condiciones_seguros"] == "NO")
								$condiciones_seguros_no = " selected";
							?>
							<option value="SI"<?php echo $condiciones_seguros_si ?>>SI</option>
		                    <option value="NO"<?php echo $condiciones_seguros_no ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
		            <td><label class="negrita">* Conoce y entiende la forma de pago de la prima de (los) seguro(s) solicitado(s).</label></td>
		            <td>
		            	<select name="primas_seguros" id="primas_seguros" style="width:147px" disabled>
		            		<option value=""></option>
							<?php

							$primas_seguros_si = "";
							$primas_seguros_no = "";

							if ($fila1["primas_seguros"] == "SI")
								$primas_seguros_si = " selected";
							else if ($fila1["primas_seguros"] == "NO")
								$primas_seguros_no = " selected";

							?>
							<option value="SI"<?php echo $primas_seguros_si ?>>SI</option>
		                    <option value="NO"<?php echo $primas_seguros_no ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
		            <td><label class="negrita">* En caso de adquirir cualquier otro(s) producto(s) de cr&eacute;dito con Kredit Plus S.A.S. diferente a la<br>libranza, acepta cancelar el valor adeudado del (los) mismo(s) antes de cancelar la libranza.</label></td>
		            <td>
		            	<select name="cancelacion_valores" id="cancelacion_valores" style="width:147px" disabled>
		            		<option value=""></option>
							<?php

							$cancelacion_valores_si = "";
							$cancelacion_valores_no = "";

							if ($fila1["cancelacion_valores"] == "SI")
								$cancelacion_valores_si = " selected";
							else if ($fila1["cancelacion_valores"] == "NO")
								$cancelacion_valores_no = " selected";

							?>
							<option value="SI"<?php echo $cancelacion_valores_si ?>>SI</option>
		                    <option value="NO"<?php echo $cancelacion_valores_no ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
		            <td><label class="negrita">* Autorizo a Kredit Plus S.A.S., ampliar el plazo, en el evento en que se presente mora en algunos<br>de los productos adquiridos bajo ni nombre.</label></td>
		            <td>
		            	<select name="ampliacion_plazo" id="ampliacion_plazo" style="width:147px" disabled>
									            		<option value=""></option>
							<?php

							$ampliacion_plazo_si = "";
							$ampliacion_plazo_no = "";

							if ($fila1["ampliacion_plazo"] == "SI")
								$ampliacion_plazo_si = " selected";
							else if ($fila1["ampliacion_plazo"] == "NO")
								$ampliacion_plazo_no = " selected";

							?>
							<option value="SI"<?php echo $ampliacion_plazo_si ?>>SI</option>
		                    <option value="NO"<?php echo $ampliacion_plazo_no ?>>NO</option>
						</select>
					</td>
				</tr>
				<tr>
		            <td><label class="negrita">* Conoce y entiende los valores descontados de manera anticipada.</label></td>
		            <td>
		            	<select name="descuentos_anticipados" id="descuentos_anticipados" style="width:147px" disabled>
		            		<option value=""></option>
							<?php

							$descuentos_anticipados_si = "";
							$descuentos_anticipados_no = "";

							if ($fila1["descuentos_anticipados"] == "SI")
								$descuentos_anticipados_si = " selected";
							else if ($fila1["descuentos_anticipados"] == "NO")
								$descuentos_anticipados_no = " selected";

							?>
							<option value="SI"<?php echo $descuentos_anticipados_si ?>>SI</option>
		                    <option value="NO"<?php echo $descuentos_anticipados_no ?>>NO</option>
						</select>
					</td>
				</tr>
				</table>
				</div>
			</td>
		</tr>
		</table>
		<table>
		<tr>
			<td style="text-align: center;" class="tilenews2">Entrevista con el Cliente</td>
		</tr>
		</table>
		<table border="0" cellspacing=1 cellpadding=2 >
			<tr>
				<td>
					<div class="box1 clearfix">
					<table border="0" cellspacing=1 cellpadding=2 >
					<tr>
			            <td><label class="negrita">* Resultado de la Entrevista:</label></td>
			            <td><select name="resultado_entrevista" id="resultado_entrevista" style="width:147px" disabled>
						<?php

						echo "<option value=''></option>";

						$selected = $fila1['resultado_entrevista']=='ACEPTADO'?'selected':'';
						echo "<option value='ACEPTADO' $selected>ACEPTADO</option>";

						$selected = $fila1['resultado_entrevista']=='RECHAZADO'?'selected':'';
						echo "<option value='RECHAZADO' $selected>RECHAZADO</option>";

						?>
							</select>
						</td>
			            <td><label class="negrita">* Fecha de Entrevista:</label></td>
			            <td><input type="text" name="fecha_entrevista" value="<?php echo $fila1["fecha_entrevista"] ?>" id="fecha_entrevista" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
					</tr>
					<tr>
						<td valign="top"><label class="negrita">* Observaciones y/o recomendaciones</label></td>
			            <td colspan='3'><input size="59" type="text" id="observaciones" name="observaciones" value="<?php echo ($fila1["observaciones"]) ?>" maxlength="60" disabled></td>
					</tr>
					</table>
					</div>
				</td>
			</tr>
		</table>
		<table>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<?php
				if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" ){ ?>
					<td align="center">
						<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato9.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato9, '1'); document.formato9.edicion_habilitada.value='1'; document.formato9.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato9, '0'); document.formato9.edicion_habilitada.value='0'; document.formato9.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
					</td>
					<td align="center">
				    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
					</td>
					<?php
				}
				?>
			</tr>
		</table>
		</form>
	</div>


	<h3>Datos Complementarios Seguro De Vida Individual</h3>
	<div id="section10">
		<form name="formato10" method="post" action="solicitud_crear.php" autocomplete="off" onSubmit="return chequeo_forma10()">
			<input  name="fecha_radicado" value="<?=$filaFE["fecha_radicado"]?>" type="hidden">
			<input type="hidden" name="action" value="">
			<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
			<input type="hidden" name="fecha_inicialbd" value="<?php echo $_REQUEST["fecha_inicialbd"] ?>">
			<input type="hidden" name="fecha_inicialbm" value="<?php echo $_REQUEST["fecha_inicialbm"] ?>">
			<input type="hidden" name="fecha_inicialba" value="<?php echo $_REQUEST["fecha_inicialba"] ?>">
			<input type="hidden" name="fecha_finalbd" value="<?php echo $_REQUEST["fecha_finalbd"] ?>">
			<input type="hidden" name="fecha_finalbm" value="<?php echo $_REQUEST["fecha_finalbm"] ?>">
			<input type="hidden" name="fecha_finalba" value="<?php echo $_REQUEST["fecha_finalba"] ?>">
			<input type="hidden" name="fechades_inicialbd" value="<?php echo $_REQUEST["fechades_inicialbd"] ?>">
			<input type="hidden" name="fechades_inicialbm" value="<?php echo $_REQUEST["fechades_inicialbm"] ?>">
			<input type="hidden" name="fechades_inicialba" value="<?php echo $_REQUEST["fechades_inicialba"] ?>">
			<input type="hidden" name="fechades_finalbd" value="<?php echo $_REQUEST["fechades_finalbd"] ?>">
			<input type="hidden" name="fechades_finalbm" value="<?php echo $_REQUEST["fechades_finalbm"] ?>">
			<input type="hidden" name="fechades_finalba" value="<?php echo $_REQUEST["fechades_finalba"] ?>">
			<input type="hidden" name="fechaprod_inicialbm" value="<?php echo $_REQUEST["fechaprod_inicialbm"] ?>">
			<input type="hidden" name="fechaprod_inicialba" value="<?php echo $_REQUEST["fechaprod_inicialba"] ?>">
			<input type="hidden" name="fechaprod_finalbm" value="<?php echo $_REQUEST["fechaprod_finalbm"] ?>">
			<input type="hidden" name="fechaprod_finalba" value="<?php echo $_REQUEST["fechaprod_finalba"] ?>">
			<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
			<input type="hidden" name="unidadnegociob" value="<?php echo $_REQUEST["unidadnegociob"] ?>">
			<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
			<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
			<input type="hidden" name="tipo_comercialb" value="<?php echo $_REQUEST["tipo_comercialb"] ?>">
			<input type="hidden" name="id_comercialb" value="<?php echo $_REQUEST["id_comercialb"] ?>">
			<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
			<input type="hidden" name="decisionb" value="<?php echo $_REQUEST["decisionb"] ?>">
			<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
			<input type="hidden" name="visualizarb" value="<?php echo $_REQUEST["visualizarb"] ?>">
			<input type="hidden" name="calificacionb" value="<?php echo $_REQUEST["calificacionb"] ?>">
			<input type="hidden" name="statusb" value="<?php echo $_REQUEST["statusb"] ?>">
			<input type="hidden" name="id_oficinab" value="<?php echo $_REQUEST["id_oficinab"] ?>">
			<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
			<input type="hidden" name="section" value="9">
			<input type="hidden" name="edicion_habilitada" id="edicion_habilitada" value="0">
			<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
			
			<table>
				<tr>
					<td style="text-align: center;" class="tilenews2">Beneficiarios a título Gratuito / Seguro Individual</td>
				</tr>
			</table>
			
			<table>
				<tr>
					<td>
						<div class="box1 clearfix">
							<table>
								<tr>
									<td>&nbsp;</td>
						            <td><label class="negrita">Nombre 1</label></td>
						            <td><label class="negrita">Nombre 2</label></td>
						            <td><label class="negrita">Apellido 1</label></td>
						            <td><label class="negrita">Apellido 2</label></td>
						         	<td><label class="negrita">Parentesco</label></td>
						            <td><label class="negrita">%</label></td>
								</tr>
								<?php
								$i = 1;
								$queryBen = sqlsrv_query($link, "SELECT * FROM solicitud_beneficiarios_seguro WHERE id_simulacion = ".$_REQUEST['id_simulacion']." ORDER BY id");
								while ($filaBen = sqlsrv_fetch_array($queryBen)){ ?>

									 <tr>
										<td><?=$i?>. </td>
							            <td><input type="text" name="nombre1_<?=$i?>" id="nombre1_<?=$i?>" value="<?=$filaBen["primer_nombre"]?>" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="nombre2_<?=$i?>" id="nombre2_<?=$i?>" value="<?=$filaBen["segundo_nombre"]?>" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="apellido1_<?=$i?>" id="apellido1_<?=$i?>" value="<?=$filaBen["primer_apellido"]?>" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="apellido2_<?=$i?>" id="apellido2_<?=$i?>" value="<?=$filaBen["segundo_apellido"]?>" maxlength="15" size="15" disabled></td>				
							            <td>
							            	<select type="text" name="parentesco_<?=$i?>" id="parentesco_<?=$i?>" maxlength="50" disabled>
							            		<option value=""></option>
							            		<?php $queryParen = sqlsrv_query($link, "SELECT * FROM parentescos");
													while ($filaParen = sqlsrv_fetch_array($queryParen)){ 
														if($filaBen["id_parentesco"] == $filaParen["id"]){
															echo '<option selected value="'.$filaParen["id"].'">'.$filaParen["descripcion"].'</option>';
														}else{
															echo '<option value="'.$filaParen["id"].'">'.$filaParen["descripcion"].'</option>';
														}
													}

												?>
							            	</select>
							            </td>				            
							            <td><input type="text" name="porcentaje_<?=$i?>" id="porcentaje_<?=$i?>" value="<?=$filaBen["porcentaje"]?>" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="3" size="3" disabled></td>
									</tr>
									<?php
									$i++;
								}

								for ($i = $i; $i <= 4; $i++){ ?>
							        <tr>
										<td><?=$i?>. </td>
							            <td><input type="text" name="nombre1_<?=$i?>" id="nombre1_<?=$i?>" value="" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="nombre2_<?=$i?>" id="nombre2_<?=$i?>" value="" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="apellido1_<?=$i?>" id="apellido1_<?=$i?>" value="" maxlength="15" size="15" disabled></td>
							            <td><input type="text" name="apellido2_<?=$i?>" id="apellido2_<?=$i?>" value="" maxlength="15" size="15" disabled></td>				
							            <td>
							            	<select type="text" name="parentesco_<?=$i?>" id="parentesco_<?=$i?>" maxlength="50" disabled>
							            		<option selected value=""></option>
							            		<?php $queryParen = sqlsrv_query($link, "SELECT * FROM parentescos");
													while ($filaParen = sqlsrv_fetch_array($queryParen)){ 
														echo '<option value="'.$filaParen["id"].'">'.$filaParen["descripcion"].'</option>';
													}
												?>
							            	</select>
							            </td>			            
							            <td><input type="text" name="porcentaje_<?=$i?>" id="porcentaje_<?=$i?>" value="" onChange="if(isnumber(this.value)==false) {this.value=''; return false}" maxlength="3" size="3" disabled></td>
									</tr>
									<?php
								} ?>
							</table>
						</div>
					</td>
				</tr>
			</table>
			
			<table>
				<tr>
					<td style="text-align: center;" class="tilenews2">Declaración de Asegurabilidad</td>
				</tr>
			</table>

			

			<table border="0" cellspacing=1 cellpadding=2 >
				<tr>
					<td>
						<div class="box1 clearfix">
						<table border="0" cellspacing=1 cellpadding=2 >
							<?php
							$i = 1;
							$respuesta9 = '';
							$respuesta10 = '';
							$respuesta11 = '';
							$respuesta12 = '';
							$respuesta13 = '';
							$respuesta14 = '';

							$queryPreg = sqlsrv_query($link, "SELECT b.descripcion, a.respuesta, b.id FROM preguntas_seguro b LEFT JOIN solicitud_preguntas_seguro a ON a.id_pregunta = b.id AND a.id_simulacion = ".$_REQUEST['id_simulacion']." ORDER BY b.id");
							while ($filaPreg = sqlsrv_fetch_array($queryPreg)){ 
								if( $i < 9){ ?>
									<tr>
							            <td><label class="negrita">* <?=$i." ".$filaPreg["descripcion"]?></label></td>
							            <td>
							            	<select name="seguro_respuesta_<?=$i?>" id="seguro_respuesta_<?=$i?>" style="width:50px" disabled>
							            		<option value=""></option>
												<option value="SI"<?php if($filaPreg["respuesta"]=='SI'){ echo "selected"; } ?>>SI</option>
							                    <option value="NO"<?php if($filaPreg["respuesta"]=='NO'){ echo "selected"; } ?>>NO</option>
											</select>
										</td>
									</tr>	
									<?php
								}

								if($filaPreg["id"] == 9){
									$respuesta9 = $filaPreg["respuesta"];
								}else if($filaPreg["id"] == 10){
									$respuesta10 = $filaPreg["respuesta"];
								}else if($filaPreg["id"] == 11){
									$respuesta11 = $filaPreg["respuesta"];
								}else if($filaPreg["id"] == 12){
									$respuesta12 = $filaPreg["respuesta"];
								}else if($filaPreg["id"] == 13){
									$respuesta13 = $filaPreg["respuesta"];
								}else if($filaPreg["id"] == 14){
									$respuesta14 = $filaPreg["respuesta"];
								}
								$i++;
							}
							?>
						
						
						<tr>
				            <td colspan="2"><label class="negrita">9. En caso de haber marcado "Si" a alguna de las anteriores preguntas diligencia los siguientes datos:</label></td>
						</tr>
						<tr>
							<td colspan="2">
								<table style="margin: 10px 20px;">
									<tr>
										<td><label class="negrita">a) Nombre de la enfermedad o padecimiento:</label></td>
										<td colspan="3">
											<table>	
												<tr>
							            			<td><input type="text" name="seguro_respuesta_9a" size="121" value="<?=$respuesta9?>" id="seguro_respuesta_9a" disabled></td>
													<td><label class="negrita">b) Fecha de diagnóstico:</label></td>
							            			<td><input type="text" size="10" name="seguro_respuesta_9b" value="<?=$respuesta10?>" id="seguro_respuesta_9b" onChange="if(validarfecha(this.value)==false) {this.value=''; return false}" disabled></td>
							            		</tr>
							            	</table>
					            		</td>
									</tr>
									<tr>
				            			<td><label class="negrita">c) Tratameintos médicos/cirugías realizadas:</label></td>
				            			<td><input type="text" size="62" name="seguro_respuesta_9c" value="<?=$respuesta11?>" id="seguro_respuesta_9c" disabled></td>
										<td><label class="negrita">d) Secuelas o Condiciones:</label></td>
				            			<td><input type="text" name="seguro_respuesta_9d" size="68" value="<?=$respuesta12?>" id="seguro_respuesta_9d" disabled></td>
									</tr>
									<tr>
				            			<td><label class="negrita">e) Tratamiento actual de la enfermedad:</label></td>
				            			<td><input type="text" size="62" name="seguro_respuesta_9e" value="<?=$respuesta13?>" id="seguro_respuesta_9e" disabled></td>
										<td><label class="negrita">f) Observación Adicional:</label></td>
				            			<td><input type="text" name="seguro_respuesta_9f" size="68" value="<?=$respuesta14?>" id="seguro_respuesta_9f" disabled></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<?php
					if (($_SESSION["S_SOLOLECTURA"] != "1"  && $formato_digital == 0) || $_SESSION["S_SUBTIPO"] == "ANALISTA_VEN_CARTERA" || $_SESSION["S_TIPO"] == "ADMINISTRADOR" ){ ?>
						<td align="center">
							<input type="button" value="Habilitar Edici&oacute;n" onClick="if (document.formato10.edicion_habilitada.value == '0') { HabilitarEdicion(document.formato10, '1'); document.formato10.edicion_habilitada.value='1'; document.formato10.guardarbutton.disabled=false; this.value='Deshabilitar Edici&oacute;n' } else { HabilitarEdicion(document.formato10, '0'); document.formato10.edicion_habilitada.value='0'; document.formato10.guardarbutton.disabled=true; this.value='Habilitar Edici&oacute;n' }">&nbsp;
						</td>
						<td align="center">
					    	<input type="submit" name="guardarbutton" value="Guardar" disabled>
						</td>
						<?php
					}
					?>
				</tr>
			</table>
		</form>
	</div>
	<?php 
	if ($fila1["id_empresa"] == 2){
		//FIANTI
		?>
		<br><br>
		<input type="submit" onclick="window.open('../formatos/formulario_Fianti.php?<?php echo rand();?>&id_simulacion=<?php echo $_REQUEST['id_simulacion'];?>','_blank');" id="imprimir_formato" name="imprimir_formato"  value="Imprimir Formato">
		<?php
		
	}else{
		//KREDIT PLUS
		?>
		<br><br>
		<input type="submit" onclick="window.open('../formatos/formulario_Kredit.php?<?php echo rand();?>&id_simulacion=<?php echo $_REQUEST['id_simulacion'];?>','_blank');" id="imprimir_formato" name="imprimir_formato"  value="Imprimir Formato">
		<?php
	}
	?>			
</div>
<?php 

include("bottom.php"); 
?>
