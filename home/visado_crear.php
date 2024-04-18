<?php include ('../functions.php'); ?>
<?php include ('../function_blob_storage.php'); ?>
<?php

if (!$_SESSION["S_LOGIN"] || ($_SESSION["S_TIPO"] != "ADMINISTRADOR" && $_SESSION["S_TIPO"] != "OPERACIONES" && $_SESSION["S_SUBTIPO"] != "ANALISTA_VISADO" && $_SESSION["S_SUBTIPO"] != "COORD_VISADO"))
{
	exit;
}

$link = conectar();

?>
<?php include("top.php"); ?>
<?php

sqlsrv_query($link,"START TRANSACTION");

if ($_REQUEST["observacion"])
{
	sqlsrv_query($link,"INSERT into simulaciones_observaciones (id_simulacion, observacion, usuario_creacion, fecha_creacion) values ('".$_REQUEST["id_simulacion"]."', '[OBS VISADO] ".utf8_encode($_REQUEST["observacion"])."', '".$_SESSION["S_LOGIN"]."', GETDATE())");
	
	$rs = sqlsrv_query($link,"select MAX(id_observacion) as m from simulaciones_observaciones");
	
	$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
	
	$id_observacion = "'".$fila["m"]."'";
}
else
{
	$id_observacion = "NULL";
}

sqlsrv_query( $link,"INSERT into simulaciones_visado (id_simulacion, tipo_visado, valor_visado, id_visador, observacion, id_observacion, usuario_creacion, fecha_creacion) VALUES ('".$_REQUEST["id_simulacion"]."', '".$_REQUEST["tipo_visado"]."', '".str_replace(",", "", $_REQUEST["valor_visado"])."', '".$_REQUEST["id_visador"]."', '".utf8_encode($_REQUEST["observacion"])."', ".$id_observacion.", '".$_SESSION["S_LOGIN"]."', getdate())");

$rs = sqlsrv_query($link,"select MAX(id_visado) as m from simulaciones_visado");

$fila = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);

$id_visado = $fila["m"];

sqlsrv_query($link,"COMMIT");

if (strcmp($_FILES["archivo"]["name"], ""))
{
	$uniqueID = uniqid();
	
	sqlsrv_query($link,"UPDATE simulaciones_visado set nombre_original = '".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."', nombre_grabado = '".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"])."' where id_visado = '".$id_visado."'");
	
	$fechaa =new DateTime();
	$fechaFormateada = $fechaa->format("d-m-Y H:i:s");
	
	$metadata1 = array(
		'id_simulacion' => $_REQUEST["id_simulacion"],
		'descripcion' => "Adjunto visado",
		'usuario_creacion' => $_SESSION["S_LOGIN"],
		'fecha_creacion' => $fechaFormateada
	);
	
	upload_file($_FILES["archivo"], "simulaciones", $_REQUEST["id_simulacion"]."/varios/".$uniqueID."_".reemplazar_caracteres_no_utf($_FILES["archivo"]["name"]), $metadata1);
}

$mensaje = "Visado ingresado exitosamente";

?>
<script>
alert("<?php echo $mensaje ?>");

window.location = 'visado_actualizar.php?id_simulacion=<?php echo $_REQUEST["id_simulacion"] ?>&descripcion_busqueda=<?php echo $_REQUEST["descripcion_busqueda"] ?>&buscar=<?php echo $_REQUEST["buscar"] ?>&page=<?php echo $_REQUEST["page"] ?>';
</script>
