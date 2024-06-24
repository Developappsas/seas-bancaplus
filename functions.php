<?php
@session_start();

//header("Expires: ".gmdate("D, d M Y H:i:s", 0)." GMT");
if (function_exists('header_remove')) {
    header_remove('X-Powered-By'); // PHP 5.3+
} else {
    @ini_set('expose_php', 'off');
}
ini_set('default_charset', 'UTF-8');

date_default_timezone_set("America/Bogota");
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
//error_reporting(0);

$PasswordMail = "nsndkjfzlhpyzrqx";
$normal_characters = "a-zA-Z0-9\sáéíóúñÁÉÍÓÚÑ°`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]]";

$normal_characters_nombres = "a-zA-Z\sáéíóúñÁÉÍÓÚÑ]";

$nombre_meses[1] = "Ene";	$nombre_meses[2] = "Feb";
$nombre_meses[3] = "Mar";	$nombre_meses[4] = "Abr";
$nombre_meses[5] = "May";	$nombre_meses[6] = "Jun";
$nombre_meses[7] = "Jul";	$nombre_meses[8] = "Ago";
$nombre_meses[9] = "Sep";	$nombre_meses[10] = "Oct";
$nombre_meses[11] = "Nov";	$nombre_meses[12] = "Dic";

$nombre_meses_completo[1] = "Enero";	$nombre_meses_completo[2] = "Febrero";
$nombre_meses_completo[3] = "Marzo";	$nombre_meses_completo[4] = "Abril";
$nombre_meses_completo[5] = "Mayo";		$nombre_meses_completo[6] = "Junio";
$nombre_meses_completo[7] = "Julio";	$nombre_meses_completo[8] = "Agosto";
$nombre_meses_completo[9] = "Septiembre";	$nombre_meses_completo[10] = "Octubre";
$nombre_meses_completo[11] = "Noviembre";	$nombre_meses_completo[12] = "Diciembre";
$urlPrincipal=url_origin();
$label_title = "";
$label_viable = "VIABLE";
$label_negado = "NEGADO";
$label_aho = "AHO";
$label_cte = "CTE";
$cod_interno_subestado_aprobado_pdte_visado = "45";
$subestados_sin_concretar = "'1'";
$subestados_neg_solicitando_cartas = "'16'";
$subestados_terminado = array(71,30,28,27,23,66,54,56,57);
// $subestados_estudio = array(70,72);
$subestados_estudio = array(70);
$subestados_validar_grantias = array(72);
//$subestados_formulario_digital = array(4,19,62,64);
$subestados_formulario_digital = array(3);
$subestados_tesoreria_ccvecimientos= array(31,48);
$subestados_agenda = "'30', '71', '70', '66', '72', '54', '56', '65', '19', '4', '62', '64', '5', '51', '31', '48', '14', '46', '49'";
$subestados_tesoreria = "'51', '31', '48', '14', '46', '49', '74', '78', '83', '84', '85' ";
$subestados_tesoreria2 = " '31', '48', '14', '46', '78', '84'";
$subestados_tesoreria_no_giro = "'51', '49', '74'";
$subestado_prospectado = "1";
$subestado_ingresado_pdte_soportes_documentos = "71";
$subestado_valid_doc_proforense = "70";
$subestado_soportes_completos = "72";
$subestado_firmado = "2";
$subestado_radicado = "3";
$subestado_procesado = "18";
$subestado_segunda_revision = "59";
$subestado_aprobado = "19";
$subestado_aprobado_pdte_visado = "4";
$subestado_aprobado_pdte_incorp = "62";
$subestado_visado = "5";
$subestado_tesoreria_con_pdtes = "51";
$subestado_confirmado = "31";
$subestado_compras_desembolso = "48, 78";
$subestado_desembolso = "14";
$subestado_desembolso_cliente = "46";
$subestado_desembolso_pdte_bloqueo = "49";
$subestados_desembolso_nuevos_tesoreria = "83, 84, 85";
$subestado_incorporado = "44";
$subestado_auditado = "43";
$subestado_negociar_cartera = "7";
$subestado_devuelto_por_inconsistencias = "28";
$tiposadjuntos_prospeccion = "'32', '31', '1', '4', '3', '2', '6', '47'";
$tiposadjuntos_firmados = "'55', '56', '57', '58', '59', '60'";
$tipoadjunto_pys = "16";
$tipoadjunto_cdd = "21";
$tiporeq_cdd = "1";
$entidad_seguro = "834";
$oficinas_prospeccion = "'0'";
$area_credito = "1";
$area_visado = "2";
$area_gestion_comercial = "5";
$usuario_master = "1";
$usuario_trecuperamos = "18";
$prefijo_tablas = "";
$separador_csv = ";";
$consulta_centrales = "0";
$experian_userid = "900387878";
$experian_password = "02LTK";
$usuarios_permiso_gastosadmin=array(4586);
$array_unidad_negocio_comision_fianti = array(4, 11, 14, 19, 23, 27, 31, 32, 41, 42, 52, 53, 64);
$array_unidad_negocio_comision_atraccion = array(6, 15, 21, 25);
$array_unidad_negocio_comision_salvamento = array(2, 12, 16, 22, 26);
$array_unidad_negocio_comision_kredit = array(1, 10, 17, 20, 24);
$array_unidad_negocio_valor_x_millon = array(92, 93, 94, 95);
$tipoadjuntos_requeridosFirma = "9, 22, 23, 25, 29, 32";
$array_pagadurias_excluidas_formato_seguro = array('FOPEP', 'CASUR', 'CREMIL');
$subestados_preaprobado = array(1, 69, 71);
$subestados_bloqueo_comprascartera =array(78);

