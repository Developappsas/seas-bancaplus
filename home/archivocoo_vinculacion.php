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

header('Content-type: text/plain');
header("Content-Disposition: attachment; filename=Bancoomeva - Vinculacion Venta " . $venta["nro_venta"] . ".txt");
header("Pragma: no-cache");
header("Expires: 0");

if (!$_REQUEST["ext"]) {
	$queryDB = "SELECT so.tipo_documento, si.cedula, so.lugar_expedicion, so.fecha_expedicion, so.lugar_nacimiento, so.fecha_nacimiento, DATEADD(YEAR, 18, so.fecha_nacimiento) as minima_fecha_expedicion, so.nombre1, so.nombre2, so.apellido1, so.apellido2, so.sexo, so.estado_civil, so.personas_acargo_adultos, so.personas_acargo_menores, so.nivel_estudios, so.ocupacion, so.declara_renta, so.funcionario_publico, so.recursos_publicos, so.personaje_publico, so.tipo_vivienda, so.anios, so.meses, so.arrendador_nombre, so.arrendador_telefono, so.arrendador_ciudad, so.residencia_estrato, so.direccion, so.residencia_barrio, so.ciudad, so.tel_residencia, so.celular, so.email, so.lugar_correspondencia, so.tipo_contrato, so.fecha_vinculacion, so.cargo, si.total_ingresos, so.otros_ingresos, so.detalle_ingresos, si.total_egresos, si.pagaduria, so.nombre_empresa, so.conyugue_tipo_documento, so.cedula_conyugue, so.conyugue_lugar_expedicion, so.conyugue_fecha_expedicion, DATEADD(YEAR, 18, so.conyugue_fecha_nacimiento) as minima_fecha_expedicion_conyugue, so.conyugue_apellido_1, so.conyugue_apellido_2, so.nombre_conyugue, so.conyugue_nombre_2, so.conyugue_fecha_nacimiento, so.conyugue_lugar_nacimiento, so.conyugue_sexo, so.conyugue_ocupacion, so.conyugue_dependencia, so.conyugue_nombre_empresa, so.conyugue_total_ingresos, so.conyugue_fecha_vinculacion, so.conyugue_cargo, so.conyugue_nivel_estudios, so.nombre_familiar, so.parentesco_familiar, so.direccion_familiar, so.ciudad_familiar, so.telefono_familiar, so.nombre_personal, so.direccion_personal, so.ciudad_personal, so.telefono_personal, so.moneda_extranjera, so.num_cuenta, so.tipo_transaccion, so.banco, so.ciudad_operaciones, so.pais_operaciones from ventas_detalle vd INNER JOIN simulaciones si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre LEFT JOIN solicitud so ON so.id_simulacion = si.id_simulacion where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";

	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}
} else {
	$queryDB = "SELECT '' as tipo_documento, si.cedula, '' as lugar_expedicion, '' as fecha_expedicion, '' as lugar_nacimiento, '' as fecha_nacimiento, '' as minima_fecha_expedicion, si.nombre1, si.nombre2, si.apellido1, si.apellido2, '' as sexo, '' as estado_civil, '' as personas_acargo_adultos, '' as personas_acargo_menores, '' as nivel_estudios, '' as ocupacion, '' as declara_renta, '' as funcionario_publico, '' as recursos_publicos, '' as personaje_publico, '' as tipo_vivienda, '' as anios, '' as meses, '' as arrendador_nombre, '' as arrendador_telefono, '' as arrendador_ciudad, '' as residencia_estrato, si.direccion, '' as residencia_barrio, '' as ciudad, si.telefono as tel_residencia, '' as celular, '' as email, '' as lugar_correspondencia, '' as tipo_contrato, '' as fecha_vinculacion, '' as cargo, 0 as total_ingresos, 0 as detalle_ingresos, 0 as total_egresos, si.pagaduria, '' as nombre_empresa, '' as conyugue_tipo_documento, '' as cedula_conyugue, '' as conyugue_lugar_expedicion, '' as conyugue_fecha_expedicion, '' as minima_fecha_expedicion_conyugue, '' as conyugue_apellido_1, '' as conyugue_apellido_2, '' as nombre_conyugue, '' as conyugue_nombre_2, '' as conyugue_fecha_nacimiento, '' as conyugue_lugar_nacimiento, '' as conyugue_sexo, '' as conyugue_ocupacion, '' as conyugue_dependencia, '' as conyugue_nombre_empresa, 0 as conyugue_total_ingresos, '' as conyugue_fecha_vinculacion, '' as conyugue_cargo, '' as conyugue_nivel_estudios, '' as nombre_familiar, '' as parentesco_familiar, '' as direccion_familiar, '' as ciudad_familiar, '' as telefono_familiar, '' as nombre_personal, '' as direccion_personal, '' as ciudad_personal, '' as telefono_personal, '' as moneda_extranjera, '' as num_cuenta, '' as tipo_transaccion, '' as banco, '' as ciudad_operaciones, '' as pais_operaciones from ventas_detalle" . $sufijo . " vd INNER JOIN simulaciones" . $sufijo . " si ON vd.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where vd.id_venta = '" . $_REQUEST["id_venta"] . "'";
}

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

