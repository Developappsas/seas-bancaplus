<?php
include('../functions.php');

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA")) {
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "SELECT * from ventas" . $sufijo . " where id_venta = '" . $_REQUEST["id_venta"] . "'";

$venta_rs = sqlsrv_query($link, $queryDB);

$venta = sqlsrv_fetch_array($venta_rs);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Giros y Finanzas - Reporte Clientes " . $venta["nro_venta"] . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

?>
<table border="0">
	<tr>
		<th>Numero Interno</th>
		<th>Cedula</th>
		<th>Nombre1</th>
		<th>Nombre2</th>
		<th>Apellido1</th>
		<th>Apellido2</th>
		<th>Codigo Gerente</th>
		<th>Tipo Cliente</th>
		<th>Tipo Documento</th>
		<th>Codigo Grupo</th>
		<th>Codigo CIIU</th>
		<th>Codigo Profesion</th>
		<th>Codigo Banca</th>
		<th>Indicativo Privacidad</th>
		<th>Codigo Sexo</th>
		<th>Estado Civil</th>
		<th>Indicativo Empresa</th>
		<th>Excento Iva</th>
		<th>Impuesto Retiro</th>
		<th>Impuesto Retefuente</th>
		<th>Indicativo Sipla</th>
		<th>Indicador Idioma</th>
		<th>Motivo Vinculacion</th>
		<th>Clase Vinculacion</th>
		<th>Indicador Cliente/Banco</th>
		<th>Hobbys</th>
		<th>Numero Hijos</th>
		<th>Ocupacion</th>
		<th>Fecha Sipla</th>
		<th>Fecha Nacimiento</th>
		<th>Lugar Nacimmiento</th>
		<th>Agencia vinculacion</th>
		<th>Usuario que vinculo</th>
		<th>Notaria Escritura</th>
		<th>Num Escritura</th>
		<th>Fecha Escritura</th>
		<th>Num p.Juridica</th>
		<th>Fecha P.Juridica</th>
		<th>Reg Camara/Comercio</th>
		<th>Registo Libros</th>
		<th>Fecha Reg.Mercantil</th>
		<th>Representante Legal</th>
		<th>Situacion Compañia</th>
		<th>Fecha Sit.Compañia</th>
		<th>Tipo Empresa</th>
		<th>Indicativo G.Contribuyente</th>
		<th>Tipo Sociedad</th>
		<th>Calificacion Bancaria</th>
		<th>Lugar Exp.Documento</th>
		<th>Indicador Estudios</th>
		<th>Cod.Instituciones Academicas</th>
		<th>Deportes</th>
		<th>Indicador Viajes</th>
		<th>Num Club</th>
		<th>Ind TRN</th>
		<th>Ind Financiero</th>
		<th>Ind Persection Bancaria</th>
		<th>Ind Monto Extranjero</th>
		<th>Cta Moneda Extranjera</th>
		<th>Cod Banco Exterior</th>
		<th>Pais Cta Exterior</th>
		<th>Tipo Doc R.Legal</th>
		<th>Nom Empresa</th>
		<th>Cargo cliente</th>
	</tr>
	<?php

	if (!$_REQUEST["ext"]) {
		$queryDB = "SELECT RIGHT(REPLICATE('0', 17) + simulaciones.cedula, 17) as cod_interno,
		RIGHT(REPLICATE('0', 17) + simulaciones.cedula, 17) as cedula,concat(solicitud.nombre1,' ',solicitud.nombre2) as nombre1,'' as 
		nombre2, solicitud.apellido1, solicitud.apellido2,
		RIGHT(REPLICATE('0', 17) + '3', 17) as cod_gerente, '2' as tipo_cliente,
		RIGHT(REPLICATE('0', 2) + '1', 2) as tipo_doc,
		RIGHT(REPLICATE('0', 17) + '999', 17) as cod_grupo,
		RIGHT(REPLICATE('0', 8) + '10', 8) as ciiu,
		RIGHT(REPLICATE('0', 5) + '9999', 5) as cod_prof,
		RIGHT(REPLICATE('0', 5) + '1', 5) as cod_banca,'1' as ind_privacidad, (case solicitud.sexo when 
		'M' then '1' when 'F' then '2' end) as sexo,(case estado_civil when 'SOLTERO' then '1' when 'CASADO' then '2' when 'UNION LIBRE' then '3' when 
		'DIVORCIADO' then '4' when 'SEPARADO' then '5' when 'VIUDO' then '6' end)as estado_civil,'0' as indi_empresa,'1' as exc_iva,'0' as imp_retiro,
		RIGHT(REPLICATE('0', 5) + '1', 5) as retefuente,'1' as  ind_sipla,
		RIGHT(REPLICATE('0', 3) + '1', 3) as ind_idioma,
		RIGHT(REPLICATE('0', 2) + '0', 2) as mot_vinculacion,
		RIGHT(REPLICATE('0', 2) + '0', 2) as 
		clas_vinculacion,
		RIGHT(REPLICATE('0', 2) + '1', 2) as ind_cliente,
		RIGHT(REPLICATE('0', 5) + '99', 5) as hobby,
		RIGHT(REPLICATE('0', 2) + solicitud.personas_acargo, 2) as num_hijos,solicitud.ocupacion as 
		ocupacion,
		RIGHT(REPLICATE('0', 9) + '0', 9) as fecha_sipla,solicitud.fecha_nacimiento as fecha_nacimiento,solicitud.lugar_nacimiento as lugar_nacimiento, 
		RIGHT(REPLICATE('0', 5) + '201', 5) as agencia_origen,'' as usuario_vinculo,
		RIGHT(REPLICATE('0', 3) + '0', 3) as notaria_escritura,
		RIGHT(REPLICATE('0', 5) + '0', 5) as num_escritura,
		RIGHT(REPLICATE('0', 8) + '0', 8) as fecha_escritura,
		RIGHT(REPLICATE('0', 3) + '0', 3) as num_pjuridica,
		RIGHT(REPLICATE('0', 8) + '0', 8) as fecha_pjuridica,
		RIGHT(REPLICATE('0', 10) + '0', 10) as reg_cyc,'' as reg_libros,
		RIGHT(REPLICATE('0', 8) + '0', 8) as fec_rmercantil,
		RIGHT(REPLICATE('0', 17) + '0', 17) as rep_legal,'4' as situa_compania,
		RIGHT(REPLICATE('0', 8) + '0', 8) as fsit_compania,'99' as tipo_empresa,'1' as 
		ind_gcontribuyente,
		RIGHT(REPLICATE('0', 2) + '99', 2) as tipo_sociedad,'' as calf_bancaria,solicitud.lugar_expedicion as lug_exdoc,
		RIGHT(REPLICATE('0', 2) + case solicitud.nivel_estudios when 'BACHILLER' then '1' when 'TECNOLOGO'  then '3' when 'UNIVERSITARIO' then '4' when 'POSTGRADO' then '5' end, 2) as ind_estudios,
		RIGHT(REPLICATE('0', 17) + '9999', 17) as cod_iacademica,
		RIGHT(REPLICATE('0', 5) + '0', 5) as deportes,
		RIGHT(REPLICATE('0', 5) + '0', 5) as ind_viaje,
		RIGHT(REPLICATE('0', 17) + '1', 17) as num_club,
		RIGHT(REPLICATE('0', 3) + '0', 3) as trn,
		RIGHT(REPLICATE('0', 3) + '0', 3) as asesor_financiero,
		RIGHT(REPLICATE('0', 3) + '0', 3) as persec_bancaria,
		RIGHT(REPLICATE('0', 3) + '0', 3) as monto_extranjero,
		RIGHT(REPLICATE('0', 17) + '0', 17) as cta_extranjera,
		RIGHT(REPLICATE('0', 7) + '0', 7) as cod_bexterior,
		RIGHT(REPLICATE('0', 7) + '0', 7) as pais_ctaext,
		RIGHT(REPLICATE('0', 17) + '0', 17) as tdoc_legal,'' as nom_empresa,
		RIGHT(REPLICATE('0', 5) + '0', 5) as cargo_cliente from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

		if ($_SESSION["S_TIPO"] == "COMERCIAL") {
			$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
		} else {
			$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
		}
	} else {
		$queryDB = "SELECT RIGHT(REPLICATE('0', 17) + simulaciones.cedula, 17) as cod_interno,
RIGHT(REPLICATE('0', 17) + simulaciones.cedula, 17) as cedula,concat(solicitud.nombre1,' ',solicitud.nombre2) as nombre1,'' as 
nombre2, solicitud.apellido1, solicitud.apellido2,
RIGHT(REPLICATE('0', 17) + '3', 17) as cod_gerente, '2' as tipo_cliente,
RIGHT(REPLICATE('0', 2) + '1', 2) as tipo_doc,
RIGHT(REPLICATE('0', 17) + '999', 17) as cod_grupo,
RIGHT(REPLICATE('0', 8) + '10', 8) as ciiu,
RIGHT(REPLICATE('0', 5) + '9999', 5) as cod_prof,
RIGHT(REPLICATE('0', 5) + '1', 5) as cod_banca,'1' as ind_privacidad, (case solicitud.sexo when 
'M' then '1' when 'F' then '2' end) as sexo,(case estado_civil when 'SOLTERO' then '1' when 'CASADO' then '2' when 'UNION LIBRE' then '3' when 
'DIVORCIADO' then '4' when 'SEPARADO' then '5' when 'VIUDO' then '6' end)as estado_civil,'0' as indi_empresa,'1' as exc_iva,'0' as imp_retiro,
RIGHT(REPLICATE('0', 5) + '1', 5) as retefuente,'1' as  ind_sipla,
RIGHT(REPLICATE('0', 3) + '1', 3) as ind_idioma,
RIGHT(REPLICATE('0', 2) + '0', 2) as mot_vinculacion,
RIGHT(REPLICATE('0', 2) + '0', 2) as 
clas_vinculacion,
RIGHT(REPLICATE('0', 2) + '1', 2) as ind_cliente,
RIGHT(REPLICATE('0', 5) + '99', 5) as hobby,
RIGHT(REPLICATE('0', 2) + solicitud.personas_acargo, 2) as num_hijos,solicitud.ocupacion as 
ocupacion,
RIGHT(REPLICATE('0', 9) + '0', 9) as fecha_sipla,solicitud.fecha_nacimiento as fecha_nacimiento,solicitud.lugar_nacimiento as lugar_nacimiento, 
RIGHT(REPLICATE('0', 5) + '201', 5) as agencia_origen,'' as usuario_vinculo,
RIGHT(REPLICATE('0', 3) + '0', 3) as notaria_escritura,
RIGHT(REPLICATE('0', 5) + '0', 5) as num_escritura,
RIGHT(REPLICATE('0', 8) + '0', 8) as fecha_escritura,
RIGHT(REPLICATE('0', 3) + '0', 3) as num_pjuridica,
RIGHT(REPLICATE('0', 8) + '0', 8) as fecha_pjuridica,
RIGHT(REPLICATE('0', 10) + '0', 10) as reg_cyc,'' as reg_libros,
RIGHT(REPLICATE('0', 8) + '0', 8) as fec_rmercantil,
RIGHT(REPLICATE('0', 17) + '0', 17) as rep_legal,'4' as situa_compania,
RIGHT(REPLICATE('0', 8) + '0', 8) as fsit_compania,'99' as tipo_empresa,'1' as 
ind_gcontribuyente,
RIGHT(REPLICATE('0', 2) + '99', 2) as tipo_sociedad,'' as calf_bancaria,solicitud.lugar_expedicion as lug_exdoc,
RIGHT(REPLICATE('0', 2) + case solicitud.nivel_estudios when 'BACHILLER' then '1' when 'TECNOLOGO'  then '3' when 'UNIVERSITARIO' then '4' when 'POSTGRADO' then '5' end, 2) as ind_estudios,
RIGHT(REPLICATE('0', 17) + '9999', 17) as cod_iacademica,
RIGHT(REPLICATE('0', 5) + '0', 5) as deportes,
RIGHT(REPLICATE('0', 5) + '0', 5) as ind_viaje,
RIGHT(REPLICATE('0', 17) + '1', 17) as num_club,
RIGHT(REPLICATE('0', 3) + '0', 3) as trn,
RIGHT(REPLICATE('0', 3) + '0', 3) as asesor_financiero,
RIGHT(REPLICATE('0', 3) + '0', 3) as persec_bancaria,
RIGHT(REPLICATE('0', 3) + '0', 3) as monto_extranjero,
RIGHT(REPLICATE('0', 17) + '0', 17) as cta_extranjera,
RIGHT(REPLICATE('0', 7) + '0', 7) as cod_bexterior,
RIGHT(REPLICATE('0', 7) + '0', 7) as pais_ctaext,
RIGHT(REPLICATE('0', 17) + '0', 17) as tdoc_legal,'' as nom_empresa,
RIGHT(REPLICATE('0', 5) + '0', 5) as cargo_cliente from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion LEFT JOIN pagadurias pa ON si.pagaduria = pa.nombre where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";
	}

	if ($_SESSION["S_SECTOR"]) {
		$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
	}

	$queryDB .= " order by si.cedula, vd.id_ventadetalle";
	// echo $queryDB;

	$rs = sqlsrv_query($link, $queryDB);

	while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {

	?>
		<tr>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_interno"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cedula"] ?></td>
			<td><?php echo strtoupper($fila["nombre1"]) ?></td>
			<td><?php echo strtoupper($fila["nombre2"]) ?></td>
			<td><?php echo strtoupper($fila["apellido1"]) ?></td>
			<td><?php echo strtoupper($fila["apellido2"]) ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_gerente"] ?></td>
			<td><?php echo $fila["tipo_cliente"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tipo_doc"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_grupo"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ciiu"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_prof"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_banca"] ?></td>
			<td><?php echo $fila["ind_privacidad"] ?></td>
			<td><?php echo $fila["sexo"] ?></td>
			<td><?php echo $fila["estado_civil"] ?></td>
			<td><?php echo $fila["indi_empresa"] ?></td>
			<td><?php echo $fila["exc_iva"] ?></td>
			<td><?php echo $fila["imp_retiro"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["retefuente"] ?></td>
			<td><?php echo $fila["ind_sipla"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ind_idioma"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["mot_vinculacion"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["clas_vinculacion"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ind_cliente"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["hobby"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["num_hijos"] ?></td>
			<td><?php echo $fila["ocupacion"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fecha_sipla"] ?></td>
			<td><?php echo date('Ymd', strtotime($fila["fecha_nacimiento"])) ?></td>
			<td><?php echo $fila["lugar_nacimiento"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["agencia_origen"] ?></td>
			<td><?php echo $fila["usuario_vinculo"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["notaria_escritura"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["num_escritura"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fecha_escritura"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["num_pjuridica"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fecha_pjuridica"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["reg_cyc"] ?></td>
			<td><?php echo $fila["reg_libros"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fec_rmercantil"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["rep_legal"] ?></td>
			<td><?php echo $fila["situa_compania"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["fsit_compania"] ?></td>
			<td><?php echo $fila["tipo_empresa"] ?></td>
			<td><?php echo $fila["ind_gcontribuyente"] ?></td>
			<td><?php echo $fila["tipo_sociedad"] ?></td>
			<td><?php echo $fila["calf_bancaria"] ?></td>
			<td><?php echo $fila["lug_exdoc"] ?></td>
			<td><?php echo $fila["ind_estudios"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_iacademica"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["deportes"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["ind_viaje"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["num_club"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["trn"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["asesor_financiero"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["persec_bancaria"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["monto_extranjero"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cta_extranjera"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cod_bexterior"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["pais_ctaext"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["tdoc_legal"] ?></td>
			<td><?php echo $fila["nom_empresa"] ?></td>
			<td style="mso-number-format:'@';"><?php echo $fila["cargo_cliente"] ?></td>
		</tr>
	<?php

	}

	?>
</table>