$cod_interno_subestado_caracterizacion = "55";//SAP

//parametros de visados e incoporaciones
$usuario_seas_modulo_incorporaciones = "seas";
$passwd_seas_modulo_incorporaciones = "Y2lO346K0h";
$token_seas_modulo_incorporaciones = "CMETYMICXZ";
$url_layer_security = "https://az-ase-use2-prd-exp-back-layersecurity-k.azurewebsites.net/api/";
$url_app_visados = "https://az-ase-use2-prd-exp-back-inc-k.azurewebsites.net/api/";
$url_app_comercial = "https://az-ase-use2-prd-back-comercial-k.azurewebsites.net/api/";

//parametros MASIVAPP OTP
$rutaOTP = 'http://otp-manager.masivapp.com/transactional-api/v1/';
$basicAuthOTP = 'S2NyZWRpdF9PVFBfOTlKRkw6ZHNONDZrMGwkRg==';
$otpConfigID_masivapp = 'cbfaf89d-4bb2-4208-9c26-f42d42411d24';
$hubFlowId_masivapp = '65ae9985dc41060011e23dfc';

function url_origin(){

	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ){
		$ssl = true;
	}else if(isset($_SERVER["HTTP_X_APPSERVICE_PROTO"]) && $_SERVER["HTTP_X_APPSERVICE_PROTO"] == 'https'){
		$ssl = true;
	}else{
		$ssl = false;
	}

	$sp = strtolower($_SERVER['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos( $sp, '/'  )) . (( $ssl ) ? 's' : '');

	$host = $_SERVER['HTTP_HOST'];

	if($host == 'localhost' || $host == '127.0.0.1'){
		$rutaOr = $protocol . '://' . $host . '/' . basename(dirname(__FILE__));
	}else{
		$rutaOr = $protocol . '://' . $host;
	}

	return $rutaOr;
}

function url_servicios_centrales($servicio,$parametro1,$parametro2,$parametro3,$parametro4,$parametro5) {
    $url_servicio="";
    switch ($servicio) {

        case "EXPERIAN": $url_servicio = "".$parametro1."/".$parametro2."/".$parametro3."/".$parametro4."/".$parametro5; break;
        case "INFORMACION_COMERCIAL": $url_servicio = "".$parametro1."/".$parametro2."/".$parametro3."/".$parametro4."/".$parametro5; break;
        case "UBICAPLUS": $url_servicio = "".$parametro1."/".$parametro2."/".$parametro3."/".$parametro4."/".$parametro5; break;
        case "CREDITVISION": $url_servicio = "".$parametro1."/".$parametro2."/".$parametro3."/".$parametro4."/".$parametro5; break;
        case "LEGALCHECK": $url_servicio = "".$parametro1."/".$parametro2."/".$parametro3."/".$parametro4."/".$parametro5; break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data){
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        break;

    }
    return $url_servicio;
}
//$experian_hdcacierta_url = "";
$experian_hdcacierta_product = "";
$transunion_userid = "";
$transunion_password = "";
$transunion_reason = "";
$transunion_infocomercial_url = "";
$transunion_infocomercial_product = "";
$transunion_legalcheck_url = "";
$transunion_legalcheck_product = "";
$transunion_ubicaplus_url = "";
$transunion_ubicaplus_product = "";

function conectar() {
	$serverName = "database";
    $user = "sa";
    $passwd = $_ENV['DB_PASSWORD'];
    $db = "seas";
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 0);

	$link = sqlsrv_connect($serverName, $connectionInfo);
	if ($link) {
		// echo "Conexión establecida.";
		return $link;
	}else{
		echo json_encode('Error al conectarse con la base de datos');
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
			}
		}
	}
}

