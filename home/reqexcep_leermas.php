<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"])
{
	exit;
}

$link = conectar();

$queryDB = "SELECT * from req_excep re INNER JOIN simulaciones si ON re.id_simulacion = si.id_simulacion INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre INNER JOIN areas_reqexcep ar ON re.id_area = ar.id_area";

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " INNER JOIN areas_reqexcep_perfiles arp ON ar.id_area = arp.id_area AND arp.id_perfil = '".$_SESSION["S_IDPERFIL"]."'";
}

$queryDB .= " where re.estado != 'ANULADO' AND re.id_reqexcep = '".$_REQUEST["id_reqexcep"]."'";

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

if ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO")
{
	$queryDB .= " AND (CASE re.id_area ";
	$queryDB .= " 	WHEN '".$area_credito."' THEN";
	$queryDB .= " 		CASE WHEN si.id_analista_riesgo_crediticio IS NOT NULL THEN";
	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_crediticio = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
	$queryDB .= " 		ELSE ";
	$queryDB .= " 			CASE WHEN si.id_analista_riesgo_operativo IS NOT NULL THEN";
	$queryDB .= " 				CASE WHEN si.id_analista_riesgo_operativo = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1 ELSE 1 = 0 END";
	$queryDB .= " 			ELSE 1 = 0 END";
	$queryDB .= " 		END";
/*	$queryDB .= " 	WHEN '".$area_visado."' THEN";
	$queryDB .= " 		CASE WHEN si.pagaduria IN (select pa.nombre from pagadurias pa INNER JOIN pagadurias_usuarios_visado puv ON puv.id_pagaduria = pa.id_pagaduria AND puv.id_usuario = '".$_SESSION["S_IDUSUARIO"]."') THEN 1 = 1";
	$queryDB .= " 		ELSE 1 = 0 END";*/
/*	$queryDB .= " 	WHEN '".$area_gestion_comercial."' THEN";
	$queryDB .= " 		CASE WHEN si.id_analista_gestion_comercial = '".$_SESSION["S_IDUSUARIO"]."' THEN 1 = 1";
	$queryDB .= " 		ELSE 1 = 0 END";*/
	$queryDB .= " 	ELSE 1 = 1 END";
	$queryDB .= " )";
}

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
echo $queryDB;
if (!sqlsrv_num_rows($rs))
{
    exit;
}

?>
<style type="text/css">
table {
	*border-collapse: collapse; /* IE7 and lower */
    border-spacing: 0; 
}

th:first-child {
	border-radius: 6px 0 0 0;
}

th:last-child {
	border-radius: 0 6px 0 0;
}

th:only-child{
	border-radius: 6px 6px 0 0;
}

tr:first-child {
	border-radius: 6px 0 0 0;
}

tr:last-child {
	border-radius: 0 6px 0 0;
}

tr:only-child{
	border-radius: 6px 6px 0 0;
}

td:first-child {
	border-radius: 6px 0 0 0;
}

td:last-child {
	border-radius: 0 6px 0 0;
}

td:only-child{
	border-radius: 6px 6px 0 0;
}
</style>
<link href="../style_impresion.css" rel="stylesheet" type="text/css">
<?php

$queryDB = "select observacion, tipo_respuesta, respuesta from req_excep where id_reqexcep = '".$_REQUEST["id_reqexcep"]."'";

$rs = sqlsrv_query($link, $queryDB);

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if ($_REQUEST["pregunta"])
{
	$titulo_reqexcep = "Detalle Descripci&oacute;n";
	
	$detalle = utf8_decode(str_replace(chr(13), "<br>", $fila["observacion"]));
}
else
{
	$titulo_reqexcep = "Detalle Respuesta";
	
	if ($fila["tipo_respuesta"])
	{
		$detalle = "[".$fila["tipo_respuesta"]."] ";
	}
	
	$detalle .= utf8_decode(str_replace(chr(13), "<br>", $fila["respuesta"]));
}

?>
<table border="0" cellspacing=3 cellpadding=0 align="center" width="90%">
<tr><td><img align src="../images/logo.png" height="80"></td></tr>
<tr><td align="center"><h4><?php echo $titulo_reqexcep ?></h4></b></td></tr>
<tr style="background:#E0ECFF;"><td class="admintable"><?php echo $detalle ?></td></tr>
</table>
