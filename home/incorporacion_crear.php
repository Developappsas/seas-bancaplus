<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link, "BEGIN TRANSACTION");

if ($_REQUEST["observacion"])
{
	sqlsrv_query($link, "insert into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '[OBS INCORPORACION] ".utf8_encode($_REQUEST["observacion"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
	
	$rs = sqlsrv_query($link, "select MAX(id_observacion) as m from simulaciones_observaciones");
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$id_observacion = "'".$fila["m"]."'";
}
else
{
	$id_observacion = "NULL";
}

sqlsrv_query($link, "COMMIT");

sqlsrv_query($link, "insert into simulaciones_incorporacion (id_simulacion, nro_incorporacion, valor_cuota, observacion, id_observacion, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".utf8_encode($_REQUEST["nro_incorporacion"])."', '".str_replace(",", "", $_REQUEST["valor_cuota"])."', '".utf8_encode($_REQUEST["observacion"])."', ".$id_observacion.", '".$_SESSION["S_LOGIN"]."', GETDATE())");

$mensaje = "Novedad ingresada exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'incorporacion_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