function conectar_utf() {
	$serverName = "database";
    $user = "sa";
    $passwd = $_ENV['DB_PASSWORD'];
    $db = "seas";
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 0);
	$link = sqlsrv_connect($serverName, $connectionInfo);
	if ($link) {
		//echo "Conexión establecida.";
		return $link;
	}else{
		echo json_encode('Error al conectarse con la base de datos');
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
			}
		}
	}
}

function conectar_consultas_externas() {
	$serverName = "database";
    $user = "sa";
    $passwd = $_ENV['DB_PASSWORD'];
    $db = "seas";
    $connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $passwd, "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true, "LoginTimeout" => 30, "Encrypt" => 0);
	$link = sqlsrv_connect($serverName, $connectionInfo);
	if ($link) {
		//echo "Conexión establecida.";
		return $link;
	}else{
		echo json_encode('Error al conectarse con la base de datos');
		if( ($errors = sqlsrv_errors() ) != null) {
			foreach( $errors as $error ) {
				echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
				echo "code: ".$error[ 'code']."<br />";
				echo "message: ".$error[ 'message']."<br />";
			}
		}
	}
}

function js_padre_hija($tabla_padre, $codigo_tp, $descripcion_tp, $tabla_hija, $codigotp_th, $codigo_th, $descripcion_th, $link)
{

?>

function valor_<?php echo $tabla_hija ?>(x){return x.substring(0,x.indexOf('-'))}

function texto_<?php echo $tabla_hija ?>(x){return x.substring(x.indexOf('-')+1,x.length)}

function Cargar<?php echo $tabla_hija ?>(id_<?php echo $tabla_padre ?>, objeto_<?php echo $tabla_hija ?>) {
	var num_<?php echo $tabla_hija ?>;
	var j, k = 1;

	num_<?php echo $tabla_hija ?> = 200;

	objeto_<?php echo $tabla_hija ?>.length = num_<?php echo $tabla_hija ?>;

<?php

	$datos_padre = sqlsrv_query($link,"select $codigo_tp, $descripcion_tp from $tabla_padre order by $codigo_tp");

	$datos_hija = sqlsrv_query($link,"select $codigo_th, $codigotp_th, $descripcion_th from $tabla_hija order by $codigotp_th, $descripcion_th");

	$fila2 = sqlsrv_fetch_array($datos_hija);

	while ($fila = sqlsrv_fetch_array($datos_padre))
	{
		$padre_hija = "PH".$fila["$codigo_tp"]." = [";

		while ($fila2["$codigotp_th"] == $fila["$codigo_tp"])
		{
			$padre_hija .= "\"".$fila2["$codigo_th"]."-".$fila2["$descripcion_th"]."\",";

			$fila2 = sqlsrv_fetch_array($datos_hija);
		}

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;
	}

	echo "\nswitch(id_$tabla_padre) {\n";

	if (sqlsrv_num_rows($datos_padre))
	{
		sqlsrv_data_seek($datos_padre, 0);
	}

	while ($fila = sqlsrv_fetch_array($datos_padre))
	{

?>

		case '<?php echo $fila["$codigo_tp"] ?>':	num_<?php echo $tabla_hija ?> = PH<?php echo $fila["$codigo_tp"] ?>.length;
				for(j = 0; j < num_<?php echo $tabla_hija ?>; j++) {
					objeto_<?php echo $tabla_hija ?>.options[k].value = valor_<?php echo $tabla_hija ?>(PH<?php echo $fila["$codigo_tp"] ?>[j]);
					objeto_<?php echo $tabla_hija ?>.options[k].text = texto_<?php echo $tabla_hija ?>(PH<?php echo $fila["$codigo_tp"] ?>[j]);
					k++;
				}
				break;

<?php

	}

?>

		default:	num_<?php echo $tabla_hija ?> = 1;
				k=0;
	}

	objeto_<?php echo $tabla_hija ?>.selectedIndex = 0;
	objeto_<?php echo $tabla_hija ?>.length = num_<?php echo $tabla_hija ?>;

	return true;
}

<?php

}

function reemplazar_caracteres($string)
{
	$oldChars = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
	$newChars = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N");
	$string = str_replace($oldChars, $newChars, utf8_decode($string));

	return $string;
}

function reemplazar_caracteres_no_utf($string)
{
	$oldChars = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
	$newChars = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N");
	$string = str_replace($oldChars, $newChars, $string);

	return $string;
}

