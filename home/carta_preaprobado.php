<?php
include('../functions.php');

ob_start();

require_once('../html2pdf/vendor/autoload.php');

use Spipu\Html2Pdf\Html2Pdf;


if (!$_SESSION["S_LOGIN"] || $_SESSION["S_SUBTIPO"] == "AUXILIAR_OFICINA") {
	exit;
}


$link = conectar();

$queryDB = "select si.nombre, si.pagaduria, si.institucion, si.desembolso_cliente, us.nombre as nombre_comercial, us.apellido from simulaciones si INNER JOIN unidades_negocio un ON si.id_unidad_negocio = un.id_unidad INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN usuarios us ON si.id_comercial = us.id_usuario where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "' AND si.decision = '" . $label_viable . "'";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if ($_SESSION["S_TIPO"] == "COMERCIAL") {
	$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
} else {
	$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
}

if ($_SESSION["S_TIPO"] == "GERENTECOMERCIAL" || $_SESSION["S_TIPO"] == "DIRECTOROFICINA" || $_SESSION["S_TIPO"] == "PROSPECCION") {
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '" . $_SESSION["S_IDUSUARIO"] . "')";

	if ($_SESSION["S_SUBTIPO"] == "PLANTA")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";

	if ($_SESSION["S_SUBTIPO"] == "PLANTA_EXTERNOS")
		$queryDB .= " AND si.telemercadeo = '0'";

	if ($_SESSION["S_SUBTIPO"] == "PLANTA_TELEMERCADEO")
		$queryDB .= " AND NOT (us.freelance = '1' OR us.outsourcing = '1')";

	if ($_SESSION["S_SUBTIPO"] == "EXTERNOS")
		$queryDB .= " AND (us.freelance = '1' OR us.outsourcing = '1') AND si.telemercadeo = '0'";

	if ($_SESSION["S_SUBTIPO"] == "TELEMERCADEO")
		$queryDB .= " AND si.telemercadeo = '1'";
}
// echo "prueba";
$simulacion = sqlsrv_query($link, $queryDB);

$fila = sqlsrv_fetch_array($simulacion);

$date = date("d") . " de " . $nombre_meses_completo[date("n")] . " de " . date("Y");
$nombre = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["nombre"])));
$pagaduria = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["pagaduria"])));
$institucion = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["institucion"])));
$valor = number_format($fila["desembolso_cliente"], 0);
$comercial = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["nombre_comercial"] . " " . $fila["apellido"])));


$parametros = sqlsrv_query($link, "select valor from parametros where codigo IN ('FICCP', 'FINCP') order by codigo");

$fila1 = sqlsrv_fetch_array($parametros);

$cargo_firmante = $fila1[0];

$fila1 = sqlsrv_fetch_array($parametros);

$nombre_firmante = $fila1[0];

$body_mail = file_get_contents("../plantillas/carta_preaprobado.html");
$body_mail = str_replace("__DATE", $date, $body_mail);
$body_mail = str_replace("__NOMBRE", $nombre, $body_mail);
$body_mail = str_replace("__PAGADURIA", $pagaduria, $body_mail);
$body_mail = str_replace("__INSTITUCION", $institucion, $body_mail);
$body_mail = str_replace("__VALOR", $valor, $body_mail);
$body_mail = str_replace("__COMERCIAL", $comercial, $body_mail);
$body_mail = str_replace("__FIRMANTE_NOMBRE", $nombre_firmante, $body_mail);
$body_mail = str_replace("__FIRMANTE_CARGO", $cargo_firmante, $body_mail);

$html2pdf = new Html2Pdf('P', 'letter', 'es');
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->WriteHTML($body_mail);
if(ob_get_length() > 0) {
    ob_clean();
}
$html2pdf->Output("CartaPreaprobado" . $_REQUEST["id_simulacion"] . ".pdf");

?>
<script>
	window.location.href = '<?php echo $ruta ?>'
</script>