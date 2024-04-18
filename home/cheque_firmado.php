<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || $_SESSION["S_TIPO"] != "ADMINISTRADOR")
{
	exit;
}

$link = conectar();

$queryDB = "SELECT si.id_simulacion from simulaciones si INNER JOIN pagadurias pa ON si.pagaduria = pa.nombre where si.id_simulacion = '".$_REQUEST["id_simulacion"]."'";

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



$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);

if (!sqlsrv_num_rows($rs1))
{
	exit;
}


$queryDB = "SELECT ent.nombre, scc.entidad from simulaciones_comprascartera scc LEFT JOIN entidades_desembolso ent ON scc.id_entidad = ent.id_entidad where scc.id_simulacion = '".$_REQUEST["id_simulacion"]."' AND scc.consecutivo = '".$_REQUEST["consecutivo"]."'";


$rs1 = sqlsrv_query($link, $queryDB, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));

$fila1 = sqlsrv_fetch_array($rs1, SQLSRV_FETCH_ASSOC);


if (!sqlsrv_num_rows($rs1))
{
	exit;
}

if ($_REQUEST["cheque_firmado"] == "true")
{
	$titulo = "Desea establecer que firm&oacute; el cheque para la compra de cartera";
}
else
{
	$titulo = "Desea anular la marcaci&oacute;n de cheque firmado para la compra de cartera";
}

?>
<?php include("top2.php"); ?>
<table border="0" cellspacing=1 cellpadding=2 width="100%">
<tr>
	<td class="titulo"><center><b><?php echo $titulo ?><br><?php echo $fila1["nombre"]." ".$fila1["entidad"] ?>?</b><br><br></center></td>
</tr>
</table>
<form name="formato" method="post" action="cheque_firmado2.php">
<table border="0" cellspacing=1 cellpadding=2>
<tr><td><br></td></tr>
<tr><td align="center">
	<input type="hidden" name="id_simulacion" value="<?php echo $_REQUEST["id_simulacion"] ?>">
	<input type="hidden" name="descripcion_busqueda" value="<?php echo $_REQUEST["descripcion_busqueda"] ?>">
	<input type="hidden" name="sectorb" value="<?php echo $_REQUEST["sectorb"] ?>">
	<input type="hidden" name="pagaduriab" value="<?php echo $_REQUEST["pagaduriab"] ?>">
	<input type="hidden" name="estadob" value="<?php echo $_REQUEST["estadob"] ?>">
	<input type="hidden" name="id_subestadob" value="<?php echo $_REQUEST["id_subestadob"] ?>">
	<input type="hidden" name="buscar" value="<?php echo $_REQUEST["buscar"] ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST["page"] ?>">
	<input type="hidden" name="consecutivo" value="<?php echo $_REQUEST["consecutivo"] ?>">
	<input type="hidden" name="cheque_firmado" value="<?php echo $_REQUEST["cheque_firmado"] ?>">
	<input type="button" value="Aceptar" onClick="submit()">&nbsp;&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="opener.location.reload(); window.close();">
	</td>
</tr>
</table>
</form>
<?php include("bottom2.php"); ?>