function reemplazar_caracteres_por_html($string)
{
	$oldChars = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ");
	$newChars = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&Aacute;", "&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&ntilde;", "&Ntilde;");
	$string = str_replace($oldChars, $newChars, utf8_decode($string));

	return $string;
}

function reemplazar_caracteres_WS($string)
{
	$oldChars = array('\n', '\"', '\u003c', '\u003e', '\u0026');
	$newChars = array('', '"', '<', '>', '&');
	$string = str_replace($oldChars, $newChars, utf8_decode($string));

	$string = preg_replace('/\s\s+/', ' ', $string);

	if (!strpos($string, '"'))
		$string = substr($string, 1);

	if (strrpos($string, '"') == strlen($string) - 1)
		$string = substr($string, 0, strlen($string) - 1);

	return $string;
}

function reemplazar_caracteres_WS2($string)
{
	$oldChars = array('\n', '\"', 'u003c', 'u003e', 'u0026');
	$newChars = array('', '"', '<', '>', '&');
	$string = str_replace($oldChars, $newChars, utf8_decode($string));

	$string = preg_replace('/\s\s+/', ' ', $string);

	if (!strpos($string, '"'))
		$string = substr($string, 1);

	if (strrpos($string, '"') == strlen($string) - 1)
		$string = substr($string, 0, strlen($string) - 1);

	return $string;
}

function Dias ($fecha, $diasLimiteOriginal, $diasFestivos)
{
	$addDiasLimite = 0;

	while ($addDiasLimite < $diasLimiteOriginal)
	{
		$addDiasLimite++;

		$dt = date("Y-m-d",strtotime("+".$addDiasLimite." days"));

		if (date("l",strtotime($dt)) == 'Saturday' || date("l", strtotime($dt)) == 'Sunday')
			$diasLimiteOriginal++;

		//Si la fecha cae un festivo. dela ista de festivos en Perido Proceso
		if (array_search($dt, $diasFestivos) != NULL)
			$diasLimiteOriginal++;
	}

	return $addDiasLimite;
}

function DeviceDetect()
{
	$tablet_browser = 0;
	$mobile_browser = 0;

	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$tablet_browser++;
	}

	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$mobile_browser++;
	}

	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$mobile_browser++;
	}

	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
	$mobile_agents = array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		'newt','noki','palm','pana','pant','phil','play','port','prox',
		'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		'wapr','webc','winw','winw','xda ','xda-');

	if (in_array($mobile_ua,$mobile_agents)) {
		$mobile_browser++;
	}

	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
		$mobile_browser++;
		//Check for tablets on opera mini alternative headers
		$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
			$tablet_browser++;
		}
	}

	if ($tablet_browser > 0) {
		// do something for tablet devices
		return 'tablet';
	}
	else if ($mobile_browser > 0) {
		// do something for mobile devices
		return 'mobile';
	}
	else {
		// do something for everything else
		return 'desktop';
	}
}

function callAPI($method, $url, $data, $parameter) {
	$curl = curl_init();

	switch ($method) {
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data){
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
		break;

		case "PUT":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data){
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
		break;

		default:
			if ($data){
				$url = sprintf("%s?%s", $url, http_build_query($data));
			}
		break;
	}

	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url.$parameter);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		"apikey: db92efc69991",
		"Content-Type: application/json",
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	// EXECUTE:
	$result = curl_exec($curl);
	if(!$result){$result = "ERROR";}
	curl_close($curl);
	return $result;
}

function WSCentrales($url, $parametros)
{
	global $link;
	$url2 = $url;

	$path = parse_url($url2, PHP_URL_PATH);

	if ($path) {
		$segments = explode('/', $path);
		foreach($segments as $index => $segment) {
			$segments[$index] = urlencode($segment);
		}

		$url2 = str_replace($path, implode('/', $segments), $url2);
	}
	$url2 = str_replace('+','%20',$url2);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $url2,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
			));

	$response = curl_exec($curl);
	curl_close($curl);

	return $response;
}
function puntuar_miles($n) {
    // first strip any formatting;
    $n = (0+str_replace(".","",$n));

    // is this a number?
    if(!is_numeric($n)) return false;

    // now filter it;
    if($n>1000000000000) return round(($n/1000000000000),1).'.000.000.000.000';
    else if($n>1000000000) return round(($n/1000000000),1).'.000.000.000';
    else if($n>1000000) return round(($n/1000000),1).'.000.000';
    else if($n>1000) return round(($n/1000),1).'.000';

    return number_format($n);
}
?>