$rs = sqlsrv_query($link, $queryDB);


while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
	$agcvin = str_pad("184", 5, 0, STR_PAD_LEFT);

	$tipdoc = "";

	switch ($fila["tipo_documento"]) {
		case "CEDULA":
			$tipdoc = "1";
			break;
		case "CEDULA EXTRANGERIA":
			$tipdoc = "2";
			break;
		case "TARJETA IDENTIDAD":
			$tipdoc = "4";
			break;
		case "REGISTRO CIVIL":
			$tipdoc = "6";
			break;
	}

	$tipdoc = str_pad($tipdoc, 2, 0, STR_PAD_LEFT);
	$nitcli = str_pad(substr(trim($fila["cedula"]), 0, 17), 17, 0, STR_PAD_LEFT);

	if ($fila["lugar_expedicion"])
		$lugexp = $fila["lugar_expedicion"];
	else if ($fila["lugar_nacimiento"])
		$lugexp = $fila["lugar_nacimiento"];
	else if ($fila["ciudad"])
		$lugexp = $fila["ciudad"];
	else
		$lugexp = "76001";

	$lugexp = str_pad($lugexp, 5, 0, STR_PAD_LEFT) . "000";

	if (strtotime($fila["fecha_expedicion"]) > strtotime($fila["minima_fecha_expedicion"]))
		$fecesc = date("Ymd", strtotime($fila["fecha_expedicion"]));
	else
		$fecesc = date("Ymd", strtotime($fila["minima_fecha_expedicion"]));

	if ($fila["lugar_nacimiento"])
		$lugcon = str_pad($fila["lugar_nacimiento"], 6, 0, STR_PAD_LEFT) . "000";
	else if ($fila["lugar_expedicion"])
		$lugcon = str_pad($fila["lugar_expedicion"], 6, 0, STR_PAD_LEFT) . "000";
	else if ($fila["ciudad"])
		$lugcon = str_pad($fila["ciudad"], 6, 0, STR_PAD_LEFT) . "000";
	else
		$lugcon = str_pad("76001", 6, 0, STR_PAD_LEFT) . "000";

	$feccon = date("Ymd", strtotime($fila["fecha_nacimiento"]));
	$prmapl = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["apellido1"]))), 0, 20), 20, " ", STR_PAD_RIGHT);
	$sgdapl = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["apellido2"]))), 0, 20), 20, " ", STR_PAD_RIGHT);
	$prmnom = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["nombre1"]))), 0, 20), 20, " ", STR_PAD_RIGHT);
	$sgdnom = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["nombre2"]))), 0, 20), 20, " ", STR_PAD_RIGHT);

	$codsex = "";

	switch ($fila["sexo"]) {
		case "M":
			$codsex = "1";
			break;
		case "F":
			$codsex = "2";
			break;
	}

	$codsex = str_pad($codsex, 1, 0, STR_PAD_LEFT);

	$estciv = "";

	switch ($fila["estado_civil"]) {
		case "SOLTERO":
			$estciv = "1";
			break;
		case "UNION LIBRE":
			$estciv = "3";
			break;
		case "CASADO":
			$estciv = "2";
			break;
		case "DIVORCIADO":
			$estciv = "4";
			break;
		case "SEPARADO":
			$estciv = "5";
			break;
		case "VIUDO":
			$estciv = "6";
			break;
	}

	if (!$estciv)
		if ($fila["nombre_conyugue"])
			$estciv = "2";
		else
			$estciv = "1";

	$estciv = str_pad($estciv, 1, 0, STR_PAD_LEFT);

	$percar = is_numeric(trim($fila["personas_acargo_adultos"])) ? trim($fila["personas_acargo_adultos"]) : 0;
	$percar = str_pad($percar, 2, 0, STR_PAD_LEFT);

	$perm18 = is_numeric(trim($fila["personas_acargo_menores"])) ? trim($fila["personas_acargo_menores"]) : 0;
	$perm18 = str_pad($perm18, 2, 0, STR_PAD_LEFT);

	$indest = "";

	switch ($fila["nivel_estudios"]) {
		case "PRIMARIA":
			$indest = "10";
			break;
		case "BACHILLER":
			$indest = "1";
			break;
		case "TECNICO":
			$indest = "2";
			break;
		case "TECNOLOGO":
			$indest = "3";
			break;
		case "UNIVERSITARIO":
			$indest = "4";
			break;
		case "ESPECIALIZACION":
			$indest = "5";
			break;
		case "MAESTRIA":
			$indest = "6";
			break;
		case "DOCTORADO":
			$indest = "7";
			break;
		default:
			$indest = "9";
	}

	$indest = str_pad($indest, 2, 0, STR_PAD_LEFT);
	$nomti1 = str_pad("99999", 40, " ", STR_PAD_RIGHT);

	$ocupac = "";

	switch ($fila["ocupacion"]) {
		case "1":
			$ocupac = "1";
			break;
		case "2":
			$ocupac = "1";
			break;
		case "3":
			$ocupac = "2";
			break;
		case "4":
			$ocupac = "8";
			break;
		case "5":
			$ocupac = "5";
			break;
		case "6":
			$ocupac = "9";
			break;
		case "7":
			$ocupac = "7";
			break;
		case "8":
			$ocupac = "1";
			break;
		case "9":
			$ocupac = "1";
			break;
	}

	$ocupac = str_pad($ocupac, 3, 0, STR_PAD_LEFT);

	$decren = ($fila["declara_renta"] == "SI") ? "S" : "N";
	$funpub = ($fila["funcionario_publico"] == "SI") ? "S" : "N";
	$admrpu = ($fila["recursos_publicos"] == "SI") ? "S" : "N";
	$recpub = ($fila["personaje_publico"] == "SI") ? "S" : "N";

	$tipviv = "3";

	/*	switch($fila["tipo_vivienda"])
	{
		case "FAMILIAR": $tipviv = "3"; break;
		case "ARRENDADA": $tipviv = "2"; break;
		case "PROPIA": $tipviv = "1"; break;
		default: $tipviv = "3";
	}*/

	$tresanio = is_numeric(trim($fila["anios"])) ? trim($fila["anios"]) : 0;
	$tresanio = str_pad($tresanio, 2, 0, STR_PAD_LEFT);

	$tresmes = is_numeric(trim($fila["meses"])) ? trim($fila["meses"]) : 0;
	$tresmes = str_pad($tresmes, 2, 0, STR_PAD_LEFT);

	if (($tresanio == "00" && $tresmes = "00") || strlen($tresmes) > 2)
		$tresmes = "01";

	if ($tipviv == "2") {
		$nomarr = substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["arrendador_nombre"]))), 0, 40);
		$telarr = is_numeric(substr(trim($fila["arrendador_telefono"]), 0, 10)) ? substr(trim($fila["arrendador_telefono"]), 0, 10) : 0;
		$ciuarr = is_numeric($fila["arrendador_ciudad"]) ? $fila["arrendador_ciudad"] : 0;
	} else {
		$nomarr = "";
		$telarr = "0";
		$ciuarr = "0";
	}

	$nomarr = str_pad($nomarr, 40, " ", STR_PAD_RIGHT);
	$telarr = str_pad($telarr, 10, 0, STR_PAD_LEFT);
	$ciuarr = str_pad($ciuarr, 7, 0, STR_PAD_LEFT) . "000";

	$estrat = is_numeric(trim($fila["residencia_estrato"])) ? intval(trim($fila["residencia_estrato"])) : 0;

	if (!$estrat)
		$estrat = "1";

	$dirres = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 60), 60, " ", STR_PAD_RIGHT);

	if (trim($fila["residencia_barrio"]))
		$barres = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["residencia_barrio"]))), 0, 30);
	else
		$barres = "NO ESPECIFICADO";

	$barres = str_pad($barres, 30, " ", STR_PAD_RIGHT);

	$codcre = is_numeric($fila["ciudad"]) ? $fila["ciudad"] : "76001";
	$codcre = str_pad($codcre, 7, 0, STR_PAD_LEFT) . "000";

	$telres = is_numeric(substr(trim($fila["tel_residencia"]), 0, 10)) ? substr(trim($fila["tel_residencia"]), 0, 10) : 0;
	$telres = str_pad($telres, 10, 0, STR_PAD_LEFT);

	$telcel = is_numeric(substr(trim($fila["celular"]), 0, 10)) ? substr(trim($fila["celular"]), 0, 10) : 0;
	$telcel = str_pad($telcel, 10, 0, STR_PAD_LEFT);

	if ($telres == "0000000000")
		$telres = $telcel;

	if ($telcel == "0000000000")
		$telcel = $telres;

	$demail = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["email"]))), 0, 40), 40, " ", STR_PAD_RIGHT);

	$dirfam = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 60), 60, " ", STR_PAD_RIGHT);
	$barfam = $barres;

	$codcfa = is_numeric($fila["ciudad"]) ? $fila["ciudad"] : "76001";
	$codcfa = str_pad($codcfa, 7, 0, STR_PAD_LEFT) . "000";

	$telfam = $telres;
	$telfam = str_pad($telfam, 10, 0, STR_PAD_LEFT);

	$indcor = "5";

	/*	switch($fila["lugar_correspondencia"])
	{
		case "CASA": $indcor = "3"; break;
		case "OFICINA": $indcor = "4"; break;
		case "EMAIL": $indcor = "5"; break;
	}*/

	$indcor = str_pad($indcor, 1, 0, STR_PAD_LEFT);

	$asocor = "6";
	$autmail = "N";
	$autcel = "N";

	if ($fila["ocupacion"] == "1" || $fila["ocupacion"] == "2" || $fila["ocupacion"] == "5") {
		if ($fila["nombre_empresa"])
			$nomemp = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["nombre_empresa"]))), 0, 40);
		else
			$nomemp = substr(trim($fila["pagaduria"]), 0, 40);
	} else
		$nomemp = "";

	$nomemp = str_pad($nomemp, 40, " ", STR_PAD_RIGHT);
	$cpaires = str_pad("COL", 5, " ", STR_PAD_RIGHT);
	$filler1 = str_pad("", 19, " ", STR_PAD_RIGHT);
	$tipvin = str_pad($tipviv, 2, 0, STR_PAD_LEFT);
	$fecing = str_pad("0", 8, 0, STR_PAD_LEFT);
	$fecfin = str_pad("0", 8, 0, STR_PAD_LEFT);
	$tipcli = "2";
	$numint = str_pad("0", 17, 0, STR_PAD_LEFT);

	if ($fila["ocupacion"] == "1" || $fila["ocupacion"] == "2" || $fila["ocupacion"] == "5") {
		$actaotr = "EDUCATIVA";
		$tipemp = "2";
		$codacta = "24";

		if ($fila["tipo_contrato"])
			$tipcon = $fila["tipo_contrato"];
		else
			$tipcon = "1";

		if ($fila["fecha_vinculacion"] && $fila["fecha_vinculacion"] != "0000-00-00")
			$fecine = date("Ymd", strtotime($fila["fecha_vinculacion"]));
		else
			$fecine = date("Ymd", time());

		if ($fila["cargo"])
			$cargoemp = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["cargo"]))), 0, 30);
		else
			$cargoemp = ".";
	} else {
		$actaotr = "";
		$tipemp = "0";
		$codacta = "0";
		$tipcon = "0";
		$fecine = "0";
		$cargoemp = "";
	}

	$actaotr = str_pad($actaotr, 30, " ", STR_PAD_RIGHT);
	$tipemp = str_pad($tipemp, 5, 0, STR_PAD_LEFT);
	$codacta = str_pad($codacta, 5, 0, STR_PAD_LEFT);
	$tipcon = str_pad($tipcon, 2, 0, STR_PAD_LEFT);
	$fecine = str_pad($fecine, 8, 0, STR_PAD_LEFT);
	$cargoemp = str_pad($cargoemp, 30, " ", STR_PAD_RIGHT);
	$posneg = "N";
	$codciiu = str_pad("0", 15, 0, STR_PAD_LEFT);
	$nomempn = str_pad("", 40, " ", STR_PAD_RIGHT);
	$dirempn = str_pad("", 60, " ", STR_PAD_RIGHT);
	$tactanio = str_pad("0", 2, 0, STR_PAD_LEFT);
	$tactmes = str_pad("0", 2, 0, STR_PAD_LEFT);
	$venanu = str_pad("0", 14, 0, STR_PAD_LEFT);
	$telempn = str_pad("0", 10, 0, STR_PAD_LEFT);
	$faxempn = str_pad("0", 10, 0, STR_PAD_LEFT);
	$ciuempn = str_pad("0", 10, 0, STR_PAD_LEFT);
	$vlrsdo = str_pad($fila["total_ingresos"], 10, 0, STR_PAD_LEFT) . "00";
	$vlrsvar = str_pad("0", 14, 0, STR_PAD_LEFT);
	$ingarr = str_pad("0", 14, 0, STR_PAD_LEFT);
	$vlrrenf = str_pad("0", 14, 0, STR_PAD_LEFT);
	$vlrhon = str_pad("0", 12, 0, STR_PAD_LEFT);

	//	if (is_numeric($fila["otros_ingresos"]))
	//		$vlrotr = $fila["otros_ingresos"];
	//	else
	$vlrotr = 0;

	$vlrotr = str_pad($vlrotr, 10, 0, STR_PAD_LEFT) . "00";
	//$ingcual = str_pad(substr(trim($fila["detalle_ingresos"]), 0, 28), 28, " ", STR_PAD_RIGHT);
	$ingcual = str_pad("", 28, " ", STR_PAD_RIGHT);
	$vlrarr = str_pad("0", 12, 0, STR_PAD_LEFT);
	$vlrgfa = str_pad("0", 12, 0, STR_PAD_LEFT);
	$vlrcco = str_pad("0", 14, 0, STR_PAD_LEFT);
	$vlrpre = str_pad("0", 14, 0, STR_PAD_LEFT);
	$vlrdnom = str_pad("0", 14, 0, STR_PAD_LEFT);
	$vlrtcre = str_pad("0", 14, 0, STR_PAD_LEFT);

	if ($fila["total_egresos"] <= $fila["total_ingresos"])
		$egrcual = str_pad($fila["total_egresos"], 12, 0, STR_PAD_LEFT) . "00";
	else
		$egrcual = str_pad($fila["total_ingresos"] - 1, 12, 0, STR_PAD_LEFT) . "00";

	if ($egrcual == "00000000000000")
		$egrcual = "00000000000100";

	$actcor = str_pad("0", 12, 0, STR_PAD_LEFT);
	$actfij = str_pad("0", 12, 0, STR_PAD_LEFT);
	$actotr = str_pad("0", 12, 0, STR_PAD_LEFT);
	$actcual = str_pad("", 30, " ", STR_PAD_RIGHT);
	$pasfin = str_pad("0", 12, 0, STR_PAD_LEFT);
	$pascor = str_pad("0", 12, 0, STR_PAD_LEFT);
	$pasotr = str_pad("0", 12, 0, STR_PAD_LEFT);
	$pascual = str_pad("", 30, " ", STR_PAD_RIGHT);

	$dircom = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["direccion"]))), 0, 60), 60, " ", STR_PAD_RIGHT);
	$barcom = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["residencia_barrio"]))), 0, 30), 30, " ", STR_PAD_RIGHT);

	$codcco = is_numeric($fila["ciudad"]) ? $fila["ciudad"] : "76001";
	$codcco = str_pad($codcco, 7, 0, STR_PAD_LEFT) . "000";

	$telcom = $telres;
	$telcom = str_pad($telcom, 10, 0, STR_PAD_LEFT);

	$extcom = str_pad("0", 10, 0, STR_PAD_LEFT);
	$faxcom = str_pad("0", 10, 0, STR_PAD_LEFT);
	$codpat1 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$dcodpat1 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$dirpat1 = str_pad("", 40, " ", STR_PAD_RIGHT);
	$vlrpat1 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$hippat1 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$vlrpat3 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$codpat2 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$dcodpat2 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$dirpat2 = str_pad("", 40, " ", STR_PAD_RIGHT);
	$vlrpat4 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$hippat2 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$vlrpat6 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$notas5 = str_pad("", 15, " ", STR_PAD_RIGHT);
	$vlrpat7 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$notas8 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$notas10 = str_pad("", 10, " ", STR_PAD_RIGHT);
	$vlrpat8 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$resdom1 = str_pad("", 55, " ", STR_PAD_RIGHT);
	$notas12 = str_pad("", 15, " ", STR_PAD_RIGHT);
	$vlrpat9 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$notas13 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$notas14 = str_pad("", 10, " ", STR_PAD_RIGHT);
	$vlrpat10 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$resdom2 = str_pad("", 55, " ", STR_PAD_RIGHT);
	$obien1 = str_pad("", 25, " ", STR_PAD_RIGHT);
	$osalcre1 = str_pad("0", 14, 0, STR_PAD_LEFT);
	$ovalcom1 = str_pad("0", 14, 0, STR_PAD_LEFT);
	$opiga1 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$obien2 = str_pad("", 25, " ", STR_PAD_RIGHT);
	$osalcre2 = str_pad("0", 14, 0, STR_PAD_LEFT);
	$ovalcom2 = str_pad("0", 14, 0, STR_PAD_LEFT);
	$opiga2 = str_pad("", 20, " ", STR_PAD_RIGHT);

	if ($fila["estado_civil"] == "CASADO") {
		$ctipdoc = "";

		switch ($fila["conyugue_tipo_documento"]) {
			case "CEDULA":
				$ctipdoc = "1";
				break;
			case "CEDULA EXTRANGERIA":
				$ctipdoc = "2";
				break;
			case "TARJETA IDENTIDAD":
				$ctipdoc = "4";
				break;
			case "REGISTRO CIVIL":
				$ctipdoc = "6";
				break;
		}

		if (!$ctipdoc)
			$ctipdoc = "1";

		$cnitcli = substr(trim($fila["cedula_conyugue"]), 0, 17);

		if ($fila["conyugue_lugar_expedicion"])
			$clugexp = $fila["conyugue_lugar_expedicion"];
		else if ($fila["conyugue_lugar_nacimiento"])
			$clugexp = $fila["conyugue_lugar_nacimiento"];
		else if ($fila["ciudad"])
			$clugexp = $fila["ciudad"];
		else
			$clugexp = "76001";

		if ($fila["conyugue_fecha_expedicion"])
			$cfecesc = date("Ymd", strtotime($fila["conyugue_fecha_expedicion"]));
		else
			$cfecesc = date("Ymd", strtotime($fila["minima_fecha_expedicion_conyugue"]));

		if (strtotime($fila["conyugue_fecha_expedicion"]) > strtotime($fila["minima_fecha_expedicion_conyugue"]))
			$cfecesc = date("Ymd", strtotime($fila["conyugue_fecha_expedicion"]));
		else
			$cfecesc = date("Ymd", strtotime($fila["minima_fecha_expedicion_conyugue"]));

		$cprmapl = substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["conyugue_apellido_1"]))), 0, 20);
		$csgdapl = substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["conyugue_apellido_2"]))), 0, 20);
		$cprmnom = substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["nombre_conyugue"]))), 0, 20);
		$csgdnom = substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["conyugue_nombre_2"]))), 0, 20);
		$cfeccon = date("Ymd", strtotime($fila["conyugue_fecha_nacimiento"]));

		if ($fila["conyugue_lugar_nacimiento"])
			$clugcon = $fila["conyugue_lugar_nacimiento"];
		else if ($fila["conyugue_lugar_expedicion"])
			$clugcon = $fila["conyugue_lugar_expedicion"];
		else if ($fila["ciudad"])
			$clugcon = $fila["ciudad"];
		else
			$clugcon = "76001";

		$ccodsex = "";

		switch ($fila["conyugue_sexo"]) {
			case "M":
				$ccodsex = "1";
				break;
			case "F":
				$ccodsex = "2";
				break;
		}

		if (!$ccodsex)
			if ($fila["sexo"] == "M")
				$ccodsex = "2";
			else
				$ccodsex = "1";

		$cocupac = "";

		switch ($fila["conyugue_ocupacion"]) {
			case "EMPLEADO":
				$cocupac = "1";
				break;
			case "INDEPENDIENTE":
				$cocupac = "2";
				break;
			case "PENSIONADO":
				$cocupac = "5";
				break;
			case "AMA DE CASA":
				$cocupac = "8";
				break;
			case "ESTUDIANTE":
				$cocupac = "9";
				break;
			case "RENTISTA CAPITAL":
				$cocupac = "7";
				break;
		}

		$cdepeco = ($fila["conyugue_dependencia"] == "SI") ? "S" : "N";

		if ($fila["conyugue_ocupacion"] == "EMPLEADO" || $fila["conyugue_ocupacion"] == "INDEPENDIENTE") {
			$cnomemp = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["conyugue_nombre_empresa"]))), 0, 40);
			$ctoting = is_numeric($fila["conyugue_total_ingresos"]) ? $fila["conyugue_total_ingresos"] : 0;

			if ($fila["conyugue_ocupacion"] == "EMPLEADO") {
				if ($fila["conyugue_fecha_vinculacion"])
					$cfecing = date("Ymd", strtotime($fila["conyugue_fecha_vinculacion"]));
				else
					$cfecing = "0";

				$ccaremp = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["conyugue_cargo"]))), 0, 30);
			} else {
				$cfecing = "0";
				$ccaremp = "";
			}
		} else {
			$cnomemp = "";
			$ctoting = "0";
			$cfecing = "0";
			$ccaremp = "";
		}

		$cindest = "";

		switch ($fila["conyugue_nivel_estudios"]) {
			case "PRIMARIA":
				$cindest = "10";
				break;
			case "BACHILLER":
				$cindest = "1";
				break;
			case "TECNICO":
				$cindest = "2";
				break;
			case "TECNOLOGO":
				$cindest = "3";
				break;
			case "UNIVERSITARIO":
				$cindest = "4";
				break;
			case "ESPECIALIZACION":
				$cindest = "5";
				break;
			case "MAESTRIA":
				$cindest = "6";
				break;
			case "DOCTORADO":
				$cindest = "7";
				break;
			default:
				$cindest = "9";
		}
	} else {
		$ctipdoc = "0";
		$cnitcli = "0";
		$clugexp = "0";
		$cfecesc = "0";
		$cprmapl = "";
		$csgdapl = "";
		$cprmnom = "";
		$csgdnom = "";
		$cfeccon = "0";
		$clugcon = "0";
		$ccodsex = "0";
		$cocupac = "0";
		$cdepeco = "";
		$cnomemp = "";
		$ctoting = "0";
		$cfecing = "0";
		$ccaremp = "";
		$cindest = "0";
	}

	$ctipdoc = str_pad($ctipdoc, 1, 0, STR_PAD_LEFT);
	$cnitcli = str_pad($cnitcli, 17, 0, STR_PAD_LEFT);
	$clugexp = str_pad($clugexp, 7, 0, STR_PAD_LEFT) . "000";
	$cfecesc = str_pad($cfecesc, 8, 0, STR_PAD_LEFT);
	$cprmapl = str_pad($cprmapl, 20, " ", STR_PAD_RIGHT);
	$csgdapl = str_pad($csgdapl, 20, " ", STR_PAD_RIGHT);
	$cprmnom = str_pad($cprmnom, 20, " ", STR_PAD_RIGHT);
	$csgdnom = str_pad($csgdnom, 20, " ", STR_PAD_RIGHT);
	$cfeccon = str_pad($cfeccon, 8, 0, STR_PAD_LEFT);
	$clugcon = str_pad($clugcon, 7, 0, STR_PAD_LEFT) . "000";
	$ccodsex = str_pad($ccodsex, 1, 0, STR_PAD_LEFT);
	$cocupac = str_pad($cocupac, 3, 0, STR_PAD_LEFT);
	$cdepeco = str_pad($cdepeco, 1, " ", STR_PAD_RIGHT);
	$cnomemp = str_pad($cnomemp, 40, " ", STR_PAD_RIGHT);
	$ctoting = str_pad($ctoting, 12, 0, STR_PAD_LEFT) . "00";
	$cfecing = str_pad($cfecing, 8, 0, STR_PAD_LEFT);
	$ccaremp = str_pad($ccaremp, 30, " ", STR_PAD_RIGHT);
	$cindest = str_pad($cindest, 2, 0, STR_PAD_LEFT);
	$nomref1 = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["nombre_familiar"]))), 0, 30), 30, " ", STR_PAD_RIGHT);

	switch ($fila["parentesco_familiar"]) {
		case "ABUELO(A)":
			$parent = "AB";
			break;
		case "COMPANERO(A)":
			$parent = "CA";
			break;
		case "CONYUGE":
			$parent = "CY";
			break;
		case "CONYUGE SOLIDARIO(A)":
			$parent = "CS";
			break;
		case "CUNADO(A)":
			$parent = "CU";
			break;
		case "HERMANASTRO(A)":
			$parent = "HD";
			break;
		case "HERMANO(A)":
			$parent = "HE";
			break;
		case "HIJA":
			$parent = "HA";
			break;
		case "HIJASTRO(A)":
			$parent = "HT";
			break;
		case "HIJO":
			$parent = "HO";
			break;
		case "MADRASTRA":
			$parent = "MD";
			break;
		case "MADRE":
			$parent = "MA";
			break;
		case "NIETO(A)":
			$parent = "NT";
			break;
		case "NUERA":
			$parent = "NU";
			break;
		case "OTRO":
			$parent = "OT";
			break;
		case "PADRASTRO":
			$parent = "PD";
			break;
		case "PADRE":
			$parent = "PA";
			break;
		case "PRIMO(A)":
			$parent = "PR";
			break;
		case "SOBRINO(A)":
			$parent = "SB";
			break;
		case "SUEGRO(A)":
			$parent = "SU";
			break;
		case "TIO(A)":
			$parent = "TI";
			break;
		case "YERNO":
			$parent = "YE";
			break;
		default:
			$parent = "OT";
	}

	$parent = str_pad($parent, 2, " ", STR_PAD_RIGHT);

	$dirref1 = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["direccion_familiar"]))), 0, 28), 28, " ", STR_PAD_RIGHT);

	$codciu1 = is_numeric($fila["ciudad_familiar"]) ? $fila["ciudad_familiar"] : "76001";
	$codciu1 = str_pad($codciu1, 7, 0, STR_PAD_LEFT) . "000";

	$telref1 = is_numeric(substr(trim($fila["telefono_familiar"]), 0, 10)) ? substr(trim($fila["telefono_familiar"]), 0, 10) : "3150000001";
	$telref1 = str_pad($telref1, 10, 0, STR_PAD_LEFT);

	$nomref2 = str_pad(substr(trim(preg_replace("/[^" . $normal_characters_nombres . "/", "", reemplazar_caracteres_no_utf($fila["nombre_personal"]))), 0, 30), 30, " ", STR_PAD_RIGHT);
	$profes1 = str_pad("0", 6, 0, STR_PAD_LEFT);
	$dprofes1 = str_pad("", 25, " ", STR_PAD_RIGHT);
	$dirref2 = str_pad(substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["direccion_personal"]))), 0, 28), 28, " ", STR_PAD_RIGHT);

	$codciu2 = is_numeric($fila["ciudad_personal"]) ? $fila["ciudad_personal"] : "76001";
	$codciu2 = str_pad($codciu2, 7, 0, STR_PAD_LEFT) . "000";

	$telref2 = is_numeric(substr(trim($fila["telefono_personal"]), 0, 10)) ? substr(trim($fila["telefono_personal"]), 0, 10) : "3150000001";
	$telref2 = str_pad($telref2, 10, 0, STR_PAD_LEFT);

	$nomref6 = str_pad("", 30, " ", STR_PAD_RIGHT);
	$sucref1 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$ind0011 = "N";
	$ind0021 = "N";
	$ind0031 = "N";

	$indmone = ($fila["moneda_extranjera"] == "SI") ? "S" : "N";
	$indcuee = ($fila["num_cuenta"]) ? "S" : "N";

	if ($fila["num_cuenta"]) {
		$indtrae = "99";
		$notamone = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["tipo_transaccion"]))), 0, 40);
	} else {
		$indtrae = "0";
		$notamone = "";
	}

	$indtrae = str_pad($indtrae, 2, 0, STR_PAD_LEFT);
	$notamone = str_pad($notamone, 40, " ", STR_PAD_RIGHT);

	if ($fila["moneda_extranjera"] == "SI") {
		$nomban1 = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["banco"]))), 0, 40);
		$ctaban1 = is_numeric($fila["num_cuenta"]) ? $fila["num_cuenta"] : 0;
		$ciuban1 = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["ciudad_operaciones"]))), 0, 40);
		$paiban1 = substr(trim(preg_replace("/[^" . $normal_characters . "/", "", reemplazar_caracteres_no_utf($fila["pais_operaciones"]))), 0, 40);
	} else {
		$nomban1 = "";
		$ctaban1 = "0";
		$ciuban1 = "";
		$paiban1 = "";
	}

	$nomban1 = str_pad($nomban1, 40, " ", STR_PAD_RIGHT);
	$ctaban1 = str_pad($ctaban1, 17, 0, STR_PAD_LEFT);
	$ciuban1 = str_pad($ciuban1, 40, " ", STR_PAD_RIGHT);
	$monban1 = str_pad("", 15, " ", STR_PAD_RIGHT);
	$paiban1 = str_pad($paiban1, 40, " ", STR_PAD_RIGHT);
	$nomban2 = str_pad("", 40, " ", STR_PAD_RIGHT);
	$ctaban2 = str_pad("0", 17, 0, STR_PAD_LEFT);
	$ciuban2 = str_pad("", 40, " ", STR_PAD_RIGHT);
	$monban2 = str_pad("", 15, " ", STR_PAD_RIGHT);
	$paiban2 = str_pad("", 40, " ", STR_PAD_RIGHT);

	$jvpar = "N";
	$jvnom1 = str_pad("", 50, " ", STR_PAD_RIGHT);
	$jvpar1 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$jvnom2 = str_pad("", 50, " ", STR_PAD_RIGHT);
	$jvpar2 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$barempn = str_pad("", 30, " ", STR_PAD_RIGHT);
	$tipprd1 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$tipprd2 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$moncta1 = str_pad("0", 17, 0, STR_PAD_LEFT);
	$moncta2 = str_pad("0", 17, 0, STR_PAD_LEFT);
	$filler2 = str_pad("", 24, " ", STR_PAD_RIGHT);
	$nciona1 = str_pad("COLOMBIANO", 20, " ", STR_PAD_RIGHT);
	$nciona2 = str_pad("COLOMBIANO", 20, " ", STR_PAD_RIGHT);
	$permeua = "N";
	$grecard = "N";
	$fdap = "N";
	$acteua = "N";
	$ciueua = "N";
	$trbeua = "N";
	$tin = str_pad("", 20, " ", STR_PAD_RIGHT);
	$hippig1 = "N";
	$hippig2 = "N";
	$codpat3 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$dcodpat3 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$hippig3 = "N";
	$codpat4 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$dcodpat4 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$hippig4 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$vlrpat11 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$codpat5 = str_pad("0", 2, 0, STR_PAD_LEFT);
	$dcodpat5 = str_pad("", 20, " ", STR_PAD_RIGHT);
	$notas15 = str_pad("", 15, " ", STR_PAD_RIGHT);
	$hippig5 = "N";
	$vlrpat12 = str_pad("0", 12, 0, STR_PAD_LEFT);
	$nit = str_pad("0", 10, 0, STR_PAD_LEFT);
	$adicional_no_esta_en_documentacion = "0";

	$registro = $agcvin . $tipdoc . $nitcli . $lugexp . $fecesc . $lugcon . $feccon . $prmapl . $sgdapl . $prmnom . $sgdnom . $codsex . $estciv . $percar . $perm18 . $indest . $nomti1 . $ocupac . $decren . $funpub . $admrpu . $recpub . $tipviv . $tresanio . $tresmes . $nomarr . $telarr . $ciuarr . $estrat . $dirres . $barres . $codcre . $telres . $telcel . $demail . $dirfam . $barfam . $codcfa . $telfam . $indcor . $asocor . $autmail . $autcel . $nomemp . $cpaires . $filler1 . $tipvin . $fecing . $fecfin . $tipcli . $numint . $actaotr . $tipemp . $codacta . $tipcon . $fecine . $cargoemp . $posneg . $codciiu . $nomempn . $dirempn . $tactanio . $tactmes . $venanu . $telempn . $faxempn . $ciuempn . $vlrsdo . $vlrsvar . $ingarr . $vlrrenf . $vlrhon . $vlrotr . $ingcual . $vlrarr . $vlrgfa . $vlrcco . $vlrpre . $vlrdnom . $vlrtcre . $egrcual . $actcor . $actfij . $actotr . $actcual . $pasfin . $pascor . $pasotr . $pascual . $dircom . $barcom . $codcco . $telcom . $extcom . $faxcom . $codpat1 . $dcodpat1 . $dirpat1 . $vlrpat1 . $hippat1 . $vlrpat3 . $codpat2 . $dcodpat2 . $dirpat2 . $vlrpat4 . $hippat2 . $vlrpat6 . $notas5 . $vlrpat7 . $notas8 . $notas10 . $vlrpat8 . $resdom1 . $notas12 . $vlrpat9 . $notas13 . $notas14 . $vlrpat10 . $resdom2 . $obien1 . $osalcre1 . $ovalcom1 . $opiga1 . $obien2 . $osalcre2 . $ovalcom2 . $opiga2 . $ctipdoc . $cnitcli . $clugexp . $cfecesc . $cprmapl . $csgdapl . $cprmnom . $csgdnom . $cfeccon . $clugcon . $ccodsex . $cocupac . $cdepeco . $cnomemp . $ctoting . $cfecing . $ccaremp . $cindest . $nomref1 . $parent . $dirref1 . $codciu1 . $telref1 . $nomref2 . $profes1 . $dprofes1 . $dirref2 . $codciu2 . $telref2 . $nomref6 . $sucref1 . $ind0011 . $ind0021 . $ind0031 . $indmone . $indcuee . $indtrae . $notamone . $nomban1 . $ctaban1 . $ciuban1 . $monban1 . $paiban1 . $nomban2 . $ctaban2 . $ciuban2 . $monban2 . $paiban2 . $jvpar . $jvnom1 . $jvpar1 . $jvnom2 . $jvpar2 . $barempn . $tipprd1 . $tipprd2 . $moncta1 . $moncta2 . $filler2 . $nciona1 . $nciona2 . $permeua . $grecard . $fdap . $acteua . $ciueua . $trbeua . $tin . $hippig1 . $hippig2 . $codpat3 . $dcodpat3 . $hippig3 . $codpat4 . $dcodpat4 . $hippig4 . $vlrpat11 . $codpat5 . $dcodpat5 . $notas15 . $hippig5 . $vlrpat12 . $nit . $adicional_no_esta_en_documentacion;

	echo $registro . "\r\n";
}
?>