<?php include ('../functions.php'); ?>
<?php

function formatDate($fecha) {
	return str_replace('-','',$fecha);
}

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "SELECT * from ventas".$sufijo." where id_venta = '".$_REQUEST["id_venta"]."'";

$venta_rs = sqlsrv_query($link,$queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

$startline = '';
$endline = "\r\n";
$snap = '';
$startdocument = '';
$enddocumento =  '';

if ($_REQUEST["type"] == 'txt') {
	header('Content-type: text/plain');
	header("Content-Disposition: attachment; filename=vinculacion_coomeva_ ".$venta["nro_venta"].".txt");

} elseif ($_REQUEST["type"] == 'xls') {
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=vinculacion_coomeva_ ".$venta["nro_venta"].".xls");
	
	$startline = '<tr><td>&#8203;';
	$endline = '</td></tr>';
	$snap = '</td><td>&#8203;';

	$startdocument = '<table>'.
	utf8_decode('<thead><tr><th>Agencia de vinculación</th><th>Tipo de documento</th><th>Cedula</th><th>Lugar de expedición de documento</th><th>Fecha de expedición de documento</th><th>Lugar de nacimiento</th><th>Fecha de nacimiento</th><th>Primer apellido</th><th>Segundo apellido</th><th>Primer nombre</th><th>Segundo nombre</th><th>Sexo</th><th>Estado civil</th><th>Personas a cargo</th><th>Personas a cargo menores de 18 años</th><th>Nivel académico</th><th>Nombre el titulo (de estudios)</th><th>Actividad laboral</th><th>Indicador declara renta</th><th>Indica si es funcionario publico</th><th>Indica si administra recursos públicos</th><th>Indica si es reconocido públicamente</th><th>Tipo de vivienda</th><th>Tiempo de residencia en la vivienda en AÑOS</th><th>Tiempo de residencia en la vivienda en MESES</th><th>Nombre del arrendador</th><th>Teléfono de arrendador</th><th>Ciudad del arrendador</th><th>Estrato</th><th>Dirección de residencia</th><th>Barrio de residencia</th><th>Ciudad de residencia</th><th>Teléfono de residencia</th><th>Teléfono celular</th><th>Dirección de correo electrónico</th><th>Dirección familiar</th><th>Barrio de dirección familiar</th><th>Ciudad dirección familiar</th><th>Teléfono familiar</th><th>Indicador de envió de correspondencia</th><th>Código de corte de facturación</th><th>Indica si autoriza envió de correo electrónico</th><th>Indica si autoriza envió de mensajes de texto x celular</th><th>Nombre de empresa donde trabaja</th><th>País de residencia</th><th>No utilizado</th><th>Tipo de vivienda</th><th>Fecha de ingreso a Multiactiva (solo para consulta)</th><th>Fecha de ingreso a banco. (solo para consulta)</th><th>Tipo de cliente (2=natural)</th><th>Numero interno (solo para consulta)</th><th>Descripción otra actividad económica</th><th>Tipo de empresa</th><th>Código de actividad de la empresa</th><th>Tipo de contrato</th><th>Fecha de ingreso a la empresa</th><th>Cargo en la empresa</th><th>Indica si posee negocio propio</th><th>Código de ciiu</th><th>Nombre de la empresa (negocio propio)</th><th>Dirección de la empresa (negocio propio)</th><th>Tiempo de actividad AÑOS</th><th>Tiempo de actividad MESES</th><th>Ventas anuales</th><th>Teléfono de negocio propio</th><th>Fax negocio propio</th><th>Ciudad negocio propio</th><th>Valor salario fijo (sueldo)</th><th>Valor salario variable</th><th>Ingresos por arriendos</th><th>Valor rentas fijas</th><th>Valor honorarios</th><th>Valor otros ingresos</th><th>Descripción de otros ingresos</th><th>Valor gastos por arriendo</th><th>Valor gastos familiares</th><th>Valor cuota Coomeva</th><th>Valor prestamos diferentes a Coomeva</th><th>Valor descuentos por nomina</th><th>Valor gastos por tarjeta de crédito</th><th>Otros gastos</th><th>Valor activos corrientes</th><th>Valor activos fijos</th><th>Valor otros activos</th><th>Descripción de otros activos</th><th>Pasivos financieros (deudas financieras)</th><th>Pasivos corrientes</th><th>Otros pasivos</th><th>Descripción otros pasivos</th><th>Dirección comercial</th><th>Barrio dirección comercial</th><th>Ciudad dirección comercial</th><th>Teléfono comercial</th><th>Extensión teléfono comercial</th><th>Fax teléfono comercial</th><th>Código tipo de patrimonio1</th><th>Dirección patrimonio1</th><th>Dirección patrimonio1</th><th>Valor comercial patrimonio1</th><th>Valor hipoteca patrimonio1</th><th>Valor pendiente de pago patrimonio1</th><th>Código tipo de patrimonio2</th><th>Descripcion Codigo Patrimonio</th><th>Dirección patrimonio2</th><th>Valor comercial patrimonio2</th><th>Valor hipoteca patrimonio2</th><th>Valor pendiente de pago patrimonio2</th><th>Clase de vehiculo1</th><th>Valor comercial de vehiculo1</th><th>Marca modelo del vehiculo1</th><th>Número de placa vehiculo1</th><th>Salo crédito vehiculo1</th><th>Prenda a favor, vehiculo1</th><th>Clase de vehiculo2</th><th>Valor comercial de vehiculo2</th><th>Marca modelo del vehiculo2</th><th>Número de placa vehiculo2</th><th>Salo crédito vehiculo2</th><th>Prenda a favor, vehiculo2</th><th>Descripción otros bienes1</th><th>Saldo del crédito otros bienes1</th><th>Valor comercial otros bienes1</th><th>Pignorado a, otros bienes1</th><th>Descripción otros bienes2</th><th>Total Egresos del Cónyuge</th><th>Valor comercial otros bienes2</th><th>Pignorado a, otros bienes2</th><th>Tipo de documento del cónyuge</th><th>Numero de documento del cónyuge</th><th>Lugar de expedición del documento del cónyuge</th><th>Fecha de expedición de documento del cónyuge</th><th>Primer apellido cónyuge</th><th>Segundo apellido cónyuge</th><th>Primer nombre cónyuge</th><th>Segundo nombre cónyuge</th><th>Fecha de nacimiento del cónyuge</th><th>Lugar de nacimiento del cónyuge</th><th>Sexo del cónyuge</th><th>Actividad económica del cónyuge</th><th>Indicador depende económicamente del cónyuge</th><th>Nombre de empresa donde trabaja el cónyuge</th><th>Total ingresos del cónyuge</th><th>Fecha de ingreso del cónyuge a la empresa</th><th>Cargo del cónyuge</th><th>Nivel académico del cónyuge</th><th>Nombre referencia1</th><th>Parentesco referencia1</th><th>Dirección referencia1</th><th>Ciudad dirección referencia1</th><th>Teléfono referencia1</th><th>Nombre referencia2</th><th>Código profesión referencia2</th><th>Descripción profesión referencia2</th><th>Dirección referencia2</th><th>Ciudad dirección referencia2</th><th>Teléfono referencia2</th><th>Referencia financiera</th><th>Sucursal referencia financiera</th><th>Indica producto ahorro - referencia financiera</th><th>Indica producto c. Corriente - referencia financiera</th><th>Indica producto portafolio - referencia financiera</th><th>Indicador hace operaciones con moneda extranjera</th><th>Indicador posee cuentas en moneda extranjera</th><th>Tipo de transacción en moneda extranjera</th><th>Descripción otro tipo de transacción</th><th>Nombre del banco donde tiene cuenta en moneda extranjera1</th><th>Número de cuenta en moneda extranjera1</th><th>Ciudad del banco donde tiene cuenta en moneda extranjera1</th><th>Moneda extranjera1</th><th>País donde posee cuenta en moneda extranjera1</th><th>Nombre del banco donde tiene cuenta en moneda extranjera2</th><th>Número de cuenta en moneda extranjera2</th><th>Ciudad del banco donde tiene cuenta en moneda extranjera2</th><th>Moneda extranjera2</th><th>País donde posee cuenta en moneda extranjera2</th><th>Indica si tiene parentesco con algún miembro de la junta de vigilancia</th><th>Nombre miembro junta vigilancia1</th><th>Parentesco con miembro de la junta de vigilancia1</th><th>Nombre miembro junta vigilancia2</th><th>Parentesco con miembro de la junta de vigilancia2</th><th>Barrio negocio propio</th><th>Tipo producto en moneda extranjera1</th><th>Tipo producto en moneda extranjera2</th><th>Monto cuenta en moneda extranjera1</th><th>Monto cuenta en moneda extranjera2</th><th>No utilizado</th><th>Nacionalidad 1</th><th>Nacionalidad 2</th><th>He permanecido 31 días o más durante el año en curso o 183 días durante un periodo de 3 años, que incluye el año en curso y los dos años inmediatamente anteriores dentro del territorio de los Estados Unidos</th><th>Soy poseedor de la tarjeta verde(Green Card) de los estados unidos de Norteamérica(Tarjeta de residencia))</th><th>Recibo sumas de dinero fijas u ocasionales(FDAP) que provienen de fuentes dentro de los Estados Unidos de Norteamérica)</th><th>Recibo ingreso bruto procedente de la venta u otra disposición de cualquier propiedad que pueda producir rentas intereses o dividendos cuya fuente se encuentre dentro de los estados unidos de Norteamérica</th><th>Soy ciudadano de los Estados Unidos residente en Colombia</th><th>Estoy obligado a tributar en Estados Unidos</th><th>Numero TIN</th><th>Hipotecado/pignorado 1 (si/no)</th><th>Hipotecado/pignorado 2 (si/no)</th><th>Codigo de Patrimonio 3</th><th>Marca modelo del vehiculo</th><th>Hipotecado/pignorado 3 (si/no)</th><th>Codigo de Patrimonio 4</th><th>Descripcion Codigo Patrimonio</th><th>Hipotecado/pignorado 4 (si/no)</th><th>Valor Comercial 4</th><th>Codigo de Patrimonio 5</th><th>Descripcion Codigo Patrimonio</th><th>Marca y Modelo</th><th>Hipotecado/pignorado 5 (si/no)</th><th>Saldo Credito 5</th></tr></thead><tbody>');
	$enddocumento =  '</tbody></table>';

} else {
	header('Content-type: text/plain');

}

header("Pragma: no-cache");
header("Expires: 0");

?>

<?php

if (!$_REQUEST["ext"])
{
	$queryDB = "SELECT so.nombre_personal, so.direccion_personal, so.ciudad_personal, so.telefono_personal, so.nombre_familiar, so.parentesco_familiar, so.direccion_familiar, so.ciudad_familiar, so.telefono_familiar, so.conyugue_dependencia, so.conyugue_ocupacion, so.conyugue_sexo, so.conyugue_fecha_nacimiento, so.conyugue_lugar_nacimiento, so.nombre_conyugue, so.conyugue_nombre_2, so.conyugue_apellido_1, so.conyugue_apellido_2, so.cedula_conyugue, so.conyugue_lugar_expedicion, so.conyugue_fecha_expedicion, so.cargo, so.fecha_vinculacion, so.nombre_empresa, so.lugar_correspondencia, so.residencia_barrio, so.email, so.celular, so.residencia_barrio, so.residencia_estrato, so.arrendador_nombre, so.arrendador_telefono, so.funcionario_publico, so.personaje_publico, so.declara_renta, so.ocupacion, so.nivel_estudios, so.personas_acargo, so.personas_acargo_menores, so.estado_civil, so.sexo, so.fecha_nacimiento, so.lugar_expedicion, so.fecha_expedicion, so.lugar_nacimiento, si.cedula, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.direccion, so.tel_residencia, ci.municipio, ci.departamento, si.total_ingresos, si.nro_libranza, si.pa, si.tasa_interes, si.plazo, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, si.pagaduria, pp.nit as nit_pagaduria, si.fecha_nacimiento, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, co.nombre as comprador, ve.fecha as fecha_venta, vd.fecha_primer_pago, DATEADD(MONTH,  (vd.cuota_hasta - vd.cuota_desde), vd.fecha_primer_pago) as fecha_vcto_final, si.plazo, si.puntaje_datacredito, SUM(cu.capital) as saldo_capital from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN cuotas cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion LEFT JOIN ciudades ci ON so.ciudad = ci.cod_municipio LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	$queryDB .= " group by si.cedula, si.fecha_desembolso, si.fecha_cartera, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.direccion, so.tel_residencia, ci.municipio, ci.departamento, si.total_ingresos, si.nro_libranza, si.pa, si.tasa_interes, si.plazo, si.puntaje_datacredito, si.opcion_credito, si.opcion_cuota_cli, si.opcion_cuota_ccc, si.opcion_cuota_cmp, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, pp.nit, si.pagaduria, si.fecha_nacimiento, vd.cuota_hasta, vd.cuota_desde, co.nombre, ve.fecha, vd.fecha_primer_pago, si.plazo order by si.cedula, vd.id_ventadetalle";
}
else
{
	$queryDB = "SELECT si.cedula, si.fecha_desembolso, FORMAT(si.fecha_cartera, 'yyyy-MM') as mes_prod, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.direccion, si.telefono as tel_residencia, si.ciudad as municipio, si.departamento, 0 as total_ingresos, si.nro_libranza, 'ESEFECTIVO' as pa, si.tasa_interes, si.plazo, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, si.pagaduria, pp.nit as nit_pagaduria, si.fecha_nacimiento, (vd.cuota_hasta - vd.cuota_desde + 1) as cuotas_vendidas, co.nombre as comprador, ve.fecha as fecha_venta, vd.fecha_primer_pago, DATEADD(MONTH,  (vd.cuota_hasta - vd.cuota_desde), vd.fecha_primer_pago) as fecha_vcto_final, si.plazo, si.puntaje_datacredito, SUM(cu.capital) as saldo_capital from ventas_detalle".$sufijo." vd INNER JOIN simulaciones".$sufijo." si ON vd.id_simulacion = si.id_simulacion INNER JOIN ventas".$sufijo." ve ON vd.id_venta = ve.id_venta INNER JOIN compradores co ON ve.id_comprador = co.id_comprador LEFT JOIN cuotas".$sufijo." cu ON si.id_simulacion = cu.id_simulacion AND cu.cuota >= vd.cuota_desde AND cu.cuota <= vd.cuota_hasta LEFT JOIN cuotas".$sufijo." cu2 ON si.id_simulacion = cu2.id_simulacion AND cu2.cuota = '1' LEFT JOIN pagaduriaspa pp ON si.pagaduria = pp.pagaduria where vd.id_venta = '".$_REQUEST["id_venta"]."'";
	
	$queryDB .= " group by si.cedula, si.fecha_desembolso, si.fecha_cartera, si.nombre1, si.nombre2, si.apellido1, si.apellido2, si.direccion, si.telefono, si.ciudad, si.departamento, si.nro_libranza, si.tasa_interes, si.plazo, si.puntaje_datacredito, si.opcion_credito, si.opcion_cuota_cso, cu2.seguro, si.valor_credito, pp.nit, si.pagaduria, si.fecha_nacimiento, vd.cuota_hasta, vd.cuota_desde, co.nombre, ve.fecha, vd.fecha_primer_pago, si.plazo order by si.cedula, vd.id_ventadetalle";
}

$rs = sqlsrv_query($link, $queryDB);

while ($v = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	$agenciaVin = str_pad(1, 5, 0, STR_PAD_LEFT);
	//$v['tipo_documento']
	$tipoDoc = str_pad(1, 2, 0, STR_PAD_LEFT);
	$no_doc = str_pad(substr($v['cedula'],0,17), 17, 0, STR_PAD_LEFT);
	$lugar_exp = str_pad($v['lugar_expedicion'], 8, 0, STR_PAD_LEFT);
	$fecha_exp = str_pad(formatDate($v['fecha_expedicion']), 8, 0, STR_PAD_LEFT);
	$lugar_nac = str_pad($v['lugar_nacimiento'], 9, 0, STR_PAD_LEFT);
	$fecha_nac = str_pad(formatDate($v['fecha_nacimiento']), 8, 0, STR_PAD_LEFT);
	$p_apellido = str_pad(substr($v['apellido1'],0,20), 20, ' ', STR_PAD_RIGHT);
	$s_apellido = str_pad(substr($v['apellido2'],0,20), 20, ' ', STR_PAD_RIGHT);
	$nombre = str_pad(substr($v['nombre1'],0,20), 20, ' ', STR_PAD_RIGHT);
	$s_nombre = str_pad(substr($v['nombre2'],0,20), 20, ' ', STR_PAD_RIGHT);
	$sex = ($v['sexo']=='F')?2:1;
	$sexo = str_pad($sex, 1, 0, STR_PAD_LEFT);

	if($v['estado_civil']=='UNION LIBRE'){
		$estadoCiv = 3;
	}if($v['estado_civil']=='SOLTERO'){
		$estadoCiv = 1;
	}if($v['estado_civil']=='CASADO'){
		$estadoCiv = 2;
	}if($v['estado_civil']=='DIVORCIADO'){
		$estadoCiv = 4;
	}if($v['estado_civil']=='VIUDO'){
		$estadoCiv = 6;
	}

	$estadoCivil = str_pad($estadoCiv, 1, 0, STR_PAD_LEFT);
	$no_per = str_pad($v['personas_acargo'], 2, 0, STR_PAD_LEFT);
	$no_per_menor_edad = str_pad($v['personas_acargo_menores'], 2, 0, STR_PAD_LEFT);

	if($v['nivel_estudios'] == 'BACHILLER'){
		$nivelEdu = 1;
	}elseif($v['nivel_estudios'] == 'TECNOLOGO'){
		$nivelEdu = 2;
	}elseif($v['nivel_estudios'] == 'UNIVERSITARIO'){
		$nivelEdu = 3;
	}elseif($v['nivel_estudios'] == 'POSTGRADO'){
		$nivelEdu = 4;
	}else{
		$nivelEdu = 0; //$v['nivel_estudios'];
	}

	$nivel_estudios = str_pad($nivelEdu, 2, 0, STR_PAD_LEFT);
	$lic = str_pad('9337', 40, ' ', STR_PAD_RIGHT);
	$ocupacion = str_pad($v['ocupacion'], 3, 0, STR_PAD_LEFT);
	$declara = ($v['declara_renta'] == 'SI')?1:0;
	$declara_renta = str_pad($declara, 1, 0, STR_PAD_LEFT);
	$funcionario = ($v['funcionario_publico'] == 'SI')?1:0;	
	$funcionario_publico = str_pad($funcionario, 1, ' ', STR_PAD_RIGHT);
	$recursos = ($v['recursos_publicos'] == 'SI')?1:0;	
	$recursos_publicos = str_pad($recursos, 1, ' ', STR_PAD_RIGHT);
	$personaje = ($v['personaje_publico'] == 'SI')?1:0;	
	$personaje_publico = str_pad($personaje, 1, ' ', STR_PAD_RIGHT);

	if($v['tipo_vivienda'] == 'FAMILIAR'){
		$valor = 1;
	}elseif($v['tipo_vivienda'] == 'ARRENDADA'){
		$valor = 2;
	}elseif($v['tipo_vivienda'] == 'PROPIA'){
		$valor = 3;
	}else{
		$valor = 0; //$v['nivel_estudios'];
	}
	$tipoVivienda = str_pad($valor, 2, 0, STR_PAD_LEFT);
	$value = is_numeric($v['anios'])?$v['anios']:0;
	$anios_vivienda = str_pad($value, 2, 0, STR_PAD_LEFT);
	$value = is_numeric($v['meses'])?$v['meses']:0;	
	$meses_vivienda = str_pad($value, 2, 0, STR_PAD_LEFT);
	$arrendatario = str_pad($v['arrendador_nombre'], 40, ' ', STR_PAD_RIGHT);
	$tel_arrendatario = str_pad($v['arrendador_telefono'], 10, 0, STR_PAD_LEFT);
	$ciudadArrendador = str_pad($v['ciudad'], 10, 0, STR_PAD_LEFT);

	$estrato = str_pad($v['estrato'], 1, 0, STR_PAD_LEFT);
	$dir_residencia = str_pad($v['direccion'], 60, ' ', STR_PAD_RIGHT);
	$barrio = str_pad($v['residencia_barrio'], 30, ' ', STR_PAD_RIGHT);
	$ciudad_residencia = str_pad($v['ciudad'], 10, 0, STR_PAD_LEFT);
	$tel_residencia = str_pad($v['tel_residencia'], 10, 0, STR_PAD_LEFT);
	$telefonoCelular = str_pad($v['celular'], 10, 0, STR_PAD_LEFT);
	$email = str_pad(substr($v['email'],0,39), 40, ' ', STR_PAD_RIGHT);

	$direccionFamiliar = str_pad(substr($v['direccion'],0,60), 60, ' ', STR_PAD_RIGHT);
	$barrioFamiliar = str_pad(substr($v['residencia_barrio'],0,30), 30, ' ', STR_PAD_RIGHT);
	$ciudadFamiliar = str_pad($v['ciudad'], 10, 0, STR_PAD_LEFT);
	$telefonoFam = str_pad(substr($v['tel_residencia'],0,10), 10, 0, STR_PAD_LEFT);
	if($v['lugar_correspondencia']=='EMAIL'){
		$corresp = 5;
	}if($v['lugar_correspondencia']=='CASA'){
		$corresp = 1;
	}if($v['lugar_correspondencia']=='OFICINA'){
		$corresp = 4;
	}
	$envioCor = str_pad($corresp, 1, 0, STR_PAD_LEFT);
	$codigoCortFact = str_pad(6, 1, 0, STR_PAD_LEFT);
	$envioEmail = str_pad('S', 1, ' ', STR_PAD_RIGHT);
    $envioSMS = str_pad('N', 1, ' ', STR_PAD_RIGHT);
	$empresa = str_pad(substr($v['pagaduria'],0,40), 40, ' ', STR_PAD_RIGHT);
	
	$pais_residencia = str_pad(substr('COL',0,5), 5, ' ', STR_PAD_RIGHT);
	$noUtilizado = str_pad('', 19, ' ', STR_PAD_RIGHT);
	$tipoVivienda = str_pad(0, 2, 0, STR_PAD_LEFT);
	$fechaIngMultiAct = str_pad(0, 8, 0, STR_PAD_LEFT);
	$fechaIngBancos = str_pad(0, 8, 0, STR_PAD_LEFT);
	$tipoCli = 2; //Persona natural
	$tipoCliente = str_pad($tipoCli, 1, 0, STR_PAD_LEFT);
	$numeroInterno = str_pad(0, 17, 0, STR_PAD_LEFT);
	
	$codigo_actividad_economica = 99;
	$actividadEco = "EDUCACION";
	$descripcionActividadEco = str_pad($actividadEco, 30, ' ', STR_PAD_RIGHT);
    $codigoActividad = str_pad($codigo_actividad_economica, 5, 0, STR_PAD_LEFT);
	$tipoEmp = 2; 
	$tipo_empresa = str_pad($tipoEmp, 5, 0, STR_PAD_LEFT);
	
	$tipo_contrato = str_pad(1, 2, 0, STR_PAD_LEFT);
	$fecha_vinculacion = str_pad($v['fecha_vinculacion'], 8, 0, STR_PAD_LEFT);
	$cargo = str_pad($v['cargo'], 30, ' ', STR_PAD_RIGHT);
	

	$poseeNegocio = str_pad(0, 1, ' ', STR_PAD_RIGHT);
	$codigoCiiu = str_pad(0, 15, 0, STR_PAD_LEFT);
	$nomEmpresaNegPropio = str_pad('', 40, ' ', STR_PAD_RIGHT);
	$dirEmpresaNegPropio = str_pad('', 60, ' ', STR_PAD_RIGHT);
	$tiempoAntividadA = str_pad(0, 2, 0, STR_PAD_LEFT);
	$tiempoAntividadM = str_pad(0, 2, 0, STR_PAD_LEFT);
	$ventasAnuales = str_pad(0, 14, 0, STR_PAD_LEFT);
	$telefonoNegocio = str_pad(0, 10, 0, STR_PAD_LEFT);
	$faxNegocioPropio = str_pad(0, 10, 0, STR_PAD_LEFT);
	$ciudadNegocioPropio = str_pad(0, 10, 0, STR_PAD_LEFT);
	$sueldo = str_pad($v['ingresos_laborales'], 12, 0, STR_PAD_LEFT);
	
        $salVar = 0;
        $salario_variable = str_pad($salVar, 14, 0, STR_PAD_LEFT);
        $arriend = 0;
        $arriendos = str_pad($arriend, 14, 0, STR_PAD_LEFT);
		$valorVentasFijas = str_pad(0, 14, 0, STR_PAD_LEFT);

        $hono = 0;
        $honorarios = str_pad($hono, 12, 0, STR_PAD_LEFT);

        $otrosIng = 0;
        $otros_ingresos = str_pad($otrosIng, 12, 0, STR_PAD_LEFT);
		$cuales_ingr ='';
        $cuales_ingresos = str_pad($cuales_ingr, 28, ' ', STR_PAD_RIGHT);
        $arrend = 0;
        $arrendamiento = str_pad($arrend, 12, 0, STR_PAD_LEFT);

        $eSosten = 0;
        $e_gastos_sostenimiento = str_pad($eSosten, 12, 0, STR_PAD_LEFT);
        $valorCuotaCoomeva = str_pad(0, 14, 0, STR_PAD_LEFT);
        $valorPrestamosDifBancoomeva = str_pad(0, 14, 0, STR_PAD_LEFT);

        $descNom = 0;
        $descuento_nomina = str_pad($descNom, 14, 0, STR_PAD_LEFT);
        $eTarj = 0;
        $e_tarjeta_credito = str_pad($eTarj, 14, 0, STR_PAD_LEFT);
        $otrosEgrer = 0;
        $otros_egresos = str_pad($otrosEgrer, 14, 0, STR_PAD_LEFT);

        $activCor = 0;
        $activosCorrientes = str_pad($activCor, 12, 0, STR_PAD_LEFT);
        $actvFij = 0;
        $activosFijos = str_pad($actvFij, 12, 0, STR_PAD_LEFT);
        $otroAct = 0;
        $otrosActivos = str_pad($otroAct, 12, 0, STR_PAD_LEFT);
        $cualesActivos = str_pad('', 30, ' ', STR_PAD_RIGHT);
        $passFin = 0;
        $pasivosFinancieros = str_pad($passFin, 12, 0, STR_PAD_LEFT);
        $pascoo = 0;
        $pasivosCorrientes = str_pad($pascoo, 12, 0, STR_PAD_LEFT);
        $otroPass = 0;
        $otrosPasivos = str_pad($otroPass, 12, 0, STR_PAD_LEFT);
        $cualesPasivos = str_pad('', 30, ' ', STR_PAD_RIGHT);

        $direccionComercial = str_pad('', 60, ' ', STR_PAD_RIGHT);
        $barrioComercial = str_pad('', 30, ' ', STR_PAD_RIGHT);
        $ciudadComercial = str_pad(0, 10, 0, STR_PAD_LEFT);
        $telefonoComercial = str_pad('', 10, 0, STR_PAD_LEFT);
        $extensionComercial = str_pad(0, 10, 0, STR_PAD_LEFT);
        $faxComercial = str_pad('', 10, 0, STR_PAD_LEFT);

        $codigoTipoPatrimonio = str_pad(0, 2, 0, STR_PAD_LEFT);
        $descripCodigoPatri = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $direccionPatri = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $valorComercialPatri = str_pad(0, 12, 0, STR_PAD_LEFT);
        $valorHipotecaPatri = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $valorPendPagoPatri =  str_pad(0, 12, 0, STR_PAD_LEFT);

        $codigoTipoPatrimonio2 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $descripCodigoPatri2 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $direccionPatri2 = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $valorComercialPatri2 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $valorHipotecaPatri2 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $valorPendPagoPatri2 =  str_pad(0, 12, 0, STR_PAD_LEFT);

        $claseVehiculo1 = str_pad(0, 15, ' ', STR_PAD_RIGHT);
        $valorComercialVehiculo1 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $marcaVehiculo1 = str_pad(0, 20, ' ', STR_PAD_RIGHT);
        $numeroPlacaVehiculo1 = str_pad(0, 10, ' ', STR_PAD_RIGHT);
        $saldoCreditoV1 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $prendaFavorV1 = str_pad(0, 55, ' ', STR_PAD_RIGHT);

        $claseVehiculo2 = str_pad(' ', 15, ' ', STR_PAD_RIGHT);
        $valorComercialVehiculo2 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $marcaVehiculo2 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $numeroPlacaVehiculo2 = str_pad(' ', 10, ' ', STR_PAD_RIGHT);
        $saldoCreditoV2 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $prendaFavorV2 = str_pad(' ', 55, ' ', STR_PAD_RIGHT);

        $descripOtrosBienes1 = str_pad(' ', 25, ' ', STR_PAD_RIGHT);
        $saldoCreditoBienes1 = str_pad(0, 14, 0, STR_PAD_LEFT);
        $valorComercialBienes1 = str_pad(0, 14, 0, STR_PAD_LEFT);
        $pignoradoOtrosBienes1 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);

        $descripOtrosBienes2 = str_pad(' ', 25, ' ', STR_PAD_RIGHT);
        $saldoCreditoBienes2 = str_pad(0, 14, 0, STR_PAD_LEFT);
        $valorComercialBienes2 = str_pad(0, 14, 0, STR_PAD_LEFT);
        $pignoradoOtrosBienes2 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
 
        $tipoDocConyugue = str_pad(1, 1, 0, STR_PAD_LEFT);
        $noDocumentoConyugue = str_pad($v['cedula_conyugue'], 17, 0, STR_PAD_LEFT);
        $lugarExpConyu = str_pad($v['conyugue_lugar_expedicion'], 10, 0, STR_PAD_LEFT);
        $fechaExpConyu = str_pad($v['conyugue_fecha_expedicion'], 8, 0, STR_PAD_LEFT);
        $primerApeConyu = str_pad($v['conyugue_apellido_1'], 20, ' ', STR_PAD_RIGHT);
        $segundoApeConyu = str_pad($v['conyugue_apellido_2'], 20, ' ', STR_PAD_RIGHT);
        $nombreConyu = str_pad($v['nombre_conyugue'], 20, ' ', STR_PAD_RIGHT);
        $sNombreConyu = str_pad($v['conyugue_nombre_2'], 20, ' ', STR_PAD_RIGHT);

        $fechaNacConyu = str_pad($v['conyugue_fecha_nacimiento'], 8, 0, STR_PAD_LEFT);
        $lugarNacConyu = str_pad($v['conyugue_lugar_nacimiento'], 10, 0, STR_PAD_LEFT);

		// !!! Por defecto valor = 1
        if($v['conyugue_sexo'] == 'F'){
            $sexCony = 2;
        }else {
            $sexCony = 1;
        }
        $sexoConyu = str_pad($sexCony, 1, 0, STR_PAD_LEFT);

        if($v['conyugue_ocupacion']=='EMPLEADO'){
            $ocupC = 1;
        }elseif($v['conyugue_ocupacion']=='INDEPENDIENTE'){
            $ocupC = 2;
        }elseif($v['conyugue_ocupacion']=='PENSIONADO'){
            $ocupC = 3;
        }elseif($v['conyugue_ocupacion']=='AMA DE CASA'){
            $ocupC = 4;
        }elseif($v['conyugue_ocupacion']=='ESTUDIANTE'){
            $ocupC = 5;
        }elseif($v['conyugue_ocupacion']=='RENTISTA CAPITAL'){
            $ocupC = 6;
        }else{
            $ocupC = $v['conyugue_ocupacion'];
        }
        $actEcoConyu = str_pad($ocupC, 3, 0, STR_PAD_LEFT);
        $dependeEcoConyu = str_pad($v['conyugue_dependencia'], 1, ' ', STR_PAD_RIGHT);

        $nomEmpresaConyu = str_pad($v['nombre_empresa'], 40, ' ', STR_PAD_RIGHT);
        $totalIngConyu = str_pad(0, 14, 0, STR_PAD_LEFT);
        $fechaIngConyu = str_pad($v['fecha_vinculacion'], 8, 0, STR_PAD_LEFT);
        $cargoConyu = str_pad($v['cargo'], 30, ' ', STR_PAD_RIGHT);
		$nivelAcaConyu = str_pad('', 2, 0, STR_PAD_LEFT);

        $nombreRefFam = str_pad($v['nombre_familiar'], 30, ' ', STR_PAD_RIGHT);
        $parentescoRefFam = str_pad($v['parentesco_familiar'], 2, ' ', STR_PAD_RIGHT);
        $direccionRefFam = str_pad($v['direccion_familiar'], 28, ' ', STR_PAD_RIGHT);
        $ciudadRefFam = str_pad($v['ciudad_familiar'], 10, 0, STR_PAD_LEFT);
        $telefonoRefFam = str_pad($v['telefono_familiar'], 10, 0, STR_PAD_LEFT);

        $nomRefPer = str_pad($v['nombre_personal'], 30, ' ', STR_PAD_RIGHT);
        $codProfesionRefPer = str_pad(0, 6, 0, STR_PAD_LEFT);
        $descripProfRefPer = str_pad(' ', 25, ' ', STR_PAD_RIGHT);
        $direccionRefPer = str_pad($v['direccion_personal'], 28, ' ', STR_PAD_RIGHT);
        $ciudadRefPer = str_pad($v['ciudad_personal'], 10, 0, STR_PAD_LEFT);
        $telefonoRefPer = str_pad($v['telefono_personal'], 10, 0, STR_PAD_LEFT);

        $refFinanciera = str_pad('', 30, ' ', STR_PAD_RIGHT);
        $sucursalFinan = str_pad('', 20, ' ', STR_PAD_RIGHT);

        $productoAhorro = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $productoCorriente = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $productoPortafo = str_pad('N', 1, ' ', STR_PAD_RIGHT);

		$opMonedaExtran = str_pad('N', 1, ' ', STR_PAD_RIGHT);
		$poseeCuentasExtran = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $tipoTransaccionExtran = str_pad(0, 2, 0, STR_PAD_LEFT);
        $descripOtroTransaccion = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $nombreBancoCuenta = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $numeroCuentaBanco =  str_pad(0, 17, 0, STR_PAD_LEFT);
        $ciudadBanco = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $monedaExt = str_pad(' ', 15, ' ', STR_PAD_RIGHT);
        $paisCuentaExtr = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $nombreBancoCuenta2 = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $numeroCuentaBanco2 =  str_pad(0, 17, 0, STR_PAD_LEFT);
        $ciudadBanco2 = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $monedaExt2 = str_pad(' ', 15, ' ', STR_PAD_RIGHT);
        $paisCuentaExtr2 = str_pad(' ', 40, ' ', STR_PAD_RIGHT);
        $parentescoMiembroJunta = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $nombreMiembroJunta = str_pad(' ', 50, ' ', STR_PAD_RIGHT);
        $parentescMiembroJunta = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $nombreMiembroJunta2 = str_pad(' ', 50, ' ', STR_PAD_RIGHT);
        $parentescMiembroJunta2 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $barrioNegPropio = str_pad(' ', 30, ' ', STR_PAD_RIGHT);
        $tipoProductoMonedaExt1 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $tipoProductoMonedaExt2 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $montoMonedaExt1 = str_pad(0, 17, 0, STR_PAD_LEFT);
        $montoMonedaExt2 = str_pad(0, 17, 0, STR_PAD_LEFT);
        $noUtilizado2 = str_pad(' ', 24, ' ', STR_PAD_RIGHT);

        $nacionalidad1 = str_pad('COLOMBIANO', 20, ' ', STR_PAD_RIGHT);
        $nacionalidad2 = str_pad('COLOMBIANO', 20, ' ', STR_PAD_RIGHT);
        $hePermanecido31DiasEEUU = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $soyPoseedorGreenCard = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $reciboSumasDineroFDAP = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $reciboIngresoBruto = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $soyCiudadano = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $tributarEEUU = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $numeroTIN = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $hipotecadoPignorado1 = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $hipotecadoPignorado2 = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $codigoPatrimonio3 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $marcaVehiculo3 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $hipotecadoPignorado3 = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $codigoPatrimonio4 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $descripCodigoPatri4 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $hipotecadoPignorado4 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $valorComercial4 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $codigoPatrimonio5 = str_pad(0, 2, 0, STR_PAD_LEFT);
        $descripCodigoPatri5 = str_pad(' ', 20, ' ', STR_PAD_RIGHT);
        $marcaVehiculo5 = str_pad(' ', 15, ' ', STR_PAD_RIGHT);
        $hipotecadoPignorado5 = str_pad('N', 1, ' ', STR_PAD_RIGHT);
        $saldoCredito5 = str_pad(0, 12, 0, STR_PAD_LEFT);
        $NIT = str_pad(0, 10, 0, STR_PAD_LEFT);

	$line = $startline.$agenciaVin.$snap.$tipoDoc.$snap.$no_doc.$snap.$lugar_exp.$snap.$fecha_exp.$snap.$lugar_nac.$snap.$fecha_nac.$snap.
	$p_apellido.$snap.$s_apellido.$snap.$nombre.$snap.$s_nombre.$snap.$sexo.$snap.$estadoCivil.$snap.$no_per.$snap.$no_per_menor_edad.$snap.
	$nivel_estudios.$snap.$lic.$snap.$ocupacion.$snap.$declara_renta.$snap.$funcionario_publico.$snap.$recursos_publicos.$snap.$personaje_publico.$snap.
	$tipo_vivienda.$snap.$anios_vivienda.$snap.$meses_vivienda.$snap.$arrendatario.$snap.$tel_arrendatario.$snap.$ciudadArrendador.$snap.
	$estrato.$snap.$dir_residencia.$snap.$barrio.$snap.$ciudad_residencia.$snap.$tel_residencia.$snap.$telefonoCelular.$snap.$email.$snap.
	$direccionFamiliar.$snap.$barrioFamiliar.$snap.$ciudadFamiliar.$snap.$telefonoFam.$snap.$envioCor.$snap.$codigoCortFact.$snap.
	$envioEmail.$snap.$envioSMS.$snap.$empresa.$snap.$pais_residencia.$snap.$noUtilizado.$snap.$tipoVivienda.$snap.$fechaIngMultiAct.$snap.
	$fechaIngBancos.$snap.$tipoCliente.$snap.$numeroInterno.$snap.$descripcionActividadEco.$snap.$tipo_empresa.$snap.$codigoActividad.$snap.
	$tipo_contrato.$snap.$fecha_vinculacion.$snap.$cargo.$snap.$poseeNegocio.$snap.$codigoCiiu.$snap.$nomEmpresaNegPropio.$snap.$dirEmpresaNegPropio.$snap.
	$tiempoAntividadA.$snap.$tiempoAntividadM.$snap.$ventasAnuales.$snap.$telefonoNegocio.$snap.$faxNegocioPropio.$snap.$ciudadNegocioPropio.$snap.
	$sueldo.$snap.$salario_variable.$snap.$arriendos.$snap.$valorVentasFijas.$snap.$honorarios.$snap.$otros_ingresos.$snap.$cuales_ingresos.$snap.
	$arrendamiento.$snap.$e_gastos_sostenimiento.$snap.$valorCuotaCoomeva.$snap.$valorPrestamosDifBancoomeva.$snap.$descuento_nomina.$snap.
	$e_tarjeta_credito.$snap.$otros_egresos.$snap.$activosCorrientes.$snap.$activosFijos.$snap.$otrosActivos.$snap.$cualesActivos.$snap.
	$pasivosFinancieros.$snap.$pasivosCorrientes.$snap.$otrosPasivos.$snap.$cualesPasivos.$snap.$direccionComercial.$snap.$barrioComercial.$snap.
	$ciudadComercial.$snap.$telefonoComercial.$snap.$extensionComercial.$snap.$faxComercial.$snap.$codigoTipoPatrimonio.$snap.$descripCodigoPatri.$snap.
	$direccionPatri.$snap.$valorComercialPatri.$snap.$valorHipotecaPatri.$snap.$valorPendPagoPatri.$snap.$codigoTipoPatrimonio2.$snap.$descripCodigoPatri2.$snap.
	$direccionPatri2.$snap.$valorComercialPatri2.$snap.$valorHipotecaPatri2.$snap.$valorPendPagoPatri2.$snap.$claseVehiculo1.$snap.$valorComercialVehiculo1.$snap.
	$marcaVehiculo1.$snap.$numeroPlacaVehiculo1.$snap.$saldoCreditoV1.$snap.$prendaFavorV1.$snap.$claseVehiculo2.$snap.$valorComercialVehiculo2.$snap.$marcaVehiculo2.$snap.
	$numeroPlacaVehiculo2.$snap.$saldoCreditoV2.$snap.$prendaFavorV2.$snap.$descripOtrosBienes1.$snap.$saldoCreditoBienes1.$snap.$valorComercialBienes1.$snap.
	$pignoradoOtrosBienes1.$snap.$descripOtrosBienes2.$snap.$saldoCreditoBienes2.$snap.$valorComercialBienes2.$snap.$pignoradoOtrosBienes2.$snap.
	$tipoDocConyugue.$snap.$noDocumentoConyugue.$snap.$lugarExpConyu.$snap.$fechaExpConyu.$snap.$primerApeConyu.$snap.$segundoApeConyu.$snap.$nombreConyu.$snap.
	$sNombreConyu.$snap.$fechaNacConyu.$snap.$lugarNacConyu.$snap.$sexoConyu.$snap.$actEcoConyu.$snap.$dependeEcoConyu.$snap.$nomEmpresaConyu.$snap.$totalIngConyu.$snap.
	$fechaIngConyu.$snap.$cargoConyu.$snap.$nivelAcaConyu.$snap.$nombreRefFam.$snap.$parentescoRefFam.$snap.$direccionRefFam.$snap.$ciudadRefFam.$snap.$telefonoRefFam.$snap.
	$nomRefPer.$snap.$codProfesionRefPer.$snap.$descripProfRefPer.$snap.$direccionRefPer.$snap.$ciudadRefPer.$snap.$telefonoRefPer.$snap.$refFinanciera.$snap.
	$sucursalFinan.$snap.$productoAhorro.$snap.$productoCorriente.$snap.$productoPortafo.$snap.$opMonedaExtran.$snap.$poseeCuentasExtran.$snap.$tipoTransaccionExtran.$snap.
	$descripOtroTransaccion.$snap.$nombreBancoCuenta.$snap.$numeroCuentaBanco.$snap.$ciudadBanco.$snap.$monedaExt.$snap.$paisCuentaExtr.$snap.$nombreBancoCuenta2.$snap.
	$numeroCuentaBanco2.$snap.$ciudadBanco2.$snap.$monedaExt2.$snap.$paisCuentaExtr2.$snap.$parentescoMiembroJunta.$snap.$nombreMiembroJunta.$snap.
	$parentescMiembroJunta.$snap.$nombreMiembroJunta2.$snap.$parentescMiembroJunta2.$snap.$barrioNegPropio.$snap.$tipoProductoMonedaExt1.$snap.
	$tipoProductoMonedaExt2.$snap.$montoMonedaExt1.$snap.$montoMonedaExt2.$snap.$noUtilizado2.$snap.$nacionalidad1.$snap.$nacionalidad2.$snap.
	$hePermanecido31DiasEEUU.$snap.$soyPoseedorGreenCard.$snap.$reciboSumasDineroFDAP.$snap.$reciboIngresoBruto.$snap.$soyCiudadano.$snap.$tributarEEUU.$snap.
	$numeroTIN.$snap.$hipotecadoPignorado1.$snap.$hipotecadoPignorado2.$snap.$codigoPatrimonio3.$snap.$marcaVehiculo3.$snap.$hipotecadoPignorado3.$snap.
	$codigoPatrimonio4.$snap.$descripCodigoPatri4.$snap.$hipotecadoPignorado4.$snap.$valorComercial4.$snap.$codigoPatrimonio5.$snap.$descripCodigoPatri5.$snap.
	$marcaVehiculo5.$snap.$hipotecadoPignorado5.$snap.$saldoCredito5.$snap.$NIT.$snap.
	$endline;

	$out.=$line;
}
echo $startdocument.$out.$enddocumento;
	/*
	switch($fila["opcion_credito"])
	{
		case "CLI":	$opcion_cuota = $fila["opcion_cuota_cli"];
					break;
		case "CCC":	$opcion_cuota = $fila["opcion_cuota_ccc"];
					break;
		case "CMP":	$opcion_cuota = $fila["opcion_cuota_cmp"];
					break;
		case "CSO":	$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
	}
	
	$cuota_corriente = $opcion_cuota - $fila["seguro"];
	
	$saldo_capital = $fila["saldo_capital"];
	
	if ($fila["cuotas_vendidas"] == $fila["plazo"])
		$saldo_capital = $fila["valor_credito"];
	*/
?>