<?php include ('../functions.php'); ?>
<?php include ('../convertNumbers.php'); ?>
<?php
ob_start();

require_once('../html2pdf/vendor/autoload.php');

use Spipu\Html2Pdf\Html2Pdf;
if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "SELECT si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

if ($_SESSION["S_SECTOR"])
{
	$queryDB .= " AND pa.sector = '".$_SESSION["S_SECTOR"]."'";
}

if (!$_REQUEST["ext"])
{
	if ($_SESSION["S_TIPO"] == "COMERCIAL")
	{
		$queryDB .= " AND si.id_comercial = '".$_SESSION["S_IDUSUARIO"]."'";
	}
	else
	{
		$queryDB .= " AND si.id_unidad_negocio IN (".$_SESSION["S_IDUNIDADNEGOCIO"].")";
	}
}

$rs = sqlsrv_query($link, $queryDB);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs))
{
	exit;
}

$cedula = $fila["cedula"];

/**
 * Buscamos TODOS los creditos para relacionarlos en la tabla
**/
$qcreditos = "SELECT id_simulacion, nombre, pagaduria, cedula, valor_credito, valor_visado, fecha_desembolso, now() as fecha, nro_libranza from simulaciones".$sufijo." si where estado = 'DES' AND cedula = '".$cedula."'";
$rcreditos = sqlsrv_query($link, $qcreditos);


while ($tabla = sqlsrv_fetch_array($rcreditos))
{
	$fecha_desembolso = date('d-m-Y',strtotime($tabla["fecha_desembolso"]));
	$date = date("d")." de ".$nombre_meses_completo[date("n")]." de ".date("Y");
	$fecha = date('Y-m-d');
	$queryB = "select fechas from festivos";
	$consulta = sqlsrv_query($link, $queryB);
	$festivos = sqlsrv_fetch_array($consulta);

	$conteo_dias = Dias($fecha, 6, $festivos);
	$dateb = date("d",strtotime("+".$conteo_dias." days"))." de ".$nombre_meses_completo[date("n",strtotime("+".$conteo_dias." days"))]." de ".date("Y",strtotime("+".$conteo_dias." days"));
	$nombre = reemplazar_caracteres_por_html(strtoupper(utf8_decode($tabla["nombre"])));
	$pagaduria = reemplazar_caracteres_por_html(strtoupper(utf8_decode($tabla["pagaduria"])));
	$cedula = number_format($tabla["cedula"], 0);

	$valor = number_format($saldo_capital, 0);
	$valor_cuota = number_format($opcion_cuota, 0);
	//$comercial = reemplazar_caracteres_por_html(strtoupper(utf8_decode($fila["nombre_comercial"]." ".$fila["apellido"])));


	$queryDB = "select * from cuotas".$sufijo." where id_simulacion = '".$tabla["id_simulacion"]."' order by cuota";
	$rs1 = sqlsrv_query($link, $queryDB);
	if (sqlsrv_num_rows($rs1))
		$plan_pagos_de_cuotas = 1;
	$queryDB = "select * from simulaciones".$sufijo." where id_simulacion = '".$tabla["id_simulacion"]."'";
	$rs = sqlsrv_query($link, $queryDB);
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	$plazo = $fila["plazo"];
	$tasa_interes = $fila["tasa_interes"];
	$saldo = $fila["valor_credito"];
	
	if (!$fila["sin_seguro"])
		$seguro = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	else
		$seguro = 0;
	
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
	$valor_cuota1 = $opcion_cuota - round($seguro);
	
	if (!$fila["sin_seguro"])
		$seguro_vida = $fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100));
	else
		$seguro_vida = 0;
	
	$cuota_corriente = $opcion_cuota - $seguro_vida;
	
	$rs1 = sqlsrv_query($link, "SELECT SUM(CASE WHEN pagada = '1' THEN capital + abono_capital ELSE CASE WHEN valor_cuota <> saldo_cuota THEN iIF (valor_cuota - saldo_cuota - interes - seguro > 0, valor_cuota - saldo_cuota - interes - seguro + abono_capital, abono_capital) ELSE 0 END END) as sfrom cuotas" . $sufijo . " where id_simulacion = '" .$tabla["id_simulacion"]."'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
	
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	
	$capital_recaudado = $fila1["s"];
	
	$saldo_capital = $fila["valor_credito"] - $capital_recaudado;
	
	$rs1 = sqlsrv_query($link, "select COUNT(*) as c from cuotas".$sufijo." where id_simulacion = '".$tabla["id_simulacion"]."' AND fecha <= CURDATE()");

	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

	$cuotas_causadas = $fila1["c"];

	if ($fila["sin_seguro"])
		$seguro_causado = round($fila["valor_credito"] / 1000000.00 * $fila["valor_por_millon_seguro"] * (1 + ($fila["porcentaje_extraprima"] / 100))) * $cuotas_causadas;

	$rs1 = sqlsrv_query($link, "select COUNT(*) as c, SUM(saldo_cuota) as s from cuotas".$sufijo." where id_simulacion = '".$tabla["id_simulacion"]."' AND fecha < CURDATE() AND pagada = '0'");
	$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);
	$cuotas_mora = $fila1["c"];
	$total_mora = $fila1["s"];
	
	if (!$cuotas_mora)
		$total_pagar = $saldo_capital * (1 + $fila["tasa_interes"] / 100.00) + $seguro_vida + $seguro_causado;
	else
		$total_pagar = $saldo_capital + ((($saldo_capital * $fila["tasa_interes"] / 100.00) + $seguro_vida) * $cuotas_mora) + $seguro_causado;

	$detalle .= "<tr><td>".$tabla["pagaduria"]."</td><td>".$tabla["nro_libranza"]."</td><td align='right'>$ ".number_format($saldo_capital, 0)."</td><td align='right'>$ ".number_format($opcion_cuota, 0)."</td><td align='right'>".$fecha_desembolso."</td></tr>";
	
	$saldo_total_capital += $saldo_capital;
}

//return;

$letras = convert_number_to_words($saldo_total_capital);

$body_mail = file_get_contents("../plantillas/certificacion_deuda.html");
$body_mail = str_replace("__DATE", $date, $body_mail);
$body_mail = str_replace("__FEC", $dateb, $body_mail);
$body_mail = str_replace("__NOMBRE", $nombre, $body_mail);
$body_mail = str_replace("__PAGADURIA", $pagaduria, $body_mail);
$body_mail = str_replace("__CEDULA", $cedula, $body_mail);
$body_mail = str_replace("__VALOR", number_format($saldo_total_capital), $body_mail);
$body_mail = str_replace("__COMERCIAL", $comercial, $body_mail);
$body_mail = str_replace("__FECHAPAGO", $fecha1, $body_mail);
$body_mail = str_replace("__DETALLE", $detalle, $body_mail);
$body_mail = str_replace("__LETRAS", $letras, $body_mail);


$html2pdf = new HTML2PDF('P','a4','es');
$html2pdf->pdf->SetDisplayMode('fullpage');
$html2pdf->WriteHTML($body_mail);
if(ob_get_length() > 0) {
    ob_clean();
}
$html2pdf->Output("certificacion_deuda" . $_REQUEST["id_simulacion"] . ".pdf");
?>
