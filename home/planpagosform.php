<?php include ('../functions.php'); ?>
<?php

$link = conectar();

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "PROSPECCION" && $_SESSION["S_TIPO"] != "TESORERIA" && $_SESSION["S_TIPO"] != "CARTERA" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_TIPO"] != "CONTABILIDAD" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_GEST_COM" && $_SESSION["S_SUBTIPO"] != "ANALISTA_REFERENCIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_TESORERIA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_JURIDICO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CARTERA" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA" && $_SESSION["S_SUBTIPO"] != "COORD_PROSPECCION" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_CREDITO" && $_SESSION["S_SUBTIPO"] != "AUXILIAR_OFICINA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";

$queryDB = "select si.* from simulaciones".$sufijo." si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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

if ($_SESSION["S_TIPO"] == "PROSPECCION")
	$queryDB .= " AND si.id_oficina IN (SELECT id_oficina FROM oficinas_usuarios WHERE id_usuario = '".$_SESSION["S_IDUSUARIO"]."')";

$rs = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs))
{
	exit;
}

if ($_REQUEST["exportar"])
{
	$titulo = "Exportar";
	
	$url_form = "planpagos.php";
}
else
{
	$titulo = "Imprimir";
	
	$url_form = "planpagos_imprimir.php";
}

?>
<?php include("top2.php"); ?>
<table border="0" cellspacing=1 cellpadding=2>
<tr>
	<td class="titulo"><center><b><?php echo $titulo ?> Plan de Pagos</b><br><br></center></td>
</tr>
</table>
<form name=formato method=post action="<?php echo $url_form ?>">
<table>
<tr height="40">
	<td>A qui&eacute;n va dirigido el plan de pagos?</td>
	<td><input type="radio" name="dirigido_a" style="background-color:#EAF1DD;" value="CLIENTE" checked>Cliente</td>
	<td><input type="radio" name="dirigido_a" style="background-color:#EAF1DD;" value="FONDEADOR">Fondeador</td>
</tr>
</table>
<br>
<p align="center">
<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
<input type="hidden" name="ext" value="<?php echo $_REQUEST["ext"] ?>">
<input type="hidden" name="exportar" value="<?php echo $_REQUEST["exportar"] ?>">
<input type="submit" value="<?php echo $titulo ?>">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="window.close()">
</p>
</form>
<?php include("bottom2.php"); ?>
