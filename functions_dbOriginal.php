<?php

header("Expires: ".gmdate("D, d M Y H:i:s", 0)." GMT");

session_start();

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);

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
$nombre_meses_completo[5] = "Mayo";	$nombre_meses_completo[6] = "Junio";
$nombre_meses_completo[7] = "Julio";	$nombre_meses_completo[8] = "Agosto";
$nombre_meses_completo[9] = "Septiembre";	$nombre_meses_completo[10] = "Octubre";
$nombre_meses_completo[11] = "Noviembre";	$nombre_meses_completo[12] = "Diciembre";

$label_title = "";
$label_viable = "VIABLE";
$label_negado = "NEGADO";
$label_aho = "AHO";
$label_cte = "CTE";
$cod_interno_subestado_aprobado_pdte_visado = "45";
$subestados_sin_concretar = "'1'";
$subestados_neg_solicitando_cartas = "'16'";
$subestados_agenda = "'2', '3', '18', '54', '30', '56', '19', '4', '5', '51', '31', '48', '14', '46', '49'";
$subestados_tesoreria = "'51', '31', '48', '14', '46', '49'";
$subestados_tesoreria_no_giro = "'51', '49'";
$subestado_prospectado = "1";
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
$subestado_compras_desembolso = "48";
$subestado_desembolso = "14";
$subestado_desembolso_cliente = "46";
$subestado_desembolso_pdte_bloqueo = "49";
$subestado_incorporado = "44";
$subestado_auditado = "43";
$subestado_negociar_cartera = "7";
$subestado_devuelto_por_inconsistencias = "28";
$tiposadjuntos_prospeccion = "'32', '31', '1', '4', '3', '2', '6', '47'";
$tiposadjuntos_firmados = "'55', '56', '57', '58', '59', '60'";
$tipoadjunto_pys = "16";
$tipoadjunto_cdd = "21";
$tiporeq_cdd = "1";
$oficinas_prospeccion = "'1', '10', '22', '8', '14', '23', '13', '18', '2', '3', '24', '6', '19', '20', '27', '15', '21', '16', '17', '28', '36', '35', '37', '33'";
$area_credito = "1";
$area_visado = "2";
$area_gestion_comercial = "5";
$usuario_master = "1";
$usuario_trecuperamos = "18";
$prefijo_tablas = "";

function conectar()
{
	$h = "seas2.esefectivo.co";
	$u = "seas2_user";
	$pw = "Kredit2019**";
	$db = "seas2_seasesef_originar_bdcomercial";

	$link = mysqli_connect($h,$u,$pw);

	mysqli_select_db($link, $db);

	//mysql_set_charset("latin1", $link);
	
	return $link;
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

	$datos_padre = mysqli_query($link, "select $codigo_tp, $descripcion_tp from $tabla_padre order by $codigo_tp");

	$datos_hija = mysqli_query($link, "select $codigo_th, $codigotp_th, $descripcion_th from $tabla_hija order by $codigotp_th, $descripcion_th");

	$fila2 = mysqli_fetch_assoc($datos_hija);

	while ($fila = mysqli_fetch_assoc($datos_padre))
	{
		$padre_hija = "PH".$fila["$codigo_tp"]." = [";

		while ($fila2["$codigotp_th"] == $fila["$codigo_tp"])
		{
			$padre_hija .= "\"".$fila2["$codigo_th"]."-".$fila2["$descripcion_th"]."\",";

			$fila2 = mysqli_fetch_assoc($datos_hija);
		}

		$padre_hija .= "\"0-Otro\"];\n";

		echo $padre_hija;
	}

	echo "\nswitch(id_$tabla_padre) {\n";

	if (mysqli_num_rows($datos_padre))
	{
		mysqli_data_seek($datos_padre, 0);
	}

	while ($fila = mysqli_fetch_assoc($datos_padre))
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

function callAPI($method, $url, $data, $parameter)
{
	$curl = curl_init();

	switch ($method)
	{
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		default:
			if ($data)
				$url = sprintf("%s?%s", $url, http_build_query($data));
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

?>
