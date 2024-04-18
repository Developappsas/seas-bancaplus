<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

include ('../functions.php');  
include ('../convertNumbers.php'); 
require_once('../html2pdf/vendor/autoload.php');

use Spipu\Html2Pdf\Html2Pdf;


if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO")) {
	exit;
}
$link = conectar();

if ($_REQUEST["ext"])
$sufijo = "_ext";

$queryDB = "select si.* from simulaciones" . $sufijo . " si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";

if ($_SESSION["S_SECTOR"]) {
	$queryDB .= " AND pa.sector = '" . $_SESSION["S_SECTOR"] . "'";
}

if (!$_REQUEST["ext"]) {
	if ($_SESSION["S_TIPO"] == "COMERCIAL") {
		$queryDB .= " AND si.id_comercial = '" . $_SESSION["S_IDUSUARIO"] . "'";
	} else {
		$queryDB .= " AND si.id_unidad_negocio IN (" . $_SESSION["S_IDUNIDADNEGOCIO"] . ")";
	}
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs)) {
	exit;
}

switch ($fila["opcion_credito"]) {
	case "CLI":
		$opcion_cuota = $fila["opcion_cuota_cli"];
		break;
		case "CCC":
			$opcion_cuota = $fila["opcion_cuota_ccc"];
			break;
			case "CMP":
				$opcion_cuota = $fila["opcion_cuota_cmp"];
				break;
				case "CSO":
					$opcion_cuota = $fila["opcion_cuota_cso"];
					break;
				}
				
				$queryDB = "select id_simulacion, nombre, pagaduria, cedula, valor_credito, fecha_desembolso,getdate() as fecha, nro_libranza from simulaciones" . $sufijo . " si where id_simulacion = '" . $_REQUEST["id_simulacion"] . "'";
				
				$simulacion = sqlsrv_query($link, $queryDB);
				
				$fila = sqlsrv_fetch_array($simulacion);
			
				
				
				
				$dated = date("d");
				$datem = $nombre_meses_completo[date("n")];
				$datey = date("Y");
				$nombre = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["nombre"])));
				$pagaduria = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["pagaduria"])));
				$cedula = number_format($fila["cedula"], 0);
				$valor = number_format($fila["valor_credito"], 0);
				$libranza = $fila["nro_libranza"];
				$cuota = number_format($opcion_cuota, 0);
				
				$parametros = sqlsrv_query($link, "select valor from parametros where codigo IN ('FICCP', 'FINCP') order by codigo");
				
				$fila1 = sqlsrv_fetch_array($parametros);
				
				$cargo_firmante = $fila1[0];
				
				$fila1 = sqlsrv_fetch_array($parametros);

$nombre_firmante = $fila1[0];

$body_mail = file_get_contents("../plantillas/paz_salvo.html");
$body_mail = str_replace("__DATED", $dated, $body_mail);
$body_mail = str_replace("__DATEM", $datem, $body_mail);
$body_mail = str_replace("__DATEY", $datey, $body_mail);
$body_mail = str_replace("__NOMBRE", $nombre, $body_mail);
$body_mail = str_replace("__PAGADURIA", $pagaduria, $body_mail);
$body_mail = str_replace("__CEDULA", $cedula, $body_mail);
$body_mail = str_replace("__VALOR", $valor, $body_mail);
$body_mail = str_replace("__CUOTA", $cuota, $body_mail);
$body_mail = str_replace("__LIBRANZA", $libranza, $body_mail);
$body_mail = str_replace("__FECHAPAGO", $fecha1, $body_mail);
$body_mail = str_replace("__FIRMANTE_CARGO", $cargo_firmante, $body_mail);


$html2pdf = new Html2Pdf('P', 'letter', 'es');
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->WriteHTML($body_mail);
  //  $ruta =  "../temp/paz_salvo" . $_REQUEST["id_simulacion"] . ".pdf";
if(ob_get_length() > 0) {
    ob_clean();
}

$html2pdf->Output("paz_salvo" . $_REQUEST["id_simulacion"] . ".pdf");

?>
