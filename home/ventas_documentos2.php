<?php include ('../functions.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || !$_SESSION["FUNC_FULLSYSTEM"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_CREDITO" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VEN_CARTERA"))
{
	exit;
}

$link = conectar();

if ($_REQUEST["ext"])
	$sufijo = "_ext";
	
$queryDB = "SELECT vdd.id_documentocierre, vdd.estado from ventas_detalle_documentos".$sufijo." vdd INNER JOIN documentos_cierre dc ON vdd.id_documentocierre = dc.id_documentocierre INNER JOIN ventas_detalle".$sufijo." vd ON vdd.id_ventadetalle = vd.id_ventadetalle where vdd.id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."'";

$queryDB .= " order by dc.nombre";

$rs = sqlsrv_query($link,$queryDB);

while ($fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))
{
	if ($_REQUEST["estado".$fila["id_documentocierre"]] != "1")
		$_REQUEST["estado".$fila["id_documentocierre"]] = "0";
	
	sqlsrv_query($link,"update ventas_detalle_documentos".$sufijo." set estado = '".$_REQUEST["estado".$fila["id_documentocierre"]]."' where id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."' and id_documentocierre = '".$fila["id_documentocierre"]."'");
	
	if ($_REQUEST["estado".$fila["id_documentocierre"]] != $fila["estado"])
	{
		sqlsrv_query($link,"update ventas_detalle_documentos".$sufijo." set usuario_modificacion = '".$_SESSION["S_LOGIN"]."', fecha_modificacion = GETDATE() where id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."' and id_documentocierre = '".$fila["id_documentocierre"]."'");
	}
}

sqlsrv_query($link,"UPDATE ventas_detalle".$sufijo." set completo = '".$_REQUEST["completo"]."' WHERE id_ventadetalle = '".$_REQUEST["id_ventadetalle"]."'");

$mensaje = "Documentos actualizados exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'ventas_actualizar.php?ext=<?php echo $_REQUEST["ext"] ?>&id_venta=<?php echo $_REQUEST["id_venta"] ?>&fecha_inicialbd=<?php echo $_REQUEST["fecha_inicialbd"] ?>&fecha_inicialbm=<?php echo $_REQUEST["fecha_inicialbm"] ?>&fecha_inicialba=<?php echo $_REQUEST["fecha_inicialba"] ?>&fecha_finalbd=<?php echo $_REQUEST["fecha_finalbd"] ?>&fecha_finalbm=<?php echo $_REQUEST["fecha_finalbm"] ?>&fecha_finalba=<?php echo $_REQUEST["fecha_finalba"] ?>&id_compradorb=<?php echo $_REQUEST["id_compradorb"] ?>&modalidadb=<?php echo $_REQUEST["modalidadb"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&descripcion_busqueda2=<?php echo $_REQUEST["descripcion_busqueda2"] ?>&estadob=<?php echo $_REQUEST["estadob"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
