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

if ($_REQUEST["cheque_firmado"] == "true")
{
	sqlsrv_query($link, "update tesoreria_cc set usuario_firma_cheque = '".$_SESSION["S_LOGIN"]."', fecha_firma_cheque = NOW() where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$_REQUEST["consecutivo"]."'");
}
else
{
	sqlsrv_query($link, "update tesoreria_cc set usuario_firma_cheque = NULL, fecha_firma_cheque = NULL where id_simulacion = '".$_REQUEST["id_simulacion"]."' AND consecutivo = '".$_REQUEST["consecutivo"]."'");
}

$mensaje = "Proceso realizado";

?>
<script>
alert("<?php echo $mensaje ?>");

opener.location.href = 'tesoreria_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&sectorb=<?php echo $_REQUEST["sectorb"] ?>&pagaduriab=<?php echo $_REQUEST["pagaduriab"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&id_subestadob=<?php echo $_REQUEST["id_subestadob"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';

window.close();
</script>

